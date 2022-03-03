<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class MFavorite extends AppModel {
	public $name = 'MFavorite';
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'modelId',
			'conditions' => array('modelType' => 'Project'),
		)
	);
	public $recursive = -1;
	
	/* Function toggle_favorite
	 * @params: $employee_id, $project_id
	 * return: 
		1: favorite
		0: non-favorite
	*/
	function toggle_favorite($employee_id, $project_id){
		$result = 0;
		$last_item = $this->find('first', array(
			'conditions' => array(
				'modelType' => 'Project',
				'modelId' => $project_id,
				'owner_id' => $employee_id
			),
			'fields' => array('id', 'id')
		));
		if( $last_item){
			foreach( $last_item as $item_id){
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
			if( $this->save($data)) $result = 1;
		}
		return $result;
	}
	function get_favorite_by_employee($employee_id,  $project_ids = null){
		if( empty($project_ids)){
			$fav = $this->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'modelType' => 'Project',
					'owner_id' => $employee_id,
				),
				'fields' => array('modelId','modelId')
			));
			return $fav;
		}
		$fav = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'modelType' => 'Project',
				'owner_id' => $employee_id,
				'modelId' => $project_ids,
			),
			'fields' => array('modelId','modelId')
		));
		$results = array();
		foreach( (array) $project_ids as $project_id){
			$results[$project_id] = isset($fav[$project_id])? 1 : 0;
		}
		return $results;
	}
}