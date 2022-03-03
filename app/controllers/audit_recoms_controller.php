<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class AuditRecomsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AuditRecoms';
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
    var $components = array('MultiFileUpload', 'LogSystem', 'SlickExporter');

    /**
     * List Category of Audit System
     */
    var $categoryOfAudits = array('auditor_company', 'mission_status', 'mission_type', 'mission_manager', 'recom_priority', 'recom_manager', 'auditor');

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null, $audit_mission_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMission');
        $this->loadModel('AuditRecomEmployeeRefer');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $infos = $this->employee_info;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : '';
        $is_sas = !empty($infos['Employee']['is_sas']) ? $infos['Employee']['is_sas'] : '';
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
             list($isUsing, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $this->employee_info['Company']['id'];
             /**
             * Lay danh sach Admin Audit
             */
            $adminAudits = $this->AuditAdmin->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('employee_id', 'employee_id')
            ));
            /**
             * Lay ten cua audit mission current
             */
            $auditMissions = $this->AuditMission->find('first', array(
                'recursisve' => -1,
                'conditions' => array('AuditMission.id' => $audit_mission_id, 'company_id' => $company_id),
                'fields' => array('mission_title')
            ));
            if( empty($auditMissions) ){
                $this->redirect(array('controller' => 'audit_missions'));
            }
            /**
             * Danh sach audit recom
             */
            $auditRecoms = $this->AuditRecom->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'audit_mission_id' => $audit_mission_id),
                'fields' => array('id', 'id_recommendation', 'contact', 'recommendation', 'audit_setting_recom_priority', 'audit_setting_recom_status_mission', 'implement_date')
            ));
            $idOfAuditRecoms = !empty($auditRecoms) ? Set::classicExtract($auditRecoms, '{n}.AuditRecom.id') : array();
            /**
             * Danh sach cac mission manager
             */
            $missionManagerRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $idOfAuditRecoms,
                    'company_id' => $company_id,
                    'type' => 0
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
                'group' => array('audit_recom_id', 'id')
            ));
            /**
             * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
             */
            $auditSettings = $this->AuditSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => array_keys($this->categoryOfAudits)
                ),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id'),
                'order' => array('weight' => 'ASC')
            ));
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
             * Display buttion Import Data
             */
            $displayImport = 'false';
            //if(in_array($employeeLogin, $adminAudits) || $role == 'admin' || $is_sas){
            if(in_array($employeeLogin, $adminAudits)){
                $displayImport = 'true';
            }
			$this->set(compact('displayImport', 'company_id', 'audit_mission_id', 'companyName', 'auditRecoms', 'missionManagerRecoms', 'auditSettings', 'employees', 'auditMissions'));
        }
    }

    /**
     * exportExcel
     * @param int $project_id
     * @return void
     * @access public
     */
    function export($company_id = null, $audit_mission_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecomFile');
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect(array('action' => 'index'));
        }
        $data = array_filter(explode(',', $this->data['Export']['list']));
        $companyName = $this->_getCompany($company_id);
        if (empty($companyName)) {
            $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
        }
        $company_id = $companyName['Company']['id'];
        /**
         * Danh sach audit recom
         */
        $auditRecoms = $this->AuditRecom->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'audit_mission_id' => $audit_mission_id, 'AuditRecom.id' => $data),
            'fields' => array('id', 'id_recommendation', 'contact', 'recommendation', 'audit_setting_recom_priority', 'recom_theme', 'comment_manager', 'audit_setting_recom_status_mission', 'audit_setting_recom_status_mission', 'response_recom_manager', 'implement_date', 'implement_revised', 'audit_setting_recom_status_recom', 'date_change_status_recom', 'author_modify', 'comment_recom', 'date_change_status_mission')
        ));
        $auditRecoms = !empty($auditRecoms) ? Set::combine($auditRecoms, '{n}.AuditRecom.id', '{n}.AuditRecom') : array();
        /**
         * Danh sach cac mission manager
         */
        $missionManagerRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_recom_id' => $data,
                'company_id' => $company_id,
                'type' => 0
            ),
            'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
            'group' => array('audit_recom_id', 'id')
        ));
        /**
         * Danh sach cac Operator Modification
         */
        $operatorModifications = $this->AuditRecomEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_recom_id' => $data,
                'company_id' => $company_id,
                'type' => 1
            ),
            'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
            'group' => array('audit_recom_id', 'id')
        ));
        /**
         * File Attachments
         */
        $auditRecomFiles = $this->AuditRecomFile->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_recom_id' => $data
            ),
            'fields' => array('id', 'file_attachment', 'audit_recom_id',),
            'group' => array('audit_recom_id', 'id')
        ));
        /**
         * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
         */
        $auditSettings = $this->AuditSetting->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'type' => array_keys($this->categoryOfAudits)
            ),
            'fields' => array('id', 'name', 'type'),
            'group' => array('type', 'id'),
            'order' => array('weight' => 'ASC')
        ));
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
        $this->set(compact('auditRecomFiles', 'operatorModifications', 'company_id', 'auditRecoms', 'missionManagerRecoms', 'auditSettings', 'employees'));
        $this->layout = '';
    }

    /**
     * exportExcel
     * @param int $project_id
     * @return void
     * @access public
     */
    function export_follow_employ($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditMission');
        $this->loadModel('AuditRecomFile');
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect(array('action' => 'index'));
        }
        $data = array_filter(explode(',', $this->data['Export']['list']));
        $companyName = $this->_getCompany($company_id);
        if (empty($companyName)) {
            $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
        }
        $company_id = $companyName['Company']['id'];
        /**
         * Danh sach audit recom
         */
        $auditRecoms = $missionManagerRecoms = array();
        if(!empty($data)){
            $auditRecoms = $this->AuditRecom->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'AuditRecom.id' => $data),
                'fields' => array('id', 'audit_mission_id', 'id_recommendation', 'contact', 'recommendation', 'audit_setting_recom_priority', 'recom_theme', 'comment_manager', 'audit_setting_recom_status_mission', 'audit_setting_recom_status_mission', 'response_recom_manager', 'implement_date', 'implement_revised', 'audit_setting_recom_status_recom', 'date_change_status_recom', 'author_modify', 'comment_recom', 'date_change_status_mission')
            ));
            $missionIds = !empty($auditRecoms) ? array_unique(Set::classicExtract($auditRecoms, '{n}.AuditRecom.audit_mission_id')) : array();
            $auditRecoms = !empty($auditRecoms) ? Set::combine($auditRecoms, '{n}.AuditRecom.id', '{n}.AuditRecom', '{n}.AuditRecom.audit_mission_id') : array();
            /**
             * Danh sach cac mission manager of recommendation
             */
            $missionManagerRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $data,
                    'company_id' => $company_id,
                    'type' => 0
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
                'group' => array('audit_recom_id', 'id')
            ));
            /**
             * Danh sach cac Operator Modification
             */
            $operatorModifications = $this->AuditRecomEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $data,
                    'company_id' => $company_id,
                    'type' => 1
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
                'group' => array('audit_recom_id', 'id')
            ));
            /**
             * File Attachments
             */
            $auditRecomFiles = $this->AuditRecomFile->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $data
                ),
                'fields' => array('id', 'file_attachment', 'audit_recom_id',),
                'group' => array('audit_recom_id', 'id')
            ));
        }
        $auditMissions = $auditMissionEmployees = array();
        if(!empty($missionIds)){
            /**
             * Danh sach cac Mission cua Recom
             */
            $auditMissions = $this->AuditMission->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'AuditMission.id' => $missionIds),
                'fields' => array('id', 'mission_title', 'mission_number', 'audit_setting_mission_status', 'audit_setting_auditor_company')
            ));
            $auditMissions = !empty($auditMissions) ? Set::combine($auditMissions, '{n}.AuditMission.id', '{n}.AuditMission') : array();
            /**
             * Danh sach cac mission manager o mission
             */
            $auditMissionEmployees = $this->AuditMissionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $missionIds,
                    'company_id' => $company_id,
                    'type' => 1
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_mission_id'),
                'group' => array('audit_mission_id', 'id')
            ));
        }
        /**
         * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
         */
        $auditSettings = $this->AuditSetting->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'type' => array_keys($this->categoryOfAudits)
            ),
            'fields' => array('id', 'name', 'type'),
            'group' => array('type', 'id'),
            'order' => array('weight' => 'ASC')
        ));
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
        $this->set(compact('auditRecomFiles', 'operatorModifications', 'company_id', 'auditRecoms', 'missionManagerRecoms', 'auditMissions', 'auditMissionEmployees', 'auditSettings', 'employees'));
        $this->layout = '';
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index_follow_employ($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMission');
        $this->loadModel('AuditRecomEmployeeRefer');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditAdmin');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $infos = $this->employee_info;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : '';
        $is_sas = !empty($infos['Employee']['is_sas']) ? $infos['Employee']['is_sas'] : '';
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
            list($isUsing, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
            if($isUsing == false){
                //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
                $this->redirect('/index');
            }
            $company_id = $companyName['Company']['id'];
            /**
             * Lay danh sach Admin Audit
             */
            $adminAudits = $this->AuditAdmin->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('employee_id', 'employee_id')
            ));
            /**
             * Danh sach audit recom
             */
            $auditRecoms = $this->AuditRecom->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'audit_mission_id', 'id_recommendation', 'contact', 'recommendation', 'audit_setting_recom_priority', 'audit_setting_recom_status_mission', 'implement_date')
            ));
            $idOfAuditRecoms = !empty($auditRecoms) ? Set::classicExtract($auditRecoms, '{n}.AuditRecom.id') : array();
            $auditMissionIds = !empty($auditRecoms) ? array_unique(Set::classicExtract($auditRecoms, '{n}.AuditRecom.audit_mission_id')) : array();
            $auditRecoms = !empty($auditRecoms) ? Set::combine($auditRecoms, '{n}.AuditRecom.id', '{n}.AuditRecom', '{n}.AuditRecom.audit_mission_id') : array();
            /**
             * Danh sach cac Mission cua Recom
             */
            $auditMissions = $this->AuditMission->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'AuditMission.id' => $auditMissionIds),
                'fields' => array('id', 'mission_title', 'mission_number', 'audit_setting_mission_status', 'audit_setting_auditor_company')
            ));
            $auditMissions = !empty($auditMissions) ? Set::combine($auditMissions, '{n}.AuditMission.id', '{n}.AuditMission') : array();
            /**
             * Danh sach cac mission manager of recommendation
             */
            $missionManagerRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $idOfAuditRecoms,
                    'company_id' => $company_id,
                    'type' => 0
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_recom_id'),
                'group' => array('audit_recom_id', 'id')
            ));
            /**
             * Danh sach cac mission manager o mission
             */
            $auditMissionEmployees = $this->AuditMissionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $auditMissionIds,
                    'company_id' => $company_id,
                    'type' => array(0, 1)
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_mission_id'),
                'group' => array('audit_mission_id', 'id')
            ));
            if(!empty($auditMissionEmployees)){
                foreach($auditMissionEmployees as $auditMissionId => $auditMissionEmployee){
                    if(!in_array($employeeLogin, array_keys($auditMissionEmployee)) && !in_array($employeeLogin, $adminAudits)){ //&& $role != 'admin'
                        if(!empty($missionManagerRecoms)){
                            foreach($missionManagerRecoms as $auditRecomId => $missionManagerRecom){
                                if(!in_array($employeeLogin, array_keys($missionManagerRecom)) && !in_array($employeeLogin, $adminAudits)){ //&& $role != 'admin'
                                    if(!empty($auditRecoms[$auditMissionId]) && !empty($auditRecoms[$auditMissionId][$auditRecomId])){
                                        unset($auditRecoms[$auditMissionId][$auditRecomId]);
                                    }
                                }
                            }
                        }
                        if(empty($auditRecoms[$auditMissionId]) && !empty($auditMissions[$auditMissionId])){
                            unset($auditMissions[$auditMissionId]);
                        }
                    }
                }
            }
            /**
             * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
             */
            $auditSettings = $this->AuditSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => array_keys($this->categoryOfAudits)
                ),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id'),
                'order' => array('weight' => 'ASC')
            ));
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
             * Display buttion Import Data
             */
            $displayImport = 'false';
            //if(in_array($employeeLogin, $adminAudits) || $role == 'admin' || $is_sas){
            if(in_array($employeeLogin, $adminAudits)){
                $displayImport = 'true';
            }
			$this->set(compact('displayImport', 'company_id', 'audit_mission_id', 'companyName', 'auditRecoms', 'missionManagerRecoms', 'auditSettings', 'employees', 'auditMissions', 'auditMissionEmployees', 'editMission'));
        }
    }

    /**
     * Import CSV
     */
    public function import_csv($company_id = null, $audit_mission_id = null){
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'AuditRecoms' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'AuditRecoms' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                //debug($csv->data); exit;
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    if(!empty($audit_mission_id)){
                        $columnMandatory = array(
                            'ID Recommendation',
                            'Statement',
                            'Theme Recommendation',
                            'Mission Manager Comments',
                            'Recommendation',
                            'Priority Recommendation',
                            'Recommendation Status(Mission Manager)',
                            'Recommendation Manager',
                            'Initial Implementation Date',
                            'Date Of Implementation Revised',
                            'Initial Response Recommendation Manager',
                            'Recommendation Status (Recommendation Manager)',
                            //'Operator Modification',
                            'Recommendation Manager Comments'
                        );
                        $validate = array('Recommendation', 'Priority Recommendation', 'Recommendation Status(Mission Manager)', 'Recommendation Manager', 'Initial Implementation Date');
                    } else {
                        $columnMandatory = array(
                            'Mission Title',
                            'ID Recommendation',
                            'Statement',
                            'Theme Recommendation',
                            'Mission Manager Comments',
                            'Recommendation',
                            'Priority Recommendation',
                            'Recommendation Status(Mission Manager)',
                            'Recommendation Manager',
                            'Initial Implementation Date',
                            'Date Of Implementation Revised',
                            'Initial Response Recommendation Manager',
                            'Recommendation Status (Recommendation Manager)',
                            //'Operator Modification',
                            'Recommendation Manager Comments'
                        );
                        $validate = array('Mission Title', 'Recommendation', 'Priority Recommendation', 'Recommendation Status(Mission Manager)', 'Recommendation Manager', 'Initial Implementation Date');
                    }
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $defaultKeys = array_keys($default);
                    $count = !empty($default) ? count($default) : 1;
                    $this->loadModel('Employee');
                    $this->loadModel('AuditSetting');
                    $this->loadModel('AuditMission');
                    $this->Employee->cacheQueries = true;
                    // Employee of company
                    $employeeCompanies = $this->Employee->CompanyEmployeeReference->find('all', array(
                        'conditions' => array(
                            'CompanyEmployeeReference.company_id' => $company_id
                        ),
                        'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
                    ));
                    $employeeCompanies = !empty($employeeCompanies) ? Set::combine($employeeCompanies, '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name')) : array();
                    $employeeIdCompanies = !empty($employeeCompanies) ? array_flip($employeeCompanies) : array();
                    $employeeIdCompanies = !empty($employeeIdCompanies) ? array_change_key_case($employeeIdCompanies, CASE_LOWER) : array();
                    /**
                     * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
                     */
                    $auditSettings = $this->AuditSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'type' => array_keys($this->categoryOfAudits)
                        ),
                        'fields' => array('name', 'id', 'type'),
                        'group' => array('type', 'id'),
                        'order' => array('weight' => 'ASC')
                    ));
                    $recomPriority = !empty($auditSettings[4]) ? array_change_key_case($auditSettings[4], CASE_LOWER) : array();
                    $recomStatus = !empty($auditSettings[3]) ? array_change_key_case($auditSettings[3], CASE_LOWER) : array();
                    $recomManager = !empty($auditSettings[5]) ? array_change_key_case($auditSettings[5], CASE_LOWER) : array();
                    $recomHaveUsing = array();
                    foreach($csv->data as $row){
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        foreach(array_keys($row) as $name){
                            if(!in_array($name, $columnMandatory)){
                                unset($row[$name]);
                            }
                        }
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, ''), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));
                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);
                            if (empty($row[$value])) {
                                $row['columnHighLight'][$value] = '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                            }
                        }
                        if(empty($row['error'])){
                            // Mission Title
                            $idAuditMission = 0;
                            if(!empty($audit_mission_id)){
                                $row['data']['audit_mission_id'] = $audit_mission_id;
                                $idAuditMission = $audit_mission_id;
                            } else {
                                if(!empty($row['Mission Title'])){
                                    $_title = strtolower(trim($row['Mission Title']));
                                    $tmp = $this->AuditMission->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'company_id' => $company_id,
                                            'AuditMission.mission_title' => $_title
                                        ),
                                        'fields' => array('id')
                                    ));
                                    if(!empty($tmp) && $tmp['AuditMission']['id']){ // da co trong he thong
                                        $row['data']['audit_mission_id'] = $tmp['AuditMission']['id'];
                                        $idAuditMission = $tmp['AuditMission']['id'];
                                    } else {
                                        $row['columnHighLight']['Mission Title'] = '';
                                        $row['error'][] = __('The Mission Title not found in system.', true);
                                    }
                                }
                            }
                            // Statement
                            if(!empty($row['Statement'])){
                                $row['data']['contact'] = $row['Statement'];
                            }
                            // Priority Recommendation
                            if(!empty($row['Priority Recommendation'])){
                                $priority = strtolower(trim($row['Priority Recommendation']));
                                if(!empty($recomPriority[$priority])){
                                    $row['data']['audit_setting_recom_priority'] = $recomPriority[$priority];
                                } else {
                                    $row['columnHighLight']['Priority Recommendation'] = '';
                                    $row['error'][] = __('The Priority Recommendation not found in system.', true);
                                }
                            }
                            // Recommendation Status(Mission Manager)
                            if(!empty($row['Recommendation Status(Mission Manager)'])){
                                $_status = strtolower(trim($row['Recommendation Status(Mission Manager)']));
                                if(!empty($recomStatus[$_status])){
                                    $row['data']['audit_setting_recom_status_mission'] = $recomStatus[$_status];
                                } else {
                                    $row['columnHighLight']['Recommendation Status(Mission Manager)'] = '';
                                    $row['error'][] = __('The Recommendation Status(Mission Manager) not found in system.', true);
                                }
                            }

                            // Initial Implementation Date
                            if(!empty($row['Initial Implementation Date'])){
                                $inplementDate = $this->_formatDateCustom($row['Initial Implementation Date']);
                                $row['data']['implement_date'] = $inplementDate;
                            }

                            // Recommendation Manager
                            if(!empty($row['Recommendation Manager'])){
                                $manager = explode(',', $row['Recommendation Manager']);
                                $valManager = array();
                                if(count($manager) > 1){
                                    // Xac dinh manager chinh va manager backup. Dem xem co bao nhiu manager backup
                                    $totalBackup = $totalManager = 0;
                                    foreach($manager as $val){
                                        if(strrpos($val, '(B)') || strrpos($val, '(b)')){
                                            $val = trim(str_replace('(b)', ' ', strtolower(trim($val))));
                                            if(in_array($val, array_keys($employeeIdCompanies))){
                                                $_valManager = !empty($employeeIdCompanies[$val]) ? $employeeIdCompanies[$val] : 0;
                                                $valManager[$_valManager] = 1;
                                            }
                                            $totalBackup++;
                                        } else {
                                            $val = strtolower(trim($val));
                                            if(in_array($val, array_keys($employeeIdCompanies))){
                                                $_valManager = !empty($employeeIdCompanies[$val]) ? $employeeIdCompanies[$val] : 0;
                                                $valManager[$_valManager] = 0;
                                            }
                                            $totalManager++;
                                        }
                                    }
                                    /**
                                     * Manager chinh
                                     */
                                    if($totalManager == 0){ // khong co manager chinh nao -> loi
                                        $row['columnHighLight']['Recommendation Manager'] = '';
                                        $row['error'][] = __('The Recommendation needs a Primary Management', true);
                                    } else {
                                        if($totalManager > 1){ // co 2 or nhieu manager chinh -> loi
                                            $row['columnHighLight']['Recommendation Manager'] = '';
                                            $row['error'][] = __('The Recommendation justs a Manager Primary', true);
                                        } else { // okie
                                            // okie. do nothing
                                        }
                                    }
                                    /**
                                     * Manager backup
                                     */
                                    if($totalBackup == count($manager)){ // tat ca employ nhap vao dieu la backup -> loi
                                        $row['columnHighLight']['Recommendation Manager'] = '';
                                        $row['error'][] = __('The Recommendation musts a Primary Management', true);
                                    } else { //okie
                                        // okie. do nothing
                                    }
                                    if(count($valManager) == 1){
                                        $key = array_keys($valManager);
                                        $valManager[$key[0]] = 0;
                                    }
                                } else {
                                    $val = !empty($manager[0]) ? $manager[0] : '';
                                    if(strrpos($val, '(B)') || strrpos($val, '(b)')){ // co 1 nguoi va la backup -> loi
                                        $row['columnHighLight']['Recommendation Manager'] = '';
                                        $row['error'][] = __('The Recommendation needs a Primary Management', true);
                                    } else {
                                        $val = strtolower(trim($val));
                                        if(in_array($val, array_keys($employeeIdCompanies))){
                                            $_valManager = !empty($employeeIdCompanies[$val]) ? $employeeIdCompanies[$val] : 0;
                                            $valManager[$_valManager] = 0;
                                        }
                                    }
                                }
                                /**
                                 * Set Data Manager
                                 */
                                $row['data']['recom_manager'] = serialize($valManager);
                                if(empty($valManager)){
                                    $row['columnHighLight']['Mission Manager'] = '';
                                    $row['error'][] = __('The Mission Manager is not blank', true);
                                }
                            }
                            // Recommendation
                            if(!empty($row['Recommendation'])){
                                $_title = (trim($row['Recommendation']));
                                if(in_array($_title, $recomHaveUsing)){
                                    $row['columnHighLight']['Recommendation'] = '';
                                    $row['error'][] = __('The Recommendation has identical', true);
                                } else {
                                    $tmp = $this->AuditRecom->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'AuditRecom.company_id' => $company_id,
                                            'AuditRecom.recommendation' => $_title,
                                            'AuditRecom.audit_mission_id' => $idAuditMission
                                        ),
                                        'fields' => array('id')
                                    ));
                                    if(!empty($tmp) && $tmp['AuditRecom']['id']){  //da co trong he thong
                                        $row['data']['id'] = $tmp['AuditRecom']['id'];
                                        $row['data']['recommendation'] = trim($row['Recommendation']);
                                    } else {
                                        $row['data']['recommendation'] = trim($row['Recommendation']);
                                    }
                                    $recomHaveUsing[$_title] = $_title;
                                }
                            }
                            // ID Recommendation
                            if(!empty($row['ID Recommendation'])){
                                $tmp = $this->AuditRecom->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'AuditRecom.company_id' => $company_id,
                                        'AuditRecom.id_recommendation' => $row['ID Recommendation'],
                                        'AuditRecom.audit_mission_id' => $idAuditMission
                                    ),
                                    'fields' => array('id')
                                ));
                                if(!empty($tmp) && $tmp['AuditRecom']['id']){  //da co trong he thong
                                    $row['data']['id'] = $tmp['AuditRecom']['id'];
                                    $row['data']['id_recommendation'] = trim($row['ID Recommendation']);
                                } else {
                                    $row['data']['id_recommendation'] = trim($row['ID Recommendation']);
                                }
                            }
                            // Theme Recommendation
                            if(!empty($row['Theme Recommendation'])){
                                $row['data']['recom_theme'] = $row['Theme Recommendation'];
                            }
                            // Mission Manager Comments
                            if(!empty($row['Mission Manager Comments'])){
                                $row['data']['comment_manager'] = $row['Mission Manager Comments'];
                            }
                            // Date Of Implementation Revised
                            if(!empty($row['Date Of Implementation Revised'])){
                                $date = $this->_formatDateCustom($row['Date Of Implementation Revised']);
                                $row['data']['implement_revised'] = $date;
                            }
                            // Initial Response Recommendation Manager
                            if(!empty($row['Initial Response Recommendation Manager'])){
                                $row['data']['response_recom_manager'] = $row['Initial Response Recommendation Manager'];
                            }
                            // Recommendation Status (Recommendation Manager)
                            if(!empty($row['Recommendation Status (Recommendation Manager)'])){
                                $_manager = strtolower(trim($row['Recommendation Status (Recommendation Manager)']));
                                if(!empty($recomManager[$_manager])){
                                    $row['data']['audit_setting_recom_status_recom'] = $recomManager[$_manager];
                                } else {
                                    $row['columnHighLight']['Recommendation Status (Recommendation Manager)'] = '';
                                    $row['error'][] = __('The Recommendation Status (Recommendation Manager) not found in system.', true);
                                }
                            }
                            // Recommendation Manager Comments
                            if(!empty($row['Recommendation Manager Comments'])){
                                $row['data']['comment_recom'] = $row['Recommendation Manager Comments'];
                            }
                            // Operator Modification
                            //if(!empty($row['Operator Modification'])){
//                                $operators = explode(',', $row['Operator Modification']);
//                                $valOperator = array();
//                                foreach($operators as $val){
//                                    $val = strtolower(trim($val));
//                                    if(in_array($val, array_keys($employeeIdCompanies))){
//                                        $_valOperator = !empty($employeeIdCompanies[$val]) ? $employeeIdCompanies[$val] : 0;
//                                        $valOperator[$_valOperator] = 0;
//                                    }
//                                }
//                                /**
//                                 * Set Data Operator
//                                 */
//                                $row['data']['recom_operator'] = serialize($valOperator);
//                            }
                        }
                        if (!empty($row['error'])) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            if (!empty($row['data']['id'])) {
                                $records['Update'][] = $row;
                            } else {
                                $records['Create'][] = $row;
                            }
                        }
                    }
                }
                unlink($filename);
            }
            $this->set('records', $records);
            $this->set('default', $default);
            $this->set(compact('company_id', 'audit_mission_id'));
        } else {
            $this->set(compact('company_id', 'audit_mission_id'));
            if(!empty($audit_mission_id)){
                $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
            } else {
                $this->redirect(array('action' => 'index_follow_employ'));
            }
        }
    }

    /**
     * Save import of project task
     */
    function save_file_import($company_id = null, $audit_mission_id = null) {
        $this->loadModel('AuditSetting');
        $this->loadModel('AuditRecomEmployeeRefer');
        set_time_limit(0);
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//export
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    if(!empty($audit_mission_id)){
                        $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
                    } else {
                        $this->redirect(array('action' => 'index_follow_employ'));
                    }
                }
                $complete = 0;
                foreach($import as $key => $data){
                    $this->AuditRecom->create();
                    if(!empty($data['id'])){
                        $this->AuditRecom->id = $data['id'];
                        $oldStatus = $this->AuditRecom->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'AuditRecom.id' => $this->AuditRecom->id
                            ),
                            'fields' => array('audit_setting_recom_status_mission')
                        ));
                        if(!empty($oldStatus)){
                            $oldStatusMission = $oldStatus['AuditRecom']['audit_setting_recom_status_mission'];
                            if(!empty($data['audit_setting_recom_status_mission']) && $oldStatusMission != $data['audit_setting_recom_status_mission']){
                                $data['date_change_status_mission'] = time();
                                $data['author_modify'] = $this->employee_info['Employee']['fullname'];
                            }
                        }
                    } else {
                        if(!empty($data['audit_setting_recom_status_mission'])){
                            $data['date_change_status_mission'] = time();
                            $data['author_modify'] = $this->employee_info['Employee']['fullname'];
                        }
                    }
                    $mangers = unserialize($data['recom_manager']);
                    //$operators = !empty($data['recom_operator']) ? unserialize($data['recom_operator']) : array();
                    unset($data['recom_manager']);
                    //unset($data['recom_operator']);
                    unset($data['id']);
                    $data['company_id'] = $company_id;
                    $data['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                    $reDatas = array(
                        'id_recommendation' => !empty($data['id_recommendation']) ? $data['id_recommendation'] : '',
                        'contact' => !empty($data['contact']) ? $data['contact'] : '',
                        'recommendation' => !empty($data['recommendation']) ? $data['recommendation'] : '',
                        'recom_theme' => !empty($data['recom_theme']) ? $data['recom_theme'] : '',
                        'comment_manager' => !empty($data['comment_manager']) ? $data['comment_manager'] : '',
                        'response_recom_manager' => !empty($data['response_recom_manager']) ? $data['response_recom_manager'] : '',
                        'comment_recom' => !empty($data['comment_recom']) ? $data['comment_recom'] : ''
                    );
                    $reDatas = $this->_repCharSymbol($reDatas);
                    $data = array_merge($data, $reDatas);
                    if($this->AuditRecom->save($data)){
                        $complete++;
                        $id = $this->AuditRecom->id;
                        /**
                         * Save Mission Manager
                         * Lay danh sach employee recom da tao
                         */
                        $listEmployRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'audit_recom_id' => $id,
                                'company_id' => $company_id,
                                'type' => 0
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if(!empty($mangers)){
                            foreach($mangers as $employ => $backup){
                                $recomManagers = array(
                                    'employee_id' => $employ,
                                    'company_id' => $company_id,
                                    'is_backup' => $backup,
                                    'audit_recom_id' => $id,
                                    'type' => 0
                                );
                                $checkDatas = $this->AuditRecomEmployeeRefer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => $recomManagers,
                                    'fields' => array('id')
                                ));
                                $this->AuditRecomEmployeeRefer->create();
                                if(!empty($checkDatas) && $checkDatas['AuditRecomEmployeeRefer']['id']){
                                    $this->AuditRecomEmployeeRefer->id = $checkDatas['AuditRecomEmployeeRefer']['id'];
                                }
                                if($this->AuditRecomEmployeeRefer->save($recomManagers)){
                                    $lastIdEmployRecoms = $this->AuditRecomEmployeeRefer->id;
                                    unset($listEmployRecoms[$lastIdEmployRecoms]);
                                }
                            }
                        }
                        if(!empty($listEmployRecoms)){
                            $this->AuditRecomEmployeeRefer->deleteAll(array('AuditRecomEmployeeRefer.id' => $listEmployRecoms), false);
                        }
                        /**
                         * Save Operator Modification
                         * Lay danh sach employee Operator Modification da tao
                         */
                        /*
                        $listEmployOperators = $this->AuditRecomEmployeeRefer->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'audit_recom_id' => $id,
                                'company_id' => $company_id,
                                'type' => 1
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if(!empty($operators)){
                            foreach($operators as $employ => $backup){
                                $readableBy = array(
                                    'employee_id' => $employ,
                                    'company_id' => $company_id,
                                    'is_backup' => 0,
                                    'type' => 1,
                                    'audit_recom_id' => $id
                                );
                                $checkDatas = $this->AuditRecomEmployeeRefer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => $readableBy,
                                    'fields' => array('id')
                                ));
                                $this->AuditRecomEmployeeRefer->create();
                                if(!empty($checkDatas) && $checkDatas['AuditRecomEmployeeRefer']['id']){
                                    $this->AuditRecomEmployeeRefer->id = $checkDatas['AuditRecomEmployeeRefer']['id'];
                                }
                                if($this->AuditRecomEmployeeRefer->save($readableBy)){
                                    $lastIdEmployOperators = $this->AuditRecomEmployeeRefer->id;
                                    unset($listEmployOperators[$lastIdEmployOperators]);
                                }
                            }
                        }
                        if(!empty($listEmployOperators)){
                            $this->AuditRecomEmployeeRefer->deleteAll(array('AuditRecomEmployeeRefer.id' => $listEmployOperators), false);
                        }
                        */
                    }
                }
                $this->Session->setFlash(sprintf(__('The Mission has been imported %s/%s.', true), $complete, count($import)));
                if(!empty($audit_mission_id)){
                    $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
                } else {
                    $this->redirect(array('action' => 'index_follow_employ'));
                }
            } else { // export csv
                App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
                $csv = new parseCSV();
                header("Content-Type: text/html; charset=ISO-8859");
                // export
                $header = array();
                $type = '';
                if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
                    $type = 'Error';
                if ($this->data['Import']['type'] == 'Create' && !empty($this->data['Create']['export']))
                    $type = 'Create';
                if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
                    $type = 'Update';
                if (!empty($type)){
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = __($key , true);
                    }
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    if(!empty($audit_mission_id)){
                        $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
                    } else {
                        $this->redirect(array('action' => 'index_follow_employ'));
                    }
                }
            }
        } else {
            if(!empty($audit_mission_id)){
                $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
            } else {
                $this->redirect(array('action' => 'index_follow_employ'));
            }
        }
        exit;
    }

    /**
     * Chuyen doi va xoa cac ky tu symbol
     */
    private function _repCharSymbol($datas){
        $result = array();
        if(!empty($datas)){
            foreach($datas as $key => $data){
                $data = mb_convert_encoding($data, 'UTF-16LE', 'UTF-8');
                $data = str_replace('‘', "'", $data);
                $data = str_replace('’', "'", $data);
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-16LE');
                $result[$key] = $data;
            }
        }
        return $result;
    }

    /**
     * Chuyen header sang dinh dang UTF 8
     */
    private function _mix_coloumn($input){
        $result = array();
        foreach($input as $value){
            $result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    /**
     * Chuyen gia tri sang dinh dang UTF 8
     */
    private function _utf8_encode_mix($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key, 'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value, 'UTF-16LE', 'UTF-8');
        }
        return $result;
    }

    /**
     * Ham dung de nhan dang va dinh dang lai date time
     */
    private function _formatDateCustom($date = null){
        $date = str_replace('/', '-', $date);
        $date = preg_replace('/[^\d-]/i', '', $date);
        $date = explode('-', $date);
        $currentDate = date('Y', time());
        $century = substr($currentDate, 0, 2);
        $day = $month = $year = 0;
        $day = !empty($date[0]) ? preg_replace('/\D/i', '', $date[0]) : '00';
        $month = !empty($date[1]) ? preg_replace('/\D/i', '', $date[1]) : '00';
        $year = !empty($date[2]) ? preg_replace('/\D/i', '', $date[2]) : '0000';
        $result = $year . '-' . $month . '-' . $day;
        $result = strtotime($result);
        return $result;
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($company_id = null, $audit_mission_id = null, $id = null, $checkAction = null) {
        // $company_info = $this->_getCompany($company_id);
        // if( !$company_info ){
        //     $this->Session->setFlash(__('Company not found.', true), 'error');
        //     $this->redirect('/audit_recoms');
        // }
        // $company_id = $company_info['Company']['id'];
        if( !isset($this->employee_info['Company']['id']) ){
            $this->Session->setFlash(__('Data not found.', true), 'error');
            $this->redirect('/audit_recoms/index_follow_employ');
        }
        $company_id = $this->employee_info['Company']['id'];
        $this->Session->write('reCheckActionAuditRecom', $checkAction);
        $this->loadModel('Employee');
        $this->loadModel('AuditSetting');
        $this->loadModel('AuditMission');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecomEmployeeRefer');
        $this->loadModel('AuditRecomFile');
        if($id == '-1'){
            $id = null;
        }
        if($id == null){ // them
            $reCheckRecom = true;
        } else { // sua
            $reCheckRecom = $this->AuditRecom->find('count', array(
            'recursive' => -1,
            'conditions' => array('AuditRecom.id' => $id, 'AuditRecom.company_id' => $company_id)));
        }
        $reCheckMision = $this->AuditMission->find('count', array(
            'recursive' => -1,
            'conditions' => array('AuditMission.id' => $audit_mission_id, 'AuditMission.company_id' => $company_id)));
        if(!$reCheckRecom || !$reCheckMision){
            $this->Session->setFlash(__('Data not found.', true), 'error');
            $this->redirect('/audit_recoms/index_follow_employ');
        }
        /**
         * Check Permission
         */
         list($isUsing, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
        if($isUsing == false){
            $this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
            $this->redirect('/audit_recoms/index_follow_employ');
        }
        /**
         * Check Permission
         */
        list($editMission, $editRecom, $disabledIDRecom) = $this->_checkPermissionIsUsingAudit($audit_mission_id, $id);
        if($editMission == false && $id == null){
            $this->Session->setFlash(__('You do not have permission to create Recommendation in this Mission.', true), 'error');
        }
        /**
         * Save Data
         */
        if(!empty($this->data)){
            if(!empty($this->data['audit_recom_manager'])){
                $this->data['audit_recom_manager'] = array_unique($this->data['audit_recom_manager']);
                if(($key = array_search(0, $this->data['audit_recom_manager'])) !== false) {
                    unset( $this->data['audit_recom_manager'][$key]);
                }
            }
            if(!empty($this->data['is_backup'])){
                $this->data['is_backup'] = array_unique($this->data['is_backup']);
                if(($key = array_search(0, $this->data['is_backup'])) !== false) {
                    unset( $this->data['is_backup'][$key]);
                }
            }
            if(!empty($this->data['author_modify'])){
                $this->data['author_modify'] = array_unique($this->data['author_modify']);
                if(($key = array_search(0, $this->data['author_modify'])) !== false) {
                    unset( $this->data['author_modify'][$key]);
                }
            }
            $countMissionManager = isset($this->data['audit_recom_manager']) ? (count($this->data['audit_recom_manager']) - 1) : 0;
            $countMissionManagerBackup = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            if($countMissionManager != $countMissionManagerBackup){
                $this->data = $this->AuditRecom->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('AuditRecom.id' => $id,
                            'AuditRecom.company_id' => $company_id)
                ));
                $this->Session->setFlash(__('Recommendation Manager data is not valid. Please input again.', true), 'error');
            } else {
                $this->AuditRecom->create();
                if (!empty($this->data['AuditRecom']['id'])) {
                    $this->AuditRecom->id = $this->data['AuditRecom']['id'];
                    $oldStatus = $this->AuditRecom->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'AuditRecom.id' => $this->AuditRecom->id,
                            'AuditRecom.company_id' => $company_id
                        ),
                        'fields' => array('audit_setting_recom_status_mission', 'audit_setting_recom_status_recom')
                    ));
                    if(!empty($oldStatus)){
                        $oldStatusMission = $oldStatus['AuditRecom']['audit_setting_recom_status_mission'];
                        $oldStatusRecom = $oldStatus['AuditRecom']['audit_setting_recom_status_recom'];
                        if(!empty($this->data['AuditRecom']['audit_setting_recom_status_mission']) && $oldStatusMission != $this->data['AuditRecom']['audit_setting_recom_status_mission']){
                            $this->data['AuditRecom']['date_change_status_mission'] = time();
                        }
                        if(!empty($this->data['AuditRecom']['audit_setting_recom_status_recom']) && $oldStatusRecom != $this->data['AuditRecom']['audit_setting_recom_status_recom']){
                            $this->data['AuditRecom']['date_change_status_recom'] = time();
                            $this->data['AuditRecom']['author_modify'] = $this->employee_info['Employee']['fullname'];
                        }
                    }
                } else {
                    if(!empty($this->data['AuditRecom']['audit_setting_recom_status_mission'])){
                        $this->data['AuditRecom']['date_change_status_mission'] = time();
                    }
                    if(!empty($this->data['AuditRecom']['audit_setting_recom_status_recom'])){
                        $this->data['AuditRecom']['date_change_status_recom'] = time();
                        $this->data['AuditRecom']['author_modify'] = $this->employee_info['Employee']['fullname'];
                    }
                }
                unset($this->data['AuditRecom']['id']);
                /**
                 * Save Audit Mission
                 */
                $this->data['AuditRecom']['company_id'] = $company_id;
                if(!empty($this->data['AuditRecom']['implement_date'])){
                    $this->data['AuditRecom']['implement_date'] = strtotime(str_replace('/', '-', $this->data['AuditRecom']['implement_date']));
                }
                if(!empty($this->data['AuditRecom']['implement_revised'])){
                    $this->data['AuditRecom']['implement_revised'] = strtotime(str_replace('/', '-', $this->data['AuditRecom']['implement_revised']));
                }
                $this->data['AuditRecom']['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                $this->data['AuditRecom']['audit_mission_id'] = $audit_mission_id;
                if($this->AuditRecom->save($this->data['AuditRecom'])){
                    $id = $this->AuditRecom->id;
                    /**
                     * Save Log
                     */
                    $dataNows = $this->AuditRecom->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('AuditRecom.id' => $id),
                        'fields' => array('recommendation')
                    ));
                    if(!empty($dataNows) && $dataNows['AuditRecom']['recommendation']){
                        $saveLogs = array(
                            'company_id' => $company_id,
                            'model' => 'AuditRecom',
                            'model_id' => $id,
                            'name' => $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time()),
                            'description' => $dataNows['AuditRecom']['recommendation'],
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'update_by_employee' => $this->employee_info['Employee']['fullname']
                        );
                        $this->LogSystem->saveLogSystem($saveLogs);
                    }
                    /**
                     * Save Mission Manager
                     * Lay danh sach employee recom da tao
                     */
                    $listEmployRecoms = $this->AuditRecomEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'audit_recom_id' => $id,
                            'company_id' => $company_id,
                            'type' => 0
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['audit_recom_manager'])){
                        foreach($this->data['audit_recom_manager'] as $value){
                            $is_backup = 0;
                            if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                                $is_backup = 1;
                            }
                            $recomManagers = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => $is_backup,
                                'audit_recom_id' => $id,
                                'type' => 0
                            );
                            $checkDatas = $this->AuditRecomEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $recomManagers,
                                'fields' => array('id')
                            ));
                            $this->AuditRecomEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['AuditRecomEmployeeRefer']['id']){
                                $this->AuditRecomEmployeeRefer->id = $checkDatas['AuditRecomEmployeeRefer']['id'];
                            }
                            if($this->AuditRecomEmployeeRefer->save($recomManagers)){
                                $lastIdEmployRecoms = $this->AuditRecomEmployeeRefer->id;
                                unset($listEmployRecoms[$lastIdEmployRecoms]);
                            }
                        }
                    }
                    if(!empty($listEmployRecoms)){
                        $this->AuditRecomEmployeeRefer->deleteAll(array('AuditRecomEmployeeRefer.id' => $listEmployRecoms), false);
                    }
                    /**
                     * Save Operator Modification
                     * Lay danh sach employee Operator Modification da tao
                     */
                    $listEmployOperators = $this->AuditRecomEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'audit_recom_id' => $id,
                            'company_id' => $company_id,
                            'type' => 1
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['author_modify'])){
                        foreach($this->data['author_modify'] as $value){
                            $readableBy = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => 0,
                                'type' => 1,
                                'audit_recom_id' => $id
                            );
                            $checkDatas = $this->AuditRecomEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $readableBy,
                                'fields' => array('id')
                            ));
                            $this->AuditRecomEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['AuditRecomEmployeeRefer']['id']){
                                $this->AuditRecomEmployeeRefer->id = $checkDatas['AuditRecomEmployeeRefer']['id'];
                            }
                            if($this->AuditRecomEmployeeRefer->save($readableBy)){
                                $lastIdEmployOperators = $this->AuditRecomEmployeeRefer->id;
                                unset($listEmployOperators[$lastIdEmployOperators]);
                            }
                        }
                    }
                    if(!empty($listEmployOperators)){
                        $this->AuditRecomEmployeeRefer->deleteAll(array('AuditRecomEmployeeRefer.id' => $listEmployOperators), false);
                    }
                    $this->Session->setFlash('Save.', 'success');
                }  else {
                    $this->Session->setFlash(__('Not Saved.', true), 'error');
                }
                $this->redirect(array('action' => 'update', $company_id, $audit_mission_id, $id, $checkAction));
            }
        } else {
            $this->data = $this->AuditRecom->find('first', array(
                'recursive' => -1,
                'conditions' => array('AuditRecom.id' => $id, 'AuditRecom.company_id' => $company_id)
            ));
        }
        /**
         * Group By follow Type. Key Type xem o $categoryOfAudits global de xac dinh the loai
         */
        $auditSettings = $this->AuditSetting->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'type' => array_keys($this->categoryOfAudits)
            ),
            'fields' => array('id', 'name', 'type'),
            'group' => array('type', 'id'),
            'order' => array('weight' => 'ASC')
        ));
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
         * Employee Refer Mision
         */
        $auditMissionEmployeeRefers = $this->AuditMissionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_mission_id' => $audit_mission_id,
                'type' => 1
            ),
            'fields' => array('employee_id', 'is_backup', 'type'),
            'group' => array('type', 'id')
        ));
        /**
         * Get Mission
         */
        $auditMissions = $this->AuditMission->find('first', array(
                'recursive' => -1,
                'conditions' => array('AuditMission.id' => $audit_mission_id),
                'fields' => array('mission_title', 'mission_number', 'audit_setting_mission_status', 'audit_setting_auditor_company')
            ));
        $auditMissions = !empty($auditMissions) ? $auditMissions['AuditMission'] : array();
        /**
         * Lay ID cuoi cung audit recom
         */
        $lastIdOfAuditRecom = $this->AuditRecom->find('first', array(
            'recursive' => -1,
            'limit' => 1,
            'order' => array('AuditRecom.id' => 'DESC'),
            'fields' => array('id')
        ));
        $lastIdOfAuditRecom = !empty($lastIdOfAuditRecom) && $lastIdOfAuditRecom['AuditRecom']['id'] ? $lastIdOfAuditRecom['AuditRecom']['id']+1 : '';
        $lastIdOfAuditRecom = !empty($lastIdOfAuditRecom) ? $lastIdOfAuditRecom : 1;
        /**
         * Employee Refer Recom
         */
        $auditRecomEmployeeRefers = $this->AuditRecomEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_recom_id' => $id,
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'is_backup', 'type'),
            'group' => array('type', 'id')
        ));
        /**
         * File Attachments
         */
        $auditRecomFiles = $this->AuditRecomFile->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_recom_id' => $id
            ),
            'fields' => array('id', 'audit_recom_id', 'file_attachment', 'size'),
        ));
        $auditRecomFiles = !empty($auditRecomFiles) ? Set::classicExtract($auditRecomFiles, '{n}.AuditRecomFile') : array();
        $this->set(compact('disabledIDRecom', 'company_id', 'audit_mission_id', 'id', 'auditMissions', 'auditSettings', 'employees', 'auditMissionEmployeeRefers', 'lastIdOfAuditRecom', 'auditRecomEmployeeRefers', 'auditRecomFiles', 'editMission', 'editRecom', 'checkAction'));
    }

    /**
     * Upload
     */
    public function upload($company_id = null, $id = null, $audit_mission_id = null, $checkAction = null) {
        $this->layout = 'ajax';
        $result = array();
        $_FILES['FileField'] = array();
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if(!empty($_FILES)){
            $path = $this->_getPath($company_id, $id);
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
                $this->loadModel('AuditRecomFile');
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->AuditRecomFile->create();
                if ($this->AuditRecomFile->save(array(
                    'audit_recom_id' => $id,
                    'file_attachment' => $attachment,
                    'size' => $size,
                    'type' => $type))) {
                    $lastId = $this->AuditRecomFile->id;
                    $result = $this->AuditRecomFile->find('first', array('recursive' => -1, 'conditions' => array('AuditRecomFile.id' => $lastId)));
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
                $dataSession = array(
					'path' => $path,
					'file' => $attachment
				);
				$_SESSION['file_multiupload'][] = $dataSession;
                if(!empty($checkAction)){
                    $_SESSION['file_multiupload_redirect'] = '/audit_recoms/update/' . $company_id . '/' . $audit_mission_id . '/' . $id . '/' . $checkAction;
                } else {
                    $_SESSION['file_multiupload_redirect'] = '/audit_recoms/update/' . $company_id . '/' . $audit_mission_id . '/' . $id;
                }
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

    public function attachment($company_id = null, $audit_recom_id = null, $id = null, $type = null, $audit_mission_id = null) {
        $this->loadModel('AuditRecomFile');
        $last = $this->AuditRecomFile->find('first', array(
            'recursive' => -1,
            'fields' => array('audit_recom_id', 'file_attachment'),
            'conditions' => array(
                'AuditRecomFile.id' => $id,
                'AuditRecomFile.audit_recom_id' => $audit_recom_id
            )
        ));
        $error = true;
        if ($last && $last['AuditRecomFile']['audit_recom_id']) {
            $attachment = $last['AuditRecomFile']['file_attachment'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                $path = trim($this->_getPath($company_id, $last['AuditRecomFile']['audit_recom_id']));
                $this->MultiFileUpload->downloadFileToServerOther($path, $attachment);
            } else {
                $path = trim($this->_getPath($company_id, $last['AuditRecomFile']['audit_recom_id'])
                        . $last['AuditRecomFile']['file_attachment']);
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
                $reCheckAction = $this->Session->read('reCheckActionAuditRecom');
                @unlink($path);
                $this->AuditRecomFile->delete($id);
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($company_id, $last['AuditRecomFile']['audit_recom_id']));
                    $redirect = '/audit_recoms/update/' . $company_id . '/' . $audit_mission_id . '/' . $audit_recom_id;
                    if(!empty($reCheckAction)){
                        $redirect = '/audit_recoms/update/' . $company_id . '/' . $audit_mission_id . '/' . $audit_recom_id . '/' . $reCheckAction;
                    }
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, $redirect);
                }
                $this->redirect(array('action' => 'update', $company_id, $audit_mission_id, $audit_recom_id));
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'update', $company_id, $audit_mission_id, $audit_recom_id));
        }
    }

    protected function _getPath($company_id = null, $audit_id = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'audit' . DS . 'recoms' . DS;
        $path .= $company['Company']['dir'] . DS . $audit_id . DS;
        return $path;
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($company_id = null, $audit_mission_id = null, $id = null, $checkAction = null) {
        if( @$this->employee_info['Company']['id'] != $company_id ){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            $this->redirect(array('action' => 'index_follow_employ'));
        }
        $this->loadModel('AuditRecomFile');
        $this->loadModel('AuditRecomEmployeeRefer');
        /**
         * Check Permission
         */
        list($editMission, $editRecom) = $this->_checkPermissionIsUsingAudit($audit_mission_id, $id);
        if($editMission == false){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            if(!empty($checkAction)){
                $this->redirect(array('action' => 'index_follow_employ'));
            } else {
                $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
            }
        }
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Recommandation', true), 'error');
            if(!empty($checkAction)){
                $this->redirect(array('action' => 'index_follow_employ'));
            } else {
                $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
            }
        }
        if ($this->_getCompany($company_id) && $this->AuditRecom->delete($id)) {
            /**
             * Xoa cac file attachment cua Recom
             */
            $files = $this->AuditRecomFile->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $id
                ),
                'fields' => array('id', 'file_attachment')
            ));
            if(!empty($files)){
                foreach($files as $idFile => $nameFile){
                    $path = trim($this->_getPath($company_id, $id)
                        . $nameFile);
                    @unlink($path);
                }
            }
            $this->AuditRecomFile->deleteAll(array('audit_recom_id' => $id), false);
            /**
             * Xoa Employee Refers Recom
             */
            $this->AuditRecomEmployeeRefer->deleteAll(array('audit_recom_id' => $id), false);
            /**
             * Set lai AUTO_INCREMENT cho table audit_recoms
             */
            $db = ConnectionManager::getDataSource('default');
            $setAuto = 'ALTER TABLE audit_recoms AUTO_INCREMENT = 1';
            $db->query($setAuto);
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        if(!empty($checkAction)){
            $this->redirect(array('action' => 'index_follow_employ'));
        } else {
            $this->redirect(array('action' => 'index', $company_id, $audit_mission_id));
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

    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingAudit($audit_mission_id = null, $audit_recom_id = null){
        $this->loadModel('Employee');
        $this->loadModel('AuditAdmin');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecomEmployeeRefer');
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
        /**
         * Lay danh sach Mission Manager
         * Type = 1: Mission Manager
         * Type = 0: Readable by (chi dc read/view)
         */
        $missionManagers = $missionReadableBy = $recomAudits = array();
        if(!empty($audit_mission_id)){
            $missionAudits = $this->AuditMissionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $audit_mission_id,
                    'company_id' => $company_id,
                ),
                'fields' => array('employee_id', 'employee_id', 'type'),
                'group' => array('type', 'id')
            ));
            $missionManagers = !empty($missionAudits[1]) ? $missionAudits[1] : array();
            $missionReadableBy = !empty($missionAudits[0]) ? $missionAudits[0] : array();
        }
        /**
         * Lay danh sach Recommendation Manager
         */
        if(!empty($audit_recom_id)){
            $recomAudits = $this->AuditRecomEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'audit_recom_id' => $audit_recom_id,
                        'company_id' => $company_id,
                        'type' => 0
                    ),
                    'fields' => array('employee_id', 'employee_id', 'audit_recom_id'),
                    'group' => array('audit_recom_id', 'id')
                ));
            $recomAudits = !empty($recomAudits[$audit_recom_id]) ? $recomAudits[$audit_recom_id] : array();
        }
        /**
         * Danh sach cac employee duoc quyen sua doi Mission Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Mission Manager in Mission
         */
        //$authorEditMissions = array_unique(array_merge($adminOfCompanies, $adminAudits, $missionManagers));
        $authorEditMissions = array_unique(array_merge($adminAudits, $missionManagers));
        /**
         * Danh sach cac employee duoc quyen sua doi Recom Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Recom Manager in Recom
         */
        //$authorEditRecoms = array_unique(array_merge($adminOfCompanies, $adminAudits, $recomAudits));
        $authorEditRecoms = array_unique(array_merge($adminAudits, $recomAudits));
        /**
         * Danh sach cac employee duoc quyen su dung Recom Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Mission Manager in Mission
         * - Recom Manager in Recom
         */
        //$authorUsingRecoms = array_unique(array_merge($adminOfCompanies, $adminAudits, $missionManagers, $recomAudits));
        $authorUsingRecoms = array_unique(array_merge($adminAudits, $missionManagers, $recomAudits));
        /**
         * Mac dinh gia tri tat cac employee chi dc read/view.
         * Neu kiem tra employee_id co ton tai 1 trong 2 list tren thi duoc sua doi tuong ung danh sach tren
         */
        $editMission = $editRecom = $disabledIDRecom = $isUsing = false;
        if(in_array($employeeLogin, $authorEditMissions)){
            $editMission = true;
        }
        if(in_array($employeeLogin, $authorEditRecoms)){
            $editRecom = true;
        }
        //(in_array($employeeLogin, $recomAudits) &&
        if(!in_array($employeeLogin, $adminOfCompanies) && !in_array($employeeLogin, $adminAudits)){
            $disabledIDRecom = true;
        }
        if(in_array($employeeLogin, $authorUsingRecoms)){
            $isUsing = true;
        }
        return array($editMission, $editRecom, $disabledIDRecom, $isUsing);
    }
    /**
     * Phan quyen cho nguoi dung: Recommendation Follow Employee
     */
    private function _checkPermissionIsUsingAuditFollowEmployee($audit_mission_id = null, $audit_recom_id = null){
        $this->loadModel('Employee');
        $this->loadModel('AuditAdmin');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecomEmployeeRefer');
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
        /**
         * Lay danh sach Mission Manager
         * Type = 1: Mission Manager
         * Type = 0: Readable by (chi dc read/view)
         */
        $missionManagers = $missionReadableBy = $recomAudits = array();
        if(!empty($audit_mission_id)){
            $missionAudits = $this->AuditMissionEmployeeRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $audit_mission_id,
                    'company_id' => $company_id,
                ),
                'fields' => array('employee_id', 'employee_id', 'type'),
                'group' => array('type', 'id')
            ));
            $missionManagers = !empty($missionAudits[1]) ? $missionAudits[1] : array();
            $missionReadableBy = !empty($missionAudits[0]) ? $missionAudits[0] : array();
        }
        /**
         * Lay danh sach Recommendation Manager
         */
        if(!empty($audit_recom_id)){
            $recomAudits = $this->AuditRecomEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'audit_recom_id' => $audit_recom_id,
                        'company_id' => $company_id,
                        'type' => 0
                    ),
                    'fields' => array('employee_id', 'employee_id', 'audit_recom_id'),
                    'group' => array('audit_recom_id', 'id')
                ));
            $recomAudits = !empty($recomAudits[$audit_recom_id]) ? $recomAudits[$audit_recom_id] : array();
        }
        /**
         * Danh sach cac employee duoc quyen sua doi Mission Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Mission Manager in Mission
         */
        //$authorEditMissions = array_unique(array_merge($adminOfCompanies, $adminAudits, $missionManagers));
        $authorEditMissions = array_unique(array_merge($adminAudits, $missionManagers));
        /**
         * Danh sach cac employee duoc quyen sua doi Recom Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Recom Manager in Recom
         */
        //$authorEditRecoms = array_unique(array_merge($adminOfCompanies, $adminAudits, $recomAudits));
        $authorEditRecoms = array_unique(array_merge($adminAudits, $recomAudits));
        /**
         * Mac dinh gia tri tat cac employee chi dc read/view.
         * Neu kiem tra employee_id co ton tai 1 trong 2 list tren thi duoc sua doi tuong ung danh sach tren
         */
        $editMission = $editRecom = false;
        if(in_array($employeeLogin, $authorEditMissions)){
            $editMission = true;
        }
        if(in_array($employeeLogin, $authorEditRecoms)){
            $editRecom = true;
        }
        return array($editMission, $editRecom);
    }
    // export Excel
    public function export_excel_follow_employ(){
    	if( !empty($this->data) ){
    		$this->SlickExporter->init();
    		$data = json_decode($this->data['data'], true);
    		$this->SlickExporter
    			->setT('Audit Recoms')	//auto translate
    			->save($data, 'audit_recom_{date}.xls');
    	}
    	die;
    }
}
?>
