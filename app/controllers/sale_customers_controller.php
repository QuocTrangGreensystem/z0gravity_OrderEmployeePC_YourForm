<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleCustomersController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleCustomers';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
    /**
     *  Get Currency Of Business
     */
    private function _getCurrencyBusiness(){
        $saleBusiness = ClassRegistry::init('SaleSetting')->find('first', array(
            'recursive' => -1,
            'conditions' => array('type' => 10, 'company_id' => $this->Session->read('Auth.employee_info.Company.id')),
            'fields' => array('name')
        ));
        return !empty($saleBusiness) && $saleBusiness['SaleSetting']['name'] ? $saleBusiness['SaleSetting']['name'] : '';
    }
    
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'PImage', 'LogSystem');
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
    }
    
    /**
     * List Category of Sales System
     */
    var $categoryOfSales = array('customer_status', 'customer_industry', 'customer_payment', 'customer_country');
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($category = null, $company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('SaleSetting');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            /**
             * Check Permission
             */
            list($read, $created, $updated, $delete) = $this->_checkPermissionIsUsingCustomer();
            $canModifyCustomer = true;
            if(!$created || !$updated || !$delete){
                $canModifyCustomer = false;
            }
            if($read == false){
                $this->Session->setFlash(__('You do not have permission in Business Customer.', true), 'error');
            } else {
                $company_id = $companyName['Company']['id'];
                /**
                 * Danh sach curtomer sales
                 */
                $salesCustomers = $this->SaleCustomer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id, 'type' => $category),
                    'fields' => array('id', 'name', 'sale_setting_customer_status', 'sale_setting_customer_industry', 'phone', 'sale_setting_customer_country', 'email')
                ));
                $salesCustomers = !empty($salesCustomers) ? Set::combine($salesCustomers, '{n}.SaleCustomer.id', '{n}.SaleCustomer') : array();
                /**
                 * Lay danh sach sale settings
                 * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
                 */
                if(Configure::read('Config.language') === 'eng'){
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name', 'type'),
                        'group' => array('type', 'id')
                    ));
                } else {
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name_fre', 'type'),
                        'group' => array('type', 'id')
                    ));
                }
            }
            
			$this->set(compact('company_id', 'companyName', 'salesCustomers', 'saleSettings', 'category', 'canModifyCustomer'));
        }   
    }
    
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($type = null, $category = null, $company_id = null, $id = null) {
        $this->loadModel('SaleSetting');
        $this->loadModel('SaleCustomerIban');
        $this->loadModel('SaleCustomerContact');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        if($this->employee_info['Company']['id'] != $company_id){
            $this->redirect(array('action' => 'index', $category));
        }
        $companyName = strtolower($this->employee_info['Company']['company_name']);
        $saleCurrency = $this->_getCurrencyBusiness();
        list($read, $created, $updated, $delete) = $this->_checkPermissionIsUsingCustomer();
        list($readSaleContact, $createdSaleContact, $updatedSaleContact, $deleteSaleContact) = $this->_checkPermissionIsUsingCustomerContact();
        if($created == false || $updated == false){
            $this->Session->setFlash(__('You have not permission to Created/Updated Business Customer', true), 'error');
        }
        if($id != null){
            $hasData = $this->SaleCustomer->find('count', array(
                'recursive' => -1,
                'conditions' => array('SaleCustomer.id' => $id, 'SaleCustomer.company_id' => $company_id)
            ));
            if( !$hasData ){
                $this->Session->setFlash(__('Data not found', true), 'error');
                $this->redirect(array('action' => 'index', $category));
            }
        }
        /**
         * Save Data
         */
        if(!empty($this->data)){
            /**
             * Check Permission
             */
            if($created == false || $updated == false){
                $this->Session->setFlash(__('You have not permission to Created/Updated Business Customer', true), 'error');
                $this->redirect(array('action' => 'update', $type, $category, $company_id, $id));
            }
            $this->SaleCustomer->create();
            if (!empty($this->data['SaleCustomer']['id'])) {
                $this->SaleCustomer->id = $this->data['SaleCustomer']['id'];
            }
            unset($this->data['SaleCustomer']['id']);
            $this->data['SaleCustomer']['company_id'] = $company_id;
            $this->data['SaleCustomer']['update_by_employee'] = $this->employee_info['Employee']['fullname'];
            $this->data['SaleCustomer']['type'] = $category;
            if($this->SaleCustomer->save($this->data['SaleCustomer'])){
                $id = $this->SaleCustomer->id;
                $this->Session->setFlash(__('Saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('Not Saved.', true), 'error');
            }
            $this->redirect(array('action' => 'update', $type, $category, $company_id, $id));
        } else {
            $this->data = $this->SaleCustomer->find('first', array(
                'recursive' => -1,
                'conditions' => array('SaleCustomer.id' => $id, 'SaleCustomer.company_id' => $company_id)
            ));
            if(empty($this->data) && !empty($id)){
                $this->redirect(array('action' => 'update', $type, $category, $company_id));
            }
        }
        /**
         * Lay danh sach sale settings
         * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
         */
        if(Configure::read('Config.language') === 'eng'){
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id')
            ));
        } else {
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name_fre', 'type'),
                'group' => array('type', 'id')
            ));
        }
        /**
         * Get Id of payment on receipt or = a réception
         */
        $paymentTypes = $this->SaleSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'SaleSetting.name' => 'payment on receipt',
                'type' => 2
            ),
            'fields' => array('id')
        ));
        $paymentTypes = !empty($paymentTypes) && $paymentTypes['SaleSetting']['id'] ? $paymentTypes['SaleSetting']['id'] : 0;
        /**
         * Get Iban
         */
        $saleIbans = $this->SaleCustomerIban->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'sale_customer_id' => $id,
                'company_id' => $company_id
            ),
            'fields' => array('id', 'defaults', 'bic', 'iban')
        ));
        $saleIbans = !empty($saleIbans) ? Set::combine($saleIbans, '{n}.SaleCustomerIban.id', '{n}.SaleCustomerIban') : array();
        /**
         * Get Contact
         */
        $saleContacts = $this->SaleCustomerContact->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'sale_customer_id' => $id,
                'company_id' => $company_id
            ),
            'fields' => array('id', 'first_name', 'last_name', 'email', 'phone', 'in_charge_of')
        ));
        $saleContacts = !empty($saleContacts) ? Set::combine($saleContacts, '{n}.SaleCustomerContact.id', '{n}.SaleCustomerContact') : array();
        $this->set(compact('saleCurrency', 'company_id', 'id', 'saleSettings', 'type', 'saleIbans', 'saleContacts', 'companyName', 'paymentTypes', 'created', 'updated', 'createdSaleContact', 'updatedSaleContact', 'category'));
    }
    
    /**
     * Update IBAN
     */
    public function update_iban(){
        $this->loadModel('SaleCustomerIban');
        $this->layout = false;
        $result = array();
        if($_POST && $this->_getCompany()){
            if($_POST['id'] == -1){
                $_POST['id'] = null;
            }
            $result['action'] = 'created';
            $this->SaleCustomerIban->create();
            if (!empty($_POST['id'])) {
                $this->SaleCustomerIban->id = $_POST['id'];
                $result['action'] = 'updated';
            } else {
                $_POST['defaults'] = 0;
            }
            $data = array(
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            );
            unset($_POST['id']);
            if ($this->SaleCustomerIban->save(array_merge($_POST, $data))) {
                $lastUpdate = $this->SaleCustomerIban->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleCustomerIban.id' => $this->SaleCustomerIban->id),
                    'fields' => array('iban')
                ));
                $result['id'] = $this->SaleCustomerIban->id;
                $result['iban'] = !empty($lastUpdate) && $lastUpdate['SaleCustomerIban']['iban'] ? $lastUpdate['SaleCustomerIban']['iban'] : '';
            }
        }
        echo json_encode($result);
        exit;
    }
    
    /**
     * Update IBAN DEFAULT
     */
    public function update_iban_default($type = null, $category = null, $company_id = null, $sale_customer_id = null, $id = null, $switch = null){
        $this->loadModel('SaleCustomerIban');
        $salesIbans = $this->SaleCustomerIban->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'sale_customer_id' => $sale_customer_id
            ),
            'fields' => array('id', 'defaults')
        ));
        if(!empty($salesIbans)){
            foreach($salesIbans as $key => $salesIban){
                $defaults = 0;
                if($key == $id && $switch === 'false'){
                    $defaults = 1;
                }
                $this->SaleCustomerIban->id = $key;
                $this->SaleCustomerIban->save(array('defaults' => $defaults));
            }
        }
        $this->redirect(array('action' => 'update', $type, $category, $company_id, $sale_customer_id));
    }
    
    /**
     * Delete Iban
     */
    public function delete_iban($type = null, $category = null, $company_id = null, $sale_customer_id = null, $id = null){
        $this->loadModel('SaleCustomerIban');
        if ($this->_getCompany($company_id) && $this->SaleCustomerIban->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'update', $type, $category, $company_id, $sale_customer_id));
    }
    
    /**
     * Update Contact
     */
    public function update_contact(){
        $this->loadModel('SaleCustomerContact');
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $this->SaleCustomerContact->create();
            if (!empty($this->data['id'])) {
                $this->SaleCustomerContact->id = $this->data['id'];
            }
            $data = array(
                'full_name' => trim($this->data['first_name']) . ' ' . trim($this->data['last_name']),
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            );
            unset($this->data['id']);
            if ($this->SaleCustomerContact->save(array_merge($this->data, $data))) {
                $result = true;
                //$this->Session->setFlash(__('Saved.', true), 'success');
            } else {
                //$this->Session->setFlash(__('Not Saved.', true), 'error');
            }
            $this->data['id'] = $this->SaleCustomerContact->id;
        } else {
            //$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    
    /**
     * Delete Contact
     */
    public function delete_contact($type = null, $category = null, $company_id = null, $sale_customer_id = null, $id = null){
        list($readSaleContact, $createdSaleContact, $updatedSaleContact, $deleteSaleContact) = $this->_checkPermissionIsUsingCustomerContact();
        if($deleteSaleContact == false){
            $this->Session->setFlash(__('You have not permission to Delete Business Customer Contact', true), 'error');
            $this->redirect(array('action' => 'update', $type, $company_id, $sale_customer_id));
        }
        $this->loadModel('SaleCustomerContact');
        if ($this->_getCompany($company_id) && $this->SaleCustomerContact->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'update', $type, $category, $company_id, $sale_customer_id));
    }
    
    /**
     * Update Website For Customer
     */
    public function update_website(){
        $this->layout = false;
        $result = '';
        if($_POST){
            $this->SaleCustomer->id = $_POST['id'];
            $_POST['website'] = $this->LogSystem->cleanHttpString($_POST['website']);
            if($this->SaleCustomer->save(array('website' => $_POST['website']))){
                $result = $_POST['website'];
            }
        }
        echo json_encode($result);
        exit;
    }
    
    /**
     * Update Group Information
     */
    public function update_group_infor(){
        $this->layout = false;
        $result = '';
        if($_POST){
            $this->SaleCustomer->id = $_POST['id'];
            unset($_POST['company_id']);
            unset($_POST['id']);
            foreach($_POST as $key => $values){
                $_POST[$key] = $this->LogSystem->cleanHttpString($values);
            }
            if($this->SaleCustomer->save($_POST)){
                $result = $_POST;
            }
        }
        echo json_encode($result);
        exit;
    }
    
    /**
     * Update Avatar
     */
    public function update_avatar($company_id = null, $id = null){
        $this->layout = false;
        $result = '';
        if(!empty($_FILES)){
            $path = $this->_getPath($company_id, $id);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment)) {
                /**
                 * Lay File Image Old And Delete
                 */
                $oldImages = $this->SaleCustomer->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleCustomer.id' => $id),
                    'fields' => array('logo')
                ));
                $oldDatas = array();
                if(!empty($oldImages) && $oldImages['SaleCustomer']['logo']){
                    $imageSaves = $oldImages['SaleCustomer']['logo'];
                    unlink($path . $imageSaves);
                    $oldImages = $oldImages['SaleCustomer']['logo'];
                    $info = pathinfo($oldImages);
                    $oldImages = explode('_resize_bk_', $oldImages);
                    if(!empty($oldImages)){
                        $oldImages = $oldImages[0] . '.' . $info['extension'];
                        if(!empty($oldImages)){
                            unlink($path . $oldImages);
                        }
                    }
                    $oldDatas = array(
                        0 => array(
                            'path' => $path,
                            'file' => $imageSaves
                        ),
                        1 => array(
                            'path' => $path,
                            'file' => $oldImages
                        )
                    );
                }
                $attachment = $attachment['attachment']['attachment'];
                $info = pathinfo($attachment);
                $newName = basename($attachment, '.' . $info['extension']) . '_resize_bk_' . time() . '.' . $info['extension'];
                $this->PImage->resizeImage('resizeCrop', $attachment, $path, $newName, 170, 206, 60);
                $this->SaleCustomer->id = $id;
                if ($this->SaleCustomer->save(array(
                    'logo' => $newName))) {
                    $result = $newName;
                    $this->Session->setFlash(__('Saved', true), 'success');
                    if($this->MultiFileUpload->otherServer == true){
                        $datas = array(
                            0 => array(
                                'path' => $path,
                                'file' => $attachment
                            ),
                            1 => array(
                                'path' => $path,
                                'file' => $newName
                            )
                        );
                        $this->MultiFileUpload->uploadMultipleFileToServerOther($datas, '/sale_customers/update/customer/' . $company_id . '/' . $id, $oldDatas);
                    }
                } else {
                    unlink($path . $newName);
                    unlink($path . $attachment);
                    if($this->MultiFileUpload->otherServer == true){
                        $datas = array(
                            0 => array(
                                'path' => $path,
                                'file' => $attachment
                            ),
                            1 => array(
                                'path' => $path,
                                'file' => $newName
                            )
                        );
                        $this->MultiFileUpload->deleteMultipleFileToServerOther($datas, '/sale_customers/update/customer/' . $company_id . '/' . $id);
                    }
                }
            } else {
                $this->SaleCustomer->id = $id;
                /**
                 * Lay File Image Old And Delete
                 */
                $oldImages = $this->SaleCustomer->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleCustomer.id' => $id),
                    'fields' => array('logo')
                ));
                $oldDatas = array();
                if(!empty($oldImages) && $oldImages['SaleCustomer']['logo']){
                    $imageSaves = $oldImages['SaleCustomer']['logo'];
                    unlink($path . $imageSaves);
                    $oldImages = $oldImages['SaleCustomer']['logo'];
                    $info = pathinfo($oldImages);
                    $oldImages = explode('_resize_bk_', $oldImages);
                    if(!empty($oldImages)){
                        $oldImages = $oldImages[0] . '.' . $info['extension'];
                        if(!empty($oldImages)){
                            unlink($path . $oldImages);
                        }
                    }
                    $oldDatas = array(
                        0 => array(
                            'path' => $path,
                            'file' => $imageSaves
                        ),
                        1 => array(
                            'path' => $path,
                            'file' => $oldImages
                        )
                    );
                }
                if($this->SaleCustomer->save(array('logo' => ''))){
                    $result = '';
                    if($this->MultiFileUpload->otherServer == true){ 
                        $this->MultiFileUpload->deleteMultipleFileToServerOther($oldDatas, '/sale_customers/update/customer/' . $company_id . '/' . $id);
                    }
                }
            }
        }
        echo json_encode($result);
        exit;
    }
    
    /**
     * Build Path. Save Avatar
     */
    protected function _getPath($company_id = null, $sale_customer_id = null){
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'business' . DS . 'customers' . DS;
        $path .= $company['Company']['dir'] . DS . $sale_customer_id . DS;
        return $path;
    }
    
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($category = null, $company_id = null, $id = null) {
        $this->loadModel('SaleCustomerIban');
        $this->loadModel('SaleCustomerContact');
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index', $category));
        }
        /**
         * Check Permission
         */
        list($read, $created, $updated, $delete) = $this->_checkPermissionIsUsingCustomer();
        if($delete == false){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            $this->redirect(array('action' => 'index', $category, $company_id));
        }
        /**
         * Lay logo/Avatar cua Id delete
         */
        $logo = $this->SaleCustomer->find('first', array(
            'recursive' => -1,
            'conditions' => array('SaleCustomer.id' => $id),
            'fields' => array('logo')
        ));
        $logo = !empty($logo) && $logo['SaleCustomer']['logo'] ? $logo['SaleCustomer']['logo'] : '';
        $path = $this->_getPath($company_id, $id);
        if ($this->_getCompany($company_id) && $this->SaleCustomer->delete($id)) {
            /**
             * Xoa logo/Avatar
             */
            unlink($path . $logo);
            /**
             * Xoa cac IBAN
             */
            $this->SaleCustomerIban->deleteAll(array('sale_customer_id' => $id), false);
            /**
             * Xoa cac Contact
             */
            $this->SaleCustomerContact->deleteAll(array('sale_customer_id' => $id), false);
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index', $category, $company_id));
    }
    
    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingCustomer(){
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : 0;
        $read = $created = $updated = $delete = false;
        if($role === 'admin'){
            $read = $created = $updated = $delete = true;
        } else {
            $this->loadModel('SaleRole');
            $saleRoles = $this->SaleRole->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLogin),
                'fields' => array('sale_role')
            ));
            $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
            if(!empty($saleRoles)){
                if($saleRoles == 1){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 2){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 3){
                    $read = true;
                } elseif($saleRoles == 4){
                    $read = true;
                } elseif($saleRoles == 5){
                    $read = true;
                }
            }
        }
        return array($read, $created, $updated, $delete);
    }
    
    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingCustomerContact(){
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : 0;
        $read = $created = $updated = $delete = false;
        if($role === 'admin'){
            $read = $created = $updated = $delete = true;
        } else {
            $this->loadModel('SaleRole');
            $saleRoles = $this->SaleRole->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLogin),
                'fields' => array('sale_role')
            ));
            $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
            if(!empty($saleRoles)){
                if($saleRoles == 1){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 2){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 3){
                    $read = $created = $updated = true;
                } elseif($saleRoles == 4){
                    $read = $created = $updated = true;
                } elseif($saleRoles == 5){
                    $read = true;
                }
            }
        }
        return array($read, $created, $updated, $delete);
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