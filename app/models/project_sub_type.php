<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectSubType extends AppModel {
	var $name = 'ProjectSubType';
    var $displayField = 'project_sub_type';
	var $validate = array(
		'project_type_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Cannot be empty'
			),
		),
		'project_sub_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Cannot be empty'
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ProjectType' => array(
			'className' => 'ProjectType',
			'foreignKey' => 'project_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        
	);

	var $hasMany = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_sub_type_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),'ProjectAmr' => array(
			'className' => 'ProjectAmr',
			'foreignKey' => 'project_amr_sub_program_id',
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