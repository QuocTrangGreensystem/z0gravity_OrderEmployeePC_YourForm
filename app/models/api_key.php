<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ApiKey extends AppModel {
	//public $primaryKey = '';
	private function getTime(){
		$time = Configure::read('Session.timeout');
		$level = Configure::read('Session.level');
		switch($level){
			case 'high':
				$time *= 10;
				break;
			case 'medium':
				$time *= 100;
				break;
			default:
				$time *= 300;
				break;
		}
		return $time;
	}
	public function generate(&$session){
		$employee = $session->read('Auth.employee_info.Employee.id');
		$company = $session->read('Auth.employee_info.Company.id');
		$expire = time() + $this->getTime();
		$unique = md5($employee . $company . $expire . microtime());
		$this->create();
		$this->save(array(
			'employee_id' => $employee,
			'company_id' => $company,
			'expire_time' => $expire,
			'api_key' => $unique
		));
		$session->write('Auth.employee_info.Employee.api_key', $unique);
		return $unique;
	}
	public function retrieve($unique){
		return $this->find('first', array(
			'conditions' => array(
				'expire_time >' => time(),
				'api_key' => $unique
			)
		));
	}
	public function updateExpireTime($unique){
		$last = $this->retrieve($unique);
		if( !empty( $last)){
			$last['ApiKey']['expire_time'] = time() + $this->getTime();
			$this->id = $last['ApiKey']['id'];
			$result = $this->save($last);
			return $result['ApiKey']['expire_time'];
		}
		return false;
	}
	public function validate(&$session, $unique){
		$employee = $session->read('Auth.employee_info.Employee.id');
		$company = $session->read('Auth.employee_info.Company.id');
		return $this->find('count', array(
			'conditions' => array(
				'employee_id' => $employee,
				'company_id' => $company,
				'expire_time >' => time(),
				'api_key' => $unique
			)
		));
	}
	public function destroy(&$session){
		$unique = $session->read('Auth.employee_info.Employee.api_key');
		$session->delete('Auth.employee_info.Employee.api_key');
		$this->deleteAll(array(
			'api_key' => $unique
		));
	}
	public function destroyAll(&$session){
		$session->delete('Auth.employee_info.Employee.api_key');
		$this->deleteAll(array(
			'employee_id' => $employee,
			'company_id' => $company,
		));
	}
}