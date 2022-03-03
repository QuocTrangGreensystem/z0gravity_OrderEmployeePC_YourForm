<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleCustomerContactsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleCustomerContacts';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
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
    function index($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('SaleCustomer');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            list($readSaleContact, $createdSaleContact, $updatedSaleContact, $deleteSaleContact) = $this->_checkPermissionIsUsingCustomerContact();
            $canModifyContact = true;
            if(!$createdSaleContact || !$updatedSaleContact || !$deleteSaleContact){
                $canModifyContact = false;
            }
            if($readSaleContact == false){
                $this->Session->setFlash(__('You do not have permission in Business Customer Contact.', true), 'error');
            } else {
                /**
                 * Danh sach Cac Sale customer Contact of company
                 */
                $saleContacts = $this->SaleCustomerContact->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'sale_customer_id', 'first_name', 'last_name', 'phone', 'email', 'in_charge_of')
                ));
                $listIdCustomer = !empty($saleContacts) ? array_unique(Set::classicExtract($saleContacts, '{n}.SaleCustomerContact.sale_customer_id')) : array();
                $saleContacts = !empty($saleContacts) ? Set::combine($saleContacts, '{n}.SaleCustomerContact.id', '{n}.SaleCustomerContact', '{n}.SaleCustomerContact.sale_customer_id') : array();
                $saleCustomers = $this->SaleCustomer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        //'SaleCustomer.id' => $listIdCustomer
                    ),
                    'fields' => array('id', 'name', 'type')
                ));
                $typeOfCustomer = !empty($saleCustomers) ? Set::combine($saleCustomers, '{n}.SaleCustomer.id', '{n}.SaleCustomer.type') : array();
                $saleCustomers = !empty($saleCustomers) ? Set::combine($saleCustomers, '{n}.SaleCustomer.id', '{n}.SaleCustomer.name') : array();
            }
			$this->set(compact('company_id', 'companyName', 'saleCustomers', 'saleContacts', 'createdSaleContact', 'updatedSaleContact', 'typeOfCustomer', 'canModifyContact'));
        }   
    }
    
    /**
     * update
     *
     * @return void
     * @access public
     */
    function update(){
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
     * delete
     *
     * @return void
     * @access public
     */
    function delete($company_id = null, $sale_customer_id = null, $id = null) {
        $this->loadModel('SaleCustomerIban');
        $this->loadModel('SaleCustomerContact');
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index', $type));
        }
        list($readSaleContact, $createdSaleContact, $updatedSaleContact, $deleteSaleContact) = $this->_checkPermissionIsUsingCustomerContact();
        if($deleteSaleContact == false){
            $this->Session->setFlash(__('You have not permission to Delete Business Customer Contact', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id) && $this->SaleCustomerContact->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index', $company_id));
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