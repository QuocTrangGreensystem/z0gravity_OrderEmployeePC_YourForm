<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectDecision extends AppModel {
	var $name = 'ProjectDecision';
	var $displayField = 'project_decision';

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'project_decision_maker',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function __construct()
	{
	  parent::__construct();
	  $this->validate = array(
		'project_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => __('Your custom message here',true),
				'allowEmpty' => false,
				'required' => false,
			),
		),
		'project_decision' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => __('Decision is not blank',true),
			),
		),
				
		//'project_decision_maker' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('Decision maker is not blank',true),
//			),
//		),
	);
	}
}
?>