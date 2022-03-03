<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectIssue extends AppModel {
	var $name = 'ProjectIssue';
	//var $displayField = 'project_issue_name';
	
	function __construct(){
		parent::__construct();
		$this->validate = array(
			'project_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('The project id of risk is not found!', true),
				),
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __('The project id is nummeric!', true),
				),
			),
			'project_issue_problem' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('Project problem is not blank!', true),
				),
			),
			//'project_issue_severity_id' => array(
//				'notempty' => array(
//					'rule' => array('notempty'),
//					'message' => __('Severity of issue is not blank!', true),
//				),
//			),
//			'project_issue_status_id' => array(
//				'notempty' => array(
//					'rule' => array('notempty'),
//					'message' => __('Status of issue is not blank!', true),
//				),
//			),
//			'issue_assign_to' => array(
//				'notempty' => array(
//					'rule' => array('notempty'),
//					'message' => __('You must assign issue for employee!', true),
//				),
//			),
		);
	}

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectIssueSeverity' => array(
			'className' => 'ProjectIssueSeverity',
			'foreignKey' => 'project_issue_severity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectIssueStatus' => array(
			'className' => 'ProjectIssueStatus',
			'foreignKey' => 'project_issue_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'issue_assign_to',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>