<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectExpectationsController extends AppController {
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

    function index($project_id){
        $this->getDataForView($project_id, 'expectation_1');
        $this->_checkWriteProfile('expectations');
        $this->Session->write('Expectation.current_page', 'index');
    }
    function view_1($project_id){
        $this->getDataForView($project_id, 'expectation_2');
        $this->Session->write('Expectation.current_page', 'view_1');
        $this->_checkWriteProfile('expectations_2');
        $this->render('index');
    }
    function view_2($project_id){
        $this->getDataForView($project_id, 'expectation_3');
        $this->Session->write('Expectation.current_page', 'view_2');
        $this->_checkWriteProfile('expectations_3');
        $this->render('index');
    }
    function view_3($project_id){
        $this->getDataForView($project_id, 'expectation_4');
        $this->Session->write('Expectation.current_page', 'view_3');
        $this->_checkWriteProfile('expectations_4');
        $this->render('index');
    }
    function view_4($project_id){
        $this->getDataForView($project_id, 'expectation_5');
        $this->Session->write('Expectation.current_page', 'view_4');
        $this->_checkWriteProfile('expectations_5');
        $this->render('index');
    }
    function view_5($project_id){
        $this->getDataForView($project_id, 'expectation_6');
        $this->Session->write('Expectation.current_page', 'view_5');
        $this->_checkWriteProfile('expectations_6');
        $this->render('index');
    }
    function getDataForView($project_id, $exp){
        $this->_checkRole(false, $project_id);
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModels('Project', 'Expectation', 'ExpectationDataset', 'ProjectMilestone', 'ExpectationTranslation', 'Employee');
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $project_id)
        ));
        $langCode = Configure::read('Config.langCode');
        $fields = ($langCode == 'fr') ? 'fre' : 'eng';
        $fieldsets = $this->Expectation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'Expectation.company_id' => $company_id,
                'Expectation.page' => $exp,
                'Expectation.display' => 1
            ),
            'joins' => array(
                array(
                    'table' => 'expectation_translations',
                    'alias' => 'Translation',
                    'type' => 'left',
                    'conditions' => array(
                        'Translation.original_text = Expectation.original_text',
                        'Translation.company_id' => $company_id
                    )
                )
            ),
            'order' => array('Expectation.weight ASC'),
            'fields' => array('Expectation.id', 'Expectation.original_text', 'Translation.eng', 'Translation.fre', 'Expectation.field')
        ));
        $project_expectations = $this->ProjectExpectation->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
        $listProjectExpectation = !empty($project_expectations) ? Set::combine($project_expectations, '{n}.ProjectExpectation.id', '{n}.ProjectExpectation.id') : array();
        $expectation_datasets = $this->ExpectationDataset->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $datasets = array();
        foreach ($expectation_datasets as $expectation_dataset) {
            $dx = $expectation_dataset['ExpectationDataset'];
            $datasets[$dx['dataset_name']][$dx['id']] = ($langCode == 'fr') ? $dx['fre'] : $dx['eng'];
        }
        $projectMilestones = $this->ProjectMilestone->find("all", array("conditions" => array('Project.id' => $project_id)));
        $milestone = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.project_milestone') : array();
        $milestone_date = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.milestone_date') : array();
        $validated = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.validated') : array();
        $this->loadModels('ProjectAlert');
        $alert = $this->ProjectAlert->find('all', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'display' => 1
            ),
            'fields' => array('id', 'alert_name', 'number_of_day'),
            'order' => array('alert_name ASC')
        ));
        $nameAlert = $dateAlert = array();
        foreach ($alert as $key => $value) {
            $dx = $value['ProjectAlert'];
            $nameAlert['alert_' . $dx['id']] = $dx['alert_name'];
            $dateAlert['alert_' . $dx['id']] = $dx['number_of_day'];
        }
        $this->Employee->virtualFields['emId'] = 'CONCAT("0-", `id`)';
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('emId', 'fullname')
        ));
        //
        $this->loadModels('ExpectationColor', 'ProfitCenter', 'ProjectExpectationEmployeeRefer');
        $this->ProfitCenter->virtualFields['pcId'] = 'CONCAT("1-", `id`)';
        $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
        $listProfit = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('pcId', 'pcName')
        ));
        $listEmployeeAndProfit = $listEmployee;
        foreach ($listProfit as $key => $value) {
            $listEmployeeAndProfit[$key] = $value;
        }
        $listColor = $this->ExpectationColor->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'color', 'key', 'default')
        ));
        $_listColor = array();
        $colorDefault = array();
        foreach ($listColor as $value) {
            $dx = $value['ExpectationColor'];
            $_listColor[$dx['key']][$dx['id']] = $dx['color'];
            if( $dx['default'] == 1){
                $colorDefault[$dx['key']] = $dx['color'];
            }
        }
        $expectationEmployeeRefer = $this->ProjectExpectationEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_expectation_id' => $listProjectExpectation
            )
        ));
        $_expectationEmployeeRefer = array();
        foreach ($expectationEmployeeRefer as $key => $value) {
            $dx = $value['ProjectExpectationEmployeeRefer'];
            $_expectationEmployeeRefer[$dx['key']][$dx['project_expectation_id']][] = $dx;
        }
        $this->set(compact('project_id', '_listColor', 'projectName', 'fieldsets', 'fields', 'project_expectations', 'datasets', 'milestone', 'company_id', 'milestone_date', 'validated', 'nameAlert', 'dateAlert', 'listEmployee', 'employee_id', 'colorDefault', 'listEmployeeAndProfit', '_expectationEmployeeRefer'));
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
            $this->loadModels('ProjectMilestone', 'ProjectExpectationEmployeeRefer');
            $this->ProjectExpectation->create();
            if (!empty($this->data['id'])) {
                $this->ProjectExpectation->id = $this->data['id'];
            }
            //assigned_to_1
            if(!empty($this->data['assigned_to_1'])){
                $_saved = $this->ProjectExpectationEmployeeRefer->find('all', array(
                    'fields' => array('id', 'project_expectation_id', 'key', 'reference_id', 'is_profit_center'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_1'
                    ),
                    'recursive' => -1
                ));
                $saved = array();
                foreach ($_saved as $value) {
                    $dx = $value['ProjectExpectationEmployeeRefer'];
                    $saved[$dx['is_profit_center'] . '-' . $dx['reference_id']] = $dx;
                }
                foreach ($this->data['assigned_to_1'] as $impact) {
                    $v = explode('-', $impact);
                    $this->ProjectExpectationEmployeeRefer->create();
                    $data = array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_1',
                        'reference_id' => $v[1],
                        'is_profit_center' => $v[0],
                    );
                    $last = isset($saved[$impact]) ? $saved[$impact] : null;
                    if (!$last) {
                        $this->ProjectExpectationEmployeeRefer->save($data);
                    }
                    unset($saved[$impact]);
                }
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
                unset($this->data['assigned_to_1']);
            } else {
                $saved = $this->ProjectExpectationEmployeeRefer->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_1'
                    ),
                    'recursive' => -1
                ));
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
            }
            //assigned_to_2
            if(!empty($this->data['assigned_to_2'])){
                $_saved = $this->ProjectExpectationEmployeeRefer->find('all', array(
                    'fields' => array('id', 'project_expectation_id', 'key', 'reference_id', 'is_profit_center'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_2'
                    ),
                    'recursive' => -1
                ));
                $saved = array();
                foreach ($_saved as $value) {
                    $dx = $value['ProjectExpectationEmployeeRefer'];
                    $saved[$dx['is_profit_center'] . '-' . $dx['reference_id']] = $dx;
                }
                foreach ($this->data['assigned_to_2'] as $impact) {
                    $v = explode('-', $impact);
                    $this->ProjectExpectationEmployeeRefer->create();
                    $data = array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_2',
                        'reference_id' => $v[1],
                        'is_profit_center' => $v[0],
                    );
                    $last = isset($saved[$impact]) ? $saved[$impact] : null;
                    if (!$last) {
                        $this->ProjectExpectationEmployeeRefer->save($data);
                    }
                    unset($saved[$impact]);
                }
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
                unset($this->data['assigned_to_2']);
            } else {
                $saved = $this->ProjectExpectationEmployeeRefer->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_2'
                    ),
                    'recursive' => -1
                ));
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
            }
            //assigned_to_3
            if(!empty($this->data['assigned_to_3'])){
                $_saved = $this->ProjectExpectationEmployeeRefer->find('all', array(
                    'fields' => array('id', 'project_expectation_id', 'key', 'reference_id', 'is_profit_center'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_3'
                    ),
                    'recursive' => -1
                ));
                $saved = array();
                foreach ($_saved as $value) {
                    $dx = $value['ProjectExpectationEmployeeRefer'];
                    $saved[$dx['is_profit_center'] . '-' . $dx['reference_id']] = $dx;
                }
                foreach ($this->data['assigned_to_3'] as $impact) {
                    $v = explode('-', $impact);
                    $this->ProjectExpectationEmployeeRefer->create();
                    $data = array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_3',
                        'reference_id' => $v[1],
                        'is_profit_center' => $v[0],
                    );
                    $last = isset($saved[$impact]) ? $saved[$impact] : null;
                    if (!$last) {
                        $this->ProjectExpectationEmployeeRefer->save($data);
                    }
                    unset($saved[$impact]);
                }
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
                unset($this->data['assigned_to_3']);
            } else {
                $saved = $this->ProjectExpectationEmployeeRefer->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array(
                        'project_expectation_id' => $this->data['id'],
                        'key' => 'assigned_to_3'
                    ),
                    'recursive' => -1
                ));
                foreach ($saved as $_save) {
                    $this->ProjectExpectationEmployeeRefer->delete($_save['id']);
                }
            }
            $data = array();
            foreach (array('date_1', 'date_2', 'date_3', 'date_4', 'date_5', 'date_6', 'date_7', 'date_8', 'date_9', 'date_10') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectMilestone->convertTime($this->data[$key]);
                }
            }
            unset($this->data['id']);
            $data = array_merge($this->data, $data);
            if ($this->ProjectExpectation->save($data)) {
                $result = true;
                // $this->Session->setFlash(__('The Milestone has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('The Expectation could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->ProjectExpectation->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @param int $id, $project_id, $phase_id, $phase
     * @return void
     * @access public
     */
    function delete($id = null, $project_id = null) {
        $current = $this->Session->read('Expectation.current_page');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project expectation', true), 'error');
            $this->redirect(array('action' => $current . '/', $project_id));
        }
		$last = $this->ProjectExpectation->find('first', array(
                'recursive' => -1,
                'fields' => array('project_id','attached_documents'),
                'conditions' => array('ProjectExpectation.id' => $id)));
		$project_id = !empty( $last) ? $last['ProjectExpectation']['project_id'] : 0;
        if ($this->_checkRole(false, $project_id)) {
            if ($last && $this->ProjectExpectation->delete($id)) {
                @unlink(trim($this->_getPath($last['ProjectExpectation']['project_id'])
                        . $last['ProjectExpectation']['attached_documents']));
                $this->Session->setFlash(__('Deleted.', true), 'success');
                $this->redirect(array('action' => $current . '/', $project_id));
            }
            $this->loadModel('ProjectExpectationEmployeeRefer');
            $list = $this->ProjectExpectationEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_expectation_id' => $id
                ),
                'fields' => array('id', 'id')
            ));
            foreach ($list as $i) {
                $this->ProjectExpectationEmployeeRefer->delete($i);
            }
            $this->Session->setFlash(__('Project expectation was not deleted', true), 'error');
        }
        $this->redirect(array('action' => $current, $project_id));
    }
    /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($project_id = null) {
        $result = false;
        $current = $this->Session->read('Expectation.current_page');
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $project_id = __('Unknown', true);
            $projectExpectations = $this->ProjectExpectation->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectExpectation.id' => $this->data['Upload']['id'])));
            if ($projectExpectations) {
                $project_id = $projectExpectations['ProjectExpectation']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectExpectation->id = $this->data['Upload']['id'];
                $last = $this->ProjectExpectation->find('first', array(
                    'recursive' => -1,
                    'fields' => array('attached_documents'),
                    'conditions' => array('id' => $this->ProjectExpectation->id)));
                if ($last && $last['ProjectExpectation']['attached_documents']) {
                    unlink($path . $last['ProjectExpectation']['attached_documents']);
                }
                $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                if ($this->ProjectExpectation->save(array(
                            'attached_documents' => $this->data['Upload']['url'],
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
                    $this->ProjectExpectation->id = $this->data['Upload']['id'];
                    $last = $this->ProjectExpectation->find('first', array(
                        'recursive' => -1,
                        'fields' => array('attached_documents'),
                        'conditions' => array('id' => $this->ProjectExpectation->id)));
                    if ($last && $last['ProjectExpectation']['attached_documents']) {
                        unlink($path . $last['ProjectExpectation']['attached_documents']);
                    }
                    if ($this->ProjectExpectation->save(array(
                                'attached_documents' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_expectations/index/' . $project_id);
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
        $this->redirect(array('action' => $current, $project_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }

    public function attachement($id = null) {
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $current = $this->Session->read('Expectation.current_page');
        $last = $this->ProjectExpectation->find('first', array(
            'recursive' => -1,
            'fields' => array('attached_documents', 'project_id'),
            'conditions' => array('ProjectExpectation.id' => $id)));
        $error = true;
        if ($last && $last['ProjectExpectation']['project_id']) {
            $path = trim($this->_getPath($last['ProjectExpectation']['project_id'])
                    . $last['ProjectExpectation']['attached_documents']);
            $attachment = $last['ProjectExpectation']['attached_documents'];
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
                $this->ProjectExpectation->id = $id;
                $this->ProjectExpectation->save(array(
                    'attached_documents' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectExpectation']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_expectations/index/' . $last['ProjectExpectation']['project_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => $current,
                $last ? $last['ProjectExpectation']['project_id'] : __('Unknown', true)));
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
        $path = FILES . 'projects' . DS . 'expectaions' . DS;
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
    function vision(){
        if(!empty($_GET)){
            $this->loadModels('Project', 'ProjectExpectationEmployeeRefer', 'Menu', 'Expectation', 'ExpectationTranslation', 'ExpectationDataset', 'ProjectMilestone');
            $conditions = array(
                'company_id' => $this->employee_info['Company']['id']
            );
            if(!empty($_GET['cateExpec'])){
                $conditions['category'] = $_GET['cateExpec'];
            }
            if(!empty($_GET['statusExpec'])){
                $conditions['project_status_id'] = $_GET['statusExpec'];
            }
            if(!empty($_GET['programExpec'])){
                $conditions['project_amr_program_id'] = $_GET['programExpec'];
            }
            if(!empty($_GET['subproExpec'])){
                $conditions['project_amr_sub_program_id'] = $_GET['subproExpec'];
            }
            $listProject = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => $conditions,
                'fields' => array('id', 'project_name')
            ));
            // get expectation.
            $_conditions = array(
                'project_id' => array_keys($listProject)
            );
            $listExpecAssignTeam = $listExpecAssignRes = array();
            if(!empty($_GET['assignedTeam'])){
                $listExpecAssignTeam = $this->ProjectExpectationEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'reference_id' => $_GET['assignedTeam'],
                        'is_profit_center' => 1
                    ),
                    'fields' => array('id', 'project_expectation_id')
                ));
            }
            if(!empty($_GET['assignedResources'])){
                $listExpecAssignRes = $this->ProjectExpectationEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'reference_id' => $_GET['assignedResources'],
                        'is_profit_center' => 0
                    ),
                    'fields' => array('id', 'project_expectation_id')
                ));
            }
            $listExpecAssignTeam = array_unique(array_merge($listExpecAssignTeam, $listExpecAssignRes));
            if(!empty($_GET['assignedResources']) || !empty($_GET['assignedTeam'])){
                $_conditions['id'] = $listExpecAssignTeam;
            }
            // check start date, end date for milestone.
            if( !empty($_GET['start']) || !empty($_GET['end']) ){
                $con = array();
                if( !empty($_GET['start']) && !empty($_GET['end']) ){
                    $sDate = $_GET['start'];
                    $sDate = date('Y-m-d', strtotime($_GET['start']));
                    $eDate = $_GET['end'];
                    $eDate = date('Y-m-d', strtotime($_GET['end']));
                    $con['milestone_date BETWEEN ? AND ?'] = array($sDate, $eDate);
                } else if(!empty($_GET['start'])){
                    $sDate = $_GET['start'];
                    $sDate = date('Y-m-d', strtotime($_GET['start']));
                    $con = array(
                        'milestone_date >=' => $sDate
                    );
                } else if(!empty($_GET['end'])){
                    $eDate = $_GET['end'];
                    $eDate = date('Y-m-d', strtotime($_GET['end']));
                    $con = array(
                        'milestone_date <=' => $eDate
                    );
                }
                $conMilestone = $this->ProjectMilestone->find('list', array(
                    'recursive' => -1,
                    'conditions' => $con,
                    'fields' => array('id', 'id')
                ));
                $_conditions['milestone'] = $conMilestone;
            }
            if(!empty($_GET['nameExpec'])){
                $_conditions['text_long_1 LIKE'] = '%' . $_GET['nameExpec'] .'%';
            }
            $projectExpec = $this->ProjectExpectation->find('all', array(
                'recursive' => -1,
                'conditions' => $_conditions,
            ));
            $listProjectExpectation = !empty($projectExpec) ? Set::combine($projectExpec, '{n}.ProjectExpectation.id', '{n}.ProjectExpectation.id') : array();
            $page = 'expectation_1';
            if(!empty($_GET['screen'])){
                $page = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'id' => $_GET['screen']
                    )
                ));
                $page = !empty($page) ? $page['Menu']['widget_id'] : 'expectation_1';
                $page = str_replace('expectations', 'expectation', $page);
                $page = ($page == 'expectation') ? 'expectation_1' : $page;
            }
            $langCode = Configure::read('Config.langCode');
            $fields = ($langCode == 'fr') ? 'fre' : 'eng';
            $fieldsets = $this->Expectation->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Expectation.company_id' => $this->employee_info['Company']['id'],
                    'Expectation.page' => $page,
                    'Expectation.display' => 1
                ),
                'joins' => array(
                    array(
                        'table' => 'expectation_translations',
                        'alias' => 'Translation',
                        'type' => 'left',
                        'conditions' => array(
                            'Translation.original_text = Expectation.original_text',
                            'Translation.company_id' => $this->employee_info['Company']['id']
                        )
                    )
                ),
                'order' => array('Expectation.weight ASC'),
                'fields' => array('Expectation.id', 'Expectation.original_text', 'Translation.eng', 'Translation.fre', 'Expectation.field')
            ));
            $expecDataset = $this->ExpectationDataset->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                )
            ));
            $datasets = array();
            foreach ($expecDataset as $key => $value) {
                $dx = $value['ExpectationDataset'];
                $datasets[$dx['dataset_name']][$dx['id']] = $dx[$fields];
            }
            $this->loadModels('ProjectAlert', 'ExpectationColor');
            $alert = $this->ProjectAlert->find('all', array(
                'recusive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'display' => 1
                ),
                'fields' => array('id', 'alert_name', 'number_of_day'),
                'order' => array('alert_name ASC')
            ));
            $nameAlert = $dateAlert = array();
            foreach ($alert as $key => $value) {
                $dx = $value['ProjectAlert'];
                $nameAlert['alert_' . $dx['id']] = $dx['alert_name'];
                $dateAlert['alert_' . $dx['id']] = $dx['number_of_day'];
            }
            // milestone.
            $projectMilestones = $this->ProjectMilestone->find("all", array("recursive" => -1,"conditions" => array('project_id' => array_keys($listProject))));
            $milestone = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.project_milestone') : array();
            $milestone_date = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.milestone_date') : array();
            $validated = !empty($projectMilestones) ? Set::combine($projectMilestones, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.validated') : array();
            // list color
            $listColor = $this->ExpectationColor->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'display' => 1
                ),
                'fields' => array('id', 'color', 'key', 'default')
            ));
            $_listColor = array();
            $colorDefault = array();
            foreach ($listColor as $value) {
                $dx = $value['ExpectationColor'];
                $_listColor[$dx['key']][$dx['id']] = $dx['color'];
                if( $dx['default'] == 1){
                    $colorDefault[$dx['key']] = $dx['color'];
                }
            }
            //assign
            $expectationEmployeeRefer = $this->ProjectExpectationEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_expectation_id' => $listProjectExpectation
                )
            ));
            $_expectationEmployeeRefer = array();
            foreach ($expectationEmployeeRefer as $key => $value) {
                $dx = $value['ProjectExpectationEmployeeRefer'];
                $_expectationEmployeeRefer[$dx['key']][$dx['project_expectation_id']][] = $dx;
            }
            $this->loadModels('ProfitCenter', 'Employee');
            $this->ProfitCenter->virtualFields['pcId'] = 'CONCAT("1-", `id`)';
            $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
            $listProfit = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('pcId', 'pcName')
            ));
            $this->Employee->virtualFields['emId'] = 'CONCAT("0-", `id`)';
            $listEmployee = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id']
                ),
                'fields' => array('emId', 'fullname')
            ));
            $listEmployeeAndProfit = $listEmployee;
            foreach ($listProfit as $key => $value) {
                $listEmployeeAndProfit[$key] = $value;
            }
            $this->set(compact('fieldsets', 'listProject', 'projectExpec', 'fields', 'datasets', 'nameAlert', 'dateAlert', 'milestone', 'milestone_date', 'validated', '_listColor', 'colorDefault', '_expectationEmployeeRefer', 'listEmployeeAndProfit'));
        } else {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect("/projects/index");
        }
    }
}
