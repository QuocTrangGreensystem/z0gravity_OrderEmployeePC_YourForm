<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectFunctionEmployeeRefer extends AppModel {

    var $name = 'ProjectFunctionEmployeeRefer';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'ProjectFunction' => array(
            'className' => 'ProjectFunction',
            'foreignKey' => 'function_id',
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
        ),
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
        ,
        'ProjectTeam' => array(
            'className' => 'ProjectTeam',
            'foreignKey' => 'project_team_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $virtualFields = array(
        'vtual_create_by_lastname' => 'SELECT last_name FROM employees as ProjectFunctionEmployeeRefer WHERE ProjectFunctionEmployeeRefer.id = employee_id',
        'vtual_create_by_firstname' => 'SELECT first_name FROM employees as ProjectFunctionEmployeeRefer WHERE ProjectFunctionEmployeeRefer.id = employee_id',
            //   $this->virtualFields['fullname'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
    );

    // function saveEvolutionImpact($data_evolution_impacts ,$project_evolution_id, $project_id){
//        $this->deleteAll(array("ProjectEvolutionImpactRefer.project_evolution_id"=>$project_evolution_id));
//        foreach ($data_evolution_impacts as $data_evolution_impact){
//            $this->data["ProjectEvolutionImpactRefer"]["project_evolution_id"] = $project_evolution_id;
//            $this->data["ProjectEvolutionImpactRefer"]["project_evolution_impact_id"] = $data_evolution_impact;
//            $this->data["ProjectEvolutionImpactRefer"]["project_id"] = $project_id;
//            $this->save($this->data["ProjectEvolutionImpactRefer"]);
//            $this->id=false;
//        }
//    }
}
?>