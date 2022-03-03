<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAcceptanceType extends AppModel {
	var $validate = array(
		'name' => array(
			'b'=>array(
				'rule'=>array('between',1,255),
				'message' => 'Cannot be empty'
			),
		),
		'company_id' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
	);
}