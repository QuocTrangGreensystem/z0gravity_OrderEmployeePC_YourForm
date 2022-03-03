<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AuditSettingsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AuditSettings';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
    /**
     * List Category of Audit System
     */
    var $categoryOfAudits = array('auditor_company', 'mission_status', 'mission_type', 'mission_manager', 'recom_priority', 'recom_manager', 'auditor');
    
    /**
     * List Status xac dinh name nao la hoan thanh
     */
    var $statusOfAudits = array('run' => 'Run Time', 'fin' => 'Finish');
    
    /**
     * List key tuong ung voi id dung o controller khac
     */
    var $foreignKeyOfAuditSettings = array(
        'auditor_company' => 'audit_setting_auditor_company',
        'auditor' => 'audit_setting_auditor',
        'mission_status' => 'audit_setting_mission_status',
        'mission_type' => 'audit_setting_mission_type',
        'mission_manager' => 'audit_setting_recom_status_mission',
        'recom_priority' => 'audit_setting_recom_priority',
        'recom_manager' => 'audit_setting_recom_status_recom'
    );
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($type = null, $company_id = null) {
        $this->loadModel('Company');
        $type = ($type == null) ? 'auditor_company' : $type;
        if(!in_array($type, $this->categoryOfAudits)){
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
            $isUsing = $this->_checkPermissionIsUsingAudit();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $companyName['Company']['id'];
            $auditSettings = $this->AuditSetting->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => array_search($type, $this->categoryOfAudits)
                ),
                'fields' => array('id', 'name', 'status', 'description', 'weight'),
                'order' => array('weight' => 'ASC')
            ));
            if(!empty($auditSettings)){
                $i = 1;
                foreach($auditSettings as $key => $auditSetting){
                    $dx = $auditSetting['AuditSetting'];
                    $this->AuditSetting->id = $dx['id'];
                    $this->AuditSetting->save(array('weight' => $i));
                    $auditSettings[$key]['AuditSetting']['weight'] = $i;
                    $i++;
                }
            }
			$this->set(compact('company_id', 'companyName', 'type', 'auditSettings'));
        }   
        $statusAudits = $this->statusOfAudits;
        $this->set(compact('statusAudits'));
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
            $this->AuditSetting->create();
            if (!empty($this->data['id'])) {
                $this->AuditSetting->id = $this->data['id'];
            }
            $data = array(
                'type' => array_search($type, $this->categoryOfAudits)
            );
            $countDataOfRecomPriorities = $this->AuditSetting->find('count', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->data['company_id'], 'type' => $data['type'])
            ));
            $data['weight'] = ($countDataOfRecomPriorities != 0) ? $countDataOfRecomPriorities+1 : 1;
            unset($this->data['id']);
            if ($this->AuditSetting->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->AuditSetting->id;
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
            if ($this->_getCompany($company_id) && $this->AuditSetting->delete($id)) {
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('Already exist.', true), 'error');
            
        }
        $this->redirect(array('action' => 'index', $type));
    }
    
    /**
     * Check data da duoc su dung hay chua?
     */
    private function _checkDataUsing($id = null, $type = null){
        $check = 0;
        if(!empty($type) && $this->foreignKeyOfAuditSettings[$type]){
            $field = $this->foreignKeyOfAuditSettings[$type];
            $fieldOfAuditRecoms = array('audit_setting_recom_priority', 'audit_setting_recom_status_mission', 'audit_setting_recom_status_recom');
            if(in_array($field, $fieldOfAuditRecoms)){
                $this->loadModel('AuditRecom');
                $check = $this->AuditRecom->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        $field => $id
                    )
                ));
            } else {
                $this->loadModel('AuditMission');
                $check = $this->AuditMission->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        $field => $id
                    )
                ));
            }
        }
        $check = ($check == 0) ? false : true;
        return $check;
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
                $last = $this->AuditSetting->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('AuditSetting.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->AuditSetting->id = $last['AuditSetting']['id'];
                    $this->AuditSetting->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
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