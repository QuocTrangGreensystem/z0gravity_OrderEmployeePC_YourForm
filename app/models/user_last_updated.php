<?php
/** 
 * z0 Gravity™
 * Copyright 2018 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UserLastUpdated extends AppModel {

    var $name = 'UserLastUpdated';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

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
            $data = $data['UserLastUpdated'];
            $data['action'] = $value;
        } else {
            $data['path'] = $name;
            $data['action'] = $value;
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
            'fields' => array('id', 'action')
        ));
        if( !empty($data) ){
            return $data['UserLastUpdated']['action'];
        } else {
            return $default;
        }
    }
}
?>