<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectMilestone extends AppModel {
	var $name = 'ProjectMilestone';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
     function __construct(){
	 parent::__construct();
      $this->validate = array(
		'project_milestone' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => __('The milestone is not blank!',true),
			),
		),
        // 'milestone_date' => array(
            // 'notempty' => array(
				// 'rule' => array('notempty'),
				// 'message' => __('The milestone date is not blank!',true),
			// ),
        // )
	);
	}
}
?>