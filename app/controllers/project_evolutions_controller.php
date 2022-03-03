<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectEvolutionsController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectEvolution', 'ProjectTeam');

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

    function index_bak($project_id = null, $view_id = null) {
        if (!$this->ProjectEvolution->Project->find('count', array(
                    'recusive' => -1, 'conditions' => array('Project.id' => $project_id)))) {
            $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $project_id), 'error');
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
        // ---------------------------

        if ($view_id != "")
            $this->set('view_id', $view_id);
        $this->ProjectEvolution->recursive = 0;
        $this->set("project_id", $project_id);
        $this->set('projectEvolutions', $this->ProjectEvolution->find('all', array('conditions' => array('ProjectEvolution.project_id' => $project_id))));
        $projects = $this->ProjectEvolution->Project->find('list');
        $projectEvolutionTypes = $this->ProjectEvolution->ProjectEvolutionType->find('list');
        $this->set('projectName', $this->ProjectEvolution->Project->find("first", array(
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
        $project_detail = $this->ProjectEvolution->Project->find("first", array(
            'recursive' => 0,
            "fields" => array(
                "Project.company_id", "Employee.id", "Employee.first_name", "Employee.last_name", "Project.start_date", "Project.end_date", "Project.planed_end_date"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $project_detail['Project']['company_id'];
        $project_teams = $this->ProjectTeam->find('all', array(
            "fields" => array("ProjectTeam.employee_id", "ProjectTeam.id"),
            "conditions" => array("ProjectTeam.project_id" => $project_id)));
        $employees_of_company = $this->ProjectEvolution->Employee->CompanyEmployeeReference->find('all', array(
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array($project_detail['Employee']['id'] => $project_detail['Employee']['first_name'] . " " . $project_detail['Employee']['last_name']);
//        foreach ($project_teams as $project_team) {
//            foreach ($project_team['ProjectFunctionEmployeeRefer'] as $project_tea) {
//                foreach ($employees_of_company as $employee) {
//                    if ($project_tea['employee_id'] == $employee["Employee"]["id"])
//                        $projectManagers[$project_tea['employee_id']] = $employee["Employee"]["fullname"];
//                }
//            }
//        }

        $allEmployees = $this->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $projectManagers += $allEmployees;
        $this->set('projectEvolutionTypes_', $this->ProjectEvolution->ProjectEvolutionType->find('list', array('fields' => array('ProjectEvolutionType.id',
                        'ProjectEvolutionType.project_type_evolution'),
                    'conditions' => array('ProjectEvolutionType.company_id' => $company_id))));
        $this->set('projectEvolutionImpacts', $this->ProjectEvolution->ProjectEvolutionImpactRefer->ProjectEvolutionImpact->find("list", array('fields' => array(
                        "ProjectEvolutionImpact.id", "ProjectEvolutionImpact.evolution_impact"),
                    'conditions' => array('ProjectEvolutionImpact.company_id' => $company_id))));
        $this->set('projectEvolutionImpacts_all', $this->ProjectEvolution->ProjectEvolutionImpactRefer->ProjectEvolutionImpact->find("list", array('fields' => array(
                        "ProjectEvolutionImpact.id", "ProjectEvolutionImpact.evolution_impact"))));

        $this->set('projectEvolutionImpactRefers', $this->ProjectEvolution->ProjectEvolutionImpactRefer->find("all", array('conditions' =>
                    array("ProjectEvolutionImpactRefer.project_id" => $project_id))));
        $this->set(compact('projects', 'projectEvolutionTypes', 'projectManagers', 'project_detail'));
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('evolution');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectEvolution->Behaviors->attach('Containable');
        $this->ProjectEvolution->cacheQueries = true;

        $projectEvolutions = $this->ProjectEvolution->find("all", array(
            'fields' => array(
                'id',
                'project_evolution',
                'project_id',
                'project_evolution_type_id',
                'evolution_applicant',
                'evolution_date_validated',
                'evolution_validator',
                'supplementary_budget',
                'man_day',
                'file_attachement',
                'format',
                'phase_id',
                'progress',
                'due_date',
                'weight'
            ),
            'order' => array('weight' => 'ASC'),
            'contain' => array(
                'ProjectEvolutionImpactRefer' => array('fields' => array('project_evolution_impact_id'))
            ), "conditions" => array('project_id' => $project_id)));

        $evolutionTypes = $this->ProjectEvolution->ProjectEvolutionType->find('list', array(
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'project_type_evolution')));
        $projectEvolutionImpacts = $this->ProjectEvolution->ProjectEvolutionImpactRefer->ProjectEvolutionImpact->find('list', array(
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'evolution_impact')));

        $employees = $this->ProjectEvolution->Employee->CompanyEmployeeReference->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $projectName['Project']['company_id'])));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));

        $sumManDay = $sumBudget = 0;
        foreach($projectEvolutions as $projectEvolution){
            $sumBudget += $projectEvolution['ProjectEvolution']['supplementary_budget'];
            $sumManDay += $projectEvolution['ProjectEvolution']['man_day'];
        }
        $this->loadModel('ProjectPhase');
        $phaseLoads = $this->ProjectPhase->find('list',array('recursive'=>-1,'conditions'=>array('ProjectPhase.company_id' => $projectName['Project']['company_id'])));
        $this->loadModel('Project');
        $projectList = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id'],
                'id !=' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $employee_info = $this->employee_info;
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
        $this->set(compact('projectList', 'phaseLoads','projectName', 'projectEvolutionImpacts', 'evolutionTypes', 'employees', 'project_id', 'projectEvolutions', 'sumBudget', 'sumManDay','budget_settings', 'employee_info'));
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectEvolution->id = $id;
                $this->ProjectEvolution->save(array(
                    'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $this->ProjectEvolution->create();
            if (!empty($this->data['id'])) {
                $this->ProjectEvolution->id = $this->data['id'];
            }
            $data = array();
            foreach (array('evolution_date_validated') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectEvolution->convertTime($this->data[$key]);
                }
                if (!empty($this->data['due_date'])) {
                    $data['due_date'] = $this->ProjectEvolution->convertTime($this->data['due_date']);
                }
            }
            if ($this->_checkRole(true)) {
                $projectName = $this->viewVars['projectName'];
                unset($this->data['id']);
                if ($this->ProjectEvolution->save(array_merge(array_diff_key($this->data, array('evolution_impact' => '')), $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('The Evolution has been saved.', true), 'success');
                    if (!empty($this->data['evolution_impact'])) {
                        $saved = $this->ProjectEvolution->ProjectEvolutionImpactRefer->find('all', array(
                            'fields' => array('id', 'project_id', 'project_evolution_impact_id', 'project_evolution_id'),
                            'conditions' => array(
                                'project_evolution_id' => $this->ProjectEvolution->id
                            ),
                            'recursive' => -1));
                        $saved = Set::combine($saved, '{n}.ProjectEvolutionImpactRefer.project_evolution_impact_id', '{n}.ProjectEvolutionImpactRefer');
                        foreach ($this->data['evolution_impact'] as $impact) {
                            $this->ProjectEvolution->ProjectEvolutionImpactRefer->create();
                            $data = array(
                                'project_evolution_impact_id' => $impact,
                                'project_id' => $projectName['Project']['id'],
                                'project_evolution_id' => $this->ProjectEvolution->id
                            );
                            $last = isset($saved[$impact]) ? $saved[$impact] : null;
                            if ($last) {
                                $data = array_merge($last, $data);
                                $this->ProjectEvolution->ProjectEvolutionImpactRefer->id = $data['id'];
                                unset($data['id']);
                            }
                            $this->ProjectEvolution->ProjectEvolutionImpactRefer->save($data);
                            unset($saved[$impact]);
                        }
                        foreach ($saved as $_save) {
                            $this->ProjectEvolution->ProjectEvolutionImpactRefer->delete($_save['id']);
                        }
                    }
                } else {
                    $this->Session->setFlash(__('The Evolution could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectEvolution->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $projectName = $this->viewVars['projectName'];
        $conditions = array('ProjectEvolution.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectEvolution.id'] = $data;
            }
        }

        $this->ProjectEvolution->Behaviors->attach('Containable');
        $this->ProjectEvolution->cacheQueries = true;
        $projectEvolutions = $this->ProjectEvolution->find("all", array(
            'recursive' => 10,
            'fields' => array('id', 'id', 'project_evolution', 'project_id', 'project_evolution_type_id', 'evolution_applicant', 'evolution_date_validated', 'evolution_validator', 'supplementary_budget', 'man_day'),
            'contain' => array(
                'ProjectEvolutionType' => array(
                    'id', 'project_type_evolution'
                ),
                'ProjectEvolutionImpactRefer' => array(
                    'id', 'ProjectEvolutionImpact' => array(
                        'evolution_impact'
                    )
                )
            ), "conditions" => $conditions));

        $employees = $this->ProjectEvolution->Employee->CompanyEmployeeReference->find('all', array(
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $projectName['Project']['company_id'])));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));

        $projectEvolutions = Set::combine($projectEvolutions, '{n}.ProjectEvolution.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectEvolutions[$id])) {
                    unset($data[$id]);
                    unset($projectEvolutions[$id]);
                    continue;
                }
                $data[$id] = $projectEvolutions[$id];
            }
            $projectEvolutions = $data;
            unset($data);
        }
        $this->set(compact('projectEvolutions', 'projectName', 'employees'));
        $this->layout = '';
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project evolution', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectEvolution', $this->ProjectEvolution->read(null, $id));
    }

    function add() {
        if (!empty($this->data)) {
            $this->ProjectEvolution->create();
            if ($this->ProjectEvolution->save($this->data)) {
                // $this->Session->setFlash(__('The project evolution has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The project evolution could not be saved. Please, try again.', true));
            }
        }
        $projects = $this->ProjectEvolution->Project->find('list');
        $projectEvolutionTypes = $this->ProjectEvolution->ProjectEvolutionType->find('list');
        $this->set(compact('projects', 'projectEvolutionTypes'));
    }

    function edit($id = null) {
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project evolution', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectEvolution"]["project_id"]));
        }
        if (!empty($this->data)) {
            $this->data["ProjectEvolution"]["evolution_date_validated"] = $str_utility->convertToSQLDate($this->data["ProjectEvolution"]["evolution_date_validated"]);

            if ($this->ProjectEvolution->save($this->data)) {
                if (empty($this->data['ProjectEvolution']['id'])) {
                    $project_evolution_id = $this->ProjectEvolution->getLastInsertID(); // get last insert id
                } else {
                    $project_evolution_id = $this->data['ProjectEvolution']['id'];
                    $data_evolution_impacts = "";
                    if (!empty($this->params['form']['ProjectEvolutionProjectEvolutionImpactId'])) {
                        $data_evolution_impacts = $this->params["form"]["ProjectEvolutionProjectEvolutionImpactId"];
                    }
                    if ($data_evolution_impacts != "") {
                        $this->ProjectEvolution->ProjectEvolutionImpactRefer->saveEvolutionImpact($data_evolution_impacts, $project_evolution_id, $this->data["ProjectEvolution"]["project_id"]);
                    }
                }
                $this->Session->setFlash(sprintf(__('The project evolution %s has been saved', true), '<b>' . $this->data["ProjectEvolution"]["project_evolution"] . '</b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectEvolution"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project evolution could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('action' => 'index', $this->data["ProjectEvolution"]["project_id"]));
            }
        } else {
            $this->Session->setFlash(__('Invalid project evolution', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectEvolution"]["project_id"]));
        }
        /*       if (empty($this->data)) {
          $this->data = $this->ProjectEvolution->read(null, $id);
          } */
        /*       $projects = $this->ProjectEvolution->Project->find('list');
          $projectEvolutionTypes = $this->ProjectEvolution->ProjectEvolutionType->find('list');
          $this->set(compact('projects', 'projectEvolutionTypes'));
         *
         */
    }

    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project evolution', true));
            $this->redirect(array('action' => 'index/', $project_id));
        }
        $last = $this->ProjectEvolution->find('first', array(
            'recursive' => -1,
            'fields' => array('project_id','file_attachement'),
            'conditions' => array('ProjectEvolution.id' => $id)));
		$project_id = !empty( $last) ? $last['ProjectEvolution']['project_id'] : 0;
        if ($this->_checkRole(false, $project_id)) {
			if ($this->ProjectEvolution->delete($id)) {
				@unlink(trim($this->_getPath($last['ProjectEvolution']['project_id'])
						. $last['ProjectEvolution']['file_attachement']));
				$this->Session->setFlash(__('Deleted', true), 'success');
				$this->redirect(array('action' => 'index', $project_id));
			}
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index/', $project_id));
    }

    function getevolution($evolution = null) {
        $this->autoRender = false;
        if ($evolution != "") {
            $retr = $this->ProjectEvolution->find('count', array('conditions' => array('ProjectEvolution.project_evolution' => $evolution)));
            echo $retr;
        }
    }

    function check_project_evolution() {
        $project_id = $_POST['project_id'];
        $evolution = $_POST['evolution'];
        $this->layout = 'ajax';
        $check = $this->ProjectEvolution->find('count', array('conditions' => array('ProjectEvolution.project_id' => $project_id,
                'ProjectEvolution.project_evolution' => $evolution)));
        echo $check;
        exit;
    }

    function check_project_evolution_edit() {
        $project_id = $_POST['project_id'];
        $evolution = $_POST['evolution'];
        $evolution_edit = $_POST['evolution_edit'];
        $this->layout = "ajax";
        $check = $this->ProjectEvolution->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectEvolution.project_id' => $project_id,
                'ProjectEvolution.project_evolution' => $evolution,
                'ProjectEvolution.id <>' => $evolution_edit)));
        echo $check;
        exit;
    }

    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $this->set('columns', $this->name_columna);
        $this->ProjectEvolution->recursive = 0;
        $this->paginate = array("conditions" => array('Project.id' => $project_id));
        $this->set('projectEvolutions', $this->paginate('ProjectEvolution'));
        $projects = $this->ProjectEvolution->Project->find('list');
        $projectEvolutionTypes = $this->ProjectEvolution->ProjectEvolutionType->find('list');
        $this->set('projectName', $this->ProjectEvolution->Project->find("first", array(
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
        $company_id = $this->ProjectEvolution->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $company_id['Project']['company_id'];
        $allEmployees = $this->ProjectEvolution->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $employeeIds = $this->ProjectEvolution->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array();
        foreach ($employeeIds as $key => $value) {
            foreach ($allEmployees as $key2 => $value2) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $value2;
                    break;
                }
            }
        }
        $this->set('projectEvolutionTypes_', $this->ProjectEvolution->ProjectEvolutionType->find('list', array('fields' => array('ProjectEvolutionType.id', 'ProjectEvolutionType.project_type_evolution'),
                    'conditions' => array('ProjectEvolutionType.company_id' => $company_id))));
        $this->set('projectEvolutionImpacts', $this->ProjectEvolution->ProjectEvolutionImpactRefer->ProjectEvolutionImpact->find("list", array(
                    'fields' => array("ProjectEvolutionImpact.id", "ProjectEvolutionImpact.evolution_impact"),
                    'conditions' => array('ProjectEvolutionImpact.company_id' => $company_id))));

        $this->set('projectEvolutionImpactRefers', $this->ProjectEvolution->ProjectEvolutionImpactRefer->find("all", array('conditions' =>
                    array("ProjectEvolutionImpactRefer.project_id" => $project_id))));
        $this->set(compact('projects', 'projectEvolutionTypes', 'projectManagers'));
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
            $projectEvolutions = $this->ProjectEvolution->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectEvolution.id' => $this->data['Upload']['id'])));
            if ($projectEvolutions) {
                $project_id = $projectEvolutions['ProjectEvolution']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectEvolution->id = $this->data['Upload']['id'];
                $last = $this->ProjectEvolution->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->ProjectEvolution->id)));
                if ($last && $last['ProjectEvolution']['file_attachement']) {
                    unlink($path . $last['ProjectEvolution']['file_attachement']);
                }
                $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                if ($this->ProjectEvolution->save(array(
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
                    $this->ProjectEvolution->id = $this->data['Upload']['id'];
                    $last = $this->ProjectEvolution->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->ProjectEvolution->id)));
                    if ($last && $last['ProjectEvolution']['file_attachement']) {
                        unlink($path . $last['ProjectEvolution']['file_attachement']);
                    }
                    if ($this->ProjectEvolution->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_evolutions/index/' . $project_id);
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
        $last = $this->ProjectEvolution->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id'),
            'conditions' => array('ProjectEvolution.id' => $id)));
        $error = true;
        if ($last && $last['ProjectEvolution']['project_id']) {
            $path = trim($this->_getPath($last['ProjectEvolution']['project_id'])
                    . $last['ProjectEvolution']['file_attachement']);
            $attachment = $last['ProjectEvolution']['file_attachement'];
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
                $this->ProjectEvolution->id = $id;
                $this->ProjectEvolution->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectEvolution']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_evolutions/index/' . $last['ProjectEvolution']['project_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectEvolution']['project_id'] : __('Unknown', true)));
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
        $path = FILES . 'projects' . DS . 'evolution' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
    public function transfer(){
        if( !isset($this->employee_info['Company']['id']) )
            return $this->redirect('/');
        $cid = $this->employee_info['Company']['id'];
        if( !empty($this->data) ){
            //pr($this->data);
            $eid = $this->data['eid'];
            $pid = $this->data['pid'];
            $new = $this->data['newId'];
            $this->loadModel('ProjectEvolutionImpactRefer');
            $this->loadModel('Project');
            //check project owner
            $check = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => array($pid, $new),
                    'company_id' => $cid
                )
            ));
            if( $check != 2){
                $this->Session->setFlash(__('Invalid project data', true), 'error');
                return $this->redirect(array('action' => 'index', $pid));
            }
            //start transfer
            $this->ProjectEvolutionImpactRefer->updateAll(array(
                'ProjectEvolutionImpactRefer.project_id' => $new
            ), array(
                //'ProjectEvolutionImpactRefer.project_id' => $pid,
                'ProjectEvolutionImpactRefer.project_evolution_id' => $eid
            ));
            $this->ProjectEvolution->save(array(
                'id' => $eid,
                'project_id' => $new
            ));
            $this->Session->setFlash(__('Item has been transfered.', true), 'success');
            $this->redirect(array('action' => 'index', $pid));

        }
        $this->redirect('/');
    }
}
?>
