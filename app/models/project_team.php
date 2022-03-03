<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectTeam extends AppModel {

    var $name = 'ProjectTeam';

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    function __construct() {
        parent::__construct();
        $this->validate = array(
//            'employee_id' => array(
//                'notempty' => array(
//                    'rule' => array('notempty'),
//                    'message' => __('The employee is not blank!', true),
//                //'allowEmpty' => false,
//                //'required' => false,
//                //'last' => true, // Stop validation after this rule
//                //'on' => 'create', // Limit validation to 'create' or 'update' operations
//                ),
//            ),
//            'project_function_id' => array(
//                'notempty' => array(
//                    'rule' => array('notempty'),
//                    'message' => __('The function is not blank!', true),
//                //'allowEmpty' => false,
//                //'required' => false,
//                //'last' => true, // Stop validation after this rule
//                //'on' => 'create', // Limit validation to 'create' or 'update' operations
//                ),
//            ),
            //'profit_center_id' => array(
//                'notempty' => array(
//                    'rule' => array('notempty'),
//                    'message' => __('The profit center is not blank!', true),
//                //'allowEmpty' => false,
//                //'required' => false,
//                //'last' => true, // Stop validation after this rule
//                //'on' => 'create', // Limit validation to 'create' or 'update' operations
//                ),
//            )
        );
    }

    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectFunction' => array(
            'className' => 'ProjectFunction',
            'foreignKey' => 'project_function_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProfitCenter' => array(
            'className' => 'ProfitCenter',
            'foreignKey' => 'profit_center_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
        /*,
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )*/
    );
    var $hasMany = array(
        'ProjectFunctionEmployeeRefer' => array(
            'className' => 'ProjectFunctionEmployeeRefer',
            'foreignKey' => 'project_team_id',
            'dependent' => true,
            'order' => 'is_backup ASC',
        )
    );

    function updateProjectTeam($project_id, $projectTeamData) {
        // check validate:
        $this->recursive = -1;
        $projectTeamData = $this->find("all", array("conditions" => array("project_id" => $project_id)));

        //exit("model Project Team");
    }

}
?>