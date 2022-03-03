<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectPhasePlan extends AppModel {

    var $name = 'ProjectPhasePlan';
    var $actsAs = array('Containable');
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPhase' => array(
            'className' => 'ProjectPhase',
            'foreignKey' => 'project_planed_phase_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPhaseStatus' => array(
            'className' => 'ProjectPhaseStatus',
            'foreignKey' => 'project_phase_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPart' => array(
            'className' => 'ProjectPart',
            'foreignKey' => 'project_part_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'project_planed_phase_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The phase is not blank!', true),
                    'allowEmpty' => false,
                ),
            )
            //'phase_planed_start_date' => array(
//                'notempty' => array(
//                    'allowEmpty' => false,
//                    'rule' => array('date'),
//                    'message' => __('The start date is not blank!', true),
//                ),
//            ),
//            'phase_planed_end_date' => array(
//                'notempty' => array(
//                    'allowEmpty' => false,
//                    'rule' => array('date'),
//                    'message' => __('The end date is not blank!', true),
//                ),
//            )
        );
    }

    function generateListPhaseProject($conditions = null, $order = null, $limit = null, $keyPath = null, $valuePath = null, $groupPath = null, $page = 1, $recursive = 0) {
        //get the data
        $data = $this->find('all', array($conditions, null, $order, $limit, $page, $recursive));
        if (!empty($data)) {
            $data = Set::combine($data, $keyPath, $valuePath, $groupPath);
        }
        return $data;
    }

    function AddProjectPhasePlan($project_id, $project_phase_id, $st_date, $end_date) {
        $this->data["ProjectPhasePlan"]["project_id"] = $project_id;
        $this->data["ProjectPhasePlan"]["project_planed_phase_id"] = $project_phase_id;
        $this->data["ProjectPhasePlan"]["phase_planed_start_date"] = $st_date;
        $this->data["ProjectPhasePlan"]["phase_planed_end_date"] = $end_date;
        $this->data["ProjectPhasePlan"]["weight"] = 1;
        $this->save($this->data["ProjectPhasePlan"]);
    }

    function SaveProjectPhasePlan($id, $project_id, $project_phase_id, $st_date, $end_date) {
        $this->data["ProjectPhasePlan"]["id"] = $id;
        $this->data["ProjectPhasePlan"]["project_id"] = $project_id;
        $this->data["ProjectPhasePlan"]["project_planed_phase_id"] = $project_phase_id;
        //$this->data["ProjectPhasePlan"]["phase_planed_start_date"] = $st_date;
        //$this->data["ProjectPhasePlan"]["phase_planed_end_date"] = $end_date;
        $this->save($this->data["ProjectPhasePlan"]);
    }

    public function beforeSave($options = array()) {
        $this->data = $this->parseData($this->data);
        return parent::beforeSave($options);
    }

    public function parseData($data) {
        foreach (array('predecessor', 'project_part_id', 'project_phase_status_id') as $field) {
            if (isset($data[$this->alias][$field]) && ($data[$this->alias][$field] == 'null' || empty($data[$this->alias][$field]))) {
                $data[$this->alias][$field] = null;
            }
        }
        return $data;
    }

	function _checkCanNotRemovedate($phasePlans){
		$this->loadModel('ProjectTask');
		$date_cond = array();
		$keys = array('task_start_date', 'task_end_date', 'initial_task_start_date', 'initial_task_end_date');
		foreach( $keys as $k){
			$date_cond[] = array(
				$k . ' is not NULL',
				$k . ' != 0000-00-00',
			);
		}
		$res = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_planed_phase_id' => $phasePlans,
				'OR' => $date_cond,
			),
			// 'fields' => array('id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'initial_task_start_date', 'initial_task_end_date')
			'fields' => array('project_planed_phase_id', 'id')
		));
		if( !empty($res)) $res = array_keys($res);
		return $res;
	}

}