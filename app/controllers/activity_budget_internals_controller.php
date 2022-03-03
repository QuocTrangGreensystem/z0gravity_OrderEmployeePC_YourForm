<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ActivityBudgetInternalsController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ project_budget_internals_controller --------------------------
     * 2 CONTROLLER NAY CO LIEN KET VOI NHAU
     * ----------CHU Y-----CHU Y-----VA CHU Y-----------------------
     *
     */
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
    var $name = 'ActivityBudgetInternals';
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
    function index($activity_id = null) {
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetInternal');
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        $projectLinked = !empty($activityName['Activity']['project']) ? $activityName['Activity']['project'] : 0;
        if($projectLinked != 0){
            $this->_checkRole(false, $projectLinked);
			$budgets = $this->ProjectBudgetInternalDetail->find('all', array(
				'recursive' => -1,
				'conditions' => array('project_id' => $projectLinked)
			));
            $getDataProjectTasks = $this->Project->dataFromProjectTask($projectLinked, $activityName['Activity']['company_id']);
        } else {
            //$this->Session->setFlash(__('The Activity don\'t linked with Project.', true), 'success');
            $getDataProjectTasks = $this->Activity->dataFromActivityTask($activity_id, $activityName['Activity']['company_id']);
            $canModified = true;
            $this->set(compact('canModified'));
			$budgets = $this->ProjectBudgetInternalDetail->find('all', array(
				'recursive' => -1,
				'conditions' => array('activity_id' => $activity_id)
			));
        }

        //$averages = $this->ProjectBudgetInternal->find('first', array(
//            'recursive' => -1,
//            'conditions' => array('activity_id' => $activity_id),
//            'fields' => array('average_daily_rate')
//        ));
//        $averages = !empty($averages['ProjectBudgetInternal']['average_daily_rate']) ? $averages['ProjectBudgetInternal']['average_daily_rate'] : 0;
		$bg_currency = $this->getCurrencyOfBudget();
        $engagedErro = 0;
        $getDataActivities = $this->_parse($activity_id);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];
        if (isset($sumEmployees[$activity_id])) {
            foreach ($sumEmployees[$activity_id] as $id => $val) {
                $reals = (isset($employees[$id]['tjm']) && $employees[$id]['tjm'] != '') ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                $engagedErro += $val * $reals;
            }
        }
        $this->_parseParams();
		$this->loadModel('ProfitCenter');
		$profits = $this->ProfitCenter->generateTreeList(array('company_id' => $activityName['Activity']['company_id']), null, null, '');
		$this->loadModel('BudgetFunder');
		$funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $activityName['Activity']['company_id']),
            'fields' => array('id','name')
        ));
        $this->set(compact('activityName', 'budgets', 'activity_id', 'averages', 'engagedErro', 'getDataProjectTasks', 'projectLinked','profits','funders', 'bg_currency'));
    }

    /**
     * Update
     *
     * @return void
     * @access public
     */
    function update($id = null) {
        $this->loadModel('ProjectBudgetInternal');
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
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Internal Costs could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->ProjectBudgetInternalDetail->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function delete($id = null, $activity_id = null) {
        $this->loadModels('ProjectBudgetInternalDetail', 'Activity');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project budget internal costs', true));
            $this->redirect(array('action' => 'index', $activity_id));
        }
		$last = $this->ProjectBudgetInternalDetail->read(null, $id);
		$activity_id = !empty( $last) ? $last['ProjectBudgetInternalDetail']['activity_id'] : 0;
		$project_id = $activity_id ? 
			$this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id),
                'fields' => array('project') 
            )) 
			: 0;
		$project_id = !empty($project_id) && !empty($project_id['Activity']['project']) ? $project_id['Activity']['project'] : 0;
		
        if ( $this->_checkRole(false, $project_id) && $this->ProjectBudgetInternalDetail->delete($id)) {
            /**
             * kiem tra xem Activity co linked voi project ko. Co thi lay id
             */
            $datas = array(
                'project_id' => $project_id,
                'activity_id' => $activity_id
            );
            $this->ProjectBudgetInternalDetail->saveInternalDetailToSyns($datas);
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index', $activity_id));
        }
        $this->Session->setFlash(__('The Budget Internal Costs was not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $activity_id));
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
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }

        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

    function clean_filters(){
        $this->loadModel('HistoryFilter');
        if (!empty($_POST)){
            $path = $_POST['path'];
            $budget_internal = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id'],
                ),
                'fields' => array( 'id', 'params'),
            ));
            if(!empty($budget_internal)){
                if ($this->HistoryFilter->delete($budget_internal['HistoryFilter']['id'])) die(1);
            } 
            die(0);
        }
        die(0);
    }
}
?>
