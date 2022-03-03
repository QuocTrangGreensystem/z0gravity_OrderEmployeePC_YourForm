<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */

class SqlManagerEmployee extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
//    public $useTable = true;
    var $name = 'SqlManagerEmployee';

    /**
     * List of validation rules.
     *
     * @var array
     */
   
    var $belongsTo = array(
        'SqlManager' => array(
            'className' => 'SqlManager',
            'foreignKey' => 'sql_manager_id',
            
        )
    );
    
    
   
    

    }