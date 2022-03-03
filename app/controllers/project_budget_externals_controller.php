<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetExternalsController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ activity_budget_externals_controller --------------------------
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
    var $name = 'ProjectBudgetExternals';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation','Number');

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
    function index($project_id = null) {
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            if( !((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))){
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/'.$value;
                }
                if( isset($_url_param['url'])) unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }

        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('external_cost');
        $this->loadModels('ProjectTask', 'Project', 'ProjectBudgetInternalDetail', 'BudgetProvider', 'BudgetType', 'BudgetFunder', 'ProfitCenter');

        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
        $budgetProviders = $this->BudgetProvider->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $projectName['Project']['company_id']),
                'fields' => array('id', 'name')
            ));
        $_budgetTypes = $this->BudgetType->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $projectName['Project']['company_id']),
                'fields' => array('id', 'name', 'capex')
            ));
        $budgetTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.name') : array();
        $capexTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.capex') : array();
        $budgetExternals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $project_id)
            ));
        $idOfExternals = !empty($budgetExternals) ? Set::classicExtract($budgetExternals, '{n}.ProjectBudgetExternal.id') : array();
		$profits = $this->ProfitCenter->generateTreeList(array('company_id' => $projectName['Project']['company_id']), null, null, '');
		$funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id','name')
        ));
        $taskExternals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special' => 1, 'external_id' => $idOfExternals),
            'group' => array('external_id'),
            'fields' => array('SUM(special_consumed) as consumed', 'SUM(estimated) as maday', 'external_id')
        ));
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
        $taskExternals = !empty($taskExternals) ? Set::combine($taskExternals, '{n}.ProjectTask.external_id', '{n}.0') : array();
        // kiem tra xem PM dc chinh sua budget internal hay ko?
        $modifyBudget = $this->checkModifyBudget();
        if($projectName && $projectName['Project']['category'] == 2){
            //$this->action = 'oppor';
        }
        // debug($budgetExternals); exit;
        $employee_info = $this->employee_info;
        $this->set(compact('projectName', 'project_id', 'getDataProjectTasks', 'budgetProviders', 'budgetTypes', 'budgetExternals', 'capexTypes', 'activityLinked', 'totalconsumed', 'profits', 'funders', 'taskExternals', 'modifyBudget', 'employee_info','budget_settings'));
    }
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $this->ProjectBudgetExternal->create();
            
            $data = array();
            foreach (array('order_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectBudgetExternal->convertTime($this->data[$key]);
                }
            }
			foreach (array('expected_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectBudgetExternal->convertTime($this->data[$key]);
                }
            }
			foreach (array('due_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectBudgetExternal->convertTime($this->data[$key]);
                }
            }
            $data['capex_id'] = (isset($this->data['capex_id']) && $this->data['capex_id'] == 'capex');
            $project_id = $this->data['project_id'];
            $activity_id = $this->data['activity_id'];
            $name = $this->data['name'];
			if (!empty($this->data['id'])) {
                $this->ProjectBudgetExternal->id = $this->data['id'];
				$external_id = $this->data['id'];
				$provision = ClassRegistry::init('ProjectBudgetProvisional');
                $provision->virtualFields['total_value'] = 'SUM(value)';
                $total = $provision->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $project_id,
                        'model' => 'External',
                        'model_id' => $external_id
                    ),
                    'fields' => array('view', 'total_value'),
                    'group' => array('view')
                ));
                if( isset($total['man-day']) ){
                    $md = floatval($this->data['man_day']);
                    if( $md < $total['man-day'] ){
                        $result = false;
                        $this->set(compact('result'));
                        $this->Session->setFlash(sprintf(__('Budget man day (%s) < provisional budget man day (%s)', true), $md, $total['man-day']), 'error');
                        $this->render();
                        return;
                    }
                }
                if( isset($total['euro']) ){
                    $eur = floatval( $this->data['budget_erro'] );
                    if( $eur < $total['euro'] ){
                        $result = false;
                        $this->set(compact('result'));
                        $this->Session->setFlash(sprintf(__('Budget euro (%s) < provisional budget euro (%s)', true), $eur, $total['euro']), 'error');
                        $this->render();
                        return;
                    }
                }
            }
            unset($this->data['id']);
            if ($this->ProjectBudgetExternal->save(array_merge($this->data, $data))) {
                $external_id = $this->ProjectBudgetExternal->id;
                 //project task
                $this->loadModel('ProjectTask');
                $project_tasks = $this->ProjectTask->find('all',array('conditions'=>array(
                    'ProjectTask.external_id'=>$external_id,'ProjectTask.project_id'=>$project_id)
                    ));
                if(!empty($project_tasks)){
                    $phase = array();
                    foreach ($project_tasks as $project_task) {
                            $_dataAL['task_title'] = $name;
                            $phase[$project_task['ProjectTask']['id']] = $project_task['ProjectTask']['project_planed_phase_id'];
                            $this->ProjectTask->id = $project_task['ProjectTask']['id'];
                            $this->ProjectTask->save($_dataAL);
                        }
                }
                //activity task
                $this->loadModel('ActivityTask');
                $activity_tasks = $this->ActivityTask->find('all',array('recursive'=>-1,'conditions'=>array(
                    'ActivityTask.external_id'=>$external_id,'ActivityTask.activity_id'=>$activity_id)
                    ));
                if(!empty($activity_tasks)){
                    foreach ($activity_tasks as $activity_task) {
                            $this->loadModel('ProjectPhasePlan');
                            $phaseTaskPlan = $this->ProjectPhasePlan->find('first',array('recursive'=>-1,'conditions'=>array(
                                'ProjectPhasePlan.id'=>$phase[$activity_task['ActivityTask']['project_task_id']]),
                                'fields' =>'project_planed_phase_id'
                                ));
                            $this->loadModel('ProjectPhase');
                            $phaseTask = $this->ProjectPhase->find('first',array('recursive'=>-1,'conditions'=>array(
                                'ProjectPhase.id'=>$phaseTaskPlan['ProjectPhasePlan']['project_planed_phase_id']),
                                'fields' =>'name'
                                ));
                            $dataAL['name'] = $phaseTask['ProjectPhase']['name'].'/'.$name;
                            $this->ActivityTask->id = $activity_task['ActivityTask']['id'];
                            $this->ActivityTask->save($dataAL);
                        }
                }
                //Added by QN on 2015/02/09
                $projectName = $this->ProjectBudgetExternal->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $project_id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update external budget `%s` for project `%s`', $this->data['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $result = true;
                // $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget External Costs could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->ProjectBudgetExternal->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
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
            $projectBudgetExternals = $this->ProjectBudgetExternal->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id'
                ), 'conditions' => array('ProjectBudgetExternal.id' => $this->data['Upload']['id'])));
            if ($projectBudgetExternals) {
                $project_id = $projectBudgetExternals['ProjectBudgetExternal']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectBudgetExternal->id = $this->data['Upload']['id'];
                $last = $this->ProjectBudgetExternal->find('first', array(
                    'recursive' => -1,
                    'fields' => array('name', 'file_attachement'),
                    'conditions' => array('id' => $this->ProjectBudgetExternal->id)));
                if ($last && $last['ProjectBudgetExternal']['file_attachement']) {
                    unlink($path . $last['ProjectBudgetExternal']['file_attachement']);
                }
                if ($this->ProjectBudgetExternal->save(array(
                            'file_attachement' => $this->data['Upload']['url'],
                            'format' => 1
                        ))) {
                    //Added by QN on 2015/02/09
                    $projectName = $this->ProjectBudgetExternal->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $this->writeLog($this->data, $this->employee_info, sprintf('Update attachment for external budget `%s` for project `%s`', $last['ProjectBudgetExternal']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
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
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm";
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                            , true), $path), 'error');
                }
                if (!empty($attachment)) {
                    $attachment = $attachment['attachment']['attachment'];
                    $this->ProjectBudgetExternal->id = $this->data['Upload']['id'];
                    $last = $this->ProjectBudgetExternal->find('first', array(
                        'recursive' => -1,
                        'fields' => array('name', 'file_attachement'),
                        'conditions' => array('id' => $this->ProjectBudgetExternal->id)));
                    if ($last && $last['ProjectBudgetExternal']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetExternal']['file_attachement']);
                    }
                    if ($this->ProjectBudgetExternal->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        //Added by QN on 2015/02/09
                        $projectName = $this->ProjectBudgetExternal->Project->find("first", array(
                            'recursive' => -1,
                            "fields" => array('project_name', 'company_id'),
                            'conditions' => array('Project.id' => $project_id)));
                        $this->writeLog($this->data, $this->employee_info, sprintf('Upload attachment for external budget `%s` for project `%s`', $last['ProjectBudgetExternal']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_budget_externals/index/' . $project_id);
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
        $last = $this->ProjectBudgetExternal->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id', 'name'),
            'conditions' => array('ProjectBudgetExternal.id' => $id)));
        $error = true;
        if ($last && $last['ProjectBudgetExternal']['project_id']) {
            $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id'])
                    . $last['ProjectBudgetExternal']['file_attachement']);
            $attachment = $last['ProjectBudgetExternal']['file_attachement'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id']));
                $this->MultiFileUpload->downloadFileToServerOther($path, $attachment);
            } else {
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
            }
            if ($type != 'download') {
                @unlink($path);
                $this->ProjectBudgetExternal->id = $id;
                $this->ProjectBudgetExternal->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                //Added by QN on 2015/02/09
                $projectName = $this->ProjectBudgetExternal->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $last['ProjectBudgetExternal']['project_id'])));
                $this->writeLog($this->data, $this->employee_info, sprintf('Delete attachment of external budget `%s` of project `%s`', $last['ProjectBudgetExternal']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_budget_externals/index/' . $last['ProjectBudgetExternal']['project_id']);
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
            ),
            'conditions' => array('Project.id' => $project_id)
        ));
        $pcompany = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'project_budgets' . DS . 'externals' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS . $project_id . DS;
        }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;

        return $path;
    }


    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->loadModel('ProjectTask');
        $this->loadModels('ActivityTask', 'ProjectBudgetProvisional', 'Project');
        $checkP = $this->ProjectTask->find('first',array('recursive'=>-1,'conditions'=>array(
            'ProjectTask.external_id'=>$id,'ProjectTask.project_id'=>$project_id,'ProjectTask.special_consumed <>'=>0),
            'fields'=>'id'
            ));
        $checkT = $this->ActivityTask->find('first',array('recursive'=>-1,'conditions'=>array(
            'ActivityTask.external_id'=>$id,'ActivityTask.special_consumed <>'=>0),
            'fields'=>'id'
            ));
        if(empty($checkP)&&empty($checkT)){
            $last = $this->ProjectBudgetExternal->find('first', array(
                'recursive' => -1,
                'fields' => array('name', 'project_id','file_attachement'),
                'conditions' => array('ProjectBudgetExternal.id' => $id)));
            $checkProvisionals = $this->ProjectBudgetProvisional->find('count', array(
                'recursive' => -1,
                'conditions' => array('model' => 'External', 'model_id' => $id, 'NOT' => array('value IS NULL'))
            ));
            if($checkProvisionals != 0){
                $this->Session->setFlash(__('Data filled in provisional screen', true), 'error');
                $this->redirect(array('action' => 'index', $project_id));
            }
            if ($last && $this->ProjectBudgetExternal->delete($id)) {
                // XOA ProjectBudgetProvisional
                $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'External', 'ProjectBudgetProvisional.model_id' => $id), false);
                @unlink(trim($this->_getPath($last['ProjectBudgetExternal']['project_id'])
                        . $last['ProjectBudgetExternal']['file_attachement']));
                /**
                 * kiem tra xem project co linked voi activity ko. Co thi lay id
                 */
                $activity_id = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id),
                    'fields' => array('activity_id')
                ));
                $datas = array(
                    'project_id' => $project_id,
                    'activity_id' => !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : 0
                );
                // Xoa project task và activity task
                $checkProTasks = $this->ProjectTask->find('all',array('recursive'=>-1,'conditions'=>array(
                    'ProjectTask.external_id'=>$id,'ProjectTask.project_id'=>$project_id),
                    'fields'=>'id'
                    ));
                $checkActTasks = $this->ActivityTask->find('all',array('recursive'=>-1,'conditions'=>array(
                    'ActivityTask.external_id'=>$id),
                    'fields'=>'id'
                    ));
                foreach($checkProTasks as $checkProTask){
                    $this->ProjectTask->delete($checkProTask['ProjectTask']['id']);
                }
                foreach($checkActTasks as $checkActTask){
                    $this->ActivityTask->delete($checkActTask['ActivityTask']['id']);
                }
                $this->ProjectBudgetExternal->saveExternalToSyns($datas);
                //Added by QN on 2015/02/09
                $projectName = $this->ProjectBudgetExternal->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $project_id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Delete external budget `%s` of project `%s`', $last['ProjectBudgetExternal']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index', $project_id));
            }
        }else{
            $this->Session->setFlash(__('Project Budget External Cost  was not deleted', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        $this->Session->setFlash(__('Project Budget External Cost  was not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $project_id));
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
    * Function popup auto created task
    */
    function add_auto_task($id = null,$project_id = null){
        $this->layout = 'ajax';
        $this->loadModel('ProjectPhasePlan');
        $phasePs = $this->ProjectPhasePlan->find('all',array('conditions'=>array(
                    'ProjectPhasePlan.project_id'=>$project_id),
            'fields'=>array('ProjectPhasePlan.id','ProjectPhasePlan.phase_real_start_date','ProjectPhasePlan.phase_real_end_date','ProjectPhase.id','ProjectPhase.name','ProjectPart.title')
        ));
        $manDay = $this->ProjectBudgetExternal->find('first',array('conditions'=>array('ProjectBudgetExternal.id'=>$id),'fields'=>array('id','man_day','name','budget_provider_id')));
        $this->loadModel('ProjectTask');
        $checks = $this->ProjectTask->find('all',array('conditions'=>array(
                'ProjectTask.project_id'=>$project_id,
                'ProjectTask.external_id'=>$id
                ),
                'recursive'=>-1,
                'fields'=>array('project_planed_phase_id','estimated','special_consumed')
            ));
        $phaseHave = array();
        $phaseCheck = array();
        if(!empty($checks)){
            foreach($checks as $key=>$value):
                $phaseHave[$value['ProjectTask']['project_planed_phase_id']] = $value;
                $phaseCheck[$value['ProjectTask']['project_planed_phase_id']] = $value['ProjectTask']['project_planed_phase_id'];
            endforeach;
        }
        $this->set(compact('phasePs','phaseHave','phaseCheck','manDay','id','project_id'));

    }
    /*
    *Add auto task special
    */
    function add_auto($project_id = null){
        if(!empty($this->data)) {
            $this->loadModel('ProjectTask');
			$this->loadModel('ActivityTask');
            $this->loadModel('Project');
            $projectName = $this->Project->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Project.id' => $project_id)
                ));
            $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
            foreach($this->data['Provider'] as $key=>$value):
                $value = str_replace(",",".",$value);
                $namePhase = $this->data['AutoTaskPhase'][$key ];
                $key = str_replace('val-'.$this->data['AutoTask']['id'].'-','',$key);
                if(floatval($value)!=0){
                    $_data = array();
                    $startP = $this->data['PhaseSE']['start-'.$this->data['AutoTask']['id'].'-'.$key];
                    $endP = $this->data['PhaseSE']['end-'.$this->data['AutoTask']['id'].'-'.$key];
                    $check = $this->ProjectTask->find('first',array('recursive'=>-1,'conditions'=>array('project_planed_phase_id'=>$key,'external_id'=>$this->data['AutoTask']['external_id'])));
                    if(!empty($check)){
                        $this->ProjectTask->id =  $check['ProjectTask']['id'];
                        $_data['task_title'] = $this->data['AutoTask']['task_title'];
                        $_data['task_assign_to'] = $this->data['AutoTask']['task_asign_to'];
                        $_data['external_id'] = $this->data['AutoTask']['external_id'];
                        $_data['special'] = 1;
                        $_data['project_planed_phase_id'] = $key;
                        $_data['project_id'] = $this->data['AutoTask']['project_id'];
                        $_data['estimated'] = floatval($value);
                        $_data['task_start_date'] = $startP;
                        $_data['task_end_date'] = $endP;
                        $this->ProjectTask->save($_data);
                        if($activityLinked){
                            $_dataAL = array();
                            $checkEdit = $this->ActivityTask->find('first',array('conditions'=>array(
                                'ActivityTask.project_task_id'=>$check['ProjectTask']['id']
                                ),
                                'recursive'=>-1,
                                'fields'=>'id'
                                ));
                            $this->ActivityTask->id =  $checkEdit['ActivityTask']['id'];
                            $_dataAL['name'] = $namePhase.'/'.$this->data['AutoTask']['task_title'];
                            $_dataAL['activity_id'] = $projectName['Project']['activity_id'];
                            $_dataAL['project_task_id'] = $check['ProjectTask']['id'];
                            $_dataAL['special']  = 1;
                            $_dataAL['external_id'] = $this->data['AutoTask']['external_id'];
                            $_dataAL['estimated'] = floatval($value);
                            $_dataAL['task_start_date'] = strtotime($startP);
                            $_dataAL['task_end_date'] = strtotime($endP);
                            $this->ActivityTask->save($_dataAL);
                        }
                    }else{
                        $this->ProjectTask->create();
                        $_data['task_title'] = $this->data['AutoTask']['task_title'];
                        $_data['task_assign_to'] = $this->data['AutoTask']['task_asign_to'];
                        $_data['external_id'] = $this->data['AutoTask']['external_id'];
                        $_data['special'] = 1;
                        $_data['project_planed_phase_id'] = $key;
                        $_data['project_id'] = $this->data['AutoTask']['project_id'];
                        $_data['estimated'] = floatval($value);
                        $_data['task_start_date'] = $startP;
                        $_data['task_end_date'] = $endP;
                        $this->ProjectTask->save($_data);
                        if($activityLinked){
                            $_dataAL = array();
                            $this->ActivityTask->create();
                            $_dataAL['name'] = $namePhase.'/'.$this->data['AutoTask']['task_title'];
                            $_dataAL['activity_id'] = $projectName['Project']['activity_id'];
                            $_dataAL['project_task_id'] = $this->ProjectTask->getLastInsertID();
                            $_dataAL['special']  = 1;
                            $_dataAL['external_id'] = $this->data['AutoTask']['external_id'];
                            $_dataAL['estimated'] = floatval($value);
                            $_dataAL['task_start_date'] = strtotime($startP);
                            $_dataAL['task_end_date'] = strtotime($endP);
                            $this->ActivityTask->save($_dataAL);
                        }

                    }
                }else{
                    $checkNull = $this->ProjectTask->find('first',array('recursive'=>-1,'conditions'=>array('project_planed_phase_id'=>$key,'external_id'=>$this->data['AutoTask']['external_id'])));
                    if(!empty($checkNull)){
                        $this->ProjectTask->delete($checkNull['ProjectTask']['id']);
                        $checkEdit = $this->ActivityTask->find('first',array('conditions'=>array(
                                'ActivityTask.project_task_id'=>$checkNull['ProjectTask']['id']
                                ),
                                'recursive'=>-1,
                                'fields'=>'id'
                                ));
                         $this->ActivityTask->delete($checkEdit['ActivityTask']['id']);
                    }
                }
            endforeach;
            $this->Session->setFlash(__('Project task auto created', true), 'success');
            $this->redirect(array('action' => 'index', $project_id));
        }else{
             $this->Session->setFlash(__('Project Budget External Cost  was not found', true), 'error');
             $this->redirect(array('action' => 'index', $project_id));
        }
    }
}
?>
