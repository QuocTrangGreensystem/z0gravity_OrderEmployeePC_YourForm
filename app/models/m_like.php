<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class MLike extends AppModel {
	public $name = 'MLike';
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'modelId',
			'conditions' => array('modelType' => 'Project'),
		)
	);
	public $recursive = -1;
	
	/* Function toggle_like
	 * @params: $employee_id, $project_id
	 * return: 
		true: liked
		false: unliked
	*/
	function toggle_like($employee_id, $project_id){
		$result = array(
			'liked' => 0,
			'countLikes' => 0
		);
		$last_item = $this->find('first', array(
			'conditions' => array(
				'modelType' => 'Project',
				'modelId' => $project_id,
				'owner_id' => $employee_id
			),
			'fields' => array('id', 'id')
		));
		// debug( $last_item ); exit;
		if( $last_item){
			foreach( $last_item as $item_id){
				// debug( $item_id ); exit;
				$this->id = $item_id;
				$this->delete();
			}
		}else{
			$data = array(
				'modelType' => 'Project',
				'modelId' => $project_id,
				'owner_id' => $employee_id,
				'create' =>  date('Y-m-d H:i:s'), 
				'updated' =>  date('Y-m-d H:i:s'), 
			);
			$this->create();
			if( $this->save($data)) $result['liked'] = 1; //liked
		}
		$countLikes = $this->get_like_by_project($project_id);
		$result['countLikes'] = (int)$countLikes[$project_id];
		// debug($result); exit;
		return $result;
	}
	function get_like_by_project($project_ids){
		// debug( $project_ids);
		$countLikes = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'modelType' => 'Project',
				'modelId' => $project_ids,
			),
			'fields' => array('MLike.modelId', 'count(MLike.id) as likes'),
			'group' => array('modelId')
		));
		// debug( $data); exit;
		$likes = array();
		foreach($countLikes as $k => $v){
			$likes[$v['MLike']['modelId']] = $v['0']['likes'];
		}
		$results = array();
		foreach( (array) $project_ids as $project_id){
			$results[$project_id] = !empty($likes[$project_id])? $likes[$project_id] : 0;
		}
		// debug( $results); exit;
		return $results;
	}
	function get_like_by_employee($employee_id, $project_ids=null){
		if( empty($project_ids)){
			$liked = $this->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'modelType' => 'Project',
					'modelId' => $project_ids,
					'owner_id' => $employee_id,
				),
				'fields' => array('modelId','modelId')
			));
			return $liked;
		}
		$liked = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'modelType' => 'Project',
				'modelId' => $project_ids,
				'owner_id' => $employee_id,
			),
			'fields' => array('modelId','modelId')
		));
		$results = array();
		foreach( (array) $project_ids as $project_id){
			$results[$project_id] = isset($liked[$project_id])? 1 : 0;
		}
		return $results;
	}
}