<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class PeriodsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
	var $uses = array('Period');
    var $name = 'Periods';
    
    /**
     * Components
     *
     * @var array
     * @access public
     */
	/*function beforeFilter()
	{
		parent::beforeFilter();
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'administrators', 'action' => 'index'));
		}
	}*/
	function index($year = null)
	{
		//do nothing
		$months = Array(
			'1'=>"January",
			'2'=>"February",
			'3'=>"March",
			'4'=>"April",
			'5'=>"May",
			'6'=>"June",
			'7'=>"July",
			'8'=>"August",
			'9'=>"September",
			'10'=>"October",
			'11'=>"November",
			'12'=>"December"
		);
		if($year == null)
		{
			$year = date('Y',time());
		}
		foreach($months as $num=>$text)
		{
			$endDateTmp = cal_days_in_month(CAL_GREGORIAN, $num, $year);
			$endDate = strtotime($endDateTmp.'-'.$num.'-'.$year);
			$startDate = strtotime('01-'.$num.'-'.$year);
			$key = $startDate;
			$this->checkAndUpdate($key,'start',$startDate,false);
			$this->checkAndUpdate($key,'end',$endDate,false);
		}
		$company = $this->employee_info['Company']['id'];
		$results = $this->Period->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company,
				"FROM_UNIXTIME(time, '%Y')" => $year
			)
		));
		$months = Array(
			'0'=> '',
			'1'=>"January",
			'2'=>"February",
			'3'=>"March",
			'4'=>"April",
			'5'=>"May",
			'6'=>"June",
			'7'=>"July",
			'8'=>"August",
			'9'=>"September",
			'10'=>"October",
			'11'=>"November",
			'12'=>"December"
		);
		$results = Set::combine($results,'{n}.Period.time','{n}.Period');
		$this->set(compact('results','year'));
	}
	function checkAndUpdate($time,$field,$value,$update = true)
	{
		$company = $this->employee_info['Company']['id'];
		$check = $this->Period->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company,
				'time' => $time
			)
		));
		$success = 0;
		if($check)
		{
			if($update == true || $check['Period'][$field] == '')
			{
				$data = array(
					'id'=> $check['Period']['id'],
					$field => $value
				);
				$this->Period->save($data);
				$success = 1;
			}
		}
		else
		{
			$this->Period->create();
			$data = array(
				'company_id'=>$company,
				$field=> $value,
				'time'=> $time
			);
			$this->Period->save($data);
			$success = 1;
		}
        /**
         * Rebuild all staffing
         */
        $this->loadModels('Project', 'Activity');
        $this->Project->updateAll(array('Project.rebuild_staffing' => 1), array('OR' => array('Project.rebuild_staffing' => 0, 'Project.rebuild_staffing IS NULL')));
        $this->Activity->updateAll(array('Activity.rebuild_staffing' => 1), array('OR' => array('Activity.rebuild_staffing' => 0, 'Activity.rebuild_staffing IS NULL')));
		return $success;
	}
	public function editMe($field = null, $value = null, $key = null){		
		$value = strtotime($this->data['value']);
		$field = $this->data['field'];
		$time = strtotime($this->data['key']) ;
		$success = $this->checkAndUpdate($time,$field,$value);
		echo $success;
		exit;
	}
}
?>