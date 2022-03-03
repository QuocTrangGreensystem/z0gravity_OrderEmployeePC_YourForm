<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectLocalView extends AppModel {

    var $name = 'ProjectLocalView';

    //var $displayField = 'project_issue_name';

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'project_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The project id of Local View is not found!', true),
                ),
                'numeric' => array(
                    'rule' => array('numeric'),
                    'message' => __('The project id is nummeric!', true),
                ),
            ),
            'attachment' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('Local View attachment is not blank!', true),
                ),
            )
        );
    }

    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
?>