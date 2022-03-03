<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetFiscalsController extends AppController {

    /**
     * Use model
     *
     * @var string
     * @access public
     */
    var $uses = array();
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectBudgetFiscals';

     /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index($project_id = null, $profiltId = null, $display = '') {
        $this->loadModels(
            'TmpStaffingSystem', 'ProjectBudgetProvisional', 'ProjectBudgetInternalDetail',
            'ProjectBudgetExternal', 'ProjectTask', 'ProjectBudgetSale', 'ProjectBudgetPurchase', 'Project', 'ProfitCenter',
            'ProjectBudgetInternalDetail', 'ActivityRequest', 'ActivityTask', 'Employee', 'HistoryFilter'
        );
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        if(empty($display)){
            $check = $this ->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'path' => 'project_budget_fiscals/saveDisplay',
                )
            ));
            if(!empty($check)){
                $display = $check['HistoryFilter']['params'];
            } else {
                $display = 'euro';
            }
        }
        $tmpProfit = false;
        if($profiltId == null || $profiltId == -1){
            if($profiltId == -1){
                $tmpProfit = true;
            }
            $profiltId = $this->employee_info['Employee']['profit_center_id'];
        }
        if (!($params = $this->_getProfits($profiltId))) {
            //$this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            //$this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        $year = date('Y', time());
        $dayMonthRange = isset($this->companyConfigs['fiscal_year']) && !empty($this->companyConfigs['fiscal_year']) ?  $this->companyConfigs['fiscal_year'] : '';
        $dayMonthRange = !empty($dayMonthRange) ? str_replace('/', '-', $dayMonthRange) : '01-01';
        /**
         * Start, end date format
         */
        $startCurrent = strtotime($dayMonthRange . '-' . $year);
        $endCurrent = mktime(0, 0, 0, date("m", $startCurrent), date("d", $startCurrent)-1, date("Y", $startCurrent)+1);
        /**
         * Start, end date of last one year
         */
        $endLastOneYear = mktime(0, 0, 0, date("m", $startCurrent), date("d", $startCurrent)-1, date("Y", $startCurrent));
        $startLastOneYear = mktime(0, 0, 0, date("m", $endLastOneYear), date("d", $endLastOneYear)+1, date("Y", $endLastOneYear)-1);
        /**
         * Start, end date of last two year
         */
        $endLastTwoYear = mktime(0, 0, 0, date("m", $startLastOneYear), date("d", $startLastOneYear)-1, date("Y", $startLastOneYear));
        $startLastTwoYear = mktime(0, 0, 0, date("m", $endLastTwoYear), date("d", $endLastTwoYear)+1, date("Y", $endLastTwoYear)-1);
        /**
         * Start, end date of next one year
         */
        $startNextOneYear = mktime(0, 0, 0, date("m", $endCurrent), date("d", $endCurrent)+1, date("Y", $endCurrent));
        $endNextOneYear = mktime(0, 0, 0, date("m", $startNextOneYear), date("d", $startNextOneYear)-1, date("Y", $startNextOneYear)+1);
        /**
         * Start, end date of next two year
         */
        $startNextTwoYear = mktime(0, 0, 0, date("m", $endNextOneYear), date("d", $endNextOneYear)+1, date("Y", $endNextOneYear));
        $endNextTwoYear = mktime(0, 0, 0, date("m", $startNextTwoYear), date("d", $startNextTwoYear)-1, date("Y", $startNextTwoYear)+1);
        /**
         * Danh sach star date, end date cua year
         */
        $listYears = array(
            ($year-2) => array('start' => $startLastTwoYear, 'end' => $endLastTwoYear),
            ($year-1) => array('start' => $startLastOneYear, 'end' => $endLastOneYear),
            $year => array('start' => $startCurrent, 'end' => $endCurrent),
            ($year+1) => array('start' => $startNextOneYear, 'end' => $endNextOneYear),
            ($year+2) => array('start' => $startNextTwoYear, 'end' => $endNextTwoYear),
        );

        $lastY = $startLastTwoYear;
        $nextY = $endNextTwoYear;
        $roles = $this->employee_info['Role']['name'];
        $company_id = $this->employee_info['Company']['id'];
        $companyName = $this->employee_info['Company']['company_name'];
        $parentOfSubPcs = $saleValues = $purchaseValues = $dataOfYearInternals = $dataOfYearExternals = $internalValues = $externalValues = array();
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'project_name', 'start_date', 'end_date', 'activity_id')
        ));


        /**
         * List PC
         */
        if($tmpProfit == false){
            $listProfitCenterChilds = $this->ProfitCenter->children($profiltId);
            $listProfitCenterChilds = !empty($listProfitCenterChilds) ? Set::classicExtract($listProfitCenterChilds, '{n}.ProfitCenter.id') : array();
            if(!empty($listProfitCenterChilds)){
                foreach($listProfitCenterChilds as $val){
                    $parentOfSubPcs[$val] = $profiltId;
                }
            }
            $listProfitCenterChilds = array_merge(array($profiltId), $listProfitCenterChilds);
        } else {
            $listProfitCenterChilds = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'condititions' => array('company_id' => $company_id),
                'fields' => array('id', 'id')
            ));
        }
        /**
         * Tinh consumed/workload theo nam cua profit center
         * Internal
         */
        if($display == 'man-day'){
            $_dataOfYearInternals = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('profit_center'),
                    'model_id' => $listProfitCenterChilds,
                    'project_id' => $project_id,
                    'date BETWEEN ? AND ?' => array($lastY, $nextY),
                    'company_id' => $company_id
                ),
                'fields' => array(
                    'model_id',
                    'SUM(CASE WHEN date BETWEEN"' . $startCurrent . '" AND "' . $endCurrent . '"THEN `consumed` ELSE 0 END) AS consumed_' . $year,
                    'SUM(CASE WHEN date BETWEEN"' . $startLastOneYear . '" AND "' . $endLastOneYear . '"THEN `consumed` ELSE 0 END) AS consumed_' . ($year-1),
                    'SUM(CASE WHEN date BETWEEN"' . $startLastTwoYear . '" AND "' . $endLastTwoYear . '"THEN `consumed` ELSE 0 END) AS consumed_' . ($year-2),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextOneYear . '" AND "' . $endNextOneYear . '"THEN `consumed` ELSE 0 END) AS consumed_' . ($year+1),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextTwoYear . '" AND "' . $endNextTwoYear . '"THEN `consumed` ELSE 0 END) AS consumed_' . ($year+2),
                    'SUM(CASE WHEN date BETWEEN"' . $startCurrent . '" AND "' . $endCurrent . '"THEN `estimated` ELSE 0 END) AS workload_' . $year,
                    'SUM(CASE WHEN date BETWEEN"' . $startLastOneYear . '" AND "' . $endLastOneYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year-1),
                    'SUM(CASE WHEN date BETWEEN"' . $startLastTwoYear . '" AND "' . $endLastTwoYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year-2),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextOneYear . '" AND "' . $endNextOneYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year+1),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextTwoYear . '" AND "' . $endNextTwoYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year+2),
                ),
                'group' => array('model_id')
            ));
            if(!empty($_dataOfYearInternals)){
                foreach($_dataOfYearInternals as $_dataOfYearInternal){
                    $values = !empty($_dataOfYearInternal[0]) ? $_dataOfYearInternal[0] : array();
                    if(!empty($values)){
                        foreach($values as $key => $value){
                            if(!isset($dataOfYearInternals[$key])){
                                $dataOfYearInternals[$key] = 0;
                            }
                            $dataOfYearInternals[$key] += $value;
                        }
                    }
                }
            }
        } else {
            /**
             * Tinh Average daily rate euro
             */
            $AVGDailyRate = $this->ProjectBudgetInternalDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id),
                'fields' => array('AVG(average) as dailyRate')
            ));
            $AVGDailyRate = !empty($AVGDailyRate) && !empty($AVGDailyRate[0][0]['dailyRate']) ? round($AVGDailyRate[0][0]['dailyRate'], 2) : 0;
            /**
             * Tinh consumed internal cua cac task theo year
             */
            $activities = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
            //Activity Task
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activities),
                'fields' => array('project_task_id', 'id')
            ));
            //Request
            $requests = $this->ActivityRequest->find(
                'all',
                array(
                    'recursive' => -1,
                    'fields' => array(
                        'id',
                        'employee_id',
                        'activity_id',
                        'task_id',
                        'SUM(CASE WHEN date BETWEEN"' . $startCurrent . '" AND "' . $endCurrent . '"THEN `value` ELSE 0 END) AS consumed_' . $year,
                        'SUM(CASE WHEN date BETWEEN"' . $startLastOneYear . '" AND "' . $endLastOneYear . '"THEN `value` ELSE 0 END) AS consumed_' . ($year-1),
                        'SUM(CASE WHEN date BETWEEN"' . $startLastTwoYear . '" AND "' . $endLastTwoYear . '"THEN `value` ELSE 0 END) AS consumed_' . ($year-2),
                        'SUM(CASE WHEN date BETWEEN"' . $startNextOneYear . '" AND "' . $endNextOneYear . '"THEN `value` ELSE 0 END) AS consumed_' . ($year+1),
                        'SUM(CASE WHEN date BETWEEN"' . $startNextTwoYear . '" AND "' . $endNextTwoYear . '"THEN `value` ELSE 0 END) AS consumed_' . ($year+2),
                    ),
                'group' => array('employee_id', 'activity_id', 'task_id'),
                'conditions' => array(
                    'status' => 2,
                    'OR' => array(
                        'task_id' => $activityTasks,
                        'activity_id' => $activities
                    ),
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0))
                )
            );
            if(!empty($requests)){
                $employeeIds = array_unique(Set::classicExtract($requests, '{n}.ActivityRequest.employee_id'));
                /**
                 * Lay list average daily of the resource
                 */
                $this->Employee->unbindModelAll();
                $this->Employee->bindModel(array('hasMany' => array('ProjectEmployeeProfitFunctionRefer')));
                $employees = $this->Employee->find('all', array(
                    'conditions' => array('Employee.id' => $employeeIds),
                    'fields' => array('id', 'tjm')
                ));
                $profitOfEmployee = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.ProjectEmployeeProfitFunctionRefer.0.profit_center_id') : array();
                $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee.tjm') : array();
                /**
                 * Tinh consumed
                 */
                foreach($requests as $request){
                    $dx = $request['ActivityRequest'];
                    $values = $request[0];
                    $employId = $dx['employee_id'];
                    $tjm = !empty($employees[$employId]) ? $employees[$employId] : 1;
                    $_profitId = !empty($profitOfEmployee[$employId]) ? $profitOfEmployee[$employId] : 0;
                    if($_profitId != 0){
                        if($tmpProfit == false){
                            $_profitId = !empty($parentOfSubPcs) && !empty($parentOfSubPcs[$_profitId]) ? $parentOfSubPcs[$_profitId] : $_profitId;
                        } else {
                            $_profitId = 0;
                        }
                    }
                    if(!empty($values)){
                        foreach($values as $key => $value){
                            $value = round($value * $tjm, 2);
                            if(!isset($dataOfYearInternals[$_profitId][$key])){
                                $dataOfYearInternals[$_profitId][$key] = 0;
                            }
                            $dataOfYearInternals[$_profitId][$key] += $value;
                        }
                    }
                }
            }
            if($tmpProfit == true){
                $dataOfYearInternals = !empty($dataOfYearInternals) && !empty($dataOfYearInternals[0]) ? $dataOfYearInternals[0] : array();
            } else {
                $dataOfYearInternals = !empty($dataOfYearInternals) && !empty($dataOfYearInternals[$profiltId]) ? $dataOfYearInternals[$profiltId] : array();
            }
            /**
             *  Tinh workload internal
             */
            $_dataOfYearInternals = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('profit_center'),
                    'model_id' => $listProfitCenterChilds,
                    'project_id' => $project_id,
                    'date BETWEEN ? AND ?' => array($lastY, $nextY),
                    'company_id' => $company_id
                ),
                'fields' => array(
                    'model_id',
                    'SUM(CASE WHEN date BETWEEN"' . $startCurrent . '" AND "' . $endCurrent . '"THEN `estimated` ELSE 0 END) AS workload_' . $year,
                    'SUM(CASE WHEN date BETWEEN"' . $startLastOneYear . '" AND "' . $endLastOneYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year-1),
                    'SUM(CASE WHEN date BETWEEN"' . $startLastTwoYear . '" AND "' . $endLastTwoYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year-2),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextOneYear . '" AND "' . $endNextOneYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year+1),
                    'SUM(CASE WHEN date BETWEEN"' . $startNextTwoYear . '" AND "' . $endNextTwoYear . '"THEN `estimated` ELSE 0 END) AS workload_' . ($year+2),
                ),
                'group' => array('model_id')
            ));
            if(!empty($_dataOfYearInternals)){
                $totalW = count($_dataOfYearInternals);
                $count = 0;
                foreach($_dataOfYearInternals as $_dataOfYearInternal){
                    $values = !empty($_dataOfYearInternal[0]) ? $_dataOfYearInternal[0] : array();
                    $count++;
                    if(!empty($values)){
                        foreach($values as $key => $value){
                            if(!isset($dataOfYearInternals[$key])){
                                $dataOfYearInternals[$key] = 0;
                            }
                            $dataOfYearInternals[$key] += $value;
                            if($count == $totalW){
                                $dataOfYearInternals[$key] = round($dataOfYearInternals[$key] * $AVGDailyRate, 2);
                            }
                        }
                    }
                }
            }
        }
        /**
         * Lay danh sach external cost va profit center cua internal
         */
        $externals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'profit_center_id', 'man_day', 'progress_md', 'budget_erro')
        ));
        $exProgress = !empty($externals) ? Set::combine($externals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal.progress_md') : array();
        $exManday = !empty($externals) ? Set::combine($externals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal.man_day') : array();
        $exBudget = !empty($externals) ? Set::combine($externals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal.budget_erro') : array();
        $externals = !empty($externals) ? Set::combine($externals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal.profit_center_id') : array();
        /**
         * External
         */
        $externalTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('special' => 1, 'project_id' => $project_id),
            'fields' => array('id', 'task_start_date', 'task_end_date', 'estimated', 'special_consumed', 'external_id')
        ));
        $_dataOfYearExternals = array();
        if(!empty($externalTasks)){
            $tExtenal = count($externalTasks);
            $cExteral = 0;
            foreach($externalTasks as $externalTask){
                $dx = $externalTask['ProjectTask'];
                $start = !empty($dx['task_start_date']) ? strtotime($dx['task_start_date']) : '';
                $end = !empty($dx['task_end_date']) ? strtotime($dx['task_end_date']) : '';
                $externalId = !empty($dx['external_id']) ? $dx['external_id'] : '';
                $pcId = !empty($externals[$externalId]) ? $externals[$externalId] : 0;
                if($pcId != 0){
                    if($tmpProfit == false){
                        $pcId = !empty($parentOfSubPcs) && !empty($parentOfSubPcs[$pcId]) ? $parentOfSubPcs[$pcId] : $pcId;
                    } else {
                        $pcId = 0;
                    }
                }
                $tMonth = 0;
                $tMonthOfYear = array();
                if(!empty($start) && !empty($end)){
                    while($start <= $end){
                        $_year = $this->_findYearOfDay($listYears, $start);
                        if(!isset($tMonthOfYear[$_year])){
                            $tMonthOfYear[$_year] = 0;
                        }
                        $tMonthOfYear[$_year] += 1;
                        $start = mktime(0, 0, 0, date("m", $start)+1, date("d", $start), date("Y", $start));
                        $tMonth++;
                    }
                }
                $workload = round($dx['estimated']/$tMonth, 2);
                $consumed = round($dx['special_consumed']/$tMonth, 2);
                $totalYear = count($tMonthOfYear);
                $check = 0;
                $restWorkload = $restConsumed = 0;
                foreach($tMonthOfYear as $y => $n){
                    $check++;
                    if($check > $totalYear){
                        break;
                    }
                    if(!isset($_dataOfYearExternals[$pcId][$externalId]['workload_'.$y])){
                        $_dataOfYearExternals[$pcId][$externalId]['workload_'.$y] = 0;
                    }
                    $_dataOfYearExternals[$pcId][$externalId]['workload_'.$y] += $workload * $n;
                    $restWorkload = $dx['estimated'] - ($workload * $n);

                    if(!isset($_dataOfYearExternals[$pcId][$externalId]['consumed_'.$y])){
                        $_dataOfYearExternals[$pcId][$externalId]['consumed_'.$y] = 0;
                    }
                    $_dataOfYearExternals[$pcId][$externalId]['consumed_'.$y] += $consumed * $n;
                    $restConsumed = $dx['special_consumed'] - ($consumed * $n);
                }
                end($tMonthOfYear);
                $lastKey = key($tMonthOfYear);
                if(!isset($_dataOfYearExternals[$pcId][$externalId]['workload_'.$lastKey])){
                    $_dataOfYearExternals[$pcId][$externalId]['workload_'.$lastKey] = 0;
                }
                $_dataOfYearExternals[$pcId][$externalId]['workload_'.$lastKey] += $restWorkload;
                if(!isset($_dataOfYearExternals[$pcId][$externalId]['consumed_'.$lastKey])){
                    $_dataOfYearExternals[$pcId][$externalId]['consumed_'.$lastKey] = 0;
                }
                $_dataOfYearExternals[$pcId][$externalId]['consumed_'.$lastKey] += $restConsumed;
            }
        }
        if(!empty($_dataOfYearExternals)){
            foreach($_dataOfYearExternals as $pc => $_dataOfYearExternal){
                foreach($_dataOfYearExternal as $exId => $values){
                    foreach($values as $key => $value){
                        $exAVGMD = !empty($exManday[$exId]) ? $exManday[$exId] : 1;
                        $exAVGBG = !empty($exBudget[$exId]) ? $exBudget[$exId] : 1;
                        $exAVG = ($exAVGMD == 0) ? 1 : round($exAVGBG/$exAVGMD, 10);
                        if($display == 'man-day'){
                            $exAVG = 1;
                        }
                        if(!isset($dataOfYearExternals[$pc][$key])){
                            $dataOfYearExternals[$pc][$key] = 0;
                        }
                        $dataOfYearExternals[$pc][$key] += round($value * $exAVG, 2);
                    }
                }
            }
        }
        if($tmpProfit == true){
            $dataOfYearExternals = !empty($dataOfYearExternals) && !empty($dataOfYearExternals[0]) ? $dataOfYearExternals[0] : array();
        } else {
            $dataOfYearExternals = !empty($dataOfYearExternals) && !empty($dataOfYearExternals[$profiltId]) ? $dataOfYearExternals[$profiltId] : array();
        }
        /**
         * Lay danh sach internal cost va profit center cua internal
         */
        $internals = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'profit_center_id')
        ));
        /**
         * Tinh provisional of project
         */
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'view' => 'man-day'),
            'fields' => array(
                'model',
                'model_id',
                'SUM(CASE WHEN date BETWEEN"' . $startCurrent . '" AND "' . $endCurrent . '"THEN `value` ELSE 0 END) AS provisional_' . $year,
                'SUM(CASE WHEN date BETWEEN"' . $startLastOneYear . '" AND "' . $endLastOneYear . '"THEN `value` ELSE 0 END) AS provisional_' . ($year-1),
                'SUM(CASE WHEN date BETWEEN"' . $startLastTwoYear . '" AND "' . $endLastTwoYear . '"THEN `value` ELSE 0 END) AS provisional_' . ($year-2),
                'SUM(CASE WHEN date BETWEEN"' . $startNextOneYear . '" AND "' . $endNextOneYear . '"THEN `value` ELSE 0 END) AS provisional_' . ($year+1),
                'SUM(CASE WHEN date BETWEEN"' . $startNextTwoYear . '" AND "' . $endNextTwoYear . '"THEN `value` ELSE 0 END) AS provisional_' . ($year+2),
            ),
            'group' => array('model', 'model_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.model_id', '{n}.0', '{n}.ProjectBudgetProvisional.model') : array();
        if(!empty($provisionals)){
            if(isset($provisionals['External']) && !empty($provisionals['External'])){
                foreach($provisionals['External'] as $id => $vals){
                    foreach($vals as $key => $val){
                        $profitCenterId = !empty($externals[$id]) ? $externals[$id] : 0;
                        if($profitCenterId != 0){
                            if($tmpProfit == false){
                                $profitCenterId = !empty($parentOfSubPcs) && !empty($parentOfSubPcs[$profitCenterId]) ? $parentOfSubPcs[$profitCenterId] : $profitCenterId;
                            } else {
                                $profitCenterId = 0;
                            }
                        }
                        $exAVGMD = !empty($exManday[$exId]) ? $exManday[$exId] : 1;
                        $exAVGBG = !empty($exBudget[$exId]) ? $exBudget[$exId] : 1;
                        $exAVG = ($exAVGMD == 0) ? 1 : round($exAVGBG/$exAVGMD, 10);
                        if($display == 'man-day'){
                            $exAVG = 1;
                        }
                        if(!isset($externalValues[$profitCenterId][$key])){
                            $externalValues[$profitCenterId][$key] = 0;
                        }
                        $externalValues[$profitCenterId][$key] += round($val * $exAVG, 2);
                    }
                }
            }
            if(isset($provisionals['Internal']) && !empty($provisionals['Internal'])){
                foreach($provisionals['Internal'] as $id => $vals){
                    foreach($vals as $key => $val){
                        $profitCenterId = !empty($internals[$id]) ? $internals[$id] : 0;
                        if($profitCenterId != 0){
                            if($tmpProfit == false){
                                $profitCenterId = !empty($parentOfSubPcs) && !empty($parentOfSubPcs[$profitCenterId]) ? $parentOfSubPcs[$profitCenterId] : $profitCenterId;
                            } else {
                                $profitCenterId = 0;
                            }
                        }
                        $inAVG = isset($AVGDailyRate) && !empty($AVGDailyRate) && ($display == 'euro') ? $AVGDailyRate : 1;
                        if(!isset($internalValues[$profitCenterId][$key])){
                            $internalValues[$profitCenterId][$key] = 0;
                        }
                        $internalValues[$profitCenterId][$key] += round($val * $inAVG, 2);
                    }
                }
            }
            if($tmpProfit == true){
                $externalValues = !empty($externalValues) && !empty($externalValues[0]) ? $externalValues[0] : array();
                $internalValues = !empty($internalValues) && !empty($internalValues[0]) ? $internalValues[0] : array();
            } else {
                $externalValues = !empty($externalValues) && !empty($externalValues[$profiltId]) ? $externalValues[$profiltId] : array();
                $internalValues = !empty($internalValues) && !empty($internalValues[$profiltId]) ? $internalValues[$profiltId] : array();
            }
        }
        /**
         * Tinh sale of project
         */
        if($tmpProfit == true && $display == 'euro'){
            $this->ProjectBudgetSale->unbindModelAll();
            $this->ProjectBudgetSale->bindModel(array('hasMany' => array('ProjectBudgetInvoice')));
            $sales = $this->ProjectBudgetSale->find('all', array(
                'conditions' => array(
                    'project_id' => $project_id
                )
            ));
            if(!empty($sales)){
                foreach($sales as $sale){
                    $dx = $sale['ProjectBudgetSale'];
                    $dy = $sale['ProjectBudgetInvoice'];
                    if(!empty($dx['order_date']) && $dx['order_date'] != '0000-00-00'){
                        $_date = strtotime($dx['order_date']);
                        $_year = $this->_findYearOfDay($listYears, $_date);
                        if(!isset($saleValues['order'][$_year])){
                            $saleValues['order'][$_year] = 0;
                        }
                        $saleValues['order'][$_year] += $dx['sold'];
                    }
                    if(!empty($dy)){
                        foreach($dy as $val){
                            if(!empty($val['due_date']) && $val['due_date'] != '0000-00-00'){
                                $_date = strtotime($val['due_date']);
                                $_year = $this->_findYearOfDay($listYears, $_date);
                                if(!isset($saleValues['toBill'][$_year])){
                                    $saleValues['toBill'][$_year] = 0;
                                }
                                $saleValues['toBill'][$_year] += $val['billed'];
                            }
                            if(!empty($val['effective_date']) && $val['effective_date'] != '0000-00-00'){
                                $_date = strtotime($val['effective_date']);
                                $_year = $this->_findYearOfDay($listYears, $_date);
                                if(!isset($saleValues['billed'][$_year])){
                                    $saleValues['billed'][$_year] = 0;
                                }
                                $saleValues['billed'][$_year] += $val['billed'];
                            }
                        }
                    }
                }
            }
            // tinh purchase
            $this->ProjectBudgetPurchase->unbindModelAll();
            $this->ProjectBudgetPurchase->bindModel(array('hasMany' => array('ProjectBudgetPurchaseInvoice')));
            $purchases = $this->ProjectBudgetPurchase->find('all', array(
                'conditions' => array(
                    'project_id' => $project_id
                )
            ));
            if(!empty($purchases)){
                foreach($purchases as $purchase){
                    $dx = $purchase['ProjectBudgetPurchase'];
                    $dy = $purchase['ProjectBudgetPurchaseInvoice'];
                    if(!empty($dx['order_date']) && $dx['order_date'] != '0000-00-00'){
                        $_date = strtotime($dx['order_date']);
                        $_year = $this->_findYearOfDay($listYears, $_date);
                        if(!isset($purchaseValues['order'][$_year])){
                            $purchaseValues['order'][$_year] = 0;
                        }
                        $purchaseValues['order'][$_year] += $dx['sold'];
                    }
                    if(!empty($dy)){
                        foreach($dy as $val){
                            if(!empty($val['due_date']) && $val['due_date'] != '0000-00-00'){
                                $_date = strtotime($val['due_date']);
                                $_year = $this->_findYearOfDay($listYears, $_date);
                                if(!isset($purchaseValues['toBill'][$_year])){
                                    $purchaseValues['toBill'][$_year] = 0;
                                }
                                $purchaseValues['toBill'][$_year] += $val['billed'];
                            }
                            if(!empty($val['effective_date']) && $val['effective_date'] != '0000-00-00'){
                                $_date = strtotime($val['effective_date']);
                                $_year = $this->_findYearOfDay($listYears, $_date);
                                if(!isset($purchaseValues['billed'][$_year])){
                                    $purchaseValues['billed'][$_year] = 0;
                                }
                                $purchaseValues['billed'][$_year] += $val['billed'];
                            }
                        }
                    }
                }
            }
        }
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
        $profiltId = ($tmpProfit == true) ? 0 : $profiltId;
        $displayYear = isset($this->companyConfigs['budget_display_y']) && !empty($this->companyConfigs['budget_display_y']) ?  true : false;
        $displayNextOneYear = isset($this->companyConfigs['budget_display_y_next_one']) && !empty($this->companyConfigs['budget_display_y_next_one']) ?  true : false;
        $displayNextTwoYear = isset($this->companyConfigs['budget_display_y_next_two']) && !empty($this->companyConfigs['budget_display_y_next_two']) ?  true : false;
        $displayLastOneYear = isset($this->companyConfigs['budget_display_y_last_one']) && !empty($this->companyConfigs['budget_display_y_last_one']) ?  true : false;
        $displayLastTwoYear = isset($this->companyConfigs['budget_display_y_last_two']) && !empty($this->companyConfigs['budget_display_y_last_two']) ?  true : false;
        $this->set(compact('display', 'year', 'paths', 'profit', 'companyName', 'project_id', 'profiltId', 'dataOfYearInternals', 'dataOfYearExternals', 'internalValues', 'externalValues', 'saleValues', 'purchaseValues', 'projects', 'displayLastTwoYear', 'displayLastOneYear', 'displayYear', 'displayNextOneYear', 'displayNextTwoYear', 'employee_info','budget_settings'));
    }

    /**
     * Tim year theo date truyen vao
     */
    private function _findYearOfDay($listYears = array(), $date = ''){
        $result = '';
        if(!empty($listYears) && !empty($date)){
            foreach($listYears as $y => $val){
                $start = $val['start'];
                $end = $val['end'];
                if($start <= $date && $date <= $end){
                    $result = $y;
                    break;
                }
            }
        }
        return $result;
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
    function _getProfits($profitId, $getDataByPath = true) {
       $this->loadModels('ActivityForecast');
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        // $profit = $Model->find('first', array(
  //           'recursive' => -1, 'fields' => array('id'),
  //           'conditions' => array(
        // 		'company_id' => $user['company_id'],
        // 		'OR' => array(
        // 			'manager_id' => $user['id'],
        // 			'manager_backup_id' => $user['id'],
        // 			)
        // 		)
        // 	)
        // );
        //modify by QN, enable control_resource for PM
        $canManageResource = $this->employee_info['CompanyEmployeeReference']['role_id'] == 3 && $this->employee_info['CompanyEmployeeReference']['control_resource'];
        $myPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'conditions' => array(
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id IS NULL'),
                'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => 0),
                'employee_id' => $user['id']
            )
        ));
        $conds = array('manager_id' => $user['id']);
        if( $canManageResource )$conds['id'] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
        $profit = $Model->find('list', array(
            'recursive' => -1, 'fields' => array('id'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
                'company_id' => $user['company_id'],
                'OR' => $conds
            )
        ));
        $this->loadModels('ProfitCenterManagerBackup');
        $backups = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $user['id']),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $profit = array_unique(array_merge($profit, $backups));
        $isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr';
        if ($isAdmin) {
            $paths = $Model->generatetreelist(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } elseif (!empty($profit)) {
            // $paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
            // 		'lft >=' => $profit['ProfitCenter']['lft'],
            // 		'rght <=' => $profit['ProfitCenter']['rght']
            // 		)));
            $paths = array();
            foreach($profit as $_val)
            {
                $paths[] = $_val;
                $pathsTemp = $Model->children($_val);
                $pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
                $paths = array_merge($paths,$pathsTemp);
            }
            $paths = $Model->generatetreelist(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } else {
            return false;
        }
        if (empty($profitId)) {
            $profitId = !empty($profit['ProfitCenter']['id']) ? $profit['ProfitCenter']['id'] : 0;
        } elseif (!isset($paths[$profitId])) {
            return false;
        }
        $profit = $Model->find('first', array('recursive' => -1, 'conditions' => array('id' => $profitId)));
        $profit = !empty($profit) ? array_shift($profit) : null;

        //APPLY FOR CASE GET DATA BY PATH
        $_pc = $profit['id'];
        if($getDataByPath)
        {
            $listManagers = ClassRegistry::init('ProfitCenter')->children($_pc);
            $listManagers = Set::combine($listManagers,'{n}.ProfitCenter.id','{n}.ProfitCenter.manager_id');
            $pathOfPC = array_merge(array($_pc),array_keys($listManagers));
            //$pathOfPC = array_keys($listManagers);
        }
        else
        {
            $pathOfPC = $_pc;
            $listManagers = $Model->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'manager_id'),
                'conditions' => array(
                    'NOT' => array('manager_id' => null),
                    'parent_id' => $pathOfPC,
                    'company_id' => $this->employee_info['Company']['id'])
            ));
        }


        $list = array_merge(array($profit['manager_id']), $listManagers);
        $list = array_unique(array_filter($list));

        $employees = array_merge($list, $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                    'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                    'conditions' => array('profit_center_id' => $pathOfPC))));

        $employees = Set::combine($this->ActivityForecast->Employee->find('all', array(
                            'order' => 'first_name DESC',
                            'fields' => array('id', 'first_name', 'last_name'),
                            'recursive' => -1, 'conditions' => array('id' => $employees)))
                        , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        //END
        $_managers = array();
        foreach ($list as $key) {
            if (isset($employees[$key])) {
                $_managers[$key] = $employees[$key] . '<strong> (Manager)</strong>';
            }
        }
        $employees = $_managers + $employees;
        if (!$isAdmin) {
            //unset($employees[$user['id']]);
        }
        return array($profit, $paths, $employees, $user);
    }
    public function saveDisplay($view){
        $this->loadModel('HistoryFilter');
        $checkS = $this ->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'project_budget_fiscals/saveDisplay',
            )
        ));
        if(!empty($checkS)){
            $saved = array(
                'params' => $view
            );
            $this->HistoryFilter->id = $checkS['HistoryFilter']['id'];
            $this->HistoryFilter->save($saved);
        } else {
            $saved = array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'project_budget_fiscals/saveDisplay',
                'params' => $view
            );
            $this->HistoryFilter->create();
            $this->HistoryFilter->save($saved);
        }
        die(1);
    }
}
?>
