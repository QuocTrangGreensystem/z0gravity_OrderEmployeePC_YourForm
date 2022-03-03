<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
App::import('Model','Project');
class ProjectEvolution extends AppModel {
	var $name = 'ProjectEvolution';
	var $displayField = 'project_evolution';
	function __construct(){
		parent::__construct();
		$this->validate = array(
			//'project_id' => array(
			//	'notempty' => array(
			//		'rule' => array('notempty'),
			//		'message' => __('Invalid Project',true),
			//	),
			//),
			'project_evolution' => array(
				'notempty' => array(
					'rule' => array('notEmpty'),
					'message' => __('Project evolution is not blank',true),
				),
			),
			//'project_evolution_type_id' => array(
			//	'numeric' => array(
			//		'rule' => array('numeric'),
			//		'message' => __('Type Evolution is not blank!',true)
			//    ),
			//),
		//	'evolution_applicant' => array(
			//	'notempty' => array(
			//		'rule' => array('notempty'),
			//		'message' =>__('Applicant is not blank',true),
		    //	),
			//)
			//,'supplementary_budget' => array(
			//	'numeric' => array(
			//		'rule' => array('numeric'),
			//		'message' => __('Supplementary budget must be number!',true),
			//	),
			//),
		);
	}
    
    public function afterSave() {
        if(!empty($this->data[$this->alias]['project_id'])){
            $this->Project->id = $this->data[$this->alias]['project_id'];
            $data['task_flag'] = 0;
            $this->Project->save($data);
        }
    }
    
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProjectEvolutionType' => array(
			'className' => 'ProjectEvolutionType',
			'foreignKey' => 'project_evolution_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Employee' => array(
			'className' => 'Employee',
			'foreignKey' => 'evolution_applicant',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ProjectEvolutionImpactRefer' => array(
			'className' => 'ProjectEvolutionImpactRefer',
			'foreignKey' => 'project_evolution_id',
			'dependent' => true,
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