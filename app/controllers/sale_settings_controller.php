<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleSettingsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleSettings';
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
    var $categoryOfSales = array('customer_status', 'customer_industry', 'customer_payment', 'customer_country', 'lead_status', 'lead_maturite', 'lead_phase', 'lead_product', 'lead_billing_period', 'lead_type_of_expense', 'currency');
    
    /**
     * List key tuong ung voi id dung o controller khac
     */
    var $foreignKeyOfSalesSettings = array(
        'customer_status' => 'sale_setting_customer_status',
        'customer_industry' => 'sale_setting_customer_industry',
        'customer_payment' => 'sale_setting_customer_payment',
        'lead_maturite' => 'sale_setting_lead_maturite',
        'lead_phase' => 'sale_setting_lead_phase',
        'lead_product' => 'sale_setting_lead_product'
    );
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($type = null, $company_id = null) {
        $this->loadModel('Company');
        $type = ($type == null) ? 'customer_status' : $type;
        if(!in_array($type, $this->categoryOfSales)){
            $this->redirect(array('action' => 'index'));
        }
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
            $isUsing = $this->_checkPermissionIsUsingAdmin();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $companyName['Company']['id'];
            $saleSettings = $this->SaleSetting->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => ($type == 'customer_country') ? 0 : $company_id,
                    'type' => array_search($type, $this->categoryOfSales)
                ),
                'fields' => array('id', 'name', 'name_fre', 'percentage', 'description', 'weight'),
                'order' => array('weight')
            ));
            if(!empty($saleSettings)){
                $i = 1;
                foreach($saleSettings as $key => $saleSetting){
                    $dx = $saleSetting['SaleSetting'];
                    $this->SaleSetting->id = $dx['id'];
                    $this->SaleSetting->save(array('weight' => $i));
                    $saleSettings[$key]['SaleSetting']['weight'] = $i;
                    $i++;
                }
            }
			$this->set(compact('company_id', 'companyName', 'type', 'saleSettings'));
        }   
    }
    
    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany($company_id)) {
            foreach ($this->data as $id => $weight) {
                $last = $this->SaleSetting->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('SaleSetting.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->SaleSetting->id = $last['SaleSetting']['id'];
                    $this->SaleSetting->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }
    
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($type = null) {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $this->SaleSetting->create();
            if (!empty($this->data['id'])) {
                $this->SaleSetting->id = $this->data['id'];
            }
            $data = array(
                'type' => array_search($type, $this->categoryOfSales)
            );
            unset($this->data['id']);
            if($type == 'customer_country'){
                $data['company_id'] = 0;
            }
            if ($this->SaleSetting->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->SaleSetting->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null, $type = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index', $type));
        }
        $isUsing = $this->_checkDataUsing($id, $type);
        if($isUsing == false){
            if ($this->_getCompany($company_id) && $this->SaleSetting->delete($id)) {
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('Already exist', true), 'error');
            
        }
        $this->redirect(array('action' => 'index', $type));
    }
    
    /**
     * Check data da duoc su dung hay chua?
     */
    private function _checkDataUsing($id = null, $type = null){
        $this->loadModel('SaleCustomer');
        $this->loadModel('SaleLead');
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleLeadProductInvoice');
        $this->loadModel('SaleLeadProductExpense');
        $check = 0;
        if(!empty($type)){
            if($type === 'customer_country'){
                $check = $this->SaleCustomer->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            'sale_setting_customer_country' => $id,
                            'invoice_sale_setting_customer_country' => $id
                        )
                    )
                ));
            } elseif($type === 'lead_maturite' || $type === 'lead_phase'){
                if($this->foreignKeyOfSalesSettings[$type]){
                    $field = $this->foreignKeyOfSalesSettings[$type];
                    $check = $this->SaleLead->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            $field => $id
                        )
                    ));
                }
            } elseif($type === 'lead_product'){
                if($this->foreignKeyOfSalesSettings[$type]){
                    $field = $this->foreignKeyOfSalesSettings[$type];
                    $checkOne = $this->SaleLeadProduct->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            $field => $id
                        )
                    ));
                    $checkTwo = $this->SaleLeadProductInvoice->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            $field => $id
                        )
                    ));
                    $checkThree = $this->SaleLeadProductExpense->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            $field => $id
                        )
                    ));
                    $check = $checkOne + $checkTwo + $checkThree;
                }
            } else {
                if($this->foreignKeyOfSalesSettings[$type]){
                    $field = $this->foreignKeyOfSalesSettings[$type];
                    $check = $this->SaleCustomer->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            $field => $id
                        )
                    ));
                }
            }
        }
        $check = ($check == 0) ? false : true;
        return $check;
    }
    
    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingAdmin(){
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : 0;
        $this->loadModel('SaleRole');
        $saleRoles = $this->SaleRole->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLogin),
            'fields' => array('sale_role')
        ));
        $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : 0;
        $isUsing = false;
        if($role === 'admin' || (!empty($saleRoles) && ($saleRoles == 1 || $saleRoles == 2))){
            $isUsing = true;
        }
        return $isUsing;
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