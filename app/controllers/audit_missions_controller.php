<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class AuditMissionsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AuditMissions';
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
    function index($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditAdmin');
        $this->loadModel('AuditRecom');
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
             * Danh sach audit mission
             */
            $auditMissions = $this->AuditMission->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'mission_title', 'mission_number', 'audit_setting_mission_status', 'audit_setting_auditor_company')
            ));
            $idOfAuditMissions = !empty($auditMissions) ? Set::classicExtract($auditMissions, '{n}.AuditMission.id') : array();
            $auditMissions = !empty($auditMissions) ? Set::combine($auditMissions, '{n}.AuditMission.id', '{n}.AuditMission') : array();
            $auditRecomOfMissions = $this->AuditRecom->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'audit_mission_id' => $idOfAuditMissions),
                'fields' => array('id', 'audit_mission_id', 'COUNT(id) as TotalRecomOfMission'),
                'group' => array('audit_mission_id')
            ));
            $auditRecomOfMissions = !empty($auditRecomOfMissions) ? Set::combine($auditRecomOfMissions, '{n}.AuditRecom.audit_mission_id', '{n}.0.TotalRecomOfMission') : array();
            /**
             * Lay danh sach mission manager.
             * Xoa cac audit mission ma employee khong quyen nhin thay
             */
            $auditMissionEmployees = $this->AuditMissionEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $idOfAuditMissions,
                    'company_id' => $company_id
                ),
                'fields' => array('employee_id', 'is_backup', 'audit_mission_id', 'type')
            ));
            $employForAuditMissions = array();
            if(!empty($auditMissionEmployees)){
                foreach($auditMissionEmployees as $auditMissionEmployee){
                    $dx = $auditMissionEmployee['AuditMissionEmployeeRefer'];
                    if(!empty($employForAuditMissions[$dx['audit_mission_id']][$dx['employee_id']])){
                        $employForAuditMissions[$dx['audit_mission_id']][$dx['employee_id']] = $dx['employee_id'];
                    }
                    $employForAuditMissions[$dx['audit_mission_id']][$dx['employee_id']] = $dx['employee_id'];
                    if($dx['type'] == 1){
                        if(!empty($missionManagers[$dx['audit_mission_id']][$dx['employee_id']])){
                            $missionManagers[$dx['audit_mission_id']][$dx['employee_id']] = '';
                        }
                        $missionManagers[$dx['audit_mission_id']][$dx['employee_id']] = $dx['is_backup'];
                    }

                }
            }
            if(!empty($employForAuditMissions)){
                foreach($employForAuditMissions as $auditId => $employForAuditMission){
                    if(!in_array($employeeLogin, $employForAuditMission) && !in_array($employeeLogin, $adminAudits)){ //&& $role != 'admin'
                        unset($auditMissions[$auditId]);
                        if(!empty($auditMissions[$auditId])){
                            unset($auditMissions[$auditId]);
                        }
                        if(!empty($missionManagers[$auditId])){
                            unset($missionManagers[$auditId]);
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
			$this->set(compact('auditRecomOfMissions', 'displayImport', 'company_id', 'companyName', 'auditMissions', 'missionManagers', 'auditSettings', 'employees'));
        }
    }

    /**
     * Import CSV
     */
    public function import_csv($company_id = null){
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'AuditMissions' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'AuditMissions' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                //debug($csv->data); exit;
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    $columnMandatory = array(
                        'Mission Title',
                        'Mission Number',
                        'Mission Status',
                        'Auditor Company',
                        'Auditor',
                        'Audited Company',
                        'Mission Manager',
                        'Mission Type',
                        'Mission Validation Date',
                        'Readable By',
                        'Comments'
                    );
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $validate = array('Mission Title', 'Mission Manager');
                    $defaultKeys = array_keys($default);
                    $count = !empty($default) ? count($default) : 1;

                    $this->loadModel('Employee');
                    $this->loadModel('AuditSetting');
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
                    $missionTypes = !empty($auditSettings[2]) ? array_change_key_case($auditSettings[2], CASE_LOWER) : array();
                    $missionStatus = !empty($auditSettings[1]) ? array_change_key_case($auditSettings[1], CASE_LOWER) : array();
                    $auditorCompanies = !empty($auditSettings[0]) ? array_change_key_case($auditSettings[0], CASE_LOWER) : array();
                    $titleHaveUsing = array();
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
                            // Mission Number
                            if(!empty($row['Mission Number'])){
                                $row['data']['mission_number'] = $row['Mission Number'];
                            }
                            // Mission Status
                            if(!empty($row['Mission Status'])){
                                $_status = strtolower(trim($row['Mission Status']));
                                if(!empty($missionStatus[$_status])){
                                    $row['data']['audit_setting_mission_status'] = $missionStatus[$_status];
                                } else {
                                    $row['columnHighLight']['Mission Status'] = '';
                                    $row['error'][] = __('The Mission Status not found in system.', true);
                                }
                            }
                            // Auditor Company
                            if(!empty($row['Auditor Company'])){
                                $_auditor = strtolower(trim($row['Auditor Company']));
                                if(!empty($auditorCompanies[$_auditor])){
                                    $row['data']['audit_setting_auditor_company'] = $auditorCompanies[$_auditor];
                                } else {
                                    $row['columnHighLight']['Auditor Company'] = '';
                                    $row['error'][] = __('The Auditor Company not found in system.', true);
                                }
                            }
                            // Mission Manager
                            if(!empty($row['Mission Manager'])){
                                $manager = explode(',', $row['Mission Manager']);
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
                                        $row['columnHighLight']['Mission Manager'] = '';
                                        $row['error'][] = __('The Mission needs a Primary Management', true);
                                    } else {
                                        if($totalManager > 1){ // co 2 or nhieu manager chinh -> loi
                                            $row['columnHighLight']['Mission Manager'] = '';
                                            $row['error'][] = __('The Mission justs a Manager Primary', true);
                                        } else { // okie
                                            // okie. do nothing
                                        }
                                    }
                                    /**
                                     * Manager backup
                                     */
                                    if($totalBackup == count($manager)){ // tat ca employ nhap vao dieu la backup -> loi
                                        $row['columnHighLight']['Mission Manager'] = '';
                                        $row['error'][] = __('The Mission musts a Primary Management', true);
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
                                        $row['columnHighLight']['Mission Manager'] = '';
                                        $row['error'][] = __('The Mision needs a Primary Management', true);
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
                                $row['data']['mission_manager'] = serialize($valManager);
                                if(empty($valManager)){
                                    $row['columnHighLight']['Mission Manager'] = '';
                                    $row['error'][] = __('The Mission Manager is not blank', true);
                                }
                            }
                            // Mission Title
                            if(!empty($row['Mission Title'])){
                                $_title = (trim($row['Mission Title']));
                                if(in_array($_title, $titleHaveUsing)){
                                    $row['columnHighLight']['Mission Title'] = '';
                                    $row['error'][] = __('The Mision Title has identical', true);
                                } else {
                                    $tmp = $this->AuditMission->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array(
                                            'company_id' => $company_id,
                                            'AuditMission.mission_title' => $_title
                                        ),
                                        'fields' => array('id')
                                    ));
                                    if(!empty($tmp) && $tmp['AuditMission']['id']){ // da co trong he thong
                                        $row['data']['id'] = $tmp['AuditMission']['id'];
                                        $row['data']['mission_title'] = trim($row['Mission Title']);
                                    } else {
                                        $row['data']['mission_title'] = trim($row['Mission Title']);
                                    }
                                    $titleHaveUsing[$_title] = $_title;
                                }
                            }
                            // Auditor
                            if(!empty($row['Auditor'])){
                                $row['data']['auditor'] = $row['Auditor'];
                            }
                            // Audited Company
                            if(!empty($row['Audited Company'])){
                                $row['data']['audited_company'] = $row['Audited Company'];
                            }
                            // Mission Type
                            if(!empty($row['Mission Type'])){
                                $_type = strtolower(trim($row['Mission Type']));
                                if(!empty($missionTypes[$_type])){
                                    $row['data']['audit_setting_mission_type'] = $missionTypes[$_type];
                                } else {
                                    $row['columnHighLight']['Mission Type'] = '';
                                    $row['error'][] = __('The Mission Type not found in system.', true);
                                }
                            }
                            // Mission Validation Date
                            if(!empty($row['Mission Validation Date'])){
                                $date = $this->_formatDateCustom($row['Mission Validation Date']);
                                $row['data']['mission_validation_date'] = $date;
                            }
                            // Readable By
                            if(!empty($row['Readable By'])){
                                $readbles = explode(',', $row['Readable By']);
                                $valReadables = array();
                                foreach($readbles as $val){
                                    $val = strtolower(trim($val));
                                    if(in_array($val, array_keys($employeeIdCompanies))){
                                        $_valReadables = !empty($employeeIdCompanies[$val]) ? $employeeIdCompanies[$val] : 0;
                                        if(!in_array($_valReadables, array_keys($valManager))){
                                            $valReadables[$_valReadables] = 0;
                                        }
                                    }
                                }
                                /**
                                 * Set Readable By
                                 */
                                $row['data']['readable_by'] = serialize($valReadables);
                            }
                            // Comments
                            if(!empty($row['Comments'])){
                                $row['data']['comment'] = $row['Comments'];
                            }
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
            $this->set(compact('company_id'));
        } else {
            $this->set(compact('company_id'));
            $this->redirect(array('action' => 'index', $company_id));
        }
    }

    /**
     * Save import of project task
     */
    function save_file_import($company_id = null) {
        $this->loadModel('AuditSetting');
        $this->loadModel('AuditMissionEmployeeRefer');
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
                    $this->redirect(array('action' => 'index', $company_id));
                }
                /**
                 * Get Status Mission: Terminé, completed, finished, Done, ended, complete, terminée
                 */
                $missionStatusFinish = $this->AuditSetting->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'AuditSetting.status' => array('fin'),
                        'type' => 1
                    ),
                    'fields' => array('id')
                ));
                $complete = 0;
                foreach($import as $key => $data){
                    $this->AuditMission->create();
                    if(!empty($data['id'])){
                        $this->AuditMission->id = $data['id'];
                    }
                    $mangers = unserialize($data['mission_manager']);
                    $readables = !empty($data['readable_by']) ? unserialize($data['readable_by']) : array();
                    unset($data['mission_manager']);
                    unset($data['readable_by']);
                    unset($data['id']);
                    if(!empty($missionStatusFinish) && $missionStatusFinish['AuditSetting']['id'] && !empty($data['audit_setting_mission_status']) && $missionStatusFinish['AuditSetting']['id'] == $data['audit_setting_mission_status']){
                        $data['mission_closing_date'] = time();
                    } else {
                        $data['mission_closing_date'] = '';
                    }
                    $data['company_id'] = $company_id;
                    $data['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                    $reDatas = array(
                        'mission_title' => !empty($data['mission_title']) ? $data['mission_title'] : '',
                        'auditor' => !empty($data['auditor']) ? $data['auditor'] : '',
                        'audited_company' => !empty($data['audited_company']) ? $data['audited_company'] : '',
                        'comment' => !empty($data['comment']) ? $data['comment'] : ''
                    );
                    $reDatas = $this->_repCharSymbol($reDatas);
                    $data = array_merge($data, $reDatas);
                    if($this->AuditMission->save($data)){
                        $complete++;
                        $id = $this->AuditMission->id;
                        /**
                         * Save Mission Manager
                         * Lay danh sach employee mission manager da tao
                         */
                        $listEmployMissionManagers = $this->AuditMissionEmployeeRefer->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'audit_mission_id' => $id,
                                'type' => 1
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if(!empty($mangers)){
                            foreach($mangers as $employ => $backup){
                                $missionManagers = array(
                                    'employee_id' => $employ,
                                    'company_id' => $company_id,
                                    'is_backup' => $backup,
                                    'type' => 1,
                                    'audit_mission_id' => $id
                                );
                                $checkDatas = $this->AuditMissionEmployeeRefer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => $missionManagers,
                                    'fields' => array('id')
                                ));
                                $this->AuditMissionEmployeeRefer->create();
                                if(!empty($checkDatas) && $checkDatas['AuditMissionEmployeeRefer']['id']){
                                    $this->AuditMissionEmployeeRefer->id = $checkDatas['AuditMissionEmployeeRefer']['id'];
                                }
                                if($this->AuditMissionEmployeeRefer->save($missionManagers)){
                                    $lastIdEmployMissionManagers = $this->AuditMissionEmployeeRefer->id;
                                    unset($listEmployMissionManagers[$lastIdEmployMissionManagers]);
                                }
                            }
                        }
                        if(!empty($listEmployMissionManagers)){
                            $this->AuditMissionEmployeeRefer->deleteAll(array('AuditMissionEmployeeRefer.id' => $listEmployMissionManagers), false);
                        }
                        /**
                         * Save Readable by
                         * Lay danh sach employee mission manager da tao
                         */
                        $listEmployReadables = $this->AuditMissionEmployeeRefer->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'audit_mission_id' => $id,
                                'type' => 0
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if(!empty($readables)){
                            foreach($readables as $employ => $backup){
                                $readableBy = array(
                                    'employee_id' => $employ,
                                    'company_id' => $company_id,
                                    'is_backup' => 0,
                                    'type' => 0,
                                    'audit_mission_id' => $id
                                );
                                $checkDatas = $this->AuditMissionEmployeeRefer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => $readableBy,
                                    'fields' => array('id')
                                ));
                                $this->AuditMissionEmployeeRefer->create();
                                if(!empty($checkDatas) && $checkDatas['AuditMissionEmployeeRefer']['id']){
                                    $this->AuditMissionEmployeeRefer->id = $checkDatas['AuditMissionEmployeeRefer']['id'];
                                }
                                if($this->AuditMissionEmployeeRefer->save($readableBy)){
                                    $lastIdEmployreadableBy = $this->AuditMissionEmployeeRefer->id;
                                    unset($listEmployReadables[$lastIdEmployreadableBy]);
                                }
                            }
                        }
                        if(!empty($listEmployReadables)){
                            $this->AuditMissionEmployeeRefer->deleteAll(array('AuditMissionEmployeeRefer.id' => $listEmployReadables), false);
                        }
                    }
                }
                $this->Session->setFlash(sprintf(__('The Mission has been imported %s/%s.', true), $complete, count($import)));
                $this->redirect(array('action' => 'index', $company_id));
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
                    $this->redirect(array('action' => 'index', $company_id));
                }
            }
        } else {
            $this->redirect(array('action' => 'index', $company_id));
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
     * exportExcel
     * @param int $project_id
     * @return void
     * @access public
     */
    function export($company_id = null) {
        $this->loadModel('Company');
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditMissionFile');
        if (empty($this->data['Export']['list'])) {
            $this->Session->setFlash(__('No data to export!', true));
            $this->redirect(array('action' => 'index'));
        }
        $data = array_filter(explode(',', $this->data['Export']['list']));
        // $companyName = $this->_getCompany($company_id);
        if (!isset($this->employee_info['Company']['id'])) {
            $this->redirect(array('controller' => 'audit_missions'));
            $this->Session->setFlash(sprintf(__('Data not found.', true), $company_id), 'error');
        }
        // $company_id = $companyName['Company']['id'];
        $company_id = $this->employee_info['Company']['id'];
        /**
         * Danh sach audit mission
         */
        $auditMissions = $this->AuditMission->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'AuditMission.id' => $data),
            'fields' => array('id', 'mission_title', 'mission_number', 'audit_setting_mission_status', 'audit_setting_auditor_company', 'auditor', 'audited_company', 'audit_setting_mission_type', 'mission_validation_date', 'comment', 'mission_closing_date')
        ));
        $auditMissions = !empty($auditMissions) ? Set::combine($auditMissions, '{n}.AuditMission.id', '{n}.AuditMission') : array();
        /**
         * Lay danh sach mission manager.
         * Xoa cac audit mission ma employee khong quyen nhin thay
         */
        $auditMissionEmployees = $this->AuditMissionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_mission_id' => $data,
                'company_id' => $company_id,
                'type' => 1
            ),
            'fields' => array('employee_id', 'is_backup', 'audit_mission_id'),
            'group' => array('audit_mission_id', 'id')
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
         * File Attachments
         */
        $auditMissionFiles = $this->AuditMissionFile->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_mission_id' => $data
            ),
            'fields' => array('id', 'file_attachment', 'audit_mission_id'),
            'group' => array('audit_mission_id', 'id')
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
        $this->set(compact('auditMissionFiles', 'company_id', 'auditMissions', 'auditMissionEmployees', 'auditSettings', 'employees'));
        $this->layout = '';
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($company_id = null, $id = null) {
        $this->loadModel('AuditSetting');
        $this->loadModel('Employee');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditMissionFile');
        /**
         * Check Permission
         */
         list($isUsing, $seeMenuBusiness) = $this->_checkPermissionMenuAudit();
        if($isUsing == false){
            //$this->Session->setFlash(__('You do not have permission to access in this function.', true), 'error');
            $this->redirect('/index');
        }
        /**
         * Check Permission
         */
        list($editMission, $deleteMission) = $this->_checkPermissionIsUsingAudit($id);
        /**
         * Save Data
         */
        if(!empty($this->data)){
            if(!empty($this->data['audit_mission_manager'])){
                $this->data['audit_mission_manager'] = array_unique($this->data['audit_mission_manager']);
                if(($key = array_search(0, $this->data['audit_mission_manager'])) !== false) {
                    unset( $this->data['audit_mission_manager'][$key]);
                }
            }
            if(!empty($this->data['is_backup'])){
                $this->data['is_backup'] = array_unique($this->data['is_backup']);
                if(($key = array_search(0, $this->data['is_backup'])) !== false) {
                    unset( $this->data['is_backup'][$key]);
                }
            }
            if(!empty($this->data['audit_readable_by'])){
                $this->data['audit_readable_by'] = array_unique($this->data['audit_readable_by']);
                if(($key = array_search(0, $this->data['audit_readable_by'])) !== false) {
                    unset( $this->data['audit_readable_by'][$key]);
                }
            }
            $countMissionManager = isset($this->data['audit_mission_manager']) ? (count($this->data['audit_mission_manager']) - 1) : 0;
            $countMissionManagerBackup = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            if($countMissionManager != $countMissionManagerBackup){
                $this->data = $this->AuditMission->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('AuditMission.id' => $id)
                ));
                $this->Session->setFlash(__('Mission Manager data is not valid. Please input again.', true), 'error');
            } else {
                $this->AuditMission->create();
                if (!empty($this->data['AuditMission']['id'])) {
                    $this->AuditMission->id = $this->data['AuditMission']['id'];
                }
                unset($this->data['AuditMission']['id']);
                /**
                 * Get Status Mission: Terminé, completed, finished, Done, ended, complete, terminée
                 */
                $missionStatusFinish = $this->AuditSetting->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'AuditSetting.status' => array('fin'),
                        'type' => 1
                    ),
                    'fields' => array('id')
                ));
                if(!empty($missionStatusFinish) && $missionStatusFinish['AuditSetting']['id'] && !empty($this->data['AuditMission']['audit_setting_mission_status']) && $missionStatusFinish['AuditSetting']['id'] == $this->data['AuditMission']['audit_setting_mission_status']){
                    $this->data['AuditMission']['mission_closing_date'] = time();
                } else {
                    $this->data['AuditMission']['mission_closing_date'] = '';
                }
                /**
                 * Save Audit Mission
                 */
                $this->data['AuditMission']['company_id'] = $company_id;
                $this->data['AuditMission']['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                if(!empty($this->data['AuditMission']['mission_validation_date'])){
                    $this->data['AuditMission']['mission_validation_date'] = strtotime(str_replace('/', '-', $this->data['AuditMission']['mission_validation_date']));
                }
                if($this->AuditMission->save($this->data['AuditMission'])){
                    $id = $this->AuditMission->id;
                    /**
                     * Save Log
                     */
                    $dataNows = $this->AuditMission->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('AuditMission.id' => $id),
                        'fields' => array('mission_title')
                    ));
                    if(!empty($dataNows) && $dataNows['AuditMission']['mission_title']){
                        $saveLogs = array(
                            'company_id' => $company_id,
                            'model' => 'AuditMission',
                            'model_id' => $id,
                            'name' => $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time()),
                            'description' => $dataNows['AuditMission']['mission_title'],
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'update_by_employee' => $this->employee_info['Employee']['fullname']
                        );
                        $this->LogSystem->saveLogSystem($saveLogs);
                    }
                    /**
                     * Save Mission Manager
                     * Lay danh sach employee mission manager da tao
                     */
                    $listEmployMissionManagers = $this->AuditMissionEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'audit_mission_id' => $id,
                            'type' => 1
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['audit_mission_manager'])){

                        foreach($this->data['audit_mission_manager'] as $value){
                            $is_backup = 0;
                            if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                                $is_backup = 1;
                            }
                            $missionManagers = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => $is_backup,
                                'type' => 1,
                                'audit_mission_id' => $id
                            );
                            $checkDatas = $this->AuditMissionEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $missionManagers,
                                'fields' => array('id')
                            ));
                            $this->AuditMissionEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['AuditMissionEmployeeRefer']['id']){
                                $this->AuditMissionEmployeeRefer->id = $checkDatas['AuditMissionEmployeeRefer']['id'];
                            }
                            if($this->AuditMissionEmployeeRefer->save($missionManagers)){
                                $lastIdEmployMissionManagers = $this->AuditMissionEmployeeRefer->id;
                                unset($listEmployMissionManagers[$lastIdEmployMissionManagers]);
                            }
                        }
                    }
                    if(!empty($listEmployMissionManagers)){
                        $this->AuditMissionEmployeeRefer->deleteAll(array('AuditMissionEmployeeRefer.id' => $listEmployMissionManagers), false);
                    }
                    /**
                     * Save Readable by
                     * Lay danh sach employee mission manager da tao
                     */
                    $listEmployReadables = $this->AuditMissionEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'audit_mission_id' => $id,
                            'type' => 0
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['audit_readable_by'])){
                        foreach($this->data['audit_readable_by'] as $value){
                            $readableBy = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => 0,
                                'type' => 0,
                                'audit_mission_id' => $id
                            );
                            $checkDatas = $this->AuditMissionEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $readableBy,
                                'fields' => array('id')
                            ));
                            $this->AuditMissionEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['AuditMissionEmployeeRefer']['id']){
                                $this->AuditMissionEmployeeRefer->id = $checkDatas['AuditMissionEmployeeRefer']['id'];
                            }
                            if($this->AuditMissionEmployeeRefer->save($readableBy)){
                                $lastIdEmployreadableBy = $this->AuditMissionEmployeeRefer->id;
                                unset($listEmployReadables[$lastIdEmployreadableBy]);
                            }
                        }
                    }
                    if(!empty($listEmployReadables)){
                        $this->AuditMissionEmployeeRefer->deleteAll(array('AuditMissionEmployeeRefer.id' => $listEmployReadables), false);
                    }
                    $this->Session->setFlash(__('Saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('Not Saved.', true), 'error');
                }
                $this->redirect(array('action' => 'update', $company_id, $id));
            }
        } else {
            $this->data = $this->AuditMission->find('first', array(
                'recursive' => -1,
                'conditions' => array('AuditMission.id' => $id)
            ));
            if(empty($this->data) && !empty($id)){
                $this->redirect(array('action' => 'update', $company_id));
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
         * Employee Refer
         */
        $auditMissionEmployeeRefers = $this->AuditMissionEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_mission_id' => $id,
                'company_id' => $company_id,
            ),
            'fields' => array('employee_id', 'is_backup', 'type'),
            'group' => array('type', 'id')
        ));
        /**
         * File Attachments
         */
        $auditMissionFiles = $this->AuditMissionFile->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'audit_mission_id' => $id
            ),
            'fields' => array('id', 'audit_mission_id', 'file_attachment', 'size'),
        ));
        $auditMissionFiles = !empty($auditMissionFiles) ? Set::classicExtract($auditMissionFiles, '{n}.AuditMissionFile') : array();
        $this->set(compact('auditSettings', 'employees', 'company_id', 'id', 'auditMissionEmployeeRefers', 'auditMissionFiles', 'editMission'));
    }

    /**
     * Upload
     */
    public function upload($company_id = null, $id = null) {
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
                $this->loadModel('AuditMissionFile');
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->AuditMissionFile->create();
                if ($this->AuditMissionFile->save(array(
                    'audit_mission_id' => $id,
                    'file_attachment' => $attachment,
                    'size' => $size,
                    'type' => $type))) {
                    $lastId = $this->AuditMissionFile->id;
                    $result = $this->AuditMissionFile->find('first', array('recursive' => -1, 'conditions' => array('AuditMissionFile.id' => $lastId)));
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
                $_SESSION['file_multiupload_redirect'] = '/audit_missions/update/' . $company_id . '/' . $id;
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

    public function attachment($company_id = null, $audit_mission_id = null, $id = null, $type = null) {
        $this->loadModel('AuditMissionFile');
        $last = $this->AuditMissionFile->find('first', array(
            'recursive' => -1,
            'fields' => array('audit_mission_id', 'file_attachment'),
            'conditions' => array(
                'AuditMissionFile.id' => $id,
                'AuditMissionFile.audit_mission_id' => $audit_mission_id
            )
        ));
        $error = true;
        if ($last && $last['AuditMissionFile']['audit_mission_id']) {
            $attachment = $last['AuditMissionFile']['file_attachment'];
            if($this->MultiFileUpload->otherServer == true && $type == 'download'){
                $this->view = 'Media';
                $path = trim($this->_getPath($company_id, $last['AuditMissionFile']['audit_mission_id']));
                $this->MultiFileUpload->downloadFileToServerOther($path, $attachment);
            } else {
                $path = trim($this->_getPath($company_id, $last['AuditMissionFile']['audit_mission_id'])
                        . $last['AuditMissionFile']['file_attachment']);
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
                $this->AuditMissionFile->delete($id);
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($company_id, $last['AuditMissionFile']['audit_mission_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/audit_missions/update/' . $company_id . '/' . $audit_mission_id);
                }
                $this->redirect(array('action' => 'update', $company_id, $audit_mission_id));
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'update', $company_id, $audit_mission_id));
        }
    }

    protected function _getPath($company_id = null, $audit_id = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'audit' . DS . 'mision' . DS;
        $path .= $company['Company']['dir'] . DS . $audit_id . DS;
        return $path;
    }

    protected function _getPathRecom($company_id = null, $audit_id = null) {
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
    function delete($company_id = null, $id = null) {
        $this->loadModel('AuditMissionFile');
        $this->loadModel('AuditMissionEmployeeRefer');
        $this->loadModel('AuditRecom');
        $this->loadModel('AuditRecomFile');
        $this->loadModel('AuditRecomEmployeeRefer');
        /**
         * Check Permission
         */
        list($editMission, $deleteMission) = $this->_checkPermissionIsUsingAudit($id);
        if($deleteMission == false){
            $this->Session->setFlash(__('You have not permission to access this function delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Recommandation', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->_getCompany($company_id) && $this->AuditMission->delete($id)) {
            /**
             * Xoa cac file attachment cua mission
             */
            $files = $this->AuditMissionFile->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_mission_id' => $id
                ),
                'fields' => array('id', 'file_attachment')
            ));
            if(!empty($files)){
                foreach($files as $idFile => $nameFile){
                    $path = trim($this->_getPath($company_id, $id)
                        . $nameFile);
                    unlink($path);
                }
            }
            $this->AuditMissionFile->deleteAll(array('audit_mission_id' => $id), false);
            /**
             * Xoa Employee Refers Recom
             */
            $this->AuditMissionEmployeeRefer->deleteAll(array('audit_mission_id' => $id), false);
            /**
             * Xoa cac Recommendation Of Mision
             */
            $listIdRecomOfMisions = $this->AuditRecom->find('list', array(
                'recursive' => -1,
                'conditions' => array('audit_mission_id' => $id),
                'fields' => array('id', 'id')
            ));
            $this->AuditRecom->deleteAll(array('audit_mission_id' => $id), false);
            /**
             * Xoa cac file attachment cua Recom
             */
            $fileRecoms = $this->AuditRecomFile->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'audit_recom_id' => $listIdRecomOfMisions
                ),
                'fields' => array('id', 'audit_recom_id', 'file_attachment')
            ));
            if(!empty($fileRecoms)){
                foreach($fileRecoms as $fileRecom){
                    $path = trim($this->_getPathRecom($company_id, $fileRecom['AuditRecomFile']['audit_recom_id'])
                        . $fileRecom['AuditRecomFile']['file_attachment']);
                    @unlink($path);
                }
            }
            $this->AuditRecomFile->deleteAll(array('audit_recom_id' => $listIdRecomOfMisions), false);
            /**
             * Xoa Employee Refers Recom
             */
            $this->AuditRecomEmployeeRefer->deleteAll(array('audit_recom_id' => $listIdRecomOfMisions), false);
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
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingAudit($audit_mission_id = null){
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
         * Danh sach cac employee duoc quyen sua doi Mission Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         * - Mission Manager in Mission
         */
        //$authorEditMissions = array_unique(array_merge($adminOfCompanies, $adminAudits, $missionManagers));
        $authorEditMissions = array_unique(array_merge($adminAudits, $missionManagers));
        /**
         * Danh sach cac employee duoc quyen delete Mission Manager o Recom.
         * Danh sach gom:
         * - Admin Company
         * - Admin Audit
         */
        //$authorDeleteMissions = array_unique(array_merge($adminOfCompanies, $adminAudits));
        $authorDeleteMissions = array_unique(array_merge($adminAudits));
        /**
         * Mac dinh gia tri tat cac employee chi dc read/view.
         * Neu kiem tra employee_id co ton tai 1 trong 2 list tren thi duoc sua doi tuong ung danh sach tren
         */
        $editMission = $deleteMission = false;
        if(in_array($employeeLogin, $authorEditMissions)){
            $editMission = true;
        }
        if(in_array($employeeLogin, $adminAudits)){
            $deleteMission = true;
        }
        return array($editMission, $deleteMission);
    }
    // export Excel
    public function export_excel(){
    	if( !empty($this->data) ){
    		$this->SlickExporter->init();
    		$data = json_decode($this->data['data'], true);
    		$this->SlickExporter
    			->setT('Audit Missions')	//auto translate
    			->save($data, 'audit_mission_{date}.xls');
    	}
    	die;
    }
}
?>
