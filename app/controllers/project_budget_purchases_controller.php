<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetPurchasesController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectBudgetPurchases';

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
        $this->_checkWriteProfile('purchase');
        $this->loadModel('Project');
        $this->loadModel('BudgetCustomer');
        $this->loadModel('ProjectBudgetPurchaseInvoice');

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
        $this->ProjectBudgetPurchase->cacheQueries = true;
        $this->ProjectBudgetPurchase->recursive = -1;
        $this->ProjectBudgetPurchase->Behaviors->attach('Containable');

        $_budgetSales = $this->ProjectBudgetPurchase->find('all', array(
                'contain' => array('ProjectBudgetPurchaseInvoice'),
                'conditions' => array('project_id' => $project_id)
            ));
        $budgetSales = array();
        if(!empty($_budgetSales)){
            $i = 999999999999;
            $billed = $paid = $billed_check = array();
            foreach($_budgetSales as $_budgetSale){
                $budgetSales[] = $_budgetSale['ProjectBudgetPurchase'];
                if(!empty($_budgetSale['ProjectBudgetPurchaseInvoice'])){
                    foreach($_budgetSale['ProjectBudgetPurchaseInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $values['billed_check'] = $values['billed'];
                        } else {
                            $values['billed_check'] = '';
                        }
                        $values['id_invoice'] = $values['id'];
                        $values['id'] = 'invoi-'.$i++;
                        $budgetSales[] = $values;
                        if(!isset($billed[$values['project_budget_purchase_id']])){
                            $billed[$values['project_budget_purchase_id']] = 0;
                        }
                        $billed[$values['project_budget_purchase_id']] += $values['billed'];
                        if(!isset($paid[$values['project_budget_purchase_id']])){
                            $paid[$values['project_budget_purchase_id']] = 0;
                        }
                        $paid[$values['project_budget_purchase_id']] += $values['paid'];
                        if(!isset($billed_check[$values['project_budget_purchase_id']])){
                            $billed_check[$values['project_budget_purchase_id']] = 0;
                        }
                        $billed_check[$values['project_budget_purchase_id']] += $values['billed_check'];
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
        $this->loadModel('ProjectBudgetPurchaseInvoice');
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            if(isset($this->data['project_budget_purchase_id']) && !empty($this->data['project_budget_purchase_id'])){
                unset($this->data['id']);
                $this->ProjectBudgetPurchaseInvoice->create();
                if (!empty($this->data['id_invoice'])) {
                    $this->ProjectBudgetPurchaseInvoice->id = $this->data['id_invoice'];
                }
                $data = array();
                $data = array(
                    'billed_check'       => (isset($this->data['billed_check']) && $this->data['billed_check'] == 'billed')
                );
                foreach (array('due_date', 'effective_date') as $key) {
                    if (!empty($this->data[$key])) {
                        $data[$key] = $this->ProjectBudgetPurchaseInvoice->convertTime($this->data[$key]);
                    }
                }
                unset($this->data['id_invoice']);
                if ($this->ProjectBudgetPurchaseInvoice->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Budget Invoices could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id_invoice'] = $this->ProjectBudgetPurchaseInvoice->id;
            } else {
                $this->ProjectBudgetPurchase->create();
                if (!empty($this->data['id'])) {
                    $this->ProjectBudgetPurchase->id = $this->data['id'];
                }
                $data = array();
                foreach (array('order_date') as $key) {
                    if (!empty($this->data[$key])) {
                        $data[$key] = $this->ProjectBudgetPurchase->convertTime($this->data[$key]);
                    }
                }
                unset($this->data['id']);
                if ($this->ProjectBudgetPurchase->save(array_merge($this->data, $data))) {
                    $result = true;
                    // $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Budget Sales could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectBudgetPurchase->id;
            }
            if( $result ){
                $projectName = $this->ProjectBudgetPurchase->Project->find("first", array(
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
        $this->loadModel('ProjectBudgetPurchaseInvoice');
        $result = false;
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $project_id = __('Unknown', true);
            // neu khong co id_invoice thi xu ly phan lien sales
            if(empty($this->data['Upload']['id_invoice'])){
                $ProjectBudgetPurchases = $this->ProjectBudgetPurchase->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'project_id'
                    ), 'conditions' => array('ProjectBudgetPurchase.id' => $this->data['Upload']['id'])));
                if ($ProjectBudgetPurchases) {
                    $project_id = $ProjectBudgetPurchases['ProjectBudgetPurchase']['project_id'];
                }

                $path = $this->_getPath($project_id);
                // neu co url
                if(!empty($this->data['Upload']['url'])){
                    $this->ProjectBudgetPurchase->id = $this->data['Upload']['id'];
                    $last = $this->ProjectBudgetPurchase->find('first', array(
                        'recursive' => -1,
                        'fields' => array('name', 'file_attachement'),
                        'conditions' => array('id' => $this->ProjectBudgetPurchase->id)));
                    if ($last && $last['ProjectBudgetPurchase']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetPurchase']['file_attachement']);
                    }
                    $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                    if ($this->ProjectBudgetPurchase->save(array(
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
                        $this->ProjectBudgetPurchase->id = $this->data['Upload']['id'];
                        $last = $this->ProjectBudgetPurchase->find('first', array(
                            'recursive' => -1,
                            'fields' => array('name', 'file_attachement'),
                            'conditions' => array('id' => $this->ProjectBudgetPurchase->id)));
                        if ($last && $last['ProjectBudgetPurchase']['file_attachement']) {
                            unlink($path . $last['ProjectBudgetPurchase']['file_attachement']);
                        }
                        if ($this->ProjectBudgetPurchase->save(array(
                                    'file_attachement' => $attachment,
                                    'format' => 2
                                ))) {
                            $this->Session->setFlash(__('Saved', true), 'success');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_budget_purchases/index/' . $project_id);
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
                    $projectName = $this->ProjectBudgetPurchase->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Update attachment for sale budget `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchase']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                }

            } else {
                // luu file attach cho invoice
                $ProjectBudgetPurchaseInvoices = $this->ProjectBudgetPurchaseInvoice->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'project_id'
                    ), 'conditions' => array('ProjectBudgetPurchaseInvoice.id' => $this->data['Upload']['id_invoice'])));
                if ($ProjectBudgetPurchaseInvoices) {
                    $project_id = $ProjectBudgetPurchaseInvoices['ProjectBudgetPurchaseInvoice']['project_id'];
                }
                $path = $this->_getPath($project_id);
                // neu co url
                if(!empty($this->data['Upload']['url'])){
                    $this->ProjectBudgetPurchaseInvoice->id = $this->data['Upload']['id_invoice'];
                    $last = $this->ProjectBudgetPurchaseInvoice->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement', 'name_invoi'),
                        'conditions' => array('id' => $this->ProjectBudgetPurchaseInvoice->id)));
                    if ($last && $last['ProjectBudgetPurchaseInvoice']['file_attachement']) {
                        unlink($path . $last['ProjectBudgetPurchaseInvoice']['file_attachement']);
                    }
                    $this->data['Upload']['url'] = $this->LogSystem->cleanHttpString($this->data['Upload']['url']);
                    if ($this->ProjectBudgetPurchaseInvoice->save(array(
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
                        $this->ProjectBudgetPurchaseInvoice->id = $this->data['Upload']['id_invoice'];
                        $last = $this->ProjectBudgetPurchaseInvoice->find('first', array(
                            'recursive' => -1,
                            'fields' => array('file_attachement', 'name_invoi'),
                            'conditions' => array('id' => $this->ProjectBudgetPurchaseInvoice->id)));
                        if ($last && $last['ProjectBudgetPurchaseInvoice']['file_attachement']) {
                            unlink($path . $last['ProjectBudgetPurchaseInvoice']['file_attachement']);
                        }
                        if ($this->ProjectBudgetPurchaseInvoice->save(array(
                                    'file_attachement' => $attachment,
                                    'format' => 2
                                ))) {
                            $this->Session->setFlash(__('Saved', true), 'success');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_budget_purchases/index/' . $project_id);
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
                    $projectName = $this->ProjectBudgetPurchaseInvoice->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Update attachment for sale invoice `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchaseInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                }
            }

        }
        $this->redirect(array('action' => 'index', $project_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }

    public function attachement($id = null, $checkTypes) {
        $this->loadModel('ProjectBudgetPurchaseInvoice');
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        if(!empty($checkTypes)){
            if($checkTypes == 'sale'){
                $last = $this->ProjectBudgetPurchase->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement', 'project_id', 'name'),
                    'conditions' => array('ProjectBudgetPurchase.id' => $id)));
                $error = true;
                if ($last && $last['ProjectBudgetPurchase']['project_id']) {
                    $path = trim($this->_getPath($last['ProjectBudgetPurchase']['project_id'])
                            . $last['ProjectBudgetPurchase']['file_attachement']);
                    $attachment = $last['ProjectBudgetPurchase']['file_attachement'];
                    if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                        $this->view = 'Media';
                        $path = trim($this->_getPath($last['ProjectBudgetPurchase']['project_id']));
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
                        $this->ProjectBudgetPurchase->id = $id;
                        $this->ProjectBudgetPurchase->save(array(
                            'file_attachement' => '',
                            'format' => 0
                        ));
                        $projectName = $this->ProjectBudgetPurchase->Project->find("first", array(
                            'recursive' => -1,
                            "fields" => array('project_name', 'company_id'),
                            'conditions' => array('Project.id' => $last['ProjectBudgetPurchase']['project_id'])));
                        $message = 'Delete attachment of sale budget `%s` of project `%s`';
                        $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchase']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                        if($this->MultiFileUpload->otherServer == true){
                            $path = trim($this->_getPath($last['ProjectBudgetPurchase']['project_id']));
                            $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_budget_purchases/index/' . $last['ProjectBudgetPurchase']['project_id']);
                        }
                    }
                }
                if ($type != 'download') {
                    $this->Session->delete('Message.flash');
                    exit();
                } elseif ($error) {
                    $this->Session->setFlash(__('File not found.', true), 'error');
                    $this->redirect(array('action' => 'index',
                        $last ? $last['ProjectBudgetPurchase']['project_id'] : __('Unknown', true)));
                }
            } elseif($checkTypes == 'invoice') {
                $last = $this->ProjectBudgetPurchaseInvoice->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement', 'project_id', 'name_invoi'),
                    'conditions' => array('ProjectBudgetPurchaseInvoice.id' => $id)));
                $error = true;
                if ($last && $last['ProjectBudgetPurchaseInvoice']['project_id']) {
                    $path = trim($this->_getPath($last['ProjectBudgetPurchaseInvoice']['project_id'])
                            . $last['ProjectBudgetPurchaseInvoice']['file_attachement']);
                    $attachment = $last['ProjectBudgetPurchaseInvoice']['file_attachement'];
                    if (file_exists($path) && is_file($path)) {
                        if ($type == 'download') {
                            if($this->MultiFileUpload->otherServer == true){
                                $this->view = 'Media';
                                $path = trim($this->_getPath($last['ProjectBudgetPurchaseInvoice']['project_id']));
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
                        $this->ProjectBudgetPurchaseInvoice->id = $id;
                        $this->ProjectBudgetPurchaseInvoice->save(array(
                            'file_attachement' => '',
                            'format' => 0
                        ));
                        $projectName = $this->ProjectBudgetPurchaseInvoice->Project->find("first", array(
                            'recursive' => -1,
                            "fields" => array('project_name', 'company_id'),
                            'conditions' => array('Project.id' => $last['ProjectBudgetPurchaseInvoice']['project_id'])));
                        $message = 'Delete attachment of sale invoice `%s` of project `%s`';
                        $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchaseInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                        if($this->MultiFileUpload->otherServer == true){
                            $path = trim($this->_getPath($last['ProjectBudgetPurchaseInvoice']['project_id']));
                            $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_budget_purchases/index/' . $last['ProjectBudgetPurchaseInvoice']['project_id']);
                        }
                    }
                }
                if ($type != 'download') {
                    $this->Session->delete('Message.flash');
                    exit();
                } elseif ($error) {
                    $this->Session->setFlash(__('File not found.', true), 'error');
                    $this->redirect(array('action' => 'index',
                        $last ? $last['ProjectBudgetPurchaseInvoice']['project_id'] : __('Unknown', true)));
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
        $this->loadModel('ProjectBudgetPurchaseInvoice');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if($checkTypes == 'sale'){
            $invoices = $this->ProjectBudgetPurchaseInvoice->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetPurchaseInvoice.project_budget_purchase_id' => $id)
            ));
            if($invoices != 0){
                $this->Session->setFlash(__('Can not deleted. The Sales already exist "Invoice"', true), 'error');
                $this->redirect(array('action' => 'index', $project_id));
            } else {
                $last = $this->ProjectBudgetPurchase->find('first', array(
                    'recursive' => -1,
                    'fields' => array('name', 'project_id','file_attachement'),
                    'conditions' => array('ProjectBudgetPurchase.id' => $id)
				));
				$project_id = @$last['ProjectBudgetPurchase']['project_id'];
				if( !$this->_checkRole(false, $project_id)){
					$this->Session->setFlash( __('Permission denied', true), 'error');
					die;
				}
				$datas = array(
					'project_id' => $project_id,
					'activity_id' => 0
				);
                if ($last && $this->ProjectBudgetPurchase->delete($id)) {
                    @unlink(trim($this->_getPath($last['ProjectBudgetPurchase']['project_id'])
                            . $last['ProjectBudgetPurchase']['file_attachement']));
                    $this->ProjectBudgetPurchase->saveSaleToSyns($datas);
                    $projectName = $this->ProjectBudgetPurchase->Project->find("first", array(
                        'recursive' => -1,
                        "fields" => array('project_name', 'company_id'),
                        'conditions' => array('Project.id' => $project_id)));
                    $message = 'Delete sale budget `%s` of project `%s`';
                    $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchase']['name'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                    $this->Session->setFlash(__('Deleted', true), 'success');
                    $this->redirect(array('action' => 'index', $project_id));
                }
            }
        } elseif($checkTypes = 'invoice'){
            $last = $this->ProjectBudgetPurchaseInvoice->find('first', array(
                'recursive' => -1,
                'fields' => array('name_invoi', 'project_id','file_attachement'),
                'conditions' => array('ProjectBudgetPurchaseInvoice.id' => $id)
			));
			$project_id = @$last['ProjectBudgetPurchaseInvoice']['project_id'];
			if( !$this->_checkRole(false, $project_id)){
				$this->Session->setFlash( __('Permission denied', true), 'error');
				die;
			}
			$datas = array(
				'project_id' => $project_id,
				'activity_id' => 0
			);
            if ($last && $this->ProjectBudgetPurchaseInvoice->delete($id)) {
                @unlink(trim($this->_getPath($last['ProjectBudgetPurchaseInvoice']['project_id'])
                        . $last['ProjectBudgetPurchaseInvoice']['file_attachement']));
                $this->ProjectBudgetPurchaseInvoice->saveInvoiceToSyns($datas);
                $projectName = $this->ProjectBudgetPurchaseInvoice->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $project_id)));
                $message = 'Delete sale invoice `%s` of project `%s`';
                $this->writeLog($this->data, $this->employee_info, sprintf($message, $last['ProjectBudgetPurchaseInvoice']['name_invoi'], $projectName['Project']['project_name']), $projectName['Project']['company_id']);

                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index', $project_id));
            }
        } else {
            //do nothing
        }
        $this->Session->setFlash(__('Project Budget Purchase  was not deleted', true), 'error');
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
