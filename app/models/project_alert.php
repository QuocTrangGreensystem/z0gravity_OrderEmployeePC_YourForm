<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAlert extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ProjectAlert';
    function __construct(){
        parent::__construct();
        $this->validate = array(
            'alert_name' => array(
                'length'=>array(
                    'rule'=>array('minLength',1),
                    'message' => __('Cannot be empty',true)
                )
            ),
            'number_of_day' => array(
                'rule' => 'numeric',
                'message' => __('Number only',true)
            )
        );
    }
}
?>
