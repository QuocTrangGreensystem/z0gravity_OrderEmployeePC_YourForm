<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectBudgetSynthesisController extends AppController {
    /**
     *
     * LUU Y: KHI CHINH SUA CONTROLLER NAY THI NHO CHINH SUA CONTROLLER
     * ------------------------ activity_budget_synthesis_controller --------------------------
     * 2 CONTROLLER NAY CO LIEN KET VOI NHAU
     * ----------CHU Y-----CHU Y-----VA CHU Y-----------------------
     *
     */
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectBudgetSynthesis';
    //var $layout = 'administrators';

    /**
     *
     * Don't using model
     *
     */
    var $uses = array();

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'Lib');

     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('synthesis');
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetSale');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('ProjectTask');
        $this->loadModel('CompanyConfigs');
        $this->loadModels('Profile', 'ProfitCenter');
		// Get setting admin internal budget
		$average_cost_default = isset($this->companyConfigs['average_cost_default']) ? $this->companyConfigs['average_cost_default'] : 0;
		$budget_euro_fill_manual = isset($this->companyConfigs['budget_euro_fill_manual']) ? $this->companyConfigs['budget_euro_fill_manual'] : 0;
		$average_euro_fill_manual = isset($this->companyConfigs['average_euro_fill_manual']) ? $this->companyConfigs['average_euro_fill_manual'] : 0;
		$consumed_used_timesheet = isset($this->companyConfigs['consumed_used_timesheet']) ? $this->companyConfigs['consumed_used_timesheet'] : 0;
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $company_id = !empty($projectName) ? $projectName['Project']['company_id'] : 0;
        // Budget Sales
        $this->ProjectBudgetSale->cacheQueries = true;
        $this->ProjectBudgetSale->recursive = -1;
        $this->ProjectBudgetSale->Behaviors->attach('Containable');
        $projectBudgetSales = $this->ProjectBudgetSale->find('all', array(
                'conditions' => array('ProjectBudgetSale.project_id' => $project_id),
                'contain' => array('ProjectBudgetInvoice' => array('billed', 'paid', 'effective_date')),
                'fields' => array('name', 'order_date', 'sold', 'man_day')
            ));
        $sales = array();
        if(!empty($projectBudgetSales)){
            foreach($projectBudgetSales as $key => $projectBudgetSale){
                $billed = $paid = $billed_check = 0;
                if(!empty($projectBudgetSale['ProjectBudgetInvoice'])){
                    foreach($projectBudgetSale['ProjectBudgetInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $billed_check += $values['billed'];
                        }
                        $billed += $values['billed'];
                        $paid += $values['paid'];
                    }
                }
                $sales[$key]['name'] = $projectBudgetSale['ProjectBudgetSale']['name'];
                $sales[$key]['order_date'] = $projectBudgetSale['ProjectBudgetSale']['order_date'];
                $sales[$key]['sold'] = $projectBudgetSale['ProjectBudgetSale']['sold'];
                $sales[$key]['man_day'] = $projectBudgetSale['ProjectBudgetSale']['man_day'];
                $sales[$key]['billed'] = $billed;
                $sales[$key]['paid'] = $paid;
                $sales[$key]['billed_check'] = $billed_check;
            }
        }
		
		$budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
		
        // Internal costs
			//Get data ProjectBudgetSyn
		$this->loadModel('ProjectBudgetSyn');
		$listBudgetSyns = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
        $internals = $internalDetails = array();
        $budgetEuro = $totalBudgetMd = 0;
        $count = $totalAverage = $_totalAverages = 0;
		$projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average', 'budget_erro')
        ));
        if(!empty($projectBudgetInternals)){
            foreach($projectBudgetInternals as $key => $projectBudgetInternal){
                $budgetMd = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                $averageDetails = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] : 0;
                // $totalAverage += $averageDetails;
                $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                $internalDetails[$key]['budget_euro'] = $budgetMd*$averageDetails;
                $budgetEuro += ($budgetMd*$averageDetails);
				$totalBudgetMd += $budgetMd;
                $count++;
            }
        }
		$totalAverage = $listBudgetSyns['internal_costs_average'];
        // if($count == 0){
            // $totalAverage = 0;
        // } else {
            // $totalAverage = round($totalAverage/$count, 2);
        // }
		// if( $average_cost_default == 0 ){
			// $_totalAverages = ($totalBudgetMd != 0) ? ($budgetEuro / $totalBudgetMd) : 0;
		// }else{
			// $_totalAverages = $totalAverage;
		// }
		$parentTasks = $this->ProjectTask->find('list', array(
		   'recursive' => -1,
		   'conditions' => array(
			   'project_id' => $project_id,
			   'parent_id >' => 0,
		   ),
		   'fields' => array('parent_id', 'parent_id'),
		   'group' => array('parent_id')
		));
		$parentTasks = !empty($parentTasks) ? array_values($parentTasks) : array();
		
		$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? intval($this->companyConfigs['manual_consumed']) : 0;
		if( $useManualConsumed){
			$getDataProjectTasks = $this->Project->dataFromProjectTaskManualConsumed($project_id);
			$externalConsumeds = $getDataProjectTasks['exConsumed'];
			$engagedErro = $getDataProjectTasks['consumed'] * $totalAverage;
			$internals = array(
				'forecastedManDay' => $getDataProjectTasks['workload'],
				'engagedEuro' => !empty($engagedErro) ? $engagedErro : 0,
				'remainEuro' => !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain']*$totalAverage : 0,
				'forecastEuro' => ($getDataProjectTasks['remain'] + $getDataProjectTasks['consumed'])*$totalAverage,
				'budgetEuro' => !empty($listBudgetSyns['internal_costs_budget']) ? $listBudgetSyns['internal_costs_budget'] : 0,
				'varEuro' => !empty($listBudgetSyns['internal_costs_var']) ? $listBudgetSyns['internal_costs_var'] : 0
			);
			// debug( $internals); exit;
		}else{
			$getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
			/* do not take into account overload 
			Ticket 526 https://nextversion.z0gravity.com/tickets/view/526#comment-5675
			Huynh Edit 2020-04-08
			*/
			
			
			$totalWorkload = $this->ProjectTask->find('all', array(
				'fields' => array(
					'SUM(ProjectTask.estimated) AS Total',
				),
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
					'special'=> 0,
					'NOT' => array(
						'id' => $parentTasks
					)
				)
			));
			$getDataProjectTasks['workload'] = !empty($totalWorkload[0][0]['Total']) ? $totalWorkload[0][0]['Total'] : 0;
			/* END do not take into account overload */
			/**
			 * Get task special
			 */
			$projectTasks = $this->ProjectTask->find('all', array(
				'fields' => array(
					'SUM(ProjectTask.estimated) AS Total',
					'SUM(ProjectTask.special_consumed) AS exConsumed'
				),
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id, 
					'special' => 1,
					'NOT' => array(
						'id' => $parentTasks
					)
				)
			));
			$varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
			$externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
			$remainExter = $varE - $externalConsumeds;
			$remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
			$internals = array(
				'forecastedManDay' => $getDataProjectTasks['consumed'] + $remainInter,
				'engagedEuro' => !empty($listBudgetSyns['internal_costs_engaged']) ? $listBudgetSyns['internal_costs_engaged'] : 0,
				'remainEuro' => !empty($listBudgetSyns['internal_costs_remain']) ? $listBudgetSyns['internal_costs_remain'] : 0,
				'forecastEuro' => !empty($listBudgetSyns['internal_costs_forecast']) ? $listBudgetSyns['internal_costs_forecast'] : 0,
				'budgetEuro' => !empty($listBudgetSyns['internal_costs_budget']) ? $listBudgetSyns['internal_costs_budget'] : 0,
				'varEuro' => !empty($listBudgetSyns['internal_costs_var']) ? $listBudgetSyns['internal_costs_var'] : 0
			);
		}
        
		
		
        
        // External costs
        $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
        ));
        $externals = array();
        if(!empty($projectBudgetExternals)){
            foreach($projectBudgetExternals as $key => $projectBudgetExternal){
                $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                $externals[$key]['name'] = $dx['name'];
                $externals[$key]['order_date'] = $dx['order_date'];
                $externals[$key]['budget_erro'] = $dx['budget_erro'];
                $externals[$key]['ordered_erro'] = $dx['ordered_erro'];
                $externals[$key]['remain_erro'] = $dx['remain_erro'];
                $externals[$key]['forecast_erro'] = $dx['ordered_erro'] + $dx['remain_erro'];
                if($dx['budget_erro'] == 0){
                    $externals[$key]['var_erro'] = round(((0) - 1)*100, 2);
                } else {
                    $externals[$key]['var_erro'] = round(((($dx['ordered_erro'] + $dx['remain_erro'])/$dx['budget_erro']) - 1)*100, 2);
                }
                //added on 2015-05-13 by QN
                $progress = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'SUM(special_consumed) as consume'
                    ),
                    'conditions' => array(
                        'external_id' => $dx['id']
                    )
                ));
                if( $progress ){
                    if( $dx['man_day'] > 0 ){
                        $externals[$key]['progress_md'] = round($progress[0]['consume'] / $dx['man_day'] * 100, 2);
                    } else {
                        $externals[$key]['progress_md'] = $dx['progress_md'];
                    }
                    $externals[$key]['consumed_md'] = $progress[0]['consume'];
                } else {
                    $externals[$key]['progress_md'] = 0;
                    $externals[$key]['consumed_md'] = 0;
                }
                $externals[$key]['man_day'] = $dx['man_day'];
                $externals[$key]['progress_erro'] = $dx['progress_erro'];
                if( $dx['progress_erro'] == 0 ){
                    $externals[$key]['progress_erro'] = round($dx['ordered_erro'] * $externals[$key]['progress_md'] / 100, 2);
                }
            }
        }
        $this->loadModels('Menu');
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenus = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        $this->loadModel('BudgetSetting');
        $company_id=$this->employee_info['Company']['id'];
        $budget_settingst=$this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name')
        ));
        $budget_settings = !empty($budget_settingst) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
		
		$this->progress_line($project_id);
		// $this->progress_line_internal($project_id);
		
        $employee_info = $this->employee_info;
        $this->set(compact('projectName', 'project_id', 'sales', 'internalDetails', 'internals', 'externals', 'settingMenus', 'employee_info', 'budget_settings', 'listBudgetSyns'));
        if($projectName && $projectName['Project']['category'] == 2){
            // $this->action = 'oppor';
        }
    }
	function ajax($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('synthesis');
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetSale');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('ProjectTask');
        $this->loadModel('CompanyConfigs');
        $this->loadModels('Profile', 'ProfitCenter');
		// Get setting admin internal budget
		$average_cost_default = isset($this->companyConfigs['average_cost_default']) ? $this->companyConfigs['average_cost_default'] : 0;
		$budget_euro_fill_manual = isset($this->companyConfigs['budget_euro_fill_manual']) ? $this->companyConfigs['budget_euro_fill_manual'] : 0;
		$average_euro_fill_manual = isset($this->companyConfigs['average_euro_fill_manual']) ? $this->companyConfigs['average_euro_fill_manual'] : 0;
		$consumed_used_timesheet = isset($this->companyConfigs['consumed_used_timesheet']) ? $this->companyConfigs['consumed_used_timesheet'] : 0;
        $projectName = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $project_id)
            ));
        $company_id = !empty($projectName) ? $projectName['Project']['company_id'] : 0;
        // Budget Sales
        $this->ProjectBudgetSale->cacheQueries = true;
        $this->ProjectBudgetSale->recursive = -1;
        $this->ProjectBudgetSale->Behaviors->attach('Containable');
        $projectBudgetSales = $this->ProjectBudgetSale->find('all', array(
                'conditions' => array('ProjectBudgetSale.project_id' => $project_id),
                'contain' => array('ProjectBudgetInvoice' => array('billed', 'paid', 'effective_date')),
                'fields' => array('name', 'order_date', 'sold', 'man_day')
            ));
        $sales = array();
        if(!empty($projectBudgetSales)){
            foreach($projectBudgetSales as $key => $projectBudgetSale){
                $billed = $paid = $billed_check = 0;
                if(!empty($projectBudgetSale['ProjectBudgetInvoice'])){
                    foreach($projectBudgetSale['ProjectBudgetInvoice'] as $values){
                        if(!empty($values['effective_date']) && $values['effective_date'] != '0000-00-00'){
                            $billed_check += $values['billed'];
                        }
                        $billed += $values['billed'];
                        $paid += $values['paid'];
                    }
                }
                $sales[$key]['name'] = $projectBudgetSale['ProjectBudgetSale']['name'];
                $sales[$key]['order_date'] = $projectBudgetSale['ProjectBudgetSale']['order_date'];
                $sales[$key]['sold'] = $projectBudgetSale['ProjectBudgetSale']['sold'];
                $sales[$key]['man_day'] = $projectBudgetSale['ProjectBudgetSale']['man_day'];
                $sales[$key]['billed'] = $billed;
                $sales[$key]['paid'] = $paid;
                $sales[$key]['billed_check'] = $billed_check;
            }
        }
		
		$budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id)
        ));
		
        // Internal costs
        $getDataProjectTasks = $this->Project->dataFromProjectTask($project_id, $projectName['Project']['company_id']);
		/* do not take into account overload 
		Ticket 526 https://nextversion.z0gravity.com/tickets/view/526#comment-5675
		Huynh Edit 2020-04-08
		*/
		$totalWorkload = $this->ProjectTask->find('all', array(
			'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special'=> 0)
		));
		// debug( $getDataProjectTasks );
		$getDataProjectTasks['workload'] = !empty($totalWorkload[0][0]['Total']) ? $totalWorkload[0][0]['Total'] : 0;
		
		 /* END do not take into account overload */
        $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average', 'budget_erro')
        ));
		
		//Get data ProjectBudgetSyns
		$this->loadModel('ProjectBudgetSyns');
		$listBudgetSyns = $this->ProjectBudgetSyns->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_budget', 'internal_costs_forecast', 'internal_costs_var', 'internal_costs_engaged', 'internal_costs_remain', 'external_costs_budget', 'external_costs_forecast', 'external_costs_var', 'external_costs_ordered')
		));
		$listBudgetSyns = !empty($listBudgetSyns) ? $listBudgetSyns[0]['ProjectBudgetSyns'] : 0;
		
        $internals = $internalDetails = array();
        $budgetEuro = $totalBudgetMd = 0;
        $count = $totalAverage = $_totalAverages = 0;
		
        if(!empty($projectBudgetInternals)){
            foreach($projectBudgetInternals as $key => $projectBudgetInternal){
                $budgetMd = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                $averageDetails = !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] : 0;
                $totalAverage += $averageDetails;
                $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                $internalDetails[$key]['budget_euro'] = $budgetMd*$averageDetails;
                $budgetEuro += ($budgetMd*$averageDetails);
				$totalBudgetMd += $budgetMd;
                $count++;
            }
        }
        if($count == 0){
            $totalAverage = 0;
        } else {
            $totalAverage = round($totalAverage/$count, 2);
        }
		if( $average_cost_default == 0 ){
			$_totalAverages = ($totalBudgetMd != 0) ? ($budgetEuro / $totalBudgetMd) : 0;
		}else{
			$_totalAverages = $totalAverage;
		}
        /**
         * Get task special
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id, 'special' => 1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $remainExter = $varE - $externalConsumeds;
        $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
        $internals = array(
            'forecastedManDay' => $getDataProjectTasks['consumed'] + $remainInter,
            'engagedEuro' => !empty($listBudgetSyns['internal_costs_engaged']) ? $listBudgetSyns['internal_costs_engaged'] : 0,
            'remainEuro' => !empty($listBudgetSyns['internal_costs_remain']) ? $listBudgetSyns['internal_costs_remain'] : 0,
            'forecastEuro' => !empty($listBudgetSyns['internal_costs_forecast']) ? $listBudgetSyns['internal_costs_forecast'] : 0,
            'budgetEuro' => !empty($listBudgetSyns['internal_costs_budget']) ? $listBudgetSyns['internal_costs_budget'] : 0,
            'varEuro' => !empty($listBudgetSyns['internal_costs_var']) ? $listBudgetSyns['internal_costs_var'] : 0
        );
        // External costs
        $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
        ));
        $externals = array();
        if(!empty($projectBudgetExternals)){
            foreach($projectBudgetExternals as $key => $projectBudgetExternal){
                $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                $externals[$key]['name'] = $dx['name'];
                $externals[$key]['order_date'] = $dx['order_date'];
                $externals[$key]['budget_erro'] = $dx['budget_erro'];
                $externals[$key]['ordered_erro'] = $dx['ordered_erro'];
                $externals[$key]['remain_erro'] = $dx['remain_erro'];
                $externals[$key]['forecast_erro'] = $dx['ordered_erro'] + $dx['remain_erro'];
                if($dx['budget_erro'] == 0){
                    $externals[$key]['var_erro'] = round(((0) - 1)*100, 2);
                } else {
                    $externals[$key]['var_erro'] = round(((($dx['ordered_erro'] + $dx['remain_erro'])/$dx['budget_erro']) - 1)*100, 2);
                }
                //added on 2015-05-13 by QN
                $progress = $this->ProjectTask->find('first', array(
                    'recursive' => -1,
                    'fields' => array(
                        'SUM(special_consumed) as consume'
                    ),
                    'conditions' => array(
                        'external_id' => $dx['id']
                    )
                ));
                if( $progress ){
                    if( $dx['man_day'] > 0 ){
                        $externals[$key]['progress_md'] = round($progress[0]['consume'] / $dx['man_day'] * 100, 2);
                    } else {
                        $externals[$key]['progress_md'] = $dx['progress_md'];
                    }
                    $externals[$key]['consumed_md'] = $progress[0]['consume'];
                } else {
                    $externals[$key]['progress_md'] = 0;
                    $externals[$key]['consumed_md'] = 0;
                }
                $externals[$key]['man_day'] = $dx['man_day'];
                $externals[$key]['progress_erro'] = $dx['progress_erro'];
                if( $dx['progress_erro'] == 0 ){
                    $externals[$key]['progress_erro'] = round($dx['ordered_erro'] * $externals[$key]['progress_md'] / 100, 2);
                }
            }
        }
        $this->loadModels('Menu');
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenus = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        $this->loadModel('BudgetSetting');
        $company_id=$this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' =>$company_id ,
                'currency_budget' =>1,
            ),
            'fields' => array('name')
        ));
        $budget_settings = !empty($budget_settingst) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
		
		$this->progress_line($project_id);
		// $this->progress_line_internal($project_id);
		
        $employee_info = $this->employee_info;
        $this->set(compact('projectName', 'project_id', 'sales', 'internalDetails', 'internals', 'externals', 'settingMenus', 'employee_info', 'budget_settings', 'listBudgetSyns'));
        // if($projectName && $projectName['Project']['category'] == 2){
            // $this->render('ajax_oppor');
        // } else {
            // $this->render('ajax');
        // }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parse($activity_id) {
        if (!($employeeName = $this->_getEmpoyee())) {
            $this->Session->setFlash(__('Your account do not have a company to management activity.', true), 'error');
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        $this->loadModel('ActivityRequest');
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $activity_id,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'activity_id')
        ));
        $groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'task_id' => $groupTaskId,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $_sumActivitys = $_sumEmployees = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach($activityTasks as $activityTask){
            foreach($_sumEmployees as $id => $_sumEmployee){
                if($activityTask['ActivityTask']['id'] == $id){
                    $dataFromEmployees[$activityTask['ActivityTask']['activity_id']][] = $_sumEmployee;
                }
            }
        }
        $rDatas = array();
        if(!empty($dataFromEmployees)){
            foreach($dataFromEmployees as $id => $dataFromEmployee){
                foreach($dataFromEmployee as $values){
                    foreach($values as $employ => $value){
                        if(!isset($rDatas[$id][$employ])){
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $_dataFromEmployees = array();
        if(!empty($rDatas)){
            foreach($rDatas as $id => $rData){
                if(in_array($id, array_keys($sumEmployees))){

                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if(!empty($sumEmployees)){
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if(!empty($rDatas)){
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                            )), '{n}.Employee.id', '{n}.Employee');

        $setDatas = array();
        $setDatas['sumEmployees'] = !empty($sumEmployees) ? $sumEmployees : array();
        $setDatas['employees'] = !empty($employees) ? $employees : array();

        return $setDatas;
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getEmpoyee() {
        if (empty($this->employee_info['Company']['id'])) {
            return false;
        }
        $this->employee_info['Employee']['company_id'] = $this->employee_info['Company']['id'];
        $this->set('company_id', $this->employee_info['Company']['id']);
        $this->set('employee_id', $this->employee_info['Employee']['id']);
        return $this->employee_info['Employee'];
    }

    /**
     * Trong table:
     * - project_budget_sales
     * - project_budget_invoices
     * - project_budget_internals
     * - project_budget_externals
     * - project_budget_external_details
     * Tim nhung project nao co linked voi activity thi them id cua activity(activity_id) do vao cac table tren.
     * Neu khong linked thi mac dinh activity_id = 0
     */
    public function addActivityIdToAllTable(){
        $this->loadModel('Project');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetInvoice');
        $this->loadModel('ProjectBudgetSale');
        /**
         * Lay danh sach project lien ket voi activity
         */
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        /**
         * Lay tat ca du lieu o table ProjectBudgetExternal
         */
        $externals = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($externals)){
            foreach($externals as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetExternal->id = $id;
                $this->ProjectBudgetExternal->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInternal
         */
        $internals = $this->ProjectBudgetInternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($internals)){
            foreach($internals as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInternal->id = $id;
                $this->ProjectBudgetInternal->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInternalDetail
         */
        $internalDetails = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($internalDetails)){
            foreach($internalDetails as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInternalDetail->id = $id;
                $this->ProjectBudgetInternalDetail->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetInvoice
         */
        $invoices = $this->ProjectBudgetInvoice->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($invoices)){
            foreach($invoices as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetInvoice->id = $id;
                $this->ProjectBudgetInvoice->save($_saved);
            }
        }
        /**
         * Lay tat ca du lieu o table ProjectBudgetSale
         */
        $sales = $this->ProjectBudgetSale->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id')
        ));
        /**
         * Luu activity_id cua table nay
         */
        if(!empty($sales)){
            foreach($sales as $id => $project_id){
                $_saved['activity_id'] = !empty($projects[$project_id]) ? $projects[$project_id] : 0;
                $this->ProjectBudgetSale->id = $id;
                $this->ProjectBudgetSale->save($_saved);
            }
        }
        echo 'Finish Roi nhe! Met qua!';
        exit;
    }
	public function progress_line($project_id, $return_type=null ){
		$this->_checkRole(false, $project_id);
		$menuBudgets = $this->Menu->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'controllers' => array('project_budget_internals', 'project_budget_externals', 'project_budget_synthesis'),
				'functions' => 'index',
				'model' => 'project',
				// 'display' => 1
			),
			'order' => array('id DESC'),
			'fields' => array( 'controllers', 'display')
		));
		$this->set(compact('menuBudgets', 'project_id'));
		//Graph external
		$this->loadModel('ProjectBudgetExternal');
		$dataset_externals = array();
		$manday_externals = 0;
		$listExternals = $this->ProjectBudgetExternal->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'expected_date', 'due_date')
		));
		
		$countLine = 0;
		if(!empty($listExternals)){
			$countLine = count($listExternals);
			$listExternals = !empty( $listExternals ) ? Set::combine($listExternals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal') : array();
		}
		$minDateEx = 0;
		$maxDateEx = 0;
		$arrayVals = array();
		$ex_budget =  $ex_ordered =  $ex_forecast = array();
		foreach($listExternals as $keyEx => &$valEx){
			$valEx['order_date'] = (!empty($valEx['order_date']) && $valEx['order_date'] != '0000-00-00') ? strtotime( date( 'Y-m-01', strtotime( $valEx['order_date']))) : 0;
			$valEx['expected_date'] = (!empty($valEx['expected_date']) && $valEx['expected_date'] != '0000-00-00') ? strtotime( date( 'Y-m-01', strtotime( $valEx['expected_date']))) : 0;
			$valEx['due_date'] = (!empty($valEx['due_date']) && $valEx['due_date'] != '0000-00-00') ? strtotime( date( 'Y-m-01', strtotime( $valEx['due_date']))) : 0;
			$arrayVals[] = $valEx['order_date'];
			$arrayVals[] = $valEx['expected_date'];
			$arrayVals[] = $valEx['due_date'];
			if( $valEx['order_date']) { // budget <-> order_date
				if( empty($ex_budget[$valEx['order_date']])) $ex_budget[$valEx['order_date']] = 0;
				$ex_budget[$valEx['order_date']] += (float)$valEx['budget_erro'];
			}
			if( $valEx['expected_date']) { // forecast <-> expected_date
				if( empty($ex_forecast[$valEx['expected_date']])) $ex_forecast[$valEx['expected_date']] = 0;
				$ex_forecast[$valEx['expected_date']] += ( (float)$valEx['ordered_erro'] + (float)$valEx['remain_erro'] );
			}
			if( $valEx['due_date']) { // ordered <-> due_date
				if( empty($ex_ordered[$valEx['due_date']])) $ex_ordered[$valEx['due_date']] = 0;
				$ex_ordered[$valEx['due_date']] += (float)$valEx['ordered_erro'];
			}
		}
		$arrayVals = array_filter($arrayVals);
		$minDateEx = !empty($arrayVals) ? min ($arrayVals) : strtotime(date('Y').'-02-01');
		$maxDateEx = (!empty($arrayVals)) ? max($arrayVals) : strtotime(date('Y').'-11-30');
		$dateEx = strtotime('previous month', strtotime(date( 'Y-m-01', $minDateEx)));
		$lastValue = array(
			'budget' => 0,
			'forecast' => 0,
			'ordered' => 0,
		);
		$dataset_externals[$dateEx] = $lastValue;
		while($dateEx <= $maxDateEx){
			$lastValue['date'] = $dateEx;
			$lastValue['date_format'] = date('m/y', $dateEx);
			if( !empty($ex_budget[$dateEx])) $lastValue['budget'] += $ex_budget[$dateEx];
			if( !empty($ex_forecast[$dateEx])) $lastValue['forecast'] += $ex_forecast[$dateEx];
			if( !empty($ex_ordered[$dateEx])) $lastValue['ordered'] += $ex_ordered[$dateEx];
			$dataset_externals[$dateEx] = $lastValue;
			$dateEx = strtotime('next month', $dateEx);
		}
		$totalExternalForecast = $lastValue['forecast'];
		$max_externals = round( max( $lastValue['budget'], $lastValue['forecast'], $lastValue['ordered'])*1.1, 2); 
		ksort( $dataset_externals);
		$countLineExter = count($dataset_externals);
		$this->set(compact('listExternals', 'countLineExter', 'dataset_externals', 'max_externals'));
		//END Graph external
		
		//Graph Internal
		$manual_consumed = (!empty($this->companyConfigs['manual_consumed']) && $this->companyConfigs['manual_consumed'] != '0');
		if($manual_consumed ){
			$this->loadModels('ProjectBudgetInternal');
			$dataset_internals = array();
			$max_internal_md = $max_internal_euro = $countLineIn = 0;
			$internal_data = $this->ProjectBudgetInternal->progress_line_manual_consumed($project_id, $this->employee_info['Company']['id']);
			extract($internal_data);
			$this->set(compact('dataset_internals', 'max_internal_md', 'max_internal_euro', 'countLineIn'));
			// return true;
		}else{
			$staffings = array();
			$staffings = $this->_staffingEmloyee($project_id);
			$dataset_internals = array();
			$dataset_internals = $this->_totalStaffing($staffings, $project_id);
			$this->loadModel('ProjectBudgetInternalDetail', 'ProjectBudgetSyn');
			$valueInternals = $this->ProjectBudgetInternalDetail->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('validation_date', 'budget_md', 'budget_erro')
			));
			$projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('internal_costs_average','internal_costs_forecast')
			));
			$project_tjm = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_average'];	
			$internal_costs_forecast = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_forecast'];		
			// Add value budget MD/Euro to data staffing 
			if( !empty( $valueInternals)){
				foreach($valueInternals as $interValue){
					$date = $interValue['ProjectBudgetInternalDetail']['validation_date'];
					if( empty( $date) || ($date == '0000-00-00')) continue;
					$date = strtotime( $date );
					$date = strtotime( date( 'Y-m-01', $date));
					if( empty( $dataset_internals[$date]['budget_md'])) $dataset_internals[$date]['budget_md'] = 0;
					$dataset_internals[$date]['budget_md'] += (float)$interValue['ProjectBudgetInternalDetail']['budget_md'];
					if( empty( $dataset_internals[$date]['budget_price'])) $dataset_internals[$date]['budget_price'] = 0;
					$dataset_internals[$date]['budget_price'] += (float)$interValue['ProjectBudgetInternalDetail']['budget_erro'];
				}
			}
			unset($valueInternals);
			$list_month = array_filter(array_keys($dataset_internals));
			$date = !empty($list_month) ? min($list_month) : strtotime(date('Y').'-02-01');
			$maxDate = !empty($list_month) ? max($list_month) : strtotime(date('Y').'-12-01');
			$first_month = strtotime('previous month', $date);
			$lastValue = array(
				'date' => $first_month,
				'date_format' => date( 'm/y' ,$first_month),
				'validated' => 0,
				'validated_price' => $internal_costs_forecast,
				'consumed' => 0,
				'consumed_price' => 0,
				'budget_md' => 0,
				'budget_price' => 0,
			);
			$dataset_internals[$first_month] = $lastValue;
			$current = time();
			while( $date <= $maxDate){
				$data = isset($dataset_internals[$date]) ? $dataset_internals[$date] : array();
				$lastValue['date'] = $date;
				$lastValue['date_format'] = date('m/y', $date);
				$lastValue['validated'] += !empty($data['validated']) ? $data['validated'] : 0;
				// $lastValue['validated_price'] = $data['validated_price'] ;
				$lastValue['consumed'] += !empty($data['consumed']) ? $data['consumed'] : 0;
				$lastValue['consumed_price'] += !empty($data['consumed_price']) ? $data['consumed_price'] : 0;
				$lastValue['budget_md'] += !empty($data['budget_md']) ? $data['budget_md'] : 0;
				$lastValue['budget_price'] += !empty($data['budget_price']) ? $data['budget_price'] : 0;
				$dataset_internals[$date] = $lastValue;
				$date = strtotime('next month', $date);
			}
			ksort($dataset_internals);
			//get max value for display ( + 10%)
			// if( empty($lastValue['validated_price'])) $lastValue['validated_price'] = 0;
			$max_internal_md = round( max( $lastValue['validated'], $lastValue['consumed'], $lastValue['budget_md'])*1.1, 2); 
			$max_internal_euro = round( max( $lastValue['validated_price'], $lastValue['consumed_price'], $lastValue['budget_price'])*1.1, 2);
			if(!empty($dataset_internals)) $countLineIn = count($dataset_internals);
			$this->set(compact('dataset_internals', 'max_internal_md', 'max_internal_euro', 'countLineIn'));
		}
		//END Graph Internal
		
		
		/* Bugget Total */
		$dataSyns = array();
		$manDayMaxSyn = 0;
		$countLineSyn = 0;
		$total_key = array_unique(array_merge( array_keys($dataset_externals), array_keys($dataset_internals)));
		sort( $total_key);
		$lastValue = array(
			'budget' => 0,
			'forecast' => $internal_costs_forecast + $totalExternalForecast,
			// 'forecast' => ($manual_consumed ? 0 : ($internal_costs_forecast + $totalExternalForecast)),
			'engared' => 0,
		);
		$lastInter = array(
			'budget' => 0,
			'engared' => 0,
		);
		$lastEx = array(
			'budget' => 0,
			'engared' => 0,
		);
		foreach( $total_key as $date){
			$lastValue['date'] = $date;
			$lastValue['date_format'] = date('m/y', $date);
			
			if( isset( $dataset_internals[$date]['budget_price']) ) 
				$lastInter['budget'] = $dataset_internals[$date]['budget_price'];
			if( isset( $dataset_externals[$date]['budget']) ) 
				$lastEx['budget'] = $dataset_externals[$date]['budget'];
			$lastValue['budget'] = $lastInter['budget'] + $lastEx['budget'];
			
			// if( $manual_consumed ){
			// if( isset( $dataset_internals[$date]['validated_price']) ) 
				// $lastValue['forecast'] += $dataset_internals[$date]['validated_price'];
			// if( isset( $dataset_externals[$date]['forecast']) ) 
				// $lastValue['forecast'] += $dataset_externals[$date]['forecast'];
			// }
			
			if( isset( $dataset_internals[$date]['consumed_price']) ) 
				$lastInter['engared'] = $dataset_internals[$date]['consumed_price'];
			if( isset( $dataset_externals[$date]['ordered']) ) 
				$lastEx['engared'] = $dataset_externals[$date]['ordered'];
			$lastValue['engared'] = $lastInter['engared'] + $lastEx['engared'];
			
			$dataSyns[$date] = $lastValue;
		}
		$manDayMaxSyn = round( max($lastValue['budget'], $lastValue['forecast'], $lastValue['engared']) *1.1, 2); 
        if(!empty($dataSyns)) $countLineSyn = count($dataSyns);
		$this->set(compact('dataSyns', 'manDayMaxSyn', 'countLineSyn'));
		
		/* END Bugget Total */
		
		if( $return_type == 'ajax'){
			$dataset_externals = array_values($dataset_externals);
			$dataset_internals = array_values($dataset_internals);
			$dataSyns = array_values($dataSyns);
			die(json_encode( compact('countLineExter', 'dataset_externals', 'max_externals', 'dataset_internals', 'max_internal_md', 'max_internal_euro', 'countLineIn', 'dataSyns', 'manDayMaxSyn', 'countLineSyn')));
		}
	}
	private function _staffingEmloyee($project_id = null){
        $this->loadModels('TmpStaffingSystem', 'Profile', 'ProfitCenter');
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay du lieu staffing cho employee
         */
        $getDatas = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'model' => 'employee',
                'company_id' => $employeeName['company_id']
            )
        ));
		
        $staffing_data = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * Employee => array(
         *      'time' => array()
         * )
         */
        if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
                // date
                $staffing_data[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($staffing_data[$dx['model_id']][$dx['date']]['validated'])){
                    $staffing_data[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $staffing_data[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($staffing_data[$dx['model_id']][$dx['date']]['consumed'])){
                    $staffing_data[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $staffing_data[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                // $staffing_data[$dx['model_id']][$dx['date']]['remains'] = $staffing_data[$dx['model_id']][$dx['date']]['validated'] - $staffing_data[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        $ids = !empty($staffing_data) ? array_keys($staffing_data) : array();
        $this->loadModel('Employee');
		
        $employees = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $ids),
			'fields' => array('id', 'fullname', 'tjm', 'profile_id', 'profit_center_id'),
			'order' => array('Employee.id')
		));
		$employees = !empty($employees) ? Set::combine($employees, '{n}.Employee.id', '{n}.Employee') : array();
		
		/* Get Employee TJM */
		// TJM of profil		
		$profiles = !empty($employees) ? Set::classicExtract($employees, '{n}.profile_id') : array();
		$profiles_tjm = array();
		if(!empty($profiles)){
			$profiles_tjm =  $this->Profile->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => array_filter($profiles)),
					'fields' => array('id', 'tjm'), 
				)
			);
			$profiles_tjm = !empty($profiles_tjm) ? Set::combine($profiles_tjm, '{n}.Profile.id', '{n}.Profile.tjm') : array();
		}
		// TJM of TEAM
		$profit_centers = !empty($employees) ? Set::classicExtract($employees, '{n}.profit_center_id') : array();
		$team_tjm = array();
		if(!empty($profit_centers)){
			$team_tjm =  $this->ProfitCenter->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => array_filter($profit_centers)),
					'fields' => array('id', 'tjm'), 
				)
			);
		$team_tjm = !empty($team_tjm) ? Set::combine($team_tjm, '{n}.ProfitCenter.id', '{n}.ProfitCenter.tjm') : array();
		}
		// TJM of Project
		$this->loadModel('ProjectBudgetSyn');
		$project_tjm = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average')
		));
		$project_tjm = (float)$project_tjm['ProjectBudgetSyn']['internal_costs_average'];	
		foreach ($employees as &$employee){
			if( empty( $employee['tjm'])){
				if( !empty( $profiles_tjm[$employee['profile_id']])) 
					$employee['tjm'] = $profiles_tjm[$employee['profile_id']];
				elseif( !empty( $team_tjm[$employee['profit_center_id']])) 
					$employee['tjm'] =  $team_tjm[$employee['profit_center_id']];
				else
					$employee['tjm'] = $project_tjm;
			}
		}
		$this->employee_tjms = $employees;
		// debug( $employees); exit;
		/* END Get Employee TJM */
		
		/* Caculate Consumed and workload by Price ( Default Euro)*/
        if(!empty($staffing_data['999999999'])){
            $notAffected = array(
				'id' => '999999999',
				'fullname' => 'Not Affected',
				'tjm' => $project_tjm
            );
            $employees['999999999'] = $notAffected;
        }
        $staffings = array();
		
		// Phan nay se kiem tra $companyConfigs['consumed_used_timesheet'] o function totalStaffing;		
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
		if( !empty( $staffing_data)){
			foreach( $staffing_data as $e_id => $data ){
				foreach($data as $date => $date_data){
					if( !$consumed_used_timesheet){
						$staffing_data[$e_id][$date]['consumed_price'] = (float)$date_data['consumed'] * $employees[$e_id]['tjm'];
					}
				}
			}
		}
        return $staffing_data;
    }
	private function _totalStaffing($staffings, $project_id=null){
        $totalStaffing = array();
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
        $projectBudgetSyn = $this->ProjectBudgetSyn->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('internal_costs_average','internal_costs_forecast')
		));
		$project_tjm = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_average'];	
		$internal_costs_forecast = (float)$projectBudgetSyn['ProjectBudgetSyn']['internal_costs_forecast'];		
		$consumeds = array();
		if( $consumed_used_timesheet) {
			$this->loadModels('ActivityRequest', 'Project', 'ActivityTask', 'ProjectBudgetSyn');
			
			$activity_id = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $project_id),
				'fields' => 'activity_id'
			));
			$activity_id = !empty( $activity_id ) ? $activity_id['Project']['activity_id'] : 0;
			$activityTasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('activity_id' => $activity_id),
				'fields' => array('id', 'activity_id')
			));
			$groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
			$data_activity = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'employee_id', 
					'value',
					'date',
					'cost_price_resource',  
					'cost_price_team', 
					'cost_price_profil'
				),  
				'conditions' => array(
					'OR' => array(
						array(
							'activity_id' => $activity_id,
							'task_id' => 0
						),
						array(
							'activity_id' => 0,
							'task_id' => array_values($groupTaskId)
						),
					),
					'status' => 2,
				),
			));
			$employees = $this->employee_tjms;
			if(!empty($data_activity)){
				foreach($data_activity as $key => $activity){
					$activity = $activity['ActivityRequest'];
					$date = strtotime( date( 'Y-m', $activity['date']) . '-01');
					$e_id = $activity['employee_id'];
					$e_tjm = !empty( $employees[$e_id]['tjm'] ) ? $employees[$e_id]['tjm'] : $project_tjm;
					if((float)$activity['cost_price_resource'] > 0) {
						$consumed = $activity['cost_price_resource'];
					}elseif((float)$activity['cost_price_profil'] > 0){
						$consumed = $activity['cost_price_profil'];
					}elseif((float)$activity['cost_price_team'] > 0){
						$consumed = $activity['cost_price_team'];
					}else{
						$consumed = $activity['value']*$e_tjm;
					}
					if( empty( $consumeds[$date]['consumed'])) $consumeds[$date]['consumed'] = 0;
					if( !isset( $consumeds[$date]['consumed_price'])) $consumeds[$date]['consumed_price'] = 0;
					$consumeds[$date]['consumed'] += $activity['value'];
					$consumeds[$date]['consumed_price'] += $consumed;
				}
			}
		}
		if(!empty($staffings)){
            foreach($staffings as $employee_id => $staffing){
                foreach($staffing as $time => $values){
                    $_time = date('m/y', $values['date']);
                    $totalStaffing[$time]['date_format'] = $_time;
                    $totalStaffing[$time]['date'] = $time;
					if( $consumed_used_timesheet) {
						$totalStaffing[$time]['consumed'] = 0;
						$totalStaffing[$time]['consumed_price'] = 0;
					}else{
						if(!isset($totalStaffing[$time]['consumed'])){
							$totalStaffing[$time]['consumed'] = 0;
						}
						$totalStaffing[$time]['consumed'] += $values['consumed'];
						if(!isset($totalStaffing[$time]['consumed_price'])){
							$totalStaffing[$time]['consumed_price'] = 0;
						}
						$totalStaffing[$time]['consumed_price'] += $values['consumed_price'];
					}
                    if(!isset($totalStaffing[$time]['validated'])){
                        $totalStaffing[$time]['validated'] = 0;
                    }
                    $totalStaffing[$time]['validated'] += $values['validated'];
                }
            }
        }
		if( $consumed_used_timesheet) {
			foreach( $consumeds as $date => $consumed){
				if( !isset( $totalStaffing[$date])){
					$totalStaffing[$date] = array(
						'date_format' => date('m/y',$date),
						'date' => $date,
						'validated' => 0,
						// 'validated_price' => 0,
						// 'remains' => 0,
						'consumed' => $consumed['consumed'],
						'consumed_price' => $consumed['consumed_price'],
					);
				}else{
					$totalStaffing[$date]['consumed'] = $consumed['consumed'];
					$totalStaffing[$date]['consumed_price'] = $consumed['consumed_price'];
				}
			}
		}
        ksort($totalStaffing);
		// debug( $totalStaffing); exit;
		return $totalStaffing;
    }
	//End Graph Internal
	
}
?>