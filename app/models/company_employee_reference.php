<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CompanyEmployeeReference extends AppModel {
	var $name = 'CompanyEmployeeReference';
	var $displayField = 'id';
	function __construct(){
		parent::__construct();
		$this->validate = array(
		'id' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'company_id' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	}
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    
	var $belongsTo = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'employee_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function saveCompanyEmployeeRefer($id ,$company_id,$employee_id,$role_id, $control = 0, $seeAllProject = 0, $seeBudget = 0){
        //$this->deleteAll(array("CompanyEmployeeReference.id"=>$id));
		$this->data["CompanyEmployeeReference"]["id"] = $id;
		$this->data["CompanyEmployeeReference"]["company_id"] = $company_id;
		$this->data["CompanyEmployeeReference"]["employee_id"] = $employee_id;
		$this->data["CompanyEmployeeReference"]["role_id"] = $role_id;
		$this->data["CompanyEmployeeReference"]["control_resource"] = $control;
		$this->data["CompanyEmployeeReference"]["see_all_projects"] = $seeAllProject;
		$this->data["CompanyEmployeeReference"]["see_budget"] = $seeBudget;
		$this->save($this->data["CompanyEmployeeReference"]);
    }
	function AddCompanyEmployeeRefer($company_id,$employee_id,$role_id, $control = 0, $seeAllProject = 0, $seeBudget = 0){
		$this->data["CompanyEmployeeReference"]["company_id"] = $company_id;
		$this->data["CompanyEmployeeReference"]["employee_id"] = $employee_id;
		$this->data["CompanyEmployeeReference"]["role_id"] = $role_id;
		$this->data["CompanyEmployeeReference"]["control_resource"] = $control;
		$this->data["CompanyEmployeeReference"]["see_all_projects"] = $seeAllProject;
		$this->data["CompanyEmployeeReference"]["see_budget"] = $seeBudget;
		$this->save($this->data["CompanyEmployeeReference"]);
    }
}
?>