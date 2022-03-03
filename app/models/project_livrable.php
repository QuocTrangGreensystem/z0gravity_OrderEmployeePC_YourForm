<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectLivrable extends AppModel {
	var $name = 'ProjectLivrable';

	var $belongsTo = array(
        'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'Project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectLivrableCategory' => array(
			'className' => 'ProjectLivrableCategory',
			'foreignKey' => 'project_livrable_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectStatus' => array(
			'className' => 'ProjectStatus',
			'foreignKey' => 'project_livrable_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'livrable_responsible',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ProjectLivrableActor' => array(
			'className' => 'ProjectLivrableActor',
			'foreignKey' => 'project_livrable_id',
			'dependent' => true,
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
    function __construct()
	{ parent::__construct();
     $this->validate = array(
		//'project_livrable_category_id' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' =>__('The project deliverable is not blank',true)
//			),
//		),
//        'livrable_responsible' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('The deliverable responsible is not blank',true),
//			),
//		),
//        'project_livrable_status_id' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				'message' => __('The deliverable status is not blank.',true),
//			),
//		),
//		'livrable_progression' => array(
//			'number' => array(
//				'rule' => array('numeric'),
//				'message' => __('The progression must be a number!',true),
//			),
//		)
	);
	}
}
?>