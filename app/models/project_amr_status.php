<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrStatus extends AppModel {
	var $name = 'ProjectAmrStatus';
	var $displayField = 'amr_status';

	var $hasMany = array(
		'ProjectAmr' => array(
			'className' => 'ProjectAmr',
			'foreignKey' => 'project_amr_status_id',
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
	function __construct(){
		parent::__construct();
		$this->validate = array(
		'amr_status' => array(
			'The Project ARM Statuses must be between 1 and 255 characters.'=>array(
				'rule'=>array('between',1,255),
				'message' =>__('Cannot be empty',true)
			),
			/*
			'Unique'=>array(
       		 	'rule' => 'isUnique',
       		 	'message' => __('This AMR status has already been taken.',true)
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
        var $belongsTo= array(
    'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
    );
}
?>