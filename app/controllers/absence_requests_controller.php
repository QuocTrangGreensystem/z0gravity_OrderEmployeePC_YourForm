<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class AbsenceRequestsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AbsenceRequests';
    var $components = array('MultiFileUpload', 'PImage', 'ZEmail');

    /**
    * Before executing controller actions
    *
    * @return void
    * @access public
    */
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->allowedFiles = "doc,docx,xlsx,xls,pdf,xlsm,ppt,pptx,pptm,csv,txt";
        $this->set('allowedFiles', $this->allowedFiles);
    }

    protected function _getProfitEmployee($getDataByPath = false) {
        $isManage = !empty($this->params['url']['id']) && !empty($this->params['url']['profit']);

        if (!($employeeName = $this->_getEmpoyee()) ||
                ($isManage && (!($params = $this->_getProfits($this->params['url']['profit'], $getDataByPath)) || !isset($params[2][$this->params['url']['id']])))) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        if ($isManage) {
            $employee = $this->AbsenceRequest->Employee->find('first', array(
                'recursive' => -1, 'fields' => array('id', 'first_name', 'last_name'), 'conditions' => array(
                    'id' => $this->params['url']['id']
                    )));
            $employeeName = array_merge($employeeName, $employee['Employee']);
            $employeeName['profit_center_id'] = $this->params['url']['profit'];
        }
        return array($isManage, $employeeName);
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($typeSelect = null) {
		$display_absence = isset($this->companyConfigs['display_absence_tab']) ? $this->companyConfigs['display_absence_tab'] : 1;
		if(!$display_absence){
			$this->redirect('/index');
		}
        $this->loadModel('AbsenceRequestConfirm');
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('AbsenceComment');
        $this->loadModel('AbsenceAttachment');
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list($isManage, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        $dayHasValidations = array();
        $year = '';
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
            $year = date('Y', $_start);
            $requestConfirm = Set::classicExtract($this->ActivityRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('status'),
                                'conditions' => array('status' => array(0, 2), 'employee_id' => $employeeName['id'], 'start' => $_start, 'end' => $_end))), 'ActivityRequestConfirm.status');
            if ($requestConfirm !== false) {
                $startDate = $_start;
                while($startDate <= $_end){
                    $dayHasValidations[$startDate] = $startDate;
                    $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate)+1, date("Y", $startDate));
                }
                //$this->Session->setFlash(sprintf(__('You can not request a absence because the your time sheet is "%s".', true)
                               // , $requestConfirm == 0 ? __('Requested', true) : __('Validated', true)), 'warning');
            }
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(false, true, false);
			
             $year = date('Y', $_start);
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => $employeeName['id'],
                    'start' => array_keys($listWeekOfMonths)
                )
            ));
            foreach($requestConfirms as $requestConfirm){
                $dx = $requestConfirm['ActivityRequestConfirm'];
                if($dx['status'] !== false){
                    while($dx['start'] <= $dx['end']){
                        $dayHasValidations[$dx['start']] = $dx['start'];
                        $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                    }
                }
            }
        }else{
            list($_start, $_end) = $this->_parseParamsMonth(true, true, true);
            $year = date('Y', $_start);
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => $employeeName['id'],
                    'start' => array_keys($listWeekOfMonths)
                )
            ));
            foreach($requestConfirms as $requestConfirm){
                $dx = $requestConfirm['ActivityRequestConfirm'];
                if($dx['status'] !== false){
                    while($dx['start'] <= $dx['end']){
                        $dayHasValidations[$dx['start']] = $dx['start'];
                        $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                    }
                }
            }
        }
        $avg = intval(($_start + $_end) / 2);
        /**
         * Cac ngay lam viec trong tuan
         */
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->get2($employeeName['company_id'], $_start, $_end);
        foreach($holidays as $time => $h){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }
        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        list($multiWeeks, $listWorkingDays) = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays);
        /**
         * Kiem tra xem tuan/thang/nam nay da send mail chua
         */
        // $requestMessage = $this->AbsenceRequestConfirm->find('first', array('recursive' => -1, 'fields' => array('id', 'count'),
        //     'conditions' => array('employee_id' => $employeeName['id'], 'start' => $_start, 'end' => $_end)));
        /**
         * Lay absence request cua tuan/thang/nam: request, reject, validation.
         */
        $requests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id'])));
        $_requests = array();
        foreach($requests as $_request) {
            $_request = array_shift($_request);
            foreach (array('am', 'pm') as $type) {
                if ($_request['absence_' . $type] && $_request['absence_' . $type] != '-1') {
                    if (!isset($_requests[$_request['absence_' . $type]])) {
                        $_requests[$_request['absence_' . $type]] = 0;
                    }
                    $_requests[$_request['absence_' . $type]] += 0.5;
                }
            }
        }
        $_comments = $this->AbsenceComment->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id'])));
        $comments = $employees = array();
        foreach ($_comments as $comment) {
            $comment = array_shift($comment);
            $comments[$comment['employee_id']][$comment['date']][$comment['time'] ? 'pm' : 'am'][$comment['id']] = array(
                'user_id' => $comment['user_id'],
                'created' => date('d-m-Y H:i', $comment['created']),
                'text' => $comment['text']);
            $employees[$comment['user_id']] = $comment['user_id'];
        }
        $employees = Set::combine($this->AbsenceRequest->Employee->find('all', array(
                            'fields' => array('id', 'first_name', 'last_name'),
                            'recursive' => -1, 'conditions' => array('id' => $employees)))
                        , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
        $requests = Set::combine($requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        $startDate = date('Y', $_start);
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id'], $employeeName['id'], $startDate);
        $absences['-1'] = array(
            'id' => '-1',
            'print' => __('Provisional day off', true)
        );
        $listBeginOfPeriods = array();
        if(!empty($absences)){
            foreach($absences as $_key => $absence){
                if(!empty($absence['begin'])){
                    $year = !empty($year) ? $year : date('Y', time());
                    $_begin = explode('-', $absence['begin']);
                    $listBeginOfPeriods[$_key] = mktime(0, 0, 0, $_begin[0], $_begin[1], $year);
                }
                if(!empty($_requests)){
                    foreach($_requests as $_id => $_request){
                        if($_key == $_id){
                            $absences[$_key]['request'] = $_request;
                        } else {
                            $absences[$_key]['request'] = 0;
                        }
                    }
                }
            }
        }
        /**
         * Lay confirm cua absence attachments in admin
         */
        $absenceAttachments = $this->AbsenceAttachment->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeName['company_id'])
        ));
        $absenceAttachments = !empty($absenceAttachments) ? array_shift($absenceAttachments) : array();
        /**
         * HuuPc: Se kiem tra vao ngay tiep theo
         */
        if (!empty($this->data)) {
            $hasRequested = $this->AbsenceRequest->find('count', array('recursive' => -1, 'conditions' => array(
                    'date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => $employeeName['id'],
                    'or' => array(
                        array('NOT' => array('response_am' => 'validated', 'absence_am' => array(0, -1))),
                        array('NOT' => array('response_pm' => 'validated', 'absence_pm' => array(0, -1)))
                ))));
            if (!$hasRequested) {
                $this->Session->setFlash(__('You have not any absence request.', true), 'error');
            }
            // elseif ($requestMessage) {
            //     $this->Session->setFlash(__('Sorry but due to security reasons you are limited to the number of send request message.', true), 'error');
            // }
            else {
                $this->AbsenceRequestConfirm->create();
                if ($this->AbsenceRequestConfirm->save(array('employee_id' => $employeeName['id'],
                            'start' => $_start, 'end' => $_end, 'company_id' => $employeeName['company_id'], 'count' => 1))) {


                    $profit = Set::classicExtract(ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array('recursive' => -1,
                                        'conditions' => array('employee_id' => $employeeName['id']))), 'ProjectEmployeeProfitFunctionRefer.profit_center_id');
                    // Get mangager PC
					$list_manager_pc = $this->get_manager_pc($profit);
			
					if(empty($list_manager_pc)){
						// Get manager parent PC
						$has_manager = 0;
						$tmp_profit = $profit;
						while( $has_manager == 0 ){
							$parent_profit = $this->ProfitCenter->find('list', array(
								'recursive' => -1, 
								'conditions' => array(
									'id' => $tmp_profit,
									'parent_id IS NOT NULL'
								),
								'fields' => array('id', 'parent_id'),
							));
							$list_manager_pc = $this->get_manager_pc($parent_profit);
							
							if(empty($list_manager_pc) && !empty($parent_profit)){
								$tmp_profit = $parent_profit;
							}else{
								$has_manager = 1;
							}
						}
					}
					
					if (empty($list_manager_pc)) {
						// Get admin company
						foreach (array(6, 2) as $role) {
							$admin_company = ClassRegistry::init('CompanyEmployeeReference')->find('list', array(
								'recursive' => -1, 'fields' => array('employee_id', 'employee_id'),
								'conditions' => array('role_id' => $role, 'CompanyEmployeeReference.company_id' => $employeeName['company_id'])));
							
							if ($admin_company) {
								break;
							}
						}
						$list_manager_pc = $admin_company;
					}
					
					$list_managers = array_values($this->AbsenceRequest->Employee->find('list', array(
						'fields' => array('email', 'email'), 
						'recursive' => -1, 
						'conditions' => array('email_receive' => true, 'id' => $list_manager_pc)
					)));
					$result = false;
                    if ($list_managers) {
                        $this->set(compact('employeeName', 'profit', 'typeSelect'));
						$to = array_shift($list_managers);
						/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug) */
						$result = $this->_z0GSendEmail($to, null, $list_managers, __('[PMS - Absence] Absences has to be validated.', true), 'absence_request');
						// debug( $result); exit;
                    }

                    if ($result) {
                        $this->Session->setFlash(__('The message has been sent.', true), 'success');
                    } else {
                        $this->Session->setFlash(__('The request email message could not be sent to your manager.', true), 'error');
                    }
                } else {
                    $this->Session->setFlash(__('Could not sent message, please try again.', true), 'error');
                }
            }
            $this->redirect(array('action' => 'index', '?' => array(
                    'week' => date('W', $avg), 'year' => date('Y', $avg))));
        }
        $this->set(compact('absenceAttachments', 'listBeginOfPeriods', 'listWorkingDays', 'typeSelect', 'requestMessage', 'requests', 'employeeName', 'constraint', 'absences', 'workdays', 'comments', 'employees', 'holidays', 'dayHasValidations', 'isManage', 'getDataByPath'));
    }
	private function get_manager_pc($profit){
		$this->loadModels('ProfitCenter', 'ProfitCenterManagerBackup');
		$manager_pc = $this->ProfitCenter->find('list', array(
			'recursive' => -1, 
			'conditions' => array('id' => $profit, 'manager_id IS NOT NULL'),
			'fields' => array('manager_id'),
		));

		$manager_pc_bk = $this->ProfitCenterManagerBackup->find('list', array(
		   'recursive' => -1, 
		   'conditions' => array(
				'profit_center_id' => $profit,
		   ),
		   'fields' => array('employee_id'),
		));
		
		$list_manager_pc = array_merge($manager_pc, $manager_pc_bk);
		
		return $list_manager_pc;
	}
    public function requestApi($typeSelect = null){
        list($isManage, $employeeName) = $this->_getProfitEmployee();
        list($_start, $_end) = $this->_parseParams2();

        $this->loadModel('AbsenceRequestConfirm');
        $this->loadModel('AbsenceComment');
        // end edit 03/05/2013
        $startDate = date('Y', $_start);
        $empID = isset($_GET['id'])?$_GET['id']:$employeeName['id'];
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id'], $empID, $startDate);
        // $absences['-1'] = array(
        //     'id' => '-1',
        //     'print' => __('Provisional day off', true)
        // );
        // modify ngay 9/1/2014
        $this->loadModel('AbsenceHistory');
        $this->loadModel('EmployeeAbsence');
        $yearCurrent = date('Y', $_start);
        $lastYear = date('Y', $_start)-1;
        $absenceHistories = $this->AbsenceHistory->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'year' => array($lastYear, $yearCurrent)
            ),
            'fields' => array('absence_id', 'total', 'begin', 'year')
        ));
        $absenceHistories = !empty($absenceHistories) ? Set::combine($absenceHistories, '{n}.AbsenceHistory.absence_id', '{n}.AbsenceHistory', '{n}.AbsenceHistory.year') : array();
        $employeeAbsences = $this->EmployeeAbsence->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $empID,
                'year' => array($lastYear, $yearCurrent)
            ),
            'fields' => array('total', 'absence_id', 'year')
        ));
        $employeeAbsences = !empty($employeeAbsences) ? Set::combine($employeeAbsences, '{n}.EmployeeAbsence.absence_id', '{n}.EmployeeAbsence', '{n}.EmployeeAbsence.year') : array();
        $setDates = '';
        if(!empty($this->params['url']['dateCurrent'])){
            $getDates = array_unique($this->params['url']['dateCurrent']);
            $setDates = max($getDates);
        }
        $yearSystem = date('Y', time());
        if(!empty($absences)){
            foreach($absences as $id => $absence){
                if(!empty($absence['begin'])){
                    $_begin = explode('-', $absence['begin']);
                    $begin = !empty($_begin) ? strtotime($_begin[1].'-'.$_begin[0].'-'.$yearCurrent) : '';
                    $absences[$id]['total'] = '';
                    if($begin > $setDates && $yearSystem <= $yearCurrent){
                        $getAbsenEmploys = !empty($employeeAbsences[$lastYear][$id]['total']) || (!empty($employeeAbsences[$lastYear][$id]) && $employeeAbsences[$lastYear][$id]['total'] == 0) ? $employeeAbsences[$lastYear][$id]['total'] : '';
                        $getAbsences = !empty($absenceHistories[$lastYear][$id]['total']) || (!empty($absenceHistories[$lastYear][$id]) && $absenceHistories[$lastYear][$id]['total'] == 0) ? $absenceHistories[$lastYear][$id]['total'] : '';
                        $absences[$id]['total'] = ($getAbsenEmploys != '') ? $getAbsenEmploys : $getAbsences;
                        $startFilter = strtotime($lastYear . '-' . $absence['begin']);
                        //$startFilter = !empty($absenceHistories[$lastYear][$id]['begin']) ? strtotime($absenceHistories[$lastYear][$id]['begin']) : '';
                        $endFilter = !empty($startFilter) ? mktime(0, 0, 0, date("m", $startFilter), date("d", $startFilter), date("Y", $startFilter)+1) : '';
                        $endFilter = strtotime("-1 day", $endFilter);
                        $requests = $this->AbsenceRequest->find("all", array(
                            'recursive' => -1,
                            "conditions" => array(
                                'date BETWEEN ? AND ?' => array($startFilter, $endFilter),
                                'OR' => array(
                                    'absence_am' => $id,
                                    'absence_pm' => $id
                                ),
                                'employee_id' => $empID,
                                //'NOT' => array('response_am' => 'rejetion', 'response_pm' => 'rejetion')
                            )
                        ));
                        $_requests = array();
                        if(!empty($requests)){
                            foreach($requests as $_request) {
                                $_request = array_shift($_request);
                                foreach (array('am', 'pm') as $type) {
                                    if (!isset($_requests[$_request['absence_' . $type]])) {
                                        $_requests[$_request['absence_' . $type]] = 0;
                                    }
                                    $_requests[$_request['absence_' . $type]] += 0.5;
                                }
                            }
                        }
                        $absences[$id]['request'] = !empty($_requests[$id]) ? $_requests[$id] : '';

                    } else {
                        $getAbsenEmploys = !empty($employeeAbsences[$yearCurrent][$id]['total']) || (!empty($employeeAbsences[$yearCurrent][$id]) && $employeeAbsences[$yearCurrent][$id]['total'] == 0) ? $employeeAbsences[$yearCurrent][$id]['total'] : '';
                        $getAbsences = !empty($absenceHistories[$yearCurrent][$id]['total']) || (!empty($absenceHistories[$yearCurrent][$id]) && $absenceHistories[$yearCurrent][$id]['total'] == 0) ? $absenceHistories[$yearCurrent][$id]['total'] : '';
                        $absences[$id]['total'] = ($getAbsenEmploys != '') ? $getAbsenEmploys : $getAbsences;
                        $startFilter = strtotime($yearCurrent . '-' . $absence['begin']);
                        //$startFilter = !empty($absenceHistories[$yearCurrent][$id]['begin']) ? strtotime($absenceHistories[$yearCurrent][$id]['begin']) : '';
                        $endFilter = !empty($startFilter) ? mktime(0, 0, 0, date("m", $startFilter), date("d", $startFilter), date("Y", $startFilter)+1) : '';
                        $requests = $this->AbsenceRequest->find("all", array(
                            'recursive' => -1,
                            "conditions" => array(
                                'date BETWEEN ? AND ?' => array($startFilter, $endFilter),
                                'OR' => array(
                                    'absence_am' => $id,
                                    'absence_pm' => $id
                                ),
                                'employee_id' => $empID,
                                //'NOT' => array('response_am' => 'rejetion', 'response_pm' => 'rejetion')
                            )
                        ));
                        $_requests = array();
                        if(!empty($requests)){
                            foreach($requests as $_request) {
                                $_request = array_shift($_request);
                                foreach (array('am', 'pm') as $type) {
                                    if (!isset($_requests[$_request['absence_' . $type]])) {
                                        $_requests[$_request['absence_' . $type]] = 0;
                                    }
                                    $_requests[$_request['absence_' . $type]] += 0.5;
                                }
                            }
                        }
                        $absences[$id]['request'] = !empty($_requests[$id]) ? $_requests[$id] : '';
                    }
                } else {
                    $requests = $this->AbsenceRequest->find("all", array(
                        'recursive' => -1,
                        "conditions" => array(
                            'date BETWEEN ? AND ?' => array($_start, $_end),
                            'OR' => array(
                                'absence_am' => $id,
                                'absence_pm' => $id
                            ),
                            'employee_id' => $empID,
                            //'NOT' => array('response_am' => 'rejetion', 'response_pm' => 'rejetion')
                        )
                    ));
                    $_requests = array();
                    if(!empty($requests)){
                        foreach($requests as $_request) {
                            $_request = array_shift($_request);
                            foreach (array('am', 'pm') as $type) {
                                if (!isset($_requests[$_request['absence_' . $type]])) {
                                    $_requests[$_request['absence_' . $type]] = 0;
                                }
                                $_requests[$_request['absence_' . $type]] += 0.5;
                            }
                        }
                    }
                    $absences[$id]['request'] = !empty($_requests[$id]) ? $_requests[$id] : '';
                }
            }
        }
        //extract
        $result = array_values($absences);
        $this->set(compact('result'));
    }

    /**
     * Upload document for absence request
     */
    public function update_document($company_id = null, $employee_id = null, $absence_id = null, $datas = null){
        $this->layout = false;
        $this->loadModel('AbsenceRequestFile');
        if(!empty($_FILES)){
            $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
            $path = FILES . 'absence_request_files' . DS;
            $path .= $company['Company']['dir'] . DS;
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            /**
             * Xu ly data datetime
             */
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment) && !empty($datas)) {
                $saves = array();
                $datas = explode(',', $datas);
                $getDatas = array();
                foreach($datas as $data){
                    $data = explode('-', $data);
                    $getDatas[$data[0]] = $data[0];
                    $saves[] = array(
                        'employee_id' => $employee_id,
                        'date' => $data[0],
                        'request_am' => ($data[1] === 'am') ? 'am' : '',
                        'request_pm' => ($data[1] === 'pm') ? 'pm' : '',
                        'file_attachment' => $attachment['attachment']['attachment'],
                        'size' => !empty($_FILES['FileField']['size']['attachment']) ? $_FILES['FileField']['size']['attachment'] : '',
                        'type' => !empty($_FILES['FileField']['type']['attachment']) ? $_FILES['FileField']['type']['attachment'] : '',
                        'absence_id' => $absence_id
                    );
                }
                if(!empty($saves)){
                    $this->AbsenceRequestFile->create();
                    if($this->AbsenceRequestFile->saveAll($saves)){
                        $this->loadModel('AbsenceAttachment');
                        list($isManage, $employeeName) = $this->_getProfitEmployee();
                        /**
                         * Lay confirm cua absence attachments in admin
                         */
                        $absenceAttachments = $this->AbsenceAttachment->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('company_id' => $employeeName['company_id'])
                        ));
                        $absenceAttachments = !empty($absenceAttachments) ? array_shift($absenceAttachments) : array();
                        $_start = !empty($getDatas) ? min($getDatas) : 0;
                        $_end = !empty($getDatas) ? max($getDatas) : 0;
                        $this->set(compact('absenceAttachments', '_start', '_end', 'employeeName'));
                        //modify by QN on 20150228
                        $this->loadModel('Absence');
                        $this->set('absenceInfo', $this->Absence->find('first', array(
                            'conditions' => array(
                                'id' => $absence_id
                            ),
                            'recursive' => -1
                        )));

                        $msg = !empty($absenceAttachments['message']) ? $absenceAttachments['message'] : '';
                        $to = !empty($absenceAttachments['hr_email']) ? $absenceAttachments['hr_email'] : '';
                        $this->_sendEmail($to, __('[AZURE - Absence] - Document ' . $msg . '.', true), 'absence_request_file', false, null, $path . $attachment['attachment']['attachment']);
                    }
                }
            }
        }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function review() {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to request a absence.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        list($_start, $_end) = $this->_parseParams2();
        $startDate = date('Y', $_start);
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id'], $employeeName['id'], $startDate);

        $this->loadModel('EmployeeAbsence');
        $this->loadModel('AbsenceHistory');
        $currentYear = date('Y', $_start);
        $lastYear = date('Y', $_start)-1;
        $absenceHistories = $this->AbsenceHistory->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'year' => array($lastYear, $currentYear)
            ),
            'fields' => array('absence_id', 'total', 'begin', 'year')
        ));
        $absenceHistories = !empty($absenceHistories) ? Set::combine($absenceHistories, '{n}.AbsenceHistory.absence_id', '{n}.AbsenceHistory', '{n}.AbsenceHistory.year') : array();
        $groupDates = !empty($absenceHistories) ? Set::classicExtract($absenceHistories, '{n}.{n}.begin') : array();
        $_employeeAbsences = $this->EmployeeAbsence->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employeeName['id'],
                'year' => array($lastYear, $currentYear)
            ),
            'fields' => array('total', 'absence_id', 'year')
        ));
        $employeeAbsences = array();
        if(!empty($_employeeAbsences)){
            foreach($_employeeAbsences as $_employeeAbsence){
                $dx = $_employeeAbsence['EmployeeAbsence'];
                if(!isset($employeeAbsences[$dx['year']][$dx['absence_id']])){
                    $employeeAbsences[$dx['year']][$dx['absence_id']] = 0;
                }
                $employeeAbsences[$dx['year']][$dx['absence_id']] = $dx['total'];
            }
        }
        $currentDate = strtotime(date('d', time()).'-'.date('m', time()).'-'.$currentYear);
        // gia lap ngay hien tai o day
        $yearSystem = date('Y', time());
        //$currentDate = strtotime(date('d', time()).'-'.'06'.'-'.$currentYear);
        $dateFollowAbsens = $allDateOfAbsences = array();
        if(!empty($absences)){
            foreach($absences as $id => $absence){
                if(!isset($allDateOfAbsences['start'])){
                    $allDateOfAbsences['start'] = 0;
                }
                if(!isset($allDateOfAbsences['end'])){
                    $allDateOfAbsences['end'] = 0;
                }
                $absence['total'] = '';
                if(!empty($absence['begin'])){
                    $_begin = explode('-', $absence['begin']);
                    $begin = !empty($_begin) ? strtotime($_begin[1].'-'.$_begin[0].'-'.$currentYear) : '';
                    if($begin > $currentDate && $yearSystem <= $currentYear){
                        $getAbsences = !empty($absenceHistories[$lastYear][$id]['total']) ? $absenceHistories[$lastYear][$id]['total'] : 0;
                        $getAbsenEmploys = !empty($employeeAbsences[$lastYear][$id]) ? $employeeAbsences[$lastYear][$id] : 0;
                        $absences[$id]['total'] = ($getAbsenEmploys>=0) ? $getAbsenEmploys : $getAbsences;
                        $getStartBegin = !empty($begin) ? mktime(0, 0, 0, date("m", $begin), date("d", $begin), date("Y", $begin)-1) : '';
                        $getEndBegin = $begin;
                        $dateFollowAbsens[$id] = array(
                            'start' => $getStartBegin,
                            'end' => $getEndBegin
                        );
                        if($allDateOfAbsences['start'] == 0){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($getStartBegin < $allDateOfAbsences['start']){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($allDateOfAbsences['end'] < $getEndBegin){
                            $allDateOfAbsences['end'] = $getEndBegin;
                        }
                    } else {
                        $getAbsences = !empty($absenceHistories[$currentYear][$id]['total']) ? $absenceHistories[$currentYear][$id]['total'] : 0;
                        $getAbsenEmploys = !empty($employeeAbsences[$currentYear][$id]) ? $employeeAbsences[$currentYear][$id] : 0;
                        $absences[$id]['total'] = ($getAbsenEmploys>=0) ? $getAbsenEmploys : $getAbsences;
                        $getStartBegin = $begin;
                        $getEndBegin = !empty($begin) ? mktime(0, 0, 0, date("m", $begin), date("d", $begin), date("Y", $begin)+1) : '';
                        $dateFollowAbsens[$id] = array(
                            'start' => $getStartBegin,
                            'end' => $getEndBegin
                        );
                        if($allDateOfAbsences['start'] == 0){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($getStartBegin < $allDateOfAbsences['start']){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($allDateOfAbsences['end'] < $getEndBegin){
                            $allDateOfAbsences['end'] = $getEndBegin;
                        }
                    }
                } else {
                    $dateFollowAbsens[$id] = array(
                        'start' => $_start,
                        'end' => $_end
                    );
                    if($allDateOfAbsences['start'] == 0){
                        $allDateOfAbsences['start'] = $_start;
                    }
                    if($_start < $allDateOfAbsences['start']){
                        $allDateOfAbsences['start'] = $_start;
                    }
                    if($allDateOfAbsences['end'] < $_end){
                        $allDateOfAbsences['end'] = $_end;
                    }
                }
            }
        }
        $checkDates = array();
        if(!empty($groupDates)){
            foreach($groupDates as $groupDate){
                foreach($groupDate as $values){
                    $checkDates[] = strtotime($values);
                }
            }
        }
        $minDate = !empty($checkDates) ? min($checkDates) : 0;
        $maxDate = !empty($checkDates) ? max($checkDates) : 0;
        $startFilter = ($minDate != 0 && $_start > $minDate) ? $minDate : $_start;
        $endFilter = ($_end < $maxDate) ? $maxDate : $_end;
        $startFilter = ($allDateOfAbsences['start'] != 0) && $startFilter > $allDateOfAbsences['start'] ? $allDateOfAbsences['start'] : $startFilter;
        $endFilter = ($endFilter < $allDateOfAbsences['end']) ? $allDateOfAbsences['end'] : $endFilter;
        $requests = array();
        $waitings = array();
        //new code
        $fields = array('employee_id');

        foreach($dateFollowAbsens as $absenceId => $date){
            $date['end'] = strtotime('-1 day', $date['end']);
            $day = sprintf('date BETWEEN %s AND %s', $date['start'], $date['end']);
            $this->AbsenceRequest->virtualFields['v_' . $absenceId] = "SUM(IF(absence_am = $absenceId AND response_am = 'validated' AND $day, 0.5, 0) + IF(absence_pm = $absenceId AND response_pm = 'validated' AND $day, 0.5, 0))";
            $this->AbsenceRequest->virtualFields['w_' . $absenceId] = "SUM(IF(absence_am = $absenceId AND response_am = 'waiting' AND $day, 0.5, 0) + IF(absence_pm = $absenceId AND response_pm = 'waiting' AND $day, 0.5, 0))";
            array_push($fields, 'v_' . $absenceId, 'w_' . $absenceId);
        }

        $_requests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            'fields' => $fields,
            'group' => array('employee_id'),
            "conditions" => array('date BETWEEN ? AND ?' => array($startFilter, $endFilter), 'employee_id' => $employeeName['id'])));

        foreach ($_requests as $request) {
            $r = $request['AbsenceRequest'];
            $resource = $r['employee_id'];

            unset($r['employee_id']);
            foreach($r as $k => $value){
                $key = explode('_', $k);
                if( $key[0] == 'v' ){
                    $requests[$key[1]] = $value;
                } else {
                    $waitings[$key[1]] = $value;
                }
            }
        }
        //$_requests = $this->AbsenceRequest->find("all", array(
//            'recursive' => -1,
//            "conditions" => array('date BETWEEN ? AND ?' => array($startFilter, $endFilter), 'employee_id' => $employeeName['id'])));
//
//        $requests = array();
//        $waitings = array();
//        foreach ($_requests as $request) {
//            $request = array_shift($request);
//            foreach (array('am', 'pm') as $type) {
//                if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'validated') {
//                    $_st = !empty($dateFollowAbsens[$request['absence_' . $type]]['start']) ? $dateFollowAbsens[$request['absence_' . $type]]['start'] : 0;
//                    $_en = !empty($dateFollowAbsens[$request['absence_' . $type]]['end']) ? $dateFollowAbsens[$request['absence_' . $type]]['end'] : 0;
//                    if(!empty($_st) && !empty($_en)){
//                        if($_st <= $request['date'] && $request['date'] <= $_en){
//                            if (!isset($requests[$request['absence_' . $type]])) {
//                                $requests[$request['absence_' . $type]] = 0;
//                            }
//                            $requests[$request['absence_' . $type]] += 0.5;
//                        }
//                    }
//                }
//                // lay thong tin xin nghi dang doi validate
//                if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'waiting') {
//                    $_st = !empty($dateFollowAbsens[$request['absence_' . $type]]['start']) ? $dateFollowAbsens[$request['absence_' . $type]]['start'] : 0;
//                    $_en = !empty($dateFollowAbsens[$request['absence_' . $type]]['end']) ? $dateFollowAbsens[$request['absence_' . $type]]['end'] : 0;
//                    $_en = strtotime("-1 day", $_en);
//                    if(!empty($_st) && !empty($_en)){
//                        if($_st <= $request['date'] && $request['date'] <= $_en){
//                            if (!isset($waitings[$request['absence_' . $type]])) {
//                                $waitings[$request['absence_' . $type]] = 0;
//                            }
//                            $waitings[$request['absence_' . $type]] += 0.5;
//                        }
//                    }
//                }
//            }
//        }
        $this->set(compact('_start','requests','waitings', 'employeeName', 'absences', 'yearSystem'));
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function reviews() {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        if (!($params = $this->_getProfits($profiltId, $getDataByPath))) {
            $this->Session->setFlash(__('Data not found to validation absences.', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        list($profit, $paths, $employees, $employeeName) = $params;
        list($_start, $_end) = $this->_parseParams2();
        $startDate = date('Y', $_start);
        $this->loadModels('EmployeeAbsence', 'AbsenceHistory', 'Employee');
        $currentYear = date('Y', $_start);
        $lastYear = date('Y', $_start)-1;
        $listEmployeeNotAction = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $employeeName['company_id'],
                'OR' => array(
                    'actif' => 0,
                    'start_date >' => date('Y-m-d', time()),
                    'end_date <' => date('Y-m-d', time())
                )
            ),
            'fields' => array('id')
        ));
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id'], null, $startDate);
        foreach ($employees as $employee => $val) {
            if(in_array($employee, $listEmployeeNotAction)){
                unset($employees[$employee]);
            } else {
                $_absences[$employee] = $this->AbsenceRequest->getEmployeeAbsences($employee, $absences, array($lastYear, $currentYear));
            }
        }
        $absenceHistories = $this->AbsenceHistory->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'year' => array($lastYear, $currentYear)
            ),
            'fields' => array('absence_id', 'total', 'begin', 'year')
        ));
        $absenceHistories = !empty($absenceHistories) ? Set::combine($absenceHistories, '{n}.AbsenceHistory.absence_id', '{n}.AbsenceHistory', '{n}.AbsenceHistory.year') : array();
        $groupDates = !empty($absenceHistories) ? Set::classicExtract($absenceHistories, '{n}.{n}.begin') : array();
        $_employeeAbsences = $this->EmployeeAbsence->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => array_keys($employees),
                'year' => array($lastYear, $currentYear)
            ),
            'fields' => array('total', 'absence_id', 'year', 'employee_id')
        ));
        $employeeAbsences = array();
        if(!empty($_employeeAbsences)){
            foreach($_employeeAbsences as $_employeeAbsence){
                $dx = $_employeeAbsence['EmployeeAbsence'];
                if(!isset($employeeAbsences[$dx['year']][$dx['employee_id']][$dx['absence_id']])){
                    $employeeAbsences[$dx['year']][$dx['employee_id']][$dx['absence_id']] = 0;
                }
                $employeeAbsences[$dx['year']][$dx['employee_id']][$dx['absence_id']] = $dx['total'];
            }
        }
        $currentDate = strtotime(date('d', time()).'-'.date('m', time()).'-'.$currentYear);
        $yearSystem = date('Y', time());
        // gia lap ngay hien tai o day
        //$currentDate = strtotime(date('d', time()).'-'.'06'.'-'.$currentYear);
        if(!empty($_absences)){
            foreach($_absences as $employId => $_absence){
                if(!empty($_absence)){
                    foreach($_absence as $absenId => $values){
                        if(!empty($values['begin'])){
                            $_begin = explode('-', $values['begin']);
                            $begin = !empty($_begin) ? strtotime($_begin[1].'-'.$_begin[0].'-'.$currentYear) : '';
                            $_absences[$employId][$absenId]['total'] = '';
                            if($begin > $currentDate && $yearSystem <= $currentYear){
                                $getAbsenEmploys = !empty($employeeAbsences[$lastYear][$employId][$absenId]) ? $employeeAbsences[$lastYear][$employId][$absenId] : 0;
                                $getAbsences = !empty($absenceHistories[$lastYear][$absenId]['total']) ? $absenceHistories[$lastYear][$absenId]['total'] : 0;
                                $_absences[$employId][$absenId]['total'] = ($getAbsenEmploys>=0) ? $getAbsenEmploys : $getAbsences;
                            } else {
                                $getAbsenEmploys = !empty($employeeAbsences[$currentYear][$employId][$absenId]) ? $employeeAbsences[$currentYear][$employId][$absenId] : 0;
                                $getAbsences = !empty($absenceHistories[$currentYear][$absenId]['total']) ? $absenceHistories[$currentYear][$absenId]['total'] : 0;
                                $_absences[$employId][$absenId]['total'] = ($getAbsenEmploys>=0) ? $getAbsenEmploys : $getAbsences;
                            }
                        }
                    }
                }
            }
        }
        $dateFollowAbsens = $allDateOfAbsences = array();
        if(!empty($absences)){
            foreach($absences as $id => $absence){
                if(!isset($allDateOfAbsences['start'])){
                    $allDateOfAbsences['start'] = 0;
                }
                if(!isset($allDateOfAbsences['end'])){
                    $allDateOfAbsences['end'] = 0;
                }
                $absence['total'] = '';
                if($absence['begin'] != '0000-00-00'){
                    $_begin = explode('-', $absence['begin']);
                    $begin = !empty($_begin) ? strtotime($_begin[2].'-'.$_begin[1].'-'.$currentYear) : '';
                    if($begin > $currentDate && $yearSystem <= $currentYear){
                        $getAbsences = !empty($absenceHistories[$lastYear][$id]['total']) ? $absenceHistories[$lastYear][$id]['total'] : 0;
                        $absences[$id]['total'] = ($getAbsences>=0) ? $getAbsences : 0;
                        $getStartBegin = !empty($begin) ? mktime(0, 0, 0, date("m", $begin), date("d", $begin), date("Y", $begin)-1) : 0;
                        $getEndBegin = $begin;
                        $dateFollowAbsens[$id] = array(
                            'start' => $getStartBegin,
                            'end' => $getEndBegin
                        );
                        if($allDateOfAbsences['start'] == 0){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($getStartBegin < $allDateOfAbsences['start']){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($allDateOfAbsences['end'] < $getEndBegin){
                            $allDateOfAbsences['end'] = $getEndBegin;
                        }
                    } else {
                        $getAbsences = !empty($absenceHistories[$currentYear][$id]['total']) ? $absenceHistories[$currentYear][$id]['total'] : 0;
                        $absences[$id]['total'] = ($getAbsences>=0) ? $getAbsences : '';
                        $getStartBegin = $begin;
                        $getEndBegin = !empty($begin) ? mktime(0, 0, 0, date("m", $begin), date("d", $begin), date("Y", $begin)+1) : 0;
                        $dateFollowAbsens[$id] = array(
                            'start' => $getStartBegin,
                            'end' => $getEndBegin
                        );
                        if($allDateOfAbsences['start'] == 0){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($getStartBegin < $allDateOfAbsences['start']){
                            $allDateOfAbsences['start'] = $getStartBegin;
                        }
                        if($allDateOfAbsences['end'] < $getEndBegin){
                            $allDateOfAbsences['end'] = $getEndBegin;
                        }
                    }
                } else {
                    $dateFollowAbsens[$id] = array(
                        'start' => $_start,
                        'end' => $_end
                    );
                    if($allDateOfAbsences['start'] == 0){
                        $allDateOfAbsences['start'] = $_start;
                    }
                    if($_start < $allDateOfAbsences['start']){
                        $allDateOfAbsences['start'] = $_start;
                    }
                    if($allDateOfAbsences['end'] < $_end){
                        $allDateOfAbsences['end'] = $_end;
                    }
                }
            }
        }
        $checkDates = array();
        if(!empty($groupDates)){
            foreach($groupDates as $groupDate){
                foreach($groupDate as $values){
                    $checkDates[] = strtotime($values);
                }
            }
        }
        $minDate = !empty($checkDates) ? min($checkDates) : 0;
        $maxDate = !empty($checkDates) ? max($checkDates) : 0;
        $startFilter = ($minDate != 0 && $_start > $minDate) ? $minDate : $_start;
        $endFilter = ($_end < $maxDate) ? $maxDate : $_end;
        $startFilter = ($allDateOfAbsences['start'] != 0) && $startFilter > $allDateOfAbsences['start'] ? $allDateOfAbsences['start'] : $startFilter;
        $endFilter = ($endFilter < $allDateOfAbsences['end']) ? $allDateOfAbsences['end'] : $endFilter;
        $requests = array();
        $waitings = array();
        //new code
        $fields = array('employee_id');
        foreach($dateFollowAbsens as $absenceId => $date){
            $date['end'] = strtotime('-1 day', $date['end']);
            $day = sprintf('date BETWEEN %s AND %s', $date['start'], $date['end']);
            $this->AbsenceRequest->virtualFields['v_' . $absenceId] = "SUM(IF(absence_am = $absenceId AND response_am = 'validated' AND $day, 0.5, 0) + IF(absence_pm = $absenceId AND response_pm = 'validated' AND $day, 0.5, 0))";
            $this->AbsenceRequest->virtualFields['w_' . $absenceId] = "SUM(IF(absence_am = $absenceId AND response_am = 'waiting' AND $day, 0.5, 0) + IF(absence_pm = $absenceId AND response_pm = 'waiting' AND $day, 0.5, 0))";
            array_push($fields, 'v_' . $absenceId, 'w_' . $absenceId);
        }

        $_requests = $this->AbsenceRequest->find("all", array(
            'recursive' => -1,
            'fields' => $fields,
            'group' => array('employee_id'),
            "conditions" => array('date BETWEEN ? AND ?' => array($startFilter, $endFilter), 'employee_id' => array_keys($employees))));

        foreach ($_requests as $request) {
            $r = $request['AbsenceRequest'];
            $resource = $r['employee_id'];

            unset($r['employee_id']);
            foreach($r as $k => $value){
                $key = explode('_', $k);
                if( $key[0] == 'v' ){
                    $requests[$resource][$key[1]] = $value;
                } else {
                    $waitings[$resource][$key[1]] = $value;
                }
            }
        }
        //old code
        // $_requests = $this->AbsenceRequest->find("all", array(
        //     'recursive' => -1,
        //     "conditions" => array('date BETWEEN ? AND ?' => array($startFilter, $endFilter), 'employee_id' => array_keys($employees))));
        // foreach ($_requests as $request) {
        //     $request = array_shift($request);
        //     foreach (array('am', 'pm') as $type) {
        //         if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'validated') {
        //             $_st = !empty($dateFollowAbsens[$request['absence_' . $type]]['start']) ? $dateFollowAbsens[$request['absence_' . $type]]['start'] : 0;
        //             $_en = !empty($dateFollowAbsens[$request['absence_' . $type]]['end']) ? $dateFollowAbsens[$request['absence_' . $type]]['end'] : 0;
        //             if(!empty($_st) && !empty($_en)){
        //                 if($_st <= $request['date'] && $request['date'] <= $_en){
        //                     if (!isset($requests[$request['employee_id']][$request['absence_' . $type]])) {
        //                         $requests[$request['employee_id']][$request['absence_' . $type]] = 0;
        //                     }
        //                     $requests[$request['employee_id']][$request['absence_' . $type]] += 0.5;
        //                 }
        //             }
        //         }
        //         //lay thong tin ngay da request nhung chua validated
        //         if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'waiting') {
        //             $_st = !empty($dateFollowAbsens[$request['absence_' . $type]]['start']) ? $dateFollowAbsens[$request['absence_' . $type]]['start'] : 0;
        //             $_en = !empty($dateFollowAbsens[$request['absence_' . $type]]['end']) ? $dateFollowAbsens[$request['absence_' . $type]]['end'] : 0;
        //             $_en = strtotime("-1 day", $_en);
        //             //pr('st=' . date('d-m-Y', $_st) . ' en=' . date('d-m-Y', $_en) . ' req=' . date('d-m-Y', $request['date']));

        //             if(!empty($_st) && !empty($_en)){
        //                 if($_st <= $request['date'] && $request['date'] <= $_en){
        //                     if (!isset($waitings[$request['employee_id']][$request['absence_' . $type]])) {
        //                         $waitings[$request['employee_id']][$request['absence_' . $type]] = 0;
        //                     }
        //                     $waitings[$request['employee_id']][$request['absence_' . $type]] += 0.5;
        //                 }
        //             }
        //         }
        //     }
        // }
        $this->set(compact('_start','requests','waitings', 'employeeName', 'absences', '_absences', 'employees', 'profit', 'paths', 'yearSystem'));
        if( isset($this->params['url']['export']) ){
            $this->layout = 'excel';
            $this->render('export_request');
        }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
	function getDateRequest($dates){
		$employees = array();
		$start = 0;
		$end = 0;
		foreach($dates as $key => $date){
			$date = explode('-', $date);
			$employees[$date[0]][$key]['start'] = $date[1];
			$employees[$date[0]][$key]['end'] = $date[2];
		}
		return $employees;
	}
    function manage($typeSelect = null, $toValidated = false) {
        set_time_limit(0);
		$data_request = $this->data;
        $this->loadModels('AbsenceComment', 'ActivityForecast', 'ActivityRequest', 'AbsenceRequestConfirm', 'ActivityRequestConfirm', 'ProjectEmployeeProfitFunctionRefer');
        $typeSelect = ($typeSelect == null) ? 'week' : $typeSelect;
        $profitOfEmployees = array();
        if( $toValidated ){
            list($_start, $_end) = $this->_parseParamsMonth();
        } else {
            list($_start, $_end) = $this->_parseParams();
        }
        if($toValidated == true && !empty($this->data['id'])){
            $this->loadModels('Employee', 'CompanyEmployeeReference', 'ProfitCenter');
            $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
            $employeeName = $this->employee_info['Employee'];
            /**
             * Get All employee of company
             */
            $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name, " ", Employee.last_name) AS full_name'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id']),
                'order' => array('Employee.first_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.0.full_name') : array();
            /**
             * Lay cac profit manager
             */
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id'], 'NOT' => array('manager_id' => null)),
                'fields' => array('id', 'manager_id')
            ));
            $employFinish = array();
            if(!empty($profitCenters)){
                foreach($profitCenters as $emp){
                    if (isset($employees[$emp]) && !isset($employFinish[$emp])) {
                        $employees[$emp] = $employees[$emp] . '<strong> (Manager)</strong>';
                        $employFinish[$emp] = $emp;
                    }
                }
            }
        } else {
            $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
            $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
            if($typeSelect =='year'){
                  if($toValidated == true){
                      //do nothing
                  } else {
                      $getDataByPath = false;
                  }
            }
            if (!($params = $this->_getProfitFollowDates($profiltId, $getDataByPath, $_start, $_end))) {
                $this->Session->setFlash(__('Data not found to validation absences.', true), 'error');
                $this->redirect(array('controller' => 'employees', 'action' => 'index'));
            }
            list($profit, $paths, $employees, $employeeName) = $params;
        }
        /**
         * Lay profit center cua employee
         */
        $emp = $this->Employee->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('company_id' => $employeeName['company_id']),
                                'fields' => array('id','end_date','start_date')
                            ));
        $emp_end = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.end_date') : array();
        $emp_start = !empty($emp) ? Set::combine($emp, '{n}.Employee.id', '{n}.Employee.start_date') : array();
        // end get employee end day > current day
        $profitOfEmployees = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => array_keys($employees)),
            'fields' => array('employee_id', 'profit_center_id')
        ));
        $dayHasValidations = array();
        $requestConfirms = array();
        $statusConfirms = array();
        $totalWeek = 0;
        if($typeSelect == 'week'){
            list($_start, $_end) = $this->_parseParams();
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'employee_id', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => array_keys($employees),
                    'start' => $_start, 'end' => $_end
                )
            ));
            if (!empty($requestConfirms)) {
                $startDate = $_start;
                foreach($requestConfirms as $requestConfirm){
                    $dx = $requestConfirm['ActivityRequestConfirm'];
                    if($dx['status'] !== false){
                        while($dx['start'] <= $dx['end']){
                            if(!isset($dayHasValidations[$dx['employee_id']][$dx['start']])){
                                $dayHasValidations[$dx['employee_id']][$dx['start']] = '';
                            }
                            $dayHasValidations[$dx['employee_id']][$dx['start']] = $dx['start'];
                            $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                        }
                        if(!isset($statusConfirms[$dx['employee_id']][$dx['status']])){
                            $statusConfirms[$dx['employee_id']][$dx['status']] = '';
                        }
                        $statusConfirms[$dx['employee_id']][$dx['status']] = $dx['status'];
                    }
                }
            }
            $totalWeek = 7;
        }elseif($typeSelect == 'month'){
            list($_start, $_end) = $this->_parseParamsMonth(true, true);
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'employee_id', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => array_keys($employees),
                    'start' => array_keys($listWeekOfMonths)
                )
            ));
            if(!empty($requestConfirms)){
                foreach($requestConfirms as $requestConfirm){
                    $dx = $requestConfirm['ActivityRequestConfirm'];
                    if($dx['status'] !== false){
                        while($dx['start'] <= $dx['end']){
                            if(!isset($dayHasValidations[$dx['employee_id']][$dx['start']])){
                                $dayHasValidations[$dx['employee_id']][$dx['start']] = '';
                            }
                            $dayHasValidations[$dx['employee_id']][$dx['start']] = $dx['start'];
                            $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                        }
                    }
                    if(!isset($statusConfirms[$dx['employee_id']][$dx['status']])){
                        $statusConfirms[$dx['employee_id']][$dx['status']] = '';
                    }
                    $statusConfirms[$dx['employee_id']][$dx['status']] = $dx['status'];
                }
            }
            $totalWeek = count($listWeekOfMonths) * 7;
        }else{
            list($_start, $_end) = $this->_parseParamsMonth(true, true, true);
            $listWeekOfMonths = $this->_splitWeekOfMonth($_start, $_end);
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'employee_id', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => array_keys($employees),
                    'start' => array_keys($listWeekOfMonths)
                )
            ));
            if(!empty($requestConfirms)){
                foreach($requestConfirms as $requestConfirm){
                    $dx = $requestConfirm['ActivityRequestConfirm'];
                    if($dx['status'] !== false){
                        while($dx['start'] <= $dx['end']){
                            if(!isset($dayHasValidations[$dx['employee_id']][$dx['start']])){
                                $dayHasValidations[$dx['employee_id']][$dx['start']] = '';
                            }
                            $dayHasValidations[$dx['employee_id']][$dx['start']] = $dx['start'];
                            $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                        }
                    }
                    if(!isset($statusConfirms[$dx['employee_id']][$dx['status']])){
                        $statusConfirms[$dx['employee_id']][$dx['status']] = '';
                    }
                    $statusConfirms[$dx['employee_id']][$dx['status']] = $dx['status'];
                }
            }
            $totalWeek = count($listWeekOfMonths) * 7;
        }
        /**
         * Cac ngay lam viec trong tuan
         */
        $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
        /**
         * Lay ngay nghi, ngay le trong tuan
         */
        $holidays = ClassRegistry::init('Holiday')->get2($employeeName['company_id'], $_start, $_end);
        foreach($holidays as $time => $h){
            $day = strtolower(date('l', $time));
            if($workdays[$day] == 0){
                unset($holidays[$time]);
            }
        }

        $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
        $conditions = array();
        $status = !empty($this->params['url']['st']) ? $this->params['url']['st'] : '';
        if ($status && !empty($constraint[$status])) {
            $conditions['or'] = array('response_am' => $status, 'response_pm' => $status);
        }
        if (!empty($this->data['id'])) {
            $conditions['updated <'] = $this->data['ls'] + 1E9;
        }
        if($toValidated == true){
            $conditions = array();
            $conditions['or'] = array('response_am' => 'waiting', 'response_pm' => 'waiting');
        }
        $_requests = $this->AbsenceRequest->find("all", array(
                            'recursive' => -1,
                            "conditions" => $conditions + array('date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => array_keys($employees))));
        $requests = Set::combine($_requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
        $dateRequests = array();
        $employOfWeeks = $listWeeks = $allRequests = array();
        if(!empty($_requests) && $toValidated == true){
            //$dateRequests = Set::combine($_requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest');
            $allRequests = Set::combine($_requests, '{n}.AbsenceRequest.employee_id', '{n}.AbsenceRequest', '{n}.AbsenceRequest.date');
            if(!empty($_requests)){
                foreach($_requests as $_data){
                    $dx = $_data['AbsenceRequest'];
                    $date = $dx['date'];
                    $_week = date('W-Y', $date);
                    $listWeeks[$_week] = $_week;
                    if(!isset($employOfWeeks[$_week])){
                        $employOfWeeks[$_week] = array();
                    }
                    $employOfWeeks[$_week][$dx['employee_id']] = $dx['employee_id'];
                }
            }
        }
        /**
         * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
         */
        list($multiWeeks, $listWorkingDays) = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays, $listWeeks);
        if ($conditions) {
            $employees = array_intersect_key($employees, $requests);
        }
        $_comments = $this->AbsenceComment->find("all", array(
            'recursive' => -1,
            "conditions" => array('date BETWEEN ? AND ?' => array($_start, $_end),
                'employee_id' => array_unique(array_merge(array($employeeName['id']), array_keys($employees))))));
        $comments = array();
        foreach ($_comments as $comment) {
            $comment = array_shift($comment);
            $comments[$comment['employee_id']][$comment['date']][$comment['time'] ? 'pm' : 'am'][$comment['id']] = array(
                'user_id' => $comment['user_id'],
                'created' => date('d-m-Y H:i', $comment['created']),
                'text' => $comment['text']);
        }
        $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id']);
        $absences['-1'] = array(
            'id' => '-1',
            'print' => __('Provisional day off', true)
        );
        if (!empty($this->data['id'])) {
            $rangeDateForEmploy = array();
            if($toValidated == true){
                $listEmploys = array();
                foreach($this->data['id'] as $val){
                    $val = explode('-', $val);
                    $listEmploys[] = $val[0];
                    if(!isset($rangeDateForEmploy[$val[0]])){
                        $rangeDateForEmploy[$val[0]] = array();
                    }
                    $weekRequest = round(($val[1] + $val[2])/2, 0);
                    $weekRequest = date('W-Y', $weekRequest);
                    $rangeDateForEmploy[$val[0]][] = $weekRequest;
                }
                $this->data['id'] = $listEmploys;
            }
            $_employees = $this->AbsenceRequest->Employee->find('list', array(
                'recursive' => -1, 'fields' => array('id', 'email'), 'conditions' => array('id' => $this->data['id'])));
            $to = array();
            $this->AbsenceRequest->cacheQueries = false;
            $response = !empty($this->data['validated']) ? 'validated' : 'rejetion';
            foreach ($_employees as $id => $email) {
                /**
                 * Kiem tra sau
                 */
                if (empty($requests[$id])) {
                    continue;
                }
                foreach ($requests[$id] as $date => $request) {
                    $checkDate = date('W-Y', $date);
                    if($toValidated == true){
                        if(!empty($rangeDateForEmploy) && !empty($rangeDateForEmploy[$id]) && in_array($checkDate, $rangeDateForEmploy[$id])){
                            //okie cho request
                        } else {
                            continue;
                        }
                    }
                    if(!empty($dayHasValidations[$id]) && !empty($dayHasValidations[$id][$date])){
                        //continue;
                        // Cac ngay da roi vao tuan da validation timesheet ben activity request nen khong cho thao tac gi nua.
                    } else {
                        $history = array();
                        $this->AbsenceRequest->id = $request['id'];
                        if (!empty($request['history'])) {
                            $history = unserialize($request['history']);
                        }
                        $save = array();
                        foreach (array('am', 'pm') as $type) {
                            if (intval($request['absence_' . $type]) > 0 && $request['response_' . $type] != $response) {
                                $save['response_' . $type] = $response;
                                if ($response == 'rejetion') {
                                    $save['absence_' . $type] = 0;
                                } elseif ($response == 'validated') {
                                    $this->ActivityForecast->updateAll(array('activity_' . $type => 0), array(
                                        'employee_id' => $id, 'date' => $date, 'NOT' => array('activity_' . $type => 0)));
                                   
                                }
                                $history[($response === 'validated' ? 'rv_' : 'rj_') . $type] = date('d-m-Y H:i');
                            }
                        }
                        $save && $save['history'] = serialize($history);
                        if ($save && $this->AbsenceRequest->save($save)) {
                            $to[$id] = $email;
                            $this->AbsenceRequestConfirm->deleteAll(array('employee_id' => $id, 'start' => $_start, 'end' => $_end), false, false);
                        }
                    }
                }
            }
            if ($to) {
                $this->Session->setFlash(__('The data has been saved.', true), 'success');
                $response = $constraint[$response]['name'];
                $this->set('isValidated', !empty($this->data['validated']));
                $this->set('typeSelect', $typeSelect);
				$mail_template = ($typeSelect == 'year') ? 'absence_response_year' : 'absence_response';
				if($typeSelect != 'year'){
					foreach( $to as $email){
						/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug) */
						$this->_z0GSendEmail($email, null, null, sprintf(__('[PMS - Absence] Absence has been %s.', true), !empty($this->data['validated']) ? __('validated', true) : __('rejected', true)), $mail_template);
					}
				}else{
					$ab_request = $this->getDateRequest($data_request['id']);
					$this->set('ab_request', $ab_request);
					foreach($ab_request as $employee_request => $dates){
						$this->set('employee_request', $employee_request);
						if(!empty($to) && !empty($to[$employee_request])){
							$this->_z0GSendEmail($to[$employee_request], null, null, sprintf(__('[PMS - Absence] Absence has been %s.', true), !empty($this->data['validated']) ? __('validated', true) : __('rejected', true)), $mail_template);
						}
					}
				}
            } else {
                $this->Session->setFlash(__('The data was not found or has an employees change the requested, please try again', true), 'error');
            }
            if($toValidated == true){
                $dateOfYear = round(($_start + $_end)/2, 0);
                $profiltId = isset($this->params['url']['profit']) ? $this->params['url']['profit'] : null;
                $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
                $this->redirect(array('action' => 'manage', 'year', 'true', '?' => array('year' => date('Y', $dateOfYear), 'month' => 1, 'profit' => $profiltId, 'get_path' => $getDataByPath)));
            }
            if($typeSelect === 'week'){
                $avg = intval( ($_start + $_end) / 2 );
                $this->redirect(array('action' => 'manage', '?' => array('st' => $status,
                    'profit' => $profit['id'],'get_path' => $getDataByPath, 'week' => date('W', $avg), 'year' => date('Y', $avg))));
            } elseif($typeSelect === 'month'){
                $this->redirect(array('action' => 'manage',
                'month', '?' => array('month' => date('m', $_start), 'year' => date('Y', $_start),'st' => $status,
                    'profit' => $profit['id'],'get_path' => $getDataByPath)));
            } else {
                $this->redirect(array('action' => 'manage',
                'year', '?' => array('month' => 1, 'year' => date('Y', $_start),'st' => $status,
                    'profit' => $profit['id'],'get_path' => $getDataByPath)));
            }

        }
        if($toValidated == true){
            $this->action = 'to_validated';
            //$this->render('to_validated');
        }
        $this->set(compact('profitOfEmployees', 'employOfWeeks', 'allRequests', 'statusConfirms', 'dayHasValidations', 'typeSelect', 'listWorkingDays', 'status', 'requests', 'profit', 'constraint', 'absences', 'workdays', 'paths', 'employees', 'comments', 'employeeName', 'holidays', 'requestConfirms', 'getDataByPath', 'totalWeek', 'multiWeeks'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        //$this->loadModel('AbsenceRequestReport');
        $this->AbsenceRequest->cacheQueries = false;
        $this->layout = false;
        $success = 0;
        $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
        list(, $employeeName) = $this->_getProfitEmployee($getDataByPath);
        if (!empty($this->data) && $employeeName && (empty($this->data['request']) || $this->data['request'] == '-1' || ($absence = $this->AbsenceRequest->AbsenceAm->find('first', array(
            'recursive' => -1, 'conditions' => array('id' => $this->data['request'])))))) {
            if (isset($this->data['request']) && $this->data['request'] == 0) {
                $success = $this->_remove($employeeName);
            } else {
                $request = $this->data['request'];
                unset($this->data['request']);
                if ($request == '-1') {
                    $absence = array(
                        'id' => '-1',
                        'print' => __('Provisional day off', true),
                        'total' => '0'
                    );
                } else {
                    $absence = $absence['AbsenceAm'];
                }
                $this->AbsenceRequest->unbindModelAll();
                $responses = array();
                foreach ($this->data as &$data) {
                    if (!empty($data['date'])) {
                        $data['employee_id'] = $employeeName['id'];
                        $last = $this->AbsenceRequest->find('first', array('recursive' => -1, 'fields' => array('id', 'absence_am', 'absence_pm', 'history'),
                            'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));
                        $history = array();
                        if ($last) {
                            $this->AbsenceRequest->id = $last['AbsenceRequest']['id'];
                            if (!empty($last['AbsenceRequest']['history'])) {
                                $history = unserialize($last['AbsenceRequest']['history']);
                            }
                            $this->AbsenceRequest->deleteAll(array(
                                'NOT' => array('id' => $this->AbsenceRequest->id),
                                'employee_id' => $data['employee_id'], 'date' => $data['date']
                            ));
                        } else {
                            $this->AbsenceRequest->create();
                        }
                        //build data for auto validate
                        $responses[ $employeeName['id'] ][ $data['date'] ] = array(
                            'employee_id' => $employeeName['id'],
                            'date' => $data['date'],
                            'am' => false,
                            'pm' => false
                        );
                        foreach (array('am', 'pm') as $type) {
                            if (!empty($data[$type])) {
                                $responses[ $employeeName['id'] ][ $data['date'] ][$type] = true;
                                $data['absence_' . $type] = $absence['id'];
                                if ($request == '-1') {
                                    $data['response_' . $type] = 'forecast';
                                } else {
                                    $data['response_' . $type] = 'waiting';
                                }
                                $history['rq_' . $type] = date('d-m-Y H:i');
                            }
                            unset($data[$type]);
                        }
                        $data['history'] = $history;
                        if (($data['result'] = (bool) $this->AbsenceRequest->save(array_merge(
                                                $data, array('history' => serialize($history)))))) {
                            /**
                             * Save absence report
                             */
                            /*
                            $absenceRequests = array();
                            if(isset($data['absence_am']) && isset($data['response_am'])){
                                $absenceRequests[] = array(
                                    'date' => $data['date'],
                                    'employee_id' => $data['employee_id'],
                                    'absence_id' => $data['absence_am'],
                                    'response' => $data['response_am'],
                                    'moment' => 'am'
                                );
                            }
                            if(isset($data['absence_pm']) && isset($data['response_pm'])){
                                $absenceRequests[] = array(
                                    'date' => $data['date'],
                                    'employee_id' => $data['employee_id'],
                                    'absence_id' => $data['absence_pm'],
                                    'response' => $data['response_pm'],
                                    'moment' => 'pm'
                                );
                            }
                            $this->AbsenceRequestReport->saveAbsenceReport($absenceRequests);
                            */
                            $success++;
                        }
                    }
                }
            }
            if ($success == count($this->data)) {
                // $this->Session->setFlash(__('The request has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('Some request could not be saved because invalidate. Please check the limitation of your day-off and Begin of Period parameter !"', true), 'warning');
            }
            /*
            auto validate - By QN on 2015-06-01
            */
            if( isset($responses) ){
                $this->loadModel('ProfitCenter');
                if( $this->isAuto($employeeName) ){
                    $new_data = $this->process($employeeName['profit_center_id'], $responses);
					$new_data = $new_data[$employeeName['id']];
					foreach( $this->data as $date => &$date_data){
						if( isset($date_data['absence_am']) ) $date_data['response_am'] = $new_data[$date]['response_am'];
						if( isset($date_data['absence_pm']) ) $date_data['response_pm'] = $new_data[$date]['response_pm'];
					}
                    $this->set('auto_validate', 1);
                }
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
    }

    function isAuto($employeeName){
        $this->loadModel('ProfitCenter');
        $auto = $this->ProfitCenter->checkAutoValidOfProfitCenter($employeeName['profit_center_id']);
        if( $auto['absence'] || ($employeeName['id'] == $this->employee_info['Employee']['id'] && $this->employee_info['Employee']['auto_by_himself']) ){
            return true;
        }
        return false;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _remove($employeeName) {
        $this->loadModel('AbsenceRequestFile');
        $success = 0;
        unset($this->data['request']);
        $files = array();
        foreach ($this->data as &$data) {
            if (!empty($data['date'])) {
                $data['employee_id'] = $employeeName['id'];
                $last = $this->AbsenceRequest->find('first', array('recursive' => -1, 'fields' => array('id', 'absence_am', 'absence_pm', 'history'),
                    'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));
                if (!$last) {
                    continue;
                }
                $history = array();
                if (!empty($last['AbsenceRequest']['history'])) {
                    $history = unserialize($last['AbsenceRequest']['history']);
                }
                $this->AbsenceRequest->id = $last['AbsenceRequest']['id'];
                $remove = 0;
                foreach (array('am', 'pm') as $type) {
                    if (!empty($data[$type])) {
                        $remove++;
                        $data['absence_' . $type] = '0';
                        $data['response_' . $type] = '0';
                        unset($history['rq_' . $type], $history['rv_' . $type], $history['rj_' . $type]);
                        if($type === 'am'){
                            $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                'fields' => array('id', 'file_attachment')
                            ));
                            if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                            }
                            $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'), false);
                        } else {
                            $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                'recursive' => -1,
                                'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                'fields' => array('id', 'file_attachment')
                            ));
                            if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                            }
                            $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_pm' => 'pm'), false);
                        }
                    }
                    unset($data[$type]);
                }
                $data['history'] = serialize($history);
                if (($remove == 2 && ($data['result'] = (bool) $this->AbsenceRequest->delete($this->AbsenceRequest->id))) ||
                        ($remove == 1 && ($data['result'] = (bool) $this->AbsenceRequest->save($data)))) {
                    $success++;
                }
            }
        }
        if(!empty($files)){
            $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $employeeName['company_id'])));
            $path = FILES . 'absence_request_files' . DS;
            $path .= $company['Company']['dir'] . DS;
            foreach($files as $file){
                $afterDeletes = $this->AbsenceRequestFile->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('file_attachment' => $file)
                ));
                if($afterDeletes == 0){
                    unlink($path . $file);
                }
            }
        }
        return $success;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function comment_update() {
        $this->loadModel('AbsenceComment');
        $this->AbsenceComment->cacheQueries = false;
        $this->layout = false;
        $success = $total = 0;
        if (!empty($this->data)) {
            foreach ($this->data as &$post) {
                foreach ($post as &$data) {
                    $total++;
                    if (!empty($data['date']) && !empty($data['employee_id']) && ($employeeName = $this->_getEmpoyee())) {
                        foreach (array('am', 'pm') as $type) {
                            $save = array(
                                'date' => $data['date'],
                                'employee_id' => $data['employee_id'],
                                'user_id' => $employeeName['id'], // Employee login
                                'time' => ($type == 'pm')
                            );
                            if (!empty($data[$type])) {
                                $save['text'] = str_replace("\n", "<br/>", trim($data[$type]));
                                $this->AbsenceComment->create();
                                if (!($data['result'] = (bool) $this->AbsenceComment->save($save))) {
                                    break;
                                }
                                $data['id_' . $type] = $this->AbsenceComment->id;
                                $data[$type] = $save['text'];
                                $data['created'] = date('d-m-Y H:i');
                            }
                        }
                        if (!empty($data['result'])) {
                            $success++;
                        }
                    }
                }
            }
            if ($success == $total) {
                $this->Session->setFlash(__('The comment has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('Some comment could not be saved because invalidate.', true), 'warning');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function comment_delete() {
        $this->layout = false;
        $this->loadModel('AbsenceComment');
        if (isset($this->params['url']['id']) && ($employeeName = $this->_getEmpoyee())) {
            if ($this->AbsenceComment->find('count', array('recursive' => -1, 'conditions' => array(
                            'id' => $this->params['url']['id'],
                            'user_id' => $employeeName['id']
                            )))) {
                $this->AbsenceComment->delete($this->params['url']['id']);
            }
        }
        $this->_stop();
    }
/*
data = {
    employee_id: {
        date: {
            am: true/false,
            pm: true/false,
            date: int,
            employee_id: int
        },
        ...
    },
    response: validate/reject
}
*/
    private function process($profitId = null, $rawData){
        $employeeName = $this->_getEmpoyee();
        $rawData['response'] = 'validated';
        if ( !empty($employeeName) && !empty($rawData['response']) && ($constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id'])) && isset($constraint[$rawData['response']])) {
            $response = 'validated';
            unset($rawData['response']);
            $this->loadModel('ActivityForecast');
            $this->loadModel('ActivityRequest');
            $this->loadModel('AbsenceRequestConfirm');
            $this->loadModel('AbsenceRequestFile');
            $files = array();
            $to = array();
            $affectedDays = array();
            foreach ($rawData as &$post) {
                foreach ($post as &$data) {
                    $last = $this->AbsenceRequest->find('first', array('recursive' => -1, 'fields' => array('id', 'history'),
                            'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));
                    if($last){
                        $this->AbsenceRequest->id = $last['AbsenceRequest']['id'];
                        if (!empty($last['AbsenceRequest']['history'])) {
                            $history = unserialize($last['AbsenceRequest']['history']);
                        }
                        $this->AbsenceRequest->cacheQueries = false;
                        foreach (array('am', 'pm') as $type) {
                            if (!empty($data[$type])) {
                                $data['response_' . $type] = $response;
                                if ($response == 'rejetion') {
                                    $data['absence_' . $type] = 0;
                                } elseif ($response == 'validated') {
                                    $this->ActivityForecast->updateAll(array('activity_' . $type => 0), array(
                                        'employee_id' => $data['employee_id'], 'date' => $data['date'], 'NOT' => array('activity_' . $type => 0)));
                                }
                                $history[($response === 'validated' ? 'rv_' : 'rj_') . $type] = date('d-m-Y H:i');
                                if($type === 'am'){
                                    $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                        'fields' => array('id', 'file_attachment')
                                    ));
                                    if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                        $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                                    }
                                    $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'), false);
                                } else {
                                    $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                        'recursive' => -1,
                                        'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                        'fields' => array('id', 'file_attachment')
                                    ));
                                    if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                        $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                                    }
                                    $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_pm' => 'pm'), false);
                                }
                            }
                            unset($data[$type]);
                        }
                        $data['history'] = $history;
                        if (($data['result'] = (bool) $this->AbsenceRequest->save(array_merge(
                                                $data, array('history' => serialize($history)))))) {
                            $to[$data['employee_id']] = $data['employee_id'];
                            $value = isset($data['response_am']) && $data['response_am'] == 'validated' ? 0.5 : 0;
                            $value += isset($data['response_pm']) && $data['response_pm'] == 'validated' ? 0.5 : 0;
                            $affectedDays[$data['employee_id']][$data['date']] = $value;
                        }
                    }
                }
            }
            if(!empty($to) && in_array($response, array('validated', 'rejetion')) )
            {
                $to = array_keys($to);
                $_employees = $this->AbsenceRequest->Employee->find('list', array(
                    'recursive' => -1, 'fields' => array('email', 'email'), 'conditions' => array('id' => $to)
                ));
                $_employees = array_values($_employees);
                $this->resetTimesheet($affectedDays);
                //$this->_sendEmail($_employees, sprintf(__('[PMS - Absence] Absence has been %s.', true), $response == 'rejetion' ? 'validated' : $response), 'absence_response');
            }
            if(!empty($files)){
                $company = ClassRegistry::init('Company')->find('first', array(
                'recursive' => -1, 'conditions' => array('Company.id' => $employeeName['company_id'])));
                $path = FILES . 'absence_request_files' . DS;
                $path .= $company['Company']['dir'] . DS;
                foreach($files as $file){
                    $afterDeletes = $this->AbsenceRequestFile->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('file_attachment' => $file)
                    ));
                    if($afterDeletes == 0){
                        unlink($path . $file);
                    }
                }
            }
        }
        return $rawData;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function manage_update($profitId = null, $_start = null, $_end = null, $typeSelect = 'week', $toValidated = false) {
        $this->set(compact('_start', '_end'));
        $this->set('typeSelect', $typeSelect);
        $this->layout = false;
        $success = $total = 0;
        if($toValidated == true){
            $this->loadModels('Employee', 'CompanyEmployeeReference', 'ProfitCenter');
            $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
            $employeeName = $this->employee_info['Employee'];
            /**
             * Get All employee of company
             */
            $employees = $this->Employee->CompanyEmployeeReference->find('all', array(
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name, " ", Employee.last_name) AS full_name'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $employeeName['company_id']),
                'order' => array('Employee.first_name')
            ));
            $employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.0.full_name') : array();
            /**
             * Lay cac profit manager
             */
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id'], 'NOT' => array('manager_id' => null)),
                'fields' => array('id', 'manager_id')
            ));
            $employFinish = array();
            if(!empty($profitCenters)){
                foreach($profitCenters as $emp){
                    if (isset($employees[$emp]) && !isset($employFinish[$emp])) {
                        $employees[$emp] = $employees[$emp] . '<strong> (Manager)</strong>';
                        $employFinish[$emp] = $emp;
                    }
                }
            }
            $conditions = $employees && !empty($this->data) && !empty($this->data['response']) &&
                ($constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id'])) && isset($constraint[$this->data['response']]);
        } else {
            $getDataByPath = isset($this->params['url']['get_path']) && $this->params['url']['get_path'] == 1 ? true : false;
            if (($params = $this->_getProfits($profitId, $getDataByPath))) {
                list($profit, $paths, $employees, $employeeName) = $params;
            }
            $conditions = $params && $employees && !empty($this->data) && !empty($this->data['response']) &&
                ($constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id'])) && isset($constraint[$this->data['response']]);
        }
        if ($conditions) {
            $response = $this->data['response'];
            unset($this->data['response']);
            $this->loadModel('ActivityForecast');
            $this->loadModel('ActivityRequest');
            $this->loadModel('AbsenceRequestConfirm');
            $this->loadModel('AbsenceRequestFile');
            $files = array();
            $to = array();
            $affectedDays = array();
            foreach ($this->data as &$post) {
                foreach ($post as &$data) {
                    $total++;
                    if (!empty($data['date']) && isset($employees[$data['employee_id']])) {
                        $last = $this->AbsenceRequest->find('first', array('recursive' => -1, 'fields' => array('id', 'history'),
                            'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'])));

                        $history = array();
                        if ($last) {
                            $this->AbsenceRequest->id = $last['AbsenceRequest']['id'];
                            if (!empty($last['AbsenceRequest']['history'])) {
                                $history = unserialize($last['AbsenceRequest']['history']);
                            }
                            $this->AbsenceRequest->cacheQueries = false;
                            foreach (array('am', 'pm') as $type) {
                                if (!empty($data[$type])) {
                                    $data['response_' . $type] = $response;
                                    if ($response == 'rejetion') {
                                        $data['absence_' . $type] = 0;
                                    } elseif ($response == 'validated') {
                                        $this->ActivityForecast->updateAll(array('activity_' . $type => 0), array(
                                            'employee_id' => $data['employee_id'], 'date' => $data['date'], 'NOT' => array('activity_' . $type => 0)));
                                        // $this->ActivityRequest->deleteAll(array(
                                        //    'date' => $data['date'], 'employee_id' => $data['employee_id']
                                        //        ), false, false);
                                    }
                                    $history[($response === 'validated' ? 'rv_' : 'rj_') . $type] = date('d-m-Y H:i');
                                    if($type === 'am'){
                                        $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                            'fields' => array('id', 'file_attachment')
                                        ));
                                        if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                            $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                                        }
                                        $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'), false);
                                    } else {
                                        $beforeDeletes = $this->AbsenceRequestFile->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_am' => 'am'),
                                            'fields' => array('id', 'file_attachment')
                                        ));
                                        if(!empty($beforeDeletes) && !empty($beforeDeletes['AbsenceRequestFile']['file_attachment'])){
                                            $files[$beforeDeletes['AbsenceRequestFile']['file_attachment']] = $beforeDeletes['AbsenceRequestFile']['file_attachment'];
                                        }
                                        $this->AbsenceRequestFile->deleteAll(array('employee_id' => $data['employee_id'], 'date' => $data['date'], 'request_pm' => 'pm'), false);
                                    }
                                }
                                unset($data[$type]);
                            }
                            $data['history'] = $history;
                            if (($data['result'] = (bool) $this->AbsenceRequest->save(array_merge(
                                                    $data, array('history' => serialize($history)))))) {
                                $to[$data['employee_id']] = $data['employee_id'];
                                $success++;
                                $value = isset($data['response_am']) && $data['response_am'] == 'validated' ? 0.5 : 0;
                                $value += isset($data['response_pm']) && $data['response_pm'] == 'validated' ? 0.5 : 0;
                                $affectedDays[$data['employee_id']][$data['date']] = $value;
                            }
                        }
                        if($response === 'rejetion'){
                            $this->AbsenceRequestConfirm->deleteAll(array('employee_id' => $data['employee_id'], 'start' => $_start, 'end' => $_end), false, false);
                        }
                    }
                }
            }
            if(!empty($to) && in_array($response, array('validated', 'rejetion')) )
            {
                $to = array_keys($to);
                $_employees = $this->AbsenceRequest->Employee->find('list', array(
                    'recursive' => -1, 'fields' => array('email', 'email'), 'conditions' => array('id' => $to)
                ));
                $_employees = array_values($_employees);
                $this->set('isValidated', $response == 'validated');
                // $this->_sendEmail($_employees, sprintf(__('[PMS - Absence] Absence has been %s.', true), $response == 'rejetion' ? 'validated' : $response), 'absence_response');
				/* Function _z0GSendEmail($to, $cc, $bcc, $subject, $element, $fileAttach, $debug) */
				foreach( $_employees as $e){
					$this->_z0GSendEmail($e, null, null, sprintf(__('[PMS - Absence] Absence has been %s.', true), $response == 'rejetion' ? 'validated' : $response), 'absence_response');
				}
            }
            if(!empty($files)){
                $company = ClassRegistry::init('Company')->find('first', array(
                'recursive' => -1, 'conditions' => array('Company.id' => $employeeName['company_id'])));
                $path = FILES . 'absence_request_files' . DS;
                $path .= $company['Company']['dir'] . DS;
                foreach($files as $file){
                    $afterDeletes = $this->AbsenceRequestFile->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('file_attachment' => $file)
                    ));
                    if($afterDeletes == 0){
                        unlink($path . $file);
                    }
                }
            }
            if ($success == $total) {
                //reset timesheet of affected day to 0
                if( $response == 'validated' ){
                    $this->resetTimesheet($affectedDays);
                }
                $this->Session->setFlash(__('The request has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('Some request could not be saved because invalidate.', true), 'warning');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
    }

    private function resetTimesheet($data){
        return;
        $this->loadModel('ActivityRequest');
        foreach($data as $eid => $arr){
            foreach($arr as $date => $value){
                //check if activity-request > value, then reset all
                $requests = $this->ActivityRequest->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'ActivityRequest.employee_id' => $eid,
                        'ActivityRequest.date' => $date,
                        'ActivityRequest.status' => array(-1, 1)
                    ),
                    'fields' => array('SUM(ActivityRequest.value) as total')
                ));
                $total = !empty($requests[0]['total']) ? $requests[0]['total'] : 0;
                if( $total > (1 - $value) ){
                    $this->ActivityRequest->updateAll(array(
                        'ActivityRequest.value' => 0
                    ), array(
                        'ActivityRequest.employee_id' => $eid,
                        'ActivityRequest.date' => $date,
                        'ActivityRequest.status' => array(-1, 1)
                    ));
                }
            }
        }
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($employee_id = null, $company_id = null) {
        die('under construction');
        $employeeName = $this->_getEmpoyee($employee_id, $company_id, $canModified);
        if (empty($employeeName)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $conditions = array('company_id' => $company_id, 'display' => true);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['id'] = $data;
            }
        }
        $this->AbsenceRequest->Absence->cacheQueries = true;
        $this->AbsenceRequest->cacheQueries = true;
        $absences = $this->AbsenceRequest->Absence->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions));
        $absenceRequests = $this->AbsenceRequest->find('all', array('conditions' => array(
                'employee_id' => $employee_id),
            'recursive' => -1));
        $absenceRequests = Set::combine($absenceRequests, '{n}.AbsenceRequest.absence_id', '{n}.AbsenceRequest');

        $absences = Set::combine($absences, '{n}.Absence.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($absences[$id])) {
                    unset($data[$id]);
                    unset($absences[$id]);
                    continue;
                }
                $data[$id] = $absences[$id];
            }
            $absences = $data;
            unset($data);
        }
        $this->set(compact('absences', 'employee_id', 'company_id', 'employeeName', 'absenceRequests'));
        $this->layout = '';
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfitFollowDates($profitId, $getDataByPath = false, $start = null, $end = null) {
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        $profit = $Model->find('list', array(
            'recursive' => -1, 'fields' => array('id'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
                'company_id' => $user['company_id'],
                'manager_id' => $user['id']
                //'OR' => array(
//                    'manager_id' => $user['id'],
//                    'manager_backup_id' => $user['id'],
//                    )
                )
            )
        );
        $this->loadModels('ProfitCenterManagerBackup');
        $backups = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $user['id']),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $profit = array_unique(array_merge($profit, $backups));
        $isAdmin = $this->employee_info['Role']['name'] == 'admin';
        if ($isAdmin) {
            $paths = $Model->generateTreeList(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        }
        elseif (!empty($profit)) {
            /*$paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
                    'lft >=' => $profit['ProfitCenter']['lft'],
                    'rght <=' => $profit['ProfitCenter']['rght']
                    )));*/
            $paths = array();
            foreach($profit as $_val)
            {
                $paths[] = $_val;
                $pathsTemp = $Model->children($_val);
                $pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
                $paths = array_merge($paths,$pathsTemp);
            }
            $paths = $Model->generateTreeList(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } else if ($this->employee_info['Role']['name'] == 'pm' && $this->employee_info['CompanyEmployeeReference']['control_resource']) {
            //find my pc
            $myPc = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array('fields' => 'profit_center_id', 'recursive' => -1, 'conditions' => array('employee_id' => $user['id'])));
            $children = Set::classicExtract($Model->children($myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id']), '{n}.ProfitCenter.id');
            if(!$children)$children = array();
            $children[] = $myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            //find my pc children
            $paths = $Model->generateTreeList(array('id' => $children), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
            //use @profitId
            if( !in_array($profitId, $children) )return false;
        } else {
            return false;
        }
        if (empty($profitId)) {
            $profitId = $profit['ProfitCenter']['id'];
        } elseif (!isset($paths[$profitId])) {
            return false;
        }
        $profit = $Model->find('first', array('recursive' => -1, 'conditions' => array('id' => $profitId)));
        $employees = array();
        if(!empty($profit)){
            $profit = array_shift($profit);

            //APPLY FOR CASE GET DATA BY PATH
            $_pc = $profit['id'];
            if($getDataByPath)
            {
                $listManagers = ClassRegistry::init('ProfitCenter')->children($_pc);
                $listManagers = Set::combine($listManagers,'{n}.ProfitCenter.id','{n}.ProfitCenter.manager_id');
                $pathOfPC = array_merge(array($_pc),array_keys($listManagers));
            }
            else
            {
                $pathOfPC = $_pc;
                $listManagers = $Model->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'manager_id'),
                    'conditions' => array(
                        'NOT' => array('manager_id' => null),
                        'parent_id' => $pathOfPC,
                        'company_id' => $this->employee_info['Company']['id'])
                ));
            }


            $list = array_merge(array($profit['manager_id']), $listManagers);
            $list = array_unique(array_filter($list));

            $employees = array_merge($list, $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                        'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                        'conditions' => array('profit_center_id' => $pathOfPC))));
            $end = strtotime('-2 days', $end);
            $employees = Set::combine($Model = ClassRegistry::init('ActivityRequest')->Employee->find('all', array(
                                'order' => 'first_name DESC',
                                'fields' => array('id', 'first_name', 'last_name'),
                                'recursive' => -1,
                                'conditions' => array(
                                    'id' => $employees,
                                    'NOT' => array(
                                        'OR' => array(
                                            array(
                                                'Employee.end_date <>' => '0000-00-00',
                                                'Employee.end_date IS NOT NULL',
                                                'Employee.end_date < '=> date('Y-m-d', $start)
                                            ),
                                            array(
                                                'Employee.start_date <>' => '0000-00-00',
                                                'Employee.start_date IS NOT NULL',
                                                'Employee.start_date > '=> date('Y-m-d', $end)
                                            )
                                        )
                                    )
                                )
                        ))
                        , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
            //END
            $_managers = array();
            foreach ($list as $key) {
                if (isset($employees[$key])) {
                    $_managers[$key] = $employees[$key] . '<strong> (Manager)</strong>';
                }
            }
            $employees = $_managers + $employees;
            if (!$isAdmin) {
                unset($employees[$user['id']]);
            }
        }
        return array($profit, $paths, $employees, $user);
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getProfits($profitId, $getDataByPath = false) {
        if (!($user = $this->_getEmpoyee())) {
            return false;
        }
        $Model = ClassRegistry::init('ProfitCenter');
        $profit = $Model->find('list', array(
            'recursive' => -1, 'fields' => array('id'),
            'order' => array('lft' => 'ASC'),
            'conditions' => array(
                'company_id' => $user['company_id'],
                'manager_id' => $user['id']
                //'OR' => array(
//                    'manager_id' => $user['id'],
//                    'manager_backup_id' => $user['id'],
//                    )
            )
            )
        );
        $this->loadModels('ProfitCenterManagerBackup');
        $backups = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $user['id']),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $profit = array_unique(array_merge($profit, $backups));
        $isAdmin = $this->employee_info['Role']['name'] == 'admin';
        if ($isAdmin) {
            $paths = $Model->generateTreeList(array('company_id' => $user['company_id']), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        }
        elseif (!empty($profit)) {
            /*$paths = $Model->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array(
                    'lft >=' => $profit['ProfitCenter']['lft'],
                    'rght <=' => $profit['ProfitCenter']['rght']
                    )));*/
            $paths = array();
            foreach($profit as $_val)
            {
                $paths[] = $_val;
                $pathsTemp = $Model->children($_val);
                $pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
                $paths = array_merge($paths,$pathsTemp);
            }
            $paths = $Model->generateTreeList(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
        } else if ($this->employee_info['Role']['name'] == 'pm' && $this->employee_info['CompanyEmployeeReference']['control_resource']) {
            //find my pc
            $myPc = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array('fields' => 'profit_center_id', 'recursive' => -1, 'conditions' => array('employee_id' => $user['id'])));
            $children = Set::classicExtract($Model->children($myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id']), '{n}.ProfitCenter.id');
            if(!$children)$children = array();
            $children[] = $myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            //find my pc children
            $paths = $Model->generateTreeList(array('id' => $children), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
            //use @profitId
            if( !in_array($profitId, $children) )return false;
        } else {
            return false;
        }
        if (empty($profitId)) {
            $profitId = $profit['ProfitCenter']['id'];
        } elseif (!isset($paths[$profitId])) {
            return false;
        }
        $profit = $Model->find('first', array('recursive' => -1, 'conditions' => array('id' => $profitId)));
        $employees = array();
        if(!empty($profit)){
            $profit = array_shift($profit);

            //APPLY FOR CASE GET DATA BY PATH
            $_pc = $profit['id'];
            if($getDataByPath)
            {
                $listManagers = ClassRegistry::init('ProfitCenter')->children($_pc);
                $listManagers = Set::combine($listManagers,'{n}.ProfitCenter.id','{n}.ProfitCenter.manager_id');
                $pathOfPC = array_merge(array($_pc),array_keys($listManagers));
            }
            else
            {
                $pathOfPC = $_pc;
                $listManagers = $Model->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'manager_id'),
                    'conditions' => array(
                        'NOT' => array('manager_id' => null),
                        'parent_id' => $pathOfPC,
                        'company_id' => $this->employee_info['Company']['id'])
                ));
            }


            $list = array_merge(array($profit['manager_id']), $listManagers);
            $list = array_unique(array_filter($list));

            $employees = array_merge($list, $Model->ProjectEmployeeProfitFunctionRefer->find('list', array(
                        'fields' => array('employee_id', 'employee_id'), 'recursive' => -1,
                        'conditions' => array('profit_center_id' => $pathOfPC))));

            $employees = Set::combine($Model = ClassRegistry::init('ActivityRequest')->Employee->find('all', array(
                                'order' => 'first_name DESC',
                                'fields' => array('id', 'first_name', 'last_name'),
                                'recursive' => -1, 'conditions' => array('id' => $employees)))
                            , '{n}.Employee.id', array('{0} {1}', '{n}.Employee.first_name', '{n}.Employee.last_name'));
            //END
            $_managers = array();
            foreach ($list as $key) {
                if (isset($employees[$key])) {
                    $_managers[$key] = $employees[$key] . '<strong> (Manager)</strong>';
                }
            }
            $employees = $_managers + $employees;
            if (!$isAdmin) {
                unset($employees[$user['id']]);
            }
        }
        return array($profit, $paths, $employees, $user);
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if (!array_filter($params)) {
            $params['week'] = date('W');
            $params['year'] = date('Y');
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            $this->Cookie->write('currentWeek', $params['week']);
            if (($week == 1 && $params['week'] <= 53) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
            $currentWeek = $this->Cookie->read('currentWeek');
            // kiem tra de giu lai tuan neu thay doi team.
            // lay tuan cuoi va tuan dau cua 1 thang. neu $currentWeek o giua thi giu nguyen tuan do.
            $newFirstWeekOfMonth = intval(date('W', mktime(0, 0, 0, $this->params['url']['month'], 1, $this->params['url']['year'])));
            $newLastWeekOfMonth = intval(date('W', mktime(0, 0, 0, $this->params['url']['month'], date('d', strtotime('last day of last month', strtotime('01-' .$this->params['url']['month'].'-'.$this->params['url']['year'] ))), $this->params['url']['year'])));
            if(!empty($currentWeek) && ($currentWeek <= $newLastWeekOfMonth) && ($currentWeek >= $newFirstWeekOfMonth)){
                $date = new DateTime();
                $date->setISODate($params['year'], $currentWeek);
                $date = strtotime($date->format('Y-m-d'));
            }
        }
        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
		$_end = strtotime('next sunday', $_start);
		
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParamsMonth($byWeek = false, $firstMonth = false, $firstYear = false) {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if (!array_filter($params)) {
            $params['week'] = date('W');
            $params['year'] = date('Y');
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 53) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        } else if( $params['year'] ){
            $_start = mktime(0, 0, 0, 1, 1, $params['year']);
            $_end = mktime(0, 0, 0, 12, 31, $params['year']);
            $this->set(compact('_start', '_end'));
            return array($_start, $_end);
        }
        if (empty($date)) {
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        if($firstMonth == true){
            $date = strtotime('01-'.date('m-Y', $date));
        }
		$text_last_day_of_week = 'friday';
		$workdays = ClassRegistry::init('Workday')->getOptions($this->employee_info['Company']['id']);
		if(!empty($workdays)){
			foreach($workdays as $key => $isWorking){
				if($isWorking == 1) $text_last_day_of_week = $key;
			}
		}
        if($byWeek == false){
            $_start = strtotime(date('m',$date).'/1/'.date('Y',$date));
            $_end = strtotime(date('m',$_start)."/".cal_days_in_month(CAL_GREGORIAN, date('m',$_start), date('Y',$_start))."/".date('Y',$_start));
        } else {
            $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
            /**
             * HuuPc add, calculate last day of month
             */
            $_month = date('m', $date) + 1;
            $_year = date('Y', $date);
            if($_month > 12){
                $_month = 1;
                $_year++;
            }
            $_date = mktime(0, 0, 0, $_month, 1, $_year);
            $mondayOfNextMonth = (date('w', $_date) == 1) ? $_date : strtotime($text_last_day_of_week, $_date);
            $_end = mktime(0, 0, 0, date('m', $mondayOfNextMonth), date('d', $mondayOfNextMonth), date('Y', $mondayOfNextMonth));
            //$_end = strtotime('next sunday', $_start);
        }
        if($firstYear == true){
            $date = strtotime('01-01-'.date('Y', $date));
            $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
            $_year = date('Y', $date) + 1;
            $_date = mktime(0, 0, 0, 1, 1, $_year);
            $mondayOfNextMonth = (date('w', $_date) == 1) ? $_date : strtotime($text_last_day_of_week, $_date);
            $_end = mktime(0, 0, 0, date('m', $mondayOfNextMonth), date('d', $mondayOfNextMonth), date('Y', $mondayOfNextMonth));
        }
        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams2() {
        $year = date('Y');
        if (!empty($this->params['url']['year'])) {
            $year = intval($this->params['url']['year']);
        }
        $_start = strtotime($year . '-1-1');
        $_end = strtotime($year . '-12-31');

        if (empty($_start) || empty($_end)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

     /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParamChecks() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 53) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }
        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_startCheck = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_endCheck = strtotime('next sunday', $_startCheck);

        $this->set(compact('_startCheck', '_endCheck'));
        return array($_startCheck, $_endCheck);
    }

    /**
     * Lay cac ngay lam viec theo config working day o admin
     */
    private function _workingDayFollowConfigAdmin($start = null, $end = null, $workdayAdmins = null, $listWeeks = array()){
        $weeks = $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $day = strtolower(date('l', $start));
                if($workdayAdmins[$day] != 0){
                    if(!empty($listWeeks)){
                        $_week = date('W-Y', $start);
                        if(!empty($listWeeks[$_week])){
                            $results[$start] = $start;
                            if(!isset($weeks[$_week][$start])){
                                $weeks[$_week][$start] = 0;
                            }
                            $weeks[$_week][$start] = $start;
                        }
                    } else {
                        $results[$start] = $start;
                    }
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return array($weeks, $results);
    }

    /**
     * Chia tuan theo thang.
     * Phan chia cac tuan tu thu 2 -> chu nhat cho 1 thang
     */
    private function _splitWeekOfMonth($start = null, $end = null){
        $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $checkDay = strtolower(date('l', $start));
                if($checkDay === 'monday'){
                    $_end = strtotime('next sunday', $start);
                    $results[$start] = $_end;
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return $results;
    }
    function available(){
        if(!empty($_GET)){
            set_time_limit(0);
            $this->loadModels('AbsenceComment', 'ActivityForecast', 'ActivityRequest', 'AbsenceRequestConfirm', 'ActivityRequestConfirm', 'ProjectEmployeeProfitFunctionRefer', 'Employee', 'CompanyEmployeeReference', 'ProfitCenter', 'HistoryFilter');
            $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
            $employeeName = $this->employee_info['Employee'];
            $profit = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id']),
                'fields' => array('id', 'name')
            ));
            // save HistoryFilter.
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'available',
                    'employee_id' => $employeeName['id']
                )
            ));
            if(!empty($check)){
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'params' => $_SERVER['REQUEST_URI']
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'available',
                    'employee_id' => $employeeName['id'],
                    'params' => $_SERVER['REQUEST_URI']
                ));
            }
            $_profit = !empty($_GET['pro']) ? $_GET['pro'] : array();
            $_emp = !empty($_GET['emp']) ? $_GET['emp'] : array();

            $month = !empty($_GET['month']) ? $_GET['month'] : date('m');
            $year = !empty($_GET['year']) ? $_GET['year'] : date('Y');
            $_start = strtotime('01-'.$month.'-'.$year);
            $_end = strtotime('01-'.$month.'-'.$year);
            $_end = strtotime('last day of this month', $_end);
            $profitOfEmployees = array();
            $conditions = array(
                'company_id' => $employeeName['company_id'],
				array(
					'OR' => array(
						array('start_date IS NULL'),
						array('start_date' => '0000-00-00'),
						array('start_date <=' => date('Y-m-d', $_end))
					),
				),
                array(
                    'OR' => array(
                        array('end_date IS NULL'),
                        array('end_date' => '0000-00-00'),
                        array('end_date >=' => date('Y-m-d', $_start))
                    )
                )
            );
            if(!empty($_profit) && !empty($_emp)){
                $conditions['OR'] = array(
                    'profit_center_id' => $_profit,
                    'Employee.id' => $_emp
                );
            } else {
                if(!empty($_profit)){
                    $conditions['profit_center_id'] = $_profit;
                }
                if(!empty($_emp)){
                    $conditions['Employee.id'] = $_emp;
                }
            }
            // employee hien thị.
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('Employee.id', 'fullname'),
                'conditions' => $conditions,
                'order' => array('fullname' => 'ASC')
            ));
            // all employee of company
            $listEmployees = $this->Employee->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'fullname'),
                'conditions' => array(
                    'company_id' => $employeeName['company_id'],
                    'OR' => array(
                        array('end_date IS NULL'),
                        array('end_date' => '0000-00-00'),
                        array('end_date >=' => date('Y-m-d', $_start))
                    )
                ),
                'order' => array('fullname')
            ));
            /**
             * Lay cac profit manager
             */
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $employeeName['company_id'], 'NOT' => array('manager_id' => null)),
                'fields' => array('id', 'manager_id')
            ));
            $employFinish = array();
            if(!empty($profitCenters)){
                foreach($profitCenters as $emp){
                    if (isset($employees[$emp]) && !isset($employFinish[$emp])) {
                        $employees[$emp] = $employees[$emp] . '<strong> (Manager)</strong>';
                        $employFinish[$emp] = $emp;
                    }
                }
            }

            // end get employee end day > current day
            $profitOfEmployees = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
                'recursive' => -1,
                'conditions' => array('employee_id' => array_keys($employees)),
                'fields' => array('employee_id', 'profit_center_id')
            ));
            $dayHasValidations = array();
            $requestConfirms = array();
            $totalWeek = 0;
            $requestConfirms = $this->ActivityRequestConfirm->find('all', array(
                'recursive' => -1,
                'fields' => array('start', 'end', 'employee_id', 'status'),
                'conditions' => array(
                    'status' => array(0, 2),
                    'employee_id' => array_keys($employees)
                )
            ));
            if(!empty($requestConfirms)){
                foreach($requestConfirms as $requestConfirm){
                    $dx = $requestConfirm['ActivityRequestConfirm'];
                    if($dx['status'] !== false){
                        while($dx['start'] <= $dx['end']){
                            if(!isset($dayHasValidations[$dx['employee_id']][$dx['start']])){
                                $dayHasValidations[$dx['employee_id']][$dx['start']] = '';
                            }
                            $dayHasValidations[$dx['employee_id']][$dx['start']] = $dx['start'];
                            $dx['start'] = mktime(0, 0, 0, date("m", $dx['start']), date("d", $dx['start'])+1, date("Y", $dx['start']));
                        }
                    }
                }
            }
            /**
             * Cac ngay lam viec trong tuan
             */
            $workdays = ClassRegistry::init('Workday')->getOptions($employeeName['company_id']);
            /**
             * Lay ngay nghi, ngay le trong tuan
             */
            $holidays = ClassRegistry::init('Holiday')->get2($employeeName['company_id'], $_start, $_end);
            foreach($holidays as $time => $h){
                $day = strtolower(date('l', $time));
                if($workdays[$day] == 0){
                    unset($holidays[$time]);
                }
            }

            $constraint = $this->AbsenceRequest->getConstraint()->getOptions($employeeName['company_id']);
            $conditions = array();
            if (!empty($this->data['id'])) {
                $conditions['updated <'] = $this->data['ls'] + 1E9;
            }
            $_requests = $this->AbsenceRequest->find("all", array(
                                'recursive' => -1,
                                "conditions" => $conditions + array('date BETWEEN ? AND ?' => array($_start, $_end), 'employee_id' => array_keys($employees))));
            $requests = Set::combine($_requests, '{n}.AbsenceRequest.date', '{n}.AbsenceRequest', '{n}.AbsenceRequest.employee_id');
            $listWeeks = array();
            if(!empty($_requests) ){
                foreach($_requests as $_data){
                    $dx = $_data['AbsenceRequest'];
                    $date = $dx['date'];
                    $_week = date('W-Y', $date);
                    $listWeeks[$_week] = $_week;
                }
            }
            /**
             * Danh sach cac ngay lam trong tuan/thang/nam theo config working day o admin
             */
            list($multiWeeks, $listWorkingDays) = $this->_workingDayFollowConfigAdmin($_start, $_end, $workdays, $listWeeks);
            if ($conditions) {
                $employees = array_intersect_key($employees, $requests);
            }
            $_comments = $this->AbsenceComment->find("all", array(
                'recursive' => -1,
                "conditions" => array('date BETWEEN ? AND ?' => array($_start, $_end),
                    'employee_id' => array_unique(array_merge(array($employeeName['id']), array_keys($employees))))));
            $comments = array();
            foreach ($_comments as $comment) {
                $comment = array_shift($comment);
                $comments[$comment['employee_id']][$comment['date']][$comment['time'] ? 'pm' : 'am'][$comment['id']] = array(
                    'user_id' => $comment['user_id'],
                    'created' => date('d-m-Y H:i', $comment['created']),
                    'text' => $comment['text']);
            }
            $absences = $this->AbsenceRequest->getAbsences($employeeName['company_id']);
            $absences['-1'] = array(
                'id' => '-1',
                'print' => __('Provisional day off', true)
            );
            $this->loadModel('FavoryAbsence');
            $favory = $this->FavoryAbsence->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employeeName['id'],
                    'company_id' => $employeeName['company_id']
                )
            ));
            $this->set(compact('profitOfEmployees', 'dayHasValidations', 'listWorkingDays', 'requests', 'profit', 'constraint', 'absences', 'workdays', 'employees', 'employeeName', 'holidays', 'requestConfirms', '_start', '_end', '_profit', 'employees', '_emp', 'month', 'year', 'favory', 'listEmployees'));
        }
    }
    public function saveFavory(){
        if(!empty($_POST)){
            $company_id = $this->employee_info['Company']['id'];
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModels('FavoryAbsence', 'ProfitCenter', 'Employee');
            $profit = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
            $employee = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'fullname')
            ));
            $this->FavoryAbsence->create();
            $title = !empty($_POST['name']) ? $_POST['name'] : '';
            $profit_id = $url = $profitName = $empName = $emp_id = '';
            if(!empty($_POST['profit'])){
                foreach ($_POST['profit'] as $value) {
                    if(empty($profit_id)){
                        $profit_id .= $value;
                    } else {
                        $profit_id .= ', ' . $value;
                    }
                    if(empty($profitName)){
                        $profitName .= $profit[$value];
                    } else {
                        $profitName .= ', ' . $profit[$value];
                    }
                    $url .= '&pro%5B%5D='.$value;
                }
            }
            if(!empty($_POST['emp'])){
                foreach ($_POST['emp'] as $value) {
                    if(empty($emp_id)){
                        $emp_id .= $value;
                    } else {
                        $emp_id .= ', ' . $value;
                    }
                    if(empty($empName)){
                        $empName .= $employee[$value];
                    } else {
                        $empName .= ', ' . $employee[$value];
                    }
                    $url .= '&emp%5B%5D='.$value;
                }
            }
            $save = array(
                'title' => $title,
                'profit_id' => $profit_id,
                'emp_id' => $emp_id,
                'url' => $url,
                'employee_id' => $employee_id,
                'company_id' => $company_id
            );
            $this->FavoryAbsence->save($save);
            $result['id'] = $this->FavoryAbsence->getLastInsertID();
            $result['title'] = $title;
            $result['profit'] = $profitName;
            $result['employee'] = $empName;
            $result['url'] = $url;
            echo json_encode($result);
            exit;
        }
    }
    public function deleteFavory(){
        if(!empty($_POST)){
            $id = $_POST['id'];
            $this->loadModel('FavoryAbsence');
            $this->FavoryAbsence->delete($id);
            die(true);
        }
        die();
    }
}
