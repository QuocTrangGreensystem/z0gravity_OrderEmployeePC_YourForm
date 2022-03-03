<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectPart extends AppModel {

    var $name = 'ProjectPart';
    var $actsAs = array('Containable');
    var $hasMany = array(
        'ProjectPhasePlan' => array(
            'className' => 'ProjectPhasePlan',
            'foreignKey' => 'project_part_id',
            'dependent' => true
        )
    );
    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $validate = array(
        'title' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'The Part is not blank!',
            )
            //'isUnique' => array(
//                'rule' => 'isUnique',
//                'message' => 'The Part has already been exist.',
//            )
        ),
        'project_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'The Project is not blank!',
            ),
        )
    );

    /**
     * I18n validate
     *
     * @param string $field The name of the field to invalidate
     * @param mixed $value Name of validation rule that was not failed, or validation message to
     *    be returned. If no validation key is provided, defaults to true.
     * 
     * @return void
     */
    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }

}
?>