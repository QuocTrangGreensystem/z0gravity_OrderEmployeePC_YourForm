<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityBudgetExternalsController extends AppController {
    /**
     * 
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ projec_budget_externals_controller --------------------------
     * 2 CONTROLLER NAY CO LIEN KET VOI NHAU
     * ----------CHU Y-----CHU Y-----VA CHU Y-----------------------
     * 
     */
    /**
     * Controller nay khong co model
     */
    var $uses = array();
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityBudgetExternals';
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
    function index($activity_id = null) {
        $this->loadModels('ProjectTask', 'Project', 'ProjectBudgetInternalDetail', 'BudgetProvider', 'BudgetType', 'BudgetFunder', 'ProfitCenter', 'Activity', 'ProjectBudgetExternal');
        
        $activityName = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id)
            ));
        $projectLinked = !empty($activityName['Activity']['project']) ? $activityName['Activity']['project'] : 0;
		$bg_currency = $this->getCurrencyOfBudget();
        if($projectLinked != 0){
            $this->_checkRole(false, $projectLinked);
            $getDataProjectTasks = $this->Project->dataFromProjectTask($projectLinked, $activityName['Activity']['company_id']);
        } else {
            $getDataProjectTasks = $this->Activity->dataFromActivityTask($activity_id, $activityName['Activity']['company_id']);
            $canModified = true;
            $this->set(compact('canModified'));
        }
        $budgetProviders = $this->BudgetProvider->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $activityName['Activity']['company_id']),
                'fields' => array('id', 'name')
            ));
        $_budgetTypes = $this->BudgetType->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $activityName['Activity']['company_id']),
                'fields' => array('id', 'name', 'capex')
            ));
        $idOfExternals = !empty($budgetExternals) ? Set::classicExtract($budgetExternals, '{n}.ProjectBudgetExternal.id') : array();
        $budgetTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.name') : array();
        $capexTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.capex') : array();
        $budgetExternals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id)
            ));
        $this->_parseParams();
		$profits = $this->ProfitCenter->generateTreeList(array('company_id' => $activityName['Activity']['company_id']), null, null, '');
		$funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $activityName['Activity']['company_id']),
            'fields' => array('id','name')
        ));
        $taskExternals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $projectLinked, 'special' => 1, 'external_id' => $idOfExternals),
            'fields' => array('SUM(special_consumed) as consumed', 'SUM(estimated) as maday', 'external_id')
        ));
        $taskExternals = !empty($taskExternals) ? Set::combine($taskExternals, '{n}.ProjectTask.external_id', '{n}.0') : array();
        if($projectLinked){
            $this->action = 'index_link';
        } else {
            $taskExternals = array();
        }
        $this->set(compact('activityName', 'activity_id', 'getDataProjectTasks', 'budgetProviders', 'budgetTypes', 'budgetExternals', 'capexTypes', 'projectLinked', 'totalconsumed','profits','funders', 'taskExternals', 'bg_currency'));
    }
	public function getDataOfExternalTask($id)
	{
		//Ham nay chi dung khi user nhap start date - end date theo rang buoc: start date - end date co thang va nam giong nhau//
		$this->loadModel('ProjectTask');
		$this->loadModel('ProjectBudgetExternal');
		$dataProgress=array();
		$externals=$this->ProjectTask->find('all',array(
			'recursive' => -1,
			'fields' => array('DISTINCT external_id as external'),
			'conditions' => array('ProjectTask.project_id' => $id,
							'ProjectTask.special' => 1
						),
			'order' => array('ProjectTask.external_id'),
			'group' => array('ProjectTask.external_id')
		));
		foreach($externals as $_index=>$_external)
		{
			$external=$_external['ProjectTask']['external'];
			$tasks=$this->ProjectTask->find('all',array(
				'recursive' => -1,
				'fields' => array('SUM(special_consumed) as consumed','task_title', 'SUM(estimated) as planed', 'DATE_FORMAT(task_start_date, "%b-%Y") as date', 'DATE_FORMAT(task_start_date, "%Y") as year'),
				'conditions' => array('ProjectTask.project_id' => $id,
								'ProjectTask.special' => 1,
								'ProjectTask.external_id' => $external
							),
				'order' => array('ProjectTask.task_start_date'),
				'group' => array('DATE_FORMAT(task_start_date, "%M%Y")')
			));
			
			$sumPlaned=$sumConsumed=$progress=0;
			foreach($tasks as $_key=>$_val)
			{
				//progress
				$sumConsumed+=$_val[0]['consumed'];
				$sumPlaned+=$_val[0]['planed'];
			}
			if($sumPlaned==0||$sumConsumed==0)
			$progress=0;
			else
			$progress=round(($sumConsumed/$sumPlaned),4)*100;

			
			$dataProgress[$external]=$progress;
		}
		$this->set(compact('dataProgress'));
	}
    
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $this->loadModel('ProjectBudgetExternal');
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $this->ProjectBudgetExternal->create();
            if (!empty($this->data['id'])) {
                $this->ProjectBudgetExternal->id = $this->data['id'];
            }
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
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
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
    public function upload($activity_id = null) {
        $this->loadModel('ProjectBudgetExternal');
        $result = false;
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $activity_id = __('Unknown', true);
            $project_id = __('Unknown', true);
            $projectBudgetExternals = $this->ProjectBudgetExternal->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'project_id',
                    'activity_id',
                ), 'conditions' => array('ProjectBudgetExternal.id' => $this->data['Upload']['id'])));
            if ($projectBudgetExternals) {
                $project_id = $projectBudgetExternals['ProjectBudgetExternal']['project_id'];
                $activity_id = $projectBudgetExternals['ProjectBudgetExternal']['activity_id'];
            }
            if($project_id != 0){
                $path = $this->_getPath($project_id);
            } else {
                $path = $this->_getPathActivity($activity_id);
            }
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectBudgetExternal->id = $this->data['Upload']['id'];
                $last = $this->ProjectBudgetExternal->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->ProjectBudgetExternal->id)));
                if ($last && $last['ProjectBudgetExternal']['file_attachement']) {
                    unlink($path . $last['ProjectBudgetExternal']['file_attachement']);
                }
                if ($this->ProjectBudgetExternal->save(array(
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
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->ProjectBudgetExternal->id)));
                    if ($last && $last['ProjectBudgetExternal']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetExternal']['file_attachement']);
                    }
                    if ($this->ProjectBudgetExternal->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/activity_budget_externals/index/' . $activity_id);
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
        $this->redirect(array('action' => 'index', $activity_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }
    
    public function attachement($id = null) {
        $this->loadModel('ProjectBudgetExternal');
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $last = $this->ProjectBudgetExternal->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'project_id', 'activity_id'),
            'conditions' => array('ProjectBudgetExternal.id' => $id)));
        $error = true;
        if ($last && $last['ProjectBudgetExternal']['activity_id']) {
            if($last['ProjectBudgetExternal']['project_id'] != 0){
                $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id'])
                    . $last['ProjectBudgetExternal']['file_attachement']);
            } else {
                $path = trim($this->_getPathActivity($last['ProjectBudgetExternal']['activity_id'])
                    . $last['ProjectBudgetExternal']['file_attachement']);
            }
            $attachment = $last['ProjectBudgetExternal']['file_attachement'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                if($last['ProjectBudgetExternal']['project_id'] != 0){
                    $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id']));
                } else {
                    $path = trim($this->_getPathActivity($last['ProjectBudgetExternal']['activity_id']));
                }
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
                if($this->MultiFileUpload->otherServer == true){
                    if($last['ProjectBudgetExternal']['project_id'] != 0){
                        $path = trim($this->_getPath($last['ProjectBudgetExternal']['project_id']));
                    } else {
                        $path = trim($this->_getPathActivity($last['ProjectBudgetExternal']['activity_id']));
                    }
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/activity_budget_externals/index/' . $last['ProjectBudgetExternal']['activity_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectBudgetExternal']['activity_id'] : __('Unknown', true)));
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
        // $pcompany = ClassRegistry::init('Company')->find('first', array(
        //     'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'project_budgets' . DS . 'externals' . DS;
        // if ($pcompany) {
        //     $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['company_name'])) . DS . $project_id . DS;
        // }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
        
        return $path;
    }
    
    protected function _getPathActivity($activity_id) {
        $this->loadModel('Activity');
        $company = $this->Activity->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name'
            ), 
            'conditions' => array('Activity.id' => $activity_id)
        ));
        // $pcompany = ClassRegistry::init('Company')->find('first', array(
        //     'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'project_budgets' . DS . 'activity_externals' . DS;
        // if ($pcompany) {
        //     $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['company_name'])) . DS . $activity_id . DS;
        // }
        $path .= $company['Company']['dir'] . DS . $activity_id . DS;

        return $path;
    }
    
    
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $activity_id = null) {
        $this->loadModels('ProjectBudgetExternal', 'Activity');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index', $activity_id));
        }
        $last = $this->ProjectBudgetExternal->find('first', array(
            'recursive' => -1,
            'fields' => array('project_id','file_attachement', 'activity_id'),
            'conditions' => array('ProjectBudgetExternal.id' => $id)));
		$activity_id = !empty( $last) ? $last['ProjectBudgetExternal']['activity_id'] : 0;
		$project_id = $activity_id ? 
			$this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array('Activity.id' => $activity_id),
                'fields' => array('project') 
            )) 
			: 0;
		$project_id = !empty($project_id) && !empty($project_id['Activity']['project']) ? $project_id['Activity']['project'] : 0;
        if( $this->_checkRole(false, $project_id) && $this->ProjectBudgetExternal->delete($id)) {
            if($last['ProjectBudgetExternal']['project_id'] != 0){
                @unlink(trim($this->_getPath($last['ProjectBudgetExternal']['project_id'])
                    . $last['ProjectBudgetExternal']['file_attachement']));
            } else {
                @unlink(trim($this->_getPathActivity($last['ProjectBudgetExternal']['activity_id'])
                    . $last['ProjectBudgetExternal']['file_attachement']));
            }
            /**
             * kiem tra xem Activity co linked voi project ko. Co thi lay id
             */
            $datas = array(
                'project_id' => $project_id,
                'activity_id' => $activity_id
            );
            $this->ProjectBudgetExternal->saveExternalToSyns($datas);
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index', $activity_id));
        }
        $this->Session->setFlash(__('Activity Budget External Cost was not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $activity_id));
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
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }
        
        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }
    /**
    * Function popup auto created task
    */
    function add_auto_task($id = null,$project_id = null){
        $this->layout = 'ajax';
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ProjectBudgetExternal');
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
    function clean_filters(){
        $this->loadModel('HistoryFilter');
        if (!empty($_POST)){
            $path = $_POST['path'];
            $budget_internal = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id'],
                ),
                'fields' => array( 'id', 'params'),
            ));
            // debug($budget_internal); exit;
            if(!empty($budget_internal)){
                if ($this->HistoryFilter->delete($budget_internal['HistoryFilter']['id'])) die(1);
            } 
            die(0);
        }
        die(0);
    }
}
?>