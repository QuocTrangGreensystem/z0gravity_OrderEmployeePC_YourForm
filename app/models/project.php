<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class Project extends AppModel {

    var $name = 'Project';
	var $actsAs = array('Lib');
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'ChiefBusiness' => array(
            'className' => 'EmployeeAlias',
            'foreignKey' => 'chief_business_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'TechicalManager' => array(
            'className' => 'EmployeeAlias',
            'foreignKey' => 'technical_manager_id',
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
        'ProjectSubSubType' => array(
            'className' => 'ProjectSubType',
            'foreignKey' => 'project_sub_sub_type_id',
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
        ),
        'Activities' => array(
            'className' => 'Activities',
            'foreignKey' => 'activity_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'BudgetCustomer' => array(
            'className' => 'BudgetCustomer',
            'foreignKey' => 'budget_customer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasMany = array(
        'ProjectImage',
        'ProjectFinance' => array(
            'className' => 'ProjectFinance',
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
		'ProjectPart' => array(
            'className' => 'ProjectPart',
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
        'ProjectPhaseCurrent' => array(
            'className' => 'ProjectPhaseCurrent',
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
        'ProjectListMultiple' => array(
            'className' => 'ProjectListMultiple',
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
        'ProjectGlobalView' => array(
            'className' => 'ProjectGlobalView',
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
        'ProjectLocalView' => array(
            'className' => 'ProjectLocalView',
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
        'ProjectEmployeeManager' => array(
            'className' => 'ProjectEmployeeManager',
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
        'ProjectBudgetSyn' => array(
            'className' => 'ProjectBudgetSyn',
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
        'ProjectBudgetInternal' => array(
            'className' => 'ProjectBudgetInternal',
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
        'ProjectBudgetInternalDetail' => array(
            'className' => 'ProjectBudgetInternalDetail',
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
        'ProjectBudgetExternal' => array(
            'className' => 'ProjectBudgetExternal',
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
        'ProjectBudgetSale' => array(
            'className' => 'ProjectBudgetSale',
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
        'ProjectBudgetInvoice' => array(
            'className' => 'ProjectBudgetInvoice',
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
        'ProjectFile' => array(
            'className' => 'ProjectFile',
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
        'MFavorite' => array(
            'className' => 'MFavorite',
            'foreignKey' => 'modelId',
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
        'MLike' => array(
            'className' => 'MLike',
            'foreignKey' => 'modelId',
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
        'LogSystem' => array(
            'className' => 'LogSystem',
            'foreignKey' => 'model_id',
        )
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
            // 'project_manager_id' => array(
                // 'notempty' => array(
                    // 'rule' => array('notempty'),
                    // 'message' => __('The project manager is not blank!', true),
                // ),
            // ),
            // 'budget' => array(
            //     'gioihan' => array(
            //         'rule' => array('comparison','>=',0),
            //         'message' => 'The budget must be a nummber & at least 0!'
            //     ),
            // ),
            // 'project_phase_id' => array(
            //     'notempty' => array(
            //         'rule' => array('notempty'),
            //         'message' => __('The project phase is not blank!', true),
            //     )
            // ),
            // 'start_date' => array(
            //     'notempty' => array(
            //         'rule' => array('notempty'),
            //         'message' => __('The start date is not blank!', true),
            //         'required' => true,
            //         'on' => 'create', // Limit validation to 'create' or 'update' operations
            //     )
            // ),
            'company_id' => array(
                'Notempty' => array(
                    'rule' => array('notEmpty'),
                    'message' => __('The company is not blank.', true),
                ),
            ),
			/*
            'created_value' => array(
                'rule' => array('numeric'),
                //'rule'=>'/^[a-z0-9]$/i',
                'message' => __('Only numbers allowed.', true),
                'allowEmpty' => true
            )
			*/
        );
    }

    /**
     * Get View Field
     *
     * @return array fieldset
     * @access public
     */
    public function getViewFieldNames() {
        $fieldDeletes = array(
            'planed_end_date' => 'planed_end_date',
            'project_amr_progression' => 'project_amr_progression',
            'md_forecasted' => 'md_forecasted',
            'md_validated' => 'md_validated',
            'md_engaged' => 'md_engaged',
            'md_variance' => 'md_variance',
            'task_flag' => 'task_flag',
            'budget' => 'budget',
            'rebuild_staffing',
            //'created',
            'updated',
            'currency_id',
            'perimeter_weather',
            'latlng',
            'is_staffing'
        );
        $fieldset = array();
        foreach (array_keys($this->schema()) as $name) {
            if(in_array($name, $fieldDeletes)){
                //do nothing
            } else {
                if(in_array($name, array_keys($this->formatFields))){
                    $fieldset['Project.' . $name] = $this->formatFields[$name];
                } else {
                    $fieldset['Project.' . $name] = Inflector::humanize(preg_replace('/_id$/', '', $name));
                }

            }
        }
        return $fieldset;
    }
    /**
     * chinh sua lai name cua cac field
     */
    public $formatFields = array(
            //'project_name' => 'Project Name',
            'long_project_name' => 'Project long name',
            //'project_code_1' => 'Project Code 1',
            //'project_code_2' => 'Project Code 2',
            'project_phase_id' => 'Current Phase',
            'project_priority_id' => 'Priority',
            'project_status_id' => 'Status',
            'yn_1' => 'Yes/No 1',
            'yn_2' => 'Yes/No 2',
            'yn_3' => 'Yes/No 3',
            'yn_4' => 'Yes/No 4',
            'yn_5' => 'Yes/No 5',
            'yn_6' => 'Yes/No 6',
            'yn_7' => 'Yes/No 7',
            'yn_8' => 'Yes/No 8',
            'yn_9' => 'Yes/No 9',
            'bool_1' => '0/1 1',
            'bool_2' => '0/1 2',
            'bool_3' => '0/1 3',
            'bool_4' => '0/1 4',
            'activated' => 'Timesheet Filling Activated',
            //'start_date' => 'Start Date',
            //'end_date' => 'End Date',
            //'primary_objectives' => 'Primary Objectives',
            //'project_objectives' => 'Project Objectives',
            'issues' => 'Issues',
            'project_type_id' => 'Project type',
            'project_sub_type_id' => 'Sub type',
            'project_sub_sub_type_id' => 'Sub sub type',
            //'chief_business_id' => 'Chef Bussiness',
            'project_amr_program_id' => 'Program',
            'project_amr_sub_program_id' => 'Sub program',
            'activity_id' => 'Link To RMS Activity',
            'technical_manager_id' => 'Technical manager',
            'functional_leader_id' => 'Functional leader',
            'uat_manager_id' => 'UAT manager',
            'complexity_id' => 'Implementation Complexity',
            'created_value' => 'Created value',
            'copy_number' => '*Copy Number',
            'is_staffing' => '*Is Staffing',
            'is_freeze' => '*Is Freeze',
            'freeze_by' => '*Freeze By',
            'freeze_time' => '*Freeze Time',
            'off_freeze' => '*Off Freeze',
            'project_phases_id' => 'Current Phase',
            //project amr
            'project_amr_category_id' => 'Category',
            'project_amr_sub_category_id' => 'Sub category',
            'project_amr_status_id' => 'Status',
            'project_amr_progression' => 'Progress %',
            'project_amr_risk_information' => 'Risk',
            'project_amr_problem_information' => 'Issue',
            'project_amr_solution' => 'Information',
            'project_amr_solution_description' => 'Action',
            'cost_control_weather' => 'Budget status',
            'planning_weather' => 'Planning status',
            'risk_control_weather' => 'Risk status',
            'organization_weather' => 'Staffing status',
            //'perimeter_weather' => 'Perimeter',
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
            'sales_sold' => 'Sold €',
            'sales_to_bill' => 'To Bill €',
            'sales_billed' => 'Billed €',
            'sales_paid' => 'Paid €',
            'sales_man_day' => 'M.D',
            'total_costs_budget' => 'Total Costs Budget €',
            'total_costs_forecast' => 'Total Costs Forecast €',
            'total_costs_var' => 'Total Costs Var %',
            'total_costs_engaged' => 'Total Costs Engaged €',
            'total_costs_remain' => 'Total Costs Remain €',
            'total_costs_man_day' => 'Total Costs M.D',
            'internal_costs_budget' => 'Budget €',
            'internal_costs_budget_man_day' => 'Budget M.D',
            'internal_costs_forecast' => 'Forecast €',
            'internal_costs_var' => 'Var %',
            'internal_costs_engaged' => 'Engaged €',
            'internal_costs_remain' => 'Remain €',
            'internal_costs_forecasted_man_day' => 'Forecast M.D',
            'internal_costs_average' => 'Average €',
            'external_costs_budget' => 'Budget €',
            'external_costs_forecast' => 'Forecast €',
            'external_costs_var' => 'Var %',
            'external_costs_ordered' => 'Ordered €',
            'external_costs_remain' => 'Remain €',
            'external_costs_man_day' => 'M.D',
            'external_costs_progress' => 'Progress %',
            'external_costs_progress_euro' => 'Progress €',
            'assign_to_profit_center' => '% Assigned to profit center',
            'assign_to_employee' => '% Assigned to employee',
            'budget_customer_id' => 'Customer',
            'project_amr_budget_comment' => 'Budget comment',
            'project_amr_scope' => 'Scope',
            'project_amr_schedule' => 'Schedule',
            'project_amr_resource' => 'Resource',
            'project_amr_technical' => 'Technical',
            'budget_weather' => 'Budget weather',
            'scope_weather' => 'Scope weather',
            'schedule_weather' => 'Schedule weather',
            'resources_weather' => 'Resource weather',
            'technical_weather' => 'Technical weather',
			
            //finance
            'bp_operation_city' => 'BP Operation City',
            'bp_investment_city' => 'BP Investment City',
            'provisional_budget_md' => 'Budget Provisional M.D',

            'todo' => 'To Do',
            'update_by_employee' => 'Updated by resource',
            'last_modified' => 'Last modified',

            'text_one_line_1' => 'Text one line 1',
            'text_one_line_2' => 'Text one line 2',
            'text_one_line_3' => 'Text one line 3',
            'text_one_line_4' => 'Text one line 4',
            'text_one_line_5' => 'Text one line 5',
            'text_one_line_6' => 'Text one line 6',
            'text_one_line_7' => 'Text one line 7',
            'text_one_line_8' => 'Text one line 8',
            'text_one_line_9' => 'Text one line 9',
            'text_one_line_10' => 'Text one line 10',
            'text_one_line_11' => 'Text one line 11',
            'text_one_line_12' => 'Text one line 12',
            'text_one_line_13' => 'Text one line 13',
            'text_one_line_14' => 'Text one line 14',
            'text_one_line_15' => 'Text one line 15',
            'text_one_line_16' => 'Text one line 16',
            'text_one_line_17' => 'Text one line 17',
            'text_one_line_18' => 'Text one line 18',
            'text_one_line_19' => 'Text one line 19',
            'text_one_line_20' => 'Text one line 20',

            'text_two_line_1' => 'Text two line 1',
            'text_two_line_2' => 'Text two line 2',
            'text_two_line_3' => 'Text two line 3',
            'text_two_line_4' => 'Text two line 4',
            'text_two_line_5' => 'Text two line 5',
            'text_two_line_6' => 'Text two line 6',
            'text_two_line_7' => 'Text two line 7',
            'text_two_line_8' => 'Text two line 8',
            'text_two_line_9' => 'Text two line 9',
            'text_two_line_10' => 'Text two line 10',
            'text_two_line_11' => 'Text two line 11',
            'text_two_line_12' => 'Text two line 12',
            'text_two_line_13' => 'Text two line 13',
            'text_two_line_14' => 'Text two line 14',
            'text_two_line_15' => 'Text two line 15',
            'text_two_line_16' => 'Text two line 16',
            'text_two_line_17' => 'Text two line 17',
            'text_two_line_18' => 'Text two line 18',
            'text_two_line_19' => 'Text two line 19',
            'text_two_line_20' => 'Text two line 20',

            'date_mm_yy_1' => 'Date(MM/YY) 1',
            'date_mm_yy_2' => 'Date(MM/YY) 2',
            'date_mm_yy_3' => 'Date(MM/YY) 3',
            'date_mm_yy_4' => 'Date(MM/YY) 4',
            'date_mm_yy_5' => 'Date(MM/YY) 5',
            'date_yy_1' => 'Date(YY) 1',
            'date_yy_2' => 'Date(YY) 2',
            'date_yy_3' => 'Date(YY) 3',
            'date_yy_4' => 'Date(YY) 4',
            'date_yy_5' => 'Date(YY) 5',
            'created' => 'Project creation date',
            'updated_opp_ip' => 'Date Opportunity to In progress',
            'updated_ip_arch' => 'Date In progress to Archived',
        );
		
	// Su dung de check permission for PM
	public $budgetFields = array(
		'ProjectAmr.manual_consumed',
		'ProjectWidget.FinancePlusinv',
		'ProjectWidget.FinancePlusfon',
		'ProjectWidget.FinancePlusfinaninv',
		'ProjectWidget.FinancePlusfinanfon',
		'ProjectWidget.Synthesis',
		'ProjectWidget.InternalBudgetMD',
		'ProjectWidget.InternalBudgetEuro',
		'ProjectWidget.ExternalBudget',
		'_white_list' => array(
			'ProjectBudgetSyn.Workload',
			'ProjectBudgetSyn.Overload',
			'ProjectBudgetSyn.Consumed',
			'ProjectBudgetSyn.Remain',
			'ProjectBudgetSyn.ManualConsumed',
			'ProjectBudgetSyn.Initialworkload',
			'ProjectBudgetSyn.Completed',
		),
		'_pattern' => array(
			'/^ProjectFinance(.*)$/',
			'/^ProjectFinancePlus(.*)$/',
			'/^ProjectFinanceTwoPlus(.*)$/',
			'/^ProjectBudgetSyn(.*)$/',
		)
	);
           
    /**
     * Parse View Field
     *
     * @param array $fields the fields map to read
     * @return array, fieldset the mapping config for extract data,
     *  field and contain option of Model::find
     * @access public
     */
    public function parseViewField($fields) {
        $fieldset = $contain = array();
        foreach ((array) $fields as $field) {
            list($model, $field) = pluginSplit($field);
            $path = '';
			
            if (preg_match('/_id$/', $field)) {
                $Object = $model == $this->alias ? $this : $this->{$model};
                foreach ($Object->belongsTo as $assoc => $data) {
                    if ($field === $data['foreignKey']) {
                        if ($model !== $this->alias) {
                            $contain[$Object->alias]['fields'][] = 'id';
                            $contain[$Object->alias][$assoc] = $Object->{$assoc}->displayField;
                            $path = $model . '.0.' . $assoc . '.' . $Object->{$assoc}->displayField;
                        } else {
                            switch ($assoc) {
                                case 'Employee':
                                case 'ChiefBusiness':
                                case 'TechicalManager': {
                                        $path = array('{0} {1}', array('{n}.' . $assoc . '.first_name', '{n}.' . $assoc . '.last_name'));
                                        $contain[$assoc][] = 'first_name';
                                        $contain[$assoc][] = 'last_name';
                                        break;
                                    }
                                default : {
                                        $path = $assoc . '.' . $Object->{$assoc}->displayField;
                                        $contain[$assoc][] = $Object->{$assoc}->displayField;
                                    }
                            }
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
                }
            }
            if(in_array($field, array_keys($this->formatFields))){
                $fields = $this->formatFields[$field];
                $fieldset[] = array(
                    'key' => $model . '.' . $field,
                    'name' => $fields,
                    //'name' => Inflector::humanize(preg_replace('/_id$/', '', $fields)),
                    'path' => $path
                );
            } else {
                $fieldset[] = array(
                    'key' => $model . '.' . $field,
                    'name' => Inflector::humanize(preg_replace('/_id$/', '', $field)),
                    'path' => $path
                );
            }
			
        }
        $fields = array();
        if (isset($contain[$this->alias])) {
            $fields = $contain[$this->alias];
            unset($contain[$this->alias]);
        }
        return array($fieldset, compact('contain', 'fields'));
    }
	
	/* 
	 * Function nay khong check dieu kien cho company. 
	*/
    public function dataFromProjectTaskManualConsumeds($projectIds = null){
		$this->loadModels('ProjectTask', 'Company', 'Project', 'CompanyConfig');
		$projects = $this->Project->find('all', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $projectIds),
			'fields' => array('id', 'company_id'),
		));
		// if( count( $projects) != count($projectIds)) return false;
		if(!empty($projects))$company_id = $projects[0]['Project']['company_id'];
		// $company_id = $projects[0]['Project']['company_id'];
		if( empty( $company_id)) return false;
		$is_manual_consumed = $this->CompanyConfig->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'CompanyConfig.cf_name' => 'manual_consumed',
				'CompanyConfig.company' => $company_id,
				'CompanyConfig.cf_value' => 1
				
			),
		));
		if( empty( $is_manual_consumed)) return false;
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $projectIds,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$dataTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array(
				'project_id', 
				'sum(ProjectTask.estimated) as workload', 
				'sum(ProjectTask.manual_consumed) as consumed',
				'SUM(ProjectTask.special_consumed) AS exConsumed',
				'SUM( IF( (ProjectTask.estimated - ProjectTask.manual_consumed) > 0 , ProjectTask.estimated - ProjectTask.manual_consumed , 0)) AS remain'
			),
			'group' => array('project_id')
		));
		$dataTasks = !empty( $dataTasks) ? Set::combine($dataTasks, '{n}.ProjectTask.project_id', '{n}.0') : array();
		return $dataTasks; 
		
	}
    public function dataFromProjectTaskManualConsumed($id=null){
		$this->loadModels('ProjectTask', 'Company', 'Project', 'CompanyConfig');
		$project = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $id),
			'fields' => array('id', 'company_id'),
		));
		// debug( $project); exit;
		if( empty( $project)) return false;
		$company_id = $project['Project']['company_id'];
		if( empty( $company_id)) return false;
		$is_manual_consumed = $this->CompanyConfig->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'CompanyConfig.cf_name' => 'manual_consumed',
				'CompanyConfig.company' => $company_id,
				'CompanyConfig.cf_value' => 1
				
			),
		));
		// debug( $is_manual_consumed); exit;
		if( empty( $is_manual_consumed)) return false;
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $id,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$taskData = $this->ProjectTask->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $id,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array(
				// 'project_id', 
				'sum(ProjectTask.estimated) as workload', 
				'sum(ProjectTask.manual_consumed) as consumed',
				'SUM(ProjectTask.special_consumed) AS exConsumed'
			),
			// 'group' => array('project_id')
		));
		$taskData = !empty( $taskData) ? $taskData[0] : array('workload' => 0, 'consumed' => 0, 'exConsumed' => 0, 'remain' => 0);
		/* Tính Remain */
		$tasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $id,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array(
				'ProjectTask.estimated', 
				'ProjectTask.manual_consumed',
			),
		));
		$remain = 0;
		foreach( $tasks as $task){
			$task = $task['ProjectTask'];
			if( $task['estimated'] > $task['manual_consumed']){
				$remain += ($task['estimated'] - $task['manual_consumed']);
			}
		}
		$taskData['remain'] = $remain;
		/* Tính Remain */
		$taskData['progression'] = (floatval($taskData['workload']) != 0) ? round($taskData['consumed']*100/ $taskData['workload'],2) : 0.00;
		$taskData = array_map('floatval', $taskData); // convert string to float
		return $taskData;
	}
    public function dataFromProjectTask($id, $company_id){
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
        $ActivityTask = ClassRegistry::init('ActivityTask');
        $ProjectTask = ClassRegistry::init('ProjectTask');
        $ProjectTaskEmployeeRefer = ClassRegistry::init('ProjectTaskEmployeeRefer');
        $Project= ClassRegistry::init('Project');
        $projectTasks = $ProjectTask->find(
                "all", array(
            'fields' => array(
                'id',
                'estimated',
                'parent_id',
                'special',
                'special_consumed',
                'manual_consumed'
                ),
            'recursive' => -1,
            "conditions" => array('project_id' => $id)
                )
        );
        $_projectTaskId = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id') : array();
        $_parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.parent_id') : array();
        foreach($projectTasks as $key => $projectTask){
            if(in_array($projectTask['ProjectTask']['id'], $_parentIds)){
                unset($projectTasks[$key]);
            }
        }
        foreach($_parentIds as $k => $value){
            if($value == 0){
                unset($_parentIds[$k]);
            }
        }
        $activityTasks = $ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'project_task_id'),
            'conditions' => array(
                'project_task_id' => $_projectTaskId,
                'NOT' => array("project_task_id" => null))
        ));
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask') : array();
        $activityRequests = $ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $_activityTaskId,
                'company_id' => $company_id,
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $newActivityRequests = array();
        foreach ($activityRequests as $key => $activityRequest) {
            $newActivityRequests[$activityRequest['ActivityRequest']['task_id']] = $activityRequest[0]['value'];
        }
        $activityRequests = $newActivityRequests;
        $total = 0;
        $sumEstimated = $sumRemain = $sumRemainConsumed = $sumRemainNotConsumed = $t = 0;
        foreach ($projectTasks as $key => $projectTask) {
            $projectTaskId = $projectTask['ProjectTask']['id'];
            if($projectTask['ProjectTask']['parent_id'] == 0){
                $sumEstimated += $projectTask['ProjectTask']['estimated'];
            }
            $referencedEmployees = $ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if(count($referencedEmployees) == 0){}
            else {
                foreach ($referencedEmployees as $key1 => $referencedEmployee) {
                    $projectTasks[$key]['ProjectTask']['ProjectTaskEmployeeRefer'][] = $referencedEmployee['ProjectTaskEmployeeRefer'];
                }
            }
            $estimated = isset($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            // Check if Activity Task Existed
            if (isset($activityTasks[$projectTaskId])) {
                $activityTaskId = $activityTasks[$projectTaskId]['id'];
                // Check if Request Existed
                if (isset($activityRequests[$activityTaskId]) && empty($projectTask['ProjectTask']['special'])) {
                    $consumed = $activityRequests[$activityTaskId];
                    $overload = 0;
                    if($consumed >= $estimated){
                        $overload = $consumed - $estimated;
                    }
                    $completed = ($estimated + $overload) - $consumed;
                    if($completed < 0){
                        $completed = 0;
                    }
                    $sumRemainConsumed += $completed;
                    $total += $consumed;
                } else {
                    if(in_array($projectTask['ProjectTask']['id'], $_parentIds, true)){
                        //unset($projectTask);
                    } else {
                        if(!empty($projectTask['ProjectTask']['special']) && $projectTask['ProjectTask']['special'] == 1){
                            $specialCS = !empty($projectTask['ProjectTask']['special_consumed']) ? $projectTask['ProjectTask']['special_consumed'] : 0;
                            $sumRemainNotConsumed += ($estimated - $specialCS);
                        } else {
                            $sumRemainNotConsumed += $estimated;
                        }
                    }
                    $total += 0;
                }
            } else {
                // Error Handle
                $sumRemainNotConsumed += $estimated;
                $total += 0;
            }
        }
        if(!empty($_parentIds)){
            foreach($_parentIds as $_parentId){
                $_activityTaskId = !empty($activityTasks[$_parentId]['id']) ? $activityTasks[$_parentId]['id'] : '';
                $_consumed = !empty($activityRequests[$_activityTaskId]) ? $activityRequests[$_activityTaskId] : 0;
                $total += $_consumed;
            }
        }
        $sumRemain = $sumRemainConsumed + $sumRemainNotConsumed;
        $project = $Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $id),
                'fields' => array('activity_id')
            ));
        $_activityIdProject = $project['Project']['activity_id'];
		$activityRequests = array();
		if( !empty($_activityIdProject)){
			$activityRequests = $ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'id',
					'SUM(value) as consumed'
				),
				'conditions' => array(
					'activity_id' => $_activityIdProject,
					'company_id' => $company_id,
					'status' => 2,
					'NOT' => array('value' => 0)
				)
			));
		}
        $_engaged = $_progression = 0;
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged+$sumRemain;
        if($validated == 0){
            $_progression = 0;
        } else {
            $_progression = round((($_engaged*100)/$validated), 2);
        }
        if($_progression > 100){
            $_progression = 100;
        } else {
            $_progression = $_progression;
        }
        $_projectTask = array();
        $_projectTask['consumed'] = $_engaged;
        $_projectTask['remain'] = $sumRemain;
        $_projectTask['workload'] = $validated;
        $_projectTask['progression'] = $_progression;
        return $_projectTask;
    }
	/* caculateProgressByConsume
	 * Ticket #615
	 * For better perfomancce, This code only use for caculateProgress for 01 company
	 * % progress = (consumed* 100) / (workload )
	 */
	public function caculateProgressByConsume($projectIds=array(), $useManualConsumed){
		$this->loadModels('ActivityRequest', 'ProjectTask', 'ActivityTask' );
		$projectTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array('project_id' => $projectIds),
			'fields' => array('id', 'project_id', 'estimated', 'special', 'special_consumed', 'manual_consumed', 'parent_id'),
		));
		$listPrent = array();
		$parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
		foreach ($projectTasks as $key => $_listTask) {
			foreach ($parentIds as $parentId) {
				if ($_listTask['ProjectTask']['id'] == $parentId) {
					$listPrent[$key] = $_listTask['ProjectTask']['id'];
				}
			}
		}
		$listTaskIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
		$activityTasks = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_task_id' => $listTaskIds),
			'fields' => array('project_task_id', 'id')
		));
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $activityTasks,
				'status' => 2
            ),
            'fields' => array('id','task_id','SUM(`value`) AS valid'),
            'group' => array('task_id'),
        ));
        $activityRequests = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0') : array();
		$results = array();
        foreach($projectTasks as $key => $projectTask){
			if (in_array($projectTask['ProjectTask']['id'], $listPrent)) continue;
			$pID = $projectTask['ProjectTask']['project_id'];
			$dx_consumed = 0;
			if(empty($results[$pID]['Workload'])) $results[$pID]['Workload'] = 0;
			$results[$pID]['Workload'] += !empty($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            $projectTaskId = $projectTask['ProjectTask']['id'];
			if( $useManualConsumed ) {
				 $dx_consumed = $projectTasks[$key]['ProjectTask']['manual_consumed'];
			}else{
				if($projectTasks[$key]['ProjectTask']['special'] == 1) {
				  $dx_consumed = $projectTasks[$key]['ProjectTask']['special_consumed'];
				}
				if(isset($activityTasks[$projectTaskId])){
					$activityTaskId = $activityTasks[$projectTaskId];
					if($projectTasks[$key]['ProjectTask']['special']==1) {
						$dx_consumed = $projectTasks[$key]['ProjectTask']['special_consumed'];
					} else {
					   $dx_consumed = !empty($activityRequests[$activityTaskId]['valid']) ? $activityRequests[$activityTaskId]['valid'] : 0;
					   
					} 
				}
			}
			if(empty($results[$pID]['Consumed'])) $results[$pID]['Consumed'] = 0;
			$results[$pID]['Consumed'] += $dx_consumed;
			
        }
		foreach( $projectIds as $pID){
			if( empty( $results[$pID]['Workload'])) $results[$pID]['Workload'] = 0;
			if( empty( $results[$pID]['Consumed'])) $results[$pID]['Consumed'] = 0;
			$_workload = $results[$pID]['Workload'] ;
			$_cons = $results[$pID]['Consumed'];
			$completed = ($_workload) == 0 ? ($_cons ? 100 : 0) : round(($_cons * 100)/$_workload, 2);
			$results[$pID]['Completed'] = $completed;
		}
		return $results;
	}
	/* caculateProgressByConsume_bak
	 * For better perfomancce, This code only use for caculateProgress for 01 company
	 * % progress = (consumed* 100) / (workload + overload)
	 */
	public function caculateProgressByConsume_bak($projectIds=array(), $useManualConsumed){
		$this->loadModels('ActivityRequest', 'ProjectTask', 'ActivityTask' );
		$projectTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array('project_id' => $projectIds),
			'fields' => array('id', 'project_id', 'estimated', 'special', 'special_consumed', 'manual_consumed', 'overload', 'manual_overload', 'parent_id'),
		));
		$listPrent = array();
		$parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
		foreach ($projectTasks as $key => $_listTask) {
			foreach ($parentIds as $parentId) {
				if ($_listTask['ProjectTask']['id'] == $parentId) {
					$listPrent[$key] = $_listTask['ProjectTask']['id'];
				}
			}
		}
		$listTaskIds = !empty($projectTasks) ? Set::classicExtract($projectTasks, '{n}.ProjectTask.id') : array();
		$activityTasks = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_task_id' => $listTaskIds),
			'fields' => array('project_task_id', 'id')
		));
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $activityTasks,
				'status' => 2
            ),
            'fields' => array('id','task_id','SUM(`value`) AS valid'),
            'group' => array('task_id'),
        ));
        $activityRequests = !empty($activityRequests) ? Set::combine($activityRequests, '{n}.ActivityRequest.task_id', '{n}.0') : array();
		$results = array();
        foreach($projectTasks as $key => $projectTask){
			if (in_array($projectTask['ProjectTask']['id'], $listPrent)) continue;
			$pID = $projectTask['ProjectTask']['project_id'];
			$dx_consumed = 0;
			$dx_overload = 0;
			if(empty($results[$pID]['Workload'])) $results[$pID]['Workload'] = 0;
			$results[$pID]['Workload'] += !empty($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            $projectTaskId = $projectTask['ProjectTask']['id'];
			if( $useManualConsumed ) {
				 $dx_consumed = $projectTasks[$key]['ProjectTask']['manual_consumed'];
				 $dx_overload = $projectTasks[$key]['ProjectTask']['manual_overload'];
			}else{
				if($projectTasks[$key]['ProjectTask']['special'] == 1) {
				  $dx_consumed = $projectTasks[$key]['ProjectTask']['special_consumed'];
				}
				if(isset($activityTasks[$projectTaskId])){
					$activityTaskId = $activityTasks[$projectTaskId];
					if($projectTasks[$key]['ProjectTask']['special']==1) {
						$dx_consumed = $projectTasks[$key]['ProjectTask']['special_consumed'];
					} else {
					   $dx_consumed = !empty($activityRequests[$activityTaskId]['valid']) ? $activityRequests[$activityTaskId]['valid'] : 0;
					} 
				}
				$dx_overload = $projectTasks[$key]['ProjectTask']['overload'];
			}
			if(empty($results[$pID]['Consumed'])) $results[$pID]['Consumed'] = 0;
			if(empty($results[$pID]['Overload'])) $results[$pID]['Overload'] = 0;
			$results[$pID]['Consumed'] += $dx_consumed;
			$results[$pID]['Overload'] += $dx_overload;
			
        }
		foreach( $results as $pID => $info){
			$_workload = $results[$pID]['Workload'];
			$_overload = $results[$pID]['Overload'];
			$_cons = $results[$pID]['Consumed'];
			$completed = ($_workload + $_overload) == 0 ? round(($_cons * 100), 2) : round(($_cons * 100) / ($_workload + $_overload), 2);
			$results[$pID]['Completed'] = $completed;
		}
		// debug( $results); exit;
        return $results;
	}
	public function caculateProgressByCloseTask($projectIds, $company_id){
		$this->loadModels('ProjectTask', 'ProjectStatus');
		$closeStatus = $this->ProjectStatus->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'status' => 'CL'
			),
			'fields' => array('id')
		));
		//Fix loi tinh ca task parent. Ticket #1100 by QuanNV
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $projectIds,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$countTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('project_id', 'count(ProjectTask.id) as total_task'),
			'group' => array('project_id')
		));
		$countClosedTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'task_status_id' => array_values($closeStatus),
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('project_id', 'count(ProjectTask.id) as closed_task'),
			'group' => array('project_id')
		));
		$results = array();
		foreach($countTasks as $k => $v){
			$results[$v['ProjectTask']['project_id']]['TotalTask'] = $v['0']['total_task'];
		}
		foreach($countClosedTasks as $k => $v){
			$results[$v['ProjectTask']['project_id']]['ClosedTask'] = $v['0']['closed_task'];
		}
		foreach($projectIds as $projectId){
			if(empty( $results[$projectId]['ClosedTask'])) $results[$projectId]['ClosedTask'] = 0;
			if( empty( $results[$projectId]['TotalTask']) ){
				$results[$projectId]['TotalTask'] = 0;
				$results[$projectId]['Completed'] = 0;
			}else{
				$results[$projectId]['Completed'] = round($results[$projectId]['ClosedTask'] * 100 / $results[$projectId]['TotalTask'], 2);
			}
		}
		return $results;
	}
	public function caculateProgressByCloseTaskWithWorkload($projectIds, $company_id){
		$this->loadModels('ProjectTask', 'ProjectStatus');
		$closeStatus = $this->ProjectStatus->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'status' => 'CL'
			),
			'fields' => array('id')
		));
		//Fix loi tinh ca task parent. Ticket #1100 by QuanNV
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $projectIds,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$countTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('project_id', 'sum(ProjectTask.estimated) as total_workload'),
			'group' => array('project_id')
		));
		$countClosedTasks = $this->ProjectTask->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'task_status_id' => array_values($closeStatus),
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('project_id', 'sum(ProjectTask.estimated) as closed_workload'),
			'group' => array('project_id')
		));
		$results = array();
		foreach($countTasks as $k => $v){
			$results[$v['ProjectTask']['project_id']]['TotalWorkload'] = $v['0']['total_workload'];
		}
		foreach($countClosedTasks as $k => $v){
			$results[$v['ProjectTask']['project_id']]['ClosedWorkload'] = $v['0']['closed_workload'];
		}
		// var_dump( $results); exit;
		foreach($projectIds as $projectId){
			if(empty( $results[$projectId]['ClosedWorkload'])) $results[$projectId]['ClosedWorkload'] = '0.00';
			if( empty( $results[$projectId]['TotalWorkload']) || (floatval($results[$projectId]['TotalWorkload']) == 0) ){
				$results[$projectId]['TotalWorkload'] = '0.00';
				$results[$projectId]['Completed'] = '0.00';
			}else{
				$results[$projectId]['Completed'] = number_format($results[$projectId]['ClosedWorkload'] * 100 / $results[$projectId]['TotalWorkload'], 2);
			}
		}
		return $results;
	}
	public function caculateProgressManual($list_project_ids=array()){
		$data = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array('id' => $list_project_ids),
			'fields' => array('id', 'manual_progress')
		));
		
		// format lai
		$results = array();
		foreach( $list_project_ids as $pID){
			$results[$pID]['Completed'] = !empty($data[$pID]) ? $data[$pID] : '0.00';
		}
		// debug( $results); exit;
		return $results;
	}
	/* 
	 * Huynh 2020/05/30
	 * caculateProgress
	 * For better perfomancce, This code only use for caculateProgress for 1 company */
	public function caculateProgress($list_project_ids = array(), $configs = array(), $company_id = false ){
		$method = isset($configs['project_progress_method'] ) ? $configs['project_progress_method'] : '';
		$useManualConsumed = !empty($configs['manual_consumed']);
		if( !is_array($list_project_ids)) $list_project_ids = array($list_project_ids);
		$data = array();
		switch($method){
			case 'count_close_task': $data = $this->caculateProgressByCloseTask($list_project_ids, $company_id); break;
			case 'workload_of_close_task': $data = $this->caculateProgressByCloseTaskWithWorkload($list_project_ids, $company_id); break;
			case 'manual': $data = $this->caculateProgressManual($list_project_ids); break;
			case 'no_progress': $data = false; break;
			case 'consumed': 
			default: $data = $this->caculateProgressByConsume($list_project_ids, $useManualConsumed);
		}
		// debug( $data); exit; 
		return $data;
	}
    public function list_company_projects($company_id=null){
		$projects =  $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.company_id' => $company_id
			),
			'fields' => array('Project.id', 'Project.project_name')
		));
		return !empty($projects) ? $projects : array();
	}
	// Only check by PM
    public function project_assign_to_pm($employee_id, $permission = 'read', $company_id = null){
		$this->loadModels('ProjectEmployeeManager');
		$list = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'OR' => array(
					array('Project.project_manager_id' => $employee_id),
					array(
						'ProjectEmployeeManager.project_manager_id' => $employee_id,
						'ProjectEmployeeManager.is_profit_center' => '0',
						'type !=' => ($permission == 'write' ? 'RA' : NULL)						
					)
				)
			),
			array(
				'table' => 'project_employee_managers',
				'alias' => 'ProjectEmployeeManager',
				'conditions' => array(
					'ProjectEmployeeManager.project_id = Project.id',
				)
			),
			'fields' => array('id', 'project_name')
		));
		return !empty($list) ? $list : array();
	}
	public function project_by_pm($employee_id, $permission = 'read', $company_id = null){
		if($permission == 'read'){
			$this->loadModels('Employee', 'CompanyConfig', 'CompanyEmployeeReference');

            $employee= $this->CompanyEmployeeReference->find("first", array(
                'recursive' => -1,
                "conditions" => array("employee_id" => $employee_id, "company_id"=>$company_id)
            ));
            if( empty( $employee)) return false;

			$see_all_project = ($employee['CompanyEmployeeReference']['see_all_projects']) || ($this->CompanyConfig->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'CompanyConfig.company' => $company_id,
					'cf_name' => 'see_all_project'
				),
				'fields' => array('cf_name', 'cf_value')
			)));
			if( $see_all_project ){
				return $this->find('list', array(
					'recursive' => -1,
					'conditions' => array('Project.company_id' => $company_id),
					'fields' => array('id', 'project_name')
				));
			}
		} else {
            $this->loadModels('ProjectEmployeeManager');
		    $list = $this->ProjectEmployeeManager->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectEmployeeManager.project_manager_id' => $employee_id,
						// 'ProjectEmployeeManager.is_profit_center' => '0',
						'ProjectEmployeeManager.type !=' => ($permission == 'write' ? 'RA' : NULL)
			),
			// array(
			// 	'table' => 'project_employee_managers',
			// 	'alias' => 'ProjectEmployeeManager',
			// 	'conditions' => array(
			// 		'ProjectEmployeeManager.project_id = Project.id',
			// 	)
			// ),
			'fields' => array('project_id')
		    ));
		return !empty($list) ? $list : array();
        }
		
	}
    public function afterSave($created){
        if( !$created ){
            $pj = $this->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $this->id)
            ));
            //archived: change activity's activated to no
            if( $pj['Project']['category'] == 3 && $pj['Project']['activity_id'] ){
                $act = ClassRegistry::init('Activity');
                $act->save(array(
                    'id' => $pj['Project']['activity_id'],
                    'activated' => 0
                ));
            }
        }
    }
	public function getWorkingDays($start, $end, $allowHolidayInMonth = false){
		return $this->Behaviors->Lib->getListWorkingDays($start, $end, $allowHolidayInMonth);
	}
    // List all PM, PM-W (TM), PM-R (RA) of project
    public function listPMProject($project_id = null, $arr_Roles = array('PM')) {
        $pm=array();
        if(in_array('PM', $arr_Roles)) {
            $this->loadModel('Project');
            $pm_project = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions'=> array(
                    'id' => $project_id
                ),
                'fields' => array(
                    'project_manager_id'
                )
            ));
            $pm = array_merge($pm,$pm_project);
        }
        // Debug($pm_project);
        $query = array(
            'recursive' => -1,
            'conditions'=> array(
                'project_id' => $project_id
            ),
            'fields' => array(
                'project_manager_id'
            )
            );
        $query['conditions']['type'] = $arr_Roles;
        
        $this->loadModel('ProjectEmployeeManager');
        $pm_refer_project = $this->ProjectEmployeeManager->find('list', $query);
        // if( empty( $pm_refer_project)) $pm_refer_project = array();
        
        $pm = array_merge($pm, $pm_refer_project);
        // Debug($pm);

        return $pm;
    }
    public function listAdminCompany($company_id = null) {
        $this->loadModel('CompanyEmployeeReference');
        $admin_company = $this->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'role_id' => array(2)
            ),
            'fields' => array(
                'employee_id'
            )
            ));
        return $admin_company;
    }
    public function listAssignedTask($task_id = null) {
        $this->loadModel('ProjectTaskEmployeeRefer');
        $assigned = array();
        $query = array(
            'recursive' => -1,
            'conditions' => array(
                'project_task_id' => $task_id,
                'is_profit_center' => 0
            ),
            'fields' => array('reference_id')
        );
        $resource_assign_to = $this->ProjectTaskEmployeeRefer->find('list', $query);
        $assigned = array_merge($assigned, $resource_assign_to);
        // Debug($assigned);
        $query['conditions']['is_profit_center'] = 1;
        $pc_assign_to = $this->ProjectTaskEmployeeRefer->find('list', $query);
        if(!empty($pc_assign_to)) {
            $this->loadModel('ProfitCenter');
            $manager_pc = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $pc_assign_to,
                ),
                'fields' => array('manager_id')
            ));
            $backup_pc = $this->ProfitCenter->ProfitCenterManagerBackup->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'profit_center_id' => $pc_assign_to,
                ),
                'fields' => array('employee_id')
            ));
            $assigned = array_merge($assigned, $manager_pc, $backup_pc);
            // Debug($manager_pc);
            // Debug($backup_pc);
            // Debug($assigned);
        }
        return $assigned;
    }
}