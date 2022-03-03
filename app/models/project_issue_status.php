<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectIssueStatus extends AppModel {
	var $name = 'ProjectIssueStatus';
	var $displayField = 'issue_status';
	function __construct(){
		parent::__construct();
			$this->validate = array(
			'issue_status' => array(
				'Length of the name'=>array(
					'rule'=>array('between',1,255),
					'message'=>__('Cannot be empty', true),
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
			'foreignKey' => 'project_issue_status_id',
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