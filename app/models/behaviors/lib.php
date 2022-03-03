<?php
/*
*@project PMS
*@author : ViN
*@date : December - 2014
*/
class LibBehavior extends ModelBehavior {
	public function caculateGlobal($total, $number, $exactlyAlgorithm = true){
        if($total == $number){
            $integer = 1;
            $excess = 0;
        } else {
            if($number == 0){
                $integer = 0;
                $excess = 0;
            } else {
				$checkResidual = $total/$number;

				if($exactlyAlgorithm)
				{
					if( $checkResidual != 0 && round($checkResidual,2) == 0 )
					{
						$integer = 0.005;
					}
					else
					{
						$integer = number_format(round($checkResidual,2), 2, '.', '');
					}
				}
				else
				{
					$integer = $checkResidual;
				}
				//$integer = $exactlyAlgorithm ? round(($total/$number),2) : $total/$number; //PHEP TINH NAY SAI KHI CHENH LECH WORKLOAD VOI DURATION QUA LON
                if(strpos($integer, '.') != false){
                    $nguyen = strstr($integer, '.', true);
                    $du = substr(strstr($integer, '.'), 0, 3);
                    $integer = (float) $nguyen.$du;
                    $afterDivision = $integer*$number;
                    $excess = round($total - $afterDivision, 2);
                } else {
                    $integer = $integer;
                    $excess = 0;
                }
            }
        }
        $result['original'] = $integer;
		if($exactlyAlgorithm)
		{
			if($excess<0)
			{
				$result['remainder'] = -0.01;
				$result['number'] = $excess/$result['remainder'];
			}
			elseif($excess==0)
			{
				$result['remainder'] = 0.00;
				$result['number'] = 0;
			}
			else
			{
				$result['remainder'] = 0.01;
				$result['number'] = $excess/$result['remainder'];
			}

		}
		else
		{
			$result['remainder'] = $excess;
			$result['number'] = 1;
		}
        return $result;
    }
	function testSession()
	{
		echo "nupakachi!!!";
		exit;
		///debug($this->getWorkingDaysByMonths(strtotime('08-09-2015'),strtotime('30-11-2015')));
	}
	private function getDaysOff($startDate, $endDate, $allowHolidayInMonth=false){
        $employeeCompany = CakeSession::read('Auth.employee_info.Company.id');
        $daysOff = array();
        if ($startDate <= $endDate) {
			$workdays = ClassRegistry::init('Workday')->getOptions($employeeCompany);
			if($allowHolidayInMonth)
			{
				//Allow holiday in month
				$holiday = ClassRegistry::init('Holiday')->getHolidaysInMonth($employeeCompany, $startDate, $endDate, false);
			}
            while ($startDate <= $endDate){
                $_start = strtolower(date("l", $startDate));
                $_end = strtolower(date("l", $endDate));
                if($workdays[$_start] == 0){
                    $daysOff[] = date("d-m-Y", $startDate);
                }
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
            }
			if($allowHolidayInMonth)
			{
				$daysOff = array_merge($daysOff,$holiday);
				$daysOff = array_unique($daysOff);
			}
        }
        return $daysOff;
    }

	/*----------------------------------
	@INPUT: ('int time','int time', boolean)
	@OUTPUT : int
	-----------------------------------*/
	function getWorkingDays($startDate, $endDate, $allowHolidayInMonth = false){
        $duration = 0;
        if($startDate != '' && $endDate != ''){
            if ($startDate <= $endDate) {
                $daysOff = $this->getDaysOff($startDate, $endDate, $allowHolidayInMonth);
                $daysOff = count($daysOff);
                $_date = 0;
                while ($startDate <= $endDate){
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                    $_date++;
                }
                if($daysOff != 0){
                    $_date = $_date - $daysOff;
                }
            } else {
                $_date = 0;
            }
            $duration = $_date;
        }
        return $duration;
    }
	function getListWorkingDays($startDate, $endDate, $allowHolidayInMonth = false){
    $date = array();
        if($startDate != '' && $endDate != ''){
            if ($startDate <= $endDate) {
                $daysOff = $this->getDaysOff($startDate, $endDate, $allowHolidayInMonth);
                //$daysOff = count($daysOff);
                $date = array();
                while ($startDate <= $endDate){
					$time = date("d-m-Y", $startDate);
					if(!in_array($time,$daysOff))
					{
						$date[$startDate] = $startDate;
					}
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                }
            } else {
                $date = array();
            }
        }
        return $date;
    }
	function diffDate($start = null, $end = null){
        $datas = array();
        if(!empty($start) && !empty($start)){
            $minMonth = date('m', $start);
            $maxMonth = date('m', $end);
            $minYear = date('Y', $start);
            $maxYear = date('Y', $end);
            while ($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
				$lastDay = cal_days_in_month(CAL_GREGORIAN, $minMonth, $minYear);
                $datas[] = array('month'=>$minMonth,'year'=>$minYear,'lastDay'=>$lastDay,'beginDay'=>'01');
                $minMonth++;
                if ($minMonth == 13) {
                    $minMonth = 1;
                    $minYear++;
                }
            }
        }
        return $datas;
    }

	/*----------------------------------
	@INPUT: ('int time','int time')
	@OUTPUT : int
	-----------------------------------*/
	function getWorkingDaysByMonthsForEmployee($start,$end, $am_absence, $pm_absence){
		$company_id = CakeSession::read('Auth.employee_info.Company.id');
		$beginDay=date('d',$start);
		$endDay=date('d',$end);
		$result=array();
		$tmp_result = array();
		$months = $this->diffDate($start,$end);

		$countItem = count($months);
		$lastItem = $countItem - 1;
		$months[0]['beginDay']=$beginDay;
		$months[$lastItem]['lastDay']=$endDay;
		$indexPlus=0;
		$tmp_indexPlus=0;
		$holidays = ClassRegistry::init('Holiday')->getOptionHolidays($start, $end, $company_id);
		$workdays = ClassRegistry::init('Workday')->getOptions($company_id);
		
		foreach($months as $key=>$data)
		{
			$KEY=strtotime('01-'.$data['month'].'-'.$data['year']);
			$lastDay = strtotime($data['lastDay'].'-'.$data['month'].'-'.$data['year']);
			$firstDay = strtotime($data['beginDay'].'-'.$data['month'].'-'.$data['year']);
			$result[$KEY] = $data;
			$tmp_result[$KEY] = $data;
			$workDaysInMonth = $this->getWorkingDays($firstDay,$lastDay,false);
		
			$tmp_result[$KEY]['workDaysInMonth'] = $workDaysInMonth;
			$tmp_result[$KEY]['indexPlus'] = $tmp_indexPlus;
			
			if(!empty($am_absence)){
				 foreach($am_absence as $index => $adate){
					 if($adate <= $end && $adate >= $start && date('m-Y', $KEY) == date('m-Y', $adate)){
					 // if(date('m-Y', $KEY) == date('m-Y', $adate)){
						  $workDaysInMonth -= 0.5;
					 }
				 }
			 } 
			 if(!empty($pm_absence)){
				 foreach($pm_absence as $index => $pdate){
					 if($pdate <= $end && $pdate >= $start &&  date('m-Y', $KEY) == date('m-Y', $pdate)){
					 // if(date('m-Y', $KEY) == date('m-Y', $pdate)){
						   $workDaysInMonth -= 0.5;
					 }
				 }
			 }
			 if(!empty($holidays)){
				 foreach($holidays as $day => $value){
					$date_text = strtolower(date("l", $day));
					if($day <= $end && $day >= $start && $workdays[$date_text] != 0){
						if(isset($value['am']) && date('m-Y', $KEY) == date('m-Y', $day))  $workDaysInMonth -= 0.5;
						if(isset($value['pm']) && date('m-Y', $KEY) == date('m-Y', $day))  $workDaysInMonth -= 0.5;
					}
				 }
			 }
			$tmp_indexPlus += $workDaysInMonth;
			$result[$KEY]['workDaysInMonth'] = $workDaysInMonth;
			$result[$KEY]['indexPlus'] = $indexPlus;
			$indexPlus += $workDaysInMonth;
		}
		if($indexPlus > 0){
			$results = array('result' => $result, 'total' => $indexPlus);
		}else{
			$results = array('result' => $tmp_result, 'total' => $tmp_indexPlus);
		}
		
		return $results;
	}
	
	function getWorkingDaysByMonths($start,$end){
		$beginDay=date('d',$start);
		$endDay=date('d',$end);
		$result=array();
		$months = $this->diffDate($start,$end);

		$countItem = count($months);
		$lastItem = $countItem - 1;
		$months[0]['beginDay']=$beginDay;
		$months[$lastItem]['lastDay']=$endDay;
		$indexPlus=0;
		
		foreach($months as $key=>$data)
		{
			$KEY=strtotime('01-'.$data['month'].'-'.$data['year']);
			$lastDay = strtotime($data['lastDay'].'-'.$data['month'].'-'.$data['year']);
			$firstDay = strtotime($data['beginDay'].'-'.$data['month'].'-'.$data['year']);
			$result[$KEY] = $data;
			$workDaysInMonth = $this->getWorkingDays($firstDay,$lastDay,true);
			$result[$KEY]['workDaysInMonth'] = $workDaysInMonth;
			$result[$KEY]['indexPlus'] = $indexPlus;
			$indexPlus += $workDaysInMonth;
		}
		$results = array('result' => $result, 'total' => $indexPlus);
		return $results;
	}
	/**
      * Ham de quy Task.
      * Example:
      * Ten Task---Assign To---------------------------Start-------End---------Worload-----Overload-----Consumed-----Remain
      * Task 1     EMPLOYEE 1, EMPLOYEE 2, EMPLOYEE3   1/10/2013   30/12/2013   48         0            30           18
      *
      * Hien tai la thang 10
      *
      * Theo Ke Hoach:
      * Ten Employee --------- Thang 10 ----------Thang 11 ------- Thang 12--------Total
      *
      * -------------Workload-    10                 10               10             30
      * -EMPLOYEE 1--Consumed-     0                  0                0              0
      * -------------Remain---    10                 10               10             30
      *
      * -------------Workload-     5                  5                5             15
      * -EMPLOYEE 2--Consumed-     0                  0                0              0
      * -------------Remain---     5                  5                5             15
      *
      * -------------Workload-     1                  1                1              3
      * -EMPLOYEE 3--Consumed-     0                  0                0              0
      * -------------Remain---     1                  1                1              3
      *
      * -> EMPLOYEE 1 co tong workload = 30.
      * -> EMPLOYEE 2 co tong workload = 15.
      * -> EMPLOYEE 3 co tong workload =  3.
      * --------------------------------------
      * -> Total:                        48.
      *
      * Theo Thuc Te:
      * Ten Employee --------- Thang 10 ----------Thang 11 ------- Thang 12--------Total
      *
      * -------------Workload-    15                6.99             7.01            29
      * -EMPLOYEE 1--Consumed-    15                0                0               15
      * -------------Remain---     0                6.99             7.01            14
      *
      * -------------Workload-    10                1.99             2.01            14
      * -EMPLOYEE 2--Consumed-    10                0                0               10
      * -------------Remain---     0                1.99             2.01             4
      *
      * -------------Workload-     5                  0                0              5
      * -EMPLOYEE 3--Consumed-     5                  0                0              0
      * -------------Remain---     0                  0                0              5
      *
      * -> EMPLOYEE 1 co tong workload = 29.    =>>>>>> Giam 1
      * -> EMPLOYEE 2 co tong workload = 14.    =>>>>>> Giam 1
      * -> EMPLOYEE 3 co tong workload =  5.    =>>>>>> Tang 2
      * --------------------------------------
      * -> Total:                        48.
      *
      * Dau vao:
      * - 1 Array tat ca cac task co trong 1 project/activity va da them consumed cua cac employee/pc thuoc task do
      * Dau ra: 1 mang gia tri gom:
      * - Tong workload cua employee/profit center/ skill(function)
      * - Ngay bat dau
      * - Ngay ket thuc
      *
      *
      * @access private
      * @return array()
      * @author HuuPC
      */
      function recursiveTask($tasks = array(), $valueAdds = null, $tmpTasks = array()){
		return $tasks;
         if(empty($tmpTasks)){
            $tmpTasks = $tasks;
         }
         $v1 = $v2 = false;
         $_v1 = $_v2 = array();
         foreach($tasks as $taskId => $task){
            end($task);
            $lastId = key($task);
            $countEmploy = count($task);
            $calculs = array();
            if(!empty($valueAdds[$taskId])){
                if($valueAdds[$taskId] != 0 || $valueAdds[$taskId] != ''){
                    $_calculs = $this->caculateGlobal($valueAdds[$taskId], $countEmploy,false);
                    $calculs[$taskId] = $_calculs;
                    $valueAdds[$taskId] = 0;
                }
            } else {
                $valueAdds[$taskId] = 0;
            }
            foreach($task as $emp => $values){
                if(empty($values['consumed'])){
                    $values['consumed'] = 0;
                }
                if(empty($values['workload'])){
                    $values['workload'] = 0;
                }
                if(!empty($calculs[$taskId])){
                    $totalCalculs[$taskId] = round($calculs[$taskId]['original'] + $calculs[$taskId]['remainder'], 2);
                    if($totalCalculs[$taskId] != 0){
                        if($values['workload'] - $totalCalculs[$taskId] < 0){
                            if($totalCalculs[$taskId] < 0){
                                if($emp == $lastId){
                                    $valueAdds[$taskId] += round(($values['workload'] + $totalCalculs[$taskId]), 2);
                                } else {
                                    $valueAdds[$taskId] += round(($values['workload'] + $calculs[$taskId]['original']), 2);
                                }
                            } else {
                                if($emp == $lastId){
                                    $valueAdds[$taskId] += round(($values['workload'] - $totalCalculs[$taskId]), 2);
                                } else {
                                    $valueAdds[$taskId] += round(($values['workload'] - $calculs[$taskId]['original']), 2);
                                }
                            }
                            $values['workload'] = 0;
                            $tmpTasks[$taskId][$emp] = $values;
                            unset($tasks[$taskId][$emp]);
                            $_v1[] = true;
                        } else {
                            if($totalCalculs[$taskId] < 0){
                                if($emp == $lastId){
                                    $values['workload'] = round(($values['workload'] + $totalCalculs[$taskId]), 2);
                                } else {
                                    $values['workload'] = round(($values['workload'] + $calculs[$taskId]['original']), 2);
                                }
                            } else {
                                if($emp == $lastId){
                                    $values['workload'] = round(($values['workload'] - $totalCalculs[$taskId]), 2);
                                } else {
                                    $values['workload'] = round(($values['workload'] - $calculs[$taskId]['original']), 2);
                                }
                            }
                            $tasks[$taskId][$emp] = $values;
                            $tmpTasks[$taskId][$emp] = $values;
                            $_v1[] = false;
                        }
                    }
                }
                if($values['consumed'] == $values['workload']){
                    $tmpTasks[$taskId][$emp] = $values;
                    unset($tasks[$taskId][$emp]);
                }
                if($values['consumed'] > $values['workload']){
                    $_tmpCals = round(($values['consumed'] - $values['workload']), 2);
                    $valueAdds[$taskId] += round(($values['workload'] - $values['consumed']), 2);
                    $values['workload'] = $values['consumed'];
                    $tmpTasks[$taskId][$emp] = $values;
                    unset($tasks[$taskId][$emp]);
                    $_v2[] = true;
                } else {
                    $_v2[] = false;
                }
            }
         }
         $v1 = (in_array(1, $_v1)) ? true : false;
         $v2 = (in_array(1, $_v2)) ? true : false;
         if($v1||$v2){
            $tmpTasks = $this->recursiveTask($tasks, $valueAdds, $tmpTasks);
         }
         return $tmpTasks;
      }
      /**
       * Ham tinh toan lai remain
       * Neu nhung thang la qua khu thi remain = 0
       * Remain nhung thang qua khu se duoc chia dieu cho thang hien tai va cac thang trong tuong lai
       * Ham cui bap - co thoi gian se viet lai
       */
      function resetRemainSystem($datas = array()){
        $currentDate = strtotime(date('01-m-Y', time()));
        $totalEstimated = $_total = $monthValidates = array();
        foreach($datas as $id => $data){
            $works = $cons = $remains = 0;
            foreach($data as $time => $value){
                if($time < $currentDate){
                    $works += $value['validated'];
                    $cons += $value['consumed'];
                    $remains += $value['remains'];
                    $_total[$id]['validated'] = $works;
                    $_total[$id]['consumed'] = $cons;
                    $_total[$id]['remain'] = $remains;
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    if(!empty($value['validated'])){
                        $monthValidates[$id][] = $time;
                    }
                }
            }
        }
        if(!empty($monthValidates)){
            foreach($monthValidates as $id => $monthValidate){
                if(in_array($currentDate, $monthValidate)){
                    //do nothing
                } else {
                    $monthValidates[$id][] = $currentDate;
                }
            }
        }
        foreach($datas as $id => $data){
            foreach($data as $time => $value){
                if($time < $currentDate){
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    $count = !empty($monthValidates[$id]) ? count($monthValidates[$id]) : 1;
                    $remain = !empty($_total[$id]) ? $_total[$id]['remain'] : 0;
                    if($count == 0){
                        $remainFirst = 0;
                        $remainSecond = 0;
                    } else {
                        $getRemain = $this->caculateGlobal($remain, $count, false);
                        $remainFirst = $getRemain['original'];
                        $remainSecond = $getRemain['remainder'];
                    }
                    if(!empty($value['validated']) || $time == $currentDate){
                        $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst;
                        if(!empty($monthValidates[$id]) && $time == max($monthValidates[$id])){
                            $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst +$remainSecond ;
                        }
                    } else {
                        if(!empty($datas[$id][$currentDate])){
                            if($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0){
                                $datas[$id][$currentDate]['remains'] = $remainFirst + $remainSecond;
                            }
                        }
                    }
                }
            }
        }
        return $datas;
      }
	  function mergeDataSystem($workloads = array(), $consumeds = array(), $previous = array(), $totalConsumedTasks = array(), $model = null, $project_id = null, $activity_id = null, $mutilMonth = 0, $periods = array()){
        $datas = array();
		$employeeName = CakeSession::read('Auth.employee_info.Company.id');
        if(!empty($workloads)){
            $dataFirsts = $dataSeconds = array();
			//Apply staffing by profile, modify by ViN 6/6/2015
			$workloadsProfile = array();
			//$SUM = 0;
			$list_employee_id = array();
			$min_date = 0;
			$max_date = 0;
            foreach($workloads as $taskId => $workload){
				 foreach($workload as $id => $values){
					  if($id < 999999999 && !empty($values['workload']) && $values['workload'] > 0){
						  $list_employee_id[] = $id;
						  if($min_date == 0) 	$min_date =$values['startDate'];
							if($max_date == 0) 	$min_date = $values['endDate'];
							$min_date = ($min_date > $values['startDate']) ? $values['startDate'] : $min_date;
							$max_date = ($max_date < $values['endDate']) ? $values['endDate'] : $max_date;
					  }
				 }
			}
			$AbsenceRequest = ClassRegistry::init('AbsenceRequest');
			$taskAbsencesAm = $AbsenceRequest->find("all", array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($min_date, $max_date), 
					'response_am' => 'validated', 
					'employee_id' => $list_employee_id
				),
				'fields' => array('employee_id', 'date'),
			));
			$amAbsences = array();
			foreach($taskAbsencesAm as $key => $amAB){
				$dx = $amAB['AbsenceRequest'];
				if(empty($amAbsences[$dx['employee_id']])) $amAbsences[$dx['employee_id']] = array();
				$amAbsences[$dx['employee_id']][] = $dx['date'];
			}
			
			$taskAbsencesPm = $AbsenceRequest->find("all", array(
				'recursive' => -1,
				'conditions' => array(
					'date BETWEEN ? AND ?' => array($min_date, $max_date),
					'response_pm' => 'validated', 
					'employee_id' => $list_employee_id
				),
				'fields' => array('employee_id', 'date'),
			));
			$pmAbsences = array();
			
			foreach($taskAbsencesPm as $key => $amAB){
				$dx = $amAB['AbsenceRequest'];
				if(empty($pmAbsences[$dx['employee_id']])) $pmAbsences[$dx['employee_id']] = array();
				$pmAbsences[$dx['employee_id']][] = $dx['date'];
			}
			$holidays = ClassRegistry::init('Holiday')->getOptionHolidays($min_date, $max_date, $employeeName);
            foreach($workloads as $taskId => $workload){
                foreach($workload as $id => $values){
                    $workloadSeconds = array();
                    if(!empty($totalConsumedTasks[$taskId][$id])){
						if(!isset($values['workload'])||empty($values['workload'])) $values['workload'] = 0;
						$values['workload']=$values['workload'];
                    }
                    $_end = !empty($values['endDate']) ? $values['endDate'] : time();
                    $_start = !empty($values['startDate']) ? $values['startDate'] : $_end;
                    if($_start > $_end){
                        $_end = $_start;
                    }
					if($_end == $_start)
					{
						$_end = $_start + 86400;
					}
					
                    $minMonth = !empty($_start) ? date('m', $_start) : '';
                    $minYear = !empty($_start) ? date('Y', $_start) : '';
                    $maxMonth = !empty($_end) ? date('m', $_end) : '';
                    $maxYear = !empty($_end) ? date('Y', $_end) : '';
					
					if( isset($values['workload']) && $values['workload'] == 0 )
					{
						$allowCaculate = false;
					}
					else
					{
						$am_absence = !empty($amAbsences[$id]) ? $amAbsences[$id] :  array();
						$pm_absence = !empty($pmAbsences[$id]) ? $pmAbsences[$id] :  array();
						$workDaysByMonths = $this->getWorkingDaysByMonthsForEmployee($_start, $_end, $am_absence, $pm_absence);
						$WORKDAYS = $workDaysByMonths['total'];
						$allowCaculate = true;
						$WORKLOADBYDAY = $this->caculateGlobal($values['workload'], $WORKDAYS*2 , true);
						$workDaysByMonths = $workDaysByMonths['result'];
						$estis = $WORKLOADBYDAY['original'];
						$remainder = $WORKLOADBYDAY['remainder'];
						$numberOfRemainder = $WORKLOADBYDAY['number'];

					}
                    $workloadFirsts = array();
                    if($mutilMonth == 1){
                        while($_start <= $_end){
                            $TIME = strtotime('01-'. date('m', $_start) .'-'. date('Y', $_start));
                            $moc = !empty($periods[$TIME]) ? $periods[$TIME] : '';
                            if(!empty($moc)){
                                if($_start > $moc){
                                    $TIME = mktime(0, 0, 0, date("m", $TIME)+1, date("d", $TIME), date("Y", $TIME));
                                } else {
                                    //do nothing
                                }
                            }
    						if($allowCaculate)
    						{
    							$_workDaysByMonths = $workDaysByMonths[$TIME];
    							$workloadFirsts[$id][$TIME]['estimated'] = $estis*$_workDaysByMonths['workDaysInMonth']*2;
    							if($_workDaysByMonths['indexPlus'] <= $numberOfRemainder)
    							{
    								$temp = $numberOfRemainder - $_workDaysByMonths['indexPlus'];
									
    								if($temp >= $_workDaysByMonths['workDaysInMonth'])
    								{
    									$workloadFirsts[$id][$TIME]['estimated'] += ($_workDaysByMonths['workDaysInMonth']*$remainder);
    								}
    								elseif($temp < $_workDaysByMonths['workDaysInMonth'])
    								{
    									$workloadFirsts[$id][$TIME]['estimated'] += ($temp*$remainder);
    								}
    							}
								
    						}
    						else
    						{
    							$workloadFirsts[$id][$TIME]['estimated']=0;
    						}

    						if(isset($values['profile_id'])&&!empty($values['profile_id']))
    						{
    							$keyDataProfile = $values['profile_id'].'-'.$TIME;
    							$MODEL_ID = $values['profile_id'] ;

    						}
    						else
    						{
    							$keyDataProfile = '999999999-'.$TIME;
    							$MODEL_ID = 999999999 ;
    						}
    						if(!isset($_dataProfile[$keyDataProfile]))
    						{
    							$_dataProfile[$keyDataProfile] = array(
    								'project_id' => $project_id,
    								'activity_id' => $activity_id,
    								'model' => 'profile',
    								'model_id' => $MODEL_ID,
    								'model_refer' => 0,
    								'date' => $TIME,
    								'estimated' => 0,
    								'consumed' => 0,
    								'company_id' => $employeeName
    							);
    						}
    						$_dataProfile[$keyDataProfile]['estimated'] += $workloadFirsts[$id][$TIME]['estimated'];
                            $_start = mktime(0, 0, 0, date("m", $_start)+1, date("d", $_start), date("Y", $_start));
                        }
                    } else {
    					//$i=0;
                        while($minYear < $maxYear || ($minYear == $maxYear && $minMonth <= $maxMonth)) {
    						//MODIFY BY VINGUYEN 18/10/2014 : APPLY FOR NEW FUNCTION CACULATE WITH ALGORITHM EXACTLY
    						$TIME=strtotime('01-'. $minMonth .'-'. $minYear);
    						if($allowCaculate)
    						{
    							$_workDaysByMonths = $workDaysByMonths[$TIME];
    							$workloadFirsts[$id][$TIME]['estimated'] = $estis*$_workDaysByMonths['workDaysInMonth']*2;
								
    							if($_workDaysByMonths['indexPlus'] <= $numberOfRemainder)
    							{
    								$temp = $numberOfRemainder - $_workDaysByMonths['indexPlus'];
    								if($temp >= $_workDaysByMonths['workDaysInMonth'])
    								{
    									$workloadFirsts[$id][$TIME]['estimated'] += ($_workDaysByMonths['workDaysInMonth']*$remainder);
    								}
    								elseif($temp < $_workDaysByMonths['workDaysInMonth'])
    								{
    									$workloadFirsts[$id][$TIME]['estimated'] += ($temp*$remainder);
    								}
    							}
    						}
    						else
    						{
    							$workloadFirsts[$id][$TIME]['estimated']=0;
    						}
                            $minMonth++;
                            if ($minMonth == 13) {
                                $minMonth = 1;
                                $minYear++;
                            }
    						//Apply staffing by profile, modify by ViN 6/6/2015
    						if(isset($values['profile_id'])&&!empty($values['profile_id']))
    						{
    							$keyDataProfile = $values['profile_id'].'-'.$TIME;
    							$MODEL_ID = $values['profile_id'] ;

    						}
    						else
    						{
    							$keyDataProfile = '999999999-'.$TIME;
    							$MODEL_ID = 999999999 ;
    						}
    						if(!isset($_dataProfile[$keyDataProfile]))
    						{
    							$_dataProfile[$keyDataProfile] = array(
    								'project_id' => $project_id,
    								'activity_id' => $activity_id,
    								'model' => 'profile',
    								'model_id' => $MODEL_ID,
    								'model_refer' => 0,
    								'date' => $TIME,
    								'estimated' => 0,
    								'consumed' => 0,
    								'company_id' => $employeeName
    							);
    							//$workloadsProfile[$values['profile_id']][$TIME]['estimated'] = 0;
    						}
    						$_dataProfile[$keyDataProfile]['estimated'] += $workloadFirsts[$id][$TIME]['estimated'];
    						//$i++;
    						//END
                        }
                    }
                    $dataSeconds[] = $workloadSeconds;
                    $dataFirsts[] = $workloadFirsts;
                }

            }
            $dataFirsts = array_merge($dataFirsts, $dataSeconds);
            if(!empty($dataFirsts)){
                foreach($dataFirsts as $dataFirst){
                    foreach($dataFirst as $emp => $values){
                        foreach($values as $time => $value){
							if(!isset($datas[$emp][$time]['estimated'])){
								$datas[$emp][$time]['estimated'] = 0;
							}
							$datas[$emp][$time]['estimated'] += $value['estimated'];
                        }
                    }
                }
                if(!empty($consumeds)){
                    foreach($consumeds as $emp => $consumed){
                        foreach($consumed as $time => $values){
                            $datas[$emp][$time]['consumed'] = $values['consumed'];
                        }
                    }
                }
            }
        }
        if(!empty($previous)){
            foreach($previous as $emp => $previou){
                foreach($previou as $time => $values){
                    /*if(!isset($datas[$emp][$time]['estimated'])){
                        $datas[$emp][$time]['estimated'] = $values['consumed'];
                    } else {
                        $datas[$emp][$time]['estimated'] += $values['consumed'];
                    }*/
					if(!isset($datas[$emp][$time]['estimated'])){
                        $datas[$emp][$time]['estimated'] = 0;
                    }
					//$datas[$emp][$time]['estimated'] = 0; //CODE NGU NHAT THE GIOI
                    if(!isset($datas[$emp][$time]['consumed'])){
                        $datas[$emp][$time]['consumed'] = $values['consumed'];
                    } else {
                        $datas[$emp][$time]['consumed'] += $values['consumed'];
                    }

                }
            }
        }
		$SUM = 0;
		$TmpStaffingSystem = ClassRegistry::init('TmpStaffingSystem');
		$EmployeeReferPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
		$datasSaveOfPCStepOne = $datasSaveOfPCNotAffected = array();
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = array();
			$model_refer = 0;
            foreach($tmpDatas as $id => $tmpData){
				if($model == 'employee' && $id > 999999999)
				{
					$model_refer = str_replace('999999999','',$id);
					$id = 999999999;
				}
				$model_refer = isset($model_refer) ? $model_refer : 0;
                foreach($tmpData as $time => $values){
					//$_estimatedTmp = isset($_datasSaffingEmp[$time]) ? $_datasSaffingEmp[$time] : 0;
					$_estimated = !empty($values['estimated']) ? $values['estimated'] : 0;
					//$_estimated += $_estimatedTmp;
					//$SUM += $_estimated;
					if($model_refer != 0 && $id == 999999999)
					{
						if(!isset($datasSaveOfPCStepOne[$model_refer.'_'.$time]))
						{
							$datasSaveOfPCStepOne[$model_refer.'_'.$time] = array(
								'project_id' => $project_id,
								'activity_id' => $activity_id,
								'model' => 'profit_center',
								'model_id' => $model_refer,
								//'model_refer' => $model_refer,
								'date' => $time,
								'estimated' => $_estimated,
								'consumed' => 0,
								'company_id' => $employeeName
							);
						}
						else
						{
							$datasSaveOfPCStepOne[$model_refer.'_'.$time]['estimated'] += $_estimated;
						}
					}
					elseif($model_refer == 0 && $id == 999999999)
					{
						$datasSaveOfPCNotAffected[] = array(
							'project_id' => $project_id,
							'activity_id' => $activity_id,
							'model' => 'profit_center',
							'model_id' => $id,
							//'model_refer' => $model_refer,
							'date' => $time,
							'estimated' => $_estimated,
							'consumed' => !empty($values['consumed']) ? $values['consumed'] : 0,
							'company_id' => $employeeName
						);
					}
                    $_datas = array(
                        'project_id' => $project_id,
                        'activity_id' => $activity_id,
                        'model' => $model,
                        'model_id' => $id,
						'model_refer' => $model_refer,
                        'date' => $time,
                        'estimated' => $_estimated,
                        'consumed' => !empty($values['consumed']) ? $values['consumed'] : 0,
                        'company_id' => $employeeName
                    );
                    $datas[] = $_datas;
                }
            }
        }
		///echo ' : '; echo $SUM;
		$_dataProfile = isset($_dataProfile) ? array_values($_dataProfile) : array();
        return array($datas,$datasSaveOfPCStepOne,$datasSaveOfPCNotAffected,$_dataProfile);
     }
	 /*----------------------------------
	@INPUT: ('int time')
	@OUTPUT : array date
	-----------------------------------*/
	 function getWorkingDaysInMonth($time,$allowHolidayInMonth = true)
	 {
		$dayOfMonth = cal_days_in_month(CAL_GREGORIAN, date("m", $time), date("Y", $time));
		$_startDate=strtotime('01-'.date("m-Y", $time));
		$_endDate=strtotime($dayOfMonth.'-'.date("m-Y", $time));
		$workingDays = $this->getListWorkingDays($_startDate, $_endDate, $allowHolidayInMonth);
		return $workingDays;
	 }
	/*----------------------------------
	@INPUT: ('int time','text week day')
	@OUTPUT : value : week day, format : int time
	-----------------------------------*/
	function getDateByWeekday($date, $weekday = 'Monday'){

		$dateTimeObj = DateTime::createFromFormat('H:i:s d-m-Y', date('H:i:s d-m-Y', $date));
		$obj2 = DateTime::createFromFormat('H:i:s d-m-Y', date('H:i:s d-m-Y', $date));
		$dateTimeObj->modify($weekday . ' this week');
		$target = $dateTimeObj->getTimestamp();
		if( $weekday == 'Monday' ){
			$obj2->modify('first day of this month');
			$firstDay = $obj2->getTimestamp();
			return max($target, $firstDay);
		} else {
			$obj2->modify('last day of this month');
			$lastDay = $obj2->getTimestamp();
			return min($target, $lastDay );
		}
	}
	function checkRebuildStaffing($keywork, $id){
		$model = ClassRegistry::init($keywork);
		$result = $model->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $id
				),
				'fields' => 'rebuild_staffing'
			)
		);
		return $result[$keywork]['rebuild_staffing'];
	}
}
?>
