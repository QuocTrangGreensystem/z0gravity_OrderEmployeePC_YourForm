<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectLivrableActor extends AppModel {
	var $name = 'ProjectLivrableActor';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ProjectLivrable' => array(
			'className' => 'ProjectLivrable',
			'foreignKey' => 'project_livrable_id',
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
	);
    
    function saveLivrableActors($data_livrable_actors, $project_livrable_id, $project_id=null){
        $this->deleteAll(array("ProjectLivrableActor.project_livrable_id"=>$project_livrable_id));
        foreach ($data_livrable_actors as $data_livrable_actor){
            $this->data["ProjectLivrableActor"]["project_livrable_id"] = $project_livrable_id;
            $this->data["ProjectLivrableActor"]["employee_id"] = $data_livrable_actor;
            $this->data["ProjectLivrableActor"]["project_id"] = $project_id;
            $this->save($this->data["ProjectLivrableActor"]);
            $this->id=false;
        }
    }
}
?>