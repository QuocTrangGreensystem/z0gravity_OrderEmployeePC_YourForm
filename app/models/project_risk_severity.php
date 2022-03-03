<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectRiskSeverity extends AppModel {
	var $name = 'ProjectRiskSeverity';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'ProjectRisk' => array(
			'className' => 'ProjectRisk',
			'foreignKey' => 'project_risk_severity_id',
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
			'risk_severity' => array(
				'Length of the name'=>array(
					'rule'=>array('between',1,1024),
					'message'=>__('Cannot be empty', true),
				)       
			),
         	'company_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('Cannot be empty',true),
				)
			)
		);
	}
}
?>