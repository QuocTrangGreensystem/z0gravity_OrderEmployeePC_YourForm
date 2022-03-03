<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectDashboard extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'ProjectDashboard';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'employee_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Employee is not blank!'
            )
        ),
        'company_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Employee is not blank!'
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
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => array('id', 'first_name', 'last_name'),
            'order' => ''
        )
    );
	public $hasMany = array(
        'ProjectDashboardShare' => array(
            'className' => 'ProjectDashboardShare',
            'foreignKey' => 'dashboard_id',
        ),
    );
	
	function get_dashboard_by_id($dashboard_id = null) {
		$this->recursive = 1;
		$data = $this->find('first', array(
			'conditions' => array('ProjectDashboard.id' => $dashboard_id)
		));
		if( !empty( $data['ProjectDashboard']['dashboard_data'] )) $data['ProjectDashboard']['dashboard_data']  = unserialize($data['ProjectDashboard']['dashboard_data'] );
		else $data['ProjectDashboard']['dashboard_data']  = array();
		if( ($data['ProjectDashboard']['share_type']=='resource') && !empty($data['ProjectDashboardShare'])){
			foreach($data['ProjectDashboardShare'] as $e){
				$data['ProjectDashboard']['share_resource'][] = $e['employee_id'];
			}
		}
		if( isset($data['ProjectDashboardShare'])) unset($data['ProjectDashboardShare']);
		if( isset($data['Employee'])) unset($data['Employee']);
		return $data;
	}

}