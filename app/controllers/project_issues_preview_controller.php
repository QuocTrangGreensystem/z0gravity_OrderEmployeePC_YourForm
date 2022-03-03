<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectIssuesPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectIssue', 'ProjectTeam');

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
        parent::beforeFilter('update_data_log');
        $this->Auth->autoRedirect = false;
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectIssue->id = $id;
                $this->ProjectIssue->save(array(
                    'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array());
        $this->_checkWriteProfile('issue');
        //$this->_checkRole(false, $project_id);
        //$projectName = $this->viewVars['projectName'];
        $this->loadModels('Project', 'UserLastUpdated');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $this->ProjectIssue->Behaviors->attach('Containable');
        $this->ProjectIssue->cacheQueries = true;

        $projectIssues = $this->ProjectIssue->find("all", array(
            'fields' => array('*'),
            'recursive' => -1, "conditions" => array('project_id' => $project_id),
            'order' => array('weight' => 'ASC')
        ));
        $listProjectIssueId = !empty($projectIssues) ? Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}.ProjectIssue.id') : array();
        $issueSeverities = $this->ProjectIssue->ProjectIssueSeverity->find('all', array(
            'fields' => array('id', 'issue_severity', 'color'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'recursive' => -1
        ));
        $colorSeverities = !empty($issueSeverities) ? Set::combine($issueSeverities, '{n}.ProjectIssueSeverity.id', '{n}.ProjectIssueSeverity.color') : array();
        $issueSeverities = !empty($issueSeverities) ? Set::combine($issueSeverities, '{n}.ProjectIssueSeverity.id', '{n}.ProjectIssueSeverity.issue_severity') : array();
        $issueStatus = $this->ProjectIssue->ProjectIssueStatus->find('list', array(
            'fields' => array('id', 'issue_status'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
                )));

        $this->loadModel('ProjectTeam');
        $this->ProjectTeam->Behaviors->attach('Containable');
        $employees = $this->ProjectTeam->find('all', array(
            'fields' => array('id'),
            'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id')),
            'conditions' => array('project_id' => $project_id)));
        $employees = array_filter(Set::classicExtract($employees, '{n}.ProjectFunctionEmployeeRefer.0.employee_id'));
        $employees = $this->ProjectIssue->Employee->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'recursive' => -1,
            'conditions' => array(
                'id' => array_merge($employees , array($projectName['Project']['project_manager_id']))
            ),
            'fields' => array('first_name', 'last_name', 'id')));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        $employees = array($projectName['Project']['project_manager_id'] =>
            $employees[$projectName['Project']['project_manager_id']]) + $employees;
        /**
         * Ten cong ty
         */
        $this->loadModel('LogSystem');
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
            'fields' => array('id', 'project_amr_problem_information')
        ));
        if(!empty($projectAmrs) && !empty($projectAmrs['ProjectAmr']['project_amr_problem_information'])){
            $updated = !empty($projectName['Project']['updated']) ? date('H:i d/m/Y', $projectName['Project']['updated']) : '../../....';
            $byEmployee = !empty($projectName['Project']['update_by_employee']) ? $projectName['Project']['update_by_employee'] : 'N/A';
            $employOldId = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array('fullname' => $byEmployee),
                'fields' => array('id')
            ));
            $saveOldLogs = array(
                'company_id' => $company_id,
                'model' => 'ProjectIssue',
                'model_id' => $projectName['Project']['id'],
                'name' => $byEmployee . ' ' . $updated,
                'description' => $projectAmrs['ProjectAmr']['project_amr_problem_information'],
                'employee_id' => !empty($employOldId) && $employOldId['Employee']['id'] ? $employOldId['Employee']['id'] : '',
                'update_by_employee' => $byEmployee
            );
            $this->LogSystem->create();
            if($this->LogSystem->save($saveOldLogs)){
                $this->ProjectAmr->id = $projectAmrs['ProjectAmr']['id'];
                $this->ProjectAmr->save(array('project_amr_problem_information' => ''));
            }
        }
        /**
         * Lay tat ca cac log
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ProjectIssue',
                'model_id' => $project_id,
                'company_id' => $projectName['Project']['company_id']
            ),
            'limit' => 1,
            'fields' => array('id', 'name', 'description', 'employee_id', 'created'),
            'order' => array('updated' => 'DESC')
        ));
        $listEmployeeLogs = !empty($logSystems) ? array_unique(Set::classicExtract($logSystems, '{n}.LogSystem.employee_id')) : array();
        $logSystems = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
        /**
         * Danh sach avatar cua employee Logs
         */
        $avatarEmploys = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $listEmployeeLogs),
            'fields' => array('id', 'avatar_resize')
        ));
        $this->loadModel('ProjectIssueColor');
        $listColor = $this->ProjectIssueColor->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'color')
        ));
        $colorDefault = $this->ProjectIssueColor->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'default' => 1
            )
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'company_id' => $company_id,
                    'company_id IS NULL',
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        $this->Employee->virtualFields['emId'] = 'CONCAT("0-", `id`)';
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'company_id' => $company_id,
                    'company_id IS NULL',
                )
            ),
            'fields' => array('emId', 'fullname')
        ));
        //
        $this->loadModels('ProfitCenter', 'ProjectIssueEmployeeRefer','HistoryFilter');
        $this->ProfitCenter->virtualFields['pcId'] = 'CONCAT("1-", `id`)';
        $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
        $listProfit = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('pcId', 'pcName')
        ));
        $listEmployeeAndProfit = $listEmployee;
        foreach ($listProfit as $key => $value) {
            $listEmployeeAndProfit[$key] = $value;
        }

        $issueEmployeeRefer = $this->ProjectIssueEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_issue_id' => $listProjectIssueId
            )
        ));
        $_issueEmployeeRefer = array();
        foreach ($issueEmployeeRefer as $key => $value) {
            $dx = $value['ProjectIssueEmployeeRefer'];
            $_issueEmployeeRefer[$dx['project_issue_id']][] = $dx;
        }
         // last updated project risk
        $issue_last_updated = $this->UserLastUpdated->find('all', array(
            'recursive' => -1,
            'fields' => array('updated', 'employee_id', 'path'),
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'path LIKE \'%project_issues_preview/index/'.$project_id .'%\'',
            ),
        ));
        $employee_update = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id','first_name', 'last_name')
        ));
        $employee_update = !empty($employee_update) ? Set::combine($employee_update, '{n}.Employee.id', '{n}.Employee') : array();

        $issue_action_update = array();
        if(!empty($issue_last_updated)){
            foreach ($issue_last_updated as $key => $value) {
                $issue_id = explode('/', $value['UserLastUpdated']['path']);
                if(!empty($issue_id[3])){
                    $issue_action_update[$issue_id[3]]['employee_name'] = '';
                    $issue_action_update[$issue_id[3]] = $value['UserLastUpdated'];
                }
            }
        }
        $colorDefault = !empty($colorDefault['ProjectIssueColor']['color']) ? $colorDefault['ProjectIssueColor']['color'] : '#004380';
        $is_avatar = $this->checkAvatar();
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('issue_action_update','employee_update','is_avatar','company_id', 'employeeLoginId', 'avatarEmployeeLogin', 'employeeLoginName', 'companyName', 'avatarEmploys', 'logSystems', 'employee_id', 'listEmployee', '_issueEmployeeRefer'));
        $this->set(compact('projectName', 'issueSeverities', 'issueStatus', 'employees', 'project_id', 'projectIssues', 'colorSeverities', 'colorDefault', 'listColor', 'listEmployeeAndProfit', 'loadFilter'));
    }

    /**
     *  Save/edit Log System
     */
    public function update_data_log(){
        $this->layout = false;
        $result = '';
        if($_POST){
            if($_POST['id'] == -1){
                unset($_POST['id']);
            }
            $result = $this->LogSystem->saveLogSystem($_POST);
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
            $is_udpate_issue_action_related = $this->is_udpate_issue_action_related($this->data['id'], $this->data['issue_action_related']);
            $this->ProjectIssue->create();
            if (!empty($this->data['id'])) {
                $this->ProjectIssue->id = $this->data['id'];
            }
            //issue_assign_to
            $this->loadModel('ProjectIssueEmployeeRefer');
            if(!empty($this->data['issue_assign_to'])){
                $_saved = $this->ProjectIssueEmployeeRefer->find('all', array(
                    'fields' => array('id', 'project_issue_id', 'reference_id', 'is_profit_center'),
                    'conditions' => array(
                        'project_issue_id' => $this->data['id']
                    ),
                    'recursive' => -1
                ));
                $saved = array();
                foreach ($_saved as $value) {
                    $dx = $value['ProjectIssueEmployeeRefer'];
                    $saved[$dx['is_profit_center'] . '-' . $dx['reference_id']] = $dx;
                }
                foreach ($this->data['issue_assign_to'] as $impact) {
                    $v = explode('-', $impact);
                    $this->ProjectIssueEmployeeRefer->create();
                    $data = array(
                        'project_issue_id' => $this->data['id'],
                        'reference_id' => $v[1],
                        'is_profit_center' => $v[0],
                    );
                    $last = isset($saved[$impact]) ? $saved[$impact] : null;
                    if (!$last) {
                        $this->ProjectIssueEmployeeRefer->save($data);
                    }
                    unset($saved[$impact]);
                }
                foreach ($saved as $_save) {
                    $this->ProjectIssueEmployeeRefer->delete($_save['id']);
                }
                unset($this->data['issue_assign_to']);
            } else {
                $saved = $this->ProjectIssueEmployeeRefer->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array(
                        'project_issue_id' => $this->data['id'],
                    ),
                    'recursive' => -1
                ));
                foreach ($saved as $_save) {
                    $this->ProjectIssueEmployeeRefer->delete($_save['id']);
                }
            }
            $data = array();
            foreach (array('risk_close_date', 'date_issue_close', 'date_open', 'delivery_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectIssue->convertTime($this->data[$key]);
                }
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectIssue->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('The Issue has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Issue could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectIssue->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        if($result){
            if($is_udpate_issue_action_related){
                $risk_last_updated = $this->UserLastUpdated->find('first', array(
                    'fields' => array('id', 'employee_id'),
                    'conditions' => array(
                        'UserLastUpdated.company_id' => $this->employee_info['Company']['id'],
                        'UserLastUpdated.path' => 'project_issues_preview/index/'. $this->data['project_id'] .'/'. $this->data['id'],
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
                        'path' => 'project_issues_preview/index/'. $this->data['project_id'] .'/'. $this->data['id'],
                        'action' => '',
                        'created' => time(),
                        'updated' => time()
                    )); 
                }
            }
        }
        $this->set(compact('result'));
    }
    private function is_udpate_issue_action_related($id = null, $issue_action_related = ''){
        if(!empty($id)){
            $project_issue =  $this->ProjectIssue->find("first", array(
                'recursive' => -1,
                "fields" => array("issue_action_related"),
                'conditions' => array('id' => $id)
            ));
            if($project_issue['ProjectIssue']['issue_action_related'] == $issue_action_related ){
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
        $conditions = array('ProjectIssue.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectIssue.id'] = $data;
            }
        }

        $this->ProjectIssue->Behaviors->attach('Containable');
        $this->ProjectIssue->cacheQueries = true;

        $projectIssues = $this->ProjectIssue->find("all", array(
            'fields' => array('id', 'project_issue_problem', 'issue_assign_to', 'project_issue_severity_id', 'project_issue_status_id', 'date_issue_close', 'issue_action_related', 'delivery_date', 'project_issue_color_id', 'date_open'),
            'contain' => array(
                'ProjectIssueSeverity' => array(
                    'id', 'issue_severity'
                ),
                'ProjectIssueStatus' => array(
                    'id', 'issue_status'
                ),
                // 'Employee' => array(
                //     'id', 'first_name', 'last_name'
                // )
            ),
            "conditions" => $conditions));
        $listProjectIssueId = Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}.ProjectIssue.id');
        $projectIssues = Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectIssues[$id])) {
                    unset($data[$id]);
                    unset($projectIssues[$id]);
                    continue;
                }
                $data[$id] = $projectIssues[$id];
            }
            $projectIssues = $data;
            unset($data);
        }
        $this->loadModels('Employee', 'ProfitCenter', 'ProjectIssueEmployeeRefer', 'ProjectIssueColor', 'ProjectIssueSeverity', 'ProjectIssueStatus');
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'company_id IS NULL',
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
        $listProfit = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'pcName')
        ));
        $projectIssueEmployeeRefer = $this->ProjectIssueEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_issue_id' => $listProjectIssueId
            ),
            'fields' => array('id', 'project_issue_id', 'reference_id', 'is_profit_center')
        ));
        $listReference = array();
        foreach ($projectIssueEmployeeRefer as $_projectIssueEmployeeRefer) {
            $dx = $_projectIssueEmployeeRefer['ProjectIssueEmployeeRefer'];
            $listReference[$dx['project_issue_id']][] = $dx;
        }
        $projectIssueColor = $this->ProjectIssueColor->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'display' => 1
            ),
            'fields' => array('id', 'color')
        ));
        $colorDefault = $this->ProjectIssueColor->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'display' => 1,
                'default' => 1
            )
        ));
        $colorDefault = !empty($colorDefault) ? $colorDefault['ProjectIssueColor']['color'] : '#004380';
        $severityColor = $this->ProjectIssueSeverity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'color')
        ));
        $issueStatus = $this->ProjectIssueStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'issue_status')
        ));
        $this->set(compact('projectIssues', 'projectName', 'listEmployee', 'listProfit', 'listReference', 'colorDefault', 'projectIssueColor', 'severityColor', 'issueStatus'));
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
            $this->Session->setFlash(__('Invalid project issue', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectIssue', $this->ProjectIssue->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectIssue->create();
            if ($this->ProjectIssue->save($this->data)) {
                $this->Session->setFlash(__('The project issue has been saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The project issue could not be saved. Please, try again.', true), "error");
            }
        }
        $projects = $this->ProjectIssue->Project->find('list');
        $projectIssueSeverities = $this->ProjectIssue->ProjectIssueSeverity->find('list');
        $projectIssueStatuses = $this->ProjectIssue->ProjectIssueStatus->find('list');
        $employees = $this->ProjectIssue->Employee->find('list');
        $this->set(compact('projects', 'projectIssueSeverities', 'projectIssueStatuses', 'employees'));
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
            $this->Session->setFlash(__('Invalid project issue', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectIssue"]["project_id"]));
        }
        if (!empty($this->data)) {
            $this->data["ProjectIssue"]["date_issue_close"] = $str_utility->convertToSQLDate($this->data["ProjectIssue"]["date_issue_close"]);
            if ($this->ProjectIssue->save($this->data)) {
                $this->Session->setFlash(sprintf(__('The project issue %s has been saved', true), '<b>' . $this->data["ProjectIssue"]["project_issue_problem"] . '</b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectIssue"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project issue could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('action' => 'index', $this->data["ProjectIssue"]["project_id"]));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectIssue->read(null, $id);
        }
        $projects = $this->ProjectIssue->Project->find('list');
        $projectIssueSeverities = $this->ProjectIssue->ProjectIssueSeverity->find('list');
        $projectIssueStatuses = $this->ProjectIssue->ProjectIssueStatus->find('list');
        $employees = $this->ProjectIssue->Employee->find('list');
        $this->set(compact('projects', 'projectIssueSeverities', 'projectIssueStatuses', 'employees'));
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
		$item = $this->ProjectIssue->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'project_id','file_attachement'),
                'conditions' => array('ProjectIssue.id' => $id)));
		$project_id = !empty( $item) ? $item['ProjectIssue']['project_id'] : 0;
        if (!empty( $item) && $this->_checkRole(false, $project_id)) {
            if ($result = $this->ProjectIssue->delete($id)) {
                @unlink(trim($this->_getPath($item['ProjectIssue']['project_id'])
                        . $item['ProjectIssue']['file_attachement']));
                $message =  __('Deleted', true);
				$this->loadModel('ProjectIssueEmployeeRefer');
				$list = $this->ProjectIssueEmployeeRefer->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'project_issue_id' => $id
					),
					'fields' => array('id', 'id')
				));
				foreach ($list as $i) {
					$this->ProjectIssueEmployeeRefer->delete($i);
				}
			}else{
				$message = __('Project issue was not deleted', true);
			}
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
            $record = $this->ProjectIssue->find('first', array('conditions' => array('ProjectIssue.id' => $project_issue_id)));
            if (!empty($record)) {
                echo $record['ProjectIssue']['issue_action_related'];
            } else {
                echo "";
            }
        }
        else
            echo "";
        exit;
    }

    /**
     * check_issue_of_project
     *
     * @return void
     * @access public
     */
    function check_issue_of_project() {
        $project_id = $_POST['project_id'];
        $project_issue_name = $_POST['project_issue_name'];
        $this->layout = "ajax";
        $check = $this->ProjectIssue->find("count", array("conditions" => array(
                "ProjectIssue.project_id" => $project_id,
                "ProjectIssue.project_issue_problem" => $project_issue_name,
                )));
        echo $check;
        exit;
    }

    /**
     * check_issue_of_project_edit
     *
     * @return void
     * @access public
     */
    function check_issue_of_project_edit() {
        $project_id = $_POST['project_id'];
        $project_issue_name = $_POST['project_issue_name'];
        $issue_to_edit = $_POST['issue_to_edit'];
        $this->layout = "ajax";
        $check = $this->ProjectIssue->find("count", array("conditions" => array(
                "ProjectIssue.project_id" => $project_id,
                "ProjectIssue.project_issue_problem" => $project_issue_name,
                "ProjectIssue.id <>" => $issue_to_edit,
                )));
        echo $check;
        exit;
    }

    /**
     * exportExcel
     *
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $this->set('columns', $this->name_columna);
        $this->paginate = array("conditions" => array('Project.id' => $project_id));
        $this->set("project_id", $project_id);
        $this->set('projectIssues', $this->paginate());
        $this->set('projectName', $this->ProjectIssue->Project->find("first", array("fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
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
            $projectIssues = $this->ProjectIssue->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectIssue.id' => $this->data['Upload']['id'])));
            if ($projectIssues) {
                $project_id = $projectIssues['ProjectIssue']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectIssue->id = $this->data['Upload']['id'];
                $last = $this->ProjectIssue->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->ProjectIssue->id)));
                if ($last && $last['ProjectIssue']['file_attachement']) {
                    unlink($path . $last['ProjectIssue']['file_attachement']);
                }
                $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                if ($this->ProjectIssue->save(array(
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
                    $this->ProjectIssue->id = $this->data['Upload']['id'];
                    $last = $this->ProjectIssue->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->ProjectIssue->id)));
                    if ($last && $last['ProjectIssue']['file_attachement']) {
                        unlink($path . $last['ProjectIssue']['file_attachement']);
                    }
                    if ($this->ProjectIssue->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_issues/index/' . $project_id);
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
        $last = $this->ProjectIssue->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id'),
            'conditions' => array('ProjectIssue.id' => $id)));
        $error = true;
        if ($last && $last['ProjectIssue']['project_id']) {
            $path = trim($this->_getPath($last['ProjectIssue']['project_id'])
                    . $last['ProjectIssue']['file_attachement']);
            $attachment = $last['ProjectIssue']['file_attachement'];
            if( $type == 'download'){
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
                $this->ProjectIssue->id = $id;
                $this->ProjectIssue->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectIssue']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_issues/index/' . $last['ProjectIssue']['project_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectIssue']['project_id'] : __('Unknown', true)));
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
        $path = FILES . 'projects' . DS . 'issue' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    public function getComment(){
        if(!empty($_POST['id'])){
            $this->loadModels('ProjectText');
            $id = $_POST['id'];
            $model = $_POST['model'];
            $idEmployee = $this->employee_info['Employee']['id'];
            $company_id = $this->employee_info['Company']['id'];
            $comments = $this->ProjectText->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $id,
                    'model' => $model
                ),
                'fields' => array('*'),
            ));
            $listComments = !empty($comments) ? Set::combine($comments, '{n}.ProjectText.id', '{n}.ProjectText') : array();
            $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ProjectText.employee_id')) : array();
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Employee.id' => $listIdEm
                ),
                'fields' => array('id', 'avatar', 'first_name', 'last_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
            $data = $_employee = array();
            foreach ($listComments as $_comment) {
                $_id = $_comment['employee_id'];
                $_comment['employee_id'] = $employees[$_id];
                $data['comment'][] = $_comment;
            }
            die(json_encode($data));
        }
        exit;
    }
    public function saveComment(){
        $success = false;
        if( !empty($_POST['id']) && (!empty($_POST['content'])) && !empty($this->employee_info['Employee']['id']) ){
            $model_id = $_POST['id'];
            $content = $_POST['content'];
            $idEm = $this->employee_info['Employee']['id'];
            $model = $_POST['model'];
            $this->loadModels('ProjectText');
            $saved = array(
                'employee_id' => $idEm,
                'model_id' => $model_id,
                'content' => $content,
                'model' => $model
            );
            $this->ProjectText->create();
            $success = (boolean) $this->ProjectText->save($saved);
        }
        die(json_encode($success));
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
