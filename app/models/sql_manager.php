<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SqlManager extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
//    public $useTable = true;
    var $name = 'SqlManager';

    /**
     * List of validation rules.
     *
     * @var array
     */
    var $hasMany  = array(
        'SqlManagerEmployee' => array(
            'className' => 'SqlManagerEmployee',
            'foreignKey' => 'sql_manager_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'dependent' => true
        )
    );
    
    var $validate = array(
        'request_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => true, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'desc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => true, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        
    );

    
    

    }
    
    
?>