<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectIssueSeverity extends AppModel {
	var $name = 'ProjectIssueSeverity';
	var $displayField = 'issue_severity';
	function __construct(){
		parent::__construct();
		$this->validate = array(
			'issue_severity' => array(
				'Length of the name'=>array(
					'rule'=>array('between',1,255),
					'message'=>__('Cannot be empty',true)
				)  
			),
			'company_id' => array(
				'Notempty'=>array(
					'rule'=>array('notEmpty'),
					'message'=>__('Cannot be empty', true),
				),          
			)
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
	);
	var $hasMany = array(
		'ProjectIssue' => array(
			'className' => 'ProjectIssue',
			'foreignKey' => 'project_issue_severity_id',
			'dependent' => false,
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

}
?>