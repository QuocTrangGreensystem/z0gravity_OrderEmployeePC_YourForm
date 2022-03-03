<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class Workday extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'Workday';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'monday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'tuesday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'wednesday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'thursday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'friday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'saturday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'sunday' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('inList', array('0.0', '0.5', '1.0')),
                'message' => 'Please supply a valid number.'
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Company is not blank!'
            ),
            // 'unique' => array(
            //     'rule' => array('isUnique'),
            //     'message' => 'The comnpany data is avaiable, please try again!'
            // ),
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

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
    public $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    public function getOptions($company_id = null, $country_id = null) {
        $default = array(
            'monday' => 1,
            'tuesday' => 1,
            'wednesday' => 1,
            'thursday' => 1,
            'friday' => 1,
            'saturday' => 0,
            'sunday' => 0
        );
        if ($company_id) {
            $conditions = array(
                'company_id' => $company_id
            );
            if($country_id){
                $conditions['country_id'] = $country_id;
            } else {
                $conditions['OR'] = array(
                    array('country_id is NULL'),
                    array('country_id' => 0)
                );
            }
            $datas = $this->find('first', array('recursive' => -1, 'fields' => array_keys($default), 'conditions' => $conditions
            ));
            if ($datas) {
                $default = array_merge($default, array_filter($datas[$this->alias]));
            }
        }
        return $default;
    }

}
?>
