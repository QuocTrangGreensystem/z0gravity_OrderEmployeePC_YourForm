<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SaleExpensesController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleExpenses';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
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
            $saleExpenses = $this->SaleExpense->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name', 'capex_opex', 'unit_us', 'unit_fr', 'depreciation_period')
            ));
            $saleExpenses = !empty($saleExpenses) ? Set::combine($saleExpenses, '{n}.SaleExpense.id', '{n}.SaleExpense') : array();
			$this->set(compact('company_id', 'companyName', 'saleExpenses'));
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
            $this->SaleExpense->create();
            if (!empty($this->data['id'])) {
                $this->SaleExpense->id = $this->data['id'];
            }
            $data = array(
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            );
            unset($this->data['id']);
            if ($this->SaleExpense->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->SaleExpense->id;
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
    function delete($company_id = null, $id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index', $type));
        }
        $isUsing = $this->_checkDataUsing($company_id, $id);
        if($isUsing == false){
            if ($this->_getCompany($company_id) && $this->SaleExpense->delete($id)) {
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
    private function _checkDataUsing($company_id = null, $id = null){
        $this->loadModel('SaleLeadProductExpense');
        $check = 0;
        if(!empty($id)){
            $check = $this->SaleLeadProductExpense->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'sale_expense_id' => $id
                )
            ));
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