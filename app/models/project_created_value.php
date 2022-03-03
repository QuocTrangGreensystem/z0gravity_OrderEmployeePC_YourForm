<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectCreatedValue extends AppModel {

    var $name = 'ProjectCreatedValue';
    var $displayField = 'name';

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    function __construct() {
        parent::__construct();
        $this->validate = array(
            'description' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('Cannot be empty', true),
                ),
            ),
            'value' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('Cannot be empty', true),
                ),
                'numeric' => array(
                    'rule' => array('numeric'),
                    'message' => __('Only numbers allowed.', true),
                ),
                'range' => array(
                    'rule' => array('range', -1, 11),
                    'message' => __('Please enter a number between 0 and 10', true),
                )
            ),
            'company_id' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('Cannot be empty', true),
                ),
            )
        );
    }

    var $hasMany = array(
//        'Project' => array(
//            'className' => 'Project',
//            'foreignKey' => 'project_created_value_id',
//            'dependent' => false,
//            'conditions' => '',
//            'fields' => '',
//            'order' => '',
//            'limit' => '',
//            'offset' => '',
//            'exclusive' => '',
//            'finderQuery' => '',
//            'counterQuery' => ''
//        )
    );
    var $belongsTo = array(
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