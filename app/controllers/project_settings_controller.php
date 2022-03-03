<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectSettingsController extends AppController {
    public function beforeFilter(){
        parent::beforeFilter();
    }

    public function index(){
        if( !isset($this->employee_info['Company']['id']) ){
            $this->Session->setFlash(__('You do not have permission to access this page', true), 'error');
            return $this->redirect('/project_phases');
        }
        $setRemain = $this->get();
        $this->set(compact('setRemain'));
        if(!empty($this->data)){
            $result = false;
            $this->ProjectSetting->id = $setRemain['ProjectSetting']['id'];
            if($this->ProjectSetting->save($this->data['Project'])){
                $result = true;
            }
            if($result == true){
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
        }
    }
	
    public function get(){
        $cid = $this->employee_info['Company']['id'];
        $data = $this->ProjectSetting->find('first', array(
            'conditions' => array('company_id' => $cid)
        ));
        if( empty($data) ){
            //insert data
            $data = array(
                'ProjectSetting' => array(
                    'company_id' => $cid,
                    'show_freeze' => 0
                )
            );
            $this->ProjectSetting->create();
            $this->ProjectSetting->save($data);
            $data['ProjectSetting']['id'] = $this->ProjectSetting->id;
        }
        return $data;
    }
    
    public function security(){
        
    }
}
?>