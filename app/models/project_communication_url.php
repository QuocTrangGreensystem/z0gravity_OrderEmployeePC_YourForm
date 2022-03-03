<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectCommunicationUrl extends AppModel {
	public $recursive = -1;
	var $belongsTo = array(
        'ProjectCommunication' => array(
            'className' => 'ProjectCommunication',
            'foreignKey' => 'communication_id ',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
	);
}
?>