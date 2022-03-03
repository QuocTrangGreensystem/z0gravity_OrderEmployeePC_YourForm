<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ExpectationDatasetsController extends AppController {
    //sync with translations controller
    public $list = array(
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
        'list_15' => 'List 15',
        'list_16' => 'List 16',
        'list_17' => 'List 17',
        'list_18' => 'List 18',
        'list_19' => 'List 19',
        'list_20' => 'List 20',
        'list_21' => 'List 21',
        'list_22' => 'List 22',
        'list_23' => 'List 23',
        'list_24' => 'List 24',
        'list_25' => 'List 25',
        'list_26' => 'List 26',
        'list_27' => 'List 27',
        'list_28' => 'List 28',
        'list_29' => 'List 29',
        'list_30' => 'List 30',
    );

    public $helpers = array('Validation');

    public function beforeFilter(){
        parent::beforeFilter();
    }

    public function getList(){
        return $this->list;
    }

    public function index($id = null){
        if( !isset($this->list[$id]) )$this->redirect('/404');
        $list = $this->ExpectationDataset->find('all', array(
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
                'company_id' => $this->data['company_id'],
                'eng' => $this->data['eng'],
                'fre' => $this->data['fre'],
                'dataset_name' => $this->data['dataset_name'],
                'display' => (!empty($this->data['display']) && $this->data['display'] == 'yes' ) ? 1 : 0,
            );
            if( !$this->data['id'] ){
                $this->ExpectationDataset->create();
            } else {
                $this->ExpectationDataset->id = $this->data['id'];
            }
			$this->data = $this->ExpectationDataset->read(null, $id);
			$this->data = $this->data['ExpectationDataset'];
            $this->ExpectationDataset->save($save);
			$result = true;
            $this->Session->setFlash(__('Saved', true), 'success');
        }else{
			$this->Session->setFlash(__('Not saved', true), 'error');
		}
		$this->set(compact('result'));
    }

    public function delete($id = null){
        $data = $this->ExpectationDataset->read(null, $id);
        if( !empty($data) && $data['ExpectationDataset']['company_id'] == $this->employee_info['Company']['id'] ){
            //do delete
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->ExpectationDataset->delete($id);
            $this->redirect(array('action' => 'index/', $this->params['pass'][1] ));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index/', $this->params['pass'][1] ));
    }
}
