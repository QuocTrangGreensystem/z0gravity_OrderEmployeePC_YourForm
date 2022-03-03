<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class TwoFactorAuthen extends AppModel {
// two_factor_auths
    /**
     * Name of the model.
     *
     * @var string
     */
//    public $useTable = true;
    var $name = 'TwoFactorAuthen';

    /**
     * List of validation rules.
     *
     * @var array
     */
    var $belongTo  = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'dependent' => true
        ),
    );
}