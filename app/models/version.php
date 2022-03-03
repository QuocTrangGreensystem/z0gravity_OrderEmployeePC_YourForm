<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Version extends AppModel {

    var $name = 'Version';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    function __construct() {
        parent::__construct();
        $this->validate = array(
            'name' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The version name is not blank!', true),
                ),
                'unique' => array(
                    'rule' => array('isUnique'),
                    'message' => __('The version name is avaiable, please enter another!', true),
                ),
            ),
            'content' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The version content is not blank!', true),
                ),
            )
        );
    } 
}
?>