<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectRisk extends AppModel {
	var $name = 'ProjectRisk';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectRiskSeverity' => array(
			'className' => 'ProjectRiskSeverity',
			'foreignKey' => 'project_risk_severity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectRiskOccurrence' => array(
			'className' => 'ProjectRiskOccurrence',
			'foreignKey' => 'project_risk_occurrence_id',
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
			'foreignKey' => 'risk_assign_to',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    function __construct(){
    parent::__construct();
	$this->validate = array(
		'project_risk' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => __('Cannot be empty',true),
			),
		),
        //'project_risk_severity_id' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('Cannot be empty',true),
//			),
//		),
//        'project_risk_occurrence_id' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('Cannot be empty',true),
//			),
//		),
//        'risk_assign_to' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('Cannot be empty',true),
//			),
//		),
	);
	}
}
?>