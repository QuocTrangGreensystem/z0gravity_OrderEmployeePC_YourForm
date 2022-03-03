<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class Z0Iterator {
	public $scope = array();
	public $iterators = array();
	public function __construct($scope){
		$this->scope = $scope;
	}
	function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
    }
	public function add($key, $callback, $thirdArgument = array()){
		$this->iterators[$key] = array( array($this->scope, $callback), $thirdArgument);
		return $this;
	}
	public function execute($data, $isResource = 0){
		$result = array();
		foreach($data as $id => $edata){
			foreach ($this->iterators as $key => $callback) {
				$localResult = call_user_func($callback[0], $id, $edata, $callback[1]);
				if( $localResult && $localResult['status'] == 1 ){
					if( isset($localResult['key']) )$k = $localResult['key'];
					else $k = $id;
					if( isset($localResult['action']) ){
						switch ($localResult['action']) {
							case 'sum':
								if( !isset($result[$key][$k]) ){
									$result[$key][$k] = 0;
								}
								$result[$key][$k] += $localResult['return'];
								break;
							case 'sumArray':
								foreach($localResult['return'] as $t => $val){
									if( !isset($result[$key][$k][$t]) ){
										$result[$key][$k][$t] = $val;
									} else {
										$result[$key][$k][$t] += $val;
									}
								}
							break;
							case 'append':
								if( !isset($result[$key][$k]) ){
									$result[$key][$k] = array();
								}
								$result[$key][$k][] = $localResult['return'];
							break;
							case 'sumCapacity':
								foreach($localResult['return'] as $part => $partData){
									foreach($partData as $time => $val){
										if( !isset($result[$key][$part][$k][$time]) ){
											$result[$key][$part][$k][$time] = $val;
										} else {
											$result[$key][$part][$k][$time] += $val;
										}
									}
								}
							break;
							case 'merge':
								foreach($localResult['return'] as $time => $dayList){
									if( isset($result[$key][$k][$time]) ){
										$result[$key][$k][$time] = $result[$key][$k][$time] + $dayList;
									} else {
										$result[$key][$k][$time] = $dayList;
									}
								}
							break;
							default:
								$result[$key][$k] = $localResult['return'];
								break;
						}
					} else {
						$result[$key][$k] = $localResult['return'];
					}
				}
			}
		}
		return $result;
	}
}
class NewStaffingController extends AppController {

	public $uses = array('Workday', 'Holiday', 'Project', 'AbsenceRequest', 'Employee', 'ProfitCenter', 'ActivityRequest', 'Period', 'ActivityProfitRefer', 'TmpStaffingSystem', 'Activity', 'EmployeeMultiResource');

	public function beforeFilter(){
		parent::beforeFilter();
	}

	/*
	* For non-resource staffing: use working[sum] as working days
	*
	* For resource staffing: dont use working[sum] as working days, use Math.max(each resource/time...) instead
	*/

	public function index(){
		App::import('Vendor', 'str_utility');
		$str = new str_utility();
		ini_set('memory_limit', '256M');
		$ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
		$cid = $this->employee_info['Company']['id'];
		if( !empty($this->params['requested']) ){
			$params = $this->params['?'];
		}
		else {
			$params = $this->params['url'];
		}
		$type = $params['type'];
		$summary = $params['summary'];
		$viewBy = $params['view_by'];
		$startDate = strtotime($str->convertToSQLDate($params['start_date']) . ' 00:00:00');
		$endDate = strtotime($str->convertToSQLDate($params['end_date']) . ' 00:00:00');

		//init periods for uusing toPeriod
		$this->getPeriods($startDate, $endDate);

		$iter = new Z0Iterator($this);
		// if( $viewBy == 'month' ){
		// 	$iter->add('theo_capacity', 'iCalculateTheoCapacity');
		// }

		$result = array();
		$result['working'] = $this->getWorkingDays($startDate, $endDate, $viewBy);

		$working = $result['working']['sum'];
		// debug($result); exit;
		switch($type){
			case 0:
			case 1:
				$resourceConditions = array();
				$isResource = false;
				if( !empty($params['resource']) ){
					$resourceConditions['id'] = explode(',', $params['resource']);
					$isResource = true;
				}
				if( !empty($params['pc']) && !$isResource ){
					$list = explode(',', $params['pc']);
					$resourceConditions['id'] = $this->Employee->find('list', array(
						'fields' => array('id', 'id'),
						'recursive' => -1,
						'conditions' => array(
							'profit_center_id' => $list,
							'company_id' => $cid
						)
					));
				}
				$resourceConditions['company_id'] = $cid;
				if( $isResource ){
					//if select resource: only get resource has consumed/workload
					$activityConditions = $conditions = array();

					if( !empty($params['family']) ){
						$activityConditions['family_id'] = explode(',', $params['family']);
					}
					if( !empty($params['subfamily']) ){
						$activityConditions['subfamily_id'] = explode(',', $params['subfamily']);
					}
					if( !empty($params['customer']) ){
						$activityConditions['budget_customer_id'] = explode(',', $params['customer']);
					}
					if( !empty($params['priority']) ){
						$conditions['project_id'] = $this->Project->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'id' => explode(',', $params['priority'])
							),
							'fields' => array('id', 'id')
						));
					}
					if( !empty($params['activity']) ){
						$conditions['activity_id'] = explode(',', $params['activity']);
					} else if( !empty($activityConditions) ) {
						$activityConditions['company_id'] = $cid;
						$conditions['activity_id'] = $this->Activity->find('list', array(
							'recursive' => -1,
							'conditions' => $activityConditions,
							'fields' => array('id', 'id')
						));
					}
					$conditions['date BETWEEN ? AND ?'] = array($startDate, $endDate);
					$conditions['model'] = 'employee';
					$conditions['model_id'] = $resourceConditions['id'];
					$conditions['company_id'] = $cid;
					// $resourceConditions['id'] = $this->TmpStaffingSystem->find('list', array(
					// 	'recursive' => -1,
					// 	'conditions' => $conditions,
					// 	'fields' => array('model_id', 'model_id')
					// ));
				}
				$resources = $this->getResources($startDate, $endDate, $resourceConditions);
				$mIds = $this->EmployeeMultiResource->isMultipleResources(array_keys($resources));
				$mCapacity = $this->EmployeeMultiResource->getCapacity($mIds, $startDate, $endDate, $viewBy);
					
				if( $isResource ){
					$result['absence'] = $this->__getAbsences($resourceConditions['id'], $startDate, $endDate, $viewBy);

					$iter->add('resource', 'iCountResource', array(
						'start' => $startDate,
						'end' => $endDate,
						'view_by' => $viewBy,
						'work_days' => $result['working'],
						'is_resource' => 1,
						'mIds' => $mIds,
						'mCapacity' => $mCapacity
					));
					
				} else {
					$iter->add('absence', 'iCountAbsenceForPC', array(
						'start' => $startDate,
						'end' => $endDate,
						'view_by' => $viewBy,
						'absence' => $this->__getAbsences(array_keys($resources), $startDate, $endDate, $viewBy)
					));
					$iter->add('resource', 'iCountResource', array(
						'start' => $startDate,
						'end' => $endDate,
						'view_by' => $viewBy,
						'work_days' => $result['working'],
						'mIds' => $mIds,
						'mCapacity' => $mCapacity
					));
				}

				$iter->add('raw_workday', 'iCountWorksDay', array(
					'start' => $startDate,
					'end' => $endDate,
					'view_by' => $viewBy,
					'work_days' => $result['working'],
					'is_resource' => $isResource
				));
				
				$result = array_merge($result, $iter->execute($resources, 0));

				//sum here
				//2016-11-04 ratio.
				if( $summary ){
					//absence
					$summary = array();
					if( !empty($result['absence']) ){
						$summary['absence'] = array();
						foreach( $result['absence'] as $list){
							foreach($list as $time => $value){
								if( !isset($summary['absence'][$time]) ){
									$summary['absence'][$time] = $value*$ratio;
								} else {
									$summary['absence'][$time] += $value*$ratio;
								}
							}
						}
					}
					//resource
					if( !empty($result['resource']['sum']) ){
						$summary['resource'] = array();
						foreach( $result['resource']['sum'] as $list){
							foreach($list as $time => $value){
								if( !isset($summary['resource'][$time]) ){
									$summary['resource'][$time] = $value;
								} else {
									$summary['resource'][$time] += $value;
								}
							}
						}
					}
					//raw capacity (not deducted from absence)
					if( !empty($result['resource']['raw_capacity']) ){
						$summary['raw_capacity'] = array();
						foreach( $result['resource']['raw_capacity'] as $list){
							foreach($list as $time => $value){
								if( !isset($summary['raw_capacity'][$time]) ){
									$summary['raw_capacity'][$time] = $value*$ratio;
								} else {
									$summary['raw_capacity'][$time] += $value*$ratio;
								}
							}
						}
					}
					//capacity
					if( !empty($summary['raw_capacity']) ){
						foreach($summary['raw_capacity'] as $time => $val){
							if( isset($summary['absence'][$time]) ){
								$summary['capacity'][$time] = $val - $summary['absence'][$time];
							} else {
								$summary['capacity'][$time] = $val;
							}
						}
						unset($summary['raw_capacity']);
					}
					//theo capacity
					if( !empty($result['resource']['capacity_theoretical']) ){
						$summary['capacity_theoretical'] = array();
						foreach( $result['resource']['capacity_theoretical'] as $list){
							foreach($list as $time => $value){
								if( !isset($summary['capacity_theoretical'][$time]) ){
									$summary['capacity_theoretical'][$time] = $value;
								} else {
									$summary['capacity_theoretical'][$time] += $value;
								}
							}
						}
					}
					//workdays
					// debug($result['raw_workday']); exit;
					if( !empty($result['raw_workday']) ){
						$raw = array();
						foreach($result['raw_workday'] as $pc => $list){
							
							foreach($list as $time => $dayList){
								if( isset($raw[$time]) )$raw[$time] = $raw[$time] + $dayList;
								else $raw[$time] = $dayList;
							
							}
						}
						foreach($raw as $time => $list){
							$summary['working'][$time] = 0;
							foreach($list as $_time => $val){
								$summary['working'][$time] += $val;
							}
						}
						
						unset($summary['raw_workday']);
					}
					$result['summary'] = $summary;
					// debug($summary);
					// exit;

				}
				//$result['working'] = $working;
				if( $type == 1 && !empty($params['list']) ){
					$l = json_decode($params['list']);
					$x = $this->sumList($result, $l);
					$result = $result + $x;
				}


				if( $isResource ){
					$x = array();
					foreach($resources as $id => $sdfsdf){
						$x[$id] = array(
							'absence' => isset($result['absence'][$id]) ? $result['absence'][$id] : array(),
							'capacity_theoretical' => isset($result['resource']['capacity_theoretical'][$id]) ? $result['resource']['capacity_theoretical'][$id] : array(),
							'resource' => array(),
							'working' => isset($result['resource']['raw_capacity'][$id]) ? $result['resource']['raw_capacity'][$id] : array(),
							'capacity' => array()
						);
						//calculate capacity
						foreach($x[$id]['working'] as $time => $val){
							//multi resource
							if( in_array($id, $mIds) ){
								$x[$id]['capacity'][$time] = floatval(isset($mCapacity[$id][$time]) ? $mCapacity[$id][$time] : 0);
								$x[$id]['working'][$time] = $result['working']['sum'][$time];
								$x[$id]['absence'][$time] = 0;
							}
							//normal resource
							else if( isset($x[$id]['absence'][$time]) ){
								$x[$id]['capacity'][$time] = $val - $x[$id]['absence'][$time];
							} else {
								$x[$id]['capacity'][$time] = $val;
							}
						}
					}

					$result = $result + $x;
					

					unset($result['resource'], $result['absence']);
				}
				//end sum
			break;
			//pc, use 0 instead
			// case 1:

			// break;
			//profile
			case 5:
			break;
		}
		// debug($result);
					// exit;
		if( isset($result['absence']) )unset($result['absence']);
		if( isset($result['resource']) )unset($result['resource']);
		if( isset($result['working']) ){
			$result['list'] = $result['working']['sum'];
			unset($result['working']);
		}
		if( isset($result['raw_workday']) )unset($result['raw_workday']);

		// debug($result); exit;
		if( empty($this->params['requested']) ){
			echo json_encode($result);
			die;
		} else {
			return $result;
		}
	}

	private function sumList($data, $pcList){
		$ratio = (!empty($this->employee_info['Company']['ratio']) && $this->employee_info['Company']['manage_hours'] == 0) ? $this->employee_info['Company']['ratio'] : 1;
		$result = array();
		foreach($pcList as $id){
			$res = $this->ProfitCenter->children($id, false, array('id'));
			//original data
			$result[$id] = array(
				// 'absence' => isset($data['absence'][$id]) ? $data['absence'][$id] : array(),
				'capacity_theoretical' => isset($data['resource']['capacity_theoretical'][$id]) ? $data['resource']['capacity_theoretical'][$id] : array(),
				// 'raw_capacity' => isset($data['resource']['raw_capacity'][$id]) ? $data['resource']['raw_capacity'][$id] : array(),
				'resource' => isset($data['resource']['sum'][$id]) ? $data['resource']['sum'][$id] : array(),
				'raw_workday' => isset($data['raw_workday'][$id]) ? $data['raw_workday'][$id] : array()
			);
			//2016-11-04 ratio.
			if(!empty($data['absence'][$id])){
				foreach ($data['absence'][$id] as $key => $value) {
					$result[$id]['absence'][$key] = $value*$ratio;
				}
			} else {
				$result[$id]['absence'] = array();
			}
			if(!empty($data['resource']['raw_capacity'][$id])){
				foreach ($data['resource']['raw_capacity'][$id] as $key => $value) {
					$result[$id]['raw_capacity'][$key] = $value*$ratio;
				}
			} else {
				$result[$id]['raw_capacity'] = array();
			}
			foreach($res as $r){
				$cid = $r['ProfitCenter']['id'];
				if( isset($data['absence'][$cid]) ){
					foreach($data['absence'][$cid] as $time => $val){
						if( !isset($result[$id]['absence'][$time]) ){
							$result[$id]['absence'][$time] = $val*$ratio;
						} else {
							$result[$id]['absence'][$time] += $val*$ratio;
						}
					}
				}
				if( isset($data['resource']['sum'][$cid]) ){
					foreach($data['resource']['sum'][$cid] as $time => $val){
						if( !isset($result[$id]['resource'][$time]) ){
							$result[$id]['resource'][$time] = $val;
						} else {
							$result[$id]['resource'][$time] += $val;
						}
					}
				}
				if( isset($data['resource']['raw_capacity'][$cid]) ){
					foreach($data['resource']['raw_capacity'][$cid] as $time => $val){
						if( !isset($result[$id]['raw_capacity'][$time]) ){
							$result[$id]['raw_capacity'][$time] = $val*$ratio;
						} else {
							$result[$id]['raw_capacity'][$time] += $val*$ratio;
						}
					}
				}
				if( isset($data['resource']['capacity_theoretical'][$cid]) ){
					foreach($data['resource']['capacity_theoretical'][$cid] as $time => $val){
						if( !isset($result[$id]['capacity_theoretical'][$time]) ){
							$result[$id]['capacity_theoretical'][$time] = $val;
						} else {
							$result[$id]['capacity_theoretical'][$time] += $val;
						}
					}
				}
				if( isset($data['raw_workday'][$cid]) ){
					foreach($data['raw_workday'][$cid] as $time => $val){
						if( isset($result[$id]['raw_workday'][$time]) ){
							$result[$id]['raw_workday'][$time] = $result[$id]['raw_workday'][$time] + $val;
						} else {
							$result[$id]['raw_workday'][$time] = $val;
						}
					}
				}
			}
			//calculate working and capacity
			foreach($result[$id]['raw_workday'] as $time => $val){
				$result[$id]['working'][$time] = count($val);
				if( isset($result[$id]['absence'][$time]) ){
					$result[$id]['capacity'][$time] = $result[$id]['raw_capacity'][$time] - $result[$id]['absence'][$time];
				} else {
					$result[$id]['capacity'][$time] = $result[$id]['raw_capacity'][$time];
				}
			}
			unset($result[$id]['raw_workday'], $result[$id]['raw_capacity']);
		}
		return $result;
	}

	private function getResources($start, $end, $conds = array()){
		$s = date('Y-m-d', $start);
		$e = date('Y-m-d', $end);
		$this->Employee->virtualFields['edata'] = "CONCAT( IF(start_date IS NULL OR start_date = '0000-00-00', '$s', start_date), '|', IF(end_date IS NULL OR end_date = '0000-00-00', '$e', end_date), '|', profit_center_id, '|', capacity_by_year )";
		$cid = $this->employee_info['Company']['id'];
		$conds = array_merge(array(
			'company_id' => $cid,
			'NOT' => $this->not($start, $end)
		), $conds);
        //unset($conds['id']); // xoa id
		$employees = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => $conds,
			'fields' => array('id', 'edata')
		));
		return $employees;
	}

	public function iCountWorksDay($id, $edata, $args){

		$cid = $this->employee_info['Company']['id'];
		$data = explode('|', $edata);
		$pc = !empty($data[2]) ? $data[2] : '';
        $_st = !empty($data[0]) ? $data[0] : '';
        $_en = !empty($data[1]) ? $data[1] : '';
		$userStart = $this->toInt($_st);
		$userEnd = $this->toInt($_en);

		$start = $args['start'];
		$end = $args['end'];

		$halfHoliday = $this->Holiday->get2($cid,$start, $end);
		$workDays = $args['work_days']['list'];
		$result = array();
		foreach ($workDays as $date => $v) {
			if( $userStart <= $date && $date <= $userEnd ){
				switch($args['view_by']){
					case 'day':
						$result[$date][$date] = 1;
					break;
					case 'week':
						$obj = new DateTime();
						$obj->setTimestamp($date);
						$obj->modify('monday this week');
						$week = $obj->getTimestamp();
						$result[$week][$date] = 1;
					break;
					default:
						$obj = new DateTime();
						$obj->setTimestamp($date);
						$obj->modify('first day of this month');
						$month = $obj->getTimestamp();
						if(!empty($halfHoliday[$date]) && (empty($halfHoliday[$date]['pm']) || empty($halfHoliday[$date]['am']))){
							$result[$month][$date] = 0.5;
						} else $result[$month][$date] = 1;
						// $result[$month][$date] = 1;
						
					break;
				}
			}
		}
		return array(
			'return' => $result,
			'action' => 'merge',
			'status' => 1,
			'key' => isset($params['is_resource']) && $params['is_resource'] ? $id : $pc
		);
	}

	public function iCountAbsenceForPC($id, $edata, $args){
		$data = explode('|', $edata);

		$viewBy = $args['view_by'];
		$start = $args['start'];
		$end = $args['end'];
		$absence = $args['absence'];

		$result = array();
		while($start <= $end){
			if( isset( $absence[$id][$start] ) ){
				if( !isset($result[$start]) ){
					$result[$start] = $absence[$id][$start];
				} else {
					$result[$start] += $absence[$id][$start];
				}
			}
			switch($args['view_by']){
				case 'day':
					$start = mktime(0, 0, 0, date('m', $start), date('d', $start)+1, date('Y', $start));
				break;
				case 'week':
					$obj = new DateTime();
					$obj->setTimestamp($start);
					$obj->modify('next monday');
					$start = $obj->getTimestamp();
				break;
				default:
					$obj = new DateTime();
					$obj->setTimestamp($start);
					$obj->modify('first day of next month');
					$start = $obj->getTimestamp();
				break;
			}
		}
		return array(
			'status' => 1,
			'key' => !empty($data[2]) ? $data[2] : '',
			'action' => 'sumArray',
			'return' => $result
		);
	}

	public function toInt($sqlDate){
		return strtotime($sqlDate . ' 00:00:00');
	}

	// public function iCountCapacityForResource($id, $edata, $args){

	// }
	//capacity for resources
	public function iCountResource($id, $edata, $args){
		$data = explode('|', $edata);
		$pc = !empty($data[2]) ? $data[2] : '';
        $_st = !empty($data[0]) ? $data[0] : '';
        $_en = !empty($data[1]) ? $data[1] : '';
		$userStart = $this->toInt($_st);
		$userEnd = $this->toInt($_en);
		$isResource = isset($args['is_resource']);
		//multi resource
		$mIds = isset($args['mIds']) ? $args['mIds'] : array();
		$mCapacity = isset($args['mCapacity']) ? $args['mCapacity'] : array();
		$isMulti = in_array($id, $mIds);

		$start = $args['start'];
		$end = $args['end'];
		$cid = $this->employee_info['Company']['id'];
		$halfHoliday = $this->Holiday->get2($cid,$start, $end);
		$workDays = $args['work_days']['list'];
		$result = array(
			'sum' => array(),
			'raw_capacity' => array(),
			'capacity_theoretical' => array()
		);
		foreach ($workDays as $date => $v) {
			if( $userStart <= $date && $date <= $userEnd ){
				switch($args['view_by']){
					case 'day':
						if( $isMulti ){
							$result['raw_capacity'][$date] = floatval(isset($mCapacity[$id][$date]) ? $mCapacity[$id][$date] : 0);
						}
						// else if( !$isResource && $isMulti ){
						// 	$result['raw_capacity'][$date] = 0;
						// }
						else {
							$result['raw_capacity'][$date] = 1;
						}
						if( !$isResource && !$isMulti ){
							$result['sum'][$date] = 1;
						}
					break;
					case 'week':
						$obj = new DateTime();
						$obj->setTimestamp($date);
						$obj->modify('monday this week');
						$week = $obj->getTimestamp();
						if( $isMulti ){
							$result['raw_capacity'][$week] = floatval(isset($mCapacity[$id][$week]) ? $mCapacity[$id][$week] : 0);
						}
						// else if( !$isResource && $isMulti ){
						// 	$result['raw_capacity'][$week] = 0;
						// }
						else if( !isset($result['raw_capacity'][$week]) ){
							$result['raw_capacity'][$week] = 1;
						} else {
							$result['raw_capacity'][$week]++;
						}
						if( !$isResource && !$isMulti ){
							$result['sum'][$week] = 1;
						}
					break;
					default:
						$obj = new DateTime();
						$obj->setTimestamp($date);
						$obj->modify('first day of this month');
						$month = $obj->getTimestamp();
						if( $isMulti ){
							$result['raw_capacity'][$month] = floatval(isset($mCapacity[$id][$month]) ? $mCapacity[$id][$month] : 0);
						}
						// else if( !$isResource && $isMulti ){
						// 	$result['raw_capacity'][$month] = 0;
						// }
						else if( !isset($result['raw_capacity'][$month]) ){
							$result['raw_capacity'][$month] = 1;
						} else {
							
							if(!empty($halfHoliday[$date]) && (empty($halfHoliday[$date]['pm']) || empty($halfHoliday[$date]['am']))){
								$result['raw_capacity'][$month] += 0.5; 
							}else $result['raw_capacity'][$month]++;
						}
						if( !$isResource && !$isMulti ){
							$result['sum'][$month] = 1;
						}
						//theo capacity
                        $_cpy = !empty($data[3]) ? $data[3] : 0;
						$cpy = floatval($_cpy);
						// if(!empty($result['capacity_theoretical'])){
						if( $cpy ){
							$result['capacity_theoretical'][$month] = round($cpy * $this->companyConfigs['capacity_by_month_' . date('n', $month)] / 100, 2);
						}
						 else {
							$result['capacity_theoretical'][$month] = 0;
						}
						// }
					break;
				}
			}
		}

		// debug($result); exit;
		return array(
			'status' => 1,
			'key' => $isResource ? $id : $pc,
			'action' => 'sumCapacity',
			'return' => $result
		);
	}
	//only by month
	public function iCalculateTheoCapacity($id, $edata){
		$data = explode('|', $edata);
		$capacity = array();
        $_cpy = !empty($data[3]) ? $data[3] : '';
		$cpy = floatval($_cpy);
		if( !$cpy ){
			return;
		}
		for($m = 1; $m <= 12; $m++ ){
			$capacity[$m] = round($cpy * $this->companyConfigs['capacity_by_month_' . $m] / 100, 2);
		}
		return array('status' => 1, 'return' => $capacity);
	}
	// public function iCalculateCapacity($id, $edata, $args){

	// }
	private function not($startDate, $endDate, $prefix = 'Employee'){
		$not = array(
			'OR' => array(
				array(
					$prefix.'.end_date <>' => '0000-00-00',
					$prefix.'.end_date IS NOT NULL',
					'UNIX_TIMESTAMP('.$prefix.'.end_date) < '=> $startDate
				),
				array(
					$prefix.'.start_date <>' => '0000-00-00',
					$prefix.'.start_date IS NOT NULL',
					'UNIX_TIMESTAMP('.$prefix.'.start_date) > '=> $endDate
				)
			)
		);
		return $not;
	}

	private function getAbsences($employees, $startDate, $endDate, $viewBy = 'month'){
		$zone = date('P');
		$total_absence = 'SUM( IF(response_am = "validated", 0.5, 0) + IF(response_pm = "validated", 0.5, 0) )';
		switch($viewBy){
			case 'day':
				$grouper = "DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(`date`), @@session.time_zone, '$zone'), '%Y-%m-%d')";
			break;
			case 'week':
				$grouper = "DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(`date`), @@session.time_zone, '$zone'), '%v-%Y')";
			break;
			default:
				$grouper = "DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(`date`), @@session.time_zone, '$zone'), '%Y-%m')";
			break;
		}
		$emp = count($employees) > 0 ? 'employee_id IN (' . implode(',', $employees) . ')' : '';
		$absences = $this->AbsenceRequest->query(
			"SELECT employee_id, $total_absence as total_absence, $grouper as grouper FROM absence_requests AS AbsenceRequest WHERE $emp AND (`date` BETWEEN $startDate AND $endDate) AND (response_am = 'validated' OR response_pm = 'validated') GROUP BY employee_id, grouper"
		);

		$result = array();
		foreach($absences as $absence){
			$eid = $absence['AbsenceRequest']['employee_id'];
			$g = $absence[0]['grouper'];
			$value = $absence[0]['total_absence'];
			switch($viewBy){
				case 'day':
					$g = strtotime($g . ' 00:00:00');
				break;
				case 'week':
					$d = explode('-', $g);
					$obj = new DateTime();
					$obj->setISODate($d[1], $d[0]);
					$obj->modify('monday this week');
					$obj->setTime(0, 0, 0);
					$g = $obj->getTimestamp();
				break;
				default:
					$g = strtotime($g . '-01 00:00:00');
				break;
			}
			$result[$eid][$g] = $value;
		}
		return $result;
	}

	private function _getAbsences($employees, $startDate, $endDate, $viewBy = 'month'){
		$this->AbsenceRequest->virtualFields['total_absence'] = 'SUM( IF(response_am = "validated", 0.5, 0) + IF(response_pm = "validated", 0.5, 0) )';
		switch($viewBy){
			case 'day':
				$this->AbsenceRequest->virtualFields['grouper'] = 'FROM_UNIXTIME(`date`, "%Y-%m-%d")';
			break;
			case 'week':
				$this->AbsenceRequest->virtualFields['grouper'] = 'CONCAT(WEEKOFYEAR(FROM_UNIXTIME(`date`)), "-", FROM_UNIXTIME(`date`, "%Y"))';
			break;
			default:
				$this->AbsenceRequest->virtualFields['grouper'] = 'FROM_UNIXTIME(`date`, "%Y-%m")';
			break;
		}
		$absences = $this->AbsenceRequest->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employees,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'OR' => array(
					'response_am' => 'validated',
					'response_pm' => 'validated'
				)
			),
			'fields' => array(
				'employee_id',
				'grouper',
				'total_absence'
			),
			'group' => array(
				'employee_id',
				'grouper'
			)
		));
		$result = array();
		foreach($absences as $absence){
			$eid = $absence['AbsenceRequest']['employee_id'];
			$g = $absence['AbsenceRequest']['grouper'];
			$value = $absence['AbsenceRequest']['total_absence'];
			switch($viewBy){
				case 'day':
					$g = strtotime($g . ' 00:00:00');
				break;
				case 'week':
					$d = explode('-', $g);
					$obj = new DateTime();
					$obj->setISODate($d[1], $d[0]);
					$obj->modify('monday this week');
					$obj->setTime(0, 0, 0);
					$g = $obj->getTimestamp();
				break;
				default:
					$g = strtotime($g . '-01 00:00:00');
				break;
			}
			$result[$eid][$g] = $value;
		}
		return $result;
	}

	private function __getAbsences($employees, $startDate, $endDate, $viewBy = 'month'){
		$this->AbsenceRequest->virtualFields['total_absence'] = 'IF(response_am = "validated", 0.5, 0) + IF(response_pm = "validated", 0.5, 0)';
		$absences = $this->AbsenceRequest->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employees,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'OR' => array(
					'response_am' => 'validated',
					'response_pm' => 'validated'
				)
			),
			'fields' => array(
				'employee_id',
				'total_absence',
				'date'
			)
		));
		$result = array();
		foreach($absences as $absence){
			$eid = $absence['AbsenceRequest']['employee_id'];
			$value = $absence['AbsenceRequest']['total_absence'];
			$g = $absence['AbsenceRequest']['date'];
			switch($viewBy){
				case 'day':
				break;
				case 'week':
					$obj = new DateTime();
					$obj->setTimestamp($g);
					$obj->modify('monday this week');
					$g = $obj->getTimestamp();
				break;
				default:
					$obj = new DateTime();
					$obj->setTimestamp($g);
					$obj->modify('first day of this month');
					$g = $obj->getTimestamp();
				break;
			}
			if( !isset($result[$eid][$g]) ){
				$result[$eid][$g] = $value;
			} else {
				$result[$eid][$g] += $value;
			}
		}
		return $result;
	}

	/*
	* return: {time : number of days, ...}
	* month: time = first work day of month
	* week: time = monday
	* day: time = date
	* exclude off/holiday days
	* sum by viewBY property
	*/
	private function getWorkingDays($startDate, $endDate, $viewBy = 'month' ){
		//$cid = $this->employee_info['Company']['id'];
		$result = array(
			'sum' => array(),
			'list' => array()
		);
		$cid = $this->employee_info['Company']['id'];
		$offDays = $this->getOffDays($startDate, $endDate);
		$halfHoliday = $this->Holiday->get2($cid,$startDate, $endDate);
		while( $startDate <= $endDate ){
			switch ($viewBy) {
				case 'day':
					$current = date('d-m-Y', $startDate);
					if( !in_array($current, $offDays) ){
						$result['sum'][$startDate] = 1;
						$result['list'][$startDate] = 1;
					}
					$startDate = mktime(0, 0, 0, date('m', $startDate), date('d', $startDate)+1, date('Y', $startDate));
				break;
				case 'week':
					$start = $startDate;
					$obj = new DateTime();
					$obj->setTimestamp($start);
					$obj->modify('sunday this week');
					$end = $obj->getTimestamp();

					$result['sum'][$startDate] = 0;
					while($start <= $end){
						$current = date('d-m-Y', $start);
						if( !in_array($current, $offDays) ){
							$result['sum'][$startDate]++;
							$result['list'][$start] = 1;
						}
						
						$start = mktime(0, 0, 0, date('m', $start), date('d', $start)+1, date('Y', $start));
					}
					//next monday
					$obj->modify('+1 day');
					$startDate = $obj->getTimestamp();
				break;
				default:
					$start = $startDate;
					$obj = new DateTime();
					$obj->setTimestamp($start);
					$obj->modify('last day of this month');
					$end = $obj->getTimestamp();

					$result['sum'][$startDate] = 0;
					while($start <= $end){
						$current = date('d-m-Y', $start);
						if( !in_array($current, $offDays) ){
							$result['sum'][$startDate]++;
							$result['list'][$start] = 1;
						}
						if(!empty($halfHoliday[$start]) && (empty($halfHoliday[$start]['pm']) || empty($halfHoliday[$start]['am']))){
							$result['sum'][$startDate] += 0.5;
							$result['list'][$start] = 0.5;
						}
						$start = mktime(0, 0, 0, date('m', $start), date('d', $start)+1, date('Y', $start));
					}
					//next month
					$obj->modify('first day of next month');
					$startDate = $obj->getTimestamp();
				break;
			}
		}
		return $result;
	}

	private function getWorkDays($startDate, $endDate){
		$result = array();
		$offDays = $this->getOffDays($startDate, $endDate);
		while( $startDate <= $endDate ){
			$current = date('d-m-Y', $startDate);
			if( !in_array($current, $offDays) ){
				$result[$startDate] = 1;
			}
			$startDate = mktime(0, 0, 0, date('m', $startDate), date('d', $startDate)+1, date('Y', $startDate));
		}
		return $result;
	}

	/*
	* return: [time, ...]
	*
	*/
	private function getOffDays($startDate, $endDate){
		$cid = $this->employee_info['Company']['id'];
		$workdays = $this->Workday->getOptions($cid);
		$result = $this->Holiday->get($cid, $startDate, $endDate, 'd-m-Y');
		while ($startDate <= $endDate){
			$_start = strtolower(date('l', $startDate));
			$_end = strtolower(date('l', $endDate));
			if($workdays[$_start] == 0){
				$result[] = date('d-m-Y', $startDate);
			}
			$startDate = mktime(0, 0, 0, date('m', $startDate), date('d', $startDate)+1, date('Y', $startDate));
		}
		return array_unique($result);
	}

	/*
	* Prepare period settings, call once
	*/
	private $periods = array();
	private function getPeriods($start, $end){
		$this->Period->virtualFields['data'] = 'CONCAT(`start`, "|", `end`)';
		$this->periods = $this->Period->find('list', array(
			'recursive' => -1,
			'time BETWEEN ? AND ?' => array($start, $end),
			'fields' => array('time', 'data')
		));
	}

	/*
	* Period converter, only by month
	* input: time (int)
	* output: time (int, start or end of period, can be off-day, configured in table periods, enabled in company_configs[staffing_by_month_multiple])
	*/
	private function toPeriod($time, $start = 1){
		//set the input to start of month (01/01)
		$obj = new DateTime();
		$obj->setTimestamp($time);
		$obj->modify('first day of this month');
		$key = $obj->getTimestamp();
		if( !$start ){
			$obj->modify('last day of this month');
			return $obj->getTimestamp();
		}
		if( $this->companyConfigs['staffing_by_month_multiple'] ){
			//get period
			if( isset($this->periods[$key]) ){
				$data = explode('|', $this->periods[$key]);
				$time = $start ? $data[0] : $data[1];
			}
		}
		return $time;
	}

	public function export(){
		if( empty($this->data) ){
			die;
		}
		$this->data['header'] = json_decode($this->data['header']);
		$this->data['data'] = json_decode($this->data['data']);
		$this->set('data', $this->data);
		$this->layout = '';
	}
}
