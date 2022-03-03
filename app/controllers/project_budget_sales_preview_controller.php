<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetSalesPreviewController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ activity_budget_sales_controller --------------------------
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
    // var $name = 'ProjectBudgetSalesPreview';
    var $uses = 'ProjectBudgetSale';
    // var $uses = array('ProjectBudgetSale');
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

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
		$usCanWrite = ($this->employee_info['Role']['name'] == 'admin') || $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('sale');
        $this->loadModel('Project');
        $this->loadModel('BudgetCustomer');
        $this->loadModel('ProjectBudgetInvoice');

        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $activityLinked = !empty($projectName['Project']['activity_id']) ? $projectName['Project']['activity_id'] : 0;
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $projectName['Project']['company_id']),
                'fields' => array('id', 'name')
            ));
        $this->ProjectBudgetSale->cacheQueries = true;
        $this->ProjectBudgetSale->recursive = -1;
        $this->ProjectBudgetSale->Behaviors->attach('Containable');

        $_budgetSales = $this->ProjectBudgetSale->find('all', array(
                'contain' => array('ProjectBudgetInvoice'),
                'conditions' => array('project_id' => $project_id)
            ));
        $budgetSales = $billed  = $paid  = $billed_check  = array();
        if(!empty($_budgetSales)){
            $i = 999999999999;
            foreach($_budgetSales as $_budgetSale){
                $budgetSales[] = $_budgetSale['ProjectBudgetSale'];
                if(!empty($_budgetSale['ProjectBudgetInvoice'])){
                    foreach($_budgetSale['ProjectBudgetInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $values['billed_check'] = $values['billed'];
                        } else {
                            $values['billed_check'] = '';
                        }
                        $values['id_invoice'] = $values['id'];
                        $values['id'] = 'invoi-'.$i++;
                        $budgetSales[] = $values;
                        if(!isset($billed[$values['project_budget_sale_id']])){
                            $billed[$values['project_budget_sale_id']] = 0;
                        }
                        $billed[$values['project_budget_sale_id']] += $values['billed'];
                        if(!isset($paid[$values['project_budget_sale_id']])){
                            $paid[$values['project_budget_sale_id']] = 0;
                        }
                        $paid[$values['project_budget_sale_id']] += $values['paid'];
                        if(!isset($billed_check[$values['project_budget_sale_id']])){
                            $billed_check[$values['project_budget_sale_id']] = 0;
                        }
                        $billed_check[$values['project_budget_sale_id']] += $values['billed_check'];
                    }
                }
            }
        }
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
        $modifyBudget = $this->checkModifyBudget();
        $modifyBudget = $modifyBudget && $usCanWrite;
        if($projectName && $projectName['Project']['category'] == 2){
            $this->action = 'oppor';
        }
        $employee_info = $this->employee_info;
        $this->set(compact('projectName', 'project_id', 'budgetCustomers', 'budgetSales', 'billed', 'paid', 'billed_check', 'activityLinked', 'modifyBudget', 'employee_info','budget_settings'));
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $this->loadModel('ProjectBudgetInvoice');
        $result = false;
		$save_result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            if(isset($this->data['project_budget_sale_id']) && !empty($this->data['project_budget_sale_id'])){
                unset($this->data['id']);
                $this->ProjectBudgetInvoice->create();
                if (!empty($this->data['id_invoice'])) {
                    $this->ProjectBudgetInvoice->id = $this->data['id_invoice'];
                }
                $data = array();
                $data = array(
                    'billed_check'       => (isset($this->data['billed_check']) && $this->data['billed_check'] == 'billed')
                );
                foreach (array('due_date', 'effective_date') as $key) {
                    if (!empty($this->data[$key])) {
                        $data[$key] = $this->ProjectBudgetInvoice->convertTime($this->data[$key]);
                    }
                }
                unset($this->data['id_invoice']);
                if ($this->ProjectBudgetInvoice->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Budget Invoices could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id_invoice'] = $this->ProjectBudgetInvoice->id;
            } else {
                $this->ProjectBudgetSale->create();
                if (!empty($this->data['id'])) {
                    $this->ProjectBudgetSale->id = $this->data['id'];
                }
                $data = array();
                foreach (array('order_date') as $key) {
                    if (!empty($this->data[$key])) {
                        $data[$key] = $this->ProjectBudgetSale->convertTime($this->data[$key]);
                    }
                }
                unset($this->data['id']);
                if ($this->ProjectBudgetSale->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Budget Sales could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectBudgetSale->id;
            }
            if( $result ){
                $projectName = $this->ProjectBudgetSale->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $this->data['project_id'])));
                $name = isset($this->data['name_invoi']) ? $this->data['name_invoi'] : $this->data['name'];
                $message = isset($this->data['name_invoi']) ? 'Update sale invoice `%s` for project `%s`' : 'Update sale budget `%s` for project `%s`';
                $this->writeLog($this->data, $this->employee_info, sprintf($message, $name, $projectName['Project']['project_name']), $projectName['Project']['company_id']);
            }
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
		//debug($_FILES);
		// debug( $this->data); exit;
        $this->loadModel('ProjectBudgetInvoice');
        $result = false;
		$save_result = false;
		$this->data['Upload'] = $this->data['popupInvoiceUpload'];
		unset( $this->data['popupInvoiceUpload']);
		//debug( $this->data); exit;
		
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
			foreach(array('name', 'type', 'tmp_name', 'error', 'size') as $key){
				$_FILES['FileField'][$key]['attachment'] = isset($_FILES['file'][$key]) ? $_FILES['file'][$key] : '';
			}
            $project_id = __('Unknown', true);
            // neu khong co id_invoice thi xu ly phan lien sales
            if(empty($this->data['Upload']['id_invoice'])){
                $projectBudgetSales = $this->ProjectBudgetSale->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'project_id'
                    ), 'conditions' => array('ProjectBudgetSale.id' => $this->data['Upload']['id'])));
                if ($projectBudgetSales) {
                    $project_id = $projectBudgetSales['ProjectBudgetSale']['project_id'];
                }

                $path = $this->_getPath($project_id);
                // neu co url
                if(!empty($this->data['Upload']['url'])){
                    $this->ProjectBudgetSale->id = $this->data['Upload']['id'];
                    $last = $this->ProjectBudgetSale->find('first', array(
                        'recursive' => -1,
                        'fields' => array('name', 'file_attachement'),
                        'conditions' => array('id' => $this->ProjectBudgetSale->id)));
                    if ($last && $last['ProjectBudgetSale']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetSale']['file_attachement']);
                    }
                    $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                    if ($this->ProjectBudgetSale->save(array(
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
                        $this->ProjectBudgetSale->id = $this->data['Upload']['id'];
                        $last = $this->ProjectBudgetSale->find('first', array(
                            'recursive' => -1,
                            'fields' => array('name', 'file_attachement'),
                            'conditions' => array('id' => $this->ProjectBudgetSale->id)));
                        if ($last && $last['ProjectBudgetSale']['file_attachement']) {
                            unlink($path . $last['ProjectBudgetSale']['file_attachement']);
                        }
                        if ($this->ProjectBudgetSale->save(array(
                                    'file_attachement' => $attachment,
                                    'format' => 2
                                ))) {
                            $this->Session->setFlash(__('Saved', true), 'success');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_budget_sales/index/' . $project_id);
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
                if( $result ){
                    $projectName = $this->ProjectBudgetSale->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Update attachment for sale budget `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetSale']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                }

            } else {
                // luu file attach cho invoice
                $projectBudgetInvoices = $this->ProjectBudgetInvoice->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'project_id'
                    ), 'conditions' => array('ProjectBudgetInvoice.id' => $this->data['Upload']['id_invoice'])));
                if ($projectBudgetInvoices) {
                    $project_id = $projectBudgetInvoices['ProjectBudgetInvoice']['project_id'];
                }
                $path = $this->_getPath($project_id);
                // neu co url
                if(!empty($this->data['Upload']['url'])){
                    $this->ProjectBudgetInvoice->id = $this->data['Upload']['id_invoice'];
                    $last = $this->ProjectBudgetInvoice->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement', 'name_invoi'),
                        'conditions' => array('id' => $this->ProjectBudgetInvoice->id)));
                    if ($last && $last['ProjectBudgetInvoice']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetInvoice']['file_attachement']);
                    }
                    $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
					$save_result = $this->ProjectBudgetInvoice->save(array(
                                'file_attachement' => $this->data['Upload']['url'],
                                'format' => 1
                            ));
                    if ( !empty($save_result) ){
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
                        $this->ProjectBudgetInvoice->id = $this->data['Upload']['id_invoice'];
                        $last = $this->ProjectBudgetInvoice->find('first', array(
                            'recursive' => -1,
                            'fields' => array('file_attachement', 'name_invoi'),
                            'conditions' => array('id' => $this->ProjectBudgetInvoice->id)));
                        if ($last && $last['ProjectBudgetInvoice']['file_attachement']) {
                            unlink($path . $last['ProjectBudgetInvoice']['file_attachement']);
                        }
						$save_result = $this->ProjectBudgetInvoice->save(array(
                                    'file_attachement' => $attachment,
                                    'format' => 2
                                ));
						if ( !empty($save_result) ){
                            $this->Session->setFlash(__('Saved', true), 'success');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_budget_sales/index/' . $project_id);
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
                if( $result ){
                    $projectName = $this->ProjectBudgetInvoice->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Update attachment for sale invoice `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                }
            }

        }
		if( $this->params['isAjax']){
			$data = array(
				'result' => $result,
				'save_result' => $save_result,
				'data' => $this->data
			);
			die(json_encode($data));
		}
        $this->redirect(array('action' => 'index', $project_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }

    public function attachement($id = null, $checkTypes) {
        $this->loadModel('ProjectBudgetInvoice');
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        if(!empty($checkTypes)){
            if($checkTypes == 'sale'){
                $last = $this->ProjectBudgetSale->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement', 'project_id', 'name'),
                    'conditions' => array('ProjectBudgetSale.id' => $id)));
                $error = true;
                if ($last && $last['ProjectBudgetSale']['project_id']) {
                    $path = trim($this->_getPath($last['ProjectBudgetSale']['project_id'])
                            . $last['ProjectBudgetSale']['file_attachement']);
                    $attachment = $last['ProjectBudgetSale']['file_attachement'];
                    if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                        $this->view = 'Media';
                        $path = trim($this->_getPath($last['ProjectBudgetSale']['project_id']));
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
                        $this->ProjectBudgetSale->id = $id;
                        $this->ProjectBudgetSale->save(array(
                            'file_attachement' => '',
                            'format' => 0
                        ));
                        $projectName = $this->ProjectBudgetSale->Project->find("first", array(
                            'recursive' => -1,
                            "fields" => array('project_name', 'company_id'),
                            'conditions' => array('Project.id' => $last['ProjectBudgetSale']['project_id'])));
                        $message = 'Delete attachment of sale budget `%s` of project `%s`';
                        $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetSale']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                        if($this->MultiFileUpload->otherServer == true){
                            $path = trim($this->_getPath($last['ProjectBudgetSale']['project_id']));
                            $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_budget_sales/index/' . $last['ProjectBudgetSale']['project_id']);
                        }
                    }
                }
                if ($type != 'download') {
                    $this->Session->delete('Message.flash');
                    exit();
                } elseif ($error) {
                    $this->Session->setFlash(__('File not found.', true), 'error');
                    $this->redirect(array('action' => 'index',
                        $last ? $last['ProjectBudgetSale']['project_id'] : __('Unknown', true)));
                }
            } elseif($checkTypes == 'invoice') {
                $last = $this->ProjectBudgetInvoice->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement', 'project_id', 'name_invoi'),
                    'conditions' => array('ProjectBudgetInvoice.id' => $id)));
                $error = true;
                if ($last && $last['ProjectBudgetInvoice']['project_id']) {
                    $path = trim($this->_getPath($last['ProjectBudgetInvoice']['project_id'])
                            . $last['ProjectBudgetInvoice']['file_attachement']);
                    $attachment = $last['ProjectBudgetInvoice']['file_attachement'];
                    if (file_exists($path) && is_file($path)) {
                        if ($type == 'download') {
                            if($this->MultiFileUpload->otherServer == true){
                                $this->view = 'Media';
                                $path = trim($this->_getPath($last['ProjectBudgetInvoice']['project_id']));
                                $this->MultiFileUpload->downloadFileToServerOther($path, $attachment);
                            } else {
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
                        }
                        $error = false;
                    }
                    if ($type != 'download') {
                        @unlink($path);
                        $this->ProjectBudgetInvoice->id = $id;
                        $this->ProjectBudgetInvoice->save(array(
                            'file_attachement' => '',
                            'format' => 0
                        ));
                        $projectName = $this->ProjectBudgetInvoice->Project->find("first", array(
                            'recursive' => -1,
                            "fields" => array('project_name', 'company_id'),
                            'conditions' => array('Project.id' => $last['ProjectBudgetInvoice']['project_id'])));
                        $message = 'Delete attachment of sale invoice `%s` of project `%s`';
                        $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                        if($this->MultiFileUpload->otherServer == true){
                            $path = trim($this->_getPath($last['ProjectBudgetInvoice']['project_id']));
                            $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_budget_sales/index/' . $last['ProjectBudgetInvoice']['project_id']);
                        }
                    }
                }
                if ($type != 'download') {
                    $this->Session->delete('Message.flash');
                    exit();
                } elseif ($error) {
                    $this->Session->setFlash(__('File not found.', true), 'error');
                    $this->redirect(array('action' => 'index',
                        $last ? $last['ProjectBudgetInvoice']['project_id'] : __('Unknown', true)));
                }
            } else {
                //do nothing
            }
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
        $path = FILES . 'project_budgets' . DS . 'sales' . DS;
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
    function delete($id = null, $project_id = null, $checkTypes) {
        $this->loadModel('ProjectBudgetInvoice');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        $datas = array(
            'project_id' => $project_id,
            'activity_id' => 0
        );
        if($checkTypes == 'sale'){
            $invoices = $this->ProjectBudgetInvoice->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetInvoice.project_budget_sale_id' => $id)
            ));
            if($invoices != 0){
                $this->Session->setFlash(__('Can not deleted. The Sales already exist "Invoice"', true), 'error');
                $this->redirect(array('action' => 'index', $project_id));
            } else {
                $last = $this->ProjectBudgetSale->find('first', array(
                    'recursive' => -1,
                    'fields' => array('name', 'project_id','file_attachement'),
                    'conditions' => array('ProjectBudgetSale.id' => $id)));
				$project_id = @$last['ProjectBudgetSale']['project_id'];
				if( !$this->_checkRole(false, $project_id)){
					$this->Session->setFlash( __('Permission denied', true), 'error');
					die;
				}
				$datas = array(
					'project_id' => $project_id,
					'activity_id' => 0
				);
                if ($last && $this->ProjectBudgetSale->delete($id)) {
                    @unlink(trim($this->_getPath($last['ProjectBudgetSale']['project_id'])
                            . $last['ProjectBudgetSale']['file_attachement']));
                    $this->ProjectBudgetSale->saveSaleToSyns($datas);
                    $projectName = $this->ProjectBudgetSale->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Delete sale budget `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetSale']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                    $this->Session->setFlash(__('Deleted', true), 'success');
                    $this->redirect(array('action' => 'index', $project_id));
                }
            }
        } elseif($checkTypes = 'invoice'){
            $last = $this->ProjectBudgetInvoice->find('first', array(
                'recursive' => -1,
                'fields' => array('name_invoi', 'project_id','file_attachement'),
                'conditions' => array('ProjectBudgetInvoice.id' => $id)
			));
			$project_id = @$last['ProjectBudgetInvoice']['project_id'];
			if( !$this->_checkRole(false, $project_id)){
				$this->Session->setFlash( __('Permission denied', true), 'error');
				die;
			}
			$datas = array(
				'project_id' => $project_id,
				'activity_id' => 0
			);
            if ($last && $this->ProjectBudgetInvoice->delete($id)) {
                @unlink(trim($this->_getPath($last['ProjectBudgetInvoice']['project_id'])
                        . $last['ProjectBudgetInvoice']['file_attachement']));
                $this->ProjectBudgetInvoice->saveInvoiceToSyns($datas);
                $projectName = $this->ProjectBudgetInvoice->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $project_id)));
                $message = 'Delete sale invoice `%s` of project `%s`';
                $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index', $project_id));
            }
        } else {
            //do nothing
        }
        $this->Session->setFlash(__('Project Budget Sales  was not deleted', true), 'error');
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
}
?>
