<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityBudgetSynthesisController extends AppController {
    /**
     * 
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ project_budget_synthesis_controller --------------------------
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
    var $name = 'ActivityBudgetSynthesis';
    //var $layout = 'administrators'; 
    
    /**
     * 
     * Don't using model
     * 
     */
    var $uses = array();

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
        $this->loadModel('ProjectBudgetSale');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('Activity');
        
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        $projectLinked = !empty($activityName['Activity']['project']) ? $activityName['Activity']['project'] : 0;
        
		if($projectLinked != 0){
            $this->_checkRole(false, $projectLinked);
            $getDataProjectTasks = $this->Project->dataFromProjectTask($projectLinked, $activityName['Activity']['company_id']);
        } else {
            $getDataProjectTasks = $this->Activity->dataFromActivityTask($activity_id, $activityName['Activity']['company_id']);
            //$this->Session->setFlash(__('The Activity don\'t linked with Project.', true), 'success');
            $canModified = true;
            $this->set(compact('canModified'));
        }
		$bg_currency = $this->getCurrencyOfBudget();
        // Budget Sales
        $this->ProjectBudgetSale->cacheQueries = true;
        $this->ProjectBudgetSale->recursive = -1;
        $this->ProjectBudgetSale->Behaviors->attach('Containable');
        $projectBudgetSales = $this->ProjectBudgetSale->find('all', array(
                'conditions' => array('ProjectBudgetSale.activity_id' => $activity_id),
                'contain' => array('ProjectBudgetInvoice' => array('billed', 'paid', 'effective_date')),
                'fields' => array('name', 'order_date', 'sold', 'man_day')
            ));
        $sales = array();
        if(!empty($projectBudgetSales)){
            foreach($projectBudgetSales as $key => $projectBudgetSale){
                $billed = $paid = $billed_check = 0;
                if(!empty($projectBudgetSale['ProjectBudgetInvoice'])){
                    foreach($projectBudgetSale['ProjectBudgetInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $billed_check += $values['billed'];
                        }
                        $billed += $values['billed'];
                        $paid += $values['paid'];
                    }
                }
                $sales[$key]['name'] = $projectBudgetSale['ProjectBudgetSale']['name'];
                $sales[$key]['order_date'] = $projectBudgetSale['ProjectBudgetSale']['order_date'];
                $sales[$key]['sold'] = $projectBudgetSale['ProjectBudgetSale']['sold'];
                $sales[$key]['man_day'] = $projectBudgetSale['ProjectBudgetSale']['man_day'];
                $sales[$key]['billed'] = $billed;
                $sales[$key]['paid'] = $paid;
                $sales[$key]['billed_check'] = $billed_check;
            }
        }
        // Internal costs
        /*$averages = $this->ProjectBudgetInternal->find('first', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('average_daily_rate')
        ));
        $averages = !empty($averages['ProjectBudgetInternal']['average_daily_rate']) ? $averages['ProjectBudgetInternal']['average_daily_rate'] : 0;*/

        $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'name', 'validation_date', 'budget_md' , 'average')
        ));
        $engagedErro = 0;
        $getDataActivities = $this->_parse($activity_id);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];
        
        if (isset($sumEmployees[$activity_id])) {
            foreach ($sumEmployees[$activity_id] as $id => $val) {
                $reals = !empty($employees[$id]['tjm']) ? (float)str_replace(',', '.', $employees[$id]['tjm']) : 1;
                $engagedErro += $val * $reals;
            }
        }
        $internals = $internalDetails = array();
        $budgetEuro = 0;
		$count = $totalAverage = 0;
        if(!empty($projectBudgetInternals)){
            foreach($projectBudgetInternals as $key => $projectBudgetInternal){
                $budgetMd = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                $averageDetails = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] : 0;
                $totalAverage += $averageDetails;
                $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                $internalDetails[$key]['budget_euro'] = $budgetMd*$averageDetails;
                $budgetEuro += ($budgetMd*$averageDetails);
                $count++;
            }
        } 
        if($count == 0){
            $totalAverage = 0;
        } else {
            $totalAverage = round($totalAverage/$count, 2);
        }
        $internals = array(
            'forecastedManDay' => $getDataProjectTasks['consumed'] + $getDataProjectTasks['remain'],
            'engagedEuro' => $engagedErro,
            'remainEuro' => $getDataProjectTasks['remain'] * $totalAverage,
            'forecastEuro' => $engagedErro + ($getDataProjectTasks['remain'] * $totalAverage),
            'budgetEuro' => $budgetEuro,
            'varEuro' => ($budgetEuro != 0) ? round(((($engagedErro + ($getDataProjectTasks['remain'] * $totalAverage))/$budgetEuro)-1)*100, 2) : 0
        );
        // External costs
        $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
        ));
        $externals = array();
        if(!empty($projectBudgetExternals)){
            foreach($projectBudgetExternals as $key => $projectBudgetExternal){
                $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                $externals[$key]['name'] = $dx['name'];
                $externals[$key]['order_date'] = $dx['order_date'];
                $externals[$key]['budget_erro'] = $dx['budget_erro'];
                $externals[$key]['ordered_erro'] = $dx['ordered_erro'];
                $externals[$key]['remain_erro'] = $dx['remain_erro'];
                $externals[$key]['forecast_erro'] = $dx['ordered_erro'] + $dx['remain_erro'];
                if($dx['budget_erro'] == 0){
                    $externals[$key]['var_erro'] = round(((0) - 1)*100, 2);
                } else {
                    $externals[$key]['var_erro'] = round(((($dx['ordered_erro'] + $dx['remain_erro'])/$dx['budget_erro']) - 1)*100, 2);
                }
                $externals[$key]['man_day'] = $dx['man_day'];
                $externals[$key]['progress_md'] = $dx['progress_md'];
                $externals[$key]['progress_erro'] = $dx['progress_erro'];
            }
        }
        $this->_parseParams();
        $this->set(compact('activityName', 'activity_id', 'sales', 'internalDetails', 'internals', 'externals', 'bg_currency'));
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
     * Trong table:
     * - project_budget_sales
     * - project_budget_invoices
     * - project_budget_internals
     * - project_budget_externals
     * - project_budget_external_details
     * Tim nhung project nao co linked voi activity thi them id cua activity(activity_id) do vao cac table tren.
     * Neu khong linked thi mac dinh activity_id = 0
     */
    public function addActivityIdToAllTable(){
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetInvoice');
        $this->loadModel('ProjectBudgetSale');
        /**
         * Lay danh sach project lien ket voi activity
         */
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        /**
         * Lay tat ca du lieu o table ProjectBudgetExternal
         */
        $externals = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($externals)){
            foreach($externals as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetExternal->id = $id;
                $this->ProjectBudgetExternal->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInternal
         */
        $internals = $this->ProjectBudgetInternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($internals)){
            foreach($internals as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInternal->id = $id;
                $this->ProjectBudgetInternal->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInternalDetail
         */
        $internalDetails = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($internalDetails)){
            foreach($internalDetails as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInternalDetail->id = $id;
                $this->ProjectBudgetInternalDetail->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInvoice
         */
        $invoices = $this->ProjectBudgetInvoice->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($invoices)){
            foreach($invoices as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInvoice->id = $id;
                $this->ProjectBudgetInvoice->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetSale
         */
        $sales = $this->ProjectBudgetSale->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($sales)){
            foreach($sales as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetSale->id = $id;
                $this->ProjectBudgetSale->save($_saved);
            }
        }
        echo 'Finish Roi nhe! Met qua!';
        exit;
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
}
?>