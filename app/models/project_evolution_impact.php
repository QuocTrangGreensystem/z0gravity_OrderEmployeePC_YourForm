<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectEvolutionImpact extends AppModel {
	var $name = 'ProjectEvolutionImpact';
	var $displayField = 'evolution_impact';
	function __construct(){
		parent::__construct();
		$this->validate = array(
		  'evolution_impact' => array(
			'The city must be between 1 and 255 characters.'=>array(
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
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'ProjectEvolutionImpactRefer' => array(
			'className' => 'ProjectEvolutionImpactRefer',
			'foreignKey' => 'project_evolution_impact_id',
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

}
?>