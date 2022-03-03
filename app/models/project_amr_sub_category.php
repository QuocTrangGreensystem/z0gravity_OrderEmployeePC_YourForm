<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrSubCategory extends AppModel {
	var $name = 'ProjectAmrSubCategory';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	function __construct(){
		parent::__construct();
		$this->validate = array(
		'amr_sub_category' => array(
			'The Project AMR Sub Categories must be between 1 and 255 characters.'=>array(
				'rule'=>array('between',1,255),
				'message' =>__('Cannot be empty',true)
			)
		),
			'company_id' => array(
				'Notempty'=>array(
					'rule'=>array('notEmpty'),
					'message'=>__('Cannot be empty', true),
				),          
			)
	);
	}
	var $hasMany = array(
		'ProjectAmr' => array(
			'className' => 'ProjectAmr',
			'foreignKey' => 'project_amr_sub_category_id',
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
            'ProjectAmrCategory' => array(
			'className' => 'ProjectAmrCategory',
			'foreignKey' => 'project_amr_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
    );

}
?>