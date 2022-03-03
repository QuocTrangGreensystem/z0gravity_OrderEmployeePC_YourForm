<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectEvolutionImpactRefer extends AppModel {
	var $name = 'ProjectEvolutionImpactRefer';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ProjectEvolution' => array(
			'className' => 'ProjectEvolution',
			'foreignKey' => 'project_evolution_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectEvolutionImpact' => array(
			'className' => 'ProjectEvolutionImpact',
			'foreignKey' => 'project_evolution_impact_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    
    function saveEvolutionImpact($data_evolution_impacts ,$project_evolution_id, $project_id){
        $this->deleteAll(array("ProjectEvolutionImpactRefer.project_evolution_id"=>$project_evolution_id));
        foreach ($data_evolution_impacts as $data_evolution_impact){
            $this->data["ProjectEvolutionImpactRefer"]["project_evolution_id"] = $project_evolution_id;
            $this->data["ProjectEvolutionImpactRefer"]["project_evolution_impact_id"] = $data_evolution_impact;
            $this->data["ProjectEvolutionImpactRefer"]["project_id"] = $project_id;
            $this->save($this->data["ProjectEvolutionImpactRefer"]);
            $this->id=false;
        }
    }
}
?>