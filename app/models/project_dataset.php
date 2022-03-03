<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectDataset extends AppModel {
	public $displayField = 'name';
	function __construct(){
		parent::__construct();
		$this->validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => array('notEmpty'),
					'message' => __('Cannot be empty', true)
				)
			)
		);
	}
}