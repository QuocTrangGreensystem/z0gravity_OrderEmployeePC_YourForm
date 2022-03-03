<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrSubProgram extends AppModel {

    var $name = 'ProjectAmrSubProgram';
    public $displayField = 'amr_sub_program';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'ProjectAmrProgram' => array(
            'className' => 'ProjectAmrProgram',
            'foreignKey' => 'project_amr_program_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'amr_sub_program' => array(
                'atleast' => array(
                    'rule' => array('between', 1, 255),
                    'message' => __('Cannot be empty', true)
                )
            ),
            'project_amr_program_id' => array(
                'notEmpty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('Cannot be empty', true)
                )
            ),
        );
    }

    var $hasMany = array(
        'ProjectAmr' => array(
            'className' => 'ProjectAmr',
            'foreignKey' => 'project_amr_sub_program_id',
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

}
?>