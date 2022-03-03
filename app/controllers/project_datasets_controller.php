<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDatasetsController extends AppController {
	//sync with translations controller
	public $list = array(
		'list' => 'Lists',
		'list_1' => 'List 1',
		'list_2' => 'List 2',
		'list_3' => 'List 3',
		'list_4' => 'List 4',
		'list_5' => 'List 5',
		'list_6' => 'List 6',
		'list_7' => 'List 7',
		'list_8' => 'List 8',
		'list_9' => 'List 9',
		'list_10' => 'List 10',
		'list_11' => 'List 11',
		'list_12' => 'List 12',
		'list_13' => 'List 13',
		'list_14' => 'List 14',

		'list_muti_1' => 'List(multiselect) 1',
		'list_muti_2' => 'List(multiselect) 2',
		'list_muti_3' => 'List(multiselect) 3',
		'list_muti_4' => 'List(multiselect) 4',
		'list_muti_5' => 'List(multiselect) 5',
		'list_muti_6' => 'List(multiselect) 6',
		'list_muti_7' => 'List(multiselect) 7',
		'list_muti_8' => 'List(multiselect) 8',
		'list_muti_9' => 'List(multiselect) 9',
		'list_muti_10' => 'List(multiselect) 10',
	);

	public $helpers = array('Validation');

	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function getList(){
		return $this->list;
	}

	public function index($id = null){
		if( !isset($this->list[$id]) )$this->redirect(array( array_pop(array_keys($this->list)) ));
		$list = $this->ProjectDataset->find('all', array(
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'dataset_name' => $id
			)
		));
		$this->set('list', $list);
		$this->set('dataset', $id);
	}

	public function update($id = null){
		$result = false;
		$this->layout = false;
		if( !empty($this->data) ){
			$save = array(
				//'id' => $this->data['ProjectDataset']['id'],
				'company_id' => $this->data['company_id'],
				'name' => $this->data['name'],
				'dataset_name' => $this->data['dataset_name'],
                'display' => $this->data['display'] ,
			);
			if( !$this->data['id'] ){
				$this->ProjectDataset->create();
			} else {
				$this->ProjectDataset->id = $this->data['id'];
			}
			if ($this->ProjectDataset->save($save)) {
				$this->data = $this->ProjectDataset->read(null, $id);
				$this->data = $this->data['ProjectDataset'];
				$result = true;
				$this->Session->setFlash(__('Saved', true), 'success');
			} else {
				$this->Session->setFlash(__('NOT SAVED', true), 'error');
			}
		}
		$this->set(compact('result'));
	}

	public function delete($id = null){
		if (empty($id)) {
            $this->Session->setFlash(__('Invalid', true), 'error');
            $this->redirect(array('action' => 'index',$this->params['pass'][1]));
        }
		//do delete
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectDataset'));
        $isUsig = $this->isUsing($id);
        if($check && !$isUsig){
			$this->Session->setFlash(__('Deleted', true), 'success');
			$this->ProjectDataset->delete($id);
			$this->redirect(array('action' => 'index', $this->params['pass'][1]));
		}
		$this->Session->setFlash(__('Not deleted', true), 'error');
		$this->redirect(array('action' => 'index', $this->params['pass'][1]));
	}
	private function isUsing($id){
        $this->loadModels('ProjectDataset', 'Project', 'ProjectListMultiple');
        $key = $this->ProjectDataset->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectDataset.id' => $id),
			'fields' => array('id', 'dataset_name')
        ));
		if( empty($key)) return false;
		$key = $key['ProjectDataset']['dataset_name'];
		if( preg_match('/^list_muti_/', $key)){
			$key = 'project_' . str_replace('muti', 'multi', $key);
			$count =  $this->ProjectListMultiple->find('count', array(
				'recursive' => -1,
				'conditions' => array( 'project_dataset_id' => $id )
			));
		}elseif( preg_match('/^list_/', $key)){ 
			$count =  $this->Project->find('count', array(
				'recursive' => -1,
				'conditions' => array($key =>$id )
			));
		}else{
			return false;
		}
		return $count;
    }
}
