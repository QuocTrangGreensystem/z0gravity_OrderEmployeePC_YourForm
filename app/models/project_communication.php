<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectCommunication extends AppModel {
	
	var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => array('id', 'project_name', 'project_objectives'),
            'order' => ''
        ),
		'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_updated',
            'conditions' => '',
            'fields' => array('id', 'first_name', 'last_name', 'fullname'),
            'order' => ''
        ),
	);
    var $hasMany = array(
        'ProjectCommunicationUrl' => array(
            'className' => 'ProjectCommunicationUrl',
            'foreignKey' => 'communication_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
        ),
	);
}
?>