<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmr extends AppModel {

    var $name = 'ProjectAmr';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_id',
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
        'ProjectAmrCategory' => array(
            'className' => 'ProjectAmrCategory',
            'foreignKey' => 'project_amr_category_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrSubCategory' => array(
            'className' => 'ProjectAmrSubCategory',
            'foreignKey' => 'project_amr_sub_category_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'project_manager_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrStatus' => array(
            'className' => 'ProjectAmrStatus',
            'foreignKey' => 'project_amr_status_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectPhases' => array(
            'className' => 'ProjectPhases',
            'foreignKey' => 'project_phases_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrCostControl' => array(
            'className' => 'ProjectAmrCostControl',
            'foreignKey' => 'project_amr_cost_control_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrOrganization' => array(
            'className' => 'ProjectAmrOrganization',
            'foreignKey' => 'project_amr_organization_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrPlan' => array(
            'className' => 'ProjectAmrPlan',
            'foreignKey' => 'project_amr_plan_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrPerimeter' => array(
            'className' => 'ProjectAmrPerimeter',
            'foreignKey' => 'project_amr_perimeter_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrRiskControl' => array(
            'className' => 'ProjectAmrRiskControl',
            'foreignKey' => 'project_amr_risk_control_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ProjectAmrProblemControl' => array(
            'className' => 'ProjectAmrProblemControl',
            'foreignKey' => 'project_amr_problem_control_id',
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
        )
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            //'budget' => array(    				
            //	  'number'=>array(
            //	               'rule'=>array('numeric'),'message'=>__('The budget must be a number!',true),'allowEmpty'=>true
            //				   ),
            //),
            'project_amr_progression' => array(
                'number' => array('rule' => array('numeric'),
                    'message' => __('The Amr progression must be number!', true), 'allowEmpty' => true)
            )
        );
    }
    
    public $formatFields = array(
            'project_amr_category_id' => 'Category',
            'project_amr_sub_category_id' => 'Sub category',
            'project_amr_status_id' => 'Status',
            'project_amr_progression' => 'Progress %',
            'project_phases_id' => 'Current Phase',
            'project_amr_risk_information' => 'Risk',
            'project_amr_problem_information' => 'Issue',
            'project_amr_solution' => 'Information',
            'project_amr_solution_description' => 'Action',
            'cost_control_weather' => 'Budget status',
            'planning_weather' => 'Planning status',
            'risk_control_weather' => 'Risk status',
            'organization_weather' => 'Staffing status',
            'perimeter_weather' => 'Perimeter status',
            'issue_control_weather' => 'Issue status',
            //'md_validated' => 'Engaged M.D',
            'md_validated' => 'Consumed',
            'md_engaged' => 'Remaining M.D',
            'md_forecasted' => 'Budget M.D',
            'md_variance' => 'Variance M.D',
            'validated_currency_id' => 'Currency',
            'forecasted' => 'CAPEX Budget',
            'validated' => 'CAPEX Engaged/Consumed',
            'engaged' => 'CAPEX Remain',
            'variance' => 'CAPEX Variance',
        );

    public function getViewFieldNames() {
        $fieldset = array();
        $exceptions = array(
            'project_amr_mep_date',
            'project_amr_program_id',
            'project_manager_id',
            //added on 2015-04-09
            //mail: PMS - 8/4/2015 - personalized view
            'project_amr_category_id',
            'project_amr_sub_category_id',
            'project_amr_sub_program_id',
            'currency_id',
            'project_amr_status_id',
            'project_phases_id',
            'project_amr_cost_control_id',
            'project_amr_organization_id',
            'project_amr_plan_id',
            'project_amr_perimeter_id',
            'project_amr_risk_control_id',
            'project_amr_problem_control_id',
            //'md_validated',
            'md_engaged',
            'md_forecasted',
            'md_variance',
            'engaged_currency_id',
            'forecasted_currency_id',
            'variance_currency_id',
            'validated',
            'engaged',
            'forecasted',
            'variance',
            'perimeter_weather',
            'validated_currency_id',
            'manual_consumed'
        );
        foreach (array_keys($this->schema()) as $name) {
            if( !in_array($name, $exceptions) ){
                if(in_array($name, array_keys($this->formatFields))){
                    $fieldset['ProjectAmr.' . $name] = $this->formatFields[$name]; 
                } else {
                    $fieldset['ProjectAmr.' . $name] = Inflector::humanize(preg_replace('/_id$/', '', $name));   
                }
            }
        }
        $fieldset['ProjectAmr.delay'] = 'Delay';
        $fieldset['ProjectAmr.done'] = 'Done';
        $fieldset['ProjectAmr.todo'] = 'To Do';
        //debug($fieldset); exit;
        return $fieldset;
    }

}
?>