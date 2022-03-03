<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class CompaniesController extends AppController {

	/**
	 * Controller name
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Companies';

	/**
	 * Helpers used by the Controller
	 *
	 * @var array
	 * @access public
	 */
	var $helpers = array('Validation');

	/**
	 * index
	 *
	 * @return void
	 * @access public
	 */
	function index() {
		$this->Company->recursive = 0;
		$this->paginate = array(
			'fields' => array('company_name'),
			'limit' => 1000
		);
		$this->set('companies', $this->paginate());
		$is_sas = $this->employee_info["Employee"]["is_sas"];
		if ($this->employee_info["Employee"]["is_sas"] != 1)
			$company_id = $this->employee_info["Company"]["id"];
		else{
			$company_id = "";
		}
		if ($company_id != "") {
			$tree = $this->Company->getTreeList($company_id);
			$isAdminLevel2 = ($this->employee_info["Company"]["parent_id"] == "") ? false : true;
		} else {
			$tree = $this->Company->generateTreeList(null, null, null, '--');
			$isAdminLevel2 = false;
		}
		
		$arr_t = array();
		$modules = $this->Company->find('all' , array(
			'fields' => array('*'),
			'conditions' => array('id' => array_keys($tree)),
			'recursive' => -1,
		));
		$modules = Set::combine($modules, '{n}.Company.id', '{n}.Company');
		foreach ($tree as $key => $value) {
			$arr_v = array();
			$parent_node = $this->Company->getparentnode($key);
			if (!empty($parent_node)) {
				$arr_v[$key]["name"] = $value;
				$arr_v[$key]["parent_id"] = $parent_node["Company"]["id"];
			} else {
				$arr_v[$key]["name"] = $value;
				$arr_v[$key]["parent_id"] = 0;
			}
			$arr_v[$key]['day_established'] = date('d-m-Y',$this->Company->getDayEstablished($key));
			$arr_v[$key]['module_pms'] = $modules[$key]['module_pms'];
			$arr_v[$key]['module_rms'] = $modules[$key]['module_rms'];
			$arr_v[$key]['module_audit'] = $modules[$key]['module_audit'];
			$arr_v[$key]['module_report'] = $modules[$key]['module_report'];
			$arr_v[$key]['module_busines'] = $modules[$key]['module_busines'];
			$arr_v[$key]['module_zogmsgs'] = $modules[$key]['module_zogmsgs'];
			$arr_v[$key]['module_ticket'] = $modules[$key]['module_ticket'];
			$arr_v[$key]['multi_country'] = $modules[$key]['multi_country'];
			$arr_v[$key]['manage_hours'] = $modules[$key]['manage_hours'];
			$arr_v[$key]['module_license'] = $modules[$key]['module_license'];
			$arr_v[$key]['unit'] = $modules[$key]['unit'];
			$arr_v[$key]['ratio'] = $modules[$key]['ratio'];
			$arr_v[$key]['day_alert_billing'] = !empty($modules[$key]['day_alert_billing']) ? date('d-m-Y', $modules[$key]['day_alert_billing']) : 0;
			$arr_v[$key]['day_licence'] = !empty($modules[$key]['day_licence']) ? date('d-m-Y', $modules[$key]['day_licence']) : 0;
			$arr_v[$key]['actif_max'] = $modules[$key]['actif_max'];
			$arr_v[$key]['no_add_more_max'] = $modules[$key]['no_add_more_max'];
			$arr_v[$key]['customer_email'] = $modules[$key]['customer_email'];
			$arr_t[$key] = $arr_v;
		}
		$tree = $arr_t;
		$tree_ = $this->Company->generateTreeList(array('Company.parent_id' => null), null, null, '--');
		$this->set('isAdminLevel2', $isAdminLevel2);
		$this->set('tree', $tree);
		$this->set('is_sas', $is_sas);
		$this->set('tree_', $tree_);
		$this->set('company_id', $company_id);
	}
	function index_plus() {
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}else{
			$tree = $this->Company->generateTreeList(null, null, null, '--');
			$this->set('tree', $tree);
		}
	}
	function delete_multi_company($company_id){
		die($company_id);
		exit;
	}
	function clone_data( $new_company = null )
	{
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}
		else
		{
			$standard_company = $this->Company->find('first', array(
				'recursive' => -1,
				'conditions' => array('Company.company_name LIKE ' => 'hazure'),
				'fields' => 'id'
			));
			$standard_company = !empty($standard_company) ? $standard_company['Company']['id'] : null;
			$tree = $this->Company->generateTreeList(null, null, null, ' -- ');
			$this->set('tree', $tree);
			$this->set('standard_company', $standard_company);
			$this->set('new_company', $new_company);
		}
	}
	/**
	 * view
	 *
	 * @return void
	 * @access public
	 */
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid company', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		$this->set('company', $this->Company->read(null, $id));
	}

	/**
	 * edit
	 *
	 * @return void
	 * @access public
	 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid id for company', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			$old_manage_hour = $this->Company->find('first', array(
				'recursive' => -1,
				'conditions' => array('Company.id' => $this->data['Company']['id']),
				'fields' => array('id', 'manage_hours')
			));
			$old_manage_hour = (empty($old_manage_hour['Company']['manage_hours']) || $old_manage_hour['Company']['manage_hours'] == null || $old_manage_hour['Company']['manage_hours'] == 0) ? 0 : $old_manage_hour['Company']['manage_hours'];
			if( $this->data['Company']['manage_hours'] != $old_manage_hour ){
				$this->loadModel('ActivityRequest');
				$count = $this->ActivityRequest->find('count', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $this->data['Company']['id'],
						'value' != 0
					)
				));
				if(!empty($count) && $count){
					$this->Session->setFlash(__('The company have request timeshet, not save', true), 'error');
					$this->redirect(array('action' => 'index'));
				}
			}
			$this->data["Company"]["day_established"] = strtotime($this->data["Company"]["day_established"]);
			if(!empty($this->data["Company"]["day_alert_billing"])) $this->data["Company"]["day_alert_billing"] = strtotime($this->data["Company"]["day_alert_billing"]);
			if(!empty($this->data["Company"]["day_licence"])) $this->data["Company"]["day_licence"] = strtotime($this->data["Company"]["day_licence"]);
			
			// Customer email.
			if(!empty($this->data['Company']['customer_email'])){
				$customer_email = json_decode($this->data['Company']['customer_email']);
				$mail_list = array();
				foreach($customer_email as $index => $c_mail){
					$mail_list[$index] = $c_mail->value;
				}
				$this->data['Company']['customer_email'] = implode(',',$mail_list);
				
			}
				
			// Set lai gia tri
            if($this->employee_info["Employee"]["is_sas"] == 0){
                // do nothing
            } else {
				if( ($this->data['Company']['module'] == 0) ){
					$this->data['Company']['module_pms'] = 1;
					$this->data['Company']['module_rms'] = 0;
				}elseif( ($this->data['Company']['module'] == 1)  ){
					$this->data['Company']['module_pms'] = 0;
					$this->data['Company']['module_rms'] = 1;
				}else{
					$this->data['Company']['module_pms'] = 1;
					$this->data['Company']['module_rms'] = 1;
				}
				$this->data['Company']['module_audit'] = !empty($this->data['Company']['module_audit']) ? 1 : 0;
				$this->data['Company']['module_report'] = !empty($this->data['Company']['module_report']) ? 1 : 0;
				$this->data['Company']['module_busines'] = !empty($this->data['Company']['module_busines']) ? 1 : 0;
				$this->data['Company']['module_zogmsgs'] = !empty($this->data['Company']['module_zogmsgs']) ? 1 : 0;
				$this->data['Company']['module_license'] = !empty($this->data['Company']['module_license']) ? 1 : 0;
				$this->data['Company']['module_ticket'] = !empty($this->data['Company']['module_ticket']) ? 1 : 0;
				
				
            }
			// BEGIN - security
			$notAllow = false;
			if ($this->data["Company"]["id"] == "") { // ADD new company
				if ($this->employee_info["Employee"]["is_sas"] == 0) {
					if ($this->employee_info["Company"]["parent_id"] != "") {
						$notAllow = true;
					} else if ($this->data["Company"]["parent_id"] != $this->employee_info["Company"]["id"]) {
						$notAllow = true;
					}
				} else {
					$is_2nd_level_company = $this->Company->find('first', array(
						'conditions' => array('Company.id' => $this->data["Company"]["parent_id"])
							));
					if (isset($is_2nd_level_company["Company"]["parent_id"]) && $is_2nd_level_company["Company"]["parent_id"] != "") {
						$notAllow = true;
					}
				}
				if($this->data["Company"]["parent_id"] != 0)
				{
					$dayEstablishedParent = $this->Company->getDayEstablished($this->data["Company"]["parent_id"]);
					if($this->data["Company"]["day_established"] < $dayEstablishedParent)
					{
						$this->data["Company"]["day_established"] = $dayEstablishedParent;
					}
				}
			} else { // EDIT a company
				if ($this->employee_info["Employee"]["is_sas"] == 0) {
					if (!($this->data["Company"]["id"] == $this->employee_info["Company"]["id"] || $this->data["Company"]["parent_id"] == $this->employee_info["Company"]["id"])) {
						$notAllow = true;
					}
				}
				if($this->data["Company"]["parent_id"] != 0)
				{
					$dayEstablishedParent = $this->Company->getDayEstablished($this->data["Company"]["parent_id"]);
					if($this->data["Company"]["day_established"] < $dayEstablishedParent)
					{
						$this->data["Company"]["day_established"] = $dayEstablishedParent;
					}
				}
			}
			if ($notAllow) {
				$this->cakeError('error404', array(array('url' => $id)));
			}
			// END - security
			$insert = false;
			if( empty($this->data['Company']['id']) ){
				$insert = true;
				$this->data['Company']['dir'] = strtolower(Inflector::slug($this->data['Company']['company_name']));
			}
			if ($this->Company->save($this->data)) {
				/**
				* Edit by QN 5/12/2014
				* initiate data for newly added company
				**/
                $cid = $this->Company->id;
				if( $insert ){
                    $list = $this->requestAction('/translations/getPages');
                    foreach($list as $page){
                        $page = Inflector::slug($page);
                        $this->requestAction('/translations/autoInsert/' . $page . '/' . $cid);
                    }
				}
				// Add profit center DEFAULT
				$this->loadModel('ProfitCenter');
				$defaultPC = array(
					'ProfitCenter' => array(
								'name' => 'DEFAULT',
								'company_id' => $this->Company->getLastInsertId()
						)
				);
				$this->ProfitCenter->create();
				$this->ProfitCenter->save($defaultPC);
				//end add
				//Add default menu
				$this->requestAction('/menus/autoInsert', array('pass' => array($cid)));
				//end menu
				$this->Session->setFlash(sprintf(__('The company %s has been saved', true), '<b>' . $this->data["Company"]["company_name"] . '</b>'), 'success');
				if( $insert ){
					$this->redirect('/companies/clone_data/'.$cid);
				}
				else
				{
					$this->redirect('/companies');
				}
			} else {
				$this->Session->setFlash(__('The company could not be saved. Please, try again.', true), 'error');
				$this->redirect('/companies');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Company->read(null, $id);
		}
	}

    /**
     * Tim tat ca employee co start date be hon ngay thanh lap cua cong ty...save lai = ngay thanh lap cua cong ty
     */
    private function _updateStartDayEmployee($company_id){
        /**
         * Lay ngay thanh lap cua cong ty
         */
        $this->loadModels('Employee');
        $dayEstablished = $this->Company->find('first', array(
            'recursive' => -1,
            'conditions' => array('Company.id' => $company_id),
            'fields' => array('day_established')
        ));
        if(!empty($dayEstablished) && !empty($dayEstablished['Company']['day_established'])){
            $dayEstablished = $dayEstablished['Company']['day_established'];
            $employees = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'AND' => array(
                        'start_date <>' => '0000-00-00',
                        'NOT' => array('start_date IS NULL'),
                        'start_date <' => date('Y-m-d', $dayEstablished)
                    )
                ),
                'fields' => array('id', 'id')
            ));
            $this->Employee->updateAll(array('Employee.start_date' => date('Y-m-d', $dayEstablished)), array('Employee.id' => $employees));
        }
        echo 'OKIE';
        exit;
    }

	/**
	 * delete
	 *
	 * @return void
	 * @access public
	 */
	function delete($id = null) {
		if (!$id || !is_numeric($id)) {
			$this->Session->setFlash(__('Invalid id for company', true), 'error');
			$this->redirect(array('action' => 'index'));
		}
		if ($this->employee_info["Employee"]["is_sas"] == 0) {
			$this->Session->setFlash(__('You are not allowed deleting this company', true), 'error');
			$this->redirect(array('action' => 'index'));
		}

		$this->Company->id = $id;
		$name = $this->Company->field("company_name");
		$companies_list = $this->Company->find('list', array(
			'fields' => array('Company.id'),
			'conditions' => array(
				'OR' => array(
					'Company.id' => $id,
					'Company.parent_id' => $id
				)
				)));
		$cer = $this->Company->CompanyEmployeeReference->find('all', array('conditions' => array(
				'CompanyEmployeeReference.company_id' => $companies_list
				)));
		if(empty($cer)){
			if ($this->Company->delete($id)) {
				/**
				*
				* Delete translation data
				*/
				$this->loadModel('TranslationEntry');
				$this->loadModel('TranslationSetting');
				$this->TranslationEntry->deleteAll(array(
					'TranslationEntry.company_id' => $id
				), false);

				$this->TranslationSetting->deleteAll(array(
					'TranslationSetting.company_id' => $id
				), false);

				$this->Session->setFlash(sprintf(__('Company %s deleted', true), '<b>' . $name . '</b>'), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}else{
			 $this->Session->setFlash(sprintf(__('Company %s was not deleted', true), '<b>' . $name . '</b>'), 'error');
			 $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(sprintf(__('NO DELETE', true), '<b>' . $name . '</b>'), 'error');
		$this->redirect(array('action' => 'index'));
	}
	
	function remove_project_task_normal() {
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}else{
			$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest', 'Company');
			$listIdProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'category' => 3
				),
				'fields' => array('id')
			));
			$listOldNormalTasks = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'is_nct' => 0,
					'project_id' => $listIdProjects,
					'or' => array(
						'estimated' => 0,
						'estimated is null',
					),
				),
				'fields' => array('id')
			));
			$tasksHasChild = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => $listOldNormalTasks
				),
				'fields' => array('parent_id', 'parent_id'),
				'group' => array('parent_id')
			));
			if( !empty($tasksHasChild)){
				foreach( $tasksHasChild as $task_id){
					unset( $listOldNormalTasks[$task_id]);
				}
			}
			$listActivityTasksLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => array_keys($listOldNormalTasks)
				),
				'fields' => array('project_task_id', 'id')
			));
			
			$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'status' => 2,
					'value !=' => 0,
					'task_id' => array_values($listActivityTasksLinked)
				),
				'fields' => array('task_id')
			));
			
			$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
			$listIdProjectsSorted = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectTask.id' => array_keys( $listActivityTasksLinked)
				),
				'fields' => array('project_id')
			));
			$listIdProjectsSorted = array_unique($listIdProjectsSorted);
			
			$listNameProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Project.id' => $listIdProjectsSorted
				),
				'fields' => array('id', 'project_name')
			));
			$this->set('listActivityTasksLinked', $listActivityTasksLinked);
			$this->set('listNameProjects', $listNameProjects);
		}
	}
	function remove_project_task_nct() {
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}else{
			$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest', 'Company');
			$listIdProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'category' => 3
				),
				'fields' => array('id')
			));
			$listOldNctTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'is_nct' => 1,
					'project_id' => $listIdProjects,
					'or' => array(
						'estimated' => 0,
						'estimated is null',
					),
				),
				'fields' => array('id')
			));
			$tasksHasChild = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => $listOldNctTask
				),
				'fields' => array('parent_id', 'parent_id'),
				'group' => array('parent_id')
			));
			if( !empty($tasksHasChild)){
				foreach( $tasksHasChild as $task_id){
					unset( $listOldNctTask[$task_id]);
				}
			}
			$listActivityTasksLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => array_keys($listOldNctTask)
				),
				'fields' => array('project_task_id', 'id')
			));
			
			$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'status' => 2,
					'value !=' => 0,
					'task_id' => array_values($listActivityTasksLinked)
				),
				'fields' => array('task_id'),
			));
			
			$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
			
			$listIdProjectsSorted = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectTask.id' => array_keys( $listActivityTasksLinked)
				),
				'fields' => array('project_id')
			));
			$listIdProjectsSorted = array_unique($listIdProjectsSorted);
			
			$listNameProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Project.id' => $listIdProjectsSorted
				),
				'fields' => array('id', 'project_name')
			));
			$this->set('listActivityTasksLinked', $listActivityTasksLinked);
			$this->set('listNameProjects', $listNameProjects);
		}
	}
	/* Email: Z0G - prod: Cleaning database
	 * Remove Clean NTC task with a workload = 0 with no team and/or no resource assigned to the NTC task for Archived project
	 */
	function remove_nct_task_no_workload_no_asigned() {
		$a = time();
		if(!$this->employee_info['Employee']['is_sas']){
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}
		$this->loadModels('Project', 'ProjectTask', 'ProjectTaskEmployeeRefer', 'ActivityTask', 'ActivityRequest', 'Company');
		// Archived project
		$listProjects = $listOldNctTask = $task_nct_has_assigned = array();
		$listProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3
			),
			'fields' => array('id', 'id')
		));
		if( !empty($listProjects)){
			$listOldNctTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'is_nct' => 1,
					'project_id' => $listProjects,
					'or' => array(
						'estimated' => 0,
						'estimated is null',
					),
				),
				'fields' => array('id', 'id')
			));
		}
		if( !empty($listOldNctTask)){
			$task_nct_has_assigned = $this->ProjectTaskEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'estimated !=' => 0,
					'estimated is not null',
					'project_task_id' => $listOldNctTask
				),
				'fields' => array('project_task_id', 'project_task_id')
			));
			$NCT_tasks_zero = array_diff( $listOldNctTask, $task_nct_has_assigned);
		}
		if( !empty($NCT_tasks_zero)){
			$tasksHasChild = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => array_keys($NCT_tasks_zero)
				),
				'fields' => array('parent_id', 'parent_id'),
				'group' => array('parent_id')
			));
		}
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $parent_id){
				unset( $NCT_tasks_zero[$parent_id]);
			}
		}
		$listActivityTasksLinked = $listTaskHasConsumed = $listTaskNotLinkActivity = array();
		if( !empty($NCT_tasks_zero)){
			$listActivityTasksLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $NCT_tasks_zero
				),
				'fields' => array('project_task_id', 'id')
			));
			$listTaskNotLinkActivity = array_diff_key($NCT_tasks_zero, $listActivityTasksLinked);
		}
		if( !empty($listActivityTasksLinked)){
			$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'status' => 2,
					'value !=' => 0,
					'task_id' => array_values($listActivityTasksLinked)
				),
				'fields' => array('task_id', 'task_id'),
			));
		
			// Loai bo cac task co consumed
			$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
			
		}
		$deleteTasks = array_unique( array_merge(array_keys( $listActivityTasksLinked), array_keys( $listTaskNotLinkActivity)));
		$NCT_tasks_zero = $listProjects = $listCompamies = array();
		if( !empty($deleteTasks)){
			$NCT_tasks_zero = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectTask.id' => $deleteTasks
				),
				'fields' => array('id', 'task_title', 'project_id')
			));
		}
		if( !empty($NCT_tasks_zero)){
			$listProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Project.id' => array_keys($NCT_tasks_zero)
				),
				'fields' => array('id', 'project_name', 'company_id')
			));
		}
		if( !empty($listProjects)){
			$listCompamies = $this->Company->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => array_keys($listProjects)
				),
				'fields' => array('id', 'company_name')
			));
		}
		$this->set(compact('listProjects', 'listCompamies', 'NCT_tasks_zero'));
	}
	function remove_sub_task_nct() {
		if(!$this->employee_info['Employee']['is_sas'])
		{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}else{
			$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest', 'Company', 'NctWorkload');
			$listIdProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'category' => 3
				),
				'fields' => array('id')
			));
			$listOldNctTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'is_nct' => 1,
					'project_id' => $listIdProjects
				),
				'fields' => array('id')
			));
			$listActivityTasksLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $listOldNctTask
				),
				'fields' => array('project_task_id', 'id')
			));
			$listNctTaskNotLinked = array_diff($listOldNctTask,array_keys($listActivityTasksLinked));
			
			$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'status' => 2,
					'value !=' => 0,
					'task_id' => array_values($listActivityTasksLinked)
				),
				'fields' => array('task_id'),
			));
			$listActivityTaskLinkedNotConsumed = array_diff($listActivityTasksLinked,array_values($listTaskHasConsumed));
			$listActivityTaskLinkedHaveConsumed = array_diff($listActivityTasksLinked,$listActivityTaskLinkedNotConsumed);
			
			//Lay id task co sub task can delete.
			$listTaskDelete = $this->NctWorkload->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'NctWorkload.project_task_id' => array_keys( $listActivityTaskLinkedNotConsumed),
					'or' => array(
						'NctWorkload.estimated' => 0,
						'NctWorkload.estimated is null',
					)
				),
				'fields' => array('project_task_id'),
			));
			$listIdProjectsSorted = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectTask.id' => $listTaskDelete
				),
				'fields' => array('project_id')
			));
			$listIdProjectsSorted = array_unique($listIdProjectsSorted);
			$listNameProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'Project.id' => $listIdProjectsSorted
				),
				'fields' => array('id', 'project_name')
			));
			$this->set('listTaskDelete', $listTaskDelete);
			$this->set('listNameProjects', $listNameProjects);
		}
	}
	function all_tools(){
		if($this->employee_info['Employee']['is_sas'] == 1 || ( !empty($this->employee_info['Role']['name']) && $this->employee_info['Role']['name'] == 'admin' ))
		{}else{
			$this->redirect(array('controller' => 'companies', 'action' => 'index'));
		}
	}
}
?>