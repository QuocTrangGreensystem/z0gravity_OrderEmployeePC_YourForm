<?php

/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectsPreviewController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectsPreview';

    /**
     * Layout used by the Controller
     *
     * @var array
     * @access public
     */
    // var $layout = 'default_preview';

    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array(
        'Project',
        'ProjectFunction',
        'Company',
        'ProjectTeam',
        'ProjectPhasePlan',
        'ProjectPhase',
        'ProjectCreatedVal',
        'ProfitCenter',
        'ProjectLivrableActors',
        'ProjectEvolutionImpact',
        'ProjectLivrableActor',
        'ProjectEvolutionImpactRefer',
        'CompanyEmployeeReference',
        'ProjectAmr',
        'ProjectMilestone',
        'ProjectTask',
        'ProjectRisk',
        'ProjectIssue',
        'ProjectDecision',
        'ProjectLivrable',
        'ProjectEvolution',
        'ProjectFunctionEmployeeRefer',
        'ProjectStatus',
        'ProjectPriority',
        'ProjectComplexity',
        'ProjectAmrProgram',
        'ProjectType',
        'ProjectSubType',
        'ProjectAmrSubProgram',
        'Currency',
        'UserView',
        'UserDefaultView',
        "Employee",
        "Activity",
        'ProjectBudgetInternal',
        'ProjectBudgetInternalDetail',
        'ProjectBudgetExternal',
        'ProjectBudgetSale',
        'ProjectBudgetInvoice',
        'ProjectBudgetSyn',
        'ProjectPhaseCurrent',
        'ProjectFinancePlus',
        'ProjectFinancePlusDetail',
        'ProjectListMultiple',
        'ProjectFile',
        'ProjectImage'
    );

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->set('allowedFiles', $this->allowedFiles);
    }

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'SlickExporter', 'Lib');
    var $paginate = array('limit' => 25, 'page' => 1, 'order' => array('project_name' => 'asc'));

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('project', $this->Project->read(null, $id));
    }

    function add_popup() {
        // debug(1); exit;
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $Model_e = ClassRegistry::init('Employee');
        $employInfors = $Model_e->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
            'fields' => array('create_a_project')
        ));
        $this->loadModel('ProjectEmployeeManager');
        $canModified = true;
        if ($employeeInfo['Employee']['is_sas'] == 1) {
            //do nothing
        } else {
            if (!$employeeInfo || $employeeInfo['Role']['name'] == 'conslt' || ($employeeInfo['Role']['name'] == 'pm')) {
                $canModified = false;
            }
        }
        $this->set('canModified', $canModified);
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $budgetCustomers = array();
        /**
         * Lay config see all project from admin
         */
        // debug(1); exit;
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $profitCenters = array();
        if ($company_id != "") {
            $this->set('company_names', $this->Project->Company->getTreeList($company_id));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id' => $company_id))));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id))));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id' => $company_id))));
            $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'role_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
            if (!$adminSeeAllProjects) {
                /**
                 * Lay cac profit center cua cong ty
                 */
                $this->loadModels('ProfitCenter');
                $profitCenters = $this->ProfitCenter->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name')
                ));
            }
            $projectEmployees = $this->ProjectTask->Employee->find('all', array(
                'order' => 'last_name  ASC',
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
                ),
                'fields' => array('first_name', 'last_name', 'id')));
            $employees = array();
            foreach ($projectEmployees as $projectEmployee) {
                $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
                foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                    if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                        $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                    }
                }
            }
            $this->set('employees', $employees);
            /**
             * Get list budget customer
             */
            $this->loadModel('BudgetCustomer');
            $budgetCustomers = $this->BudgetCustomer->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        } else {
            $this->set('company_names', $this->Project->Company->generateTreeList(null, null, null, '--'));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('conditions' => array('ProjectPhase.activated' => 1))));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list'));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list'));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('conditions' => array('ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list'));
        }
        $this->set(compact('budgetCustomers', 'adminSeeAllProjects', 'profitCenters'));
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        if (!empty($this->data)) {
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            /**
             * Xu ly functional
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }

            for ($i = 1; $i <= 10; $i++) {
                if (!empty($this->data['project_list_multi_' . $i])) {
                    $this->data['project_list_multi_' . $i] = array_unique($this->data['project_list_multi_' . $i]);
                    if (($key = array_search(0, $this->data['project_list_multi_' . $i])) !== false) {
                        unset($this->data['project_list_multi_' . $i][$key]);
                    }
                }
            }
            /**
             * Xu ly uat
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            $this->Project->create();
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["Project"]["start_date"] = isset($this->data["Project"]["start_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["start_date"]) : null;
            $this->data["Project"]["end_date"] = isset($this->data["Project"]["end_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["end_date"]) : null;
            if (!$canModified) {
                $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
            }
            if (!empty($this->data['Project']['project_amr_program_id']))
                $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            if (!empty($this->data['Project']['project_amr_sub_program_id']))
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            if (!empty($this->data['Project']['project_manager_id']))
                $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            $data_arm['ProjectAmr']['weather'] = 'sun';
            $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['planning_weather'] = 'sun';
            $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['organization_weather'] = 'sun';
            $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
            $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')
            ));
            $this->data['Project']['company_id'] = $company_id;
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $this->data['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
            $this->data['Project']['last_modified'] = time();
            // debug($this->data); exit;
            if ($this->Project->save($this->data)) {
                $this->writeLog($this->data, $this->employee_info, 'Create project #' . $this->Project->id, $company_id);
                $project_ = $this->Project->find("first", array('field' => array('Project.id'), 'order' => array("Project.id" => "DESC")));
                $project_id = $project_['Project']['id'];
                $project_phase_id = $project_['Project']['project_phase_id'];
                $st_date = $this->data["Project"]["start_date"];
                if ($this->data["Project"]["end_date"] == "")
                    $end_date = '';
                else
                    $end_date = $this->data["Project"]["end_date"];
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                $this->Session->setFlash(__('Saved', true));
                $pid = $this->Project->getLastInsertID();
                //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $pid;
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'PM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 0;
                            if (!empty($value[1]) && $value[1] == 1) {
                                $is_profit = 1;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                'project_id' => $pid,
                                'type' => 'RA',
                                'activity_id' => 0,
                                'is_profit_center' => $is_profit
                            );
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'CB',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'TM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'FL',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['functional_leader_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            'project_id' => $pid,
                            'type' => 'UM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['uat_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            $saved = array(
                                'project_id' => $pid,
                                'project_phase_id' => $phaseId
                            );
                            $this->ProjectPhaseCurrent->create();
                            $this->ProjectPhaseCurrent->save($saved);
                        }
                    }
                }
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($this->data['project_list_multi_' . $i])) {

                        $field = 'project_list_multi_' . $i;
                        $dataRefers = array(
                            'project_id' => $pid,
                            'key' => $field
                        );
                        $checkDatas = $this->ProjectListMultiple->find('list', array(
                            'recursive' => -1,
                            'conditions' => $dataRefers,
                            'fields' => array('project_dataset_id', 'id')
                        ));
                        foreach ($this->data['project_list_multi_' . $i] as $value) {
                            if (in_array($value, array_keys($checkDatas))) {
                                
                            } else {
                                $this->ProjectListMultiple->create();
                                $this->ProjectListMultiple->save(array(
                                    'project_id' => $pid,
                                    'project_dataset_id' => $value,
                                    'key' => $field
                                ));
                            }
                            unset($checkDatas[$value]);
                        }
                        if (!empty($checkDatas)) {
                            foreach ($checkDatas as $va) {
                                $this->ProjectListMultiple->id = $va;
                                $this->ProjectListMultiple->delete();
                            }
                        }
                    }
                }
                //End Create arm project
                /**
                 * Lay screen mac dinh cua project
                 */
                $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
                ));
                $ACLController = 'projects';
                $ACLAction = 'edit';
                if (!empty($screenDefaults)) {
                    $ACLController = $screenDefaults['Menu']['controllers'];
                    $ACLAction = $screenDefaults['Menu']['functions'];
                }
                $this->redirect("/projects/your_form/" . $pid);
            } else {
                $this->Session->setFlash(__('Not saved.', true), 'error');
            }
        }
        $currencies = $this->Project->Currency->find('list');
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $this->set(compact('projectPhases', 'projectPriorities', 'projectStatuses', 'currencies', 'projectManagers', 'currency_name', 'adminSeeAllProjects'));
        //tree company
        $tree = $this->Project->Company->generateTreeList(null, null, null, '->');
        if (!$this->is_sas) {
            $parent = array();
            foreach ($tree as $key => $value) {
                $parentPath = $this->Project->Company->getpath($key);
                foreach ($parentPath as $par) {
                    $parent[$key]['name'] = $value;
                    if ($par['Company']['id'] != $key) {
                        $parent[$key]['list'][] = $par['Company']['id'];
                    }
                }
            }
            foreach ($tree as $key => $value) {
                if (!empty($parent[$key]['list'])) {
                    if ($key != $this->employee_info['Company']['id'])
                        if (!in_array($this->employee_info['Company']['id'], $parent[$key]['list']))
                            unset($tree[$key]);
                }else {
                    unset($tree[$key]);
                }
            }
        }
        // --------------------------------------------------
        $this->set('tree', $tree);
        // get youform fields from setting admin / yourform
        $this->loadModel('Translation');
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => 'Details',
                'TranslationSetting.company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));

        // Get fields your form in top section
        // All fields in top section is define in Translation setting admin / Detail_5  
        $translation_data_default = array();
        //if(!empty($employeeInfo['Color']['is_new_design']) && $employeeInfo['Color']['is_new_design'] == 1){
        $translation_data_default = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => 'Details_5',
                'TranslationSetting.company_id' => $this->employee_info['Company']['id'],
                'TranslationSetting.show' => 1
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('checkAvatar', $this->checkAvatar());
        $this->set('datasets', $datasets);
        $this->set('translation_data', $translation_data); //check if your_form is shown and is default screen
        $this->set('translation_data_default', $translation_data_default); //check if your_form is shown and is default screen
        $this->render('add-popup');
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $Model_e = ClassRegistry::init('Employee');
        $employInfors = $Model_e->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
            'fields' => array('create_a_project')
        ));
        $employInfors = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? true : false;
        if ($this->employee_info["Role"]["name"] == 'pm' && !$employInfors) {
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('ProjectEmployeeManager');
        $canModified = true;
        if ($employeeInfo['Employee']['is_sas'] == 1) {
            //do nothing
        } else {
            if (!$employeeInfo || $employeeInfo['Role']['name'] == 'conslt' || ($employeeInfo['Role']['name'] == 'pm')) {
                $canModified = false;
            }
        }
        $this->set('canModified', $canModified);
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $budgetCustomers = array();
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $profitCenters = array();
        if ($company_id != "") {
            $this->set('company_names', $this->Project->Company->getTreeList($company_id));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id' => $company_id))));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id))));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id' => $company_id))));
            $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'role_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
            if (!$adminSeeAllProjects) {
                /**
                 * Lay cac profit center cua cong ty
                 */
                $this->loadModels('ProfitCenter');
                $profitCenters = $this->ProfitCenter->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name')
                ));
            }
            $projectEmployees = $this->ProjectTask->Employee->find('all', array(
                'order' => 'last_name  ASC',
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
                ),
                'fields' => array('first_name', 'last_name', 'id')));
            $employees = array();
            foreach ($projectEmployees as $projectEmployee) {
                $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
                foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                    if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                        $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                    }
                }
            }
            $this->set('employees', $employees);
            /**
             * Get list budget customer
             */
            $this->loadModel('BudgetCustomer');
            $budgetCustomers = $this->BudgetCustomer->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        } else {
            $this->set('company_names', $this->Project->Company->generateTreeList(null, null, null, '--'));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('conditions' => array('ProjectPhase.activated' => 1))));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list'));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list'));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('conditions' => array('ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list'));
        }
        $this->set(compact('budgetCustomers', 'adminSeeAllProjects', 'profitCenters'));
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        if (!empty($this->data)) {
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /**
             * Xu ly functional
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /**
             * Xu ly uat
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            //$countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->Session->setFlash(__('The project must have a project manager. Please, try again.', true), 'error');
            // } else {
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // /}
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            $this->Project->create();
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["Project"]["start_date"] = isset($this->data["Project"]["start_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["start_date"]) : null;
            $this->data["Project"]["end_date"] = isset($this->data["Project"]["end_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["end_date"]) : null;
            if (!$canModified) {
                $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
            }
            $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            $data_arm['ProjectAmr']['weather'] = 'sun';
            $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['planning_weather'] = 'sun';
            $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['organization_weather'] = 'sun';
            $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
            $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')
            ));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $this->data['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
            $this->data['Project']['last_modified'] = time();
            if ($this->Project->save($this->data)) {
                $this->writeLog($this->data, $this->employee_info, 'Create project #' . $this->Project->id, $company_id);
                $project_ = $this->Project->find("first", array('field' => array('Project.id'), 'order' => array("Project.id" => "DESC")));
                $project_id = $project_['Project']['id'];
                $project_phase_id = $project_['Project']['project_phase_id'];
                $st_date = $this->data["Project"]["start_date"];
                if ($this->data["Project"]["end_date"] == "")
                    $end_date = '';
                else
                    $end_date = $this->data["Project"]["end_date"];
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                $this->Session->setFlash(__('Saved', true));
                $pid = $this->Project->getLastInsertID();
                //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $pid;
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'PM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                // 'is_backup' => 0,
                                'project_id' => $pid,
                                'type' => 'RA',
                                'activity_id' => 0,
                                'is_profit_center' => $is_profit
                            );
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'CB',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'TM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'FL',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['functional_leader_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'UM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['uat_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            $saved = array(
                                'project_id' => $pid,
                                'project_phase_id' => $phaseId
                            );
                            $this->ProjectPhaseCurrent->create();
                            $this->ProjectPhaseCurrent->save($saved);
                        }
                    }
                }
                //End Create arm project
                /**
                 * Lay screen mac dinh cua project
                 */
                $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
                ));
                $ACLController = 'projects_preview';
                $ACLAction = 'edit';
                if (!empty($screenDefaults)) {
                    $ACLController = $screenDefaults['Menu']['controllers'];
                    $ACLAction = $screenDefaults['Menu']['functions'];
                }
                $this->redirect("/projects/your_form/" . $pid);
            } else {
                $this->Session->setFlash(__('Not saved.', true), 'error');
            }
        }
        //}
        $currencies = $this->Project->Currency->find('list');
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $this->set(compact('projectPhases', 'projectPriorities', 'projectStatuses', 'currencies', 'projectManagers', 'currency_name', 'adminSeeAllProjects'));
        //tree company
        $tree = $this->Project->Company->generateTreeList(null, null, null, '->');
        if (!$this->is_sas) {
            $parent = array();
            foreach ($tree as $key => $value) {
                $parentPath = $this->Project->Company->getpath($key);
                foreach ($parentPath as $par) {
                    $parent[$key]['name'] = $value;
                    if ($par['Company']['id'] != $key) {
                        $parent[$key]['list'][] = $par['Company']['id'];
                    }
                }
            }
            foreach ($tree as $key => $value) {
                if (!empty($parent[$key]['list'])) {
                    if ($key != $this->employee_info['Company']['id'])
                        if (!in_array($this->employee_info['Company']['id'], $parent[$key]['list']))
                            unset($tree[$key]);
                }else {
                    unset($tree[$key]);
                }
            }
        }
        // --------------------------------------------------
        $this->set('tree', $tree);
        $this->loadModel('Translation');
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => 'Details',
                'TranslationSetting.company_id' => $company_id
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        $this->set('translation_data', $translation_data); //check if your_form is shown and is default screen
        $your_form = $this->requestAction('/menus/getMenu/projects/your_form');
        $this->render('your_form_add');
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        $this->loadModel('ProjectEmployeeManager');
        // Check the role
        if (!$this->_checkRole(true, $id, empty($this->data) ? array('element' => 'warning') : array())) {
            $this->data = null;
        }

        $this->_checkWriteProfile('details');
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $employee_id = $employeeInfo['Employee']['id'];
        $changeProjectManager = !(empty($employeeInfo['Role']) || $employeeInfo['Role']['name'] == 'conslt' || $employeeInfo['Role']['name'] == 'pm');
        if ($employeeInfo['Role']['name'] == 'pm') {
            $checkIsManager = $this->Project->find('first', array(
                'conditions' => array(
                    'Project.id' => $id,
                    'Project.project_manager_id' => $employee_id
                )
            ));
            $changeProjectManager = !empty($checkIsManager) ? true : false;
        }
        if ($employeeInfo) {
            $fullname = $employeeInfo['Employee']['first_name'] . " " . $employeeInfo['Employee']['last_name'];
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;

        $company_idd = $company_id_of_project['Project']['company_id'];
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->redirect(array('action' => 'index'));
        }

        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        // security
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $company_id = $this->Project->find("first", array(
            'recursive' => 0,
            "fields" => array("Project.company_id", 'Company.company_name'),
            'conditions' => array('Project.id' => $id)));
        $name_company = $company_id['Company']['company_name'];
        $company_id = $company_id['Project']['company_id'];
        $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectAmrProgram.company_id ' => $company_id
            ),
            'fields' => array('id', 'family_id', 'sub_family_id')
        ));
        $famAndSubFamOfPrograms = !empty($famAndSubFamOfPrograms) ? Set::combine($famAndSubFamOfPrograms, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram') : array();
        $subFams = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => array_keys($famAndSubFamOfPrograms)),
            'fields' => array('id', 'sub_family_id')
        ));
        /**
         * Lay tat ca cac phase cua 1 project
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        if (!empty($this->data)) {
            if ($this->data['Project']['category'] != 2) {
                $this->data['Project']['is_staffing'] = 0;
            }
            if (!empty($this->data['Project']['tmp_activity']) || $this->data['Project']['tmp_activity'] == 0) {
                if ($this->data['Project']['tmp_activity'] == 0) {
                    $this->data['Project']['activity_id'] = null;
                } else {
                    $this->data['Project']['activity_id'] = $this->data['Project']['tmp_activity'];
                }
                unset($this->data['Project']['tmp_activity']);
            }
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /*
             * Xu li functional leader
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /*
             * Xu li uat manager
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            //$countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // debug($countBackupPM); debug($this->data); exit;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->data = $this->Project->read(null, $id);
            //     $this->Session->setFlash(__('Please select at least a project manager without the flag backup.', true), 'error');
            // } else {
            $activityLinked = !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0;
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // }
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);
            $data_project_teams = $this->params['form'];
            $project_id = $this->data["Project"]["id"];
            if (!empty($this->data['Project']['project_amr_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            }
            if (!empty($this->data['Project']['project_amr_sub_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            }
            $checkBackup = $this->ProjectEmployeeManager->find('count', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id, 'project_manager_id' => $employeeInfo['Employee']['id'], 'type' => 'PM')
            ));
            if (!$changeProjectManager && $employeeInfo['Role']['name'] != 'admin') {
                if (!empty($checkBackup)) {
                    //do nothing
                } else {
                    $Model4 = ClassRegistry::init('Employee');
                    $employInfors = $Model4->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
                        'fields' => array('update_your_form')
                    ));
                    if (!empty($employInfors) && !empty($employInfors['Employee']['update_your_form'])) {
                        // thang nay dc chinh sua tat ca projet...ke me no
                    } else {
                        $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
                    }
                }
            }
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $oldProject = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $this->data['Project']['id']),
                'fields' => array('activity_id')
            ));
            if (!empty($oldProject['Project']['activity_id'])) {
                //da linked roi ko lam gi ca
            } else {
                $this->data['Project']['activity_id'] = $activityLinked;
            }
            if (!empty($this->data['Project']['activity_id'])) {
                //bat dau linked
            } else {
                unset($this->data['Project']['activity_id']);
            }
            if ($activityLinked == -1) {
                $this->data['Project']['activity_id'] = null;
            }
            $this->data['Project']['last_modified'] = time();
            $range = range(1, 4);
            foreach ($range as $num) {
                if (isset($this->data['Project']['date_' . $num])) {
                    $this->data['Project']['date_' . $num] = $str_utility->convertToSQLDate($this->data['Project']['date_' . $num]);
                }
            }
            //$data_arm['ProjectAmr']['project_phases_id']                = $this->data['Project']['project_phase_id'];
            $this->loadModel('Activity');
            // rename activity linked
            $oldProjectName = $this->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name'),
                'conditions' => array('Project.id' => $id)));
            $oldProjectName = !empty($oldProjectName) ? $oldProjectName['Project']['project_name'] : '';
            $newProjectName = !empty($this->data) && !empty($this->data['Project']['project_name']) ? $this->data['Project']['project_name'] : '';
            if ($oldProjectName != $newProjectName) {
                $ActivityLinkedName = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id', 'name', 'long_name', 'short_name'),
                    'conditions' => array(
                        'project' => $id
                    )
                ));
                if (!empty($ActivityLinkedName)) {
                    $saved['name'] = $newProjectName;
                    $idActivityLinked = $ActivityLinkedName['Activity']['id'];
                    $this->Activity->id = $idActivityLinked;
                    $this->Activity->save($saved);
                }
            }
            // end
            // truong hop pm co quyen change status nhung khong the update your form.
            if ($employeeInfo['Role']['name'] == 'pm' && $employeeInfo['Employee']['change_status_project'] == 1 && $employeeInfo['Employee']['update_your_form'] == 0) {
                unset($this->data['Project']['project_manager_id']);
                unset($this->data['Project']['chief_business_id']);
                unset($this->data['Project']['technical_manager_id']);
                unset($this->data['Project']['functional_leader_id']);
                unset($this->data['Project']['uat_manager_id']);
            }
            if ($this->Project->save($this->data)) {
                $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update project `%s` via Details', $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $id = $this->Project->id;
                if ($activityLinked == -1) {
                    $activityLinked = 0;
                }
                if (!empty($activityLinked) && $activityLinked != 0 && isset($this->data['Project']['activated'])) {
                    $this->Activity->id = $activityLinked;
                    $idProgram = !empty($this->data['Project']['project_amr_program_id']) ? $this->data['Project']['project_amr_program_id'] : '';
                    $idSubProgram = !empty($this->data['Project']['project_amr_sub_program_id']) ? $this->data['Project']['project_amr_sub_program_id'] : '';
                    $fam = !empty($famAndSubFamOfPrograms[$idProgram]['family_id']) ? $famAndSubFamOfPrograms[$idProgram]['family_id'] : '';
                    $sfam = !empty($subFams[$idSubProgram]) ? $subFams[$idSubProgram] : '';
                    if (!empty($this->data['Project']['category']) && $this->data['Project']['category'] == 1 && $activate_family_linked_program) {
                        if (!empty($fam)) {
                            $this->Activity->save(array(
                                'project_manager_id' => $this->data['Project']['project_manager_id'],
                                'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                                'activated' => $this->data['Project']['activated'],
                                'family_id' => $fam,
                                'subfamily_id' => $sfam
                            ));
                        }
                    } else {
                        $this->Activity->save(array(
                            'project_manager_id' => $this->data['Project']['project_manager_id'],
                            'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                            'activated' => $this->data['Project']['activated']
                        ));
                    }
                }
                $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
                $this->ProjectAmr->id = $ProjectArms['ProjectAmr']['id'];
                $this->ProjectAmr->save($data_arm);
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                //link project - activity
                if (!empty($this->data['Project']['activity_id'])) {
                    // update tmp
                    $this->loadModel('TmpStaffingSystem');
                    $this->TmpStaffingSystem->updateAll(
                            array('TmpStaffingSystem.activity_id' => $this->data['Project']['activity_id']), array('TmpStaffingSystem.project_id' => $id)
                    );
                    // update project budget sync
                    $this->loadModel('ProjectBudgetSyn');
                    $this->ProjectBudgetSyn->updateAll(
                            array('ProjectBudgetSyn.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetSyn.project_id' => $id)
                    );
                    // update project provisional
                    $this->loadModel('ProjectBudgetProvisional');
                    $this->ProjectBudgetProvisional->updateAll(
                            array('ProjectBudgetProvisional.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetProvisional.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlus');
                    $this->ProjectFinancePlus->updateAll(
                            array('ProjectFinancePlus.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlus.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlusDetail');
                    $this->ProjectFinancePlusDetail->updateAll(
                            array('ProjectFinancePlusDetail.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlusDetail.project_id' => $id)
                    );
                    $this->loadModel('ActivityTask');
                    $this->loadModel('ProjectStatus');
                    $activityIdTask = $this->data['Project']['activity_id'];
                    if (isset($activityIdTask) && !empty($activityIdTask) && $activityIdTask != '' && $activityIdTask != null) {
                        $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.activity_id' => $activityIdTask)));
                        $projectStatus = $this->ProjectStatus->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('ProjectStatus.status' => 'IP', 'company_id' => $company_id),
                            'fields' => array('id')
                        ));
                        $projectStatus = $projectStatus['ProjectStatus']['id'];
                        $projectTasks = $this->Project->ProjectTask->find('all', array('recursive' => -1, 'conditions' => array('ProjectTask.project_id' => $id, 'ProjectTask.task_status_id' => $projectStatus)));
                        $listProjectPhase = ClassRegistry::init('ProjectPhase')->find('list', array('recursive' => -1, 'conditions' => array('company_id' => $company_id, 'ProjectPhase.activated' => 1)));
                        $listPlan = ClassRegistry::init('ProjectPhasePlan')->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('project_id' => $id),
                            'fields' => array('id', 'project_planed_phase_id')
                        ));
                        $activity = ClassRegistry::init('Activity')->find('first', array('recursive' => -1, 'conditions' => array('Activity.id' => $this->data['Project']['activity_id']), 'fields' => array('id', 'project')));
                        $this->loadModel('Activity');
                        if (!empty($activity)) {
                            $activities = ClassRegistry::init('Activity')->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('Activity.project' => $id),
                                'fields' => array('id', 'project')
                            ));
                            if (!empty($activities)) {
                                foreach ($activities as $act) {
                                    $this->Activity->id = $act['Activity']['id'];
                                    $this->Activity->saveField('project', null);
                                }
                            }
                            $this->Activity->id = $activity['Activity']['id'];
                            $this->Activity->saveField('project', $id);
                        }
                        foreach ($projectTasks as $projectTask) {
                            $project_task_id[] = $projectTask['ProjectTask']['id'];
                        }
                        if (!empty($project_task_id)) {
                            $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.project_task_id' => $project_task_id)));
                        }
                        foreach ($activityTasks as $activityTask) {
                            $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                            $this->ActivityTask->delete();
                        }
                        $dataActivityTask = array();
                        foreach ($projectTasks as $projectTask) {
                            $idProjectPhase = $projectTask['ProjectTask']['project_planed_phase_id'];
                            $_name = isset($idProjectPhase) ? $listPlan[$idProjectPhase] : '';
                            $_name = !empty($_name) ? $listProjectPhase[$_name] : '';
                            $nameProjectTask = $projectTask['ProjectTask']['task_title'];
                            $this->ActivityTask->create();
                            $dataActivityTask['ActivityTask']['name'] = $_name . '/ ' . $nameProjectTask;
                            $dataActivityTask['ActivityTask']['activity_id'] = $activityIdTask;
                            $dataActivityTask['ActivityTask']['project_task_id'] = $projectTask['ProjectTask']['id'];
                            $this->ActivityTask->save($dataActivityTask['ActivityTask']);
                        }
                    }
                }
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                $listEmployeeRefers = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id
                    ),
                    'fields' => array('id', 'id', 'type'),
                    'group' => array('type', 'id')
                ));
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                //'is_backup' => 0,
                                'project_id' => $id,
                                'type' => 'RA',
                                'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0,
                                'is_profit_center' => $is_profit
                            );
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['RA'][$lastEmployRefers]);
                            }
                        }
                    }
                    if (!empty($listEmployeeRefers['RA'])) {
                        $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['RA']), false);
                    }
                    unset($listEmployeeRefers['RA']);
                }
                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'PM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['PM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['PM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['PM']), false);
                }
                unset($listEmployeeRefers['PM']);
                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'CB',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['CB'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['CB'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['CB']), false);
                }
                unset($listEmployeeRefers['CB']);
                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'TM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['TM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['TM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['TM']), false);
                }
                unset($listEmployeeRefers['TM']);
                //functional leader & uat manager
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'FL',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['FL'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['FL'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['FL']), false);
                }
                unset($listEmployeeRefers['FL']);
                //uat manager
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'UM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['UM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['UM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['UM']), false);
                }
                unset($listEmployeeRefers['UM']);
                $oldPhase = array();
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            if (in_array($phaseId, $phasePlans)) { // ton tai trong phase plan of project
                                $oldPhase[] = $phaseId;
                            } else {
                                $saved = array(
                                    'project_id' => $id,
                                    'project_phase_id' => $phaseId
                                );
                                $this->ProjectPhaseCurrent->create();
                                $this->ProjectPhaseCurrent->save($saved);
                            }
                        }
                    }
                }
                foreach ($phasePlans as $idPlan => $phaseId) {
                    if (in_array($phaseId, $oldPhase)) {
                        //do nothing
                    } else {
                        $this->ProjectPhaseCurrent->delete($idPlan);
                        //delete phase
                    }
                }
                $this->Session->setFlash(__('Saved', true));
            } else {
                $this->Session->setFlash(__('The project could not be saved. Please, try again.', true), 'error');
            }
            $this->redirect("/projects/edit/" . $project_id);
        }
        //}
        if (empty($this->data)) {
            $this->data = $this->Project->read(null, $id);
        }
        $IdTask = $this->data['Project']['activity_id'];
        $this->set('famAndSubFamOfPrograms', $famAndSubFamOfPrograms);
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1), 'order' => 'ProjectComplexity.name ASC')));
        $this->set('Priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id ' => $company_id))));
        $this->set('Statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id ' => $company_id))));
        $this->set('ProjectTypes', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id ' => $company_id, 'ProjectType.display' => 1))));
        $this->set('ProjectSubTypes', $this->ProjectSubType->find('list', array('fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 'conditions' => array('ProjectSubType.project_type_id ' => $this->data['Project']['project_type_id'], 'ProjectSubType.display' => 1))));
        $this->set('ProjectArmPrograms', $this->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id ' => $company_id))));
        $this->set('ProjectArmSubPrograms', $this->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program'), 'conditions' => array('ProjectAmrSubProgram.project_amr_program_id ' => $this->data['Project']['project_amr_program_id']))));
        $this->set('ProjectActivities', ClassRegistry::init('Activity')->find('list', array('fields' => array('Activity.id', 'Activity.name'), 'conditions' => array('Activity.company_id ' => $company_id, 'OR' => array('Activity.pms' => 1, 'NOT' => array('Activity.project' => null))))));
        $this->set('listIdActivity', $this->Project->find('list', array('recursive' => -1, 'fields' => array('Project.activity_id'), 'conditions' => array('Project.company_id' => $company_id))));
        //$this->set('activityRequest'         , ClassRegistry::init('ActivityRequest')->find('count', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask))));
        // Find Task and Activities in request then block the combo box
        $find_tasks_in_activity = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask)));
        $find_tasks_in_activity = Set::combine($find_tasks_in_activity, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $find_tasks_in_activity = array_keys($find_tasks_in_activity);
        // Check if the project is linked to any activity or not
        if (isset($IdTask)) {
            if (!empty($find_tasks_in_activity)) {
                $conditions = array('task_id' => $find_tasks_in_activity);
                $activityRequest = ClassRegistry::init('ActivityRequest')->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "OR" => array(
                            'activity_id' => $IdTask,
                            $conditions
                        )
                    )
                        )
                );
            } else {
                $activityRequest = 0;
            }
        } else {
            $activityRequest = 0;
        }
        $activityRequest = json_encode($activityRequest);
        // Check activities and tasks, if assigned then lock the combo box
        $this->set('activityRequest', $activityRequest);

        $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));

        $profitCenters = array();
        if (!$adminSeeAllProjects) {
            /**
             * Lay cac profit center cua cong ty
             */
            $this->loadModels('ProfitCenter');
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        }
        $projectEmployees = $this->ProjectTask->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees),
				'Employee.last_name NOT' => 'NULL'
            ),
            'fields' => array('first_name', 'last_name', 'id', 'avatar_resize')));
        $employees = $avatarOfEmploys = array();
        foreach ($projectEmployees as $projectEmployee) {
            $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                }
            }
            $avatarOfEmploys[$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['avatar_resize'];
        }
        $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_idd)));
        // edit get start end date from Phase
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) as MinStartDate',
                'MAX(phase_real_end_date) as MaxEndDate'
            )
        ));
        if (!empty($projectPhasePlans)) {
            $_datas['start_date'] = $projectPhasePlans[0][0]['MinStartDate'];
            $_datas['end_date'] = $projectPhasePlans[0][0]['MaxEndDate'];
            $this->Project->id = $id;
            $this->Project->save($_datas);
        }
        $this->set(compact('projectPhasePlans'));
        /**
         * Danh sach employee: PM, TECH, CHIEF
         */
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $listEmployeeManagers = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('type' => 'RA')
            ),
            'fields' => array('project_manager_id', 'is_backup', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listReadAccess = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA'
            ),
            'fields' => array('project_manager_id', 'is_profit_center', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listEmployeeManagers['RA'] = !empty($listReadAccess) && !empty($listReadAccess['RA']) ? $listReadAccess['RA'] : array();

        if (!empty($this->data['Project']['project_manager_id'])) {
            $listEmployeeManagers['PM'][$this->data['Project']['project_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['chief_business_id'])) {
            $listEmployeeManagers['CB'][$this->data['Project']['chief_business_id']] = 0;
        }
        if (!empty($this->data['Project']['technical_manager_id'])) {
            $listEmployeeManagers['TM'][$this->data['Project']['technical_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['functional_leader_id'])) {
            $listEmployeeManagers['FL'][$this->data['Project']['functional_leader_id']] = 0;
        }
        if (!empty($this->data['Project']['uat_manager_id'])) {
            $listEmployeeManagers['UM'][$this->data['Project']['uat_manager_id']] = 0;
        }
        /**
         * Lay cac phase co task
         */
        $phaseHaveTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('project_planed_phase_id', 'project_planed_phase_id')
        ));
        /**
         * Viet lai lan 2, get lai phase sau khi thuc hien save
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        /**
         * Change date 07/04/2014.
         * Get all family + sub family of company
         */
        $families = $subFamilies = array();
        $haveActivity = 'false';
        $this->loadModel('Family');
        $_families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields' => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeInfo['Company']['id'])));
        if (!empty($_families)) {
            foreach ($_families as $_family) {
                $dx = $_family['Family'];
                if (!empty($dx['parent_id'])) {
                    //sub family
                } else {
                    $families[$dx['id']] = $dx['name'];
                }
            }
            if (!empty($families)) {
                $this->loadModel('ActivityFamily');
                $subFamilies = $this->ActivityFamily->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => array_shift(array_keys($families))
                    ),
                    'fields' => array('id', 'name'),
                    'order' => array('name')
                ));
            }
        }
        if ($employeeInfo['Role']['name'] === 'admin' || $employeeInfo['Role']['name'] === 'pm') {
            /**
             * Kiem tra xem activity da co timesheet nao chua
             */
            if (!empty($this->data['Project']['activity_id']) || $this->data['Project']['activity_id'] != 0) {
                $this->loadModel('ActivityRequest');
                $this->loadModel('ActivityTask');
                $tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $this->data['Project']['activity_id']),
                    'fields' => array('id', 'id')
                ));
                if (!empty($tasks)) {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'OR' => array(
                                'activity_id' => $this->data['Project']['activity_id'],
                                'task_id' => $tasks
                            )
                        )
                    ));
                } else {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $this->data['Project']['activity_id'])
                    ));
                }
                if ($request != 0) {
                    $haveActivity = 'true';
                }
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeInfo['Company']['id']),
            'fields' => array('id', 'name')
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        // BudgetSetting
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        $this->set(compact('phasePlans', 'phaseHaveTasks', 'avatarOfEmploys', 'listEmployeeManagers', 'employBackups', 'employeeInfo', 'families', 'subFamilies', 'haveActivity', 'budgetCustomers', 'budget_settings'));
        $this->set(compact('project_name', 'employees', 'currency_name', 'name_company', 'changeProjectManager', 'profitCenters', 'adminSeeAllProjects', 'activate_family_linked_program'));
        $this->set('project_id', $id);
    }

    function your_form($id = null) {
        // redirect ve index neu ko phai la admin hoac pm
        $this->loadModels('BudgetSetting', 'Project', 'ProjectEmployeeManager', 'CompanyEmployeeReference', 'ProjectAmr', 'ProjectGlobalView', 'Activity', 'ProjectManagerUpdateField');
        $this->_checkRole(false, $id);
        // kiem tra PM co the change project manager select field
        $pmCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'project_id' => $id,
				'type !=' => 'RA'
            )
        ));
        // kiem tra PM o bang project
        $pmOfProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'id' => $id
            )
        ));		
        $pmCanChange = (!empty($pmCanChange) || !empty($pmOfProject) || $this->employee_info['CompanyEmployeeReference']['role_id'] == 2) ? true : false;
        $company_id = $this->employee_info['Company']['id'];
        $getPM = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id,
                'company_id' => $company_id,
            ),
            'fields' => array('project_manager_id')
        ));
        $id_PM = !empty($getPM['Project']['project_manager_id']) ? $getPM['Project']['project_manager_id'] : 0;
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name')
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $page = 'Details';
        $curentPage = 'your_form';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form');

        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id),
            'fields' => array('project_name', 'start_date', 'end_date', 'address', 'latlng', 'activity_id')
        ));
		$activated_of_actvity = 0;
		if(!empty( $projectName['Project']['activity_id'])){
			$activated_of_actvity = $this->Activity->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $projectName['Project']['activity_id']),
				'fields' => array('activated')
			));
			if(!empty($activated_of_actvity)) {
				$activated_of_actvity = $activated_of_actvity['Activity']['activated'];
			}
		}
        $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($projectName['Project']['start_date']));
        $projectDate = abs(strtotime($projectName['Project']['end_date']) - strtotime($projectName['Project']['start_date']));
        $initDate = 100;
        if( ($projectCurrentDate <= $projectDate) && !empty($projectDate))
            $initDate = floor(($projectCurrentDate / $projectDate) * 100);
        $projectArms = $this->ProjectAmr->find('first', array(
            'conditions' => array(
                'project_id' => $id,
            ),
            'fields' => array('weather'))
        );

        // image project global

        $listProjects = array();
        $globals = array();

        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)));
		
		$noFileExists = false;
        if ($projectGlobalView) {
				$link = trim($this->_getPathGlobal($id, 'global')
						. $projectGlobalView['ProjectGlobalView']['attachment']);
				if (!file_exists($link) && !is_file($link)) {
					$noFileExists = true;
				}
			
            $globals[$id] = array(
                'global' => $projectGlobalView,
                'file' => $noFileExists
            );
        }
		// Show employee has left when duplicate ridrect to here
		$employee_has_left = isset($_GET['show_employee']) ? $_GET['show_employee'] : false;
		if($employee_has_left){
			$this->getEmployeeHasLeft($id);
		}
		
		// Ticket #1082 Viet Nguyen
		// Get pm can update your_form fields.
		//
		$pm_update_field = array();
		if(!empty($this->companyConfigs['can_manage_your_form_field'])){
			$pm_employee_update = $this->ProjectManagerUpdateField->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('field', 'employee_id')
			));
			if(!empty($pm_employee_update)){
				foreach($pm_employee_update as $index => $value){
					$field = $value['ProjectManagerUpdateField']['field'];
					$employee_id = $value['ProjectManagerUpdateField']['employee_id'];
					$pm_update_field[$field][] = $employee_id;
				}
			}
		}
        $this->set('checkAvatar', $this->checkAvatar());
        $this->set(compact('page', 'budget_settings', 'id_PM', 'pmCanChange', 'projectArms', 'initDate', 'globals', 'projectGlobalView', 'activated_of_actvity', 'noFileExists', 'pm_update_field'));
    }
	function getEmployeeHasLeft($project_id){
		$this->loadModels('Employee', 'ProfitCenter', 'ProjectTask', 'ProjectTaskEmployeeRefer');
		$company_id = $this->employee_info['Company']['id'];
		$project_tasks =  $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
			),
			'fields' => array('id', 'task_title')
		));
		
		$task_id = !empty($project_tasks) ? Set::extract($project_tasks, '{n}.ProjectTask.id') : array();
		$project_tasks = !empty($project_tasks) ? Set::combine($project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask.task_title') : array();
		
		$employee_assigned_id = array();
		$pc_assiged_id = array();
		$resource_refer_id = array();
		if(!empty($task_id)){
			$resource_refer_id = $this->ProjectTaskEmployeeRefer->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $task_id,
				),
				'fields' => array('reference_id', 'is_profit_center', 'project_task_id')
			));
			
			foreach($resource_refer_id as $index => $value){
				$dx = $value['ProjectTaskEmployeeRefer'];
				if($dx['is_profit_center'] == 1){
					$pc_assiged_id[] = $dx['reference_id'];
				}else{
					$employee_assigned_id[] = $dx['reference_id'];
				}
			}
		}
		$listEmployeeNotActive = array();
		$listEmployeeNotActive_id = array();
		if(!empty($employee_assigned_id)){
			$listEmployeeNotActive = $this->Employee->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $employee_assigned_id,
					'actif !=' => 1,
					'OR' => array(
						'start_date IS NOT NULL',
						'start_date !=' => '0000-00-00',
						'start_date >' => date('Y-m-d', time())
					),
					'AND' => array(
						'OR' => array(
							'end_date IS NOT NULL',
							'end_date !=' => '0000-00-00',
							'end_date <' => date('Y-m-d', time())
						)
					)
				),
				'fields' => array('id', 'first_name', 'last_name')
			));
			$listEmployeeNotActive_id = !empty($listEmployeeNotActive) ? Set::extract($listEmployeeNotActive, '{n}.Employee.id') : array();
			$listEmployeeNotActive = !empty($listEmployeeNotActive) ? Set::combine($listEmployeeNotActive, '{n}.Employee.id', '{n}.Employee') : array();
			
		}
		$listProfitCenterActive = array();
		if(!empty($employee_assigned_id)){
			$listProfitCenterActive = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $pc_assiged_id,
				),
				'fields' => array('id', 'id')
			));
		}
		$list_task_need_update = array();
		if(!empty($resource_refer_id) && (!empty($employee_assigned_id) || !empty($pc_assiged_id))){
			foreach($resource_refer_id as $index => $value){
				$dx = $value['ProjectTaskEmployeeRefer'];
				$resource_id =  $dx['reference_id'];
				$task_id =  $dx['project_task_id'];
				if($dx['is_profit_center'] == 1 && !in_array($resource_id, $listProfitCenterActive)){
					if(!isset($list_task_need_update[$resource_id])) $list_task_need_update[$resource_id] = array();
					$list_task_need_update[$resource_id][$task_id] = $project_tasks[$task_id];
					
				}else if($dx['is_profit_center'] == 0 && in_array($resource_id, $listEmployeeNotActive_id)){
					if(!isset($list_task_need_update[$resource_id])) $list_task_need_update[$resource_id] = array();
					$list_task_need_update[$resource_id][$task_id] = $project_tasks[$task_id];
				}
			}
		}
		
		$this->set(compact('listEmployeeNotActive', 'list_task_need_update'));
	}
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $view_id = null) {

        $redirect = array('action' => 'index');
        $ajax = false;
        if (isset($_POST['ajax']))
            $ajax = $_POST['ajax'];
        if (isset($_GET['cate'])) {
            $redirect['?'] = array('cate' => $_GET['cate']);
        }
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project', true), 'error');
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect($redirect);
            }
        }
        if (!$this->_checkRole(true, $id)) {
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect($redirect);
            }
        }

        if (!$this->checkPMBeforeDeleteProject($id)) {
            $this->redirect($redirect);
        }

        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->cakeError('error404', array(array('url' => $id)));
        }
        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->cakeError('error404', array(array('url' => $id)));
                }
            }
        }
        // security
        $this->loadModel('Activity');
        $activityId = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('project' => $id),
            'fields' => array('id')
        ));
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'id')
        ));
        $request = $request_timesheet = 0;
        if (!empty($projectTasks)) {
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $projectTasks),
                'fields' => array('id', 'id')
            ));
            if (!empty($activityTasks)) {
                $request = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $activityTasks,
                        'status' => 2,
                    )
                ));
                $request_timesheet = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $activityTasks,
                        'status <>' => 2,
                    )
                ));
            }
        }
        if (!empty($activityId) && $activityId['Activity']['id']) {
            $request_activity = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId['Activity']['id'],
                    'status' => 2,
                )
            ));
            $ac_request_timesheet = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId['Activity']['id'],
                    'status <>' => 2,
                )
            ));
            $request += $request_activity;
            $request_timesheet += $ac_request_timesheet;
        }
        if ($request != 0 || $request_timesheet != 0) {
            if ($request != 0) {
                $this->Session->setFlash(__('The project have task(s) with consumed.', true), 'error');
            } else {
                $this->Session->setFlash(__('A task is used in a timesheet.', true), 'error');
            }
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Project->recursive = -1;
            $p = $this->Project->read(null, $id);
            if ($this->Project->delete($id)) {
                $this->writeLog($p, $this->employee_info, sprintf('Delete project `%s`', $p['Project']['project_name']), $p['Project']['company_id']);
                /**
                 * Delete staffing
                 */
                $this->loadModel('TmpStaffingSystem');
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $id), false);
                /**
                 * Delete task and assign task
                 */
                if (!empty($projectTasks)) {
                    $this->ProjectTask->deleteAll(array('ProjectTask.id' => $projectTasks), false);
                    $this->loadModel('ProjectTaskEmployeeRefer');
                    $this->ProjectTaskEmployeeRefer->deleteAll(array('project_task_id' => $projectTasks), false);
                }
                /**
                 * Delete linked activity
                 */
                if (!empty($activityId) && $activityId['Activity']['id']) {
                    // When you delete a project linked to an activity delete the project and the activity linked ( excepted if there are consumed )
                    $this->Activity->delete($activityId['Activity']['id'], false);
                    /**
                     * Xoa cac task linked
                     */
                    $this->ActivityTask->deleteAll(array('project_task_id' => $projectTasks), false);
                }
                $this->Session->setFlash(__('Deleted', true), 'success');
                if (is_numeric($view_id)) {
                    $this->redirect(array('controller' => 'user_views', 'action' => 'project_view', $view_id));
                } else {
                    if ($ajax == true) {
                        die(1);
                    } else {
                        $this->redirect($redirect);
                    }
                }
            }
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        if ($ajax == true) {
            die(1);
        } else {
            $this->redirect($redirect); // 
        }
    }

    private function checkPMBeforeDeleteProject($project_id) {
        // Employee allow delete project
        // PM has right delete project + PM is PM of this project
        $delete = true;
        $this->loadModel('ProjectEmployeeManager');
        // kiem tra PM co the change project manager select field
        $pmCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'project_id' => $project_id,
                'type' => 'PM'
            )
        ));
        // kiem tra PM o bang project
        $pmOfProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'id' => $project_id
            )
        ));

        $pmCanDelete = (!empty($pmCanChange) || !empty($pmOfProject)) ? true : false;

        if ($this->employee_info['Role']['name'] == 'pm' && (!$this->employee_info['Employee']['delete_a_project'] || !$pmCanDelete)) {
            $this->Session->setFlash(__('Employee not allow delete project', true), 'error');
            $delete = false;
        }
        return $delete;
    }

    /**
     * get_project_manager
     * Get project manager
     *
     * @return void
     * @access public
     */
    function get_project_manager($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'role_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
            $projectEmployees = $this->ProjectTask->Employee->find('all', array(
                'order' => 'last_name  ASC',
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
                ),
                'fields' => array('first_name', 'last_name', 'id')));
            $employees = array();
            foreach ($projectEmployees as $projectEmployee) {
                $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
                foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                    if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                        $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                    }
                }
            }
            foreach ($employees as $key => $employee) {
                $_output = '';
                foreach ($employee as $_id => $_val) {
                    $_output .= "<option value='" . $_id . "'>" . $_val . "</option>";
                }
                $employees[$key] = $_output;
            }
            echo json_encode($employees);
        }
        exit();
    }

    /**
     * get_phase
     * Get Phase
     *
     * @return void
     * @access public
     */
    function get_phase($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectPhase->recursive = -1;
            $phaseIds = $this->ProjectPhase->find('list', array('conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => array('ProjectPhase.phase_order ASC')));
            if (!empty($phaseIds)) {
                foreach ($phaseIds as $id => $phase_name) {
                    echo "<option value='" . $id . "'>" . $phase_name . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_status
     * Get status
     *
     * @return void
     * @access public
     */
    function get_status($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectStatus->recursive = -1;
            $statusIds = $this->ProjectStatus->find('list', array('conditions' => array('ProjectStatus.company_id' => $company_id)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_complexity
     * Get complexity
     *
     * @return void
     * @access public
     */
    function get_complexity($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectComplexity->recursive = -1;
            $statusIds = $this->ProjectComplexity->find('list', array('conditions' => array('ProjectComplexity.company_id' => $company_id, 'ProjectComplexity.display' => 1)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_priority
     * Get priority
     *
     * @return void
     * @access public
     */
    function get_priority($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectPriority->recursive = -1;
            $statusIds = $this->ProjectPriority->find('list', array('conditions' => array('ProjectPriority.company_id' => $company_id)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_currency
     * Get Currency
     *
     * @return void
     * @access public
     */
    function get_currency($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->Currency->recursive = -1;
            $statusIds = $this->Currency->find('list', array('conditions' => array('Currency.company_id' => $company_id)));
            if (!empty($statusIds)) {
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_program
     * Get program for project
     *
     * @return void
     * @access public
     */
    function get_program($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectAmrProgram->recursive = -1;
            $statusIds = $this->ProjectAmrProgram->find('list', array('conditions' => array('ProjectAmrProgram.company_id' => $company_id), 'fields' => array('amr_program')));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_project_type
     * Get typer for project
     *
     * @return void
     * @access public
     */
    function get_project_type($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectType->recursive = -1;
            $typeIds = $this->ProjectType->find('list', array('conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1), 'fields' => array('project_type')));
            if (!empty($typeIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($typeIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_project_sub_type
     * Get subtype for project
     *
     * @return void
     * @access public
     */
    function get_project_sub_type($id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectType->ProjectSubType->find('list', array('conditions' => array('ProjectSubType.project_type_id' => $id, 'ProjectSubType.display' => 1), 'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type')));
            if (!empty($list)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "'>" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub type--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub type--%s", true), '<option>', '</option>');
        }
    }

    /**
     * exportExcel
     *
     * @return void
     * @access public
     */
    function exportExcel($view_id = null) {
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));
        $view_content = $view_content["UserView"]["content"];
        if ($view_content == "") { //default view
            $view_content = '<user_view>
                                    <project_detail project_name = "Project Name" />
                                    <project_detail company_id = "Company" />
                                    <project_detail project_manager_id = "Project Manager" />
                                    <project_detail project_priority_id = "Priority" />
                                    <project_detail project_status_id = "Status" />
                                    <project_detail start_date = "Start Date" />
                            </user_view>';
        }
        $this->set('view_content', $view_content);
        $this->layout = 'excel';
        if ($this->is_sas)
            $projects = $this->Project->find('all');
        else {
            $projects = array();
            $sub_companies = $this->Project->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
            foreach ($sub_companies as $sub_company) {
                $projects = array_merge($projects, $this->Project->find('all', array('conditions' => array('Project.company_id' => $sub_company['Company']['id']))));
            }
        }
        $this->set('projects', $projects);
    }

    /**
     * exportExcelDetail
     * Export to Excel in project detail
     * @return void
     * @access public
     */
    function exportExcelFlashInfo($project_id = null) {
        /*
         * Security - 2015/03/11
         * check company id
         */
        $page = 'Details';
        // debug($project_id); exit;
        $project = $this->Project->find('first', array(
            'fields' => array('Project.id', 'Project.project_name', 'Project.company_id'),
            'conditions' => array('Project.id' => $project_id)
        ));

        if (!isset($this->employee_info['Company']['id']) || $this->employee_info['Company']['id'] != $project['Project']['company_id']) {
            $this->Session->setFlash(__('The project to export was not found', true), 'error');
            $this->redirect('/projects');
        }
        // debug($this->name_columna); exit;
        $this->set('columns', $this->name_columna);
        $employee_current = $this->employee_info['Employee']['id'];

        $employees = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'fullname'),
            'conditions' => array('is_sas' => 0),
            'order' => array('id' => 'ASC')
        ));
        $view_content = array(
            'project_code' => 'Project Code 1',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'project_manager' => 'Project Manager',
            'technical_manager' => 'Technical manager',
            'primary_objectives' => 'Primary Objectives',
            'engaged' => 'Consumed',
            'validated' => 'Planed',
            'weather' => 'Weather',
            'rank' => 'Rank',
            'overload' => 'Overload',
            'risk_comment' => 'Points de coordination',
            'kpi_comment' => 'Réalisation',
            'projectRisks' => 'Risques nouveaux',
            'todo' => 'Travaux à mener',
            'done' => 'Décisions',
        );
        $this->loadModels('Project', 'ProjectAmr', 'ProjectAmrProgram', 'Employee', 'ProjectBudgetSyn', 'LogSystem');

        $flash_data = array();
        $project = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('*')
        ));
        // debug($project); exit;
        $employee_name = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'first_name', 'last_name')
        ));
        $employee_name = !empty($employee_name) ? Set::combine($employee_name, '{n}.Employee.id', '{n}.Employee') : array();
        if (!empty($project)) {
            $flash_data['project_id'] = $project['Project']['id'];
            $flash_data['project_name'] = $project['Project']['project_name'];
            $flash_data['project_code'] = $project['Project']['project_code_1'];
            $flash_data['start_date'] = $project['Project']['start_date'];
            $flash_data['end_date'] = $project['Project']['start_date'];
            $flash_data['project_manager'] = $employee_name[$project['Project']['project_manager_id']]['first_name'] . ' ' . $employee_name[$project['Project']['project_manager_id']]['last_name'];
            $flash_data['technical_manager'] = '';
            if (!empty($project['Project']['technical_manager_id'])) {
                $flash_data['technical_manager'] = $employee_name[$project['Project']['technical_manager_id']]['first_name'] . ' ' . $employee_name[$project['Project']['technical_manager_id']]['last_name'];
            }
            if (!empty($project['Project']['primary_objectives'])) {
                $flash_data['primary_objectives'] = $project['Project']['primary_objectives'];
            }
            $_projectTask = $this->_getPorjectTask($project_id);
            $engaged = $_projectTask['engaged'];
            $validated = $_projectTask['validated'];

            // Consumed
            $flash_data['engaged'] = $engaged;

            // Workload
            $flash_data['validated'] = $validated;
        }
        $project_amr = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('*')
        ));
        $flash_data['weather'] = $flash_data['rank'] = '';
        if (!empty($project_amr)) {
            $flash_data['weather'] = $project_amr['ProjectAmr']['weather'];
            $flash_data['rank'] = $project_amr['ProjectAmr']['rank'];
        }


        $overload = $this->ProjectBudgetSyn->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => 6,
            ),
            'fields' => array('overload')
        ));

        if (!empty($overload)) {
            $flash_data['overload'] = $overload['ProjectBudgetSyn']['overload'];
        }

        // LOG SYSTEM
        $log_systems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model_id' => $project_id,
            ),
            'fields' => array('*'),
            'order' => array('created' => 'ASC')
        ));

        $risk_comment = $kpi_comment = $todo = $done = array();
        foreach ($log_systems as $key => $log_system) {
            if (!empty($log_system['LogSystem']) && !empty($log_system['LogSystem']['model'])) {
                if ($log_system['LogSystem']['model'] == 'ProjectRisk') {
                    $risk_comment[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'ProjectAmr') {
                    $kpi_comment[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'ToDo') {
                    $todo[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'Done') {
                    $done[] = $log_system;
                }
            }
        }
        $projectRisks = $this->ProjectRisk->find("first", array(
            'fields' => array('id', 'project_risk', 'updated'),
            'recursive' => -1, "conditions" => array('project_id' => $project_id),
            'order' => array('updated' => 'DESC')
        ));

        $is_avatar = $this->checkAvatar();
        $projectMilestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
            'order' => array('weight' => 'ASC')
        ));
        $projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $projectMilestones = Set::sort($projectMilestones, '{n}.milestone_date', 'asc');

        $this->set(compact('view_content', 'flash_data', 'is_avatar', 'risk_comment', 'kpi_comment', 'todo', 'done', 'projectMilestones', 'projectRisks'));
        $this->set('employees', $employees);
        $this->set('project_name', $project);
    }

    /**
     * project_detail_view
     *
     * @return void
     * @access public
     */
    function project_detail_view($view_id = null, $project_id = null) {
		$this->_checkRole(true, $project_id);
        if (!empty($this->data)) {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);
            $data_project_teams = $this->params['form'];
            if ($this->Project->save($this->data)) {
                $view_id = $this->params["form"]["ViewId"];
                $project_id = $this->params["form"]["ProjectId"];
                $this->Session->setFlash(__('The project has saved.', true));
            } else {
                $this->Session->setFlash(__('The project could not be saved. Please, try again.', true));
            }
            $this->redirect("/user_views/project_detail_view/" . $view_id . "/" . $project_id);
        }
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
        $datas = $this->ActivityRequest->find('all', array(
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
        $taskIdOfActivity = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        $_datas = $this->ActivityRequest->find('all', array(
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
        foreach ($_datas as $_data) {
            $employees[$_data['ActivityRequest']['employee_id']] = $_data['ActivityRequest']['employee_id'];
            // tinh sumactivity. 201/01/2017
            if (empty($_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]])) {
                $_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]] = 0;
            }
            $_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]] += $_data[0]['value'];
            //end
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach ($_sumEmployees as $id => $_sumEmployee) {
            $dataFromEmployees[$taskIdOfActivity[$id]][] = $_sumEmployee;
        }
        $rDatas = array();
        if (!empty($dataFromEmployees)) {
            foreach ($dataFromEmployees as $id => $dataFromEmployee) {
                foreach ($dataFromEmployee as $values) {
                    foreach ($values as $employ => $value) {
                        if (!isset($rDatas[$id][$employ])) {
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
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
        if (!empty($rDatas)) {
            foreach ($rDatas as $id => $rData) {
                if (in_array($id, array_keys($sumEmployees))) {
                    
                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if (!empty($sumEmployees)) {
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if (!empty($rDatas)) {
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
        if (!empty($sumEmployGroups)) {
            foreach ($sumEmployGroups as $key => $sumEmployGroup) {
                foreach ($sumEmployGroup as $acId => $values) {
                    foreach ($values as $employs => $value) {
                        if (!isset($sumEmployees[$acId][$employs])) {
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
     * Index
     *
     * @return void
     * @access public
     */
    public function index($viewId = null) {

        // debug($viewId);exit;
        $mobileEnabled = $this->isTouch();

        $this->loadModels('Menu', 'LogSystem', 'ProjectFinance', 'ProjectPhasePlan', 'Employee', 'ProfitCenter');
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        /**
         * Doc lai 2 tham so create va delete cua employee dang login
         */
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employee['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if (!empty($employInfors['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName'));
        $employee['Employee']['create_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? $employInfors['Employee']['create_a_project'] : 0;
        $employee['Employee']['delete_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? $employInfors['Employee']['delete_a_project'] : 0;
        $employee['Employee']['update_your_form'] = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? $employInfors['Employee']['update_your_form'] : 0;
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        /**
         * Multiples Sort Building
         */
        if (isset($_POST['multiSort'])) {
            $fieldSort = array();
            if ($_POST['actSort'] == 'add') {
                if ($_POST['flag'] != 0) {
                    $fieldSort = $this->Session->read('sFieldSort');
                } else {
                    $fieldSort = array();
                    $this->Session->write('sFieldSort', '');
                }
                $fieldSort[] = array('columnId' => $_POST['value'], 'sortAsc' => 1);
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if ($_POST['actSort'] == 'remove') {
                $fieldSort = $this->Session->read('sFieldSort');
                $fieldSort1 = array();
                foreach ($fieldSort as $array) {
                    if ($array['columnId'] != $_POST['value'])
                        $fieldSort1[] = array('columnId' => $array['columnId'], 'sortAsc' => $array['sortAsc']);
                }
                $this->Session->write('sFieldSort', $fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if ($_POST['actSort'] == 'update') {
                $fieldSort = $this->Session->read('sFieldSort');
                $count = count($fieldSort);
                for ($i = 0; $i < $count; $i++) {
                    if ($fieldSort[$i]['columnId'] == $_POST['value'])
                        $fieldSort[$i]['sortAsc'] = round($_POST['type']);
                }
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        /**
         * Su dung helper GanttVs
         */
        $this->helpers = array_merge($this->helpers, array('GanttVs'));
        /**
         * Lay screen mac dinh cua project
         */
        $checkDisplayProfileScreen = 1;
        $ACLController = '';
        $ACLAction = '';
        if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $screenDefaults = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $listScreen = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            if (empty($listScreen)) {
                $checkDisplayProfileScreen = 2;
            } else {
                $ACLController = $listScreen['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $listScreen['ProfileProjectManagerDetail']['functions'];
            }
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $screenDefaults['ProfileProjectManagerDetail']['functions'];
            }
        } else {
            $screenDefaults = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $ACLController = 'projects_preview';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
        }
        $this->set(compact('checkDisplayProfileScreen'));
        $screenDashboard = array();
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company_id,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));

        $screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();
        $this->set(compact('screenDashboard'));
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('id' => 'ASC')
        ));
        $logGroups = Set::combine($logSystems, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        /**
         * Extract url
         */
        extract(array_merge(array('cate' => null), $this->params['url']));
        /**
         * Check category
         */
        if (!empty($cate)) {
            if ($cate != 2) {
                $this->Session->write("App.status", $cate);
                if ($mobileEnabled)
                    $this->Session->write("App.mobileStatus", $cate);
            } else {
                $this->Session->write("App.status", $cate);
            }
        } else {
            $cate = $this->Session->read("App.status");
            if ($mobileEnabled) {
                $mobileCategory = $this->Session->read("App.mobileStatus");
                if ($mobileCategory) {
                    if ($mobileCategory != 2) {
                        $this->Session->write("App.mobileStatus", $mobileCategory);
                    }
                    $this->Session->write("App.mobileStatus", $mobileCategory);
                } else {
                    $cate = 1;
                    $this->Session->write("App.mobileStatus", 1);
                }
            } else {
                if ($cate) {
                    if ($cate != 2) {
                        $this->Session->write("App.status", $cate);
                    }
                    $this->Session->write("App.status", $cate);
                } else {
                    $cate = 1;
                    $this->Session->write("App.status", 1);
                }
            }
        }
        $checkStatus = -2; // -2: khong co gi ca. -1: Predefined. 0: Default
        if ($cate == 2) {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileOppView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatusOppor');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        } else {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatus');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        }
        /**
         * Lay column display of project
         */
        $fieldset = array();
        $viewGantt = false;
        $confirmGantt = array();
        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                'conditions' => array('UserView.id' => $viewId)));
            $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
            $confirmGantt = array(
                'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                'real' => !empty($fieldset['UserView']['real']) ? true : false,
                'type' => 3, /** mac dinh la year */
                'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
            );
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            }
            $checkStatus = $viewId;
            if ($cate == 2) {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileOppView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatusOppor', $checkStatus);
                }
            } else {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatus', $checkStatus);
                }
            }
        } else {
            if ($mobileEnabled) {
                $defaultView = $this->Project->UserView->UserStatusView->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'UserStatusView.employee_id' => $employee['Employee']['id'],
                        'View.model' => 'project',
                        'UserStatusView.mobile' => 1
                    ),
                    'joins' => array(
                        array(
                            'table' => 'user_views',
                            'alias' => 'View',
                            'type' => 'inner',
                            'conditions' => array('View.id = UserStatusView.user_view_id')
                        )
                    ),
                    'fields' => array('user_view_id')
                ));
                if ($defaultView) {
                    $defaultView['UserDefaultView']['user_view_id'] = $defaultView['UserStatusView']['user_view_id'];
                }
            } else {
                $defaultView = $this->Project->UserDefaultView->find('first', array(
                    'conditions' => array(
                        'employee_id' => $employee['Employee']['id'],
                        'model' => 'project'
                    ),
                    'recursive' => -1,
                    'fields' => array('user_view_id')
                ));
            }
            if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                $checkStatus = -2;
                $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                $fieldset = $this->Project->UserView->find('first', array(
                    'fields' => array('UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                    'conditions' => array('UserView.id' => $defaultView)));
                $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
                $confirmGantt = array(
                    'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                    'real' => !empty($fieldset['UserView']['real']) ? true : false,
                    'type' => 3, /** mac dinh la year */
                    'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                    'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                    'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                    'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
                );
                if (!empty($fieldset)) {
                    $fieldset = unserialize($fieldset['UserView']['content']);
                }
                $checkStatus = $viewId;
                $this->Session->write("App.PersonalizedDefault", true);
            } else {
                $checkStatus = -1;
                $fieldset = array(
                    'Project.project_name',
                    // 'Project.project_phase_id',
                    'Project.project_manager_id',
                        // 'Project.chief_business_id',
                        // 'Project.technical_manager_id',
                        // 'Project.project_priority_id',
                        // 'Project.project_status_id',
                        // 'Project.start_date',
                        // 'ProjectAmr.weather'
                );
                if ($mobileEnabled) {
                    $fieldset = array(
                        'Project.project_name',
                        'Project.project_manager_id',
                            // 'Project.project_phase_id',
                            // 'Project.start_date',
                            // 'ProjectAmr.weather'
                    );
                }
            }
        }
		$listFields = $fieldset;
		$display_widgets = array();
		foreach( $fieldset as $k => $field){
			preg_match('/^ProjectWidget.(.*)$/', $field, $matches);
			if( !empty($matches[0])){
				$display_widgets[] = $field;
				unset( $fieldset[$k]);
			}
		}
		$fieldset = array_values($fieldset);
        $checkFieldCurrentPhase = false;
        if (!empty($fieldset) && in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
		$checkReadAccess = false;
        if (!empty($fieldset) && in_array('Project.read_access', $fieldset)) {
            $checkReadAccess = true;
        }
        $checkFieldPurchase = false;
        foreach (array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid') as $v) {
            if (!empty($fieldset) && in_array($v, $fieldset)) {
                $checkFieldPurchase = true;
            }
        }
        $checkFieldMuti = false;
        for ($i = 1; $i < 10; $i++) {
            if (!empty($fieldset) && in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        $checkNextMileston = false;
        if (!empty($fieldset) && (in_array('Project.next_milestone_in_day', $fieldset) || in_array('Project.next_milestone_in_week', $fieldset))) {
            $checkNextMileston = true;
        }
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $noProjectManager = false;
        if (!in_array('Project.project_manager_id', $fieldset)) {
            $fieldset[] = 'Project.project_manager_id';
            $noProjectManager = true;
        }

        list($fieldset, $options) = $this->Project->parseViewField($fieldset);


        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if ($value == 'comment') {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }
        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        $fieldFinancementTwoPlus = !empty($options['contain']['ProjectFinanceTwoPlus']) ? $options['contain']['ProjectFinanceTwoPlus'] : array();
        unset($options['contain']['ProjectFinanceTwoPlus']);
        $options['fields'][] = 'last_modified';
        //regardless which fields are selected, these are mandatory
        $options['fields'][] = 'project_manager_id';
        $options['fields'][] = 'technical_manager_id';
        $options['fields'][] = 'chief_business_id';
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!in_array('category', $options['fields'])) {
            $options['fields'][] = 'category';
        }
        // array project task fields.
        $projectTaskFields = array(
            'Initialworkload',
            'Workload',
            'Overload',
            'Consumed',
            'InUsed',
            'ManualConsumed',
            'Completed',
            'Remain',
            'Amount€',
            '%progressorder',
            '%progressorder€',
            'UnitPrice',
            'Consumed€',
            'Remain€',
            'Workload€',
            'Estimated€',
        );
        /**
         * Lay fields in Finance
         */
        $checkEngedMd = $checkTaskEuro = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if ($value == 'internal_costs_engaged_md') {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkEngedMd = true;
                }
                if (in_array($value, $projectTaskFields)) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkTaskEuro = true;
                }
            }
        }
        $this->loadModel('ProjectFinanceTwoPlus');
        $financeFields = array();
        $LANG = Configure::read('Config.language');
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]), $this->ProjectFinanceTwoPlus->defaultField($employee["Company"]["id"]));
        /**
         * Lay project manager va thong tin confirm lien quan
         */
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'][] = array('Project.company_id' => $subCompanies);
                if (!empty($cate)) {
                    if ($cate == 5) {
                        $options['conditions'][] = array('Project.category' => array(1, 2));
                    } else if ($cate == 6) {
                        $options['conditions'][] = array('Project.category' => array(2));
                    } else {
                        $options['conditions'][] = array('Project.category' => $cate);
                    }
                }
            } else {
                $projects = $this->Project->ProjectTeam->ProjectFunctionEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'project_team_id'),
                    'conditions' => array(
                        'ProjectFunctionEmployeeRefer.employee_id' => $employee["Employee"]["id"])));
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_id', 'project_id'),
                    'conditions' => array('ProjectTeam.id' => $projects)
                ));
                $options['conditions'][] = array('Project.id' => $projects);
                if (!empty($cate)) {
                    $options['conditions'][] = array('Project.category' => $cate);
                }
            }
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'),
                        'conditions' => array('ProjectStatus.company_id' => $employee["Company"]["id"]))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
                        'conditions' => array('ProjectAmrProgram.company_id' => $employee["Company"]["id"]))));
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $employee["Company"]["id"],
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(array('ProfitCenter.company_id' => $employee["Company"]["id"]), null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list', array('conditions' => array('ProjectFunction.company_id' => $employee["Company"]["id"]))));
        } else {
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'))));
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list'));
        }
        if ($viewGantt) {
            $options['contain']['ProjectPhasePlan'] = array(
                'fields' => array(
                    'id',
                    'project_planed_phase_id',
                    'phase_planed_start_date',
                    'phase_planed_end_date',
                    'phase_real_start_date',
                    'phase_real_end_date',
                ),
                'ProjectPhase' => array('name', 'color'));
            $options['contain']['ProjectMilestone'] = array(
                'order' => 'milestone_date ASC',
                'fields' => array(
                    'project_milestone',
                    'milestone_date',
                    'validated',
                    'project_id'
            ));
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }

        if ($checkFieldPurchase) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if (in_array($value, array('purchases_sold', 'purchases_to_bill', 'purchases_billed', 'purchases_paid'))) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                }
            }
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        if ($checkNextMileston) {
            if (!empty($options['fields'])) {
                foreach ($options['fields'] as $key => $value) {
                    if ($value == 'next_milestone_in_day' || $value == 'next_milestone_in_week') {
                        unset($options['fields'][$key]);
                    }
                }
            }
        }

        // Update by VN
        // Nhung field nay ko co trong bang project amr,
        // Chi su dung field name de get data comment tu log system

        $fiedsNotExists = array('project_amr_scope', 'project_amr_budget_comment', 'project_amr_resource', 'project_amr_schedule', 'project_amr_technical');
        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if (in_array($value, $fiedsNotExists)) {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }
        /**
         * Lay thong tin project
         */
        $this->Project->recursive = 0;
        $this->Project->Behaviors->attach('Containable');
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->loadModels('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = $listProjectOfPM = array();
        $viewProjects = true;
        if ($role == 'pm') {
            $listProjectOfPM = $this->getProjectOfPM($employee, $employeeId);
        }
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employeeId,
                        'OR' => array(
							'is_profit_center' => 0,
							'is_profit_center is NULL'
						),
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employee["Company"]["id"],
                        'project_manager_id' => $employeeId
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        if (!empty($listProjectIds)) {
            $options['conditions'][] = array('Project.id' => $listProjectIds);
        }
        if ($checkReadAccess) {
            foreach ($options['fields'] as $key => $value) {
                if ($options['fields'][$key] == 'read_access') {
                    unset($options['fields'][$key]);
                    $options['contain']['ProjectEmployeeManager'] = array(
                        'fields' => array(
                            'project_manager_id',
                    ));
                }
            }
        }
        if ($viewProjects == false) {
            $projects = array();
        } else {
            $projects = $this->Project->find('all', $options);
        }
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));

        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
        $listProjectLinked = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.activity_id') : array();
        $this->_parseNew($listProjectLinked, $projects, $viewGantt);
        // get Task euro 02/03/2017.
        $TaskEuros = array();
        if ($checkTaskEuro) {
            $TaskEuros = $this->getTaskEuro($projectIds);
        }
        // get nextMilestone
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        if ($checkNextMileston) {
            list($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek) = $this->getNextMilestone($projectIds);
        }
        $this->loadModels('ProjectBudgetPurchase', 'ProjectBudgetPurchaseInvoice');
        $Purchase = array();
        if ($checkFieldPurchase) {
            $this->ProjectBudgetPurchase->cacheQueries = true;
            $this->ProjectBudgetPurchase->recursive = -1;
            $this->ProjectBudgetPurchase->Behaviors->attach('Containable');

            $_budgetSales = $this->ProjectBudgetPurchase->find('all', array(
                'contain' => array('ProjectBudgetPurchaseInvoice'),
                'conditions' => array('project_id' => $projectIds)
                    )
            );
            foreach ($_budgetSales as $key => $value) {
                $dx = $value['ProjectBudgetPurchase'];
                if (empty($Purchase[$dx['project_id']]['purchases_sold'])) {
                    $Purchase[$dx['project_id']]['purchases_sold'] = 0;
                }
                $Purchase[$dx['project_id']]['purchases_sold'] += $dx['sold'];
                if (!empty($value['ProjectBudgetPurchaseInvoice'])) {
                    foreach ($value['ProjectBudgetPurchaseInvoice'] as $_key => $_value) {
                        if (empty($Purchase[$_value['project_id']]['purchases_to_bill'])) {
                            $Purchase[$_value['project_id']]['purchases_to_bill'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_to_bill'] += $_value['billed'];
                        if (empty($Purchase[$_value['project_id']]['purchases_paid'])) {
                            $Purchase[$_value['project_id']]['purchases_paid'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_paid'] += $_value['paid'];
                        if (!empty($_value['effective_date']) && $_value['effective_date'] != '0000-00-00') {
                            if (empty($Purchase[$_value['project_id']]['purchases_billed'])) {
                                $Purchase[$_value['project_id']]['purchases_billed'] = 0;
                            }
                            $Purchase[$_value['project_id']]['purchases_billed'] += $_value['billed'];
                        }
                    }
                }
            }
        }
        $this->set(compact('listColorNextMil', 'listNextMilestoneByDay', 'listNextMilestoneByWeek', 'Purchase'));
        /* get project_copy_id */
        $copy_ids = Set::classicExtract($projects, '{n}.Project.project_copy_id');
        $copy_ids = array_unique($copy_ids);
        $projectCopies = array();
        if (!empty($copy_ids)) {
            $projectCopies = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $copy_ids
                ),
                'fields' => array('id', 'project_name')
            ));
        }
        $freeze_ids = array_unique(Set::classicExtract($projects, '{n}.Project.freeze_by'));
        $freezers = array();
        if (!empty($freeze_ids)) {
            $this->Employee->virtualFields['fullname'] = 'CONCAT(first_name, \' \', last_name)';
            $freezers = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $freeze_ids
                ),
                'fields' => array('id', 'fullname')
            ));
        }
        $this->set(compact('projectCopies', 'freezers'));
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $staffingSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee', 'profit_center'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('model', 'project_id', 'SUM(estimated) AS Total'),
            'group' => array('model', 'project_id'),
            'order' => array('project_id')
        ));
        $staffingSystems = !empty($staffingSystems) ? Set::combine($staffingSystems, '{n}.TmpStaffingSystem.project_id', '{n}.0.Total', '{n}.TmpStaffingSystem.model') : array();
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        if (!empty($pairs)) {
            $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
                'recursive' => -1,
                'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
                'fields' => array(
                    'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                    'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                    'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
                )
            ));
        }
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();

        $Internal = ClassRegistry::init('ProjectBudgetInternalDetail');
        $Internal->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $Internal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));
        $this->set('internalBudgets', $internalBudgets);
        /**
         * Lay ngay ket thuc theo ke hoach cua tat ca project
         */
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_keys($listProjectLinked),
                'NOT' => array(
                    'phase_planed_start_date' => '0000-00-00',
                    'phase_planed_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'project_id',
                'MAX(phase_planed_end_date) AS MaxEndDatePlan',
                'MAX(phase_real_end_date) AS MaxEndDateReal'
            ),
            'group' => array('project_id')
        ));
        $projectPhasePlans = !empty($projectPhasePlans) ? Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_id', '{n}.0') : array();
        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        $this->set(compact('consumedAndWorkloadForActivities', 'currentYears', 'provisionals'));
        /**
         * Lay du lieu o table finacement plus
         */
        $finances = $finEuros = $finPercents = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
            foreach ($fieldFinancements['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[1]) && $_field[1] == 'percent') {
                    $finPercents[] = 'ProjectFinancePlus.' . $field;
                } else {
                    $finEuros[] = 'ProjectFinancePlus.' . $field;
                }
            }
        }
        $financesTwoPlus = $finTwoPlusEuros = $finTwoPlusPercent = array();
        $startFinanceTwoPlus = $endFinanceTwoPlus = date('Y', time());
        $this->loadModels('ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        if (!empty($fieldFinancementTwoPlus['fields'])) {
            $_financesTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array('project_id', 'year', 'budget_initial', 'budget_revised', 'last_estimated', 'engaged', 'bill', 'disbursed'),
            ));
            foreach ($_financesTwoPlus as $key => $value) {
                $dx = $value['ProjectFinanceTwoPlusDetail'];
                // tinh tung nam.
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] += $dx['disbursed'];
                // tinh total
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial'] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised'] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated'])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated'] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged'])) {
                    $financesTwoPlus[$dx['project_id']]['engaged'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged'] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill'])) {
                    $financesTwoPlus[$dx['project_id']]['bill'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill'] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed'])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed'] += $dx['disbursed'];
            }
            $financeTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(year) AS startDate',
                    'MAX(year) AS endDate'
                )
            ));
            $financeTwoDates = $this->ProjectFinanceTwoPlusDate->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(start) AS fonStart',
                    'MAX(end) AS fonEnd'
                )
            ));
            $startFinanceTwoPlus = !empty($financeTwoPlus[0][0]['startDate']) ? $financeTwoPlus[0][0]['startDate'] : date('Y', time());
            $endFinanceTwoPlus = !empty($financeTwoPlus[0][0]['endDate']) ? $financeTwoPlus[0][0]['endDate'] : date('Y', time());
            if (!empty($financeTwoDates) && !empty($financeTwoDates[0][0])) {
                $financeTwoDates = $financeTwoDates[0][0];
                $startFinanceTwoPlus = (date('Y', $financeTwoDates['fonStart']) < $startFinanceTwoPlus) ? date('Y', $financeTwoDates['fonStart']) : $startFinanceTwoPlus;
                $endFinanceTwoPlus = (date('Y', $financeTwoDates['fonEnd']) > $endFinanceTwoPlus) ? date('Y', $financeTwoDates['fonEnd']) : $endFinanceTwoPlus;
            }
            foreach ($fieldFinancementTwoPlus['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[0]) && $_field[0] == 'percent') {
                    $finTwoPlusPercent[] = 'ProjectFinanceTwoPlus.' . $field;
                } else {
                    $finTwoPlusEuros[] = 'ProjectFinanceTwoPlus.' . $field;
                }
            }
        }
        $this->set(compact('finances', 'finEuros', 'finPercents', 'financesTwoPlus', 'startFinanceTwoPlus', 'endFinanceTwoPlus', 'finTwoPlusEuros', 'finTwoPlusPercent'));
        /*
         * Manual consumed
         */
        $this->loadModel('ProjectTask');
        $this->ProjectTask->virtualFields['manual'] = 'SUM(manual_consumed)';
        $tasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array(
                'project_id',
                'manual'
            ),
            'group' => array('project_id')
        ));
        $this->set('manualData', $tasks);
        /**
         * Dung nhieu view trong 1 controller. Neu cate = 2 chuyen den view: opportunity.
         */
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $appstatus = $this->Session->read("App.status");
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        //priorities
        $priorities = $this->Project->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'priority')
        ));
        $this->set('priorities', $priorities);
        //editable list, available for pm only
        $this->set('isPM', $this->employee_info['CompanyEmployeeReference']['role_id'] == 3);
        $this->set('isAdmin', $this->employee_info['CompanyEmployeeReference']['role_id'] == 2);
        $this->set('editable', $this->canModifyProject($projectIds));
        //log system for done & todo
        if (!empty($projectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $projectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr' or model = 'Scope' or model = 'Schedule' or model = 'Budget' or model = 'Resources' or model = 'Technical') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        // get value for dialog vision task
        $projectStatus = $this->Project->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $projectProgram = $this->Project->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->Project->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_amr_program_id' => array_keys($projectProgram)
            ),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_name')
        ));
        $listProfitCenter = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname')
        ));
        $listProjectCode = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_code_1', 'project_code_2')
        ));
        $_listProjectCode = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_1') : array();
        $_listProjectCode1 = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_2') : array();
        if (!empty($_listProjectCode)) {
            $_listProjectCode = array_unique($_listProjectCode);
            $_listProjectCode = array_combine($_listProjectCode, $_listProjectCode);
            unset($_listProjectCode['']);
        }
        if (!empty($_listProjectCode1)) {
            $_listProjectCode1 = array_unique($_listProjectCode1);
            $_listProjectCode1 = array_combine($_listProjectCode1, $_listProjectCode1);
            unset($_listProjectCode1['']);
        }
        $_milestone = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_milestone', 'project_milestone')
        ));
        // get company config
        $this->loadModel('CompanyConfig');
        $showTaskVision = $this->CompanyConfig->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'display_visions'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showTaskVision = !empty($showTaskVision) ? $showTaskVision['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('projectStatus', 'projectProgram', 'projectSubProgram', 'listProfitCenter', '_listProjectCode', '_listProjectCode1', 'showTaskVision', 'listEmployee', '_milestone'));
        //end ----
        $this->loadModel('ProjectType');
        $listprojectTypes = $this->Project->ProjectType->find('list', array(
            'fields' => array('ProjectType.id', 'ProjectType.project_type'),
            'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1)
        ));
        $listprojectSubTypes = $this->Project->ProjectSubType->find('list', array(
            'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
            'conditions' => array('ProjectSubType.project_type_id' => array_keys($listprojectTypes), 'ProjectSubType.display' => 1)
        ));
        //Lay Budget settings
        $this->loadModel('BudgetSetting');
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $displayExpectation = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
                'display' => 1
            ),
        ));
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $_f = array('id', 'name_eng');
        } else {
            $_f = array('id', 'name_fre');
        }
        $screenExpec = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
            ),
            'fields' => $_f
        ));
        $this->loadModel('HistoryFilter');
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'projects/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->loadModel('ProjectEmployeeManager');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            ),
            'fields' => array('project_id', 'project_id')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $_listAvata = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model_id' => $projectIds
            )
        ));
        $listAvata = $listEmp = array();
        foreach ($_listAvata as $value) {
            $dx = $value['LogSystem'];
            $listAvata[$dx['model'] . '-' . $dx['model_id']] = $dx['employee_id'];
            $listEmp[$dx['model'] . '-' . $dx['model_id']] = $this->getNameEmployee($dx['update_by_employee']);
        }

        // data filter box

        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
        $listProjectManager = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.id', 'Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listProjectManager = !empty($listProjectManager) ? Set::combine($listProjectManager, '{n}.Project.id', '{n}.Employee') : array();


        $listPMFields = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listPMFields = !empty($listPMFields) ? Set::combine($listPMFields, '{n}.Employee.id', '{n}.Employee') : array();

        /* Edit by Huynh 12-11-2018
         * Add PM From project_employee_managers
         */
        $list_all_pm = array();
        $list_pm_avatar = array();
        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
        if (!empty($projectIds)) {
            $list_all_pm = $this->ProjectEmployeeManager->find('all', array(
                'fields' => array('project_id', 'project_manager_id'),
                'conditions' => array(
                    'project_id' => $projectIds,
                    'type' => 'PM',
                    'is_profit_center' => 0
                ),
                'order' => 'project_id ASC'
            ));
            $list_pm_avatar = !empty($projects) ? Set::classicExtract($list_all_pm, '{n}.ProjectEmployeeManager.project_manager_id') : array();
            $list_all_pm = !empty($list_all_pm) ? Set::combine($list_all_pm, '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager.project_id') : '';
        }
        $list_pm_avatar = array_unique(array_merge($list_pm_avatar, Set::classicExtract($projects, '{n}.Project.project_manager_id')));
        for ($_index = 0; $_index < count($projects); $_index++) {
            $this_id = $projects[$_index]['Project']['id'];
            $old_pm = !empty($projects[$_index]['Project']['project_manager_id']) ? $projects[$_index]['Project']['project_manager_id'] : '';
            $new_pm = $old_pm ? array($old_pm => $old_pm) : array();
            if (isset($list_all_pm[$this_id])) {
                $new_pm = array_unique(array_merge($new_pm, $list_all_pm[$this_id]));
            }
            $projects[$_index]['Project']['project_manager_id'] = array_values($new_pm);
        }
        $list_avatar = $this->requestAction('employees/get_list_avatar/', array('pass' => array($list_pm_avatar)));
        $this->set('list_avatar', $list_avatar);

        /* END Edit by Huynh 12-11-2018
         * Add PM From project_employee_managers
         */
			
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
		
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id']), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set(compact('logs', 'currency_name', 'appstatus', 'staffingSystems', 'projectPhasePlans', 'budgetSyns', 'ACLController', 'ACLAction', 'logGroups', 'fieldset', 'projects', 'viewId', 'noProjectManager', 'employee', 'checkStatus', 'personDefault', 'financeFields', 'LANG', 'viewGantt', 'confirmGantt', 'cate', 'listPc', 'listprojectTypes', 'listprojectSubTypes', 'budget_settings', 'checkEngedMd', 'TaskEuros', 'projectTaskFields', 'displayExpectation'));
        $this->set(compact('screenExpec', 'savePosition', 'listEmployeeManager', 'employee_id', 'listAvata', 'listEmp', 'listProgramFields', 'listProjectManager', 'listPMFields', 'listProjectOfPM', 'loadFilter'));
        $this->set('checkAvatar', $this->checkAvatar());
        if ($cate == 2) {
            $this->Session->write("App.status_oppor", 2);
            //if( !$mobileEnabled )$this->action = 'opportunity';
        } else {
            $this->Session->delete('App.status_oppor');
        }
        $employeeHasAvatar = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'avatar_resize !=' => null,
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id')
        ));
        $employeeAdminHasAvatar = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'avatar_resize !=' => null,
                'company_id' => null
            ),
            'fields' => array('id')
        ));
	
        $employeeHasAvatar = array_merge($employeeHasAvatar, $employeeAdminHasAvatar);
        $this->set('employeeHasAvatar', $employeeHasAvatar);
    }

    private function getProjectOfPM($employee, $employeeId) {
        $this->loadModels('ProjectEmployeeManager');
        $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $employeeId,
                'OR' => array(
					'is_profit_center' => 0,
					'is_profit_center is NULL'
				),
                'type' => 'PM',
            ),
            'fields' => array('project_id', 'project_id')
        ));
        $listProjectOfEmployManager = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $employee["Company"]["id"],
                'project_manager_id' => $employeeId
            ),
            'fields' => array('id', 'id')
        ));
        $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectOfEmployManager);
        return $listProjectIds;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index_update($viewId = null) {
        $mobileEnabled = $this->isTouch();
        $this->loadModels('Menu', 'LogSystem', 'ProjectFinance', 'ProjectPhasePlan', 'Employee', 'ProfitCenter');
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        /**
         * Doc lai 2 tham so create va delete cua employee dang login
         */
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employee['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if (!empty($employInfors['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName'));
        $employee['Employee']['create_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? $employInfors['Employee']['create_a_project'] : 0;
        $employee['Employee']['delete_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? $employInfors['Employee']['delete_a_project'] : 0;
        $employee['Employee']['update_your_form'] = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? $employInfors['Employee']['update_your_form'] : 0;
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        /**
         * Multiples Sort Building
         */
        if (isset($_POST['multiSort'])) {
            $fieldSort = array();
            if ($_POST['actSort'] == 'add') {
                if ($_POST['flag'] != 0) {
                    $fieldSort = $this->Session->read('sFieldSort');
                } else {
                    $fieldSort = array();
                    $this->Session->write('sFieldSort', '');
                }
                $fieldSort[] = array('columnId' => $_POST['value'], 'sortAsc' => 1);
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if ($_POST['actSort'] == 'remove') {
                $fieldSort = $this->Session->read('sFieldSort');
                $fieldSort1 = array();
                foreach ($fieldSort as $array) {
                    if ($array['columnId'] != $_POST['value'])
                        $fieldSort1[] = array('columnId' => $array['columnId'], 'sortAsc' => $array['sortAsc']);
                }
                $this->Session->write('sFieldSort', $fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if ($_POST['actSort'] == 'update') {
                $fieldSort = $this->Session->read('sFieldSort');
                $count = count($fieldSort);
                for ($i = 0; $i < $count; $i++) {
                    if ($fieldSort[$i]['columnId'] == $_POST['value'])
                        $fieldSort[$i]['sortAsc'] = round($_POST['type']);
                }
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        /**
         * Su dung helper GanttVs
         */
        $this->helpers = array_merge($this->helpers, array('GanttVs'));
        /**
         * Lay screen mac dinh cua project
         */
        $checkDisplayProfileScreen = 1;
        $ACLController = '';
        $ACLAction = '';
        if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $screenDefaults = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $listScreen = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            if (empty($listScreen)) {
                $checkDisplayProfileScreen = 2;
            } else {
                $ACLController = $listScreen['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $listScreen['ProfileProjectManagerDetail']['functions'];
            }
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $screenDefaults['ProfileProjectManagerDetail']['functions'];
            }
        } else {
            $screenDefaults = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $ACLController = 'projects_preview';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
        }
        $this->set(compact('checkDisplayProfileScreen'));
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('id' => 'ASC')
        ));
        $logGroups = Set::combine($logSystems, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        /**
         * Extract url
         */
        extract(array_merge(array('cate' => null), $this->params['url']));
        /**
         * Check category
         */
        if (!empty($cate)) {
            if ($cate != 2) {
                $this->Session->write("App.status", $cate);
                if ($mobileEnabled)
                    $this->Session->write("App.mobileStatus", $cate);
            } else {
                $this->Session->write("App.status", $cate);
            }
        } else {
            $cate = $this->Session->read("App.status");
            if ($mobileEnabled) {
                $mobileCategory = $this->Session->read("App.mobileStatus");
                if ($mobileCategory) {
                    if ($mobileCategory != 2) {
                        $this->Session->write("App.mobileStatus", $mobileCategory);
                    }
                    $this->Session->write("App.mobileStatus", $mobileCategory);
                } else {
                    $cate = 1;
                    $this->Session->write("App.mobileStatus", 1);
                }
            } else {
                if ($cate) {
                    if ($cate != 2) {
                        $this->Session->write("App.status", $cate);
                    }
                    $this->Session->write("App.status", $cate);
                } else {
                    $cate = 1;
                    $this->Session->write("App.status", 1);
                }
            }
        }
        $checkStatus = -2; // -2: khong co gi ca. -1: Predefined. 0: Default
        if ($cate == 2) {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileOppView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatusOppor');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        } else {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatus');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        }
        /**
         * Lay column display of project
         */
        $fieldset = array();
        $viewGantt = false;
        $confirmGantt = array();
        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                'conditions' => array('UserView.id' => $viewId)));
            $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
            $confirmGantt = array(
                'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                'real' => !empty($fieldset['UserView']['real']) ? true : false,
                'type' => 3, /** mac dinh la year */
                'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
            );
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            }
            $checkStatus = $viewId;
            if ($cate == 2) {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileOppView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatusOppor', $checkStatus);
                }
            } else {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatus', $checkStatus);
                }
            }
        } else {
            if ($mobileEnabled) {
                $defaultView = $this->Project->UserView->UserStatusView->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'UserStatusView.employee_id' => $employee['Employee']['id'],
                        'View.model' => 'project',
                        'UserStatusView.mobile' => 1
                    ),
                    'joins' => array(
                        array(
                            'table' => 'user_views',
                            'alias' => 'View',
                            'type' => 'inner',
                            'conditions' => array('View.id = UserStatusView.user_view_id')
                        )
                    ),
                    'fields' => array('user_view_id')
                ));
                if ($defaultView) {
                    $defaultView['UserDefaultView']['user_view_id'] = $defaultView['UserStatusView']['user_view_id'];
                }
            } else {
                $defaultView = $this->Project->UserDefaultView->find('first', array(
                    'conditions' => array(
                        'employee_id' => $employee['Employee']['id'],
                        'model' => 'project'
                    ),
                    'recursive' => -1,
                    'fields' => array('user_view_id')
                ));
            }
            if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                $checkStatus = -2;
                $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                $fieldset = $this->Project->UserView->find('first', array(
                    'fields' => array('UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                    'conditions' => array('UserView.id' => $defaultView)));
                $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
                $confirmGantt = array(
                    'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                    'real' => !empty($fieldset['UserView']['real']) ? true : false,
                    'type' => 3, /** mac dinh la year */
                    'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                    'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                    'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                    'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
                );
                if (!empty($fieldset)) {
                    $fieldset = unserialize($fieldset['UserView']['content']);
                }
                $checkStatus = $viewId;
                $this->Session->write("App.PersonalizedDefault", true);
            } else {
                $checkStatus = -1;
                $fieldset = array(
                    'Project.project_name',
                    // 'Project.project_phase_id',
                    'Project.project_manager_id',
                        // 'Project.chief_business_id',
                        // 'Project.technical_manager_id',
                        // 'Project.project_priority_id',
                        // 'Project.project_status_id',
                        // 'Project.start_date',
                        // 'ProjectAmr.weather'
                );
                if ($mobileEnabled) {
                    $fieldset = array(
                        'Project.project_name',
                        'Project.project_manager_id',
                            // 'Project.project_phase_id',
                            // 'Project.start_date',
                            // 'ProjectAmr.weather'
                    );
                }
            }
        }
        $checkFieldCurrentPhase = false;
        if (in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
        $checkFieldPurchase = false;
        foreach (array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid') as $v) {
            if (in_array($v, $fieldset)) {
                $checkFieldPurchase = true;
            }
        }
        $checkFieldMuti = false;
        for ($i = 1; $i < 10; $i++) {
            if (in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        $checkNextMileston = false;
        if (in_array('Project.next_milestone_in_day', $fieldset) || in_array('Project.next_milestone_in_week', $fieldset)) {
            $checkNextMileston = true;
        }
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $noProjectManager = false;
        if (!in_array('Project.project_manager_id', $fieldset)) {
            $fieldset[] = 'Project.project_manager_id';
            $noProjectManager = true;
        }
        list($fieldset, $options) = $this->Project->parseViewField($fieldset);
        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if ($value == 'comment') {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }
        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        $fieldFinancementTwoPlus = !empty($options['contain']['ProjectFinanceTwoPlus']) ? $options['contain']['ProjectFinanceTwoPlus'] : array();
        unset($options['contain']['ProjectFinanceTwoPlus']);
        $options['fields'][] = 'last_modified';
        //regardless which fields are selected, these are mandatory
        $options['fields'][] = 'project_manager_id';
        $options['fields'][] = 'technical_manager_id';
        $options['fields'][] = 'chief_business_id';
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!in_array('category', $options['fields'])) {
            $options['fields'][] = 'category';
        }
        // array project task fields.
        $projectTaskFields = array(
            'Initialworkload',
            'Workload',
            'Overload',
            'Consumed',
            'InUsed',
            'ManualConsumed',
            'Completed',
            'Remain',
            'Amount€',
            '%progressorder',
            '%progressorder€',
            'UnitPrice',
            'Consumed€',
            'Remain€',
            'Workload€',
            'Estimated€',
        );
        /**
         * Lay fields in Finance
         */
        $checkEngedMd = $checkTaskEuro = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if ($value == 'internal_costs_engaged_md') {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkEngedMd = true;
                }
                if (in_array($value, $projectTaskFields)) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkTaskEuro = true;
                }
            }
        }
        $this->loadModel('ProjectFinanceTwoPlus');
        $financeFields = array();
        $LANG = Configure::read('Config.language');
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]), $this->ProjectFinanceTwoPlus->defaultField($employee["Company"]["id"]));
        /**
         * Lay project manager va thong tin confirm lien quan
         */
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'][] = array('Project.company_id' => $subCompanies);
                if (!empty($cate)) {
                    if ($cate == 5) {
                        $options['conditions'][] = array('Project.category' => array(1, 2));
                    } else if ($cate == 6) {
                        $options['conditions'][] = array('Project.category' => array(2));
                    } else {
                        $options['conditions'][] = array('Project.category' => $cate);
                    }
                }
            } else {
                $projects = $this->Project->ProjectTeam->ProjectFunctionEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'project_team_id'),
                    'conditions' => array(
                        'ProjectFunctionEmployeeRefer.employee_id' => $employee["Employee"]["id"])));
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_id', 'project_id'),
                    'conditions' => array('ProjectTeam.id' => $projects)
                ));
                $options['conditions'][] = array('Project.id' => $projects);
                if (!empty($cate)) {
                    $options['conditions'][] = array('Project.category' => $cate);
                }
            }
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'),
                        'conditions' => array('ProjectStatus.company_id' => $employee["Company"]["id"]))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
                        'conditions' => array('ProjectAmrProgram.company_id' => $employee["Company"]["id"]))));
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $employee["Company"]["id"],
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(array('ProfitCenter.company_id' => $employee["Company"]["id"]), null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list', array('conditions' => array('ProjectFunction.company_id' => $employee["Company"]["id"]))));
        } else {
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'))));
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list'));
        }
        if ($viewGantt) {
            $options['contain']['ProjectPhasePlan'] = array(
                'fields' => array(
                    'id',
                    'project_planed_phase_id',
                    'phase_planed_start_date',
                    'phase_planed_end_date',
                    'phase_real_start_date',
                    'phase_real_end_date',
                ),
                'ProjectPhase' => array('name', 'color'));
            $options['contain']['ProjectMilestone'] = array(
                'order' => 'milestone_date ASC',
                'fields' => array(
                    'project_milestone',
                    'milestone_date',
                    'validated',
                    'project_id'
            ));
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }
        if ($checkFieldPurchase) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if (in_array($value, array('purchases_sold', 'purchases_to_bill', 'purchases_billed', 'purchases_paid'))) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                }
            }
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        if ($checkNextMileston) {
            if (!empty($options['fields'])) {
                foreach ($options['fields'] as $key => $value) {
                    if ($value == 'next_milestone_in_day' || $value == 'next_milestone_in_week') {
                        unset($options['fields'][$key]);
                    }
                }
            }
        }
        /**
         * Lay thong tin project
         */
        $this->Project->recursive = 0;
        $this->Project->Behaviors->attach('Containable');
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->loadModels('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employeeId,
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employee["Company"]["id"],
                        'project_manager_id' => $employeeId
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        if (!empty($listProjectIds)) {
            $options['conditions'][] = array('Project.id' => $listProjectIds);
        }
        if ($viewProjects == false) {
            $projects = array();
        } else {
            $projects = $this->Project->find('all', $options);
        }
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));

        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
        $listProjectLinked = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.activity_id') : array();
        $this->_parseNew($listProjectLinked, $projects, $viewGantt);
        // get Task euro 02/03/2017.
        $TaskEuros = array();
        if ($checkTaskEuro) {
            $TaskEuros = $this->getTaskEuro($projectIds);
        }
        // get nextMilestone
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        if ($checkNextMileston) {
            list($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek) = $this->getNextMilestone($projectIds);
        }
        $this->loadModels('ProjectBudgetPurchase', 'ProjectBudgetPurchaseInvoice');
        $Purchase = array();
        if ($checkFieldPurchase) {
            $this->ProjectBudgetPurchase->cacheQueries = true;
            $this->ProjectBudgetPurchase->recursive = -1;
            $this->ProjectBudgetPurchase->Behaviors->attach('Containable');

            $_budgetSales = $this->ProjectBudgetPurchase->find('all', array(
                'contain' => array('ProjectBudgetPurchaseInvoice'),
                'conditions' => array('project_id' => $projectIds)
                    )
            );
            foreach ($_budgetSales as $key => $value) {
                $dx = $value['ProjectBudgetPurchase'];
                if (empty($Purchase[$dx['project_id']]['purchases_sold'])) {
                    $Purchase[$dx['project_id']]['purchases_sold'] = 0;
                }
                $Purchase[$dx['project_id']]['purchases_sold'] += $dx['sold'];
                if (!empty($value['ProjectBudgetPurchaseInvoice'])) {
                    foreach ($value['ProjectBudgetPurchaseInvoice'] as $_key => $_value) {
                        if (empty($Purchase[$_value['project_id']]['purchases_to_bill'])) {
                            $Purchase[$_value['project_id']]['purchases_to_bill'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_to_bill'] += $_value['billed'];
                        if (empty($Purchase[$_value['project_id']]['purchases_paid'])) {
                            $Purchase[$_value['project_id']]['purchases_paid'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_paid'] += $_value['paid'];
                        if (!empty($_value['effective_date']) && $_value['effective_date'] != '0000-00-00') {
                            if (empty($Purchase[$_value['project_id']]['purchases_billed'])) {
                                $Purchase[$_value['project_id']]['purchases_billed'] = 0;
                            }
                            $Purchase[$_value['project_id']]['purchases_billed'] += $_value['billed'];
                        }
                    }
                }
            }
        }
        $this->set(compact('listColorNextMil', 'listNextMilestoneByDay', 'listNextMilestoneByWeek', 'Purchase'));
        /* get project_copy_id */
        $copy_ids = Set::classicExtract($projects, '{n}.Project.project_copy_id');
        $copy_ids = array_unique($copy_ids);
        $projectCopies = array();
        if (!empty($copy_ids)) {
            $projectCopies = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $copy_ids
                ),
                'fields' => array('id', 'project_name')
            ));
        }
        $freeze_ids = array_unique(Set::classicExtract($projects, '{n}.Project.freeze_by'));
        $freezers = array();
        if (!empty($freeze_ids)) {
            $this->Employee->virtualFields['fullname'] = 'CONCAT(first_name, \' \', last_name)';
            $freezers = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $freeze_ids
                ),
                'fields' => array('id', 'fullname')
            ));
        }
        $this->set(compact('projectCopies', 'freezers'));
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $staffingSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee', 'profit_center'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('model', 'project_id', 'SUM(estimated) AS Total'),
            'group' => array('model', 'project_id'),
            'order' => array('project_id')
        ));
        $staffingSystems = !empty($staffingSystems) ? Set::combine($staffingSystems, '{n}.TmpStaffingSystem.project_id', '{n}.0.Total', '{n}.TmpStaffingSystem.model') : array();
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        if (!empty($pairs)) {
            $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
                'recursive' => -1,
                'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
                'fields' => array(
                    'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                    'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                    'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
                )
            ));
        }
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();

        $Internal = ClassRegistry::init('ProjectBudgetInternalDetail');
        $Internal->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $Internal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));
        $this->set('internalBudgets', $internalBudgets);
        /**
         * Lay ngay ket thuc theo ke hoach cua tat ca project
         */
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_keys($listProjectLinked),
                'NOT' => array(
                    'phase_planed_start_date' => '0000-00-00',
                    'phase_planed_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'project_id',
                'MAX(phase_planed_end_date) AS MaxEndDatePlan',
                'MAX(phase_real_end_date) AS MaxEndDateReal'
            ),
            'group' => array('project_id')
        ));
        $projectPhasePlans = !empty($projectPhasePlans) ? Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_id', '{n}.0') : array();
        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        $this->set(compact('consumedAndWorkloadForActivities', 'currentYears', 'provisionals'));
        /**
         * Lay du lieu o table finacement plus
         */
        $finances = $finEuros = $finPercents = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
            foreach ($fieldFinancements['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[1]) && $_field[1] == 'percent') {
                    $finPercents[] = 'ProjectFinancePlus.' . $field;
                } else {
                    $finEuros[] = 'ProjectFinancePlus.' . $field;
                }
            }
        }
        $financesTwoPlus = $finTwoPlusEuros = $finTwoPlusPercent = array();
        $startFinanceTwoPlus = $endFinanceTwoPlus = date('Y', time());
        $this->loadModels('ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        if (!empty($fieldFinancementTwoPlus['fields'])) {
            $_financesTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array('project_id', 'year', 'budget_initial', 'budget_revised', 'last_estimated', 'engaged', 'bill', 'disbursed'),
            ));
            foreach ($_financesTwoPlus as $key => $value) {
                $dx = $value['ProjectFinanceTwoPlusDetail'];
                // tinh tung nam.
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] += $dx['disbursed'];
                // tinh total
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial'] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised'] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated'])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated'] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged'])) {
                    $financesTwoPlus[$dx['project_id']]['engaged'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged'] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill'])) {
                    $financesTwoPlus[$dx['project_id']]['bill'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill'] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed'])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed'] += $dx['disbursed'];
            }
            $financeTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(year) AS startDate',
                    'MAX(year) AS endDate'
                )
            ));
            $financeTwoDates = $this->ProjectFinanceTwoPlusDate->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(start) AS fonStart',
                    'MAX(end) AS fonEnd'
                )
            ));
            $startFinanceTwoPlus = !empty($financeTwoPlus[0][0]['startDate']) ? $financeTwoPlus[0][0]['startDate'] : date('Y', time());
            $endFinanceTwoPlus = !empty($financeTwoPlus[0][0]['endDate']) ? $financeTwoPlus[0][0]['endDate'] : date('Y', time());
            if (!empty($financeTwoDates) && !empty($financeTwoDates[0][0])) {
                $financeTwoDates = $financeTwoDates[0][0];
                $startFinanceTwoPlus = (date('Y', $financeTwoDates['fonStart']) < $startFinanceTwoPlus) ? date('Y', $financeTwoDates['fonStart']) : $startFinanceTwoPlus;
                $endFinanceTwoPlus = (date('Y', $financeTwoDates['fonEnd']) > $endFinanceTwoPlus) ? date('Y', $financeTwoDates['fonEnd']) : $endFinanceTwoPlus;
            }
            foreach ($fieldFinancementTwoPlus['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[0]) && $_field[0] == 'percent') {
                    $finTwoPlusPercent[] = 'ProjectFinanceTwoPlus.' . $field;
                } else {
                    $finTwoPlusEuros[] = 'ProjectFinanceTwoPlus.' . $field;
                }
            }
        }
        $this->set(compact('finances', 'finEuros', 'finPercents', 'financesTwoPlus', 'startFinanceTwoPlus', 'endFinanceTwoPlus', 'finTwoPlusEuros', 'finTwoPlusPercent'));
        /*
         * Manual consumed
         */
        $this->loadModel('ProjectTask');
        $this->ProjectTask->virtualFields['manual'] = 'SUM(manual_consumed)';
        $tasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array(
                'project_id',
                'manual'
            ),
            'group' => array('project_id')
        ));
        $this->set('manualData', $tasks);
        /**
         * Dung nhieu view trong 1 controller. Neu cate = 2 chuyen den view: opportunity.
         */
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $appstatus = $this->Session->read("App.status");

        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        //priorities
        $priorities = $this->Project->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'priority')
        ));
        $this->set('priorities', $priorities);
        //editable list, available for pm only
        $this->set('isPM', $this->employee_info['CompanyEmployeeReference']['role_id'] == 3);
        $this->set('isAdmin', $this->employee_info['CompanyEmployeeReference']['role_id'] == 2);
        $this->set('editable', $this->canModifyProject($projectIds));
        //log system for done & todo
        if (!empty($projectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $projectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        // get value for dialog vision task
        $projectStatus = $this->Project->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $projectProgram = $this->Project->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->Project->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_amr_program_id' => array_keys($projectProgram)
            ),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_name')
        ));
        $listProfitCenter = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname')
        ));
        $listProjectCode = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_code_1', 'project_code_2')
        ));
        $_listProjectCode = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_1') : array();
        $_listProjectCode1 = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_2') : array();
        if (!empty($_listProjectCode)) {
            $_listProjectCode = array_unique($_listProjectCode);
            $_listProjectCode = array_combine($_listProjectCode, $_listProjectCode);
            unset($_listProjectCode['']);
        }
        if (!empty($_listProjectCode1)) {
            $_listProjectCode1 = array_unique($_listProjectCode1);
            $_listProjectCode1 = array_combine($_listProjectCode1, $_listProjectCode1);
            unset($_listProjectCode1['']);
        }
        $_milestone = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_milestone', 'project_milestone')
        ));
        // get company config
        $this->loadModel('CompanyConfig');
        $showTaskVision = $this->CompanyConfig->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'display_visions'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showTaskVision = !empty($showTaskVision) ? $showTaskVision['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('projectStatus', 'projectProgram', 'projectSubProgram', 'listProfitCenter', '_listProjectCode', '_listProjectCode1', 'showTaskVision', 'listEmployee', '_milestone'));
        //end ----
        $this->loadModel('ProjectType');
        $listprojectTypes = $this->Project->ProjectType->find('list', array(
            'fields' => array('ProjectType.id', 'ProjectType.project_type'),
            'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1)
        ));
        $listprojectSubTypes = $this->Project->ProjectSubType->find('list', array(
            'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
            'conditions' => array('ProjectSubType.project_type_id' => array_keys($listprojectTypes), 'ProjectSubType.display' => 1)
        ));
        //Lay Budget settings
        $this->loadModel('BudgetSetting');
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $displayExpectation = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
                'display' => 1
            ),
        ));
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $_f = array('id', 'name_eng');
        } else {
            $_f = array('id', 'name_fre');
        }
        $screenExpec = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
            ),
            'fields' => $_f
        ));
        $this->loadModel('HistoryFilter');
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'projects/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->loadModel('ProjectEmployeeManager');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            ),
            'fields' => array('project_id', 'project_id')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $_listAvata = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model_id' => $projectIds
            )
        ));
        $listAvata = array();
        foreach ($_listAvata as $value) {
            $dx = $value['LogSystem'];
            $listAvata[$dx['model'] . '-' . $dx['model_id']] = $dx['employee_id'];
        }
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id']), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set(compact('logs', 'currency_name', 'appstatus', 'staffingSystems', 'projectPhasePlans', 'budgetSyns', 'ACLController', 'ACLAction', 'logGroups', 'fieldset', 'projects', 'viewId', 'noProjectManager', 'employee', 'checkStatus', 'personDefault', 'financeFields', 'LANG', 'viewGantt', 'confirmGantt', 'cate', 'listPc', 'listprojectTypes', 'listprojectSubTypes', 'budget_settings', 'checkEngedMd', 'TaskEuros', 'projectTaskFields', 'displayExpectation'));
        $this->set(compact('screenExpec', 'savePosition', 'listEmployeeManager', 'employee_id', 'listAvata'));
        if ($cate == 2) {
            $this->Session->write("App.status_oppor", 2);
            //if( !$mobileEnabled )$this->action = 'opportunity';
        } else {
            $this->Session->delete('App.status_oppor');
        }
    }

    public function getComment() {
        if (!empty($_POST['id'])) {
            $this->loadModel('Employee');
            $id = $_POST['id'];
            $model = $_POST['model'];
            $this->loadModels('LogSystem', 'Project');
            $logSystems = $this->LogSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => (int) $id,
                    'model' => $model,
                ),
                // 'limit' => 3,
                'fields' => array('id', 'model_id', 'model', 'description', 'updated', 'update_by_employee', 'name', 'employee_id'),
                'order' => array('updated' => 'DESC')
            ));
            // debug($logSystems); exit;
            $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id
                ),
                'fields' => array('project_name', 'start_date', 'end_date')
            ));
            $listComments = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
            // $i = 0;
            $project_name = $project['Project']['project_name'];

            $progress = $this->getProjectProgress($id);
			$initDate = $progress[$id]['Completed'];
            $data['initDate'] = $progress[$id]['Completed'];
            $checkAvata = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'avatar_resize')
            ));
            $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
            $hasAvatar = $checkAvata;
			$listEmployees = $this->_getListEmployee(null, true);
            $data = array();
            foreach ($listComments as $_comment) {
				$e_id = $_comment['employee_id'];
				$_comment['name'] = $listEmployees[$e_id]['fullname']. ' ' . date('H:i d/m/Y', $_comment['updated']);
				// $_comment['updated'] = date('d/m/Y', $_comment['updated']);
                $data[] = $_comment;
            }
            die(json_encode(array('comment' => $data, 'project_name' => $project_name, 'initDate' => $initDate, 'hasAvatar' => $hasAvatar, 'model' => $model)));
        }
        exit;
    }
	// Huynh 
	/* private function getProjectProgress
	 * Ticket #615 «EVOLUTION MUTUALISABLE» «Progress of a project» «EN COURS DE DEV» «Développeur» «z0 Gravity»
	 * Create 5 ways to calculate the progress of a project
	 */
	private function getProjectProgress($projectIds){
		$company_id = $this->employee_info['Company']['id'];
		$result = $this->Project->caculateProgress($projectIds, $this->companyConfigs, $company_id);
		return $result;
	}


    public function getProjectTaskText() {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $this->loadModels('ProjectTaskTxt', 'ProjectTask', 'Employee');
            $projectTaskTxt = $this->ProjectTaskTxt->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $id,
                ),
                'limit' => 2,
                'field' => '*',
                'order' => array('id' => 'DESC')
            ));
            $projectTaskTxt = !empty($projectTaskTxt) ? Set::combine($projectTaskTxt, '{n}.ProjectTaskTxt.id', '{n}.ProjectTaskTxt') : array();
            $listEmployeeId = array();
            foreach ($projectTaskTxt as $key => $value) {
                $listEmployeeId[] = $value['employee_id'];
            }
            if (!empty($listEmployeeId)) {
                $getEmpoyee = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $listEmployeeId[0],
                    ),
                    'fields' => array('fullname')
                ));
            }
            if (!empty($getEmpoyee['Employee']['fullname']))
                $data['update_by_employee'] = $getEmpoyee['Employee']['fullname'];
            $project = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id
                ),
                'fields' => array('task_title', 'task_start_date', 'task_end_date')
            ));
            $i = 0;
            $data['project_name'] = $project['ProjectTask']['task_title'];

            $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($project['ProjectTask']['task_end_date']));
            $projectDate = abs(strtotime($project['ProjectTask']['task_end_date']) - strtotime($project['ProjectTask']['task_start_date']));
            $initDate = 100;
            if ($projectCurrentDate < $projectDate)
                $initDate = floor(($projectCurrentDate / $projectDate) * 100);
            $data['initDate'] = $initDate;
            foreach ($projectTaskTxt as $key => $_comment) {
                $data[] = $_comment;
            }
            die(json_encode($data));
        }
        exit;
    }

    public function updateTaskText() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectTaskTxt');
        // debug($this->data); exit;
        if (!empty($this->data)) {
            $task_id = $this->data['id'];
            $employee_id = $this->employee_info['Employee']['id'];
            $create = date('Y/m/d H:i', time());
            unset($this->data['id']);

            $data = array(
                'project_task_id' => $task_id,
                'employee_id' => $employee_id,
                'comment' => $this->data['text'],
                'created' => $create
            );


            $this->ProjectTaskTxt->create();
            if (!empty($data) && $this->ProjectTaskTxt->save($data)) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function canModifyProject($projects) {
        return ClassRegistry::init('ProjectEmployeeManager')->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $projects,
                        'project_manager_id' => $this->employee_info['Employee']['id'],
                        'type' => array('PM', 'TM', 'CB'),
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
        ));
    }

    /**
     * Xay dung neu parse
     */
    public function _parseNew($listProjectToActivities = array(), $projects = array(), $viewGantt = false) {
        $manual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
        $this->loadModel('ProjectEvolution');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        /**
         * Lay company cua used dang login
         */
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay evolution theo danh sach id
         */
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => array_keys($listProjectToActivities)),
            'fields' => array('id', 'project_id', 'SUM(man_day) as totalMD'),
            'group' => array('project_id')
        ));
        $projectEvolutions = !empty($projectEvolutions) ? Set::combine($projectEvolutions, '{n}.ProjectEvolution.project_id', '{n}.0.totalMD') : array();
        /**
         * Lay cac project Task theo danh sach project
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => array_keys($listProjectToActivities)),
            'fields' => array('id', 'parent_id', 'project_planed_phase_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed', 'manual_consumed', 'manual_overload')
        ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
        $listIdProjectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.id', '{n}.id') : array();
        /**
         * Lay danh sach activity task theo danh sach project task
         */
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listIdProjectTasks),
            'fields' => array('id', 'activity_id', 'project_task_id')
        ));
        $listATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        /**
         * Lay consumed cua cac activity
         */
        $consumedOfActivities = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'activity_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('activity_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0),
                'OR' => array(
                    'task_id' => array_keys($activityTasks),
                    'activity_id' => $listProjectToActivities
                )
            )
        ));
        $consumedOfActivities = !empty($consumedOfActivities) ? Set::combine($consumedOfActivities, '{n}.ActivityRequest.task_id', '{n}.0.value', '{n}.ActivityRequest.activity_id') : array();
        $sumPrevious = $sumActivities = $consumedOfTasks = array();
        if (!empty($consumedOfActivities)) {
            $consumedOfTasks = !empty($consumedOfActivities[0]) ? $consumedOfActivities[0] : array();
            unset($consumedOfActivities[0]);
            foreach ($consumedOfActivities as $activityID => $value) {
                $val = array_shift($value);
                $sumActivities[$activityID] = $val;
                $sumPrevious[$activityID] = $val;
            }
            if (!empty($consumedOfTasks)) {
                foreach ($consumedOfTasks as $taskId => $value) {
                    $activityID = !empty($activityTasks[$taskId]) ? $activityTasks[$taskId] : 0;
                    if (!isset($sumActivities[$activityID])) {
                        $sumActivities[$activityID] = 0;
                    }
                    $sumActivities[$activityID] += $value;
                }
            }
        }
        /**
         * Tinh consumed cua employee theo activity
         */
        $consumedActivityOfEmployees = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'activity_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('employee_id', 'activity_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'OR' => array(
                    'task_id' => array_keys($activityTasks),
                    'activity_id' => $listProjectToActivities
                ),
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
                )
        );
        foreach ($consumedActivityOfEmployees as $consumedActivityOfEmployee) {
            $dx = $consumedActivityOfEmployee['ActivityRequest'];
            $value = $consumedActivityOfEmployee['0']['value'];
            $activityID = $dx['activity_id'];
            if (!empty($dx['task_id'])) {
                $activityID = !empty($activityTasks[$dx['task_id']]) ? $activityTasks[$dx['task_id']] : 0;
            }
            if (!isset($sumEmployees[$activityID][$dx['employee_id']])) {
                $sumEmployees[$activityID][$dx['employee_id']] = 0;
            }
            $sumEmployees[$activityID][$dx['employee_id']] += $value;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                        )), '{n}.Employee.id', '{n}.Employee');
        /**
         * Tinh remain, remain special,.... theo tung task cua project
         */
        $sumWorload = $sumOverload = $sumRemains = $sumRemainSpecials = array();
        foreach ($projectTasks as $taskId => &$projectTask) {
            if (in_array($taskId, $parentIds)) {
                unset($projectTasks[$taskId]);
            } else {
                $projectId = $projectTask['project_id'];
                $dataEstimated = $projectTask['estimated'];
                if ($manual) {
                    $projectTask['overload'] = $projectTask['manual_overload'];
                }
                $dataOverload = $projectTask['overload'];
                /**
                 * Sum Workload
                 */
                if (!isset($sumWorload[$projectId])) {
                    $sumWorload[$projectId] = 0;
                }
                $sumWorload[$projectId] += $dataEstimated;
                /**
                 * Sum overload
                 */
                if (!isset($sumOverload[$projectId])) {
                    $sumOverload[$projectId] = 0;
                }
                $sumOverload[$projectId] += $dataOverload;
                /**
                 * Remain cua task thuoc project task
                 * Remain cua cac task thuoc external budget
                 */
                $consumedForTasks = 0;
                if (!$manual) {
                    if (!empty($projectTask['special']) && $projectTask['special'] == 1) {
                        $consumedForTasks = !empty($projectTask['special_consumed']) ? $projectTask['special_consumed'] : 0;
                        if (!isset($sumRemainSpecials[$projectId])) {
                            $sumRemainSpecials[$projectId] = 0;
                        }
                        $sumRemainSpecials[$projectId] += $dataEstimated - $consumedForTasks;
                    } else {
                        $ATaskId = !empty($listATaskLinkPTasks[$taskId]) ? $listATaskLinkPTasks[$taskId] : 0;
                        $consumedForTasks = !empty($consumedOfTasks[$ATaskId]) ? $consumedOfTasks[$ATaskId] : 0;
                    }
                } else {
                    if (!isset($sumRemainSpecials[$projectId])) {
                        $sumRemainSpecials[$projectId] = 0;
                    }
                    $sumRemainSpecials[$projectId] += $dataEstimated - $projectTask['manual_consumed'];
                    $consumedForTasks = $projectTask['manual_consumed'];
                }
                if (!isset($sumRemains[$projectId])) {
                    $sumRemains[$projectId] = 0;
                }
                $sumRemains[$projectId] += number_format(($dataEstimated + $dataOverload) - $consumedForTasks, 2);
            }
        }
        /**
         * Lay consumed cua tung task
         */
        $requestTasks = $this->ActivityRequest->find('all', array(
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
                'task_id' => array_keys($activityTasks),
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $requestTasks = !empty($requestTasks) ? Set::combine($requestTasks, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
        $phases = array();
        if (!empty($projects) && $viewGantt && !empty($projectTasks)) {
            foreach ($projectTasks as $projectTask) {
                $dx = $projectTask;
                if (!isset($phases[$dx['project_id']]['workload'])) {
                    $phases[$dx['project_id']]['workload'] = 0;
                }
                $phases[$dx['project_id']]['workload'] += (float) $dx['estimated'] + (float) $dx['overload'];
                $ATaskId = !empty($listATaskLinkPTasks[$dx['id']]) ? $listATaskLinkPTasks[$dx['id']] : 0;
                if ($manual) {
                    $consumed = $projectTask['manual_consumed'];
                } else {
                    $consumed = !empty($requestTasks) && !empty($requestTasks[$ATaskId]) ? $requestTasks[$ATaskId] : 0;
                    if (!empty($dx['special'])) {
                        $consumed = $dx['special_consumed'];
                    }
                }
                if (!isset($phases[$dx['project_id']]['consumed'])) {
                    $phases[$dx['project_id']]['consumed'] = 0;
                }
                $phases[$dx['project_id']]['consumed'] += $consumed;
            }
        }
        $this->set(compact('projectEvolutions', 'consumedOfTasks', 'sumActivities', 'sumEmployees', 'employees', 'sumWorload', 'sumOverload', 'sumRemainSpecials', 'sumRemains', 'phases', 'sumPrevious'));
    }

    /**
     * phpexcel
     * Export to Excel
     *
     * @return void
     * @access public
     */
    public function export_project($viewId = null) {
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $employee = $this->Session->read("Auth.employee_info");
        $fieldset = array();
        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content'),
                'conditions' => array('UserView.id' => $viewId)));
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            } else {
                $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
            }
        } else {
            $fieldset = array(
                'Project.project_name',
                'Project.project_phase_id',
                'Project.project_manager_id',
                'Project.chief_business_id',
                'Project.technical_manager_id',
                'Project.project_priority_id',
                'Project.project_status_id',
                'Project.start_date',
                'ProjectAmr.weather'
            );
        }
        $this->loadModels('ProjectFinance');
        $financeFields = array();
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]));
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $this->Project->Behaviors->attach('Containable');
        $addInternalMD = $addExternalMD = false;
        if (in_array('ProjectBudgetSyn.provisional_budget_md', $fieldset)) {
            if (!in_array('ProjectBudgetSyn.internal_costs_budget_man_day', $fieldset)) {
                $addInternalMD = true;
                $fieldset[] = 'ProjectBudgetSyn.internal_costs_budget_man_day';
            }
            if (!in_array('ProjectBudgetSyn.external_costs_man_day', $fieldset)) {
                $addExternalMD = true;
                $fieldset[] = 'ProjectBudgetSyn.external_costs_man_day';
            }
        }
        $checkFieldCurrentPhase = false;
        if (in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
        $checkFieldMuti = false;
        for ($i = 1; $i < 10; $i++) {
            if (in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        list($fieldset, $options) = $this->Project->parseViewField($fieldset);
        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'] = array('Project.company_id' => $subCompanies);
            } else {
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'fields' => array('id', 'project_id'),
                    'conditions' => array('Project.company_id' => $employee["Company"]["id"],
                        'ProjectTeam.employee_id' => $employee["Employee"]["id"])));
                $options['conditions'] = array('Project.id' => $projects);
            }
        }
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $options['conditions']['Project.id'] = $data;
            }
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        $checkEngedMd = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            if (in_array('internal_costs_engaged_md', $options['contain']['ProjectBudgetSyn']['fields'])) {
                $key = array_search('internal_costs_engaged_md', $options['contain']['ProjectBudgetSyn']['fields']);
                unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                $checkEngedMd = true;
            }
        }
        $projects = Set::combine($this->Project->find('all', $options), '{n}.Project.id', '{n}');
        $company_id = $employee["Company"]["id"];
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $idProjects = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.id') : array();
        $getDataFromTasks = $this->_makeDataAmr($idProjects);
        $listProjectLinked = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        $getDataActivities = $this->_parse($listProjectLinked);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];
        $this->ProjectBudgetInternalDetail->virtualFields['value'] = 'AVG(ProjectBudgetInternalDetail.average)';
        $getAverages = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array(
                'project_id',
                'value'
            // 'AVG(average) as value'
            ),
            'conditions' => array('project_id' => $idProjects),
            'group' => array('project_id')
        ));
        // $getAverages = !empty($getAverages) ? Set::combine($getAverages, '{n}.ProjectBudgetInternalDetail.project_id', '{n}.0.value') : array();
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if (!empty($dataSystems)) {
            foreach ($dataSystems as $dataSystem) {
                $dx = $dataSystem['TmpStaffingSystem'];
                if (!isset($assignEmployees[$dx['project_id']])) {
                    $assignEmployees[$dx['project_id']] = 0;
                }
                $assignEmployees[$dx['project_id']] += $dx['estimated'];
            }
        }
        $_dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('profit_center'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignProfitCenters = array();
        if (!empty($_dataSystems)) {
            foreach ($_dataSystems as $_dataSystem) {
                $dx = $_dataSystem['TmpStaffingSystem'];
                if (!isset($assignProfitCenters[$dx['project_id']])) {
                    $assignProfitCenters[$dx['project_id']] = 0;
                }
                $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
            }
        }
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $this->loadModel('LogSystem');
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('created' => 'DESC')
        ));
        $logGroups = array();
        if (!empty($logSystems)) {
            foreach ($logSystems as $logSystem) {
                $dx = $logSystem['LogSystem'];
                $logGroups[$dx['model_id']][$dx['model']][] = $dx;
            }
        }
        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $idProjects,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $company_id
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $idProjects,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        /**
         * Lay du lieu o table finacement plus
         */
        $finances = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $idProjects),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
        }
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
            'recursive' => -1,
            'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
            'fields' => array(
                'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
            )
        ));
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();
        $this->ProjectBudgetInternalDetail->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $idProjects
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));

        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        unset($rawDatasets);
        $projectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id'], 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC'));
        foreach ($projects as $key => $project) {
            $dx = $project['Project'];
            if (isset($project['ProjectAmr'][0])) {
                $projects[$key]['ProjectAmr'][0]['project_amr_progression'] = $getDataFromTasks[$key]['project_amr_progression'];
                $projects[$key]['ProjectAmr'][0]['md_validated'] = $getDataFromTasks[$key]['md_validated'];
                $projects[$key]['ProjectAmr'][0]['md_engaged'] = $getDataFromTasks[$key]['md_engaged'];
                $projects[$key]['ProjectAmr'][0]['md_forecasted'] = $getDataFromTasks[$key]['md_forecasted'];
                $projects[$key]['ProjectAmr'][0]['md_variance'] = $getDataFromTasks[$key]['md_variance'];

                $projects[$key]['ProjectAmr'][0]['validated_currency_id'] = !empty($project['ProjectAmr'][0]['validated_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['validated_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['engaged_currency_id'] = !empty($project['ProjectAmr'][0]['engaged_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['engaged_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['forecasted_currency_id'] = !empty($project['ProjectAmr'][0]['forecasted_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['forecasted_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['variance_currency_id'] = !empty($project['ProjectAmr'][0]['variance_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['variance_currency_id']] : '';
            }
            if (empty($project['ProjectAmr'][0]['project_amr_risk_information'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectRisk'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectRisk'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_risk_information'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            if (empty($project['ProjectAmr'][0]['project_amr_problem_information'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectIssue'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectIssue'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_problem_information'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            if (empty($project['ProjectAmr'][0]['project_amr_solution'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectAmr'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectAmr'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_solution'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            // }
            //budget syns
            if (!empty($budgetSyns[$dx['id']]))
                $projects[$key]['ProjectBudgetSyn'][0] = $budgetSyns[$dx['id']];
            $engagedErro = 0;
            $activityId = !empty($listProjectLinked[$dx['id']]) ? $listProjectLinked[$dx['id']] : 0;
            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $id => $val) {
                    $reals = !empty($employees[$id]['tjm']) ? (float) str_replace(',', '.', $employees[$id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $engagedErro;
            // internal cost
            // remain = md_engaged va comsumed = md_validated
            $consumed = !empty($getDataFromTasks[$dx['id']]['md_validated']) ? $getDataFromTasks[$dx['id']]['md_validated'] : 0;
            $remain = !empty($getDataFromTasks[$dx['id']]['md_engaged']) ? $getDataFromTasks[$dx['id']]['md_engaged'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_engaged_md'] = $consumed;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $consumed + $remain;
            $_average = !empty($getAverages[$dx['id']]) ? $getAverages[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain'] = round($remain * $_average, 2);
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast'] = round($engagedErro + ($remain * $_average), 2);
            $internalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget'] : 0;
            $internalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_var'] = ($internalBudget == 0) ? '-100%' : round((($internalForecast / $internalBudget) - 1) * 100, 2) . '%';
            $internalRemain = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain'] : 0;
            $internalForecastManday = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] : 0;
            //external cost
            $externalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_budget'] : 0;
            $externalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_forecast'] : 0;
            $externalOrdered = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_ordered']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_ordered'] : 0;
            $externalRemain = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_remain']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_remain'] : 0;
            $externalManday = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_man_day'] : 0;
            // total cost
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget'] = $internalBudget + $externalBudget;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast'] = $internalForecast + $externalForecast;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_engaged'] = $engagedErro + $externalOrdered;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_remain'] = $internalRemain + $externalRemain;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_man_day'] = $internalForecastManday + $externalManday;
            $totalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast'] : 0;
            $totalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget'] : 0;
            $totalVar = ($totalBudget == 0) ? '-100%' : round((($totalForecast / $totalBudget) - 1) * 100, 2) . '%';
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_var'] = $totalVar;
            //assign pc and employee
            $tWorkload = !empty($getDataFromTasks[$dx['id']]['workload']) ? $getDataFromTasks[$dx['id']]['workload'] : 0;
            $tOverload = !empty($getDataFromTasks[$dx['id']]['overload']) ? $getDataFromTasks[$dx['id']]['overload'] : 0;
            ;
            $assgnPc = !empty($assignProfitCenters[$dx['id']]) ? $assignProfitCenters[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['assign_to_profit_center'] = ($tWorkload == 0) ? '0%' : ((round(($assgnPc / $tWorkload) * 100, 2) > 100) ? '100%' : round(($assgnPc / $tWorkload) * 100, 2) . '%');
            $assgnEmploy = !empty($assignEmployees[$dx['id']]) ? $assignEmployees[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['assign_to_employee'] = ($tWorkload == 0) ? '0%' : ((round(($assgnEmploy / $tWorkload) * 100, 2) > 100) ? '100%' : round(($assgnEmploy / $tWorkload) * 100, 2) . '%');
            $projectID = $dx['id'];
            $projects[$key]['ProjectBudgetSyn'][0]['workload'] = $tWorkload;
            $projects[$key]['ProjectBudgetSyn'][0]['overload'] = $tOverload;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . $currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 3)] : 0;

            $projects[$key]['ProjectBudgetSyn'][0]['consumed_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . $currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 3)] : 0;

            $projects[$key]['ProjectBudgetSyn'][0]['provisional_y'] = !empty($provisionals[$projectID]['provisional_' . $currentYears]) ? $provisionals[$projectID]['provisional_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_one_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 1)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_two_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 2)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_thr_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 3)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_one_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 1)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_two_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 2)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_thr_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 3)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 3)] : 0;
            $totalInter = !empty($internalBudgets[$projectID]) ? $internalBudgets[$projectID] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] = $totalInter;

            //$totalInter = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_budget_md'] = $totalInter + $externalManday;
            if (!empty($finances[$projectID])) {
                $totalBudgetInv = $totalAvanInv = $totalBudgetFon = $totalAvanFon = 0;
                $percentYearInvs = $percentYearFons = array();
                foreach ($finances[$projectID] as $_key => $fins) {
                    $projects[$key]['ProjectFinancePlus'][0][$_key] = $fins;
                    if (!empty($_key)) {
                        $_key = explode('_', $_key);
                        if (!empty($_key[0]) && $_key[0] == 'inv') {
                            if (!isset($percentYearInvs[$_key[2]][$_key[1]])) {
                                $percentYearInvs[$_key[2]][$_key[1]] = 0;
                            }
                            $percentYearInvs[$_key[2]][$_key[1]] += $fins;
                            if (!empty($_key[1]) && $_key[1] == 'budget') {
                                $totalBudgetInv += $fins;
                            } else {
                                $totalAvanInv += $fins;
                            }
                        } else {
                            if (!isset($percentYearFons[$_key[2]][$_key[1]])) {
                                $percentYearFons[$_key[2]][$_key[1]] = 0;
                            }
                            $percentYearFons[$_key[2]][$_key[1]] += $fins;
                            if (!empty($_key[1]) && $_key[1] == 'budget') {
                                $totalBudgetFon += $fins;
                            } else {
                                $totalAvanFon += $fins;
                            }
                        }
                    }
                }
                if (!empty($percentYearInvs)) {
                    foreach ($percentYearInvs as $year => $percentYearInv) {
                        $bud = !empty($percentYearInv['budget']) ? $percentYearInv['budget'] : 0;
                        $ava = !empty($percentYearInv['avancement']) ? $percentYearInv['avancement'] : 0;
                        $per = ($bud == 0 ) ? 0 : round($ava / $bud * 100, 2);
                        if ($per > 100) {
                            $per = 100;
                        } elseif ($per < 0) {
                            $per = 0;
                        }
                        $projects[$key]['ProjectFinancePlus'][0]['inv_percent_' . $year] = round($per, 2);
                    }
                }
                if (!empty($percentYearFons)) {
                    foreach ($percentYearFons as $year => $percentYearFon) {
                        $bud = !empty($percentYearFon['budget']) ? $percentYearFon['budget'] : 0;
                        $ava = !empty($percentYearFon['avancement']) ? $percentYearFon['avancement'] : 0;
                        $per = ($bud == 0 ) ? 0 : round($ava / $bud * 100, 2);
                        if ($per > 100) {
                            $per = 100;
                        } elseif ($per < 0) {
                            $per = 0;
                        }
                        $projects[$key]['ProjectFinancePlus'][0]['fon_percent_' . $year] = round($per, 2);
                    }
                }
                $totalPercentInv = ($totalBudgetInv == 0) ? 0 : $totalAvanInv / $totalBudgetInv * 100;
                if ($totalPercentInv > 100) {
                    $totalPercentInv = 100;
                } elseif ($totalPercentInv < 0) {
                    $totalPercentInv = 0;
                }
                $totalPercentFon = ($totalBudgetFon == 0) ? 0 : $totalAvanFon / $totalBudgetFon * 100;
                if ($totalPercentFon > 100) {
                    $totalPercentFon = 100;
                } elseif ($totalPercentFon < 0) {
                    $totalPercentFon = 0;
                }
                $projects[$key]['ProjectFinancePlus'][0]['inv_budget'] = !empty($totalBudgetInv) ? $totalBudgetInv : '';
                $projects[$key]['ProjectFinancePlus'][0]['inv_avancement'] = !empty($totalAvanInv) ? $totalAvanInv : '';
                $projects[$key]['ProjectFinancePlus'][0]['inv_percent'] = !empty($totalPercentInv) ? round($totalPercentInv, 2) : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_budget'] = !empty($totalBudgetFon) ? $totalBudgetFon : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_avancement'] = !empty($totalAvanFon) ? $totalAvanFon : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_percent'] = !empty($totalPercentFon) ? round($totalPercentFon, 2) : '';
            }
        }
        $this->set(compact('fieldset', 'projects', 'addInternalMD', 'addExternalMD', 'datasets', 'projectPhases', 'financeFields'));
        $this->layout = '';
    }

    /**
     * export_vision
     *
     * @return void
     * @access public
     */
    function export_vision() {
        $this->layout = false;
        if (!empty($this->data['Export']) && !empty($this->data['Export']['rows'])) {
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);

            $tmpFile = TMP . 'page_' . time() . '.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));

            list($_width, $_height) = getimagesize($tmpFile);

            $image = imagecreatefrompng($tmpFile);
            $crop = imagecreatetruecolor($_width - 28, $height);

            imagecopy($crop, $image, 0, 0, 28, 220, $_width, $height);

            imagepng($crop, $tmpFile);
            $this->set(compact('tmpFile', 'height', 'rows'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * projects_vision
     * Project vision
     * @return void
     * @access public
     */
    function projects_vision($category = 1, $list = null) {
        $conditions = array();
        if ($list !== null) {
            $list = explode('-', $list);
            $conditions = array('Project.id' => $list);
        } else {
            $default = array(
                'ProjectProjectAmrProgramId_port' => '',
                'ProjectProjectAmrSubProgramId_port' => '',
                'ProjectProjectManagerId_port' => '',
                'ProjectProjectStatusId_port' => ''
            );
            $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);

            if (empty($conditions['ProjectProjectStatusId_port'])) {
                $conditions['ProjectProjectStatusId_port'] = $category;
            }
            $keys = array(
                'project_amr_program_id',
                'project_amr_sub_program_id',
                'project_manager_id',
                'category'
            );
            $conditions = Set::filter(array_combine($keys, $conditions));
        }
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $limit = isset($this->params['url']['limit']) ? (int) $this->params['url']['limit'] : 10;
        $projects = array();
        $_fields = array(
            'Project.project_name',
            'Project.start_date',
            'Project.end_date',
            'Project.planed_end_date',
            'Project.category',
            'ProAmr.amr_program',
        );
        $_joins = array(
            array(
                'table' => 'project_amr_programs',
                'alias' => 'ProAmr',
                'type' => 'LEFT',
                'foreignKey' => 'employee_id',
                'conditions' => array(
                    'ProAmr.id = Project.project_amr_program_id'
                )
            )
        );
        $this->paginate = array(
            //'conditions' => array('Project.category' => $category),
            'conditions' => $conditions,
            'joins' => $_joins,
            'order' => array('ProAmr.amr_program', 'Project.project_name'),
            'contain' => array('ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ), 'ProjectPhase' => array('name', 'color'))), 'limit' => $limit, 'fields' => $_fields);
        if ($this->is_sas)
            $projects = $this->Project->find('all', $this->paginate);
        else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
                $projects = $this->paginate('Project', array('Project.company_id' => $sub_companies));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('list', array('recursive' => -1, 'fields' => array('id', 'project_id'), 'conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $projects = $this->paginate('Project', array('Project.id' => $employee_project_teams));
            }
        }
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $this->set(compact('projects', 'limit', 'display'));
    }

    /**
     * projects_list
     * Projects list
     * @return void
     * @access public
     */
    function projects_list() {
        $this->Project->recursive = 1;
        $this->Project->Behaviors->attach('Containable');
        $projects = array();
        if ($this->is_sas) {
            $projects = $this->Project->find('all', array(
                'contain' => array('Company'),
                'conditions' => array('Project.category' => 4),
                'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')));
        } else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array(
                    'conditions' => array('OR' => array(
                            'Company.id' => $this->employee_info['Company']['id'],
                            'Company.parent_id' => $this->employee_info['Company']['id']))));
                $company_ids = array();
                foreach ($sub_companies as $comp_id => $company_name) {
                    $company_ids[] = $comp_id;
                }

                $projects = $this->Project->find('all', array(
                    'conditions' => array('Project.company_id' => $company_ids, 'Project.category' => 4),
                    'contain' => array('Company'),
                    'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('all', array('conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $project_ids = array();
                foreach ($employee_project_teams as $employee_project_team) {
                    $project_ids = $employee_project_team["ProjectTeam"]["project_id"];
                }
                $projects = $this->Project->find('all', array(
                    'conditions' => array('Project.id' => $project_ids, 'Project.category' => 4),
                    'contain' => array('Company'),
                    'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')
                ));
            }
        }
        $this->set('projects', $projects);
    }

    /**
     * duplicate
     *
     * @return void
     * @access public
     */
    function duplicate() {
        $this->autoRender = false;
        $data = $this->data['Project']['duplicate'];
        $n = explode("|", $data, 2);
        if (!empty($data)) {
            //Copy record of Project
            $new_record = $this->Project->findById($n[0]);
            $this->_checkRole(false, $n[0]);
            $this->Project->id = $n[0];
            $this->Project->saveField('copy_number', $new_record['Project']['copy_number'] + 1);
            $num_co = $new_record['Project']['copy_number'] + 1;

            if ($num_co >= 2)
                $num_co = ' (' . $num_co . ')';
            else
                $num_co = '';
            $oldId = $new_record['Project']['id'];
            $new_record['Project']['project_copy'] = 1;
            $new_record['Project']['project_copy_id'] = $new_record['Project']['id'];
            $new_record['Project']['category'] = 2;  //when copy project, default project status = 2 (Opportunity).
            unset($new_record['Project']['id']);
            $new_record['Project']['project_name'] = 'Copy of ' . $n[1] . $num_co;
            $this->Project->create();
            $new_record['Project']['copy_number'] = 0;
            $new_record['Project']['created'] = time();
            $new_record['Project']['updated'] = time();
            $new_record['Project']['last_modified'] = time();
            if (empty($this->viewVars['canModified'])) {
                $employee = $this->Session->read("Auth.employee_info");
                $new_record['Project']['project_manager_id'] = $employee["Employee"]["id"];
            }
            unset($new_record['Project']['activity_id']);
            $this->Project->save($new_record);
            $id_duplicate = $this->Project->getLastInsertID();
            //Copy record of ProjectTeam
            if (!empty($new_record['ProjectTeam'])) {
                foreach ($new_record['ProjectTeam'] as $pteam) {
                    $pteam['project_id'] = $id_duplicate;
                    $team_id = $pteam['id'];
                    unset($pteam['id']);
                    $this->ProjectTeam->create();
                    $this->ProjectTeam->save($pteam);
                    $pteam_id = $this->ProjectTeam->getLastInsertID();

                    $pteam_cur_id = $team_id;
                    $this->ProjectFunctionEmployeeRefer->recursive = -1;
                    $PFERs = $this->ProjectFunctionEmployeeRefer->find('all', array(
                        'conditions' => array('project_team_id' => $pteam_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectFunctionEmployeeRefer']['id']);
                            $PFER['ProjectFunctionEmployeeRefer']['project_team_id'] = $pteam_id;
                            $this->ProjectFunctionEmployeeRefer->create();
                            $this->ProjectFunctionEmployeeRefer->save($PFER);
                        }
                    }
                }
            }
            //Copy record of ProjectMilestone
			$milestone_refer = array();
            if (!empty($new_record['ProjectMilestone'])) {
                foreach ($new_record['ProjectMilestone'] as $pmiles) {
                    $pmiles['project_id'] = $id_duplicate;
					$oldMilesId = $pmiles['id'];
                    unset($pmiles['id']);
                    $this->ProjectMilestone->create();
                    if($this->ProjectMilestone->save($pmiles)){
						$newMilesId = $this->ProjectMilestone->getLastInsertID();
						$milestone_refer[$oldMilesId] = $newMilesId;
					}
                }
            }
            //Copy record of ProjectPhasePlan
            if (!empty($new_record['ProjectPhasePlan'])) {
                $project_planed_phase_id_old = $listPhasePredes = $listPhaseIds = array();
                foreach ($new_record['ProjectPhasePlan'] as $pphase) {
                    $pphase['project_id'] = $id_duplicate;
                    $ProjectPhasePlan_id_old = $pphase['id'];
                    $oldPhaseId = $pphase['id'];
                    if (!empty($pphase['predecessor'])) {
                        $listPhasePredes[$oldPhaseId] = $pphase['predecessor'];
                    }
                    unset($pphase['id']);
                    $this->ProjectPhasePlan->create();
                    $this->ProjectPhasePlan->save($pphase);
                    $newPhaseId = $this->ProjectPhasePlan->getLastInsertID();
                    $listPhaseIds[$oldPhaseId] = $newPhaseId;
                    $project_planed_phase_id_old[$ProjectPhasePlan_id_old] = $this->ProjectPhasePlan->getLastInsertID();
                }
                if (!empty($listPhasePredes)) {
                    foreach ($listPhasePredes as $old => $pre) {
                        $idUpdate = !empty($listPhaseIds[$old]) ? $listPhaseIds[$old] : 0;
                        $this->ProjectPhasePlan->id = $idUpdate;
                        $saved['predecessor'] = !empty($listPhaseIds[$pre]) ? $listPhaseIds[$pre] : '';
                        $this->ProjectPhasePlan->save($saved);
                    }
                }
            }
            //Copy record of ProjectTask
            //co project_id cu, get tat ca  cac task co project_planed_phase_id giu lai
            $company_id = $this->employee_info['Company']['id'];
            $this->loadModels('ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'NctWorkload');
            $listEmployeeActive = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'actif' => 1,
                    'OR' => array(
                        'start_date IS NULL',
                        'start_date' => '0000-00-00',
                        'start_date <=' => date('Y-m-d', time())
                    ),
                    'AND' => array(
                        'OR' => array(
                            'end_date IS NULL',
                            'end_date' => '0000-00-00',
                            'end_date >=' => date('Y-m-d', time())
                        )
                    )
                ),
                'fields' => array('id', 'id')
            ));
            $listProfitCenterActive = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                ),
                'fields' => array('id', 'id')
            ));
            if (!empty($new_record['ProjectTask'])) {
                $listTaskIds = $listTaskPredes = array();
                foreach ($new_record['ProjectTask'] as $ptask) {
                    $ptask['project_id'] = $id_duplicate;
                    $ptask['project_planed_phase_id'] = $project_planed_phase_id_old[$ptask['project_planed_phase_id']];
                    $oldTaskId = $ptask['id'];
                    if (!empty($ptask['parent_id'])) {
                        $ptask['parent_id'] = !empty($listTaskIds[$ptask['parent_id']]) ? $listTaskIds[$ptask['parent_id']] : 0;
                    }
                    if (!empty($ptask['predecessor'])) {
                        $listTaskPredes[$oldTaskId] = $ptask['predecessor'];
                    }
                    $projectTaskEmpRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        ),
                        'fields' => array('reference_id', 'project_task_id', 'is_profit_center', 'estimated')
                    ));
                    $listNCT = $this->NctWorkload->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        ),
                        'fields' => array('task_date', 'estimated', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date', 'end_date'),
                    ));
                    unset($ptask['task_completed']);
                    unset($ptask['task_assign_to']);
                    unset($ptask['task_real_end_date']);
                    unset($ptask['initial_estimated']);
                    unset($ptask['overload']);
                    unset($ptask['id']);
                    unset($ptask['initial_task_start_date']);
                    unset($ptask['initial_task_end_date']);
                    unset($ptask['manual_consumed']);
                    unset($ptask['manual_overload']);
                    unset($ptask['created']);
                    unset($ptask['updated']);
                    unset($ptask['special_consumed']);
					
					// Milestone reference
					if(!empty($milestone_refer) && !empty($ptask['milestone_id']) && !empty($milestone_refer[$ptask['milestone_id']])){
						$ptask['milestone_id'] = $milestone_refer[$ptask['milestone_id']];
					}
					
                    $this->ProjectTask->create();
                    $this->ProjectTask->save($ptask);
                    $newTaskId = $this->ProjectTask->getLastInsertID();
                    $listTaskIds[$oldTaskId] = $newTaskId;
                    $estimatedOfNewTask = 0;
                    foreach ($projectTaskEmpRefers as $key => $projectTaskEmpRefer) {
                        $dx = $projectTaskEmpRefer['ProjectTaskEmployeeRefer'];
                        if (($dx['is_profit_center'] == 1 && in_array($dx['reference_id'], $listProfitCenterActive)) || ($dx['is_profit_center'] == 0 && in_array($dx['reference_id'], $listEmployeeActive))) {
                            $this->ProjectTaskEmployeeRefer->create();
                            $this->ProjectTaskEmployeeRefer->save(array(
                                'reference_id' => $dx['reference_id'],
                                'project_task_id' => $newTaskId,
                                'is_profit_center' => $dx['is_profit_center'],
                                'estimated' => $dx['estimated']
                            ));
                            $estimatedOfNewTask += !empty($dx['estimated']) ? $dx['estimated'] : 0;
                        }
                    }
                    $this->ProjectTask->id = $newTaskId;
                    $this->ProjectTask->save(array('estimated' => $estimatedOfNewTask));
                    if (!empty($listNCT)) {
                        foreach ($listNCT as $key => $value) {
                            if ($ptask['is_nct']) {
                                $nct_fields = array(
                                    'task_date' => $value['NctWorkload']['task_date'],
                                    'estimated' => $value['NctWorkload']['estimated'],
                                    'reference_id' => $value['NctWorkload']['reference_id'],
                                    'is_profit_center' => $value['NctWorkload']['is_profit_center'],
                                    'project_task_id' => $newTaskId,
                                    'activity_task_id' => 0,
                                    'group_date' => $value['NctWorkload']['group_date'],
                                    'end_date' => $value['NctWorkload']['end_date'],
                                );
                                $this->NctWorkload->create();
                                $this->NctWorkload->save($nct_fields);
                            }
                        }
                    }
                }
                if (!empty($listTaskPredes)) {
                    foreach ($listTaskPredes as $old => $pre) {
                        $idUpdate = !empty($listTaskIds[$old]) ? $listTaskIds[$old] : 0;
                        $this->ProjectTask->id = $idUpdate;
                        $saved['predecessor'] = !empty($listTaskIds[$pre]) ? $listTaskIds[$pre] : '';
                        $this->ProjectTask->save($saved);
                    }
                }
            }
            /**
             * Save project_employee_manager
             * Lay danh sach project_employee_manager da tao
             */
            // $this->loadModel('ProjectEmployeeManager');
            // $dataManagers = $this->ProjectEmployeeManager->find('all',array(
            //     'recursive' => -1,
            //     'conditions' => array(
            //         'project_id' => $oldId
            //     )
            // ));
            // if(!empty($dataManagers)){
            //     $_dataManagers = array();
            //     foreach($dataManagers as $value){
            //         $_dataManagers[] = array(
            //             'project_manager_id' => $value['ProjectEmployeeManager']['project_manager_id'],
            //             // 'is_backup' => $value['ProjectEmployeeManager']['is_backup'],
            //             'project_id' => $id_duplicate,
            //             'type' => $value['ProjectEmployeeManager']['type'],
            //             'activity_id' => $value['ProjectEmployeeManager']['activity_id']
            //         );
            //     }
            //     if(!empty($_dataManagers)){
            //         $this->ProjectEmployeeManager->create();
            //         $this->ProjectEmployeeManager->saveAll($_dataManagers);
            //     }
            // }
            //Copy record of ProjectRisk
            if (!empty($new_record['ProjectRisk'])) {
                foreach ($new_record['ProjectRisk'] as $prisk) {
                    $prisk['project_id'] = $id_duplicate;
                    unset($prisk['id']);
                    $this->ProjectRisk->create();
                    $this->ProjectRisk->save($prisk);
                }
            }

            //Copy record of ProjectIssue
            if (!empty($new_record['ProjectIssue'])) {
                foreach ($new_record['ProjectIssue'] as $pissue) {
                    $pissue['project_id'] = $id_duplicate;
                    unset($pissue['id']);
                    $this->ProjectIssue->create();
                    $this->ProjectIssue->save($pissue);
                }
            }

            //Copy record of ProjectDecision
            if (!empty($new_record['ProjectDecision'])) {
                foreach ($new_record['ProjectDecision'] as $pdecision) {
                    $pdecision['project_id'] = $id_duplicate;
                    unset($pdecision['id']);
                    $this->ProjectDecision->create();
                    $this->ProjectDecision->save($pdecision);
                }
            }

            //Copy record of ProjectEvolution
            if (!empty($new_record['ProjectLivrable'])) {
                foreach ($new_record['ProjectLivrable'] as $plivra) {
                    $plivra['project_id'] = $id_duplicate;
                    $plivra_id = $plivra['id'];
                    unset($plivra['id']);
                    $this->ProjectLivrable->create();
                    $this->ProjectLivrable->save($plivra);

                    $plivra_new_id = $this->ProjectLivrable->getLastInsertID();
                    $plivra_cur_id = $plivra_id;
                    $this->ProjectLivrableActor->recursive = -1;
                    $PFERs = $this->ProjectLivrableActor->find('all', array(
                        'conditions' => array('ProjectLivrableActor.project_livrable_id' => $plivra_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectLivrableActor']['id']);
                            $PFER['ProjectLivrableActor']['project_livrable_id'] = $plivra_new_id;
                            $PFER['ProjectLivrableActor']['project_id'] = $id_duplicate;
                            $this->ProjectLivrableActor->create();
                            $this->ProjectLivrableActor->save($PFER);
                        }
                    }
                }
            }

            //Copy record of ProjectEvolution
            if (!empty($new_record['ProjectEvolution'])) {
                foreach ($new_record['ProjectEvolution'] as $pevolu) {
                    $pevolu['project_id'] = $id_duplicate;
                    $pevolu_id = $pevolu['id'];
                    unset($pevolu['id']);
                    $this->ProjectEvolution->create();
                    $this->ProjectEvolution->save($pevolu);
                    $pevolu_new_id = $this->ProjectEvolution->getLastInsertID();

                    $pevolu_cur_id = $pevolu_id;
                    $this->ProjectEvolutionImpactRefer->recursive = -1;
                    $PFERs = $this->ProjectEvolutionImpactRefer->find('all', array(
                        'conditions' => array('project_evolution_id' => $pevolu_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectEvolutionImpactRefer']['id']);
                            $PFER['ProjectEvolutionImpactRefer']['project_evolution_id'] = $pevolu_new_id;
                            $PFER['ProjectEvolutionImpactRefer']['project_id'] = $id_duplicate;
                            $this->ProjectEvolutionImpactRefer->create();
                            $this->ProjectEvolutionImpactRefer->save($PFER);
                        }
                    }
                }
            }

            //Copy record of ProjectAmr
            if (!empty($new_record['ProjectAmr'])) {
                foreach ($new_record['ProjectAmr'] as $pamr) {
                    $pamr['project_id'] = $id_duplicate;
                    unset($pamr['id']);
                    $this->ProjectAmr->create();
                    $this->ProjectAmr->save($pamr);
                }
            }

            //Copy record of Created value
            if (!empty($new_record['ProjectCreatedVal'])) {
                foreach ($new_record['ProjectCreatedVal'] as $pcreated) {
                    $pcreated['project_id'] = $id_duplicate;
                    unset($pcreated['id']);
                    $this->ProjectCreatedVal->create();
                    $this->ProjectCreatedVal->save($pcreated);
                }
            }
            //Copy Budget Internal Cost.
            if (!empty($new_record['ProjectBudgetInternal'])) {
                foreach ($new_record['ProjectBudgetInternal'] as $internal) {
                    $internal['project_id'] = $id_duplicate;
                    unset($internal['id']);
                    $internal['activity_id'] = 0;
                    $this->ProjectBudgetInternal->create();
                    $this->ProjectBudgetInternal->save($internal);
                }
            }
            //Copy Budget Internal Cost Detail.
            if (!empty($new_record['ProjectBudgetInternalDetail'])) {
                foreach ($new_record['ProjectBudgetInternalDetail'] as $internalDetail) {
                    $internalDetail['project_id'] = $id_duplicate;
                    unset($internalDetail['id']);
                    $internalDetail['activity_id'] = 0;
                    $this->ProjectBudgetInternalDetail->create();
                    $this->ProjectBudgetInternalDetail->save($internalDetail);
                }
            }

            //Copy Budget External Cost.
            if (!empty($new_record['ProjectBudgetExternal'])) {
                foreach ($new_record['ProjectBudgetExternal'] as $external) {
                    $external['project_id'] = $id_duplicate;
                    unset($external['id']);
                    $external['activity_id'] = 0;
                    $this->ProjectBudgetExternal->create();
                    $this->ProjectBudgetExternal->save($external);
                }
            }

            //Copy Budget External Cost.
            if (!empty($new_record['ProjectBudgetSale'])) {
                foreach ($new_record['ProjectBudgetSale'] as $sale) {
                    $sale['project_id'] = $id_duplicate;
                    $oldSaleId = $sale['id'];
                    unset($sale['id']);
                    $sale['activity_id'] = 0;
                    $this->ProjectBudgetSale->create();
                    $this->ProjectBudgetSale->save($sale);
                    $newSaleId = $this->ProjectBudgetSale->getLastInsertID();

                    $this->ProjectBudgetInvoice->recursive = -1;
                    $PFERs = $this->ProjectBudgetInvoice->find('all', array(
                        'conditions' => array('project_budget_sale_id' => $oldSaleId)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectBudgetInvoice']['id']);
                            $PFER['ProjectBudgetInvoice']['activity_id'] = 0;
                            $PFER['ProjectBudgetInvoice']['project_budget_sale_id'] = $newSaleId;
                            $PFER['ProjectBudgetInvoice']['project_id'] = $id_duplicate;
                            $this->ProjectBudgetInvoice->create();
                            $this->ProjectBudgetInvoice->save($PFER);
                        }
                    }
                }
            }

            //Copy record of Budget Syns
            if (!empty($new_record['ProjectBudgetSyn'])) {
                foreach ($new_record['ProjectBudgetSyn'] as $syns) {
                    $syns['project_id'] = $id_duplicate;
                    unset($syns['id']);
                    $syns['activity_id'] = 0;
                    $this->ProjectBudgetSyn->create();
                    $this->ProjectBudgetSyn->save($syns);
                }
            }

            $this->Session->setFlash(__('CREATED', true));
            $company_id = $this->employee_info["Company"]["id"];
            $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
            ));
            $ACLController = 'projects_preview';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
            $this->ProjectTask->staffingSystem($id_duplicate);
            $this->redirect("/projects/your_form/" . $id_duplicate);
        } else
            $this->Session->setFlash(__('KO', true));
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
                        $selected = "selected";
                    else
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
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_program($currenr_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        echo sprintf(__("%s--Select sub program--%s", true), '<option value="">', '</option>');
        if (!empty($this->params['url']['data'])) {
            $list = $this->ProjectAmr->ProjectAmrSubProgram->find('list', array('conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => $this->params['url']['data']), 'fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program')));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        }
    }

    /**
     * get value from project task
     *
     * @param int $id
     * @return array
     * @access public
     */
    private function _getPorjectTask($id) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectTask');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id
            )
        ));

        $projectTasks = $this->ProjectTask->find(
                "all", array(
            'fields' => array(
                'id',
                'estimated',
                'parent_id'
            ),
            'recursive' => -1,
            "conditions" => array('project_id' => $id, 'special' => 0)
                )
        );
        $_projectTaskId = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id') : array();
        $_parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.parent_id') : array();
        foreach ($projectTasks as $key => $projectTask) {
            if (in_array($projectTask['ProjectTask']['id'], $_parentIds)) {
                unset($projectTasks[$key]);
            }
        }
        foreach ($_parentIds as $k => $value) {
            if ($value == 0) {
                unset($_parentIds[$k]);
            }
        }
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_task_id'),
            'conditions' => array(
                'project_task_id' => $_projectTaskId,
                'special' => 0,
                'NOT' => array("project_task_id" => null))
        ));
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask') : array();

        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'employee_id', 'task_id', 'SUM(value) as value'),
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
            if ($projectTask['ProjectTask']['parent_id'] == 0) {
                $sumEstimated += $projectTask['ProjectTask']['estimated'];
            }
            $referencedEmployees = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if (count($referencedEmployees) == 0) {
                
            } else {
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
                    if ($completed < 0) {
                        $completed = 0;
                    }
                    $sumRemainConsumed += $completed;
                    $total += $consumed;
                } else {
                    if (in_array($projectTask['ProjectTask']['id'], $_parentIds, true)) {
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
        if (!empty($_parentIds)) {
            foreach ($_parentIds as $_parentId) {
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
        $validated = $_engaged + $sumRemain;
        if ($validated == 0) {
            $_progression = 0;
        } else {
            $_progression = round((($_engaged * 100) / $validated), 2);
        }
        if ($_progression > 100) {
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

    private function _makeDataAmr($listProjects = array()) {
        $this->loadModel('ProjectEvolution');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        /**
         * Lay company cua used dang login
         */
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay evolution theo danh sach id
         */
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProjects),
            'fields' => array('id', 'project_id', 'SUM(man_day) as totalMD'),
            'group' => array('project_id')
        ));
        $projectEvolutions = !empty($projectEvolutions) ? Set::combine($projectEvolutions, '{n}.ProjectEvolution.project_id', '{n}.0.totalMD') : array();
        /**
         * Lay danh sach activity theo list project
         */
        $listActivities = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null),
                'Project.id' => $listProjects
            ),
            'fields' => array('id', 'activity_id')
        ));
        /**
         * Lay cac project Task theo danh sach project
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProjects),
            'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed')
        ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
        $listIdProjectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.id', '{n}.id') : array();
        /**
         * Lay danh sach activity task theo danh sach project task
         */
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listIdProjectTasks),
            'fields' => array('id', 'activity_id', 'project_task_id')
        ));
        $listATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        /**
         * Lay consumed cua cac task
         */
        $consumedOfTasks = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                //'value'
                'SUM(value) as value'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0),
                'task_id' => array_keys($activityTasks)
            )
        ));
        /**
         * Lay consumed cua cac activity
         */
        $consumedOfActivities = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'activity_id',
                'SUM(value) as value'
            ),
            'group' => array('activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $listActivities,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
                )
        );
        /**
         * Chuyen consumed cua task vao activity
         */
        $sumActivities = array();
        if (!empty($consumedOfTasks)) {
            foreach ($consumedOfTasks as $_data) {
                $dx = $_data['ActivityRequest'];
                $activityId = !empty($activityTasks[$dx['task_id']]) ? $activityTasks[$dx['task_id']] : 0;
                if (!isset($sumActivities[$activityId])) {
                    $sumActivities[$activityId] = 0;
                }
                $sumActivities[$activityId] += $_data[0]['value'];
            }
        }
        /**
         * Cong consumed cua activity voi consumed cua task
         */
        if (!empty($consumedOfActivities)) {
            foreach ($consumedOfActivities as $data) {
                $dx = $data['ActivityRequest'];
                $data = $data['0']['value'];
                if (!isset($sumActivities[$dx['activity_id']])) {
                    $sumActivities[$dx['activity_id']] = 0;
                }
                $sumActivities[$dx['activity_id']] += $data;
            }
        }
        /**
         * Tinh cons
         */
        $results = $sumWorload = $sumOverload = $employees = $sumEmployees = $sumRemains = $sumRemainSpecials = array();
        $consumedOfTasks = !empty($consumedOfTasks) ? Set::combine($consumedOfTasks, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
        foreach ($projectTasks as $taskId => $projectTask) {
            if (in_array($taskId, $parentIds)) {
                unset($projectTasks[$taskId]);
            } else {
                $projectId = $projectTask['project_id'];
                $dataEstimated = $projectTask['estimated'];
                $dataOverload = $projectTask['overload'];
                if (!isset($sumWorload[$projectId])) {
                    $sumWorload[$projectId] = 0;
                }
                $sumWorload[$projectId] += $dataEstimated;

                if (!isset($sumOverload[$projectId])) {
                    $sumOverload[$projectId] = 0;
                }
                $sumOverload[$projectId] += $dataOverload;

                $consumedForTasks = 0;
                if (!empty($projectTask['special']) && $projectTask['special'] == 1) {
                    $consumedForTasks = !empty($projectTask['special_consumed']) ? $projectTask['special_consumed'] : 0;
                    if (!isset($sumRemainSpecials[$projectId])) {
                        $sumRemainSpecials[$projectId] = 0;
                    }
                    $sumRemainSpecials[$projectId] += $dataEstimated - $consumedForTasks;
                } else {
                    $ATaskId = !empty($listATaskLinkPTasks[$taskId]) ? $listATaskLinkPTasks[$taskId] : 0;
                    $consumedForTasks = !empty($consumedOfTasks[$ATaskId]) ? $consumedOfTasks[$ATaskId] : 0;
                }
                if (!isset($sumRemains[$projectId])) {
                    $sumRemains[$projectId] = 0;
                }
                $sumRemains[$projectId] += ($dataEstimated + $dataOverload) - $consumedForTasks;
            }
        }
        unset($sumActivities[0]);
        foreach ($listActivities as $projectId => $activityId) {
            $workload = isset($sumWorload[$projectId]) ? $sumWorload[$projectId] : 0;
            $overload = isset($sumOverload[$projectId]) ? $sumOverload[$projectId] : 0;
            $consumed = isset($sumActivities[$activityId]) ? $sumActivities[$activityId] : 0;
            $remainSPCs = isset($sumRemainSpecials[$projectId]) ? $sumRemainSpecials[$projectId] : 0;
            $remains = isset($sumRemains[$projectId]) ? $sumRemains[$projectId] : 0;
            $remains = $remains - $remainSPCs;
            $progress = 0;
            if (($workload + $overload) == 0) {
                $progress = 0;
            } else {
                $com = round(($consumed * 100) / ($workload + $overload), 2);
                if ($com > 100) {
                    $progress = 100;
                } else {
                    $progress = $com;
                }
            }
            //md_engaged
            $results[$projectId]['workload'] = $workload;
            $results[$projectId]['overload'] = $overload;
            $results[$projectId]['md_forecasted'] = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_validated'] = $consumed; //consumed
            $results[$projectId]['md_engaged'] = $remains; //remain
            $mdForecasted = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
            $results[$projectId]['project_id'] = $projectId;
            $results[$projectId]['project_amr_progression'] = $progress;
            if (!empty($listProjects[$projectId])) {
                unset($listProjects[$projectId]);
            }
        }
        foreach ($listProjects as $projectId) {
            $workload = isset($sumWorload[$projectId]) ? $sumWorload[$projectId] : 0;
            $overload = isset($sumOverload[$projectId]) ? $sumOverload[$projectId] : 0;
            $consumed = 0;
            $remainSPCs = isset($sumRemainSpecials[$projectId]) ? $sumRemainSpecials[$projectId] : 0;
            $remains = isset($sumRemains[$projectId]) ? $sumRemains[$projectId] : 0;
            $remains = $remains - $remainSPCs;
            $progress = 0;
            if (($workload + $overload) == 0) {
                $progress = 0;
            } else {
                $com = round(($consumed * 100) / ($workload + $overload), 2);
                if ($com > 100) {
                    $progress = 100;
                } else {
                    $progress = $com;
                }
            }
            //md_engaged
            $results[$projectId]['workload'] = $workload;
            $results[$projectId]['overload'] = $overload;
            $results[$projectId]['md_forecasted'] = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_validated'] = $consumed; //consumed
            $results[$projectId]['md_engaged'] = $remains; //remain
            $mdForecasted = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
            $results[$projectId]['project_id'] = $projectId;
            $results[$projectId]['project_amr_progression'] = $progress;
        }
        return $results;
    }

    public function getPersonalizedViews($status = null) {
        $this->layout = false;
        $userViews = array();
        if (!empty($status)) {
            $conditions = array();
            switch ($status) {
                case 6: {
                        $conditions = array(
                            'UserStatusView.oppor_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 3: {
                        $conditions = array(
                            'UserStatusView.archived_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 4: {
                        $conditions = array(
                            'UserStatusView.model_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 5: {
                        $conditions = array(
                            'OR' => array(
                                'UserStatusView.model_view' => 1,
                                'UserStatusView.oppor_view' => 1
                            ),
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 1:
                default: {
                        $conditions = array(
                            'UserStatusView.progress_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
            }
            if ($this->Session->read('mobile'))
                $conditions['UserView.mobile'] = 1;
            if ($this->is_sas)
                $userViews = $this->UserView->find('list', array(
                    'recursive' => -1,
                    'fields' => array('UserView.id', 'UserView.name'),
                    'conditions' => array('model' => 'project')));
            else {
                if (!empty($conditions)) {
                    $userViews = $this->UserView->find('list', array(
                        'recursive' => 0,
                        'fields' => array('UserView.id', 'UserView.name'),
                        'order' => 'UserView.public ASC',
                        'group' => 'UserView.id',
                        'conditions' => array(
                            'UserView.model' => 'project',
                            'OR' => array(
                                'UserView.employee_id' => $this->employee_info['Employee']['id'],
                                array(
                                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                                    'UserView.public' => true
                                ),
                            ),
                            $conditions
                        )
                    ));
                }
            }
        }
        echo json_encode($userViews);
        exit;
    }

    function getFamily($program_id = null) {
        $this->layout = false;
        $this->loadModel('ProjectAmrProgram');
        $results = '';
        if (!empty($program_id)) {
            $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectAmrProgram.id ' => $program_id
                ),
                'fields' => array('id', 'family_id')
            ));
            if (!empty($famAndSubFamOfPrograms) && !empty($famAndSubFamOfPrograms['ProjectAmrProgram']['family_id'])) {
                $results = $famAndSubFamOfPrograms['ProjectAmrProgram']['family_id'];
            }
        }
        echo json_encode($results);
        exit;
    }

    function getSubFamily($familyId = null, $sub_program_id = null, $company_id = null) {
        $this->layout = false;
        $this->loadModels('ActivityFamily', 'ProjectAmrSubProgram');
        $select = '<option value>-- Any -- </option>';
        $subFamId = '';
        if (!empty($familyId)) {
            $lists = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'parent_id' => $familyId,
                    'company_id' => !empty($company_id) ? $company_id : $this->employee_info["Company"]["id"]
                ),
                'fields' => array('id', 'name'),
                'order' => array('name')
            ));
            if (!empty($lists)) {
                foreach ($lists as $id => $val) {
                    $select .= "<option value='" . $id . "' >" . $val . "</option>";
                }
            }
            if ($sub_program_id) {
                $subFams = $this->ProjectAmrSubProgram->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectAmrSubProgram.id' => $sub_program_id),
                    'fields' => array('id', 'sub_family_id')
                ));
                if (!empty($subFams) && !empty($subFams['ProjectAmrSubProgram']['sub_family_id'])) {
                    $subFamId = $subFams['ProjectAmrSubProgram']['sub_family_id'];
                }
            }
        }
        $results = array(
            'select' => $select,
            'subFamId' => $subFamId
        );
        echo json_encode($results);
        exit;
    }

    /**
     * Tao 1 activity moi va lien ket voi project vua thay doi status tu Opportunity sang In progress
     */
    public function saveActivityLinked($project_id = null, $company_id = null, $isImport = false ) {
        $this->layout = false;
        $this->loadModels('Activity', 'ActivityFamily', 'ActivityTask', 'ProjectTask', 'ProjectEmployeeManager', 'Project', 'ProjectAmrProgram', 'ProjectAmrSubProgram');
		$this->_checkRole(true, $project_id);
		if($this->employee_info['Employee']['change_status_project'] != 1 && $this->employee_info['Role']['name'] == 'pm'){
			echo 'Error';
			exit;
		}
        $activity_id = '';
        if (!empty($this->data)) {
            $data = array();
			if($isImport){
				$project_imported = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('id' => $project_id),
                    'fields' => array('project_name', 'activated'),
                ));
				$data['name'] = $project_imported['Project']['project_name'];
				$data['name_detail'] = '';
				$data['short_name'] = $project_imported['Project']['project_name'];
				$data['activated'] = !empty($project_imported['Project']['activated']) ? $project_imported['Project']['activated'] : null;
			}else{
				$data = $this->data;
			}
            $activity_families = array();
            $fam_id = '';
            $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;
            $opportunity_to_in_progress_without_validation = isset($this->companyConfigs['opportunity_to_in_progress_without_validation']) && !empty($this->companyConfigs['opportunity_to_in_progress_without_validation']) ? true : false;

            // Edit by Viet Nguyen
            // Liked family of activity with program of project
            // If has linked and family linked is empty -> Error
            if ($activate_family_linked_program && $opportunity_to_in_progress_without_validation) {
                $projet_program = $this->Project->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_amr_program_id', 'project_amr_program_id'),
                    'conditions' => array('id' => $project_id))
                );
                if (!empty($projet_program)) {
                    $activity_families = $this->ProjectAmrProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id ' => $company_id,
                            'id' => $projet_program,
                        ),
                        'fields' => array('id', 'family_id')
                    ));
                    $fam_id = !empty($activity_families['ProjectAmrProgram']['family_id']) ? $activity_families['ProjectAmrProgram']['family_id'] : '';
                    if (!empty($fam_id)) {
                        $activity_families = $this->ActivityFamily->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'id' => $fam_id
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if (empty($activity_families))
                            $fam_id = '';
                    }
                }
            }else {
                $activity_families = $this->ActivityFamily->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id
                    ),
                    'fields' => array('id')
                ));
                if (!empty($activity_families)) {
                    $fam_id = $activity_families['ActivityFamily']['id'];
                }
            }
            // exit;
            if (empty($data['family_id']) && empty($fam_id)) {
                // echo json_encode('Error');
                exit;
            }

            $this->Project->id = $project_id;
            $this->Project->save(array('category' => 1, 'updated_opp_ip' => time()));

            // Viet nguyen update
            // when program linked to family then sub program too link sub family

            $fam_id = !empty($data['family_id']) ? $data['family_id'] : $fam_id;
            // Get sub family
            $sub_family_id = '';
            if (empty($data['sub_family_id'])) {
                $sub_familie = $this->ActivityFamily->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'parent_id' => $fam_id,
                    ),
                    'fields' => array('id')
                ));
                $sub_family_id = !empty($sub_familie) ? $sub_familie['ActivityFamily']['id'] : '';
            } else {
                $sub_family_id = $data['sub_family_id'];
            }
            $saved = array(
                'name' => $data['name'],
                'long_name' => $data['name_detail'],
                'short_name' => $data['short_name'],
                'family_id' => $fam_id,
                'subfamily_id' => $sub_family_id,
                'pms' => 1,
                'project' => $project_id,
                'company_id' => $company_id,
                'activated' => $data['activated'],
            );
            $check = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id,
                    'company_id' => $company_id
                )
            ));


            if (!empty($check)) {
                $this->Activity->id = $check['Activity']['id'];
            } else {
                $this->Activity->create();
            }
            // IF save sussces activity then linked sub program with sub family
            $sub_program_id = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id ' => $company_id,
                    'id' => $project_id,
                ),
                'fields' => array('project_amr_sub_program_id', 'project_amr_sub_program_id')
            ));
            if ($this->Activity->save($saved) && !empty($sub_program_id) && !empty($sub_family_id)) {
                // get the first sub program of program
                $this->ProjectAmrSubProgram->id = $sub_program_id;
                $this->ProjectAmrSubProgram->saveField('sub_family_id', $sub_family_id);
            }
            if (!empty($check)) {
                $activity_id = $check['Activity']['id'];
            } else {
                $activity_id = $this->Activity->getLastInsertID();
            }
			$this->Project->id = $project_id;
            $this->Project->saveField('activity_id', $activity_id);
            $project_tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                )
            ));
			
            // Update actvity_id khi duplicate project
            $this->updateProjectBudget($project_id, $activity_id);
            if (!empty($project_tasks)) {
                $this->ProjectTask->staffingSystem($project_id);
                foreach ($project_tasks as $project_task) {
                    $this->_syncActivityTask($project_id, $project_task, $project_task['ProjectTask']['id']);
                }
            }
			$log_arr = array(
				'project_id' => $project_id,
				'category' => 1,
				'activity_id' => $activity_id
			);
			$this->writeLog($log_arr, $this->employee_info, sprintf('Update status: `%s` for project `%s`', 1, $project_id));
        }
		if($isImport){
			return $activity_id;
		}else{
			echo json_encode($activity_id);
			exit;
		}
    }

    // Create by VN
    // Update actvity_id khi chuyen trang thai
    // OP -> IN -> activity_id = activity_id_linked
    // IP -> OP ->activity_id = 0
    private function updateProjectBudget($project_id, $activity_id) {
        $this->loadModels('ProjectBudgetSyn', 'ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectBudgetSale');
        $this->_checkRole(true, $project_id);
		// Update activity_id linked in ProjectBudgetSyn table
        $project_budget_sysn = $this->ProjectBudgetSyn->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array('id')
        ));
        $project_budget_sysn_id = !empty($project_budget_sysn) ? $project_budget_sysn['ProjectBudgetSyn']['id'] : '';
        if (!empty($project_budget_sysn_id)) {
            $this->ProjectBudgetSyn->id = $project_budget_sysn_id;
            $this->ProjectBudgetSyn->saveField('activity_id', $activity_id);
        }

        // Update activity_id linked in project budget internal detail
        $acId_internal = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_internal)) {
            foreach ($acId_internal as $acId_inter) {
                $this->ProjectBudgetInternalDetail->id = $acId_inter;
                $this->ProjectBudgetInternalDetail->saveField('activity_id', $activity_id);
            }
        }

        // Update activity_id linked in project budget external
        $acId_external = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_external)) {
            foreach ($acId_external as $acId_exter) {
                $this->ProjectBudgetExternal->id = $acId_exter;
                $this->ProjectBudgetExternal->saveField('activity_id', $activity_id);
            }
        }

        // Update activity_id linked in project budget sales
        $acId_sale = $this->ProjectBudgetSale->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_sale)) {
            foreach ($acId_sale as $acId_sl) {
                $this->ProjectBudgetSale->id = $acId_sl;
                $this->ProjectBudgetSale->saveField('activity_id', $activity_id);
            }
        }
		$listModels = array('ProjectFinancePlus', 'ProjectFinancePlusDetail', 'ProjectBudgetInvoice','ProjectBudgetProvisional','ProjectBudgetPurchaseInvoice','ProjectBudgetPurchase','ProjectEmployeeManager');
		foreach( $listModels as $model){
			$this->loadModel($model);
			$this->$model->updateAll(
				array($model.'.activity_id' => $activity_id), 	// data
				array($model.'.project_id' => $project_id) 		// conditions
			);
		}
    }

    private function _getPhaseses($project_id) {
        if (!isset($this->_phases)) {
            $project = $this->_getProject($project_id);
            $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
            $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
                'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
                'conditions' => array(
                    "ProjectPhasePlan.project_id" => $project['Project']['id'],
                    'company_id' => $project['Project']['company_id']
                )
            ));
            $this->_phases = $projectPhases;
        }
        return $this->_phases;
    }

    /**
     * Function: Create new activity task
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
    private function _createActivityTask($project_task, $activity_id, $project_task_id, $project_id) {
        $dataActivityTask = array();
        // $phases = $this->_getPhasesesCombined($project_id);
        // $phase_name = isset($project_task['ProjectTask']['project_planed_phase_id']) ? $phases[$project_task['ProjectTask']['project_planed_phase_id']] : '';
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id, $project_task['ProjectTask']['project_planed_phase_id']) : '';
        $activity_task_name = $phase_name . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        if (!empty($checkTask)) {
            $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
        } else {
            $this->ActivityTask->create();
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
            $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
        }
        $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
        $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
        $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
        $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
        $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
        $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
        $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
        $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
        //update nctworkload activity task id
        $this->loadModel('NctWorkload');
        $this->NctWorkload->updateAll(array(
            'NctWorkload.activity_task_id' => $this->ActivityTask->id
                ), array(
            'NctWorkload.project_task_id' => $project_task_id
        ));
        return $result;
    }

    /**
     * Function: create activity subtask
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
	private function _getProjectTaskById($project_task_id){
        $project_task = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                "ProjectTask.id" => $project_task_id
            ),
            'fields' => array('task_title', 'task_start_date', 'task_end_date', 'duration')
        ));
        return $project_task;
    }
    private function _createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $parent_project_task_id) {
        $dataActivityTask = array();
        //$phases = $this->_getPhasesesCombined($project_id);
        //$phase_name = $phases[$project_task['ProjectTask']['project_planed_phase_id']];
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id, $project_task['ProjectTask']['project_planed_phase_id']) : '';
        //parent_project_task_id
        //_getActivityTasksByActivityIdAndProjectTaskId
        $parent_task = $this->_getProjectTaskById($parent_project_task_id);
        $parent_activity_task = $this->_getActivityTasksByActivityIdAndProjectTaskIdPart2($activity_id, $parent_project_task_id);
        $task_title = !empty($parent_task['ProjectTask']['task_title']) ? $parent_task['ProjectTask']['task_title'] : '';
        $activity_task_name = $phase_name . "/" . $task_title . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        $result = array();
        if (!empty($parent_activity_task['ActivityTask']['id'])) {
            if (!empty($checkTask)) {
                $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            } else {
                $this->ActivityTask->create();
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
                $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
                $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
                $dataActivityTask['ActivityTask']['parent_id'] = $parent_activity_task['ActivityTask']['id'];
            }
            $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
            $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
            $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
            $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
            $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
            $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
            $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
            $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
            //update nctworkload activity task id
            $this->loadModel('NctWorkload');
            $this->NctWorkload->updateAll(array(
                'NctWorkload.activity_task_id' => $this->ActivityTask->id
                    ), array(
                'NctWorkload.project_task_id' => $project_task_id
            ));
        }

        return $result;
    }

    //create project task - update activity task deltail
    private function _syncActivityTask($project_id, $project_task, $project_task_id) {
        $projects = $this->_getProject($project_id);
        $activity = $this->_getActivity($project_id);
        if (isset($activity)) {
            if (isset($activity[0])) {
                if (isset($activity[0]['Activity'])) {
                    if (isset($activity[0]['Activity']['id'])) {
                        $activity_id = $activity[0]['Activity']['id'];
                        $is_exist_activity_task = $this->_checkExistActivityTask($activity_id, $project_task_id);
                        if ($project_task['ProjectTask']['parent_id'] == 0 || $project_task['ProjectTask']['parent_id'] == null || $project_task['ProjectTask']['parent_id'] == '') {
                            $this->_createActivityTask($project_task, $activity_id, $project_task_id, $project_id);
                        } else {
                            $this->_createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $project_task['ProjectTask']['parent_id']);
                        }
                    }
                }
            }
        }
    }

    private function _getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id) {
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id,
            )
        ));
        return $activityTasks;
    }

    private function _getActivityTasksByActivityIdAndProjectTaskIdPart2($activityIds, $taskIds) {
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find(
                'first', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activityIds,
                'project_task_id' => $taskIds,
            ),
            'fields' => array('id')
        ));
        return $activityTasks;
    }

    /**
     * Function:
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
    private function _checkExistActivityTask($activity_id, $project_task_id) {
        $activity_tasks = $this->_getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id);
        if (isset($activity_tasks)) {
            if (count($activity_tasks) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function _getPhaseNameByPhasePlanId($project_id, $project_planed_phase_id) {
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhase.name');
        return $projectPhases[$project_planed_phase_id];
    }

    /**
     * Function: Get activity
     * @var     : $project_id
     * @return  : activity object
     * @author : BANGVN
     * */
    private function _getActivity($project_id) {
        if (isset($this->_activity)) {
            
        } else {
            $this->loadModel('Activity');
            $activity = ClassRegistry::init('Activity')->find('all', array(
                'recursive' => -1,
                'conditions' => array('Activity.project' => $project_id),
            ));
            $this->_activity = $activity;
        }

        return $this->_activity;
    }

    private function _getProject($project_id) {
        if (!isset($this->_project)) {
            $project = $this->ProjectTask->Project->find("first", array(
                'conditions' => array('Project.id' => $project_id)
                    )
            );
            $this->_project = $project;
        }

        return $this->_project;
    }

    /**
     * Xoa activity lien ket khi chuyen tu In progress sang Opportunity
     */
    public function deleteActivityLinked($project_id = null, $isImport = false) {
        $this->layout = false;
        $this->loadModel('Activity');
        $this->loadModel('ActivityTask');
		$this->_checkRole(false, $project_id);
		if($this->employee_info['Employee']['change_status_project'] != 1 && $this->employee_info['Role']['name'] == 'pm'){
			echo 'Error';
			exit;
		}
		$result = '';
        // Update by VN
        // Check data task not in timesheet before delete task
        $task_in_timesheet = $this->_getCountTaskTimeSheet($project_id);
        if (!empty($project_id) && $task_in_timesheet == 0) {
            // viet nay hoi thua, nhung chu yeu de xoa nhung project duplicate activity truoc day.
            $cate = !empty($_POST['category']) ? $_POST['category'] : 2;
            $this->Project->id = $project_id;
            $this->Project->save(array(
				'category' => $cate,
				'activity_id' => null
			));
            $activity = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                ),
                'fields' => array('id', 'project')
            ));
            if (!empty($activity)) {
                foreach ($activity as $activity_id => $p) {
                    $tasks = $this->ActivityTask->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $activity_id),
                        'fields' => array('id', 'id')
                    ));
                    if (!empty($tasks)) {
                        foreach ($tasks as $id) {
                            $this->ActivityTask->delete($id);
                        }
                    }
                    $this->Activity->delete($activity_id);
                }
            }
			// Update actvity_id khi delete activity linked
			$this->updateProjectBudget($project_id, 0);
			$this->writeLog($project_id, $this->employee_info, sprintf('Update status: `%s` for project `%s`', $cate, $project_id));
            $result = 'OK';
        } else {
            $result = 'Error';
        }
		if($isImport){
			return $result;
		}else{
			echo $result;
			exit;
		}
    }

    private function _getCountTaskTimeSheet($project_id) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('Project');
        $activity_id = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id
            ),
            'fields' => array('id', 'activity_id')
        ));

        $tasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'id')
        ));
        if (!empty($tasks)) {
            $request = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'activity_id' => $activity_id,
                        'task_id' => $tasks
                    )
                )
            ));
        } else {
            $request = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id)
            ));
        }
        return $request;
    }

    /**
     * Get status of company
     */
    private function _getIdStatusClosedOfCompany() {
        $this->loadModel('ProjectStatus');
        $infors = $this->Session->read('Auth.employee_info');
        $status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                //'name' => array('Clos', 'closed ', 'A lancer', 'A launch'),
                'company_id' => $infors['Company']['id']
            )
        ));
        $close = $aLancer = '';
        if (!empty($status)) {
            foreach ($status as $id => $val) {
                $val = trim(strtolower($val));
                if ($val == 'clos' || $val == 'closed') {
                    $close = $id;
                } elseif ($val == 'a lancer' || $val == 'a launch') {
                    $aLancer = $id;
                }
            }
        }
        return $close;
    }
	
	
	/* 
	* BEGIN parseInputFilter
	* param $cate, $projectL
	* return array(
			'cate' => 
			'prog_id' => 
			'filter' => 
			
		)
	* Ticket 401
	*/
	private function parseInputFilter($cate){
		$company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
		
		$appstatus = '';
		// $cate = 1;
		$prog = array();
		$filter = array();
		
		// Get HistoryFilter. Use for get data & save data
		$list_prog_path = 'project_grid_prog_filter';
		$last_prog = $this->Employee->HistoryFilter->find('first', array(
				'recursive' => -1,
				'fields' => array(
					'id', 'params'
				),
				'conditions' => array(
					'path' => $list_prog_path,
					'employee_id' => $employee_id
				)
			));
		
		// Get data from FORM 
		if( !empty( $this->data['Project']['cate'])){ 
			$cate = $this->data['Project']['cate'];
			$appstatus = $cate;
			$prog = !empty( $this->data['Project']['typeProject']) ? $this->data['Project']['typeProject'] : array();
		}
		// Get data from URL
		elseif( !empty($cate) ){
			// Url with list Prograam ID: p-234-345-346
			if( preg_match('/^p-/', $cate)){
				$prog = explode('-', str_replace('p-', '', $cate));
				$cate = 5;
			}
			// Url with list filter: filter-weather_sun-pm_2378-avancement_ADV-pname_PROJECT
			elseif( preg_match('/^filter-/', $cate)){
				$filter = explode('-',str_replace('filter-', '', $cate));
				$cate = 5;
			}else{
				$cate = intval($cate);
				$appstatus = $cate;
			}
		}
		//get From History filter
		elseif (!empty($last_prog)) {
			$history_prog_id = unserialize($last_prog['HistoryFilter']['params']);
			$cate = !empty($history_prog_id['cate']) ? $history_prog_id['cate'] : '1';
			$prog = !empty($history_prog_id['list_prog_id']) ? $history_prog_id['list_prog_id'] : array();
			
		}
		// END get data 
		
		// Save data 
		if (empty($last_prog)){
			$this->Employee->HistoryFilter->create();
		}else{
			 $this->Employee->HistoryFilter->id = $last_prog['HistoryFilter']['id'];
		}
		$this->Employee->HistoryFilter->save( 
			array(
				'path' => $list_prog_path,
				'params' => serialize(array(
					'list_prog_id' => array_values($prog),
					'cate' => $cate
					)),
				'employee_id' => $employee_id
			),
			array(
				'validate' => false,
				'callbacks' => false
			)
		);
		// Saved data 
		
		$result = array( $cate, $prog, $filter );
		// debug( $result); exit;
		return $result;
		
	}
	/* END parseInputFilter */

    public function map($cat = '', $projects = '') {
        $company = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        list($cat, $prog, $filter) = $this->parseInputFilter($cat);
        $type = $cat == 5 ? array(1, 2) : ($cat == 6 ? 2 : $cat);
        $cond = array(
            'category' => $type,
            'company_id' => $company
        );
        if (!empty($projects)) {
            $cond['id'] = explode('-', $projects);
            $prog = array();
        }
        $prog = array_values($prog);
        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
		if(!empty($listProgramFields)){
			asort($listProgramFields);
		}
        if (!empty($prog)) {
            $cond['project_amr_program_id'] = $prog;
        }
		/**
		* Update ticket #418 - Lấy lại list project cho screen Map, same với project hiển thị bên Project List. By QuanNV 01/07/2019
		*/
		$adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
		$this->loadModel('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        $listProjectOfPM = array();
		$employee = $this->Session->read("Auth.employee_info");
		
        if ($role == 'pm') {
            $listProjectOfPM = $this->getProjectOfPM($employee, $employeeId);
        }
		
		if($role == 'pm'){
			if(!$adminSeeAllProjects){
				$this->loadModel('ProjectEmployeeManager');
				if (!$seeAllOfEmploy) {
					$listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'project_manager_id' => $employeeId,
							'OR' => array(
								'is_profit_center' => 0,
								'is_profit_center is NULL'
							),
						),
						'fields' => array('project_id', 'project_id')
					));
					$listProjectOfEmployManager = $this->Project->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $employee["Company"]["id"],
							'project_manager_id' => $employeeId
						),
						'fields' => array('id', 'id')
					));
					$listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectOfEmployManager);
					$projects = $this->Project->find('all', array(
						'recursive' => -1,
						'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
						'conditions' => array(
							'Project.category' => $cond['category'],
							'Project.company_id' => $cond['company_id'],
							'Project.id' => $listProjectIds
						)
					));
				} else {
					$projects = $this->Project->find('all', array(
						'recursive' => -1,
						'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
						'conditions' => $cond
					));
				}
			} else{
				$projects = $this->Project->find('all', array(
					'recursive' => -1,
					'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
					'conditions' => $cond
				));
			}
		} else {
			$projects = $this->Project->find('all', array(
				'recursive' => -1,
				'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
				'conditions' => $cond
			));
		}
		/*
		*End Update. 01/07/2019
		*/
        $projectsId = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.id') : array();
        $this->loadModels('ProjectAmrProgram', 'ProjectGlobalView', 'ProjectAmr');
        $projectAmrProgram = $this->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'color'),
            'conditions' => array(
                'company_id' => $company
            )
        ));
        $imageGlobals = $this->ProjectGlobalView->find('list', array(
            'recursive' => -1,
            'fields' => array('project_id', 'attachment'),
            'conditions' => array(
                'project_id' => $projectsId
            )
        ));
        $projectAmrs = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'fields' => array('project_id', 'weather', 'rank'),
            'conditions' => array('project_id' => $projectsId)
        ));
        $listWeather = !empty($projectAmrs) ? Set::combine($projectAmrs, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.weather') : array();
        $listRank = !empty($projectAmrs) ? Set::combine($projectAmrs, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.rank') : array();
        $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $company, 'default_screen' => 1)
        ));

        $screenDashboard = '';
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));

        $screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();

        // ob_clean();debug($screenDashboard);exit;

        $ACLController = 'projects';
        $ACLAction = 'edit';
        if (!empty($screenDefaults)) {
            $ACLController = $screenDefaults['Menu']['controllers'];
            $ACLAction = $screenDefaults['Menu']['functions'];
        }
        $this->set(compact('screenDashboard'));
        $this->set(compact('projects', 'cat', 'projectAmrProgram', 'imageGlobals', 'listWeather', 'listRank', 'ACLController', 'ACLAction', 'listProgramFields', 'prog'));
    }

    public function updatePriority() {
		$result = 0;
        if (!empty($this->data)) {
			$this->_checkRole(true, $this->data['id']);
            $result = $this->Project->save(array(
                'id' => $this->data['id'],
                'project_priority_id' => $this->data['project_priority_id']
            ));
			$result = !empty($result ) ? 1 : 0;
        }
        die($result);
    }

    public function updateTypeAndProgram() {
		$result = 0;
        if (!empty($this->data)) {
			$this->_checkRole(true, $this->data['project_id']);
            $key = explode('.', $this->data['keys']);
            $result = $this->Project->save(array(
                'id' => $this->data['project_id'],
                $key[1] => $this->data['data_id']
            ));
			$result = !empty($result ) ? 1 : 0;
        }
        die($result);
    }

    public function checkCode1($id = null) {
        $code = $this->data['code'];
		$result = true;
		$data = array();
		$message = "";
		if($code !=''){
			$projectCode = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'id !=' => $id,
					'project_code_1' => $code
				),
				'fields' => array('project_name')
			));
		}
        if (!empty($projectCode)) {
			$result = false;
            $data = $projectCode['Project']['project_name'];
        }
        die(json_encode(array(
			'result' => $result,
			'data' => $data,
			'message' => $message
		)));
    }

    public function checkCodeAdd1() {
        $code = $this->data['code'];
        $projectCode = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'project_code_1' => $code
            ),
            'fields' => array('project_name')
        ));
        if (!empty($projectCode)) {
            die($projectCode['Project']['project_name']);
        }
        die;
    }

    public function export_vision_task() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($_GET)) {
            $list = $_GET;
            // lay du lieu cho export.
            $datas = $this->getDataForVisionTasks($list);
            // kiem tra neu data empty thi thong bao loi
            if (empty($datas)) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect("/projects/index");
            }
            $langCode = Configure::read('Config.language');
            if ($langCode == 'eng') {
                $fieldName = array('name', 'english');
            } else {
                $fieldName = array('name', 'france');
            }
            $this->loadModel('VisionTaskExport');
            $fieldset = $this->VisionTaskExport->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'display' => 1
                ),
                'fields' => $fieldName,
                'order' => array('weight')
            ));
            $this->set('fieldset', $fieldset);
            $this->set('datas', $datas);
            $this->layout = '';
        }
    }

    public function check_task_for_task_vision() {
        if (!empty($_POST)) {
            $taskName = $_POST['task'];
            $company_id = $this->employee_info['Company']['id'];
            $listProject = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'fields' => array('id', 'project_name')
            ));
            $_conditions['task_title LIKE'] = '%' . $taskName . '%';
            $_conditions['project_id'] = array_keys($listProject);
            if (!empty($_POST['start']) && $_POST['start'] != '') {
                $start = $_POST['start'];
                $start = explode('-', $start);
                $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
                $_conditions['task_start_date >='] = $_start;
            }
            if (!empty($_POST['end']) && $_POST['end'] != '') {
                $start = $_POST['end'];
                $start = explode('-', $start);
                $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
                $_conditions['task_end_date <='] = $_start;
            }
            $countTask = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => $_conditions
            ));
            die(json_encode($countTask));
        }
        die;
    }

    public function tasks_vision() {
        if (!empty($_GET)) {
            $list = $_GET;
            $company_id = $this->employee_info['Company']['id'];
            $datas = $this->getDataForVisionTasks($list);
            $langCode = Configure::read('Config.language');
            if ($langCode == 'eng') {
                $fieldName = array('name', 'english');
            } else {
                $fieldName = array('name', 'france');
            }
			$bg_currency = $this->getCurrencyOfBudget();
            $this->loadModel('VisionTaskExport');
            $fieldset = $this->VisionTaskExport->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'display' => 1
                ),
                'fields' => $fieldName,
                'order' => array('weight')
            ));
            $clStatus = $this->ProjectStatus->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'status' => 'CL'
                ),
                'fields' => array('id')
            ));
            $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
            $this->set(compact('datas', 'fieldset', 'clStatus', 'bg_currency'));
        } else {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect("/projects/index");
        }
    }

    public function getDataForVisionTasks($list, $id = null) {
        $conditions = array();
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $conditions['company_id'] = $company_id;
        // kiem tra va get project theo category.
        if (!empty($list['nameCategory'])) {
            $conditions['category'] = $list['nameCategory'];
        }
        // lay theo status PJ
        if (!empty($list['nameStatusProject'])) {
            $conditions['project_status_id'] = $list['nameStatusProject'];
        }
        if (!empty($list['nameProgramProject'])) {
            $conditions['project_amr_program_id'] = $list['nameProgramProject'];
        }
        if (!empty($list['nameSubProgramProject'])) {
            $conditions['project_amr_sub_program_id'] = $list['nameSubProgramProject'];
        }
        if (!empty($list['nameCodeProject'])) {
            $conditions['project_code_1'] = $list['nameCodeProject'];
        }
        if (!empty($list['nameCodeProject1'])) {
            $conditions['project_code_2'] = $list['nameCodeProject1'];
        }
        // phan nay thuoc assistant.
        if (!empty($list['manager'])) {
            $this->loadModel('ProjectEmployeeManager');
            $list_project_my_manager = array();
            $list_project_my_manager = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'project_manager_id' => $employee_id,
                    'category' => 1
                ),
                'fields' => array('Project.id', 'Project.id')
            ));
            $list_project_my_manager_bakcup = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectEmployeeManager.project_manager_id' => $employee_id,
                    'ProjectEmployeeManager.is_profit_center' => 0
                ),
                'joins' => array(
                    array(
                        'table' => 'projects',
                        'alias' => 'Project',
                        'conditions' => array(
                            'Project.id = ProjectEmployeeManager.project_id',
                            'Project.category' => 1
                        )
                    )
                ),
                'fields' => array('ProjectEmployeeManager.project_id', 'ProjectEmployeeManager.project_id')
            ));
            $list_project_my_manager = array_merge($list_project_my_manager, $list_project_my_manager_bakcup);
            $conditions['Project.id'] = $list_project_my_manager;
        }
        $listProject = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('id', 'project_name', 'project_amr_program_id', 'project_amr_sub_program_id', 'project_code_1', 'project_code_2')
        ));
        $_listProjectIds = !empty($listProject) ? Set::classicExtract($listProject, '{n}.Project.id') : array();
        $listProjecNames = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_name') : array();
        $listProjectProgramIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_amr_program_id') : array();
        $listProjectSubProgramIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_amr_sub_program_id') : array();
        $listCodeProject = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_code_1') : array();
        $listCodeProject1 = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_code_2') : array();
        //
        $listIdModifyByPm = array();
        $roleLogin = $this->employee_info['Role']['name'];
        $this->loadModel('ProjectEmployeeManager');
        if ($roleLogin == 'pm') {
            $prList1 = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $_listProjectIds,
                    'Project.project_manager_id' => $employee_id
                ),
                'fields' => array('id', 'id')
            ));
            $prList2 = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $_listProjectIds,
                    'project_manager_id' => $employee_id
                ),
                'fields' => array('project_id', 'project_id')
            ));
            $listIdModifyByPm = array_unique(array_merge($prList1, $prList2));
        }
        // debug($_listProjectIds); exit;
        // list employee assign to
        $prList3 = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $_listProjectIds,
                'Project.project_manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));


        // task.
        $_conditions = array();
        $_conditions['project_id'] = $_listProjectIds;
        if (!empty($list['nameStatusTask'])) {
            $_conditions['OR'] = array(
                'task_status_id' => $list['nameStatusTask'],
                'task_status_id IS NULL'
            );
        }
        if (!empty($list['nameProrityTask'])) {
            $_conditions['task_priority_id'] = $list['nameProrityTask'];
        }
        if (!empty($list['nameStartDateVision'])) {
            $start = explode('-', $list['nameStartDateVision']);
            $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
            $_conditions['task_start_date >='] = $_start;
        }
        if (!empty($list['nameEndDateVision'])) {
            $end = explode('-', $list['nameEndDateVision']);
            $_end = $end[2] . '-' . $end[1] . '-' . $end[0];
            $_conditions['task_end_date <='] = $_end;
        }
        $this->loadModels('ProjectTaskEmployeeRefer', 'ProjectMilestone');
        if (!empty($list['nameMilestone'])) {
            $m = $this->ProjectMilestone->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_milestone' => $list['nameMilestone']
                ),
                'fields' => array('id', 'id')
            ));
            $_conditions['milestone_id'] = $m;
        }
        // get assign.
        if ($this->params['action'] == 'tasks_vision_new' && in_array($this->params['pass'][0], array(7, 8, 9))) {
            // truong hop cho list la 7,8,9. assign cho team.
            if (!empty($list['assignedTeam'])) {
                $this->loadModel('Employee');
                $listEmOfTeam = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'profit_center_id' => $list['assignedTeam']
                    ),
                    'fields' => array('id', 'id')
                ));
                $listEmOfTeam[$employee_id] = $employee_id;
                $listTaskAssign = $this->ProjectTaskEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            array(
                                'reference_id' => $list['assignedTeam'],
                                'is_profit_center' => 1
                            ),
                            array(
                                'reference_id' => $listEmOfTeam,
                                'is_profit_center' => 0
                            )
                        )
                    ),
                    'fields' => array('id', 'project_task_id')
                ));
                if (!empty($listTaskAssign)) {
                    $_conditions['ProjectTask.id'] = $listTaskAssign;
                } else if ($this->params['action'] == 'tasks_vision_new') {
                    // truong hop nay k co gi de xuat hien.
                    $_conditions['ProjectTask.id'] = 0;
                }
            }
        } else {
            if (!empty($list['assignedTeam']) || !empty($list['assignedResources'])) {
                $listTaskAssignForTeam = $listTaskAssignForEmployee = array();
                if (!empty($list['assignedTeam']) && $list['assignedTeam'] != '') {
                    $listTaskAssignForTeam = $this->ProjectTaskEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $list['assignedTeam'],
                            'is_profit_center' => 1
                        ),
                        'fields' => array('id', 'project_task_id')
                    ));
                }
                if (!empty($list['assignedResources']) && $list['assignedResources'] != '') {
                    $listTaskAssignForEmployee = $this->ProjectTaskEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $list['assignedResources'],
                            'is_profit_center' => 0
                        ),
                        'fields' => array('id', 'project_task_id')
                    ));
                }
                $listTaskAssign = array_unique(array_merge($listTaskAssignForTeam, $listTaskAssignForEmployee));
                if (!empty($listTaskAssign)) {
                    $_conditions['ProjectTask.id'] = $listTaskAssign;
                } else if ($this->params['action'] == 'tasks_vision_new') {
                    // truong hop nay k co gi de xuat hien.
                    $_conditions['ProjectTask.id'] = 0;
                }
            }
        }
        if (!empty($list['nameTaskProject'])) {
            $_conditions['task_title LIKE'] = '%' . $list['nameTaskProject'] . '%';
        }
        // list task for export
        $listTaskExport = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => $_conditions,
            'fields' => array('id', 'task_title', 'project_id', 'project_planed_phase_id', 'task_status_id', 'task_priority_id', 'task_start_date', 'task_end_date', 'text_1', 'initial_estimated', 'initial_task_start_date', 'initial_task_end_date', 'duration', 'overload', 'amount', 'progress_order', 'milestone_id'),
            'order' => array('project_id')
        ));
        $projectProgram = $this->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_amr_program_id' => array_keys($projectProgram)),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        $listPriority = $this->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'priority')
        ));
        $milestone = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $_listProjectIds),
            'fields' => array('id', 'project_id', 'project_milestone', 'milestone_date', 'validated')
        ));
        $listMilestone = !empty($milestone) ? Set::combine($milestone, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.project_milestone') : array();
        $_milestoneColor = array();
        foreach ($milestone as $_milestone) {
            $dx = $_milestone['ProjectMilestone'];
            if (!empty($dx['validated'])) {
                $_milestoneColor[$dx['id']] = 'milestone-green';
            } else {
                $currentDate = strtotime(date('d-m-Y', time()));
                $k = strtotime($dx['milestone_date']);
                if ($currentDate > $k) {
                    $_milestoneColor[$dx['id']] = 'milestone-mi';
                } elseif ($currentDate < $k) {
                    $_milestoneColor[$dx['id']] = 'milestone-blue';
                } else {
                    $_milestoneColor[$dx['id']] = 'milestone-orange';
                }
            }
        }
        $listPhasePlansIds = !empty($listTaskExport) ? Set::classicExtract($listTaskExport, '{n}.ProjectTask.project_planed_phase_id') : array();
        $listTaskIds = !empty($listTaskExport) ? Set::classicExtract($listTaskExport, '{n}.ProjectTask.id') : array();
        $listProjectIdOfTasks = !empty($listTaskExport) ? Set::combine($listTaskExport, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
        $_listPhaseAndPartIds = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectPhasePlan.id' => $listPhasePlansIds),
            'fields' => array('id', 'project_planed_phase_id', 'project_part_id')
        ));
        $listPhaseIds = !empty($_listPhaseAndPartIds) ? Set::combine($_listPhaseAndPartIds, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
        $listProjectPartIds = !empty($_listPhaseAndPartIds) ? Set::combine($_listPhaseAndPartIds, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $this->loadModels('ProjectPhases', 'ProjectParts');
        $listPhaseNames = $this->ProjectPhases->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectPhases.id' => $listPhaseIds),
            'fields' => array('id', 'name')
        ));
        $listPartNames = $this->ProjectParts->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectParts.id' => $listProjectPartIds),
            'fields' => array('id', 'title')
        ));
        $listAssign = $this->ProjectTaskEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listTaskIds),
            'fields' => array('id', 'reference_id', 'project_task_id', 'is_profit_center')
        ));
        $pcNames = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $employeeNames = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'fullname')
        ));
        // set name assign
        $nameListAssign = $listEditStatus = $listEmployeeRefer = array();
        foreach ($listAssign as $key => $value) {
            $dx = $value['ProjectTaskEmployeeRefer'];
            // $nameListAssign[$dx['project_task_id']] = '';
            $listEmployeeRefer[$dx['reference_id']]['is_profit_center'] = $dx['is_profit_center'];
            if ($dx['is_profit_center'] == 1) {
                $name = !empty($pcNames[$dx['reference_id']]) ? 'PC / ' . $pcNames[$dx['reference_id']] : '';
                $listEmployeeRefer[$dx['reference_id']]['employee_name'] = !empty($pcNames[$dx['reference_id']]) ? $pcNames[$dx['reference_id']] : '';
            } else {
                $name = !empty($employeeNames[$dx['reference_id']]) ? $employeeNames[$dx['reference_id']] : '';
                $listEmployeeRefer[$dx['reference_id']]['employee_name'] = !empty($employeeNames[$dx['reference_id']]) ? $employeeNames[$dx['reference_id']] : '';
            }
            if (empty($nameListAssign[$dx['project_task_id']])) {
                $nameListAssign[$dx['project_task_id']] = $name;
            } else {
                $nameListAssign[$dx['project_task_id']] .= ', ' . $name;
            }
            $listEditStatus[] = $dx['reference_id'];
        }
        $listEditStatus = array_unique($listEditStatus);
        // get consume of tasks
        $this->loadModel('ActivityRequest');
        $this->loadModels('ActivityTask', 'HistoryFilter');
        $listActivityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('project_task_id', 'id'),
            'conditions' => array('project_task_id' => $listTaskIds)
        ));
        $this->ActivityRequest->virtualFields['consumed'] = 'SUM(ActivityRequest.value)';
        $consumeOfActivityTasks = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $listActivityTasks,
                'value !=' => 0,
                'status' => 2
            ),
            'fields' => array('task_id', 'consumed'),
            'group' => array('task_id')
        ));
        $this->ActivityRequest->virtualFields['in_used'] = 'SUM(ActivityRequest.value)';
        $inUsedOfActivityTasks = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $listActivityTasks,
                'value !=' => 0,
                'status !=' => 2
            ),
            'fields' => array('task_id', 'in_used'),
            'group' => array('task_id')
        ));
        $workloadOfTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.id' => $listTaskIds
            ),
            'fields' => array('id', 'estimated')
        ));
        $datas = array();
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        // filter.
        $curDate = time();
        // tranh truong hop buoi chieu => sai so.
        $curDate = date('d-m-Y', $curDate);
        $curDate = strtotime($curDate);
        $clStatus = $this->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status' => 'CL'
            ),
            'fields' => array('id')
        ));
        $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
        $filter_color = $this->HistoryFilter->find('first', array('recursive' => -1, 'conditions' => array('employee_id' => $employee_id, 'path' => 'vision_task_filter_color')));
        $filter_color = !empty($filter_color['HistoryFilter']['params']) ? $filter_color['HistoryFilter']['params'] : '';
        foreach ($listTaskExport as $key => $value) {
            $dx = $value['ProjectTask'];
            $taskId = $dx['id'];
            // lay program
            if (!empty($listProjectIdOfTasks[$taskId])) {
                $datas[$taskId]['project_name'] = $listProjecNames[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['task_title'] = $dx['task_title'];
                $datas[$taskId]['assigned'] = !empty($nameListAssign[$taskId]) ? $nameListAssign[$taskId] : '';
                $datas[$taskId]['start_date'] = $str_utility->convertToVNDate($dx['task_start_date']);
                $datas[$taskId]['end_date'] = $str_utility->convertToVNDate($dx['task_end_date']);
                $datas[$taskId]['workload'] = $workloadOfTasks[$taskId];
                $datas[$taskId]['project_id'] = $listProjectIdOfTasks[$taskId];
                $datas[$taskId]['code_project_1'] = $listCodeProject[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['code_project_2'] = $listCodeProject1[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['initial_estimated'] = $dx['initial_estimated'];
                $datas[$taskId]['initial_task_start_date'] = $dx['initial_task_start_date'];
                $datas[$taskId]['initial_task_end_date'] = $dx['initial_task_end_date'];
                $datas[$taskId]['duration'] = $dx['duration'];
                $datas[$taskId]['overload'] = $dx['overload'];
                $datas[$taskId]['amount'] = $dx['amount'];
                $datas[$taskId]['progress_order'] = $dx['progress_order'];

                if (!empty($listActivityTasks[$taskId])) {
                    $datas[$taskId]['consume'] = (!empty($consumeOfActivityTasks[$listActivityTasks[$taskId]]) ) ? round($consumeOfActivityTasks[$listActivityTasks[$taskId]], 2) : 0;
                } else {
                    $datas[$taskId]['consume'] = 0.00;
                }
                if (!empty($listActivityTasks[$taskId])) {
                    $datas[$taskId]['in_used'] = (!empty($inUsedOfActivityTasks[$listActivityTasks[$taskId]]) ) ? $inUsedOfActivityTasks[$listActivityTasks[$taskId]] : 0;
                } else {
                    $datas[$taskId]['in_used'] = 0;
                }
                // completed
                if ($datas[$taskId]['consume'] == 0 || $datas[$taskId]['workload'] == 0) {
                    $datas[$taskId]['completed'] = 0;
                } else {
                    $_cal = round($datas[$taskId]['consume'] / ($datas[$taskId]['workload'] + $datas[$taskId]['overload']) * 100, 2);
                    $datas[$taskId]['completed'] = ($_cal > 100) ? 100 : $_cal;
                }
                //remain
                $datas[$taskId]['remain'] = round($datas[$taskId]['workload'] - $datas[$taskId]['consume'] + $datas[$taskId]['overload'], 2);
                $datas[$taskId]['text'] = $dx['text_1'];

                if ($this->params['action'] == 'tasks_vision' || $this->params['action'] == 'tasks_vision_new') {
                    $datas[$taskId]['part_name'] = !empty($listProjectPartIds[$dx['project_planed_phase_id']]) ? $listProjectPartIds[$dx['project_planed_phase_id']] : '';
                    $datas[$taskId]['phase_name'] = !empty($listPhaseIds[$dx['project_planed_phase_id']]) ? $listPhaseIds[$dx['project_planed_phase_id']] : '';
                    $datas[$taskId]['status'] = !empty($dx['task_status_id']) ? $dx['task_status_id'] : '';
                    $datas[$taskId]['milestone'] = !empty($dx['milestone_id']) ? $dx['milestone_id'] : '';
                    $datas[$taskId]['priority'] = !empty($dx['task_priority_id']) ? $dx['task_priority_id'] : '';
                    $datas[$taskId]['amr_program'] = !empty($listProjectProgramIds[$listProjectIdOfTasks[$taskId]]) ? $listProjectProgramIds[$listProjectIdOfTasks[$taskId]] : '';
                    $datas[$taskId]['sub_amr_program'] = !empty($listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]) ? $listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]] : '';
                    $sDate = strtotime($dx['task_start_date']);
                    $eDate = strtotime($dx['task_end_date']);
                    if ($filter_color == 'green') {
                        if (($datas[$taskId]['consume'] == 0 && $sDate < $curDate && $datas[$taskId]['workload'] > 0 && $datas[$taskId]['status'] != $clStatus) || (!empty($datas[$taskId]['status']) && $datas[$taskId]['status'] != $clStatus && $eDate < $curDate) || ($datas[$taskId]['workload'] < $datas[$taskId]['consume'])) {
                            unset($datas[$taskId]);
                        }
                    } elseif ($filter_color == 'red') {
                        if (($datas[$taskId]['consume'] == 0 && $sDate < $curDate && $datas[$taskId]['workload'] > 0 && $datas[$taskId]['status'] != $clStatus) || (!empty($datas[$taskId]['status']) && $datas[$taskId]['status'] != $clStatus && $eDate < $curDate) || ($datas[$taskId]['workload'] < $datas[$taskId]['consume'])) {
                            //do nothing
                        } else {
                            unset($datas[$taskId]);
                        }
                    }
                } else {
                    if (!empty($listProjectPartIds[$dx['project_planed_phase_id']])) {
                        $datas[$taskId]['part_name'] = !empty($listPartNames[$listProjectPartIds[$dx['project_planed_phase_id']]]) ? $listPartNames[$listProjectPartIds[$dx['project_planed_phase_id']]] : '';
                    }
                    if (!empty($listPhaseIds[$dx['project_planed_phase_id']])) {
                        $datas[$taskId]['phase_name'] = !empty($listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]]) ? $listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]] : '';
                    }
                    $datas[$taskId]['status'] = !empty($listStatus[$dx['task_status_id']]) ? $listStatus[$dx['task_status_id']] : '';
                    $datas[$taskId]['milestone'] = !empty($dx['milestone_id']) && !empty($listMilestone[$dx['milestone_id']]) ? $listMilestone[$dx['milestone_id']] : '';
                    $datas[$taskId]['priority'] = !empty($listPriority[$dx['task_priority_id']]) ? $listPriority[$dx['task_priority_id']] : '';
                    $datas[$taskId]['amr_program'] = (!empty($projectProgram[$listProjectProgramIds[$listProjectIdOfTasks[$taskId]]]) || !empty($listProjectProgramIds[$listProjectIdOfTasks[$taskId]]) ) ? $projectProgram[$listProjectProgramIds[$listProjectIdOfTasks[$taskId]]] : '';
                    $datas[$taskId]['sub_amr_program'] = (!empty($projectSubProgram[$listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]]) || !empty($listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]])) ? $projectSubProgram[$listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]] : '';
                }
            }
        }
        // debug($listIdModifyByPm); exit;
        $this->set(compact('listStatus', 'listPriority', 'projectProgram', 'projectSubProgram', 'listIdModifyByPm', 'roleLogin', 'listPartNames', 'listPhaseNames', 'filter_color', 'listMilestone', '_milestoneColor'));
        //permission edit text of task.
        $this->loadModels('ProjectEmployeeManager', 'ProjectTaskTxt', 'Employee');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_unique($listProjectIdOfTasks),
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            )
        ));
        $listEmployeeManagerOfT = array();
        $listEmployeeManager = !empty($listEmployeeManager) ? Set::combine($listEmployeeManager, '{n}.ProjectEmployeeManager.project_id', '{n}.ProjectEmployeeManager.project_id') : array();
        foreach ($listProjectIdOfTasks as $key => $value) {
            if (!empty($listEmployeeManager[$value]))
                $listEmployeeManagerOfT[$key] = $key;
        }
        $fullName = $this->employee_info['Employee']['fullname'];
        $employee_id = $this->employee_info['Employee']['id'];
        // ***************
        $listIdAva = $this->ProjectTaskTxt->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_task_id' => array_keys($listProjectIdOfTasks)
            ),
            'fields' => array('project_task_id', 'employee_id'),
            'order' => array('created' => 'DESC')
        ));
        $listIdAva = !empty($listIdAva) ? Set::combine($listIdAva, '{n}', '{n}.ProjectTaskTxt.employee_id') : array();
        $listEmployeeId = array();
        foreach ($listIdAva as $key => $value) {
            $listEmployeeId[] = $value;
        }

        $getEmpoyee = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'Employee.id' => $listEmployeeId
            ),
            'joins' => array(
                array(
                    'table' => 'project_task_txts',
                    'alias' => 'ProjectTaskTxt',
                    'conditions' => array(
                        'Employee.id = ProjectTaskTxt.employee_id'
                    ),
                    'type' => 'left'
                )
            ),
            'fields' => array('ProjectTaskTxt.project_task_id', 'Employee.first_name', 'Employee.last_name', 'Employee.fullname'),
        ));
        $getEmpoyee = !empty($getEmpoyee) ? Set::combine($getEmpoyee, '{n}.ProjectTaskTxt.project_task_id', '{n}.Employee') : array();

        $checkAvata = $this->checkAvatar();

        $this->set(compact('listEmployeeManagerOfT', 'fullName', 'listIdAva', 'employee_id', 'listEditStatus', 'getEmpoyee', 'listEmployeeRefer', 'listAssign', 'checkAvata'));
        return $datas;
    }

    public function update_vision_task() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectTaskTxt');
        if (!empty($this->data)) {
            $data = array(
                'task_status_id' => !empty($this->data['Status']) ? $this->data['Status'] : '',
                'text_1' => !empty($this->data['Text']) ? $this->data['Text'] : ''
            );
            if (!empty($data['text_1']) && !empty($this->data['id'])) {
                $this->ProjectTaskTxt->create();
                $this->ProjectTaskTxt->save(array(
                    'project_task_id' => $this->data['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'comment' => $data['text_1']
                ));
            }
            if (!empty($this->data['id'])) {
                $this->ProjectTask->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->ProjectTask->save($data)) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->ProjectTask->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function export() {
        if (!empty($this->data)) {
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Task Visions')  //auto translate
                    ->save($data, 'task_vision_{date}.xls');
        }
        die;
    }

    public function updateWeather() {
        if (!empty($this->data)) {
			$this->_checkRole(true, $this->data['project_id']);
            $key = explode('.', $this->data['key']);
            $this->loadModel('ProjectAmr');
            $find = $this->ProjectAmr->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $this->data['project_id']),
                'fields' => array('id')
            ));
            $id = !empty($find) ? $find['ProjectAmr']['id'] : 0;
            $this->ProjectAmr->id = $id;
            $this->ProjectAmr->save(array(
                'project_id' => $this->data['project_id'],
                $key[1] => $this->data['val']
            ));
        }
        die;
    }

    // get data for your form (your_form, your_form_1, your_form_2, your_form_3, your_form_4).
    public function getDataForYourForm($page, $curentPage, $id) {
        $this->loadModel('ProjectEmployeeManager');
        // Check the role
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $employee_id = $employeeInfo['Employee']['id'];
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if (!empty($employeeInfo['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employeeInfo['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName'));
        $checkReadAccess = $this->ProjectEmployeeManager->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA',
                'project_manager_id' => $employee_id
            )
        ));
        $this->set(compact('checkReadAccess'));
        $changeProjectManager = !(empty($employeeInfo['Role']) || $employeeInfo['Role']['name'] == 'conslt' || $employeeInfo['Role']['name'] == 'pm');
        if ($employeeInfo['Role']['name'] == 'pm') {
            $checkIsManager = $this->Project->find('first', array(
                'conditions' => array(
                    'Project.id' => $id,
                    'Project.project_manager_id' => $employee_id
                )
            ));
            $changeProjectManager = !empty($checkIsManager) ? true : false;
        }

        if ($employeeInfo) {
            $fullname = $employeeInfo['Employee']['first_name'] . " " . $employeeInfo['Employee']['last_name'];
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }

        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;

        $company_idd = $company_id_of_project['Project']['company_id'];
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->redirect(array('action' => 'index'));
        }
        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        // security
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $company_id = $this->Project->find("first", array(
            'recursive' => 0,
            "fields" => array("Project.company_id", 'Company.company_name'),
            'conditions' => array('Project.id' => $id)));

        $name_company = $company_id['Company']['company_name'];
        $company_id = $company_id['Project']['company_id'];
        $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectAmrProgram.company_id ' => $company_id
            ),
            'fields' => array('id', 'family_id', 'sub_family_id')
        ));
        $famAndSubFamOfPrograms = !empty($famAndSubFamOfPrograms) ? Set::combine($famAndSubFamOfPrograms, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram') : array();
        $subFams = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => array_keys($famAndSubFamOfPrograms)),
            'fields' => array('id', 'sub_family_id')
        ));
        /**
         * Lay tat ca cac phase cua 1 project
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        // lay list multi select.
        $_ProjectMultiLists = $this->ProjectListMultiple->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id
            ),
            'fields' => array('id', 'project_dataset_id', 'key')
        ));
        $ProjectMultiLists = array();
        if (!empty($_ProjectMultiLists)) {
            foreach ($_ProjectMultiLists as $_ProjectMultiList) {
                $dx = $_ProjectMultiList['ProjectListMultiple'];
                $ProjectMultiLists[$dx['key']][$dx['id']] = $dx['project_dataset_id'];
            }
        }
        $this->set('ProjectMultiLists', $ProjectMultiLists);
        // get image for projects.
        $images = $this->ProjectImage->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'company_id' => $company_id,
                'is_file' => 1
            )
        ));
        $this->set('images', $images);
        // lay project document upload
        $projectFiles = $this->ProjectFile->find('all', array(
            'recurisve' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'key', 'file_attachment', 'size', 'project_id', 'type')
        ));
        $projectFiles = !empty($projectFiles) ? Set::combine($projectFiles, '{n}.ProjectFile.id', '{n}.ProjectFile', '{n}.ProjectFile.key') : array();
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->set(compact('adminSeeAllProjects'));
        $this->set('projectFiles', $projectFiles);
        if (!empty($this->data)) {
            if ($this->data['Project']['category'] != 2) {
                $this->data['Project']['is_staffing'] = 0;
            }
            if (!empty($this->data['Project']['tmp_activity']) || $this->data['Project']['tmp_activity'] == 0) {
                if ($this->data['Project']['tmp_activity'] == 0) {
                    $this->data['Project']['activity_id'] = null;
                } else {
                    $this->data['Project']['activity_id'] = $this->data['Project']['tmp_activity'];
                }
                unset($this->data['Project']['tmp_activity']);
            }
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /*
             * Xu li functional leader
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /*
             * Xu li uat manager
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            for ($i = 1; $i <= 16; $i++) {
                $key = 'price_' . $i;
                $this->data['Project'][$key] = str_replace(" ", "", $this->data['Project'][$key]);
            }
            // save list multi select.
            for ($i = 1; $i <= 10; $i++) {
                $key = 'project_list_multi_' . $i;
                if (!empty($this->data[$key])) {
                    $oldList = array();
                    foreach ($this->data[$key] as $listId) {
                        if (!empty($listId) && $listId != 0) {
                            if (!empty($ProjectMultiLists[$key]) && in_array($listId, $ProjectMultiLists[$key])) { // kiem tra xem no da co ton tai chua. neu chua thi tao moi
                                $oldList[] = $listId;
                            } else {
                                $this->ProjectListMultiple->create();
                                $this->ProjectListMultiple->save(array(
                                    'project_id' => $id,
                                    'project_dataset_id' => $listId,
                                    'key' => $key
                                ));
                            }
                        }
                    }
                    if (!empty($ProjectMultiLists[$key])) {
                        foreach ($ProjectMultiLists[$key] as $_id => $listId) {
                            if (in_array($listId, $oldList)) { // kiem tra xem list moi co khong? neu list cu co ma list moi khong co thi delete.
                                //do nothing
                            } else {
                                $this->ProjectListMultiple->delete($_id);
                            }
                        }
                        unset($this->data[$key]);
                    }
                }
            }

            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            // $countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->data = $this->Project->read(null, $id);
            //     $this->Session->setFlash(__('Please select at least a project manager without the flag backup.', true), 'error');
            // } else {
            $activityLinked = !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0;
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // }
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }

            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);

            $data_project_teams = $this->params['form'];
            $project_id = $this->data["Project"]["id"];

            if (!empty($this->data['Project']['project_amr_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            }
            if (!empty($this->data['Project']['project_amr_sub_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            }
            $checkBackup = $this->ProjectEmployeeManager->find('count', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id, 'project_manager_id' => $employeeInfo['Employee']['id'], 'type' => 'PM')
            ));
            if (!$changeProjectManager && $employeeInfo['Role']['name'] != 'admin') {
                if (!empty($checkBackup)) {
                    //do nothing
                } else {
                    $Model4 = ClassRegistry::init('Employee');
                    $employInfors = $Model4->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
                        'fields' => array('update_your_form')
                    ));
                    if (!empty($employInfors) && !empty($employInfors['Employee']['update_your_form'])) {
                        // thang nay dc chinh sua tat ca projet...ke me no
                    } else {
                        $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
                    }
                }
            }
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            if (!empty($this->data['ProjectAmr']['weather'][0])) {
                $data_arm['ProjectAmr']['weather'] = $this->data['ProjectAmr']['weather'][0];
            }
            if (!empty($this->data['ProjectAmr']['rank'][0])) {
                $data_arm['ProjectAmr']['rank'] = $this->data['ProjectAmr']['rank'][0];
            }
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $oldProject = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $this->data['Project']['id']),
                'fields' => array('activity_id')
            ));
            if (!empty($oldProject['Project']['activity_id'])) {
                //da linked roi ko lam gi ca
            } else {
                $this->data['Project']['activity_id'] = $activityLinked;
            }
            if (!empty($this->data['Project']['activity_id'])) {
                //bat dau linked
            } else {
                unset($this->data['Project']['activity_id']);
            }
            if ($activityLinked == -1) {
                $this->data['Project']['activity_id'] = null;
            }
            $this->data['Project']['last_modified'] = time();
            $range = range(1, 14);
            foreach ($range as $num) {
                if (isset($this->data['Project']['date_' . $num])) {
                    $this->data['Project']['date_' . $num] = $str_utility->convertToSQLDate($this->data['Project']['date_' . $num]);
                }
            }
            $this->loadModel('Activity');
            // rename activity linked
            $oldProjectName = $this->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name'),
                'conditions' => array('Project.id' => $id)));
            $oldProjectName = !empty($oldProjectName) ? $oldProjectName['Project']['project_name'] : '';
            $newProjectName = !empty($this->data) && !empty($this->data['Project']['project_name']) ? $this->data['Project']['project_name'] : '';
            if ($oldProjectName != $newProjectName) {
                $ActivityLinkedName = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id', 'name', 'long_name', 'short_name'),
                    'conditions' => array(
                        'project' => $id
                    )
                ));
                if (!empty($ActivityLinkedName)) {
                    $saved['name'] = $newProjectName;
                    $idActivityLinked = $ActivityLinkedName['Activity']['id'];
                    $this->Activity->id = $idActivityLinked;
                    $this->Activity->save($saved);
                }
            }
            // end
            // truong hop pm co quyen change status nhung khong the update your form.
            if ($employeeInfo['Role']['name'] == 'pm' && $employeeInfo['Employee']['change_status_project'] == 1 && $employeeInfo['Employee']['update_your_form'] == 0) {
                unset($this->data['Project']['project_manager_id']);
                unset($this->data['Project']['chief_business_id']);
                unset($this->data['Project']['technical_manager_id']);
                unset($this->data['Project']['functional_leader_id']);
                unset($this->data['Project']['uat_manager_id']);
            }
            if ($this->Project->save($this->data)) {
                $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update project `%s` via Details', $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $id = $this->Project->id;
                if ($activityLinked == -1) {
                    $activityLinked = 0;
                }
                if (!empty($activityLinked) && $activityLinked != 0 && isset($this->data['Project']['activated'])) {
                    $this->Activity->id = $activityLinked;
                    $idProgram = !empty($this->data['Project']['project_amr_program_id']) ? $this->data['Project']['project_amr_program_id'] : '';
                    $idSubProgram = !empty($this->data['Project']['project_amr_sub_program_id']) ? $this->data['Project']['project_amr_sub_program_id'] : '';
                    $fam = !empty($famAndSubFamOfPrograms[$idProgram]['family_id']) ? $famAndSubFamOfPrograms[$idProgram]['family_id'] : '';
                    $sfam = !empty($subFams[$idSubProgram]) ? $subFams[$idSubProgram] : '';
                    if (!empty($this->data['Project']['category']) && $this->data['Project']['category'] == 1 && $activate_family_linked_program) {
                        if (!empty($fam)) {
                            $this->Activity->save(array(
                                'project_manager_id' => $this->data['Project']['project_manager_id'],
                                'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                                'activated' => $this->data['Project']['activated'],
                                'family_id' => $fam,
                                'subfamily_id' => $sfam
                            ));
                        }
                    } else {
                        $this->Activity->save(array(
                            'project_manager_id' => $this->data['Project']['project_manager_id'],
                            'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                            'activated' => $this->data['Project']['activated']
                        ));
                    }
                }
                $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
                $this->ProjectAmr->id = $ProjectArms['ProjectAmr']['id'];
                $this->ProjectAmr->save($data_arm);
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                //link project - activity
                if (!empty($this->data['Project']['activity_id'])) {
                    // update tmp
                    $this->loadModel('TmpStaffingSystem');
                    $this->TmpStaffingSystem->updateAll(
                            array('TmpStaffingSystem.activity_id' => $this->data['Project']['activity_id']), array('TmpStaffingSystem.project_id' => $id)
                    );
                    // update project budget sync
                    $this->loadModel('ProjectBudgetSyn');
                    $this->ProjectBudgetSyn->updateAll(
                            array('ProjectBudgetSyn.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetSyn.project_id' => $id)
                    );
                    // update project provisional
                    $this->loadModel('ProjectBudgetProvisional');
                    $this->ProjectBudgetProvisional->updateAll(
                            array('ProjectBudgetProvisional.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetProvisional.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlus');
                    $this->ProjectFinancePlus->updateAll(
                            array('ProjectFinancePlus.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlus.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlusDetail');
                    $this->ProjectFinancePlusDetail->updateAll(
                            array('ProjectFinancePlusDetail.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlusDetail.project_id' => $id)
                    );
                    $this->loadModel('ActivityTask');
                    $this->loadModel('ProjectStatus');
                    $activityIdTask = $this->data['Project']['activity_id'];
                    if (isset($activityIdTask) && !empty($activityIdTask) && $activityIdTask != '' && $activityIdTask != null) {
                        $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.activity_id' => $activityIdTask)));
                        $projectStatus = $this->ProjectStatus->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('ProjectStatus.status' => 'IP', 'company_id' => $company_id),
                            'fields' => array('id')
                        ));
                        $projectStatus = $projectStatus['ProjectStatus']['id'];
                        $projectTasks = $this->Project->ProjectTask->find('all', array('recursive' => -1,
                            'conditions' => array('ProjectTask.project_id' => $id,
                                'ProjectTask.task_status_id' => $projectStatus)));

                        $listProjectPhase = ClassRegistry::init('ProjectPhase')->find('list', array('recursive' => -1, 'conditions' => array('company_id' => $company_id, 'ProjectPhase.activated' => 1)));
                        $listPlan = ClassRegistry::init('ProjectPhasePlan')->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('project_id' => $id),
                            'fields' => array('id', 'project_planed_phase_id')
                        ));
                        $activity = ClassRegistry::init('Activity')->find('first', array('recursive' => -1,
                            'conditions' => array('Activity.id' => $this->data['Project']['activity_id']),
                            'fields' => array('id', 'project')));

                        $this->loadModel('Activity');

                        if (!empty($activity)) {
                            $activities = ClassRegistry::init('Activity')->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('Activity.project' => $id),
                                'fields' => array('id', 'project')
                            ));
                            if (!empty($activities)) {
                                foreach ($activities as $act) {
                                    $this->Activity->id = $act['Activity']['id'];
                                    $this->Activity->saveField('project', null);
                                }
                            }
                            $this->Activity->id = $activity['Activity']['id'];
                            $this->Activity->saveField('project', $id);
                        }

                        foreach ($projectTasks as $projectTask) {
                            $project_task_id[] = $projectTask['ProjectTask']['id'];
                        }
                        if (!empty($project_task_id)) {
                            $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.project_task_id' => $project_task_id)));
                        }
                        foreach ($activityTasks as $activityTask) {
                            $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                            $this->ActivityTask->delete();
                        }
                        $dataActivityTask = array();
                        foreach ($projectTasks as $projectTask) {
                            $idProjectPhase = $projectTask['ProjectTask']['project_planed_phase_id'];
                            $_name = isset($idProjectPhase) ? $listPlan[$idProjectPhase] : '';
                            $_name = !empty($_name) ? $listProjectPhase[$_name] : '';
                            $nameProjectTask = $projectTask['ProjectTask']['task_title'];
                            $this->ActivityTask->create();
                            $dataActivityTask['ActivityTask']['name'] = $_name . '/ ' . $nameProjectTask;
                            $dataActivityTask['ActivityTask']['activity_id'] = $activityIdTask;
                            $dataActivityTask['ActivityTask']['project_task_id'] = $projectTask['ProjectTask']['id'];
                            $this->ActivityTask->save($dataActivityTask['ActivityTask']);
                        }
                    }
                }
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                $listEmployeeRefers = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id
                    ),
                    'fields' => array('id', 'id', 'type'),
                    'group' => array('type', 'id')
                ));

                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'PM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['PM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                // 'is_backup' => 0,
                                'project_id' => $id,
                                'type' => 'RA',
                                'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0,
                                'is_profit_center' => $is_profit
                            );
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['RA'][$lastEmployRefers]);
                            }
                        }
                    }
                    if (!empty($listEmployeeRefers['RA'])) {
                        $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['RA']), false);
                    }
                    unset($listEmployeeRefers['RA']);
                }
                if (!empty($listEmployeeRefers['PM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['PM']), false);
                }
                unset($listEmployeeRefers['PM']);

                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'CB',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['CB'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['CB'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['CB']), false);
                }
                unset($listEmployeeRefers['CB']);


                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'TM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['TM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['TM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['TM']), false);
                }
                unset($listEmployeeRefers['TM']);

                //functional leader & uat manager
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'FL',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['FL'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['FL'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['FL']), false);
                }
                unset($listEmployeeRefers['FL']);
                //uat manager
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'UM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['UM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['UM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['UM']), false);
                }
                unset($listEmployeeRefers['UM']);

                $oldPhase = array();
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            if (in_array($phaseId, $phasePlans)) { // ton tai trong phase plan of project
                                $oldPhase[] = $phaseId;
                            } else {
                                $saved = array(
                                    'project_id' => $id,
                                    'project_phase_id' => $phaseId
                                );
                                $this->ProjectPhaseCurrent->create();
                                $this->ProjectPhaseCurrent->save($saved);
                            }
                        }
                    }
                }
                foreach ($phasePlans as $idPlan => $phaseId) {
                    if (in_array($phaseId, $oldPhase)) {
                        //do nothing
                    } else {
                        $this->ProjectPhaseCurrent->delete($idPlan);
                    }
                }
                $this->Session->setFlash(__('Saved', true));
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect("/projects/" . $curentPage . "/" . $project_id);
        }
        // }
        if (empty($this->data)) {
            $this->data = $this->Project->read(null, $id);
        }
        $IdTask = $this->data['Project']['activity_id'];
        $this->set('famAndSubFamOfPrograms', $famAndSubFamOfPrograms);
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1), 'order' => 'ProjectComplexity.name ASC')));
        $this->set('Priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id ' => $company_id))));
        $this->set('Statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id ' => $company_id))));
        $this->set('ProjectTypes', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id ' => $company_id, 'ProjectType.display' => 1))));
        $this->set('ProjectSubTypes', $this->ProjectSubType->find('list', array('fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 'conditions' => array('ProjectSubType.project_type_id ' => $this->data['Project']['project_type_id'], 'ProjectSubType.display' => 1))));
		$projectSubSubTypes = array();
		if(!empty($this->data['Project']['project_sub_type_id'])){
			$projectSubSubTypes = $this->ProjectSubType->find('list', array(
			'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 
			'conditions' => array(
				'parent_id ' => $this->data['Project']['project_sub_type_id'], 
				'display' => 1,
				)
			));
		}
        $this->set(compact('projectSubSubTypes'));
        $this->set('ProjectArmPrograms', $this->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id ' => $company_id))));
        $this->set('ProjectArmSubPrograms', $this->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program'), 'conditions' => array('ProjectAmrSubProgram.project_amr_program_id ' => $this->data['Project']['project_amr_program_id']))));
        $this->set('ProjectActivities', ClassRegistry::init('Activity')->find('list', array('fields' => array('Activity.id', 'Activity.name'), 'conditions' => array('Activity.company_id ' => $company_id, 'OR' => array('Activity.pms' => 1, 'NOT' => array('Activity.project' => null))))));
        $this->set('listIdActivity', $this->Project->find('list', array('recursive' => -1, 'fields' => array('Project.activity_id'), 'conditions' => array('Project.company_id' => $company_id))));

        // Find Task and Activities in request then block the combo box
        $find_tasks_in_activity = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask)));
        $find_tasks_in_activity = Set::combine($find_tasks_in_activity, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $find_tasks_in_activity = array_keys($find_tasks_in_activity);
        // Check if the project is linked to any activity or not
        if (isset($IdTask)) {
            if (!empty($find_tasks_in_activity)) {
                $conditions = array('task_id' => $find_tasks_in_activity);
                $activityRequest = ClassRegistry::init('ActivityRequest')->find(
                        'count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "OR" => array(
                            'activity_id' => $IdTask,
                            $conditions
                        )
                    )
                        )
                );
            } else {
                $activityRequest = 0;
            }
        } else {
            $activityRequest = 0;
        }
        $activityRequest = json_encode($activityRequest);
        // Check activities and tasks, if assigned then lock the combo box
        $this->set('activityRequest', $activityRequest);

        $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
		$curentDate = date('Y-m-d');
		$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
        $projectEmployees = $this->ProjectTask->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees),
				'Employee.last_name NOT' => 'NULL'
            ),
            'fields' => array('first_name', 'last_name', 'id', 'avatar_resize','actif', 'available')
			)
		);
		
        $_employees = $avatarOfEmploys = array();
        foreach ($projectEmployees as $projectEmployee) {
            $_employees['project'][$projectEmployee['Employee']['id']]['full_name'] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
			$em_actif = intval($projectEmployee['Employee']['actif']) ? intval($projectEmployee['Employee']['available']) : 0;
			$_employees['project'][$projectEmployee['Employee']['id']]['actif'] = $em_actif;
            foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $_employees[$key][$projectEmployee['Employee']['id']] = $_employees['project'][$projectEmployee['Employee']['id']];
                }
            }
            $avatarOfEmploys[$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['avatar_resize'];
        }
        $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_idd)));

        // edit get start end date from Phase
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) as MinStartDate',
                'MAX(phase_real_end_date) as MaxEndDate'
            )
        ));
        if (!empty($projectPhasePlans)) {
            $_datas['start_date'] = $projectPhasePlans[0][0]['MinStartDate'];
            $_datas['end_date'] = $projectPhasePlans[0][0]['MaxEndDate'];
            $this->Project->id = $id;
            $this->Project->save($_datas);
        }
        $this->set(compact('projectPhasePlans'));
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $listEmployeeManagers = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('type' => 'RA')
            ),
            'fields' => array('project_manager_id', 'is_backup', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
       
        // debug($listEmployeeManagers);
        // exit;
        $listReadAccess = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA'
            ),
            'fields' => array('project_manager_id', 'is_profit_center', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listEmployeeManagers['RA'] = !empty($listReadAccess) && !empty($listReadAccess['RA']) ? $listReadAccess['RA'] : array();

        if (!empty($this->data['Project']['project_manager_id'])) {
            $listEmployeeManagers['PM'][$this->data['Project']['project_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['chief_business_id'])) {
            $listEmployeeManagers['CB'][$this->data['Project']['chief_business_id']] = 0;
        }
        if (!empty($this->data['Project']['technical_manager_id'])) {
            $listEmployeeManagers['TM'][$this->data['Project']['technical_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['functional_leader_id'])) {
            $listEmployeeManagers['FL'][$this->data['Project']['functional_leader_id']] = 0;
        }
        if (!empty($this->data['Project']['uat_manager_id'])) {
            $listEmployeeManagers['UM'][$this->data['Project']['uat_manager_id']] = 0;
        }
        $employBackups = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => array_keys($listEmployeeManagers)),
            'fields' => array('id', 'first_name', 'last_name')
        ));
        /**
         * Lay cac phase co task
         */
        $phaseHaveTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('project_planed_phase_id', 'project_planed_phase_id')
        ));
        /**
         * Viet lai lan 2, get lai phase sau khi thuc hien save
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        /**
         * Change date 07/04/2014.
         * Get all family + sub family of company
         */
        $families = $subFamilies = array();
        $haveActivity = 'false';
        $this->loadModel('Family');
        $_families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields' => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeInfo['Company']['id'])));
        if (!empty($_families)) {
            foreach ($_families as $_family) {
                $dx = $_family['Family'];
                if (!empty($dx['parent_id'])) {
                    //sub family
                } else {
                    $families[$dx['id']] = $dx['name'];
                }
            }
            if (!empty($families)) {
                $this->loadModel('ActivityFamily');
                $first_family =  array_keys($families);
				$first_family =  $first_family[0];
                $subFamilies = $this->ActivityFamily->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => $first_family
                    ),
                    'fields' => array('id', 'name'),
                    'order' => array('name')
                ));
            }
        }
        if ($employeeInfo['Role']['name'] === 'admin' || $employeeInfo['Role']['name'] === 'pm') {
            /**
             * Kiem tra xem activity da co timesheet nao chua
             */
            if (!empty($this->data['Project']['activity_id']) || $this->data['Project']['activity_id'] != 0) {
                $this->loadModel('ActivityRequest');
                $this->loadModel('ActivityTask');
                $tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $this->data['Project']['activity_id']),
                    'fields' => array('id', 'id')
                ));
                if (!empty($tasks)) {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'OR' => array(
                                'activity_id' => $this->data['Project']['activity_id'],
                                'task_id' => $tasks
                            )
                        )
                    ));
                } else {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $this->data['Project']['activity_id'])
                    ));
                }
                if ($request != 0) {
                    $haveActivity = 'true';
                }
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeInfo['Company']['id']),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('phaseHaveTasks', 'phasePlans', 'avatarOfEmploys', 'listEmployeeManagers', 'employBackups', 'employeeInfo', 'families', 'subFamilies', 'haveActivity', 'budgetCustomers'));
        $this->set(compact('project_name', '_employees', 'currency_name', 'name_company', 'changeProjectManager', 'activate_family_linked_program'));
        $this->set('project_id', $id);
        $this->loadModels('Translation', 'ProjectDetailEmployeeSetting');
        // $page = 'Details_5';
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => $page,
                'TranslationSetting.company_id' => $company_id
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
		$employee_setting_details = array();
		if( !empty( $this->companyConfigs['manage_project_setting'])){
			$translate_setting_ids = !empty( $translation_data) ? Set::classicExtract( $translation_data, '{n}.TranslationSetting.id') : array();
			$employee_setting_details = $this->ProjectDetailEmployeeSetting->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'translation_setting_id' => $translate_setting_ids,
					'employee_id' => $this->employee_info['Employee']['id']
				),
				'fields' => '*',
			));
			$employee_setting_details = !empty($employee_setting_details) ? Set::combine($employee_setting_details, '{n}.ProjectDetailEmployeeSetting.translation_setting_id', '{n}.ProjectDetailEmployeeSetting') : array();
		}
		// Get fields your form in top section
        // All fields in top section is define in Translation setting admin / Detail_5  
        $translation_data_box_1 = array();

        $translation_data_box_1 = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => 'Details_5',
                'TranslationSetting.company_id' => $company_id,
				'Translation.field' => array('project_name', 'project_manager_id','project_amr_program_id')
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        // }
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        // lay milestone for next milestone.
        $nextMilestone = $this->ProjectMilestone->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'milestone_date >' => date('Y-m-d', time())
            ),
            'fields' => array('id', 'milestone_date'),
            'order' => array('milestone_date' => 'ASC')
        ));
        $nextMilestoneByDay = $nextMilestoneByWeek = 0;
        if (!empty($nextMilestone)) {
            $nextMilestone = $nextMilestone['ProjectMilestone']['milestone_date'];
            $nextMilestone = strtotime($nextMilestone);
            $now = time();
            if ($now < $nextMilestone) {
                $diff = ($nextMilestone - $now) / (24 * 60 * 60) + 1;
                $nextMilestoneByDay = (int) ($diff);
                $nextMilestoneByWeek = (int) ($diff / 7);
            }
        }
        $profitCenters = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));

        // debug($translation_data); exit;
        $this->set(compact('nextMilestoneByDay', 'nextMilestoneByWeek', 'profitCenters', 'translation_data_box_1'));
        $this->set(compact('translation_data', 'employee_setting_details', 'datasets'));
    }

    // your_form_1
    function your_form_1($id = null) {
        $page = 'Details_1';
        $curentPage = 'your_form_1';
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_1');
        $this->set('page', $page);
        $this->render('your_form');
    }

    // your_form_2
    function your_form_2($id = null) {
        $page = 'Details_2';
        $curentPage = 'your_form_2';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $this->_checkWriteProfile('your_form_2');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    // your_form_3
    function your_form_3($id = null) {
        $page = 'Details_3';
        $curentPage = 'your_form_3';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_3');
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    // your_form_4
    function your_form_4($id = null) {
        $page = 'Details_4';
        $curentPage = 'your_form_4';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_4');
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    public function saveFilterSortVisionTask() {
        if (!empty($_POST)) {
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModel('HistoryFilter');
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'path' => 'vision_task_filter_color'
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $_POST['which']));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'params' => $_POST['which'],
                    'path' => 'vision_task_filter_color',
                    'employee_id' => $employee_id
                ));
            }
            die(1);
        }
        echo 'Error';
        exit;
    }

    function tasks_vision_new($style = 1) {
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModel('ProfitCenterManagerBackup');
        $manager = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));
        $manager_backup = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            ),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
        $profit_center_id = $this->employee_info['Employee']['profit_center_id'];
        $profit_center_id = !empty($list_profit_center_managers) ? $list_profit_center_managers : $profit_center_id;
        $list = array();
        $list_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status !=' => 'CL',
                'display' => 1
            ),
            'fields' => array('id', 'id')
        ));
        switch ($style) {
            case '1':
            case '2':
            case '3':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'assignedResources' => array(
                        '0' => $employee_id
                    ),
                    'nameStatusTask' => $list_status
                );
                break;
            case '4':
            case '5':
            case '6':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'manager' => 1
                );
                break;
            case '7':
            case '8':
            case '9':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'assignedTeam' => $profit_center_id
                );
                break;
        }
        $datas = $this->getDataForVisionTasks($list);
        $curDate = time();
        if (in_array($style, array(2, 5, 8))) {
            foreach ($datas as $key => $data) {
                if (!empty($data['end_date']) && strtotime($data['end_date']) <= $curDate && (in_array($data['status'], $list_status) || empty($data['status']))) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        } else if (in_array($style, array(3, 6, 9))) {
            foreach ($datas as $key => $data) {
                if ($data['overload'] > 0 && in_array($data['status'], $list_status)) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        }
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $fieldName = array('name', 'english');
        } else {
            $fieldName = array('name', 'france');
        }
        $this->loadModel('VisionTaskExport');
        $fieldset = $this->VisionTaskExport->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => $fieldName,
            'order' => array('weight')
        ));
        $clStatus = $this->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status' => 'CL'
            ),
            'fields' => array('id')
        ));
        $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
        $this->set(compact('datas', 'fieldset', 'clStatus'));
        $this->render('tasks_vision');
    }
	public function hasActivity($project_id = null){
		if( empty( $project_id)) die(false);
		$haveActivity = 'false';
		if( $this->_checkRole( true, $project_id)){
			$this->loadModel('ActivityRequest');
			$this->loadModel('Project');
			$this->loadModel('ActivityTask');
			$this->Project->id = $project_id;
			$activity_id = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $project_id,
				),
				'fields' => array('id', 'activity_id')
			));
			$activity_id = !empty( $activity_id ) ? $activity_id[$project_id] : 0;
			// debug( $activity_id ); exit;
			if( $activity_id ){
				$tasks = $this->ActivityTask->find('list', array(
					'recursive' => -1,
					'conditions' => array('activity_id' => $activity_id),
					'fields' => array('id', 'id')
				));
				$request = 0;
				if (!empty($tasks)) {
					$request = $this->ActivityRequest->find('count', array(
						'recursive' => -1,
						'conditions' => array(
							'OR' => array(
								'activity_id' => $activity_id,
								'task_id' => $tasks
							)
						)
					));
				} else {
					$request = $this->ActivityRequest->find('count', array(
						'recursive' => -1,
						'conditions' => array('activity_id' => $activity_id)
					));
				}
				if ($request) {
					$haveActivity = 'true';
				}
			}
		}
		die( $haveActivity);
		
	}

    function kanban_my_assitant($style = 1) {
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModel('ProfitCenterManagerBackup');
        $manager = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));
        $manager_backup = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            ),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
        $profit_center_id = $this->employee_info['Employee']['profit_center_id'];
        $profit_center_id = !empty($list_profit_center_managers) ? $list_profit_center_managers : $profit_center_id;
        $list = array();
        $list_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status !=' => 'CL',
                'display' => 1
            ),
            'fields' => array('id', 'id')
        ));
        switch ($style) {
            case '1':
            case '2':
            case '3':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'assignedResources' => array(
                        '0' => $employee_id
                    ),
                    'nameStatusTask' => $list_status
                );
                break;
            // case '4':
            // case '5':
            // case '6':
            //     $list = array(
            //         'nameCategory' => array(
            //             '0' => 1
            //         ),
            //         'nameStatusTask' => $list_status,
            //         'manager' => 1
            //     );
            //     break;
            // case '7':
            // case '8':
            // case '9':
            //     $list = array(
            //         'nameCategory' => array(
            //             '0' => 1
            //         ),
            //         'nameStatusTask' => $list_status,
            //         'assignedTeam' => $profit_center_id
            //     );
            //     break;
        }
        $datas = $this->getDataForVisionTasks($list);
        // debug($datas); exit;
        $curDate = time();
        if (in_array($style, array(2, 5, 8))) {
            foreach ($datas as $key => $data) {
                if (!empty($data['end_date']) && strtotime($data['end_date']) <= $curDate && (in_array($data['status'], $list_status) || empty($data['status']))) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        } else if (in_array($style, array(3, 6, 9))) {
            foreach ($datas as $key => $data) {
                if ($data['overload'] > 0 && in_array($data['status'], $list_status)) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        }
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $fieldName = array('name', 'english');
        } else {
            $fieldName = array('name', 'france');
        }
        $this->loadModel('VisionTaskExport');
        $fieldset = $this->VisionTaskExport->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => $fieldName,
            'order' => array('weight')
        ));
        $clStatus = $this->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status' => 'CL'
            ),
            'fields' => array('id')
        ));
        $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
        $this->set(compact('datas', 'fieldset', 'clStatus'));
        $this->render('kanban_my_assitant');
    }

    protected function _getPath($company_id = null, $project_id = null, $key = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'projects' . DS . $key . DS;
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
        return $path;
    }

    /**
     * Upload
     */
    public function uploads($project_id = null, $key = null) {
        $this->layout = 'ajax';
        $result = array();
        $_FILES['FileField'] = array();
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($_FILES['file'])) {
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if (!empty($_FILES)) {
            $path = $this->_getPath($company_id, $project_id, $key);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
            }
            if (!empty($attachment)) {
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->ProjectFile->create();
                if ($this->ProjectFile->save(array(
                            'project_id' => $project_id,
                            'key' => $key,
                            'file_attachment' => $attachment,
                            'size' => $size,
                            'type' => $type))) {
                    $lastId = $this->ProjectFile->id;
                    $result = $this->ProjectFile->find('first', array('recursive' => -1, 'conditions' => array('ProjectFile.id' => $lastId)));
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
                $dataSession = array(
                    'path' => $path,
                    'file' => $attachment
                );
                $_SESSION['file_multiupload'][] = $dataSession;
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

    public function attachment($key = null, $project_id = null, $id = null, $type = null) {
		$this->_checkRole(false, $project_id);
        $company_id = $this->employee_info['Company']['id'];
        $last = $this->ProjectFile->find('first', array(
            'recursive' => -1,
            'fields' => array('project_id', 'file_attachment'),
            'conditions' => array(
                'ProjectFile.id' => $id,
                'ProjectFile.project_id' => $project_id,
                'ProjectFile.key' => $key
            ),
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Project.id = ProjectFile.project_id',
                        'company_id' => $company_id
                    )
                )
            )
        ));
        $error = true;
        if ($last && $last['ProjectFile']['project_id']) {
            $attachment = $last['ProjectFile']['file_attachment'];
            if ($type == 'download') {
                $path = trim($this->_getPath($company_id, $last['ProjectFile']['project_id'], $key)
                        . $last['ProjectFile']['file_attachment']);
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
                $this->ProjectFile->delete($id);
                if ($this->MultiFileUpload->otherServer == true) {
                    $path = trim($this->_getPath($company_id, $last['ProjectFile']['project_id'], $key));
                    if (!empty($_SERVER['HTTP_REFERER'])) {
                        $redirect = $_SERVER['HTTP_REFERER'];
                    } else {
                        $redirect = '/projects/your_form/' . $project_id;
                    }
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, $redirect);
                }
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $redirect = $_SERVER['HTTP_REFERER'];
                    $this->redirect($redirect);
                } else {
                    $this->redirect(array('action' => 'your_form', $project_id));
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'your_form', $project_id));
        }
    }

    // export Excel Index Screen
    public function export_excel_index() {
        if (!empty($this->data)) {
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Project Index') //auto translate
                    ->save($data, 'project_index_{date}.xls');
        }
        die;
    }

    public function checkStaffing() {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan', 'ActivityTask', 'ActivityRequest', 'TmpStaffingSystem', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
            $company_id = $this->employee_info['Employee']['company_id'];
            $projects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $id
                ),
                'fields' => array('id', 'project_name', 'activity_id', 'rebuild_staffing'),
                'order' => array('project_name')
            ));
            $activitiesLinked = Set::combine($projects, '{n}.Project.id', '{n}.Project.linked');
            $projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.project_name');
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 0
                ),
                'fields' => array('id', 'parent_id', 'project_id')
            ));
            $listProjectTaskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');
            $listActivityTaskFollowProject = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id');
            $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
            foreach ($projectTasks as $key => $projectTask) {
                foreach ($parentIds as $parentId) {
                    if ($projectTask['ProjectTask']['id'] == $parentId) {
                        unset($listProjectTaskIds[$key]);
                    }
                }
            }
            $projectTasks = array_values($listProjectTaskIds);
            $tasks = array_values($listActivityTaskFollowProject);
            $activityTasksLinked = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $tasks),
                'fields' => array('project_task_id', 'id')
            ));
            $CONSUMED[$id] = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed'),
                'conditions' => array(
                    'status' => 2,
                    'task_id' => $activityTasksLinked,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0, "task_id" => null)
                ),
            ));
            $CONSUMED[$id] = Set::classicExtract($CONSUMED[$id], '{n}.0.consumed');
            $_previous = array_values($activitiesLinked);
            $previous = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed', 'activity_id'),
                'conditions' => array(
                    'status' => 2,
                    'activity_id' => $_previous,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0),
                ),
                'group' => 'activity_id'
            ));
            $previous = Set::combine($previous, '{n}.ActivityRequest.activity_id', '{n}.0.consumed');
            $results = array();
            $workloadFromTask = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'id' => $projectTasks,
                    'special' => 0
                ),
                'fields' => array('SUM(estimated) as workload', 'project_id'),
                'group' => 'project_id'
            ));
            $workloadFromTask = Set::combine($workloadFromTask, '{n}.ProjectTask.project_id', '{n}.0.workload');
            $staffings = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id
                ),
                'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model', 'project_id'),
                'group' => array('model', 'project_id')
            ));
            foreach ($staffings as $index => $data) {
                $_data = array_merge($data[0], $data['TmpStaffingSystem']);
                if ($_data['model'] == 'employee') {
                    $staffingsE[$_data['project_id']] = $_data['workload'];
                } elseif ($_data['model'] == 'profile') {
                    $staffingsP2[$_data['project_id']] = $_data['workload'];
                } else {
                    $staffingsP[$_data['project_id']] = $_data['workload'];
                }
                if ($_data['model'] == 'employee') {
                    $staffingsConsumedE[$_data['project_id']] = $_data['consumed'];
                } else {
                    $staffingsConsumedP[$_data['project_id']] = $_data['consumed'];
                }
            }
            $listIdRebuilds = array();
            $error = 0;
            $_workload = isset($workloadFromTask[$id]) ? $workloadFromTask[$id] : 0.00;
            $_staffingE = isset($staffingsE[$id]) ? $staffingsE[$id] : 0.00;
            $_staffingP = isset($staffingsP[$id]) ? $staffingsP[$id] : 0.00;
            $_staffingP2 = isset($staffingsP2[$id]) ? $staffingsP2[$id] : 0.00;
            if ($_staffingE != $_workload) {
                $error = 1;
            }
            if ($_staffingP != $_workload) {
                $error = 2;
            }
            if ($_staffingP2 != $_workload) {
                $error = 7;
            }
            $_previous = 0;
            if (isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id])) {
                $_previous = $activitiesLinked[$id];
            }
            $_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
            $_consumed = isset($CONSUMED[$id]) ? $_consumed + $CONSUMED[$id][0] : $_consumed;
            $_consumed = number_format($_consumed, 2, '.', '');
            $_staffingConsumedE = isset($staffingsConsumedE[$id]) ? $staffingsConsumedE[$id] : 0.00;
            $_staffingConsumedP = isset($staffingsConsumedP[$id]) ? $staffingsConsumedP[$id] : 0.00;
            if ($_staffingConsumedE != $_consumed) {
                $error = 4;
            }
            if ($_staffingConsumedP != $_consumed) {
                $error = 5;
            }
            if ($error > 0) {
                echo "check staffing";
                $this->ProjectTask->staffingSystem($id);
            }
            echo 'done';
        }
        exit;
    }

    public function checkStaffings() {
        set_time_limit(0);
        ignore_user_abort(true);
        $first = !empty($_POST['first']) ? $_POST['first'] : 1;
        $LIMIT = 70;
        $done = false;
        if ($first == 1) {
            $LIMIT = 20;
            $this->Session->write('ProjectChecked', array());
        }
        $this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan', 'ActivityTask', 'ActivityRequest', 'TmpStaffingSystem', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
        $company_id = $this->employee_info['Employee']['company_id'];
        $projects = $this->Project->find('all', array(
            'limit' => $LIMIT,
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => 1,
                'NOT' => array('Project.id' => $this->Session->read('ProjectChecked'))
            ),
            'fields' => array('id', 'project_name', 'activity_id', 'rebuild_staffing'),
            'order' => array('project_name')
        ));
        $activitiesLinked = Set::combine($projects, '{n}.Project.id', '{n}.Project.linked');
        $projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.project_name');
        $projectListIds = array_keys($projects);
        $projectIdChecked = array_merge($this->Session->read('ProjectChecked'), $projectListIds);
        $this->Session->write('ProjectChecked', $projectIdChecked);
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.project_id' => $projectListIds,
                'ProjectTask.special' => 0,
            ),
            'fields' => array('id', 'parent_id', 'project_id')
        ));
        $listProjectTaskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');
        $listActivityTaskFollowProject = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id', '{n}.ProjectTask.project_id');
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        foreach ($projectTasks as $key => $projectTask) {
            foreach ($parentIds as $parentId) {
                if ($projectTask['ProjectTask']['id'] == $parentId) {
                    unset($listProjectTaskIds[$key]);
                }
            }
        }
        $projectTasks = array_values($listProjectTaskIds);
        //GET CONSUMED
        foreach ($listActivityTaskFollowProject as $projectId => $tasks) {
            $tasks = array_values($tasks);
            $activityTasksLinked = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $tasks),
                'fields' => array('project_task_id', 'id')
            ));
            $CONSUMED[$projectId] = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed'),
                'conditions' => array(
                    'status' => 2,
                    'task_id' => $activityTasksLinked,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0, "task_id" => null)
                ),
            ));
            $CONSUMED[$projectId] = Set::classicExtract($CONSUMED[$projectId], '{n}.0.consumed');
        }
        $_previous = array_values($activitiesLinked);
        $previous = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('SUM(value) AS consumed', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $_previous,
                'company_id' => $company_id,
                'NOT' => array('value' => 0),
            ),
            'group' => 'activity_id'
        ));
        $previous = Set::combine($previous, '{n}.ActivityRequest.activity_id', '{n}.0.consumed');
        $results = array();

        $workloadFromTask = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectListIds,
                'id' => $projectTasks,
                'special' => 0
            ),
            'fields' => array('SUM(estimated) as workload', 'project_id'),
            'group' => 'project_id'
        ));
        $workloadFromTask = Set::combine($workloadFromTask, '{n}.ProjectTask.project_id', '{n}.0.workload');
        $staffings = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectListIds
            ),
            'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model', 'project_id'),
            'group' => array('model', 'project_id')
        ));
        foreach ($staffings as $index => $data) {
            $_data = array_merge($data[0], $data['TmpStaffingSystem']);
            //DATA WORKLOAD
            if ($_data['model'] == 'employee') {
                $staffingsE[$_data['project_id']] = $_data['workload'];
            } elseif ($_data['model'] == 'profile') {
                $staffingsP2[$_data['project_id']] = $_data['workload'];
            } else {
                $staffingsP[$_data['project_id']] = $_data['workload'];
            }
            //DATA CONSUMED
            if ($_data['model'] == 'employee') {
                $staffingsConsumedE[$_data['project_id']] = $_data['consumed'];
            } else {
                $staffingsConsumedP[$_data['project_id']] = $_data['consumed'];
            }
        }
        $listIdRebuilds = array();
        foreach ($projects as $id => $name) {
            $error = 0;
            $_workload = isset($workloadFromTask[$id]) ? $workloadFromTask[$id] : 0.00;
            $_staffingE = isset($staffingsE[$id]) ? $staffingsE[$id] : 0.00;
            $_staffingP = isset($staffingsP[$id]) ? $staffingsP[$id] : 0.00;
            $_staffingP2 = isset($staffingsP2[$id]) ? $staffingsP2[$id] : 0.00;
            if ($_staffingE != $_workload) {
                $error = 1;
            }
            if ($_staffingP != $_workload) {
                $error = 2;
            }
            if ($_staffingP2 != $_workload) {
                $error = 7;
            }
            $_previous = 0;
            if (isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id])) {
                $_previous = $activitiesLinked[$id];
            }
            $_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
            $_consumed = isset($CONSUMED[$id]) ? $_consumed + $CONSUMED[$id][0] : $_consumed;
            $_consumed = number_format($_consumed, 2, '.', '');
            $_staffingConsumedE = isset($staffingsConsumedE[$id]) ? $staffingsConsumedE[$id] : 0.00;
            $_staffingConsumedP = isset($staffingsConsumedP[$id]) ? $staffingsConsumedP[$id] : 0.00;
            if ($_staffingConsumedE != $_consumed) {
                $error = 4;
            }
            if ($_staffingConsumedP != $_consumed) {
                $error = 5;
            }
            if ($error > 0) {
                $listIdRebuilds[] = $id;
            }
        }
        foreach ($listIdRebuilds as $id) {
            if ($id != null)
                $this->ProjectTask->staffingSystem($id);
        }
        $countRecord = $this->Project->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => 1,
            )
        ));
        $progress = round(((count($this->Session->read('ProjectChecked')) / $countRecord) * 100), 2);
        if ($progress >= 100)
            $done = true;
        $result['done'] = $done;
        $result['progress'] = $progress;
        echo json_encode($result);
        exit;
    }

    private function getTaskEuro($projectIds) {
        $result = array();
        $this->loadModels('ProjectTask', 'ActivityTask', 'ActivityRequest');
        $useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
        foreach ($projectIds as $_id) {
            $listTask = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $_id),
                'fields' => array('id', 'task_title', 'estimated', 'initial_estimated', 'manual_consumed', 'unit_price', 'special', 'special_consumed', 'parent_id', 'task_completed', 'overload', 'predecessor', 'manual_overload', 'progress_order', 'amount')
            ));
            $parentIds = array_unique(Set::classicExtract($listTask, '{n}.ProjectTask.parent_id'));
            $listPrent = array();
            foreach ($listTask as $key => $_listTask) {
                foreach ($parentIds as $parentId) {
                    if ($_listTask['ProjectTask']['id'] == $parentId) {
                        $listPrent[$key] = $key;
                    }
                }
            }
            $listTaskIds = !empty($listTask) ? Set::classicExtract($listTask, '{n}.ProjectTask.id') : array();
            $listTaskIdFollowTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $listTaskIds),
                'fields' => array('project_task_id', 'id')
            ));
            $consumed = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array('task_id' => $listTaskIdFollowTasks, 'status' => 2),
                'fields' => array('task_id', 'SUM(value) as consumed'),
                'group' => array('task_id')
            ));
            $inUsedOfActivityTasks = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $listTaskIdFollowTasks,
                ),
                'fields' => array('task_id', 'SUM(IF(`status` != 2, `value`, 0)) as inUsed'),
                'group' => array('task_id')
            ));
            $inUsedOfActivityTasks = !empty($inUsedOfActivityTasks) ? Set::combine($inUsedOfActivityTasks, '{n}.ActivityRequest.task_id', '{n}.0.inUsed') : array();
            $consumed = !empty($consumed) ? Set::combine($consumed, '{n}.ActivityRequest.task_id', '{n}.0.consumed') : array();
            $workload_euro = $consumed_euro = $remain_euro = $estimated_euro = $_cons = $_workload = $initial_estimated = $unit_price = $_remain = $overload = $manual_overload = $amount = 0;
            $_manual_consumed = $completed = $_inUsed = $_progress_order_amount = 0;
            foreach ($listTask as $key => $value) {
                $dx = $value['ProjectTask'];
                $unit = !empty($dx['unit_price']) ? $dx['unit_price'] : 0;
                $workload = !empty($dx['estimated']) ? $dx['estimated'] : 0;
                $manual_consumed = !empty($dx['manual_consumed']) ? $dx['manual_consumed'] : 0;
                $_consumed_rq = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($consumed[$listTaskIdFollowTasks[$dx['id']]]) ? $consumed[$listTaskIdFollowTasks[$dx['id']]] : 0;
                // special consumed.
                $_specialcs = !empty($dx['special_consumed']) && $dx['special'] == 1 ? $dx['special_consumed'] : 0;
                $_consumed = $_consumed_rq + $_specialcs;
                $_overload = !empty($dx['overload']) ? $dx['overload'] : 0;
                $_remain_euro = ($workload > $_consumed) ? ($workload - $_consumed) * $unit : 0;
                $_cons += $_consumed;
                $consumed_euro += ($_consumed + $manual_consumed) * $unit;
                if ($useManualConsumed) {
                    $_consumed += $manual_consumed;
                }
                $_amount = !empty($dx['amount']) ? $dx['amount'] : 0;
                $progress_order_amount = round($dx['progress_order'] * $_amount / 100, 2);
                if (in_array($key, $listPrent))
                    continue;
                $remain = ($workload - $_consumed) > 0 ? $workload - $_consumed : 0;
                $initial_estimated += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] : 0;
                $_progress_order_amount += $progress_order_amount;
                $amount += $_amount;
                $unit_price += $unit;
                $_workload += $workload;
                $_remain += $remain;
                $overload += $_overload;
                $workload_euro += $workload * $unit;
                $remain_euro += $_remain_euro;
                $_manual_consumed += $manual_consumed;
                $manual_overload += !empty($dx['manual_overload']) ? $dx['manual_overload'] : 0;
                $estimated_euro += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] * $unit : 0;
                $inUsed = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]]) ? $inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]] : 0;
                $_inUsed += $inUsed;
            }
            if ($useManualConsumed) {
                $result[$_id]['Overload'] = $manual_overload;
                if ($_manual_consumed != 0) {
                    $_completed = ($_workload + $manual_overload) == 0 ? round(($_manual_consumed * 100), 2) : round(($_manual_consumed * 100) / ($_workload + $manual_overload), 2);
                } else {
                    $_completed = 0;
                }
            } else {
                $result[$_id]['Overload'] = $overload;
                if ($_consumed != 0) {
                    $_completed = ($_workload + $overload) == 0 ? round(($_cons * 100), 2) : round(($_cons * 100) / ($_workload + $overload), 2);
                } else {
                    $_completed = 0;
                }
            }
            $_progress_order = $amount != 0 ? round($_progress_order_amount / $amount * 100, 2) : 0;
            $_completed = $_completed > 0 ? $_completed : 0;
            $_completed = $_completed < 100 ? $_completed : 100;
            $result[$_id]['Consumed'] = $_cons;
            $result[$_id]['Remain'] = $_remain;
            $result[$_id]['Workload'] = $_workload;
            $result[$_id]['Initialworkload'] = $initial_estimated;
            $result[$_id]['UnitPrice'] = $unit_price;
            $result[$_id]['Workload€'] = $workload_euro;
            $result[$_id]['Consumed€'] = $consumed_euro;
            $result[$_id]['Remain€'] = $remain_euro;
            $result[$_id]['Estimated€'] = $estimated_euro;
            $result[$_id]['ManualOverload'] = $manual_overload;
            $result[$_id]['ManualConsumed'] = $_manual_consumed;
            $result[$_id]['Completed'] = $_completed;
            $result[$_id]['InUsed'] = $_inUsed;
            $result[$_id]['Amount€'] = $amount;
            $result[$_id]['%progressorder'] = $_progress_order;
            $result[$_id]['%progressorder€'] = $_progress_order_amount;
        }
        return $result;
    }

    function export_pdf() {
        $this->layout = false;
        ini_set('memory_limit', '2048M');
        $employee_name = $this->employee_info['Employee']['fullname'];
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($this->data['Export'])) {
            $project_name = $this->data['Export']['project_name'];
            $project_id = $this->data['Export']['project_id'];
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);
            $tmpFile = TMP . 'your_form.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));
            list($_width, $_height) = getimagesize($tmpFile);
            $height = 230;
            if (!empty($this->data['Export']['export_screen']) && $this->data['Export']['export_screen'] == 'ajax') {
                $height = 50;
            }
            $i = 0;
            $image = imagecreatefrompng($tmpFile);
            $list = array();
            do {
                if ($_height - $height >= 850) {
                    $h = 850;
                } else {
                    $h = $_height - $height;
                }
                if ($h > 100) {
                    $i++;
                    $crop = imagecreatetruecolor($_width, $h);
                    //set white background
                    $white = imagecolorallocate($crop, 255, 255, 255);
                    imagefill($crop, 0, 0, $white);

                    $tmpFile1 = TMP . 'your_form_' . $i . '.png';
                    imagecopy($crop, $image, 0, 0, 0, $height, $_width, $h);
                    imagepng($crop, $tmpFile1);
                    $list[$i] = $tmpFile1;
                    $height_last = $h;
                }
                $height += 850;
            } while ($height < $_height);

            unlink($tmpFile);
            $path = $this->_getPathLivrables($project_id);
            $this->loadModels('ProjectLivrable', 'Menu');
            $your_form_name = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'model' => 'project',
                    'controllers' => 'projects',
                    'functions' => 'your_form_plus',
                    'widget_id' => 'your_form_plus'
                )
            ));
            $now = date('d-m-Y_H-i-s', time());
            if (!empty($your_form_name)) {
                $your_form_name = $your_form_name['Menu']['name_fre'];
            } else {
                $your_form_name = 'Fiche+';
            }
            $file_name = $project_name . '_' . $employee_name . '_' . $now . '.pdf';
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $file_name = str_replace(' ', '-', $file_name);
            $file_name = $str_utility->removeVNUnicode($file_name);
            $saved = array(
                'name' => $your_form_name . ' ' . $now,
                'project_id' => $project_id,
                'livrable_file_attachment' => $file_name,
                'livrable_progression' => 0,
                'upload_date' => time(),
                'format' => 2
            );
            $this->ProjectLivrable->create();
            $this->ProjectLivrable->save($saved);
            $this->set(compact('list', 'height', 'rows', 'i', 'height_last', 'project_name', 'employee_name', 'file_name', 'path'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function flash_info($project_id = null) {
        $this->loadModels('Project', 'ProjectAmr', 'ProjectAmrProgram', 'Employee', 'ProjectBudgetSyn', 'LogSystem', 'HistoryFilter', 'ProjectRisk', 'UserLastUpdated');
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        $flash_data = array();
        $project = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('*')
        ));
        $project_program = $this->ProjectAmrProgram->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project['Project']['project_amr_program_id']
            ),
            'fields' => array('amr_program')
        ));
        if (!empty($project_program)) {
            $flash_data['project_program'] = $project_program['ProjectAmrProgram']['amr_program'];
        }
        $employee_name = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
            'fields' => array('id', 'first_name', 'last_name')
        ));
        $employee_name = !empty($employee_name) ? Set::combine($employee_name, '{n}.Employee.id', '{n}.Employee') : array();
        if (!empty($project)) {
            $flash_data['project_id'] = $project['Project']['id'];
            $flash_data['project_name'] = $project['Project']['project_name'];
            $flash_data['project_code'] = $project['Project']['project_code_1'];
            $flash_data['start_date'] = $project['Project']['start_date'];
            $flash_data['end_date'] = $project['Project']['start_date'];
            $flash_data['project_manager'] = $employee_name[$project['Project']['project_manager_id']]['first_name'] . ' ' . $employee_name[$project['Project']['project_manager_id']]['last_name'];
            if (!empty($project['Project']['technical_manager_id'])) {
                $flash_data['technical_manager'] = $employee_name[$project['Project']['technical_manager_id']]['first_name'] . ' ' . $employee_name[$project['Project']['technical_manager_id']]['last_name'];
            }
            if (!empty($project['Project']['primary_objectives'])) {
                $flash_data['primary_objectives'] = $project['Project']['primary_objectives'];
            }
            $_projectTask = $this->_getPorjectTask($project_id);
            $engaged = $_projectTask['engaged'];
            $validated = $_projectTask['validated'];
            // Workload
            $flash_data['validated'] = $validated;
            // Consumed
            $flash_data['engaged'] = $engaged;
        }
        $projectRisks = $this->ProjectRisk->find("first", array(
            'fields' => array('id', 'project_risk', 'updated'),
            'recursive' => -1, "conditions" => array('project_id' => $project_id),
            'order' => array('updated' => 'DESC')
        ));
        // last updated project risk
        $risk_last_updated = $this->UserLastUpdated->find('first', array(
            'fields' => array('updated', 'employee_id'),
            'conditions' => array(
                'UserLastUpdated.company_id' => $this->employee_info['Company']['id'],
                'UserLastUpdated.path' => 'project_risks/index/' . $project_id,
            ),
        ));
        if (!empty($risk_last_updated)) {
            $projectRisks['employee_name'] = $employee_name[$risk_last_updated['UserLastUpdated']['employee_id']]['first_name'] . ' ' . $employee_name[$risk_last_updated['UserLastUpdated']['employee_id']]['last_name'];
            $projectRisks['employee_id'] = $risk_last_updated['UserLastUpdated']['employee_id'];
        }
        $project_amr = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('*')
        ));

        if (!empty($project_amr)) {
            $flash_data['weather'] = $project_amr['ProjectAmr']['weather'];
            $flash_data['rank'] = $project_amr['ProjectAmr']['rank'];
        }


        $overload = $this->ProjectBudgetSyn->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => 6,
            ),
            'fields' => array('overload')
        ));

        if (!empty($overload)) {
            $flash_data['overload'] = $overload['ProjectBudgetSyn']['overload'];
        }

        // LOG SYSTEM
        $log_systems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model_id' => $project_id,
            ),
            'fields' => array('*'),
            'order' => array('created' => 'ASC')
        ));

        $risk_comment = $kpi_comment = $todo = $done = array();
        foreach ($log_systems as $key => $log_system) {
            if (!empty($log_system['LogSystem']) && !empty($log_system['LogSystem']['model'])) {
                if ($log_system['LogSystem']['model'] == 'ProjectRisk') {
                    $risk_comment[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'ProjectAmr') {
                    $kpi_comment[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'ToDo') {
                    $todo[] = $log_system;
                }
                if ($log_system['LogSystem']['model'] == 'Done') {
                    $done[] = $log_system;
                }
            }
        }
        $is_avatar = $this->checkAvatar();
        $projectMilestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
            'order' => array('weight' => 'ASC')
        ));
        $projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $projectMilestones = Set::sort($projectMilestones, '{n}.milestone_date', 'asc');
        $listProjects = '';
        $listProjects = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'project_list_filter',
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => 'params'
        ));
        if ($listProjects) {

            $listProjects = $listProjects['HistoryFilter']['params'];
            $listProjects = explode('-', $listProjects);
        } else {
            $listProjects = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                ),
                'fields' => array('id')
            ));
            $listProjects = !empty($listProjects) ? Set::extract($listProjects, '{n}') : array();
        }
        $_project_nav = array(
            'prev' => '',
            'current' => $project_id,
            'next' => '',
        );
        if ($listProjects) {
            $key = array_search($project_id, $listProjects);
            if ($key !== false) {
                if ($key == (count($listProjects) - 1))
                    $_project_nav['next'] = $listProjects['0'];
                else
                    $_project_nav['next'] = $listProjects[$key + 1];
                if ($key == 0)
                    $_project_nav['prev'] = $listProjects[count($listProjects) - 1];
                else
                    $_project_nav['prev'] = $listProjects[$key - 1];
            }
            else {
                $_project_nav['prev'] = $listProjects[count($listProjects) - 1];
                $_project_nav['next'] = $listProjects['0'];
            }
        }

        $this->set(compact('flash_data', 'is_avatar', 'risk_comment', 'kpi_comment', 'todo', 'done', 'projectMilestones', '_project_nav', 'projectRisks'));
    }

    /**
     *  Save/edit Log System
     */
    public function update_data_log() {
        $this->loadModels('Project', 'LogSystem');
        $this->layout = false;
        $result = array();
        // debug($this->employee_info); exit;
        if (empty($this->data['id']) && $this->data['id'] != 'primary_objectives') {
            // Create new comment
            if (empty($this->data['description'])) {
                $this->Session->setFlash(__('Unable to create new comment.', true), 'error');
                exit;
            }
            $this->LogSystem->create();
            $result = $this->LogSystem->save(array(
                'id' => $this->LogSystem->id,
                'description' => $this->data['description'],
                'updated' => time(),
                'created' => time(),
                'employee_id' => $this->employee_info['Employee']['id'],
                'update_by_employee' => $this->employee_info['Employee']['fullname'],
                'name' => $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y'),
                'model_id' => $this->data['model_id'],
                'model' => $this->data['model'],
                'company_id' => $this->employee_info['Company']['id'],
            ));
            $log_system = array();
            if (!empty($result)) {
                // LOG SYSTEM
                $log_system = $this->LogSystem->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'model_id' => $this->data['model_id'],
                        'model' => $this->data['model'],
                    ),
                    'fields' => array('*'),
                    'order' => array('created' => 'DESC')
                ));

                // format date 
                foreach ($log_system as $key => $value) {
                    $log_system[$key]['LogSystem']['created'] = date('d M Y', $log_system[$key]['LogSystem']['created']);
                }
            }
            echo json_encode($log_system);
            exit;
        }
        if (!empty($this->data)) {
            if ($this->data['model'] == 'primary_objectives') {
                $this->Project->id = $this->data['model_id'];
                $saveLogs = array(
                    'primary_objectives' => $this->data['description'],
                    'last_modified' => time(),
                );
                $result = $this->Project->save($saveLogs);
                echo json_encode($result);
            } else {
                $this->LogSystem->id = $this->data['id'];
                $saveLogs = array(
                    'description' => $this->data['description'],
                    'updated' => time(),
                );
                $result = $this->LogSystem->save($saveLogs);
                echo json_encode($result);
            }
        }
        $this->Project->id = $this->data['model_id'];
        $this->Project->save(array(
            'last_modified' => time(),
            'update_by_employee' => $this->employee_info['Employee']['fullname']
        ));
        exit;
    }

    /**
     *  Save/edit Log System
     */
    public function add_project_risk() {
        $this->loadModels('ProjectRisk', 'UserLastUpdated');
        $this->layout = false;
        $result = array();
        if (!empty($this->data)) {
            // Create new comment
            if (empty($this->data['project_risk'])) {
                $this->Session->setFlash(__('Unable to create new comment.', true), 'error');
                exit;
            }
            $this->ProjectRisk->create();
            $result = $this->ProjectRisk->save(array(
                'id' => $this->ProjectRisk->id,
                'project_risk' => $this->data['project_risk'],
                'project_id' => $this->data['project_id'],
                'project_risk_severity_id' => '',
                'project_risk_occurrence_id' => '',
                'project_issue_status_id' => '',
                'risk_assign_to' => '',
                'risk_close_date' => '',
                'actions_manage_risk' => '',
                'company_id' => $this->employee_info['Company']['id'],
                'created' => time(),
                'updated' => time(),
                'file_attachement' => '',
                'format' => '',
                'weight' => 0
            ));
            $log_system = array();
            if (!empty($result)) {
                // LOG SYSTEM
                $log_system = $this->ProjectRisk->find('first', array(
                    'conditions' => array(
                        'ProjectRisk.project_id' => $this->data['project_id']
                    ),
                    'fields' => array('ProjectRisk.id', 'ProjectRisk.project_risk', 'ProjectRisk.updated'),
                    'order' => array('ProjectRisk.updated' => 'DESC')
                ));
                // format date 
                $log_system['ProjectRisk']['updated'] = date('d M Y', $log_system['ProjectRisk']['updated']);
                $log_system['ProjectRisk']['employee_id'] = $this->employee_info['Employee']['id'];
                // User last updated

                $risk_last_updated = $this->UserLastUpdated->find('first', array(
                    'fields' => array('id', 'employee_id'),
                    'conditions' => array(
                        'UserLastUpdated.company_id' => $this->employee_info['Company']['id'],
                        'UserLastUpdated.path' => 'project_risks/index/' . $this->data['project_id'],
                    ),
                ));

                if (!empty($risk_last_updated)) {
                    $this->UserLastUpdated->id = $risk_last_updated['UserLastUpdated']['id'];
                    $this->UserLastUpdated->save(array(
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'updated' => time()
                    ));
                } else {
                    $this->UserLastUpdated->create();
                    $this->UserLastUpdated->save(array(
                        'id' => $this->UserLastUpdated->id,
                        'company_id' => $this->employee_info['Company']['id'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'path' => 'project_risks/index/' . $this->data['project_id'],
                        'action' => '',
                        'created' => time(),
                        'updated' => time()
                    ));
                }

                $log_system['ProjectRisk']['employee_id'] = $this->employee_info['Employee']['id'];
            }
            echo json_encode($log_system);
        }
        exit;
    }

    public function update_project_risk() {
        $this->loadModels('ProjectRisk', 'UserLastUpdated');
        if (!empty($this->data)) {
            $this->ProjectRisk->id = $this->data['id'];
            $saveProjectRisk = array(
                'project_risk' => $this->data['project_risk'],
                'updated' => time(),
            );
            $result = $this->ProjectRisk->save($saveProjectRisk);
            $last_updated = $this->UserLastUpdated->find('first', array(
                'fields' => array('id'),
                'conditions' => array(
                    'UserLastUpdated.company_id' => $this->employee_info['Company']['id'],
                    'UserLastUpdated.path' => 'project_risks/index/' . $this->data['project_id'],
                ),
            ));
            if (!empty($last_updated['UserLastUpdated']['id'])) {
                $this->UserLastUpdated->id = $last_updated['UserLastUpdated']['id'];
                $this->UserLastUpdated->save(array(
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'updated' => time()
                ));
            }
            echo json_encode($result);
        }
        exit;
    }

    public function fixWeatherUpdate() {
        $this->loadModels('Project', 'ProjectAmr');

        $idPA = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'fields' => array('project_id'),
            'conditions' => array('not' => array('project_id' => null)),
        ));

        $listPA = array();
        foreach ($idPA as $key => $value) {
            $listPA[$key] = $value['ProjectAmr']['project_id'];
        }
        // list projects can't update weather
        $projects = $this->Project->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_amr_program_id', 'project_amr_sub_program_id', 'project_manager_id', 'start_date', 'end_date'),
            'conditions' => array('not' => array('id' => $listPA)),
        ));

        // data default
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        if (!empty($projects)) {
            foreach ($projects as $key => $value) {

                $data_arm['ProjectAmr']['project_amr_program_id'] = $value['Project']['project_amr_program_id'];
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $value['Project']['project_amr_sub_program_id'];
                $data_arm['ProjectAmr']['project_manager_id'] = $value['Project']['project_manager_id'];
                $data_arm['ProjectAmr']['weather'] = 'sun';
                $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['planning_weather'] = 'sun';
                $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['organization_weather'] = 'sun';
                $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
                $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['customer_point_of_view'] = 'sun';
                $data_arm['ProjectAmr']['rank'] = 'up';
                $data_arm['ProjectAmr']['created'] = $value['Project']['start_date'];
                $data_arm['ProjectAmr']['updated'] = $value['Project']['start_date'];

                //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $value['Project']['id'];
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
            }
        }
        $this->redirect(array('action' => 'index'));
    }

    protected function _getPathGlobal($project_id, $global_view = false) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'localviews' . DS;
        if ($global_view == 'global')
            $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    public function checkNameAcitivity() {
        if (!empty($_POST)) {
            $name = $_POST['data'];
            $this->loadModel('Activity');
            $check = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'name' => $name
                )
            ));
            if (!empty($check)) {
                echo 1;
            } else {
                echo 0;
            }
        }
        exit;
    }

    private function getNextMilestone($listProjectId) {
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        $this->loadModels('ProjectMilestone');
        if (!empty($listProjectId)) {
            foreach ($listProjectId as $id) {
                $nextMilestone = $this->ProjectMilestone->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id,
                        'milestone_date >' => date('Y-m-d', time())
                    ),
                    'fields' => array('id', 'milestone_date'),
                    'order' => array('milestone_date' => 'ASC')
                ));
                $nextMilestoneByDay = $nextMilestoneByWeek = 0;
                $bgc = '#ff290a';
                if (!empty($nextMilestone)) {
                    $nextMilestone = $nextMilestone['ProjectMilestone']['milestone_date'];
                    $nextMilestone = strtotime($nextMilestone);
                    $now = time();
                    if ($now < $nextMilestone) {
                        $diff = ($nextMilestone - $now) / (24 * 60 * 60) + 1;
                        $nextMilestoneByDay = (int) ($diff);
                        $nextMilestoneByWeek = (int) ($diff / 7);
                    }
                    if ($nextMilestoneByWeek > 0 && $nextMilestoneByWeek <= 3) {
                        $bgc = '#F3960B';
                    } else if ($nextMilestoneByWeek > 3) {
                        $bgc = '#5DBF56';
                    }
                }
                $listColorNextMil[$id] = $bgc;
                $listNextMilestoneByDay[$id] = $nextMilestoneByDay;
                $listNextMilestoneByWeek[$id] = $nextMilestoneByWeek;
            }
        }
        return array($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek);
    }

    function your_form_filter($project_id) {
        $this->loadModels('YourFormFilter', 'Menu', 'ProjectStatus', 'ProjectIssueStatus', 'ProfileProjectManagerDetail');
        $isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
        $employee_id = $this->employee_info['Employee']['id'];
        $company_id = $this->employee_info['Company']['id'];
        $this->autoInsertFilter();
        $yourFromFilters = $this->YourFormFilter->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
            ),
            'order' => array('weight')
        ));
        $langCode = Configure::read('Config.langCode');
        foreach ($yourFromFilters as $key => $yourFromFilter) {

            $dx = $yourFromFilter['YourFormFilter'];
            $widget = $dx['widget'];

            // thay doi widget_id
            if ($widget == 'buget_internal') {
                $widget = 'internal_cost';
            } else if ($widget == 'budget_externals') {
                $widget = 'external_cost';
            } else if ($widget == 'project_task') {
                $widget = 'task';
            } else if ($widget == 'location') {
                $widget = 'local_view';
            }

            if ($isProfileManager != 0) {
                $check = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model_id' => $isProfileManager,
                        'widget_id' => $widget,
                        'display' => 1
                    )
                ));
                $check = !empty($check['ProfileProjectManagerDetail']) ? $check['ProfileProjectManagerDetail'] : array();
            } else {
                $check = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model' => 'project',
                        'widget_id' => $widget,
                        'display' => 1
                    )
                ));
                $check = !empty($check['Menu']) ? $check['Menu'] : array();
            }


            if (empty($check)) {
                if ($isProfileManager != 0) {
                    $check = $this->ProfileProjectManagerDetail->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model_id' => $isProfileManager,
                            'widget_id' => $widget,
                        )
                    ));
                    $check = !empty($check['ProfileProjectManagerDetail']) ? $check['ProfileProjectManagerDetail'] : array();
                } else {
                    $check = $this->Menu->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model' => 'project',
                            'widget_id' => $widget
                        )
                    ));
                    $check = !empty($check['Menu']) ? $check['Menu'] : array();
                }
            }
            if (!empty($check)) {
                $name = ($langCode == 'fr') ? $check['name_fre'] : $check['name_eng'];
                if (!empty($check) && $check['display'] == 0) {
                    //$yourFromFilters[$key]['display'] = 0;
                    unset($yourFromFilters[$key]);
                } else if ($dx['widget'] == 'kpi') {
                    unset($yourFromFilters[$key]);
                } else if (!empty($check) && ($check['display'] == 1)) {
                    $yourFromFilters[$key]['YourFormFilter']['name'] = $name;
                }
            } else if ($dx['widget'] == 'weather') {
                $yourFromFilters[$key]['YourFormFilter']['name'] = 'Weather';
            } else {
                unset($yourFromFilters[$key]);
            }
        }
        $projectStatus = $this->ProjectStatus->find('list', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'name')
        ));
        $projectIssueStatus = $this->ProjectIssueStatus->find('list', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'issue_status')
        ));
        $_statusFilters = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'widget' => array('issue', 'risk', 'project_task', 'finance_plus', 'global_view', 'finance_two_plus')
            ),
            'fields' => array('widget', 'status')
        ));
        $statusFilters = array();
        foreach ($_statusFilters as $key => $statusFilter) {
            $statusFilters[$key] = explode(',', $statusFilter);
        }
        $this->set(compact('yourFromFilters', 'employee_id', 'project_id', 'projectStatus', 'projectIssueStatus', 'statusFilters'));
    }

    public function autoInsertFilter() {
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModels('YourFormFilter');
        $default = array(
            0 => array(
                'widget' => 'your_form',
                'employee_id' => $employee_id,
                'name' => 'Your form',
                'display' => 0,
                'weight' => 1
            ),
            1 => array(
                'widget' => 'weather',
                'employee_id' => $employee_id,
                'name' => 'Weather',
                'display' => 1,
                'weight' => 2
            ),
            2 => array(
                'widget' => 'gantt',
                'employee_id' => $employee_id,
                'name' => 'Gantt',
                'display' => 1,
                'weight' => 3
            ),
            3 => array(
                'widget' => 'milestone',
                'employee_id' => $employee_id,
                'name' => 'Gantt detail',
                'display' => 0,
                'weight' => 4
            ),
            4 => array(
                'widget' => 'risk',
                'employee_id' => $employee_id,
                'name' => 'Risk',
                'display' => 0,
                'weight' => 5
            ),
            5 => array(
                'widget' => 'issue',
                'employee_id' => $employee_id,
                'name' => 'Issue',
                'display' => 0,
                'weight' => 6
            ),
            6 => array(
                'widget' => 'location',
                'employee_id' => $employee_id,
                'name' => 'Location',
                'display' => 0,
                'weight' => 7
            ),
            7 => array(
                'widget' => 'dependency',
                'employee_id' => $employee_id,
                'name' => 'Dependency',
                'display' => 0,
                'weight' => 8
            ),
            8 => array(
                'widget' => 'global_view',
                'employee_id' => $employee_id,
                'name' => 'Global views',
                'display' => 0,
                'weight' => 9
            ),
            9 => array(
                'widget' => 'buget_internal',
                'employee_id' => $employee_id,
                'name' => 'Budget internals',
                'display' => 0,
                'weight' => 10
            ),
            10 => array(
                'widget' => 'budget_externals',
                'employee_id' => $employee_id,
                'name' => 'Budget externals',
                'display' => 0,
                'weight' => 11
            ),
            11 => array(
                'widget' => 'project_task',
                'employee_id' => $employee_id,
                'name' => 'Project task',
                'display' => 0,
                'weight' => 12
            ),
            12 => array(
                'widget' => 'phase',
                'employee_id' => $employee_id,
                'name' => 'Phase plan',
                'display' => 0,
                'weight' => 13
            ),
            13 => array(
                'widget' => 'finance_plus',
                'employee_id' => $employee_id,
                'name' => 'Finance+',
                'display' => 0,
                'weight' => 14
            ),
            14 => array(
                'widget' => 'fy_budget',
                'employee_id' => $employee_id,
                'name' => 'FY Budget',
                'display' => 0,
                'weight' => 15
            ),
            15 => array(
                'widget' => 'finance_two_plus',
                'employee_id' => $employee_id,
                'name' => 'Finance++',
                'display' => 0,
                'weight' => 16
            )
        );
        foreach ($default as $key => $value) {
            $check = $this->YourFormFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'widget' => $value['widget'],
                    'employee_id' => $employee_id,
                )
            ));
            if (empty($check)) {
                $this->YourFormFilter->create();
                $this->YourFormFilter->save($value);
            }
        }
    }

    public function update_your_form_filter() {
        $result = false;
        $this->layout = false;
        $this->loadModels('YourFormFilter');
        if (!empty($this->data)) {
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes')
            );
            $this->YourFormFilter->create();
            if (!empty($this->data['id'])) {
                $this->YourFormFilter->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->YourFormFilter->save(array_merge($this->data, $data))) {
                $result = true;
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->YourFormFilter->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function order_your_form_filter($employee_id = null) {
        $this->loadModels('YourFormFilter');
        if (!empty($this->data)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight != 0) {
                    $this->YourFormFilter->id = $id;
                    $this->YourFormFilter->saveField('weight', $weight);
                }
            }
        }
        die;
    }

    public function updateSelectStatus() {
        if (!empty($_POST)) {
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModels('YourFormFilter');
            $check = $this->YourFormFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'widget' => $_POST['widget'],
                    'employee_id' => $employee_id
                )
            ));
            $status = '';
            foreach ($_POST['data'] as $value) {
                if (empty($status)) {
                    $status .= $value;
                } else {
                    $status .= ',' . $value;
                }
            }
            if (!empty($check)) {
                $id = $check['YourFormFilter']['id'];
                $this->YourFormFilter->id = $id;
                $this->YourFormFilter->save(array('status' => $status));
            }
            echo 'DONE!';
        }
        die;
    }

    public function updateDisplayFilter() {
        if (!empty($this->data)) {
            $id = $this->data['id'];
            $display = $this->data['display'];
            $col = $this->data['col'];
            $this->loadModels('YourFormFilter');
            $this->YourFormFilter->id = $id;
            $this->YourFormFilter->save(array($col => $display));
            echo 'DONE!';
        }
        die;
    }

    public function attachment_static($project_id = null) {
        $this->layout = false;
        $link = '';
        $key = isset($_GET['sid']) ? $_GET['sid'] : '';
        if ($key) {
            $info = $this->ApiKey->retrieve($key);
            if (empty($info)) {
                die('Permission denied');
            }
        } else {
            die('Permission denied');
        }
        $link = trim(FILES . 'staticmap.png');
        if (empty($link)) {
            $link = '';
        } else {
            if (!file_exists($link) || !is_file($link)) {
                $link = '';
            }
            $info = pathinfo($link);
            $this->view = 'Media';
            $params = array(
                'id' => !empty($info['basename']) ? $info['basename'] : '',
                'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                'name' => !empty($info['filename']) ? $info['filename'] : '',
                'mimeType' => array(
                    'bmp' => 'image/bmp',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pps' => 'application/vnd.ms-powerpoint',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                ),
                'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
            );
            if (!empty($this->params['url']['download'])) {
                $params['download'] = true;
            }
            $this->set($params);
        }
    }

    private function getDataOfExternalTask($id) {
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectBudgetExternal');
        $externals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('DISTINCT external_id as external'),
            'conditions' => array('ProjectTask.project_id' => $id,
                'ProjectTask.special' => 1
            ),
            'order' => array('ProjectTask.external_id'),
            'group' => array('ProjectTask.external_id')
        ));
        $dataExternals = array();
        foreach ($externals as $_index => $_external) {
            $external = $_external['ProjectTask']['external'];
            $providerName = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'fields' => array('BP.name'),
                'conditions' => array('ProjectBudgetExternal.id' => $external),
                'joins' => array(
                    array(
                        'table' => 'budget_providers',
                        'alias' => 'BP',
                        'type' => 'LEFT',
                        'foreignKey' => 'budget_provider_id',
                        'conditions' => array(
                            'BP.id = ProjectBudgetExternal.budget_provider_id'
                        )
                    )
                )
            ));
            $setYearExternal = array();
            $dataSetsExternal = array();
            $maxValue = 0;
            $minDate = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('MIN(task_start_date) as date'),
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                )
            ));
            $maxDate = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('MAX(task_end_date) as date'),
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                )
            ));
            $minDate = explode('-', $minDate[0][0]['date']);
            $maxDate = explode('-', $maxDate[0][0]['date']);
            $minDate = mktime(0, 0, 0, $minDate[1] - 1, $minDate[2], $minDate[0]);
            $maxDate = mktime(0, 0, 0, $maxDate[1] + 1, $maxDate[2], $maxDate[0]);
            $tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(special_consumed) as consumed', 'task_title', 'SUM(estimated) as planed', 'DATE_FORMAT(task_start_date, "%m-%y") as date', 'DATE_FORMAT(task_start_date, "%Y") as year'),
                'conditions' => array('ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                ),
                'order' => array('ProjectTask.task_start_date'),
                'group' => array('DATE_FORMAT(task_start_date, "%M%Y")')
            ));
            $tasks = Set::combine($tasks, '{n}.0.date', '{n}');
            $sumPlaned = $sumConsumed = $progress = 0;
            while ($minDate <= $maxDate) {
                $key = date("m", $minDate) . '-' . date("y", $minDate);
                if (isset($tasks[$key])) {
                    $_val = $tasks[$key];
                    $setYearExternal[] = $_val[0]['year'];
                    $setNameExternal = $_val['ProjectTask']['task_title'];
                    //progress
                    $sumConsumed += $_val[0]['consumed'];
                    $sumPlaned += $_val[0]['planed'];
                }
                if ($minDate <= time())
                    $dataSetsExternal[] = array('date' => $key, 'planed' => $sumPlaned, 'consumed' => $sumConsumed);
                else
                    $dataSetsExternal[] = array('date' => $key, 'planed' => $sumPlaned);
                $minDate = mktime(0, 0, 0, date("m", $minDate) + 1, date("d", $minDate), date("Y", $minDate));
            }

            if ($sumPlaned == 0 || $sumConsumed == 0)
                $progress = 0;
            else
                $progress = round(($sumConsumed / $sumPlaned), 2) * 100;
            $maxValue = $sumPlaned + round($sumPlaned * 0.1);
            $manDayExternals = $maxValue;
            $setYearExternal = array_unique($setYearExternal);
            $setYearExternal = join("-", $setYearExternal);
            $dataExternals[$external] = array('progressExternal' => $progress, 'dataSetsExternal' => $dataSetsExternal, 'manDayExternals' => $manDayExternals, 'setYearExternal' => $setYearExternal, 'setNameExternal' => $setNameExternal, 'setProviderName' => isset($providerName[0]['BP']['name']) ? $providerName[0]['BP']['name'] : null);
        }
        $this->set(compact('dataExternals'));
    }

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

    private function _dashBoard($project_id) {
        $this->loadModel('Project');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        list($_start, $_end) = $this->_parseParams();
        $staffings = array();
        if ($filterId == 1) {
            $staffings = $this->_staffingEmloyee($project_id);
        }
        $result = $manDays = array();
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $sumConsumed = $sumPlaned = 0;
        if (!empty($staffings)) {
            $datas = $this->_totalStaffing($staffings);
            $_setYear = $years = $estimatedNextMonths = array();
            $consumed = $validated = 0;

            $lastData = count($datas) - 1;
            $i = 0;
            foreach ($datas as $key => $val) {
                if ($i == 0)
                    $startDay = $val['set_date'];
                elseif ($i == $lastData)
                    $endDay = $val['set_date'];
                $i++;
            }
            $minDate = mktime(0, 0, 0, date("m", $startDay) - 1, date("d", $startDay), date("Y", $startDay));
            $maxDate = !empty($endDay) ? mktime(0, 0, 0, date("m", $endDay) + 1, date("d", $endDay), date("Y", $endDay)) : 0;
            while ($minDate < $maxDate) {
                $key = date("m", $minDate) . '/' . date("y", $minDate);
                if (isset($datas[$key])) {
                    $_val = $datas[$key];
                    $setYear[] = date("Y", $minDate);

                    //progress
                    $sumConsumed += $_val['consumed'];
                    $sumPlaned += $_val['validated'];
                }
                if ($minDate <= time()) {
                    $dataSets[] = array('date' => $key, 'validated' => $sumPlaned, 'consumed' => $sumConsumed);
                } else {
                    $dataSets[] = array('date' => $key, 'validated' => $sumPlaned);
                }
                $minDate = mktime(0, 0, 0, date("m", $minDate) + 1, date("d", $minDate), date("Y", $minDate));
            }
        }
        $manDays = $sumPlaned + round($sumPlaned * 0.1, 2);
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('project_name')
        ));
        $_setYear = !empty($_setYear) ? array_unique($_setYear) : array();
        asort($_setYear);
        $setYear = !empty($_setYear) ? implode('-', $_setYear) : '';
        $display = isset($this->params['url']['ControlDisplay']) ? $this->params['url']['ControlDisplay'] : range(1, 3);

        $countLine = 0;
        if (!empty($dataSets))
            $countLine = count($dataSets);
        $this->set(compact('dataSets', 'filterId', 'project_id', 'manDays', 'projectName', 'setYear', 'display', 'countLine'));
    }

    /**
     * Staffing of employee
     *
     *
     * @return array()
     * @access private
     */
    private function _staffingEmloyee($project_id = null) {
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
        if (!empty($getDatas)) {
            foreach ($getDatas as $getData) {
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if (!isset($datas[$dx['model_id']][$dx['date']]['validated'])) {
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if (!isset($datas[$dx['model_id']][$dx['date']]['consumed'])) {
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if (!empty($datas)) {
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
        if (!empty($datas['999999999'])) {
            $notAffected = array(
                'Employee' => array(
                    'id' => '999999999',
                    'fullname' => 'Not Affected'
                )
            );
            $employees[] = $notAffected;
        }
        $staffings = array();
        foreach ($employees as $key => $employee) {
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
    private function _totalStaffing($staffings) {
        $staffings = !empty($staffings) ? Set::combine($staffings, '{n}.id', '{n}.data') : array();
        $datas = array();
        if (!empty($staffings)) {
            foreach ($staffings as $staffing) {
                foreach ($staffing as $time => $values) {
                    $_time = date('m/y', $values['date']);
                    $datas[$time]['date'] = $_time;
                    $datas[$time]['set_date'] = $time;
                    if (!isset($datas[$time]['consumed'])) {
                        $datas[$time]['consumed'] = 0;
                    }
                    $datas[$time]['consumed'] += $values['consumed'];
                    if (!isset($datas[$time]['validated'])) {
                        $datas[$time]['validated'] = 0;
                    }
                    $datas[$time]['validated'] += $values['validated'];
                    if (!isset($datas[$time]['remains'])) {
                        $datas[$time]['remains'] = 0;
                    }
                    $datas[$time]['remains'] += $values['remains'];
                }
            }
        }
        ksort($datas);
        $results = array();
        if (!empty($datas)) {
            foreach ($datas as $key => $data) {
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
    private function _resetRemainSystem($datas = array()) {
        $currentDate = strtotime(date('01-m-Y', time()));
        $totalEstimated = $_total = $monthValidates = array();
        foreach ($datas as $id => $data) {
            $works = $cons = $remains = 0;
            foreach ($data as $time => $value) {
                if ($time < $currentDate) {
                    $works += $value['validated'];
                    $cons += $value['consumed'];
                    $remains += $value['remains'];
                    $_total[$id]['validated'] = $works;
                    $_total[$id]['consumed'] = $cons;
                    $_total[$id]['remain'] = $remains;
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    if (!empty($value['validated'])) {
                        $monthValidates[$id][] = $time;
                    }
                }
            }
        }
        if (!empty($monthValidates)) {
            foreach ($monthValidates as $id => $monthValidate) {
                if (in_array($currentDate, $monthValidate)) {
                    //do nothing
                } else {
                    $monthValidates[$id][] = $currentDate;
                }
            }
        }
        foreach ($datas as $id => $data) {
            foreach ($data as $time => $value) {
                if ($time < $currentDate) {
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    $count = !empty($monthValidates[$id]) ? count($monthValidates[$id]) : 1;
                    $remain = !empty($_total[$id]) ? $_total[$id]['remain'] : 0;
                    if ($count == 0) {
                        $remainFirst = 0;
                        $remainSecond = 0;
                    } else {
                        $getRemain = $this->Lib->caculateGlobal($remain, $count, false);
                        $remainFirst = $getRemain['original'];
                        $remainSecond = $getRemain['remainder'];
                    }
                    if (!empty($value['validated']) || $time == $currentDate) {
                        $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst;
                        if (!empty($monthValidates[$id]) && $time == max($monthValidates[$id])) {
                            $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst + $remainSecond;
                        }
                    } else {
                        if (!empty($datas[$id][$currentDate])) {
                            if ($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0) {
                                $datas[$id][$currentDate]['remains'] = $remainFirst + $remainSecond;
                            }
                        }
                    }
                }
            }
        }
        return $datas;
    }

    protected function _getPathLivrables($project_id) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'livrable' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
    function index_plus($cate = 1, $projectL = '') {
        $this->loadModels('Menu', 'ProjectGlobalView', 'ProjectAmr', 'LogSystem', 'CompanyEmployeeReference', 'ProjectAmrProgram', 'Employee');
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];

        if (!empty($this->params['form']['typeProject'])) {
            $list_prog_id = $this->params['form']['typeProject'];
            foreach ($list_prog_id as $key => $val) {
                if (empty($val))
                    unset($list_prog_id[$key]);
            }
            if (!empty($list_prog_id)) {
                $list_prog_id = array_merge(array('p'), array_values($list_prog_id));
                $cate = implode('-', $list_prog_id);
            }
        }
        $prog_id = explode('-', $cate);
        $prog = array();
        $filter = array();
        if (!empty($prog_id) && $prog_id[0] == 'p') {
            unset($prog_id[0]);
            $prog = array_values($prog_id);
            $cate = 1;
        } else if (!empty($prog_id) && $prog_id[0] == 'filter') {
            $cate = 1;
            $filter = $prog_id;
        } else
            $cate = $prog_id[0];
        // debug($filter); exit;
        $cate = isset($cate) ? $cate : 2;
        $_type = $cate == 5 ? array(1, 2) : ($cate == 6 ? 2 : $cate);
        /**
         * Lay config see all project from admin
         */
        $appstatus = $cate;
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employee_id,
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'project_manager_id' => $employee_id
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }

        $cond = array(
            'Project.company_id' => $company_id,
            'Project.category' => $_type
        );
        $join_cond = $join_avancement = array();
        if (!empty($prog)) {
            $cond['project_amr_program_id'] = array_values($prog);
        }

        if (!empty($filter) && $filter[0] == 'filter') {
            foreach ($filter as $value) {
                $value = explode('_', $value);
                if (!empty($value) && $value[0] == 'prog')
                    $cond['Project.project_amr_program_id'] = $value[1];
                if (!empty($value) && $value[0] == 'weather')
                    $join_cond['ProjectAmr.weather'] = $value[1];
                if (!empty($value) && $value[0] == 'pm')
                    $cond['Project.project_manager_id'] = $value[1];
                if (!empty($value) && $value[0] == 'avancement')
                    $join_avancement['LogSystem.description LIKE'] = '%' . $value[1] . '%';
                if (!empty($value) && $value[0] == 'pname')
                    $cond['Project.project_name LIKE'] = '%' . $value[1] . '%';
            }
        }
        $order = '';
        if (!empty($projectL)) {
            $cond['id'] = explode('-', $projectL);
        } else {
            if (!empty($listProjectIds)) {
                $cond['id'] = $listProjectIds;
            }
        }
        // order theo id o project list index
        // debug( count($cond['id'])); exit;
        if (!empty($cond['id']) && count($cond['id']) > 1) {
            $order = "FIELD(id," . implode(", ", $cond['id']) . ")";
        }
        extract(array_merge(array('cate' => null), $this->params['url']));

        if (!empty($cond) && !empty($join_cond['ProjectAmr.weather'])) {
            $_listProjects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    $cond,
                ),
                'joins' => array(
                    array(
                        'table' => 'project_amrs',
                        'alias' => 'ProjectAmr',
                        'conditions' => array(
                            'Project.id = ProjectAmr.project_id',
                            $join_cond,
                        ),
                    )
                ),
            ));
        } else if (!empty($cond) && !empty($join_avancement)) {
            $_listProjects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    $cond,
                ),
                'joins' => array(
                    array(
                        'table' => 'log_systems',
                        'alias' => 'LogSystem',
                        'conditions' => array(
                            'Project.id = LogSystem.model_id',
                            $join_avancement,
                        ),
                    )
                ),
            ));
        } else {
            $_listProjects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    $cond,
                ),
                'order' => $order,
            ));
        }

        $i = 0;
        $listProjects = array();
        $globals = array();
        $listProjectIds = !empty($_listProjects) ? Set::combine($_listProjects, '{n}.Project.id', '{n}.Project.id') : array();

        $listProgram = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        'Project.id' => $listProjectIds,
                    )
                )
            ),
            'fields' => array('Project.id', 'ProjectAmrProgram.amr_program')
        ));

        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
        $listProjectManager = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.id', 'Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listProjectManager = !empty($listProjectManager) ? Set::combine($listProjectManager, '{n}.Project.id', '{n}.Employee') : array();

        $listPMFields = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listPMFields = !empty($listPMFields) ? Set::combine($listPMFields, '{n}.Employee.id', '{n}.Employee') : array();
        foreach ($_listProjects as $key => $_listProject) {
            if ($key % 4 == 0 && $key != 0) {
                $i++;
            }
            $listProjects[$i][] = $_listProject;
            // tinh global.
            $project_id = $_listProject['Project']['id'];
            $projectGlobalView = $this->ProjectGlobalView->find("first", array(
                'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
                "conditions" => array('project_id' => $project_id)));

            if ($projectGlobalView) {
                $noFileExists = false;
                $link = trim($this->_getPathGlobal($project_id, 'global')
                        . $projectGlobalView['ProjectGlobalView']['attachment']);
                if (!file_exists($link) || !is_file($link)) {
                    $noFileExists = true;
                }
                $globals[$project_id] = array(
                    'global' => $projectGlobalView,
                    'file' => $noFileExists
                );
            }
        }

        $listWeather = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $listProjectIds
            ),
            'fields' => array('project_id', 'weather', 'rank')
        ));
        $projectGloView = $this->ProjectGlobalView->find("all", array(
            'recursive' => -1, 'fields' => array('id', 'project_id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $listProjectIds)));
        $projectGloView = !empty($projectGloView) ? Set::combine($projectGloView, '{n}.ProjectGlobalView.project_id', '{n}.ProjectGlobalView') : array();
        $listRank = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.rank') : array();
        $listWeather = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.weather') : array();
        $screenDefaults = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
        ));
        $screenDashboard = '';
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company_id,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));

        $screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();

        $ACLController = 'projects_preview';
        $ACLAction = 'edit';
        if (!empty($screenDefaults)) {
            $ACLController = $screenDefaults['Menu']['controllers'];
            $ACLAction = $screenDefaults['Menu']['functions'];
        }
        $this->loadModel('HistoryFilter');
        if (!empty($projectL) && in_array($cate, array(1, 2, 3, 4, 5))) {
            $_url = $cate . '/' . $projectL;
            $url_rollback = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url'
                )
            ));
            if (!empty($url_rollback)) {
                $this->HistoryFilter->id = $url_rollback['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $_url));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url',
                    'params' => $_url
                ));
            }
        }
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'path' => 'Project_grid/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        //log system for done & todo
        if (!empty($listProjectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $listProjectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        $profileName = array();
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $this->employee_info['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        if (!empty($employInfors['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName', 'screenDashboard'));
        $this->set(compact('projectGloView', 'personDefault', 'appstatus', 'cate', 'logs', 'listProjects', 'ACLController', 'ACLAction', 'globals', 'listRank', 'listWeather', 'savePosition', 'listProgram', 'listProgramFields', 'listProjectManager', 'listPMFields', 'prog'));
    }

    function ajax($project_id = null, $view = 'ajax') {
        $this->loadModels('BudgetSetting', 'ProjectRisk', 'ProjectRiskOccurrence', 'ProjectRiskSeverity', 'ProjectIssueStatus', 'ProjectIssueSeverity', 'ProjectIssue', 'ProjectLocalView', 'Dependency', 'ProjectDependency', 'ProjectGlobalView', 'ProjectIssueColor');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));

        $this->loadModels('Menu', 'YourFormFilter', 'Profile', 'ProjectPhaseStatus', 'LogSystem', 'ProjectAcceptance', 'ProjectAcceptanceType', 'ProjectAmr', 'ProjectEmployeeManager');
        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project'
            ),
            'fields' => array('widget_id', 'display', 'name_eng', 'name_fre')
        ));

        $langCode = Configure::read('Config.langCode');
        $_fields = ($langCode == 'fr') ? '{n}.Menu.name_fre' : '{n}.Menu.name_eng';
        $tranMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', $_fields) : array();
        $showMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', '{n}.Menu.display') : array();
        if (!isset($showMenu['finance_two_plus'])) {
            $showMenu['finance_two_plus'] = 0;
        }
        // filter your form.
        $this->autoInsertFilter();
        $yourFormFilter = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('widget', 'display'),
            'order' => array('weight')
        ));
        $id = $project_id;
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $page = 'Details';
        $curentPage = 'your_form_plus';
        if ($yourFormFilter['your_form'] == 1) {
            $this->getDataForYourForm($page, $curentPage, $id);
        } else {
            $project_name = $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
            $this->set('project_name', $project_name);
        }
        $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
        $projectRisks = $this->ProjectRisk->find("all", array(
            'fields' => array('id', 'project_risk', 'risk_assign_to', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_close_date', 'actions_manage_risk', 'file_attachement', 'format', 'project_issue_status_id', 'weight'),
            'recursive' => -1, "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $riskSeverities = $this->ProjectRiskSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $riskOccurrences = $this->ProjectRiskOccurrence->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_occurrence'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueStatus = $this->ProjectIssueStatus->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_status'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueSeverities = $this->ProjectIssueSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $projectIssues = $this->ProjectIssue->find("all", array(
            'recursive' => -1,
            "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $listProjectIssueId = !empty($projectIssues) ? Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}.ProjectIssue.id') : array();
        // project issue employee reference.
        $this->loadModels('Employee', 'ProfitCenter', 'ProjectIssueEmployeeRefer');
        $listEmployeeAssign = $this->Employee->find('list', array(
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
        $listProfitAssign = $this->ProfitCenter->find('list', array(
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
            if ($dx['is_profit_center'] == 0) {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listEmployeeAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listEmployeeAssign[$dx['reference_id']];
                }
            } else {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listProfitAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listProfitAssign[$dx['reference_id']];
                }
            }
        }
        $projectLocalView = $this->ProjectLocalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)
        ));
        if ($projectLocalView) {
            $link = trim($this->_getPathGlobal($id)
                    . $projectLocalView['ProjectLocalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExists = true;
            }
        }
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)
        ));
        //dependency
        $deps = $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name', 'color')
        ));
        $dependencies = $colors = array();
        foreach ($deps as $dep) {
            $dependencies[$dep['Dependency']['id']] = $dep['Dependency']['name'];
            $colors[$dep['Dependency']['id']] = $dep['Dependency']['color'];
        }
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => array(1, 2),
                'id !=' => $id
            ),
            'fields' => array('id', 'project_name')
        ));
        $dataDependency = $this->ProjectDependency->retrieve($id);
        $list = !empty($data) ? array_unique(Set::extract($data, '{n}.ProjectDependency.target_id')) : array();
        $count = $this->ProjectDependency->countChildren($list);
        // history.
        $this->loadModels('HistoryFilter', 'ProjectPhase');
        $employee_info = $this->employee_info;
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'dependency_' . $id,
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if (!empty($history)) {
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        // global view
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)));

        if ($projectGlobalView) {
            $link = trim($this->_getPathGlobal($id, 'global')
                    . $projectGlobalView['ProjectGlobalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExistsGlobal = true;
            }
        }
        // project budget internal.
        $getDataProjectTasks = $this->Project->dataFromProjectTask($id, $company_id);
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $getDataProjectTasks['workload'] -= $varE;
        $budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $this->loadModels('ProjectBudgetExternal', 'BudgetProvider', 'BudgetType', 'BudgetFunder', 'ProjectFinancePlusDate', 'ProjectPhasePlan', 'ProjectPhaseStatus', 'ProjectPart');
        $externalBudgets = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('SUM(man_day) AS exBudget')
        ));
        $externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
        $engagedErro = 0;
        if (!empty($projectName['Project']['activity_id'])) {
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $emp = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $_id => $val) {
                    $reals = !empty($emp[$_id]['tjm']) ? (float) str_replace(',', '.', $emp[$_id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
        }
        // budget external
        $budgetProviders = $this->BudgetProvider->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $_budgetTypes = $this->BudgetType->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'capex')
        ));
        $budgetTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.name') : array();
        $capexTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.capex') : array();
        $budgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $idOfExternals = !empty($budgetExternals) ? Set::classicExtract($budgetExternals, '{n}.ProjectBudgetExternal.id') : array();
        $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $company_id), null, null, '');
        $funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $taskExternals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1, 'external_id' => $idOfExternals),
            'group' => array('external_id'),
            'fields' => array('SUM(special_consumed) as consumed', 'SUM(estimated) as maday', 'external_id')
        ));
        $taskExternals = !empty($taskExternals) ? Set::combine($taskExternals, '{n}.ProjectTask.external_id', '{n}.0') : array();
        // finance+

        if (isset($showMenu['finance_plus']) && $showMenu['finance_plus'] && $yourFormFilter['finance_plus'] == 1) {
            $invStart = $invEnd = $fonStart = $fonEnd = time();
            $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id)
            ));
            if (!empty($getSaveHistory)) {
                $invStart = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_start'] : time();
                $invEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_end'] : time();
                $fonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_start'] : time();
                $fonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_end'] : time();
            }
            /**
             * Lay cac finance+
             */
            $finances = $this->ProjectFinancePlus->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id')
            ));
            /**
             * Finance detail
             */
            $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
            ));
            $dataFinan = $totals = array();
            foreach ($_financeDetails as $key => $value) {
                $dx = $value['ProjectFinancePlusDetail'];
                if ($dx['type'] == 'fon') {
                    $dataFinan['fon'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['fon_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['fon'][$dx['model']])) {
                        $totals['fon'][$dx['model']] = 0;
                    }
                    $totals['fon'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                } else {
                    $dataFinan['inv'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['inv_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['inv'][$dx['model']])) {
                        $totals['inv'][$dx['model']] = 0;
                    }
                    $totals['inv'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                }
            }
        }
        //project phase plan
        $_projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('id', 'project_part_id', 'predecessor', 'planed_duration', 'project_planed_phase_id', 'project_phase_status_id',
                'phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date', 'weight', 'ref1', 'ref2', 'ref3', 'ref4', 'profile_id', 'progress'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id', 'color'
                )),
            'order' => array('weight' => 'asc'),
            'conditions' => array('project_id' => $id)
        ));
        //auto format order
        $count = count($_projectPhasePlans);
        for ($i = 0; $i < $count; $i++) {
            $newWeight = $i + 1;
            if ($_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] != "$newWeight") {
                $_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] = $newWeight;
                $this->ProjectPhasePlan->id = $_projectPhasePlans[$i]['ProjectPhasePlan']['id'];
                $this->ProjectPhasePlan->save(array(
                    'weight' => intval($newWeight)), array('validate' => false, 'callbacks' => false));
            }
        }
        //END
        $_listIDPhasePlans = !empty($_projectPhasePlans) ? Set::classicExtract($_projectPhasePlans, '{n}.ProjectPhasePlan.id') : array();
        $countTaskOfPhases = array();
        if (!empty($_listIDPhasePlans)) {
            $_projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_planed_phase_id' => $_listIDPhasePlans, 'project_id' => $id),
                'fields' => array('id', 'project_planed_phase_id', 'COUNT(project_planed_phase_id) AS Total'),
                'group' => array('project_planed_phase_id')
            ));
            $countTaskOfPhases = !empty($_projectTasks) ? Set::combine($_projectTasks, '{n}.ProjectTask.project_planed_phase_id', '{n}.0.Total') : array();
        }
        $_projectPhases = $this->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
            ),
            'fields' => array('id', 'name', 'profile_id', 'activated'),
            'order' => array('phase_order' => 'asc')
        ));
        $x = $projectPhases1 = array();
        foreach ($_projectPhases as $phase) {
            $p = $phase['ProjectPhase'];
            $projectPhases1[$p['id']] = $p['name'];
            if ($p['activated']) {
                $x[$p['id']] = $p['name'];
            }
        }
        $_projectPhases = $x;
        unset($x);
        $_phaseDefaults = !empty($_projectPhases) ? Set::combine($_projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.profile_id') : array();
        $_projectParts = $this->ProjectPart->find('list', array(
            'conditions' => array(
                'project_id' => $id
        )));
        $_projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'phase_status')
        ));
        $this->loadModels('Profile');
        $_profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $currentPhase = $this->ProjectPhasePlan->Project->find('first', array(
            'fields' => array('Project.id'), 'contain' => array(
                'ProjectPhase' => array('name'),
            ), 'conditions' => array('Project.id' => $id)));
        $this->set('currentPhase', isset($currentPhase['ProjectPhase']['id']) ? (int) $currentPhase['ProjectPhase']['id'] : 0);
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $this->set(compact('_projectPhases', 'projectPhases1', '_projectPhaseStatuses', '_projectPhasePlans', '_projectParts', 'countTaskOfPhases', '_profiles', 'activateProfile', '_phaseDefaults', 'manuallyAchievement'));
        // gantt planning.
        $this->helpers = array_merge($this->helpers, array(
            'GanttVs', 'GanttV2'));
        $mileStone = $this->Project->find("first", array(
            'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC')),
            "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'),
            'conditions' => array('Project.id' => $id)
        ));
        $_view = true;
        $listProjectToActivities[$projectName['Project']['id']] = $projectName['Project']['activity_id'];
        $this->_parseNew($listProjectToActivities, $projectName, $_view);
        // project task
        if ($showMenu['task'] && $yourFormFilter['project_task'] == 1) {
            $orders = $this->requestAction('/admin_task/getTaskSettings');
            $i18n = $this->requestAction('/translations/getByLang/Project_Task');
            $projectTaskForTasks = $this->requestAction('/project_tasks/getDataTasks/' . $id);
            $settingP = $this->requestAction('/project_settings/get');
            $checkP = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $id),
                'fields' => 'is_freeze'
            ));
        }
        $this->loadModels('ProjectIssueSeverity');
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
        $colorDefault = !empty($colorDefault['ProjectIssueColor']['color']) ? $colorDefault['ProjectIssueColor']['color'] : '#004380';
        $colorSeverities = $this->ProjectIssueSeverity->find('list', array(
            'fields' => array('id', 'color'),
            'conditions' => array(
                'company_id' => $company_id
            ),
            'recursive' => -1
        ));
        // end
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id', 'name')
        ));
        $projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'phase_status')
        ));
        $commentKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ProjectAmr',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $todoKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ToDo',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $doneKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'Done',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $acceptancesKpi = $this->ProjectAcceptance->find('all', array(
            'fields' => '*',
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $typeAcceptance = $this->ProjectAcceptanceType->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $budgetKpi = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenusKpi = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        // Internal costs
        $internals = $internalDetails = array();
        if (!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_internals'])) {
            $remainExter = $varE - $externalConsumeds;
            $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
            $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectName['Project']['id']),
                'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average')
            ));
            $engagedErro = 0;
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $employees = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $_id => $val) {
                    $reals = !empty($employees[$_id]['tjm']) ? (float) str_replace(',', '.', $employees[$_id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
            $count = $budgetEuro = $budgetManDay = $average = 0;
            if (!empty($projectBudgetInternals)) {
                foreach ($projectBudgetInternals as $key => $projectBudgetInternal) {
                    $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                    $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                    if (empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average'])) {
                        $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] = 0;
                    }
                    $average += $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                    $internalDetails[$key]['budget_euro'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                    $budgetEuro += ($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average']);
                    $budgetManDay += !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                    $count++;
                }
            }
            $average = ($count == 0) ? 0 : round($average / $count, 2);
            $internals = array(
                'budgetEuro' => $budgetEuro,
                'budgetManDay' => $budgetManDay,
                'forecastedManDay' => $getDataProjectTasks['consumed'] + $getDataProjectTasks['remain'],
                'forecastEuro' => $engagedErro + ($remainInter * $average),
                'varEuro' => ($budgetEuro != 0) ? round(((($engagedErro + ($remainInter * $average)) / $budgetEuro) - 1) * 100, 2) : 0,
                'consumedManday' => $getDataProjectTasks['consumed'],
                'consumedEuro' => $engagedErro,
                'remainManday' => $getDataProjectTasks['remain'],
                'remainEuro' => $remainInter * $average,
            );
        }
        // External costs
        $externals = array();
        if (!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_externals'])) {
            $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectName['Project']['id']),
                'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
            ));
            if (!empty($projectBudgetExternals)) {
                $exBudgetManDay = $exBudgetEuro = $exRemainEuro = $exForecastEuro = $exOrderEuro = 0;
                foreach ($projectBudgetExternals as $key => $projectBudgetExternal) {
                    $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                    $exBudgetEuro += !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                    $exRemainEuro += !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $order = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                    $exOrderEuro += $order;
                    $remain = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $exForecastEuro += $order + $remain;
                    $exBudgetManDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                }
                $exVarEuro = ($exBudgetEuro == 0 ) ? -100 : round(((($exOrderEuro + $exRemainEuro) / $exBudgetEuro) - 1) * 100, 2);
                $externals['BudgetManDay'] = $exBudgetManDay;
                $externals['BudgetEuro'] = $exBudgetEuro;
                $externals['ForecastEuro'] = $exForecastEuro;
                $externals['VarEuro'] = $exVarEuro;
                $externals['ConsumedEuro'] = $exOrderEuro;
                $externals['RemainEuro'] = $exRemainEuro;
            }
        }
        $this->getDataOfExternalTask($projectName['Project']['id']);
        $_projectTaskKpi = $this->_getPorjectTask($projectName['Project']['id']);
        $progression = $_projectTaskKpi['progression'];
        $this->_dashBoard($projectName['Project']['id']);
        //filter status.
        $_statusFilters = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'widget' => array('issue', 'risk', 'project_task', 'finance_plus', 'global_view', 'finance_two_plus')
            ),
            'fields' => array('widget', 'status')
        ));
        $statusFilters = array();
        foreach ($_statusFilters as $key => $statusFilter) {
            if (!empty($statusFilter)) {
                $statusFilters[$key] = explode(',', $statusFilter);
            }
        }
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
        $projectBackup = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_manager_id')
        ));
        $listCurPhase = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_phase_id')
        ));
        $breakpage = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'page_break' => 1
            ),
            'fields' => array('id', 'widget')
        ));
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ? $displayParst['Menu']['display'] : 0;
        $this->set(compact('budgetKpi', 'settingMenusKpi', 'internals', 'externals', 'progression', 'projectBackup', 'listCurPhase', 'breakpage'));
        $this->set(compact('showMenu', 'tranMenu', 'visionTaskExports', 'yourFormFilter', 'totals', 'manuallyAchievement', 'activateProfile', 'profiles', 'projectPhaseStatuses', 'commentKpi', 'todoKpi', 'doneKpi', 'statusFilters', 'displayParst', 'acceptancesKpi', 'typeAcceptance'));
        $this->set(compact('taskExternals', 'budgetTypes', 'capexTypes', 'budgetProviders', 'budgetExternals', 'dataFinan', 'finances', 'mileStone', 'orders', 'i18n', 'projectTaskForTasks', 'settingP', 'checkP', 'listReference', 'listColor', 'colorDefault', 'colorSeverities'));
        $this->set(compact('dependencies', 'colors', 'listProjects', 'dataDependency', 'project_id', 'list', 'count', 'history', 'projectGlobalView', 'noFileExistsGlobal', 'getDataProjectTasks', 'budgets', 'engagedErro', 'externalBudgets', 'externalConsumeds'));
        $this->set(compact('page', 'budget_settings', 'ProjectArms', 'id', 'projectRisks', 'riskSeverities', 'riskOccurrences', 'issueStatus', 'riskSeverities', 'issueSeverities', 'projectIssues', 'projectLocalView', 'noFileExists', 'projectName'));
        $this->render('ajax');
    }

    public function savePopupPosition() {
        if (!empty($_POST)) {
            $data = json_encode($_POST);
            $this->loadModel('HistoryFilter');
            $employee_id = $this->employee_info['Employee']['id'];
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'Project_grid/popup_position',
                    'employee_id' => $employee_id
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'path' => 'Project_grid/popup_position',
                    'params' => $data
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'Project_grid/popup_position',
                    'employee_id' => $employee_id,
                    'params' => $data
                ));
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }

    public function savePopupPositions() {
        if (!empty($_POST)) {
            $data = json_encode($_POST);
            $this->loadModel('HistoryFilter');
            $employee_id = $this->employee_info['Employee']['id'];
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'projects/popup_position',
                    'employee_id' => $employee_id
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'path' => 'projects/popup_position',
                    'params' => $data
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'projects/popup_position',
                    'employee_id' => $employee_id,
                    'params' => $data
                ));
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }

    public function saveFieldYourForm($project_id) {
		$result = array();
		$message = '';
		if( empty( $_POST['field'])){
			die(json_encode(array(
				'result' => 'failed',
				'data' => array(),
				'message' => __('Submit failed, please correct data before submit.', true)
			)));
		}
		if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		$update_your_form = ($this->employee_info['Role']['name'] == 'admin');
		$can_update = true;
		/*
		Ticket #1096
		A PM who has the right to modify the project, should be able to modify the % progress.
		*/
		if( $_POST['field'] == 'manual_progress'){
			$update_your_form = $can_update = true;
		}elseif( $this->employee_info['Role']['name'] == 'pm'){
			if( !empty( $this->employee_info['Employee']['profile_account']) ){
				$update_your_form = $this->ProfileProjectManagerDetail->find('count', array(
					'recursive' => -1,
					'conditions' => array(
						'widget_id' => 'your_form',
						'model_id' => $this->employee_info['Employee']['profile_account'],
						'read_write' => 1
					),
				));
			}else{
				$update_your_form = $this->employee_info['Employee']['update_your_form'];
			}
			// Ticket #1082 Viet Nguyen
			// Get pm can update your_form fields.
			
			if (!empty($_POST) && !empty($this->companyConfigs['can_manage_your_form_field'])){
				$this->loadModel('ProjectManagerUpdateField');
				$pm_can_update = $this->ProjectManagerUpdateField->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'field' => $_POST['field']
					),
					'fields' => array('id', 'employee_id'),
				));
				if(!empty($pm_can_update) && !in_array($this->employee_info['Employee']['id'], $pm_can_update)){
					$can_update = false;
				}
			}
		}
        if (!empty($_POST) && $update_your_form && $can_update) {
            $field = isset($_POST['field']) ? $_POST['field'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $text = !empty( $_POST['label']) ? $_POST['label'] : '';
            // debug($value); exit;
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if ( (strpos($field, 'date_') !== false || strpos($field, '_date') !== false) && strpos($field, 'date_yy') === false && strpos($field, 'date_mm_yy') === false ) {
                $value = $str_utility->convertToSQLDate($value);
            }elseif(strpos($field, 'date_yy') !== false){
				$value = $str_utility->convertToYYYY($value);
			}elseif(strpos($field, 'date_mm_yy') !== false){
				$value = $str_utility->convertToMMYY($value);
			}elseif (strpos($field, 'price_') !== false) {
                $value = str_replace(' ', '', $value);
            }
			$datas = array(
				$field => $value,
                'last_modified' => time(),
                'update_by_employee' => $this->employee_info['Employee']['fullname']
			);
			/* 
				Viet Nguyen Update: 19/02/2020
				Refresh data khi update:
				 1) project_type -> empty data: project_sub_type, project_sub_sub_type
				 2) project_sub_type -> empty data: project_sub_sub_type
			*/
			$refesh_field = array();
			
			if($field == 'project_type_id'){
				$datas['project_sub_type_id'] = NULL;
				$datas['project_sub_sub_type_id'] = NULL;
			}
			if($field == 'project_sub_type_id'){
				$datas['project_sub_sub_type_id'] = NULL;
			}
            $this->Project->id = $project_id;
			if( isset($datas['category']) && $datas['category'] == 3){
				$old_cate = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $project_id
					),
					'fields' => array('id','category')
				));
				$old_cate = $old_cate[$project_id];
				if( $old_cate == 1){
					$datas['updated_ip_arch'] = time();
				}
			}
            $result = $this->Project->save($datas);
            $this->loadModels('Activity', 'ProjectEmployeeManager', 'ProjectPhaseCurrent', 'ProjectListMultiple', 'ProjectAmrProgram','ProjectAmrSubProgram');
			// Huynh Edit 2020-06-02: Can save value = 0 (Zero)
			if( !isset($result['Project'][$field])){
				$result = array();
				$message = !empty( $text) ? sprintf(__('%s could not be saved. Please, try again.',true), $text) : __('Cannot be saved', true);
			}
            $this->loadModels('Activity', 'ProjectEmployeeManager', 'ProjectPhaseCurrent', 'ProjectListMultiple', 'ProjectAmrProgram');
            $activity_id = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                )
            ));
            // $activity_id = !empty($activity_id) ? $activity_id['Activity']['id'] : 0;

            if (!empty($result) && !empty($activity_id)) {
                if (strpos($field, 'project_amr_program_id') !== false) {
                    $getFamilyActivity = $this->ProjectAmrProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $value,
                            'company_id' => $this->employee_info['Company']['id'],
                        ),
                        'fields' => array('family_id'),
                    ));

                    $family_id = !empty($getFamilyActivity) ? $getFamilyActivity['ProjectAmrProgram']['family_id'] : 0;

                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'family_id' => $family_id,
                    ));
                }
				//Kiem tra field thay doi co phai sub_program.Updated by QuanNV 13/06/2019
				if (strpos($field, 'project_amr_sub_program_id') !== false) {
                    $getSubFamily = $this->ProjectAmrSubProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $value,
                        ),
                        'fields' => array('sub_family_id'),
                    ));
                    $sub_family_id = !empty($getSubFamily) ? $getSubFamily['ProjectAmrSubProgram']['sub_family_id'] : 0;
					//Thay doi gia tri sub_family_id moi sau khi thay doi sub_program.Updated by QuanNV 13/06/2019
                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'subfamily_id' => $sub_family_id,
                    ));
                }
                if (strpos($field, 'project_name') !== false) {
                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'name' => $value,
                        'long_name' => $value,
                        'short_name' => $value,
                    ));
                }
            }
        }
		if( !empty($result)) $message = __('Saved', true);
        die(json_encode(array(
			'result' => !empty($result) ? 'success' : 'failed',
			'data' => $result,
			'message' => $message
		)));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function saveFieldUploadYourForm($project_id = null) {
        if (!empty($_POST)) {
            $field = $_POST['field'];
            // $value = $_POST['value'];
            // debug($field); exit;
            if (empty($_FILES)) {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            } else {
                if ($this->_checkRole(true, $project_id)) {
                    $path = $this->_getPath($project_id);
                    App::import('Core', 'Folder');
                    new Folder($path, true, 0777);
                    if (file_exists($path)) {
                        $this->MultiFileUpload->encode_filename = false;
                        $this->MultiFileUpload->uploadpath = $path;
                        $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
                        $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                        $attachment = $this->MultiFileUpload->upload();
                    } else {
                        $attachment = "";
                        $this->session_commit()->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                                , true), $path), 'error');
                    }

                    if (!empty($attachment)) {
                        $attachment = $attachment['attachment']['attachment'];
                        $this->ProjectGlobalView->create();
                        $last = $this->ProjectGlobalView->find('first', array(
                            'recursive' => -1,
                            'fields' => array('id', 'attachment'),
                            'conditions' => array('project_id' => $project_id)));
                        if ($last) {
                            $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                            @unlink($path . $last['ProjectGlobalView']['attachment']);
                        }
                        if ($this->ProjectGlobalView->save(array(
                                    'project_id' => $project_id,
                                    'attachment' => $attachment,
                                    'is_file' => $this->data['ProjectGlobalView']['is_file']
                                ))) {
                            if ($this->MultiFileUpload->otherServer == true) {
                                $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $project_id);
                            }
                            $this->Session->setFlash(__('Saved', true), 'success');
                        } else {
                            @unlink($path . $attachment);
                            $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                            if ($this->MultiFileUpload->otherServer == true) {
                                $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $project_id);
                            }
                        }
                    } else {
                        $this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
                    }
                }
            }
            $this->redirect(array('action' => 'index', $project_id));
        } else {
            $last = $this->ProjectGlobalView->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'attachment', 'is_file'),
                'conditions' => array('project_id' => $project_id)));
            if ($last) {
                $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                @unlink($path . $last['ProjectGlobalView']['attachment']);
            }
            $is_https = 0;
            $pos = strpos($this->data['ProjectGlobalView']['attachment'], 'https');
            if ($pos !== false) {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('https://', '', $this->data['ProjectGlobalView']['attachment']);
                $is_https = 1;
            } else {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('http://', '', $this->data['ProjectGlobalView']['attachment']);
                //$is_https = 0;
            }
            if ($this->ProjectGlobalView->save(array(
                        'project_id' => $project_id,
                        'attachment' => $this->data['ProjectGlobalView']['attachment'],
                        'is_file' => $this->data['ProjectGlobalView']['is_file'],
                        'is_https' => $is_https
                    ))) {
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                @unlink($path . $attachment);
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect(array('action' => 'index', $project_id));
        }
    }

    public function saveFieldYourFormEditer($project_id) {
		if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		$update_your_form = true;
		$can_update = true;
		if( $this->employee_info['Role']['name'] == 'pm'){
			// Hien tai PM profile khong co "update_your_form" nen k check duoc
			if( !empty( $this->employee_info['Employee']['profile_account'])  || empty($this->employee_info['Employee']['update_your_form'])) $update_your_form = false;
			// Ticket #1082 Viet Nguyen
			// Get pm can update your_form fields.
			
			if (!empty($_POST) && !empty($this->companyConfigs['can_manage_your_form_field'])){
				$this->loadModel('ProjectManagerUpdateField');
				$pm_can_update = $this->ProjectManagerUpdateField->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'field' => $_POST['field']
					),
					'fields' => array('id', 'employee_id'),
				));
				if(!empty($pm_can_update) && !in_array($this->employee_info['Employee']['id'], $pm_can_update)){
					$can_update = false;
				}
			}
		}
        if (!empty($_POST) && $update_your_form && $can_update) {
            $field = $_POST['field'];
            $value = $_POST['value'];
            if ($field == 'ProjectEditor1') {
                $field = 'editor_1';
            } else if ($field == 'ProjectEditor2') {
                $field = 'editor_2';
            } else if ($field == 'ProjectEditor3') {
                $field = 'editor_3';
            } else if ($field == 'ProjectEditor4') {
                $field = 'editor_4';
            } else {
                $field = 'editor_5';
            }
            $this->Project->id = $project_id;
            $this->Project->save(array(
                $field => $value,
                'last_modified' => time(),
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            ));
            echo "Done";
            die;
        }
        echo "Error";
        die;
    }

    public function saveFieldYourFormPM($project_id) {
		if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		$update_your_form = true;
		$can_update = true;
		if( $this->employee_info['Role']['name'] == 'pm'){
			// Hien tai PM profile khong co "update_your_form" nen k check duoc
			if( !empty( $this->employee_info['Employee']['profile_account'])  || empty($this->employee_info['Employee']['update_your_form'])) $update_your_form = false;
			
			// Ticket #1082 Viet Nguyen
			// Get pm can update your_form fields.
			if (!empty($_POST) && !empty($this->companyConfigs['can_manage_your_form_field'])){
				$this->loadModel('ProjectManagerUpdateField');
				$yf_field = $_POST['field'];
				switch( $_POST['field']){
					case 'project_employee_manager':
						$yf_field = 'project_manager_id';
						break;
					case 'chief_business_list':
						$yf_field = 'chief_business_id';
						break;
					case 'technical_manager_list':
						$yf_field = 'technical_manager_id';
						break;
					case 'functional_leader_list':
						$yf_field = 'functional_leader_id';
						break;
					case 'uat_manager_list':
						$yf_field = 'uat_manager_id';
						break;
				}
				if (strpos($yf_field, 'project_list_multi_') !== false) {
					$yf_field = str_replace('project_list_multi_', 'list_muti_', $yf_field);
				}
				
				$pm_can_update = $this->ProjectManagerUpdateField->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->employee_info['Company']['id'],
						'field' => $yf_field,
					),
					'fields' => array('id', 'employee_id'),
				));
				if(!empty($pm_can_update) && !in_array($this->employee_info['Employee']['id'], $pm_can_update)){
					$can_update = false;
				}
			}
		}
        if (!empty($_POST) && $update_your_form && $can_update) {
            $field = $_POST['field'];
            $values = !empty($_POST['value']) ? $_POST['value'] : '';
            $this->loadModels('Activity', 'ProjectEmployeeManager', 'ProjectPhaseCurrent', 'ProjectListMultiple');
            $activity_id = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                )
            ));
            $activity_id = !empty($activity_id) ? $activity_id['Activity']['id'] : 0;
            if ($field == 'project_employee_manager') {
                $type = 'PM';
                $is_profit_center = 0;
                $project_manager_id = !empty($values) ? $values[0] : 0;
                $this->Project->id = $project_id;
                $this->Project->save(array(
                    'project_manager_id' => $project_manager_id,
                    'last_modified' => time(),
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                ));
            }
            if ($field == 'chief_business_list') {
                $type = 'CB';
                $is_profit_center = 0;
            }
            if ($field == 'technical_manager_list') {
                $type = 'TM';
                $is_profit_center = 0;
				$this->Project->id = $project_id;
                $this->Project->save(array(
                    'technical_manager_id' => '',
                    'last_modified' => time(),
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                ));
            }
            if ($field == 'functional_leader_list') {
                $type = 'FL';
                $is_profit_center = 0;
            }
            if ($field == 'uat_manager_list') {
                $type = 'UM';
                $is_profit_center = 0;
            }
            if ($field == 'read_access') {
                $type = 'RA';
                $is_profit_center = 0;
                $dataRefers = array(
                    'project_id' => $project_id,
                    'type' => $type,
                );
                $checkDatas = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_manager_id', 'id')
                ));
                foreach ($values as $value) {
                    $value = explode('-', $value);
                    $is_profit_center = isset($value[1]) ? $value[1] : '';
                    if (in_array($value[0], array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectEmployeeManager->create();
                        $this->ProjectEmployeeManager->save(array(
                            'project_id' => $project_id,
                            'project_manager_id' => $value[0],
                            // 'is_backup' => 0,
                            'activity_id' => $activity_id,
                            'type' => $type,
                            'is_profit_center' => $is_profit_center
                        ));
                    }
                    unset($checkDatas[$value[0]]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectEmployeeManager->id = $va;
                        $this->ProjectEmployeeManager->delete();
                    }
                }
            } else if ($field == 'project_phase_id') {
                $dataRefers = array(
                    'project_id' => $project_id,
                );
                $checkDatas = $this->ProjectPhaseCurrent->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_phase_id', 'id')
                ));
                foreach ($values as $value) {
                    if (in_array($value, array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectPhaseCurrent->create();
                        $this->ProjectPhaseCurrent->save(array(
                            'project_id' => $project_id,
                            'project_phase_id' => $value,
                        ));
                    }
                    unset($checkDatas[$value]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectPhaseCurrent->id = $va;
                        $this->ProjectPhaseCurrent->delete();
                    }
                }
            } else if (strpos($field, 'project_list_multi_') !== false) {
                $dataRefers = array(
                    'project_id' => $project_id,
                    'key' => $field
                );
                $checkDatas = $this->ProjectListMultiple->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_dataset_id', 'id')
                ));
                foreach ($values as $value) {
                    if (in_array($value, array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectListMultiple->create();
                        $this->ProjectListMultiple->save(array(
                            'project_id' => $project_id,
                            'project_dataset_id' => $value,
                            'key' => $field
                        ));
                    }
                    unset($checkDatas[$value]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectListMultiple->id = $va;
                        $this->ProjectListMultiple->delete();
                    }
                }
            } else {

                //// 
                $dataRefers = array(
                    'project_id' => $project_id,
                    'type' => $type,
                );
                $checkDatas = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_manager_id', 'id')
                ));
                if (!empty($values)) {
                    foreach ($values as $value) {
                        if (in_array($value, array_keys($checkDatas))) {
                            
                        } else {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save(array(
                                'project_id' => $project_id,
                                'project_manager_id' => $value,
                                // 'is_backup' => 0,
                                'activity_id' => $activity_id,
                                'type' => $type,
                                'is_profit_center' => $is_profit_center
                            ));
                        }
                        unset($checkDatas[$value]);
                    }
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $idPM =>$va) {
                        $this->ProjectEmployeeManager->deleteAll(array(
							'ProjectEmployeeManager.project_id' => $project_id,
							'ProjectEmployeeManager.project_manager_id' => $idPM,
							'ProjectEmployeeManager.type' => $type,
						), false);
                    }
                }
            }
            echo "Done";
            die;
        }
        echo "Error";
        die;
    }

    public function update() {
        $result = false;
        $this->layout = false;
        $this->loadModel('LogSystem');
        $model = '';
        if (!empty($this->data)) {
            $project_id = $this->data['id'];
			$this->_checkRole(false, $project_id);
            if (!( $this->_checkRole(true, $project_id) ))
                die(0);
			$project = $this->Project->find('list', array(
				'recursive' => -1,
                'conditions' => array(
                    'id' => $project_id
                ),
                'fields' => array('id', 'project_name'),
			));
            $company_id = $this->employee_info['Company']['id'];
            $name = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
            if (isset($this->data['field']))
                $this->data['model'] = $this->data['field'];
            if (isset($this->data['model']))
                $this->data['field'] = $this->data['model'];
			if( empty($this->data['model']) ) {
				$this->data['model'] = 'ProjectAmr';
			}
			switch ($this->data['model']){
				case 'ToDo':
				case 'todo': $this->data['model'] = 'ToDo'; break;
				case 'Done':
				case 'done': $this->data['model'] = 'Done'; break;
				case 'ProjectIssue':
				case 'project_amr_problem_information': 
					$this->data['model'] = 'ProjectIssue';
					break;
				case 'ProjectRisk':
				case 'project_amr_risk_information': 
					$this->data['model'] = 'ProjectRisk'; 
					break;
				case 'Scope':
				case 'Schedule':
				case 'Budget':
				case 'Resources':
				case 'Technical':
					break;
				default: $this->data['model'] = 'ProjectAmr';
			}
			$model = $this->data['model'];
            unset($this->data['id']);
			
			if (in_array($model, array('ProjectAmr', 'ProjectRisk', 'ProjectIssue', 'Done', 'ToDo', 'Scope', 'Schedule', 'Budget', 'Resources', 'Technical'))){
            
				$data = array();
				if (!empty($this->data['logid'])) {
					$log_id = $this->data['logid'];
					$check = $this->LogSystem->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $log_id,
							'model_id' => $project_id
						),
						'fields' => array('model', 'description'),
						'order' => array('id' => 'ASC')
					));
					if( !empty( $check)){
						$this->LogSystem->id = $log_id;
						$save_field = array(
							'description' => $this->data['text'],
							'updated' => time(),
							'employee_id' => $this->employee_info['Employee']['id'],
							'name' => $name,
							'update_by_employee' => $this->employee_info['Employee']['fullname'],
						);
						$data = array_merge($check['LogSystem'], $save_field);
					}
				} else {
					$this->LogSystem->create();
					$data = array(
						'company_id' => $company_id,
						'model' => $model,
						'model_id' => $project_id,
						'name' => $name,
						'description' => $this->data['text'],
						'employee_id' => $this->employee_info['Employee']['id'],
						'update_by_employee' => $this->employee_info['Employee']['fullname']
					);
				}
				$result = $this->LogSystem->save($data);
				if( !empty($result)){
					$data = array();
					$data['project_name'] = $project[$project_id];	
					$logSystems = $this->LogSystem->find('all', array(
						'recursive' => -1,
						'conditions' => array(
							'model_id' => $project_id,
							'model' => $model,
						),
						'limit' => 99,
						'fields' => array('id', 'model_id', 'model', 'description', 'updated', 'update_by_employee', 'name', 'employee_id'),
						'order' => array('updated' => 'DESC')
					));
					$listComments = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
					$i = 0;
					foreach ($listComments as $_comment) {
						$data[] = $_comment;
						// $data[$i++]['updated'] = date('d/m/Y', $_comment['updated']);
					}

                    $this->notifyForComment($project_id, $model, $this->data['text']); // Send notify to users.
					$data['current'] = time();
					die(json_encode($data));
				}
			}
		}    
        die(0);
    }

    function index_preview($project_id = null) {
        $this->loadModels('ProjectMilestone', 'LogSystem', 'Project', 'ProjectEvolution', 'TmpStaffingSystem', 'ProjectAmr', 'ProjectTask', 'Employee', 'ZogMsg', 'ProjectFinancePlusDetail');
        // heading

        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $project_id),
            'fields' => array('project_name', 'start_date', 'end_date')
        ));
        $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($projectName['Project']['end_date']));
        $projectDate = abs(strtotime($projectName['Project']['end_date']) - strtotime($projectName['Project']['start_date']));
        $initDate = 100;
        if ($projectCurrentDate < $projectDate)
            $initDate = floor(($projectCurrentDate / $projectDate) * 100);
        $projectArms = $this->ProjectAmr->find('first', array(
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('weather'))
        );
        // Gant
        $this->_ganttFromPhase($project_id);
        $employee_id = $this->employee_info['Employee']['id'];
        $projectMilestones = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'project_milestone', 'milestone_date', 'validated'),
            'order' => array('weight' => 'ASC')
        ));
        $tmpProjectMilestones = $projectMilestones;
        $projectMilestones = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone') : array();
        $employee_id = $this->employee_info['Employee']['id'];

        // List assign to resources
        $listAssign = $this->_getTeamEmployees($project_id);
        $this->set('listAssign', $listAssign);
        /**
         * Lay tat ca cac log
         */
        $company_id = $this->employee_info['Company']['id'];
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk', 'ToDo', 'Done'),
                'model_id' => $project_id,
                'company_id' => $company_id,
            ),
            'fields' => array('*'),
            'order' => array('updated' => 'DESC')
        ));
        $groupByModels = $listEmployeeLogs = array();
        if (!empty($logSystems)) {
            foreach ($logSystems as $logSystem) {
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
        // Projects
        $projects = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $company_id
            ),
            'fields' => array('id', 'project_objectives', 'project_manager_id')
        ));
        $project = array();
        if (!empty($projects)) {
            foreach ($projects as $key => $value) {
                $project = $value['Project'];
            }
        }
        $this->_dashBoard($project_id);

        // progression
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectEvolution.project_id' => $project_id),
            'fields' => array('SUM(man_day) as totalMD')
        ));
        $_projectTask = $this->_getPorjectTask($project_id);
        $validated = isset($projectEvolutions[0][0]['totalMD']) ? $projectEvolutions[0][0]['totalMD'] : 0;
        $engaged = $_projectTask['engaged'];
        $remain = $_projectTask['remain'];
        $validated = $_projectTask['validated'];
        $variance = $engaged + $remain - $validated;
        $progression = $_projectTask['progression'];

        // staffing progression
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $project_id,
                'NOT' => array(
                    'model_id' => 999999999
                ),
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if (!empty($dataSystems)) {
            foreach ($dataSystems as $dataSystem) {
                $dx = $dataSystem['TmpStaffingSystem'];
                if (!isset($assignEmployees[$dx['project_id']])) {
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
                'company_id' => $company_id
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));

        /**
         * Finance detail
         */
        $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
        ));
        $totals = array();
        if (!empty($_financeDetails)) {
            foreach ($_financeDetails as $financeDetail) {
                $dx = $financeDetail['ProjectFinancePlusDetail'];
                $financeDetails[$dx['project_finance_plus_id']][$dx['model'] . '_' . $dx['year']] = $dx;
                $yearOfFinances[$dx['type']][$dx['year']] = $dx['year'];
                if (empty($totals[$dx['type']][$dx['model']])) {
                    $totals[$dx['type']][$dx['model']] = 0;
                }
                $totals[$dx['type']][$dx['model']] += $dx['value'];
            }
        }
        // debug($totals); exit;
        // debug($_dataSystems); exit;
        $assignProfitCenters = array();
        if (!empty($_dataSystems)) {
            foreach ($_dataSystems as $_dataSystem) {
                $dx = $_dataSystem['TmpStaffingSystem'];
                if (!isset($assignProfitCenters[$dx['project_id']])) {
                    $assignProfitCenters[$dx['project_id']] = 0;
                }
                $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
            }
        }
        $tWorkload = !empty($_projectTask['validated']) ? $_projectTask['validated'] : 0;
        $assgnPc = !empty($assignProfitCenters[$project_id]) ? $assignProfitCenters[$project_id] : 0;
        $assginProfitCenter = ($tWorkload == 0) ? '0' : ((round(($assgnPc / $tWorkload) * 100, 2) > 100) ? '100' : round(($assgnPc / $tWorkload) * 100, 2));
        $assgnEmploy = !empty($assignEmployees[$project_id]) ? $assignEmployees[$project_id] : 0;
        $assgnEmployee = ($tWorkload == 0) ? '0' : ((round(($assgnEmploy / $tWorkload) * 100, 2) > 100) ? '100' : round(($assgnEmploy / $tWorkload) * 100, 2));

        // Zog Messages
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

        // debug($eZogMsgs);
        // debug($zogMsgs); exit;
        $this->set('checkAvatar', $this->checkAvatar());
        $this->set(compact('eZogMsgs', 'projectMilestones', 'employee_id', 'project_id', 'tmpProjectMilestones', 'logSystems', 'project', 'progression', 'validated', 'engaged', 'assgnEmploy', 'tWorkload', 'assgnEmployee', 'projectArms', 'projectName', 'initDate', 'listEmAssign', 'logSystems', 'todos', 'dones', 'zogMsgs', 'totals'));
    }

    /**
     * Function: Get Employees with in current Project
     * @var     : int $project_id
     * @return  : array of Employees
     * @author : BANGVN
     */
    private function _getTeamEmployees($project_id) {
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
        if (!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1) {
            // List all employees followed in this project
            $teams = $this->ProjectTeam->find('all', array(
                'fields' => array('id', 'profit_center_id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
                'conditions' => array('project_id' => $project_id)
            ));
            $project = $this->_getProject($project_id);
            $employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();
            if (!empty($employeeIds)) {
                foreach ($employeeIds as $employeeId) {
                    foreach ($employeeId as $v) {
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
            if (!empty($employees)) {
                $i = 0;
                foreach ($employees as $id => $name) {
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($name) ? $name : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $i++;
                }
            }
            $getEmploy = !empty($rDatas) ? $rDatas : array();
            $profitCenterIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.profit_center_id')) : array();
            if (!empty($profitCenterIds)) {
                foreach ($profitCenterIds as $profitCenterId) {
                    foreach ($profitCenterId as $v) {
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
                if (!empty($employ)) {
                    $employees = array_merge($getEmploy, $employ);
                } else {
                    $employees = $getEmploy;
                }
            } else {
                $employees = $getEmploy;
            }
        } else {
            $employees = $this->ProjectTask->Employee->find('list', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    'OR' => array(
                        array('end_date' => '0000-00-00'),
                        array('end_date IS NULL'),
                        array('end_date >=' => date('Y-m-d')),
                    ),
                    'actif' => 1,
                    'company_id' => $company_id
                ),
                'fields' => array('id', 'fullname')
            ));
            $rDatas = array();
            if (!empty($employees)) {
                $i = 0;
                foreach ($employees as $id => $name) {
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($name) ? $name : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
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
            if (!empty($employ)) {
                $employees = array_merge($employees, $employ);
            }
        }
        return $employees;
    }

    private function _ganttFromPhase($project_id) {
        $this->loadModel('Project');
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
        //set default real time
        $displayplan = isset($this->params['url']['displayplan']) ? (int) $this->params['url']['displayplan'] : 0;
        $displayreal = isset($this->params['url']['displayreal']) ? (int) $this->params['url']['displayreal'] : 1;
        $this->set(compact('projects', 'display', 'displayreal', 'displayplan'));
    }

    public function deleteActivityProjectNull() {
        $this->loadModel('Activity');
        $activity_id = $this->Activity->find('list', array(
            'conditions' => array(
                'project IS NULL'
            ),
            'fields' => array('id'),
        ));
        if (!empty($activity_id))
            $this->Activity->delete($activity_id);
        $this->redirect(array('action' => 'index'));
    }

    public function getNameEmployee($nameEmployee = null) {
        if (!empty($nameEmployee)) {
            $nameEmployee = trim($nameEmployee);
            $name = explode(" ", $nameEmployee);
            if (!empty($name)) {
                $full_name = trim($name[0]) . ' ' . trim($name[1]);
                $name = substr(trim($name[0]), 0, 1) . '' . substr(trim($name[1]), 0, 1) . '_' . $full_name;
            }
        }
        return $name;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload_global($project_id = null) {
        $this->loadModel('ProjectGlobalView');
		$this->_checkRole(true, $project_id);
        if ($this->data['ProjectGlobalView']['is_file']) {
			if($this->data['ProjectGlobalView']['is_file'] == 2){
                if(empty($this->data['ProjectGlobalView']['attachment'])){
                    $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
                }else{
                    $this->ProjectGlobalView->create();
                    $this->ProjectGlobalView->save(array(
                        'project_id' => $project_id,
                        'attachment' => $this->data['ProjectGlobalView']['attachment'],
                        'is_file' => 0,
                        'is_https' => 0,
                    ));

                }
            }else{
				if (empty($_FILES)) {
					$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
				} else {
					$path = $this->_getPathProjectGlobalView($project_id);
					App::import('Core', 'Folder');
					new Folder($path, true, 0777);
					if (file_exists($path)) {
						$this->MultiFileUpload->encode_filename = false;
						$this->MultiFileUpload->uploadpath = $path;
						$this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
						$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
						$attachment = $this->MultiFileUpload->upload();
					} else {
						$attachment = "";
						$this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
												, true), $path), 'error');
					}
					if (!empty($attachment)) {
						$attachment = $attachment['attachment']['attachment'];
						$this->ProjectGlobalView->create();
						$last = $this->ProjectGlobalView->find('first', array(
							'recursive' => -1,
							'fields' => array('id', 'attachment'),
							'conditions' => array('project_id' => $project_id)));
						if ($last) {
							$this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
							@unlink($path . $last['ProjectGlobalView']['attachment']);
						}
						if ($this->ProjectGlobalView->save(array(
									'project_id' => $project_id,
									'attachment' => $attachment,
									'is_file' => $this->data['ProjectGlobalView']['is_file']
								))) {
							if ($this->MultiFileUpload->otherServer == true) {
								$this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $project_id);
							}
							$this->Session->setFlash(__('Saved', true), 'success');
						} else {
							@unlink($path . $attachment);
							$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
							if ($this->MultiFileUpload->otherServer == true) {
								$this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $project_id);
							}
						}
					} else {
						$this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
					}
				}
			}
            $this->redirect(array('action' => 'your_form', $project_id));
        } else {
            $last = $this->ProjectGlobalView->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'attachment', 'is_file'),
                'conditions' => array('project_id' => $project_id)));
            if ($last) {
                $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                @unlink($path . $last['ProjectGlobalView']['attachment']);
            }
            $is_https = 0;
            $pos = strpos($this->data['ProjectGlobalView']['attachment'], 'https');
            if ($pos !== false) {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('https://', '', $this->data['ProjectGlobalView']['attachment']);
                $is_https = 1;
            } else {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('http://', '', $this->data['ProjectGlobalView']['attachment']);
                //$is_https = 0;
            }
            if ($this->ProjectGlobalView->save(array(
                        'project_id' => $project_id,
                        'attachment' => $this->data['ProjectGlobalView']['attachment'],
                        'is_file' => $this->data['ProjectGlobalView']['is_file'],
                        'is_https' => $is_https
                    ))) {
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                @unlink($path . $attachment);
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect(array('action' => 'your_form', $project_id));
        }
    }

    protected function _getPathProjectGlobalView($project_id) {
        $this->loadModel('ProjectGlobalView');
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

    function change_milestone_status($project_id, $milestone_item) {
        return 'true';
        exit;
    }

    function save_project_list_filter() {
        if (!empty($_POST)) {
            extract($_POST);
            $path = rtrim($_POST['path'], '/');
            $params = $_POST['params'];
            $employId = $this->Session->read('Auth.Employee.id');
            $employId = isset($employId) ? $employId : null;
            $last = $this->Employee->HistoryFilter->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'id', 'params'
                ),
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id']
            )));

            $this->Employee->HistoryFilter->create();
            if (!empty($last)) {
                $this->Employee->HistoryFilter->id = $last['HistoryFilter']['id'];
                unset($last);
            }
            if (empty($params)) {
                Configure::write('debug', 0);
                echo json_encode($_params);
                exit();
            }
            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => $params,
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
            echo json_encode(explode('-', $params));
        }
        exit();
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
     * view
     *
     * @return void
     * @access public
     */
    public function update_activated() {
		$result = array();
		$message = '';
		if(!empty($_POST['data'])){
			
			$this->loadModels('Activity', 'ProjectEmployeeManager', 'Project');
			$id = $_POST['data']['id'];
			// kiem tra PM co the change project manager select field
			$pmCanChange = $this->ProjectEmployeeManager->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_manager_id' => $this->employee_info['Employee']['id'],
					'project_id' => $id,
					'type' => 'PM'
				)
			));
			// kiem tra PM o bang project
			$pmOfProject = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_manager_id' => $this->employee_info['Employee']['id'],
					'id' => $id
				)
			));
			$pmCanChange = (!empty($pmCanChange) || !empty($pmOfProject)) ? true : false;
			if($pmCanChange || $this->employee_info['CompanyEmployeeReference']['role_id'] == 2){
				$activaty_id = $_POST['data']['activaty_id'];
				$activated = $_POST['data']['activated'];
				//Ticket #611
				$this->Project->id = $id;
				if($this->Project->saveField('activated', $activated)){
					$result = $_POST['data'];
					$message = 'Data updated.';
				}else{
					$message = 'Data update error';
				}
				
				$this->Activity->id = $activaty_id;
				if($this->Activity->saveField('activated', $activated)){
					$result = $_POST['data'];
					$message = 'Data updated.';
				}else{
					$message = 'Data update error';
				}
			}else{
				$message = 'You do not have permission to access in this function.';
			}
		}else{
			$message = 'Data submit is empty.';
		}
		die(json_encode(array(
			'result' => !empty($result) ? 'success' : 'failed',
			'data' => $result,
			'message' => $message
		)));
    }

}