<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectFunction extends AppModel {

    var $name = 'ProjectFunction';
    var $displayField = 'name';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $hasMany = array(
        'ProjectTeam' => array(
            'className' => 'ProjectTeam',
            'foreignKey' => 'project_function_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ), 
        'ProjectEmployeeProfitFunctionRefer' => array(
            'className' => 'ProjectEmployeeProfitFunctionRefer',
            'foreignKey' => 'function_id',
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
    var $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id'),
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'name' => array(
                'Length of the name' => array(
                    'rule' => array('between', 1, 255),
                    'message' => __('Cannot be empty', true),
                )
            ),
            'company_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('Cannot be empty', true),
            ))
        );
    }

}
?>