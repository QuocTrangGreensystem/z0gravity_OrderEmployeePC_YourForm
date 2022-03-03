<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AuditAdminsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AuditAdmins';
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
            $this->loadModel('Employee');
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $isUsing = $this->_checkPermissionIsUsingAudit();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $companyName['Company']['id'];
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
                'conditions' => $conditions
            ));
            $adminAudits = $this->AuditAdmin->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'employee_id')
            ));
			$this->set(compact('company_id', 'companyName', 'employees', 'adminAudits'));
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
            $this->AuditAdmin->create();
            if (!empty($this->data['id'])) {
                $this->AuditAdmin->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if ($this->AuditAdmin->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->AuditAdmin->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    
    /**
     * Log
     *
     * @return void
     * @access public
     */
    public function audit_log($type = null, $company_id = null){
        $type = ($type == null) ? 'audit_mission' : $type;
        $groupType = array('audit_mission', 'audit_recom');
        if(!in_array($type, $groupType)){
            $this->redirect(array('action' => 'audit_logs'));
        }
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $this->loadModel('Employee');
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $isUsing = $this->_checkPermissionIsUsingAudit();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $companyName['Company']['id'];
            $this->loadModel('LogSystem');
            $logSystems = $this->LogSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'model' => ($type == 'audit_mission') ? 'AuditMission' : 'AuditRecom'
                ),
                'fields' => array('id', 'name', 'description'),
                'order' => array('updated' => 'DESC')
            ));
            $logSystems = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
			$this->set(compact('company_id', 'companyName', 'employees', 'logSystems'));
        }
    }
    
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Employee', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->_getCompany($company_id) && $this->AuditAdmin->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }
    
    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingAudit(){
        $this->loadModel('Employee');
        $this->loadModel('AuditAdmin');
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        /**
         * Lay danh sach Admin Company
         */
        $adminOfCompanies = $this->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'employee_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id, 'role_id' => 2)));
        /**
         * Lay danh sach Admin Audit
         */
        $adminAudits = $this->AuditAdmin->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('employee_id', 'employee_id') 
        ));
        $admins = array_unique(array_merge($adminOfCompanies, $adminAudits));
        $isUsing = false;
        if(in_array($employeeLogin, $admins)){
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
?>