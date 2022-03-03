<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ExpectationColorsController extends AppController {
    //sync with translations controller
    public $list = array(
        'list_color_1' => 'List color 1',
        'list_color_2' => 'List color 2',
        'list_color_3' => 'List color 3',
        'list_color_4' => 'List color 4',
        'list_color_5' => 'List color 5',
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
        $list = $this->ExpectationColor->find('all', array(
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'key' => $id
            )
        ));
        $this->set('list', $list);
        $this->set('dataset', $id);
    }
    public function update($id = null){
		$result = false;
		$this->layout = false;
		if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project function', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if( !empty($this->data) ){
            // xoa default neu co default = 1.
            if($this->data['default'] == 1){
                $last = $this->ExpectationColor->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'key' => $this->data['key'],
                        'default' => 1
                    )
                ));
                if(!empty($last)){
                    $this->ExpectationColor->id = $last['ExpectationColor']['id'];
                    $this->ExpectationColor->save(array('default' => 0));
                }
            }
            $save = array(
                'company_id' => $this->employee_info['Company']['id'],
                'color' => $this->data['color'],
                'default' => $this->data['default'],
                'key' => $this->data['key'],
                'display' => !empty($this->data['display']) ? 1 : 0,
            );
            if( !$this->data['id'] ){
                $this->ExpectationColor->create();
            } else {
                $this->ExpectationColor->id = $this->data['id'];
            }
			$this->data = $this->ExpectationColor->read(null, $id);
			$this->data = $this->data['ExpectationColor'];
			$result = true;
            $this->ExpectationColor->save($save);
            $this->Session->setFlash(__('Saved', true), 'success');
        }else{
			$this->Session->setFlash(__('Not saved', true), 'error');
		}
		$this->set(compact('result'));
    }

    public function delete($name, $id){
        $data = $this->ExpectationColor->read(null, $id);
        if( !empty($data) && $data['ExpectationColor']['company_id'] == $this->employee_info['Company']['id'] ){
            //do delete
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->ExpectationColor->delete($id);
            $this->redirect(array('action' => 'index', $name));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $name));
    }
}
