<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectEmployeeManager extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ProjectEmployeeManager';

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
     public $belongsTo = array(
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