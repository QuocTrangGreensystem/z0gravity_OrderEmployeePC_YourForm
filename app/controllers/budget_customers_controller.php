<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class BudgetCustomersController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'BudgetCustomers';
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
    var $components = array('MultiFileUpload');
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
        $this->loadModel('Company');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->BudgetCustomer->cacheQueries = true;
            $budgetCustomers = $this->BudgetCustomer->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('budgetCustomers', 'company_id', 'companyName'));
        }
																						 
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
        if (!empty($this->data) && $this->_getCompany()) {
            $this->BudgetCustomer->create();
            if (!empty($this->data['id'])) {
                $this->BudgetCustomer->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->BudgetCustomer->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Customer could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->BudgetCustomer->id;
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
    public function upload($company_id = null) {
        $result = false;
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $company_id = __('Unknown', true);
            $budgetCustomers = $this->BudgetCustomer->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'company_id'
                ), 'conditions' => array('BudgetCustomer.id' => $this->data['Upload']['id'])));
            if ($budgetCustomers) {
                $company_id = $budgetCustomers['BudgetCustomer']['company_id'];
            }
            $path = $this->_getPath($company_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->BudgetCustomer->id = $this->data['Upload']['id'];
                $last = $this->BudgetCustomer->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->BudgetCustomer->id)));
                if ($last && $last['BudgetCustomer']['file_attachement']) {
                    unlink($path . $last['BudgetCustomer']['file_attachement']);
                }
                if ($this->BudgetCustomer->save(array(
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
                    $this->BudgetCustomer->id = $this->data['Upload']['id'];
                    $last = $this->BudgetCustomer->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->BudgetCustomer->id)));
                    if ($last && $last['BudgetCustomer']['file_attachement']) {
                        unlink($path . $last['BudgetCustomer']['file_attachement']);
                    }
                    if ($this->BudgetCustomer->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/budget_customers/index/');
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
        $this->redirect(array('action' => 'index'));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }
    
    public function attachement($id = null) {
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $last = $this->BudgetCustomer->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'company_id'),
            'conditions' => array('BudgetCustomer.id' => $id)));
        $error = true;
        if ($last && $last['BudgetCustomer']['company_id']) {
            $path = trim($this->_getPath($last['BudgetCustomer']['company_id'])
                    . $last['BudgetCustomer']['file_attachement']);
            $attachment = $last['BudgetCustomer']['file_attachement'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                $path = trim($this->_getPath($last['BudgetCustomer']['company_id']));
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
                $this->BudgetCustomer->id = $id;
                $this->BudgetCustomer->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['BudgetCustomer']['company_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/budget_customers/index/');
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['BudgetCustomer']['company_id'] : __('Unknown', true)));
        }
    }
    
    protected function _getPath($conmpany_id) {
        $company = $this->BudgetCustomer->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 
            'conditions' => array('BudgetCustomer.company_id' => $conmpany_id)
        ));
        $pcompany = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'budgets' . DS . 'customers' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
    
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		if((!$this->is_sas) && (!$this->_isBelongToCompany($id, 'BudgetCustomer'))){
			$this->_functionStop(false, $id, __('You have not permission to access this function', true), false, array('action' => 'index'));
		}
        $allowDeleteBudgetCustomer = $this->_budgetCustomerIsUsing($id);
        if($allowDeleteBudgetCustomer == 'true'){
                 $last = $this->BudgetCustomer->find('first', array(
                'recursive' => -1,
                'fields' => array('company_id','file_attachement'),
                'conditions' => array('BudgetCustomer.id' => $id)));
                if ($last && $this->BudgetCustomer->delete($id)) {
                    @unlink(trim($this->_getPath($last['BudgetCustomer']['company_id'])
                            . $last['BudgetCustomer']['file_attachement']));
                    $this->Session->setFlash(__('Deleted', true), 'success');
                    $this->redirect(array('action' => 'index'));
                }
        } else {
            $this->Session->setFlash(__('Budget customer is being in used. You can not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Budget Customer was not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
    
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
         /**
     *  Kiem tra budget customer da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _budgetCustomerIsUsing($id = null){
        $this->loadModel('Activity');
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetSale');
        $checkActivity = $this->Activity->find('count', array(
                'recursive' => -1,
                'conditions' => array('Activity.budget_customer_id' => $id)
            ));
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.budget_customer_id' => $id)
            ));
        $checkProjectBudgetSale = $this->ProjectBudgetSale->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetSale.budget_customer_id' => $id)
            ));
        $allowDeleteBudgetCustomer= 'true';
        if($checkActivity != 0  || $checkProject != 0 || $checkProjectBudgetSale != 0){
            $allowDeleteBudgetCustomer = 'false';
        }
        
        return $allowDeleteBudgetCustomer;
    }
}
?>