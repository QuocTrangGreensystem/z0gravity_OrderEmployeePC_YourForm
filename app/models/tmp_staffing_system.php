<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class TmpStaffingSystem extends AppModel {

    var $name = 'TmpStaffingSystem';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
   // public $belongsTo = array(
//        'Project' => array(
//            'className' => 'Project',
//            'foreignKey' => 'project_id',
//            'conditions' => '',
//            'fields' => '',
//            'order' => ''
//        )
//    );
	public function checkStaffingByDate($date = null, $company = null, $tickRebuildStaffing = false)
	{
		if($date !== null)
		{
			$date = date('m-Y',$date);
			$results = $this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company,
					'FROM_UNIXTIME(date, "%m-%Y")' => $date
				),
				'fields' => array('project_id', 'activity_id')
			));
			$projects = Set::ClassicExtract($results,'{n}.TmpStaffingSystem.project_id');
			$activities = Set::ClassicExtract($results,'{n}.TmpStaffingSystem.activity_id');
			if($tickRebuildStaffing)
			{
				$this->tickRebuildStaffing($projects, $activities);
			}
			return $results;
		}
	}
	public function tickRebuildStaffing($projects, $activities)
	{	
		//$ActivityTask = ClassRegistry::init('ActivityTask');
		//$ProjectTask = ClassRegistry::init('ProjectTask');
		$Activity = ClassRegistry::init('Activity');
		$Project = ClassRegistry::init('Project');
		if(!empty($projects))
		{
			$Project->updateAll(
				array('Project.rebuild_staffing' => 1),
				array('Project.id' => $projects)
			);
		}
		if(!empty($activities))
		{
			$Activity->updateAll(
				array('Activity.rebuild_staffing' => 1),
				array('Activity.id' => $activities)
			);
		}
	}
	function checkStaffingOfEmployee($id,$company,$checkRebuilStaffing = false)
	{
		$projects = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'TmpStaffingSystem.model_id' => $id,
				'model' => 'employee',
				'project_id <>' => 0,
				'TmpStaffingSystem.company_id' => $company
			),
			'fields'=> array('project_id','project_id'),
			//'joins' => $_joins,
			//'group'=>$_group
		));
		$activities = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'TmpStaffingSystem.model_id' => $id,
				'model' => 'employee',
				'activity_id <>' => 0,
				'project_id' => 0,
				'TmpStaffingSystem.company_id' => $company
			),
			'fields'=> array('activity_id','activity_id'),
			//'joins' => $_joins,
			//'group'=>$_group
		));
		if($checkRebuilStaffing == true)
		{
			$this->tickRebuildStaffing($projects, $activities);
		}
		$count = count($projects);
		$count += count($activities);
		$results = array(
			'Project' => $projects,
			'Activity' => $activities,
			'count' => $count
		);
		return $results;
	}
	/*-------------------
	@Get lists consumed by day
	INPUT : conditions
	OUTPUT: Array values
	-------------------*/
	function getConsumedByDateFromDataStaffing($startDate, $endDate, $company, $profit_center, $employees = null)
	{
		$_conditions = array(
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'model' => 'profit_center',
			'model_id' => $profit_center,
			'TmpStaffingSystem.company_id' => $company
		);
		if(!empty($employees))
		{
			$_conditions['model'] = 'employee';
			$_conditions['model_id'] = $employees;
		}
		
		$_fields=array(
			'SUM(TmpStaffingSystem.consumed) as consumed',
			'TmpStaffingSystem.date'
		);
		//$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		$_group=array('TmpStaffingSystem.date');
		$results = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $_conditions,
			'fields'=>$_fields,
			'order' => array(),
			'group'=>$_group
		));
		$results = Set::combine($results,'{n}.TmpStaffingSystem.date','{n}.0.consumed');
		return $results; 
	}
	/*-------------------
	@Get lists consumed by day
	INPUT : conditions
	OUTPUT: Array values
	-------------------*/
	function getConsumedByDateFromDataStaffingAllEmployees($startDate, $endDate, $company, $profit_center, $employees = null)
	{
		$_conditions = array(
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'model' => 'profit_center',
			'model_id' => $profit_center,
			'TmpStaffingSystem.company_id' => $company
		);
		if(!empty($employees))
		{
			$_conditions['model'] = 'employee';
			$_conditions['model_id'] = $employees;
		}
		
		$_fields=array(
			'SUM(TmpStaffingSystem.consumed) as consumed',
			'TmpStaffingSystem.date',
			'TmpStaffingSystem.model_id',
		);
		$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		// $_group=array('TmpStaffingSystem.date');
		$results = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $_conditions,
			'fields'=>$_fields,
			'order' => array(),
			'group'=>$_group
		));
		// $results = Set::combine($results,'{n}.TmpStaffingSystem.date','{n}.0.consumed');
		return $results; 
	}
	
	/*-------------------
	@Get lists consumed by day for list employees
	INPUT : conditions
	OUTPUT: Array values
	-------------------*/
	function getConsumedByDateFromDataStaffingForEmployees($startDate, $endDate, $company, $profit_center, $employees = null)
	{
		$_conditions = array(
			'date BETWEEN ? AND ?' => array($startDate, $endDate),
			'model' => 'profit_center',
			'model_id' => $profit_center,
			'TmpStaffingSystem.company_id' => $company
		);
		if(!empty($employees))
		{
			$_conditions['model'] = 'employee';
			$_conditions['model_id'] = $employees;
		}
		
		$_fields=array(
			'SUM(TmpStaffingSystem.consumed) as consumed',
			'TmpStaffingSystem.date',
			'TmpStaffingSystem.model_id',
		);
		//$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		$_group=array('TmpStaffingSystem.model_id', 'TmpStaffingSystem.date');
		$results = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $_conditions,
			'fields'=>$_fields,
			'order' => array(),
			'group'=>$_group
		));
		// $results = Set::combine($results,'{n}.TmpStaffingSystem.date','{n}.0.consumed');
		return $results; 
	}
	/**
	 *Function staffing by profile
	 *created ViN
	 *date 08/06/2015
	 **/
	function staffingProfile($id,$employeeName,$isActivity = false)
	{
		$dayEstablished = $employeeName['day_established'];
		$_fields=array(
					'TmpStaffingSystem.model_id',
					'TmpStaffingSystem.model',
					'project_id',
					'activity_id',
					'TmpStaffingSystem.company_id',
					'TmpStaffingSystem.date',
					'SUM(TmpStaffingSystem.estimated) as estimated',
					//'Profile.name'
				);
		$_joins = array(
					array(
						'table' => 'profiles',
						'alias' => 'Profile',
						'type' => 'LEFT',
						'foreignKey' => 'model_id',
						'conditions'=> array(
							'TmpStaffingSystem.model_id = Profile.id', 
						)
					)
				);
		$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		if($isActivity == true)
		{
			$_conditions = array(
				'TmpStaffingSystem.activity_id' => $id,
				'model' => 'profile',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			);
		}
		else
		{
			$_conditions = array(
				'TmpStaffingSystem.project_id' => $id,
				'model' => 'profile',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			);
		}
		$getDatas = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $_conditions,
			'fields'=>$_fields,
			//'joins' => $_joins,
			'order' => array('TmpStaffingSystem.date'),
			'group'=>$_group
		));
		//CAPACITY
		/*--------------------------------------------------------
		doan nay goi xuong function calculateStaffingByProfiles,
		do bi ep dealine nen chua kip sua, co thoi gian ranh ngoi sua sau.
		--------------------------------------------------------*/
		$Profile = ClassRegistry::init('Profile');
		$ProfileValue = ClassRegistry::init('ProfileValue');
		$lastYear = date('Y', time()) - 1;
		$nextYear = date('Y', time()) + 5;
		$profiles = $Profile->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $employeeName['company_id']),
			'fields' => array('id', 'name', 'capacity_by_year')
		));
		$profileIds = !empty($profiles) ? Set::classicExtract($profiles, '{n}.Profile.id') : array();
		$profiles = Set::combine($profiles, '{n}.Profile.id', '{n}.Profile');
		$profileValues = $ProfileValue->find('list', array(
			'recursive' => -1,
			'conditions' => array('profile_id' => $profileIds, 'year BETWEEN ? AND ?' => array($lastYear, $nextYear)),
			'fields' => array('year', 'value', 'profile_id'),
			'group' => array('profile_id', 'year')
		));
		//END
		$count = count($getDatas);
		$i = 0;
		$profileOfProject = array();
		if(!empty($getDatas)){
			foreach($getDatas as $key=>$getData){
				//$dx = array_merge($getData['Activity'],$getData['TmpStaffingSystem'],$getData[0]);						
				$dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
				$i++;
				if($i==1)
				$startDate = $dx['date'];
				elseif($i == $count)
				$endDate = $dx['date'];					
				$key = $dx['model_id'];
				if(!isset($data[$key]))
				{
					$data[$key]['id'] = $key;
					$data[$key]['name'] = isset($profiles[$key]['name']) ? $profiles[$key]['name'] : 'Not Affected';
					$data[$key]['data'] = array();
					$profileOfProject[] = $key ;
				}
				
				$data[$key]['data'][$dx['date']]['date'] = $dx['date'];
				$data[$key]['data'][$dx['date']]['validated'] = $dx['estimated'];
				$data[$key]['data'][$dx['date']]['consumed'] = 0;
				$data[$key]['data'][$dx['date']]['capacity'] = 0;
				$data[$key]['data'][$dx['date']]['employee'] = 0;
				$data[$key]['data'][$dx['date']]['resource_theoretical'] = 0;
				$data[$key]['data'][$dx['date']]['fte'] = 0;
			}
		}
		$staffings = $data;
		//GET RESOURCE
		$Employee = ClassRegistry::init('Employee');
		$ActivityTask = ClassRegistry::init('ActivityTask');
		$allEmp = $Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array(	
				'NOT' => $ActivityTask->notConditions($startDate, $endDate),
				'Employee.profile_id'=>$profileOfProject,
			),
			'fields' => array('Employee.id','Employee.start_date','Employee.end_date','Employee.profile_id')
		));
		$dataEmployee = array();
		foreach($allEmp as $_index=>$_data)
		{
			$_data = $_data['Employee'];
			if(strtotime($_data['start_date']) < $dayEstablished)
			{
				$_data['start_date'] = $dayEstablished;
			}
			$startTmp = strtotime($_data['start_date']);
			$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
			$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
			$dataEmployee[$_data['profile_id']][$_data['id']] = $_data;
		}
		//END
		//GET TOTAL WORKLOAD FOR PC
		$getDataTotal = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				//'TmpStaffingSystem.project_id' => $project_id,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'TmpStaffingSystem.model_id' => $profileOfProject,
				'model' => 'profile',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			),
			'fields'=>$_fields,
			//'joins' => $_joins,
			'order' => array('TmpStaffingSystem.date'),
			'group'=>$_group
		));
		if(!empty($getDataTotal)){
			foreach($getDataTotal as $key=>$getData){						
				$dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
				$key = $dx['date'];
				$dataTotal[$key][$dx['model_id']] = $dx['estimated'];
			}
		}
		$_startDate = $startDate;
		//$_startDate = mktime(0, 0, 0, date("m", $startDate)-1, date("d", $startDate), date("Y", $startDate));
		//$endDate = mktime(0, 0, 0, date("m", $endDate)+1, date("d", $endDate), date("Y", $endDate));
		while($_startDate <= $endDate)
		{
			foreach($profileOfProject as $key)
			{
				if(!isset($staffings[$key]['data'][$_startDate]) || empty($staffings[$key]['data'][$_startDate]))
				{
					$staffings[$key]['data'][$_startDate] = array(
						'date' => $_startDate,
						'validated' => 0,
						'consumed' => 0,
						'totalWorkload' => 0,
						'employee' => 0,
						'resource_theoretical' => 0,
						'fte' => 0
					);
				}
				$totalWorkload = isset($dataTotal[$_startDate][$key]) ? $dataTotal[$_startDate][$key] : 0 ;
				list($capacityByMonth,$FTE,$resource_theoretical) = $this->caculateCapacityByProfile($key,$totalWorkload,$profiles,$profileValues,$_startDate);
				$staffings[$key]['data'][$_startDate]['capacity'] = $capacityByMonth;
				$staffings[$key]['data'][$_startDate]['resource_theoretical'] = $resource_theoretical;
				$staffings[$key]['data'][$_startDate]['fte'] += $FTE;
				$staffings[$key]['data'][$_startDate]['totalWorkload'] = $totalWorkload ;
				//Calculate resource
				$pros = 0;
				if(!empty($dataEmployee[$key]))
				{
					foreach( $dataEmployee[$key] as $_key => $val )
					{
						if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
						{
							$pros ++;
						}
						else
						{
							if(date('d-m-Y',$val['start_date']) == date('d-m-Y',$_startDate))
							{
								//start_date between of month
								$pros ++;
							}
						}
					}
				}
				$staffings[$key]['data'][$_startDate]['employee'] = $pros;
				//end
			}
			$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate)); 
		}
		ksort($staffings);
		return array_values($staffings);
		//0934059899 : Hao
		//END
	}
	function calculateStaffingByProfiles($listProfiles, $startDate, $endDate, $employeeName)
	{
		$dayEstablished = $employeeName['day_established'];
		//CAPACITY
		$Profile = ClassRegistry::init('Profile');
		$ProfileValue = ClassRegistry::init('ProfileValue');
		$lastYear = date('Y', time()) - 1;
		$nextYear = date('Y', time()) + 5;
		$profiles = $Profile->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $employeeName['company_id']),
			'fields' => array('id', 'name', 'capacity_by_year')
		));
		$profileIds = !empty($profiles) ? Set::classicExtract($profiles, '{n}.Profile.id') : array();
		$profiles = Set::combine($profiles, '{n}.Profile.id', '{n}.Profile');
		$profileValues = $ProfileValue->find('list', array(
			'recursive' => -1,
			'conditions' => array('profile_id' => $profileIds, 'year BETWEEN ? AND ?' => array($lastYear, $nextYear)),
			'fields' => array('year', 'value', 'profile_id'),
			'group' => array('profile_id', 'year')
		));
		//END
		$i = 0;
		if(!empty($listProfiles))
		{
			$profileOfProject = $listProfiles;
		}
		else
		{
			$profileOfProject = $profileIds;
		}
		
		//GET RESOURCE
		$Employee = ClassRegistry::init('Employee');
		$ActivityTask = ClassRegistry::init('ActivityTask');
		$allEmp = $Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array(	
				'NOT' => $ActivityTask->notConditions($startDate, $endDate),
				'Employee.profile_id'=>$profileOfProject,
			),
			'fields' => array('Employee.id','Employee.start_date','Employee.end_date','Employee.profile_id')
		));
		$dataEmployee = array();
		foreach($allEmp as $_index=>$_data)
		{
			$_data = $_data['Employee'];
			if(strtotime($_data['start_date']) < $dayEstablished)
			{
				$_data['start_date'] = $dayEstablished;
			}
			$startTmp = strtotime($_data['start_date']);
			$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
			$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
			$dataEmployee[$_data['profile_id']][$_data['id']] = $_data;
		}
		//END
		//GET TOTAL WORKLOAD FOR PC
		$_fields=array(
					'TmpStaffingSystem.model_id',
					'TmpStaffingSystem.model',
					'project_id',
					'activity_id',
					'TmpStaffingSystem.company_id',
					'TmpStaffingSystem.date',
					'SUM(TmpStaffingSystem.estimated) as estimated',
					//'Profile.name'
				);

		$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		$getDataTotal = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				//'TmpStaffingSystem.project_id' => $project_id,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				'TmpStaffingSystem.model_id' => $profileOfProject,
				'model' => 'profile',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			),
			'fields'=>$_fields,
			'order' => array('TmpStaffingSystem.date'),
			'group'=>$_group
		));
		if(!empty($getDataTotal)){
			foreach($getDataTotal as $key=>$getData){						
				$dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
				$key = $dx['date'];
				$dataTotal[$key][$dx['model_id']] = $dx['estimated'];
			}
		}
		$_startDate = $startDate;
		$staffings = array();
		while($_startDate <= $endDate)
		{
			foreach($profileOfProject as $key)
			{
				if(!isset($staffings[$key]['data'][$_startDate]) || empty($staffings[$key]['data'][$_startDate]))
				{
					$staffings[$key]['data'][$_startDate] = array(
						'date' => $_startDate,
						'validated' => 0,
						'consumed' => 0,
						'totalWorkload' => 0,
						'employee' => 0,
						'resource_theoretical' => 0,
						'fte' => 0
					);
					//$totalWorkload = 0;
				}
				else
				{
					//$totalWorkload = $staffings[$key]['data'][$_startDate]['totalWorkload'];
				}
				$totalWorkload = isset($dataTotal[$_startDate][$key]) ? $dataTotal[$_startDate][$key] : 0 ;
				list($capacityByMonth,$FTE,$resource_theoretical) = $this->caculateCapacityByProfile($key,$totalWorkload,$profiles,$profileValues,$_startDate);
				$staffings[$key]['data'][$_startDate]['capacity'] = $capacityByMonth;
				$staffings[$key]['data'][$_startDate]['resource_theoretical'] = $resource_theoretical;
				$staffings[$key]['data'][$_startDate]['fte'] += $FTE;
				$staffings[$key]['data'][$_startDate]['totalWorkload'] = $totalWorkload ;
				//Calculate resource
				$pros = 0;
				if(!empty($dataEmployee[$key]))
				{
					foreach( $dataEmployee[$key] as $_key => $val )
					{
						if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
						{
							$pros ++;
						}
						else
						{
							if(date('d-m-Y',$val['start_date']) == date('d-m-Y',$_startDate))
							{
								//start_date between of month
								$pros ++;
							}
						}
					}
				}
				$staffings[$key]['data'][$_startDate]['employee'] = $pros;
				//end
			}
			$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate)); 
		}
		return $staffings;
	}
	function caculateCapacityAllProfile($startDate, $endDate, $employeeName)
	{
		$dayEstablished = $employeeName['day_established'];
		$_fields=array(
					'TmpStaffingSystem.model_id',
					//'TmpStaffingSystem.model',
					//'project_id',
					//'activity_id',
					//'TmpStaffingSystem.company_id',
					'TmpStaffingSystem.date',
					'SUM(TmpStaffingSystem.estimated) as estimated',
					//'Profile.name'
				);
		$_group=array('TmpStaffingSystem.model_id','TmpStaffingSystem.date');
		/*$_group=array('TmpStaffingSystem.date');*/
		//CAPACITY
		$Profile = ClassRegistry::init('Profile');
		$ProfileValue = ClassRegistry::init('ProfileValue');
		$lastYear = date('Y', time()) - 1;
		$nextYear = date('Y', time()) + 5;
		$profiles = $Profile->find('all', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $employeeName['company_id']),
			'fields' => array('id', 'name', 'capacity_by_year')
		));
		$profileIds = !empty($profiles) ? Set::classicExtract($profiles, '{n}.Profile.id') : array();
		$profiles = Set::combine($profiles, '{n}.Profile.id', '{n}.Profile');
		$profileValues = $ProfileValue->find('list', array(
			'recursive' => -1,
			'conditions' => array('profile_id' => $profileIds, 'year BETWEEN ? AND ?' => array($lastYear, $nextYear)),
			'fields' => array('year', 'value', 'profile_id'),
			'group' => array('profile_id', 'year')
		));
		//END
		
		//GET RESOURCE
		$Employee = ClassRegistry::init('Employee');
		$ActivityTask = ClassRegistry::init('ActivityTask');
		$allEmp = $Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array(	
				'NOT' => $ActivityTask->notConditions($startDate, $endDate),
				'Employee.profile_id'=>$profileIds,
			),
			'fields' => array('Employee.id','Employee.start_date','Employee.end_date','Employee.profile_id')
		));
		$dataEmployee = array();
		foreach($allEmp as $_index=>$_data)
		{
			$_data = $_data['Employee'];
			if(strtotime($_data['start_date']) < $dayEstablished)
			{
				$_data['start_date'] = $dayEstablished;
			}
			$startTmp = strtotime($_data['start_date']);
			$_data['start_date'] = $startTmp < $dayEstablished ? $dayEstablished : $startTmp;
			$_data['end_date'] = $_data['end_date'] == '0000-00-00' || $_data['end_date'] == null ?  $endDate : strtotime($_data['end_date']);
			$dataEmployee[$_data['profile_id']][$_data['id']] = $_data;
		}
		//END
		//GET TOTAL WORKLOAD FOR PC
		
		$getDataTotal = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				//'TmpStaffingSystem.project_id' => $project_id,
				'date BETWEEN ? AND ?' => array($startDate, $endDate),
				//'TmpStaffingSystem.model_id' => $profileIds,
				'model' => 'profile',
				'TmpStaffingSystem.company_id' => $employeeName['company_id']
			),
			'fields'=>$_fields,
			//'joins' => $_joins,
			'order' => array('TmpStaffingSystem.date'),
			'group'=>$_group
		));
		
		$dataTotal = array();
		if(!empty($getDataTotal)){
			foreach($getDataTotal as $key=>$getData){						
				$dx = array_merge($getData['TmpStaffingSystem'],$getData[0]);
				$key = $dx['date'];
				$dataTotal[$key][$dx['model_id']] = $dx['estimated'];
			}
		}
		$_startDate = $startDate;
		$staffings = array();
		while($_startDate <= $endDate)
		{
			if(!isset($staffings[$_startDate]) || empty($staffings[$_startDate]))
			{
				$staffings[$_startDate] = array(
					'date' => $_startDate,
					'validated' => 0,
					'consumed' => 0,
					'capacity' => 0,
					'totalWorkload' => 0,
					'employee' => 0,
					'resource_theoretical' => 0,
					'fte' => 0
				);

			}
			foreach($profileIds as $key)
			{
				$totalWorkload = isset($dataTotal[$_startDate][$key]) ? $dataTotal[$_startDate][$key] : 0 ;
				
				list($capacityByMonth,$FTE,$resource_theoretical) = $this->caculateCapacityByProfile($key,$totalWorkload,$profiles,$profileValues,$_startDate);
				$staffings[$_startDate]['capacity'] += $capacityByMonth;
				$staffings[$_startDate]['resource_theoretical'] += $resource_theoretical;
				$staffings[$_startDate]['fte'] += $FTE;
				$staffings[$_startDate]['totalWorkload'] += $totalWorkload ;
				//Calculate resource
				$pros = 0;
				if(!empty($dataEmployee[$key]))
				{
					foreach( $dataEmployee[$key] as $_key => $val )
					{
						if($val['start_date'] <= $_startDate && $_startDate <= $val['end_date'])
						{
							$pros ++;
						}
						else
						{
							if(date('d-m-Y',$val['start_date']) == date('d-m-Y',$_startDate))
							{
								//start_date between of month
								$pros ++;
							}
						}
					}
				}
				$staffings[$_startDate]['employee'] += $pros;
				//end
			}
			//sum Profile Not Affected
			$staffings[$_startDate]['totalWorkload'] += isset($dataTotal[$_startDate][999999999]) ? $dataTotal[$_startDate][999999999] : 0 ;
			$staffings[$_startDate]['validated'] = $staffings[$_startDate]['totalWorkload'] ;
			$_startDate = mktime(0, 0, 0, date("m", $_startDate)+1, date("d", $_startDate), date("Y", $_startDate)); 
		}
		return $staffings;
	}
	function caculateCapacityByProfile($key,$totalWorkload,$profiles,$profileValues,$date)
	{
		$session = new SessionComponent();
		$companyConfigs = $session->read('companyConfigs');
		$keyMonth = date('n',$date);
		$keyYear = date('Y',$date);
		$keyCapacityByMonth = 'capacity_by_month_'.$keyMonth;
		$capacity = 0;
		$FTE = 0 ;
		$_FTE = 0;
		$resource_theoretical = 0;
		//$_capacity = 0 ;
		$theoreticalCapacity = isset($companyConfigs[$keyCapacityByMonth]) ? $companyConfigs[$keyCapacityByMonth]*0.01 : 0;
		if(isset($profiles[$key]))
		{
			if(!empty($profiles[$key]['capacity_by_year']) && isset($profileValues[$key][$keyYear]) && !empty($profileValues[$key][$keyYear]))
			{
				$resource_theoretical = $profileValues[$key][$keyYear];
				$_FTE = $profiles[$key]['capacity_by_year'] * $theoreticalCapacity ; 
				$capacity = round($profileValues[$key][$keyYear] * $_FTE,2);
				$FTE = round((($totalWorkload - $capacity) / $_FTE),2);
			}
		}
		return array($capacity,$FTE,$resource_theoretical);
	}
}
?>