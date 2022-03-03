<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Currency extends AppModel {
	var $name = 'Currency';
	var $displayField = 'sign_currency';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	function __construct(){
	parent::__construct();
    	$this->validate = array(
    	   'sign_currency' => array(
            'not Empty'=>array(
    		    'rule' => 'notEmpty',
                'message' => __('Cannot be empty',true)
    	       )
            ),
            'company_id'=>array(
    			'notEmpty'=>array(
                    'rule'=>'notEmpty','message'=>__('Cannot be empty',true)
    			)
    	   )
    	);
	}
	
	var $hasMany = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'currency_id',
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