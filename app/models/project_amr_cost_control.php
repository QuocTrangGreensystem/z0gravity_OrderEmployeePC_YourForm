<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrCostControl extends AppModel {
	var $name = 'ProjectAmrCostControl';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
   
	var $hasMany = array(
		'ProjectAmr' => array(
			'className' => 'ProjectAmr',
			'foreignKey' => 'project_amr_cost_control_id',
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
        var $belongsTo= array(
            'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
    );
	function __construct(){
		parent::__construct();
		$this->validate = array(
		'amr_cost_control' => array(
			'The Project AMR Cost Controls must be between 1 and 255 characters.'=>array(
				'rule'=>array('between',1,255),
				'message' => __('Cannot be empty',true)
			),
			/*
			'Unique'=>array(
       		 	'rule' => 'isUnique',
       		 	'message' => __('This AMR cost control has already been taken.',true)
    		)
    		*/
		),
			'company_id' => array(
				'Notempty'=>array(
					'rule'=>array('notEmpty'),
					'message'=>__('Cannot be empty', true),
				),          
			)
	);
	}
	
}
?>