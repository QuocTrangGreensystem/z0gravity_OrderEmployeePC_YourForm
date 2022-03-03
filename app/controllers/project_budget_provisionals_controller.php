<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetProvisionalsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectBudgetProvisionals';

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
    function index($project_id = null, $viewFollow = 'man-day') {
		$usCanWrite = ($this->employee_info['Role']['name'] == 'admin') || $this->_checkRole(false, $project_id);
        $viewFollow = ($viewFollow != 'euro') ? 'man-day' : $viewFollow;
        $this->_checkWriteProfile('provisional');
        $this->loadModels('Project', 'ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectPhasePlan');
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'project_name', 'start_date', 'end_date', 'activity_id')
        ));
        $projectStart = !empty($projects) && !empty($projects['Project']['start_date']) && $projects['Project']['start_date'] != '0000-00-00' ? strtotime($projects['Project']['start_date']) : 0;
        $projectEnd = !empty($projects) && !empty($projects['Project']['end_date']) && $projects['Project']['end_date'] != '0000-00-00' ? strtotime($projects['Project']['end_date']) : 0;
        $internals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'budget_md', 'average', 'profit_center_id', 'funder_id')
        ));
        $externals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'man_day', 'budget_erro', 'profit_center_id', 'funder_id')
        ));
        $_provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'view' => $viewFollow),
            'fields' => array('id', 'project_id', 'model', 'model_id', 'date', 'value')
        ));
        $phasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectPhasePlan.project_id' => $project_id,
                'NOT' => array(
                    'phase_real_start_date' => '0000-00-00',
                    'phase_real_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'MIN(ProjectPhasePlan.phase_real_start_date) as min_date',
                'MAX(ProjectPhasePlan.phase_real_end_date) as max_date'
            )
        ));
        $realStart = !empty($phasePlans[0][0]['min_date']) ? strtotime($phasePlans[0][0]['min_date']) : 0;
        $realEnd = !empty($phasePlans[0][0]['max_date']) ? strtotime($phasePlans[0][0]['max_date']) : 0;
        $provisionals = $dayOfPros = array();
        if(!empty($_provisionals)){
            foreach($_provisionals as $_provisional){
                $dx = $_provisional['ProjectBudgetProvisional'];
                $provisionals[$dx['model']][$dx['model_id']][$dx['date']] = $dx;
                $dayOfPros[$dx['date']] = $dx['date'];
            }
        }
        $proStart = !empty($dayOfPros) ? min($dayOfPros) : 0;
        $proEnd = !empty($dayOfPros) ? max($dayOfPros) : 0;
        $startDates = array_filter(array($projectStart, $realStart, $proStart));
        $startDates = !empty($startDates) ? min($startDates) : 0;
        $endDates = array_filter(array($projectEnd, $realEnd, $proEnd));
        $endDates = !empty($endDates) ? max($endDates) : 0;
        $this->set('funders', ClassRegistry::init('BudgetFunder')->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        )));
        $this->set('pcs', ClassRegistry::init('ProfitCenter')->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        )));
        $this->loadModel('BudgetSetting');
        $company_id=$this->employee_info['Company']['id'];
        $budget_settingst=$this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $employee_info = $this->employee_info;
        $modifyBudget = $this->checkModifyBudget();
        $modifyBudget = $modifyBudget && $usCanWrite;
        $this->set(compact('projects', 'internals', 'externals', 'provisionals', 'project_id', 'viewFollow', 'modifyBudget', 'startDates', 'endDates', 'employee_info','budget_settings'));
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
                                // $this->Session->setFlash(__('Saved', true), 'success');
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
