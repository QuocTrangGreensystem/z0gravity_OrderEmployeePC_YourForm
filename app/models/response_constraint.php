<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ResponseConstraint extends AppModel {

    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ResponseConstraint';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'key' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The key is not blank!'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The name is not blank!'
            ),
        ),
        'color' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The color is not blank!'
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Company is not blank!'
            )
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

    /**
     * Get Response Constraint
     * 
     * @return array
     */
    public function getOptions($company_id = null) {
        if (!$this->_options) {
            $this->_options = array(
                'validated' => array(
                    'name' => __('Validate', true),
                    'color' => '#99cc00'
                ),
                'temporarily' => array(
                    'name' => __('Temporary validate', true),
                    'color' => '#800080'
                ),
                'rejetion' => array(
                    'name' => __('Reject', true),
                    'color' => '#993300'
                ),
                'waiting' => array(
                    'name' => __('Waiting validation', true),
                    'color' => '#ff6600'
                ),
                'holiday' => array(
                    'name' => __('Holiday', true),
                    'color' => '#ffff00'
                ),
                'forecast' => array(
                    'name' => __('Provisionnal day off', true),
                    'color' => '#cccccc'
                ),
            );
        }
        if (!$company_id) {
            return $this->_options;
        }
        $datas = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id)
                ));

        $options = array();
        foreach ($datas as $data) {
            $options[$data[$this->alias]['key']] = array(
                'name' => $data[$this->alias]['name'],
                'color' => $data[$this->alias]['color']
            );
        }
        return array_merge($this->_options, $options);
    }
}
?>