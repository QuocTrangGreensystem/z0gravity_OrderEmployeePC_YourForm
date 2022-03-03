<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrPlan extends AppModel {
	var $name = 'ProjectAmrPlan';
    
	var $hasMany = array(
		'ProjectAmr' => array(
			'className' => 'ProjectAmr',
			'foreignKey' => 'project_amr_plan_id',
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
		'amr_plan' => array(
			'The Project AMR Plans must be between 1 and 255 characters.'=>array(
				'rule'=>array('between',1,255),
				'message' => __('Cannot be empty',true)
			),
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