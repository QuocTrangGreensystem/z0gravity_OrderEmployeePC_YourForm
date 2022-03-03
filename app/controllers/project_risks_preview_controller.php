<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectRisksPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */

    var $name = 'ProjectRisksPreview';
    var $uses = array('ProjectRisk', 'ProjectTeam');

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'LogSystem');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
    }
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array());
        //$this->_checkRole(false, $project_id);
        //$projectName = $this->viewVars['projectName'];
        $this->_checkWriteProfile('risk');
        $this->loadModels('Project', 'ProjectRisk', 'ProjectRiskSeverity', 'ProjectRiskOccurrence', 'UserLastUpdated', 'HistoryFilter');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $this->ProjectRisk->Behaviors->attach('Containable');
        $this->ProjectRisk->cacheQueries = true;

        $projectRisks = $this->ProjectRisk->find("all", array(
            'fields' => array('id', 'project_risk', 'risk_assign_to', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_close_date', 'actions_manage_risk', 'file_attachement', 'format', 'project_issue_status_id', 'weight'),
            'recursive' => -1, "conditions" => array('project_id' => $project_id),
            'order' => array('weight' => 'ASC')
        ));

        $riskSeverities = $this->ProjectRiskSeverity->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_severity', 'value_risk_severitie'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            )
        ));
        $riskSeverities_color = !empty($riskSeverities) ? Set::combine($riskSeverities, '{n}.ProjectRiskSeverity.id', '{n}.ProjectRiskSeverity') : array();
        $riskSeverities = !empty($riskSeverities) ? Set::combine($riskSeverities, '{n}.ProjectRiskSeverity.id', '{n}.ProjectRiskSeverity.risk_severity') : array();

        $riskOccurrences = $this->ProjectRiskOccurrence->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_occurrence','value_risk_occurrence'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            )
        ));
        $riskOccurrences_color = !empty($riskOccurrences) ? Set::combine($riskOccurrences, '{n}.ProjectRiskOccurrence.id', '{n}.ProjectRiskOccurrence') : array();
        $riskOccurrences = !empty($riskOccurrences) ? Set::combine($riskOccurrences, '{n}.ProjectRiskOccurrence.id', '{n}.ProjectRiskOccurrence.risk_occurrence') : array();

        $this->loadModel('ProjectTeam');
        $this->loadModels('LogSystem', 'Menu', 'Employee');
        $this->ProjectTeam->Behaviors->attach('Containable');
        // checkDisplayProjectTeam. kiem tra project team co hien thi hay k.
        $checkDisplayProjectTeam = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id'],
                'controllers' => 'project_teams',
                'functions' => 'index',
                'widget_id' => 'team',
                'model' => 'project',
                // 'display' => 1
            ),
            'order' => array('id DESC')
        ));
         if(!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1){
            $employees = $this->ProjectTeam->find('all', array(
                'fields' => array('id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id')),
                'conditions' => array('project_id' => $project_id)));
            $employees = array_filter(Set::classicExtract($employees, '{n}.ProjectFunctionEmployeeRefer.0.employee_id'));
            $employees = $this->ProjectRisk->Employee->find('all', array(
                'order' => array('Employee.last_name' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_merge($employees, array($projectName['Project']['project_manager_id']))
                ),
                'fields' => array('first_name', 'last_name', 'id')));

            $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
            $employees = array($projectName['Project']['project_manager_id'] =>
                $employees[$projectName['Project']['project_manager_id']]) + $employees;
        // else thuc hien doan sau.
        } else {
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname'),
                'conditions' => array(
                    'company_id' => $projectName['Project']['company_id'],
                    'actif' => 1,
                     'OR' => array(
                        array('end_date' => '0000-00-00'),
                        array('end_date IS NULL'),
                        array('end_date >=' => date('Y-m-d')),
                    )
                )
            ));
        }
            
        /**
         * Ten cong ty
         */
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $companyName = strtolower($this->employee_info['Company']['company_name']);
        $company_id = $this->employee_info['Company']['id'];
        $avatarEmployeeLogin = $this->employee_info['Employee']['avatar_resize'];
        $this->loadModel('ProjectAmr');
        $projectAmrs = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array('id', 'project_amr_risk_information')
        ));
        if(!empty($projectAmrs) && !empty($projectAmrs['ProjectAmr']['project_amr_risk_information'])){
            $updated = !empty($projectName['Project']['updated']) ? date('H:i d/m/Y', $projectName['Project']['updated']) : '../../....';
            $byEmployee = !empty($projectName['Project']['update_by_employee']) ? $projectName['Project']['update_by_employee'] : 'N/A';
            $employOldId = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array('fullname' => $byEmployee),
                'fields' => array('id')
            ));
            $saveOldLogs = array(
                'company_id' => $company_id,
                'model' => 'ProjectRisk',
                'model_id' => $projectName['Project']['id'],
                'name' => $byEmployee . ' ' . $updated,
                'description' => $projectAmrs['ProjectAmr']['project_amr_risk_information'],
                'employee_id' => !empty($employOldId) && $employOldId['Employee']['id'] ? $employOldId['Employee']['id'] : '',
                'update_by_employee' => $byEmployee
            );
            $this->LogSystem->create();
            if($this->LogSystem->save($saveOldLogs)){
                $this->ProjectAmr->id = $projectAmrs['ProjectAmr']['id'];
                $this->ProjectAmr->save(array('project_amr_risk_information' => ''));
            }
        }
        /**
         * Lay tat ca cac log
         */
        $logSystems = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ProjectRisk',
                'model_id' => $project_id,
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'name', 'description', 'employee_id', 'created'),
            'order' => array('updated' => 'DESC')
        ));
        $listEmployeeLogs = !empty($logSystems) ? array_unique(Set::classicExtract($logSystems, '{n}.LogSystem.employee_id')) : array();
        /**
         * Get Status Isses
         */
        $this->loadModel('ProjectIssue');
        $issueStatus = $this->ProjectIssue->ProjectIssueStatus->find('list', array(
            'fields' => array('id', 'issue_status'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
                )));

         // last updated project risk
        $risk_last_updated = $this->UserLastUpdated->find('all', array(
            'recursive' => -1,
            'fields' => array('updated', 'employee_id', 'path'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'path LIKE \'%project_risks_preview/index/'.$project_id .'%\'',
            ),
        ));
        $employee_update = $this->_getListEmployee(null, true);

        $risk_action_update = array();
        if(!empty($risk_last_updated)){
            foreach ($risk_last_updated as $key => $value) {
                $risk_id = explode('/', $value['UserLastUpdated']['path']);
                if(!empty($risk_id[3])){
                    $risk_action_update[$risk_id[3]]['employee_name'] = '';
                    $risk_action_update[$risk_id[3]] = $value['UserLastUpdated'];
                }
            }
        }
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('risk_action_update','employee_update', 'issueStatus', 'company_id', 'employeeLoginId', 'avatarEmployeeLogin', 'employeeLoginName', 'companyName', 'logSystems', 'projectName', 'riskSeverities', 'riskSeverities_color', 'riskOccurrences', 'riskOccurrences_color', 'employees', 'project_id', 'projectRisks', 'loadFilter'));
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectRisk->id = $id;
                $this->ProjectRisk->save(array(
                    'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     *  Save/edit Log System
     */
    public function update_data_log(){
        $this->layout = false;
        $result = '';
        $data = $_POST;
		if(!empty($data['id'])){
			unset($data['created']);
		}else{
			$data['created'] = time();
		}
        $data['name'] = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
        $data['company_id'] = $this->employee_info['Company']['id'];
        $data['employee_id'] = $this->employee_info['Employee']['id'];
        $data['update_by_employee'] = $this->employee_info['Employee']['fullname'];
        $data['updated'] = time();
        if($data){
            if(!empty($data['id']) && $data['id'] == -1){
                unset($data['id']);
            }
            $result = $this->LogSystem->saveLogSystem($data);
            $result['LogSystem']['created'] = date('d M Y', $result['LogSystem']['created']);
            $result['LogSystem']['date_updated'] = date('d M Y', $result['LogSystem']['updated']);
        }
        echo json_encode($result);
        exit;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $this->loadModel('UserLastUpdated');
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $is_udpate_actions_manage_risk = $this->is_udpate_actions_manage_risk($this->data['id'], $this->data['actions_manage_risk']);
            $this->ProjectRisk->create();
            if (!empty($this->data['id'])) {
                $this->ProjectRisk->id = $this->data['id'];
            }
            $data = array();
            foreach (array('risk_close_date') as $key) {
                if (isset($this->data[$key])) {
                    $data[$key] = $this->ProjectRisk->convertTime($this->data[$key]);
                }
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectRisk->save(array_merge($this->data, $data))) {
                    $result = true;
                } else {
                    $this->Session->setFlash(__('The Risk could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectRisk->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        if($result){
            if($is_udpate_actions_manage_risk){
                $risk_last_updated = $this->UserLastUpdated->find('first', array(
                    'fields' => array('id', 'employee_id'),
                    'conditions' => array(
                        'UserLastUpdated.company_id' => $this->employee_info['Company']['id'],
                        'UserLastUpdated.path' => 'project_risks_preview/index/'. $this->data['project_id'] .'/'. $this->data['id'],
                    ),
                ));
                if(!empty($risk_last_updated)){
                    $this->UserLastUpdated->id = $risk_last_updated['UserLastUpdated']['id'];
                    $this->UserLastUpdated->save(array(
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'updated' => time()
                    ));
                }else{
                   $this->UserLastUpdated->create();
                    $this->UserLastUpdated->save(array(
                        'id' => $this->UserLastUpdated->id,
                        'company_id' => $this->employee_info['Company']['id'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'path' => 'project_risks_preview/index/'. $this->data['project_id'] .'/'. $this->data['id'],
                        'action' => '',
                        'created' => time(),
                        'updated' => time()
                    )); 
                }
            }
        }
        $this->set(compact('result'));
    }
    private function is_udpate_actions_manage_risk($id = null, $actions_manage_risk = ''){
        if(!empty($id)){
            $project_risk =  $this->ProjectRisk->find("first", array(
                'recursive' => -1,
                "fields" => array("actions_manage_risk"),
                'conditions' => array('id' => $id)
            ));
            if($project_risk['ProjectRisk']['actions_manage_risk'] == $actions_manage_risk ){
                return false;
            }
            return true;
        }
    }
    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $conditions = array('ProjectRisk.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectRisk.id'] = $data;
            }
        }

        $this->ProjectRisk->Behaviors->attach('Containable');
        $this->ProjectRisk->cacheQueries = true;

        $projectRisks = $this->ProjectRisk->find("all", array(
            'fields' => array('id', 'project_risk', 'risk_assign_to', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_close_date', 'actions_manage_risk', 'project_issue_status_id'),
            'contain' => array(
                'ProjectRiskSeverity' => array(
                    'id', 'risk_severity'
                ),
                'ProjectRiskOccurrence' => array(
                    'id', 'risk_occurrence'
                ),
                'Employee' => array(
                    'id', 'first_name', 'last_name'
                ),
                'ProjectIssueStatus' => array(
                    'id', 'issue_status'
                ),
            ),
            "conditions" => $conditions));
        $projectRisks = Set::combine($projectRisks, '{n}.ProjectRisk.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectRisks[$id])) {
                    unset($data[$id]);
                    unset($projectRisks[$id]);
                    continue;
                }
                $data[$id] = $projectRisks[$id];
            }
            $projectRisks = $data;
            unset($data);
        }
        $this->set(compact('projectRisks', 'projectName'));
        $this->layout = '';
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project risk', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectRisk', $this->ProjectRisk->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectRisk->create();
            if ($this->ProjectRisk->save($this->data)) {
                $this->Session->setFlash(__('The project risk has been saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The project risk could not be saved. Please, try again.', true), 'error');
            }
        }
        $projects = $this->ProjectRisk->Project->find('list');
        $projectRiskSeverities = $this->ProjectRisk->ProjectRiskSeverity->find('list');
        $projectRiskOccurrences = $this->ProjectRisk->ProjectRiskOccurrence->find('list');
        $this->set(compact('projects', 'projectRiskSeverities', 'projectRiskOccurrences'));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project risk', true));
            $this->redirect(array('action' => 'index', $this->data["ProjectRisk"]["project_id"]));
        }
        if (!empty($this->data)) {
            $this->data["ProjectRisk"]["risk_close_date"] = $str_utility->convertToSQLDate($this->data["ProjectRisk"]["risk_close_date"]);
            if ($this->ProjectRisk->save($this->data)) {
                $this->Session->setFlash(sprintf(__('The project risk %s has been saved.', true), '<b> ' . $this->data["ProjectRisk"]["project_risk"] . ' </b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectRisk"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project risk could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('action' => 'index', $this->data["ProjectRisk"]["project_id"]));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectRisk->read(null, $id);
        }
        $projects = $this->ProjectRisk->Project->find('list');
        $projectRiskSeverities = $this->ProjectRisk->ProjectRiskSeverity->find('list');
        $projectRiskOccurrences = $this->ProjectRisk->ProjectRiskOccurrence->find('list');
        $this->set(compact('projects', 'projectRiskSeverities', 'projectRiskOccurrences'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $project_id = null) {
		$result = false;
		$message = '';
        if (!$id) {
            $message = __('Invalid id for project risk', true);
        }
		$item = $this->ProjectRisk->find('first', array(
			'recursive' => -1,
			'fields' => array('id', 'project_id','file_attachement'),
			'conditions' => array('ProjectRisk.id' => $id)
		));
		$project_id = !empty( $item) ? $item['ProjectRisk']['project_id'] : 0;
        if (!empty( $item) && $this->_checkRole(false, $project_id)) {
            if ($result = $this->ProjectRisk->delete($id)) {
                @unlink(trim($this->_getPath($item['ProjectRisk']['project_id'])
                        . $item['ProjectRisk']['file_attachement']));
                $message =  __('Deleted', true);
            }
        }else{
			$message = __('Not deleted', true);
		}
		$this->_functionStop($result, $id, $message, false, array('action' => 'index', $project_id));
    }

    /**
     * loadTextarea
     *
     * @return void
     * @access public
     */
    function loadTextarea($project_issue_id = null) {
        $this->autoRender = false;
        if ($project_issue_id != "") {
            $record = $this->ProjectRisk->find('first', array('conditions' => array('ProjectRisk.id' => $project_issue_id)));
            if (!empty($record)) {
                echo $record['ProjectRisk']['actions_manage_risk'];
            } else {
                echo "";
            }
        }
        else
            echo "";
        exit;
    }

    /**
     * check_duplicate
     * Check duplicate exists
     *
     * @param int $project_id, $project_risk
     * @return void
     * @access public
     */
    function check_duplicate() {
        $project_id = $_POST['project_id'];
        $project_risk = $_POST['project_risk'];
        $check = $this->ProjectRisk->find('count', array('conditions' => array(
                "ProjectRisk.project_id" => $project_id,
                "ProjectRisk.project_risk" => $project_risk,
                )));
        echo $check;
        exit;
    }

    /**
     * exportExcel
     * Export to Excel
     *
     * @param int $project_id
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $this->set('columns', $this->name_columna);
        $this->paginate = array("conditions" => array('Project.id' => $project_id));
        $this->set('projectRisks', $this->paginate());
        $this->set("project_id", $project_id);
        $this->set('projectName', $this->ProjectRisk->Project->find("first", array("fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
    }

    public function dashboard($project_id){
        $this->loadModels('ProjectRiskSeverity', 'ProjectRiskOccurrence');
        $projectRiskSeverities = $this->ProjectRisk->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'joins' => array(
                array(
                    'table' => 'project_risk_severities',
                    'alias' => 'ProjectRiskSeverity',
                    // 'type' => 'LEFT',
                    'conditions' => array('ProjectRisk.project_risk_severity_id = ProjectRiskSeverity.id')
                )
            ),
            'fields' => array('ProjectRisk.id', 'ProjectRisk.weight', 'ProjectRisk.project_risk', 'ProjectRisk.project_issue_status_id', 'ProjectRisk.project_risk_severity_id', 'ProjectRiskSeverity.value_risk_severitie'),
			'order' => 'weight'
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
		$i = 1;
        foreach ($projectRiskSeverities as $key => $projectRiskSeverity) {
			$r_id = $projectRiskSeverity['ProjectRisk']['id'];
			if( empty($projectRiskSeverity['ProjectRisk']['weight'])) $projectRiskSeverity['ProjectRisk']['weight'] = $i++;
            $projectDashBoard[$key] = $projectRiskSeverity['ProjectRisk'];
			$sev = !empty($typeRiskSeverities[$r_id]) ? $typeRiskSeverities[$r_id] : 0;
			$occ = !empty($typeRiskOccurrence[$r_id]) ? $typeRiskOccurrence[$r_id] : 0;
            $projectDashBoard[$key]['dashboard_type'] = $sev.'_'.$occ;
        }
        // $this->set(compact('projectDashBoard'));
        die(json_encode($projectDashBoard));
        exit;
    }
    public function dash_board($project_id){
        $this->loadModel('Project');
        $this->loadModel('Employee');
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id),
                'fields' => array('project_name', 'company_id')
            ));

        $risks = $this->ProjectRisk->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                    'OR' => array(
                        'risk_close_date' => '0000-00-00',
                        'risk_close_date >' => date('Y-m-d', time())
                    )
                ),
                'fields' => array('project_risk', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_assign_to')
            ));
        $listEmployee = !empty($risks) ? array_unique(Set::classicExtract($risks, '{n}.ProjectRisk.risk_assign_to')) : array();
        $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $listEmployee),
                'fields' => array('id', 'fullname')
            ));
        /**
         * Lay cac level risk severity
         * Muc 1: Forte
         * Muc 2: Moyenne
         * Muc 3: Faible
         */
        // Forte
        $forteSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%for%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%strong%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%high%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $forteSeverity = !empty($forteSeverity['ProjectRiskSeverity']) ? array($forteSeverity['ProjectRiskSeverity']['id'] => $forteSeverity['ProjectRiskSeverity']['risk_severity']) : array('1' => 'Forte');
        // Moyenne
        $moyenneSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%moy%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%ave%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%med%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $moyenneSeverity = !empty($moyenneSeverity['ProjectRiskSeverity']) ? array($moyenneSeverity['ProjectRiskSeverity']['id'] => $moyenneSeverity['ProjectRiskSeverity']['risk_severity']) : array('2' => 'Moyenne');
        // Faible
        $faibleSeverity = $this->ProjectRisk->ProjectRiskSeverity->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskSeverity.risk_severity LIKE \'%fai%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%low%\'',
                    'ProjectRiskSeverity.risk_severity LIKE \'%small%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_severity'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $faibleSeverity = !empty($faibleSeverity['ProjectRiskSeverity']) ? array($faibleSeverity['ProjectRiskSeverity']['id'] => $faibleSeverity['ProjectRiskSeverity']['risk_severity']) : array('3' => 'Faible');
        $riskSeverities = Set::pushDiff($forteSeverity, $moyenneSeverity);
        $riskSeverities = Set::pushDiff($riskSeverities, $faibleSeverity);
        /**
         * Lay cac level risk Occurrence
         * Muc 1: Forte
         * Muc 2: Moyenne
         * Muc 3: Faible
         */
        // Forte
        $forteOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%for%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%strong%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%high%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $forteOccur = !empty($forteOccur['ProjectRiskOccurrence']) ? array($forteOccur['ProjectRiskOccurrence']['id'] => $forteOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('1' => 'Forte');
        // Moyenne
        $moyenneOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%moy%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%ave%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%med%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $moyenneOccur = !empty($moyenneOccur['ProjectRiskOccurrence']) ? array($moyenneOccur['ProjectRiskOccurrence']['id'] => $moyenneOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('2' => 'Moyenne');
        // Faible
        $faibleOccur = $this->ProjectRisk->ProjectRiskOccurrence->find('first', array(
            'recursive' => -1,
            'conditions'=>array(
                'OR' => array(
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%fai%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%low%\'',
                    'ProjectRiskOccurrence.risk_occurrence LIKE \'%small%\''
                ),
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'risk_occurrence'),
            'limit' => 1,
            'order' => array('id' => 'ASC')
        ));
        $faibleOccur = !empty($faibleOccur['ProjectRiskOccurrence']) ? array($faibleOccur['ProjectRiskOccurrence']['id'] => $faibleOccur['ProjectRiskOccurrence']['risk_occurrence']) : array('3' => 'Faible');
        $riskOccurrences = Set::pushDiff($forteOccur, $moyenneOccur);
        $riskOccurrences = Set::pushDiff($riskOccurrences, $faibleOccur);

        $datas = array();
        foreach($riskOccurrences as $id => $riskOccurrence){
            foreach($risks as $risk){
                if($id == $risk['ProjectRisk']['project_risk_occurrence_id']){
                    $_datas = array(
                        'occur' => trim($riskOccurrence),
                        'severity' => $risk['ProjectRisk']['project_risk_severity_id'],
                        'name' => $risk['ProjectRisk']['project_risk'],
                        'assign' => !empty($employees[$risk['ProjectRisk']['risk_assign_to']]) ? $employees[$risk['ProjectRisk']['risk_assign_to']] : ''
                    );
                    $datas[trim($riskOccurrence)][] = $_datas;
                }
            }
        }
        $listSeverities = array();
        /**
         * Lay value Forte Occur
         */
        $forteOccur = array_values($forteOccur);
        $forteOccur = $forteOccur[0];
        /**
         * Lay value Moyenne Occur
         */
        $moyenneOccur = array_values($moyenneOccur);
        $moyenneOccur = $moyenneOccur[0];
        /**
         * Lay value Faible Occur
         */
        $faibleOccur = array_values($faibleOccur);
        $faibleOccur = $faibleOccur[0];
        /**
         * Lay value Forte Severity
         */
        $forteSeverity = array_values($forteSeverity);
        $listSeverities = array_merge($listSeverities, $forteSeverity);
        $forteSeverity = $forteSeverity[0];
        /**
         * Lay value Moyenne Severity
         */
        $moyenneSeverity = array_values($moyenneSeverity);
        $listSeverities = array_merge($listSeverities, $moyenneSeverity);
        $moyenneSeverity = $moyenneSeverity[0];
        /**
         * Lay value Faible Severity
         */
        $faibleSeverity = array_values($faibleSeverity);
        $listSeverities = array_merge($listSeverities, $faibleSeverity);
        $faibleSeverity = $faibleSeverity[0];
        $severities = array(
                $forteSeverity => array(140, 135, 130, 125, 120, 115, 110, 105),
                $moyenneSeverity => array(95, 90, 85, 80, 75, 70, 65, 60, 55),
                $faibleSeverity => array(50, 45, 40, 35, 30, 25, 20, 15, 10, 5)
            );
        $radius = array(
                $forteOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FF3333'
                        )
                    ),
                $moyenneOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFA953'
                        )
                    ),
                $faibleOccur => array(
                        $faibleSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        ),
                        $moyenneSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        ),
                        $forteSeverity => array(
                            'minRadius' => 20,
                            'maxRadius' => 20,
                            'color' => '#FFFF79'
                        )
                    ),
            );
        $resetDatas = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                foreach($data as $k => $vl){
                    foreach($riskSeverities as $id => $riskSeveritie){
                        $riskSeveritie = trim($riskSeveritie);
                        if($id == $vl['severity']){
                            $vl['severity'] = $severities[$riskSeveritie][$k];
                            $vl['minRadius'] = $radius[$key][$riskSeveritie]['minRadius'];
                            $vl['maxRadius'] = $radius[$key][$riskSeveritie]['maxRadius'];
                            $vl['color'] = $radius[$key][$riskSeveritie]['color'];
                            $resetDatas[$key][$riskSeveritie][] = $vl;
                        }
                    }
                }
            }
        }
        $results = array();
        if(!empty($resetDatas)){
            foreach($resetDatas as $resetData){
                foreach($resetData as $_datas){
                    foreach($_datas as $_data){
                        $results[] = $_data;
                    }
                }
            }
        }

        $endDatas = $series = array();
        foreach($results as $key => $result){
            if($result['occur'] == $faibleOccur){
                $endDatas[0]['occur'] = $faibleOccur;
                $endDatas[0]['SalesQ'.$key] = $result['severity'];
                $endDatas[0]['YoYGrowthQ'.$key] = 1;
                $endDatas[0]['minRadius'.$key] = $result['minRadius'];
                $endDatas[0]['maxRadius'.$key] = $result['maxRadius'];
                $endDatas[0]['name'.$key] = $result['name'];
                $endDatas[0]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[0]['occur'] = $faibleOccur;
            }
            if($result['occur'] == $moyenneOccur){
                $endDatas[1]['occur'] = $moyenneOccur;
                $endDatas[1]['SalesQ'.$key] = $result['severity'];
                $endDatas[1]['YoYGrowthQ'.$key] = 1;
                $endDatas[1]['minRadius'.$key] = $result['minRadius'];
                $endDatas[1]['maxRadius'.$key] = $result['maxRadius'];

                $endDatas[1]['name'.$key] = $result['name'];
                $endDatas[1]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[1]['occur'] = $moyenneOccur;
            }
            if($result['occur'] == $forteOccur){
                $endDatas[2]['occur'] = $forteOccur;
                $endDatas[2]['SalesQ'.$key] = $result['severity'];
                $endDatas[2]['YoYGrowthQ'.$key] = 1;
                $endDatas[2]['minRadius'.$key] = $result['minRadius'];
                $endDatas[2]['maxRadius'.$key] = $result['maxRadius'];

                $endDatas[2]['name'.$key] = $result['name'];
                $endDatas[2]['assign'.$key] = $result['assign'];
            } else {
                $endDatas[2]['occur'] = $forteOccur;
            }
            $series[$key]['occur'] = $result['occur'];
            $series[$key]['SalesQ'.$key] = $result['severity'];
            $series[$key]['YoYGrowthQ'.$key] = 1;
            $series[$key]['minRadius'.$key] = $result['minRadius'];
            $series[$key]['maxRadius'.$key] = $result['maxRadius'];
            $series[$key]['name'.$key] = $result['name'];
            $series[$key]['assign'.$key] = $result['assign'];
            $series[$key]['color'.$key] = $result['color'];
        }
        if(empty($endDatas)){
           $endDatas = array(
                0 => array(
                    'occur' => $faibleOccur,
                ),
                1 => array(
                    'occur' => $moyenneOccur,
                ),
                2 => array(
                    'occur' => $forteOccur,
                )
            );
        }
        $lenght = count($results);
        $this->set(compact('projectName', 'project_id', 'endDatas', 'lenght', 'series', 'listSeverities'));
     }

     /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($project_id = null) {
        $result = false;
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $project_id = __('Unknown', true);
            $projectRisks = $this->ProjectRisk->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectRisk.id' => $this->data['Upload']['id'])));
            if ($projectRisks) {
                $project_id = $projectRisks['ProjectRisk']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectRisk->id = $this->data['Upload']['id'];
                $last = $this->ProjectRisk->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->ProjectRisk->id)));
                if ($last && $last['ProjectRisk']['file_attachement']) {
                    unlink($path . $last['ProjectRisk']['file_attachement']);
                }
                if ($this->ProjectRisk->save(array(
                            'file_attachement' => $this->data['Upload']['url'],
                            'format' => 1
                        ))) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                    $result = true;
                } else {
                    $this->Session->setFlash(__('The url could not be uploaded.', true), 'error');
                }
            } else {
                App::import('Core', 'Folder');
                new Folder($path, true, 0777);
                if (file_exists($path)) {
                    $this->MultiFileUpload->encode_filename = false;
                    $this->MultiFileUpload->uploadpath = $path;
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,gzip,tgz,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm";
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                            , true), $path), 'error');
                }
                if (!empty($attachment)) {
                    $attachment = $attachment['attachment']['attachment'];
                    $this->ProjectRisk->id = $this->data['Upload']['id'];
                    $last = $this->ProjectRisk->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->ProjectRisk->id)));
                    if ($last && $last['ProjectRisk']['file_attachement']) {
                        unlink($path . $last['ProjectRisk']['file_attachement']);
                    }
                    if ($this->ProjectRisk->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_risks/index/' . $project_id);
                        }
                        $result = true;
                    } else {
                        unlink($path . $attachment);
                        $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                    }
                } else {
                    $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
                }
            }
        }
        $this->redirect(array('action' => 'index', $project_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }

    public function attachement($id = null) {
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $last = $this->ProjectRisk->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id'),
            'conditions' => array('ProjectRisk.id' => $id)));
        $error = true;
        if ($last && $last['ProjectRisk']['project_id']) {
            $path = trim($this->_getPath($last['ProjectRisk']['project_id'])
                    . $last['ProjectRisk']['file_attachement']);
            $attachment = $last['ProjectRisk']['file_attachement'];
            if($type == 'download'){
                if (file_exists($path) && is_file($path)) {
                    if ($type == 'download') {
                        $info = pathinfo($path);
                        $this->view = 'Media';
                        $params = array(
                            'id' => $info['basename'],
                            'path' => $info['dirname'] . DS,
                            'name' => $info['filename'],
                            'extension' => strtolower($info['extension']),
                            'download' => true
                        );
                        $params['mimeType'][$info['extension']] = 'application/octet-stream';
                        $this->set($params);
                    }
                    $error = false;
                }
            } else {
                @unlink($path);
                $this->ProjectRisk->id = $id;
                $this->ProjectRisk->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectRisk']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_risks/index/' . $last['ProjectRisk']['project_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectBudgetExternal']['project_id'] : __('Unknown', true)));
        }
    }

     protected function _getPath($project_id) {
        $this->loadModel('Project');
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'risk' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
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

}
?>
