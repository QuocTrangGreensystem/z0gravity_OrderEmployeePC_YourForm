<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class EmployeeAbsencesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'EmployeeAbsences';
    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($employee_id = null, $company_id = null) {
        $canModified = true;
        list($_start, $_end) = $this->_parseParams2();
        $employeeName = $this->_getEmpoyee($employee_id, $company_id, $canModified);
        //if (empty($employeeName)) {
//            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
//            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
//        }
        $this->EmployeeAbsence->Absence->cacheQueries = true;
        $this->EmployeeAbsence->cacheQueries = true;
        $absences = $this->EmployeeAbsence->Absence->find("all", array(
            'recursive' => -1,
            'order' => array('weight' => 'ASC'),
            "conditions" => array('company_id' => $company_id, 'display' => true)));
        $start = date('Y', $_start);
        $employeeAbsences = $this->EmployeeAbsence->find('all', 
                array(
                    'conditions' => array(
                    'employee_id' => $employee_id,
                    'year' => $start
                ),
                'recursive' => -1
        ));
        $employeeAbsences = Set::combine($employeeAbsences, '{n}.EmployeeAbsence.absence_id', '{n}.EmployeeAbsence');
        $this->set(compact('absences', 'employee_id', 'company_id', 'employeeName', 'employeeAbsences', 'canModified', 'start', 'absenceHistories'));
    }
    
    function checkAbsence($absenceId, $employee, $year){
        $this->layout = 'ajax';
        $this->loadModel('EmployeeAbsence');
        $employeeAbsences = $this->EmployeeAbsence->find('first', array(
                'recursive' => -1,
                'conditions' => array('absence_id' => $absenceId, 'employee_id' => $employee, 'year' => $year),
                'fields' => array('begin', 'total')
            ));
        $employeeAbsences['EmployeeAbsence']['begin'] = isset($employeeAbsences['EmployeeAbsence']['begin']) ? $employeeAbsences['EmployeeAbsence']['begin'] : '';
        $employeeAbsences['EmployeeAbsence']['total'] = isset($employeeAbsences['EmployeeAbsence']['total']) ? $employeeAbsences['EmployeeAbsence']['total'] : 0;  
        $result = $employeeAbsences;
        $this->set(compact('result'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        $canModified = true;

        if (!empty($this->data['id']) && isset($this->data['employee_id']) && isset($this->data['company_id']) && !empty($this->data['year'])
                && $this->_getEmpoyee($this->data['employee_id'], $this->data['company_id'], $canModified)
                && $this->EmployeeAbsence->Absence->find('count', array('recursive' => -1,
                    'conditions' => array('id' => $this->data['id'], 'company_id' => $this->data['company_id'], 'display' => true, 'activated' => true)))) {
            $this->EmployeeAbsence->create();
            $this->data['absence_id'] = $this->data['id'];
            $data = array();
            foreach (array('begin') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->EmployeeAbsence->convertTime($this->data[$key]);
                }
            }
            unset($this->data['id']);
            $last = $this->EmployeeAbsence->find('first', array(
                'recursive' => -1, 'fields' => array('id'),
                'conditions' => array('employee_id' => $this->data['employee_id'], 'absence_id' => $this->data['absence_id'], 'year' => $this->data['year'])));
            if ($last) {
                $this->EmployeeAbsence->id = $last['EmployeeAbsence']['id'];
            }
            if ($this->EmployeeAbsence->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('The Absence has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('The Absence could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->data['absence_id'];
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($employee_id = null, $company_id = null) {
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
        $this->EmployeeAbsence->Absence->cacheQueries = true;
        $this->EmployeeAbsence->cacheQueries = true;
        $absences = $this->EmployeeAbsence->Absence->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions));
        $employeeAbsences = $this->EmployeeAbsence->find('all', array('conditions' => array(
                'employee_id' => $employee_id),
            'recursive' => -1));
        $employeeAbsences = Set::combine($employeeAbsences, '{n}.EmployeeAbsence.absence_id', '{n}.EmployeeAbsence');

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
        $this->set(compact('absences', 'employee_id', 'company_id', 'employeeName', 'employeeAbsences'));
        $this->layout = '';
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $employee_id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for absence', true), 'error');
            $this->redirect(array('action' => 'index', $id));
        }
        $last = $this->EmployeeAbsence->find('first', array(
            'recursive' => -1, 'fields' => array('id'),
            'conditions' => array('employee_id' => $employee_id, 'absence_id' => $id)));
		$canModified = true;
		if( !$this->_getEmpoyee($employee_id, $company_id, $canModified) ){
			$this->_functionStop(false, $id, __('Absence could not set on default', true), false, array('action' => 'index', $employee_id, $company_id));
		}
        if (!$last || $this->EmployeeAbsence->delete($last['EmployeeAbsence']['id'])) {
            $this->Session->setFlash(__('Absence has been set on default', true), 'success');
        } else {
            $this->Session->setFlash(__('Absence could not set on default', true), 'error');
        }
        $this->redirect(array('action' => 'index', $employee_id, $company_id));
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee(&$employee_id, &$company_id, &$canModified) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
            if ($this->employee_info['Role']['name'] != 'admin' && $this->employee_info['Role']['name'] != 'hr') {
                $canModified = false;
                $employee_id = $this->employee_info['Employee']['id'];
            }
        }
        $this->loadModel('CompanyEmployeeReference');
        $employeeName = $this->CompanyEmployeeReference->find("first", array("conditions" => array(
                "CompanyEmployeeReference.employee_id" => $employee_id, 'CompanyEmployeeReference.company_id' => $company_id)));
        if (empty($employeeName)) {
            return false;
        }
        return $employeeName;
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
        $_startCheck = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_endCheck = strtotime('next sunday', $_startCheck);

        $this->set(compact('_startCheck', '_endCheck'));
        return array($_startCheck, $_endCheck);
    }

}
?>