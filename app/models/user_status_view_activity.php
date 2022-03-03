<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UserStatusViewActivity extends AppModel {

    var $name = 'UserStatusViewActivity';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserView' => array(
            'className' => 'UserView',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
?>