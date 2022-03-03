<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectPriority extends AppModel {
	var $name = 'ProjectPriority';
	var $displayField = 'priority';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	function __construct(){
		parent::__construct();
		$this->validate = array(
    		'priority' => array(
    			'The priority must be between 1 and 50 characters.'=>array(
    				'rule'=>array('between',1,50),
    				'message' => __('Cannot be empty',true)
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
		
	var $hasMany = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_priority_id',
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
	var $belongsTo = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		);
	
}
?>