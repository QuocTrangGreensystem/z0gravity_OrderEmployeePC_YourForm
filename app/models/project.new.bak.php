<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Project extends AppModel {

    var $name = 'Project';
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'project_manager_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPhase' => array(
            'className' => 'ProjectPhase',
            'foreignKey' => 'project_phase_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPriority' => array(
            'className' => 'ProjectPriority',
            'foreignKey' => 'project_priority_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectStatus' => array(
            'className' => 'ProjectStatus',
            'foreignKey' => 'project_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserView' => array(
            'className' => 'UserView',
            'foreignKey' => 'id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserDefaultView' => array(
            'className' => 'UserDefaultView',
            'foreignKey' => 'id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectType' => array(
            'className' => 'ProjectType',
            'foreignKey' => 'project_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectSubType' => array(
            'className' => 'ProjectSubType',
            'foreignKey' => 'project_sub_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrProgram' => array(
            'className' => 'ProjectAmrProgram',
            'foreignKey' => 'project_amr_program_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrSubProgram' => array(
            'className' => 'ProjectAmrSubProgram',
            'foreignKey' => 'project_amr_sub_program_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectComplexity' => array(
            'className' => 'ProjectComplexity',
            'foreignKey' => 'complexity_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasMany = array(
        'ProjectTeam' => array(
            'className' => 'ProjectTeam',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectPhasePlan' => array(
            'className' => 'ProjectPhasePlan',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectMilestone' => array(
            'className' => 'ProjectMilestone',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectTask' => array(
            'className' => 'ProjectTask',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectRisk' => array(
            'className' => 'ProjectRisk',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectIssue' => array(
            'className' => 'ProjectIssue',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectDecision' => array(
            'className' => 'ProjectDecision',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectLivrable' => array(
            'className' => 'ProjectLivrable',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectEvolution' => array(
            'className' => 'ProjectEvolution',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectAmr' => array(
            'className' => 'ProjectAmr',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectCreatedVal' => array(
            'className' => 'ProjectCreatedVal',
            'foreignKey' => 'project_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'project_name' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The project name is not blank!', true),
                ),
            ),
            'project_manager_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The project manager is not blank!', true),
                ),
            ),
            //		'budget' => array(				
            //			'gioihan' => array(
            //				'rule' => array('comparison','>=',0),
            //				'message' => 'The budget must be a nummber & at least 0!'
            //				),
            //		),
            'project_phase_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The project phase is not blank!', true),
                )
            ),
            //	'start_date' => array(
            //		'notempty' => array(
            //			'rule' => array('notempty'),
            //			'message' => __('The start date is not blank!', true),
            //			'required' => true,
            //			'on' => 'create', // Limit validation to 'create' or 'update' operations
            //		)
            //	),
            'company_id' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('The company is not blank.', true),
                ),
            ),
            'created_value' => array(
                'rule' => array('numeric'),
                //'rule'=>'/^[a-z0-9]$/i',
                'message' => __('Only numbers allowed.', true),
                'allowEmpty' => true
            )
        );
    }

    /**
     * Get View Field 
     *
     * @return array fieldset
     * @access public
     */
    public function getViewFieldNames() {
        $fieldset = array();
        foreach (array_keys($this->schema()) as $name) {
            $fieldset['Project.' . $name] = Inflector::humanize(preg_replace('/_id$/', '', $name));
        }
        return $fieldset;
    }

    /**
     * Parse View Field 
     *
     * @param array $fields the fields map to read
     * @return array, fieldset the mapping config for extract data,
     *  field and contain option of Model::find
     * @access public
     */
    protected function _parseFilter($Model, $conditions) {
        $last = $Model->find('first', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => $conditions
                ));
        if ($last) {
            return $last[$Model->alias]['id'];
        }
        return null;
    }

    /**
     * Parse View Field 
     *
     * @param array $fields the fields map to read
     * @return array, fieldset the mapping config for extract data,
     *  field and contain option of Model::find
     * @access public
     */
    public function parseViewField($fields, $filters = array()) {
        $fieldset = $conditions = $contain = array();
        foreach ((array) $fields as $_fieldName) {
            list($model, $field) = pluginSplit($_fieldName);
            $path = '';
            if (preg_match('/_id$/', $field)) {
                $Object = $model == $this->alias ? $this : $this->{$model};
                foreach ($Object->belongsTo as $assoc => $data) {
                    
                    if ($field === $data['foreignKey']) {
                        
                        $_conditions = array();
                        
                        if ($model !== $this->alias) {
                            $contain[$Object->alias]['fields'][] = 'id';
                            $contain[$Object->alias][$assoc] = $Object->{$assoc}->displayField;
                            $path = $model . '.0.' . $assoc . '.' . $Object->{$assoc}->displayField;
                        } else {
                            switch ($assoc) {
                                case 'Employee': {
                                        $path = array('{0} {1}', array('{n}.Employee.first_name', '{n}.Employee.last_name'));
                                        $contain[$assoc][] = 'first_name';
                                        $contain[$assoc][] = 'last_name';

                                        if (isset($filters[$_fieldName])) {
                                            list($_firstName, $_lastName) = explode(' ', $filters[$_fieldName]);
                                            $_conditions = array(
                                                'first_name' => $_firstName,
                                                'last_name' => $_lastName
                                            );
                                        }
                                        break;
                                    }
                                default : {
                                        $path = $assoc . '.' . $Object->{$assoc}->displayField;
                                        $contain[$assoc][] = $Object->{$assoc}->displayField;
                                    }
                            }
                        }


                        if (empty($_conditions) && isset($filters[$_fieldName])) {
                            $_conditions = array(
                                $Object->{$assoc}->displayField => $filters[$_fieldName]
                            );
                        }
                        
                        if (!empty($_conditions) && ($filter = $this->_parseFilter($Object->{$assoc}, $_conditions))) {
                            $contain[$assoc]['conditions'] = array($assoc .'.id' => $filter);
                        }
                       
                        break;
                    }
                }
            }
            if (!$path) {

                if ($model !== $this->alias) {
                    $contain[$model]['fields'][] = $field;
                    $path = $model . '.0.' . $field;
                } else {
                    $path = $model . '.' . $field;
                    $contain[$model][] = $field;
                    if(isset($filters[$_fieldName])){
                        $conditions[$_fieldName] = $filters[$_fieldName];
                    }
                }
            }
            $fieldset[] = array(
                'key' => $model . '.' . $field,
                'name' => Inflector::humanize(preg_replace('/_id$/', '', $field)),
                'path' => $path
            );
        }
        $fields = array();
        if (isset($contain[$this->alias])) {
            $fields = $contain[$this->alias];
            unset($contain[$this->alias]);
        }
        return array($fieldset, compact('contain', 'fields','conditions'));
    }

}
?>