<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2019 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class CompanyColumnDefaultsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'CompanyColumnDefaults';
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index() {
        $company_id = $this->employee_info['Company']['id'];
		$defaultColumns = $this->CompanyColumnDefault->find('list', array(
			'recursive' => -1,
			'fields' => array('field_name', 'width'),
			'conditions' => array(
				'company_id' => $company_id,
			)
		));
		/*
		* Tao array, de insert vao database khi login lan dau vao man hinh admin nay.
		* Neu them field, them o day va file index.
		*/
		$defaultField = array(
			0 => array(
				'column_name' => 'Program',
				'field_name' => 'Project.project_amr_program_id',
				'width' => '200',
				'company_id' => $company_id
			),
			1 => array(
				'column_name' => 'Sub Program',
				'field_name' => 'Project.project_amr_sub_program_id',
				'width' => '200',
				'company_id' => $company_id
			),
			2 => array(
				'column_name' => 'Project Name',
				'field_name' => 'Project.project_name',
				'width' => '300',
				'company_id' => $company_id
			),
			3 => array(
				'column_name' => 'Project Manager',
				'field_name' => 'Project.project_manager_id',
				'width' => '150',
				'company_id' => $company_id
			),
			4 => array(
				'column_name' => 'Weather',
				'field_name' => 'ProjectAmr.weather',
				'width' => '100',
				'company_id' => $company_id
			),
			5 => array(
				'column_name' => 'Todo',
				'field_name' => 'ProjectAmr.todo',
				'width' => '400',
				'company_id' => $company_id
			),
			6 => array(
				'column_name' => 'Progress',
				'field_name' => 'ProjectAmr.project_amr_progression',
				'width' => '400',
				'company_id' => $company_id
			),
			7 => array(
				'column_name' => 'Done',
				'field_name' => 'ProjectAmr.done',
				'width' => '400',
				'company_id' => $company_id
			),
			8 => array(
				'column_name' => 'Amount €',
				'field_name' => 'ProjectBudgetSyn.Amount€',
				'width' => '50',
				'company_id' => $company_id
			)
		);
		//User yeu cau tao them nen add them array de merge
		$defaultAddField = array (
			 array(
				'column_name' => 'Comment',
				'field_name' => 'ProjectAmr.comment',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Budget',
				'field_name' => 'ProjectAmr.budget',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Risk',
				'field_name' => 'ProjectAmr.project_amr_risk_information',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Issue',
				'field_name' => 'ProjectAmr.project_amr_problem_information',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Customer Point Of View',
				'field_name' => 'ProjectAmr.customer_point_of_view',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Information',
				'field_name' => 'ProjectAmr.project_amr_solution',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Planning status',
				'field_name' => 'ProjectAmr.planning_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Risk status',
				'field_name' => 'ProjectAmr.risk_control_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			array(
				'column_name' => 'Issue status',
				'field_name' => 'ProjectAmr.issue_control_weather',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Trend',
				'field_name' => 'ProjectAmr.rank',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Scope',
				'field_name' => 'ProjectAmr.project_amr_scope',
				'width' => '400',
				'company_id' => $company_id
			),
			array(
				'column_name' => 'Schedule',
				'field_name' => 'ProjectAmr.project_amr_schedule',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Resource',
				'field_name' => 'ProjectAmr.project_amr_resource',
				'width' => '400',
				'company_id' => $company_id
			),
			array(
				'column_name' => 'Technical',
				'field_name' => 'ProjectAmr.project_amr_technical',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Budget comment',
				'field_name' => 'ProjectAmr.project_amr_budget_comment',
				'width' => '400',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Budget weather',
				'field_name' => 'ProjectAmr.budget_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Scope weather',
				'field_name' => 'ProjectAmr.scope_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Schedule weather',
				'field_name' => 'ProjectAmr.schedule_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Resource weather',
				'field_name' => 'ProjectAmr.resources_weather',
				'width' => '150',
				'company_id' => $company_id
			),
			 array(
				'column_name' => 'Technical weather',
				'field_name' => 'ProjectAmr.technical_weather',
				'width' => '150',
				'company_id' => $company_id
			)
		);
		// insert mang da tao vao database
		if(empty($defaultColumns)){
			$this->CompanyColumnDefault->create();
			$this->CompanyColumnDefault->saveAll(array_merge($defaultField,$defaultAddField));
			$defaultColumns = $this->CompanyColumnDefault->find('all', array(
				'recursive' => -1,
				'field' => array('id', 'field_name', 'width'),
				'conditions' => array(
					'company_id' => $company_id,
				)
			));
		}
		foreach ($defaultAddField as $val){
				$check_exit = $this->CompanyColumnDefault->find('first', array(
				'recursive' => -1,
				'field' => array('id', 'field_name','company_id'),
				'conditions' => array(
					'company_id' => $val['company_id'],
					'field_name' => $val['field_name'],
				)
			));
			if(!$check_exit){
				$this->CompanyColumnDefault->create();
				$this->CompanyColumnDefault->save($val);
			}
		}
        $this->set(compact('company_id','defaultColumns'));
	}
    /**
     * update
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
			$checkID1 = $this->CompanyColumnDefault->find('first',array(
				'recursive' => -1,
				'field' => array('id'),
				'conditions'=> array(
					'field_name' => $this->data['field_name'],
					'company_id' => $this->data['company_id'],
				)
			));
            $this->CompanyColumnDefault->create();
            if (!empty($checkID1['CompanyColumnDefault']['id'])) {
                $this->CompanyColumnDefault->id = $checkID1['CompanyColumnDefault']['id'];
            }
			//Tao mang de merge. Update by QuanNV 12/04/2019
            $data = array(
				'width' => $this->data['width'],
			);
            unset($checkID1['CompanyColumnDefault']['id']);
            if ($this->CompanyColumnDefault->save(array_merge($checkID1['CompanyColumnDefault'], $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Width Column could not be saved. Please, try again.', true), 'error');
            }
            $checkID1['CompanyColumnDefault']['id'] = $this->CompanyColumnDefault->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
	/**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
}