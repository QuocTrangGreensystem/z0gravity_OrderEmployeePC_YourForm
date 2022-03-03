<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class BudgetProvidersController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'BudgetProviders';
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
            $this->BudgetProvider->cacheQueries = true;
            $budgetProviders = $this->BudgetProvider->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('budgetProviders', 'company_id', 'companyName'));
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
            $this->BudgetProvider->create();
            if (!empty($this->data['id'])) {
                $this->BudgetProvider->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->BudgetProvider->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Provider could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->BudgetProvider->id;
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
            $budgetProviders = $this->BudgetProvider->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'company_id'
                ), 'conditions' => array('BudgetProvider.id' => $this->data['Upload']['id'])));
            if ($budgetProviders) {
                $company_id = $budgetProviders['BudgetProvider']['company_id'];
            }
            $path = $this->_getPath($company_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->BudgetProvider->id = $this->data['Upload']['id'];
                $last = $this->BudgetProvider->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file_attachement'),
                    'conditions' => array('id' => $this->BudgetProvider->id)));
                if ($last && $last['BudgetProvider']['file_attachement']) {
                    unlink($path . $last['BudgetProvider']['file_attachement']);
                }
                if ($this->BudgetProvider->save(array(
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
                    $this->BudgetProvider->id = $this->data['Upload']['id'];
                    $last = $this->BudgetProvider->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file_attachement'),
                        'conditions' => array('id' => $this->BudgetProvider->id)));
                    if ($last && $last['BudgetProvider']['file_attachement']) {
                        unlink($path . $last['BudgetProvider']['file_attachement']);
                    }
                    if ($this->BudgetProvider->save(array(
                                'file_attachement' => $attachment,
                                'format' => 2
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/budget_providers/index/');
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
        $last = $this->BudgetProvider->find('first', array(
            'recursive' => -1,
            'fields' => array('file_attachement', 'company_id'),
            'conditions' => array('BudgetProvider.id' => $id)));
        $error = true;
        if ($last && $last['BudgetProvider']['company_id']) {
            $path = trim($this->_getPath($last['BudgetProvider']['company_id'])
                    . $last['BudgetProvider']['file_attachement']);
            $attachment = $last['BudgetProvider']['file_attachement'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                $path = trim($this->_getPath($last['BudgetProvider']['company_id']));
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
                $this->BudgetProvider->id = $id;
                $this->BudgetProvider->save(array(
                    'file_attachement' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['BudgetProvider']['company_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/budget_providers/index/');
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['BudgetProvider']['company_id'] : __('Unknown', true)));
        }
    }
    
    protected function _getPath($conmpany_id) {
        $company = $this->BudgetProvider->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 
            'conditions' => array('BudgetProvider.company_id' => $conmpany_id)
        ));
        $pcompany = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'budgets' . DS . 'providers' . DS;
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
        $this->loadModel('ProjectBudgetExternal');
        $checkProviders = $this->ProjectBudgetExternal->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetExternal.budget_provider_id' => $id)
            ));
        if($checkProviders != 0){
            $this->Session->setFlash(__('Provider already in used. You can not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        } else {
            $last = $this->BudgetProvider->find('first', array(
                'recursive' => -1,
                'fields' => array('company_id','file_attachement'),
                'conditions' => array(
					'BudgetProvider.id' => $id,
					'BudgetProvider.company_id' => $this->employee_info['company_id']
				)));
            if ($last && $this->BudgetProvider->delete($id)) {
                @unlink(trim($this->_getPath($last['BudgetProvider']['company_id'])
                        . $last['BudgetProvider']['file_attachement']));
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Provider is not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
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
}
?>