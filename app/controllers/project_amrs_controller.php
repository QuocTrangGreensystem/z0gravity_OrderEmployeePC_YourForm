<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAmrsController extends AppController {

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt', 'GanttSt');


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
    function _parseParams() {
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
        // if( !empty($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
        //     $this->redirect(array('controller' => 'project_amrs_preview','action' => 'index_plus/'. $id));
        // }
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
            "fields" => array("Project.project_name", 'updated', 'last_modified', 'update_by_employee', 'id', 'company_id'),
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
        $this->set('budget_settings',$budget_settings);
        //END
    }

    /**
     *  Save/edit Log System
     */
    public function update_data_log(){

        $this->loadModel('Project');
        $this->layout = false;
        $result = array();
		
        if(!empty($this->data)){
			$this->data['name'] = $this->data['name'].''.date('h:i d/m/Y',time());
            $result = $this->LogSystem->saveLogSystem($this->data);
            echo json_encode($result);
        }
        $this->Project->id = $this->data['project_id'];
        $this->Project->save(array(
           'last_modified' => time(),
           'update_by_employee' => $this->employee_info['Employee']['fullname']
        ));
        exit;
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
        // debug($_activityIdProject);
        // debug($projectName['Project']['company_id']);
        // debug($activityRequests);
        $_engaged = $_progression = 0;
        //debug($activityRequests[0][0]['consumed']);
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged+$sumRemain;

        // debug($validated);
        // debug($_engaged); exit;
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
        $this->set(compact('activity_id', 'sales', 'internalDetails', 'internals', 'externals'));
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
            $this->redirect(array('action' => 'index_plus'));
        }
    }
    public function updateWeather(){
        $this->loadModel('Project');
        if(!empty($this->data)){
            $field = $this->data['field'];
            $field = str_replace('data[ProjectAmr][', '', $field);
            $field = str_replace('][]', '', $field);
            $find = $this->ProjectAmr->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $this->data['project_id']),
                'fields' => array('id')
            ));
            $id = !empty($find) ? $find['ProjectAmr']['id'] : 0;
            $this->ProjectAmr->id = $id;
            $this->ProjectAmr->save(array(
                $field => $this->data['value']
            ));

            $this->Project->id = $this->data['project_id'];
            $this->Project->save(array(
               'last_modified' => time(),
               'update_by_employee' => $this->employee_info['Employee']['fullname']
            ));
        }
        die;
    }
}
?>
