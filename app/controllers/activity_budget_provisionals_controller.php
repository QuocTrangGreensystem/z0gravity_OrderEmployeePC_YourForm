<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityBudgetProvisionalsController extends AppController {
    /**
     * Controller nay khong co model
     */
    var $uses = array();
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityBudgetProvisionals';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($activity_id = null, $viewFollow = 'man-day') {
        $viewFollow = ($viewFollow != 'euro') ? 'man-day' : $viewFollow;
        $this->loadModels('Project', 'ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectBudgetProvisional','Activity');
        $activities = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('Activity.id' => $activity_id),
            'fields' => array('id', 'name', 'project')
        ));
        $internals = $externals = $provisionals = array();
		$bg_currency = $this->getCurrencyOfBudget();
        if(!empty($activities) && !empty($activities['Activity']['project'])){
            $project_id = $activities['Activity']['project'];
            $projects = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('id', 'project_name', 'start_date', 'end_date', 'activity_id')
            ));
            $internals = $this->ProjectBudgetInternalDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'name', 'budget_md', 'average')
            ));
            $externals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'name', 'man_day', 'budget_erro')
            ));
            $_provisionals = $this->ProjectBudgetProvisional->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id, 'view' => $viewFollow),
                'fields' => array('id', 'project_id', 'model', 'model_id', 'date', 'value')
            ));
            $provisionals = array();
            if(!empty($_provisionals)){
                foreach($_provisionals as $_provisional){
                    $dx = $_provisional['ProjectBudgetProvisional'];
                    $provisionals[$dx['model']][$dx['model_id']][$dx['date']] = $dx;
                }
            }
        } else {
            $this->Session->setFlash(__('Provisionals screen not build with the activity doesn\'t linked project', true), 'success');
        }
        $this->set(compact('projects', 'internals', 'externals', 'provisionals', 'project_id', 'activity_id', 'activities', 'viewFollow', 'bg_currency'));
    }
    
    /**
     * Update
     *
     * @return void
     * @access public
     */
    function update() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectBudgetProvisional');
        if (!empty($this->data)) {
            $saveData = $this->data;
            $splitId = !empty($this->data['id']) ? explode('_', $this->data['id']) : '';
            $data = array();
            if(!empty($splitId)){
                $data['project_id'] = !empty($this->data['project_id']) ? $this->data['project_id'] : 0;
                $data['activity_id'] = !empty($this->data['activity_id']) ? $this->data['activity_id'] : 0;
                $data['model'] = !empty($splitId[1]) ? $splitId[1] : 'Internal';
                $data['model_id'] = !empty($splitId[0]) ? $splitId[0] : 'Internal';
                $data['view'] = !empty($this->data['view']) ? $this->data['view'] : 'man-day';
                unset($this->data['id']);
                unset($this->data['project_id']);
                unset($this->data['activity_id']);
                unset($this->data['view']);
                if(!empty($this->data)){
                    foreach($this->data as $date => $value){
                        if(is_numeric($date)){
                            $data['date'] = $date;
                            $rowUpdate = $this->ProjectBudgetProvisional->find('first', array(
                                'recursive' => -1,
                                'conditions' => array($data),
                                'fields' => array('id')
                            ));
                            $this->ProjectBudgetProvisional->create();
                            if(!empty($rowUpdate) && !empty($rowUpdate['ProjectBudgetProvisional']['id'])){
                                $this->ProjectBudgetProvisional->id = $rowUpdate['ProjectBudgetProvisional']['id'];
                            }
                            $data['value'] = $value;
                            if($this->ProjectBudgetProvisional->save($data)){
                                $this->Session->setFlash(__('Saved', true), 'success');
                            } else {
                                $this->Session->setFlash(__('Not Saved', true), 'error');
                            }
                            unset($data['value']);
                        }
                    }
                } else {
                    $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
                }
            } else {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            }
            $this->data = $saveData;
            $result = true;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
}
?>