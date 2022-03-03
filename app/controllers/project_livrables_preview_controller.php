<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectLivrablesPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */ 
    var $uses = array('ProjectLivrable', 'ProjectTeam');

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
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        // redirect ve index neu ko phai la admin hoac pm
        $this->_checkRole(false, $project_id);

        $this->loadModels('ProjectEmployeeManager','CompanyEmployeeReference');
        $this->_checkWriteProfile('deliverable');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectLivrable->Behaviors->attach('Containable');
        $this->ProjectLivrable->cacheQueries = true;
        $projectLivrables = $this->ProjectLivrable->find("all", array(
            'fields' => array('*'),
            'order' => array('weight'),
            'contain' => array(
                'ProjectLivrableActor' => array('fields' => array('id'), 'Employee' => array('id')),
            ), "conditions" => array('project_id' => $project_id)));
        $livrableCategories = $this->ProjectLivrable->ProjectLivrableCategory->find('all', array(
            'fields' => array('id', 'livrable_cat', 'livrable_icon'),
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            )
        ));
        $projectCode = $this->ProjectLivrable->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            )
        ));
        $this->loadModel('Employee');
        //$modifyF = $this->Employee->find('first',array('recusive'=>-1,'conditions'=>array('Employee.id'=>$projectName['Project']['freeze_by']),'fields'=>array('Employee.fullname')));
        //get all employee company.
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    array('company_id' => $projectName['Project']['company_id']),
                    array('company_id' => null)
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        $livrableIcon = !empty($livrableCategories) ? Set::combine($livrableCategories, '{n}.ProjectLivrableCategory.id', '{n}.ProjectLivrableCategory.livrable_icon') : array();
		
		/* 
		* Ticket 316
		* Before: Use png icon
		* After: user SVG icon 
		* Nếu trước đâu sử dụng PNG thì sử dụng SVG default z0g.svg
		*/
		foreach($livrableIcon as $key => $val){
			$icon = explode( '.', $val);
			if( end($icon) != 'svg'){
				$livrableIcon[$key] = 'z0g.svg';
			}
		}
		/* End */
		
		// debug( $livrableIcon); exit;
        $livrableCategories = !empty($livrableCategories) ? Set::combine($livrableCategories, '{n}.ProjectLivrableCategory.id', '{n}.ProjectLivrableCategory.livrable_cat') : array();
        $projectStatuses = $this->ProjectLivrable->Project->ProjectStatus->find('list', array(
            'conditions' => array('ProjectStatus.company_id' => $projectName['Project']['company_id'])));
        $employees = $this->ProjectLivrable->Employee->CompanyEmployeeReference->find('all', array(
            'conditions' => array(
				'CompanyEmployeeReference.company_id' => $projectName['Project']['company_id'],
				'Employee.first_name IS NOT NULL',
				'Employee.last_name IS NOT NULL'			
			),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name'),
			'order' => array('Employee.last_name' => 'asc'),
		));
		
        $employees = Set::combine($employees, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        $this->loadModels('Employee', 'ProfitCenter', 'HistoryFilter');
        $listEm = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname', 'profit_center_id')
        ));
		// debug($listEm);
        $listEmIdOfPc = $listEm ? Set::combine($listEm, '{n}.Employee.id', '{n}.Employee.profit_center_id') : array();
        $listEm = $listEm ? Set::combine($listEm, '{n}.Employee.id', '{n}.Employee.fullname') : array();
		// debug($listEm); exit;
		$listIdEm = array_keys($listEm);
		// debug($listIdEm); exit;
		$list_avatar = $this->requestAction('employees/get_list_avatar/', array('pass' => array($listIdEm)));
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'name')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $url = str_replace('?hl=fr', '', $_SERVER['REQUEST_URI']);
        $url = str_replace('?hl=en', '', $url);
        $url = str_replace('?hl=vi', '', $url);
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('projectName', 'livrableCategories', 'projectStatuses', 'employees', 'project_id', 'projectLivrables', 'livrableIcon', 'projectCode', 'listEm', 'listEmIdOfPc', 'listPc', 'employee_id', 'loadFilter','listEmployee', 'list_avatar'));
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectLivrable->id = $id;
                $this->ProjectLivrable->save(array(
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
            $this->ProjectLivrable->create();
            if (!empty($this->data['id'])) {
                $this->ProjectLivrable->id = $this->data['id'];
            }
            $data = array();
            foreach (array('livrable_date_delivery', 'livrable_date_delivery_planed') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectLivrable->convertTime($this->data[$key]);
                }
            }
            $data['employee_id_upload'] = $this->employee_info['Employee']['id'];
            $this->data['updated'] = time();
            if( !( is_numeric( $this->data['livrable_progression']) && ($this->data['livrable_progression'] >=0 ) && ( $this->data['livrable_progression']) <= 100)){
				$this->set(compact('result'));
				$this->Session->setFlash(__('The Deliverables could not be saved. Please, try again.', true), 'error');
				return;
			} 
            if( !$this->data['project_livrable_category_id'] )$this->data['project_livrable_category_id'] = '';
            if ($this->_checkRole(true)) {
                $projectName = $this->viewVars['projectName'];
                unset($this->data['id']);
                if ($this->ProjectLivrable->save(array_merge(array_diff_key($this->data, array('actor_list' => '')), $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                    if (!empty($this->data['actor_list'])) {
                        $saved = $this->ProjectLivrable->ProjectLivrableActor->find('all', array(
                            'fields' => array('id', 'project_id', 'employee_id', 'project_livrable_id'),
                            'conditions' => array(
                                'project_livrable_id' => $this->ProjectLivrable->id
                            ),
                            'recursive' => -1));
                        $saved = Set::combine($saved, '{n}.ProjectLivrableActor.employee_id', '{n}.ProjectLivrableActor');
                        foreach ($this->data['actor_list'] as $employee) {
                            $this->ProjectLivrable->ProjectLivrableActor->create();
                            $data = array(
                                'employee_id' => $employee,
                                'project_id' => $projectName['Project']['id'],
                                'project_livrable_id' => $this->ProjectLivrable->id,
                            );
                            $last = isset($saved[$employee]) ? $saved[$employee] : null;
                            if ($last) {
                                $data = array_merge($last, $data);
                                $this->ProjectLivrable->ProjectLivrableActor->id = $data['id'];
                                unset($data['id']);
                            }
                            $this->ProjectLivrable->ProjectLivrableActor->save($data);
                            unset($saved[$employee]);
                        }
                        foreach ($saved as $_save) {
                            $this->ProjectLivrable->ProjectLivrableActor->delete($_save['id']);
                        }
                    }
                } else {
                    $this->Session->setFlash(__('The Deliverables could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectLivrable->id;
            }
            $this->loadModels('ProjectLivrableComment');
            $this->ProjectLivrableComment->create();
            $this->ProjectLivrableComment->save(array(
                'project_livrable_id' => $this->data['id'],
                'employee_id' => $this->employee_info['Employee']['id'],
                'comment' => 'Information of the document modified',
            ));
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

            $_FILES['FileField'] = array();
            if(!empty($_FILES)){
                $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            }
            unset($_FILES['file']);

            $filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);

            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $pos1 = strpos($this->data['Upload']['url'], 'ttps://');
                $pos2 = strpos($this->data['Upload']['url'], 'ttp://');
                if((!empty($pos1) && $pos1 == 1) || (!empty($pos2) && $pos2 == 1)){
                    $format = 1;
                } else {
                    $format = 3;
                }
                $this->ProjectLivrable->id = $this->data['Upload']['id'];
                $last = $this->ProjectLivrable->find('first', array(
                    'recursive' => -1,
                    'fields' => array('livrable_file_attachment'),
                    'conditions' => array('id' => $this->ProjectLivrable->id)));
                if ($last && $last['ProjectLivrable']['livrable_file_attachment']) {
                    unlink($path . $last['ProjectLivrable']['livrable_file_attachment']);
                }
                $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                if ($this->ProjectLivrable->save(array(
                            'livrable_file_attachment' => $this->data['Upload']['url'],
                            'format' => $format,
                            'upload_date' => time()
                        ))) {
                    // $this->Session->setFlash(__('Saved', true), 'success');
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
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                            , true), $path), 'error');
                }
                if (!empty($attachment)) {
                    $attachment = $attachment['attachment']['attachment'];
                    $this->ProjectLivrable->id = $this->data['Upload']['id'];
                    $last = $this->ProjectLivrable->find('first', array(
                        'recursive' => -1,
                        // 'fields' => array('livrable_file_attachment'),
                        'conditions' => array('id' => $this->ProjectLivrable->id)));
                    if ($last && $last['ProjectLivrable']['livrable_file_attachment']) {
                        unlink($path . $last['ProjectLivrable']['livrable_file_attachment']);
                    }
                    if ($this->ProjectLivrable->save(array(
                                'livrable_file_attachment' => $attachment,
                                'format' => 2,
                                'upload_date' => time()
                            ))) {
                        // $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_livrables/index/' . $project_id);
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
        if($result){
            $this->loadModels('ProjectLivrableComment');
            $this->ProjectLivrableComment->create();
            $this->ProjectLivrableComment->save(array(
                'project_livrable_id' => $this->data['Upload']['id'],
                'employee_id' => $this->employee_info['Employee']['id'],
                'comment' => 'Document uploaded'
            ));
        }
        $this->redirect(array('action' => 'index', $project_id));
        $this->set(compact('result', 'attachment'));
    }

    public function attachment($id = null) {
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $last = $this->ProjectLivrable->find('first', array(
            'recursive' => -1,
            'fields' => array('livrable_file_attachment', 'project_id'),
            'conditions' => array('ProjectLivrable.id' => $id)));
		/**
		* Quan update PM Read Access co quyen open file o man hinh document 24-01-2019
		*/
		/** 
		* End update 24-01-2019
		*/
        $error = true;
        if ($last && $last['ProjectLivrable']['project_id']) {
			$project_id = $last['ProjectLivrable']['project_id'];
			$this->_checkRole(false, $project_id);
            $path = trim($this->_getPath($project_id)
                    . $last['ProjectLivrable']['livrable_file_attachment']);
            $attachment = $last['ProjectLivrable']['livrable_file_attachment'];
            if($type != 'delete'){
                if (file_exists($path) && is_file($path)) {
                    $info = pathinfo($path);
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
						'download' => true,
                        'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                    );
					if (!empty($info['extension']) && !in_array($info['extension'], array_keys($params['mimeType']))  ) {
                    // if (!empty($info['extension']) && !in_array( array('bmp','ppt','pps','docx','xlsx' ,'pptx'))  ) {
                       $params['mimeType'][$info['extension']] = 'application/octet-stream';
                    }
					// debug( $params); exit;
                    $this->set($params);
                    $error = false;
                }
            } else {
                @unlink($path);
                $this->ProjectLivrable->id = $id;
                $this->ProjectLivrable->save(array(
                    'livrable_file_attachment' => '',
                    'format' => 0,
                    'upload_date' => time()
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectLivrable']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_livrables/index/' . $last['ProjectLivrable']['project_id']);
                }
                $this->loadModels('ProjectLivrableComment');
                $this->ProjectLivrableComment->create();
                $this->ProjectLivrableComment->save(array(
                    'project_livrable_id' => $id,
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'comment' => 'Document deleted'
                ));
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectLivrable']['project_id'] : __('Unknown', true)));
        }
    }

    protected function _getPath($project_id) {
        $company = $this->ProjectLivrable->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectLivrable->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'livrable' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
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
        $conditions = array('ProjectLivrable.project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectLivrable.id'] = $data;
            }
        }

        $this->ProjectLivrable->Behaviors->attach('Containable');
        $this->ProjectLivrable->cacheQueries = true;
        $projectLivrables = $this->ProjectLivrable->find("all", array(
            'recursive' => 10,
            'fields' => array('*'),
            'contain' => array(
                'ProjectLivrableCategory' => array(
                    'id', 'livrable_cat'
                ),
                'ProjectStatus' => array(
                    'id', 'name'
                ),
                'ProjectLivrableActor' => array(
                    'Employee' => array('id', 'first_name', 'last_name'), 'fields' => array('id')
                ),
                'Employee' => array(
                    'id', 'first_name', 'last_name'
                )
            ), "conditions" => $conditions));
        $projectLivrables = Set::combine($projectLivrables, '{n}.ProjectLivrable.id', '{n}');
        $projectCode = $this->ProjectLivrable->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $project_id
            )
        ));
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectLivrables[$id])) {
                    unset($data[$id]);
                    unset($projectLivrables[$id]);
                    continue;
                }
                $data[$id] = $projectLivrables[$id];
            }
            $projectLivrables = $data;
            unset($data);
        }
        $this->loadModels('Employee', 'ProfitCenter');
        $listEm = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname', 'profit_center_id')
        ));
        $listEmIdOfPc = $listEm ? Set::combine($listEm, '{n}.Employee.id', '{n}.Employee.profit_center_id') : array();
        $listEm = $listEm ? Set::combine($listEm, '{n}.Employee.id', '{n}.Employee.fullname') : array();
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('projectLivrables', 'projectName', 'projectCode', 'listEm', 'listPc', 'listEmIdOfPc'));
        $this->layout = '';
    }

    /**
     * check_max_size
     *
     * @return void
     * @access public
     */
    function check_max_size() {
        $this->autoRender = false;
        if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {
            if (empty($_POST) && empty($_FILES)) {
                $length = $_SERVER['CONTENT_LENGTH'];
                $length = round($length / 1024 / 1024, 2);
            }
        }
        if ($length > 25) { // if max size upload larger 25M
            exit(0);
        } else {
            exit(1);
        }
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit() {
        //$this->updateProjectLivrable($this->data, $this->data['ProjectLivrable']['project_id']);
        //$this->data["ProjectLivrable"]["project_id"] = $project_id;
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();

        $max_size = $str_utility->checkMaxPostSize();
        $max_size = round($max_size / 1024 / 1024, 2);
        if ($max_size > 25) {
            echo ("<script>alert('" . __("Error: The file attachment is larger than 25MB!", true) . "'); history.back();</script>");
            exit();
        } else {
            $project_id = $this->data["ProjectLivrable"]["project_id"];
            $this->data["ProjectLivrable"]["livrable_date_delivery"] = $str_utility->convertToSQLDate($this->data["ProjectLivrable"]["livrable_date_delivery"]);
            $this->data["ProjectLivrable"]["livrable_date_delivery_planed"] = $str_utility->convertToSQLDate($this->data["ProjectLivrable"]["livrable_date_delivery_planed"]);
            $projectLivrableCates = $this->ProjectLivrable->ProjectLivrableCategory->find("list", array(
                "fields" => array("ProjectLivrableCategory.id",
                    "ProjectLivrableCategory.livrable_cat")));

            $prj = $this->ProjectLivrable->Project->find('first', array('conditions' => array('Project.id' => $project_id)));
            $cmp = $this->ProjectLivrable->Project->Company->find('first', array('recursive' => 0, 'conditions' => array('Company.id' => $prj['Company']['parent_id'])));
            $pdir = strtolower(str_replace(' ', '_', $cmp['Company']['company_name']));
            $udir = strtolower(str_replace(' ', '_', $prj['Company']['company_name']));
            if (!empty($pdir))
                $udir = $pdir . "/" . $udir;
            if (!is_dir("files/projects/livrable/" . $pdir)) {
                mkdir("files/projects/livrable/" . $pdir);
                chmod("files/projects/livrable/" . $pdir, 0777);
            }
            if (!is_dir("files/projects/livrable/" . $udir)) {
                mkdir("files/projects/livrable/" . $udir);
                chmod("files/projects/livrable/" . $udir, 0777);
            }
            $reVal = "";
            if (!empty($this->params['form']['FileField'])) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = "files/projects/livrable/" . $udir . "/";

                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                //exit();
                $reVal = $this->MultiFileUpload->upload();
            }
            if (!empty($reVal)) { // file upload successful
                $this->data["ProjectLivrable"]["livrable_file_attachment"] = $udir . "/" . $reVal['livrable_file_attachment']['livrable_file_attachment'];

                if ($this->ProjectLivrable->save($this->data["ProjectLivrable"])) {
                    if (empty($this->data['ProjectLivrable']['id']))
                        $id_project_livrable = $this->ProjectLivrable->getLastInsertID();
                    else
                        $id_project_livrable = $this->data['ProjectLivrable']['id'];
                    if (!empty($this->params['form']['ProjectLivrableLivrableActor'])) {
                        $data_livrable_actors = $this->params['form']['ProjectLivrableLivrableActor'];
                        $this->ProjectLivrable->ProjectLivrableActor->saveLivrableActors($data_livrable_actors, $id_project_livrable, $project_id);
                    }

                    $this->Session->setFlash(sprintf(__('The project delivrable %s has been saved', true), '<b>' . $projectLivrableCates[$this->data['ProjectLivrable']['project_livrable_category_id']] . '</b>'), 'success');
                } else {
                    $this->Session->setFlash(__('The project delivrable could not be saved. Please try again enter informations for (*) fields match.', true), 'error');
                }
            } else {
                if (empty($this->data['ProjectLivrable']['id']))
                    $this->data["ProjectLivrable"]["livrable_file_attachment"] = "";
                if ($this->ProjectLivrable->save($this->data["ProjectLivrable"])) {
                    if (empty($this->data['ProjectLivrable']['id']))
                        $project_livrable_id = $this->ProjectLivrable->getLastInsertID();
                    else
                        $project_livrable_id = $this->data['ProjectLivrable']['id'];

                    if (!empty($this->params['form']['ProjectLivrableLivrableActor'])) {
                        $data_livrable_actors = $this->params['form']['ProjectLivrableLivrableActor'];
                        $this->ProjectLivrable->ProjectLivrableActor->saveLivrableActors($data_livrable_actors, $project_livrable_id, $project_id);
                    }
                    $this->Session->setFlash(sprintf(__('The project delivrable %s has been saved but file is not attached', true), '<b>' . $projectLivrableCates[$this->data['ProjectLivrable']['project_livrable_category_id']] . '</b>'), 'warning');
                } else {
                    $this->Session->setFlash(__('The project delivrable could not be saved. Please try again enter informations for (*) fields match.', true), 'error');
                }
            }
            $this->redirect('/project_livrables/index/' . $this->data['ProjectLivrable']['project_id']);
        }
    }

    /**
     * updateProjectLivrable
     *
     * @return void
     * @access public
     */
    function updateProjectLivrable($data, $project_id) {

    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null,$project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project delivrable', true), 'error');
            $this->redirect($this->referer());
        }
        $last = $this->ProjectLivrable->find('first', array(
            'recursive' => -1,
            'fields' => array('project_id','livrable_file_attachment'),
            'conditions' => array(
				'id' => $id,
			)
		));
		$canModified = !empty($last) ? $this->_checkRole(false, $last['ProjectLivrable']['project_id'])  : false;
        if ($canModified && $this->ProjectLivrable->delete($id)) {
            @unlink(trim($this->_getPath($last['ProjectLivrable']['project_id'])
                    . $last['ProjectLivrable']['livrable_file_attachment']));
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('Project delivrable was not deleted', true), 'error');
        $this->redirect($this->referer());
    }

    /**
     * delete_livrable_file
     * Delete file attachment livrable
     * @return void
     * @access public
     */
    function delete_livrable_file($project_livrable_id = null) {
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }

        if (!$project_livrable_id) {
            $this->Session->setFlash(__('Invalid id for livrable of project', true), 'error');
            $this->redirect($this->referer());
        }
        $this->ProjectLivrable->id = $project_livrable_id;
        $file2del = "files/projects/livrable/" . $this->ProjectLivrable->field("livrable_file_attachment");
        unlink($file2del);
        $this->ProjectLivrable->set('livrable_file_attachment', '');
        if ($this->ProjectLivrable->save()) {
            $this->Session->setFlash(__('The attached file of the project livrable is deleted', true), 'success');
            $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('The file of the project livrable was not deleted', true), 'error');
        $this->redirect($this->referer());
    }

    /**
     * exportExcel
     * Export to Excel
     * @return void
     * @access public
     */
    function exportExcel($project_id = null) {
        //$this->layout = 'excel';
        $this->set('columns', $this->name_columna);
        $this->paginate = array("conditions" => array('Project.id' => $project_id));
        $company_id = $this->ProjectLivrable->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $project_id)));
        $company_id = $company_id['Project']['company_id'];
        $projectLivrableCategories = $this->ProjectLivrable->ProjectLivrableCategory->find("list", array(
            "fields" => array("ProjectLivrableCategory.id",
                "ProjectLivrableCategory.livrable_cat"), 'conditions' => array('ProjectLivrableCategory.company_id' => $company_id)));

        $projectLivrableActors = $this->ProjectLivrable->ProjectLivrableActor->find("all", array('conditions' => array(
                'ProjectLivrableActor.project_id' => $project_id
                )));
        $projectStatuses = $this->ProjectLivrable->Project->ProjectStatus->find('list', array('conditions' => array('ProjectStatus.company_id' => $company_id)));


        $employee_ids = $this->ProjectLivrable->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $employes = $this->ProjectLivrable->Employee->find('list', array("fields" => array("Employee.id", "Employee.fullname")));
        $projectManagers = array();
        foreach ($employee_ids as $key => $value) {
            foreach ($employes as $key2 => $name) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $name;
                    break;
                }
            }
        }
        $projectStatuses = $this->ProjectLivrable->Project->ProjectStatus->find('list', array('conditions' => array('ProjectStatus.company_id' => $company_id)));
        $this->set("project_id", $project_id);
        $this->set('projectLivrables', $this->paginate());
        $this->set('projectName', $this->ProjectLivrable->Project->find("first", array("fields" => array("Project.project_name"),
                    'conditions' => array('Project.id' => $project_id))));
        $this->set(compact('projectLivrables', 'projectLivrableCategories', 'projectLivrableActors', 'projectStatuses', 'projectManagers'
                ));
    }
    public function getComment(){
        if(!empty($_POST['id'])){
            $id = $_POST['id'];
            $idEmployee = $this->employee_info['Employee']['id'];
            $this->loadModels('ProjectLivrableComment');
            $comments = $this->ProjectLivrableComment->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_livrable_id' => $id
                ),
                'fields' => array('*')
            ));

            $data['idEm'] = $idEmployee;
            $listComments = !empty($comments) ? Set::combine($comments, '{n}.ProjectLivrableComment.id', '{n}.ProjectLivrableComment') : array();
            $listIdEm = !empty($comments) ? array_unique(Set::classicExtract($comments, '{n}.ProjectLivrableComment.employee_id')) : array();
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
                $_comment['comment'] = __($_comment['comment'], true);
                $_id = $_comment['employee_id'];
                $_comment['employee_id'] = $employees[$_id];
                $data['comment'][] = $_comment;
            }
            die(json_encode($data));
        }
        exit;
    }
    public function update_text(){
        $result = array();
        if( !empty($this->data['id']) ){
            $employee = $this->employee_info['Employee']['id'];

            if($this->employee_info['Employee']['company_id'] == null){
                $this->loadModel('Employee');
                $_idEm = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'fullname' => $this->employee_info['Employee']['fullname'],
                        'company_id' => null
                    ),
                    'fields' => array('id', 'fullname')
                ));
                $employee = !empty($_idEm['Employee']['id']) ? $_idEm['Employee']['id'] : 0;
            }
            $result['_idEm'] = $employee;
            $result['text_updater'] = $this->employee_info['Employee']['fullname'];
            $result['text_time'] = date('Y-m-d H:i:s');
            $result['comment'] = $this->data['text_1'];
            $this->loadModel('ProjectLivrableComment');
            $this->ProjectLivrableComment->create();
            $this->ProjectLivrableComment->save(array(
                'project_livrable_id' => $this->data['id'],
                'employee_id' => $employee,
                'comment' => $this->data['text_1'],
                'created' => $result['text_time']
            ));
        }
        die(json_encode($result));
    }
    public function getCommentTxt(){
        $this->layout = false;
        $results = array();
        if(!empty($_POST)){
            $pTaskId = $_POST['id'];
            $results['id'] = $pTaskId;
            $this->loadModels('ProjectLivrableComment');
            $result = $this->ProjectLivrableComment->find('all', array(
               'recursive' => -1,
                'conditions' => array('project_livrable_id' => $pTaskId),
                'fields' => array('id', 'employee_id', 'comment', 'created'),
                'order' => array('created' => 'DESC')
            ));
            $results['result'] = $result;
        }
        die(json_encode($results));
    }
    public function saveHiddenColums($project_id = ""){
        if(!empty($project_id) && !empty($_POST)){
            $checked = $_POST['checked'];
            $url = $_POST['url'];
            $this->loadModels('HistoryFilter');
            $last = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => $url,
                    'employee_id' => $this->employee_info['Employee']['id']
                )
            ));
            if(!empty($last)){
                $this->HistoryFilter->id = $last['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $checked));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => $url,
                    'params' => $checked,
                    'employee_id' => $this->employee_info['Employee']['id']
                ));
            }
            die(json_encode(true));
        }
        exit;
    }
    /**
     * Upload
     */
    public function uploads($project_id = null, $key = null) {
        $this->layout = 'ajax';
        $result = array();
        $_FILES['FileField'] = array();
        $company_id = $this->employee_info['Company']['id'];
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if(!empty($_FILES)){
            $path = $this->_getPath($project_id);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
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
                $this->ProjectLivrable->id = $key;
                $last = $this->ProjectLivrable->find('first', array(
                    'recursive' => -1,
                    // 'fields' => array('livrable_file_attachment'),
                    'conditions' => array('id' => $this->ProjectLivrable->id)));
                if ($last && $last['ProjectLivrable']['livrable_file_attachment']) {
                    unlink($path . $last['ProjectLivrable']['livrable_file_attachment']);
                };
                if ($this->ProjectLivrable->save(array(
                    'livrable_file_attachment' => $attachment,
                    'format' => 2,
                    'upload_date' => time()
                ))) {
                    $lastId = $this->ProjectLivrable->id;
                    $result = $this->ProjectLivrable->find('first', array('recursive' => -1, 'conditions' => array('ProjectLivrable.id' => $lastId)));
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
    /* By Dai Huynh 19-07-2018 */ 
    public function add_new_document($project_id = null){
		if ( !$this->_checkRole(false, $project_id) ) {
			$this->Session->setFlash(__('Permission denied', true), 'error');
			exit;
		}
        $this->loadModel('ProjectLivrable');
        $result = false;
        $newDocumentId = '';
        if( empty($this->data['popupUpload']['name'])){
            $this->Session->setFlash(__('Unable to create new Document. Name is mising', true), 'error');
        }
        else{
            //Create new Document
            $new_data = array(
                'id'=> 0,
                'project_livrable_category_id'=> '',
                'project_livrable_status_id' => '',
                'project_id' => $project_id,
                'livrable_progression' => 0,
                'livrable_responsible' => '',
                'livrable_date_delivery' => '',
                'livrable_date_delivery_planed' => '',
                'livrable_file_attachment'=> '',
                'format'=> '',
                'version'=> '',
                'name'=> $this->data['popupUpload']['name'],
                'weight'=> 0,

                );
            //$this->data = $new_data;
            /* Copy from update() function */

            $result = false;
            $this->layout = false;
            if (!empty($new_data)) {
                $this->ProjectLivrable->create();

                if (!empty($new_data['id'])) {
                    $this->ProjectLivrable->id = $new_data['id'];
                }
                $data = array();
                foreach (array('livrable_date_delivery', 'livrable_date_delivery_planed') as $key) {
                    if (!empty($new_data[$key])) {
                        $data[$key] = $this->ProjectLivrable->convertTime($new_data[$key]);
                    }
                }
                $data['employee_id_upload'] = $this->employee_info['Employee']['id'];
                if( !$new_data['project_livrable_category_id'] ) $new_data['project_livrable_category_id'] = '';

                if ($this->_checkRole(false, $project_id)) {  
                    $projectName = $this->viewVars['projectName'];
                    unset($new_data['id']);
                    if ($this->ProjectLivrable->save(array_merge(array_diff_key($new_data, array('actor_list' => '')), $data))) {
                        $result = true;

                        // $this->Session->setFlash(__('Saved', true), 'success');
                        if (!empty($new_data['actor_list'])) {
                            $saved = $this->ProjectLivrable->ProjectLivrableActor->find('all', array(
                                'fields' => array('id', 'project_id', 'employee_id', 'project_livrable_id'),
                                'conditions' => array(
                                    'project_livrable_id' => $this->ProjectLivrable->id
                                ),
                                'recursive' => -1));
                            $saved = Set::combine($saved, '{n}.ProjectLivrableActor.employee_id', '{n}.ProjectLivrableActor');
                            foreach ($new_data['actor_list'] as $employee) {
                                $this->ProjectLivrable->ProjectLivrableActor->create();
                                $data = array(
                                    'employee_id' => $employee,
                                    'project_id' => $projectName['Project']['id'],
                                    'project_livrable_id' => $this->ProjectLivrable->id
                                );
                                $last = isset($saved[$employee]) ? $saved[$employee] : null;
                                if ($last) {
                                    $data = array_merge($last, $data);
                                    $this->ProjectLivrable->ProjectLivrableActor->id = $data['id'];
                                    unset($data['id']);
                                }
                                $this->ProjectLivrable->ProjectLivrableActor->save($data);
                                unset($saved[$employee]);
                            }
                            foreach ($saved as $_save) {
                                $this->ProjectLivrable->ProjectLivrableActor->delete($_save['id']);
                            }
                        }
                    } else {
                        $this->Session->setFlash(__('The Deliverables could not be saved. Please, try again.', true), 'error');
                    }
                    $new_data['id'] = $this->ProjectLivrable->id;
                }
                $this->loadModels('ProjectLivrableComment');
                $this->ProjectLivrableComment->create();
                $this->ProjectLivrableComment->save(array(
                    'project_livrable_id' => $new_data['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'comment' => 'Information of the document modified'
                ));
            } else {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            }

			/* End Copy update() function */
            if($result && $new_data['id']){
               $this->data['popupUpload']['id'] = $new_data['id'];
                /* Upload file */ 
                $_FILES['FileField'] = array();
                if(!empty($_FILES['file']['name'])){
                    $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                    $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                    $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                    $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                    $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
                    $filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);
                }
                $path = $this->_getPath($project_id);

                if(!empty($_FILES['file']['name'])){
                    unset($_FILES['file']);
                    App::import('Core', 'Folder');
                    new Folder($path, true, 0777);
                    if (file_exists($path)) {
                        $this->MultiFileUpload->encode_filename = false;
                        $this->MultiFileUpload->uploadpath = $path;
                        $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
                        $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                        $attachment = $this->MultiFileUpload->upload();
                    } else {
                        $attachment = "";
                        $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                                , true), $path), 'error');
                    }
                    if (!empty($attachment)) {
                        $attachment = $attachment['attachment']['attachment'];
                        $this->ProjectLivrable->id = $this->data['popupUpload']['id'];
                        $last = $this->ProjectLivrable->find('first', array(
                            'recursive' => -1,
                            // 'fields' => array('livrable_file_attachment'),
                            'conditions' => array('id' => $this->ProjectLivrable->id)));
                        if ($last && $last['ProjectLivrable']['livrable_file_attachment']) {
                            unlink($path . $last['ProjectLivrable']['livrable_file_attachment']);
                        }
                        if ($this->ProjectLivrable->save(array(
                                    'livrable_file_attachment' => $attachment,
                                    'format' => 2,
                                    'upload_date' => time()
                                ))) {
                            // $this->Session->setFlash(__('Saved', true), 'success');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_livrables/index/' . $project_id);
                            }
                            $result = true;
                            $this->Session->setFlash(__('Saved', true), 'success');
                        } else {
                            unlink($path . $attachment);
                            $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                        }
                    } else {
                        $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
                    }
                }
                // neu co url
                elseif(!empty($this->data['popupUpload']['url'])){
                    $pos1 = strpos($this->data['popupUpload']['url'], 'ttps://');
                    $pos2 = strpos($this->data['popupUpload']['url'], 'ttp://');
                    if((!empty($pos1) && $pos1 == 1) || (!empty($pos2) && $pos2 == 1)){
                        $format = 1;
                    } else {
                        $format = 3;
                    }
                    $this->ProjectLivrable->id = $this->data['popupUpload']['id'];
                    $last = $this->ProjectLivrable->find('first', array(
                        'recursive' => -1,
                        'fields' => array('livrable_file_attachment'),
                        'conditions' => array('id' => $this->ProjectLivrable->id)));
                    if ($last && $last['ProjectLivrable']['livrable_file_attachment']) {
                        unlink($path . $last['ProjectLivrable']['livrable_file_attachment']);
                    }
					//Comment ham cleanHttpString de xoa chuoi http va htpps . By QuanNV
                    // $this->data['popupUpload']['url'] = $this->LogSystem->cleanHttpString($this->data['popupUpload']['url']);
                    if ($this->ProjectLivrable->save(array(
                                'livrable_file_attachment' => $this->data['popupUpload']['url'],
                                'format' => $format,
                                'upload_date' => time()
                            ))) {
                        // $this->Session->setFlash(__('Saved', true), 'success');
                        $result = true;
                        $this->Session->setFlash(__('Saved', true), 'success');
                    } else {
                        $this->Session->setFlash(__('The url could not be uploaded.', true), 'error');
                    }
                } 
            }
            if($result){
                $this->loadModels('ProjectLivrableComment');
                $this->ProjectLivrableComment->create();
                $this->ProjectLivrableComment->save(array(
                    'project_livrable_id' => $this->data['popupUpload']['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'comment' => 'Document uploaded'
                ));
            }
        }
        $this->set(compact('result'));
        if($this->enable_newdesign){
			$_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
			$_controller_preview =  trim( str_replace('_preview', '', $_controller)).'_preview';
			$_action = 'index';
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
}
?>
