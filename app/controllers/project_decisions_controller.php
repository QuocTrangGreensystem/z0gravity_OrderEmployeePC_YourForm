<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDecisionsController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $name = 'ProjectDecisions';

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

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index_bak($project_id = null, $view_id = null) {
        if (!$this->ProjectDecision->Project->find('count', array(
                    'recusive' => -1, 'conditions' => array('Project.id' => $project_id)))) {
            $this->Session->setFlash(sprintf(__('The project "#%s" was not found, please try again', true), $project_id), 'error');
            $this->redirect(array('controller' => 'projects', 'action' => 'index'));
        }
        // ---------------------------

        if ($view_id != "")
            $this->set('view_id', $view_id);
        $task_emp_pro = $this->ProjectDecision->Project->find("first", array(
            'recursive' => 0,
            "fields" => array("Project.company_id", "Employee.id", "Employee.first_name", "Employee.last_name"),
            'conditions' => array('Project.id' => $project_id)));
        $companyId = $task_emp_pro['Project']['company_id'];
        $this->ProjectDecision->recursive = 0;
        $this->set("project_id", $project_id);
        $this->set('projectDecisions', $this->ProjectDecision->find('all', array("conditions" => array('ProjectDecision.project_id' => $project_id))));

        $this->set('projectName', $this->ProjectDecision->Project->find("first", array(
                    "fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));

        $this->set('employees', $this->ProjectDecision->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname'))));
        if ($companyId != "") {
            $allEmployees = $this->ProjectDecision->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
            $employeeIds = $this->ProjectDecision->Employee->CompanyEmployeeReference->find('all', array('conditions' => array('CompanyEmployeeReference.company_id' => $companyId)));
            $teams = $this->ProjectDecision->Project->ProjectTeam->find('all', array('conditions' => array('ProjectTeam.project_id' => $project_id)));
            $projectManagers = array($task_emp_pro['Employee']['id'] => $task_emp_pro['Employee']['first_name'] . " " . $task_emp_pro['Employee']['last_name']);
            $allEmployees = $this->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
            $projectManagers += $allEmployees;
        }
        else
            $projectManagers = $this->ProjectDecision->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $this->set('projectManagers', $projectManagers);
        $project = $this->ProjectDecision->Project->find('first', array('conditions' => array('Project.id' => $project_id)));
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        $project['Project']['start_date'] = $str_utility->convertToVNDate($project['Project']['start_date']);
        $project['Project']['end_date'] = $str_utility->convertToVNDate($project['Project']['end_date']);
        $project['Project']['planed_end_date'] = $str_utility->convertToVNDate($project['Project']['planed_end_date']);
        $this->set('project', $project);
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null, $download = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('decision');
        $projectName = $this->viewVars['projectName'];

        $employees = $this->ProjectDecision->Employee->CompanyEmployeeReference->find('all', array(
            'order' => array('Employee.last_name' => 'asc'),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $projectName['Project']['company_id'])));
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));

        $this->ProjectDecision->Behaviors->attach('Containable');
        $this->ProjectDecision->cacheQueries = true;

        $projectDecisions = $this->ProjectDecision->find("all", array(
            'fields' => array('*'),
            'order' => array('weight' => 'ASC'),
            'recursive' => -1, "conditions" => array('project_id' => $project_id)
        ));

        $this->set(compact('projectName', 'project_id', 'projectDecisions', 'employees'));
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectDecision->id = $id;
                $this->ProjectDecision->save(array(
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
            $this->ProjectDecision->create();
            if (!empty($this->data['id'])) {
                $this->ProjectDecision->id = $this->data['id'];
            }
            $data = array();
            foreach (array('project_decision_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectDecision->convertTime($this->data[$key]);
                }
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectDecision->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('The Decision has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Decision could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectDecision->id;
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
        $conditions = array('ProjectDecision.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectDecision.id'] = $data;
            }
        }

        $this->ProjectDecision->Behaviors->attach('Containable');
        $this->ProjectDecision->cacheQueries = true;

        $projectDecisions = $this->ProjectDecision->find("all", array(
            'fields' => array('id', 'project_decision', 'project_decision_explanation', 'project_decision_maker', 'project_decision_date'),
            'contain' => array(
                'Employee' => array(
                    'id', 'first_name', 'last_name'
                )
            ),
            "conditions" => $conditions));
        $projectDecisions = Set::combine($projectDecisions, '{n}.ProjectDecision.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectDecisions[$id])) {
                    unset($data[$id]);
                    unset($projectDecisions[$id]);
                    continue;
                }
                $data[$id] = $projectDecisions[$id];
            }
            $projectDecisions = $data;
            unset($data);
        }
        $this->set(compact('projectDecisions'));
        $this->layout = '';
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project decision', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectDecision', $this->ProjectDecision->read(null, $id));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectDecision->create();
            if ($this->ProjectDecision->save($this->data)) {
                $this->Session->setFlash(__('The project decision has been saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The project decision could not be saved. Please, try again.', true), 'error');
            }
        }
        $projects = $this->ProjectDecision->Project->find('list');
        $employees = $this->ProjectDecision->Employee->find('list');
        $this->set(compact('projects', 'employees'));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project decision', true), 'error');
            $this->redirect(array('action' => 'index', $this->data["ProjectDecision"]["project_id"]));
        }

        if (!empty($this->data)) {
            $this->data["ProjectDecision"]["project_decision_date"] = $str_utility->convertToSQLDate($this->data["ProjectDecision"]["project_decision_date"]);

            if ($this->ProjectDecision->save($this->data)) {
                $this->Session->setFlash(sprintf(__('The project decision %s has been saved', true), '<b>' . $this->data["ProjectDecision"]["project_decision"] . '</b>'), 'success');
                $this->redirect(array('action' => 'index', $this->data["ProjectDecision"]["project_id"]));
            } else {
                $this->Session->setFlash(__('The project decision could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('action' => 'index', $this->data["ProjectDecision"]["project_id"]));
            }
        }

        if (empty($this->data)) {
            $this->data = $this->ProjectDecision->read(null, $id);
            $this->redirect(array('action' => 'index', $this->data["ProjectDecision"]["project_id"]));
        }
        $projects = $this->ProjectDecision->Project->find('list');
        $employees = $this->ProjectDecision->Employee->find('list');
        $this->set(compact('projects', 'employees'));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project decision', true), 'error');
            $this->redirect(array('action' => 'index/', $project_id));
        }
		$last = $this->ProjectDecision->find('first', array(
			'recursive' => -1,
			'fields' => array('project_id','file_attachement'),
			'conditions' => array('ProjectDecision.id' => $id)));
		$project_id = !empty( $last) ? $last['ProjectEvolution']['project_id'] : 0;
        if ($this->_checkRole(false, $project_id)) {
            if ($this->ProjectDecision->delete($id)) {
                @unlink(trim($this->_getPath($last['ProjectDecision']['project_id'])
                        . $last['ProjectDecision']['file_attachement']));
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index/', $project_id));
            }
            $this->Session->setFlash(__('Project decision was not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'index/', $project_id));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function check_decision_of_project_edit() {
        $project_id = $_POST['project_id'];
        $project_decision_name = $_POST['project_decision_name'];
        $decision_to_edit = $_POST['decision_to_edit'];
        $this->layout = "ajax";
        $check = $this->ProjectDecision->find("count", array("conditions" => array(
                "ProjectDecision.project_id" => $project_id,
                "ProjectDecision.project_decision" => $project_decision_name,
                "ProjectDecision.id <>" => $decision_to_edit,
                )));
        echo $check;
        exit;
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function check_decision_of_project() {
        $project_id = $_POST['project_id'];
        $project_decision_name = $_POST['project_decision_name'];
        $this->layout = "ajax";
        $check = $this->ProjectDecision->find("count", array("conditions" => array(
                "ProjectDecision.project_id" => $project_id,
                "ProjectDecision.project_decision" => $project_decision_name,
                )));
        echo $check;
        exit;
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function loadTextarea($project_decision_id = null) {
        $this->autoRender = false;
        if ($project_decision_id != "") {
            $record = $this->ProjectDecision->find('first', array('conditions' => array('ProjectDecision.id' => $project_decision_id)));
            if (!empty($record)) {
                echo $record['ProjectDecision']['project_decision_explanation'];
            } else {
                echo "";
            }
        }
        else
            echo "";
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
        $this->set('projectDecisions', $this->paginate());
        $this->set('projectName', $this->ProjectDecision->Project->find("first", array("fields" => array("Project.project_name"),
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
            $projectDecisions = $this->ProjectDecision->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectDecision.id' => $this->data['Upload']['id'])));
            if ($projectDecisions) {
                $project_id = $projectDecisions['ProjectDecision']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectDecision->id = $this->data['Upload']['id'];
                $last = $this->ProjectDecision->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->ProjectDecision->id)));
                if ($last && $last['ProjectDecision']['file_attachement']) {
                    unlink($path . $last['ProjectDecision']['file_attachement']);
                }
                $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                if ($this->ProjectDecision->save(array(
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
                    $this->ProjectDecision->id = $this->data['Upload']['id'];
                    $last = $this->ProjectDecision->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->ProjectDecision->id)));
                    if ($last && $last['ProjectDecision']['file_attachement']) {
                        unlink($path . $last['ProjectDecision']['file_attachement']);
                    }
                    if ($this->ProjectDecision->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_decisions/index/' . $project_id);
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
        $last = $this->ProjectDecision->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id'),
            'conditions' => array('ProjectDecision.id' => $id)));
        $error = true;
        if ($last && $last['ProjectDecision']['project_id']) {
            $path = trim($this->_getPath($last['ProjectDecision']['project_id'])
                    . $last['ProjectDecision']['file_attachement']);
            $attachment = $last['ProjectDecision']['file_attachement'];
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
                $this->ProjectDecision->id = $id;
                $this->ProjectDecision->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectDecision']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_decisions/index/' . $last['ProjectDecision']['project_id']);
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
        $path = FILES . 'projects' . DS . 'decision' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

}
?>
