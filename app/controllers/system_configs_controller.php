<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SystemConfigsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
	var $uses = array('SystemConfig');
    var $name = 'SystemConfigs';
    
    /**
     * Components
     *
     * @var array
     * @access public
     */
	function beforeFilter()
	{
		parent::beforeFilter();
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'administrators', 'action' => 'index'));
		}
	}
	function index($ajax = '')
	{
		//do nothing
	}
	public function editMe($field = null, $value = null, $private = false){		
		$value = $this->data['value'];
		$field = $this->data['field'];
		$check = $this->SystemConfig->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'cf_name' => $field,
			)
		));
		if($check)
		{
			$data = array(
				'id'=> $check['SystemConfig']['id'],
				'cf_value'=> $value
			);
			$this->SystemConfig->save($data);
			$success = 1;
		}
		else
		{
			$this->SystemConfig->create();
			$data = array(
				'cf_name'=> $field,
				'cf_value'=> $value
			);
			$this->SystemConfig->save($data);
			$success = 1;
		}
		echo $success;
		exit;
	}
}
?>