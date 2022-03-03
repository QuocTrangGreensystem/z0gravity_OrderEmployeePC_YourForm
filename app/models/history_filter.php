<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class HistoryFilter extends AppModel {

    var $name = 'HistoryFilter';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
		
    );
	function loadFilter($path){
		$result = array();
		if(!empty($path)){
			$employee = CakeSession::read('Auth.employee_info.Employee.id');
			$result = $this->find('first', array(
				'conditions' => array(
					'path' => $path,
					'employee_id' => $employee
				),
				'fields' => array('params'),
			));
			
			$result = !empty($result) ? $result['HistoryFilter']['params'] : array();
		}
		return $result;
	}
    function saveSetting($name, $value){
        $employee = CakeSession::read('Auth.employee_info.Employee.id');
        $data = $this->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'path' => $name,
                'employee_id' => $employee
            ),
            'fields' => array('id', 'path', 'employee_id')
        ));
        if( !empty($data) ){
            $data = $data['HistoryFilter'];
            $data['params'] = $value;
        } else {
            $data['path'] = $name;
            $data['params'] = $value;
            $data['employee_id'] = $employee;
            $this->create();
        }
        $this->save($data);
    }

    function getSetting($name, $default = null){
        $employee = CakeSession::read('Auth.employee_info.Employee.id');
        $data = $this->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'path' => $name,
                'employee_id' => $employee
            ),
            'fields' => array('id', 'params')
        ));
        if( !empty($data) ){
            return $data['HistoryFilter']['params'];
        } else {
            return $default;
        }
    }
    function getSettings($paths){
        $employee = CakeSession::read('Auth.employee_info.Employee.id');
        $data = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'path' => $paths,
                'employee_id' => $employee
            ),
            'fields' => array('id', 'params', 'path')
        ));
        return !empty($data) ? (Set::combine($data, '{n}.HistoryFilter.path', '{n}.HistoryFilter.params')) : array();
    }
}
?>