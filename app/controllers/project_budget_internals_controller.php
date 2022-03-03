<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetInternalsController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ activity_budget_internals_controller --------------------------
     * 2 CONTROLLER NAY CO LIEN KET VOI NHAU
     * ----------CHU Y-----CHU Y-----VA CHU Y-----------------------
     *
     */
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectBudgetInternals';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');

     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            if( !((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))){
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/'.$value;
                }
                if( isset($_url_param['url'])) unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('internal_cost');
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetInternalDetail');
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
        $this->loadModel('ProjectTask');
        //$this->ProjectTask->virtualFields = array('total' => 'SUM(ProjectTask.estimated)');
        //$this->ProjectTask->virtualFields = array('exConsumed' => 'SUM(ProjectTask.special_consumed)');
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special'=>1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $getDataProjectTasks['workload'] -= $varE;
        //debug($getDataProjectTasks);
        $budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        //$averages = $this->ProjectBudgetInternal->find('first', array(
//            'recursive' => -1,
//            'conditions' => array('project_id' => $project_id),
//            'fields' => array('average_daily_rate')
//        ));
//        $averages = !empty($averages['ProjectBudgetInternal']['average_daily_rate']) ? $averages['ProjectBudgetInternal']['average_daily_rate'] : 0;
        /**
         * get external budget of project
         */
        $this->loadModel('ProjectBudgetExternal');
        $externalBudgets = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('SUM(man_day) AS exBudget')
        ));
        $externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
        $engagedErro = 0;
        if(!empty($projectName['Project']['activity_id'])){
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $employees = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $id => $val) {
                    $reals = (isset($employees[$id]['tjm']) && $employees[$id]['tjm'] != '') ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
        } else {
            //$this->Session->setFlash(__('The Project don\'t linked with Activity.', true), 'success');
        }
        if($projectName && $projectName['Project']['category'] == 2){
            //$this->action = 'oppor';
        }
        $this->loadModel('ProfitCenter');
        $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $projectName['Project']['company_id']), null, null, '');

        $this->loadModel('BudgetFunder');
        $funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id','name')
        ));
        //Lay Budget_settings
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
        // kiem tra xem PM dc chinh sua budget internal hay ko?
        $modifyBudget = $this->checkModifyBudget();
        $this->set(compact('projectName', 'budgets', 'project_id', 'engagedErro', 'getDataProjectTasks', 'activityLinked', 'externalBudgets', 'externalConsumeds','profits','funders', 'modifyBudget', 'employee_info','budget_settings'));
    }

    /**
     * Update
     *
     * @return void
     * @access public
     */
    function update($id = null) {
        if ($this->data) {
            $last = $this->ProjectBudgetInternal->find('first', array(
                'fields' => 'id',
                'conditions' => array('project_id' => $this->data['project_id']),
                'recursive' => -1));
            $this->ProjectBudgetInternal->create();
            if ($last) {
                $this->ProjectBudgetInternal->id = $last['ProjectBudgetInternal']['id'];
            }
            $this->data['average_daily_rate'] = floatval(str_replace(",", ".", $this->data['average_daily_rate']));
            $this->ProjectBudgetInternal->save($this->data);
        }
        exit();
    }

    /**
     * update detail
     *
     * @return void
     * @access public
     */
    public function update_detail() {
        $this->loadModel('ProjectBudgetInternalDetail');
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $this->ProjectBudgetInternalDetail->create();
            if (!empty($this->data['id'])) {
                $this->ProjectBudgetInternalDetail->id = $this->data['id'];
                $provision = ClassRegistry::init('ProjectBudgetProvisional');
                $provision->virtualFields['total_value'] = 'SUM(value)';
                $total = $provision->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $this->data['project_id'],
                        'model' => 'Internal',
                        'model_id' => $this->data['id']
                    ),
                    'fields' => array('view', 'total_value'),
                    'group' => array('view')
                ));
                if( isset($total['man-day']) ){
                    $md = floatval($this->data['budget_md']);
                    if( $md < $total['man-day'] ){
                        $result = false;
                        $this->set(compact('result'));
                        $this->Session->setFlash(
                            sprintf(__('Budget man day (%s) < provisional budget man day (%s)', true), $md, $total['man-day']),
                            'error'
                        );
                        $this->render();
                        return;
                    }
                }
                if( isset($total['euro']) ){
                    if( $this->data['average'] && $this->data['budget_md'] ){
                        $eur = floatval( $this->data['average'] * $this->data['budget_md'] );
                        if( $eur < $total['euro'] ){
                            $result = false;
                            $this->set(compact('result'));
                            $this->Session->setFlash(
                                sprintf(__('Budget euro (%s) < provisional budget euro (%s)', true), $eur, $total['euro']),
                                'error'
                            );
                            $this->render();
                            return;
                        }
                    }
                }
            }
            $data = array();
            foreach (array('validation_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectBudgetInternalDetail->convertTime($this->data[$key]);
                }
            }
            unset($this->data['id']);
            if ($this->ProjectBudgetInternalDetail->save(array_merge($this->data, $data))) {
                $result = true;
                //Added by QN on 2015/02/09
                $projectName = $this->ProjectBudgetInternalDetail->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $this->data['project_id'])));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update internal budget `%s` for project `%s`', $this->data['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                // $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->data['id'] = $this->ProjectBudgetInternalDetail->id;
        } else {
            $this->Session->setFlash(__('Not saved', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function delete($id = null, $project_id = null) {
        $this->loadModels('ProjectBudgetInternalDetail', 'ProjectBudgetProvisional', 'Project');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project budget internal costs', true));
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->ProjectBudgetInternalDetail->recursive = -1;
        $last = $this->ProjectBudgetInternalDetail->read(null, $id);
        $checkProvisionals = $this->ProjectBudgetProvisional->find('count', array(
            'recursive' => -1,
            'conditions' => array('model' => 'Internal', 'model_id' => $id, 'NOT' => array('value IS NULL'))
        ));

        if($checkProvisionals != 0){
            $this->Session->setFlash(__('Data filled in provisional screen', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if ($this->ProjectBudgetInternalDetail->delete($id)) {
            $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'Internal', 'ProjectBudgetProvisional.model_id' => $id), false);
            /**
             * kiem tra xem project co linked voi activity ko. Co thi lay id
             */
            $activity_id = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('activity_id')
            ));
            $datas = array(
                'project_id' => $project_id,
                'activity_id' => !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : 0
            );
            $this->ProjectBudgetInternalDetail->saveInternalDetailToSyns($datas);
            $projectName = $this->ProjectBudgetInternalDetail->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name', 'company_id'),
                'conditions' => array('Project.id' => $project_id)));
            $this->writeLog($this->data, $this->employee_info, sprintf('Delete internal budget `%s` of project `%s`', $last['ProjectBudgetInternalDetail']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->Session->setFlash(__('The Budget Internal Costs was not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $project_id));
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parse($activity_id) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModel('ActivityRequest');
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $activity_id,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'activity_id')
        ));
        $groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_datas = $this->ActivityRequest->find(
            'all',
            array(
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
                'activity_id' => 0,
                'task_id' => $groupTaskId,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $_sumActivitys = $_sumEmployees = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach($activityTasks as $activityTask){
            foreach($_sumEmployees as $id => $_sumEmployee){
                if($activityTask['ActivityTask']['id'] == $id){
                    $dataFromEmployees[$activityTask['ActivityTask']['activity_id']][] = $_sumEmployee;
                }
            }
        }
        $rDatas = array();
        if(!empty($dataFromEmployees)){
            foreach($dataFromEmployees as $id => $dataFromEmployee){
                foreach($dataFromEmployee as $values){
                    foreach($values as $employ => $value){
                        if(!isset($rDatas[$id][$employ])){
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $_dataFromEmployees = array();
        if(!empty($rDatas)){
            foreach($rDatas as $id => $rData){
                if(in_array($id, array_keys($sumEmployees))){

                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if(!empty($sumEmployees)){
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if(!empty($rDatas)){
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            )), '{n}.Employee.id', '{n}.Employee');

        $setDatas = array();
        $setDatas['sumEmployees'] = !empty($sumEmployees) ? $sumEmployees : array();
        $setDatas['employees'] = !empty($employees) ? $employees : array();

        return $setDatas;
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }
}
?>
