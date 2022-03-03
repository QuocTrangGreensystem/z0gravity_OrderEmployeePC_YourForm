<?php
/**
 * z0 Gravity�
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class AbsencesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Absences';
	var $components = array('MultiFileUpload', 'PImage');
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null, $country_id = null) {
        $this->loadModel('AbsenceHistory');
        list($_start, $_end) = $this->_parseParams();
        $absenceHistories = array();
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Absence->Company->find('list');
            $this->viewPath = 'absences' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->Absence->cacheQueries = true;
            $conditions['company_id'] = $company_id;
            $this->loadModel('Company');
            $mutil_country = $this->Company->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'multi_country'),
                'conditions' => array('Company.id' => $company_id)
            ));
            $mutil_country = !empty($mutil_country) ? $mutil_country['Company']['multi_country'] : 0;
            if($mutil_country) {
                if(empty($country_id)){
                    $country_id = $this->employee_info['Employee']['country_id'];
                }
                $conditions['country_id'] = $country_id;
            } else {
                $conditions['OR'] = array(
                    array('country_id is NULL'),
                    array('country_id' => 0)
                );
            }
            $absences = $this->Absence->find("all", array(
                'recursive' => -1,
                'order' => array('weight' => 'ASC'),
                "conditions" => $conditions
            ));
            if(!empty($absences)){
                $i = 1;
                foreach($absences as $key => $absence){
                    $dx = $absence['Absence'];
                    $this->Absence->id = $dx['id'];
                    $this->Absence->save(array('weight' => $i));
                    $absences[$key]['Absence']['weight'] = $i;
                    $i++;
                }
            }
            /**
             * Lay ngay hien tai hien tai
             * Va 1 nam ve truoc
             */
            //$currentDate = time();
            //Test voi ngay gia lap.
            $currentDate = strtotime('31-'.date('m-Y', $_start));
            $lastYear = mktime(0, 0, 0, date("m", $currentDate), date("d", $currentDate)-365, date("Y", $currentDate));
            $_conditions = array(
                'AbsenceHistory.begin BETWEEN ? AND ?' => array(date('Y-m-d', $lastYear), date('Y-m-d', $currentDate)),
                'AbsenceHistory.company_id' => $company_id
            );
            if($mutil_country){
                $_conditions['AbsenceHistory.country_id'] = $country_id;
            }
            $absenceHistories = $this->AbsenceHistory->find('all', array(
                'recurisve' => -1,
                'conditions' => $_conditions,
                'fields' => array('absence_id', 'total', 'begin')
            ));
            $absenceHistories = !empty($absenceHistories) ? Set::combine($absenceHistories, '{n}.AbsenceHistory.absence_id', '{n}.AbsenceHistory') : array();
			$this->set(compact('absences', 'companyName', 'absenceHistories', 'mutil_country'));
        }
        $this->loadModel('Country');
        $list_country = $this->Country->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'name'),
            'conditions' => array('company_id' => $company_id)
        ));
        $typeSelect = $country_id;
        $this->set(compact('companies', 'company_id', 'absenceHistories', 'list_country', 'typeSelect'));
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
                $last = $this->Absence->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('Absence.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->Absence->id = $last['Absence']['id'];
                    $this->Absence->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $this->loadModel('AbsenceHistory');
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            //$this->data['begin'] = date('d-m-Y',strtotime($this->data['begin'].'-'.date("Y")));
            $this->Absence->create();
            $oldDatas = array();
            if (!empty($this->data['id'])) {
                $this->Absence->id = $this->data['id'];
                $oldDatas = $this->Absence->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Absence.id' => $this->data['id']),
                    'fields' => array('id', 'total', 'begin', 'company_id')
                ));
            }
            $data = array(
                'activated' => (isset($this->data['activated']) && $this->data['activated'] == 'yes'),
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes'),
                'recursive' => (isset($this->data['recursive']) && $this->data['recursive'] == 'yes'),
                'document' => (isset($this->data['document']) && $this->data['document'] == 'yes'),
            );
            if($data['recursive']){
                // khong co end of period. thoi gian lap di lap lai la 1
                $this->data['end_of_period'] = '';
            }
            foreach (array('begin', 'end_of_period') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->Absence->convertTime($this->data[$key]);
                }
            }
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            //$checkRequest = $this->_validatedInputData($this->Absence->id, $this->data['total']);
            $checkRequest = 'true';
            if($checkRequest == 'true'){
                $datas = array_merge($this->data, $data);
                if ($this->Absence->save(array_merge($this->data, $data))) {
                    $lastId = $this->Absence->id;
                    if(!empty($datas['begin'])){
                        list($y, $m, $d) = !empty($datas['begin']) ? explode('-', $datas['begin']) : array();
                        $_saved = array(
                            'absence_id' => $lastId,
                            'total' => $datas['total'],
                            'begin' => $datas['begin'],
                            'day' => $d,
                            'month' => $m,
                            'year' => $y,
                            'company_id' => $datas['company_id']
                        );
                        $tmp = $this->AbsenceHistory->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'AbsenceHistory.absence_id' => $lastId,
                                //'AbsenceHistory.month' => $m,
                                'AbsenceHistory.year' => $y,
                                'AbsenceHistory.company_id' => $datas['company_id']
                            ),
                            'fields' => array('id')
                        ));
                        $this->AbsenceHistory->create();
                        if(!empty($tmp) && !empty($tmp['AbsenceHistory']['id'])){
                            $this->AbsenceHistory->id = $tmp['AbsenceHistory']['id'];
                        }
                        $this->AbsenceHistory->save($_saved);
                    } else {
                        if(!empty($oldDatas)){
                            list($y, $m, $d) = !empty($oldDatas['Absence']['begin']) ? explode('-', $oldDatas['Absence']['begin']) : array();
                            $tmp = $this->AbsenceHistory->find('first', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'AbsenceHistory.absence_id' => $oldDatas['Absence']['id'],
                                    'AbsenceHistory.begin' => $oldDatas['Absence']['begin'],
                                    'AbsenceHistory.month' => $m,
                                    'AbsenceHistory.year' => $y,
                                    'AbsenceHistory.company_id' => $oldDatas['Absence']['company_id']
                                ),
                                'fields' => array('id')
                            ));
                            if(!empty($tmp) && !empty($tmp['AbsenceHistory']['id'])){
                                $this->AbsenceHistory->delete($tmp['AbsenceHistory']['id']);
                            }
                        }
                    }
                    $result = true;
                    $this->Session->setFlash(__('The Absence has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Absence could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->Absence->id;
            } else {
                $this->Session->setFlash(sprintf(__('The "%s" had the request larger then "%s". The data could not be saved. Please, try again.', true)
                            , $this->data['name'], $this->data['total']));
            }
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
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for absence', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
		$absence = $this->Absence->read(null, $id);
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'Absence'));
        $allowDeleteAbsence = $this->_absenceIsUsing($id);
        if($check && ($allowDeleteAbsence == 'true')){
            if ($this->Absence->delete($id)) {
                $this->Session->setFlash(__('Absence has been deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Cannot be deleted, type of absence already selected', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->redirect(array('action' => 'index', $company_id));
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
        $companyName = $this->Absence->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

    function import_csv() {
        //$this->autoRender = false;
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {

            $this->components = array('MultiFileUpload');
            $this->Component->_loadComponents($this);
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
            $reVal = $this->MultiFileUpload->upload();

            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Update' => array(),
                        'Error' => array()
                    );
                    $default = array(
                        'Employee ID' => '', // r
                        'Code 1' => '', // r
                        'Year' => '', // r
                        'Number By Year' => '', // r
                    );
                    $this->loadModel('Absences');
                    $this->loadModel('Employee');
                    $this->Absences->cacheQueries = true;
                    $this->Employee->cacheQueries = true;

                    $validate = array('Employee ID', 'Code 1', 'Year', 'Number By Year');
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    foreach ($csv->data as $row) {
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        $error = false;
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, 0), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));

                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);

                            if (empty($row[$value])) {
                                if($value!=0){
                                    $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                                }
                            }
                        }

                        if (empty($row['error'])) {
                            // Type
                            if (!empty($row['Code 1'])) {
                                $tmp = $this->Absences->find('first', array(
                                    'recursive' => -1,
                                    'fields' => array('id','begin'),
                                    'conditions' => array('code1' => $row['Code 1'])));
                                if (empty($tmp)) {
                                    $row['error'][] = __('The Absences Code 1 not found', true);
                                } else {
                                    $row['data']['absence_id'] = $tmp['Absences']['id'];
                                    if (!empty($row['Year'])) {
                                        $row['data']['year'] = $row['Year'];
                                    }else{
                                        $row['data']['year'] = date('Y',strtotime($tmp['Absences']['begin']));
                                    }
                                    if ($row['Year'] != date('Y',strtotime($tmp['Absences']['begin']))) {
                                        $row['data']['begin'] = $row['Year'].'-'.date('m-d',strtotime($tmp['Absences']['begin']));
                                    }else{
                                       $row['data']['begin'] = $tmp['Absences']['begin'];
                                    }

                                }
                            }

                            // Begin date
                            //if (!empty($row['Begin Of Period'])) {
//                                $row['data']['begin'] = str_replace('/', '-', $row['Begin Of Period']);
//                                if (!$row['data']['begin']) {
//                                    $row['error'][] = __('The start date is invalid.', true);
//                                    $error = true;
//                                }
//                            }

                            // number by year
                            //if (!empty($row['Number By Year'])) {
                                //debug($row['Number By Year']);
                                $row['data']['total'] = $row['Number By Year'];
                                //$check = $this->_validatedImport($row);
//                                if($check == 'true'){
//
//                                } else {
//                                    $row['error'][] = __('The numbers had the request larger number input.', true);
//                                    $error = true;
//                                }
                           // }

                            //year
                            // if (!empty($row['Year'])) {
                            //     $row['data']['year'] = $row['Year'];
                            // }

                            //Employee
                            if(!empty($row['Employee ID'])){
                                //$row['data']['employee_id'] = $row['Employee ID'];
                                $tmps = $this->Employee->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array('Employee.code_id' => $row['Employee ID']),
                                    'fields' => array('id')
                                ));
                                if (!empty($tmps)) {
                                    $row['data']['employee_id'] = $tmps['Employee']['id'];
                                } else {
                                    $row['error'][] = __('The Employee not found.', true);
                                }
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
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function save_file_import() {
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('action' => 'index'));
                }
                $complete = 0;
                foreach ($import as $data) {
                    $this->loadModel('EmployeeAbsence');
                    $tmp = $this->EmployeeAbsence->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'EmployeeAbsence.absence_id' => $data['absence_id'],
                                'EmployeeAbsence.employee_id' => $data['employee_id'],
                                'EmployeeAbsence.year' => $data['year']
                            ),
                            'fields' => array('id')
                        ));
                    //$data['begin'] = strtotime($data['begin']);
                    //$data['begin'] = date('Y-m-d', $data['begin']);
                    if(!empty($tmp)){
                        $this->EmployeeAbsence->id = $tmp['EmployeeAbsence']['id'];
                    } else {
                        $this->EmployeeAbsence->create();
                    }
                    if($this->EmployeeAbsence->save($data)){
                        $complete++;
                    }
                }
                $this->Session->setFlash(sprintf(__('The employees has been imported %s/%s.', true), $complete, count($import)));
                $this->redirect(array('action' => 'index'));
            } else {
                App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
                $csv = new parseCSV();
                // export
                $type = '';
                if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
                    $type = 'Error';
                if ($this->data['Import']['type'] == 'Create' && !empty($this->data['Create']['export']))
                    $type = 'Create';
                if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
                    $type = 'Update';

                if (!empty($type)) {
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = $key;
                    }
                    $csv->output($type . ".csv", $this->data[$type]['export'], $header, ",");
                    //$csv->output($type . ".csv", $this->data[$type]['export'], array_values($this->_defaultCsv()));
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
        exit;
    }

    public function _validatedInputData($absenId = null, $number = null){
        $this->loadModel('AbsenceRequest');

        $check = 'true';
        if($absenId == null){
            $check  = 'true';
        } else {
            $getBegin = $this->Absence->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('Absence.id' => $absenId),
                    'fields' => array('begin', 'total')
                ));
            $getTotal = isset($getBegin['Absence']['total']) ? $getBegin['Absence']['total'] : 0;
            $getBegin = ($getBegin['Absence']['begin'] != '0000-00-00') ? $getBegin['Absence']['begin'] : '';
            if(!empty($getTotal) && !empty($getBegin)){
                $start = strtotime($getBegin);
                $end = mktime(0, 0, 0, date("m", $start), date("d", $start)+365, date("Y", $start));
                $_requests = $this->AbsenceRequest->find("all", array(
                        'recursive' => -1,
                        "conditions" => array(
                                //'date BETWEEN ? AND ?' => array($start, $end),
                                'OR' => array(
                                    'absence_am' => $absenId,
                                    'absence_pm' => $absenId
                                ))
                    ));
                $requests = array();
                foreach ($_requests as $request) {
                    $request = array_shift($request);
                    foreach (array('am', 'pm') as $type) {
                        if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'validated') {
                            if (!isset($requests[$request['employee_id']][$request['absence_' . $type]])) {
                                $requests[$request['employee_id']][$request['absence_' . $type]] = 0;
                            }
                            $requests[$request['employee_id']][$request['absence_' . $type]] += 0.5;
                        }
                    }
                }
                if(!empty($requests)){
                    foreach($requests as $request){
                        foreach($request as $request){
                            if($request > $number){ // s? request l?n hon s? nh?p v�o n�n d?ng l?i
                                $check  = 'false';
                            }
                        }
                    }
                } else {
                    $check  = 'true';
                }
            } else {
                $check  = 'true';
            }
        }
        return $check;
    }

    public function _validatedImport(array $datas){
        unset($datas['data']);
        unset($datas['error']);
        $check = 'true';
        // get id of employee
        $this->loadModel('Employee');
        $employeeId = $this->Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array('code_id' => $datas['Employee ID']),
                'fields' => array('id')
            ));
        $employeeId = isset($employeeId) ? $employeeId['Employee']['id'] : '';
        //get id of absence
        $this->loadModel('Absence');
        $absenceId = $this->Absence->find('first', array(
                'recursive' => -1,
                'conditions' => array('code1' => $datas['Code 1']),
                'fields' => array('id')
            ));
        $absenceId = isset($absenceId) ? $absenceId['Absence']['id'] : '';
        //get absence of employee
        $this->loadModel('EmployeeAbsence');
        $employeeAbsence = $this->EmployeeAbsence->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'absence_id' => $absenceId,
                    'employee_id' => $employeeId,
                    'year' => $datas['Year']
                )
            ));
        if(count($employeeAbsence) == 0){
             $check = 'true';
        } else {
            $this->loadModel('AbsenceRequest');
            $start = strtotime($employeeAbsence['EmployeeAbsence']['begin']);
            $end = mktime(0, 0, 0, date("m", $start), date("d", $start)+365, date("Y", $start));
            $_requests = $this->AbsenceRequest->find("all", array(
                    'recursive' => -1,
                    "conditions" => array(
                            //'date BETWEEN ? AND ?' => array($start, $end),
                            'employee_id' => $employeeId,
                            'OR' => array(
                                'absence_am' => $absenceId,
                                'absence_pm' => $absenceId
                            ))
                ));
            $requests = array();
            foreach ($_requests as $request) {
                $request = array_shift($request);
                foreach (array('am', 'pm') as $type) {
                    if ($request['absence_' . $type] && $request['absence_' . $type] != '-1' && $request['response_' . $type] == 'validated') {
                        if (!isset($requests[$request['employee_id']][$request['absence_' . $type]])) {
                            $requests[$request['employee_id']][$request['absence_' . $type]] = 0;
                        }
                        $requests[$request['employee_id']][$request['absence_' . $type]] += 0.5;
                    }
                }
            }
            if(!empty($requests)){
                foreach($requests as $request){
                    foreach($request as $request){
                        if($request > $datas['Number By Year']){ // so request lon hon so nhap vao nen dung lai
                            $check  = 'false';
                        }
                    }
                }
            } else {
                $check  = 'true';
            }
        }
        return $check;
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
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 52) || ($week != 1 && $params['week'] <= $week)) {
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
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);

        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }
       /**
     *  Kiem tra absence da co su dung
     *  @return boolean
     *  @access private
     */

    private function _absenceIsUsing($id = null){
        $this->loadModel('EmployeeAbsence');
        $this->loadModel('AbsenceHistory');
        $this->loadModel('AbsenceRequest');
        $checkEmployeeAbsence = $this->EmployeeAbsence->find('count', array(
                'recursive' => -1,
                'conditions' => array('EmployeeAbsence.absence_id' => $id)
            ));
        $checkAbsenceHistory = $this->AbsenceHistory->find('count', array(
                'recursive' => -1,
                'conditions' => array('AbsenceHistory.absence_id' => $id)
            ));
        $checkAbsenceRequest = $this->AbsenceRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array('OR'=>array('AbsenceRequest.absence_am' => $id,'AbsenceRequest.absence_pm' => $id))
            ));
        $allowDeleteAbsence= 'true';
        if($checkEmployeeAbsence != 0 || $checkAbsenceHistory != 0 || $checkAbsenceRequest != 0){
            $allowDeleteAbsence = 'false';
        }

        return $allowDeleteAbsence;
    }
	function attached_documents($company_id = null)
	{
		$companyName = $this->_getCompany($company_id);
		$company_id = $companyName['Company']['id'];
		$this->loadModel('AbsenceAttachment');
		$data = $this->AbsenceAttachment->find('first',array(
			'recursive' => -1,
			'conditions' => array('company_id' => $company_id),
			//'limit' => 1
		));

		//$data = Set::ClassicExtract($data,'{n}');
		$data = $data['AbsenceAttachment'];
		if(empty($data))
		{
			$this->AbsenceAttachment->create();
			$data = array(
				'hr_email' => 'RG@monext.net',
				'document_mandatory' => 0,
				'attachment' => '',
				'message' => '',
				'company_id' => $company_id,
			);
			$this->AbsenceAttachment->save($data);
			$data['id'] = $this->AbsenceAttachment->getLastInsertID();
		}
		$this->set(compact('companyName','company_id','data'));
	}

	public function update_document($id = null, $company_id = null){
        $this->layout = false;
        $result = '';
		$this->loadModel('AbsenceAttachment');
        if(!empty($_FILES)){
			$company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
			$path = FILES . 'absence_attachment' . DS;
			$path .= $company['Company']['dir'] . DS;
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = true;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "doc,docx,xlsx,xls";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment)) {
				$oldAttachment = $this->AbsenceAttachment->find('first',array(
					'recursive' => -1,
					'fields' => array('attachment'),
					'conditions' => array(
						'company_id' => $company_id,
						'id' => $id
					)
				));
				$oldFile = $oldAttachment['AbsenceAttachment']['attachment'];
				$oldAttachment = $path . $oldAttachment['AbsenceAttachment']['attachment'];
				if(file_exists($oldAttachment) && $oldFile != '')
				{
					unlink($oldAttachment);
				}
				$this->edit_attachment($id,$company_id,'attachment',$attachment['attachment']['encrypt_name'],true);
                $result = array('success'=>1,'message'=>'Attached document completed!','file'=>$attachment['attachment']['encrypt_name']);
            } else {
                $result = array('success'=>-1,'message'=>'Attached document failed!','file'=>'');
            }
        }
		echo json_encode($result);
        /*if($check == 'false'){
            $this->redirect(array('action' => 'my_profile'));
        }*/
        //$this->redirect(array('action' => 'edit', $id, $company_id));
		exit;
    }
	public function edit_attachment($id = null, $company_id = null, $field = null, $value = null, $private = false){
		$this->loadModel('AbsenceAttachment');
		$check = $this->AbsenceAttachment->find('count',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'id' => $id
			)
		));
		if($check)
		{
			 $this->AbsenceAttachment->updateAll(
                array('AbsenceAttachment.'.$field => "'$value'"),
                array('AbsenceAttachment.company_id' => $company_id,'AbsenceAttachment.id ' => $id)
            );
		}
		if($private)
		{
			return true;
		}
		else
		{
			exit;
		}
	}
	function config_capacities($company_id = null)
	{
		$companyName = $this->_getCompany($company_id);
		$company_id = $companyName['Company']['id'];
		$this->loadModel('AbsenceAttachment');
		// $data = $this->AbsenceAttachment->find('first',array(
			// 'recursive' => -1,
			// 'conditions' => array('company_id' => $company_id),
		// ));

		//$data = Set::ClassicExtract($data,'{n}');

		$data = array();
		$this->set(compact('companyName','company_id','data'));
	}
}
?>
