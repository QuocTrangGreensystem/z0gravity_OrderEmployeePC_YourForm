<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectCreatedVal extends AppModel {

    var $name = 'ProjectCreatedVal';
    var $displayField = 'name';

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    function __construct() {
        parent::__construct();
        $this->validate = array(
            'description' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('The description is not blank.', true),
                ),
            ),
            'value' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('The value is not blank.', true),
                ),
                'rule' => array('numeric'),
                //'rule'=>'/^[a-z0-9]$/i',
                'message' => __('Only numbers allowed.', true),
            ),
            'company_id' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('The company is not blank.', true),
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
//        'Company' => array(
//            'className' => 'Company',
//            'foreignKey' => 'company_id',
//            'conditions' => '',
//            'fields' => '',
//            'order' => ''
//        )
    );

}
?>