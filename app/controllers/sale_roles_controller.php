<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class SaleRolesController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleRoles';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * All Role Of Business
     */
    var $roleOfBusiness = array('not_role', 'sales_director', 'sales_manager', 'salesman', 'financial', 'auditor', 'easyrap');

     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('Employee');
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
            /**
             * Get Employee Of Company($company_id)
             */
            $references = $this->Employee->CompanyEmployeeReference->find('all', array(
                'group' => 'employee_id',
                'fields' => array(
                    'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id'
                ),
                'recursive' => 1,
                'conditions' => array('OR' => array('Company.id' => $company_id,
                        'Company.parent_id' => $company_id))));
            $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
            $conditions['Employee.id'] = array_keys($references);
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname'),
                'conditions' => $conditions,
                'order' => array('fullname')
            ));
            /**
             * Get Employee And Role Of Business
             */
            $saleRoles = $this->SaleRole->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('employee_id', 'sale_role')
            ));
			$this->set(compact('company_id', 'companyName', 'employees', 'saleRoles'));
        }
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($type = null, $company_id = null, $employee_id = null, $switch = null) {
        if ($this->_getCompany()) {
            if(!$switch){
                $type = 'not_role';
            }
            $data = array(
                'employee_id' => $employee_id,
                'company_id' => $company_id
            );
            $last = $this->SaleRole->find('first', array(
                'recursive' => -1,
                'conditions' => $data,
                'fields' => array('id')
            ));
            $this->SaleRole->create();
            if(!empty($last) && $last['SaleRole']['id']){
                $this->SaleRole->id = $last['SaleRole']['id'];
            }
            $data['sale_role'] = array_search($type, $this->roleOfBusiness);
            if ($this->SaleRole->save($data)) {
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
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