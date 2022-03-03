<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CustomerLogo extends AppModel {
	var $name = 'CustomerLogo';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	function __construct(){
		parent::__construct();
		$this->validate = array(
			'name' => array(
				'The logo name must be between 1 and 255 characters.'=>array(
					'rule'=>array('between',1,255),
					'message' => __('Cannot be empty',true)
				)
			),
		);
	}
    var $belongsTo= array(
            'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
    );
	public function getListCustomerLogo(){
		$this->loadModel('CustomerLogo');
		$company_id =  CakeSession::read('Auth.employee_info.Company.id');
		$list_logo = $this->CustomerLogo->find('all', array(
			'recursive' => -1,
			'conditions' =>  array(
				'OR' => array(
					'company_id IS NULL',
					'company_id' => $company_id
				),
			),
			'fields' => array('id', 'logo_name', 'company_id'),
		)); 
		$list_logo = !empty($list_logo) ? Set::combine($list_logo, '{n}.CustomerLogo.id', '{n}.CustomerLogo') : array();
		return array_values($list_logo);
	}
}
?>