<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivitySettingsController extends AppController {
    
    public $uses = array();
    
    public function index(){
        
        $this->loadModel('ActivitySetting');
        $setRemain = $this->ActivitySetting->find('first');
        $managerHour = !empty($this->employee_info['Company']['manage_hours']) ? $this->employee_info['Company']['manage_hours'] : 0;
        $this->set(compact('setRemain', 'managerHour'));
        if(!empty($this->data)){
            $result = false;
            $this->ActivitySetting->id = 1;
            if($this->ActivitySetting->save($this->data['Activity'])){
                $result = true;
            }
            if($result == true){
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Activity Settings could not be saved. Please, try again.', true), 'error');
            }
        }
    }
    
    public function forecasts(){
        
    }
    
    public function diary(){
        
    }
    public function team_workload(){
        
    }
    
    private function _projectTeam(){
        $this->loadModel('ProjectTeam');
        $project_id = $this->_projects();
        $projectTeams = $this->ProjectTeam->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'project_function_id', 'allow_request'),
                'order' => array('id')
            ));
        return $projectTeams;
    }
    
    private function _employee(){
        $this->loadModel('ProjectFunctionEmployeeRefer');
        
        $projectTeams = $this->_projectTeam();
        $projectTeams = Set::combine($projectTeams, '{n}.ProjectTeam.id', '{n}.ProjectTeam.id');
        
        $employees = $this->ProjectFunctionEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_team_id' => $projectTeams),
                'fields' => array('id', 'employee_id', 'allow_request')
            ));
        return $employees;   
    }
    private function _remain(){
        $this->loadModel('ProjectTask');
        $project_id = $this->_projects();
        $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('id', 'project_id', 'estimated', 'allow_request')
            ));
        $projectTasks = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask');
        $taskRequests = $this->_projectRequest();
        foreach($projectTasks as $id => $projectTask){
            $projectTasks[$id]['consumed'] = isset($taskRequests[$id]) ? $taskRequests[$id] : 0;
            $projectTasks[$id]['estimated'] = isset($projectTask['estimated']) ? $projectTask['estimated'] : 0;
        }
        $remains = array();
        foreach($projectTasks as $id => $projectTask){
            $remains[$id]['id'] = $projectTask['id'];
            $remains[$id]['remain'] = $projectTask['estimated'] - $projectTask['consumed'];
            $remains[$id]['allow_request'] = $projectTask['allow_request'];
        }
        foreach($remains as $key => $remain){
            if($remain['remain'] != 0){
                unset($remains[$key]);
            }
        }
        
        return $remains;
    }
    
    private function _projectRequest(){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        
        $projectName = $this->viewVars['employee_info'];
        
        $activityRequests = $this->ActivityRequest->find(
                'all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $projectName['CompanyEmployeeReference']['company_id'],
                'NOT' => array('value' => 0, "task_id" => null),
            )
                )
        );
        $activityRequests = Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0.value');
        $listId = array_keys($activityRequests);
        
        $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.id' => $listId),
                'fields' => array('id', 'project_task_id')
            ));
        $requestTasks = array();
        foreach($activityTasks as $activityTask){
            foreach($activityRequests as $id => $activityRequest){
                if($id == $activityTask['ActivityTask']['id']){
                    $requestTasks[$id]['id'] = $activityTask['ActivityTask']['id'];
                    $requestTasks[$id]['project_task_id'] = $activityTask['ActivityTask']['project_task_id'];
                    $requestTasks[$id]['consumed'] = $activityRequest;
                }
            }
        }
        $requestTasks = Set::combine($requestTasks, '{n}.project_task_id', '{n}.consumed');
        return $requestTasks;
    }
    
    private function _projects(){
        $this->loadModel('Project');
        $projects = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('activity_id' => null)
                ),
                'fields' => array('id')
            ));
        return $projects;
    }
}
?>