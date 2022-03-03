<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectEmployeeProfitFunctionRefer extends AppModel {

    var $name = 'ProjectEmployeeProfitFunctionRefer';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectFunction' => array(
            'className' => 'ProjectFunction',
            'foreignKey' => 'function_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function saveFunctionEmployee($profit_center_id, $employee_id, $data_functions = null) {
        if (!empty($data_functions)) {
            foreach ($data_functions as $data_function) {
                $this->data["ProjectEmployeeProfitFunctionRefer"]["profit_center_id"] = $profit_center_id;
                $this->data["ProjectEmployeeProfitFunctionRefer"]["employee_id"] = $employee_id;
                $this->data["ProjectEmployeeProfitFunctionRefer"]["function_id"] = $data_function;
                $this->save($this->data["ProjectEmployeeProfitFunctionRefer"]);
                $this->id = false;
            }
        } else {
            $this->data["ProjectEmployeeProfitFunctionRefer"]["profit_center_id"] = $profit_center_id;
            $this->data["ProjectEmployeeProfitFunctionRefer"]["employee_id"] = $employee_id;
            $this->save($this->data["ProjectEmployeeProfitFunctionRefer"]);
            $this->id = false;
        }
    }

    function editFunctionEmployee($profit_center_id, $employee_id, $data_functions = null) {
        $this->deleteAll(array("ProjectEmployeeProfitFunctionRefer.employee_id" => $employee_id));
        if (!empty($data_functions)) {
            foreach ($data_functions as $data_function) {
                $this->data["ProjectEmployeeProfitFunctionRefer"]["profit_center_id"] = $profit_center_id;
                $this->data["ProjectEmployeeProfitFunctionRefer"]["employee_id"] = $employee_id;
                $this->data["ProjectEmployeeProfitFunctionRefer"]["function_id"] = $data_function;
                $this->save($this->data["ProjectEmployeeProfitFunctionRefer"]);
                $this->id = false;
            }
        } else {
            $this->data["ProjectEmployeeProfitFunctionRefer"]["profit_center_id"] = $profit_center_id;
            $this->data["ProjectEmployeeProfitFunctionRefer"]["employee_id"] = $employee_id;
            $this->save($this->data["ProjectEmployeeProfitFunctionRefer"]);
            $this->id = false;
        }
    }
    var $virtualFields = array(
        //'manager_firstname' => 'User.first_name || \' \' || User.last_name',
        'lastname' => 'SELECT last_name FROM employees as ProjectEmployeeProfitFunctionRefer WHERE ProjectEmployeeProfitFunctionRefer.employee_id = id',
        'firstname' => 'SELECT first_name FROM employees as ProjectEmployeeProfitFunctionRefer WHERE ProjectEmployeeProfitFunctionRefer.employee_id = id',
        'actif' => 'SELECT actif FROM employees as ProjectEmployeeProfitFunctionRefer WHERE ProjectEmployeeProfitFunctionRefer.employee_id = id',
        'end_date' => 'SELECT end_date FROM employees as ProjectEmployeeProfitFunctionRefer WHERE ProjectEmployeeProfitFunctionRefer.employee_id = id',
        'external' => 'SELECT external FROM employees as ProjectEmployeeProfitFunctionRefer WHERE ProjectEmployeeProfitFunctionRefer.employee_id = id',
    );
	function getEmployeesOfPCs($listPCs)
	{
		$employeeOfPCs = $this->find('list', array(
				'recursive' => -1,
				'conditions' => array(	
					'ProjectEmployeeProfitFunctionRefer.profit_center_id'=>$listPCs,
				),
				'fields' => array('employee_id','employee_id')
		));
		$employeeOfPCs = array_keys($employeeOfPCs);
		return $employeeOfPCs;
	}
	function getPcByEmployees($employees)
	{
		$pcs = $this->find('list', array(
				'recursive' => -1,
				'conditions' => array(	
					'ProjectEmployeeProfitFunctionRefer.employee_id'=>$employees,
				),
				'fields' => array('profit_center_id','profit_center_id')
		));
		$pcs = array_keys($pcs);
		return $pcs;
	}
}
?>