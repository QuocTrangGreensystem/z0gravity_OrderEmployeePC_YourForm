<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAmrsPreviewController extends AppController {

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
     var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt', 'GanttSt','GanttV2Preview');

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('Project', 'ProjectAmr', 'UserView', 'UserDefaultView',);

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->loadModels('ProjectAcceptanceType', 'ProjectAcceptance');
    }

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('LogSystem', 'Lib');
    /**
     * Staffing of employee
     *
     *
     * @return array()
     * @access private
     */
     private function _staffingEmloyee($project_id = null){
        $this->loadModel('TmpStaffingSystem');
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay du lieu staffing cho employee
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'employee',
                'company_id' => $employeeName['company_id']
            )
        ));


        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * Employee => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if(!empty($datas)){
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }
        $ids = !empty($datas) ? array_keys($datas) : array();
        $this->loadModel('Employee');
        $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $ids),
                'fields' => array('id', 'fullname'),
                'order' => array('Employee.id')
            ));
        if(!empty($datas['999999999'])){
            $notAffected = array(
                'Employee' => array(
                    'id' => '999999999',
                    'fullname' => 'Not Affected'
                )
            );
            $employees[] = $notAffected;
        }
        $staffings = array();
        foreach($employees as $key => $employee){
            $staffings[$key]['id'] = $employee['Employee']['id'];
            $staffings[$key]['is_check'] = 1;
            $staffings[$key]['name'] = $employee['Employee']['fullname'];
            $staffings[$key]['data'] = isset($datas[$employee['Employee']['id']]) ? $datas[$employee['Employee']['id']] : '';
        }

        return $staffings;
     }

    /**
     *  Tinh toan staffing ho tro cho bo loc va Dashboard generating
     *
     */
     private function _totalStaffing($staffings){
        $staffings = !empty($staffings) ? Set::combine($staffings, '{n}.id', '{n}.data') : array();
        $datas = array();
        if(!empty($staffings)){
            foreach($staffings as $staffing){
                foreach($staffing as $time => $values){
                    $_time = date('m/y', $values['date']);
                    $datas[$time]['date'] = $_time;
                    $datas[$time]['set_date'] = $time;
                    if(!isset($datas[$time]['consumed'])){
                        $datas[$time]['consumed'] = 0;
                    }
                    $datas[$time]['consumed'] += $values['consumed'];
                    if(!isset($datas[$time]['validated'])){
                        $datas[$time]['validated'] = 0;
                    }
                    $datas[$time]['validated'] += $values['validated'];
                    if(!isset($datas[$time]['remains'])){
                        $datas[$time]['remains'] = 0;
                    }
                    $datas[$time]['remains'] += $values['remains'];
                }
            }
        }
        ksort($datas);
        $results = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                $_time = date('m/y', $key);
                $results[$_time] = $data;
            }
        }
        return $results;
     }

     /**
       * Ham tinh toan lai remain
       * Neu nhung thang la qua khu thi remain = 0
       * Remain nhung thang qua khu se duoc chia dieu cho thang hien tai va cac thang trong tuong lai
       * Ham cui bap - co thoi gian se viet lai
       */
      private function _resetRemainSystem($datas = array()){
        $currentDate = strtotime(date('01-m-Y', time()));
        $totalEstimated = $_total = $monthValidates = array();
        foreach($datas as $id => $data){
            $works = $cons = $remains = 0;
            foreach($data as $time => $value){
                if($time < $currentDate){
                    $works += $value['validated'];
                    $cons += $value['consumed'];
                    $remains += $value['remains'];
                    $_total[$id]['validated'] = $works;
                    $_total[$id]['consumed'] = $cons;
                    $_total[$id]['remain'] = $remains;
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    if(!empty($value['validated'])){
                        $monthValidates[$id][] = $time;
                    }
                }
            }
        }
        if(!empty($monthValidates)){
            foreach($monthValidates as $id => $monthValidate){
                if(in_array($currentDate, $monthValidate)){
                    //do nothing
                } else {
                    $monthValidates[$id][] = $currentDate;
                }
            }
        }
        foreach($datas as $id => $data){
            foreach($data as $time => $value){
                if($time < $currentDate){
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    $count = !empty($monthValidates[$id]) ? count($monthValidates[$id]) : 1;
                    $remain = !empty($_total[$id]) ? $_total[$id]['remain'] : 0;
                    if($count == 0){
                        $remainFirst = 0;
                        $remainSecond = 0;
                    } else {
                        $getRemain = $this->Lib->caculateGlobal($remain, $count, false);
                        $remainFirst = $getRemain['original'];
                        $remainSecond = $getRemain['remainder'];
                    }
                    if(!empty($value['validated']) || $time == $currentDate){
                        $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst;
                        if(!empty($monthValidates[$id]) && $time == max($monthValidates[$id])){
                            $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst +$remainSecond ;
                        }
                    } else {
                        if(!empty($datas[$id][$currentDate])){
                            if($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0){
                                $datas[$id][$currentDate]['remains'] = $remainFirst + $remainSecond;
                            }
                        }
                    }
                }
            }
        }
        return $datas;
      }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    private function _parseParams() {
        $year = date('Y');
        if (!empty($this->params['url']['year'])) {
            $year = intval($this->params['url']['year']);
        }
        $_start = strtotime($year . '-1-1');
        $_end = strtotime($year . '-12-31');

        if (empty($_start) || empty($_end)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

    private function _dashBoard($project_id){
        $this->loadModel('Project');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        list($_start, $_end) = $this->_parseParams();
        $staffings = array();
        if($filterId == 1){
            $staffings = $this->_staffingEmloyee($project_id);
        } elseif($filterId == 2){
            $staffings = $this->_staffingProfitCenter($project_id);
        } else {
            $staffings = $this->_staffingFunction($project_id);
        }
        $result = $manDays = array();
        //$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        //$months = range(1, 12);
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $sumConsumed=$sumPlaned=0;
        if(!empty($staffings)){
            $datas = $this->_totalStaffing($staffings);
            $_setYear = $years = $estimatedNextMonths = array();
            $consumed = $validated = 0;

            $lastData=count($datas)-1;
            $i=0;
            foreach($datas as $key=>$val) {
                if($i==0) $startDay=$val['set_date'];
                elseif($i==$lastData) $endDay=$val['set_date'];
                $i++;
            }

            $minDate=mktime(0,0,0, date("m", $startDay)-1, date("d", $startDay), date("Y", $startDay));
            $maxDate= !empty($endDay) ? mktime(0,0,0, date("m", $endDay)+1, date("d", $endDay), date("Y", $endDay)) : 0;
            while($minDate<$maxDate) {
                $key=date("m", $minDate).'/'.date("y", $minDate);
                //$manDays[]=$key;
                if(isset($datas[$key])) {
                    $_val=$datas[$key];
                    $setYear[]=date("Y", $minDate);
                    //progress
                    $sumConsumed+=$_val['consumed'];
                    $sumPlaned+=$_val['validated'];
                }
                if($minDate <= time()) {
                    $dataSets[]=array('date'=>$key,'validated'=>$sumPlaned,'consumed'=>$sumConsumed);
                } else {
                    $dataSets[]=array('date'=>$key,'validated'=>$sumPlaned);
                }
                $minDate = mktime(0, 0, 0, date("m", $minDate)+1, date("d", $minDate), date("Y", $minDate));
            }
        }
        $manDays=$sumPlaned+round($sumPlaned*0.1,2);
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('project_name')
            ));
        $_setYear = !empty($_setYear) ? array_unique($_setYear) : array();
        asort($_setYear);
        $setYear = !empty($_setYear) ? implode('-' ,$_setYear) : '';
        $display = isset($this->params['url']['ControlDisplay']) ? $this->params['url']['ControlDisplay'] : range(1, 3);
        $countDataSet = 0;
        if(!empty($dataSets)) $countDataSet = count($dataSets);
        $this->set(compact('dataSets', 'filterId', 'project_id', 'manDays', 'projectName', 'setYear', 'display', 'countDataSet'));
     }

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function index_plus($id = null) {
        $project_id = $id;
        if (!$this->_checkRole(false, $id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
            $this->data = null;
        }
        $this->_checkWriteProfile('kpi');
        // ---------------------------
        $employee_info = $this->Session->read("Auth.employee_info");
        $employee_id = $employee_info['Employee']['id'];
        $this->set('project_id', $id);
      

        if (empty($this->data)) {
            $this->data = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
            $this->set(compact('data'));
        } else {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            // weather
            $this->data["ProjectAmr"]["weather"] = $this->data["ProjectAmr"]["weather"][0];
            // rank
            $this->data["ProjectAmr"]["rank"] = $this->data["ProjectAmr"]["rank"][0];
            unset($this->data["ProjectAmr"]["displayplan"]);
            unset($this->data["ProjectAmr"]["displayreal"]);
            if ($this->Project->ProjectAmr->save($this->data["ProjectAmr"])) {
                if (empty($this->data['ProjectAmr']['id']))
                    $project_amr_id = $this->Project->ProjectAmr->getLastInsertID(); // get last insert id
                else {
                    $project_amr_id = $this->data['ProjectAmr']['id'];
                }
                $this->Project->id = $this->data['ProjectAmr']['project_id'];
                $data_project['Project']['update_by_employee'] = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
                $data_project['Project']['last_modified'] = time();
                $this->Project->save($data_project);
                //Added by QN on 2015/02/05
                $projectName = $this->Project->find("first", array(
                'recursive' => -1,
                "fields" => array("project_name", 'company_id'),
                'conditions' => array('Project.id' => $id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update project `%s` via KPI', $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                // $this->Session->setFlash(__('The project AMR has been saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The project AMR could not be saved. Please try again enter informations for (*) fields match.', true), 'error');
            }
            $this->redirect($this->referer());
        }

        $view_id = $this->UserDefaultView->find('first', array('fields' => 'UserDefaultView.user_view_id', 'conditions' => array('UserDefaultView.employee_id' =>
                $employee_id)));
        $view_id = $view_id['UserDefaultView']['user_view_id'];
        $view = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
        $view_content = $view['UserView']['content'];
        if ($view_content == "") { //default view
            $view_content = '<user_view>
                                <project_detail project_name = "Project Name" />
                                <project_detail company_id = "Company" />
                                <project_detail project_manager_id = "Project Manager" />
                                <project_detail project_priority_id = "Priority" />
                                <project_detail project_status_id = "Status" />
                                <project_detail start_date = "Start Date" />
                                <project_detail issues = "Issues" />
                                <project_detail constraint = "Constraint" />
                                <project_detail remark = "Remark" />
                                <project_detail project_objectives = "Project Objectives" />
                                <project_amr weather = "Weather" />
                                <project_amr project_amr_program_id = "Program" />
                                <project_amr project_amr_sub_program_id = "Sub Program" />
                                <project_amr project_amr_category_id = "Category" />
                                <project_amr project_amr_sub_category_id = "Sub Category" />
                                <project_amr project_manager_id = "Project Manager" />
                                <project_amr budget = "Budget" />
                                <project_amr project_amr_status_id = "Status" />
                                <project_amr project_amr_mep_date = "MEP Date" />
                                <project_amr project_amr_progression = "Progression" />
                                <project_amr project_phases_id = "Phase" />
                                <project_amr project_amr_cost_control_id = "Cost Control" />
                                <project_amr project_amr_organization_id = "Organization" />
                                <project_amr project_amr_plan_id = "Planning" />
                                <project_amr project_amr_perimeter_id = "Perimeter" />
                                <project_amr project_amr_risk_control_id = "Risk Control" />
                                <project_amr project_amr_problem_control_id = "Problem Control" />
                                <project_amr project_amr_risk_information = "Risk Information" />
                                <project_amr project_amr_problem_information = "Problem Information" />
                                <project_amr project_amr_solution = "Solution" />
                                <project_amr project_amr_solution_description = "Solution Description" />
                        </user_view>';
        }
        $projectStatuses = $this->Project->ProjectStatus->find('list');
        $amrCurrencies = $this->Project->Currency->find('list');

        $project_detail = $this->Project->find("first", array('conditions' => array('Project.id' => $id)));
        $project_manager = $this->Project->Employee->find('first', array('fields' => array('Employee.id', 'Employee.fullname'),
            'conditions' => array('Employee.id' => $project_detail['Project']['project_manager_id'])));
        $company_id = $project_detail['Project']['company_id'];
        $projectAmrManagers = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));


        $projectAmrPrograms = $this->Project->ProjectAmr->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
            'conditions' => array('ProjectAmrProgram.company_id' => $company_id)));
        $projectAmrSubPrograms = $this->Project->ProjectAmr->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program')));
        $projectAmrCategories = $this->Project->ProjectAmr->ProjectAmrCategory->find('list', array('fields' => array('ProjectAmrCategory.id', 'ProjectAmrCategory.amr_category'),
            'conditions' => array('ProjectAmrCategory.company_id' => $company_id)));
        $projectAmrSubCategories = $this->Project->ProjectAmr->ProjectAmrSubCategory->find('list', array('fields' => array('ProjectAmrSubCategory.id', 'ProjectAmrSubCategory.amr_sub_category')));
        $projectAmrStatuses = $this->Project->ProjectAmr->ProjectAmrStatus->find('list', array('fields' => array('ProjectAmrStatus.id', 'ProjectAmrStatus.amr_status'),
            'conditions' => array('ProjectAmrStatus.company_id' => $company_id)));
        $projectAmrCostControls = $this->Project->ProjectAmr->ProjectAmrCostControl->find('list', array('fields' => array('ProjectAmrCostControl.id', 'ProjectAmrCostControl.amr_cost_control'),
            'conditions' => array('ProjectAmrCostControl.company_id' => $company_id)));
        $projectAmrOrganizations = $this->Project->ProjectAmr->ProjectAmrOrganization->find('list', array('fields' => array('ProjectAmrOrganization.id', 'ProjectAmrOrganization.amr_organization'),
            'conditions' => array('ProjectAmrOrganization.company_id' => $company_id)));
        $projectAmrPlans = $this->Project->ProjectAmr->ProjectAmrPlan->find('list', array('fields' => array('ProjectAmrPlan.id', 'ProjectAmrPlan.amr_plan'),
            'conditions' => array('ProjectAmrPlan.company_id' => $company_id)));
        $projectAmrPerimeters = $this->Project->ProjectAmr->ProjectAmrPerimeter->find('list', array('fields' => array('ProjectAmrPerimeter.id', 'ProjectAmrPerimeter.amr_perimeter'),
            'conditions' => array('ProjectAmrPerimeter.company_id' => $company_id)));
        $projectAmrRiskControls = $this->Project->ProjectAmr->ProjectAmrRiskControl->find('list', array('fields' => array('ProjectAmrRiskControl.id', 'ProjectAmrRiskControl.amr_risk_control'),
            'conditions' => array('ProjectAmrRiskControl.company_id' => $company_id)));
        $projectAmrProblemControls = $this->Project->ProjectAmr->ProjectAmrProblemControl->find('list', array('fields' => array('ProjectAmrProblemControl.id', 'ProjectAmrProblemControl.amr_problem_control'),
            'conditions' => array('ProjectAmrProblemControl.company_id' => $company_id)));
        $projectAmrPhases = $this->Project->ProjectPhase->find('list', array('order' => array("ProjectPhase.phase_order ASC")));
        //options for projectManagers
        $allEmployees = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $employeeIds = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array();
        foreach ($employeeIds as $key => $value) {
            foreach ($allEmployees as $key2 => $value2) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $value2;
                    break;
                }
            }
        }
        $this->data['ProjectAmr']['project_manager_id'] = $project_manager['Employee']['id'];
        $this->data['ProjectAmr']['currency_id'] = $project_detail['Project']['currency_id'];
        // options for ProjectPhases
        $ProjectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => array('ProjectPhase.phase_order ASC')));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $start_date = $project_detail['Project']['start_date'];
        $end_date = $project_detail['Project']['end_date'];
        $end_plan_date = $project_detail['Project']['planed_end_date'];
        $projectName = $this->Project->find("first", array(
            'recursive' => -1,
            "fields" => array("Project.project_name", 'updated', 'last_modified', 'update_by_employee', 'id', 'company_id', 'address','primary_objectives','project_objectives'),
            'conditions' => array('Project.id' => $id)));
        // change feedback 05/04/2013
        $this->loadModel('ProjectEvolution');
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectEvolution.project_id' => $id),
                'fields' => array('SUM(man_day) as totalMD')
            ));
        $_projectTask = $this->_getPorjectTask($id);
        $validated = isset($projectEvolutions[0][0]['totalMD']) ? $projectEvolutions[0][0]['totalMD'] : 0;
        $engaged = $_projectTask['engaged'];
        $remain = $_projectTask['remain'];
        $validated =  $_projectTask['validated'];
        //24/10/2013 huy thang
        // $variance = $validated - $engaged - $remain;
        $variance = $engaged + $remain - $validated;
        //24/10/2013 huy thang

        $progression = $_projectTask['progression'];

        $this->set(compact('validated', 'engaged', 'remain', 'variance', 'progression', 'validated'));
        // end change feedback 05/04/2013

        // change feedback 22/04/2013
        $this->loadModel('ProjectPhasePlan');

        $projectPlans = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MIN(phase_planed_start_date) AS startDate',
                    'MAX(phase_planed_end_date) AS endDate'
                )
            ));
        $projectPlan = $this->ProjectPhasePlan->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MAX(phase_planed_end_date) AS endDate'
                )
            ));
        $projectReal = $this->ProjectPhasePlan->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MAX(phase_real_end_date) AS endDate'
                )
            ));
        $timeDelay = strtotime($projectReal[0]['endDate']) - strtotime($projectPlan[0]['endDate']);
        $delay = intval($timeDelay/86400);
        $endDateAllTask = null;
        if(!empty($projectPlans[0][0]['endDate'])){
            $endDateAllTask = strtotime($projectPlans[0][0]['endDate']);
        }
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $id,
                'NOT' => array(
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if(!empty($dataSystems)){
            foreach($dataSystems as $dataSystem){
                $dx = $dataSystem['TmpStaffingSystem'];
                if(!isset($assignEmployees[$dx['project_id']])){
                    $assignEmployees[$dx['project_id']] = 0;
                }
                $assignEmployees[$dx['project_id']] += $dx['estimated'];
            }
        }
        $_dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('profit_center'),
                'project_id' => $id,
                'NOT' => array(
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignProfitCenters = array();
        if(!empty($_dataSystems)){
            foreach($_dataSystems as $_dataSystem){
                $dx = $_dataSystem['TmpStaffingSystem'];
                if(!isset($assignProfitCenters[$dx['project_id']])){
                    $assignProfitCenters[$dx['project_id']] = 0;
                }
                $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
            }
        }
        $tWorkload = !empty($_projectTask['validated']) ? $_projectTask['validated'] : 0;
        $assgnPc = !empty($assignProfitCenters[$id]) ? $assignProfitCenters[$id] : 0;
        $assginProfitCenter = ($tWorkload == 0) ? '0' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100' : round(($assgnPc/$tWorkload)*100, 2));
        $assgnEmploy = !empty($assignEmployees[$id]) ? $assignEmployees[$id] : 0;
        $assgnEmployee = ($tWorkload == 0) ? '0' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100' : round(($assgnEmploy/$tWorkload)*100, 2));
        //Gantt+ insert to KPI
        $this->_ganttFromPhase($id);
        // Dash board budget
        $this->_dashBoard($id);
        //Graph External task
        $this->getDataOfExternalTask($id);
        // lay du lieu cho phan budget internal, external, sales
        $this->_getDataBudget($id);
        $this->set(compact('endDateAllTask', 'assginProfitCenter', 'assgnEmployee', 'assgnEmploy', 'tWorkload', 'assgnPc'));
        // end change feedback 22/04/2013
        /**
         * Ten cong ty
         */
        $this->loadModel('LogSystem');
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $companyName = strtolower($this->employee_info['Company']['dir']);
        $company_id = $this->employee_info['Company']['id'];
        $avatarEmployeeLogin = $this->employee_info['Employee']['avatar_resize'];
        if(!empty($this->data) && !empty($this->data['ProjectAmr']['project_amr_solution'])){
            $updated = !empty($projectName['Project']['updated']) ? date('H:i d/m/Y', $projectName['Project']['updated']) : '../../....';
            $byEmployee = !empty($projectName['Project']['update_by_employee']) ? $projectName['Project']['update_by_employee'] : 'N/A';
            $employOldId = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array('fullname' => $byEmployee),
                'fields' => array('id')
            ));
            $saveOldLogs = array(
                'company_id' => $company_id,
                'model' => 'ProjectAmr',
                'model_id' => $projectName['Project']['id'],
                'name' => $byEmployee . ' ' . $updated,
                'description' => $this->data['ProjectAmr']['project_amr_solution'],
                'employee_id' => !empty($employOldId) && $employOldId['Employee']['id'] ? $employOldId['Employee']['id'] : '',
                'update_by_employee' => $byEmployee
            );
            $this->LogSystem->create();
            if($this->LogSystem->save($saveOldLogs)){
                $this->ProjectAmr->id = $this->data['ProjectAmr']['id'];
                $this->ProjectAmr->save(array('project_amr_solution' => ''));
            }
        }
		
        /**
         * Lay tat ca cac log
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk', 'ToDo', 'Done'),
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('*'),
            'order' => array('updated' => 'DESC')
        ));
        $groupByModels = $listEmployeeLogs = array();
        if(!empty($logSystems)){
            foreach($logSystems as $logSystem){
                $dx = $logSystem['LogSystem'];
                $groupByModels[$dx['model']][] = $dx;
                $listEmployeeLogs[$dx['employee_id']] = $dx['employee_id'];
            }
        }
        $commentIssues = !empty($groupByModels['ProjectIssue']) ? $groupByModels['ProjectIssue'] : array();
        $commentRisks = !empty($groupByModels['ProjectRisk']) ? $groupByModels['ProjectRisk'] : array();
        $logSystems = !empty($groupByModels['ProjectAmr']) ? $groupByModels['ProjectAmr'] : array();
        $todos = !empty($groupByModels['ToDo']) ? $groupByModels['ToDo'] : array();
        $dones = !empty($groupByModels['Done']) ? $groupByModels['Done'] : array();
        //acceptance
        $acceptances = $this->ProjectAcceptance->find('all', array(
            'fields' => '*',
            'conditions' => array(
                'project_id' => $id
            )
        ));
        $this->set('types', $this->ProjectAcceptanceType->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array(
                'company_id' => $company_id
            )
        )));
        $this->set('acceptances', $acceptances);
        /**
         * Danh sach avatar cua employee Logs
         */
        $avatarEmploys = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $listEmployeeLogs),
            'fields' => array('id', 'avatar_resize')
        ));
        /**
         * See budget of PM
         */
        $seeBudgetPM = ClassRegistry::init('CompanyEmployeeReference')->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id),
            'fields' => array('see_budget', 'role_id')
        ));
        $resetRole = !empty($seeBudgetPM) && !empty($seeBudgetPM['CompanyEmployeeReference']['role_id']) ? $seeBudgetPM['CompanyEmployeeReference']['role_id'] : 4;
        $seeBudgetPM = !empty($seeBudgetPM) && !empty($seeBudgetPM['CompanyEmployeeReference']['see_budget']) ? $seeBudgetPM['CompanyEmployeeReference']['see_budget'] : 0;
        $this->loadModels('Menu');
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenus = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        $this->set(compact('settingMenus', 'resetRole', 'seeBudgetPM', 'commentRisks', 'commentIssues', 'company_id', 'employeeLoginId', 'avatarEmployeeLogin', 'employeeLoginName', 'companyName', 'avatarEmploys', 'logSystems', 'todos', 'dones', 'employee_info'));
        $this->set(compact('projectStatuses', 'projectManagers', 'project_manager', 'start_date', 'end_date', 'end_plan_date', 'view_content', 'projectAmrManagers', 'projectAmrPrograms', 'projectAmrSubPrograms', 'projectAmrCategories', 'projectAmrSubCategories', 'ProjectPhases', 'projectAmrStatuses', 'projectAmrCostControls', 'projectAmrOrganizations', 'projectAmrPlans', 'projectAmrPerimeters', 'projectAmrRiskControls', 'projectAmrProblemControls', 'projectAmrPhases', 'amrCurrencies', 'currency_name', 'projectName', 'data','delay'));
		
        //CHECK REBUILD STAFFING
        $lib = new LibBehavior();
        $rebuildStaffing = $lib->checkRebuildStaffing('Project',$id);
        $this->set('rebuildStaffing', $rebuildStaffing);
        $this->set('project_id', $id);
        //them budget_setting(doan coment se xoa sau khi chay xong)
		
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

        // Get layout setting
        $this->loadModel('ProjectIndicatorSetting');
        $indicatorSetting = $this->ProjectIndicatorSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'employee_id' => $this->employee_info['Employee']['id'],
            ),
            'fields' => array('widget_setting')
        ));
        $layout_setting = $grid_setting = $order_row = array();
        if(!empty($indicatorSetting['ProjectIndicatorSetting']['widget_setting'])){
            $indicatorSetting = $indicatorSetting['ProjectIndicatorSetting']['widget_setting'];
            $indicatorSetting = json_decode($indicatorSetting);
            $i = 0;
            foreach ($indicatorSetting as $value) {
                $value = explode( '|',$value);
                $grid_setting = explode( '-',$value[1]);
                $layout_setting[$value[0]]['display'] =  $value[2];
                $order_row[$i]['display'] = $value[2];
                $order_row[$i]['widget'] = $value[0];
                foreach ($grid_setting as $set) {
                    $set = explode( '_', $set);
                    $layout_setting[$value[0]][$set[0]] = $set[1];
                    $order_row[$i] = $set[1];
                }
                
            }
        }
        $this->set('budget_settings',$budget_settings);
        $this->set(compact('layout_setting'));
		
		/* Huynh */ 
		$this->loadModel('ProjectGlobalView');
		$this->loadModel('ProjectImage');
		$projectGlobalView = $this->ProjectGlobalView->find("first", array(
			'recursive' => -1, 'fields' => array('id', 'attachment','is_file', 'is_https'),
			"conditions" => array('project_id' => $project_id)
		));
		$projectGlobalViewExists = true;
		if ($projectGlobalView) {
			$link = trim($this->_getPathGlobalView($project_id)
					. $projectGlobalView['ProjectGlobalView']['attachment']);
			if (!file_exists($link) || !is_file($link)) {
				$projectGlobalViewExists = false;
			}
		}
		$this->set('projectGlobalView',$projectGlobalView);
		$this->set('projectGlobalViewExists',$projectGlobalViewExists);
		
		$projectImage = $this->ProjectImage->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'type' => array('image', 'application')
			)
		));
		$this->set('projectImage',$projectImage);
		
		/*
         * Finance detail
         */
		$this->loadModel('ProjectFinancePlusDetail');
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $totals = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if(empty($totals[$dx['type']][$dx['model']])){
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        };
		$this->set('financeTotals',$totals);
		$this->_get_create_value($project_id);
		
		/* END Huynh */ 
		
        //END
        $listAssign = $this->_getTeamEmployees($project_id);
        $this->set('listAssign', $listAssign);
        $checkAvatar = $this->checkAvatar($project_id);
        $this->set('checkAvatar', $checkAvatar);
        $this->z0g_messages($project_id);
		/* Huynh */
        $this->set('projectName', $projectName);
		/* End Huynh */
		
    }
	private function _get_create_value($project_id){
		$this->loadModels('ProjectCreatedVal','ProjectCreatedValue');
		if (!$this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
        }
		$employee_info = $this->employee_info;
        $this->_checkWriteProfile('created_value');
		$sum_of_type = $this->requestAction('/project_created_vals_preview/updateCreatedValue/'. $project_id);
        $project_name = $this->Project->find('first', array(
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('Project.project_name'),
            'recursive' => -1));
        $this->set('project_name', $project_name['Project']['project_name']);
        $projectCreatedVals = $this->ProjectCreatedVal->find('all', array('conditions' => array('ProjectCreatedVal.project_id' => $project_id)));
        
        $projectC = array();
        $id = '';
        if (!empty($projectCreatedVals)) {
            foreach ($projectCreatedVals as $projectCreatedVal) {
                if ($projectCreatedVal['ProjectCreatedVal']['description'] != '0')
                    $projectC['ProjectCreatedVal'] = unserialize($projectCreatedVal['ProjectCreatedVal']['description']);
                else
                    $projectC['ProjectCreatedVal'] = "";
            }
            $id = $projectCreatedVals[0]['ProjectCreatedVal']['id'];
        }
        /** Created by Dai Huynh 29/05/2018 
        * table project_created_values
        * input: $projectC['ProjectCreatedVal'] : id type_value
        * output: type_value / sum('value')
        */

        $sumSelectedOfTypeVals = array();
        if(!empty($projectC['ProjectCreatedVal'])){
            $sumSelectedOfTypeVals = $this->ProjectCreatedValue->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $projectC['ProjectCreatedVal'],
                ),
                'fields' => array(
                    'type_value',
                    'SUM(`value`) as `value`',
                ),
                'group' => array('type_value')
            ));
            $sumSelectedOfTypeVals = !empty($sumSelectedOfTypeVals) ? Set::combine($sumSelectedOfTypeVals, '{n}.ProjectCreatedValue.type_value', '{n}.0.value') : array();
        }
		
        $this->set('id', $id);
        $this->set('projectC', $projectC);
        $this->set(compact('project_id'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        $sumOfTypeVal = array();
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'block_name', 'next_block'),
				'order' => array('value_order')));
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] = 0;
            }
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] += $value['ProjectCreatedValue']['value'];
            }
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        } else {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'block_name', 'next_block'),
				'order' => array('value_order')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        }
		// totalValue
		if(!empty($sum_of_type)){
			$sumOfTypeVal = $sum_of_type;
			$totalValue['ProjectCreatedValue']['total'] =  array_sum($sum_of_type);
		}
        $dataProjectCreatedValsComment = $this->getDataProjectCreatedValsComment($project_id);

        $employees_commentID = array();
        $employees_commentID = !empty($dataProjectCreatedValsComment) ? Set::extract($dataProjectCreatedValsComment, '{n}.ProjectCreatedValsComment.employee_id') : array();
        $employees_commentID = array_unique($employees_commentID);
        $employees_comment_info = array();
        $employees_comment_info = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('id' => $employees_commentID),
            'fields' => array('id','first_name', 'last_name','avatar_resize')
        ));
        $employees_comment_info = !empty($employees_comment_info) ? Set::combine($employees_comment_info, '{n}.Employee.id', '{n}.Employee') : '';
		$canComment = $this->_canComment($project_id);
        $this->set(compact('created_values','totalValue', 'sumOfTypeVal','sumSelectedOfTypeVals','dataProjectCreatedValsComment','employees_comment_info','canComment'));
	}
	/* Function getDataProjectCreatedValsComment
    * Created by Dai Huynh 29/5/2018
    * @params $project_id
    * Return array of comment for ProjectCreatedVals
    */
    private function getDataProjectCreatedValsComment($project_id = null){
        $this->loadModel('ProjectCreatedValsComment');
         if(empty($project_id)) return false;
        $dataProjectCreatedValsComment = $this->ProjectCreatedValsComment->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array(
                'employee_id', 
                'comment', 
                'created', 
                'type_value', 
            ),
        ));
        return $dataProjectCreatedValsComment;
    }
	
	private function getStatusEX(){
        $this->loadModel('ProjectStatus');
        $status = array();
        $projectStatus = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info["Company"]["id"]
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name', 'status')
        ));
        foreach ($projectStatus as $key => $value) {
            $status[$key]['text'] = $value['ProjectStatus']['name'];
            $status[$key]['dataField'] = $value['ProjectStatus']['id'];
        }
        return $status;
    }
    private function _widget_name(){
		$this->loadModel('Menu');
		$translateWidget = $this->Menu->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info["Company"]["id"],
				'model' => 'project',
			),
			'fields' => array('widget_id','name_eng','name_fre')
		));
		$langCode = Configure::read('Config.langCode');
		$currentLang = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
		$translateWidget = !empty($translateWidget) ? Set::combine($translateWidget, '{n}.Menu.widget_id', '{n}.Menu') : array();
        $list_wid = array(
			'project_synthesis' => 'Synthesis',
			'project_gantt' => 'Gantt++',
			'project_milestones' => !empty($translateWidget['milestone'][$currentLang]) ? $translateWidget['milestone'][$currentLang] : 'Milestones',
            'project_pictures' => 'Vision',
            'project_location' => !empty($translateWidget['local_view'][$currentLang]) ? $translateWidget['local_view'][$currentLang] : 'Localisation',
            'indicator_assign_object' => 'Participants & Objectifs',
            'project_budget' => !empty($translateWidget['finance_plus'][$currentLang]) ? $translateWidget['finance_plus'][$currentLang] : 'Budget',
			'project_progress_line' => 'Progression',
            'project_task' => !empty($translateWidget['task'][$currentLang]) ? $translateWidget['task'][$currentLang] : 'Tasks',
            'project_messages' => 'Messages',
            'project_risk' => !empty($translateWidget['risk'][$currentLang]) ? $translateWidget['risk'][$currentLang] : 'Risques',
            'project_created_value' => !empty($translateWidget['created_value'][$currentLang]) ? $translateWidget['created_value'][$currentLang] : __('Created Value', true),
            'project_status' => 'Status',
            'project_synthesis_budget' => !empty($translateWidget['synthesis'][$currentLang]) ? $translateWidget['synthesis'][$currentLang] : __('Synthesis Budget',true),
        );
        return  $list_wid;
    }
	private function _list_widget_init(){
        $this->loadModels('ProjectStatus', 'ProjectIssueStatus');
        $list_wid = $this->_widget_name();

        $value_init = array(
            'display' => 0,
            'row' => 1,
            'col' => 1,
            'sizex' => 1,
            'sizey' => 1,
			'show'  => 0,
        );
		$value_default = array(
			'display' => 1,
            'row' => 1,
            'col' => 1,
            'sizex' => 2,
            'sizey' => 1,
			'show'  => 1,
		);
        $task_status = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name')
        ));
        $task_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
        $this->set('task_status', $task_status);
		$issueStatus = $this->ProjectIssueStatus->find('list', array(
			'fields' => array('id', 'issue_status'),
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
		)));
        $this->set('issueStatus', $issueStatus);
        $widget_init = array();
        $i= 0;
        foreach ($list_wid as $key => $value) {
            $widget_init[$i] = $value_init;
			if($key == 'project_milestones' || $key == 'project_gantt' || $key == 'project_synthesis'){
				$value_default['row'] = $i + 1;
				$widget_init[$i] = $value_default;
			}else{
				$value_init['row'] = 4;
				$widget_init[$i] = $value_init;
			}
            $widget_init[$i]['name'] = $value;
            $widget_init[$i]['widget'] = $key;
            if($key == 'project_task' && !empty($task_status)){
				$widget_init[$i]['task_status'] = array();
				$default_status = 1;
                foreach ($task_status as $status_id => $name) {
                    $widget_init[$i]['task_status'][] = array(
						'status_id' => $status_id,
						'status_display' => 1,
						'default' => $default_status
					);
					$default_status = 0;
                }
            }
			if($key == 'project_risk' && !empty($issueStatus)){
				foreach( $issueStatus as $id => $name){
					$widget_init[$i]['options'][] = array(
						'id' => $id,
						'display' => 1
					);
				}
            }
			if($key == 'project_status'){
				$widget_init[$i]['options']= array(
					0 => array(
						'model' => 'Scope',
						'model_display' => 1
					),
					1 => array(
						'model' => 'Schedule',
						'model_display' => 1
					),
					2 => array(
						'model' => 'Budget',
						'model_display' => 1
					),
					3 => array(
						'model' => 'Resources',
						'model_display' => 1
					),
					4 => array(
						'model' => 'Technical',
						'model_display' => 1
					)
				);
            }
			if($key == 'project_synthesis'){
				$widget_init[$i]['options']= array(
					0 => array(
						'model' => 'ProjectRisk',
						'model_display' => 1
					),
					1 => array(
						'model' => 'ProjectAmr',
						'model_display' => 1
					),
					2 => array(
						'model' => 'ProjectIssue',
						'model_display' => 1
					),
					3 => array(
						'model' => 'Done',
						'model_display' => 1
					)
				);
            }
			if($key == 'project_synthesis_budget'){
				$widget_init[$i]['options']= array(
					0 => array(
						'model' => 'SynthesisBudget',
						'model_display' => 1
					),
					1 => array(
						'model' => 'BudgetInternal',
						'model_display' => 1
					),
					2 => array(
						'model' => 'BudgetExternal',
						'model_display' => 1
					)
				);
            }
			if($key == 'project_budget'){
				$widget_init[$i]['options']= array(
					0 => array(
						'model' => 'inv',
						'model_display' => 1
					),
					1 => array(
						'model' => 'fon',
						'model_display' => 1
					),
					2 => array(
						'model' => 'finaninv',
						'model_display' => 1
					),
					3 => array(
						'model' => 'finanfon',
						'model_display' => 1
					)
				);
            }
            $i++;
        }
        return $widget_init;
    }
	private function admin_default_setting($is_index = false){
        $this->loadModels('CompanyDefaultSetting', 'ProjectIndicatorSetting');
		$popup_setting = $indicatorSetting = array();
		$widget_name = $this->_widget_name();
		$popup_setting = $this->ProjectIndicatorSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'employee_id' => $this->employee_info['Employee']['id'],
			),
			'fields' => array('widget_setting') 
		));
		$popup_setting = !empty($popup_setting) ? @unserialize($popup_setting['ProjectIndicatorSetting']['widget_setting']) : array();
		if(!empty($popup_setting)){
			$popup_setting = Set::combine($popup_setting, '{n}.widget', '{n}');
		}
		$companyDefaultSetting = $this->CompanyDefaultSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'df_key' => 'dashboard_default_setting',
			),
			'fields' => array('df_value')
		));
		
		$companyDefaultSetting = !empty($companyDefaultSetting) ? @unserialize($companyDefaultSetting['CompanyDefaultSetting']['df_value']) : array();
	
		if(!empty($companyDefaultSetting)){
			// $companyDefaultSetting = Set::combine($companyDefaultSetting, '{n}.widget', '{n}');
			$dfSetting = array();
			foreach($companyDefaultSetting as $key => $value){
				$dfSetting[$value['widget']] = $value;
			}
			$companyDefaultSetting = $dfSetting;
		}
		
		// get data setting for popup
		$widget_init = $this->_list_widget_init();
		$widget_init = !empty( $widget_init)? Set::combine($widget_init, '{n}.widget', '{n}') : array();
		foreach($widget_init as $key => $value){
			if(!isset($companyDefaultSetting[$key])) $companyDefaultSetting[$key] =  $value;
			if($key =="project_task"){
				if(!isset($companyDefaultSetting[$key]['task_status'])) $companyDefaultSetting[$key]['task_status'] = $value['task_status'];
			}else{
				if(!isset($companyDefaultSetting[$key]['options']) && isset($value['options'])) $companyDefaultSetting[$key]['options'] =  $value['options'];
			}
		}
		// debug( $companyDefaultSetting['project_task']);
		if(!empty($companyDefaultSetting)){
			foreach($companyDefaultSetting as $key => $value){
				if($value['show'] == 1){
					if(!empty($popup_setting[$key])){
						$indicatorSetting[$key] = $popup_setting[$key];
					}else{
						$indicatorSetting[$key] = $value;
					}
				}
			}
		}
		// get data setting for admin
		if(!$is_index){
			$indicatorSetting = $companyDefaultSetting;
		}
		// debug( $companyDefaultSetting['project_task']);
		// debug( $indicatorSetting['project_task']);
        $layout_setting = $grid_setting = $order_setting = $model_display = array();
		$slider_active = 0;
		$widget_name = $this->_widget_name();
        if(!empty($indicatorSetting) && $is_index){
            foreach ($indicatorSetting as $key => $value) {
				$indicatorSetting[$key]['name'] = $widget_name[$key];
				if(!empty($widget_name[$key]) || (!empty($value['show']) && $value['show'] == 1)){
					if($value['widget'] == 'project_task'){
						$task_status = $value['task_status'];
						$slider_active = 0;
						foreach ($task_status as $k => $status) {
							if(!empty($status['default'])){
								$slider_active = $k;
								break;
							}
						}
					}
					if($value['widget'] == 'project_synthesis'){
						if(!empty($value['model'])){
							$syn_models = $value['model'];
							foreach ($syn_models as $model_id => $model_val) {
								$model_display[$model_id] = $model_val;
							}
						}
					}	
				}   
            }

        }
		$model_display = !empty($model_display) ? $model_display : array();
        $list_widgets = !empty($order_setting) ? $order_setting : array();
		/*
		Ticket #1098 «EVOLUTION MUTUALISABLE» «BUdget not displayed» «BACKLOG / EN COURS DE DEV NEXTVERSIONX» «Développeur» «z0 Gravity»
		* Do not display Budget for PM without see budget permission
		*/
		if( $this->employee_info['Role']['name'] == 'pm' && isset($this->employee_info['CompanyEmployeeReference']['see_budget']) && ($this->employee_info['CompanyEmployeeReference']['see_budget'] == '0')){
			foreach( array('project_budget', 'project_synthesis_budget') as $_widget){
				if( isset($indicatorSetting[$_widget])  ) unset($indicatorSetting[$_widget]);
				}
		}
        return array($indicatorSetting , $slider_active, $model_display);
    }
	
	private function _get_finance_value($project_id, $setting){
		/**
         * Lay start date va end date
         */
		
		$this->loadModels('ProjectFinancePlusDate', 'ProjectFinancePlus','ProjectFinancePlusDetail' );
        if (!$this->_checkRole(false, $project_id)) {
            $this->data = null;
        }
        $this->_checkWriteProfile('finance_plus');
        $employee_info = $this->employee_info;
		$types = array();
		foreach($setting as $type => $display){
			if($display == 1){
				array_push($types, $type);
			}
		}
        $invStart = $invEnd = $fonStart = $fonEnd = '';
        $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        $type_full = '';
		foreach($types as $type){
			$typeStart = $typeEnd = '';
			if(!empty($getSaveHistory)) {
				$typeStart = !empty($getSaveHistory['ProjectFinancePlusDate'][$type.'_start']) ? $getSaveHistory['ProjectFinancePlusDate'][$type.'_start'] : '';
				$typeEnd = !empty($getSaveHistory['ProjectFinancePlusDate'][$type.'_end']) ? $getSaveHistory['ProjectFinancePlusDate'][$type.'_end'] : '';
			}				
			$typeStart = !empty($this->params['url'][$type.'_start']) ? strtotime(@$this->params['url'][$type.'_start']) : (!empty($typeStart) ? $typeStart : time());
			$typeEnd = !empty($this->params['url'][$type.'_end']) ? strtotime(@$this->params['url'][$type.'_end']) : (!empty($typeEnd) ? $typeEnd : time());
			
			if(!empty($this->params['url'][$type.'_full'])) $type_full = '#'.$type.'-chard';
			
			if($type == 'inv'){
				$invStart = $typeStart;
				$invEnd = $typeEnd;
			}
			if($type == 'fon'){
				$fonStart = $typeStart;
				$fonEnd = $typeEnd;
			}
			if($type == 'finaninv'){
				$finanInvStart = $typeStart;
				$finanInvEnd = $typeEnd;
			}
			if($type == 'finanfon'){
				$finanFonStart = $typeStart;
				$finanFonEnd = $typeEnd;
			}
		}
        /**
         * Lay project
         */
        $projects = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('id', 'activity_id', 'project_name', 'company_id')
        ));
        $company_id = !empty($projects['Project']['company_id']) ? $projects['Project']['company_id'] : 0;
        $activity_id = !empty($projects['Project']['activity_id']) ? $projects['Project']['activity_id'] : 0;
        $projectName = !empty($projects['Project']['project_name']) ? $projects['Project']['project_name'] : '';
        /**
         * Lay cac finance+
         */
         $finances = $this->ProjectFinancePlus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				'activity_id' => $activity_id,
			),
            'fields' => array('id', 'name', 'type', 'finance_date'),
            // 'group' => array('type', 'id')
        ));
		$new = array();
		if( !empty($finances)){
			foreach( $finances as $key => $finance){
				$finance = $finance['ProjectFinancePlus'];
				$finance['finance_date'] = (!empty( $finance['finance_date']) && $finance['finance_date'] != '0000-00-00') ? date("d-m-Y", strtotime($finance['finance_date'])) : '';
				$new[$finance['type']][$finance['id']] = $finance;
			}
		}
		$finances = $new;
		unset( $new);
         /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array(
				'project_id' => $project_id,
				'activity_id' => $activity_id
			),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        $financeDetails = array();
        $yearOfFinances = array();
        $totals = array();
        if( !empty($_financeDetails) ){
            foreach($_financeDetails as $financeDetail){
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if(empty($totals[$dx['type']][$dx['model']])){
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        }
		foreach($types as $type ){
			if( !empty($yearOfFinances[$type]) ){
				$typeStartData = strtotime(date('d-m') . '-' . min($yearOfFinances[$type]));
				$typeEndData = strtotime(date('d-m') . '-' . max($yearOfFinances[$type]));
				if(empty($this->params['url'][$type.'_start'])){
					if($type == 'inv' && $typeStartData < $invStart){
						$invStart = $typeStartData;
					}
					if($type == 'fon' && $typeStartData < $fonStart){
						$fonStart = $typeStartData;
					}
					if($type == 'finaninv' && $typeStartData < $finanInvStart){
						$finanInvStart = $typeStartData;
					}
					if($type == 'finanfon' && $typeStartData < $finanFonStart){
						$finanFonStart = $typeStartData;
					}
				}
				if(empty($this->params['url'][$type.'_end'])){
					if($type == 'inv' && $typeEndData < $invEnd){
						$invEnd = $typeEndData;
					}
					if($type == 'fon' && $typeEndData < $fonEnd){
						$fonEnd = $typeEndData;
					}
					if($type == 'finaninv' && $typeEndData < $finanInvEnd){
						$finanInvEnd = $typeEndData;
					}
					if($type == 'finanfon' && $typeEndData < $finanFonEnd){
						$finanFonEnd = $typeEndData;
					}
				}
			}
			if($type == 'inv'){
				$totals[$type]['start'] = $invStart;
				$totals[$type]['end'] = $invEnd;
			}
			if($type == 'fon'){
				$totals[$type]['start'] = $fonStart;
				$totals[$type]['end'] = $fonEnd;
			}
			if($type == 'finaninv'){
				$totals[$type]['start'] = $finanInvStart;
				$totals[$type]['end'] = $finanInvEnd;
			}
			if($type == 'finanfon'){
				$totals[$type]['start'] = $finanFonStart;
				$totals[$type]['end'] = $finanFonEnd;
			}
			
		}
        $saveHistory = array(
            'inv_start' => !empty($invStart) ? $invStart : null,
            'inv_end' => !empty($invEnd) ? $invEnd : null,
            'fon_start' => !empty($fonStart) ? $fonStart : null,
            'fon_end' => !empty($fonEnd) ? $fonEnd : null,
            'finaninv_start' => !empty($finanInvStart) ? $finanInvStart : null,
            'finaninv_end' => !empty($finanInvEnd) ? $finanInvEnd : null,
            'finanfon_start' => !empty($finanFonStart) ? $finanFonStart : null,
            'finanfon_end' => !empty($finanFonEnd) ? $finanFonEnd : null,
            'company_id' => $company_id,
            'project_id' => $project_id
        );
		
        if( !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinancePlusDate']['id']) ){
            $this->ProjectFinancePlusDate->id = $getSaveHistory['ProjectFinancePlusDate']['id'];
        } else {
            $this->ProjectFinancePlusDate->create();
        }
        $this->ProjectFinancePlusDate->save($saveHistory);
        $this->loadModels('HistoryFilter');
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => $this->params['url']['url'],
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
		$this->set(compact('invStart', 'invEnd', 'fonStart', 'fonEnd', 'project_id', 'projects', 'finances', 'financeDetails', 'history', 'totals', 'types', 'type_full', 'saveHistory'));
	}
	private function _get_gantt_info($project_id){
		$this->loadModel('ProjectPhasePlan');
		
		$condPhase = array();
		$phase = isset($this->params['url']['phase']) ? $this->params['url']['phase'] : null;
        if($phase) {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id,'ProjectPhasePlan.id' => $phase);
        } else {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id);
        }
		
		$options = array(
            'limit' => 2000,
            'contain' => array(
                'ProjectPhase' => array('name', 'color')
            ),
            'conditions' => $condPhase,
            'order' => array('weight')
        );
        $phasePlans = $this->ProjectPhasePlan->find('all', $options);
		$this->set(compact('phasePlans'));
		// $this->set('phasePlans', $phasePlans);
	}
    private function progress_line($project_id){
        $this->loadModel('Project');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        list($_start, $_end) = $this->_parseParams();
        $staffings = array();
        if($filterId == 1){
            $staffings = $this->_staffingEmloyee($project_id);
        }
        $result = $manDays = array();
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $sumConsumed=$sumPlaned=0;
        if(!empty($staffings)){
            $datas = $this->_totalStaffing($staffings);
            $_setYear = $years = $estimatedNextMonths = array();
            $consumed = $validated = 0;

            $lastData=count($datas)-1;
            $i=0;
            foreach($datas as $key=>$val) {
                if($i==0) $startDay=$val['set_date'];
                elseif($i==$lastData) $endDay=$val['set_date'];
                $i++;
            }
            $minDate=mktime(0,0,0, date("m", $startDay)-1, date("d", $startDay), date("Y", $startDay));
            $maxDate= !empty($endDay) ? mktime(0,0,0, date("m", $endDay)+1, date("d", $endDay), date("Y", $endDay)) : 0;
            while($minDate<$maxDate) {
                $key=date("m", $minDate).'/'.date("y", $minDate);
                if(isset($datas[$key])) {
                    $_val=$datas[$key];
                    $setYear[]=date("Y", $minDate);

                    //progress
                    $sumConsumed+=$_val['consumed'];
                    $sumPlaned+=$_val['validated'];
                }
                if($minDate <= time()) {
                    $dataSets[]=array('date'=>$key,'validated'=>$sumPlaned,'consumed'=>$sumConsumed);
                } else {
                    $dataSets[]=array('date'=>$key,'validated'=>$sumPlaned);
                }
                $minDate = mktime(0, 0, 0, date("m", $minDate)+1, date("d", $minDate), date("Y", $minDate));
            }
        }
        $manDays=$sumPlaned+round($sumPlaned*0.1,2);
        $projectProgressLine = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('project_name')
            ));
        $_setYear = !empty($_setYear) ? array_unique($_setYear) : array();
        asort($_setYear);
        $setYear = !empty($_setYear) ? implode('-' ,$_setYear) : '';
        $display = isset($this->params['url']['ControlDisplay']) ? $this->params['url']['ControlDisplay'] : range(1, 3, 1);

        $countLine = 0;
        if(!empty($dataSets)) $countLine = count($dataSets);
        $this->set(compact('dataSets', 'filterId', 'project_id', 'manDays', 'projectProgressLine', 'setYear', 'display', 'countLine'));
    }

    private function getLogSystems($project_id){
        $this->loadModels('LogSystem');
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model_id'  => $project_id,

            ),
            'order'=>array('updated' => 'DESC'),
            'fields' => '*'
        ));
        $logGroups = $listEmployees = array();
		// debug( $logSystems);
        if(!empty($logSystems)){
            foreach($logSystems as $logSystem){
                $dx = $logSystem['LogSystem'];
                $logGroups[$dx['model']][] = $dx;
                $listEmployees[] = $dx['employee_id'];
            }
        }
        return array($logGroups, $listEmployees);
        
    }
    private function getNameEmployee($nameEmployee = null){
        if(!empty($nameEmployee)){
            $nameEmployee = trim($nameEmployee);
            $name = explode(" ", $nameEmployee);
            if(!empty($name)){
                $full_name = trim($name[0]) .' '.trim($name[1]);
                $name = substr( trim($name[0]),  0, 1) .''.substr( trim($name[1]),  0, 1) .'_'. $full_name;
            }
        }
        return $name;
    } 
    private function project_task($project_id, $data_status){
		$check_manual_consumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
		// debug($check_manual_consumed);exit;
        $this->loadModels('ProjectTask', 'ProjectStatus', 'Project', 'ProjectTaskEmployeeRefer');
        // get PM in project for assign widget 
        $project = $this->Project->find('first', array(
            // 'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id,
            ),
            'fields' => array('project_manager_id', 'activity_id'),
        ));
        $project_manager_id = $project['Project']['project_manager_id'];
		$_activityIdProject = $project['Project']['activity_id'];
        // Get tasks for widget task
        $project_task = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'task_status_id' => $data_status
            ),
            'fields' => array('id','project_id', 'task_status_id', 'task_title', 'updated', 'task_start_date', 'task_end_date', 'estimated', 'special', 'special_consumed', 'attachment', 'text_1', 'text_updater', 'text_time', 'is_nct', 'parent_id', 'overload','manual_overload', 'manual_consumed', 'project_planed_phase_id'),
        ));
		// debug($project_task);
		// exit;
		$listTaskIds = !empty( $project_task ) ? Set::classicExtract( $project_task, '{n}.ProjectTask.id') : array();
		$parent_task = !empty($project_task) ? Set::classicExtract($project_task, '{n}.ProjectTask.parent_id') : array();
		$this->loadModels('ProjectTaskAttachments', 'ProjectTaskAttachmentView', 'ProjectTaskTxt', 'ProjectTaskTxtRefer');
		// attachment
		$projectTaskAttachments = $this->ProjectTaskAttachments->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'task_id' => $listTaskIds,
				'attachment !=' => null
			),
			'fields' => array('task_id')
		));
		$projectTaskAttachmentRead = $this->ProjectTaskAttachmentView->find('list', array(
				'recursive' => -1,
                'conditions' => array(
					'task_id' => $listTaskIds,
					'employee_id' => $this->employee_info['Employee']['id']
				),
			'fields' => array('task_id', 'read_status')
		));
		
		//comment
		$projectTaskComments = $this->ProjectTaskTxt->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => $listTaskIds,
				'comment !=' => null
			),
			'fields' => array('id', 'project_task_id')
		));
		$projectTaskCommentRead = $this->ProjectTaskTxtRefer->find('list', array(
				'recursive' => -1,
                'conditions' => array(
					'task_id' => $listTaskIds,
					'employee_id' => $this->employee_info['Employee']['id']
				),
			'fields' => array('task_id', 'read_status')
		));
		
		/* END get attachment & comment */
        $list_tasks = array();
		$list_task_id = !empty($project_task) ? Set::classicExtract( $project_task,  '{n}.ProjectTask.id'): array();
		$task_assignedto = $this->ProjectTaskEmployeeRefer->find('all', array(
			'recursive' => -1,
            'conditions' => array(
                'project_task_id' => $list_task_id,
            ),
            'fields' => array('project_task_id','reference_id', 'is_profit_center'),
		));
		$tasks_assigned = array();
		foreach($task_assignedto as $key => $val){
			if( !isset($tasks_assigned[$val['ProjectTaskEmployeeRefer']['project_task_id']])){
				$tasks_assigned[$val['ProjectTaskEmployeeRefer']['project_task_id']] = array();
			}
			$tasks_assigned[$val['ProjectTaskEmployeeRefer']['project_task_id']][] = $val['ProjectTaskEmployeeRefer'];
			if( $val['ProjectTaskEmployeeRefer']['is_profit_center'] == 0){
				$list_assigned_employee[] = $val['ProjectTaskEmployeeRefer']['reference_id'];
			}
		}
		$listAssignTask = $this->_getTeamEmployees($project_id);
		
		$listAssignID = !empty($listAssignTask) ? Set::classicExtract( $listAssignTask,  '{n}.Employee.id'): array();
		$list_assigned_employee = array();
		if(!empty($listAssignID)){
			$list_assigned_employee = array_merge($listAssignID, $list_assigned_employee);
		}
		$list_assigned_employee = array_unique($list_assigned_employee);
		// $list_assigned_avatar = $this->requestAction('employees/get_list_avatar/', array('pass' => array($list_assigned_employee)));
		
		
		// attached file
        $listAvatar = $this->requestAction('kanban/listEmployeeAssigned/' . $project_id. '/true');
        $attachedFile = $this->requestAction('kanban/get_attached_file/' . $project_id);

		if(!empty($_activityIdProject)){
            $this->loadModel('ActivityRequest');
            $this->loadModel('ActivityTask');
			
			/*
			SELECT project_tasks.id, activity_requests.task_id, sum(`value`) FROM `activity_requests` 
				Join `activity_tasks` ON activity_tasks.id = activity_requests.task_id
				join `project_tasks` ON activity_tasks.project_task_id = project_tasks.id
				WHERE `activity_tasks`.activity_id = 3204 and activity_requests.`status` = 2
				GROUP BY activity_requests.task_id;
			*/
            $listTaskConsumed = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array(
                    'ActivityTask.project_task_id',
                    'SUM(ActivityRequest.value) as consumed',
                ),
				'joins' => array(
					array(
						'table' => 'activity_tasks',
						'alias' => 'ActivityTask',
						'conditions' => array(
							'ActivityTask.id = ActivityRequest.task_id',
						)
					),
				),
                'conditions' => array(
                    // 'activity_id' => $_activityIdProject,
                    'company_id' => $this->employee_info['Company']['id'],
					'ActivityTask.project_task_id' => $list_task_id,
                    'ActivityRequest.status' => 2,
                    // 'NOT' => array('value' => 0)
                ),
				'group' => array('ActivityTask.project_task_id'),
            ));
			$listTaskConsumed = !empty( $listTaskConsumed ) ? Set::combine( $listTaskConsumed, '{n}.ActivityTask.project_task_id', '{n}.0.consumed') : array();
		}

		$task_kanban = $data_tasks = array();
        if(!empty($project_task)){
            foreach ($project_task as $key => $task) {
				$taskId = $task['ProjectTask']['id'];
				if(!in_array($taskId,$parent_task)){
					// attachment
					$task['ProjectTask']['attachment_count'] = 0;
					$task['ProjectTask']['attach_read_status'] = 0;
					if( !empty($task['ProjectTask']['attachment'])) {
						$task['ProjectTask']['attachment_count'] += 1;
						$task['ProjectTask']['attach_read_status'] = 1;
					}
					foreach( $projectTaskAttachments as $_id => $_task_id){
						if( $_task_id == $taskId){
							$task['ProjectTask']['attachment_count'] += 1;
							$task['ProjectTask']['attach_read_status'] = 0;
						}
					}
					if ( array_key_exists($taskId, $projectTaskAttachmentRead )) $task['ProjectTask']['attach_read_status'] = intval($projectTaskAttachmentRead[$taskId]);
					
					// commment
					$task['ProjectTask']['comment_count'] = 0;
					$task['ProjectTask']['read_status'] = 0;
					foreach( $projectTaskComments as $_id => $_task_id){
						if( $_task_id == $taskId){
							$task['ProjectTask']['comment_count'] += 1;
							$task['ProjectTask']['read_status'] = 0;
						}
					}
					if ( array_key_exists($taskId, $projectTaskCommentRead )) $task['ProjectTask']['read_status'] = intval($projectTaskCommentRead[$taskId]);
					/* END update attachment and comment */
					
					$task['ProjectTask']['task_start_date'] = strtotime($task['ProjectTask']['task_start_date']);
					$task['ProjectTask']['consumed'] = isset($listTaskConsumed[$task['ProjectTask']['id']]) ? round($listTaskConsumed[$task['ProjectTask']['id']], 2) : 0;
					$task['ProjectTask']['assigned'] = isset($tasks_assigned[$task['ProjectTask']['id']]) ? $tasks_assigned[$task['ProjectTask']['id']] : array();
					$task['ProjectTask']['task_end_date'] = strtotime($task['ProjectTask']['task_end_date']);
					$task['ProjectTask']['late'] = 0;
					if(strtotime(date('d-m-Y')) > $task['ProjectTask']['task_end_date']){
						$task['ProjectTask']['late'] = 1;
					}
					$list_tasks[$task['ProjectTask']['task_status_id']][] = $task['ProjectTask'];
					if($task['ProjectTask']['task_end_date']){
						$task['ProjectTask']['task_end_date_format'] = date('d-F-Y', $task['ProjectTask']['task_end_date']);
						$task['ProjectTask']['task_end_date'] = date('Y-m-d', $task['ProjectTask']['task_end_date']);
					}
					$data_tasks[$task['ProjectTask']['id']] = $task['ProjectTask'];
					$task_kanban[] = $task['ProjectTask'];
				}
            }
        }
		// order project task status like setting
		$list_tasks_order = array();
		if(!empty($list_tasks)){
			foreach ($data_status as $key => $value) {
				// if(!empty($list_tasks[$key])){
					$list_tasks_order[$key] = !empty($list_tasks[$key]) ? $list_tasks[$key] : array();
				// }
			}
			$list_tasks = $list_tasks_order;
		}
        // Check avatar
        $this->phase_vision($project_id, $new_gantt = null);
        $employee_id = $this->employee_info['Employee']['id'];
        $task_status = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name', 'status')
        ));
        $list_project_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
        $list_org_project_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
		
		
		// Ticket #393. Check enable_newdesign de hien thi button (+) Add new task. Update by QuanNV 06/06/2019.
		$this->loadModel('Menu');
		$on_newdesign_projecttask = $this->Menu->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project',
				'controllers' => 'project_tasks'
			),
			'fields' => array('enable_newdesign')
		));
        // kiem tra role
        $myRole = $this->employee_info['Role']['name'];
        $emp_id_login = $this->employee_info['Employee']['id'];
		// debug($list_tasks);
		// exit;
		$on_newdesign_projecttask = !empty($on_newdesign_projecttask) ? $on_newdesign_projecttask['Menu']['enable_newdesign'] : 0;
        $this->set(compact('list_tasks', 'project_manager_id', 'list_project_status', 'list_org_project_status' /* , 'list_assigned_avatar' */, 'listAvatar', 'task_kanban', 'data_tasks', 'listAssignTask', 'on_newdesign_projecttask', 'attachedFile', 'check_manual_consumed', 'myRole', 'emp_id_login'));
    }
    /**
     * phase_vision
     * Phase project list
     *
     * @return void
     * @access public
     */
    private function phase_vision($project_id = null, $new_gantt = null) {
        $this->loadModel('ProjectPhasePlan');

        $phase = isset($this->params['url']['phase']) ? $this->params['url']['phase'] : null;
        if($phase) {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id,'ProjectPhasePlan.id' => $phase);
            $condPhaseProject = array('Project.id' => $project_id, 'Project.project_phase_id' => $phase);
        } else {
            $condPhase = array('ProjectPhasePlan.project_id' => $project_id);
            $condPhaseProject = array('Project.id' => $project_id);
        }
        $this->_checkRole(false, $project_id);
        $projectPlanName = $this->viewVars['projectName'];
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $this->set('projectPlanName', $this->ProjectPhasePlan->Project->find("first", array(
                    'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC', 'conditions' => array('ProjectMilestone.milestone_date !=' => '0000-00-00'))),
                    "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'), 'conditions' => $condPhaseProject
                    )));
        $options = array(
            'limit' => 2000,
            'contain' => array(
                'ProjectPhase' => array('name', 'color')
            ),
            'conditions' => $condPhase,
            'order' => array('weight')
        );
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $phasePlans = $this->ProjectPhasePlan->find('all', $options);
        $phaseIds = Set::classicExtract($phasePlans, '{n}.ProjectPhasePlan.id');
        $parts = $this->ProjectPhasePlan->ProjectPart->find('list', array('order' => array('weight' => 'ASC')));
        $getDatas = $this->_taskCaculates($project_id, $projectPlanName['Project']['company_id'], $phasePlans);
        $taskCompleted = $getDatas['part'];
        $phaseCompleted = $getDatas['phase'];
        $projectTasks = $getDatas['task'];
        $onClickPhaseIds = array();
        foreach($projectTasks as $projectTask){
            foreach($phaseIds as $phaseId){
                if($phaseId == $projectTask['project_part_id']){
                    $onClickPhaseIds['wd-'.$phaseId][] = $projectTask['id'];
                }
                if(!empty($projectTask['children'])){
                    foreach($projectTask['children'] as $vl){
                        if($projectTask['id'] == $vl['project_part_id']){
                            $onClickPhaseIds['wd-'.$projectTask['id']][] = $vl['id'];
                        }
                    }
                }
            }
        }
        if(!empty($onClickPhaseIds)){
            foreach($onClickPhaseIds as $key => $onClickPhaseId){
                $onClickPhaseIds[$key] = array_unique($onClickPhaseId);
            }
        }
        $callProjects = isset($this->params['url']['call']) ? (int) $this->params['url']['call'] : 0;
        // save History.
        $this->loadModel('HistoryFilter');
        $default = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'path' => 'project_tasks_preview',
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('id', 'params')
        ));
        if(!empty($this->params['url']['type'])){
            $type = $this->params['url']['type'];
            if(!empty($default)){
                $this->HistoryFilter->id = $default['HistoryFilter']['id'];
            } else {
                $this->HistoryFilter->create();
            }
            $this->HistoryFilter->save(array('params' => $type, 'path' => 'project_tasks_preview', 'employee_id' => $this->employee_info['Employee']['id']));
        } else {
            $type = !empty($default) ? $default['HistoryFilter']['params'] : 'year';
        }
        $this->set(compact('phasePlans', 'display', 'project_id', 'parts', 'phaseCompleted', 'projectTasks', 'taskCompleted', 'onClickPhaseIds', 'callProjects', 'type'));
    }
    private function _taskCaculates($project_id = null, $company_id = null, $phasePlans = array()){
        $manual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ActivityTask');
        $this->loadModels('ActivityRequest', 'ProjectStatus');

        $projectTasks = $this->ProjectTask->find('all', array(
            //'recursive' => -1,
            'conditions' => array('ProjectTask.project_id' => $project_id),
            'fields' => array('id', 'task_title', 'parent_id', 'project_planed_phase_id', 'task_start_date', 'task_end_date', 'estimated', 'predecessor', 'special', 'special_consumed', 'manual_consumed', 'overload', 'manual_overload','initial_task_end_date', 'initial_task_start_date', 'task_status_id'),
            'order' => array('ProjectTask.weight' => 'ASC')
        ));
        $taskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');

        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $taskIds),
            'fields' => array('project_task_id', 'id')
        ));

        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $activityTasks,
                'company_id' => $company_id,
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $activityRequests = Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0.value');
        $consumeds = array();
        foreach($activityTasks as $id => $activityTask){
            if(in_array($activityTask, array_keys($activityRequests))){
                $consumeds[$id] = $activityRequests[$activityTask];
            } else {
                $consumeds[$id] = 0;
            }
        }
		$task_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'status'),
        ));
		
        foreach($projectTasks as $key => $projectTask){
            $dx = $projectTask['ProjectTask'];
            //manual consumed on 2015-08-05
			// #2011 Case consumed = 0, task close then completed = 100%
            if( $manual ){
                $projectTasks[$key]['ProjectTask']['consumed'] = $projectTasks[$key]['ProjectTask']['manual_consumed'];
                $projectTasks[$key]['ProjectTask']['overload'] = $overload = $projectTasks[$key]['ProjectTask']['manual_overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
					if($projectTasks[$key]['ProjectTask']['consumed'] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($projectTasks[$key]['ProjectTask']['consumed'] * 100 / ($overload + $dx['estimated']), 2);
					}
                }
            }
            else if(!empty($dx['special'])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
					if($dx['special_consumed'] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($dx['special_consumed'] * 100 / ($overload + $dx['estimated']), 2);
					}
                }
                $projectTasks[$key]['ProjectTask']['consumed'] = $dx['special_consumed'];
            } else if(!empty($consumeds[$dx['id']])){
                $overload = $projectTasks[$key]['ProjectTask']['overload'];
                if($dx['estimated'] == 0){
                    $projectTasks[$key]['ProjectTask']['completed'] = 0;
                } else {
					if($consumeds[$dx['id']] == 0 && !empty($task_status[$projectTasks[$key]['ProjectTask']['task_status_id']]) && $task_status[$projectTasks[$key]['ProjectTask']['task_status_id']] == 'CL'){
						$projectTasks[$key]['ProjectTask']['completed'] = 100;
					}else{
						$projectTasks[$key]['ProjectTask']['completed'] = round($consumeds[$dx['id']] * 100 / ($overload + $dx['estimated']), 2);
					}
                }
                $projectTasks[$key]['ProjectTask']['consumed'] = $consumeds[$dx['id']];
            }
            if( !$dx['parent_id'] )$projectTasks[$key]['ProjectTask']['parent'] = true;
        }
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        if(!empty($parentIds)){
            foreach($parentIds as $key => $parentId){
                if($parentId == 0 || $parentId == ''){
                    unset($parentIds[$key]);
                }
            }
            $dataParents = array();
            if(!empty($parentIds)){
                foreach($parentIds as $parentId){
                    foreach($projectTasks as $projectTask){
                        if($parentId == $projectTask['ProjectTask']['parent_id']){
                            $dataParents[$parentId]['estimated'][] = $projectTask['ProjectTask']['estimated'];
                            $dataParents[$parentId]['consumed'][] = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
                            $dataParents[$parentId]['overload'][] = isset($projectTask['ProjectTask']['overload']) ? $projectTask['ProjectTask']['overload'] : 0;
                        }
                    }
                }
            }
            if(!empty($dataParents)){
                foreach($dataParents as $id => $dataParent){
                    $_estimated = array_sum($dataParent['estimated']);
                    $_consumed = array_sum($dataParent['consumed']);
                    $dataParents[$id]['estimated'] = $_estimated;
                    $dataParents[$id]['consumed'] = $_consumed;
                    $dataParents[$id]['overload'] = isset($dataParent['overload']) ? array_sum($dataParent['overload']) : 0;
                    if($_estimated == 0){
                        $dataParents[$id]['completed'] = 0;
                    } else{
                        $dataParents[$id]['completed'] = round($_consumed * 100 / ($dataParents[$id]['overload'] + $_estimated), 2);
                    }
                }
            }
            if(!empty($dataParents)){
                foreach($projectTasks as $key => $projectTask){
                    foreach($dataParents as $id => $dataParent){
                        if($projectTask['ProjectTask']['id'] == $id){
                            $projectTasks[$key]['ProjectTask']['estimated'] = $dataParent['estimated'];
                            $projectTasks[$key]['ProjectTask']['consumed'] = $dataParent['consumed'];
                            $projectTasks[$key]['ProjectTask']['completed'] = $dataParent['completed'];
                            $projectTasks[$key]['ProjectTask']['overload'] = $dataParent['overload'];
                            //$projectTasks[$key]['ProjectTask']['parent'] = true;
                        }
                    }
                }
            }
        }
        //xu ly phan assign employee
        $this->loadModel('Employee');
        $this->loadModel('ProfitCenter');
        $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname')
            ));
        $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        foreach($projectTasks as $key => $projectTask){
            if(!empty($projectTask['ProjectTaskEmployeeRefer'])){
                foreach($projectTask['ProjectTaskEmployeeRefer'] as $k => $vl){
                    if($vl['is_profit_center'] == 0){
                        $projectTasks[$key]['ProjectTask']['assign'][] = !empty($employees[$vl['reference_id']]) ? $employees[$vl['reference_id']] : '';
                    } else {
                        $projectTasks[$key]['ProjectTask']['assign'][] = !empty($profitCenters[$vl['reference_id']]) ? 'PC / ' . $profitCenters[$vl['reference_id']] : '';
                    }
                }
                unset($projectTasks[$key]['ProjectTaskEmployeeRefer']);
            } else {
                unset($projectTasks[$key]['ProjectTaskEmployeeRefer']);
            }
        }
        // the data of task
        $datas = array();
        foreach($projectTasks as $key => $projectTask){
            $datas[$key]['id'] = 'task-' . $projectTask['ProjectTask']['id'];
            if($projectTask['ProjectTask']['parent_id'] != 0){
                $datas[$key]['project_part_id'] = 'task-' . $projectTask['ProjectTask']['parent_id'];
            } else {
                $datas[$key]['project_part_id'] = $projectTask['ProjectTask']['project_planed_phase_id'];
            }
            $datas[$key]['name'] = $projectTask['ProjectTask']['task_title'];
            $datas[$key]['predecessor'] = !empty($projectTask['ProjectTask']['predecessor']) ? 'task-' . $projectTask['ProjectTask']['predecessor'] : '';
            $datas[$key]['completed'] = isset($projectTask['ProjectTask']['completed']) ? $projectTask['ProjectTask']['completed'] : 0;
            $datas[$key]['color'] = '';
            $datas[$key]['rstart'] = isset($projectTask['ProjectTask']['task_start_date']) ? strtotime($projectTask['ProjectTask']['task_start_date']) : 0;
            $datas[$key]['rend'] = isset($projectTask['ProjectTask']['task_end_date']) ? strtotime($projectTask['ProjectTask']['task_end_date']) : 0;
            $datas[$key]['start'] = isset($projectTask['ProjectTask']['initial_task_start_date']) ? strtotime($projectTask['ProjectTask']['initial_task_start_date']) : 0;
            $datas[$key]['end'] = isset($projectTask['ProjectTask']['initial_task_end_date']) ? strtotime($projectTask['ProjectTask']['initial_task_end_date']) : 0;
            $datas[$key]['assign'] = !empty($projectTask['ProjectTask']['assign']) ? implode(', ', $projectTask['ProjectTask']['assign']) : '';
        }
        $parentTaskIds = array_unique(Set::classicExtract($datas, '{n}.project_part_id'));
        foreach($parentTaskIds as $key => $parentTaskId){
            if(is_numeric($parentTaskId)){
                unset($parentTaskIds[$key]);
            }
        }
        $rChilds = array();
        foreach($datas as $key => $data){
            foreach($parentTaskIds as $parentTaskId){
                if($data['project_part_id'] == $parentTaskId){
                    $rChilds[$parentTaskId][] = $data;
                    unset($datas[$key]);
                }
            }
        }
        if(!empty($rChilds)){
            foreach($datas as $key => $data){
                foreach($rChilds as $id => $rChild){
                    if($data['id'] == $id){
                        foreach($rChild as $vl){
                            $vl['start'] = !empty($vl['start']) && $vl['start']>0 ? $vl['start'] : $data['start'];
                            $datas[$key]['children'][] = $vl;
                        }
                    }
                }
            }
        }
        // caculate completed of phase
        //$phases = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.project_planed_phase_id'));
        $this->loadModel('ProjectPhasePlan');
        $phases = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'id')
        ));
        $_phases = array();
        foreach($phases as $phase){
            foreach($projectTasks as $projectTask){
                if($phase == $projectTask['ProjectTask']['project_planed_phase_id'] && isset($projectTask['ProjectTask']['parent']) ){
                    $_phases[$phase]['estimated'][] = $projectTask['ProjectTask']['estimated'];
                    $_phases[$phase]['consumed'][] = isset($projectTask['ProjectTask']['consumed']) ? $projectTask['ProjectTask']['consumed'] : 0;
                    $_phases[$phase]['overload'][] = isset($projectTask['ProjectTask']['overload']) ? $projectTask['ProjectTask']['overload'] : 0;
                }
            }
            $_phases[$phase]['completed'] = 0;
        }
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ?  true : false;
        $phaseProgress = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.progress') : array();
        $partPhases = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $groupPhaseFollowPart = !empty($phasePlans) ? Set::combine($phasePlans, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $progressPart = array();
        foreach($_phases as $id => $_phase){
            $_estimated = !empty($_phase['estimated']) ? array_sum($_phase['estimated']) : 0;
            $_consumed = !empty($_phase['consumed']) ? array_sum($_phase['consumed']) : 0;
            $overload = !empty($_phase['overload']) ? array_sum($_phase['overload']) : 0;
            $_phases[$id]['estimated'] = $_estimated;
            $_phases[$id]['consumed'] = $_consumed;
            $_phases[$id]['overload'] = $overload;
            if($_estimated == 0){
                $_phases[$id]['completed'] = 0;
            } else{
                $_phases[$id]['completed'] = round($_consumed * 100 / ($overload + $_estimated), 2);
            }
            if($manuallyAchievement){
                $_phases[$id]['completed'] = 0;
                if(!empty($phaseProgress) && !empty($phaseProgress[$id])){
                    $_phases[$id]['completed'] = $phaseProgress[$id];
                }
                $partId = !empty($partPhases[$id]) ? $partPhases[$id] : 0;
                if(!isset($progressPart[$partId])){
                    $progressPart[$partId] = 0;
                }
                $progressPart[$partId] += $_phases[$id]['completed'];
            }
        }
        // caculate completed of part
        $phasePlans = $this->ProjectPhasePlan->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'NOT' => array('project_part_id' => null)
            ),
            'fields' => array('id', 'project_part_id')
        ));
        $parts = array();
        if(!empty($phasePlans)){
            foreach($phasePlans as $phaseId => $phasePlan){
                foreach($_phases as $id => $_phase){
                    if($phaseId == $id){
                        $parts[$phasePlan]['estimated'][] = $_phase['estimated'];
                        $parts[$phasePlan]['consumed'][] = $_phase['consumed'];
                        $parts[$phasePlan]['overload'][] = $_phase['overload'];
                    }
                }
            }
        }
        foreach($parts as $id => $part){
            $_estimated = array_sum($part['estimated']);
            $_consumed = array_sum($part['consumed']);
            $_overload = array_sum($part['overload']);
            $parts[$id]['estimated'] = $_estimated;
            $parts[$id]['consumed'] = $_consumed;
            $parts[$id]['overload'] = $_overload;
            if($_estimated == 0){
                $parts[$id]['completed'] = 0;
            } else{
                $parts[$id]['completed'] = round($_consumed * 100 / ($_overload + $_estimated), 2);
            }
            if($manuallyAchievement){
                $parts[$id]['completed'] = 0;
                if(!empty($progressPart) && !empty($progressPart[$id])){
                    $totalPhaseDependPart = !empty($groupPhaseFollowPart) && !empty($groupPhaseFollowPart[$id]) ? count($groupPhaseFollowPart[$id]) : 0;
                    $parts[$id]['completed'] = ($totalPhaseDependPart == 0) ? 0 : round($progressPart[$id]/$totalPhaseDependPart, 2);
                }
            }
        }
        $results['task'] = $datas;
        $results['phase'] = $_phases;
        $results['part'] = $parts;

        return $results;
    }
    private function list_projectStatus($project_id = null, $name = false){
        $this->loadModel('ProjectStatus');
        $company_id = $this->ProjectTask->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $company_id['Project']['company_id'];
        $list_projectStatus = $this->ProjectStatus->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        return $list_projectStatus;
    }
    public function risk_dashboard($project_id){
        $this->loadModels('Project', 'ProjectRiskSeverity', 'ProjectRiskOccurrence', 'ProjectRisk');
		$count = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => 'id'
		));
		if( empty( $count)){
			die(json_encode(array())); exit;
		}
        $projectRiskSeverities = $this->ProjectRisk->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'joins' => array(
                array(
                    'table' => 'project_risk_severities',
                    'alias' => 'ProjectRiskSeverity',
                    'conditions' => array('ProjectRisk.project_risk_severity_id = ProjectRiskSeverity.id')
                )
            ),
            'fields' => array('ProjectRisk.id', 'ProjectRisk.project_risk', 'ProjectRisk.project_risk_severity_id', 'ProjectRiskSeverity.value_risk_severitie')
        ));        
        $typeRiskSeverities = !empty($projectRiskSeverities) ? Set::combine($projectRiskSeverities, '{n}.ProjectRisk.id', '{n}.ProjectRiskSeverity.value_risk_severitie') : array();

        $projectRiskOccurrence = $this->ProjectRisk->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'joins' => array(
                array(
                    'table' => 'project_risk_occurrences',
                    'alias' => 'ProjectRiskOccurrence',
                    'conditions' => array('ProjectRisk.project_risk_occurrence_id = ProjectRiskOccurrence.id')
                )
            ),
            'fields' => array('ProjectRisk.id', 'ProjectRiskOccurrence.value_risk_occurrence')
        ));
        $typeRiskOccurrence = !empty($projectRiskOccurrence) ? Set::combine($projectRiskOccurrence, '{n}.ProjectRisk.id', '{n}.ProjectRiskOccurrence.value_risk_occurrence') : array();
        
        $projectDashBoard = array();
        foreach ($projectRiskSeverities as $key => $projectRiskSeverity) {
            $projectDashBoard[$key] = $projectRiskSeverity['ProjectRisk'];
            $projectDashBoard[$key]['dashboard_type'] = $typeRiskSeverities[$projectRiskSeverity['ProjectRisk']['id']].'_'.$typeRiskOccurrence[$projectRiskSeverity['ProjectRisk']['id']];
        }
        // $this->set(compact('projectDashBoard'));
        die(json_encode($projectDashBoard));
        exit;
    }
    private function z0g_messages($project_id){
        // Zog Messages
        $this->loadModels('ZogMsg', 'Employee');
        $zogMsgs = $this->ZogMsg->find('all', array(
            'recursive' => -1,
            'limit' => 5,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array('id', 'employee_id', 'content', 'created'),
            'order' => array('created' => 'DESC')
        ));
        $listIdEm = !empty($zogMsgs) ? array_unique(Set::classicExtract($zogMsgs, '{n}.ZogMsg.employee_id')) : array();
        $eZogMsgs = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'Employee.id' => $listIdEm
            ),
            'fields' => array('id', 'avatar', 'first_name', 'last_name')
        ));
        
        $zogMsgs = !empty($zogMsgs) ? Set::classicExtract($zogMsgs, '{n}.ZogMsg') : array();
        $eZogMsgs = !empty($eZogMsgs) ? Set::combine($eZogMsgs, '{n}.Employee.id', '{n}.Employee') : array();
        $this->set('project_id', $project_id);
        $this->set('zogMsgs', $zogMsgs);
        $this->set('eZogMsgs', $eZogMsgs);
    }

    private function _getEmployeeAssign($project_id ) {
        $this->loadModels('ProjectTask','ProjectTaskEmployeeRefer', 'ProjectEmployeeManager', 'Employee', 'ProjectTeam');
        $list_tasks_ids = $resource_ids = $project_manager_ids = $list_assign_to = array();
        $list_tasks_ids = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id'),
        ));
        // get resource assign to 
        if(!empty($list_tasks_ids)){
            $resource_ids = $this->ProjectTaskEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $list_tasks_ids,
                ),
                'fields' => array('reference_id', 'is_profit_center'),
            ));
        }
        $project_manager_ids  = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'type' => 'PM'
            ),
            'fields' => array('project_manager_id', 'is_profit_center'),
        ));

        $list_assign_to = $resource_ids + $project_manager_ids;
        $list_assigns = $employees_name = $profitCenters = array();
        if(!empty($list_assign_to)){
            $employee_ids = $pc_ids = array();
            foreach ($list_assign_to as $id => $is_profit_center) {
                if($is_profit_center == 1){
                    $pc_ids[$id] = $id;
                }else{
                    $employee_ids[$id] = $id;
                }
            }
            if($employee_ids){
                $employees_name = $this->Employee->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $employee_ids,
                        'NOT' => array('is_sas' => 1),
                    ),
                    'fields' => array('id', 'fullname')
                ));
                $employees_name = !empty($employees_name) ? Set::combine($employees_name, '{n}.Employee.id', '{n}.Employee.fullname') : array();
            }
            if($pc_ids){
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'ProfitCenter.id' => $pc_ids
                    ),
                    'fields' => array('id', 'name')
                ));
                $profitCenters = !empty($profitCenters) ? Set::combine($profitCenters, '{n}.ProfitCenter.id', '{n}.ProfitCenter.name') : array();
            }
            foreach ($list_assign_to as $id => $is_profit_center) {
                if($is_profit_center){
                    $list_assigns[$id]['id'] = $id;
                    $list_assigns[$id]['is_profit_center'] = $is_profit_center;
                    if( !empty($profitCenters[$id])) $list_assigns[$id]['fullname'] = $profitCenters[$id];
                }else{
                    $list_assigns[$id]['id'] = $id;
                    $list_assigns[$id]['is_profit_center'] = $is_profit_center;
                    if($employees_name[$id]) $list_assigns[$id]['fullname'] = $employees_name[$id];
                }
            }
        }
        return $list_assigns;
    }
	/**
     * Function: Get Employees with in current Project
     * @var     : int $project_id
     * @return  : array of Employees
     * @author : BANGVN
     */
    private function _getTeamEmployees($project_id ) {
        $this->loadModel('ProjectTeam');
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTeam->Behaviors->attach('Containable');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Menu');
        $company_id = $this->employee_info['Company']['id'];
        // lay menu project team. Neu not display thi cho hien thi all employee.
        $checkDisplayProjectTeam = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'controllers' => 'project_teams',
                'functions' => 'index',
                'widget_id' => 'team',
                'model' => 'project',
                // 'display' => 1
            ),
            'order' => array('id DESC')
        ));
        if(!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1){
            // List all employees followed in this project
            $teams = $this->ProjectTeam->find('all', array(
                'fields' => array('id', 'profit_center_id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
                'conditions' => array('project_id' => $project_id)
            ));
            $project = $this->_getProject($project_id);
            $employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();
            if(!empty($employeeIds)){
                foreach($employeeIds as $employeeId){
                    foreach($employeeId as $v){
                        $_employeeId[] = $v;
                    }
                }
                $employeeIds = array_unique($_employeeId);
            }
            $employees = $this->ProjectTask->Employee->find('list', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_merge($employeeIds, array($project['Project']['project_manager_id'])),
                    'NOT' => array('is_sas' => 1),
                ),
                'fields' => array('id', 'fullname')
            ));
            $rDatas = array();
            if(!empty($employees)){
                $i = 0;
                foreach($employees as $id => $name){
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($name) ? $name : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $i++;
                }
            }
            $getEmploy = !empty($rDatas) ? $rDatas : array();
            $profitCenterIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.profit_center_id')) : array();
            if(!empty($profitCenterIds)){
                foreach($profitCenterIds as $profitCenterId){
                    foreach($profitCenterId as $v){
                        $_profitCenterId[] = $v;
                    }
                }
                $profitCenterIds = array_unique($_profitCenterId);
            }
            if (!empty($profitCenterId)) {
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $projectName['Project']['company_id'],
                        'ProfitCenter.id' => $profitCenterIds
                    )
                ));
                $employ = array();
                foreach ($profitCenters as $ks => $profitCenter) {
                    $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                    $employ[$ks]['Employee']['is_profit_center'] = 1;
                    //$employ[$ks]['Employee']['profit_center_id'] = -1;
                    $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                }
                if(!empty($employ)){
                    $employees = array_merge($getEmploy , $employ);
                } else {
                    $employees = $getEmploy;
                }
            } else {
                $employees = $getEmploy;
            }
        } else {
            $curentDate = date('Y-m-d');
			$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
            $employees = $this->ProjectTask->Employee->find('all', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    'company_id' => $company_id
                ),
                 'fields' => array('id', 'fullname','actif', 'available')
            ));
			
            $rDatas = array();
            if (!empty($employees)) {
                $i = 0;
                foreach($employees as $emp){
					$id = $emp['Employee']['id'];
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($emp['Employee']['fullname']) ? $emp['Employee']['fullname'] : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $rDatas[$i]['Employee']['actif'] = intval($emp['Employee']['actif']) ? intval($emp['Employee']['available']) : 0;
                    $i++;
                }
            }
            $employees = $rDatas;
            // lay team
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                'recursive' => -1,
                'order' => array('ProfitCenter.name' => 'asc'),
                'conditions' => array(
                    'company_id' => $projectName['Project']['company_id']
                )
            ));
            $employ = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $employ[$ks]['Employee']['is_profit_center'] = 1;
                //$employ[$ks]['Employee']['profit_center_id'] = -1;
                $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
            }
            if( !empty($employ) ){
                $employees = array_merge($employees , $employ);
            }
        }
        return $employees;

    }

    private function _getProject($project_id) {
        if (!isset($this->_project)) {
            $project = $this->ProjectTask->Project->find("first",
                array(
                    'conditions' => array('Project.id' => $project_id)
                )
            );
            $this->_project = $project;
        }

        return $this->_project;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function checkAvatar() {
        $this->loadModel('Employee');
        $checkAvata = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'avatar_resize')
        ));

        $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
        return $checkAvata;
    }
	/**
	* Get image path
	*/
	protected function _getPathGlobalView($project_id) {
		// $this->loadModel('ProjectGlobalView');
        $company = $this->ProjectGlobalView->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectGlobalView->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
	
    /**
     *  Save/edit Log System
     */
    public function update_data_log(){
        $this->layout = false;
        $result = '';
        $data = $_POST;
        $data['name'] = $this->employee_info['Employee']['fullname'];
        $data['company_id'] = $this->employee_info['Company']['id'];
        $data['created'] = time();
        $data['employee_id'] = $this->employee_info['Employee']['id'];
        $data['update_by_employee'] = $this->employee_info['Employee']['fullname'];
        $data['updated'] = time();
        $log_system = array();
        $listEmployees = array($employee_info['Employee']['id']);
        if($data){
            if(!empty($data['id']) && $data['id'] == -1){
                unset($data['id']);
            }
			// elseif( !empty($data['id'])){
				// $this->loadModels('LogSystem');
				// $logSystems = $this->LogSystem->find('all', array(
					// 'recursive' => -1,
					// 'conditions' => array(
						// 'company_id' => $this->employee_info['Company']['id'],
						// 'model_id'  => $project_id,

					// ),
					// 'order'=>array('updated' => 'DESC'),
					// 'fields' => '*'
				// ));
			// }
            $result = $this->LogSystem->saveLogSystem($data);
            $result['LogSystem']['updated'] = date('d M Y', $result['LogSystem']['updated']);
            if($result){
				list($log_system, $_listEmployee) = $this->getLogSystems($data['model_id']);
				$listEmployees = array_merge( $listEmployees , $_listEmployee);
                $log_system = $log_system[$data['model']];
                foreach ($log_system as $key => $value) {
                    $log_system[$key]['updated'] = date('d M Y', $value['updated']);
                }
            }
            
        }
        echo json_encode($log_system);
        exit;
    }
    public function update_task_title($project_id){
        $flag = 0;
        if(!empty($project_id) && $this->_pmCanModify('indicator', $project_id)){
            $this->loadModel('ProjectTask');
            if($_POST){
                $this->ProjectTask->id = $_POST['id'];
                $this->ProjectTask->saveField('task_title', $_POST['task_title']);
                $flag = 1;
            }
        }
        die(json_encode($flag));
    }
    public function getDataOfExternalTask($id)
    {
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectBudgetExternal');
        $externals=$this->ProjectTask->find('all',array(
            'recursive' => -1,
            'fields' => array('DISTINCT external_id as external'),
            'conditions' => array('ProjectTask.project_id' => $id,
                            'ProjectTask.special' => 1
                        ),
            'order' => array('ProjectTask.external_id'),
            'group' => array('ProjectTask.external_id')
        ));
        $dataExternals=array();
        foreach($externals as $_index=>$_external)
        {
            $external=$_external['ProjectTask']['external'];
            $providerName=$this->ProjectBudgetExternal->find('all',array(
                'recursive' => -1,
                'fields' => array('BP.name'),
                'conditions' => array('ProjectBudgetExternal.id'=>$external),
                'joins' => array(
                            array(
                                'table' => 'budget_providers',
                                'alias' => 'BP',
                                'type' => 'LEFT',
                                'foreignKey' => 'budget_provider_id',
                                'conditions'=> array(
                                    'BP.id = ProjectBudgetExternal.budget_provider_id'
                                )
                            )
            )));

            $setYearExternal=array();
            $dataSetsExternal=array();
            $maxValue=0;

            $minDate=$this->ProjectTask->find('all',array(
                'recursive' => -1,
                'fields' => array('MIN(task_start_date) as date'),
                'conditions' => array('ProjectTask.project_id' => $id,
                                'ProjectTask.special' => 1,
                                'ProjectTask.external_id' => $external
                            )
            ));
            $maxDate=$this->ProjectTask->find('all',array(
                'recursive' => -1,
                'fields' => array('MAX(task_end_date) as date'),
                'conditions' => array('ProjectTask.project_id' => $id,
                                'ProjectTask.special' => 1,
                                'ProjectTask.external_id' => $external
                            )
            ));
            $minDate=explode('-',$minDate[0][0]['date']);
            $maxDate=explode('-',$maxDate[0][0]['date']);
            // $minDate=strtotime($minDate[0][0]['date']);
            // $maxDate=strtotime($maxDate[0][0]['date'])+86400;
            $minDate=mktime(0,0,0,$minDate[1]-1,$minDate[2],$minDate[0]);
            $maxDate=mktime(0,0,0,$maxDate[1]+1,$maxDate[2],$maxDate[0]);

            $tasks=$this->ProjectTask->find('all',array(
                'recursive' => -1,
                'fields' => array('SUM(special_consumed) as consumed','task_title', 'SUM(estimated) as planed', 'DATE_FORMAT(task_start_date, "%m-%y") as date', 'DATE_FORMAT(task_start_date, "%Y") as year'),
                'conditions' => array('ProjectTask.project_id' => $id,
                                'ProjectTask.special' => 1,
                                'ProjectTask.external_id' => $external
                            ),
                'order' => array('ProjectTask.task_start_date'),
                'group' => array('DATE_FORMAT(task_start_date, "%M%Y")')
            ));
            $tasks=Set::combine($tasks,'{n}.0.date', '{n}');

            $sumPlaned=$sumConsumed=$progress=0;
            while($minDate<=$maxDate)
            {
                $key=date("m", $minDate).'-'.date("y", $minDate);
                //$arrMonth[]=$key;
                if(isset($tasks[$key]))
                {
                    $_val=$tasks[$key];
                    $setYearExternal[]=$_val[0]['year'];
                    $setNameExternal=$_val['ProjectTask']['task_title'];

                    //progress
                    $sumConsumed+=$_val[0]['consumed'];
                    $sumPlaned+=$_val[0]['planed'];
                }
                // else
                // {
                    // $sumconsumed=$sumconsumed;
                    // $sumplaned=$sumconsumed;
                // }
                if($minDate<=time())
                $dataSetsExternal[]=array('date'=>$key,'planed'=>$sumPlaned,'consumed'=>$sumConsumed);
                else
                $dataSetsExternal[]=array('date'=>$key,'planed'=>$sumPlaned);
                $minDate = mktime(0, 0, 0, date("m", $minDate)+1, date("d", $minDate), date("Y", $minDate));
            }

            if($sumPlaned==0||$sumConsumed==0)
            $progress=0;
            else
            $progress=round(($sumConsumed/$sumPlaned),2)*100;
            $maxValue=$sumPlaned+round($sumPlaned*0.1);

            $manDayExternals=$maxValue;
            $setYearExternal=array_unique($setYearExternal);
            $setYearExternal=join("-",$setYearExternal);
            $dataExternals[$external]=array('progressExternal'=>$progress,'dataSetsExternal'=>$dataSetsExternal,'manDayExternals'=>$manDayExternals,'setYearExternal'=>$setYearExternal,'setNameExternal'=>$setNameExternal,'setProviderName'=>isset($providerName[0]['BP']['name'])?$providerName[0]['BP']['name']:null);
        }
        $this->set(compact('dataExternals'));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function index($id = null) {
        if (!$this->_checkRole(true, $id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
            $this->data = null;
        }
        // ---------------------------
        $employee_info = $this->Session->read("Auth.employee_info");
        $employee_id = $employee_info['Employee']['id'];
        $this->set('project_id', $id);
        if (empty($this->data)) {
            $this->data = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
            $this->set(compact('data'));
        } else {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
           // $this->data["ProjectAmr"]["project_amr_mep_date"] = $str_utility->convertToSQLDate($this->data["ProjectAmr"]["project_amr_mep_date"]);
            // weather
            $this->data["ProjectAmr"]["weather"] = $this->data["ProjectAmr"]["weather"][0];

            $data_project['Project']['project_amr_program_id'] = $this->data['ProjectAmr']['project_amr_program_id'];
            $data_project['Project']['project_amr_sub_program_id'] = $this->data['ProjectAmr']['project_amr_sub_program_id'];
            //$data_project['Project']['project_manager_id'] = $this->data['ProjectAmr']['project_manager_id'];
            $data_project['Project']['budget'] = $this->data['ProjectAmr']['validated'];
            //$data_project['Project']['issues'] = $this->data['ProjectAmr']['project_amr_problem_information'];
            $data_project['Project']['currency_id'] = $this->data['ProjectAmr']['validated_currency_id'];
           // $data_project['Project']['planed_end_date'] = $this->data['ProjectAmr']['project_amr_mep_date'];
            $data_project['Project']['project_phase_id'] = $this->data['ProjectAmr']['project_phases_id'];

            if ($this->Project->ProjectAmr->save($this->data["ProjectAmr"])) {
                if (empty($this->data['ProjectAmr']['id']))
                    $project_amr_id = $this->Project->ProjectAmr->getLastInsertID(); // get last insert id
                else {
                    $project_amr_id = $this->data['ProjectAmr']['id'];
                }
                $this->Project->id = $this->data['ProjectAmr']['project_id'];
                $data_project['Project']['update_by_employee'] = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
                $this->Project->save($data_project);
                // $this->Session->setFlash(__('The project AMR has been saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The project AMR could not be saved. Please try again enter informations for (*) fields match.', true), 'error');
            }
            $this->redirect($this->referer());
        }

        $view_id = $this->UserDefaultView->find('first', array('fields' => 'UserDefaultView.user_view_id', 'conditions' => array('UserDefaultView.employee_id' =>
                $employee_id)));
        $view_id = $view_id['UserDefaultView']['user_view_id'];
        $view = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
        $view_content = $view['UserView']['content'];
        if ($view_content == "") { //default view
            $view_content = '<user_view>
                                <project_detail project_name = "Project Name" />
                                <project_detail company_id = "Company" />
                                <project_detail project_manager_id = "Project Manager" />
                                <project_detail project_priority_id = "Priority" />
                                <project_detail project_status_id = "Status" />
                                <project_detail start_date = "Start Date" />
                                <project_detail issues = "Issues" />
                                <project_detail constraint = "Constraint" />
                                <project_detail remark = "Remark" />
                                <project_detail project_objectives = "Project Objectives" />
                                <project_amr weather = "Weather" />
                                <project_amr project_amr_program_id = "Program" />
                                <project_amr project_amr_sub_program_id = "Sub Program" />
                                <project_amr project_amr_category_id = "Category" />
                                <project_amr project_amr_sub_category_id = "Sub Category" />
                                <project_amr project_manager_id = "Project Manager" />
                                <project_amr budget = "Budget" />
                                <project_amr project_amr_status_id = "Status" />
                                <project_amr project_amr_mep_date = "MEP Date" />
                                <project_amr project_amr_progression = "Progression" />
                                <project_amr project_phases_id = "Phase" />
                                <project_amr project_amr_cost_control_id = "Cost Control" />
                                <project_amr project_amr_organization_id = "Organization" />
                                <project_amr project_amr_plan_id = "Planning" />
                                <project_amr project_amr_perimeter_id = "Perimeter" />
                                <project_amr project_amr_risk_control_id = "Risk Control" />
                                <project_amr project_amr_problem_control_id = "Problem Control" />
                                <project_amr project_amr_risk_information = "Risk Information" />
                                <project_amr project_amr_problem_information = "Problem Information" />
                                <project_amr project_amr_solution = "Solution" />
                                <project_amr project_amr_solution_description = "Solution Description" />
                        </user_view>';
        }
        $projectStatuses = $this->Project->ProjectStatus->find('list');
        $amrCurrencies = $this->Project->Currency->find('list');

        $project_detail = $this->Project->find("first", array('conditions' => array('Project.id' => $id)));
        $project_manager = $this->Project->Employee->find('first', array('fields' => array('Employee.id', 'Employee.fullname'),
            'conditions' => array('Employee.id' => $project_detail['Project']['project_manager_id'])));
        $company_id = $project_detail['Project']['company_id'];
        $projectAmrManagers = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));


        $projectAmrPrograms = $this->Project->ProjectAmr->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
            'conditions' => array('ProjectAmrProgram.company_id' => $company_id)));
        $projectAmrSubPrograms = $this->Project->ProjectAmr->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program')));
        $projectAmrCategories = $this->Project->ProjectAmr->ProjectAmrCategory->find('list', array('fields' => array('ProjectAmrCategory.id', 'ProjectAmrCategory.amr_category'),
            'conditions' => array('ProjectAmrCategory.company_id' => $company_id)));
        $projectAmrSubCategories = $this->Project->ProjectAmr->ProjectAmrSubCategory->find('list', array('fields' => array('ProjectAmrSubCategory.id', 'ProjectAmrSubCategory.amr_sub_category')));
        $projectAmrStatuses = $this->Project->ProjectAmr->ProjectAmrStatus->find('list', array('fields' => array('ProjectAmrStatus.id', 'ProjectAmrStatus.amr_status'),
            'conditions' => array('ProjectAmrStatus.company_id' => $company_id)));
        $projectAmrCostControls = $this->Project->ProjectAmr->ProjectAmrCostControl->find('list', array('fields' => array('ProjectAmrCostControl.id', 'ProjectAmrCostControl.amr_cost_control'),
            'conditions' => array('ProjectAmrCostControl.company_id' => $company_id)));
        $projectAmrOrganizations = $this->Project->ProjectAmr->ProjectAmrOrganization->find('list', array('fields' => array('ProjectAmrOrganization.id', 'ProjectAmrOrganization.amr_organization'),
            'conditions' => array('ProjectAmrOrganization.company_id' => $company_id)));
        $projectAmrPlans = $this->Project->ProjectAmr->ProjectAmrPlan->find('list', array('fields' => array('ProjectAmrPlan.id', 'ProjectAmrPlan.amr_plan'),
            'conditions' => array('ProjectAmrPlan.company_id' => $company_id)));
        $projectAmrPerimeters = $this->Project->ProjectAmr->ProjectAmrPerimeter->find('list', array('fields' => array('ProjectAmrPerimeter.id', 'ProjectAmrPerimeter.amr_perimeter'),
            'conditions' => array('ProjectAmrPerimeter.company_id' => $company_id)));
        $projectAmrRiskControls = $this->Project->ProjectAmr->ProjectAmrRiskControl->find('list', array('fields' => array('ProjectAmrRiskControl.id', 'ProjectAmrRiskControl.amr_risk_control'),
            'conditions' => array('ProjectAmrRiskControl.company_id' => $company_id)));
        $projectAmrProblemControls = $this->Project->ProjectAmr->ProjectAmrProblemControl->find('list', array('fields' => array('ProjectAmrProblemControl.id', 'ProjectAmrProblemControl.amr_problem_control'),
            'conditions' => array('ProjectAmrProblemControl.company_id' => $company_id)));
        $projectAmrPhases = $this->Project->ProjectPhase->find('list', array('order' => array("ProjectPhase.phase_order ASC")));
        //options for projectManagers
        $allEmployees = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $employeeIds = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array();
        foreach ($employeeIds as $key => $value) {
            foreach ($allEmployees as $key2 => $value2) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $value2;
                    break;
                }
            }
        }
        $this->data['ProjectAmr']['project_manager_id'] = $project_manager['Employee']['id'];
        $this->data['ProjectAmr']['currency_id'] = $project_detail['Project']['currency_id'];
        // options for ProjectPhases
        $ProjectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => array('ProjectPhase.phase_order ASC')));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $start_date = $project_detail['Project']['start_date'];
        $end_date = $project_detail['Project']['end_date'];
        $end_plan_date = $project_detail['Project']['planed_end_date'];
        $projectName = $this->Project->find("first", array("fields" => array("Project.project_name"),
            'conditions' => array('Project.id' => $id)));

        // change feedback 05/04/2013
        $this->loadModel('ProjectEvolution');
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectEvolution.project_id' => $id),
                'fields' => array('SUM(man_day) as totalMD')
            ));
        $_projectTask = $this->_getPorjectTask($id);
        $validated = isset($projectEvolutions[0][0]['totalMD']) ? $projectEvolutions[0][0]['totalMD'] : 0;
        $engaged = $_projectTask['engaged'];
        $remain = $_projectTask['remain'];

        //24/10/2013 huy thang
        // $variance = $validated - $engaged - $remain;
        $variance = $engaged + $remain - $validated;
        //24/10/2013 huy thang

        $progression = $_projectTask['progression'];

        $this->set(compact('validated', 'engaged', 'remain', 'variance', 'progression'));
        // end change feedback 05/04/2013

        // change feedback 22/04/2013
        $this->loadModel('ProjectPhasePlan');

        $projectPlans = $this->ProjectPhasePlan->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'NOT' => array('phase_planed_start_date' => '0000-00-00', 'phase_planed_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'MIN(phase_planed_start_date) AS startDate',
                    'MAX(phase_planed_end_date) AS endDate'
                )
            ));
        $endDateAllTask = null;
        if(!empty($projectPlans[0][0]['endDate'])){
            $endDateAllTask = strtotime($projectPlans[0][0]['endDate']);
        }
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $id,
                'NOT' => array(
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if(!empty($dataSystems)){
            foreach($dataSystems as $dataSystem){
                $dx = $dataSystem['TmpStaffingSystem'];
                if(!isset($assignEmployees[$dx['project_id']])){
                    $assignEmployees[$dx['project_id']] = 0;
                }
                $assignEmployees[$dx['project_id']] += $dx['estimated'];
            }
        }
        $_dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('profit_center'),
                'project_id' => $id,
                'NOT' => array(
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignProfitCenters = array();
        if(!empty($_dataSystems)){
            foreach($_dataSystems as $_dataSystem){
                $dx = $_dataSystem['TmpStaffingSystem'];
                if(!isset($assignProfitCenters[$dx['project_id']])){
                    $assignProfitCenters[$dx['project_id']] = 0;
                }
                $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
            }
        }
        $tWorkload = !empty($_projectTask['validated']) ? $_projectTask['validated'] : 0;
        $assgnPc = !empty($assignProfitCenters[$id]) ? $assignProfitCenters[$id] : 0;
        $assginProfitCenter = ($tWorkload == 0) ? '0' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100' : round(($assgnPc/$tWorkload)*100, 2));
        $assgnEmploy = !empty($assignEmployees[$id]) ? $assignEmployees[$id] : 0;
        $assgnEmployee = ($tWorkload == 0) ? '0' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100' : round(($assgnEmploy/$tWorkload)*100, 2));
        //Gantt+ insert to KPI
        $this->_ganttFromPhase($id);
        $this->set(compact('endDateAllTask', 'assginProfitCenter', 'assgnEmployee', 'employee_info'));
        // end change feedback 22/04/2013
        $this->set(compact('projectStatuses', 'projectManagers', 'project_manager', 'start_date', 'end_date', 'end_plan_date', 'view_content', 'projectAmrManagers', 'projectAmrPrograms', 'projectAmrSubPrograms', 'projectAmrCategories', 'projectAmrSubCategories', 'ProjectPhases', 'projectAmrStatuses', 'projectAmrCostControls', 'projectAmrOrganizations', 'projectAmrPlans', 'projectAmrPerimeters', 'projectAmrRiskControls', 'projectAmrProblemControls', 'projectAmrPhases', 'amrCurrencies', 'currency_name', 'projectName', 'data'));
    }

    /**
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_program($id = null, $currenr_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectAmr->ProjectAmrSubProgram->find('list', array('conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => explode(',', $id)), 'fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program')));
            if (!empty($list)) {
                echo sprintf(__("%s--Select sub program--%s", true), '<option value="">', '</option>');
                foreach ($list as $k => $v) {
                    if ($k == $currenr_id)
                        $selected = "selected"; else
                        $selected = "";
                    //echo "<label><input type='checkbox' value='".$k."' name='ProjectProjectAmrSubProgramId[]' />" . $v . "</label>";
                    echo "<option value='" . $k . "' $selected >" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub program--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub program--%s", true), '<option>', '</option>');
        }
    }

    /**
     * get_sub_categories
     * Get sub categories
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_categories($id = null, $currenr_id = null) {
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectAmr->ProjectAmrSubCategory->find('list', array('conditions' => array('ProjectAmrSubCategory.project_amr_category_id' => $id),
                'fields' => array('ProjectAmrSubCategory.id', 'ProjectAmrSubCategory.amr_sub_category')));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if ($k == $currenr_id)
                        $selected = "selected"; else
                        $selected = "";
                    echo "<option value='" . $k . "' $selected>" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub category--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub category--%s", true), '<option>', '</option>');
        }
    }

    /**
     * exportExcel
     * Export to Excel
     * @param int $project_id
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        if (!$this->_checkRole(true, $project_id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
            $this->data = null;
        }
        // ---------------------------

        //$this->layout='excel';
        $this->set('columns', $this->name_columna);
        $this->ProjectAmr->recursive = 0;
        $employee_info = $this->Session->read("Auth.employee_info");
        $employee_id = $employee_info['Employee']['id'];
        $view_id = $this->UserDefaultView->find('first', array('fields' => 'UserDefaultView.user_view_id', 'conditions' => array('UserDefaultView.employee_id' =>
                $employee_id)));
        $view_id = $view_id['UserDefaultView']['user_view_id'];
        //$view = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
        //$view_content = $view['UserView']['content'];
        //if ($view_content == "") { //default view
        $view_content = '<user_view>
                                    <project_detail project_name = "Project Name" />
                                    <project_detail company_id = "Company" />
                                    <project_detail project_manager_id = "Project Manager" />
                                    <project_detail project_priority_id = "Priority" />
                                    <project_detail project_status_id = "Status" />
                                    <project_detail start_date = "Start Date" />
                                    <project_detail issues = "Issues" />
                                    <project_detail constraint = "Constraint" />
                                    <project_detail remark = "Remark" />
                                    <project_detail project_objectives = "Project Objectives" />
                                    <project_amr weather = "Weather" />
                                    <project_amr project_amr_program_id = "Program" />
                                    <project_amr project_amr_sub_program_id = "Sub Program" />
                                    <project_amr project_amr_category_id = "Category" />
                                    <project_amr project_amr_sub_category_id = "Sub Category" />
                                    <project_amr project_manager_id = "Project Manager" />
                                    <project_amr project_amr_mep_date = "MEP Date" />
                                    <project_amr project_amr_progression = "Progression" />
                                    <project_amr assign_to_pc = "% Assigned to profit center" />
                                    <project_amr assign_to_employee = "% Assigned to employee" />
                                    <project_amr project_phases_id = "Phase" />
                                    <project_amr project_amr_cost_control_id = "Cost Control" />
                                    <project_amr project_amr_organization_id = "Organization" />
                                    <project_amr project_amr_plan_id = "Planning" />
                                    <project_amr project_amr_perimeter_id = "Perimeter" />
                                    <project_amr project_amr_risk_control_id = "Risk Control" />
                                    <project_amr project_amr_problem_control_id = "Problem Control" />
                                    <project_amr project_amr_risk_information = "Risk Information" />
                                    <project_amr project_amr_problem_information = "Problem Information" />
                                    <project_amr project_amr_solution = "Solution" />
                                    <project_amr project_amr_solution_description = "Solution Description" />
                                    <project_amr cost_control_weather = "Cost Control" />
                                    <project_amr planning_weather = "Planning" />
                                    <project_amr risk_control_weather = "Risk Control" />
                                    <project_amr organization_weather = "Organization" />
                                    <project_amr perimeter_weather = "Perimeter" />
                                    <project_amr issue_control_weather = "Issue Control" />
                                    <project_amr md_validated = "M.D Validated" />
                                    <project_amr md_engaged = "M.D Engaged" />
                                    <project_amr md_forecasted = "M.D Forecasted" />
                                    <project_amr md_variance = "M.D Variance" />
                                    <project_amr validated_currency_id = "Validated Currency" />
                                    <project_amr engaged_currency_id = "Engaged Currency" />
                                    <project_amr forecasted_currency_id = "Forecasted Currency" />
                                    <project_amr variance_currency_id = "Variance Currency" />
                                    <project_amr validated = "Validated" />
                                    <project_amr engaged = "Engaged" />
                                    <project_amr forecasted = "Forecasted" />
                                    <project_amr variance = "Variance" />
                                </user_view>';
        //}
        $amr_content = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $project_id)));
        if ($amr_content == "") {
            $this->Session->setFlash(__('Save data before export', true), 'error');
            $this->redirect("/project_amrs/index/" . $project_id);
        } else {
            $project_name = $amr_content['Project']['project_name'];

            $amr_program = $amr_content['ProjectAmrProgram']['amr_program'];
            $amr_sub_program = $amr_content['ProjectAmrSubProgram']['amr_sub_program'];
            $amr_category = $amr_content['ProjectAmrCategory']['amr_category'];
            $amr_sub_category = $amr_content['ProjectAmrSubCategory']['amr_sub_category'];
            $project_manager = $amr_content['Employee']['fullname'];
            $budget = $amr_content['Project']['budget'];
            $amr_status = $amr_content['ProjectAmrStatus']['amr_status'];
            $amr_phase = $amr_content['ProjectPhases']['name'];
            $amr_cost_control = $amr_content['ProjectAmrCostControl']['amr_cost_control'];
            $amr_organization = $amr_content['ProjectAmrOrganization']['amr_organization'];
            $amr_plan = $amr_content['ProjectAmrPlan']['amr_plan'];
            $amr_perimeter = $amr_content['ProjectAmrPerimeter']['amr_perimeter'];
            $amr_risk_control = $amr_content['ProjectAmrRiskControl']['amr_risk_control'];
            $amr_problem_control = $amr_content['ProjectAmrProblemControl']['amr_problem_control'];
            $amr_risk_info = $amr_content['ProjectAmr']['project_amr_risk_information'];
            $amr_problem_info = $amr_content['ProjectAmr']['project_amr_problem_information'];
            $amr_solution = $amr_content['ProjectAmr']['project_amr_solution'];
            $amr_solution_decs = $amr_content['ProjectAmr']['project_amr_solution_description'];
            $mepdate = $amr_content['ProjectAmr']['project_amr_mep_date'];
            //$progress = $amr_content['ProjectAmr']['project_amr_progression'];

            $currency = $this->Project->Currency->find('first', array('fields' => array('Currency.sign_currency'), 'recursive' => -1,
                'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['currency_id'])));
            $currency = $currency['Currency']['sign_currency'];

            if (!empty($amr_content['ProjectAmr']['weather']))
                $weather = $amr_content['ProjectAmr']['weather']; else
                $weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['cost_control_weather']))
                $cost_control_weather = $amr_content['ProjectAmr']['cost_control_weather']; else
                $cost_control_weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['planning_weather']))
                $planning_weather = $amr_content['ProjectAmr']['planning_weather']; else
                $planning_weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['risk_control_weather']))
                $risk_control_weather = $amr_content['ProjectAmr']['risk_control_weather']; else
                $risk_control_weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['perimeter_weather']))
                $perimeter_weather = $amr_content['ProjectAmr']['perimeter_weather']; else
                $perimeter_weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['organization_weather']))
                $organization_weather = $amr_content['ProjectAmr']['organization_weather'];else
                $organization_weather = 'sun';
            if (!empty($amr_content['ProjectAmr']['issue_control_weather']))
                $issue_control_weather = $amr_content['ProjectAmr']['issue_control_weather']; else
                $issue_control_weather = 'sun';

            //25/10/2013 huy thang
            // $md_validated = $amr_content['ProjectAmr']['md_validated'];
            // $md_engaged = $amr_content['ProjectAmr']['md_engaged'];
            // $md_forecasted = $amr_content['ProjectAmr']['md_forecasted'];
            // $md_variance = $amr_content['ProjectAmr']['md_variance'];

            $this->loadModel('ProjectEvolution');
            $projectEvolutions = $this->ProjectEvolution->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectEvolution.project_id' => $project_id),
                'fields' => array('SUM(man_day) as totalMD')
            ));
            $_projectTask = $this->_getPorjectTask($project_id);
            $md_validated = isset($projectEvolutions[0][0]['totalMD']) ? $projectEvolutions[0][0]['totalMD'] : 0;
            $md_engaged = $_projectTask['engaged'];
            $md_forecasted = $_projectTask['remain'];

            $md_variance = $md_engaged + $md_forecasted - $md_validated;
            //25/10/2013 huy thang

            if (!empty($amr_content['ProjectAmr']['validated_currency_id'])) {
                $validated_currency = $this->Project->Currency->find('first', array(
                    'fields' => array('Currency.sign_currency'),
                    'recursive' => -1,
                    'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['validated_currency_id'])));
                $validated_currency = $validated_currency['Currency']['sign_currency'];
            }else
                $validated_currency = '';

            if (!empty($amr_content['ProjectAmr']['engaged_currency_id'])) {
                $engaged_currency = $this->Project->Currency->find('first', array(
                    'fields' => array('Currency.sign_currency'),
                    'recursive' => -1,
                    'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['engaged_currency_id'])));
                $engaged_currency = $engaged_currency['Currency']['sign_currency'];
            }else
                $engaged_currency = '';

            if (!empty($amr_content['ProjectAmr']['forecasted_currency_id'])) {
                $forecasted_currency = $this->Project->Currency->find('first', array(
                    'fields' => array('Currency.sign_currency'),
                    'recursive' => -1,
                    'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['forecasted_currency_id'])));
                $forecasted_currency = $forecasted_currency['Currency']['sign_currency'];
            }else
                $forecasted_currency = '';

            if (!empty($amr_content['ProjectAmr']['variance_currency_id'])) {
                $variance_currency = $this->Project->Currency->find('first', array(
                    'fields' => array('Currency.sign_currency'),
                    'recursive' => -1,
                    'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['variance_currency_id'])));
                $variance_currency = $variance_currency['Currency']['sign_currency'];
            }else
                $variance_currency = '';

            $validated = $amr_content['ProjectAmr']['validated'];
            $engaged = $amr_content['ProjectAmr']['engaged'];
            $forecasted = $amr_content['ProjectAmr']['forecasted'];
            $variance = $amr_content['ProjectAmr']['variance'];
            /**
             * Lay data budget
             */
            $employeeName = $this->_getEmpoyee();
            $this->loadModel('TmpStaffingSystem');
            $dataSystems = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('employee'),
                    'project_id' => $project_id,
                    'NOT' => array(
                        'model_id' => 999999999
                    ),
                    'company_id' => $employeeName['company_id']
                ),
                'fields' => array('project_id', 'model_id', 'estimated')
            ));
            $assignEmployees = array();
            if(!empty($dataSystems)){
                foreach($dataSystems as $dataSystem){
                    $dx = $dataSystem['TmpStaffingSystem'];
                    if(!isset($assignEmployees[$dx['project_id']])){
                        $assignEmployees[$dx['project_id']] = 0;
                    }
                    $assignEmployees[$dx['project_id']] += $dx['estimated'];
                }
            }
            $_dataSystems = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model' => array('profit_center'),
                    'project_id' => $project_id,
                    'NOT' => array(
                        'model_id' => 999999999
                    ),
                    'company_id' => $employeeName['company_id']
                ),
                'fields' => array('project_id', 'model_id', 'estimated')
            ));
            $assignProfitCenters = array();
            if(!empty($_dataSystems)){
                foreach($_dataSystems as $_dataSystem){
                    $dx = $_dataSystem['TmpStaffingSystem'];
                    if(!isset($assignProfitCenters[$dx['project_id']])){
                        $assignProfitCenters[$dx['project_id']] = 0;
                    }
                    $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
                }
            }
            $tWorkload = !empty($_projectTask['validated']) ? $_projectTask['validated'] : 0;
            $assgnPc = !empty($assignProfitCenters[$project_id]) ? $assignProfitCenters[$project_id] : 0;
            $assginProfitCenter = ($tWorkload == 0) ? '0' : ((round(($assgnPc/$tWorkload)*100, 2) > 100)? '100' : round(($assgnPc/$tWorkload)*100, 2));
            $assgnEmploy = !empty($assignEmployees[$project_id]) ? $assignEmployees[$project_id] : 0;
            $assgnEmployee = ($tWorkload == 0) ? '0' : ((round(($assgnEmploy/$tWorkload)*100, 2) > 100)? '100' : round(($assgnEmploy/$tWorkload)*100, 2));
            $progress = !empty($_projectTask['progression']) ? $_projectTask['progression'] : 0;
			
			
            $this->set(compact(
                'view_content', 'assginProfitCenter', 'assgnEmployee',
                'weather',
                'amr_program', 'amr_sub_program', 'amr_category', 'amr_sub_category', 'project_manager', 'budget',
                'amr_status', 'amr_status', 'amr_phase', 'amr_cost_control', 'amr_organization', 'amr_plan',
                'amr_perimeter', 'amr_risk_control', 'amr_problem_control', 'amr_risk_info',
                'amr_problem_info', 'amr_solution', 'amr_solution_decs', 'mepdate', 'currency', 'progress',
                'project_name', 'cost_control_weather', 'planning_weather', 'risk_control_weather', 'perimeter_weather',
                'organization_weather', 'issue_control_weather', 'md_validated', 'md_engaged', 'md_forecasted', 'md_variance',
                'validated_currency', 'engaged_currency', 'forecasted_currency', 'variance_currency', 'validated', 'engaged',
                'forecasted', 'variance'
            ));
        }
    }

    private function _getPorjectTask($id){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectTask');
        $projectName = $this->viewVars['projectName'];

        $projectTasks = $this->ProjectTask->find(
                "all", array(
				'fields' => array(
					'id',
					'estimated',
					'parent_id'
					),
				'recursive' => -1,
				"conditions" => array('project_id' => $id,'special'=>0)
            )
        );
        $_projectTaskId = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id') : array();
        $_parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.parent_id') : array();
        foreach($projectTasks as $key => $projectTask){
            if(in_array($projectTask['ProjectTask']['id'], $_parentIds)){
                unset($projectTasks[$key]);
            }
        }
        foreach($_parentIds as $k => $value){
            if($value == 0){
                unset($_parentIds[$k]);
            }
        }
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'project_task_id'),
            'conditions' => array(
                'project_task_id' => $_projectTaskId,
                'special'=>0,
                'NOT' => array("project_task_id" => null))
        ));
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask') : array();

        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $_activityTaskId,
                'company_id' => $projectName['Project']['company_id'],
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $newActivityRequests = array();
        foreach ($activityRequests as $key => $activityRequest) {
            $newActivityRequests[$activityRequest['ActivityRequest']['task_id']] = $activityRequest[0]['value'];
        }
        $activityRequests = $newActivityRequests;
        $total = 0;
        $sumEstimated = $sumRemain = $sumRemainConsumed = $sumRemainNotConsumed = $t = 0;
        foreach ($projectTasks as $key => $projectTask) {
            $projectTaskId = $projectTask['ProjectTask']['id'];
            if($projectTask['ProjectTask']['parent_id'] == 0){
                $sumEstimated += $projectTask['ProjectTask']['estimated'];
            }
            $referencedEmployees = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if(count($referencedEmployees) == 0){}
            else {
                foreach ($referencedEmployees as $key1 => $referencedEmployee) {
                    $projectTasks[$key]['ProjectTask']['ProjectTaskEmployeeRefer'][] = $referencedEmployee['ProjectTaskEmployeeRefer'];
                }
            }
            $estimated = isset($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            // Check if Activity Task Existed
            if (isset($activityTasks[$projectTaskId])) {
                $activityTaskId = $activityTasks[$projectTaskId]['id'];
                // Check if Request Existed
                if (isset($activityRequests[$activityTaskId])) {
                    $consumed = $activityRequests[$activityTaskId];
                    $completed = $estimated - $consumed;
                    if($completed < 0){
                        $completed = 0;
                    }
                    $sumRemainConsumed += $completed;
                    $total += $consumed;
                } else {
                    if(in_array($projectTask['ProjectTask']['id'], $_parentIds, true)){
                        //unset($projectTask);
                    } else {
                        $sumRemainNotConsumed += $estimated;
                    }
                    $total += 0;
                }
            } else {
                // Error Handle
                $sumRemainNotConsumed += $estimated;
                $total += 0;
            }
        }
        if(!empty($_parentIds)){
            foreach($_parentIds as $_parentId){
                $_activityTaskId = !empty($activityTasks[$_parentId]['id']) ? $activityTasks[$_parentId]['id'] : '';
                $_consumed = !empty($activityRequests[$_activityTaskId]) ? $activityRequests[$_activityTaskId] : 0;
                $total += $_consumed;
            }
        }
        $sumRemain = $sumRemainConsumed + $sumRemainNotConsumed;

        $this->loadModel('Project');
        $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $id),
                'fields' => array('activity_id')
            ));
        $_activityIdProject = $project['Project']['activity_id'];
        $this->loadModel('ActivityRequest');
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'SUM(value) as consumed'
            ),
            'conditions' => array(
                'activity_id' => $_activityIdProject,
                'company_id' => $projectName['Project']['company_id'],
                'status' => 2,
                'NOT' => array('value' => 0)
            )
        ));
        $_engaged = $_progression = 0;
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged+$sumRemain;

        if($validated == 0){
            $_progression = 0;
        } else {
            $_progression = round((($_engaged*100)/$validated), 2);
        }
        if($_progression > 100){
            $_progression = 100;
        } else {
            $_progression = $_progression;
        }


        $_projectTask = array();
        $_projectTask['engaged'] = $_engaged;
        $_projectTask['remain'] = $sumRemain;
        $_projectTask['validated'] = $validated;
        $_projectTask['progression'] = $_progression;

        return $_projectTask;
    }

    private function _ganttFromPhase($project_id){
        $this->loadModel('Project');
        $this->loadModel('ProjectMilestone');
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $projects = array();
        $projects = $this->Project->find('all', array(
            'conditions' => array('Project.id' => $project_id),
            'contain' => array(
                'ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ),
                'ProjectPhase' => array('name', 'color')
                )
            ),
            'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date')
        ));
        $projectMilestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
            'order' => array('milestone_date' => 'ASC')
        ));
        $projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $this->set(compact('projects', 'display', 'projectMilestones'));
    }

     /**
     * Budget Internal, External, Sales.
     *
     * @return void
     * @access public
     */
    private function _getDataBudget($project_id = null) {
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetSale');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('Activity');
        // get project and data of project
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
        // project task
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special' => 1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $this->ProjectTask->virtualFields = array('s_over' => 'SUM(ProjectTask.overload)');
        $varO = $this->ProjectTask->find('first', array(
            'fields' => array('ProjectTask.s_over'),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special'=>0)
        ));
        $workloadExter = $varE;
        $remainExter = $varE - $externalConsumeds;
        $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
        /**
         *
         */
        $getWordloads = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed')
        ));
        $parentGetWl = !empty($getWordloads) ? Set::classicExtract($getWordloads, '{n}.ProjectTask.parent_id') : array();
        $getWordloads = !empty($getWordloads) ? Set::combine($getWordloads, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $sumWorload = 0;
        foreach($getWordloads as $taskId => $getWordload){
            if(in_array($taskId, $parentGetWl)){
                unset($getWordloads[$taskId]);
            } else {
                $projectId = $getWordload['project_id'];
                $dataEstimated = $getWordload['estimated'];
                $sumWorload += $dataEstimated;
            }
        }
        $workloadInter = $sumWorload - $varE;
        // Budget Sales
        $this->ProjectBudgetSale->cacheQueries = true;
        $this->ProjectBudgetSale->recursive = -1;
        $this->ProjectBudgetSale->Behaviors->attach('Containable');
        $projectBudgetSales = $this->ProjectBudgetSale->find('all', array(
                'conditions' => array('ProjectBudgetSale.project_id' => $project_id),
                'contain' => array('ProjectBudgetInvoice' => array('billed', 'paid', 'effective_date')),
                'fields' => array('sold', 'man_day')
            ));
        $sales = array();
        if(!empty($projectBudgetSales)){
            $sold = $billed = $paid = $billed_check = $manDay = 0;
            foreach($projectBudgetSales as $key => $projectBudgetSale){

                if(!empty($projectBudgetSale['ProjectBudgetInvoice'])){
                    foreach($projectBudgetSale['ProjectBudgetInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $billed_check += $values['billed'];
                        }
                        $billed += $values['billed'];
                        $paid += $values['paid'];
                    }
                }
                $sold += !empty($projectBudgetSale['ProjectBudgetSale']['sold']) ? $projectBudgetSale['ProjectBudgetSale']['sold'] : 0;
                $manDay += !empty($projectBudgetSale['ProjectBudgetSale']['man_day']) ? $projectBudgetSale['ProjectBudgetSale']['man_day'] : 0;
            }
            $sales['sold'] = $sold;
            $sales['paid'] = $paid;
            $sales['billed'] = $billed_check;
            $sales['manDay'] = $manDay;
        }
        // Internal costs
        $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average')
        ));
        $engagedErro = 0;
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
        $internals = $internalDetails = array();
        $count = $budgetEuro = $budgetManDay = $average = 0;
        if(!empty($projectBudgetInternals)){
            foreach($projectBudgetInternals as $key => $projectBudgetInternal){
                $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                if(empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average'])){
                    $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] = 0;
                }
                $average += $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                $internalDetails[$key]['budget_euro'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']*$projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                $budgetEuro += ($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']*$projectBudgetInternal['ProjectBudgetInternalDetail']['average']);
                $budgetManDay += !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                $count++;
            }
        }
        $average = ($count == 0) ? 0 : round($average/$count, 2);
        $internals = array(
            'budgetEuro' => $budgetEuro,
            'budgetManDay' => $budgetManDay,
            'forecastedManDay' => $getDataProjectTasks['consumed'] + $getDataProjectTasks['remain'],
            'forecastEuro' => $engagedErro + ($remainInter * $average),
            'varEuro' => ($budgetEuro != 0) ? round(((($engagedErro + ($remainInter * $average))/$budgetEuro)-1)*100, 2) : 0,
            'consumedManday' => $getDataProjectTasks['consumed'],
            'consumedEuro' => $engagedErro,
            'remainManday' => $getDataProjectTasks['remain'],
            'remainEuro' => $remainInter * $average,
        );
//        // External costs
        $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
        ));
        $externals = array();
        if(!empty($projectBudgetExternals)){
            $exBudgetManDay = $exBudgetEuro = $exRemainEuro = $exForecastEuro = $exOrderEuro = 0;
            foreach($projectBudgetExternals as $key => $projectBudgetExternal){
                $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                $exBudgetEuro += !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                $exRemainEuro += !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                $order = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                $exOrderEuro += $order;
                $remain = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                $exForecastEuro += $order + $remain;
                $exBudgetManDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
            }
            $exVarEuro = ($exBudgetEuro == 0 ) ? -100 : round(((($exOrderEuro + $exRemainEuro)/$exBudgetEuro) - 1)*100, 2);
            $externals['BudgetManDay'] = $exBudgetManDay;
            $externals['BudgetEuro'] = $exBudgetEuro;
            $externals['ForecastEuro'] = $exForecastEuro;
            $externals['VarEuro'] = $exVarEuro;
            $externals['ConsumedEuro'] = $exOrderEuro;
            $externals['RemainEuro'] = $exRemainEuro;
        }
        $this->set(compact('externalConsumeds', 'workloadExter', 'remainExter', 'remainInter', 'workloadInter'));
        $this->set(compact('activityName', 'activity_id', 'sales', 'internalDetails', 'internals', 'externals'));
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
    function export_pdf(){
        $this->layout = false;
        ini_set('memory_limit', '2048M');
        if (!empty($this->data['Export'])) {
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);
            $tmpFile = TMP . 'kpi.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));
            list($_width, $_height) = getimagesize($tmpFile);
            //
            $height = 195;
            $i = 0;
            $image = imagecreatefrompng($tmpFile);
            $list = array();
            do {
                $i++;
                if($_height - $height >= 1900){
                    $h = 1900;
                } else {
                    $h = $_height - $height;
                }
                $crop = imagecreatetruecolor($_width, $h);
                //set white background
                $white    = imagecolorallocate($crop, 255, 255, 255);
                imagefill($crop, 0, 0, $white);

                $tmpFile1 = TMP . 'kpi_' . $i . '.png';
                imagecopy($crop, $image, 0, 0, 0, $height, $_width, 1900);
                imagepng($crop, $tmpFile1);
                $height += 1900;
                $list[$i] = $tmpFile1;
            } while($height < $_height);
            unlink($tmpFile);
            $this->set(compact('list', 'height', 'rows'));
        } else {
            $this->redirect(array('action' => 'indicator'));
        }
    }
    public function save_layout_setting($project_id){
        $this->loadModel('ProjectIndicatorSetting');
		if(($this->employee_info['Role']['name'] == 'pm') || ($this->employee_info['Role']['name'] == 'admin')){
            if(!empty($_POST['data'])){
                $data_setting = $_POST['data'];
				$aub = serialize($data_setting);
                $find = $this->ProjectIndicatorSetting->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                    ),
                    'fields' => array('id')
                ));
                $id = !empty($find) ? $find['ProjectIndicatorSetting']['id'] : 0;
                if(empty($id)){
                    $this->ProjectIndicatorSetting->create();
                }else{
                    $this->ProjectIndicatorSetting->id = $id;
                } 
                $save_setting = $this->ProjectIndicatorSetting->save(array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'widget_setting' => serialize($data_setting),
                    'created' => time(),
                    'user_last_updated' => $this->employee_info['Employee']['id'],
                    'updated' => time()
                ));
                if($save_setting) die(json_encode($save_setting));
            }
        }
        die(json_encode(false));
    }
    public function save_dashboard_setting(){
        $this->loadModel('CompanyDefaultSetting');
		if(!empty($_POST['data'])){
			$data_setting = $_POST['data'];
			$find = $this->CompanyDefaultSetting->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'df_key' => 'dashboard_default_setting',
				),
				'fields' => array('id')
			));
			$id = !empty($find) ? $find['CompanyDefaultSetting']['id'] : 0;
			if(empty($id)){
				$this->CompanyDefaultSetting->create();
			}else{
				$this->CompanyDefaultSetting->id = $id;
			} 
			$save_setting = $this->CompanyDefaultSetting->save(array(
				'company_id' => $this->employee_info['Company']['id'],
				'df_key' => 'dashboard_default_setting',
				'df_value' => serialize($data_setting),
				'employee_updated' => $this->employee_info['Employee']['id'],
				'created' => time(),
				'updated' => time()
			));
			if($save_setting) die(json_encode($save_setting));
		}
        die(json_encode(false));
    }
    public function updateWeather(){
        $this->loadModel('Project');
        $flag = 0;
		$allowArray = array('sunny','sun', 'cloud','fair','cloudy','furry','rain', 'up', 'mid', 'down');
		$val = ( in_array($this->data['value'], $allowArray) ) ? $this->data['value'] : null;
		
        if(!empty($this->data)){
			
            if($this->_pmCanModify('indicator', $this->data['project_id'])){
                $field = $this->data['field'];
                $field = str_replace('data[ProjectAmr][', '', $field);
                $field = str_replace('][]', '', $field);
				
				$project_id = $this->Project->find('list', array(
					'recursive' => -1,
                    'conditions' => array(
						'id' => $this->data['project_id'],
						'company_id' => $this->employee_info['Company']['id']
					),
                    'fields' => array('id')
				));
				$project_id = array_values($project_id);
				
				$project_id = !empty($project_id) ? $project_id[0] : 0;
                $find = $this->ProjectAmr->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('project_id' => $project_id),
                    'fields' => array('id')
                ));
				$fieldAmr = array();
				$fieldAmr[$field] = $val;
				if(!empty($find)){
					$this->ProjectAmr->id = $find['ProjectAmr']['id'];
				}else{
					$this->ProjectAmr->create();
					$fieldAmr['project_id'] = $project_id;
				}                
                $this->ProjectAmr->save($fieldAmr);

                $this->Project->id = $this->data['project_id'];
                $this->Project->save(array(
                   'last_modified' => time(),
                   'update_by_employee' => $this->employee_info['Employee']['fullname']
                ));
                $flag = intval(1 && $project_id);
            }
            
        }
        die (json_encode($flag));
    }
	public function admin(){
		$widget_init = $this->_list_widget_init();
		$widget_init = !empty( $widget_init)?  Set::combine($widget_init, '{n}.widget', '{n}') : array();
        $widget_name = $this->_widget_name();
        $admin_setting = $this->admin_default_setting();
		$list_widgets = !empty($admin_setting[0]) ? $admin_setting[0] : $widget_init;
		foreach( $widget_init as $key => $val){
			if( empty ($list_widgets[$key]) ){
				$list_widgets[$key] = $val;
			}
			$list_widgets[$key]['name'] = $widget_name[$key];
		}
		$list_widgets = array_values($list_widgets);
		
		$this->set(compact('list_widgets'));
	}
	/**
     * index
     *
     * @return void
     * @access public
     */
    public function indicator($id){
		// Checkrole
		$this->_checkRole(false, $id);
        $this->_checkWriteProfile('indicator');
		$bg_currency = $this->getCurrencyOfBudget();
		$project_id = $id;
		$company_id = $this->employee_info['Company']['id'];
		$this->getAllProject($company_id, $project_id);
		$this->getPMOfProject($id);
		$employee_info = $this->employee_info;
		$project = $this->Project->find('first', array(
			'conditions' => array(
				'Project.company_id' => $employee_info['Company']['id'],
				'Project.id' => $id,
			),
		));
		$wd_listEmployees = array($employee_info['Employee']['id']);
		$this->_getListEmployee(); // app_controller
		
		//Favorite project.
		$favorites = $this->getFavoritebyEmployee($this->employee_info['Employee']['id'], $project_id);
		
		//-------------------------------------------//
		// Get Layout setting 
		$admin_setting = $this->admin_default_setting(true); // true: for index screen / false: for admin screen 
		
		$list_widgets = $admin_setting[0];
		
		$task_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => 'id',
        ));
		$this->loadModels('Translation', 'TranslationSetting', 'ProjectStatus', 'Employee', 'ProjectAmrProgram', 'ProjectPhasePlan');
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $company_id
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		$phase_end_dates = $this->ProjectPhasePlan->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_id' => $project_id),
			'fields' => array('phase_planed_end_date', 'phase_real_end_date')
		));
		// debug($phase_end_dates); 
		$phase_plan_end_date = 0;
		$phase_real_end_date = 0;
		foreach( $phase_end_dates as $p_d => $r_d){
			$p_d = strtotime($p_d);
			$r_d = strtotime($r_d);
			if( $p_d > $phase_plan_end_date) $phase_plan_end_date = $p_d;
			if( $r_d > $phase_real_end_date) $phase_real_end_date = $r_d;
		} 
		$project_progress = $this->Project->caculateProgress($project_id, $this->companyConfigs, $company_id);
		if( $project_progress ) $project_progress = $project_progress[$project_id]['Completed'];
		$this->set(compact('adminTaskSetting', 'phase_plan_end_date', 'phase_real_end_date', 'project_progress'));
		// END Get Layout setting
		//-------------------------------------------//
		
		foreach($list_widgets as $widget){  
			if( $widget['display'] ) {
				// project_synthesis
				if( $widget['widget'] == 'project_synthesis'){
					if( empty($logGroups)){
						list($logGroups, $_listEmployee) = $this->getLogSystems($project_id);
						$wd_listEmployees = array_merge( $wd_listEmployees , $_listEmployee);
						$synthesis_column = $this->filterColumnSynthesis();
						$this->set(compact('logGroups', 'synthesis_column'));
						
					}
					$model_display = array_filter( $list_widgets['project_synthesis']['options']); // remove empty fields
					//Ticket #499
					$a = array();
					foreach($model_display as $index => $model_val){
						if(!empty($model_val['model_display'])){
							$a[$model_val['model']] = $model_val['model_display'];
						}
					}
					$model_display = $a;
					//Done ticket #499
					$this->set(compact('model_display'));
					
				}elseif($widget['widget'] == 'project_status'){
					if( empty($logGroups)){
						list($logGroups, $_listEmployee) = $this->getLogSystems($project_id);
						$wd_listEmployees = array_merge( $wd_listEmployees , $_listEmployee);
						$this->set(compact('logGroups'));
						
					}
					$stt_models = !empty($widget['options']) ? array_filter( $widget['options']) : array();
					$projectWeatherStatus = $this->Project->ProjectAmr->find('all', 
						array(
							'fields' => array(
								'ProjectAmr.scope_weather as Scope', 
								'ProjectAmr.schedule_weather as Schedule', 
								'ProjectAmr.budget_weather as Budget', 
								'ProjectAmr.resources_weather as Resources', 
								'ProjectAmr.technical_weather as Technical'
							), 
							'conditions' => array('Project.id' => $project_id)
						)
					);
					$this->set(compact('stt_models', 'projectWeatherStatus'));
				}elseif($widget['widget'] == 'project_pictures'){
					$this->loadModel('ProjectGlobalView');
					$this->loadModel('ProjectImage');
					$projectGlobalView = $this->ProjectGlobalView->find("first", array(
						'recursive' => -1, 
						'fields' => array('id', 'attachment','is_file', 'is_https'),
						"conditions" => array('project_id' => $project_id)
					));
					$projectGlobalViewExists = true;
					if ($projectGlobalView) {
						$link = trim($this->_getPathGlobalView($project_id) . $projectGlobalView['ProjectGlobalView']['attachment']);
						if (!file_exists($link) || !is_file($link)) {
							$projectGlobalViewExists = false;
						}
					}

					$projectImage = $this->ProjectImage->find('all', array(
						'recursive' => -1,
						'conditions' => array(
						'project_id' => $project_id,
							'type' => array('image', 'application')
						)
					));
					$this->set(compact('projectGlobalView','projectGlobalViewExists', 'projectImage'));
				}elseif($widget['widget'] == 'project_location'){
					// Do no thing 
					// $projectName đã được lấy ở _checkRole()
				}elseif($widget['widget'] == 'project_milestones'){
					
					$projectMilestones = $project['ProjectMilestone'];
					$currentDate = strtotime(date('d-m-Y', time()));
					$miles_first_late = array();
					$miles_next_future = array();
					$miles_first_date_late = '';
					foreach ($projectMilestones as $p) { 
						if(($p['milestone_date'] != '0000-00-00') && ($p['milestone_date'] != '')){
							$milestone_date = strtotime($p['milestone_date']);
							if((!$p['validated']) && ($milestone_date <= $currentDate)) {
								if( empty($miles_first_late) ) $miles_first_late = $p;
								$miles_first_date_late = strtotime($miles_first_late['milestone_date']);
								if( $miles_first_date_late > $milestone_date) $miles_first_late = $p;
							}
							if ($milestone_date > $currentDate) {
								if( empty($miles_next_future) ) $miles_next_future = $p;
								$miles_next_future_date = strtotime($miles_next_future['milestone_date']);
								if( $miles_next_future_date > $milestone_date) $miles_next_future = $p;
							}
						}
					} 
					// debug( compact('projectMilestones', 'miles_first_late', 'miles_next_future')); exit;
					$this->set(compact('projectMilestones', 'miles_first_late', 'miles_next_future'));
				}elseif($widget['widget'] == 'indicator_assign_object'){
					//assign
					$listAssign = $this->_getEmployeeAssign($project_id);
					$list_employee_manager = $project['ProjectEmployeeManager'];
					$list_employee_manager = !empty($list_employee_manager) ? Set::classicExtract($list_employee_manager, '{n}.project_manager_id') : array();
					$list_employee_manager[] = $project['Project']['project_manager_id'];
					$list_employee_manager = !empty($list_employee_manager) ? array_unique($list_employee_manager) : array();
					$this->set(compact('listAssign', 'list_employee_manager'));
					//object
					// Do no thing 
					// $projectName đã được lấy ở _checkRole()
				}elseif($widget['widget'] == 'project_progress_line'){
					$this->progress_line($project_id);
				}elseif($widget['widget'] == 'project_messages'){
					$this->z0g_messages($project_id);
				}elseif($widget['widget'] == 'project_risk'){
					// Do no thing 
				}elseif($widget['widget'] == 'project_created_value'){
					$this->_get_create_value($project_id);	
				}elseif($widget['widget'] == 'project_budget'){
					$default = array(
						0 => array(
							'model' => 'inv',
							'model_display' => 1
						),
						1 => array(
							'model' => 'fon',
							'model_display' => 1
						),
						2 => array(
							'model' => 'finaninv',
							'model_display' => 1
						),
						3 => array(
							'model' => 'finanfon',
							'model_display' => 1
						)
					);
					
					$types = isset($widget['options']) ? $widget['options'] :  $default;
					$budget_types = Set::combine($types, '{n}.model', '{n}.model_display');
					
					$this->_get_finance_value($project_id, $budget_types);
				}elseif($widget['widget'] == 'project_gantt'){ // Viet
					
				}elseif($widget['widget'] == 'project_task'){
					$this->wd_project_tasks($project_id);
				
				}elseif($widget['widget'] == 'project_synthesis_budget'){
					$this->loadModels('Menu');
					$menuBudgets = $this->Menu->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $company_id,
							'controllers' => array('project_budget_internals', 'project_budget_externals', 'project_budget_synthesis'),
							'functions' => 'index',
							'model' => 'project',
							// 'display' => 1
						),
						'order' => array('id DESC'),
						'fields' => array( 'controllers', 'display')
					));
					if( empty( $menuBudgets['project_budget_internals'] )){
						unset( $list_widgets['project_synthesis_budget']['model']['BudgetInternal']);
					}
					if( empty( $menuBudgets['project_budget_externals'] )){
						unset( $list_widgets['project_synthesis_budget']['model']['BudgetExternal']);
					}
					if( empty( $menuBudgets['project_budget_synthesis'] )){
						unset( $list_widgets['project_synthesis_budget']['model']['SynthesisBudget']);
					}
					$this->wd_project_synthesis_budget($project_id);
				
				}
			}
		}
		$wd_listEmployees = array_unique($wd_listEmployees);
		$listEmployeeNames = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $wd_listEmployees),
			'fields' => array('id', 'fullname'),
			'order' => array('Employee.id')
		));
		// checkPPM dung de kiem tra User co phai la Profile project manager. Ticket #395, update by QuanNV 14/06/2019
		$checkPPM = array();
		$checkPPM = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $employee_info['Employee']['id']),
			'fields' => array('profile_account')
		));
		// 	End update.
		$this->set('projectArmPrograms', $this->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id ' => $company_id))));
		
		// Project budget systhyns
		$this->loadModels('ProjectBudgetSyn', 'Menu');
		$this->loadModels('CustomerLogo');
		$listLogo = $this->CustomerLogo->getListCustomerLogo();
		$employee_logo = $this->Employee->find('list', array(
			'recurisve' => -1,
			'conditions' => array(
				'id' => $this->employee_info['Employee']['id'],
			),
			'fields' => array('logo_id', 'logo_id')
		));
		$employee_logo_id = 0;
		if(!empty($listLogo)){
			$employee_logo_id = $listLogo[0]['id'];
			if(!empty($employee_logo)){
				foreach($listLogo as $index => $_logo){
					$logo_id = $_logo['id'];
					if(!empty($employee_logo[$logo_id])) $employee_logo_id = $logo_id;
				}
			}
		}
		$this->set(compact('project','list_widgets', 'project_id', 'listEmployeeNames','checkPPM', 'bg_currency', 'favorites', 'employee_logo_id'));
	}
	public function history_height_input_add_comment() {
        $this->loadModel('HistoryFilter');
        $filter_synthesis = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
            'conditions' => array(
                'path' => 'project_synthesis_height_input_add_comment',
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
		$saved = array(
			'employee_id' => $this->employee_info['Employee']['id'],
			'path' => 'project_synthesis_height_input_add_comment',
			'created' => time(),
			'updated' => time()
		);
        if( empty($filter_synthesis) ){
			if(!empty($this->data)){
				$columns = $this->data['params'];
				$saved['params'] =  $this->data['params'];
				$this->HistoryFilter->create();
			}
        }else{
			$filter_synthesis = $filter_synthesis['HistoryFilter'];
			$columns = $filter_synthesis['params'];
			if(!empty($this->data)){
				$columns = $this->data['params'];
				$this->HistoryFilter->id = $filter_synthesis['id'];
				$saved['params'] =  $this->data['params'];
				$saved['created'] =  $filter_synthesis['created'];
			}
		}
		if(!empty($this->data)){
			if($this->HistoryFilter->save($saved)){
				die('OK');
			}
		}
		die('KO');
		
    }
	public function filterColumnSynthesis() {
        $this->loadModel('HistoryFilter');
        $filter_synthesis = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
            'conditions' => array(
                'path' => 'project_synthesis_column',
                'employee_id' => $this->employee_info['Employee']['id']
            )
        ));
		$columns = 4;
		$saved = array(
			'employee_id' => $this->employee_info['Employee']['id'],
			'path' => 'project_synthesis_column',
			'created' => time(),
			'updated' => time()
		);
        if( empty($filter_synthesis) ){
			if(!empty($this->data)){
				$columns = $this->data['params'];
				$saved['params'] =  $this->data['params'];
				$this->HistoryFilter->create();
			}
        }else{
			$filter_synthesis = $filter_synthesis['HistoryFilter'];
			$columns = $filter_synthesis['params'];
			if(!empty($this->data)){
				$columns = $this->data['params'];
				$this->HistoryFilter->id = $filter_synthesis['id'];
				$saved['params'] =  $this->data['params'];
				$saved['created'] =  $filter_synthesis['created'];
			}
		}
		if(!empty($this->data)){
			$this->HistoryFilter->save($saved);
		}
		if(!empty($this->params['isAjax'])) die($columns);
        return $columns;
    }
	function getFavoritebyEmployee($employee_id, $project_ids=null){
		$this->loadModel('MFavorite');
		$fav =  $this->MFavorite->get_favorite_by_employee($employee_id, $project_ids);
		return $fav;
	}
		
	private function getAllProject($company_id, $project_id){
		$this->loadModels('Project', 'ProjectEmployeeManager', 'CompanyConfig');
		// debug( $this->projectName); exit;
		$role = $this->employee_info['Role']['name'];
		$list_projects = array();
		
		if($role == 'admin' || $role == 'pm'){
			$conditions = array(
				'company_id' => $company_id,
				'OR' => array(
					'category' => 1,
					'id' => $project_id
				)
			);
			$see_all_projects = $this->employee_info['CompanyEmployeeReference']['see_all_projects'];
			$company_config_see_all_projects = $this->CompanyConfig->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'company' => $company_id,
					'cf_name' => 'see_all_projects',
					'cf_value' => 1,
				)
			));

			if($role == 'pm' && $see_all_projects != 1 && $company_config_see_all_projects == 0){
				$employee_id = $this->employee_info['Employee']['id'];
				$pm_project = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_manager_id' => $employee_id,
					),
					'fields' => array('id', 'id'),
				));
				$pm_project_refer = $this->ProjectEmployeeManager->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_manager_id' => $employee_id,
					),
					'fields' => array('project_id', 'project_id'),
				));
				
				$list_project_id = array_unique(array_merge($pm_project, $pm_project_refer, array($project_id)));
				
				$conditions['id'] = $list_project_id;
			}
			$list_projects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => array('project_name' => 'ASC'),
				'fields' => array('id', 'project_name'),
			));
		}
	
		$this->set(compact('list_projects'));
	}
	function getPMOfProject($project_id){
		$pmProject =  $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('project_manager_id', 'project_manager_id'),
		));
		
		$pmProjectRefer = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'type' => 'PM'
            ),
            'fields' => array('project_manager_id', 'project_manager_id'),
        ));
		
		$pmProject = array_unique(array_merge($pmProject, $pmProjectRefer));
		if(!empty($pmProject)){
			$list_pm_project =  $this->Employee->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $pmProject,
				),
				'fields' => array('id', 'CONCAT(Employee.first_name, " ", Employee.last_name) AS full_name'),
			));
		}
		$list_pm_project = !empty($list_pm_project) ? Set::combine($list_pm_project, '{n}.Employee.id', '{n}.0.full_name') : array();
		
		$this->set(compact('list_pm_project'));
	}
	// Created by Huynh Le
	// Get data for project_synthesis_budget widget
	
	private function wd_project_synthesis_budget($project_id){
		$this->loadModels('ProjectBudgetSyns', 'BudgetSetting');
		
		$company_id = $this->employee_info['Company']['id'];
		$currency_budget = $this->BudgetSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' =>$company_id ,
				'currency_budget' =>1,
			),
			'fields' => array('name',)
		));
		$currency_budget = !empty($currency_budget['BudgetSetting']['name']) ? $currency_budget['BudgetSetting']['name'] : '&euro;';
		// PIE
		$this->loadModel('ProjectBudgetSyn');
		$valBudgetSyns = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
		// debug( $valBudgetSyns ); exit;
		$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? intval($this->companyConfigs['manual_consumed']) : 0;
		if( $useManualConsumed){
			$getDataProjectTasks = $this->Project->dataFromProjectTaskManualConsumed($project_id);
			$externalConsumeds = $getDataProjectTasks['exConsumed'];
			$totalAverage = $valBudgetSyns['internal_costs_average'];
			$engagedErro = $getDataProjectTasks['consumed'] * $totalAverage;
			$remainEuro = ($getDataProjectTasks['remain'])*$totalAverage;
			$forecastEuro = $engagedErro + $remainEuro;
			$valBudgetSyns['internal_costs_forecast'] = $forecastEuro;
			$valBudgetSyns['internal_costs_engaged'] = $engagedErro;
			$valBudgetSyns['internal_costs_remain'] = $remainEuro;
		}else{
			$getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $company_id);
		}
		// $projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
			// 'recursive' => -1,
			// 'conditions' => array(
				// 'project_id' => $project_id
			// ),
			// 'fields' => array('internal_costs_budget_man_day','internal_costs_forecasted_man_day', 'internal_costs_budget', 'external_costs_budget', 'internal_costs_forecast', 'external_costs_forecast')
		// ));
		// Line
		// Load Ajax late
		$this->set( compact('valBudgetSyns', 'currency_budget', 'getDataProjectTasks'));
	}
	// Created by Viet Nguyen
	// Get data for gant+
	
	public function wd_project_gantt($project_id, $type = 'month'){
		$this->loadModels('ProjectTask');
        $this->phase_vision($project_id, null);
		$this->set(compact('type'));
		$this->render('/project_tasks_preview/gantt_chart');
	
	}
	
	// Created by Viet Nguyen
	// Get data for Task
	public function wd_project_tasks($project_id){
		
		$admin_setting = $this->admin_default_setting(true);
		$slider_active = 0;
		if(!empty($admin_setting)){
			$list_widgets = !empty($admin_setting[0]) ? $admin_setting[0] : $widget_init;
			// debug( $admin_setting); exit;
			$slider_active = $admin_setting[1];
			$model_display = $admin_setting[2];
		}
		$this->loadModel('ProjectStatus');
		$task_status = $this->ProjectStatus->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
			),
			'order' => array('weight' => 'ASC'),
			'fields' => 'id',
		));
		$data_status = array();
		foreach ($list_widgets as $key => $widget) {
			if($widget['widget'] == 'project_task' && !empty($widget['task_status'])){
				foreach ($widget['task_status'] as $id => $display) {
					if($display){
						$data_status[$id] = $id;
					}
				}
			}
		}
		$widget_init = !empty( $widget_init)? Set::combine($widget_init, '{n}.widget', '{n}') : array();

		$list_widgets = array_values($list_widgets);
		$list_widgets = Set::sort($list_widgets, '{n}.row', 'asc');

		$data_status = !empty($data_status) ? $data_status : array();
		$this->project_task($project_id, $task_status);
        $checkAvatar = $this->checkAvatar($project_id);
		$projectStatusEX = $this->getStatusEX();
		/**
		* QuanNV . Update ticket 404.
		*/
		$this->loadModels ('Translation','TranslationSetting');
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $this->employee_info['Company']['id']
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		/*
		* End update ticket 404.
		*/
        $this->set(compact( 'checkAvatar', 'projectStatusEX', 'slider_active','adminTaskSetting'));
	
	}
	public function delete_comment(){
		$result = '';
		$data = array();
		if (!empty($_POST['log_id'])) {
			$cm_id = $_POST['log_id'];
			$this->loadModels('LogSystem');
			$this->LogSystem->id = $cm_id;
			$comment = $this->LogSystem->read();
			if( empty($comment)) die(json_encode($data));
			$project_id = @$comment['LogSystem']['model_id'];
			$check = $this->_isBelongToCompany($project_id, 'Project');
			if( $check && $comment && (( $this->employee_info['Role']['name'] == 'admin') || ($comment['LogSystem']['employee_id'] == $this->employee_info['Employee']['id']))){
				if($this->LogSystem->delete()){
					$result = "success";
				}
				$logSystems = $this->LogSystem->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'model_id' => @$comment['LogSystem']['model_id'],
						'model' => @$comment['LogSystem']['model'],
					),
					// 'limit' => 3,
					'fields' => array('*'),
					'order' => array('updated' => 'DESC')
				));
				$data = !empty($logSystems) ? $logSystems['LogSystem'] : array();
			}
		}
        die(json_encode($data));
	}
}
?>
