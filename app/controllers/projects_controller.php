<?php

/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Projects';
    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";
    public $allowedImageFiles = "jpg,jpeg,bmp,gif,png";

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array(
        'Project',
        'ProjectFunction',
        'Company',
        'ProjectTeam',
        'ProjectPhasePlan',
        'ProjectPhase',
        'ProjectCreatedVal',
        'ProfitCenter',
        'ProjectLivrableActors',
        'ProjectEvolutionImpact',
        'ProjectLivrableActor',
        'ProjectEvolutionImpactRefer',
        'CompanyEmployeeReference',
        'ProjectAmr',
        'ProjectMilestone',
        'ProjectTask',
        'ProjectRisk',
        'ProjectIssue',
        'ProjectDecision',
        'ProjectLivrable',
        'ProjectEvolution',
        'ProjectFunctionEmployeeRefer',
        'ProjectStatus',
        'ProjectPriority',
        'ProjectComplexity',
        'ProjectAmrProgram',
        'ProjectType',
        'ProjectSubType',
        'ProjectAmrSubProgram',
        'Currency',
        'UserView',
        'UserDefaultView',
        "Employee",
        "Activity",
        'ProjectBudgetInternal',
        'ProjectBudgetInternalDetail',
        'ProjectBudgetExternal',
        'ProjectBudgetSale',
        'ProjectBudgetInvoice',
        'ProjectBudgetSyn',
        'ProjectPhaseCurrent',
        'ProjectFinancePlus',
        'ProjectFinancePlusDetail',
        'ProjectListMultiple',
        'ProjectFile',
        'ProjectImage'
    );

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->autoRedirect = false;
        $this->set('allowedFiles', $this->allowedFiles);
    }

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'SlickExporter', 'Lib');
    var $paginate = array('limit' => 25, 'page' => 1, 'order' => array('project_name' => 'asc'));

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('project', $this->Project->read(null, $id));
    }

    function add_popup() {
        $employee_info = $this->employee_info;
		
		// Check Role
		$profileName = array();
        if (!empty($employee_info['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employee_info['Employee']['profile_account']
                )
            ));
        }
		// Ticket #393. Check enable_newdesign de hien thi button (+) Add new task. Update by QuanNV 06/06/2019.
		$this->loadModel('Menu');
		$is_task_newdesign = $this->Menu->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $employee_info["Company"]["id"],
				'model' => 'project',
				'controllers' => 'project_tasks'
			),
			'fields' => array('enable_newdesign')
		));
		$is_task_newdesign = !empty($is_task_newdesign) ? $is_task_newdesign['Menu']['enable_newdesign'] : 0;
		
		$roleLogin = $employee_info['Role']['name'];
		$canAddProject = (($roleLogin == 'admin') || (!empty($employee_info['Employee']['create_a_project'])));
		if ( !empty( $profileName['ProfileProjectManager']['can_create_project'] ) ) $canAddProject = 1;
		$canAddTask = ($roleLogin == 'admin' || $roleLogin == 'pm');
		$canAddTask = $canAddTask && $is_task_newdesign;
		$canAddEmployee = ($roleLogin == 'admin' || (!empty($employee_info['CompanyEmployeeReference']['control_resource'])));
		if ( !empty( $profileName['ProfileProjectManager']['create_resource'] ) ) $canAddEmployee = 1;
		
		if( !empty($employee_info["Employee"]["is_sas"]) ) $this->render();
		$company_id = $employee_info["Company"]["id"];
		$employee_id = $employee_info['Employee']['id'];
		
		$canAddMoreMax = true;
		$canAddMoreMax = $this->Employee->canAddMoreMax($company_id);
		
		$this->loadModels('Project', 'ProjectEmployeeManager', 'ProfileProjectManager', 'Profile', 'Translation');
		if( $canAddProject){
			// - All Project of company // User for check name exist
			$listAllProjects = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $company_id),
				'fields' => array('id', 'project_name')
			));
			
			// - All Status
			$listAllStatus = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id),'order' => 'weight'));
			
			// - All Program
			$listAllPrograms = $this->Project->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id' => $company_id)));
				
			if(!empty($listAllPrograms)){
				asort($listAllPrograms);
			}
			// - List PM can be assign for PM
			
			// - List PM can be assign  for Technical Manager
			$companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
				'recursive' => -1,
				'fields' => array('employee_id', 'role_id'),
				'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));

			$curentDate = date('Y-m-d');
			$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
			$projectEmployees = $this->ProjectTask->Employee->find('all', array(
				'order' => 'last_name  ASC',
				'recursive' => -1,
				'conditions' => array(
					'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
				),
				'fields' => array('first_name', 'last_name', 'id', 'avatar_resize','actif', 'available')
			));
			$_employees = array();
			foreach ($projectEmployees as $projectEmployee) {
				$_employees['project'][$projectEmployee['Employee']['id']]['full_name'] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
				$em_actif = intval($projectEmployee['Employee']['actif']) ? intval($projectEmployee['Employee']['available']) : 0;
				$_employees['project'][$projectEmployee['Employee']['id']]['actif'] = $em_actif;
				foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
					if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
						$_employees[$key][$projectEmployee['Employee']['id']] = $_employees['project'][$projectEmployee['Employee']['id']];
					}
				}
			}
			// - List PM can be assign  for Read Access
			// Kêt hợp list PM và list PC
			$profitCenters = $this->ProfitCenter->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'name')
			));
			
			// get list model project
			// Phần này lấy cả những project model của công ty con. Nhưng function hiện tại không cho copy những project này. Ở đây lấy tất cả. nhưng View chỉ hiển thị những project của công ty mẹ, sẽ sửa lại sau khi nhận feedback 
			$sub_companies = $this->Project->Company->find('list', array(
				'conditions' => array(
					'Company.parent_id' => $company_id
				)
			));
			$company_ids = array($company_id);
			foreach ($sub_companies as $comp_id => $company_name) {
				$company_ids[] = $comp_id;
			}
			
			$model_projects = $this->Project->find('all', array(
				'conditions' => array(
					'Project.company_id' => $company_ids, 
					'Project.category' => 4
				),
				'contain' => array('Company'),
				'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')
			));
		}
		
		if( $canAddTask ){
			$listIdModifyByPm = array();;
			if ($roleLogin == 'pm') {
				$list_project_my_manager = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'project_manager_id' => $employee_id,
						'category' => 1
					),
					'fields' => array('Project.id', 'Project.id')
				));
				// Ticket #427. Cho phep hien thi project co user la PM, Technical manager, UAT manager, Chief Business, Functional leader
				$list_project_my_manager_bakcup = $this->ProjectEmployeeManager->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'ProjectEmployeeManager.project_manager_id' => $employee_id,
						'ProjectEmployeeManager.is_profit_center' => 0,
						'ProjectEmployeeManager.type !=' => 'RA'
					),
					'joins' => array(
						array(
							'table' => 'projects',
							'alias' => 'Project',
							'conditions' => array(
								'Project.id = ProjectEmployeeManager.project_id',
								'Project.category' => 1
							)
						)
					),
					'fields' => array('ProjectEmployeeManager.project_id', 'ProjectEmployeeManager.project_id')
				));
				$list_project_my_manager = !empty( $list_project_my_manager) ? $list_project_my_manager : array();
				$list_project_my_manager_bakcup = !empty( $list_project_my_manager_bakcup) ? $list_project_my_manager_bakcup : array();
				$list_project_my_manager = array_merge($list_project_my_manager, $list_project_my_manager_bakcup);
				$listIdModifyByPm = array_unique($list_project_my_manager);
			}
			$listProjectbyPM = array();
			if ($roleLogin == 'pm') {
				$listProjectbyPM = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'Project.id' => $listIdModifyByPm,
						'Project.company_id' => $company_id,
						'Project.category' => array(1,2)
					),
					'fields' => array('id', 'Project.project_name')
				));
			}
			if ($roleLogin == 'admin') {
				$listProjectbyPM = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'Project.company_id' => $company_id,
						'Project.category' => array(1,2)
					),
					'fields' => array('id', 'Project.project_name')
				));
			}
			asort($listProjectbyPM);
			$listPhasesbyProject = $this->ProjectPhasePlan->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => array_keys((array) $listProjectbyPM)
				),
				'joins' => array(
					array(
						'table' => 'project_phases',
						'alias' => 'ProjectsPhase',
						'conditions' => array(
							'ProjectPhasePlan.project_planed_phase_id = ProjectsPhase.id',
							'ProjectsPhase.company_id = ' . $company_id
						)
					)
				),
				'fields' => array('ProjectPhasePlan.id', 'ProjectPhasePlan.project_planed_phase_id', 'ProjectPhasePlan.project_id', 'ProjectPhasePlan.phase_planed_start_date', 'ProjectPhasePlan.phase_planed_end_date', 'ProjectPhasePlan.phase_real_start_date', 'ProjectPhasePlan.phase_real_end_date', 'ProjectsPhase.name', 'ProjectPhasePlan.project_part_id')
			));
			$parts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
				'order' => array('weight' => 'ASC')
			));
			$listPhases = array();
			foreach ($listPhasesbyProject as $Phase) {
				$p = $Phase['ProjectPhasePlan'];
				$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
				
				if (empty($listPhases[$p['project_id']]))$listPhases[$p['project_id']] = array();
				
				if( !empty($part_id) && !empty($parts[$part_id ]) ){
					$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
					$Phase['ProjectsPhase']['name'] = $Phase['ProjectsPhase']['name'].' ('.$part_name.')';
				}
				$listPhases[$p['project_id']][$Phase['ProjectPhasePlan']['id']] = array(
					'id' => $Phase['ProjectPhasePlan']['id'],
					'name' => $Phase['ProjectsPhase']['name'],
					'phase_planed_start_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_planed_start_date'])),
					'phase_real_start_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_real_start_date'])),
					'phase_planed_end_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_planed_end_date'])),
					'phase_real_end_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_real_end_date'])),
				);
			}
			if( empty ($listAllStatus)) $listAllStatus = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id),'order' => 'weight'));
			$projectPriorities = $this->ProjectTask->ProjectPriority->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id
				),
				'fields' => array('id', 'priority'),
			));
			
			$projectProfiles = $this->Profile->find('list', array(
				'recursive' => -1,
				'order' => array('id' => 'ASC'),
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('id', 'name'),
			));
			$adminTaskSetting = $this->Translation->find('list', array(
				'conditions' => array(
					'page' => 'Project_Task',
					'TranslationSetting.company_id' => $company_id
				),
				'recursive' => -1,
				'fields' => array('original_text', 'TranslationSetting.show'),
				'joins' => array(
					array(
						'table' => 'translation_settings',
						'alias' => 'TranslationSetting',
						'conditions' => array(
							'Translation.id = TranslationSetting.translation_id',
						),
						'type' => 'left'
					)
				),
				'order' => array('TranslationSetting.setting_order' => 'ASC')
			));
			$workdays = $this->requestAction('/project_tasks/getWorkdays');
		}
		if( $canAddEmployee ){
			$default_user_profile = $this->Employee->default_user_profile($company_id);
			$profile_name = $this->ProfileProjectManager->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('ProfileProjectManager.id', 'ProfileProjectManager.profile_name'),
			));
			$roles = $this->Employee->CompanyEmployeeReference->Role->find('list', array('fields' => array('Role.id', 'Role.desc')));
			// PM không thể tạo Admin / webservices
			if($roleLogin == 'pm') unset( $roles[2]);
			
			if( !empty( $profile_name) ) {
				foreach( $profile_name as $key => $name){
					 $roles['profile_'.$key] = $name;
				}
			}
			if( empty( $profitCenters)){
				$profitCenters = $this->ProfitCenter->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id
					),
					'fields' => array('id', 'name')
				));
			}
			
		}
		$this->set(compact('canAddProject','canAddTask','canAddEmployee','company_id', 'roleLogin','canAddMoreMax'));
		
		//add Project
		$this->set(compact('listAllProjects','listAllStatus','_employees','listAllPrograms', 'profitCenters', 'model_projects'));
		
		// add Task
		$this->set(compact('listProjectbyPM', 'listPhases', 'projectPriorities', 'projectProfiles', 'adminTaskSetting', 'workdays'));

		// add Employee
		$this->set(compact('default_user_profile', 'profile_name', 'roles'));
		// exit;
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $Model_e = ClassRegistry::init('Employee');
        $employInfors = $Model_e->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
            'fields' => array('create_a_project')
        ));
        $employInfors = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? true : false;
        if ($this->employee_info["Role"]["name"] == 'pm' && !$employInfors) {
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('ProjectEmployeeManager');
        $canModified = true;
        if ($employeeInfo['Employee']['is_sas'] == 1) {
            //do nothing
        } else {
            if (!$employeeInfo || $employeeInfo['Role']['name'] == 'conslt' || ($employeeInfo['Role']['name'] == 'pm')) {
                $canModified = false;
            }
        }
        $this->set('canModified', $canModified);
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $budgetCustomers = array();
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $profitCenters = array();
        if ($company_id != "") {
            $this->set('company_names', $this->Project->Company->getTreeList($company_id));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id' => $company_id))));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id),'order' => 'weight')));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id' => $company_id))));
            $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'role_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
            if (!$adminSeeAllProjects) {
                /**
                 * Lay cac profit center cua cong ty
                 */
                $this->loadModels('ProfitCenter');
                $profitCenters = $this->ProfitCenter->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name')
                ));
            }
            $projectEmployees = $this->ProjectTask->Employee->find('all', array(
                'order' => 'last_name  ASC',
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
                ),
                'fields' => array('first_name', 'last_name', 'id')));
            $employees = array();
            foreach ($projectEmployees as $projectEmployee) {
                $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
                foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                    if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                        $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                    }
                }
            }
            $this->set('employees', $employees);
            /**
             * Get list budget customer
             */
            $this->loadModel('BudgetCustomer');
            $budgetCustomers = $this->BudgetCustomer->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        } else {
            $this->set('company_names', $this->Project->Company->generateTreeList(null, null, null, '--'));
            $this->set('project_phases', $this->Project->ProjectPhase->find('list', array('conditions' => array('ProjectPhase.activated' => 1))));
            $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'))));
            $this->set('project_priorities', $this->Project->ProjectPriority->find('list'));
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list'));
            $this->set('project_types', $this->Project->ProjectType->find('list', array('conditions' => array('ProjectType.display' => 1))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list'));
        }
        $this->set(compact('budgetCustomers', 'adminSeeAllProjects', 'profitCenters'));
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        if (!empty($this->data)) {
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /**
             * Xu ly functional
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /**
             * Xu ly uat
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            //$countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->Session->setFlash(__('The project must have a project manager. Please, try again.', true), 'error');
            // } else {
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // /}
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            /* functional & uat */
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }            
			if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            $this->Project->create();
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["Project"]["start_date"] = isset($this->data["Project"]["start_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["start_date"]) : null;
            $this->data["Project"]["end_date"] = isset($this->data["Project"]["end_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["end_date"]) : null;
            if (!$canModified) {
                $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
            }
            $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            $data_arm['ProjectAmr']['weather'] = 'sun';
            $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['planning_weather'] = 'sun';
            $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
            $data_arm['ProjectAmr']['organization_weather'] = 'sun';
            $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
            $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')
            ));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $this->data['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
            $this->data['Project']['last_modified'] = time();
            if ($this->Project->save($this->data)) {
                $this->writeLog($this->data, $this->employee_info, 'Create project #' . $this->Project->id, $company_id);
                $project_ = $this->Project->find("first", array('field' => array('Project.id'), 'order' => array("Project.id" => "DESC")));
                $project_id = $project_['Project']['id'];
                $project_phase_id = $project_['Project']['project_phase_id'];
                $st_date = $this->data["Project"]["start_date"];
                if ($this->data["Project"]["end_date"] == "")
                    $end_date = '';
                else
                    $end_date = $this->data["Project"]["end_date"];
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                $this->Session->setFlash(__('Saved', true));
                $pid = $this->Project->getLastInsertID();
                //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $pid;
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'PM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                // 'is_backup' => 0,
                                'project_id' => $pid,
                                'type' => 'RA',
                                'activity_id' => 0,
                                'is_profit_center' => $is_profit
                            );
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'CB',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'TM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'FL',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['functional_leader_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $pid,
                            'type' => 'UM',
                            'activity_id' => 0
                        );
                        if ($value != $this->data['Project']['uat_manager_id']) {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save($dataRefers);
                        }
                    }
                }
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            $saved = array(
                                'project_id' => $pid,
                                'project_phase_id' => $phaseId
                            );
                            $this->ProjectPhaseCurrent->create();
                            $this->ProjectPhaseCurrent->save($saved);
                        }
                    }
                }
                //End Create arm project
                /**
                 * Lay screen mac dinh cua project
                 */
                $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
                ));
                $ACLController = 'projects';
                $ACLAction = 'edit';
                if (!empty($screenDefaults)) {
                    $ACLController = $screenDefaults['Menu']['controllers'];
                    $ACLAction = $screenDefaults['Menu']['functions'];
                }
                $this->redirect("/projects/your_form/" . $pid);
            } else {
                $this->Session->setFlash(__('Not saved.', true), 'error');
            }
        }
        //}
        $currencies = $this->Project->Currency->find('list');
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $this->set(compact('projectPhases', 'projectPriorities', 'projectStatuses', 'currencies', 'projectManagers', 'currency_name', 'adminSeeAllProjects'));
        //tree company
        $tree = $this->Project->Company->generateTreeList(null, null, null, '->');
        if (!$this->is_sas) {
            $parent = array();
            foreach ($tree as $key => $value) {
                $parentPath = $this->Project->Company->getpath($key);
                foreach ($parentPath as $par) {
                    $parent[$key]['name'] = $value;
                    if ($par['Company']['id'] != $key) {
                        $parent[$key]['list'][] = $par['Company']['id'];
                    }
                }
            }
            foreach ($tree as $key => $value) {
                if (!empty($parent[$key]['list'])) {
                    if ($key != $this->employee_info['Company']['id'])
                        if (!in_array($this->employee_info['Company']['id'], $parent[$key]['list']))
                            unset($tree[$key]);
                }else {
                    unset($tree[$key]);
                }
            }
        }
        // --------------------------------------------------
        $this->set('tree', $tree);
        $this->loadModel('Translation');
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => 'Details',
                'TranslationSetting.company_id' => $company_id
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        $this->set('translation_data', $translation_data); //check if your_form is shown and is default screen
        $your_form = $this->requestAction('/menus/getMenu/projects/your_form');
        $this->render('your_form_add');
    }

	
	/* Add new Project for popup
	 * Phần này chỉ làm riêng cho popup
	 * Function đầy đủ xem ở projects / add 
	 */
	public function add_new_project_popup(){
		$employee_info = $this->employee_info;
		$canAddProject = (($employee_info['Role']['id'] == 2) || (!empty($employee_info['Employee']['create_a_project'])));
		 // Check Role
		$company_id = $employee_info["Company"]["id"];
		if(!$canAddProject || empty($this->data) || empty( $company_id) ) {
			$this->cakeError('404'); exit;
		}
		$this->loadModels('ProjectEmployeeManager');
		/**
		 * Xu ly project manager. Xoa cac dong = 0
		 */
		if (!empty($this->data['project_employee_manager'])) {
			$this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
			if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
				unset($this->data['project_employee_manager'][$key]);
			}
		}
		
		$adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
		/**
		 * Xu ly read access. Xoa cac dong = 0
		 */
		if (!empty($this->data['read_access'])) {
			$this->data['read_access'] = array_unique($this->data['read_access']);
			if (($key = array_search(0, $this->data['read_access'])) !== false) {
				unset($this->data['read_access'][$key]);
			}
		}
		
		/**
		 * Xu ly technical_manager_list. Xoa cac dong = 0
		 */
		if (!empty($this->data['technical_manager_list'])) {
			$this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
			if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
				unset($this->data['technical_manager_list'][$key]);
			}
		}
		
		/**
		 * Xu ly project manager, technical_manager_list.
		 */
		// Gan gia tri dau tien cho field project_manager_id.
		$first_pm = reset($this->data['project_employee_manager']);
		if(!empty($employee_info['Employee']['create_a_project'])){
			$this->data['Project']['project_manager_id'] = $employee_info['Employee']['id'];
		}else{
			$this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? $first_pm : '';
		}
		//Gan gia tri dau tien cho field technical_manager_id. Cho nay gay loi luu thieu gia tri vao table project_employee_manager khi moi tao project. Quan comment lại. QuanNV 16/07/2019.
		// $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
		//Khong luu Technical_manager vào table project. Chuyen sang luu truc tiep vao table project_employee_manager. QuanNV 16/07/2019
		$this->data["Project"]["technical_manager_id"] = '';
		
		//Save to Project
		$this->Project->create();
        App::import("vendor", "str_utility");
		$this->data["Project"]["company_id"] = $company_id;
		$this->data["Project"]["start_date"] = isset($this->data["Project"]["start_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["start_date"]) : null;
		$this->data["Project"]["end_date"] = isset($this->data["Project"]["end_date"]) ? $str_utility->convertToSQLDate($this->data["Project"]["end_date"]) : null;
		$this->data['Project']['update_by_employee'] = !empty($employee_info['Employee']['fullname']) ? $employee_info['Employee']['fullname'] : '';
		$this->data['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
		$this->data['Project']['weather'] = !empty( $this->data['Project']['weather'] ) ? $this->data['Project']['weather'] : 'sun';
		$this->data['Project']['rank'] = !empty( $this->data['Project']['rank'] ) ? $this->data['Project']['weather'] : 'mid';
		// $this->data['Project']['category'] = 2; // when create project, default project status = 2 (Opportunity).
		$this->data['Project']['last_modified'] = time();
		$data_arm['ProjectAmr']['weather'] = 'sun';
		$data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
		$data_arm['ProjectAmr']['planning_weather'] = 'sun';
		$data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
		$data_arm['ProjectAmr']['organization_weather'] = 'sun';
		$data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
		$data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
		$result = $this->Project->save($this->data);
		if( $result){
			$this->writeLog($this->data, $this->employee_info, 'Create project #' . $this->Project->id, $company_id);
			$this->Session->setFlash(__('Saved', true));
			$pid = $this->Project->getLastInsertID();
			
			 //Begin Create arm project
			$data_arm['ProjectAmr']['project_id'] = $pid;
			$this->ProjectAmr->create();
			$this->ProjectAmr->save($data_arm);
			/**
			 * Save project_employee_manager
			 * Lay danh sach project_employee_manager da tao
			 */
			if (!empty($this->data['project_employee_manager'])) {
				foreach ($this->data['project_employee_manager'] as $value) {
					$dataRefers = array(
						'project_manager_id' => $value,
						'project_id' => $pid,
						'type' => 'PM',
						'activity_id' => 0
					);
					// if ($value != $this->data['Project']['project_manager_id']) {
						$this->ProjectEmployeeManager->create();
						$result[] = $this->ProjectEmployeeManager->save($dataRefers);
					// }
				}
			}
			if (!$adminSeeAllProjects) {
				if (!empty($this->data['read_access'])) {
					foreach ($this->data['read_access'] as $value) {
						$value = explode('-', $value);
						$is_profit = empty($value[1]) ? 0 : 1;
						$dataRefers = array(
							'project_manager_id' => $value[0],
							'project_id' => $pid,
							'type' => 'RA',
							'activity_id' => 0,
							'is_profit_center' => $is_profit
						);
						$this->ProjectEmployeeManager->create();
						$result[] = $this->ProjectEmployeeManager->save($dataRefers);
					}
				}
			}
			if (!empty($this->data['technical_manager_list'])) {
				foreach ($this->data['technical_manager_list'] as $value) {
					$dataRefers = array(
						'project_manager_id' => $value,
						'project_id' => $pid,
						'type' => 'TM',
						'activity_id' => 0
					);
					if ($value != $this->data['Project']['technical_manager_id']) {
						$this->ProjectEmployeeManager->create();
						$result[] = $this->ProjectEmployeeManager->save($dataRefers);
					}
				}
			}
		
			/*** Check setting Phase de auto tao Phase. Ticket #801 */
			$listPhaseDefault = $this->ProjectPhase->find('list',array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'add_when_create_project' => 1,
					'activated' => 1
				),
				'order' => 'ProjectPhase.phase_order ASC',
				'fields' => array('id')
			));
			if(!empty($listPhaseDefault)){
				$i = 1;
				foreach ($listPhaseDefault as $keyPhase => $idPhaseDefault){
					$newRecord['project_id'] = $pid;
					$newRecord['project_planed_phase_id'] = $idPhaseDefault;
					$newRecord['progress'] = 0;
					$newRecord['weight'] = $i;
					$newRecord['phase_planed_start_date'] = '0000-00-00';
					$newRecord['phase_real_start_date'] = '0000-00-00';
					$newRecord['phase_planed_end_date'] = '0000-00-00';
					$newRecord['phase_real_end_date'] = '0000-00-00';
					$this->ProjectPhasePlan->create();
					$this->ProjectPhasePlan->save($newRecord);
					$i++;
				}
			}
			/*** End ticket #801 */
		}
		if( !empty($pid) && !empty($_FILES)) {
			$this->loadModel('ProjectGlobalView');
			$path = $this->_getPathProjectGlobalView($pid);
			
			App::import('Core', 'Folder');
			new Folder($path, true, 0777);
			if (file_exists($path)) {
				$_FILES['FileField'] = array();
				if(!empty($_FILES)){
					$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
					$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
					$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
					$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
					$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
				}
				unset($_FILES['file']);
				$this->MultiFileUpload->encode_filename = false;
				$this->MultiFileUpload->uploadpath = $path;
				$this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedImageFiles;
				$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
				$attachment = $this->MultiFileUpload->upload();
			} else {
				$attachment = "";
				$this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
			}
			if (!empty($attachment)) {
				$attachment = $attachment['attachment']['attachment'];
				$this->ProjectGlobalView->create();
				$last = $this->ProjectGlobalView->find('first', array(
					'recursive' => -1,
					'fields' => array('id', 'attachment'),
					'conditions' => array('project_id' => $pid)));
				if ($last) {
					$this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
					@unlink($path . $last['ProjectGlobalView']['attachment']);
				}
				$result_upload = $this->ProjectGlobalView->save(array(
					'project_id' => $pid,
					'attachment' => $attachment,
					'is_file' => 1
				));
				if ($result_upload) {
					if($this->MultiFileUpload->otherServer == true){
						$this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $pid);
					}
					$this->Session->setFlash(__('Saved', true), 'success');
				} else {
					@unlink($path . $attachment);
					$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
					if($this->MultiFileUpload->otherServer == true){
						$this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $pid);
					}
				}
			} else {
				$this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
			}
			
		}
		$link_open = '/';
		if( !empty( $result)){
			$screenDefaults = ClassRegistry::init('Menu')->find('first', array(
				'recursive' => -1,
				'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
			));
			$ACLController = 'projects';
			$ACLAction = 'edit';
			if (!empty($screenDefaults)) {
				$ACLController = $screenDefaults['Menu']['controllers'];
				$ACLAction = $screenDefaults['Menu']['functions'];
				$link_open = '/' .$ACLController.'/'.$ACLAction.'/'.$pid;
			}
			if( empty( $link_open)) $link_open = '/projects/your_form/'. $pid;
		}
		if( !empty( $this->data['Project']['open_deatail'])){
			$result['redirect'] = $link_open;
		}
		if( $this->params['isAjax']) die(json_encode(array(
			'success' => !empty( $result) ? 'success' : 'failed',
			'data' => !empty( $result) ? $result : '',
			'upload' => !empty( $result_upload) ? 'success' : 'failed',
			'data_upload' => !empty( $result_upload) ? $result_upload : '',
		)));
		
		$this->redirect( !empty($this->data['Project']['return']) ? $this->data['Project']['return'] : $link_open );
		 
	}
	
	/* End Add new Project for popup */
	
	
	/**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        $this->loadModel('ProjectEmployeeManager');
		$this->_checkRole(true, $id); 
        // Check the role
        if (!$this->_checkRole(false, $id, empty($this->data) ? array('element' => 'warning') : array())) {
            $this->data = null;
        }

        $this->_checkWriteProfile('details');
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $employee_id = $employeeInfo['Employee']['id'];
        $changeProjectManager = !(empty($employeeInfo['Role']) || $employeeInfo['Role']['name'] == 'conslt' || $employeeInfo['Role']['name'] == 'pm');
        if ($employeeInfo['Role']['name'] == 'pm') {
            $checkIsManager = $this->Project->find('first', array(
                'conditions' => array(
                    'Project.id' => $id,
                    'Project.project_manager_id' => $employee_id
                )
            ));
            $changeProjectManager = !empty($checkIsManager) ? true : false;
        }
        if ($employeeInfo) {
            $fullname = $employeeInfo['Employee']['first_name'] . " " . $employeeInfo['Employee']['last_name'];
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;

        $company_idd = $company_id_of_project['Project']['company_id'];
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->redirect(array('action' => 'index'));
        }

        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        // security
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $company_id = $this->Project->find("first", array(
            'recursive' => 0,
            "fields" => array("Project.company_id", 'Company.company_name'),
            'conditions' => array('Project.id' => $id)));
        $name_company = $company_id['Company']['company_name'];
        $company_id = $company_id['Project']['company_id'];
        $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectAmrProgram.company_id ' => $company_id
            ),
            'fields' => array('id', 'family_id', 'sub_family_id')
        ));
        $famAndSubFamOfPrograms = !empty($famAndSubFamOfPrograms) ? Set::combine($famAndSubFamOfPrograms, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram') : array();
        $subFams = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => array_keys($famAndSubFamOfPrograms)),
            'fields' => array('id', 'sub_family_id')
        ));
        /**
         * Lay tat ca cac phase cua 1 project
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        if (!empty($this->data)) {
            if ($this->data['Project']['category'] != 2) {
                $this->data['Project']['is_staffing'] = 0;
            }
            if (!empty($this->data['Project']['tmp_activity']) || $this->data['Project']['tmp_activity'] == 0) {
                if ($this->data['Project']['tmp_activity'] == 0) {
                    $this->data['Project']['activity_id'] = null;
                } else {
                    $this->data['Project']['activity_id'] = $this->data['Project']['tmp_activity'];
                }
                unset($this->data['Project']['tmp_activity']);
            }
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /*
             * Xu li functional leader
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /*
             * Xu li uat manager
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            //$countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->data = $this->Project->read(null, $id);
            //     $this->Session->setFlash(__('Please select at least a project manager without the flag backup.', true), 'error');
            // } else {
            $activityLinked = !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0;
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // }
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);
            $data_project_teams = $this->params['form'];
            $project_id = $this->data["Project"]["id"];
            if (!empty($this->data['Project']['project_amr_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            }
            if (!empty($this->data['Project']['project_amr_sub_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            }
            $checkBackup = $this->ProjectEmployeeManager->find('count', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id, 'project_manager_id' => $employeeInfo['Employee']['id'], 'type' => 'PM')
            ));
            if (!$changeProjectManager && $employeeInfo['Role']['name'] != 'admin') {
                if (!empty($checkBackup)) {
                    //do nothing
                } else {
                    $Model4 = ClassRegistry::init('Employee');
                    $employInfors = $Model4->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
                        'fields' => array('update_your_form')
                    ));
                    if (!empty($employInfors) && !empty($employInfors['Employee']['update_your_form'])) {
                        // thang nay dc chinh sua tat ca projet...ke me no
                    } else {
                        $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
                    }
                }
            }
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $oldProject = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $this->data['Project']['id']),
                'fields' => array('activity_id')
            ));
            if (!empty($oldProject['Project']['activity_id'])) {
                //da linked roi ko lam gi ca
            } else {
                $this->data['Project']['activity_id'] = $activityLinked;
            }
            if (!empty($this->data['Project']['activity_id'])) {
                //bat dau linked
            } else {
                unset($this->data['Project']['activity_id']);
            }
            if ($activityLinked == -1) {
                $this->data['Project']['activity_id'] = null;
            }
            $this->data['Project']['last_modified'] = time();
            $range = range(1, 4);
            foreach ($range as $num) {
                if (isset($this->data['Project']['date_' . $num])) {
                    $this->data['Project']['date_' . $num] = $str_utility->convertToSQLDate($this->data['Project']['date_' . $num]);
                }
            }
            //$data_arm['ProjectAmr']['project_phases_id']                = $this->data['Project']['project_phase_id'];
            $this->loadModel('Activity');
            // rename activity linked
            $oldProjectName = $this->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name'),
                'conditions' => array('Project.id' => $id)));
            $oldProjectName = !empty($oldProjectName) ? $oldProjectName['Project']['project_name'] : '';
            $newProjectName = !empty($this->data) && !empty($this->data['Project']['project_name']) ? $this->data['Project']['project_name'] : '';
            if ($oldProjectName != $newProjectName) {
                $ActivityLinkedName = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id', 'name', 'long_name', 'short_name'),
                    'conditions' => array(
                        'project' => $id
                    )
                ));
                if (!empty($ActivityLinkedName)) {
                    $saved['name'] = $newProjectName;
                    $idActivityLinked = $ActivityLinkedName['Activity']['id'];
                    $this->Activity->id = $idActivityLinked;
                    $this->Activity->save($saved);
                }
            }
            // end
            // truong hop pm co quyen change status nhung khong the update your form.
            if ($employeeInfo['Role']['name'] == 'pm' && $employeeInfo['Employee']['change_status_project'] == 1 && $employeeInfo['Employee']['update_your_form'] == 0) {
                unset($this->data['Project']['project_manager_id']);
                unset($this->data['Project']['chief_business_id']);
                unset($this->data['Project']['technical_manager_id']);
                unset($this->data['Project']['functional_leader_id']);
                unset($this->data['Project']['uat_manager_id']);
            }
            if ($this->Project->save($this->data)) {
                $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update project `%s` via Details', $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $id = $this->Project->id;
                if ($activityLinked == -1) {
                    $activityLinked = 0;
                }
                if (!empty($activityLinked) && $activityLinked != 0 && isset($this->data['Project']['activated'])) {
                    $this->Activity->id = $activityLinked;
                    $idProgram = !empty($this->data['Project']['project_amr_program_id']) ? $this->data['Project']['project_amr_program_id'] : '';
                    $idSubProgram = !empty($this->data['Project']['project_amr_sub_program_id']) ? $this->data['Project']['project_amr_sub_program_id'] : '';
                    $fam = !empty($famAndSubFamOfPrograms[$idProgram]['family_id']) ? $famAndSubFamOfPrograms[$idProgram]['family_id'] : '';
                    $sfam = !empty($subFams[$idSubProgram]) ? $subFams[$idSubProgram] : '';
                    if (!empty($this->data['Project']['category']) && $this->data['Project']['category'] == 1 && $activate_family_linked_program) {
                        if (!empty($fam)) {
                            $this->Activity->save(array(
                                'project_manager_id' => $this->data['Project']['project_manager_id'],
                                'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                                'activated' => $this->data['Project']['activated'],
                                'family_id' => $fam,
                                'subfamily_id' => $sfam
                            ));
                        }
                    } else {
                        $this->Activity->save(array(
                            'project_manager_id' => $this->data['Project']['project_manager_id'],
                            'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                            'activated' => $this->data['Project']['activated']
                        ));
                    }
                }
                $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
                $this->ProjectAmr->id = $ProjectArms['ProjectAmr']['id'];
                $this->ProjectAmr->save($data_arm);
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                //link project - activity
                if (!empty($this->data['Project']['activity_id'])) {
                    // update tmp
                    $this->loadModel('TmpStaffingSystem');
                    $this->TmpStaffingSystem->updateAll(
                            array('TmpStaffingSystem.activity_id' => $this->data['Project']['activity_id']), array('TmpStaffingSystem.project_id' => $id)
                    );
                    // update project budget sync
                    $this->loadModel('ProjectBudgetSyn');
                    $this->ProjectBudgetSyn->updateAll(
                            array('ProjectBudgetSyn.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetSyn.project_id' => $id)
                    );
                    // update project provisional
                    $this->loadModel('ProjectBudgetProvisional');
                    $this->ProjectBudgetProvisional->updateAll(
                            array('ProjectBudgetProvisional.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetProvisional.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlus');
                    $this->ProjectFinancePlus->updateAll(
                            array('ProjectFinancePlus.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlus.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlusDetail');
                    $this->ProjectFinancePlusDetail->updateAll(
                            array('ProjectFinancePlusDetail.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlusDetail.project_id' => $id)
                    );
                    $this->loadModel('ActivityTask');
                    $this->loadModel('ProjectStatus');
                    $activityIdTask = $this->data['Project']['activity_id'];
                    if (isset($activityIdTask) && !empty($activityIdTask) && $activityIdTask != '' && $activityIdTask != null) {
                        $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.activity_id' => $activityIdTask)));
                        $projectStatus = $this->ProjectStatus->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('ProjectStatus.status' => 'IP', 'company_id' => $company_id),
							'order' => 'weight',
                            'fields' => array('id')
                        ));
                        $projectStatus = $projectStatus['ProjectStatus']['id'];
                        $projectTasks = $this->Project->ProjectTask->find('all', array('recursive' => -1, 'conditions' => array('ProjectTask.project_id' => $id, 'ProjectTask.task_status_id' => $projectStatus)));
                        $listProjectPhase = ClassRegistry::init('ProjectPhase')->find('list', array('recursive' => -1, 'conditions' => array('company_id' => $company_id, 'ProjectPhase.activated' => 1)));
                        $listPlan = ClassRegistry::init('ProjectPhasePlan')->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('project_id' => $id),
                            'fields' => array('id', 'project_planed_phase_id')
                        ));
                        $activity = ClassRegistry::init('Activity')->find('first', array('recursive' => -1, 'conditions' => array('Activity.id' => $this->data['Project']['activity_id']), 'fields' => array('id', 'project')));
                        $this->loadModel('Activity');
                        if (!empty($activity)) {
                            $activities = ClassRegistry::init('Activity')->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('Activity.project' => $id),
                                'fields' => array('id', 'project')
                            ));
                            if (!empty($activities)) {
                                foreach ($activities as $act) {
                                    $this->Activity->id = $act['Activity']['id'];
                                    $this->Activity->saveField('project', null);
                                }
                            }
                            $this->Activity->id = $activity['Activity']['id'];
                            $this->Activity->saveField('project', $id);
                        }
                        foreach ($projectTasks as $projectTask) {
                            $project_task_id[] = $projectTask['ProjectTask']['id'];
                        }
                        if (!empty($project_task_id)) {
                            $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.project_task_id' => $project_task_id)));
                        }
                        foreach ($activityTasks as $activityTask) {
                            $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                            $this->ActivityTask->delete();
                        }
                        $dataActivityTask = array();
                        foreach ($projectTasks as $projectTask) {
                            $idProjectPhase = $projectTask['ProjectTask']['project_planed_phase_id'];
                            $_name = isset($idProjectPhase) ? $listPlan[$idProjectPhase] : '';
                            $_name = !empty($_name) ? $listProjectPhase[$_name] : '';
                            $nameProjectTask = $projectTask['ProjectTask']['task_title'];
                            $this->ActivityTask->create();
                            $dataActivityTask['ActivityTask']['name'] = $_name . '/ ' . $nameProjectTask;
                            $dataActivityTask['ActivityTask']['activity_id'] = $activityIdTask;
                            $dataActivityTask['ActivityTask']['project_task_id'] = $projectTask['ProjectTask']['id'];
                            $this->ActivityTask->save($dataActivityTask['ActivityTask']);
                        }
                    }
                }
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                $listEmployeeRefers = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id
                    ),
                    'fields' => array('id', 'id', 'type'),
                    'group' => array('type', 'id')
                ));
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                //'is_backup' => 0,
                                'project_id' => $id,
                                'type' => 'RA',
                                'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0,
                                'is_profit_center' => $is_profit
                            );
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['RA'][$lastEmployRefers]);
                            }
                        }
                    }
                    if (!empty($listEmployeeRefers['RA'])) {
                        $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['RA']), false);
                    }
                    unset($listEmployeeRefers['RA']);
                }
                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            //'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'PM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['PM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['PM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['PM']), false);
                }
                unset($listEmployeeRefers['PM']);
                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'CB',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['CB'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['CB'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['CB']), false);
                }
                unset($listEmployeeRefers['CB']);
                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'TM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['TM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['TM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['TM']), false);
                }
                unset($listEmployeeRefers['TM']);
                //functional leader & uat manager
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'FL',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['FL'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['FL'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['FL']), false);
                }
                unset($listEmployeeRefers['FL']);
                //uat manager
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'UM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['UM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['UM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['UM']), false);
                }
                unset($listEmployeeRefers['UM']);
                $oldPhase = array();
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            if (in_array($phaseId, $phasePlans)) { // ton tai trong phase plan of project
                                $oldPhase[] = $phaseId;
                            } else {
                                $saved = array(
                                    'project_id' => $id,
                                    'project_phase_id' => $phaseId
                                );
                                $this->ProjectPhaseCurrent->create();
                                $this->ProjectPhaseCurrent->save($saved);
                            }
                        }
                    }
                }
                foreach ($phasePlans as $idPlan => $phaseId) {
                    if (in_array($phaseId, $oldPhase)) {
                        //do nothing
                    } else {
                        $this->ProjectPhaseCurrent->delete($idPlan);
                        //delete phase
                    }
                }
                $this->Session->setFlash(__('Saved', true));
            } else {
                $this->Session->setFlash(__('The project could not be saved. Please, try again.', true), 'error');
            }
            $this->redirect("/projects/edit/" . $project_id);
        }
        //}
        if (empty($this->data)) {
            $this->data = $this->Project->read(null, $id);
        }
        $IdTask = $this->data['Project']['activity_id'];
        $this->set('famAndSubFamOfPrograms', $famAndSubFamOfPrograms);
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1), 'order' => 'ProjectComplexity.name ASC')));
        $this->set('Priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id ' => $company_id))));
        $this->set('Statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id ' => $company_id),'order' => 'weight')));
        $this->set('ProjectTypes', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id ' => $company_id, 'ProjectType.display' => 1))));
        $this->set('ProjectSubTypes', $this->ProjectSubType->find('list', array('fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 'conditions' => array('ProjectSubType.project_type_id ' => $this->data['Project']['project_type_id'], 'ProjectSubType.display' => 1))));
        $this->set('ProjectArmPrograms', $this->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id ' => $company_id))));
        $this->set('ProjectArmSubPrograms', $this->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program'), 'conditions' => array('ProjectAmrSubProgram.project_amr_program_id ' => $this->data['Project']['project_amr_program_id']))));
        $this->set('ProjectActivities', ClassRegistry::init('Activity')->find('list', array('fields' => array('Activity.id', 'Activity.name'), 'conditions' => array('Activity.company_id ' => $company_id, 'OR' => array('Activity.pms' => 1, 'NOT' => array('Activity.project' => null))))));
        $this->set('listIdActivity', $this->Project->find('list', array('recursive' => -1, 'fields' => array('Project.activity_id'), 'conditions' => array('Project.company_id' => $company_id))));
        //$this->set('activityRequest'         , ClassRegistry::init('ActivityRequest')->find('count', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask))));
        // Find Task and Activities in request then block the combo box
        $find_tasks_in_activity = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask)));
        $find_tasks_in_activity = Set::combine($find_tasks_in_activity, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $find_tasks_in_activity = array_keys($find_tasks_in_activity);
        // Check if the project is linked to any activity or not
        if (isset($IdTask)) {
            if (!empty($find_tasks_in_activity)) {
                $conditions = array('task_id' => $find_tasks_in_activity);
                $activityRequest = ClassRegistry::init('ActivityRequest')->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "OR" => array(
                            'activity_id' => $IdTask,
                            $conditions
                        )
                    )
                        )
                );
            } else {
                $activityRequest = 0;
            }
        } else {
            $activityRequest = 0;
        }
        $activityRequest = json_encode($activityRequest);
        // Check activities and tasks, if assigned then lock the combo box
        $this->set('activityRequest', $activityRequest);

        $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));

        $profitCenters = array();
        if (!$adminSeeAllProjects) {
            /**
             * Lay cac profit center cua cong ty
             */
            $this->loadModels('ProfitCenter');
            $profitCenters = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name')
            ));
        }
        $projectEmployees = $this->ProjectTask->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees),
				'Employee.last_name NOT' => 'NULL'
            ),
            'fields' => array('first_name', 'last_name', 'id', 'avatar_resize')));
        $employees = $avatarOfEmploys = array();
        foreach ($projectEmployees as $projectEmployee) {
            $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
            foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                }
            }
            $avatarOfEmploys[$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['avatar_resize'];
        }
        $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_idd)));
        // edit get start end date from Phase
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) as MinStartDate',
                'MAX(phase_real_end_date) as MaxEndDate'
            )
        ));
        if (!empty($projectPhasePlans)) {
            $_datas['start_date'] = $projectPhasePlans[0][0]['MinStartDate'];
            $_datas['end_date'] = $projectPhasePlans[0][0]['MaxEndDate'];
            $this->Project->id = $id;
            $this->Project->save($_datas);
        }
        $this->set(compact('projectPhasePlans'));
        /**
         * Danh sach employee: PM, TECH, CHIEF
         */
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $listEmployeeManagers = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('type' => 'RA')
            ),
            'fields' => array('project_manager_id', 'is_backup', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listReadAccess = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA'
            ),
            'fields' => array('project_manager_id', 'is_profit_center', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listEmployeeManagers['RA'] = !empty($listReadAccess) && !empty($listReadAccess['RA']) ? $listReadAccess['RA'] : array();

        if (!empty($this->data['Project']['project_manager_id'])) {
            $listEmployeeManagers['PM'][$this->data['Project']['project_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['chief_business_id'])) {
            $listEmployeeManagers['CB'][$this->data['Project']['chief_business_id']] = 0;
        }
        if (!empty($this->data['Project']['technical_manager_id'])) {
            $listEmployeeManagers['TM'][$this->data['Project']['technical_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['functional_leader_id'])) {
            $listEmployeeManagers['FL'][$this->data['Project']['functional_leader_id']] = 0;
        }
        if (!empty($this->data['Project']['uat_manager_id'])) {
            $listEmployeeManagers['UM'][$this->data['Project']['uat_manager_id']] = 0;
        }
        /**
         * Lay cac phase co task
         */
        $phaseHaveTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('project_planed_phase_id', 'project_planed_phase_id')
        ));
        /**
         * Viet lai lan 2, get lai phase sau khi thuc hien save
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        /**
         * Change date 07/04/2014.
         * Get all family + sub family of company
         */
        $families = $subFamilies = array();
        $haveActivity = 'false';
        $this->loadModel('Family');
        $_families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields' => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeInfo['Company']['id'])));
        if (!empty($_families)) {
            foreach ($_families as $_family) {
                $dx = $_family['Family'];
                if (!empty($dx['parent_id'])) {
                    //sub family
                } else {
                    $families[$dx['id']] = $dx['name'];
                }
            }
            if (!empty($families)) {
                $this->loadModel('ActivityFamily');
                $family_ids = array_keys($families);
                $subFamilies = $this->ActivityFamily->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => array_shift($family_ids)
                    ),
                    'fields' => array('id', 'name'),
                    'order' => array('name')
                ));
            }
        }
        if ($employeeInfo['Role']['name'] === 'admin' || $employeeInfo['Role']['name'] === 'pm') {
            /**
             * Kiem tra xem activity da co timesheet nao chua
             */
            if (!empty($this->data['Project']['activity_id']) || $this->data['Project']['activity_id'] != 0) {
                $this->loadModel('ActivityRequest');
                $this->loadModel('ActivityTask');
                $tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $this->data['Project']['activity_id']),
                    'fields' => array('id', 'id')
                ));
                if (!empty($tasks)) {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'OR' => array(
                                'activity_id' => $this->data['Project']['activity_id'],
                                'task_id' => $tasks
                            )
                        )
                    ));
                } else {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $this->data['Project']['activity_id'])
                    ));
                }
                if ($request != 0) {
                    $haveActivity = 'true';
                }
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeInfo['Company']['id']),
            'fields' => array('id', 'name')
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        // BudgetSetting
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        $this->set(compact('phasePlans', 'phaseHaveTasks', 'avatarOfEmploys', 'listEmployeeManagers', 'employeeInfo', 'families', 'subFamilies', 'haveActivity', 'budgetCustomers', 'budget_settings'));
        $this->set(compact('project_name', 'employees', 'currency_name', 'name_company', 'changeProjectManager', 'profitCenters', 'adminSeeAllProjects', 'activate_family_linked_program'));
        $this->set('project_id', $id);
    }

    function your_form($id = null) {
        // redirect sang new design neu selected new design
        if (!empty($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']) {
            $this->redirect(array('controller' => 'projects_preview', 'action' => 'your_form/' . $id));
        }
        $this->loadModels('BudgetSetting', 'Project', 'ProjectEmployeeManager', 'CompanyEmployeeReference');
        $this->_checkRole(false, $id);
        // kiem tra PM co the change project manager select field
        $pmCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'project_id' => $id,
                'type NOT' => 'RA'
            )
        ));
        // kiem tra PM o bang project
        $pmOfProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'id' => $id
            )
        ));
        // kiem tra PM o bang project
        $empCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type NOT' => 'RA',
                'project_id' => $id
            )
        ));
        $pmCanChange = (!empty($pmCanChange) || !empty($pmOfProject)) ? true : false;
        $company_id = $this->employee_info['Company']['id'];
        $getPM = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id,
                'company_id' => $company_id,
            ),
            'fields' => array('project_manager_id')
        ));
        $id_PM = !empty($getPM['Project']['project_manager_id']) ? $getPM['Project']['project_manager_id'] : 0;
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $page = 'Details';
        $curentPage = 'your_form';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form');
        $this->set(compact('page', 'budget_settings', 'id_PM', 'pmCanChange'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $view_id = null) {
        $redirect = array('action' => 'index');
        $ajax = false;
        if (isset($_POST['ajax']))
            $ajax = $_POST['ajax'];
        if (isset($_GET['cate'])) {
            $redirect['?'] = array('cate' => $_GET['cate']);
        }
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project', true), 'error');
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect($redirect);
            }
        }

        if (!$this->_checkRole(true, $id)) {
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect($redirect);
            }
        }
        if (!$this->checkPMBeforeDeleteProject($id)) {
            $this->redirect($redirect);
        }
        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->cakeError('error404', array(array('url' => $id)));
        }
        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->cakeError('error404', array(array('url' => $id)));
                }
            }
        }
        // security
        $this->loadModel('Activity');
        $activityId = $this->Activity->find('first', array(
            'recursive' => -1,
            'conditions' => array('project' => $id),
            'fields' => array('id')
        ));
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'id')
        ));
        $request = $request_timesheet = 0;
        if (!empty($projectTasks)) {
            $activityTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $projectTasks),
                'fields' => array('id', 'id')
            ));
            if (!empty($activityTasks)) {
                $request = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $activityTasks,
                        'status' => 2,
                    )
                ));
                $request_timesheet = $this->ActivityRequest->find('count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'task_id' => $activityTasks,
                        'status <>' => 2,
                    )
                ));
            }
        }
        if (!empty($activityId) && $activityId['Activity']['id']) {
            $request_activity = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId['Activity']['id'],
                    'status' => 2,
                )
            ));
            $ac_request_timesheet = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityId['Activity']['id'],
                    'status <>' => 2,
                )
            ));
            $request += $request_activity;
            $request_timesheet += $ac_request_timesheet;
        }
        if ($request != 0 || $request_timesheet != 0) {
            if ($request != 0) {
                $this->Session->setFlash(__('The project have task(s) with consumed.', true), 'error');
            } else {
                $this->Session->setFlash(__('A task is used in a timesheet.', true), 'error');
            }
            if ($ajax == true) {
                die(1);
            } else {
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Project->recursive = -1;
            $p = $this->Project->read(null, $id);
            if ($this->Project->delete($id)) {
                $this->writeLog($p, $this->employee_info, sprintf('Delete project `%s`', $p['Project']['project_name']), $p['Project']['company_id']);
                /**
                 * Delete staffing
                 */
                $this->loadModel('TmpStaffingSystem');
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $id), false);
                /**
                 * Delete task and assign task
                 */
                if (!empty($projectTasks)) {
                    $this->ProjectTask->deleteAll(array('ProjectTask.id' => $projectTasks), false);
                    $this->loadModel('ProjectTaskEmployeeRefer');
                    $this->ProjectTaskEmployeeRefer->deleteAll(array('project_task_id' => $projectTasks), false);
                }
                /**
                 * Delete linked activity
                 */
                if (!empty($activityId) && $activityId['Activity']['id']) {
                    // When you delete a project linked to an activity delete the project and the activity linked ( excepted if there are consumed )
                    $this->Activity->delete($activityId['Activity']['id'], false);
                    /**
                     * Xoa cac task linked
                     */
                    $act_task_id = $this->ActivityTask->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $projectTasks,
                        ),
                        'fields' => array('id', 'id'),
                    ));
                    if (!empty($act_task_id)) {
                        $this->ActivityRequest->deleteAll(array('task_id' => $act_task_id), false);
                    }
                    $this->ActivityRequest->deleteAll(array('ActivityRequest.activity_id' => $activityId['Activity']['id']), false);
                    $this->ActivityTask->deleteAll(array('project_task_id' => $projectTasks), false);

                    // clean cache menu acitivity / task 
                    $company = $this->employee_info['Company']['id'];
                    $cacheName = $company . '_' . $this->employee_info['Employee']['id'] . '_context_menu';
                    Cache::delete($cacheName);
                    $cacheNameMenu = $company . '_' . $this->employee_info['Employee']['id'] . '_context_menu_cache';
                    Cache::delete($cacheNameMenu);
                }
                $this->Session->setFlash(__('Deleted', true), 'success');
                if (is_numeric($view_id)) {
                    $this->redirect(array('controller' => 'user_views', 'action' => 'project_view', $view_id));
                } else {
                    if ($ajax == true) {
                        die(1);
                    } else {
                        $this->redirect($redirect);
                    }
                }
            }
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        if ($ajax == true) {
            die(1);
        } else {
            $this->redirect($redirect);
        }
    }

    private function checkPMBeforeDeleteProject($project_id) {
        // Employee allow delete project
        // PM has right delete project + PM is PM of this project
        $delete = true;
        $this->loadModel('ProjectEmployeeManager');
        // kiem tra PM co the change project manager select field
        $pmCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'project_id' => $project_id,
                'type' => 'PM'
            )
        ));
        // kiem tra PM o bang project
        $pmOfProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'id' => $project_id
            )
        ));

        $pmCanDelete = (!empty($pmCanChange) || !empty($pmOfProject)) ? true : false;

        if ($this->employee_info['Role']['name'] == 'pm' && (!$this->employee_info['Employee']['delete_a_project'] || !$pmCanDelete)) {
            $this->Session->setFlash(__('Employee not allow delete project', true), 'error');
            $delete = false;
        }
        return $delete;
    }

    /**
     * get_project_manager
     * Get project manager
     *
     * @return void
     * @access public
     */
    function get_project_manager($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
                'recursive' => -1,
                'fields' => array('employee_id', 'role_id'),
                'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
            $projectEmployees = $this->ProjectTask->Employee->find('all', array(
                'order' => 'last_name  ASC',
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_keys($companyEmployees),
					'Employee.last_name NOT' => 'NULL'
                ),
                'fields' => array('first_name', 'last_name', 'id')));
            $employees = array();
            foreach ($projectEmployees as $projectEmployee) {
                $employees['project'][$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
                foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                    if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                        $employees[$key][$projectEmployee['Employee']['id']] = $employees['project'][$projectEmployee['Employee']['id']];
                    }
                }
            }
            foreach ($employees as $key => $employee) {
                $_output = '';
                foreach ($employee as $_id => $_val) {
                    $_output .= "<option value='" . $_id . "'>" . $_val . "</option>";
                }
                $employees[$key] = $_output;
            }
            echo json_encode($employees);
        }
        exit();
    }

    /**
     * get_phase
     * Get Phase
     *
     * @return void
     * @access public
     */
    function get_phase($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectPhase->recursive = -1;
            $phaseIds = $this->ProjectPhase->find('list', array('conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => array('ProjectPhase.phase_order ASC')));
            if (!empty($phaseIds)) {
                foreach ($phaseIds as $id => $phase_name) {
                    echo "<option value='" . $id . "'>" . $phase_name . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_status
     * Get status
     *
     * @return void
     * @access public
     */
    function get_status($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectStatus->recursive = -1;
            $statusIds = $this->ProjectStatus->find('list', array('conditions' => array('ProjectStatus.company_id' => $company_id)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_complexity
     * Get complexity
     *
     * @return void
     * @access public
     */
    function get_complexity($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectComplexity->recursive = -1;
            $statusIds = $this->ProjectComplexity->find('list', array('conditions' => array('ProjectComplexity.company_id' => $company_id, 'ProjectComplexity.display' => 1)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_priority
     * Get priority
     *
     * @return void
     * @access public
     */
    function get_priority($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectPriority->recursive = -1;
            $statusIds = $this->ProjectPriority->find('list', array('conditions' => array('ProjectPriority.company_id' => $company_id)));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_currency
     * Get Currency
     *
     * @return void
     * @access public
     */
    function get_currency($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->Currency->recursive = -1;
            $statusIds = $this->Currency->find('list', array('conditions' => array('Currency.company_id' => $company_id)));
            if (!empty($statusIds)) {
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_program
     * Get program for project
     *
     * @return void
     * @access public
     */
    function get_program($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectAmrProgram->recursive = -1;
            $statusIds = $this->ProjectAmrProgram->find('list', array('conditions' => array('ProjectAmrProgram.company_id' => $company_id), 'fields' => array('amr_program')));
            if (!empty($statusIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($statusIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_project_type
     * Get typer for project
     *
     * @return void
     * @access public
     */
    function get_project_type($company_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if ($company_id != "") {
            $this->ProjectType->recursive = -1;
            $typeIds = $this->ProjectType->find('list', array('conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1), 'fields' => array('project_type')));
            if (!empty($typeIds)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($typeIds as $key => $value) {
                    echo "<option value='" . $key . "'>" . $value . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }

    /**
     * get_project_sub_type
     * Get subtype for project
     *
     * @return void
     * @access public
     */
    function get_project_sub_type($id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectType->ProjectSubType->find('list', array('conditions' => array('ProjectSubType.project_type_id' => $id, 'ProjectSubType.display' => 1), 'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type')));
            if (!empty($list)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "'>" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub type--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub type--%s", true), '<option>', '</option>');
        }
    }

    /**
     * get_project_sub_sub_type
     * Get subsubtype for project
     *
     * @return void
     * @access public
     */
    function get_project_sub_sub_type($id = null) {
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectSubType->find('list', array(
				'conditions' => array(
					'ProjectSubType.parent_id' => $id, 
					'ProjectSubType.display' => 1
				), 
				'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type')
			));
			
            if (!empty($list)) {
                echo "<option >" . __("--Select--", true) . "</option>";
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "'>" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub sub type--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub sub type--%s", true), '<option>', '</option>');
        }
    }

    /**
     * exportExcel
     *
     * @return void
     * @access public
     */
    function exportExcel($view_id = null) {
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));
        $view_content = $view_content["UserView"]["content"];
        if ($view_content == "") { //default view
            $view_content = '<user_view>
                                    <project_detail project_name = "Project Name" />
                                    <project_detail company_id = "Company" />
                                    <project_detail project_manager_id = "Project Manager" />
                                    <project_detail project_priority_id = "Priority" />
                                    <project_detail project_status_id = "Status" />
                                    <project_detail start_date = "Start Date" />
                            </user_view>';
        }
        $this->set('view_content', $view_content);
        $this->layout = 'excel';
        if ($this->is_sas)
            $projects = $this->Project->find('all');
        else {
            $projects = array();
            $sub_companies = $this->Project->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
            foreach ($sub_companies as $sub_company) {
                $projects = array_merge($projects, $this->Project->find('all', array('conditions' => array('Project.company_id' => $sub_company['Company']['id']))));
            }
        }
        $this->set('projects', $projects);
    }

    /**
     * exportExcelDetail
     * Export to Excel in project detail
     * @return void
     * @access public
     */
    function exportExcelDetail($page = 'Details', $project_id = null) {
        /*
         * Security - 2015/03/11
         * check company id
         */
        $project = $this->Project->find('first', array('fields' => array('Project.id', 'Project.project_name', 'Project.company_id'), 'conditions' => array('Project.id' => $project_id)));
        if (!isset($this->employee_info['Company']['id']) || $this->employee_info['Company']['id'] != $project['Project']['company_id']) {
            $this->Session->setFlash(__('The project to export was not found', true), 'error');
            $this->redirect('/projects');
        }
        $this->set('columns', $this->name_columna);
        $employee_current = $this->employee_info['Employee']['id'];
        $view_id = $this->Project->UserDefaultView->find('list', array('fields' => array('UserDefaultView.employee_id', 'UserDefaultView.user_view_id')));
        $this->loadModel('Translation');
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => $page,
                'TranslationSetting.company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        $view_content = array();
        foreach ($translation_data as $key => $value) {
            $dx = $value['Translation'];
            $dy = $value['TranslationSetting'];
            if ($dy['show'] == 1 && !empty($dx['field'])) {
                $view_content[$dx['field']] = $dx['original_text'];
            }
        }
        $this->set('view_content', $view_content);
        $projects = $this->Project->find('all', array('conditions' => array('Project.id' => $project_id)));
        $employees = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'fullname'),
            'conditions' => array('is_sas' => 0),
            'order' => array('id' => 'ASC')
        ));
        $this->set('employees', $employees);
        $this->set('project_name', $project);
        $this->set('projects', $projects);
        //$this->layout = 'excel';
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->loadModel('ProjectListMultiple');
        $listMuti = $this->ProjectListMultiple->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            )
        ));
        $_listMuti = array();
        foreach ($listMuti as $key => $value) {
            $dx = $value['ProjectListMultiple'];
            $_listMuti[$dx['key']][$dx['id']] = $dx['project_dataset_id'];
        }
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id'], 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set('datasets', $datasets);
        $this->set(compact('_listMuti'));
    }

    /**
     * project_detail_view
     *
     * @return void
     * @access public
     */
    function project_detail_view($view_id = null, $project_id = null) {
		$this->_checkRole(false, $project_id);
        if (!empty($this->data)) {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);
            $data_project_teams = $this->params['form'];
            if ($this->Project->save($this->data)) {
                $view_id = $this->params["form"]["ViewId"];
                $project_id = $this->params["form"]["ProjectId"];
                $this->Session->setFlash(__('The project has saved.', true));
            } else {
                $this->Session->setFlash(__('The project could not be saved. Please, try again.', true));
            }
            $this->redirect("/user_views/project_detail_view/" . $view_id . "/" . $project_id);
        }
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
        $datas = $this->ActivityRequest->find('all', array(
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
        $taskIdOfActivity = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        $_datas = $this->ActivityRequest->find('all', array(
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
        foreach ($_datas as $_data) {
            $employees[$_data['ActivityRequest']['employee_id']] = $_data['ActivityRequest']['employee_id'];
            // tinh sumactivity. 201/01/2017
            if (empty($_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]])) {
                $_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]] = 0;
            }
            $_sumActivitys[$taskIdOfActivity[$_data['ActivityRequest']['task_id']]] += $_data[0]['value'];
            //end
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach ($_sumEmployees as $id => $_sumEmployee) {
            $dataFromEmployees[$taskIdOfActivity[$id]][] = $_sumEmployee;
        }
        $rDatas = array();
        if (!empty($dataFromEmployees)) {
            foreach ($dataFromEmployees as $id => $dataFromEmployee) {
                foreach ($dataFromEmployee as $values) {
                    foreach ($values as $employ => $value) {
                        if (!isset($rDatas[$id][$employ])) {
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
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
        if (!empty($rDatas)) {
            foreach ($rDatas as $id => $rData) {
                if (in_array($id, array_keys($sumEmployees))) {
                    
                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if (!empty($sumEmployees)) {
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if (!empty($rDatas)) {
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
        if (!empty($sumEmployGroups)) {
            foreach ($sumEmployGroups as $key => $sumEmployGroup) {
                foreach ($sumEmployGroup as $acId => $values) {
                    foreach ($values as $employs => $value) {
                        if (!isset($sumEmployees[$acId][$employs])) {
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
     * Index lazy
     *
     * @return void
     * @access public
     */
	 /* Phần này chưa thực hiện lấy list project từ url */
    public function index_lazy($viewId = null) {
		$mobileEnabled = $this->isTouch();
		$this->loadModels('CompanyColumnDefault', 'Menu');
		$company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : '';
		$employee = $this->employee_info;
		$profileName = array();
		$viewGantt = false;
        if (!empty($employee['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employee['Employee']['profile_account']
                )
            ));
        }
		
		$column_width = $this->CompanyColumnDefault->find('list', array(
			'recursive' => -1,
			'fields' => array('field_name','width'),
			'conditions' => array(
				'CompanyColumnDefault.company_id' => $company_id,
			)
		));
		
		$listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
		$this->getDefaultScreenProject();
		
		// get category
		/*
		1: inprogress
		2: opportunity
		3: archived
		4: model
		5: inprogress + opportunity
		6: view all
		*/
		$cate = !empty( $this->params['url']['cate']) ? $this->params['url']['cate'] : '';
		if( empty($cate)) $cate = $this->Session->read("App.status");
		if( empty($cate)) $cate = 1;
		$this->Session->write("App.status", $cate);
		
		/* Check permission */ 
		$role = $employee['Role']['name'];
		$seeAllProjects = ($role == 'admin') ? true :  !empty($this->companyConfigs['see_all_projects']) ? true : false;
		
		$canAddProject = $canAddTask = false;
		$canAddProject = (($role == 'admin') || (!empty($employee['Employee']['create_a_project'])));
		$profileName = array();
        if (!empty($employee['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employee['Employee']['profile_account']
                )
            ));
			if ( !empty( $profileName['ProfileProjectManager']['can_create_project'] ) ) $canAddProject = 1;
        }
		
		$task_screen_newdesign = $this->Menu->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $employee["Company"]["id"],
				'model' => 'project',
				'controllers' => 'project_tasks'
			),
			'fields' => array('enable_newdesign')
		));
		$canAddTask = ($role == 'admin' || $role == 'pm');
		$canAddTask = $canAddTask && !empty($task_screen_newdesign);
		$canAddEmployee = ($role == 'admin' || (!empty($employee['CompanyEmployeeReference']['control_resource'])));
		if ( !empty( $profileName['ProfileProjectManager']['create_resource'] ) ) $canAddEmployee = 1;
		
		/* END Check permission */ 
		
		/* Get List Project */
		$listProjectIds = $listProjectOfPM = array();
		$listProjectIds = $this->getProjectOfPM(null, true, $cate); // null is current employee
		$listProjectOfPM = $this->getProjectOfPM(null, false, $cate);
		/* END Get List Project */
		
		/* get View */
		// -2: khong co gi ca. -1: Predefined. 0: Default
		if(empty($viewId)){
			if ($cate == 2)	$viewId = $this->Session->read('App.ProjectStatusOppor');
			else 			$viewId = $this->Session->read('App.ProjectStatus');
		}
		if ($viewId == -1 || $viewId == -2) {
			$viewId = null;
		}		
		/* END get View */
		// Check viewID is exist
		$cond = array(
			'UserView.model' => 'project',
			'UserStatusView.employee_id' => $this->employee_info['Employee']['id'],
			'OR' => array(
				'UserView.employee_id' => $this->employee_info['Employee']['id'],
				array(
					'UserView.company_id' => $this->employee_info["Company"]["id"],
					'UserView.public' => true,
					'UserView.employee_id !=' => $this->employee_info['Employee']['id'],
				),
			),
		);
		if( $mobileEnabled ) $cond['UserStatusView.mobile'] = 1;
		/*
		1: inprogress
		2: opportunity
		3: archived
		4: model
		5: inprogress + opportunity
		6: view all
		*/
		switch($cate){
			case 1: {// InProgress
				$cond['UserStatusView.progress_view'] = 1;
				break;
			}
			case 2: {// opportunity
				$cond['UserStatusView.oppor_view'] = 1;
				break;
			}
			case 3: {// archived
				$cond['UserStatusView.archived_view'] = 1;
				break;
			}
			case 4: {// model
				$cond['UserStatusView.model_view'] = 1;
				break;
			}
			case 5: {// model
				$cond['UserStatusView.progress_view'] = 1;
				$cond['UserStatusView.oppor_view'] = 1;
				break;
			}
			default: {
				break;
			}
		}
		$getView = $this->Project->UserView->find('all', array(
			'recursive' => -1,
			'fields' => array('*'),
			// 'fields' => array(
				// 'UserView.name',
				// 'UserView.content',
				// 'UserView.gantt_view',
				// 'UserView.initial',
				// 'UserView.real',
				// 'UserView.type',
				// 'UserView.stones',
				// 'UserView.display_all_name_of_milestones',
				// 'UserView.from',
				// 'UserView.to',
				// 'UserStatusView.*'
			// ),
			'joins' => array(
				array(
					'table' => 'user_status_views',
					'alias' => 'UserStatusView',
					'conditions' => array(
						'UserView.id = UserStatusView.user_view_id',
					),
				),	
			),
			'conditions' => $cond,
			'order' => 'UserView.name ASC',
		));
		unset($cond);
		if($viewId){
			
			if( empty($fieldset)){
				$viewId = null;
			}else{
				$viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
			}
			
		}

		if( $viewGantt) $this->helpers = array_merge($this->helpers, array('GanttVs'));
		$this->set(compact('profileName','column_width'));
		$this->set(compact('$listPc',''));
		// exit;
		die('OK');
	}
	/** END Index lazy */
	
	/* 
	 * getDefaultScreenProject
	 * Send to view default screen for project
	 * ACLController
     * ACLAction
	 */
	function getDefaultScreenProject(){
		$ACLController = '';
        $ACLAction = '';
		$company_id = $this->employee_info['Company']['id'];
		if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $screenDefaults = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $listScreen = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            if(!empty($listScreen)) {
                $ACLController = $listScreen['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $listScreen['ProfileProjectManagerDetail']['functions'];
            }
            if(!empty($screenDefaults)) {
                $ACLController = $screenDefaults['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $screenDefaults['ProfileProjectManagerDetail']['functions'];
            }
        } else {
            $screenDefaults = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $ACLController = 'projects';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
        }
		
		/* Kiem tra screen Dashboard enable / disable */
        $screenDashboard = array();
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company_id,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));
		
		$screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();
        $this->set(compact('screenDashboard','ACLController','ACLAction'));
	}
	/* END getDefaultScreenProject */
	 
    /**
     * Huynh 28-10-2019 Function updateMultiSort
	 * Sperator from index
     */
	public function updateMultiSort(){
		$fieldSort = array();
		if (isset($_POST['multiSort'])) {
            if ($_POST['actSort'] == 'add') {
                if ($_POST['flag'] != 0) {
                    $fieldSort = $this->Session->read('sFieldSort');
                } else {
                    $fieldSort = array();
                }
                $fieldSort[] = array('columnId' => $_POST['value'], 'sortAsc' => 1);
            }
            if ($_POST['actSort'] == 'remove') {
                $old_fieldSort = $this->Session->read('sFieldSort');
                $fieldSort = array();
                foreach ($old_fieldSort as $array) {
                    if ($array['columnId'] != $_POST['value']){
                        $fieldSort[] = array(
							'columnId' => $array['columnId'],
							'sortAsc' => $array['sortAsc']
						);
					}
                }
            }
            if ($_POST['actSort'] == 'update') {
                $fieldSort = $this->Session->read('sFieldSort');
                $count = count($fieldSort);
                for ($i = 0; $i < $count; $i++) {
                    if ($fieldSort[$i]['columnId'] == $_POST['value'])
                        $fieldSort[$i]['sortAsc'] = round($_POST['type']);
                };
            }
        }
		$this->Session->write('sFieldSort', $fieldSort);
		die(json_encode($fieldSort));
	}
	 
	private function dataDashboard(){
        $dash_widgets = $this->get_dash_widget();
        $this->loadModels('ProjectDashboard', 'ProjectDashboardActive', 'ProjectDashboardShare', 'HistoryFilter');
		$employee_id = $this->employee_info['Employee']['id'];
		$company_id = $this->employee_info['Company']['id'];
		$canShareDashboard = !empty( $this->companyConfigs['share_a_dashboard']);
		$dashboards = $shared_dashboards = array();
		if( $canShareDashboard){
			$dashboards = $this->ProjectDashboard->find('all', array(
				'recusive' => -1,
				'conditions' => array(
					'ProjectDashboard.company_id' => $company_id,
					'OR' => array(
						'ProjectDashboard.employee_id' => $employee_id,
						'ProjectDashboard.share_type' => 'everybody',
					)
				),
				'joins' => array(
					array(
						'table' => 'project_dashboard_shares',
						'alias' => 'ProjectDashboardShare',
						'conditions' => array(
							'ProjectDashboard.id = ProjectDashboardShare.dashboard_id', 
							'ProjectDashboardShare.employee_id' => $employee_id
						),
						'type' => 'left'
					)
				),
				'fields' => array('ProjectDashboard.id', 'ProjectDashboard.employee_id', 'ProjectDashboard.share_type', 'ProjectDashboard.dashboard_data'),
				'order' => array('share_type')
			));
			$shared_dashboards = $this->ProjectDashboard->find('all', array(
				'recusive' => -1,
				'conditions' => array(
					'ProjectDashboard.company_id' => $company_id,
					'ProjectDashboard.share_type' => 'resource',
					'ProjectDashboardShare.employee_id' => $employee_id,
				),
				'joins' => array(
					array(
						'table' => 'project_dashboard_shares',
						'alias' => 'ProjectDashboardShare',
						'conditions' => array('ProjectDashboard.id = ProjectDashboardShare.dashboard_id'),
						'type' => 'inner'
					)
				),
				'fields' => array('ProjectDashboard.id', 'ProjectDashboard.employee_id', 'ProjectDashboard.share_type', 'ProjectDashboard.dashboard_data'),
			));
		}else{
			$dashboards = $this->ProjectDashboard->find('all', array(
				'recusive' => -1,
				'conditions' => array(
					'ProjectDashboard.company_id' => $company_id,
					'ProjectDashboard.employee_id' => $employee_id,
				),
				'fields' => array('ProjectDashboard.id', 'ProjectDashboard.employee_id', 'ProjectDashboard.share_type', 'ProjectDashboard.dashboard_data'),
			));
		}
        $active_dashboard = $this->ProjectDashboardActive->find('first', array(
            'recusive' => -1,
            'conditions' => array(
                'ProjectDashboardActive.employee_id' => $employee_id,
            ),
            'fields' => array('employee_id', 'dashboard_id')
        ));
		$active_id = !empty($active_dashboard) ? $active_dashboard['ProjectDashboardActive']['dashboard_id'] : 0;
        $dashboards = array_merge($dashboards, $shared_dashboards);
		if( empty( $dashboards)){
			$this->ProjectDashboard->create();
			$this->ProjectDashboard->save(array(
				'company_id' => $company_id,
				'employee_id' => $employee_id,
				'created' => time(),
				'updated' => time(),
				'dashboard_data' => serialize(array(
					'name' => __('Dashboard', true) . ' 1'
				)),
			));
			if( empty($active_id)){
				$dashboard_id = $this->ProjectDashboard->id;
				$this->ProjectDashboardActive->create();
				$this->ProjectDashboardActive->save(array(
					'employee_id' => $employee_id,
					'dashboard_id' => $dashboard_id,
					'updated' => time(),
				));
				$active_id = $this->ProjectDashboardActive->id;
			}
			return $this->dataDashboard();
		}
		$dashboard_histories = array_map(function($dashboard) {
			$share = array();
			if( !empty($dashboard['ProjectDashboardShare'])){
				foreach($dashboard['ProjectDashboardShare'] as $v){
					$share[] = $v['employee_id'];
				}
			}
			$dashboard = $dashboard['ProjectDashboard'];
			$dashboard['dashboard_data'] = unserialize($dashboard['dashboard_data']);
			$dashboard['share_resource'] = $share;
            return $dashboard;
        }, $dashboards);
        $dashboard_histories = Set::combine($dashboard_histories, '{n}.id', '{n}');
		$dashboard_histories['acti'] = !empty( $active_id) ? $active_id : 0;
        $this->set(compact('dashboard_histories', 'dash_widgets', 'canShareDashboard' ));
		$this->_getListEmployee();
    }
	/**
     * Index
     *
     * @return void
     * @access public
     */
    public function index($viewId = null) {
        if (isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']) {
            if (!((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))) {
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview = trim(str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_') . '_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/' . $value;
                }
                if (isset($_url_param['url']))
                    unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }
		ini_set('memory_limit', '256M');  
        $this->dataDashboard();
        $mobileEnabled = $this->isTouch();
        $this->loadModels('Menu', 'LogSystem', 'ProjectFinance', 'ProjectPhasePlan', 'Employee', 'ProfitCenter','HistoryFilter');
		
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        /**
         * Doc lai 2 tham so create va delete cua employee dang login
         */
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employee['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        $profileName = array();
        if (!empty($employInfors['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
        }
		/**
         * checkWidth dung de truyen width mac dinh ra hien thi. QuanNV
         */
		$this->loadModel('CompanyColumnDefault');
		$checkWidth = array();
		$checkWidth = $this->CompanyColumnDefault->find('list', array(
			'recursive' => -1,
			'fields' => array('field_name','width'),
			'conditions' => array(
				'CompanyColumnDefault.company_id' => $company_id,
			)
		));
		
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
		$this->set(compact('profileName','checkWidth', 'loadFilter'));
        $employee['Employee']['create_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? $employInfors['Employee']['create_a_project'] : 0;
        $employee['Employee']['delete_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? $employInfors['Employee']['delete_a_project'] : 0;
        $employee['Employee']['update_your_form'] = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? $employInfors['Employee']['update_your_form'] : 0;
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        /**
         * Multiples Sort Building
         */
        if (isset($_POST['multiSort'])) {
            $fieldSort = array();
            if ($_POST['actSort'] == 'add') {
                if ($_POST['flag'] != 0) {
                    $fieldSort = $this->Session->read('sFieldSort');
                } else {
                    $fieldSort = array();
                    $this->Session->write('sFieldSort', '');
                }
                $fieldSort[] = array('columnId' => $_POST['value'], 'sortAsc' => 1);
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if ($_POST['actSort'] == 'remove') {
                $fieldSort = $this->Session->read('sFieldSort');
                $fieldSort1 = array();
                foreach ($fieldSort as $array) {
                    if ($array['columnId'] != $_POST['value'])
                        $fieldSort1[] = array('columnId' => $array['columnId'], 'sortAsc' => $array['sortAsc']);
                }
                $this->Session->write('sFieldSort', $fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if ($_POST['actSort'] == 'update') {
                $fieldSort = $this->Session->read('sFieldSort');
                $count = count($fieldSort);
                for ($i = 0; $i < $count; $i++) {
                    if ($fieldSort[$i]['columnId'] == $_POST['value'])
                        $fieldSort[$i]['sortAsc'] = round($_POST['type']);
                }
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        /**
         * Su dung helper GanttVs
         */
        $this->helpers = array_merge($this->helpers, array('GanttVs'));
        /**
         * Lay screen mac dinh cua project
         */
        $checkDisplayProfileScreen = 1;
        $ACLController = '';
        $ACLAction = '';
        if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $screenDefaults = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $listScreen = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            if (empty($listScreen)) {
                $checkDisplayProfileScreen = 2;
            } else {
                $ACLController = $listScreen['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $listScreen['ProfileProjectManagerDetail']['functions'];
            }
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $screenDefaults['ProfileProjectManagerDetail']['functions'];
            }
        } else {
            $screenDefaults = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $ACLController = 'projects';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
        }
        $this->set(compact('checkDisplayProfileScreen'));
        /*
         * Kiem tra screen Dashboard enable / disable
         */
        $screenDashboard = array();
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company_id,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));
		
		$screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();
        $this->set(compact('screenDashboard'));
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('id' => 'ASC')
        ));
        $logGroups = Set::combine($logSystems, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        /**
         * Extract url
         */
        extract(array_merge(array('cate' => null), $this->params['url']));
        /**
         * Check category
         */
        if (!empty($cate)) {
            if ($cate != 2) {
                $this->Session->write("App.status", $cate);
                if ($mobileEnabled)
                    $this->Session->write("App.mobileStatus", $cate);
            } else {
                $this->Session->write("App.status", $cate);
            }
        } else {
            $cate = $this->Session->read("App.status");
            if ($mobileEnabled) {
                $mobileCategory = $this->Session->read("App.mobileStatus");
                if ($mobileCategory) {
                    if ($mobileCategory != 2) {
                        $this->Session->write("App.mobileStatus", $mobileCategory);
                    }
                    $this->Session->write("App.mobileStatus", $mobileCategory);
                } else {
                    $cate = 1;
                    $this->Session->write("App.mobileStatus", 1);
                }
            } else {
                if ($cate) {
                    if ($cate != 2) {
                        $this->Session->write("App.status", $cate);
                    }
                    $this->Session->write("App.status", $cate);
                } else {
                    $cate = 1;
                    $this->Session->write("App.status", 1);
                }
            }
        }
        $checkStatus = -2; // -2: khong co gi ca. -1: Predefined. 0: Default
		$listCatView = $this->getPersonalizedViews($cate, true);
		if(!empty($listCatView)){
			if(!array_key_exists($viewId, $listCatView)){
				$viewId = -1;
			}
		}
        if ($cate == 2) {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileOppView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatusOppor');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        } else {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatus');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        }


        /**
         * Lay column display of project
         */
        $fieldset = array();
        $viewGantt = false;
        $confirmGantt = array();

        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                'conditions' => array('UserView.id' => $viewId)));

            $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
            $confirmGantt = array(
                'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                'real' => !empty($fieldset['UserView']['real']) ? true : false,
                'type' => 3, /** mac dinh la year */
                'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
            );
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            }
            $checkStatus = $viewId;
            if ($cate == 2) {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileOppView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatusOppor', $checkStatus);
                }
            } else {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatus', $checkStatus);
                }
            }
        } else {
            if ($mobileEnabled) {
                $defaultView = $this->Project->UserView->UserStatusView->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'UserStatusView.employee_id' => $employee['Employee']['id'],
                        'View.model' => 'project',
                        'UserStatusView.mobile' => 1
                    ),
                    'joins' => array(
                        array(
                            'table' => 'user_views',
                            'alias' => 'View',
                            'type' => 'inner',
                            'conditions' => array('View.id = UserStatusView.user_view_id')
                        )
                    ),
                    'fields' => array('user_view_id')
                ));
                if ($defaultView) {
                    $defaultView['UserDefaultView']['user_view_id'] = $defaultView['UserStatusView']['user_view_id'];
                }
            } else {
                $defaultView = $this->Project->UserDefaultView->find('first', array(
                    'conditions' => array(
                        'employee_id' => $employee['Employee']['id'],
                        'model' => 'project'
                    ),
                    'recursive' => -1,
                    'fields' => array('user_view_id')
                ));
            }
			/*
			* Hien thi view default cua user
			*/
            if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                $checkStatus = -2;
                $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                $fieldset = $this->Project->UserView->find('first', array(
                    'fields' => array('UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                    'conditions' => array('UserView.id' => $defaultView)));
                $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
                $confirmGantt = array(
                    'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                    'real' => !empty($fieldset['UserView']['real']) ? true : false,
                    'type' => 3, /** mac dinh la year */
                    'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                    'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                    'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                    'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
                );
                if (!empty($fieldset)) {
                    $fieldset = unserialize($fieldset['UserView']['content']);
                }
                $checkStatus = $viewId;
                $this->Session->write("App.PersonalizedDefault", true);
            } else {
                $checkStatus = -1;
                $this->loadModel('CompanyViewDefault');
                $this->loadModel('UserView');
                /*
                 * Kiem tra user chua co view default thi lay default trong admin
                 */
				$companyViewDefault = $this->CompanyViewDefault->find('first', array(
					'conditions' => array(
						'default_view' => 1,
						'company_id' => $employee['Company']['id'],
					),
					'recursive' => -1,
					'fields' => array('user_view_id')
				));
				// debug( $companyViewDefault); exit;
                if (empty($companyViewDefault)) {
                    $fieldset = array(
                        'Project.project_name',
                        'Project.project_manager_id',
                    );
                } else {
					$checkStatus = $companyViewDefault['CompanyViewDefault']['user_view_id'];
                    $fieldset = $this->Project->UserView->find('first', array(
                        'fields' => array('UserView.id', 'UserView.name', 'UserView.content'),
                        'conditions' => array('UserView.id' => $checkStatus)));
                    $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
                    $confirmGantt = array(
                        'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                        'real' => !empty($fieldset['UserView']['real']) ? true : false,
                        'type' => 3, /** mac dinh la year */
                        'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                        'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                        'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                        'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
                    );
                    if (!empty($fieldset)) {
                        $fieldset = unserialize($fieldset['UserView']['content']);
                    }
                }
                if ($mobileEnabled) {
                    $fieldset = array(
                        'Project.project_name',
                        'Project.project_manager_id',
                    );
                }
            }
        }
		$listFields = $fieldset;
        $checkReadAccess = false;
        if (!empty($fieldset) && in_array('Project.read_access', $fieldset)) {
            $checkReadAccess = true;
        }
        $checkFieldPurchase = false;
        foreach (array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid') as $v) {
            if (!empty($fieldset) && in_array($v, $fieldset)) {
                $checkFieldPurchase = true;
            }
        }
        $checkFieldMuti = false;
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($fieldset) && in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        $checkNextMileston = false;
        if (!empty($fieldset) && (in_array('Project.next_milestone_in_day', $fieldset) || in_array('Project.next_milestone_in_week', $fieldset))) {
            $checkNextMileston = true;
        }
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $noProjectManager = false;
        if (!in_array('Project.project_manager_id', $fieldset)) {
            $fieldset[] = 'Project.project_manager_id';
            $noProjectManager = true;
        }
		$getStaffingSystems = false;
        if (!empty($fieldset) && ( in_array('ProjectBudgetSyn.assign_to_employee', $fieldset)|| in_array('ProjectBudgetSyn.assign_to_profit_center', $fieldset))) {
            $getStaffingSystems = true;
        }
        $checkFieldCurrentPhase = false;
        if (in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
		$fieldset[] = 'MFavorite.modelId';
		$userFields = $fieldset;
		list($fieldset, $options) = $this->Project->parseViewField($fieldset);
		
		// debug( $fieldset); exit;
        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if ($value == 'comment') {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }

        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        $fieldFinancementTwoPlus = !empty($options['contain']['ProjectFinanceTwoPlus']) ? $options['contain']['ProjectFinanceTwoPlus'] : array();
        unset($options['contain']['ProjectFinanceTwoPlus']);
        $options['fields'][] = 'last_modified';
        //regardless which fields are selected, these are mandatory
        $options['fields'][] = 'project_manager_id';
        $options['fields'][] = 'technical_manager_id';
        $options['fields'][] = 'chief_business_id';
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!in_array('category', $options['fields'])) {
            $options['fields'][] = 'category';
        }
        // array project task fields.
        $projectTaskFields = array(
            'Initialworkload',
            'Workload',
            'Overload',
            'Consumed',
            'InUsed',
            'ManualConsumed',
            'Completed',
            'Remain',
            'Amount€',
            '%progressorder',
            '%progressorder€',
            'UnitPrice',
            'Consumed€',
            'Remain€',
            'Workload€',
            'Estimated€',
        );
        /**
         * Lay fields in Finance
         */
        $checkEngedMd = $checkTaskEuro = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if ($value == 'internal_costs_engaged_md') {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkEngedMd = true;
                }
                if (in_array($value, $projectTaskFields)) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkTaskEuro = true;
                }
            }
        }
		/* Huynh 16-07-2019
		* 
		*/
		$upload_documents_fields = array(
			'upload_documents_1',
			'upload_documents_2',
			'upload_documents_3',
			'upload_documents_4',
			'upload_documents_5',
		);
		foreach ($options['fields'] as $key => $value) {
			if (in_array($value,$upload_documents_fields) ){
				unset($options['fields'][$key]);				
			}
			$options['contain']['ProjectFile']['fields'] = array('id','key','file_attachment','type');
		}
		$isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
		$enableWidgets = array();
		$LANG = Configure::read('Config.language');
		if($isProfileManager != 0 ){
			$enableWidgets = $this->ProfileProjectManagerDetail->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
				),
				'fields' => array('widget_id',  'name_'.$LANG)
			));
		}else{
			$enableWidgets = $this->Menu->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
					'model' => 'project'
				),
				'fields' => array('widget_id', 'name_'.$LANG)
			));
		}	
		if( !$this->canSeeBudget){
			$budgetFields = $this->Project->budgetFields;
			$_patterns = !empty( $budgetFields['_pattern']) ? $budgetFields['_pattern'] : false;
			$_white_list = !empty( $budgetFields['_white_list']) ? $budgetFields['_white_list'] : false;
			unset($budgetFields['_pattern']);
			$new_fieldset = array();
			foreach( $fieldset as $i => $v){
				$_key = $v['key'];
				$keep = true;
				if( in_array($_key, $_white_list)){
					$new_fieldset[] = $v;
					continue;
				}
				if( $_patterns ) {
					foreach($_patterns as $_pattern)
						if( preg_match($_pattern, $_key, $matches))
							$keep = false;
				}
				if( $keep && in_array($_key, $budgetFields)) $keep = false;
				if( $keep) $new_fieldset[] = $v;
			}
			$fieldset = $new_fieldset;
			unset( $new_fieldset);
		}
		$get_milestone_wigdet = $get_finance_plus_widget = 0;
		$_fieldset = $fieldset;
		$i = 0;
		$filter_alert = 0;
		$get_progress = false;
		
		foreach($fieldset as $k => $_field){
			$_fieldset[$k]['order'] = $i;
			switch($_field['key']){
				case 'Project.project_name':
					$_fieldset[] = array(
						'key' => 'MFavorite.modelId',
						'name' => ' ',
						'nameExport' => __('Favorites',true),
						'path' => 'MFavorite.0.modelId',
						'order' => $i++,
					);
					break;
				case 'MFavorite.modelId':
					$_fieldset[$k] = '';
					break;
				case 'ProjectWidget.Phase':
					if( isset($enableWidgets['phase']) ){
						$get_progress = true;
						$filter_alert = 1;
						$wd_phase_name = $enableWidgets['phase'];
						$arr_phase = array(
							'0' => array(
								'key' => 'ProjectWidget.Phase_plan',
								'name' => $enableWidgets['phase'],
								'nameExport' => $wd_phase_name .' - '. __('Plan end date', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							'1' => array(
								'key' => 'ProjectWidget.Phase_progress',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
								'name' => ' ',
							),
							'2' => array(
								'key' => 'ProjectWidget.Phase_real',
								'name' => ' ',
								'nameExport' => $wd_phase_name .' - '. __('Real end date', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							'3' => array(
								'key' => 'ProjectWidget.Phase_diff',
								'name' => ' ',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);						
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_phase);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				case 'ProjectWidget.Milestone':
					if( isset($enableWidgets['milestone']) ){
						$filter_alert = 1;
						$get_milestone_wigdet = 1;
						$wd_mileston_name =  $enableWidgets['milestone'];
						$arr_milestone = array(
							array(
								'key' => 'ProjectWidget.Milestone_late',
								'name' => $enableWidgets['milestone'],
								'nameExport' => $wd_mileston_name .' - '. __('Late milestone', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Milestone_next',
								'name' => ' ',
								'nameExport' => $wd_mileston_name .' - '. __('Next milestone', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_milestone);
					}else{
						$_fieldset[$k] = '';
						
					}
					break;
				case 'ProjectWidget.FinancePlusinv':
					if( isset($enableWidgets['finance_plus']) ){
						$get_finance_plus_widget = 1;
						$filter_alert = 1;
						$inv_finance = array(
							array(
								'key' => 'ProjectWidget.FinancePlus_inv_budget',
								'name' => 'inv',
								'nameExport' => 'inv',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_inv_engaged',
								'name' => ' ',
								'nameExport' => 'inv',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_inv_percent',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
					
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $inv_finance);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				case 'ProjectWidget.FinancePlusfon':
					if( isset($enableWidgets['finance_plus']) ){
						$get_finance_plus_widget = 1;
						$filter_alert = 1;
						$fon_finance = array(
							array(
								'key' => 'ProjectWidget.FinancePlus_fon_budget',
								'name' => 'fon',
								'nameExport' => 'fon',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_fon_engaged',
								'name' => ' ',
								'nameExport' => 'fon',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_fon_percent',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
					
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $fon_finance);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				
				case 'ProjectWidget.FinancePlusfinaninv':
					if( isset($enableWidgets['finance_plus']) ){
						$get_finance_plus_widget = 1;
						$filter_alert = 1;
						$finaninv_finance = array(
							array(
								'key' => 'ProjectWidget.FinancePlus_finaninv_budget',
								'name' => 'finaninv',
								'nameExport' => 'finaninv',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_finaninv_engaged',
								'name' => ' ',
								'nameExport' => 'finaninv',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_finaninv_percent',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
					
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $finaninv_finance);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				
				case 'ProjectWidget.FinancePlusfinanfon':
					if( isset($enableWidgets['finance_plus']) ){
						$get_finance_plus_widget = 1;
						$filter_alert = 1;
						$finanfon_finance = array(
							array(
								'key' => 'ProjectWidget.FinancePlus_finanfon_budget',
								'name' => 'finanfon',
								'nameExport' => 'finanfon',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_finanfon_engaged',
								'name' => ' ',
								'nameExport' => 'finanfon',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.FinancePlus_finanfon_percent',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
					
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $finanfon_finance);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				case 'ProjectWidget.Synthesis':
					if( isset($enableWidgets['synthesis']) ){
						$filter_alert = 1;
						$arr_synthesis = array(
							array(
								'key' => 'ProjectWidget.Synthesis_budget',
								'name' => $enableWidgets['synthesis'],
								'nameExport' => $enableWidgets['synthesis'] . ' - '. __('Budget €', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Synthesis_forecast',
								'name' => ' ',
								'nameExport' => $enableWidgets['synthesis'] . ' - '. __('Forecast €', true),
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Synthesis_percent',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Phase',
								'order' => $i++,
							),
						);
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_synthesis);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				case 'ProjectWidget.Progress':
					$_fieldset[$k] = '';
					$_fieldset[] = array(
						'key' => 'ProjectWidget.Project_progress',
						'path' => 'ProjectWidget.0.Phase',
						'order' => $i++,
						'name' => __('% Progress', true),
					);
					$get_progress = true;
					break;
				
				case 'ProjectWidget.InternalBudgetMD':
					if( isset($enableWidgets['internal_cost']) ){
						$filter_alert = 1;
						$arr_internal = array(
							array(
								'key' => 'ProjectWidget.Internal_budget_md',
								'name' => $enableWidgets['internal_cost'] . ' '. __('M.D', true),
								'nameExport' => 'Internal Budget M.D',
								'path' => 'ProjectBudgetSyn.0.internal_costs_budget_man_day',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_forecast_md',
								'name' => ' ',
								'nameExport' => 'Internal Forecast M.D',
								'path' => 'ProjectWidget.0.internal_costs_forecasted_man_day',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_percent_forecast_md',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectBudgetSyn.0.Internal_percent_forecast_md',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_consumed_md',
								'name' => ' ',
								'nameExport' => 'Internal Engaged M.D',
								'path' => 'ProjectBudgetSyn.0.internal_costs_engaged_md',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_percent_consumed_md',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Internal_percent_consumed_md',
								'order' => $i++,
							),
						);
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_internal);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				case 'ProjectWidget.InternalBudgetEuro':
					if(isset($enableWidgets['internal_cost']) || isset($enableWidgets['synthesis'])){
						$filter_alert = 1;
						$currency = $this->getCurrencyOfBudget();
						$internal_cost_name = !empty($enableWidgets['internal_cost']) ? $enableWidgets['internal_cost'] . ' '. $currency : '';
						$arr_internal = array(
							array(
								'key' => 'ProjectWidget.Internal_budget_euro',
								'name' => $internal_cost_name,
								'nameExport' => 'Internal Budget €',
								'path' => 'ProjectBudgetSyn.0.internal_costs_budget',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_forecast_euro',
								'name' => ' ',
								'nameExport' => 'Internal Forecast €',
								'path' => 'ProjectWidget.0.internal_costs_forecast',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_percent_forecast_euro',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectBudgetSyn.0.Internal_percent_forecast_costs',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_engaged_euro',
								'name' => ' ',
								'nameExport' => 'Internal Engaged €',
								'path' => 'ProjectBudgetSyn.0.internal_costs_engaged',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.Internal_percent_consumed_euro',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectWidget.0.Internal_percent_consumed_costs',
								'order' => $i++,
							),
						);
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_internal);
					}else{
						$_fieldset[$k] = '';
					}
					break;
				
				case 'ProjectWidget.ExternalBudget':
					if( isset($enableWidgets['external_cost']) || isset($enableWidgets['synthesis'])){
						$filter_alert = 1;
						$arr_external = array(
							array(
								'key' => 'ProjectWidget.External_budget_erro',
								'name' => !empty($enableWidgets['external_cost']) ? $enableWidgets['external_cost'] : '',
								'nameExport' => 'External Budget Euro',
								'path' => 'ProjectBudgetSyn.0.external_costs_budget',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.External_forecast_erro',
								'name' => ' ',
								'nameExport' => 'External Forecast Euro',
								'path' => 'ProjectWidget.0.external_costs_forecast',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.External_percent_forecast_erro',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectBudgetSyn.0.external_percent_forecast_costs',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.External_ordered_erro',
								'name' => ' ',
								'nameExport' => 'External Ordered Euro',
								'path' => 'ProjectBudgetSyn.0.external_costs_ordered',
								'order' => $i++,
							),
							array(
								'key' => 'ProjectWidget.External_percent_ordered_erro',
								'name' => ' ',
								'nameExport' => '%',
								'path' => 'ProjectBudgetSyn.0.external_percent_ordered_costs',
								'order' => $i++,
							),
						);
						$_fieldset[$k] = '';
						$_fieldset = array_merge($_fieldset, $arr_external);
					}else{
						$_fieldset[$k] = '';
					}
					break;
			}
			$i++;
		}
		// Order lai sau khi xoa cac screen off
		if( !empty( $_fieldset)){
			$_fieldset = array_filter($_fieldset);
			$fieldset = array_values(Set::sort($_fieldset , '{n}.order', 'asc'));
		}
		if( !empty( $options['contain']['ProjectWidget']['fields'])){
			unset( $options['contain']['ProjectWidget']);
		}
		/* END Huynh*/
        $this->loadModel('ProjectFinanceTwoPlus');
        $financeFields = array();
        $LANG = Configure::read('Config.language');
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]), $this->ProjectFinanceTwoPlus->defaultField($employee["Company"]["id"]));
        /**
         * Lay project manager va thong tin confirm lien quan
         */
		 
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'][] = array('Project.company_id' => $subCompanies);
                if (!empty($cate)) {
                    if ($cate == 5) { //InProgress and Opportunity
                        $options['conditions'][] = array('Project.category' => array(1, 2));
                    } else if ($cate == 6) { //Opportunity
                        $options['conditions'][] = array('Project.category' => array(2));
                    } else {
                        $options['conditions'][] = array('Project.category' => $cate);
                    }
                }
            } else {
                $projects = $this->Project->ProjectTeam->ProjectFunctionEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'project_team_id'),
                    'conditions' => array(
                        'ProjectFunctionEmployeeRefer.employee_id' => $employee["Employee"]["id"])));
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_id', 'project_id'),
                    'conditions' => array('ProjectTeam.id' => $projects)
                ));
                $options['conditions'][] = array('Project.id' => $projects);
                if (!empty($cate)) {
                    $options['conditions'][] = array('Project.category' => $cate);
                }
            }
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'),
                        'conditions' => array('ProjectStatus.company_id' => $employee["Company"]["id"]),'order' => 'weight')));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
                        'conditions' => array('ProjectAmrProgram.company_id' => $employee["Company"]["id"]))));
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $employee["Company"]["id"],
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(array('ProfitCenter.company_id' => $employee["Company"]["id"]), null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list', array('conditions' => array('ProjectFunction.company_id' => $employee["Company"]["id"]))));
        } else {
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'),'order' => 'weight')));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'))));
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list'));
        }
        if ($viewGantt) {
            $options['contain']['ProjectPhasePlan'] = array(
                'fields' => array(
                    'id',
                    'project_planed_phase_id',
                    'phase_planed_start_date',
                    'phase_planed_end_date',
                    'phase_real_start_date',
                    'phase_real_end_date',
                ),
                'ProjectPhase' => array('name', 'color'));
            $options['contain']['ProjectMilestone'] = array(
                'order' => 'milestone_date ASC',
                'fields' => array(
                    'project_milestone',
                    'milestone_date',
                    'validated',
                    'project_id'
            ));
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }

        if ($checkFieldPurchase) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if (in_array($value, array('purchases_sold', 'purchases_to_bill', 'purchases_billed', 'purchases_paid'))) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                }
            }
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        if ($checkNextMileston) {
            if (!empty($options['fields'])) {
                foreach ($options['fields'] as $key => $value) {
                    if ($value == 'next_milestone_in_day' || $value == 'next_milestone_in_week') {
                        unset($options['fields'][$key]);
                    }
                }
            }
        }

		
        // Update by VN
        // Nhung field nay ko co trong bang project amr,
        // Chi su dung field name de get data comment tu log system

        $fiedsNotExists = array('project_amr_scope', 'project_amr_budget_comment', 'project_amr_resource', 'project_amr_schedule', 'project_amr_technical');
        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if (in_array($value, $fiedsNotExists)) {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }
        /**
         * Lay thong tin project
         */
        $this->Project->recursive = 0;
        $this->Project->Behaviors->attach('Containable');
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->loadModels('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        $listProjectOfPM = array();
        if ($role == 'pm') {
            $listProjectOfPM = $this->getProjectOfPM($employee);
        }
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employeeId,
						'OR' => array(
							'is_profit_center' => 0,
							'is_profit_center is NULL'
						),
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
				
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employee["Company"]["id"],
                        'project_manager_id' => $employeeId
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        if (!empty($listProjectIds)) {
            $options['conditions'][] = array('Project.id' => $listProjectIds);
        }
        if ($checkReadAccess) {
            foreach ($options['fields'] as $key => $value) {
                if ($options['fields'][$key] == 'read_access') {
                    unset($options['fields'][$key]);
                    $options['contain']['ProjectEmployeeManager'] = array(
                        'fields' => array(
                            'project_manager_id',
                    ));
                }
            }
        }
		// debug( $options); exit;
        if ($viewProjects == false) {
            $projects = array();
        } else {
            $projects = $this->Project->find('all', $options);
        }
		// debug( $projects); exit;
		foreach( $projects as $key => $project){
			$projects[$key]['ProjectFile'] = !empty( $project['ProjectFile'] ) ? Set::combine( $project['ProjectFile'], '{n}.id', '{n}' , '{n}.key') : array();
		}
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
		
        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
		$ajax_get_progress_line = false;
		if( isset($enableWidgets['internal_cost']) || isset($enableWidgets['synthesis'])){ 
			// $this->internal_progress_line($projectIds); //7 seconds
			$ajax_get_progress_line = true;
		}
		if( isset($enableWidgets['external_cost']) || isset($enableWidgets['synthesis']) ){ 
			// $this->external_progress_line($projectIds); 
			$ajax_get_progress_line = true;
		}
		$this->set('ajax_get_progress_line', $ajax_get_progress_line);
        $listProjectLinked = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.activity_id') : array();
        $this->_parseNew($listProjectLinked, $projects, $viewGantt); //5s
        // get Task euro 02/03/2017.
        $TaskEuros = array();
        if ($checkTaskEuro) {
            $TaskEuros = $this->getTaskEuro($projectIds);
        }
        // get nextMilestone
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        if ($checkNextMileston) {
            list($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek) = $this->getNextMilestone($projectIds);
        }
		$milestoneWidgetData = array();
		if( $get_milestone_wigdet){
			$this->loadModels('ProjectMilestone');
			$nextMilestone = $lateMilestone = array();
			$futureMilestones = $this->ProjectMilestone->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $projectIds,
					'milestone_date >' => date('Y-m-d', time()),
				),
				'fields' => array('milestone_date',  'project_milestone', 'project_id'),
				'order' => array('milestone_date' => 'ASC'),
			));
			$futureMilestones = !empty($futureMilestones) ? Set::combine($futureMilestones, '{n}.ProjectMilestone.milestone_date', '{n}.ProjectMilestone', '{n}.ProjectMilestone.project_id') : array();
			foreach( $futureMilestones as $p_id => $milestones){
				if( !empty($milestones)){
					$first = array_shift($milestones);
					if(strtotime($first['milestone_date']) >= time()){
						$first['date'] = date('d-m-Y', strtotime($first['milestone_date']));
						$milestoneWidgetData[$p_id]['next_milestone'] = $first;
					}
				}
			}
			$lateMilestones = $this->ProjectMilestone->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $projectIds,
					'milestone_date <=' => date('Y-m-d', time()),
					'initial_date !=' => 0,
					'initial_date is not NULL',
					'validated !=' => 1
				),
				'fields' => array('milestone_date',  'project_milestone', 'project_id'),
				'order' => array('milestone_date' => 'ASC'),
			));
			$lateMilestones = !empty($lateMilestones) ? Set::combine($lateMilestones, '{n}.ProjectMilestone.milestone_date', '{n}.ProjectMilestone', '{n}.ProjectMilestone.project_id') : array();
			foreach( $lateMilestones as $p_id => $milestones){
				if( !empty($milestones)){
					$first = array_shift($milestones);
					$first['date'] = date('d-m-Y', strtotime($first['milestone_date']));
					$milestoneWidgetData[$p_id]['milestone_late'] = $first;
				}
			}
			unset( $futureMilestones);
			unset( $lateMilestones);
		}
        $this->loadModels('ProjectBudgetPurchase', 'ProjectBudgetPurchaseInvoice');
        $Purchase = array();

        if ($checkFieldPurchase) {
            $this->ProjectBudgetPurchase->cacheQueries = true;
            $this->ProjectBudgetPurchase->recursive = -1;
            $this->ProjectBudgetPurchase->Behaviors->attach('Containable');

            $_budgetSales = $this->ProjectBudgetPurchase->find('all', array(
                'contain' => array('ProjectBudgetPurchaseInvoice'),
                'conditions' => array('project_id' => $projectIds)
                    )
            );
            foreach ($_budgetSales as $key => $value) {
                $dx = $value['ProjectBudgetPurchase'];
                if (empty($Purchase[$dx['project_id']]['purchases_sold'])) {
                    $Purchase[$dx['project_id']]['purchases_sold'] = 0;
                }
                $Purchase[$dx['project_id']]['purchases_sold'] += $dx['sold'];
                if (!empty($value['ProjectBudgetPurchaseInvoice'])) {
                    foreach ($value['ProjectBudgetPurchaseInvoice'] as $_key => $_value) {
                        if (empty($Purchase[$_value['project_id']]['purchases_to_bill'])) {
                            $Purchase[$_value['project_id']]['purchases_to_bill'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_to_bill'] += $_value['billed'];
                        if (empty($Purchase[$_value['project_id']]['purchases_paid'])) {
                            $Purchase[$_value['project_id']]['purchases_paid'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_paid'] += $_value['paid'];
                        if (!empty($_value['effective_date']) && $_value['effective_date'] != '0000-00-00') {
                            if (empty($Purchase[$_value['project_id']]['purchases_billed'])) {
                                $Purchase[$_value['project_id']]['purchases_billed'] = 0;
                            }
                            $Purchase[$_value['project_id']]['purchases_billed'] += $_value['billed'];
                        }
                    }
                }
            }
        }
        $this->set(compact('listColorNextMil', 'listNextMilestoneByDay', 'listNextMilestoneByWeek', 'Purchase', 'milestoneWidgetData'));
        /* get project_copy_id */
        $copy_ids = Set::classicExtract($projects, '{n}.Project.project_copy_id');
        $copy_ids = array_unique($copy_ids);
        $projectCopies = array();
        if (!empty($copy_ids)) {
            $projectCopies = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $copy_ids
                ),
                'fields' => array('id', 'project_name')
            ));
        }
        $freeze_ids = array_unique(Set::classicExtract($projects, '{n}.Project.freeze_by'));
        $freezers = array();
        if (!empty($freeze_ids)) {
            $this->Employee->virtualFields['fullname'] = 'CONCAT(first_name, \' \', last_name)';
            $freezers = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $freeze_ids
                ),
                'fields' => array('id', 'fullname')
            ));
        }
        $this->set(compact('projectCopies', 'freezers'));
        
		/**
         * Lay data budget
         */
		$staffingSystems = array();
		$this->loadModel('TmpStaffingSystem');
		if( $getStaffingSystems){
			$staffingSystems = $this->TmpStaffingSystem->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'model' => array('employee', 'profit_center'),
					'project_id' => $projectIds,
					'model_id !=' => 999999999,
					// 'NOT' => array(
						// 'project_id' => 0,
						// 'model_id' => 999999999
					// ),
					'company_id' => $employee["Company"]["id"]
				),
				'fields' => array('model', 'project_id', 'SUM(estimated) AS Total'),
				'group' => array('model', 'project_id'),
				'order' => array('project_id')
			));
			$staffingSystems = !empty($staffingSystems) ? Set::combine($staffingSystems, '{n}.TmpStaffingSystem.project_id', '{n}.0.Total', '{n}.TmpStaffingSystem.model') : array();
		}
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        if (!empty($pairs)) {
            $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
                'recursive' => -1,
                'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
                'fields' => array(
                    'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                    'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                    'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
                )
            ));
        }
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();
		
        $Internal = ClassRegistry::init('ProjectBudgetInternalDetail');
        $Internal->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $Internal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));
        $this->set('internalBudgets', $internalBudgets);
        /**
         * Lay ngay ket thuc theo ke hoach cua tat ca project
         */
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_keys($listProjectLinked),
                'NOT' => array(
                    'phase_planed_start_date' => '0000-00-00',
                    'phase_planed_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'project_id',
                'MAX(phase_planed_end_date) AS MaxEndDatePlan',
                'MAX(phase_real_end_date) AS MaxEndDateReal',
                'MIN(phase_planed_start_date) AS MinStartDatePlan',
                'MIN(phase_real_start_date) AS MinStartDateReal'
            ),
            'group' => array('project_id')
        ));
        $projectPhasePlans = !empty($projectPhasePlans) ? Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_id', '{n}.0') : array();
		// debug( $projectPhasePlans);
		foreach( $projectPhasePlans as $p_id=>$phase){
			$maxEndDatePlan = strtotime($phase['MaxEndDatePlan']);
			$maxEndDateReal = strtotime($phase['MaxEndDateReal']);
			$minStartDatePlan = strtotime($phase['MinStartDatePlan']);
			$minStartDateReal = strtotime($phase['MinStartDateReal']);
			$projectPhasePlans[$p_id]['max_end_date_plan'] = !empty( $maxEndDatePlan) ? date('d-m-Y', $maxEndDatePlan) : '';
			$projectPhasePlans[$p_id]['max_end_date_real'] = !empty( $maxEndDateReal) ? date('d-m-Y', $maxEndDateReal) : '';
			$projectPhasePlans[$p_id]['min_start_date_plan'] = !empty( $minStartDatePlan) ? date('d-m-Y', $minStartDatePlan) : '';
			$projectPhasePlans[$p_id]['min_start_date_real'] = !empty( $minStartDateReal) ? date('d-m-Y', $minStartDateReal) : '';
			$projectPhasePlans[$p_id]['diff'] = (!empty( $maxEndDatePlan) && !empty( $maxEndDateReal)) ? floor(($maxEndDateReal - $maxEndDatePlan)/(60*60*24)) : '';
		}
        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3),
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        $this->set(compact('consumedAndWorkloadForActivities', 'currentYears', 'provisionals'));
        /**
         * Lay du lieu o table finacement plus
         */
		$financePlusWidgetData = array();
		$finance_progress_column = array();
		if(  $get_finance_plus_widget) {
			$this->loadModel('ProjectFinancePlusDetail');
			$financePlusData = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array(
					'project_id' => $projectIds,
				),
                'fields' => array(
                    'project_id', 
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model) AS keyValue',
                    'value',
					'year'
                ),
            ));
		
			foreach($financePlusData as $key => $financePlus){
				$dx = $financePlus['ProjectFinancePlusDetail'];
				$dy = $financePlus[0];
				if(empty($finance_progress_column[$dx['project_id']])) $finance_progress_column[$dx['project_id']] = array();
				if(empty($finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']])) $finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']] = 0;
				$finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']] += (!empty($dx['value']) ? $dx['value'] : 0);	
				
				if(empty($financePlusWidgetData[$dx['project_id']])) $financePlusWidgetData[$dx['project_id']] = array();
				if(empty($financePlusWidgetData[$dx['project_id']][$dy['keyValue']])) $financePlusWidgetData[$dx['project_id']][$dy['keyValue']] = 0;
				 $financePlusWidgetData[$dx['project_id']][$dy['keyValue']] += (!empty($dx['value']) ? $dx['value'] : 0);	
			}
			
			$finan_type = array('inv', 'fon','finaninv', 'finanfon');
			foreach($financePlusWidgetData as $p_id => $finance){
				$percent = 0;
				foreach($finan_type as $key => $type){
					$avancement = !empty($finance[$type . '_avancement']) ? $finance[$type . '_avancement'] : 0;
					$budget = !empty($finance[$type . '_budget']) ? $finance[$type . '_budget'] : 0;
					if( $budget != 0){
						$percent = round(100 * $avancement / $budget);
					}else{
						$percent = $avancement > 0 ? 100 : 0;
					}
					$financePlusWidgetData[$p_id][$type . '_progress'] = $percent;
				}
			}
			
		}
		
		// exit;
        $finances = $finEuros = $finPercents = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
            foreach ($fieldFinancements['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[1]) && $_field[1] == 'percent') {
                    $finPercents[] = 'ProjectFinancePlus.' . $field;
                } else {
                    $finEuros[] = 'ProjectFinancePlus.' . $field;
                }
            }
        }
        $financesTwoPlus = $finTwoPlusEuros = $finTwoPlusPercent = array();
        $startFinanceTwoPlus = $endFinanceTwoPlus = date('Y', time());
        $this->loadModels('ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        if (!empty($fieldFinancementTwoPlus['fields'])) {
            $_financesTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array('project_id', 'year', 'budget_initial', 'budget_revised', 'last_estimated', 'engaged', 'bill', 'disbursed'),
            ));
            foreach ($_financesTwoPlus as $key => $value) {
                $dx = $value['ProjectFinanceTwoPlusDetail'];
                // tinh tung nam.
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] += $dx['disbursed'];
                // tinh total
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial'] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised'] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated'])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated'] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged'])) {
                    $financesTwoPlus[$dx['project_id']]['engaged'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged'] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill'])) {
                    $financesTwoPlus[$dx['project_id']]['bill'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill'] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed'])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed'] += $dx['disbursed'];
            }
            $financeTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(year) AS startDate',
                    'MAX(year) AS endDate'
                )
            ));
            $financeTwoDates = $this->ProjectFinanceTwoPlusDate->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(start) AS fonStart',
                    'MAX(end) AS fonEnd'
                )
            ));
            $startFinanceTwoPlus = !empty($financeTwoPlus[0][0]['startDate']) ? $financeTwoPlus[0][0]['startDate'] : date('Y', time());
            $endFinanceTwoPlus = !empty($financeTwoPlus[0][0]['endDate']) ? $financeTwoPlus[0][0]['endDate'] : date('Y', time());
            if (!empty($financeTwoDates) && !empty($financeTwoDates[0][0])) {
                $financeTwoDates = $financeTwoDates[0][0];
                $startFinanceTwoPlus = (date('Y', $financeTwoDates['fonStart']) < $startFinanceTwoPlus) ? date('Y', $financeTwoDates['fonStart']) : $startFinanceTwoPlus;
                $endFinanceTwoPlus = (date('Y', $financeTwoDates['fonEnd']) > $endFinanceTwoPlus) ? date('Y', $financeTwoDates['fonEnd']) : $endFinanceTwoPlus;
            }
            foreach ($fieldFinancementTwoPlus['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[0]) && $_field[0] == 'percent') {
                    $finTwoPlusPercent[] = 'ProjectFinanceTwoPlus.' . $field;
                } else {
                    $finTwoPlusEuros[] = 'ProjectFinanceTwoPlus.' . $field;
                }
            }
        }
		/*
		* Ticket 703 Add by QuanNV 06/08/2020
		* Count sum tasks trong all project display.
		*/
		$this->loadModels('ProjectTask','ProjectStatus');
		$this->ProjectTask->virtualFields['ed_unix'] = 'UNIX_TIMESTAMP(task_end_date)';
		$list_project_tasks = $this->ProjectTask->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds
			),
			'fields' => array('id', 'task_title', 'project_id', 'task_status_id', 'task_end_date', 'ed_unix')
		));
		$project_task_id =  !empty($list_project_tasks) ? Set::classicExtract($list_project_tasks, '{n}.ProjectTask.id') : array();
		$task_refer_project = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
		$project_used = array();
		if($cate != 6){
			$task_used_timesheet = $this->getProjectUsed($project_task_id);
			if(!empty($task_used_timesheet)){
				foreach($task_used_timesheet as $index => $task_id){
					if(!empty($task_refer_project[$task_id])){
						$project_used[$task_refer_project[$task_id]] = $task_refer_project[$task_id];
					}
				}
			}
		}
		$typeTaskStatus = $this->ProjectStatus->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'status')
		));
		// debug( $list_project_tasks); 
		$list_project_tasks = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask', '{n}.ProjectTask.project_id') : array();
		$summary_tasks = array();
		// debug( date('Y-m-d', time())); exit;
		$current_date = strtotime( date('Y-m-d', time()));
		foreach($list_project_tasks as $p_id => $list_tasks){
			$summary_tasks[$p_id]['count_task'] = count( $list_tasks );
			$summary_tasks[$p_id]['count_task_intime'] = 0;
			$summary_tasks[$p_id]['count_by_stt'] = array();
			$summary_tasks[$p_id]['count_task_late'] = 0;
			foreach( $list_tasks as $t_id => $task){
				$stt = $task['task_status_id'];
				if( empty( $stt)) continue;
				if( empty( $summary_tasks[$p_id]['count_by_stt'][$stt])) $summary_tasks[$p_id]['count_by_stt'][$stt] = 0;
				$summary_tasks[$p_id]['count_by_stt'][$stt] +=1;
				if( !empty( $typeTaskStatus[$stt]) &&  ($typeTaskStatus[$stt] == 'IP') && $task['ed_unix'] < $current_date){
					$summary_tasks[$p_id]['count_task_late'] += 1;
				} else{
					$summary_tasks[$p_id]['count_task_intime'] += 1;
				}
			}
		}
		
		// debug( time() - $a); 
		// debug( $summary_tasks); 
		// exit;
		/*
		* End add code: count sum task.
		*/
        $this->set(compact('finances', 'finEuros', 'finPercents', 'financesTwoPlus', 'startFinanceTwoPlus', 'endFinanceTwoPlus', 'finTwoPlusEuros', 'finTwoPlusPercent', 'enableWidgets', 'summary_tasks', 'typeTaskStatus', 'project_used'));
        /*
         * Manual consumed
         */
        $this->loadModel('ProjectTask');
        $this->ProjectTask->virtualFields['manual'] = 'SUM(manual_consumed)';
        $tasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array(
                'project_id',
                'manual'
            ),
            'group' => array('project_id')
        ));
        $this->set('manualData', $tasks);
        /**
         * Dung nhieu view trong 1 controller. Neu cate = 2 chuyen den view: opportunity.
         */
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $appstatus = $this->Session->read("App.status");
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        //priorities
        $priorities = $this->Project->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'priority')
        ));
        $this->set('priorities', $priorities);
        //editable list, available for pm only
        $this->set('isPM', $this->employee_info['CompanyEmployeeReference']['role_id'] == 3);
        $this->set('isAdmin', $this->employee_info['CompanyEmployeeReference']['role_id'] == 2);
        $this->set('editable', $this->canModifyProject($projectIds));
        $this->set('favorites', $this->getFavoritebyEmployee($this->employee_info['Employee']['id'], $projectIds));
        //log system for done & todo
        if (!empty($projectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $projectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr' or model = 'Scope' or model = 'Schedule' or model = 'Budget' or model = 'Resources' or model = 'Technical') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        // get value for dialog vision task
        $projectStatus = $this->Project->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
			'order' => 'weight',
            'fields' => array('id', 'name')
        ));
        $projectProgram = $this->Project->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->Project->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_amr_program_id' => array_keys($projectProgram)
            ),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_name')
        ));
        
		$this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'name')
        ));
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname')
        ));
        $listProjectCode = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_code_1', 'project_code_2')
        ));
        $_listProjectCode = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_1') : array();
        $_listProjectCode1 = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_2') : array();
        if (!empty($_listProjectCode)) {
            $_listProjectCode = array_unique($_listProjectCode);
            $_listProjectCode = array_combine($_listProjectCode, $_listProjectCode);
            unset($_listProjectCode['']);
        }
        if (!empty($_listProjectCode1)) {
            $_listProjectCode1 = array_unique($_listProjectCode1);
            $_listProjectCode1 = array_combine($_listProjectCode1, $_listProjectCode1);
            unset($_listProjectCode1['']);
        }
        $_milestone = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_milestone', 'project_milestone')
        ));
        // get company config
        $this->loadModel('CompanyConfig');
        $showTaskVision = $this->CompanyConfig->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'display_visions'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showTaskVision = !empty($showTaskVision) ? $showTaskVision['CompanyConfig']['cf_value'] : 0;
		
		// $showDashboard = $this->CompanyConfig->find('list', array(
			// 'recursive' => -1,
			// 'conditions' => array(
				// 'company' => $company_id,
				// 'cf_name' => 'display_project_dashboard',
				// 'cf_value' => 1
			// ),
			// 'fields' => array('cf_value')
		// ));
        $this->set(compact('projectStatus', 'projectProgram', 'projectSubProgram', '_listProjectCode', '_listProjectCode1', 'showTaskVision', 'listEmployee', '_milestone', 'budgetCustomers'));
        //end ----
        $this->loadModel('ProjectType');
        $listprojectTypes = $this->Project->ProjectType->find('list', array(
            'fields' => array('ProjectType.id', 'ProjectType.project_type'),
            'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1)
        ));
        $listprojectSubTypes = $this->Project->ProjectSubType->find('list', array(
            'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
            'conditions' => array('ProjectSubType.project_type_id' => array_keys($listprojectTypes), 'ProjectSubType.display' => 1)
        ));
		 $listprojectSubSubTypes = $this->Project->ProjectSubType->find('list', array(
            'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
            'conditions' => array('ProjectSubType.parent_id' => array_keys($listprojectSubTypes), 'ProjectSubType.display' => 1)
        ));
		
        //Lay Budget settings
        $this->loadModel('BudgetSetting');
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $displayExpectation = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
                'display' => 1
            ),
        ));
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $_f = array('id', 'name_eng');
        } else {
            $_f = array('id', 'name_fre');
        }
        $screenExpec = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
            ),
            'fields' => $_f
        ));
        $this->loadModel('HistoryFilter');
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'projects/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->loadModel('ProjectEmployeeManager');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            ),
            'fields' => array('project_id', 'project_id')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $_listAvata = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model_id' => $projectIds
            )
        ));
        $listAvata = $listEmp = array();
        foreach ($_listAvata as $value) {
            $dx = $value['LogSystem'];
            $listAvata[$dx['model'] . '-' . $dx['model_id']] = $dx['employee_id'];
            $listEmp[$dx['model'] . '-' . $dx['model_id']] = $this->getNameEmployee($dx['update_by_employee']);
        }

        // data filter box

        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
        $listProjectManager = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.id', 'Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listProjectManager = !empty($listProjectManager) ? Set::combine($listProjectManager, '{n}.Project.id', '{n}.Employee') : array();


        $listPMFields = $this->Employee->find('all', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Employee.id = Project.project_manager_id',
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Employee.id', 'Employee.first_name', 'Employee.last_name')
        ));
        $listPMFields = !empty($listPMFields) ? Set::combine($listPMFields, '{n}.Employee.id', '{n}.Employee') : array();

        /* Edit by Huynh 12-11-2018
         * Add PM From project_employee_managers
         */
        $list_all_pm = array();
        $list_pm_avatar = array();
        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
        if (!empty($projectIds)) {
            $list_all_pm = $this->ProjectEmployeeManager->find('all', array(
                'fields' => array('project_id', 'project_manager_id'),
                'conditions' => array(
                    'project_id' => $projectIds,
                    'type' => 'PM',
                    'is_profit_center' => 0
                ),
                'order' => 'project_id ASC'
            ));
            $list_pm_avatar = !empty($projects) ? Set::classicExtract($list_all_pm, '{n}.ProjectEmployeeManager.project_manager_id') : array();

            $list_all_pm = !empty($list_all_pm) ? Set::combine($list_all_pm, '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager.project_manager_id', '{n}.ProjectEmployeeManager.project_id') : '';
        }
        $list_pm_avatar = array_unique(array_merge($list_pm_avatar, Set::classicExtract($projects, '{n}.Project.project_manager_id')));
        for ($_index = 0; $_index < count($projects); $_index++) {
            $this_id = $projects[$_index]['Project']['id'];
            $old_pm = !empty($projects[$_index]['Project']['project_manager_id']) ? $projects[$_index]['Project']['project_manager_id'] : '';
            $new_pm = $old_pm ? array($old_pm => $old_pm) : array();
            if (isset($list_all_pm[$this_id])) {
                $new_pm = array_unique(array_merge($new_pm, $list_all_pm[$this_id]));
            }
            $projects[$_index]['Project']['project_manager_id'] = array_values($new_pm);
        }
        /* END Edit by Viet 17-11-2018 
          Get all project employee manager
         */
        $project_read_access_manager_list = array();
        $_list_project = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project') : array();
        $_uat_manager_list = $_technical_manager_list = $_chief_business_list = $_functional_leader_list = $_read_access_manager_list = $list_all_resource = array();
		// QuanNV update them dieu kien kiem tra is_profit_center = NULL. 20/06/2019
		// projectIds danh sach id cua cac project trong company.
        
        if (!empty($projectIds)) {
            $employee_manager = $this->ProjectEmployeeManager->find('all', array(
                'fields' => array('id', 'project_id', 'project_manager_id', 'type','is_profit_center'),
                'conditions' => array(
                    'project_id' => $projectIds,
				// 	'OR' => array(
				// 		'is_profit_center' => 0,
				// 		'is_profit_center is NULL',
				// 	),
                 ),
                'order' => 'project_id ASC'
            ));
            
            foreach ($employee_manager as $key => $value) {
                
                $type = $value['ProjectEmployeeManager']['type'];
                $project_id = $value['ProjectEmployeeManager']['project_id'];
                $project_manager_id = $value['ProjectEmployeeManager']['project_manager_id'];
                $is_profit_center = $value['ProjectEmployeeManager']['is_profit_center'];
                $list_all_resource[] = $project_manager_id;
                if ($type == 'TM') {
                    $_technical_manager_list[$project_id][] = $project_manager_id;
                } elseif ($type == 'FL') {
                    $_functional_leader_list[$project_id][] = $project_manager_id;
                } elseif ($type == 'UM') {
                    $_uat_manager_list[$project_id][] = $project_manager_id;
                } elseif ($type == 'CB') {
                    $_chief_business_list[$project_id][] = $project_manager_id;
                } elseif ($type == 'RA') {
                    $_read_access_manager_list[$project_id][] = $project_manager_id;
                    $project_read_access_manager_list[$project_id][] = array(
                        'id' => $project_manager_id,
                        'is_profit_center' => $is_profit_center
                    );
                }
            }
            // debug($project_read_access_manager_list);
            // exit;
            foreach ($_list_project as $project_id => $project) {

                if (isset($project['uat_manager_id']) && $project['uat_manager_id']) {
                    $_uat_manager_list[$project_id][] = $project['uat_manager_id'];
                    $list_all_resource[] = $project['uat_manager_id'];
                }
                if (isset($project['functional_leader_id']) && $project['functional_leader_id']) {
                    $_functional_leader_list[$project_id][] = $project['functional_leader_id'];
                    $list_all_resource[] = $project['functional_leader_id'];
                }
                if (isset($project['technical_manager_id']) && $project['technical_manager_id']) {
                    $_technical_manager_list[$project_id][] = $project['technical_manager_id'];
                    $list_all_resource[] = $project['technical_manager_id'];
                }
                if (isset($project['chief_business_id']) && $project['chief_business_id']) {
                    $_chief_business_list[$project_id][] = $project['chief_business_id'];
                    $list_all_resource[] = $project['chief_business_id'];
                }
            }
        }
        $sql_request = array();
        if ($this->employee_info['Role']['name'] == 'pm') {
            $this->loadModel('SqlManagerEmployee');
            $sql_request = $this->SqlManagerEmployee->find('list', array(
				'recurisve' => -1,
                'conditions' => array(
                    'employee_id' => $this->employee_info['Employee']['id'],
					'company_id' => $this->employee_info['Company']['id'],
                ),
				'fields' => array('id', 'sql_manager_id')
            ));
        }
		$this->loadModels('CustomerLogo');
		$listLogo = $this->CustomerLogo->getListCustomerLogo();
		
		$employee_logo = $this->Employee->find('list', array(
			'recurisve' => -1,
			'conditions' => array(
				'id' => $this->employee_info['Employee']['id'],
			),
			'fields' => array('logo_id', 'logo_id')
		));
        $list_pm_avatar = array_unique(array_merge($list_pm_avatar, $list_all_resource));
        $list_avatar = $this->requestAction('employees/get_list_avatar/', array('pass' => array($list_pm_avatar)));
        $this->set('list_avatar', $list_avatar);
        $this->set(compact('_technical_manager_list', '_functional_leader_list', '_uat_manager_list', '_chief_business_list', '_read_access_manager_list', 'sql_request', 'listLogo', 'employee_logo',"project_read_access_manager_list"));

        /* END Edit by Huynh 12-11-2018
         * Add PM From project_employee_managers
         */
		
		/*
		* Ticket 526 Edit by QuanNV 22/01/2020
		*/
		$this->loadModels('ProjectBudgetSyns', 'ProjectTarget');
		$allProjectBudgetSyns = $this->ProjectBudgetSyns->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
			),
		));
		$allProjectBudgetSyns = !empty($allProjectBudgetSyns) ? Set::combine($allProjectBudgetSyns, '{n}.ProjectBudgetSyns.project_id', '{n}.ProjectBudgetSyns') : array();
		$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? intval($this->companyConfigs['manual_consumed']) : 0;
		$dataActivityTaskManual = array();
		if( $useManualConsumed){
			$dataActivityTaskManual = $this->Project->dataFromProjectTaskManualConsumeds($projectIds);
			foreach(  $allProjectBudgetSyns as $_pid => $_budgetSyn){
				$tjm = !empty($_budgetSyn['internal_costs_average']) ? $_budgetSyn['internal_costs_average'] : 0;
				$_forecastEuro = $_engagedErro = $_remainEuro = 0;
				if( !empty($dataActivityTaskManual[$_pid])){
					$_engagedErro = $dataActivityTaskManual[$_pid]['consumed'] * $tjm;
					$_remainEuro = ($dataActivityTaskManual[$_pid]['remain'])*$tjm;
					$_forecastEuro = $_engagedErro + $_remainEuro;
				}
				$_budgetSyn['internal_costs_forecast'] = $_forecastEuro;
				$_budgetSyn['internal_costs_engaged'] = $_engagedErro;
				$_budgetSyn['internal_costs_remain'] = $_remainEuro;
				$allProjectBudgetSyns[$_pid] = $_budgetSyn;	
			}
		}
		$projectProgress = array();
		// $progress_method = isset($this->companyConfigs['project_progress_method'] ) ? $this->companyConfigs['project_progress_method'] : '';
		if( $get_progress )	$projectProgress = $this->getProjectProgress($projectIds);
		
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id']), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set(compact('projectIds', 'logs', 'currency_name', 'appstatus', 'staffingSystems', 'projectPhasePlans', 'budgetSyns', 'ACLController', 'ACLAction', 'logGroups', 'fieldset', 'projects', 'viewId', 'noProjectManager', 'employee', 'checkStatus', 'personDefault', 'financeFields', 'LANG', 'viewGantt', 'confirmGantt', 'cate', 'listPc', 'listprojectTypes', 'listprojectSubTypes', 'listprojectSubSubTypes', 'budget_settings', 'checkEngedMd', 'TaskEuros', 'projectTaskFields', 'displayExpectation'));
        $this->set(compact('screenExpec', 'savePosition', 'listEmployeeManager', 'employee_id', 'listAvata', 'listEmp', 'listProgramFields', 'listProjectManager', 'listPMFields', 'listProjectOfPM','allProjectBudgetSyns','dataActivityTaskManual','projectProgress', 'filter_alert', 'financePlusWidgetData', 'finance_progress_column'));
        if ($cate == 2) {
            $this->Session->write("App.status_oppor", 2);
        } else {
            $this->Session->delete('App.status_oppor');
        }
		// debug( $projectPhasePlans); exit;
    }
	public function getProjectUsed($project_task_id) {
		$this->loadModels('ActivityRequest', 'ActivityTask');
		$p_task_used = array();
		$task_refer_activity = $this->ActivityTask->find('list', array(
			'recurisve' => -1,
			'conditions' => array(
				'project_task_id' => $project_task_id,
			),
			'fields' => array('project_task_id', 'id')
		));
		if(!empty($task_refer_activity)){
			$a_task_used = $this->ActivityRequest->find('list', array(
				'recurisve' => -1,
				'conditions' => array(
					'task_id' => $task_refer_activity,
				),
				'fields' => array('task_id', 'task_id'),
				'group' => array('task_id')
			));
			$activity_ids = array_flip($task_refer_activity);
			foreach($a_task_used as $id => $task_id){
				if(!empty($activity_ids[$task_id])){
					$p_task_used[] = $activity_ids[$task_id];
				}
			}
		}
		return $p_task_used;
	}
	/* Function getProjectOfPM
	Return list project of curent employee and PC
	* @param: $employee array(
		CompanyEmployeeReference => array(see_all_projects => '')
		Employee => array(id => '', profit_center_id => '')
		Company => array(id => '')
		Role => array('name' => '')
	)
	* @param: $canEdit, if $canEdit only get project that current user can edit
	Return list Project_ids 
	*/
    
    private function getProjectOfPM($employee,  $canEdit = true, $category = '') {
		if( empty($employee)) $employee = $this->employee_info;
		if( $category == 5) $category = array( 1, 2);
		$employeeId = $employee['Employee']['id'];
		$profitCenterId = $employee['Employee']['profit_center_id'];
		$role = $employee['Role']['name'];
		$conditions = array(
			'company_id' => $employee['Company']['id'],
		);
		if( !empty($category)){
			$conditions['category'] = $category;
		}
		if( $role == 'admin'){
			$listProjectIds =  $this->Project->find('list', array(
				'recurisve' => -1,
				'conditions' => $conditions,
				'fields' => array('id')
			));
			return array_values($listProjectIds);
		}
		if( $role == 'pm'){
			$seeAllProjects = !empty($this->companyConfigs['see_all_projects']) ? true : (!empty($employee['CompanyEmployeeReference']['see_all_projects']) ? true : false ) ;
			if( $seeAllProjects && !$canEdit){
				$listProjectIds =  $this->Project->find('list', array(
					'recurisve' => -1,
					'conditions' => $conditions,
					'fields' => array('id')
				));
				return array_values($listProjectIds);
			}
			$this->loadModels('ProjectEmployeeManager');
			$conditions = array(
				'or' => array(
					array(
						'ProjectEmployeeManager.project_manager_id' => $employeeId,
						'ProjectEmployeeManager.is_profit_center !=' => 1,
					), 
					array(
						'ProjectEmployeeManager.project_manager_id' => $profitCenterId,
						'ProjectEmployeeManager.is_profit_center' => 1,
					)
				),
			);
			if( $canEdit ) $conditions['ProjectEmployeeManager.type !='] = 'RA';
			else $conditions[] = 'ProjectEmployeeManager.type is not NULL';
			$options = array(
				'recursive' => -1,
				'fields' => array('ProjectEmployeeManager.project_id')
			);
			if( !empty($category)){
				$conditions['Project.category'] = $category;
				$options['joins'] = array( 
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array(
							'Project.id = ProjectEmployeeManager.project_id',
						)
					)
				);
			}
			$options['conditions'] = $conditions;
			$listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list',$options);
			$listProjectOfEmployManager = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $employee['Company']['id'],
					'project_manager_id' => $employeeId
				),
				'fields' => array('id', 'id')
			));
			$listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectOfEmployManager);
			// return $listProjectIds;
			return array_values(array_unique($listProjectIds));
		}
		if ($role == 'conslt' && !$canEdit) {
			$listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_manager_id' => $profitCenterId,
					'is_profit_center' => 1
				),
				'fields' => array('project_id', 'project_id')
			));
            return array_values($listProjectIdOfPcs);
		}
		return array();
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index_update($viewId = null) {
        $mobileEnabled = $this->isTouch();
        $this->loadModels('Menu', 'LogSystem', 'ProjectFinance', 'ProjectPhasePlan', 'Employee', 'ProfitCenter');
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        /**
         * Doc lai 2 tham so create va delete cua employee dang login
         */
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $employee['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if (!empty($employInfors['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName'));
        $employee['Employee']['create_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['create_a_project']) ? $employInfors['Employee']['create_a_project'] : 0;
        $employee['Employee']['delete_a_project'] = !empty($employInfors) && !empty($employInfors['Employee']['delete_a_project']) ? $employInfors['Employee']['delete_a_project'] : 0;
        $employee['Employee']['update_your_form'] = !empty($employInfors) && !empty($employInfors['Employee']['update_your_form']) ? $employInfors['Employee']['update_your_form'] : 0;
        $listPc = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        /**
         * Multiples Sort Building
         */
        if (isset($_POST['multiSort'])) {
            $fieldSort = array();
            if ($_POST['actSort'] == 'add') {
                if ($_POST['flag'] != 0) {
                    $fieldSort = $this->Session->read('sFieldSort');
                } else {
                    $fieldSort = array();
                    $this->Session->write('sFieldSort', '');
                }
                $fieldSort[] = array('columnId' => $_POST['value'], 'sortAsc' => 1);
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
            if ($_POST['actSort'] == 'remove') {
                $fieldSort = $this->Session->read('sFieldSort');
                $fieldSort1 = array();
                foreach ($fieldSort as $array) {
                    if ($array['columnId'] != $_POST['value'])
                        $fieldSort1[] = array('columnId' => $array['columnId'], 'sortAsc' => $array['sortAsc']);
                }
                $this->Session->write('sFieldSort', $fieldSort1);
                echo json_encode($fieldSort1);
                exit();
            }
            if ($_POST['actSort'] == 'update') {
                $fieldSort = $this->Session->read('sFieldSort');
                $count = count($fieldSort);
                for ($i = 0; $i < $count; $i++) {
                    if ($fieldSort[$i]['columnId'] == $_POST['value'])
                        $fieldSort[$i]['sortAsc'] = round($_POST['type']);
                }
                $this->Session->write('sFieldSort', $fieldSort);
                echo json_encode($fieldSort);
                exit();
            }
        }
        /**
         * Su dung helper GanttVs
         */
        $this->helpers = array_merge($this->helpers, array('GanttVs'));
        /**
         * Lay screen mac dinh cua project
         */
        $checkDisplayProfileScreen = 1;
        $ACLController = '';
        $ACLAction = '';
        if (!empty($this->employee_info['Employee']['profile_account'])) {
            $this->loadModel('ProfileProjectManagerDetail');
            $screenDefaults = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $listScreen = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('model_id' => $this->employee_info['Employee']['profile_account'], 'company_id' => $company_id, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            if (empty($listScreen)) {
                $checkDisplayProfileScreen = 2;
            } else {
                $ACLController = $listScreen['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $listScreen['ProfileProjectManagerDetail']['functions'];
            }
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['ProfileProjectManagerDetail']['controllers'];
                $ACLAction = $screenDefaults['ProfileProjectManagerDetail']['functions'];
            }
        } else {
            $screenDefaults = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1, 'display' => 1),
                'fields' => array('controllers', 'functions')
            ));
            $ACLController = 'projects';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
        }
        $this->set(compact('checkDisplayProfileScreen'));
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('id' => 'ASC')
        ));
        $logGroups = Set::combine($logSystems, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        /**
         * Extract url
         */
        extract(array_merge(array('cate' => null), $this->params['url']));
        /**
         * Check category
         */
        if (!empty($cate)) {
            if ($cate != 2) {
                $this->Session->write("App.status", $cate);
                if ($mobileEnabled)
                    $this->Session->write("App.mobileStatus", $cate);
            } else {
                $this->Session->write("App.status", $cate);
            }
        } else {
            $cate = $this->Session->read("App.status");
            if ($mobileEnabled) {
                $mobileCategory = $this->Session->read("App.mobileStatus");
                if ($mobileCategory) {
                    if ($mobileCategory != 2) {
                        $this->Session->write("App.mobileStatus", $mobileCategory);
                    }
                    $this->Session->write("App.mobileStatus", $mobileCategory);
                } else {
                    $cate = 1;
                    $this->Session->write("App.mobileStatus", 1);
                }
            } else {
                if ($cate) {
                    if ($cate != 2) {
                        $this->Session->write("App.status", $cate);
                    }
                    $this->Session->write("App.status", $cate);
                } else {
                    $cate = 1;
                    $this->Session->write("App.status", 1);
                }
            }
        }
        $checkStatus = -2; // -2: khong co gi ca. -1: Predefined. 0: Default
        if ($cate == 2) {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileOppView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatusOppor');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        } else {
            if (empty($viewId)) {
                if ($mobileEnabled) {
                    $viewId = $this->Session->read('App.mobileView');
                } else {
                    $viewId = $this->Session->read('App.ProjectStatus');
                }
            } else if ($viewId == -1 || $viewId == -2) {
                $viewId = null;
            }
        }
        /**
         * Lay column display of project
         */
        $fieldset = array();
        $viewGantt = false;
        $confirmGantt = array();
        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                'conditions' => array('UserView.id' => $viewId)));
            $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
            $confirmGantt = array(
                'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                'real' => !empty($fieldset['UserView']['real']) ? true : false,
                'type' => 3, /** mac dinh la year */
                'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
            );
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            }
            $checkStatus = $viewId;
            if ($cate == 2) {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileOppView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatusOppor', $checkStatus);
                }
            } else {
                if ($mobileEnabled) {
                    $this->Session->write('App.mobileView', $checkStatus);
                } else {
                    $this->Session->write('App.ProjectStatus', $checkStatus);
                }
            }
        } else {
            if ($mobileEnabled) {
                $defaultView = $this->Project->UserView->UserStatusView->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'UserStatusView.employee_id' => $employee['Employee']['id'],
                        'View.model' => 'project',
                        'UserStatusView.mobile' => 1
                    ),
                    'joins' => array(
                        array(
                            'table' => 'user_views',
                            'alias' => 'View',
                            'type' => 'inner',
                            'conditions' => array('View.id = UserStatusView.user_view_id')
                        )
                    ),
                    'fields' => array('user_view_id')
                ));
                if ($defaultView) {
                    $defaultView['UserDefaultView']['user_view_id'] = $defaultView['UserStatusView']['user_view_id'];
                }
            } else {
                $defaultView = $this->Project->UserDefaultView->find('first', array(
                    'conditions' => array(
                        'employee_id' => $employee['Employee']['id'],
                        'model' => 'project'
                    ),
                    'recursive' => -1,
                    'fields' => array('user_view_id')
                ));
            }
            if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                $checkStatus = -2;
                $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                $fieldset = $this->Project->UserView->find('first', array(
                    'fields' => array('UserView.content', 'UserView.gantt_view', 'UserView.initial', 'UserView.real', 'UserView.type', 'UserView.stones', 'UserView.display_all_name_of_milestones', 'UserView.from', 'UserView.to'),
                    'conditions' => array('UserView.id' => $defaultView)));
                $viewGantt = !empty($fieldset['UserView']['gantt_view']) ? true : false;
                $confirmGantt = array(
                    'initial' => !empty($fieldset['UserView']['initial']) ? true : false,
                    'real' => !empty($fieldset['UserView']['real']) ? true : false,
                    'type' => 3, /** mac dinh la year */
                    'from' => !empty($fieldset['UserView']['from']) ? $fieldset['UserView']['from'] : 0,
                    'to' => !empty($fieldset['UserView']['to']) ? $fieldset['UserView']['to'] : 0,
                    'stones' => !empty($fieldset['UserView']['stones']) ? true : false,
                    'display_all_name_of_milestones' => !empty($fieldset['UserView']['display_all_name_of_milestones']) ? $fieldset['UserView']['display_all_name_of_milestones'] : false
                );
                if (!empty($fieldset)) {
                    $fieldset = unserialize($fieldset['UserView']['content']);
                }
                $checkStatus = $viewId;
                $this->Session->write("App.PersonalizedDefault", true);
            } else {
                $checkStatus = -1;
                $fieldset = array(
                    'Project.project_name',
                    // 'Project.project_phase_id',
                    'Project.project_manager_id',
                        // 'Project.chief_business_id',
                        // 'Project.technical_manager_id',
                        // 'Project.project_priority_id',
                        // 'Project.project_status_id',
                        // 'Project.start_date',
                        // 'ProjectAmr.weather'
                );
                if ($mobileEnabled) {
                    $fieldset = array(
                        'Project.project_name',
                        'Project.project_manager_id',
                            // 'Project.project_phase_id',
                            // 'Project.start_date',
                            // 'ProjectAmr.weather'
                    );
                }
            }
        }
        $checkFieldCurrentPhase = false;
        if (in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
        $checkFieldPurchase = false;
        foreach (array('ProjectBudgetSyn.purchases_sold', 'ProjectBudgetSyn.purchases_to_bill', 'ProjectBudgetSyn.purchases_billed', 'ProjectBudgetSyn.purchases_paid') as $v) {
            if (in_array($v, $fieldset)) {
                $checkFieldPurchase = true;
            }
        }
        $checkFieldMuti = false;
        for ($i = 1; $i < 10; $i++) {
            if (in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        $checkNextMileston = false;
        if (in_array('Project.next_milestone_in_day', $fieldset) || in_array('Project.next_milestone_in_week', $fieldset)) {
            $checkNextMileston = true;
        }
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $noProjectManager = false;
        if (!in_array('Project.project_manager_id', $fieldset)) {
            $fieldset[] = 'Project.project_manager_id';
            $noProjectManager = true;
        }
        list($fieldset, $options) = $this->Project->parseViewField($fieldset);
        if (!empty($options['contain']['ProjectAmr']['fields'])) {
            foreach ($options['contain']['ProjectAmr']['fields'] as $key => $value) {
                if ($value == 'comment') {
                    unset($options['contain']['ProjectAmr']['fields'][$key]);
                }
            }
        }
        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        $fieldFinancementTwoPlus = !empty($options['contain']['ProjectFinanceTwoPlus']) ? $options['contain']['ProjectFinanceTwoPlus'] : array();
        unset($options['contain']['ProjectFinanceTwoPlus']);
        $options['fields'][] = 'last_modified';
        //regardless which fields are selected, these are mandatory
        $options['fields'][] = 'project_manager_id';
        $options['fields'][] = 'technical_manager_id';
        $options['fields'][] = 'chief_business_id';
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!in_array('category', $options['fields'])) {
            $options['fields'][] = 'category';
        }
        // array project task fields.
        $projectTaskFields = array(
            'Initialworkload',
            'Workload',
            'Overload',
            'Consumed',
            'InUsed',
            'ManualConsumed',
            'Completed',
            'Remain',
            'Amount€',
            '%progressorder',
            '%progressorder€',
            'UnitPrice',
            'Consumed€',
            'Remain€',
            'Workload€',
            'Estimated€',
        );
        /**
         * Lay fields in Finance
         */
        $checkEngedMd = $checkTaskEuro = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if ($value == 'internal_costs_engaged_md') {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkEngedMd = true;
                }
                if (in_array($value, $projectTaskFields)) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                    $checkTaskEuro = true;
                }
            }
        }
        $this->loadModel('ProjectFinanceTwoPlus');
        $financeFields = array();
        $LANG = Configure::read('Config.language');
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]), $this->ProjectFinanceTwoPlus->defaultField($employee["Company"]["id"]));
        /**
         * Lay project manager va thong tin confirm lien quan
         */
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'][] = array('Project.company_id' => $subCompanies);
                if (!empty($cate)) {
                    if ($cate == 5) {
                        $options['conditions'][] = array('Project.category' => array(1, 2));
                    } else if ($cate == 6) {
                        $options['conditions'][] = array('Project.category' => array(2));
                    } else {
                        $options['conditions'][] = array('Project.category' => $cate);
                    }
                }
            } else {
                $projects = $this->Project->ProjectTeam->ProjectFunctionEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'project_team_id'),
                    'conditions' => array(
                        'ProjectFunctionEmployeeRefer.employee_id' => $employee["Employee"]["id"])));
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_id', 'project_id'),
                    'conditions' => array('ProjectTeam.id' => $projects)
                ));
                $options['conditions'][] = array('Project.id' => $projects);
                if (!empty($cate)) {
                    $options['conditions'][] = array('Project.category' => $cate);
                }
            }
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'),
                        'conditions' => array('ProjectStatus.company_id' => $employee["Company"]["id"]),'order' => 'weight')));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
                        'conditions' => array('ProjectAmrProgram.company_id' => $employee["Company"]["id"]))));
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.company_id' => $employee["Company"]["id"],
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(array('ProfitCenter.company_id' => $employee["Company"]["id"]), null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list', array('conditions' => array('ProjectFunction.company_id' => $employee["Company"]["id"]))));
        } else {
            $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('all', array(
                'conditions' => array(
                    'CompanyEmployeeReference.role_id' => array(2, 3),
                ),
                'fields' => array('Employee.id', 'CONCAT(Employee.first_name," ",Employee.last_name) as fullname')
            ));
            $projectManagers = Set::combine($projectManagers, '{n}.Employee.id', '{n}.0.fullname');
            $this->set('projectManagersOption', $projectManagers);
            $this->set('project_statuses', $this->Project->ProjectStatus->find('list', array(
                        'fields' => array('ProjectStatus.id', 'ProjectStatus.name'))));
            $this->set('project_arm_programs', $this->Project->ProjectAmrProgram->find('list', array(
                        'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'))));
            $this->set('profit_centers', $this->Project->ProjectTeam->ProfitCenter->generateTreeList(null, null, null, ' -- ', -1));
            $this->set('project_functions', $this->Project->ProjectTeam->ProjectFunction->find('list'));
        }
        if ($viewGantt) {
            $options['contain']['ProjectPhasePlan'] = array(
                'fields' => array(
                    'id',
                    'project_planed_phase_id',
                    'phase_planed_start_date',
                    'phase_planed_end_date',
                    'phase_real_start_date',
                    'phase_real_end_date',
                ),
                'ProjectPhase' => array('name', 'color'));
            $options['contain']['ProjectMilestone'] = array(
                'order' => 'milestone_date ASC',
                'fields' => array(
                    'project_milestone',
                    'milestone_date',
                    'validated',
                    'project_id'
            ));
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }
        if ($checkFieldPurchase) {
            foreach ($options['contain']['ProjectBudgetSyn']['fields'] as $key => $value) {
                if (in_array($value, array('purchases_sold', 'purchases_to_bill', 'purchases_billed', 'purchases_paid'))) {
                    unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                }
            }
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        if ($checkNextMileston) {
            if (!empty($options['fields'])) {
                foreach ($options['fields'] as $key => $value) {
                    if ($value == 'next_milestone_in_day' || $value == 'next_milestone_in_week') {
                        unset($options['fields'][$key]);
                    }
                }
            }
        }
        /**
         * Lay thong tin project
         */
        $this->Project->recursive = 0;
        $this->Project->Behaviors->attach('Containable');
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->loadModels('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employeeId,
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $employee["Company"]["id"],
                        'project_manager_id' => $employeeId
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        if (!empty($listProjectIds)) {
            $options['conditions'][] = array('Project.id' => $listProjectIds);
        }
        if ($viewProjects == false) {
            $projects = array();
        } else {
            $projects = $this->Project->find('all', $options);
        }
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));

        $projectIds = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
        $listProjectLinked = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.activity_id') : array();
        $this->_parseNew($listProjectLinked, $projects, $viewGantt);
        // get Task euro 02/03/2017.
        $TaskEuros = array();
        if ($checkTaskEuro) {
            $TaskEuros = $this->getTaskEuro($projectIds);
        }
        // get nextMilestone
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        if ($checkNextMileston) {
            list($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek) = $this->getNextMilestone($projectIds);
        }
        $this->loadModels('ProjectBudgetPurchase', 'ProjectBudgetPurchaseInvoice');
        $Purchase = array();
        if ($checkFieldPurchase) {
            $this->ProjectBudgetPurchase->cacheQueries = true;
            $this->ProjectBudgetPurchase->recursive = -1;
            $this->ProjectBudgetPurchase->Behaviors->attach('Containable');

            $_budgetSales = $this->ProjectBudgetPurchase->find('all', array(
                'contain' => array('ProjectBudgetPurchaseInvoice'),
                'conditions' => array('project_id' => $projectIds)
                    )
            );
            foreach ($_budgetSales as $key => $value) {
                $dx = $value['ProjectBudgetPurchase'];
                if (empty($Purchase[$dx['project_id']]['purchases_sold'])) {
                    $Purchase[$dx['project_id']]['purchases_sold'] = 0;
                }
                $Purchase[$dx['project_id']]['purchases_sold'] += $dx['sold'];
                if (!empty($value['ProjectBudgetPurchaseInvoice'])) {
                    foreach ($value['ProjectBudgetPurchaseInvoice'] as $_key => $_value) {
                        if (empty($Purchase[$_value['project_id']]['purchases_to_bill'])) {
                            $Purchase[$_value['project_id']]['purchases_to_bill'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_to_bill'] += $_value['billed'];
                        if (empty($Purchase[$_value['project_id']]['purchases_paid'])) {
                            $Purchase[$_value['project_id']]['purchases_paid'] = 0;
                        }
                        $Purchase[$_value['project_id']]['purchases_paid'] += $_value['paid'];
                        if (!empty($_value['effective_date']) && $_value['effective_date'] != '0000-00-00') {
                            if (empty($Purchase[$_value['project_id']]['purchases_billed'])) {
                                $Purchase[$_value['project_id']]['purchases_billed'] = 0;
                            }
                            $Purchase[$_value['project_id']]['purchases_billed'] += $_value['billed'];
                        }
                    }
                }
            }
        }
        $this->set(compact('listColorNextMil', 'listNextMilestoneByDay', 'listNextMilestoneByWeek', 'Purchase'));
        /* get project_copy_id */
        $copy_ids = Set::classicExtract($projects, '{n}.Project.project_copy_id');
        $copy_ids = array_unique($copy_ids);
        $projectCopies = array();
        if (!empty($copy_ids)) {
            $projectCopies = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $copy_ids
                ),
                'fields' => array('id', 'project_name')
            ));
        }
        $freeze_ids = array_unique(Set::classicExtract($projects, '{n}.Project.freeze_by'));
        $freezers = array();
        if (!empty($freeze_ids)) {
            $this->Employee->virtualFields['fullname'] = 'CONCAT(first_name, \' \', last_name)';
            $freezers = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $freeze_ids
                ),
                'fields' => array('id', 'fullname')
            ));
        }
        $this->set(compact('projectCopies', 'freezers'));
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $staffingSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee', 'profit_center'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('model', 'project_id', 'SUM(estimated) AS Total'),
            'group' => array('model', 'project_id'),
            'order' => array('project_id')
        ));
        $staffingSystems = !empty($staffingSystems) ? Set::combine($staffingSystems, '{n}.TmpStaffingSystem.project_id', '{n}.0.Total', '{n}.TmpStaffingSystem.model') : array();
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        if (!empty($pairs)) {
            $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
                'recursive' => -1,
                'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
                'fields' => array(
                    'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                    'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                    'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
                )
            ));
        }
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();

        $Internal = ClassRegistry::init('ProjectBudgetInternalDetail');
        $Internal->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $Internal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));
        $this->set('internalBudgets', $internalBudgets);
        /**
         * Lay ngay ket thuc theo ke hoach cua tat ca project
         */
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_keys($listProjectLinked),
                'NOT' => array(
                    'phase_planed_start_date' => '0000-00-00',
                    'phase_planed_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'project_id',
                'MAX(phase_planed_end_date) AS MaxEndDatePlan',
                'MAX(phase_real_end_date) AS MaxEndDateReal'
            ),
            'group' => array('project_id')
        ));
        $projectPhasePlans = !empty($projectPhasePlans) ? Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_id', '{n}.0') : array();
        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        $this->set(compact('consumedAndWorkloadForActivities', 'currentYears', 'provisionals'));
        /**
         * Lay du lieu o table finacement plus
         */
        $finances = $finEuros = $finPercents = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
            foreach ($fieldFinancements['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[1]) && $_field[1] == 'percent') {
                    $finPercents[] = 'ProjectFinancePlus.' . $field;
                } else {
                    $finEuros[] = 'ProjectFinancePlus.' . $field;
                }
            }
        }
        $financesTwoPlus = $finTwoPlusEuros = $finTwoPlusPercent = array();
        $startFinanceTwoPlus = $endFinanceTwoPlus = date('Y', time());
        $this->loadModels('ProjectFinanceTwoPlusDetail', 'ProjectFinanceTwoPlusDate');
        if (!empty($fieldFinancementTwoPlus['fields'])) {
            $_financesTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array('project_id', 'year', 'budget_initial', 'budget_revised', 'last_estimated', 'engaged', 'bill', 'disbursed'),
            ));
            foreach ($_financesTwoPlus as $key => $value) {
                $dx = $value['ProjectFinanceTwoPlusDetail'];
                // tinh tung nam.
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial_' . $dx['year']] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised_' . $dx['year']] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated_' . $dx['year']] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged_' . $dx['year']] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill_' . $dx['year']] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed_' . $dx['year']] += $dx['disbursed'];
                // tinh total
                if (empty($financesTwoPlus[$dx['project_id']]['budget_initial'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_initial'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_initial'] += $dx['budget_initial'];

                if (empty($financesTwoPlus[$dx['project_id']]['budget_revised'])) {
                    $financesTwoPlus[$dx['project_id']]['budget_revised'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['budget_revised'] += $dx['budget_revised'];

                if (empty($financesTwoPlus[$dx['project_id']]['last_estimated'])) {
                    $financesTwoPlus[$dx['project_id']]['last_estimated'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['last_estimated'] += $dx['last_estimated'];

                if (empty($financesTwoPlus[$dx['project_id']]['engaged'])) {
                    $financesTwoPlus[$dx['project_id']]['engaged'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['engaged'] += $dx['engaged'];

                if (empty($financesTwoPlus[$dx['project_id']]['bill'])) {
                    $financesTwoPlus[$dx['project_id']]['bill'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['bill'] += $dx['bill'];

                if (empty($financesTwoPlus[$dx['project_id']]['disbursed'])) {
                    $financesTwoPlus[$dx['project_id']]['disbursed'] = 0;
                }
                $financesTwoPlus[$dx['project_id']]['disbursed'] += $dx['disbursed'];
            }
            $financeTwoPlus = $this->ProjectFinanceTwoPlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(year) AS startDate',
                    'MAX(year) AS endDate'
                )
            ));
            $financeTwoDates = $this->ProjectFinanceTwoPlusDate->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectIds),
                'fields' => array(
                    'MIN(start) AS fonStart',
                    'MAX(end) AS fonEnd'
                )
            ));
            $startFinanceTwoPlus = !empty($financeTwoPlus[0][0]['startDate']) ? $financeTwoPlus[0][0]['startDate'] : date('Y', time());
            $endFinanceTwoPlus = !empty($financeTwoPlus[0][0]['endDate']) ? $financeTwoPlus[0][0]['endDate'] : date('Y', time());
            if (!empty($financeTwoDates) && !empty($financeTwoDates[0][0])) {
                $financeTwoDates = $financeTwoDates[0][0];
                $startFinanceTwoPlus = (date('Y', $financeTwoDates['fonStart']) < $startFinanceTwoPlus) ? date('Y', $financeTwoDates['fonStart']) : $startFinanceTwoPlus;
                $endFinanceTwoPlus = (date('Y', $financeTwoDates['fonEnd']) > $endFinanceTwoPlus) ? date('Y', $financeTwoDates['fonEnd']) : $endFinanceTwoPlus;
            }
            foreach ($fieldFinancementTwoPlus['fields'] as $field) {
                $_field = explode('_', $field);
                if (!empty($_field[0]) && $_field[0] == 'percent') {
                    $finTwoPlusPercent[] = 'ProjectFinanceTwoPlus.' . $field;
                } else {
                    $finTwoPlusEuros[] = 'ProjectFinanceTwoPlus.' . $field;
                }
            }
        }
        $this->set(compact('finances', 'finEuros', 'finPercents', 'financesTwoPlus', 'startFinanceTwoPlus', 'endFinanceTwoPlus', 'finTwoPlusEuros', 'finTwoPlusPercent'));
        /*
         * Manual consumed
         */
        $this->loadModel('ProjectTask');
        $this->ProjectTask->virtualFields['manual'] = 'SUM(manual_consumed)';
        $tasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array(
                'project_id',
                'manual'
            ),
            'group' => array('project_id')
        ));
        $this->set('manualData', $tasks);
        /**
         * Dung nhieu view trong 1 controller. Neu cate = 2 chuyen den view: opportunity.
         */
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $appstatus = $this->Session->read("App.status");

        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        $this->set('datasets', $datasets);
        //priorities
        $priorities = $this->Project->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'priority')
        ));
        $this->set('priorities', $priorities);
        //editable list, available for pm only
        $this->set('isPM', $this->employee_info['CompanyEmployeeReference']['role_id'] == 3);
        $this->set('isAdmin', $this->employee_info['CompanyEmployeeReference']['role_id'] == 2);
        $this->set('editable', $this->canModifyProject($projectIds));
        //log system for done & todo
        if (!empty($projectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $projectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        // get value for dialog vision task
        $projectStatus = $this->Project->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),'order' => 'weight',
            'fields' => array('id', 'name')
        ));
        $projectProgram = $this->Project->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->Project->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_amr_program_id' => array_keys($projectProgram)
            ),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_name')
        ));
        $listProfitCenter = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'name')
        ));
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname')
        ));
        $listProjectCode = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'project_code_1', 'project_code_2')
        ));
        $_listProjectCode = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_1') : array();
        $_listProjectCode1 = !empty($listProjectCode) ? Set::classicExtract($listProjectCode, '{n}.Project.project_code_2') : array();
        if (!empty($_listProjectCode)) {
            $_listProjectCode = array_unique($_listProjectCode);
            $_listProjectCode = array_combine($_listProjectCode, $_listProjectCode);
            unset($_listProjectCode['']);
        }
        if (!empty($_listProjectCode1)) {
            $_listProjectCode1 = array_unique($_listProjectCode1);
            $_listProjectCode1 = array_combine($_listProjectCode1, $_listProjectCode1);
            unset($_listProjectCode1['']);
        }
        $_milestone = $this->ProjectMilestone->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds
            ),
            'fields' => array('project_milestone', 'project_milestone')
        ));
        // get company config
        $this->loadModel('CompanyConfig');
        $showTaskVision = $this->CompanyConfig->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company' => $company_id,
                'cf_name' => 'display_visions'
            ),
            'fields' => array('id', 'cf_value')
        ));
        $showTaskVision = !empty($showTaskVision) ? $showTaskVision['CompanyConfig']['cf_value'] : 0;
        $this->set(compact('projectStatus', 'projectProgram', 'projectSubProgram', 'listProfitCenter', '_listProjectCode', '_listProjectCode1', 'showTaskVision', 'listEmployee', '_milestone'));
         //end ----
        $this->loadModel('ProjectType');
        $listprojectTypes = $this->Project->ProjectType->find('list', array(
            'fields' => array('ProjectType.id', 'ProjectType.project_type'),
            'conditions' => array('ProjectType.company_id' => $company_id, 'ProjectType.display' => 1)
        ));
        $listprojectSubTypes = $this->Project->ProjectSubType->find('list', array(
            'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
            'conditions' => array('ProjectSubType.project_type_id' => array_keys($listprojectTypes), 'ProjectSubType.display' => 1)
        ));
        //Lay Budget settings
        $this->loadModel('BudgetSetting');
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $displayExpectation = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
                'display' => 1
            ),
        ));
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $_f = array('id', 'name_eng');
        } else {
            $_f = array('id', 'name_fre');
        }
        $screenExpec = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
                'controllers' => 'project_expectations',
            ),
            'fields' => $_f
        ));
        $this->loadModel('HistoryFilter');
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'path' => 'projects/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->loadModel('ProjectEmployeeManager');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            ),
            'fields' => array('project_id', 'project_id')
        ));
        $employee_id = $this->employee_info['Employee']['id'];
        $_listAvata = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model_id' => $projectIds
            )
        ));
        $listAvata = array();
        foreach ($_listAvata as $value) {
            $dx = $value['LogSystem'];
            $listAvata[$dx['model'] . '-' . $dx['model_id']] = $dx['employee_id'];
        }
		
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id']), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set(compact('logs', 'currency_name', 'appstatus', 'staffingSystems', 'projectPhasePlans', 'budgetSyns', 'ACLController', 'ACLAction', 'logGroups', 'fieldset', 'projects', 'viewId', 'noProjectManager', 'employee', 'checkStatus', 'personDefault', 'financeFields', 'LANG', 'viewGantt', 'confirmGantt', 'cate', 'listPc', 'listprojectTypes', 'listprojectSubTypes', 'budget_settings', 'checkEngedMd', 'TaskEuros', 'projectTaskFields', 'displayExpectation'));
        $this->set(compact('screenExpec', 'savePosition', 'listEmployeeManager', 'employee_id', 'listAvata'));
        if ($cate == 2) {
            $this->Session->write("App.status_oppor", 2);
            //if( !$mobileEnabled )$this->action = 'opportunity';
        } else {
            $this->Session->delete('App.status_oppor');
        }
    }

    public function getComment() {
        if (!empty($_POST['id'])) {
            $this->loadModel('Employee');
            $id = $_POST['id'];
            $model = $_POST['model'];
            $this->loadModels('LogSystem', 'Project');
            $logSystems = $this->LogSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $id,
                    'model' => $model,
                ),
                // 'limit' => 3,
                'fields' => array('id', 'model_id', 'model', 'description', 'updated', 'update_by_employee', 'name', 'employee_id'),
                'order' => array('id' => 'DESC')
            ));
            $project = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id
                ),
                'fields' => array('project_name', 'start_date', 'end_date')
            ));
            $listComments = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
            $i = 0;
            $data['project_name'] = $project['Project']['project_name'];
            
            $progress = $this->getProjectProgress($id);
            $data['initDate'] = !empty($progress[$id]['Completed']) ? $progress[$id]['Completed'] : 0;
            $checkAvata = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'avatar_resize')
            ));
            $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
            $data['hasAvatar'] = $checkAvata;
            foreach ($listComments as $_comment) {
                $data[] = $_comment;
                // $data[$i++]['updated'] = date('d/m/Y', $_comment['updated']);
            }
            die(json_encode($data));
        }
        exit;
    }

    public function getProjectTaskText() {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $this->loadModels('ProjectTaskTxt', 'ProjectTask', 'Employee');
            $projectTaskTxt = $this->ProjectTaskTxt->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_task_id' => $id,
                ),
                'limit' => 2,
                'field' => '*',
                'order' => array('id' => 'DESC')
            ));
            $projectTaskTxt = !empty($projectTaskTxt) ? Set::combine($projectTaskTxt, '{n}.ProjectTaskTxt.id', '{n}.ProjectTaskTxt') : array();
            $listEmployeeId = array();
            foreach ($projectTaskTxt as $key => $value) {
                $listEmployeeId[] = $value['employee_id'];
            }
            if (!empty($listEmployeeId)) {
                $getEmpoyee = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'id' => $listEmployeeId[0],
                    ),
                    'fields' => array('fullname')
                ));
            }
            if (!empty($getEmpoyee['Employee']['fullname']))
                $data['update_by_employee'] = $getEmpoyee['Employee']['fullname'];
            $project = $this->ProjectTask->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $id
                ),
                'fields' => array('task_title', 'task_start_date', 'task_end_date')
            ));
            $i = 0;
            $data['project_name'] = $project['ProjectTask']['task_title'];

            $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($project['ProjectTask']['task_end_date']));
            $projectDate = abs(strtotime($project['ProjectTask']['task_end_date']) - strtotime($project['ProjectTask']['task_start_date']));
            $initDate = 100;
            if ($projectCurrentDate < $projectDate)
                $initDate = floor(($projectCurrentDate / $projectDate) * 100);
            $data['initDate'] = $initDate;
            foreach ($projectTaskTxt as $key => $_comment) {
                $data[] = $_comment;
            }
            die(json_encode($data));
        }
        exit;
    }

    public function updateTaskText() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectTaskTxt');
        if (!empty($this->data)) {
            $task_id = $this->data['id'];
            $employee_id = $this->employee_info['Employee']['id'];
            $create = date('Y/m/d H:i', time());
            unset($this->data['id']);

            $data = array(
                'project_task_id' => $task_id,
                'employee_id' => $employee_id,
                'comment' => $this->data['text'],
                'created' => $create
            );


            $this->ProjectTaskTxt->create();
            if (!empty($data) && $this->ProjectTaskTxt->save($data)) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function canModifyProject($projects) {
        return ClassRegistry::init('ProjectEmployeeManager')->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $projects,
                        'project_manager_id' => $this->employee_info['Employee']['id'],
                        'type !=' => 'RA',
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
        ));
    }

    /**
     * Xay dung neu parse
     */
    public function _parseNew($listProjectToActivities = array(), $projects = array(), $viewGantt = false) {
        $manual = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : false;
        $this->loadModel('ProjectEvolution');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        /**
         * Lay company cua used dang login
         */
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay evolution theo danh sach id
         */
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => array_keys($listProjectToActivities)),
            'fields' => array('id', 'project_id', 'SUM(man_day) as totalMD'),
            'group' => array('project_id')
        ));
        $projectEvolutions = !empty($projectEvolutions) ? Set::combine($projectEvolutions, '{n}.ProjectEvolution.project_id', '{n}.0.totalMD') : array();
        /**
         * Lay cac project Task theo danh sach project
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => array_keys($listProjectToActivities)),
            'fields' => array('id', 'parent_id', 'project_planed_phase_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed', 'manual_consumed', 'manual_overload')
        ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
        $listIdProjectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.id', '{n}.id') : array();
        /**
         * Lay danh sach activity task theo danh sach project task
         */
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listIdProjectTasks),
            'fields' => array('id', 'activity_id', 'project_task_id')
        ));
        $listATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        /**
         * Lay consumed cua cac activity
         */
        $consumedOfActivities = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'activity_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('activity_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0),
                'OR' => array(
                    'task_id' => array_keys($activityTasks),
                    'activity_id' => $listProjectToActivities
                )
            )
        ));
        $consumedOfActivities = !empty($consumedOfActivities) ? Set::combine($consumedOfActivities, '{n}.ActivityRequest.task_id', '{n}.0.value', '{n}.ActivityRequest.activity_id') : array();
        $sumPrevious = $sumActivities = $consumedOfTasks = array();
        if (!empty($consumedOfActivities)) {
            $consumedOfTasks = !empty($consumedOfActivities[0]) ? $consumedOfActivities[0] : array();
            unset($consumedOfActivities[0]);
            foreach ($consumedOfActivities as $activityID => $value) {
                $val = array_shift($value);
                $sumActivities[$activityID] = $val;
                $sumPrevious[$activityID] = $val;
            }
            if (!empty($consumedOfTasks)) {
                foreach ($consumedOfTasks as $taskId => $value) {
                    $activityID = !empty($activityTasks[$taskId]) ? $activityTasks[$taskId] : 0;
                    if (!isset($sumActivities[$activityID])) {
                        $sumActivities[$activityID] = 0;
                    }
                    $sumActivities[$activityID] += $value;
                }
            }
        }
        /**
         * Tinh consumed cua employee theo activity
         */
        $consumedActivityOfEmployees = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'activity_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('employee_id', 'activity_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'OR' => array(
                    'task_id' => array_keys($activityTasks),
                    'activity_id' => $listProjectToActivities
                ),
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
                )
        );
		$sumEmployees = array();
        foreach ($consumedActivityOfEmployees as $consumedActivityOfEmployee) {
            $dx = $consumedActivityOfEmployee['ActivityRequest'];
            $value = $consumedActivityOfEmployee['0']['value'];
            $activityID = $dx['activity_id'];
            if (!empty($dx['task_id'])) {
                $activityID = !empty($activityTasks[$dx['task_id']]) ? $activityTasks[$dx['task_id']] : 0;
            }
            if (!isset($sumEmployees[$activityID][$dx['employee_id']])) {
                $sumEmployees[$activityID][$dx['employee_id']] = 0;
            }
            $sumEmployees[$activityID][$dx['employee_id']] += $value;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $employees = Set::combine($this->ActivityRequest->Employee->find('all', array(
                            'fields' => array('id', 'tjm', 'actif'), 'recursive' => -1,
                        )), '{n}.Employee.id', '{n}.Employee');
        /**
         * Tinh remain, remain special,.... theo tung task cua project
         */
        $sumWorload = $sumOverload = $sumRemains = $sumRemainSpecials = array();
        foreach ($projectTasks as $taskId => &$projectTask) {
            if (in_array($taskId, $parentIds)) {
                unset($projectTasks[$taskId]);
            } else {
                $projectId = $projectTask['project_id'];
                $dataEstimated = $projectTask['estimated'];
                if ($manual) {
                    $projectTask['overload'] = $projectTask['manual_overload'];
                }
                $dataOverload = $projectTask['overload'];
                /**
                 * Sum Workload
                 */
                if (!isset($sumWorload[$projectId])) {
                    $sumWorload[$projectId] = 0;
                }
                $sumWorload[$projectId] += $dataEstimated;
                /**
                 * Sum overload
                 */
                if (!isset($sumOverload[$projectId])) {
                    $sumOverload[$projectId] = 0;
                }
                $sumOverload[$projectId] += $dataOverload;
                /**
                 * Remain cua task thuoc project task
                 * Remain cua cac task thuoc external budget
                 */
                $consumedForTasks = 0;
                if (!$manual) {
                    if (!empty($projectTask['special']) && $projectTask['special'] == 1) {
                        $consumedForTasks = !empty($projectTask['special_consumed']) ? $projectTask['special_consumed'] : 0;
                        if (!isset($sumRemainSpecials[$projectId])) {
                            $sumRemainSpecials[$projectId] = 0;
                        }
                        $sumRemainSpecials[$projectId] += $dataEstimated - $consumedForTasks;
                    } else {
                        $ATaskId = !empty($listATaskLinkPTasks[$taskId]) ? $listATaskLinkPTasks[$taskId] : 0;
                        $consumedForTasks = !empty($consumedOfTasks[$ATaskId]) ? $consumedOfTasks[$ATaskId] : 0;
                    }
                } else {
                    if (!isset($sumRemainSpecials[$projectId])) {
                        $sumRemainSpecials[$projectId] = 0;
                    }
                    $sumRemainSpecials[$projectId] += $dataEstimated - $projectTask['manual_consumed'];
                    $consumedForTasks = $projectTask['manual_consumed'];
                }
                if (!isset($sumRemains[$projectId])) {
                    $sumRemains[$projectId] = 0;
                }
                $sumRemains[$projectId] += number_format(($dataEstimated + $dataOverload) - $consumedForTasks, 2);
            }
        }
        /**
         * Lay consumed cua tung task
         */
        $requestTasks = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                'SUM(value) as value'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => array_keys($activityTasks),
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $requestTasks = !empty($requestTasks) ? Set::combine($requestTasks, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
        $phases = array();
        if (!empty($projects) && $viewGantt && !empty($projectTasks)) {
            foreach ($projectTasks as $projectTask) {
                $dx = $projectTask;
                if (!isset($phases[$dx['project_id']]['workload'])) {
                    $phases[$dx['project_id']]['workload'] = 0;
                }
                $phases[$dx['project_id']]['workload'] += (float) $dx['estimated'] + (float) $dx['overload'];
                $ATaskId = !empty($listATaskLinkPTasks[$dx['id']]) ? $listATaskLinkPTasks[$dx['id']] : 0;
                if ($manual) {
                    $consumed = $projectTask['manual_consumed'];
                } else {
                    $consumed = !empty($requestTasks) && !empty($requestTasks[$ATaskId]) ? $requestTasks[$ATaskId] : 0;
                    if (!empty($dx['special'])) {
                        $consumed = $dx['special_consumed'];
                    }
                }
                if (!isset($phases[$dx['project_id']]['consumed'])) {
                    $phases[$dx['project_id']]['consumed'] = 0;
                }
                $phases[$dx['project_id']]['consumed'] += $consumed;
            }
        }
        $this->set(compact('projectEvolutions', 'consumedOfTasks', 'sumActivities', 'sumEmployees', 'employees', 'sumWorload', 'sumOverload', 'sumRemainSpecials', 'sumRemains', 'phases', 'sumPrevious'));
    }

    /**
     * php Excel
     * Export to Excel
     *
     * @return void
     * @access public
     */
	private function get_dash_widget(){
		$this->loadModels('ProfileProjectManagerDetail');
		$isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
		$enableWidgets = array();
		$wg_id = array( 'phase', 'milestone', 'internal_cost', 'finance_plus', 'external_cost', 'synthesis');
		// $wg_id = array( 'phase', 'milestone');
		$LANG = Configure::read('Config.language');
		if($isProfileManager != 0 ){
			$dash_widgets = $this->ProfileProjectManagerDetail->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
					'widget_id' => $wg_id,
				),
				'fields' => array('widget_id',  'name_'.$LANG)
			));
		}else{
			$dash_widgets = $this->Menu->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
					'model' => 'project',
					'widget_id' => $wg_id,
				),
				'fields' => array('widget_id', 'name_'.$LANG)
			));
		}
		foreach($dash_widgets as $wg_id => $text){
			switch ($wg_id) {
				case 'finance_plus':
					$dash_widgets['ProjectWidget.FinancePlusinv'] = __d('Budget_Investment', "Budget Investment", null);
					$dash_widgets['ProjectWidget.FinancePlusfon'] = __d('Budget_Operation', "Budget Operation", null);
					$dash_widgets['ProjectWidget.FinancePlusfinanfon'] = __d('Budget_Operation', "Finance Operation", null);
					$dash_widgets['ProjectWidget.FinancePlusfinaninv'] = __d('Budget_Operation', "Finance Investment", null);
					unset($dash_widgets[$wg_id]);
					break;
				case 'internal_cost':
					$dash_widgets['ProjectWidget.InternalBudgetEuro'] = $text;
					$dash_widgets['ProjectWidget.InternalBudgetMD'] = $text .' '. __('M.D', true);
					unset($dash_widgets[$wg_id]);
					break;
				case 'external_cost':
					$dash_widgets['ProjectWidget.ExternalBudget'] = $text;
					unset($dash_widgets[$wg_id]);
					break;
				default: 
					$dash_widgets['ProjectWidget.' . ucfirst($wg_id)] = $text;
					unset($dash_widgets[$wg_id]);
					break;
			}
		}
		$list_select_yourform = array(
			'project_manager_id',
			'project_amr_program_id',
			'project_status_id',
			'project_priority_id',
			'team',
			'list_1',
			'list_2',
			'list_3',
			'list_4',
			'list_5',
			'list_6',
			'list_7',
			'list_8',
			'list_9',
			'list_10',
			'list_11',
			'list_12',
			'list_13',
			'list_14',
			'list_15',
			'price_1',
			'price_2',
			'price_3',
			'price_4',
			'price_5',
			'price_6',
			'price_7',
			'price_8',
			'price_9',
			'price_10',
			'price_11',
			'price_12',
			'price_13',
			'price_14',
			'price_15',
			'price_16',
			'number_1',
			'number_2',
			'number_3',
			'number_4',
			'number_5',
			'number_6',
			'number_7',
			'number_8',
			'number_9',
			'number_10',
			'number_11',
			'number_12',
			'number_13',
			'number_14',
			'number_15',
			'number_16',
			'number_17',
			'number_18',
			'yn_1',
			'yn_2',
			'yn_3',
			'yn_4',
			'yn_5',
			'yn_6',
			'yn_7',
			'yn_8',
			'yn_9',
			'project_type_id',
			'project_sub_type_id',
			'project_sub_sub_type_id',
			'complexity_id',
			'budget_customer_id',
			// Multiselect fields
			'project_phase_id',
			'list_muti_1',
			'list_muti_2',
			'list_muti_3',
			'list_muti_4',
			'list_muti_5',
			'list_muti_6',
			'list_muti_7',
			'list_muti_8',
			'list_muti_9',
			'list_muti_10',
			// End Multiselect fields
		);
		$this->loadModels( 'Translation');
		$translation_data = $this->Translation->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => array('Details'),
                'TranslationSetting.company_id' => $this->employee_info['Company']['id'],
				'TranslationSetting.show' => 1,
				// 'field' => $list_select_yourform
            ),
            'fields' => array( 'field', 'original_text'),
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
		foreach(  $list_select_yourform as $wg_id){
			if( isset( $translation_data[$wg_id]))
				$dash_widgets['Project.' . $wg_id] = $translation_data[$wg_id];
		}
		// Fields yourform alway display
		$dash_widgets['ProjectAmr.weather'] = 'Weather';
		return $dash_widgets;
	}
    public function export_project($viewId = null) {
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $employee = $this->Session->read("Auth.employee_info");
        $fieldset = array();
        if ($viewId) {
            $fieldset = $this->Project->UserView->find('first', array(
                'fields' => array('UserView.name', 'UserView.content'),
                'conditions' => array('UserView.id' => $viewId)));
            if (!empty($fieldset)) {
                $fieldset = unserialize($fieldset['UserView']['content']);
            } else {
                $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
            }
        } else {
            $fieldset = array(
                'Project.project_name',
                'Project.project_phase_id',
                'Project.project_manager_id',
                'Project.chief_business_id',
                'Project.technical_manager_id',
                'Project.project_priority_id',
                'Project.project_status_id',
                'Project.start_date',
                'ProjectAmr.weather'
            );
        }
        $this->loadModels('ProjectFinance');
        $financeFields = array();
        $financeFields = array_merge($this->ProjectFinance->defaultFields($employee["Company"]["id"]), $this->ProjectFinance->defaultFieldPlus($employee["Company"]["id"]));
        if (empty($fieldset)) {
            $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
        }
        $this->Project->Behaviors->attach('Containable');
        $addInternalMD = $addExternalMD = false;
        if (in_array('ProjectBudgetSyn.provisional_budget_md', $fieldset)) {
            if (!in_array('ProjectBudgetSyn.internal_costs_budget_man_day', $fieldset)) {
                $addInternalMD = true;
                $fieldset[] = 'ProjectBudgetSyn.internal_costs_budget_man_day';
            }
            if (!in_array('ProjectBudgetSyn.external_costs_man_day', $fieldset)) {
                $addExternalMD = true;
                $fieldset[] = 'ProjectBudgetSyn.external_costs_man_day';
            }
        }
        $checkFieldCurrentPhase = false;
        if (in_array('Project.project_phase_id', $fieldset)) {
            $checkFieldCurrentPhase = true;
        }
        $checkFieldMuti = false;
        for ($i = 1; $i < 10; $i++) {
            if (in_array('Project.list_muti_' . $i, $fieldset)) {
                $checkFieldMuti = true;
            }
        }
        list($fieldset, $options) = $this->Project->parseViewField($fieldset);
        $fieldFinancements = !empty($options['contain']['ProjectFinancePlus']) ? $options['contain']['ProjectFinancePlus'] : array();
        unset($options['contain']['ProjectFinancePlus']);
        if (!in_array('id', $options['fields'])) {
            $options['fields'][] = 'id';
        }
        if (!in_array('activity_id', $options['fields'])) {
            $options['fields'][] = 'activity_id';
        }
        if (!$this->is_sas) {
            if ($employee["Role"]["name"] != "conslt") {
                $subCompanies = $this->Project->Company->find('list', array(
                    'fields' => array('id', 'id'),
                    'conditions' => array('OR' => array(
                            'Company.id' => $employee["Company"]["id"], 'Company.parent_id' => $employee["Company"]["id"]))));
                $options['conditions'] = array('Project.company_id' => $subCompanies);
            } else {
                $projects = $this->Project->ProjectTeam->find('list', array(
                    'fields' => array('id', 'project_id'),
                    'conditions' => array('Project.company_id' => $employee["Company"]["id"],
                        'ProjectTeam.employee_id' => $employee["Employee"]["id"])));
                $options['conditions'] = array('Project.id' => $projects);
            }
        }
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $options['conditions']['Project.id'] = $data;
            }
        }
        if ($checkFieldCurrentPhase) {
            $options['contain']['ProjectPhaseCurrent'] = array(
                'fields' => array(
                    'id',
                    'project_phase_id',
            ));
        }
        if ($checkFieldMuti) {
            $options['contain']['ProjectListMultiple'] = array(
                'fields' => array(
                    'id',
                    'project_dataset_id',
                    'key'
            ));
        }
        $checkEngedMd = false;
        if (!empty($options['contain']['ProjectBudgetSyn']['fields'])) {
            if (in_array('internal_costs_engaged_md', $options['contain']['ProjectBudgetSyn']['fields'])) {
                $key = array_search('internal_costs_engaged_md', $options['contain']['ProjectBudgetSyn']['fields']);
                unset($options['contain']['ProjectBudgetSyn']['fields'][$key]);
                $checkEngedMd = true;
            }
        }
        $projects = Set::combine($this->Project->find('all', $options), '{n}.Project.id', '{n}');
        $company_id = $employee["Company"]["id"];
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $idProjects = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.id') : array();
        $getDataFromTasks = $this->_makeDataAmr($idProjects);
        $listProjectLinked = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        $getDataActivities = $this->_parse($listProjectLinked);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];
        $this->ProjectBudgetInternalDetail->virtualFields['value'] = 'AVG(ProjectBudgetInternalDetail.average)';
        $getAverages = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array(
                'project_id',
                'value'
            // 'AVG(average) as value'
            ),
            'conditions' => array('project_id' => $idProjects),
            'group' => array('project_id')
        ));
        // $getAverages = !empty($getAverages) ? Set::combine($getAverages, '{n}.ProjectBudgetInternalDetail.project_id', '{n}.0.value') : array();
        /**
         * Lay data budget
         */
        $this->loadModel('TmpStaffingSystem');
        $dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignEmployees = array();
        if (!empty($dataSystems)) {
            foreach ($dataSystems as $dataSystem) {
                $dx = $dataSystem['TmpStaffingSystem'];
                if (!isset($assignEmployees[$dx['project_id']])) {
                    $assignEmployees[$dx['project_id']] = 0;
                }
                $assignEmployees[$dx['project_id']] += $dx['estimated'];
            }
        }
        $_dataSystems = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('profit_center'),
                'NOT' => array(
                    'project_id' => 0,
                    'model_id' => 999999999
                ),
                'company_id' => $employee["Company"]["id"]
            ),
            'fields' => array('project_id', 'model_id', 'estimated')
        ));
        $assignProfitCenters = array();
        if (!empty($_dataSystems)) {
            foreach ($_dataSystems as $_dataSystem) {
                $dx = $_dataSystem['TmpStaffingSystem'];
                if (!isset($assignProfitCenters[$dx['project_id']])) {
                    $assignProfitCenters[$dx['project_id']] = 0;
                }
                $assignProfitCenters[$dx['project_id']] += $dx['estimated'];
            }
        }
        /**
         * Lay phan Log cua he thong. Bao gom Log: Risk, Issue, Log KPI+
         */
        $this->loadModel('LogSystem');
        $logSystems = $this->LogSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => array('ProjectAmr', 'ProjectIssue', 'ProjectRisk')
            ),
            'order' => array('created' => 'DESC')
        ));
        $logGroups = array();
        if (!empty($logSystems)) {
            foreach ($logSystems as $logSystem) {
                $dx = $logSystem['LogSystem'];
                $logGroups[$dx['model_id']][$dx['model']][] = $dx;
            }
        }

        /**
         * lay consumed va workload cua 3 nam truoc va 3 nam sau
         */
        $currentYears = date('Y', time());
        $lastY = strtotime('01-01-' . ($currentYears - 3));
        $nextY = strtotime('31-12-' . ($currentYears + 3));
        $consumedAndWorkloadForActivities = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => array('employee'),
                'project_id' => $idProjects,
                'date BETWEEN ? AND ?' => array($lastY, $nextY),
                'company_id' => $company_id
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `consumed` ELSE 0 END) AS consumed_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `consumed` ELSE 0 END) AS consumed_' . ($currentYears + 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `estimated` ELSE 0 END) AS workload_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `estimated` ELSE 0 END) AS workload_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $consumedAndWorkloadForActivities = !empty($consumedAndWorkloadForActivities) ? Set::combine($consumedAndWorkloadForActivities, '{n}.TmpStaffingSystem.project_id', '{n}.0') : array();
        /**
         * Lay du lieu project budget Provisional
         */
        $this->loadModel('ProjectBudgetProvisional');
        $provisionals = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $idProjects,
                'date BETWEEN ? AND ?' => array($lastY, $nextY)
            ),
            'fields' => array(
                'project_id',
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . $currentYears . '" THEN `value` ELSE 0 END) AS provisional_' . $currentYears,
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears - 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears - 3),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 1) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 1),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 2) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 2),
                'SUM(CASE WHEN FROM_UNIXTIME(date, "%Y") = "' . ($currentYears + 3) . '" THEN `value` ELSE 0 END) AS provisional_' . ($currentYears + 3)
            ),
            'group' => array('project_id')
        ));
        $provisionals = !empty($provisionals) ? Set::combine($provisionals, '{n}.ProjectBudgetProvisional.project_id', '{n}.0') : array();
        /**
         * Lay du lieu o table finacement plus
         */
        $finances = array();
        if (!empty($fieldFinancements['fields'])) {
            $finances = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $idProjects),
                'fields' => array(
                    'project_id', 'type', 'model', 'year',
                    'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model, "_", ProjectFinancePlusDetail.year) AS keyValue',
                    'SUM(value) AS val'
                ),
                'group' => array('project_id', 'type', 'model', 'year')
            ));
            $finances = !empty($finances) ? Set::combine($finances, '{n}.0.keyValue', '{n}.0.val', '{n}.ProjectFinancePlusDetail.project_id') : array();
        }
        /**
         * Lay du lieu synthesis budget de tinh total cac budget.
         */
        $pairs = !empty($projects) ? Set::format($projects, '("{0}","{1}")', array('{n}.Project.id', '{n}.Project.activity_id')) : array();
        $this->loadModel('ProjectBudgetSyn');
        $budgetSyns = $this->ProjectBudgetSyn->find('all', array(
            'recursive' => -1,
            'conditions' => array('(project_id, activity_id) IN (' . implode(',', $pairs) . ')'),
            'fields' => array(
                'id', 'project_id', 'internal_costs_budget', 'internal_costs_average', 'external_costs_budget', 'internal_costs_forecast',
                'external_costs_forecast', 'external_costs_ordered', 'internal_costs_remain', 'external_costs_remain',
                'internal_costs_forecasted_man_day', 'external_costs_man_day', 'sales_sold', 'internal_costs_budget_man_day'
            )
        ));
        $budgetSyns = !empty($budgetSyns) ? Set::combine($budgetSyns, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn') : array();
        $this->ProjectBudgetInternalDetail->virtualFields['total_md'] = 'SUM(budget_md)';
        $internalBudgets = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $idProjects
            ),
            'fields' => array('project_id', 'total_md'),
            'group' => 'project_id'
        ));

        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name')
        ));
        $datasets = array();
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        unset($rawDatasets);
        $projectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $this->employee_info['Company']['id'], 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC'));
        foreach ($projects as $key => $project) {
            $dx = $project['Project'];
            if (isset($project['ProjectAmr'][0])) {
                $projects[$key]['ProjectAmr'][0]['project_amr_progression'] = $getDataFromTasks[$key]['project_amr_progression'];
                $projects[$key]['ProjectAmr'][0]['md_validated'] = $getDataFromTasks[$key]['md_validated'];
                $projects[$key]['ProjectAmr'][0]['md_engaged'] = $getDataFromTasks[$key]['md_engaged'];
                $projects[$key]['ProjectAmr'][0]['md_forecasted'] = $getDataFromTasks[$key]['md_forecasted'];
                $projects[$key]['ProjectAmr'][0]['md_variance'] = $getDataFromTasks[$key]['md_variance'];

                $projects[$key]['ProjectAmr'][0]['validated_currency_id'] = !empty($project['ProjectAmr'][0]['validated_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['validated_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['engaged_currency_id'] = !empty($project['ProjectAmr'][0]['engaged_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['engaged_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['forecasted_currency_id'] = !empty($project['ProjectAmr'][0]['forecasted_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['forecasted_currency_id']] : '';
                $projects[$key]['ProjectAmr'][0]['variance_currency_id'] = !empty($project['ProjectAmr'][0]['variance_currency_id']) ? $currency_name[$project['ProjectAmr'][0]['variance_currency_id']] : '';
            }
            if (empty($project['ProjectAmr'][0]['project_amr_risk_information'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectRisk'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectRisk'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_risk_information'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            if (empty($project['ProjectAmr'][0]['project_amr_problem_information'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectIssue'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectIssue'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_problem_information'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            if (empty($project['ProjectAmr'][0]['project_amr_solution'])) {
                if (!empty($logGroups[$project['Project']['id']]) && !empty($logGroups[$project['Project']['id']]['ProjectAmr'])) {
                    $_output = Set::classicExtract($logGroups[$project['Project']['id']]['ProjectAmr'], '{n}.description');
                    $projects[$key]['ProjectAmr'][0]['project_amr_solution'] = empty($_output) ? '' : array_shift($_output);
                }
            }
            // }
            //budget syns
            if (!empty($budgetSyns[$dx['id']]))
                $projects[$key]['ProjectBudgetSyn'][0] = $budgetSyns[$dx['id']];
            $engagedErro = 0;
            $activityId = !empty($listProjectLinked[$dx['id']]) ? $listProjectLinked[$dx['id']] : 0;
            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $id => $val) {
                    $reals = !empty($employees[$id]['tjm']) ? (float) str_replace(',', '.', $employees[$id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_engaged'] = $engagedErro;
            // internal cost
            // remain = md_engaged va comsumed = md_validated
            $consumed = !empty($getDataFromTasks[$dx['id']]['md_validated']) ? $getDataFromTasks[$dx['id']]['md_validated'] : 0;
            $remain = !empty($getDataFromTasks[$dx['id']]['md_engaged']) ? $getDataFromTasks[$dx['id']]['md_engaged'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_engaged_md'] = $consumed;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] = $consumed + $remain;
            $_average = !empty($getAverages[$dx['id']]) ? $getAverages[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain'] = round($remain * $_average, 2);
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast'] = round($engagedErro + ($remain * $_average), 2);
            $internalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget'] : 0;
            $internalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecast'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_var'] = ($internalBudget == 0) ? '-100%' : round((($internalForecast / $internalBudget) - 1) * 100, 2) . '%';
            $internalRemain = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_remain'] : 0;
            $internalForecastManday = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_forecasted_man_day'] : 0;
            //external cost
            $externalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_budget'] : 0;
            $externalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_forecast'] : 0;
            $externalOrdered = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_ordered']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_ordered'] : 0;
            $externalRemain = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_remain']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_remain'] : 0;
            $externalManday = !empty($projects[$key]['ProjectBudgetSyn'][0]['external_costs_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['external_costs_man_day'] : 0;
            // total cost
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget'] = $internalBudget + $externalBudget;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast'] = $internalForecast + $externalForecast;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_engaged'] = $engagedErro + $externalOrdered;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_remain'] = $internalRemain + $externalRemain;
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_man_day'] = $internalForecastManday + $externalManday;
            $totalForecast = !empty($projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast']) ? $projects[$key]['ProjectBudgetSyn'][0]['total_costs_forecast'] : 0;
            $totalBudget = !empty($projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget']) ? $projects[$key]['ProjectBudgetSyn'][0]['total_costs_budget'] : 0;
            $totalVar = ($totalBudget == 0) ? '-100%' : round((($totalForecast / $totalBudget) - 1) * 100, 2) . '%';
            $projects[$key]['ProjectBudgetSyn'][0]['total_costs_var'] = $totalVar;
            //assign pc and employee
            $tWorkload = !empty($getDataFromTasks[$dx['id']]['workload']) ? $getDataFromTasks[$dx['id']]['workload'] : 0;
            $tOverload = !empty($getDataFromTasks[$dx['id']]['overload']) ? $getDataFromTasks[$dx['id']]['overload'] : 0;
            ;
            $assgnPc = !empty($assignProfitCenters[$dx['id']]) ? $assignProfitCenters[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['assign_to_profit_center'] = ($tWorkload == 0) ? '0%' : ((round(($assgnPc / $tWorkload) * 100, 2) > 100) ? '100%' : round(($assgnPc / $tWorkload) * 100, 2) . '%');
            $assgnEmploy = !empty($assignEmployees[$dx['id']]) ? $assignEmployees[$dx['id']] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['assign_to_employee'] = ($tWorkload == 0) ? '0%' : ((round(($assgnEmploy / $tWorkload) * 100, 2) > 100) ? '100%' : round(($assgnEmploy / $tWorkload) * 100, 2) . '%');
            $projectID = $dx['id'];
            $projects[$key]['ProjectBudgetSyn'][0]['workload'] = $tWorkload;
            $projects[$key]['ProjectBudgetSyn'][0]['overload'] = $tOverload;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . $currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 1)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 2)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['workload_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 3)]) ? $consumedAndWorkloadForActivities[$projectID]['workload_' . ($currentYears + 3)] : 0;

            $projects[$key]['ProjectBudgetSyn'][0]['consumed_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . $currentYears]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_last_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_one_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 1)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_two_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 2)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['consumed_next_thr_y'] = !empty($consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 3)]) ? $consumedAndWorkloadForActivities[$projectID]['consumed_' . ($currentYears + 3)] : 0;

            $projects[$key]['ProjectBudgetSyn'][0]['provisional_y'] = !empty($provisionals[$projectID]['provisional_' . $currentYears]) ? $provisionals[$projectID]['provisional_' . $currentYears] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_one_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 1)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_two_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 2)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_last_thr_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears - 3)]) ? $provisionals[$projectID]['provisional_' . ($currentYears - 3)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_one_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 1)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 1)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_two_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 2)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 2)] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_next_thr_y'] = !empty($provisionals[$projectID]['provisional_' . ($currentYears + 3)]) ? $provisionals[$projectID]['provisional_' . ($currentYears + 3)] : 0;
            $totalInter = !empty($internalBudgets[$projectID]) ? $internalBudgets[$projectID] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] = $totalInter;

            //$totalInter = !empty($projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day']) ? $projects[$key]['ProjectBudgetSyn'][0]['internal_costs_budget_man_day'] : 0;
            $projects[$key]['ProjectBudgetSyn'][0]['provisional_budget_md'] = $totalInter + $externalManday;
            if (!empty($finances[$projectID])) {
                $totalBudgetInv = $totalAvanInv = $totalBudgetFon = $totalAvanFon = 0;
                $percentYearInvs = $percentYearFons = array();
                foreach ($finances[$projectID] as $_key => $fins) {
                    $projects[$key]['ProjectFinancePlus'][0][$_key] = $fins;
                    if (!empty($_key)) {
                        $_key = explode('_', $_key);
                        if (!empty($_key[0]) && $_key[0] == 'inv') {
                            if (!isset($percentYearInvs[$_key[2]][$_key[1]])) {
                                $percentYearInvs[$_key[2]][$_key[1]] = 0;
                            }
                            $percentYearInvs[$_key[2]][$_key[1]] += $fins;
                            if (!empty($_key[1]) && $_key[1] == 'budget') {
                                $totalBudgetInv += $fins;
                            } else {
                                $totalAvanInv += $fins;
                            }
                        } else {
                            if (!isset($percentYearFons[$_key[2]][$_key[1]])) {
                                $percentYearFons[$_key[2]][$_key[1]] = 0;
                            }
                            $percentYearFons[$_key[2]][$_key[1]] += $fins;
                            if (!empty($_key[1]) && $_key[1] == 'budget') {
                                $totalBudgetFon += $fins;
                            } else {
                                $totalAvanFon += $fins;
                            }
                        }
                    }
                }
                if (!empty($percentYearInvs)) {
                    foreach ($percentYearInvs as $year => $percentYearInv) {
                        $bud = !empty($percentYearInv['budget']) ? $percentYearInv['budget'] : 0;
                        $ava = !empty($percentYearInv['avancement']) ? $percentYearInv['avancement'] : 0;
                        $per = ($bud == 0 ) ? 0 : round($ava / $bud * 100, 2);
                        if ($per > 100) {
                            $per = 100;
                        } elseif ($per < 0) {
                            $per = 0;
                        }
                        $projects[$key]['ProjectFinancePlus'][0]['inv_percent_' . $year] = round($per, 2);
                    }
                }
                if (!empty($percentYearFons)) {
                    foreach ($percentYearFons as $year => $percentYearFon) {
                        $bud = !empty($percentYearFon['budget']) ? $percentYearFon['budget'] : 0;
                        $ava = !empty($percentYearFon['avancement']) ? $percentYearFon['avancement'] : 0;
                        $per = ($bud == 0 ) ? 0 : round($ava / $bud * 100, 2);
                        if ($per > 100) {
                            $per = 100;
                        } elseif ($per < 0) {
                            $per = 0;
                        }
                        $projects[$key]['ProjectFinancePlus'][0]['fon_percent_' . $year] = round($per, 2);
                    }
                }
                $totalPercentInv = ($totalBudgetInv == 0) ? 0 : $totalAvanInv / $totalBudgetInv * 100;
                if ($totalPercentInv > 100) {
                    $totalPercentInv = 100;
                } elseif ($totalPercentInv < 0) {
                    $totalPercentInv = 0;
                }
                $totalPercentFon = ($totalBudgetFon == 0) ? 0 : $totalAvanFon / $totalBudgetFon * 100;
                if ($totalPercentFon > 100) {
                    $totalPercentFon = 100;
                } elseif ($totalPercentFon < 0) {
                    $totalPercentFon = 0;
                }
                $projects[$key]['ProjectFinancePlus'][0]['inv_budget'] = !empty($totalBudgetInv) ? $totalBudgetInv : '';
                $projects[$key]['ProjectFinancePlus'][0]['inv_avancement'] = !empty($totalAvanInv) ? $totalAvanInv : '';
                $projects[$key]['ProjectFinancePlus'][0]['inv_percent'] = !empty($totalPercentInv) ? round($totalPercentInv, 2) : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_budget'] = !empty($totalBudgetFon) ? $totalBudgetFon : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_avancement'] = !empty($totalAvanFon) ? $totalAvanFon : '';
                $projects[$key]['ProjectFinancePlus'][0]['fon_percent'] = !empty($totalPercentFon) ? round($totalPercentFon, 2) : '';
            }
        }
        $this->set(compact('fieldset', 'projects', 'addInternalMD', 'addExternalMD', 'datasets', 'projectPhases', 'financeFields'));
        $this->layout = '';
    }

    /**
     * export_vision
     *
     * @return void
     * @access public
     */
    function export_vision() {
        $this->layout = false;
        if (!empty($this->data['Export']) && !empty($this->data['Export']['rows'])) {
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);

            $tmpFile = TMP . 'page_' . time() . '.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));

            list($_width, $_height) = getimagesize($tmpFile);

            $image = imagecreatefrompng($tmpFile);
            $crop = imagecreatetruecolor($_width - 28, $height);

            imagecopy($crop, $image, 0, 0, 28, 220, $_width, $height);

            imagepng($crop, $tmpFile);
            $this->set(compact('tmpFile', 'height', 'rows'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * projects_vision
     * Project vision
     * @return void
     * @access public
     */
    function projects_vision($category = 1, $list = null) {
        $conditions = array();
        if ($list !== null) {
            $list = explode('-', $list);
            $conditions = array('Project.id' => $list);
        } else {
            $default = array(
                'ProjectProjectAmrProgramId_port' => '',
                'ProjectProjectAmrSubProgramId_port' => '',
                'ProjectProjectManagerId_port' => '',
                'ProjectProjectStatusId_port' => ''
            );
            $conditions = array_intersect_key(array_merge($default, $this->params['url']), $default);

            if (empty($conditions['ProjectProjectStatusId_port'])) {
                $conditions['ProjectProjectStatusId_port'] = $category;
            }
            $keys = array(
                'project_amr_program_id',
                'project_amr_sub_program_id',
                'project_manager_id',
                'category'
            );
            $conditions = Set::filter(array_combine($keys, $conditions));
        }
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $limit = isset($this->params['url']['limit']) ? (int) $this->params['url']['limit'] : 10;
        $projects = array();
        $_fields = array(
            'Project.project_name',
            'Project.start_date',
            'Project.end_date',
            'Project.planed_end_date',
            'Project.category',
            'ProAmr.amr_program',
        );
        $_joins = array(
            array(
                'table' => 'project_amr_programs',
                'alias' => 'ProAmr',
                'type' => 'LEFT',
                'foreignKey' => 'employee_id',
                'conditions' => array(
                    'ProAmr.id = Project.project_amr_program_id'
                )
            )
        );
        $this->paginate = array(
            //'conditions' => array('Project.category' => $category),
            'conditions' => $conditions,
            'joins' => $_joins,
            'order' => array('ProAmr.amr_program', 'Project.project_name'),
            'contain' => array('ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ), 'ProjectPhase' => array('name', 'color'))), 'limit' => $limit, 'fields' => $_fields);
        if ($this->is_sas)
            $projects = $this->Project->find('all', $this->paginate);
        else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array('recursive' => -1, 'fields' => array('id', 'id'), 'conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
                $projects = $this->paginate('Project', array('Project.company_id' => $sub_companies));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('list', array('recursive' => -1, 'fields' => array('id', 'project_id'), 'conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $projects = $this->paginate('Project', array('Project.id' => $employee_project_teams));
            }
        }
        $display = isset($this->params['url']['display']) ? (int) $this->params['url']['display'] : 0;
        $this->set(compact('projects', 'limit', 'display'));
    }

    /**
     * projects_list
     * Projects list
     * @return void
     * @access public
     */// Function này cần được xóa sau khi hoàn thành chức năng tạo project bằng popup. Ticket #375
    function projects_list() {
        $this->Project->recursive = 1;
        $this->Project->Behaviors->attach('Containable');
        $projects = array();
        if ($this->is_sas) {
            $projects = $this->Project->find('all', array(
                'contain' => array('Company'),
                'conditions' => array('Project.category' => 4),
                'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')));
        } else {
            $employee_info = $this->Session->read("Auth.employee_info");
            $my_employee_id = $employee_info["Employee"]["id"];
            $role = $employee_info["Role"]["name"];
            $company_id = $employee_info["Company"]["id"];
            // if admin role then list all projects of companies & sub companies
            if ($role != "conslt") {
                $sub_companies = $this->Project->Company->find('list', array(
                    'conditions' => array('OR' => array(
                            'Company.id' => $this->employee_info['Company']['id'],
                            'Company.parent_id' => $this->employee_info['Company']['id']))));
                $company_ids = array();
                foreach ($sub_companies as $comp_id => $company_name) {
                    $company_ids[] = $comp_id;
                }

                $projects = $this->Project->find('all', array(
                    'conditions' => array('Project.company_id' => $company_ids, 'Project.category' => 4),
                    'contain' => array('Company'),
                    'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')));
            } else {
                $employee_project_teams = $this->Project->ProjectTeam->find('all', array('conditions' => array('Project.company_id' => $company_id,
                        'ProjectTeam.employee_id' => $my_employee_id)));
                $project_ids = array();
				
                foreach ($employee_project_teams as $employee_project_team) {
                    $project_ids = $employee_project_team["ProjectTeam"]["project_id"];
                }
                $projects = $this->Project->find('all', array(
                    'conditions' => array('Project.id' => $project_ids, 'Project.category' => 4),
                    'contain' => array('Company'),
                    'fields' => array('Project.id', 'Project.project_name', 'Project.start_date', 'Project.end_date', 'Company.company_name')
                ));
            }
        }
        $this->set('projects', $projects);
    }
	
	private function copy_task_attachment($filename){
		$result = false;
		if( empty($filename) ) return false;
		$company = $this->employee_info['Company']['id'];
        $path = FILES . 'projects' . DS . 'project_tasks' . DS . $company . DS;
		$old_file = new File($path.$filename);
		if( !$old_file->exists() ) return false;
		$file_info = $old_file->info();
		$continue = 1;
		$new_name = '';
		while($continue){
			$new_name = $file_info['filename'].'('.$continue.').'.$file_info['extension'];
			if( file_exists($path.$new_name) ){
				$continue++;
			}else{
				$continue = 0;
			}
		}
		if( $old_file->copy($path.$new_name)) $result = $new_name;
		return $result;
	}

    /**
     * duplicate
     *
     * @return void
     * @access public
     * Khi update code function nay, nho update cac function lien quan
	 * projects/duplicate
	 * projects/duplicateProject
	 * project_importers/saveProjectFromModel
	 */
    function duplicate() {
		if(($this->employee_info['Role']['name'] != 'admin') && ($this->employee_info['Employee']['create_a_project'] != '1') ){
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'projects', 'action' => 'index'));
		}
        $this->autoRender = false;
        $data = $this->data['Project']['duplicate'];
		//tach chuoi $data thanh mang.
        $n = explode("|", $data, 2);
        if (!empty($data)) {
            //Copy record of Project
            $new_record = $this->Project->findById($n[0]);
            $this->Project->id = $n[0];
            $this->Project->saveField('copy_number', $new_record['Project']['copy_number'] + 1);
            $num_co = $new_record['Project']['copy_number'] + 1;

            if ($num_co >= 2)
                $num_co = ' (' . $num_co . ')';
            else
                $num_co = '';
            $oldId = $new_record['Project']['id'];
            $new_record['Project']['project_copy'] = 1;
            $new_record['Project']['project_copy_id'] = $new_record['Project']['id'];
            $new_record['Project']['category'] = 2;  //when copy project, default project status = 2 (Opportunity).
            unset($new_record['Project']['id']);
            $new_record['Project']['project_name'] = 'Copy of ' . $n[1] . $num_co;
            $this->Project->create();
            $new_record['Project']['copy_number'] = 0;
            $new_record['Project']['created'] = time();
            $new_record['Project']['updated'] = time();
            $new_record['Project']['last_modified'] = time();
            $new_record['Project']['project_code_1'] = null;
            if (empty($this->viewVars['canModified'])) {
                $employee = $this->Session->read("Auth.employee_info");
                $new_record['Project']['project_manager_id'] = $employee["Employee"]["id"];
            }
            unset($new_record['Project']['activity_id']);
            $this->Project->save($new_record);
            $id_duplicate = $this->Project->getLastInsertID();
            //Copy record of ProjectTeam
            if (!empty($new_record['ProjectTeam'])) {
                foreach ($new_record['ProjectTeam'] as $pteam) {
                    $pteam['project_id'] = $id_duplicate;
                    $team_id = $pteam['id'];
                    unset($pteam['id']);
                    $this->ProjectTeam->create();
                    $this->ProjectTeam->save($pteam);
                    $pteam_id = $this->ProjectTeam->getLastInsertID();

                    $pteam_cur_id = $team_id;
                    $this->ProjectFunctionEmployeeRefer->recursive = -1;
                    $PFERs = $this->ProjectFunctionEmployeeRefer->find('all', array(
                        'conditions' => array('project_team_id' => $pteam_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectFunctionEmployeeRefer']['id']);
                            $PFER['ProjectFunctionEmployeeRefer']['project_team_id'] = $pteam_id;
                            $this->ProjectFunctionEmployeeRefer->create();
                            $this->ProjectFunctionEmployeeRefer->save($PFER);
                        }
                    }
                }
            }
			//Copy record of ProjectPart
			$id_part_new = 0;
			$this->loadModel('ProjectPart');
			$newPart = array();
			if (!empty($new_record['ProjectPart'])) {
                foreach ($new_record['ProjectPart'] as $pparts) {
					$id_part_old = $pparts['id'];
                    $pparts['project_id'] = $id_duplicate;
                    unset($pparts['id']);
                    $this->ProjectPart->create();
                    $this->ProjectPart->save($pparts);
					$id_part_new = $this->ProjectPart->getLastInsertID();
					if (!empty($new_record['ProjectPhasePlan'])) {
						foreach ($new_record['ProjectPhasePlan'] as $pphase) {
							if($pphase['project_part_id'] == $id_part_old){
								$newPart[$pphase['id']] = $id_part_new;
							}
						}
					}
                }
            }
            //Copy record of ProjectMilestone
			$milestone_refer = array();
            if (!empty($new_record['ProjectMilestone'])) {
                foreach ($new_record['ProjectMilestone'] as $pmiles) {
                    $pmiles['project_id'] = $id_duplicate;
					$oldMilesId = $pmiles['id'];
                    unset($pmiles['id']);
                    $this->ProjectMilestone->create();
                    if($this->ProjectMilestone->save($pmiles)){
						$newMilesId = $this->ProjectMilestone->getLastInsertID();
						$milestone_refer[$oldMilesId] = $newMilesId;
					}
                }
            }
            //Copy record of ProjectPhasePlan
            if (!empty($new_record['ProjectPhasePlan'])) {
                $project_planed_phase_id_old = $listPhasePredes = $listPhaseIds = array();
                foreach ($new_record['ProjectPhasePlan'] as $pphase) {
                    $pphase['project_id'] = $id_duplicate;
                    $ProjectPhasePlan_id_old = $pphase['id'];
                    $oldPhaseId = $pphase['id'];
                    if (!empty($pphase['predecessor'])) {
                        $listPhasePredes[$oldPhaseId] = $pphase['predecessor'];
                    }
					
					if(!empty($newPart)){
						foreach($newPart as $key => $value){
							if($pphase['id'] == $key){
								$pphase['project_part_id'] = $value;
							}
						}
					}
					
                    unset($pphase['id']);
                    $this->ProjectPhasePlan->create();
                    $this->ProjectPhasePlan->save($pphase);
                    $newPhaseId = $this->ProjectPhasePlan->getLastInsertID();
                    $listPhaseIds[$oldPhaseId] = $newPhaseId;
                    $project_planed_phase_id_old[$ProjectPhasePlan_id_old] = $this->ProjectPhasePlan->getLastInsertID();
                }
                if (!empty($listPhasePredes)) {
                    foreach ($listPhasePredes as $old => $pre) {
                        $idUpdate = !empty($listPhaseIds[$old]) ? $listPhaseIds[$old] : 0;
                        $this->ProjectPhasePlan->id = $idUpdate;
                        $saved['predecessor'] = !empty($listPhaseIds[$pre]) ? $listPhaseIds[$pre] : '';
                        $this->ProjectPhasePlan->save($saved);
                    }
                }
            }
            //Copy record of ProjectTask
            //co project_id cu, get tat ca  cac task co project_planed_phase_id giu lai
            $company_id = $this->employee_info['Company']['id'];
            $this->loadModels('ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'NctWorkload', 'ProjectTaskTxt', 'ProjectTaskAttachment');
            $listEmployeeActive = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'actif' => 1,
                    'OR' => array(
                        'start_date IS NULL',
                        'start_date' => '0000-00-00',
                        'start_date <=' => date('Y-m-d', time())
                    ),
                    'AND' => array(
                        'OR' => array(
                            'end_date IS NULL',
                            'end_date' => '0000-00-00',
                            'end_date >=' => date('Y-m-d', time())
                        )
                    )
                ),
                'fields' => array('id', 'id')
            ));
            $listProfitCenterActive = $this->ProfitCenter->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                ),
                'fields' => array('id', 'id')
            ));
            if (!empty($new_record['ProjectTask'])) {
                $listTaskIds = $listTaskPredes = array();
                $old_comment = array();
				$all_tasks = array();
				/* Parent task duoc copy truoc. Sau do la cac task con lai */
				$tasks = $new_record['ProjectTask'];
				$tasks = Set::combine( $new_record['ProjectTask'], '{n}.id', '{n}');
				$parent_tasks = Set::classicExtract( $tasks, '{n}.parent_id');
				$parent_tasks = array_unique( $parent_tasks);
				foreach( $parent_tasks as $t_id){
					if( !empty($t_id))$all_tasks[] = $tasks[$t_id];
					unset($tasks[$t_id]);
				}
				$all_tasks = array_merge($all_tasks, $tasks);
                foreach ($all_tasks as $ptask) {
                    $ptask['project_id'] = $id_duplicate;
                    $ptask['project_planed_phase_id'] = $project_planed_phase_id_old[$ptask['project_planed_phase_id']];
                    $oldTaskId = $ptask['id'];
                    if (!empty($ptask['parent_id'])) {
                        $ptask['parent_id'] = !empty($listTaskIds[$ptask['parent_id']]) ? $listTaskIds[$ptask['parent_id']] : 0;
                    }
                    if (!empty($ptask['predecessor'])) {
                        $listTaskPredes[$oldTaskId] = $ptask['predecessor'];
                    }
                    $projectTaskEmpRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        ),
                        'fields' => array('reference_id', 'project_task_id', 'is_profit_center', 'estimated')
                    ));
                    $projectTaskTxts = $this->ProjectTaskTxt->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        )
                    ));
                    $projectTaskAttachments = $this->ProjectTaskAttachment->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'task_id' => $oldTaskId
                        )
                    ));

                    $listNCT = $this->NctWorkload->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        ),
                        'fields' => array('task_date', 'estimated', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date', 'end_date'),
                    ));
                    unset($ptask['task_completed']);
                    unset($ptask['task_assign_to']);
                    unset($ptask['task_real_end_date']);
                    unset($ptask['initial_estimated']);
                    unset($ptask['overload']);
                    unset($ptask['id']);
                    unset($ptask['initial_task_start_date']);
                    unset($ptask['initial_task_end_date']);
                    unset($ptask['manual_consumed']);
                    unset($ptask['manual_overload']);
                    unset($ptask['created']);
                    unset($ptask['updated']);
                    $old_comment = $ptask['text_1'];
                    unset($ptask['text_1']); // Remove comment
                    $old_attachment = $ptask['attachment'];
                    unset($ptask['attachment']); // Remove attachment
                    unset($ptask['special_consumed']);

                    // Edit comment info
                    $ptask['text_updater'] = null;
                    $ptask['text_time'] = null;
					// Milestone reference
					if(!empty($milestone_refer) && !empty($ptask['milestone_id']) && !empty($milestone_refer[$ptask['milestone_id']])){
						$ptask['milestone_id'] = $milestone_refer[$ptask['milestone_id']];
					}
                    $this->ProjectTask->create();
                    $_new_task = $this->ProjectTask->save($ptask);
                    // Copy comment
                    $_task_comment = $this->ProjectTaskTxt->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'project_task_id' => $oldTaskId
                        ),
                    ));
                    $newTaskId = $this->ProjectTask->getLastInsertID();
                    $listTaskIds[$oldTaskId] = $newTaskId;
                    $estimatedOfNewTask = 0;
                    foreach ($projectTaskEmpRefers as $key => $projectTaskEmpRefer) {
                        $dx = $projectTaskEmpRefer['ProjectTaskEmployeeRefer'];
                        if (($dx['is_profit_center'] == 1 && in_array($dx['reference_id'], $listProfitCenterActive)) || ($dx['is_profit_center'] == 0 && in_array($dx['reference_id'], $listEmployeeActive))) {
                            $this->ProjectTaskEmployeeRefer->create();
                            $this->ProjectTaskEmployeeRefer->save(array(
                                'reference_id' => $dx['reference_id'],
                                'project_task_id' => $newTaskId,
                                'is_profit_center' => $dx['is_profit_center'],
                                'estimated' => $dx['estimated']
                            ));
                            $estimatedOfNewTask += !empty($dx['estimated']) ? $dx['estimated'] : 0;
                        }
                    }
                    $this->ProjectTask->id = $newTaskId;
                    $this->ProjectTask->save(array('estimated' => $estimatedOfNewTask));

                    // Copy comment
                    if (!empty($old_comment)) {
                        $this->ProjectTaskTxt->create();
                        $this->ProjectTaskTxt->save(array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'project_task_id' => $newTaskId,
                            'comment' => $old_comment,
                            'created' => date('Y-m-d H:i:s')
                        ));
                    }
                    foreach ($projectTaskTxts as $key => $projectTaskTxt) {
                        $task_txt = $projectTaskTxt['ProjectTaskTxt'];
                        $this->ProjectTaskTxt->create();
                        $this->ProjectTaskTxt->save(array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'project_task_id' => $newTaskId,
                            'comment' => $task_txt['comment'],
                            'created' => date('Y-m-d H:i:s')
                        ));
                    }
                    // copy Attachments
                    if (!empty($old_attachment)) {
                        $_is_file = 0;
                        $file_attachment = $url_attachment = array();
                        $file_attachment = explode('file:', $old_attachment, 2);
                        $url_attachment = explode('url:', $old_attachment, 2);
                        $old_attachment = '';
                        if ((!$url_attachment[0]) && isset($url_attachment[1])) {
                            $old_attachment = $url_attachment[1];
                        }
                        if ((!$file_attachment[0]) && isset($file_attachment[1])) {
                            $old_attachment = $file_attachment[1];
                            $_is_file = 1;
                        }
                        $new_attachment = '';
                        if ($_is_file && $old_attachment) {
                            // Copy file sang new task theo định dạng filename(<num>).ext
                            $new_attachment = $this->copy_task_attachment($old_attachment);
                        }
                        $new_attachment = !empty($new_attachment) ? $new_attachment : (!$_is_file ? $old_attachment : '');
                        if (!empty($new_attachment)) {
                            $this->ProjectTaskAttachment->create();
                            $this->ProjectTaskAttachment->save(array(
                                'project_id' => $id_duplicate,
                                'task_id' => $newTaskId,
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'attachment' => $new_attachment,
                                'created' => time(),
                                'updated' => time(),
                                'is_file' => $_is_file,
                                'is_https' => 0,
                            ));
                        }
                    }
                    foreach ($projectTaskAttachments as $key => $projectTaskAttachment) {
                        $task_att = $projectTaskAttachment['ProjectTaskAttachment'];
                        $new_attachment = '';
                        if ($task_att['is_file'] && !empty($task_att['attachment'])) {
                            // Copy file sang new task theo định dạng filename(<num>).ext
                            $new_attachment = $this->copy_task_attachment($task_att['attachment']);
                        }
                        if (!$task_att['is_file']) {
                            $new_attachment = $task_att['attachment'];
                        }
                        if ($new_attachment) {
                            $this->ProjectTaskAttachment->create();
                            $this->ProjectTaskAttachment->save(array(
                                'project_id' => $id_duplicate,
                                'task_id' => $newTaskId,
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'attachment' => $new_attachment,
                                'created' => time(),
                                'updated' => time(),
                                'is_file' => $task_att['is_file'],
                                'is_https' => $task_att['is_https'],
                            ));
                        }
                    }
                    // End copy Attachments

                    if (!empty($listNCT)) {
                        foreach ($listNCT as $key => $value) {
                            if ($ptask['is_nct']) {
                                $nct_fields = array(
                                    'task_date' => $value['NctWorkload']['task_date'],
                                    'estimated' => $value['NctWorkload']['estimated'],
                                    'reference_id' => $value['NctWorkload']['reference_id'],
                                    'is_profit_center' => $value['NctWorkload']['is_profit_center'],
                                    'project_task_id' => $newTaskId,
                                    'activity_task_id' => 0,
                                    'group_date' => $value['NctWorkload']['group_date'],
                                    'end_date' => $value['NctWorkload']['end_date'],
                                );
                                $this->NctWorkload->create();
                                $this->NctWorkload->save($nct_fields);
                            }
                        }
                    }
                }
                if (!empty($listTaskPredes)) {
                    foreach ($listTaskPredes as $old => $pre) {
                        $idUpdate = !empty($listTaskIds[$old]) ? $listTaskIds[$old] : 0;
                        $this->ProjectTask->id = $idUpdate;
                        $saved['predecessor'] = !empty($listTaskIds[$pre]) ? $listTaskIds[$pre] : '';
                        $this->ProjectTask->save($saved);
                    }
                }
            }
            /**
             * Save project_employee_manager
             * Lay danh sach project_employee_manager da tao
             */
            $this->loadModel('ProjectEmployeeManager');
            $dataManagers = $this->ProjectEmployeeManager->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $oldId
                )
            ));
            if (!empty($dataManagers)) {
                $_dataManagers = array();
                foreach ($dataManagers as $value) {
                    $_dataManagers[] = array(
                        'project_manager_id' => $value['ProjectEmployeeManager']['project_manager_id'],
                        // 'is_backup' => $value['ProjectEmployeeManager']['is_backup'],
                        'project_id' => $id_duplicate,
                        'type' => $value['ProjectEmployeeManager']['type'],
                        'activity_id' => $value['ProjectEmployeeManager']['activity_id']
                    );
                }
                if (!empty($_dataManagers)) {
                    $this->ProjectEmployeeManager->create();
                    $this->ProjectEmployeeManager->saveAll($_dataManagers);
                }
            }

            //Copy record of ProjectRisk
            if (!empty($new_record['ProjectRisk'])) {
                foreach ($new_record['ProjectRisk'] as $prisk) {
                    $prisk['project_id'] = $id_duplicate;
                    unset($prisk['id']);
                    $this->ProjectRisk->create();
                    $this->ProjectRisk->save($prisk);
                }
            }

            //Copy record of ProjectIssue
            if (!empty($new_record['ProjectIssue'])) {
                foreach ($new_record['ProjectIssue'] as $pissue) {
                    $pissue['project_id'] = $id_duplicate;
                    unset($pissue['id']);
                    $this->ProjectIssue->create();
                    $this->ProjectIssue->save($pissue);
                }
            }

            //Copy record of ProjectDecision
            if (!empty($new_record['ProjectDecision'])) {
                foreach ($new_record['ProjectDecision'] as $pdecision) {
                    $pdecision['project_id'] = $id_duplicate;
                    unset($pdecision['id']);
                    $this->ProjectDecision->create();
                    $this->ProjectDecision->save($pdecision);
                }
            }

            //Copy record of ProjectEvolution
            if (!empty($new_record['ProjectLivrable'])) {
                foreach ($new_record['ProjectLivrable'] as $plivra) {
                    $plivra['project_id'] = $id_duplicate;
                    $plivra_id = $plivra['id'];
                    unset($plivra['id']);
                    $this->ProjectLivrable->create();
                    $this->ProjectLivrable->save($plivra);

                    $plivra_new_id = $this->ProjectLivrable->getLastInsertID();
                    $plivra_cur_id = $plivra_id;
                    $this->ProjectLivrableActor->recursive = -1;
                    $PFERs = $this->ProjectLivrableActor->find('all', array(
                        'conditions' => array('ProjectLivrableActor.project_livrable_id' => $plivra_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectLivrableActor']['id']);
                            $PFER['ProjectLivrableActor']['project_livrable_id'] = $plivra_new_id;
                            $PFER['ProjectLivrableActor']['project_id'] = $id_duplicate;
                            $this->ProjectLivrableActor->create();
                            $this->ProjectLivrableActor->save($PFER);
                        }
                    }
                }
            }

            //Copy record of ProjectEvolution
            if (!empty($new_record['ProjectEvolution'])) {
                foreach ($new_record['ProjectEvolution'] as $pevolu) {
                    $pevolu['project_id'] = $id_duplicate;
                    $pevolu_id = $pevolu['id'];
                    unset($pevolu['id']);
                    $this->ProjectEvolution->create();
                    $this->ProjectEvolution->save($pevolu);
                    $pevolu_new_id = $this->ProjectEvolution->getLastInsertID();

                    $pevolu_cur_id = $pevolu_id;
                    $this->ProjectEvolutionImpactRefer->recursive = -1;
                    $PFERs = $this->ProjectEvolutionImpactRefer->find('all', array(
                        'conditions' => array('project_evolution_id' => $pevolu_cur_id)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectEvolutionImpactRefer']['id']);
                            $PFER['ProjectEvolutionImpactRefer']['project_evolution_id'] = $pevolu_new_id;
                            $PFER['ProjectEvolutionImpactRefer']['project_id'] = $id_duplicate;
                            $this->ProjectEvolutionImpactRefer->create();
                            $this->ProjectEvolutionImpactRefer->save($PFER);
                        }
                    }
                }
            }

            //Copy record of ProjectAmr
            if (!empty($new_record['ProjectAmr'])) {
                foreach ($new_record['ProjectAmr'] as $pamr) {
                    $pamr['project_id'] = $id_duplicate;
                    unset($pamr['id']);
                    $this->ProjectAmr->create();
                    $this->ProjectAmr->save($pamr);
                }
            }

            //Copy record of Created value
            if (!empty($new_record['ProjectCreatedVal'])) {
                foreach ($new_record['ProjectCreatedVal'] as $pcreated) {
                    $pcreated['project_id'] = $id_duplicate;
                    unset($pcreated['id']);
                    $this->ProjectCreatedVal->create();
                    $this->ProjectCreatedVal->save($pcreated);
                }
            }
            //Copy Budget Internal Cost.
            if (!empty($new_record['ProjectBudgetInternal'])) {
                foreach ($new_record['ProjectBudgetInternal'] as $internal) {
                    $internal['project_id'] = $id_duplicate;
                    unset($internal['id']);
                    $internal['activity_id'] = 0;
                    $this->ProjectBudgetInternal->create();
                    $this->ProjectBudgetInternal->save($internal);
                }
            }
            //Copy Budget Internal Cost Detail.
            if (!empty($new_record['ProjectBudgetInternalDetail'])) {
                foreach ($new_record['ProjectBudgetInternalDetail'] as $internalDetail) {
                    $internalDetail['project_id'] = $id_duplicate;
                    unset($internalDetail['id']);
                    $internalDetail['activity_id'] = 0;
                    $this->ProjectBudgetInternalDetail->create();
                    $this->ProjectBudgetInternalDetail->save($internalDetail);
                }
            }

            //Copy Budget External Cost.
            if (!empty($new_record['ProjectBudgetExternal'])) {
                foreach ($new_record['ProjectBudgetExternal'] as $external) {
                    $external['project_id'] = $id_duplicate;
                    unset($external['id']);
                    $external['activity_id'] = 0;
                    $this->ProjectBudgetExternal->create();
                    $this->ProjectBudgetExternal->save($external);
                }
            }

            //Copy Budget External Cost.
            if (!empty($new_record['ProjectBudgetSale'])) {
                foreach ($new_record['ProjectBudgetSale'] as $sale) {
                    $sale['project_id'] = $id_duplicate;
                    $oldSaleId = $sale['id'];
                    unset($sale['id']);
                    $sale['activity_id'] = 0;
                    $this->ProjectBudgetSale->create();
                    $this->ProjectBudgetSale->save($sale);
                    $newSaleId = $this->ProjectBudgetSale->getLastInsertID();

                    $this->ProjectBudgetInvoice->recursive = -1;
                    $PFERs = $this->ProjectBudgetInvoice->find('all', array(
                        'conditions' => array('project_budget_sale_id' => $oldSaleId)));
                    if (!empty($PFERs)) {
                        foreach ($PFERs as $PFER) {
                            unset($PFER['ProjectBudgetInvoice']['id']);
                            $PFER['ProjectBudgetInvoice']['activity_id'] = 0;
                            $PFER['ProjectBudgetInvoice']['project_budget_sale_id'] = $newSaleId;
                            $PFER['ProjectBudgetInvoice']['project_id'] = $id_duplicate;
                            $this->ProjectBudgetInvoice->create();
                            $this->ProjectBudgetInvoice->save($PFER);
                        }
                    }
                }
            }

            //Copy record of Budget Syns
            // if (!empty($new_record['ProjectBudgetSyn'])) {
            // foreach ($new_record['ProjectBudgetSyn'] as $syns) {
            // $syns['project_id'] = $id_duplicate;
            // unset($syns['id']);
            // $syns['activity_id'] = 0;
            // $this->ProjectBudgetSyn->create();
            // $this->ProjectBudgetSyn->save($syns);
            // }
            // }

            $this->Session->setFlash(__('CREATED', true));
            $company_id = $this->employee_info["Company"]["id"];
            $screenDefaults = ClassRegistry::init('Menu')->find('first', array(
                'recursive' => -1,
                'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
            ));
            $ACLController = 'projects';
            $ACLAction = 'edit';
            if (!empty($screenDefaults)) {
                $ACLController = $screenDefaults['Menu']['controllers'];
                $ACLAction = $screenDefaults['Menu']['functions'];
            }
            $this->ProjectTask->staffingSystem($id_duplicate);
            $this->redirect("/projects/your_form/" . $id_duplicate);
        } else
            $this->Session->setFlash(__('KO', true));
    }
	/* Duplicate from Yoir form screen  
	 * Khi update code function nay, nho update cac function lien quan
	 * projects/duplicate
	 * projects/duplicateProject
	 * project_importers/saveProjectFromModel
	 */
	function duplicateProject($project_id) {
		// Copy all your form field
		// This function duplicate all type project (in, opp, arch, mod)
		// Ticket : Duplicate project from yourform screen
		$has_left_employee = false;
		if(!empty($project_id)){
			$new_record = $this->Project->findById($project_id);
			if( !empty( $new_record['Project']['project_manager_id'] ) || !empty($new_record['ProjectEmployeeManager'])){
				$this->_checkRole(false, $project_id);
				$this->Project->id = $project_id;
				$this->Project->saveField('copy_number', $new_record['Project']['copy_number'] + 1);
				$num_co = $new_record['Project']['copy_number'] + 1;
				if ($num_co >= 2)
					$num_co = ' (' . $num_co . ')';
				else
					$num_co = '';
				$oldId = $new_record['Project']['id'];
				$new_record['Project']['project_copy'] = 1;
				$new_record['Project']['project_copy_id'] = $new_record['Project']['id'];
				$new_record['Project']['category'] = 2;  //when copy project, default project status = 2 (Opportunity).
				unset($new_record['Project']['id']);
				$new_record['Project']['project_name'] = 'Copy of ' . $new_record['Project']['project_name'] . $num_co;
				$new_record['Project']['copy_number'] = 0;
				$new_record['Project']['created'] = time();
				$new_record['Project']['updated'] = time();
				$new_record['Project']['last_modified'] = time();
				$new_record['Project']['activated'] = null;
				$new_record['Project']['is_freeze'] = null;
				$new_record['Project']['freeze_by'] = null;
				$new_record['Project']['freeze_time'] = null;
				$new_record['Project']['project_code_1'] = null;
				if (empty($this->viewVars['canModified'])) {
					$employee = $this->Session->read("Auth.employee_info");
					$new_record['Project']['project_manager_id'] = $employee["Employee"]["id"];
				}
				unset($new_record['Project']['activity_id']);
				$this->Project->create();
				$this->Project->save($new_record);
				$id_duplicate = $this->Project->getLastInsertID();
				//Copy record of ProjectTeam
				if (!empty($new_record['ProjectTeam'])) {
					foreach ($new_record['ProjectTeam'] as $pteam) {
						$pteam['project_id'] = $id_duplicate;
						$team_id = $pteam['id'];
						unset($pteam['id']);
						$this->ProjectTeam->create();
						$this->ProjectTeam->save($pteam);
						$pteam_id = $this->ProjectTeam->getLastInsertID();

						$pteam_cur_id = $team_id;
						$this->ProjectFunctionEmployeeRefer->recursive = -1;
						$PFERs = $this->ProjectFunctionEmployeeRefer->find('all', array(
							'conditions' => array('project_team_id' => $pteam_cur_id)));
						if (!empty($PFERs)) {
							foreach ($PFERs as $PFER) {
								unset($PFER['ProjectFunctionEmployeeRefer']['id']);
								$PFER['ProjectFunctionEmployeeRefer']['project_team_id'] = $pteam_id;
								$this->ProjectFunctionEmployeeRefer->create();
								$this->ProjectFunctionEmployeeRefer->save($PFER);
							}
						}
					}
				}
				
				// Copy Part all information
				$id_part_new = 0;
				$this->loadModel('ProjectPart');
				$newPart = array();
				if (!empty($new_record['ProjectPart'])) {
					foreach ($new_record['ProjectPart'] as $pparts) {
						$id_part_old = $pparts['id'];
						$pparts['project_id'] = $id_duplicate;
						unset($pparts['id']);
						$this->ProjectPart->create();
						$this->ProjectPart->save($pparts);
						$id_part_new = $this->ProjectPart->getLastInsertID();
						if (!empty($new_record['ProjectPhasePlan'])) {
							foreach ($new_record['ProjectPhasePlan'] as $pphase) {
								if($pphase['project_part_id'] == $id_part_old){
									$newPart[$pphase['id']] = $id_part_new;
								}
							}
						}
					}
				}
				// Copy phase all information 
				// Copy all  tasks ( NTC also ) with workload , assigned  to , predecessor , but  for statut  = the first statut of the list , start date, end date but DO NOT COPY consumed, message, files of the task 
				
				// Copy milestones only planned date and Validé = NO 
				$milestone_refer = array();
				if (!empty($new_record['ProjectMilestone'])) {
					foreach ($new_record['ProjectMilestone'] as $pmiles) {
						$pmiles['validated'] = 0;
						$pmiles['initial_date'] = null;
						$pmiles['effective_date'] = null;
						$pmiles['milestone_date'] = 0;
						$pmiles['project_id'] = $id_duplicate;
						$oldMilesId = $pmiles['id'];
						unset($pmiles['id']);
						$this->ProjectMilestone->create();
						if($this->ProjectMilestone->save($pmiles)){
							$newMilesId = $this->ProjectMilestone->getLastInsertID();
							$milestone_refer[$oldMilesId] = $newMilesId;
						}
					}
				}
				//Copy record of ProjectAmr
				if (!empty($new_record['ProjectAmr'])) {
					foreach ($new_record['ProjectAmr'] as $pamr) {
						$pamr['project_id'] = $id_duplicate;
						unset($pamr['id']);
						$this->ProjectAmr->create();
						$this->ProjectAmr->save($pamr);
					}
				}

				//Copy record of Created value
				if (!empty($new_record['ProjectCreatedVal'])) {
					foreach ($new_record['ProjectCreatedVal'] as $pcreated) {
						$pcreated['project_id'] = $id_duplicate;
						unset($pcreated['id']);
						$this->ProjectCreatedVal->create();
						$this->ProjectCreatedVal->save($pcreated);
					}
				}

				//Copy record of ProjectListMultiple
				if (!empty($new_record['ProjectListMultiple'])) {
					foreach ($new_record['ProjectListMultiple'] as $pListMulti) {
						$pListMulti['project_id'] = $id_duplicate;
						unset($pListMulti['id']);
						$this->ProjectListMultiple->create();
						$this->ProjectListMultiple->save($pListMulti);
					}
				}
					
				//Copy record of ProjectPhaseCurrent
				if (!empty($new_record['ProjectPhaseCurrent'])) {
					foreach ($new_record['ProjectPhaseCurrent'] as $pPhaseCurrent) {
						$pPhaseCurrent['project_id'] = $id_duplicate;
						unset($pPhaseCurrent['id']);
						$this->ProjectPhaseCurrent->create();
						$this->ProjectPhaseCurrent->save($pPhaseCurrent);
					}
				}
					
				//Copy record of ProjectPhasePlan
				if (!empty($new_record['ProjectPhasePlan'])) {
					$project_planed_phase_id_old = $listPhasePredes = $listPhaseIds = array();
					foreach ($new_record['ProjectPhasePlan'] as $pphase) {
						$pphase['project_id'] = $id_duplicate;
						$ProjectPhasePlan_id_old = $pphase['id'];
						$oldPhaseId = $pphase['id'];
						if (!empty($pphase['predecessor'])) {
							$listPhasePredes[$oldPhaseId] = $pphase['predecessor'];
						}
						
						if(!empty($newPart)){
							foreach($newPart as $key => $value){
								if($pphase['id'] == $key){
									$pphase['project_part_id'] = $value;
								}
							}
						}
						
						unset($pphase['id']);
						$this->ProjectPhasePlan->create();
						$this->ProjectPhasePlan->save($pphase);
						$newPhaseId = $this->ProjectPhasePlan->getLastInsertID();
						$listPhaseIds[$oldPhaseId] = $newPhaseId;
						$project_planed_phase_id_old[$ProjectPhasePlan_id_old] = $this->ProjectPhasePlan->getLastInsertID();
						
					}
					if (!empty($listPhasePredes)) {
						foreach ($listPhasePredes as $old => $pre) {
							$idUpdate = !empty($listPhaseIds[$old]) ? $listPhaseIds[$old] : 0;
							$this->ProjectPhasePlan->id = $idUpdate;
							$saved['predecessor'] = !empty($listPhaseIds[$pre]) ? $listPhaseIds[$pre] : '';
							$this->ProjectPhasePlan->save($saved);
						}
					}
				}
				
				//Copy record of ProjectTask
				//co project_id cu, get tat ca  cac task co project_planed_phase_id giu lai
				$company_id = $this->employee_info['Company']['id'];
				$this->loadModels('ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter', 'NctWorkload', 'ProjectStatus');
				$first_status = $this->ProjectStatus->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'display' => 1,
					),
					'order' => array('weight' => 'ASC'),
					'fields' => array('id')
				));
				$first_status_id = !empty($first_status) ? $first_status['ProjectStatus']['id'] : null;
				$listEmployeeActive = $this->Employee->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'actif' => 1,
						'OR' => array(
							'start_date IS NULL',
							'start_date' => '0000-00-00',
							'start_date <=' => date('Y-m-d', time())
						),
						'AND' => array(
							'OR' => array(
								'end_date IS NULL',
								'end_date' => '0000-00-00',
								'end_date >=' => date('Y-m-d', time())
							)
						)
					),
					'fields' => array('id', 'id')
				));
				$listProfitCenterActive = $this->ProfitCenter->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
					),
					'fields' => array('id', 'id')
				));
				if (!empty($new_record['ProjectTask'])) {
					$listTaskIds = $listTaskPredes = array();
					$all_tasks = array();
					/* Parent task duoc copy truoc. Sau do la cac task con lai */
					$tasks = $new_record['ProjectTask'];
					$tasks = Set::combine( $new_record['ProjectTask'], '{n}.id', '{n}');
					$parent_tasks = Set::classicExtract( $tasks, '{n}.parent_id');
					$parent_tasks = array_unique( $parent_tasks);
					foreach( $parent_tasks as $t_id){
						if( !empty($t_id))$all_tasks[] = $tasks[$t_id];
						unset($tasks[$t_id]);
					}
					$all_tasks = array_merge($all_tasks, $tasks);
					foreach ($all_tasks as $ptask) {
						$ptask['project_id'] = $id_duplicate;
						$ptask['project_planed_phase_id'] = !empty($project_planed_phase_id_old[$ptask['project_planed_phase_id']]) ? $project_planed_phase_id_old[$ptask['project_planed_phase_id']] : 0;
						$oldTaskId = $ptask['id'];
						if (!empty($ptask['parent_id'])) {
							$ptask['parent_id'] = !empty($listTaskIds[$ptask['parent_id']]) ? $listTaskIds[$ptask['parent_id']] : 0;
						}
						if (!empty($ptask['predecessor'])) {
							$listTaskPredes[$oldTaskId] = $ptask['predecessor'];
						}
						$projectTaskEmpRefers = $this->ProjectTaskEmployeeRefer->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'project_task_id' => $oldTaskId
							),
							'fields' => array('reference_id', 'project_task_id', 'is_profit_center', 'estimated')
						));
						
						$listNCT = $this->NctWorkload->find('all', array(
							'recursive' => -1,
							'conditions' => array(
								'project_task_id' => $oldTaskId
							),
							'fields' => array('task_date', 'estimated', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date', 'end_date'),
						));
						// project_planed_phase_id
						unset($ptask['task_completed']);
						unset($ptask['task_assign_to']);
						unset($ptask['task_real_end_date']);
						unset($ptask['initial_estimated']);
						unset($ptask['overload']);
						unset($ptask['id']);
						unset($ptask['initial_task_start_date']);
						unset($ptask['initial_task_end_date']);
						unset($ptask['manual_consumed']);
						unset($ptask['manual_overload']);
						unset($ptask['text_1']); // Remove comment
						unset($ptask['attachment']); // Remove attachment
						$ptask['special_consumed'] = 0;
						
						$ptask['created'] = time();
						$ptask['updated'] = time();
						// Edit comment info
						$ptask['text_updater'] = null;
						$ptask['text_time'] = null;
						$ptask['task_status_id'] = $first_status_id;
						// Milestone reference
						if(!empty($milestone_refer) && !empty($ptask['milestone_id']) && !empty($milestone_refer[$ptask['milestone_id']])){
							$ptask['milestone_id'] = $milestone_refer[$ptask['milestone_id']];
						}
						$this->ProjectTask->create();
						$_new_task = $this->ProjectTask->save($ptask);
						$newTaskId = $this->ProjectTask->getLastInsertID();
						$listTaskIds[$oldTaskId] = $newTaskId;
						$estimatedOfNewTask = 0;
						if(!empty($projectTaskEmpRefers)){
							foreach ($projectTaskEmpRefers as $key => $projectTaskEmpRefer) {
								$dx = $projectTaskEmpRefer['ProjectTaskEmployeeRefer'];
								if (!($dx['is_profit_center'] == 1 && in_array($dx['reference_id'], $listProfitCenterActive)) || ($dx['is_profit_center'] == 0 && in_array($dx['reference_id'], $listEmployeeActive))) {
									$has_left_employee = true;
								}
								$this->ProjectTaskEmployeeRefer->create();
								$this->ProjectTaskEmployeeRefer->save(array(
									'reference_id' => $dx['reference_id'],
									'project_task_id' => $newTaskId,
									'is_profit_center' => $dx['is_profit_center'],
									'estimated' => $dx['estimated']
								));
								$estimatedOfNewTask += !empty($dx['estimated']) ? $dx['estimated'] : 0;
							}
							$this->ProjectTask->id = $newTaskId;
							$this->ProjectTask->save(array('estimated' => $estimatedOfNewTask));
						}
						
						if (!empty($listNCT)) {
							foreach ($listNCT as $key => $value) {
								if ($ptask['is_nct']) {
									$nct_fields = array(
										'task_date' => $value['NctWorkload']['task_date'],
										'estimated' => $value['NctWorkload']['estimated'],
										'reference_id' => $value['NctWorkload']['reference_id'],
										'is_profit_center' => $value['NctWorkload']['is_profit_center'],
										'project_task_id' => $newTaskId,
										'activity_task_id' => 0,
										'group_date' => $value['NctWorkload']['group_date'],
										'end_date' => $value['NctWorkload']['end_date'],
									);
									$this->NctWorkload->create();
									$this->NctWorkload->save($nct_fields);
								}
							}
						}
					}
					if (!empty($listTaskPredes)) {
						foreach ($listTaskPredes as $old => $pre) {
							$idUpdate = !empty($listTaskIds[$old]) ? $listTaskIds[$old] : 0;
							$this->ProjectTask->id = $idUpdate;
							$saved['predecessor'] = !empty($listTaskIds[$pre]) ? $listTaskIds[$pre] : '';
							$this->ProjectTask->save($saved);
						}
					}
				}
				/**
				 * Save project_employee_manager
				 * Lay danh sach project_employee_manager da tao
				 */
				$this->loadModel('ProjectEmployeeManager');
				$dataManagers = $this->ProjectEmployeeManager->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $oldId
					)
				));
				if (!empty($dataManagers)) {
					$_dataManagers = array();
					foreach ($dataManagers as $value) {
						$_dataManagers[] = array(
							'project_manager_id' => $value['ProjectEmployeeManager']['project_manager_id'],
							'project_id' => $id_duplicate,
							'type' => $value['ProjectEmployeeManager']['type'],
							'activity_id' => 0,
						);
					}
					if (!empty($_dataManagers)) {
						$this->ProjectEmployeeManager->create();
						$this->ProjectEmployeeManager->saveAll($_dataManagers);
					}
				}
				$this->ProjectTask->staffingSystem($id_duplicate);
			}else{
				$this->Session->setFlash( __('A project manager cannot be created without a project manager', true));
			}
			$this->redirect(array('controller'=> 'projects', 'action' => 'your_form', $id_duplicate, '?' => array('show_employee' => $has_left_employee)));
		} else $this->Session->setFlash(__('KO', true));
	}
    /**
     * get_sub_categories
     * Get sub categories
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_categories($id = null, $currenr_id = null) {
        $this->autoRender = false;
        if (!empty($id)) {
            $list = $this->ProjectAmr->ProjectAmrSubCategory->find('list', array('conditions' => array('ProjectAmrSubCategory.project_amr_category_id' => $id),
                'fields' => array('ProjectAmrSubCategory.id', 'ProjectAmrSubCategory.amr_sub_category')));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if ($k == $currenr_id)
                        $selected = "selected";
                    else
                        $selected = "";
                    echo "<option value='" . $k . "' $selected>" . $v . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select sub category--%s", true), '<option>', '</option>');
            }
        } else {
            echo sprintf(__("%s--Select sub category--%s", true), '<option>', '</option>');
        }
    }

    /**
     * get_sub_program
     * Get sub program
     * @param int $id
     * @return void
     * @access public
     */
    function get_sub_program($currenr_id = null) {
        Configure::write('debug', 0);
        $this->autoRender = false;
        echo sprintf(__("%s--Select sub program--%s", true), '<option value="">', '</option>');
        if (!empty($this->params['url']['data'])) {
            $list = $this->ProjectAmr->ProjectAmrSubProgram->find('list', array('conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => $this->params['url']['data']), 'fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program')));
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    echo "<option value='" . $k . "' >" . $v . "</option>";
                }
            }
        }
    }

    /**
     * get value from project task
     *
     * @param int $id
     * @return array
     * @access public
     */
    private function _getPorjectTask($id) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectTask');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id
            )
        ));

        $projectTasks = $this->ProjectTask->find(
                "all", array(
            'fields' => array(
                'id',
                'estimated',
                'parent_id'
            ),
            'recursive' => -1,
            "conditions" => array('project_id' => $id, 'special' => 0)
                )
        );
        $_projectTaskId = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id') : array();
        $_parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.parent_id') : array();
        foreach ($projectTasks as $key => $projectTask) {
            if (in_array($projectTask['ProjectTask']['id'], $_parentIds)) {
                unset($projectTasks[$key]);
            }
        }
        foreach ($_parentIds as $k => $value) {
            if ($value == 0) {
                unset($_parentIds[$k]);
            }
        }
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_task_id'),
            'conditions' => array(
                'project_task_id' => $_projectTaskId,
                'special' => 0,
                'NOT' => array("project_task_id" => null))
        ));
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask') : array();

        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $_activityTaskId,
                'company_id' => $projectName['Project']['company_id'],
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
            if ($projectTask['ProjectTask']['parent_id'] == 0) {
                $sumEstimated += $projectTask['ProjectTask']['estimated'];
            }
            $referencedEmployees = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if (count($referencedEmployees) == 0) {
                
            } else {
                foreach ($referencedEmployees as $key1 => $referencedEmployee) {
                    $projectTasks[$key]['ProjectTask']['ProjectTaskEmployeeRefer'][] = $referencedEmployee['ProjectTaskEmployeeRefer'];
                }
            }
            $estimated = isset($projectTask['ProjectTask']['estimated']) ? $projectTask['ProjectTask']['estimated'] : 0;
            // Check if Activity Task Existed
            if (isset($activityTasks[$projectTaskId])) {
                $activityTaskId = $activityTasks[$projectTaskId]['id'];
                // Check if Request Existed
                if (isset($activityRequests[$activityTaskId])) {
                    $consumed = $activityRequests[$activityTaskId];
                    $completed = $estimated - $consumed;
                    if ($completed < 0) {
                        $completed = 0;
                    }
                    $sumRemainConsumed += $completed;
                    $total += $consumed;
                } else {
                    if (in_array($projectTask['ProjectTask']['id'], $_parentIds, true)) {
                        //unset($projectTask);
                    } else {
                        $sumRemainNotConsumed += $estimated;
                    }
                    $total += 0;
                }
            } else {
                // Error Handle
                $sumRemainNotConsumed += $estimated;
                $total += 0;
            }
        }
        if (!empty($_parentIds)) {
            foreach ($_parentIds as $_parentId) {
                $_activityTaskId = !empty($activityTasks[$_parentId]['id']) ? $activityTasks[$_parentId]['id'] : '';
                $_consumed = !empty($activityRequests[$_activityTaskId]) ? $activityRequests[$_activityTaskId] : 0;
                $total += $_consumed;
            }
        }
        $sumRemain = $sumRemainConsumed + $sumRemainNotConsumed;

        $this->loadModel('Project');
        $project = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $id),
            'fields' => array('activity_id')
        ));
        $_activityIdProject = $project['Project']['activity_id'];
        $this->loadModel('ActivityRequest');
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'SUM(value) as consumed'
            ),
            'conditions' => array(
                'activity_id' => $_activityIdProject,
                'company_id' => $projectName['Project']['company_id'],
                'status' => 2,
                'NOT' => array('value' => 0)
            )
        ));
        $_engaged = $_progression = 0;
        $_engaged = isset($activityRequests[0][0]['consumed']) ? $total + $activityRequests[0][0]['consumed'] : $total;
        $validated = $_engaged + $sumRemain;
        if ($validated == 0) {
            $_progression = 0;
        } else {
            $_progression = round((($_engaged * 100) / $validated), 2);
        }
        if ($_progression > 100) {
            $_progression = 100;
        } else {
            $_progression = $_progression;
        }
        $_projectTask = array();
        $_projectTask['engaged'] = $_engaged;
        $_projectTask['remain'] = $sumRemain;
        $_projectTask['validated'] = $validated;
        $_projectTask['progression'] = $_progression;
        return $_projectTask;
    }

    private function _makeDataAmr($listProjects = array()) {
        $this->loadModel('ProjectEvolution');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        /**
         * Lay company cua used dang login
         */
        $employeeName = $this->_getEmpoyee();
        /**
         * Lay evolution theo danh sach id
         */
        $projectEvolutions = $this->ProjectEvolution->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProjects),
            'fields' => array('id', 'project_id', 'SUM(man_day) as totalMD'),
            'group' => array('project_id')
        ));
        $projectEvolutions = !empty($projectEvolutions) ? Set::combine($projectEvolutions, '{n}.ProjectEvolution.project_id', '{n}.0.totalMD') : array();
        /**
         * Lay danh sach activity theo list project
         */
        $listActivities = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null),
                'Project.id' => $listProjects
            ),
            'fields' => array('id', 'activity_id')
        ));
        /**
         * Lay cac project Task theo danh sach project
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $listProjects),
            'fields' => array('id', 'parent_id', 'project_id', 'estimated', 'overload', 'special', 'special_consumed')
        ));
        $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask') : array();
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.parent_id')) : array();
        $listIdProjectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.id', '{n}.id') : array();
        /**
         * Lay danh sach activity task theo danh sach project task
         */
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listIdProjectTasks),
            'fields' => array('id', 'activity_id', 'project_task_id')
        ));
        $listATaskLinkPTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.id', '{n}.ActivityTask.activity_id') : array();
        /**
         * Lay consumed cua cac task
         */
        $consumedOfTasks = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'task_id',
                //'value'
                'SUM(value) as value'
            ),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0),
                'task_id' => array_keys($activityTasks)
            )
        ));
        /**
         * Lay consumed cua cac activity
         */
        $consumedOfActivities = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array(
                'id',
                'employee_id',
                'activity_id',
                'SUM(value) as value'
            ),
            'group' => array('activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $listActivities,
                'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
                )
        );
        /**
         * Chuyen consumed cua task vao activity
         */
        $sumActivities = array();
        if (!empty($consumedOfTasks)) {
            foreach ($consumedOfTasks as $_data) {
                $dx = $_data['ActivityRequest'];
                $activityId = !empty($activityTasks[$dx['task_id']]) ? $activityTasks[$dx['task_id']] : 0;
                if (!isset($sumActivities[$activityId])) {
                    $sumActivities[$activityId] = 0;
                }
                $sumActivities[$activityId] += $_data[0]['value'];
            }
        }
        /**
         * Cong consumed cua activity voi consumed cua task
         */
        if (!empty($consumedOfActivities)) {
            foreach ($consumedOfActivities as $data) {
                $dx = $data['ActivityRequest'];
                $data = $data['0']['value'];
                if (!isset($sumActivities[$dx['activity_id']])) {
                    $sumActivities[$dx['activity_id']] = 0;
                }
                $sumActivities[$dx['activity_id']] += $data;
            }
        }
        /**
         * Tinh cons
         */
        $results = $sumWorload = $sumOverload = $employees = $sumEmployees = $sumRemains = $sumRemainSpecials = array();
        $consumedOfTasks = !empty($consumedOfTasks) ? Set::combine($consumedOfTasks, '{n}.ActivityRequest.task_id', '{n}.0.value') : array();
        foreach ($projectTasks as $taskId => $projectTask) {
            if (in_array($taskId, $parentIds)) {
                unset($projectTasks[$taskId]);
            } else {
                $projectId = $projectTask['project_id'];
                $dataEstimated = $projectTask['estimated'];
                $dataOverload = $projectTask['overload'];
                if (!isset($sumWorload[$projectId])) {
                    $sumWorload[$projectId] = 0;
                }
                $sumWorload[$projectId] += $dataEstimated;

                if (!isset($sumOverload[$projectId])) {
                    $sumOverload[$projectId] = 0;
                }
                $sumOverload[$projectId] += $dataOverload;

                $consumedForTasks = 0;
                if (!empty($projectTask['special']) && $projectTask['special'] == 1) {
                    $consumedForTasks = !empty($projectTask['special_consumed']) ? $projectTask['special_consumed'] : 0;
                    if (!isset($sumRemainSpecials[$projectId])) {
                        $sumRemainSpecials[$projectId] = 0;
                    }
                    $sumRemainSpecials[$projectId] += $dataEstimated - $consumedForTasks;
                } else {
                    $ATaskId = !empty($listATaskLinkPTasks[$taskId]) ? $listATaskLinkPTasks[$taskId] : 0;
                    $consumedForTasks = !empty($consumedOfTasks[$ATaskId]) ? $consumedOfTasks[$ATaskId] : 0;
                }
                if (!isset($sumRemains[$projectId])) {
                    $sumRemains[$projectId] = 0;
                }
                $sumRemains[$projectId] += ($dataEstimated + $dataOverload) - $consumedForTasks;
            }
        }
        unset($sumActivities[0]);
        foreach ($listActivities as $projectId => $activityId) {
            $workload = isset($sumWorload[$projectId]) ? $sumWorload[$projectId] : 0;
            $overload = isset($sumOverload[$projectId]) ? $sumOverload[$projectId] : 0;
            $consumed = isset($sumActivities[$activityId]) ? $sumActivities[$activityId] : 0;
            $remainSPCs = isset($sumRemainSpecials[$projectId]) ? $sumRemainSpecials[$projectId] : 0;
            $remains = isset($sumRemains[$projectId]) ? $sumRemains[$projectId] : 0;
            $remains = $remains - $remainSPCs;
            $progress = 0;
            if (($workload + $overload) == 0) {
                $progress = 0;
            } else {
                $com = round(($consumed * 100) / ($workload + $overload), 2);
                if ($com > 100) {
                    $progress = 100;
                } else {
                    $progress = $com;
                }
            }
            //md_engaged
            $results[$projectId]['workload'] = $workload;
            $results[$projectId]['overload'] = $overload;
            $results[$projectId]['md_forecasted'] = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_validated'] = $consumed; //consumed
            $results[$projectId]['md_engaged'] = $remains; //remain
            $mdForecasted = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
            $results[$projectId]['project_id'] = $projectId;
            $results[$projectId]['project_amr_progression'] = $progress;
            if (!empty($listProjects[$projectId])) {
                unset($listProjects[$projectId]);
            }
        }
        foreach ($listProjects as $projectId) {
            $workload = isset($sumWorload[$projectId]) ? $sumWorload[$projectId] : 0;
            $overload = isset($sumOverload[$projectId]) ? $sumOverload[$projectId] : 0;
            $consumed = 0;
            $remainSPCs = isset($sumRemainSpecials[$projectId]) ? $sumRemainSpecials[$projectId] : 0;
            $remains = isset($sumRemains[$projectId]) ? $sumRemains[$projectId] : 0;
            $remains = $remains - $remainSPCs;
            $progress = 0;
            if (($workload + $overload) == 0) {
                $progress = 0;
            } else {
                $com = round(($consumed * 100) / ($workload + $overload), 2);
                if ($com > 100) {
                    $progress = 100;
                } else {
                    $progress = $com;
                }
            }
            //md_engaged
            $results[$projectId]['workload'] = $workload;
            $results[$projectId]['overload'] = $overload;
            $results[$projectId]['md_forecasted'] = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_validated'] = $consumed; //consumed
            $results[$projectId]['md_engaged'] = $remains; //remain
            $mdForecasted = !empty($projectEvolutions[$projectId]) ? $projectEvolutions[$projectId] : 0;
            $results[$projectId]['md_variance'] = round($remains + $consumed - $mdForecasted, 2);
            $results[$projectId]['project_id'] = $projectId;
            $results[$projectId]['project_amr_progression'] = $progress;
        }
        return $results;
    }
	/* get_upload_document */
    public function get_upload_document($project_id = null, $return = false) {
		$result = false;
		$message = '';
		$all_attach = array();
		$project = array();
		if($this->_checkRole(false, $project_id) || ($this->employee_info['CompanyEmployeeReference']['see_all_projects'] == 1 || $this->companyConfigs['see_all_projects'] == 1)){
			$this->loadModels('ProjectFile');
			$project = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $project_id,
					'company_id' => $this->employee_info['Company']['id']
				),
				'fields' => array('id', 'project_name')
			));
			$all_attach = $this->ProjectFile->find('all', array(
				'conditions' => array(
					'project_id' => $project_id,
					'key' => $this->data['key']
					),
			));
			$result = true;
		}else{
			$message = __('Access denied', true);
		}
		if(  $return ) return array(
			'ProjectFile' => $all_attach,
			'Project' => !empty($project['Project']) ? $project['Project'] : array(),
		);
		die(json_encode(array(
			'result' => !empty($result ) ? 'success' : 'failed',
			'data' => array(
				'ProjectFile' => $all_attach,
				'Project' => !empty($project['Project']) ? $project['Project'] : array(),
				'canEdit' => $this->_checkRole(false, $project_id),
				),
			'message' => $message
		)));
		
	}
    public function getPersonalizedViews($status = null, $return = false) {
        if(!$return ) $this->layout = false;
        $userViews = array();
        if (!empty($status)) {
            $conditions = array();
            switch ($status) {
                case 6: {
                        $conditions = array(
                            'UserStatusView.oppor_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 3: {
                        $conditions = array(
                            'UserStatusView.archived_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 4: {
                        $conditions = array(
                            'UserStatusView.model_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 5: {
                        $conditions = array(
                            'OR' => array(
                                'UserStatusView.model_view' => 1,
                                'UserStatusView.oppor_view' => 1
                            ),
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
                case 1:
                default: {
                        $conditions = array(
                            'UserStatusView.progress_view' => 1,
                            'UserStatusView.employee_id' => $this->employee_info['Employee']['id']
                        );
                        break;
                    }
            }
			$com_conditions = array();
			switch ($status) {
                case 6: {
                        $com_conditions = array(
                            'CompanyViewDefault.oppor_view' => 1,
                        );
                        break;
                    }
                case 3: {
                        $com_conditions = array(
                            'CompanyViewDefault.archived_view' => 1,
                        );
                        break;
                    }
                case 4: {
                        $com_conditions = array(
                            'CompanyViewDefault.model_view' => 1,
                        );
                        break;
                    }
                case 5: {
                        $com_conditions = array(
                            'OR' => array(
                                'CompanyViewDefault.model_view' => 1,
                                'CompanyViewDefault.oppor_view' => 1
                            ),
                        );
                        break;
                    }
                case 1:
                default: {
                        $com_conditions = array(
                            'CompanyViewDefault.progress_view' => 1,
                        );
                        break;
                    }
            }
            if ($this->Session->read('mobile')){
				$conditions['UserView.mobile'] = 1;
				$com_conditions['CompanyViewDefault.mobile'] = 1;
			}
			$com_conditions['CompanyViewDefault.company_id'] = $this->employee_info["Company"]["id"];
            if ($this->is_sas)
                $userViews = $this->UserView->find('list', array(
                    'recursive' => -1,
                    'fields' => array('UserView.id', 'UserView.name'),
                    'conditions' => array('model' => 'project')));
            else {
                if (!empty($conditions)) {
					$userViews = $this->UserView->find('list', array(
						'recursive' => 0,
						'fields' => array('UserView.id', 'UserView.name'),
						'order' => 'UserView.name ASC',
						'group' => 'UserView.id',
						'conditions' => array(
							'UserView.model' => 'project',
							'Employee.company_id' => $this->employee_info["Company"]["id"],
							'OR' => array(
								array(
									'UserView.employee_id' => $this->employee_info['Employee']['id'],
									),
								array(
									'UserView.public' => true
							)),
							$conditions
						),
					));
                }
            }
			/*
			* Kiem tra user co o bang user_status_view. Neu ko thi bo qua gop array (ticket #358). Update new ticket #1167
			*/
			$this->loadModel('UserStatusView');
			// $hasStatusView = $this->UserStatusView->find('first', array(
				// 'recursive' => -1,
				// 'conditions' => array(
					// 'employee_id' => $this->employee_info['Employee']['id']
				// ),
				// 'fields' => array('employee_id')
			// ));
			// if (empty($hasStatusView)){
				$this->loadModel('CompanyViewDefault');
				$company_views  = $this->CompanyViewDefault->find('list', array(
					'conditions' => $com_conditions,
					'recursive' => -1,
					'joins' => array(
						array(
							'table' => 'user_views',
							'alias' => 'UserView',
							'conditions' => array(
								'UserView.id = CompanyViewDefault.user_view_id',
								'UserView.company_id' => $this->employee_info["Company"]["id"],
							)
						)
					),
					'fields' => array('UserView.id', 'UserView.name'),
				));
				foreach( $company_views as $key => $view){
					if( !array_key_exists( $key, $userViews ) )  $userViews[$key] = $view;
				}
			// }
        }
		if( $return) return $userViews;
		// debug( $userViews); exit;
        die(json_encode($userViews));
    }

    function getFamily($program_id = null) {
        $this->layout = false;
        $this->loadModel('ProjectAmrProgram');
        $results = '';
        if (!empty($program_id)) {
            $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectAmrProgram.id ' => $program_id
                ),
                'fields' => array('id', 'family_id')
            ));
            if (!empty($famAndSubFamOfPrograms) && !empty($famAndSubFamOfPrograms['ProjectAmrProgram']['family_id'])) {
                $results = $famAndSubFamOfPrograms['ProjectAmrProgram']['family_id'];
            }
        }
        echo json_encode($results);
        exit;
    }

    function getSubFamily($familyId = null, $sub_program_id = null, $company_id = null) {
        $this->layout = false;
        $this->loadModels('ActivityFamily', 'ProjectAmrSubProgram');
        $select = '<option value>' . __('-- Any --', true) . '</option>';
        $subFamId = '';
        if (!empty($familyId)) {
            $lists = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'parent_id' => $familyId,
                    'company_id' => !empty($company_id) ? $company_id : $this->employee_info["Company"]["id"]
                ),
                'fields' => array('id', 'name'),
                'order' => array('name')
            ));
            if (!empty($lists)) {
                foreach ($lists as $id => $val) {
                    $select .= "<option value='" . $id . "' >" . $val . "</option>";
                }
            }
            if ($sub_program_id) {
                $subFams = $this->ProjectAmrSubProgram->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectAmrSubProgram.id' => $sub_program_id),
                    'fields' => array('id', 'sub_family_id')
                ));
                if (!empty($subFams) && !empty($subFams['ProjectAmrSubProgram']['sub_family_id'])) {
                    $subFamId = $subFams['ProjectAmrSubProgram']['sub_family_id'];
                }
            }
        }
        $results = array(
            'select' => $select,
            'subFamId' => $subFamId
        );
        echo json_encode($results);
        exit;
    }

    /**
     * Tao 1 activity moi va lien ket voi project vua thay doi status tu Opportunity sang In progress
     */
    public function saveActivityLinked($project_id = null, $company_id = null) {
        $this->layout = false;
        $this->loadModels('Activity', 'ActivityFamily', 'ActivityTask', 'ProjectTask', 'ProjectEmployeeManager', 'Project', 'ProjectAmrProgram', 'ProjectAmrSubProgram');
		$this->_checkRole(false, $project_id);
		if($this->employee_info['Employee']['change_status_project'] != 1 && $this->employee_info['Role']['name'] == 'pm'){
			echo 'Error';
			exit;
		}
        $activity_id = '';
        if (!empty($this->data)) {
            $data = $this->data;
            $activity_families = array();
            $fam_id = '';
            $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;
            $opportunity_to_in_progress_without_validation = isset($this->companyConfigs['opportunity_to_in_progress_without_validation']) && !empty($this->companyConfigs['opportunity_to_in_progress_without_validation']) ? true : false;

            // Edit by Viet Nguyen
            // Liked family of activity with program of project
            // If has linked and family linked is empty -> Error
            if ($activate_family_linked_program && $opportunity_to_in_progress_without_validation) {
                $projet_program = $this->Project->find('list', array(
                    'recursive' => -1,
                    'fields' => array('project_amr_program_id', 'project_amr_program_id'),
                    'conditions' => array('id' => $project_id))
                );
                if (!empty($projet_program)) {
                    $activity_families = $this->ProjectAmrProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id ' => $company_id,
                            'id' => $projet_program,
                        ),
                        'fields' => array('id', 'family_id')
                    ));
                    $fam_id = !empty($activity_families['ProjectAmrProgram']['family_id']) ? $activity_families['ProjectAmrProgram']['family_id'] : '';
                    if (!empty($fam_id)) {
                        $activity_families = $this->ActivityFamily->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'id' => $fam_id
                            ),
                            'fields' => array('id', 'id')
                        ));
                        if (empty($activity_families))
                            $fam_id = '';
                    }
                }
            }else {
                $activity_families = $this->ActivityFamily->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id
                    ),
                    'fields' => array('id')
                ));
                if (!empty($activity_families)) {
                    $fam_id = $activity_families['ActivityFamily']['id'];
                }
            }
            // exit;
            if (empty($data['family_id']) && empty($fam_id)) {
                // echo json_encode('Error');
                exit;
            }

            $this->Project->id = $project_id;
            $this->Project->save(array('category' => 1, 'updated_opp_ip' => time()));

            // Viet nguyen update
            // when program linked to family then sub program too link sub family

            $fam_id = !empty($data['family_id']) ? $data['family_id'] : $fam_id;
            // Get sub family
            $sub_family_id = '';
            if (empty($data['sub_family_id'])) {
                $sub_familie = $this->ActivityFamily->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'parent_id' => $fam_id,
                    ),
                    'fields' => array('id')
                ));
                $sub_family_id = !empty($sub_familie) ? $sub_familie['ActivityFamily']['id'] : '';
            } else {
                $sub_family_id = $data['sub_family_id'];
            }
            $saved = array(
                'name' => $data['name'],
                'long_name' => $data['name_detail'],
                'short_name' => $data['short_name'],
                'family_id' => $fam_id,
                'subfamily_id' => $sub_family_id,
                'pms' => 1,
                'project' => $project_id,
                'company_id' => $company_id,
                'activated' => $data['activated'],
            );
            $check = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id,
                    'company_id' => $company_id
                )
            ));


            if (!empty($check)) {
                $this->Activity->id = $check['Activity']['id'];
            } else {
                $this->Activity->create();
            }
            // IF save sussces activity then linked sub program with sub family
            $sub_program_id = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id ' => $company_id,
                    'id' => $project_id,
                ),
                'fields' => array('project_amr_sub_program_id', 'project_amr_sub_program_id')
            ));
            if ($this->Activity->save($saved) && !empty($sub_program_id) && !empty($sub_family_id)) {
                // get the first sub program of program
                $this->ProjectAmrSubProgram->id = $sub_program_id;
                $this->ProjectAmrSubProgram->saveField('sub_family_id', $sub_family_id);
            }
            if (!empty($check)) {
                $activity_id = $check['Activity']['id'];
            } else {
                $activity_id = $this->Activity->getLastInsertID();
            }
			$this->Project->id = $project_id;
            $this->Project->saveField('activity_id', $activity_id);

            // save PM activity
            // kiem tra PM co the change project manager select field

            $employeePM = $this->ProjectEmployeeManager->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                    'type' => 'PM'
                ),
                'fields' => array('project_manager_id'),
            ));
            $employeePM = !empty($employeePM) ? Set::classicExtract($employeePM, '{n}.ProjectEmployeeManager.project_manager_id') : array();
            $project_tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                )
            ));
			
            // Update actvity_id khi duplicate project
            $this->updateProjectBudget($project_id, $activity_id);
            if (!empty($project_tasks)) {
                $this->ProjectTask->staffingSystem($project_id);
                foreach ($project_tasks as $project_task) {
                    $this->_syncActivityTask($project_id, $project_task, $project_task['ProjectTask']['id']);
                }
            }
        }
        echo json_encode($activity_id);
        exit;
    }

    // Create by VN
    // Update actvity_id khi chuyen trang thai
    // OP -> IN -> activity_id = activity_id_linked
    // IP -> OP ->activity_id = 0
    private function updateProjectBudget($project_id, $activity_id) {
        $this->loadModels('ProjectBudgetSyn', 'ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectBudgetSale');
		$this->_checkRole(false, $project_id);
        // Update activity_id linked in ProjectBudgetSyn table
        $project_budget_sysn = $this->ProjectBudgetSyn->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'fields' => array('id')
        ));
        $project_budget_sysn_id = !empty($project_budget_sysn) ? $project_budget_sysn['ProjectBudgetSyn']['id'] : '';
        if (!empty($project_budget_sysn_id)) {
            $this->ProjectBudgetSyn->id = $project_budget_sysn_id;
            $this->ProjectBudgetSyn->saveField('activity_id', $activity_id);
        }

        // Update activity_id linked in project budget internal detail
        $acId_internal = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_internal)) {
            foreach ($acId_internal as $acId_inter) {
                $this->ProjectBudgetInternalDetail->id = $acId_inter;
                $this->ProjectBudgetInternalDetail->saveField('activity_id', $activity_id);
            }
        }

        // Update activity_id linked in project budget external
        $acId_external = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_external)) {
            foreach ($acId_external as $acId_exter) {
                $this->ProjectBudgetExternal->id = $acId_exter;
                $this->ProjectBudgetExternal->saveField('activity_id', $activity_id);
            }
        }

        // Update activity_id linked in project budget sales
        $acId_sale = $this->ProjectBudgetSale->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
            ),
            'fields' => array('id', 'id')
        ));
        if (!empty($acId_sale)) {
            foreach ($acId_sale as $acId_sl) {
                $this->ProjectBudgetSale->id = $acId_sl;
                $this->ProjectBudgetSale->saveField('activity_id', $activity_id);
            }
        }
		$listModels = array('ProjectFinancePlus', 'ProjectFinancePlusDetail', 'ProjectBudgetInvoice','ProjectBudgetProvisional','ProjectBudgetPurchaseInvoice','ProjectBudgetPurchase','ProjectEmployeeManager');
		foreach( $listModels as $model){
			$this->loadModel($model);
			$this->$model->updateAll(
				array($model.'.activity_id' => $activity_id), 	// data
				array($model.'.project_id' => $project_id) 		// conditions
			);
		}
    }

    private function _getPhaseses($project_id) {
        if (!isset($this->_phases)) {
            $project = $this->_getProject($project_id);
            $this->ProjectTask->ProjectPhasePlan->Behaviors->attach('Containable');
            $projectPhases = $this->ProjectTask->ProjectPhasePlan->find('all', array(
                'fields' => array('id', 'phase_planed_start_date', 'phase_planed_end_date', 'project_part_id'),
                'contain' => array('ProjectPhase' => array('id', 'name'), 'ProjectPart' => array('id', 'title')),
                'conditions' => array(
                    "ProjectPhasePlan.project_id" => $project['Project']['id'],
                    'company_id' => $project['Project']['company_id']
                )
            ));
            $this->_phases = $projectPhases;
        }
        return $this->_phases;
    }

    /**
     * Function: Create new activity task
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
    private function _createActivityTask($project_task, $activity_id, $project_task_id, $project_id) {
        $dataActivityTask = array();
        // $phases = $this->_getPhasesesCombined($project_id);
        // $phase_name = isset($project_task['ProjectTask']['project_planed_phase_id']) ? $phases[$project_task['ProjectTask']['project_planed_phase_id']] : '';
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id, $project_task['ProjectTask']['project_planed_phase_id']) : '';
        $activity_task_name = $phase_name . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        if (!empty($checkTask)) {
            $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
        } else {
            $this->ActivityTask->create();
            $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
            $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
        }
        $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
        $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
        $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
        $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
        $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
        $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
        $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
        $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
        //update nctworkload activity task id
        $this->loadModel('NctWorkload');
        $this->NctWorkload->updateAll(array(
            'NctWorkload.activity_task_id' => $this->ActivityTask->id
                ), array(
            'NctWorkload.project_task_id' => $project_task_id
        ));
        return $result;
    }

    /**
     * Function: create activity subtask
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
	private function _getProjectTaskById($project_task_id){
        $project_task = $this->ProjectTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                "ProjectTask.id" => $project_task_id
            ),
            'fields' => array('task_title', 'task_start_date', 'task_end_date', 'duration')
        ));
        return $project_task;
    }
    private function _createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $parent_project_task_id) {
        $dataActivityTask = array();
        //$phases = $this->_getPhasesesCombined($project_id);
        //$phase_name = $phases[$project_task['ProjectTask']['project_planed_phase_id']];
        $phase_name = !empty($project_task['ProjectTask']['project_planed_phase_id']) ? $this->_getPhaseNameByPhasePlanId($project_id, $project_task['ProjectTask']['project_planed_phase_id']) : '';
        //parent_project_task_id
        //_getActivityTasksByActivityIdAndProjectTaskId
        $parent_task = $this->_getProjectTaskById($parent_project_task_id);
        $parent_activity_task = $this->_getActivityTasksByActivityIdAndProjectTaskIdPart2($activity_id, $parent_project_task_id);
        $task_title = !empty($parent_task['ProjectTask']['task_title']) ? $parent_task['ProjectTask']['task_title'] : '';
        $activity_task_name = $phase_name . "/" . $task_title . "/" . $project_task['ProjectTask']['task_title'];
        $this->loadModel('ActivityTask');
        $checkTask = $this->ActivityTask->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id
            ),
            'fields' => array('id')
        ));
        $result = array();
        if (!empty($parent_activity_task['ActivityTask']['id'])) {
            if (!empty($checkTask)) {
                $this->ActivityTask->id = $checkTask['ActivityTask']['id'];
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
            } else {
                $this->ActivityTask->create();
                $dataActivityTask['ActivityTask']['name'] = $activity_task_name;
                $dataActivityTask['ActivityTask']['activity_id'] = $activity_id;
                $dataActivityTask['ActivityTask']['project_task_id'] = $project_task_id;
                $dataActivityTask['ActivityTask']['parent_id'] = $parent_activity_task['ActivityTask']['id'];
            }
            $dataActivityTask['ActivityTask']['task_status_id'] = @$project_task['ProjectTask']['task_status_id'];
            $dataActivityTask['ActivityTask']['milestone_id'] = @$project_task['ProjectTask']['milestone_id'];
            $dataActivityTask['ActivityTask']['is_nct'] = isset($project_task['ProjectTask']['is_nct']) ? $project_task['ProjectTask']['is_nct'] : 0;
            $dataActivityTask['ActivityTask']['manual_consumed'] = isset($project_task['ProjectTask']['manual_consumed']) ? $project_task['ProjectTask']['manual_consumed'] : 0;
            $dataActivityTask['ActivityTask']['special'] = isset($project_task['ProjectTask']['special']) ? $project_task['ProjectTask']['special'] : 0;
            $dataActivityTask['ActivityTask']['amount'] = isset($project_task['ProjectTask']['amount']) ? $project_task['ProjectTask']['amount'] : 0;
            $dataActivityTask['ActivityTask']['progress_order'] = isset($project_task['ProjectTask']['progress_order']) ? $project_task['ProjectTask']['progress_order'] : 0;
            $result = $this->ActivityTask->save($dataActivityTask['ActivityTask']);
            //update nctworkload activity task id
            $this->loadModel('NctWorkload');
            $this->NctWorkload->updateAll(array(
                'NctWorkload.activity_task_id' => $this->ActivityTask->id
                    ), array(
                'NctWorkload.project_task_id' => $project_task_id
            ));
        }

        return $result;
    }

    //create project task - update activity task deltail
    private function _syncActivityTask($project_id, $project_task, $project_task_id) {
        $projects = $this->_getProject($project_id);
        $activity = $this->_getActivity($project_id);
        if (isset($activity)) {
            if (isset($activity[0])) {
                if (isset($activity[0]['Activity'])) {
                    if (isset($activity[0]['Activity']['id'])) {
                        $activity_id = $activity[0]['Activity']['id'];
                        $is_exist_activity_task = $this->_checkExistActivityTask($activity_id, $project_task_id);
                        if ($project_task['ProjectTask']['parent_id'] == 0 || $project_task['ProjectTask']['parent_id'] == null || $project_task['ProjectTask']['parent_id'] == '') {
                            $this->_createActivityTask($project_task, $activity_id, $project_task_id, $project_id);
                        } else {
                            $this->_createActivitySubTask($project_task, $activity_id, $project_task_id, $project_id, $project_task['ProjectTask']['parent_id']);
                        }
                    }
                }
            }
        }
    }

    private function _getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id) {
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activity_id,
                'project_task_id' => $project_task_id,
            )
        ));
        return $activityTasks;
    }

    private function _getActivityTasksByActivityIdAndProjectTaskIdPart2($activityIds, $taskIds) {
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find(
                'first', array(
            'conditions' => array(
                'NOT' => array("project_task_id" => null),
                'activity_id' => $activityIds,
                'project_task_id' => $taskIds,
            ),
            'fields' => array('id')
        ));
        return $activityTasks;
    }

    /**
     * Function:
     * @var     :
     * @return  :
     * @author : BANGVN
     * */
    private function _checkExistActivityTask($activity_id, $project_task_id) {
        $activity_tasks = $this->_getActivityTasksByActivityIdAndProjectTaskId($activity_id, $project_task_id);
        if (isset($activity_tasks)) {
            if (count($activity_tasks) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function _getPhaseNameByPhasePlanId($project_id, $project_planed_phase_id) {
        $projectPhases = $this->_getPhaseses($project_id);
        $projectPhases = Set::combine($projectPhases, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhase.name');
        return $projectPhases[$project_planed_phase_id];
    }

    /**
     * Function: Get activity
     * @var     : $project_id
     * @return  : activity object
     * @author : BANGVN
     * */
    private function _getActivity($project_id) {
        if (isset($this->_activity)) {
            
        } else {
            $this->loadModel('Activity');
            $activity = ClassRegistry::init('Activity')->find('all', array(
                'recursive' => -1,
                'conditions' => array('Activity.project' => $project_id),
            ));
            $this->_activity = $activity;
        }

        return $this->_activity;
    }

    private function _getProject($project_id) {
        if (!isset($this->_project)) {
            $project = $this->ProjectTask->Project->find("first", array(
                'conditions' => array('Project.id' => $project_id)
                    )
            );
            $this->_project = $project;
        }

        return $this->_project;
    }

    /**
     * Xoa activity lien ket khi chuyen tu In progress sang Opportunity
     */
    public function deleteActivityLinked($project_id = null) {
        $this->layout = false;
        $this->loadModel('Activity');
        $this->loadModel('ActivityTask');
		$this->_checkRole(false, $project_id);
		if($this->employee_info['Employee']['change_status_project'] != 1 && $this->employee_info['Role']['name'] == 'pm'){
			echo 'Error';
			exit;
		}
        // Update by VN
        // Check data task not in timesheet before delete task
        $task_in_timesheet = $this->_getCountTaskTimeSheet($project_id);
        if (!empty($project_id) && $task_in_timesheet == 0) {
            // viet nay hoi thua, nhung chu yeu de xoa nhung project duplicate activity truoc day.
            $cate = !empty($_POST['category']) ? $_POST['category'] : 2;
            $this->Project->id = $project_id;
            $this->Project->save(array(
				'category' => $cate,
				'activity_id' => null
			));
            $activity = $this->Activity->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                ),
                'fields' => array('id', 'project')
            ));
            if (!empty($activity)) {
                foreach ($activity as $activity_id => $p) {
                    $tasks = $this->ActivityTask->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $activity_id),
                        'fields' => array('id', 'id')
                    ));
                    if (!empty($tasks)) {
                        foreach ($tasks as $id) {
                            $this->ActivityTask->delete($id);
                        }
                    }
                    $this->Activity->delete($activity_id);
                }
            }

            // Update actvity_id khi delete activity linked
			$this->updateProjectBudget($project_id, 0);

            echo 'OK';
        } else {
            echo 'Error';
        }
        exit;
    }

    private function _getCountTaskTimeSheet($project_id) {
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('Project');
        $activity_id = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id
            ),
            'fields' => array('id', 'activity_id')
        ));

        $tasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'id')
        ));
        if (!empty($tasks)) {
            $request = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'OR' => array(
                        'activity_id' => $activity_id,
                        'task_id' => $tasks
                    )
                )
            ));
        } else {
            $request = $this->ActivityRequest->find('count', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activity_id)
            ));
        }
        return $request;
    }

    /**
     * Get status of company
     */
    private function _getIdStatusClosedOfCompany() {
        $this->loadModel('ProjectStatus');
        $infors = $this->Session->read('Auth.employee_info');
        $status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                //'name' => array('Clos', 'closed ', 'A lancer', 'A launch'),
                'company_id' => $infors['Company']['id']
            )
        ));
        $close = $aLancer = '';
        if (!empty($status)) {
            foreach ($status as $id => $val) {
                $val = trim(strtolower($val));
                if ($val == 'clos' || $val == 'closed') {
                    $close = $id;
                } elseif ($val == 'a lancer' || $val == 'a launch') {
                    $aLancer = $id;
                }
            }
        }
        return $close;
    }

    public function map($cat = 1, $projects = '') {
        if (isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']) {
            if (!((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))) {
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview = trim(str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_') . '_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/' . $value;
                }
                if (isset($_url_param['url']))
                    unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }
        $type = $cat == 5 ? array(1, 2) : ($cat == 6 ? 2 : $cat);
        $company = $this->employee_info['Company']['id'];
        $cond = array(
            'category' => $type,
            'company_id' => $company
        );
        if (!empty($projects)) {
            $cond['id'] = explode('-', $projects);
        }
        $prog = array();
        if (!empty($this->params['form']['typeProject'])) {
            $prog = !empty($this->params['form']['typeProject']) ? $this->params['form']['typeProject'] : array();
            foreach ($prog as $key => $val) {
                if (empty($val))
                    unset($prog[$key]);
            }
        }
        $prog = array_values($prog);
        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
        if (!empty($prog)) {
            $cond['project_amr_program_id'] = $prog;
        }
        /**
		* Update ticket #418 - Lấy lại list project cho screen Map, same với project hiển thị bên Project List. By QuanNV 01/07/2019
		*/
		$adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
		$this->loadModel('CompanyEmployeeReference');
        $role = $this->employee_info['Role']['name'];
        $employeeId = $this->employee_info['Employee']['id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employeeId),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        $listProjectOfPM = array();
		$employee = $this->Session->read("Auth.employee_info");
		
        if ($role == 'pm') {
            $listProjectOfPM = $this->getProjectOfPM($employee);
        }
		
		if($role == 'pm'){
			if(!$adminSeeAllProjects){
				$this->loadModel('ProjectEmployeeManager');
				if (!$seeAllOfEmploy) {
					$listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'project_manager_id' => $employeeId,
							'OR' => array(
								'is_profit_center' => 0,
								'is_profit_center is NULL'
							),
						),
						'fields' => array('project_id', 'project_id')
					));
					$listProjectOfEmployManager = $this->Project->find('list', array(
						'recursive' => -1,
						'conditions' => array(
							'company_id' => $employee["Company"]["id"],
							'project_manager_id' => $employeeId
						),
						'fields' => array('id', 'id')
					));
					$listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectOfEmployManager);
					$projects = $this->Project->find('all', array(
						'recursive' => -1,
						'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
						'conditions' => array(
							'Project.category' => $cond['category'],
							'Project.company_id' => $cond['company_id'],
							'Project.id' => $listProjectIds
						)
					));
				} else {
					$projects = $this->Project->find('all', array(
						'recursive' => -1,
						'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
						'conditions' => $cond
					));
				}
			} else{
				$projects = $this->Project->find('all', array(
					'recursive' => -1,
					'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
					'conditions' => $cond
				));
			}
		} else {
			$projects = $this->Project->find('all', array(
				'recursive' => -1,
				'fields' => array('id', 'category', 'project_name', 'latlng', 'address', 'project_amr_program_id'),
				'conditions' => $cond
			));
		}
		/*
		*End Update. 01/07/2019
		*/
        $projectsId = !empty($projects) ? Set::combine($projects, '{n}.Project.id', '{n}.Project.id') : array();
        $this->loadModels('ProjectAmrProgram', 'ProjectGlobalView', 'ProjectAmr');
        $projectAmrProgram = $this->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'color'),
            'conditions' => array(
                'company_id' => $company
            )
        ));
        $imageGlobals = $this->ProjectGlobalView->find('list', array(
            'recursive' => -1,
            'fields' => array('project_id', 'attachment'),
            'conditions' => array(
                'project_id' => $projectsId
            )
        ));
        $projectAmrs = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'fields' => array('project_id', 'weather', 'rank'),
            'conditions' => array('project_id' => $projectsId)
        ));
        $listWeather = !empty($projectAmrs) ? Set::combine($projectAmrs, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.weather') : array();
        $listRank = !empty($projectAmrs) ? Set::combine($projectAmrs, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.rank') : array();
        $this->set(compact('projects', 'cat', 'projectAmrProgram', 'imageGlobals', 'listWeather', 'listRank', 'listProgramFields', 'prog'));
    }

    public function updatePriority() {
		$result = 0;
        if (!empty($this->data)) {
			$this->_checkRole(false, $this->data['id']);
            $result = $this->Project->save(array(
                'id' => $this->data['id'],
                'project_priority_id' => $this->data['project_priority_id']
            ));
			$result = !empty($result ) ? 1 : 0;
        }
        die($result);
    }

    public function updateTypeAndProgram() {
		$result = 0;
        if (!empty($this->data)) {
			$this->_checkRole(false, $this->data['project_id']);
            $key = explode('.', $this->data['keys']);
            $result = $this->Project->save(array(
                'id' => $this->data['project_id'],
                $key[1] => $this->data['data_id']
            ));
			$result = !empty($result ) ? 1 : 0;
        }
        die($result);
    }

    public function checkCode1($id) {
        $code = $this->data['code'];
        $projectCode = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'id !=' => $id,
                'project_code_1' => $code
            ),
            'fields' => array('project_name')
        ));
        if (!empty($projectCode)) {
            die($projectCode['Project']['project_name']);
        }
        die;
    }

    public function checkProjectName($id) {
		$this->layout = false;
        $project_name = $_POST['project_name'];
		$exists = 0;
        if(!empty($project_name)) {
			$projectExist = $this->Project->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'project_name' => $project_name
				),
				'fields' => array('project_name')
			));
			if (!empty($projectExist)) {
				$exists = 1;
			}
		}
        die(json_encode($exists));
    }

    public function checkCodeAdd1() {
        $code = $this->data['code'];
        $projectCode = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'project_code_1' => $code
            ),
            'fields' => array('project_name')
        ));
        if (!empty($projectCode)) {
            die($projectCode['Project']['project_name']);
        }
        die;
    }

    public function export_vision_task() {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($_GET)) {
            $list = $_GET;
            // lay du lieu cho export.
            $datas = $this->getDataForVisionTasks($list);
            // kiem tra neu data empty thi thong bao loi
            if (empty($datas)) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect("/projects/index");
            }
            $langCode = Configure::read('Config.language');
            if ($langCode == 'eng') {
                $fieldName = array('name', 'english');
            } else {
                $fieldName = array('name', 'france');
            }
            $this->loadModel('VisionTaskExport');
            $fieldset = $this->VisionTaskExport->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'display' => 1
                ),
                'fields' => $fieldName,
                'order' => array('weight')
            ));
            $this->set('fieldset', $fieldset);
            $this->set('datas', $datas);
            $this->layout = '';
        }
    }

    public function check_task_for_task_vision() {
        if (!empty($_POST)) {
            $taskName = $_POST['task'];
            $company_id = $this->employee_info['Company']['id'];
            $listProject = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'fields' => array('id', 'project_name')
            ));
            $_conditions['task_title LIKE'] = '%' . $taskName . '%';
            $_conditions['project_id'] = array_keys($listProject);
            if (!empty($_POST['start']) && $_POST['start'] != '') {
                $start = $_POST['start'];
                $start = explode('-', $start);
                $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
                $_conditions['task_start_date >='] = $_start;
            }
            if (!empty($_POST['end']) && $_POST['end'] != '') {
                $start = $_POST['end'];
                $start = explode('-', $start);
                $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
                $_conditions['task_end_date <='] = $_start;
            }
            $countTask = $this->ProjectTask->find('count', array(
                'recursive' => -1,
                'conditions' => $_conditions
            ));
            die(json_encode($countTask));
        }
        die;
    }

    public function tasks_vision() {
        if (!empty($_GET)) {
            $list = $_GET;
            $company_id = $this->employee_info['Company']['id'];
            $datas = $this->getDataForVisionTasks($list);
            $langCode = Configure::read('Config.language');
            if ($langCode == 'eng') {
                $fieldName = array('name', 'english');
            } else {
                $fieldName = array('name', 'france');
            }
            $this->loadModel('VisionTaskExport');
            $fieldset = $this->VisionTaskExport->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'display' => 1
                ),
                'fields' => $fieldName,
                'order' => array('weight')
            ));
            $clStatus = $this->ProjectStatus->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'status' => 'CL'
                ),
				'order' => array('weight' => 'ASC'),
                'fields' => array('id')
            ));
            $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
			$this->loadModel('Menu');
			$on_newdesign_assistant= $this->Menu->find('first',array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'model' => 'project',
					'controllers' => 'project_tasks'
				),
				'fields' => array('enable_newdesign')
			));
			$on_newdesign_assistant = !empty($on_newdesign_assistant) ? $on_newdesign_assistant['Menu']['enable_newdesign'] : 0;
            $this->set(compact('datas', 'fieldset', 'clStatus', 'on_newdesign_assistant'));
        } else {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect("/projects/index");
        }
    }

    public function getDataForVisionTasks($list, $id = null) {
		$company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $conditions = array();
        
        $conditions['company_id'] = $company_id;
        // kiem tra va get project theo category.
        if (!empty($list['nameCategory'])) {
            $conditions['category'] = $list['nameCategory'];
        }
        // lay theo status PJ
        if (!empty($list['nameStatusProject'])) {
            $conditions['project_status_id'] = $list['nameStatusProject'];
        }
        if (!empty($list['nameProgramProject'])) {
            $conditions['project_amr_program_id'] = $list['nameProgramProject'];
        }
        if (!empty($list['nameSubProgramProject'])) {
            $conditions['project_amr_sub_program_id'] = $list['nameSubProgramProject'];
        }
        if (!empty($list['nameCodeProject'])) {
            $conditions['project_code_1'] = $list['nameCodeProject'];
        }
        if (!empty($list['nameCodeProject1'])) {
            $conditions['project_code_2'] = $list['nameCodeProject1'];
        }
        // phan nay thuoc assistant.
        if (!empty($list['manager'])) {
            $this->loadModel('ProjectEmployeeManager');
            $list_project_my_manager = array();
            $list_project_my_manager = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'project_manager_id' => $employee_id,
                    'category' => 1
                ),
                'fields' => array('Project.id', 'Project.id')
            ));
            $list_project_my_manager_bakcup = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectEmployeeManager.project_manager_id' => $employee_id,
                    'ProjectEmployeeManager.is_profit_center' => 0
                ),
                'joins' => array(
                    array(
                        'table' => 'projects',
                        'alias' => 'Project',
                        'conditions' => array(
                            'Project.id = ProjectEmployeeManager.project_id',
                            'Project.category' => 1
                        )
                    )
                ),
                'fields' => array('ProjectEmployeeManager.project_id', 'ProjectEmployeeManager.project_id')
            ));
            $list_project_my_manager = array_merge($list_project_my_manager, $list_project_my_manager_bakcup);
            $conditions['Project.id'] = $list_project_my_manager;
        }
		//1
        $listProject = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array('id', 'project_name', 'project_amr_program_id', 'project_amr_sub_program_id', 'project_code_1', 'project_code_2', 'project_manager_id', 'list_1', 'list_2', 'list_3', 'list_4', 'list_5', 'list_6', 'list_7', 'list_8', 'list_9', 'list_10', 'list_11', 'list_12', 'list_13', 'list_14', 'project_status_id', 'project_priority_id', 'team', 'project_type_id', 'project_sub_type_id', 'project_sub_sub_type_id', 'complexity_id', 'budget_customer_id')
        ));
        // status
        $_listProjectIds = !empty($listProject) ? Set::classicExtract($listProject, '{n}.Project.id') : array();
        $listProjecNames = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_name') : array();
        $listProjectProgramIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_amr_program_id') : array();
        $listProjectSubProgramIds = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_amr_sub_program_id') : array();
        $listCodeProject = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_code_1') : array();
        $listCodeProject1 = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project.project_code_2') : array();
		$listProject = !empty($listProject) ? Set::combine($listProject, '{n}.Project.id', '{n}.Project') : array();
        //
        $listIdModifyByPm = array();
        $roleLogin = $this->employee_info['Role']['name'];
        $this->loadModel('ProjectEmployeeManager');
        if ($roleLogin == 'pm') {
            $prList1 = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $_listProjectIds,
                    'Project.project_manager_id' => $employee_id,
                    'Project.company_id' => $company_id
                ),
                'fields' => array('id', 'id')
            ));
            $prList2 = $this->ProjectEmployeeManager->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectEmployeeManager.project_id' => $_listProjectIds,
                    'ProjectEmployeeManager.project_manager_id' => $employee_id,
					'ProjectEmployeeManager.type !=' => 'RA'
                ),
                'joins' => array(
                    array(
                        'table' => 'projects',
                        'alias' => 'Project',
                        'conditions' => array(
                            'Project.id = ProjectEmployeeManager.project_id',
                            'Project.company_id = ' . $company_id
                        )
                    )
                ),
                'fields' => array('project_id', 'project_id')
            ));
            $listIdModifyByPm = array_unique(array_merge($prList1, $prList2));
        }
        $listProjectbyPM = array();
        if ($roleLogin == 'pm') {
            $listProjectbyPM = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $listIdModifyByPm,
                    'Project.company_id' => $company_id,
                    'Project.category' => 1
                ),
                'fields' => array('id', 'Project.project_name')
            ));
        }
        if ($roleLogin == 'admin') {
            $listProjectbyPM = $this->Project->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.company_id' => $company_id,
                    'Project.category' => 1
                ),
                'fields' => array('id', 'Project.project_name')
            ));
        }
        $listPhasesbyProject = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_keys((array) $listProjectbyPM)
            ),
            'joins' => array(
                array(
                    'table' => 'project_phases',
                    'alias' => 'ProjectsPhase',
                    'conditions' => array(
                        'ProjectPhasePlan.project_planed_phase_id = ProjectsPhase.id',
                        'ProjectsPhase.company_id = ' . $company_id
                    )
                )
            ),
            'fields' => array('ProjectPhasePlan.id', 'ProjectPhasePlan.project_planed_phase_id', 'ProjectPhasePlan.project_id', 'ProjectPhasePlan.phase_planed_start_date', 'ProjectPhasePlan.phase_planed_end_date', 'ProjectPhasePlan.phase_real_start_date', 'ProjectPhasePlan.phase_real_end_date', 'ProjectsPhase.name', 'ProjectPhasePlan.project_part_id')
        ));
		$parts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
			'order' => array('weight' => 'ASC')
		));
        $listPhases = array();
        foreach ($listPhasesbyProject as $Phase) {
            $p = $Phase['ProjectPhasePlan'];
			$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
			
            if (empty($listPhases[$p['project_id']])) $listPhases[$p['project_id']] = array();
			
			if( !empty($part_id) && !empty($parts[$part_id ]) ){
				$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
				$Phase['ProjectsPhase']['name'] = $Phase['ProjectsPhase']['name'].' ('.$part_name.')';
			}
            $listPhases[$p['project_id']][$Phase['ProjectPhasePlan']['id']] = array(
                'id' => $Phase['ProjectPhasePlan']['id'],
                'name' => $Phase['ProjectsPhase']['name'],
                'phase_planed_start_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_planed_start_date'])),
                'phase_real_start_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_real_start_date'])),
                'phase_planed_start_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_planed_start_date'])),
                'phase_real_end_date' => date('d-m-Y', strtotime($Phase['ProjectPhasePlan']['phase_real_end_date'])),
            );
        }
        $this->set(compact('listProjectbyPM', 'listPhases'));

        // list employee assign to
        $prList3 = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'Project.id' => $_listProjectIds,
                'Project.project_manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));

        // task.
        $_conditions = array();
        $_conditions['project_id'] = $_listProjectIds;
        if (!empty($list['nameStatusTask'])) {
            $_conditions['OR'] = array(
                'task_status_id' => $list['nameStatusTask'],
                'task_status_id IS NULL'
            );
        }
		if(!empty($_listProjectIds)){
			$listTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => array_values($_listProjectIds)
				),
				'fields' => array('id')
			));
			if(!empty($listTask)){
				$tasksHasChild = $this->ProjectTask->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'parent_id' => $listTask
					),
					'fields' => array('parent_id', 'parent_id'),
					'group' => array('parent_id')
				));
				if( !empty($tasksHasChild)){
					foreach( $tasksHasChild as $task_id){
						unset( $listTask[$task_id]);
					}
				}
				$_conditions['id'] = $listTask;
			}
		}
        if (!empty($list['nameProrityTask'])) {
            $_conditions['task_priority_id'] = $list['nameProrityTask'];
        }
        if (!empty($list['nameStartDateVision'])) {
            $start = explode('-', $list['nameStartDateVision']);
            $_start = $start[2] . '-' . $start[1] . '-' . $start[0];
            $_conditions['task_start_date >='] = $_start;
        }
        if (!empty($list['nameEndDateVision'])) {
            $end = explode('-', $list['nameEndDateVision']);
            $_end = $end[2] . '-' . $end[1] . '-' . $end[0];
            $_conditions['task_end_date <='] = $_end;
        }
        $this->loadModels('ProjectTaskEmployeeRefer', 'ProjectMilestone');
        if (!empty($list['nameMilestone'])) {
            $m = $this->ProjectMilestone->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_milestone' => $list['nameMilestone']
                ),
                'fields' => array('id', 'id')
            ));
            $_conditions['milestone_id'] = $m;
        }
        // get assign.
        if (($this->params['action'] == 'tasks_vision_new' || $this->params['action'] == 'kanban_vision') && in_array($this->params['pass'][0], array(7, 8, 9))) {
            // truong hop cho list la 7,8,9. assign cho team.
            if (!empty($list['assignedTeam'])) {
                $this->loadModel('Employee');
                $listEmOfTeam = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'profit_center_id' => $list['assignedTeam']
                    ),
                    'fields' => array('id', 'id')
                ));
                $listEmOfTeam[$employee_id] = $employee_id;
                $listTaskAssign = $this->ProjectTaskEmployeeRefer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            array(
                                'reference_id' => $list['assignedTeam'],
                                'is_profit_center' => 1
                            ),
                            array(
                                'reference_id' => $listEmOfTeam,
                                'is_profit_center' => 0
                            )
                        )
                    ),
                    'fields' => array('id', 'project_task_id')
                ));
                if (!empty($listTaskAssign)) {
                    $_conditions['ProjectTask.id'] = $listTaskAssign;
                } else if ($this->params['action'] == 'tasks_vision_new') {
                    // truong hop nay k co gi de xuat hien.
                    $_conditions['ProjectTask.id'] = 0;
                }
            }
        } else {
            if (!empty($list['assignedTeam']) || !empty($list['assignedResources'])) {
                $listTaskAssignForTeam = $listTaskAssignForEmployee = array();
                if (!empty($list['assignedTeam']) && $list['assignedTeam'] != '') {
                    $listTaskAssignForTeam = $this->ProjectTaskEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $list['assignedTeam'],
                            'is_profit_center' => 1
                        ),
                        'fields' => array('id', 'project_task_id')
                    ));
                }
                if (!empty($list['assignedResources']) && $list['assignedResources'] != '') {
                    $listTaskAssignForEmployee = $this->ProjectTaskEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'reference_id' => $list['assignedResources'],
                            'is_profit_center' => 0
                        ),
                        'fields' => array('id', 'project_task_id')
                    ));
                }
                $listTaskAssign = array_unique(array_merge($listTaskAssignForTeam, $listTaskAssignForEmployee));
                if (!empty($listTaskAssign)) {
                    $_conditions['ProjectTask.id'] = $listTaskAssign;
                } else if ($this->params['action'] == 'tasks_vision_new') {
                    // truong hop nay k co gi de xuat hien.
                    $_conditions['ProjectTask.id'] = 0;
                }
            }
        }
        if (!empty($list['nameTaskProject'])) {
            $_conditions['task_title LIKE'] = '%' . $list['nameTaskProject'] . '%';
        }
        if (!empty($_GET['view'])) {
            if ($_GET['view'] == 'today') {
                $_conditions['task_end_date'] = date('Y-m-d', time());
            } elseif ($_GET['view'] == 'week') {
                $week_start = date('Y-m-d', strtotime("-1 weeks"));
                $week_end = date('Y-m-d');
                $_conditions['AND'] = array(
                    'task_end_date >' => $week_start,
                    'task_end_date <=' => $week_end,
                );
            } elseif ($_GET['view'] == 'month') {
                $month_start = date('Y-m-d', strtotime("-30 days"));
                $month_end = date('Y-m-d');
                $_conditions['AND'] = array(
                    'task_end_date >' => $month_start,
                    'task_end_date <=' => $month_end,
                );
            } else {
                $year_start = date('Y-m-d', strtotime("-1 years"));
                $year_end = date('Y-m-d');
                $_conditions['AND'] = array(
                    'task_end_date >' => $year_start,
                    'task_end_date <=' => $year_end,
                );
            }
        }
        // list task for export
        $listTaskExport = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => $_conditions,
            'fields' => array('id', 'task_title', 'project_id', 'project_planed_phase_id', 'task_status_id', 'task_priority_id', 'task_start_date', 'task_end_date', 'text_1', 'text_time', 'text_updater', 'initial_estimated', 'initial_task_start_date', 'initial_task_end_date', 'duration', 'overload', 'amount', 'progress_order', 'milestone_id', 'attachment', 'is_nct'),
            'order' => array('project_id')
        ));
        $projectProgram = $this->ProjectAmrProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'amr_program')
        ));
        $projectSubProgram = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_amr_program_id' => array_keys($projectProgram)),
            'fields' => array('id', 'amr_sub_program')
        ));
        $listStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name')
        ));
        $listPriority = $this->ProjectPriority->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'priority')
        ));
        $milestone = $this->ProjectMilestone->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $_listProjectIds),
            'fields' => array('id', 'project_id', 'project_milestone', 'milestone_date', 'validated')
        ));
		
        $listMilestone = !empty($milestone) ? Set::combine($milestone, '{n}.ProjectMilestone.id', '{n}.ProjectMilestone.project_milestone') : array();
        $_milestoneColor = array();
        foreach ($milestone as $_milestone) {
            $dx = $_milestone['ProjectMilestone'];
            if (!empty($dx['validated'])) {
                $_milestoneColor[$dx['id']] = 'milestone-green';
            } else {
                $currentDate = strtotime(date('d-m-Y', time()));
                $k = strtotime($dx['milestone_date']);
                if ($currentDate > $k) {
                    $_milestoneColor[$dx['id']] = 'milestone-mi';
                } elseif ($currentDate < $k) {
                    $_milestoneColor[$dx['id']] = 'milestone-blue';
                } else {
                    $_milestoneColor[$dx['id']] = 'milestone-orange';
                }
            }
        }
        $listTaskIds = !empty($listTaskExport) ? Set::classicExtract($listTaskExport, '{n}.ProjectTask.id') : array();
        $listPhasePlansIds = !empty($listTaskExport) ? Set::classicExtract($listTaskExport, '{n}.ProjectTask.project_planed_phase_id') : array();
        $listProjectIdOfTasks = !empty($listTaskExport) ? Set::combine($listTaskExport, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
        $_listPhaseAndPartIds = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectPhasePlan.id' => $listPhasePlansIds),
            'fields' => array('id', 'project_planed_phase_id', 'project_part_id')
        ));
        
        $listPhaseIds = !empty($_listPhaseAndPartIds) ? Set::combine($_listPhaseAndPartIds, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_planed_phase_id') : array();
        $listProjectPartIds = !empty($_listPhaseAndPartIds) ? Set::combine($_listPhaseAndPartIds, '{n}.ProjectPhasePlan.id', '{n}.ProjectPhasePlan.project_part_id') : array();
        $this->loadModels('ProjectPhases', 'ProjectParts');
        $listPhases = $this->ProjectPhases->find('all', array(
            'recursive' => -1,
            'conditions' => array('ProjectPhases.id' => $listPhaseIds),
            'fields' => array('id', 'name', 'color')
        ));
		
		$listPhaseNames = !empty($listPhases) ? Set::combine($listPhases, '{n}.ProjectPhases.id', '{n}.ProjectPhases.name') : array();
		$listPhaseColor = !empty($listPhases) ? Set::combine($listPhases, '{n}.ProjectPhases.id', '{n}.ProjectPhases.color') : array();
        $listPartNames = $this->ProjectParts->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectParts.id' => $listProjectPartIds),
            'fields' => array('id', 'title')
        ));
        $listAssign = $this->ProjectTaskEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listTaskIds),
            'fields' => array('id', 'reference_id', 'project_task_id', 'is_profit_center')
        ));
        $pcNames = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $employeeNames = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'fullname')
        ));

        $listIdAssigned = !empty($listAssign) ? Set::classicExtract($listAssign, '{n}.ProjectTaskEmployeeRefer.reference_id') : array();
        $listIdAssigned = !empty($listIdAssigned) ? array_unique(array_values($listIdAssigned)) : array();
        $employeeAssignedAvt = $this->requestAction('employees/get_list_avatar/', array('pass' => array($listIdAssigned)));
        // set name assign
        $listAssignTasks = $listEditStatus = $listAssignTasksFilter = $listResourceAssignName = array();
        foreach ($listAssign as $key => $value) {
            $dx = $value['ProjectTaskEmployeeRefer'];
            if (empty($listAssignTasks[$dx['project_task_id']]))
                $listAssignTasks[$dx['project_task_id']] = array();
            if ($dx['is_profit_center'] == 1) {
                $name = !empty($pcNames[$dx['reference_id']]) ? 'PC / ' . $pcNames[$dx['reference_id']] : '';
            } else {
                $name = !empty($employeeNames[$dx['reference_id']]) ? $employeeNames[$dx['reference_id']] : '';
            }
            $dx['name'] = $name;
            // $listAssignTasks[$dx['project_task_id']][] = $dx;
            $listAssignTasks[$dx['project_task_id']][] = $dx['reference_id'] . '_' . $dx['is_profit_center'];
            $listEditStatus[] = $dx['reference_id'];
            $listAssignTasksFilter[$dx['reference_id'] . '_' . $dx['is_profit_center']] = $dx['name'];
            $listResourceAssignName[$dx['reference_id']] = $dx['name'];
        }
        $listEditStatus = array_unique($listEditStatus);
        // get consume of tasks
        $this->loadModel('ActivityRequest');
        $this->loadModels('ActivityTask', 'HistoryFilter');
        $listActivityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('project_task_id', 'id'),
            'conditions' => array('project_task_id' => $listTaskIds)
        ));
        $this->ActivityRequest->virtualFields['consumed'] = 'SUM(ActivityRequest.value)';
        $consumeOfActivityTasks = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $listActivityTasks,
                'value !=' => 0,
                'status' => 2
            ),
            'fields' => array('task_id', 'consumed'),
            'group' => array('task_id')
        ));
        $this->ActivityRequest->virtualFields['in_used'] = 'SUM(ActivityRequest.value)';
        $inUsedOfActivityTasks = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'task_id' => $listActivityTasks,
                'value !=' => 0,
                'status !=' => 2
            ),
            'fields' => array('task_id', 'in_used'),
            'group' => array('task_id')
        ));
        $workloadOfTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.id' => $listTaskIds
            ),
            'fields' => array('id', 'estimated')
        ));
        $datas = array();
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        // filter.
        $curDate = time();
        // tranh truong hop buoi chieu => sai so.
        $curDate = date('d-m-Y', $curDate);
        $curDate = strtotime($curDate);
        $clStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status' => 'CL'
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id')
        ));
        $clStatus = !empty($clStatus) ? array_values($clStatus) : array();
        $filter_color = $this->HistoryFilter->find('first', array('recursive' => -1, 'conditions' => array('employee_id' => $employee_id, 'path' => 'vision_task_filter_color')));
        $filter_color = !empty($filter_color['HistoryFilter']['params']) ? $filter_color['HistoryFilter']['params'] : '';
		
		$this->loadModels('ProjectTaskAttachments', 'ProjectTaskAttachmentView', 'ProjectTaskTxt', 'ProjectTaskTxtRefer');
		// attachment
		$projectTaskAttachments = $this->ProjectTaskAttachments->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'task_id' => $listTaskIds,
				'attachment !=' => null
			),
			'fields' => array('task_id')
		));
		
		$projectTaskAttachments = $this->ProjectTaskAttachments->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'task_id' => $listTaskIds,
				'attachment !=' => null
			),
			'fields' => array('id', 'task_id', 'attachment')
		));
		$attachedFile = !empty($projectTaskAttachments) ? Set::combine($projectTaskAttachments, '{n}.ProjectTaskAttachments.id', '{n}.ProjectTaskAttachments.attachment') : array();
		$projectTaskAttachments = !empty($projectTaskAttachments) ? Set::combine($projectTaskAttachments, '{n}.ProjectTaskAttachments.id', '{n}.ProjectTaskAttachments.task_id') : array();
		$projectTaskAttachmentRead = $this->ProjectTaskAttachmentView->find('list', array(
				'recursive' => -1,
                'conditions' => array(
					'task_id' => $listTaskIds,
					'employee_id' => $this->employee_info['Employee']['id']
				),
			'fields' => array('task_id', 'read_status')
		));
		
		//comment
		$projectTaskComments = $this->ProjectTaskTxt->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => $listTaskIds,
				'comment !=' => null
			),
			'fields' => array('id', 'project_task_id')
		));
		$projectTaskCommentRead = $this->ProjectTaskTxtRefer->find('list', array(
				'recursive' => -1,
                'conditions' => array(
					'task_id' => $listTaskIds,
					'employee_id' => $this->employee_info['Employee']['id']
				),
			'fields' => array('task_id', 'read_status')
		));
		
        foreach ($listTaskExport as $key => $value) {
            $dx = $value['ProjectTask'];
            $taskId = $dx['id'];
            // lay program
            if (!empty($listProjectIdOfTasks[$taskId])) {
                $datas[$taskId]['id'] = $dx['id'];
                $datas[$taskId]['project_name'] = $listProjecNames[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['task_title'] = $dx['task_title'];
                $datas[$taskId]['assigned'] = !empty($listAssignTasks[$dx['id']]) ? $listAssignTasks[$dx['id']] : array();
                $datas[$taskId]['start_date'] = $str_utility->convertToVNDate($dx['task_start_date']);
                $datas[$taskId]['end_date'] = $str_utility->convertToVNDate($dx['task_end_date']);
                $datas[$taskId]['end_date_format'] = date('d F Y', strtotime($dx['task_end_date']));
                $datas[$taskId]['task_end_date'] = date('d F Y', strtotime($dx['task_end_date']));
                $datas[$taskId]['workload'] = $workloadOfTasks[$taskId];
                $datas[$taskId]['project_id'] = $listProjectIdOfTasks[$taskId];
                $datas[$taskId]['code_project_1'] = $listCodeProject[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['code_project_2'] = $listCodeProject1[$listProjectIdOfTasks[$taskId]];
                $datas[$taskId]['initial_estimated'] = $dx['initial_estimated'];
                $datas[$taskId]['initial_task_start_date'] = $dx['initial_task_start_date'];
                $datas[$taskId]['initial_task_end_date'] = $dx['initial_task_end_date'];
                $datas[$taskId]['duration'] = $dx['duration'];
                $datas[$taskId]['overload'] = $dx['overload'];
                $datas[$taskId]['amount'] = $dx['amount'];
                $datas[$taskId]['progress_order'] = $dx['progress_order'];
                $datas[$taskId]['is_nct'] = $dx['is_nct'];
                $datas[$taskId]['project_planed_name'] = !empty($listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]]) ? $listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]] : '';
                $datas[$taskId]['project_planed_color'] = !empty($listPhaseColor[$listPhaseIds[$dx['project_planed_phase_id']]]) ? $listPhaseColor[$listPhaseIds[$dx['project_planed_phase_id']]] : '';
                if (!empty($listActivityTasks[$taskId])) {
                    $datas[$taskId]['consume'] = (!empty($consumeOfActivityTasks[$listActivityTasks[$taskId]]) ) ? round($consumeOfActivityTasks[$listActivityTasks[$taskId]], 2) : 0;
                } else {
                    $datas[$taskId]['consume'] = 0.00;
                }
                if (!empty($listActivityTasks[$taskId])) {
                    $datas[$taskId]['in_used'] = (!empty($inUsedOfActivityTasks[$listActivityTasks[$taskId]]) ) ? $inUsedOfActivityTasks[$listActivityTasks[$taskId]] : 0;
                } else {
                    $datas[$taskId]['in_used'] = 0;
                }
                // completed
                if ($datas[$taskId]['consume'] == 0 || $datas[$taskId]['workload'] == 0) {
                    $datas[$taskId]['completed'] = 0;
                } else {
                    $_cal = round($datas[$taskId]['consume'] / ($datas[$taskId]['workload'] + $datas[$taskId]['overload']) * 100, 2);
                    $datas[$taskId]['completed'] = ($_cal > 100) ? 100 : $_cal;
                }
                // attachment attachment
                $datas[$taskId]['attachment_count'] = 0;
                $datas[$taskId]['attach_read_status'] = 0;
                if (!empty($dx['attachment_count'])) {
                    $datas[$taskId]['attachment_count'] = 1;
                    $datas[$taskId]['attach_read_status'] = 1;
                }
				
				foreach( $projectTaskAttachments as $_id => $_task_id){
					if( $_task_id == $taskId){
						$datas[$taskId]['attachment_count'] += 1;
						$datas[$taskId]['attach_read_status'] = 0;
					}
				}
                if (array_key_exists($taskId, $projectTaskAttachmentRead))
                    $datas[$taskId]['attach_read_status'] = intval($projectTaskAttachmentRead[$taskId]);
				
				
                //remain
                $datas[$taskId]['remain'] = round($datas[$taskId]['workload'] - $datas[$taskId]['consume'] + $datas[$taskId]['overload'], 2);
                $datas[$taskId]['text'] = $dx['text_1'];


                if ($this->params['action'] == 'tasks_vision' || $this->params['action'] == 'tasks_vision_new' || $this->params['action'] == 'kanban_vision') {
                    $datas[$taskId]['part_name'] = !empty($listProjectPartIds[$dx['project_planed_phase_id']]) ? $listProjectPartIds[$dx['project_planed_phase_id']] : '';
                    $datas[$taskId]['phase_name'] = !empty($listPhaseIds[$dx['project_planed_phase_id']]) ? $listPhaseIds[$dx['project_planed_phase_id']] : '';
                    $datas[$taskId]['status'] = !empty($dx['task_status_id']) ? $dx['task_status_id'] : '';
                    $datas[$taskId]['task_status_id'] = !empty($dx['task_status_id']) ? $dx['task_status_id'] : '';
                    $datas[$taskId]['milestone'] = !empty($dx['milestone_id']) ? $dx['milestone_id'] : '';
                    $datas[$taskId]['priority'] = !empty($dx['task_priority_id']) ? $dx['task_priority_id'] : '';
                    $datas[$taskId]['amr_program'] = !empty($listProjectProgramIds[$listProjectIdOfTasks[$taskId]]) ? $listProjectProgramIds[$listProjectIdOfTasks[$taskId]] : '';
                    $datas[$taskId]['sub_amr_program'] = !empty($listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]) ? $listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]] : '';
                    $sDate = strtotime($dx['task_start_date']);
                    $eDate = strtotime($dx['task_end_date']);
					if (($datas[$taskId]['consume'] == 0 && $sDate < $curDate && $datas[$taskId]['workload'] > 0 && (!in_array($datas[$taskId]['status'],$clStatus))) || (!empty($datas[$taskId]['status']) && (!in_array($datas[$taskId]['status'], $clStatus)) && $eDate < $curDate) || ($datas[$taskId]['workload'] < $datas[$taskId]['consume'])) {
						$datas[$taskId]['task_late'] = true;
						$datas[$taskId]['task_late_status'] = 'red';
					}else{
						$datas[$taskId]['task_late'] = false;
						$datas[$taskId]['task_late_status'] = 'green';
					}
					
					// commment
					$datas[$taskId]['comment_count'] = 0;
					$datas[$taskId]['read_status'] = 0;
					foreach( $projectTaskComments as $_id => $_task_id){
						if( $_task_id == $taskId){
							$datas[$taskId]['comment_count'] += 1;
							$datas[$taskId]['read_status'] = 0;
						}
					}
					if ( array_key_exists($taskId, $projectTaskCommentRead )) $datas[$taskId]['read_status'] = intval($projectTaskCommentRead[$taskId]);
                } else {
                    if (!empty($listProjectPartIds[$dx['project_planed_phase_id']])) {
                        $datas[$taskId]['part_name'] = !empty($listPartNames[$listProjectPartIds[$dx['project_planed_phase_id']]]) ? $listPartNames[$listProjectPartIds[$dx['project_planed_phase_id']]] : '';
                    }
                    if (!empty($listPhaseIds[$dx['project_planed_phase_id']])) {
                        $datas[$taskId]['phase_name'] = !empty($listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]]) ? $listPhaseNames[$listPhaseIds[$dx['project_planed_phase_id']]] : '';
                    }
                    $datas[$taskId]['status'] = !empty($listStatus[$dx['task_status_id']]) ? $listStatus[$dx['task_status_id']] : '';
                    $datas[$taskId]['task_status_id'] = !empty($dx['task_status_id']) ? $dx['task_status_id'] : '';
                    $datas[$taskId]['milestone'] = !empty($dx['milestone_id']) && !empty($listMilestone[$dx['milestone_id']]) ? $listMilestone[$dx['milestone_id']] : '';
                    $datas[$taskId]['priority'] = !empty($listPriority[$dx['task_priority_id']]) ? $listPriority[$dx['task_priority_id']] : '';
                    if (!empty($projectProgram)) {
                        $datas[$taskId]['amr_program'] = (!empty($projectProgram[$listProjectProgramIds[$listProjectIdOfTasks[$taskId]]]) || !empty($listProjectProgramIds[$listProjectIdOfTasks[$taskId]]) ) ? $projectProgram[$listProjectProgramIds[$listProjectIdOfTasks[$taskId]]] : '';
                    }
                    $datas[$taskId]['sub_amr_program'] = (!empty($projectSubProgram[$listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]]) || !empty($listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]])) ? $projectSubProgram[$listProjectSubProgramIds[$listProjectIdOfTasks[$taskId]]] : '';
                }
            }
        }
		
        $this->set(compact('listStatus', 'listPriority', 'projectProgram', 'projectSubProgram', 'listIdModifyByPm', 'roleLogin', 'listPartNames', 'listPhaseNames', 'filter_color', 'listMilestone', '_milestoneColor', 'listAssignTasksFilter', 'listResourceAssignName', 'attachedFile'));
        //permission edit text of task.
        $this->loadModels('ProjectEmployeeManager', 'ProjectTaskTxt');
        $listEmployeeManager = $this->ProjectEmployeeManager->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => array_unique($listProjectIdOfTasks),
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'type !=' => 'RA',
                'is_profit_center' => 0
            )
        ));
        $listEmployeeManagerOfT = array();
        $listEmployeeManager = !empty($listEmployeeManager) ? Set::combine($listEmployeeManager, '{n}.ProjectEmployeeManager.project_id', '{n}.ProjectEmployeeManager.project_id') : array();
        foreach ($listProjectIdOfTasks as $key => $value) {
            if (!empty($listEmployeeManager[$value]))
                $listEmployeeManagerOfT[$key] = $key;
        }
        $fullName = $this->employee_info['Employee']['fullname'];
        $employee_id = $this->employee_info['Employee']['id'];
        $listIdAva = $this->ProjectTaskTxt->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_task_id' => array_keys($listProjectIdOfTasks)
            ),
            'fields' => array('project_task_id', 'employee_id'),
            'order' => array('created' => 'DESC')
        ));
        // query comment in new table ;
        // Get comment
        $this->loadModel('ProjectTaskTxt');
        $projectTaskTxts = $this->ProjectTaskTxt->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $listTaskIds),
            'joins' => array(array(
                    'table' => 'employees',
                    'alias' => 'Employee',
                    'type' => 'left',
                    'conditions' => array(
                        'ProjectTaskTxt.employee_id = Employee.id',
                    )
                )),
            'fields' => array('ProjectTaskTxt.id', 'ProjectTaskTxt.project_task_id', 'ProjectTaskTxt.comment', 'Employee.first_name', 'Employee.last_name', 'ProjectTaskTxt.created', 'Employee.id')
        ));
        // Add comment and attachment status

        foreach ($datas as $_taskID => $projectTask) {
            if ($projectTask['id'] != 0) {
                // $_taskID = $projectTask['ProjectTask']['id'];
                if (empty($datas[$_taskID]['text_empl']))
                    $datas[$_taskID]['text_empl'] = $this->employee_info['Employee']['id'];
                if (empty($datas[$_taskID]['text_updater']))
                    $datas[$_taskID]['text_updater'] = $this->employee_info['Employee']['first_name'] . ' ' . $this->employee_info['Employee']['last_name'];
                foreach ($projectTaskTxts as $taskTxt) {
                    if ($_taskID == $taskTxt['ProjectTaskTxt']['project_task_id']) {
                        $new_update = strtotime($taskTxt['ProjectTaskTxt']['created']);
                        $old_update = strtotime(!empty($projectTask['ProjectTask']['text_time']) ? $projectTask['ProjectTask']['text_time'] : 0);

                        if ($new_update > $old_update) {
                            $datas[$_taskID]['text'] = $taskTxt['ProjectTaskTxt']['comment'];
                            $datas[$_taskID]['text_updater'] = $taskTxt['Employee']['first_name'] . ' ' . $taskTxt['Employee']['last_name'];
                            $datas[$_taskID]['text_empl'] = $taskTxt['Employee']['id'];
                        }
                        $datas[$_taskID]['text_time'] = max($new_update, $old_update);
                    }
                }
                $datas[$_taskID]['ed_format'] = !empty($projectTask['end_date']) ? date('d-m-Y-W', strtotime($projectTask['end_date'])) : '';
                $datas[$_taskID]['cu_format'] = date('d-m-Y-W');
            }
        }
		/* #1821 - Update VisionTaskExport from translate */
		$this->loadModels('VisionTaskExport', 'Translation', 'TranslationEntry', 'TranslationSetting');
		$visionTaskExports = $this->VisionTaskExport->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'order' => array('weight' => 'ASC')
		));
		$translation_displayed = array();
		if( !empty( $visionTaskExports )){
			foreach( $visionTaskExports as $v){
				$v = $v['VisionTaskExport'];
				if( !empty( $v['translation_id']) && ($v['display']==1) ){
					$translation_displayed[$v['name']] = $v['name'];
				}
			}
		}
		$translation_saved = !empty($visionTaskExports) ? Set::classicExtract($visionTaskExports, '{n}.VisionTaskExport.translation_id') : array();
		$translation_saved = array_filter( $translation_saved);
		$fieldset = !empty($visionTaskExports) ? Set::combine($visionTaskExports, '{n}.VisionTaskExport.name', '{n}.VisionTaskExport.id') : array();
		$visionTaskExports = !empty($visionTaskExports) ? Set::combine($visionTaskExports, '{n}.VisionTaskExport.id', '{n}.VisionTaskExport') : array();
		$this->TranslationSetting->virtualFields['custom_order'] = 'CASE
			WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
			WHEN TranslationSetting.setting_order IS NULL THEN 999
		END';
		$this->Translation->hasMany = array(
			'TranslationEntry' => array(
				'className' => 'TranslationEntry',
				'foreignKey' => 'translation_id',
				'conditions' => array(
					'TranslationEntry.company_id' => $company_id
					
				),
				'fields' => array(
					'TranslationEntry.text', 
					'TranslationEntry.code',
					'TranslationEntry.language',
				)
			),
			'TranslationSetting' => array(
				'className' => 'TranslationSetting',
				'foreignKey' => 'translation_id',
				'conditions' => array(
					'TranslationSetting.company_id' => $company_id,
					// 'TranslationSetting.show' => 1
				),
				'fields' => array(
					'TranslationSetting.translation_id', 
					'TranslationSetting.show',
					'TranslationSetting.custom_order',
				),
			)
		);
		$translateData = $this->Translation->find('all', array(
			// 'recursive' => -1,
			'conditions' => array(
				'Translation.page' => 'Details',
				'Translation.original_text' => $this->tasks_vision_display_your_form_fields(),
			),
			'contain' => array('TranslationEntry', 'TranslationSetting'),
			'fields' => array(
				'Translation.id','Translation.original_text',
			),
		));
		$translateData = !empty( $translateData) ? Set::sort($translateData, '{n}.TranslationSetting.0.custom_order', 'asc') : array();
		$translateData = !empty( $translateData) ? Set::combine($translateData, '{n}.Translation.id', '{n}') : array();
		$translate_ids = array();
		foreach( $translateData as $_t_id => $_translation){
			$_show = isset($_translation['TranslationSetting'][0]['show']) ? $_translation['TranslationSetting'][0]['show'] : 0;
			if( $_show) $translate_ids[] = $_t_id;
		}
		$this->VisionTaskExport->deleteAll(
			array(
				'VisionTaskExport.company_id' => $company_id,
				'NOT' => array(
					'VisionTaskExport.translation_id is NULL',
					'VisionTaskExport.translation_id' => $translate_ids,
				)
			), false
		);
		$_last = end( $visionTaskExports);
		$max_weight =  $_last['weight'];
		foreach ( $translateData as $k => $v){
			$_show = isset($v['TranslationSetting'][0]['show']) ? $v['TranslationSetting'][0]['show'] : 0;
			$_name = $v['Translation']['original_text'];
			if( !$_show) {
				unset( $translation_displayed[$_name]);
				continue;
			}
			$_t_id = $v['Translation']['id'];
			$_t_entry = !empty( $v['TranslationEntry'] ) ? Set::combine($v['TranslationEntry'], '{n}.code', '{n}.text') : array();
			$update_data = array(
				'company_id' => $company_id,
				'translation_id' => $_t_id,
				'name' => $_name,
				'english' => !empty( $_t_entry['eng'] ) ? $_t_entry['eng'] : $_name,
				'france' => !empty( $_t_entry['fre'] ) ? $_t_entry['fre'] : $_name,
				// 'display' => 0,
				// 'weight' => ++$max_weight,
			);
			if( !in_array($_t_id, $translation_saved)){
				$this->VisionTaskExport->create();
				$update_data['display'] = 0;
				$update_data['weight'] = ++$max_weight;
			}else{
				$this->VisionTaskExport->id = $fieldset[$_name];
			}
			$this->VisionTaskExport->save($update_data);
		}
		
			/* Get Data for your form fields */
			$projects_data = array();
			$projectDatasets = $budgetCustomers = $projectComplexities = $projectSubTypes = $projectTypes = $profitCenters = $projectPhases = array();
			if(!empty($listProject)) foreach( $translation_displayed as $org_text){
				switch ($org_text) {
					case 'Project Manager':
					case 'Technical manager':
					case 'Read Access':
					case 'UAT manager':
					case 'Chief Business':
					case 'Functional leader':
						if(empty($pmProjects)){
							$pmProjects = array();
							$this->loadModels('Project', 'ProjectEmployeeManager');
							$pmProjects = $this->ProjectEmployeeManager->find('all', array(
								'recursive' => -1,
								'conditions' => array(
									'project_id' => $_listProjectIds,
								),
								'fields' => array('project_manager_id', 'project_id', 'is_profit_center', 'type')
							));
							$_types = array(
								'PM' => 'Project Manager',
								'TM' => 'Technical manager',
								'RA' => 'Read Access',
								'UM' => 'UAT manager',
								'CB' => 'Chief Business',
								'FL' => 'Functional leader',
							);
							if( !empty( $pmProjects )){
							foreach( $pmProjects as $pmProject){
								$pmProject = $pmProject['ProjectEmployeeManager'];
								$_type = $_types[$pmProject['type']];
								$_p_id = $pmProject['project_id'];
								$projects_data[$_p_id][$_type][] = $pmProject['project_manager_id'] . '_' . intval( $pmProject['is_profit_center']);
							}
							}
							foreach($listProject as $p_id => $pr){
								if( !empty($pr['project_manager_id']))
								$projects_data[$p_id]['Project Manager'][] = $pr['project_manager_id'] . '_0';
								unset($listProject[$p_id]['project_manager_id']);
							}
							foreach($projects_data as $p_id => $pr){
								foreach( $pr as $_type => $_pm_data)
								$listProject[$p_id][$_type] = array_unique( $_pm_data);
							}
						}
						break;
					case 'List 1':
					case 'List 2':
					case 'List 3':
					case 'List 4':
					case 'List 5':
					case 'List 6':
					case 'List 7':
					case 'List 8':
					case 'List 9':
					case 'List 10':
					case 'List 11':
					case 'List 12':
					case 'List 13':
					case 'List 14':
						if( empty( $model)) {
							$variable = 'projectDatasets';
							$model = 'ProjectDataset';
							$key = str_replace(' ', '_', strtolower($org_text));
							$conds = array(
								'company_id' => $company_id,
								// 'dataset_name' => $key,
							);
						}
						if( empty( $projectDatasets)){
							$this->loadModel('ProjectDataset');
							$projectDatasets = $this->ProjectDataset->find('all', array(
								'recursive' => -1,
								'conditions' => $conds,
								// 'fields' => array('*')
							));
							$projectDatasets = !empty( $projectDatasets) ? Set::combine($projectDatasets, '{n}.ProjectDataset.id', '{n}.ProjectDataset.name', '{n}.ProjectDataset.dataset_name') : array();
							$this->set(compact('projectDatasets'));
						}
					break;
					case 'Project type':
						if( empty( $model)) {
							$key = 'project_type_id';
							$variable = 'projectTypes';
							$model = 'ProjectType';
							$input_assoc[] = $model;
							$conds = array(
								'company_id' => $company_id,
								'display' => 1,
							);
						}
					case 'Sub type':
					case 'Sub sub type':
						if( empty( $model)) {
							$variable = 'projectSubTypes';
							$model = 'ProjectSubType';
							$conds = array(
								'display' => 1,
							);
						}
					case 'Implementation Complexity':
						if( empty( $model)) {
							$variable = 'projectComplexities';
							$model = 'ProjectComplexity';
							$input_assoc[] = $model;
							$conds = array(
								'company_id' => $company_id,
								'display' => 1,
							);
						}
					case 'Customer':
						if( empty( $model)) {
							$variable = 'budgetCustomers';
							$model = 'BudgetCustomer';
							$input_assoc[] = $model;
							$conds = array(
								'company_id' => $company_id,
							);
						}
					case 'Team':
						if( empty( $model)) {
							$variable = 'profitCenters';
							$model = 'ProfitCenter';
							$conds = array(
								'company_id' => $company_id,
							);
						}
						$this->loadModel($model);
						$displayField = $this->$model->displayField;
						if( empty( $$variable)){
							$$variable = $this->$model->find('list', array(
								'recursive' => -1,
								'conditions' => $conds,
								'fields' => array('id', $displayField)
							));
						}
						$this->set(compact($variable));				
					break;
					case 'List(multiselect) 1':
					case 'List(multiselect) 2':
					case 'List(multiselect) 3':
					case 'List(multiselect) 4':
					case 'List(multiselect) 5':
					case 'List(multiselect) 6':
					case 'List(multiselect) 7':
					case 'List(multiselect) 8':
					case 'List(multiselect) 9':
					case 'List(multiselect) 10':
						$this->loadModels('ProjectListMultiple', 'ProjectDataset');
						if( empty($projectListMultiData)){
						$projectListMultiData = $this->ProjectListMultiple->find('all', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $_listProjectIds),
							'fields' => array('*')
						));
						foreach($projectListMultiData as $k => $v){
							$v = $v['ProjectListMultiple'];
							$p_id = $v['project_id'];
							$listProject[$p_id][$v['key']][] = $v['project_dataset_id'];
						}
						}
						if( empty( $projectDatasets)){
							$this->loadModel('ProjectDataset');
							$projectDatasets = $this->ProjectDataset->find('all', array(
								'recursive' => -1,
								'conditions' => $conds,
								// 'fields' => array('*')
							));
							$projectDatasets = !empty( $projectDatasets) ? Set::combine($projectDatasets, '{n}.ProjectDataset.id', '{n}.ProjectDataset.name', '{n}.ProjectDataset.dataset_name') : array();
						}
						$this->set(compact('projectDatasets'));
					
					break;
					case 'Current Phase':
						$this->loadModels('ProjectPhaseCurrent','ProjectPhase');
						$phaseCurrent = $this->ProjectPhaseCurrent->find('list', array(
							'recursive' => -1,
							'conditions' => array('project_id' => $_listProjectIds),
							'fields' => array('id', 'project_phase_id', 'project_id')
						));
						foreach($phaseCurrent as $p_id => $v){
							$listProject[$p_id]['Current Phase'] = !empty($v) ? array_values($v) : array();
						}
						$projectPhases = $this->ProjectPhase->find('list', array(
							'fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 
							'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 
							'order' => 'ProjectPhase.phase_order ASC'
						));
						$this->set(compact('projectPhases'));
					break;
				}
				unset( $model);
			
			}
		$companyEmployees = array();
		$company_employees = $this->_getListEmployee(null, true);
		foreach( $company_employees as $id => $e){
			if( !empty( $e['is_pc'])){
				$id = str_replace('-', '_', $id);
				$companyEmployees[$id] = $e['name'];
			}else{
				$id = $id. '_0';
				$companyEmployees[$id] = $e['fullname'];
			}
		}
		$this->set( compact('projectDatasets', 'budgetCustomers', 'projectComplexities', 'projectSubTypes', 'projectTypes', 'profitCenters', 'projectPhases', 'companyEmployees'));
		/* END #1821 - Update VisionTaskExport from translate */
		
        $this->set(compact('listEmployeeManagerOfT', 'fullName', 'listIdAva', 'employee_id', 'listEditStatus', 'employeeAssignedAvt', 'listAssign','listProject'));
        return $datas;
    }

    public function update_vision_task() {
        $result = false;
        $this->layout = false;
        $this->loadModel('ProjectTaskTxt');
        if (!empty($this->data)) {
            $data = array(
                'task_status_id' => !empty($this->data['Status']) ? $this->data['Status'] : '',
                'text_1' => !empty($this->data['Text']) ? $this->data['Text'] : ''
            );
            if (!empty($data['text_1']) && !empty($this->data['id'])) {
                $this->ProjectTaskTxt->create();
                $this->ProjectTaskTxt->save(array(
                    'project_task_id' => $this->data['id'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'comment' => $data['text_1']
                ));
            }
            if (!empty($this->data['id'])) {
                $this->ProjectTask->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->ProjectTask->save($data)) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->ProjectTask->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function export() {
        if (!empty($this->data)) {
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Task Visions')  //auto translate
                    ->save($data, 'task_vision_{date}.xls');
        }
        die;
    }

    public function updateWeather() {
        if (!empty($this->data)) {
			$this->_checkRole(false, $this->data['project_id']);
            $key = explode('.', $this->data['key']);
            $this->loadModel('ProjectAmr');
            $find = $this->ProjectAmr->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $this->data['project_id']),
                'fields' => array('id')
            ));
            $id = !empty($find) ? $find['ProjectAmr']['id'] : 0;
            $this->ProjectAmr->id = $id;
            $this->ProjectAmr->save(array(
                'project_id' => $this->data['project_id'],
                $key[1] => $this->data['val']
            ));
        }
        die;
    }

    // get data for your form (your_form, your_form_1, your_form_2, your_form_3, your_form_4).
    public function getDataForYourForm($page, $curentPage, $id) {
        $this->loadModel('ProjectEmployeeManager');
        // Check the role
        $employeeInfo = $this->Session->read("Auth.employee_info");
        $employee_id = $employeeInfo['Employee']['id'];
        $this->loadModel('ProfileProjectManager');
        $profileName = array();
        if (!empty($employeeInfo['Employee']['profile_account'])) {
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employeeInfo['Employee']['profile_account']
                )
            ));
        }
        $this->set(compact('profileName'));
        $checkReadAccess = $this->ProjectEmployeeManager->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA',
                'project_manager_id' => $employee_id
            )
        ));
        $this->set(compact('checkReadAccess'));
        $changeProjectManager = !(empty($employeeInfo['Role']) || $employeeInfo['Role']['name'] == 'conslt' || $employeeInfo['Role']['name'] == 'pm');
        if ($employeeInfo['Role']['name'] == 'pm') {
            $checkIsManager = $this->Project->find('first', array(
                'conditions' => array(
                    'Project.id' => $id,
                    'Project.project_manager_id' => $employee_id
                )
            ));
            $changeProjectManager = !empty($checkIsManager) ? true : false;
        }

        if ($employeeInfo) {
            $fullname = $employeeInfo['Employee']['first_name'] . " " . $employeeInfo['Employee']['last_name'];
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }

        // security
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        $activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;

        $company_idd = $company_id_of_project['Project']['company_id'];
        if ($company_id_of_project['Project']['company_id'] == "") {
            $this->redirect(array('action' => 'index'));
        }
        if ($this->is_sas != 1) {
            $company_id_of_project = $company_id_of_project['Project']['company_id'];
            $parent_id_of_company_id_of_project = $this->Project->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_project)));
            if ($parent_id_of_company_id_of_project['Company']['parent_id'] != null) {
                $parent_id_of_company_id_of_project = $parent_id_of_company_id_of_project['Company']['parent_id'];
            } else
                $parent_id_of_company_id_of_project = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_project)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_project == "" || $company_id_of_admin != $parent_id_of_company_id_of_project) {
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        // security
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $company_id = $this->Project->find("first", array(
            'recursive' => 0,
            "fields" => array("Project.company_id", 'Company.company_name'),
            'conditions' => array('Project.id' => $id)));

        $name_company = $company_id['Company']['company_name'];
        $company_id = $company_id['Project']['company_id'];
        $famAndSubFamOfPrograms = $this->ProjectAmrProgram->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectAmrProgram.company_id ' => $company_id
            ),
            'fields' => array('id', 'family_id', 'sub_family_id')
        ));
        $famAndSubFamOfPrograms = !empty($famAndSubFamOfPrograms) ? Set::combine($famAndSubFamOfPrograms, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram') : array();
        $subFams = $this->ProjectAmrSubProgram->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => array_keys($famAndSubFamOfPrograms)),
            'fields' => array('id', 'sub_family_id')
        ));
        /**
         * Lay tat ca cac phase cua 1 project
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        // lay list multi select.
        $_ProjectMultiLists = $this->ProjectListMultiple->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id
            ),
            'fields' => array('id', 'project_dataset_id', 'key')
        ));
        $ProjectMultiLists = array();
        if (!empty($_ProjectMultiLists)) {
            foreach ($_ProjectMultiLists as $_ProjectMultiList) {
                $dx = $_ProjectMultiList['ProjectListMultiple'];
                $ProjectMultiLists[$dx['key']][$dx['id']] = $dx['project_dataset_id'];
            }
        }
        $this->set('ProjectMultiLists', $ProjectMultiLists);
        // get image for projects.
        $images = $this->ProjectImage->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'company_id' => $company_id,
                'is_file' => 1
            )
        ));
        $this->set('images', $images);
        // lay project document upload
        $projectFiles = $this->ProjectFile->find('all', array(
            'recurisve' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'key', 'file_attachment', 'size', 'project_id', 'type')
        ));
        $projectFiles = !empty($projectFiles) ? Set::combine($projectFiles, '{n}.ProjectFile.id', '{n}.ProjectFile', '{n}.ProjectFile.key') : array();
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $this->set(compact('adminSeeAllProjects'));
        $this->set('projectFiles', $projectFiles);
        if (!empty($this->data)) {
            if ($this->data['Project']['category'] != 2) {
                $this->data['Project']['is_staffing'] = 0;
            }
            if (!empty($this->data['Project']['tmp_activity']) || $this->data['Project']['tmp_activity'] == 0) {
                if ($this->data['Project']['tmp_activity'] == 0) {
                    $this->data['Project']['activity_id'] = null;
                } else {
                    $this->data['Project']['activity_id'] = $this->data['Project']['tmp_activity'];
                }
                unset($this->data['Project']['tmp_activity']);
            }
            /**
             * Xu ly project manager
             */
            if (!empty($this->data['project_employee_manager'])) {
                $this->data['project_employee_manager'] = array_unique($this->data['project_employee_manager']);
                if (($key = array_search(0, $this->data['project_employee_manager'])) !== false) {
                    unset($this->data['project_employee_manager'][$key]);
                }
            }
            // if(!empty($this->data['is_backup'])){
            //     $this->data['is_backup'] = array_unique($this->data['is_backup']);
            //     if(($key = array_search(0, $this->data['is_backup'])) !== false) {
            //         unset($this->data['is_backup'][$key]);
            //     }
            // }
            /**
             * Xu ly read access
             */
            if (!empty($this->data['read_access'])) {
                $this->data['read_access'] = array_unique($this->data['read_access']);
                if (($key = array_search(0, $this->data['read_access'])) !== false) {
                    unset($this->data['read_access'][$key]);
                }
            }
            /**
             * Xu ly chief_business_list
             */
            if (!empty($this->data['chief_business_list'])) {
                $this->data['chief_business_list'] = array_unique($this->data['chief_business_list']);
                if (($key = array_search(0, $this->data['chief_business_list'])) !== false) {
                    unset($this->data['chief_business_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_chief'])){
            //     $this->data['is_backup_chief'] = array_unique($this->data['is_backup_chief']);
            //     if(($key = array_search(0, $this->data['is_backup_chief'])) !== false) {
            //         unset($this->data['is_backup_chief'][$key]);
            //     }
            // }
            /**
             * Xu ly technical_manager_list
             */
            if (!empty($this->data['technical_manager_list'])) {
                $this->data['technical_manager_list'] = array_unique($this->data['technical_manager_list']);
                if (($key = array_search(0, $this->data['technical_manager_list'])) !== false) {
                    unset($this->data['technical_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_tech'])){
            //     $this->data['is_backup_tech'] = array_unique($this->data['is_backup_tech']);
            //     if(($key = array_search(0, $this->data['is_backup_tech'])) !== false) {
            //         unset($this->data['is_backup_tech'][$key]);
            //     }
            // }
            /*
             * Xu li functional leader
             */
            if (!empty($this->data['functional_leader_list'])) {
                $this->data['functional_leader_list'] = array_unique($this->data['functional_leader_list']);
                if (($key = array_search(0, $this->data['functional_leader_list'])) !== false) {
                    unset($this->data['functional_leader_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_lead'])){
            //     $this->data['is_backup_lead'] = array_unique($this->data['is_backup_lead']);
            //     if(($key = array_search(0, $this->data['is_backup_lead'])) !== false) {
            //         unset($this->data['is_backup_lead'][$key]);
            //     }
            // }
            /*
             * Xu li uat manager
             */
            if (!empty($this->data['uat_manager_list'])) {
                $this->data['uat_manager_list'] = array_unique($this->data['uat_manager_list']);
                if (($key = array_search(0, $this->data['uat_manager_list'])) !== false) {
                    unset($this->data['uat_manager_list'][$key]);
                }
            }
            // if(!empty($this->data['is_backup_uat'])){
            //     $this->data['is_backup_uat'] = array_unique($this->data['is_backup_uat']);
            //     if(($key = array_search(0, $this->data['is_backup_uat'])) !== false) {
            //         unset($this->data['is_backup_uat'][$key]);
            //     }
            // }
            if (!empty($this->data['project_phase_id'])) {
                $this->data['project_phase_id'] = array_unique($this->data['project_phase_id']);
                if (($key = array_search(0, $this->data['project_phase_id'])) !== false) {
                    unset($this->data['project_phase_id'][$key]);
                }
            }
            for ($i = 1; $i <= 16; $i++) {
                $key = 'price_' . $i;
                $this->data['Project'][$key] = str_replace(" ", "", $this->data['Project'][$key]);
            }
            // save list multi select.
            for ($i = 1; $i <= 10; $i++) {
                $key = 'project_list_multi_' . $i;
                if (!empty($this->data[$key])) {
                    $oldList = array();
                    foreach ($this->data[$key] as $listId) {
                        if (!empty($listId) && $listId != 0) {
                            if (!empty($ProjectMultiLists[$key]) && in_array($listId, $ProjectMultiLists[$key])) { // kiem tra xem no da co ton tai chua. neu chua thi tao moi
                                $oldList[] = $listId;
                            } else {
                                $this->ProjectListMultiple->create();
                                $this->ProjectListMultiple->save(array(
                                    'project_id' => $id,
                                    'project_dataset_id' => $listId,
                                    'key' => $key
                                ));
                            }
                        }
                    }
                    if (!empty($ProjectMultiLists[$key])) {
                        foreach ($ProjectMultiLists[$key] as $_id => $listId) {
                            if (in_array($listId, $oldList)) { // kiem tra xem list moi co khong? neu list cu co ma list moi khong co thi delete.
                                //do nothing
                            } else {
                                $this->ProjectListMultiple->delete($_id);
                            }
                        }
                        unset($this->data[$key]);
                    }
                }
            }

            $countEmployeePM = isset($this->data['project_employee_manager']) ? (count($this->data['project_employee_manager']) - 1) : 0;
            // $countBackupPM = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            // if($employeeInfo['Role']['name'] == 'admin' && $countEmployeePM != $countBackupPM){
            //     $this->data = $this->Project->read(null, $id);
            //     $this->Session->setFlash(__('Please select at least a project manager without the flag backup.', true), 'error');
            // } else {
            $activityLinked = !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0;
            if (!empty($this->data['project_employee_manager'])) {
                // if(!empty($this->data['is_backup'])){
                //     $this->data['Project']['project_manager_id'] = array_diff($this->data['project_employee_manager'], $this->data['is_backup']);
                //     $this->data['Project']['project_manager_id'] = !empty($this->data['Project']['project_manager_id']) ? array_shift($this->data['Project']['project_manager_id']) : '';
                // } else {
                $this->data['Project']['project_manager_id'] = !empty($this->data['project_employee_manager']) ? array_shift($this->data['project_employee_manager']) : '';
                // }
            } else {
                $this->data['Project']['project_manager_id'] = '';
            }
            if (!empty($this->data['chief_business_list'])) {
                // if(!empty($this->data['is_backup_chief'])){
                //     $this->data['Project']['chief_business_id'] = array_diff($this->data['chief_business_list'], $this->data['is_backup_chief']);
                //     $this->data['Project']['chief_business_id'] = !empty($this->data['Project']['chief_business_id']) ? array_shift($this->data['Project']['chief_business_id']) : '';
                // } else {
                $this->data['Project']['chief_business_id'] = !empty($this->data['chief_business_list']) ? array_shift($this->data['chief_business_list']) : '';
                // }
            } else {
                $this->data['Project']['chief_business_id'] = '';
            }
            if (!empty($this->data['technical_manager_list'])) {
                // if(!empty($this->data['is_backup_tech'])){
                //     $this->data['Project']['technical_manager_id'] = array_diff($this->data['technical_manager_list'], $this->data['is_backup_tech']);
                //     $this->data['Project']['technical_manager_id'] = !empty($this->data['Project']['technical_manager_id']) ? array_shift($this->data['Project']['technical_manager_id']) : '';
                // } else {
                $this->data['Project']['technical_manager_id'] = !empty($this->data['technical_manager_list']) ? array_shift($this->data['technical_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['technical_manager_id'] = '';
            }
            if (!empty($this->data['functional_leader_list'])) {
                // if(!empty($this->data['is_backup_lead'])){
                //     $this->data['Project']['functional_leader_id'] = array_diff($this->data['functional_leader_list'], $this->data['is_backup_lead']);
                //     $this->data['Project']['functional_leader_id'] = !empty($this->data['Project']['functional_leader_id']) ? array_shift($this->data['Project']['functional_leader_id']) : '';
                // } else {
                $this->data['Project']['functional_leader_id'] = !empty($this->data['functional_leader_list']) ? array_shift($this->data['functional_leader_list']) : '';
                // }
            } else {
                $this->data['Project']['functional_leader_id'] = '';
            }
            if (!empty($this->data['uat_manager_list'])) {
                // if(!empty($this->data['is_backup_uat'])){
                //     $this->data['Project']['uat_manager_id'] = array_diff($this->data['uat_manager_list'], $this->data['is_backup_uat']);
                //     $this->data['Project']['uat_manager_id'] = !empty($this->data['Project']['uat_manager_id']) ? array_shift($this->data['Project']['uat_manager_id']) : '';
                // } else {
                $this->data['Project']['uat_manager_id'] = !empty($this->data['uat_manager_list']) ? array_shift($this->data['uat_manager_list']) : '';
                // }
            } else {
                $this->data['Project']['uat_manager_id'] = '';
            }

            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (isset($this->data["Project"]["start_date"]))
                $this->data["Project"]["start_date"] = $str_utility->convertToSQLDate($this->data["Project"]["start_date"]);
            if (isset($this->data["Project"]["end_date"]))
                $this->data["Project"]["end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["end_date"]);
            if (isset($this->data["Project"]["planed_end_date"]))
                $this->data["Project"]["planed_end_date"] = $str_utility->convertToSQLDate($this->data["Project"]["planed_end_date"]);

            $data_project_teams = $this->params['form'];
            $project_id = $this->data["Project"]["id"];

            if (!empty($this->data['Project']['project_amr_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_program_id'] = $this->data['Project']['project_amr_program_id'];
            }
            if (!empty($this->data['Project']['project_amr_sub_program_id'])) {
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $this->data['Project']['project_amr_sub_program_id'];
            }
            $checkBackup = $this->ProjectEmployeeManager->find('count', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id, 'project_manager_id' => $employeeInfo['Employee']['id'], 'type' => 'PM')
            ));
            if (!$changeProjectManager && $employeeInfo['Role']['name'] != 'admin') {
                if (!empty($checkBackup)) {
                    //do nothing
                } else {
                    $Model4 = ClassRegistry::init('Employee');
                    $employInfors = $Model4->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $employeeInfo['Employee']['id']),
                        'fields' => array('update_your_form')
                    ));
                    if (!empty($employInfors) && !empty($employInfors['Employee']['update_your_form'])) {
                        // thang nay dc chinh sua tat ca projet...ke me no
                    } else {
                        $this->data['Project']['project_manager_id'] = $employeeInfo['Employee']['id'];
                    }
                }
            }
            $data_arm['ProjectAmr']['project_manager_id'] = $this->data['Project']['project_manager_id'];
            if (!empty($this->data['ProjectAmr']['weather'][0])) {
                $data_arm['ProjectAmr']['weather'] = $this->data['ProjectAmr']['weather'][0];
            }
            if (!empty($this->data['ProjectAmr']['rank'][0])) {
                $data_arm['ProjectAmr']['rank'] = $this->data['ProjectAmr']['rank'][0];
            }
            $change_role = $this->CompanyEmployeeReference->find("first", array(
                'conditions' => array("CompanyEmployeeReference.employee_id" => $this->data['Project']['project_manager_id']),
                'fields' => array('CompanyEmployeeReference.id', 'CompanyEmployeeReference.employee_id', 'CompanyEmployeeReference.role_id', 'Role.id', 'Role.name')));
            $this->data['Project']['update_by_employee'] = !empty($employeeInfo['Employee']['fullname']) ? $employeeInfo['Employee']['fullname'] : '';
            $oldProject = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $this->data['Project']['id']),
                'fields' => array('activity_id')
            ));
            if (!empty($oldProject['Project']['activity_id'])) {
                //da linked roi ko lam gi ca
            } else {
                $this->data['Project']['activity_id'] = $activityLinked;
            }
            if (!empty($this->data['Project']['activity_id'])) {
                //bat dau linked
            } else {
                unset($this->data['Project']['activity_id']);
            }
            if ($activityLinked == -1) {
                $this->data['Project']['activity_id'] = null;
            }
            $this->data['Project']['last_modified'] = time();
            $range = range(1, 14);
            foreach ($range as $num) {
                if (isset($this->data['Project']['date_' . $num])) {
                    $this->data['Project']['date_' . $num] = $str_utility->convertToSQLDate($this->data['Project']['date_' . $num]);
                }
            }
            $this->loadModel('Activity');
            // rename activity linked
            $oldProjectName = $this->Project->find("first", array(
                'recursive' => -1,
                "fields" => array('project_name'),
                'conditions' => array('Project.id' => $id)));
            $oldProjectName = !empty($oldProjectName) ? $oldProjectName['Project']['project_name'] : '';
            $newProjectName = !empty($this->data) && !empty($this->data['Project']['project_name']) ? $this->data['Project']['project_name'] : '';
            if ($oldProjectName != $newProjectName) {
                $ActivityLinkedName = $this->Activity->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id', 'name', 'long_name', 'short_name'),
                    'conditions' => array(
                        'project' => $id
                    )
                ));
                if (!empty($ActivityLinkedName)) {
                    $saved['name'] = $newProjectName;
                    $idActivityLinked = $ActivityLinkedName['Activity']['id'];
                    $this->Activity->id = $idActivityLinked;
                    $this->Activity->save($saved);
                }
            }
            // end
            // truong hop pm co quyen change status nhung khong the update your form.
            if ($employeeInfo['Role']['name'] == 'pm' && $employeeInfo['Employee']['change_status_project'] == 1 && $employeeInfo['Employee']['update_your_form'] == 0) {
                unset($this->data['Project']['project_manager_id']);
                unset($this->data['Project']['chief_business_id']);
                unset($this->data['Project']['technical_manager_id']);
                unset($this->data['Project']['functional_leader_id']);
                unset($this->data['Project']['uat_manager_id']);
            }
            if ($this->Project->save($this->data)) {
                $projectName = $this->Project->find("first", array(
                    'recursive' => -1,
                    "fields" => array('project_name', 'company_id'),
                    'conditions' => array('Project.id' => $id)));
                $this->writeLog($this->data, $this->employee_info, sprintf('Update project `%s` via Details', $projectName['Project']['project_name']), $projectName['Project']['company_id']);
                $id = $this->Project->id;
                if ($activityLinked == -1) {
                    $activityLinked = 0;
                }
                if (!empty($activityLinked) && $activityLinked != 0 && isset($this->data['Project']['activated'])) {
                    $this->Activity->id = $activityLinked;
                    $idProgram = !empty($this->data['Project']['project_amr_program_id']) ? $this->data['Project']['project_amr_program_id'] : '';
                    $idSubProgram = !empty($this->data['Project']['project_amr_sub_program_id']) ? $this->data['Project']['project_amr_sub_program_id'] : '';
                    $fam = !empty($famAndSubFamOfPrograms[$idProgram]['family_id']) ? $famAndSubFamOfPrograms[$idProgram]['family_id'] : '';
                    $sfam = !empty($subFams[$idSubProgram]) ? $subFams[$idSubProgram] : '';
                    if (!empty($this->data['Project']['category']) && $this->data['Project']['category'] == 1 && $activate_family_linked_program) {
                        if (!empty($fam)) {
                            $this->Activity->save(array(
                                'project_manager_id' => $this->data['Project']['project_manager_id'],
                                'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                                'activated' => $this->data['Project']['activated'],
                                'family_id' => $fam,
                                'subfamily_id' => $sfam
                            ));
                        }
                    } else {
                        $this->Activity->save(array(
                            'project_manager_id' => $this->data['Project']['project_manager_id'],
                            'budget_customer_id' => $this->data['Project']['budget_customer_id'],
                            'activated' => $this->data['Project']['activated']
                        ));
                    }
                }
                $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
                $this->ProjectAmr->id = $ProjectArms['ProjectAmr']['id'];
                $this->ProjectAmr->save($data_arm);
                if ($change_role['Role']['name'] == 'conslt') {
                    $roles = $this->Employee->CompanyEmployeeReference->Role->find('first', array('fields' => array('Role.id'), 'conditions' => array('Role.name' => 'pm')));
                    $this->CompanyEmployeeReference->id = $change_role['CompanyEmployeeReference']['id'];
                    $this->CompanyEmployeeReference->saveField('role_id', $roles['Role']['id']);
                }
                //link project - activity
                if (!empty($this->data['Project']['activity_id'])) {
                    // update tmp
                    $this->loadModel('TmpStaffingSystem');
                    $this->TmpStaffingSystem->updateAll(
                            array('TmpStaffingSystem.activity_id' => $this->data['Project']['activity_id']), array('TmpStaffingSystem.project_id' => $id)
                    );
                    // update project budget sync
                    $this->loadModel('ProjectBudgetSyn');
                    $this->ProjectBudgetSyn->updateAll(
                            array('ProjectBudgetSyn.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetSyn.project_id' => $id)
                    );
                    // update project provisional
                    $this->loadModel('ProjectBudgetProvisional');
                    $this->ProjectBudgetProvisional->updateAll(
                            array('ProjectBudgetProvisional.activity_id' => $this->data['Project']['activity_id']), array('ProjectBudgetProvisional.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlus');
                    $this->ProjectFinancePlus->updateAll(
                            array('ProjectFinancePlus.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlus.project_id' => $id)
                    );
                    // update project FinancePlus
                    $this->loadModel('ProjectFinancePlusDetail');
                    $this->ProjectFinancePlusDetail->updateAll(
                            array('ProjectFinancePlusDetail.activity_id' => $this->data['Project']['activity_id']), array('ProjectFinancePlusDetail.project_id' => $id)
                    );
                    $this->loadModel('ActivityTask');
                    $this->loadModel('ProjectStatus');
                    $activityIdTask = $this->data['Project']['activity_id'];
                    if (isset($activityIdTask) && !empty($activityIdTask) && $activityIdTask != '' && $activityIdTask != null) {
                        $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.activity_id' => $activityIdTask)));
                        $projectStatus = $this->ProjectStatus->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('ProjectStatus.status' => 'IP', 'company_id' => $company_id),
							'order' => array('weight' => 'ASC'),
                            'fields' => array('id')
                        ));
                        $projectStatus = $projectStatus['ProjectStatus']['id'];
                        $projectTasks = $this->Project->ProjectTask->find('all', array('recursive' => -1,
                            'conditions' => array('ProjectTask.project_id' => $id,
                                'ProjectTask.task_status_id' => $projectStatus)));

                        $listProjectPhase = ClassRegistry::init('ProjectPhase')->find('list', array('recursive' => -1, 'conditions' => array('company_id' => $company_id, 'ProjectPhase.activated' => 1)));
                        $listPlan = ClassRegistry::init('ProjectPhasePlan')->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('project_id' => $id),
                            'fields' => array('id', 'project_planed_phase_id')
                        ));
                        $activity = ClassRegistry::init('Activity')->find('first', array('recursive' => -1,
                            'conditions' => array('Activity.id' => $this->data['Project']['activity_id']),
                            'fields' => array('id', 'project')));

                        $this->loadModel('Activity');

                        if (!empty($activity)) {
                            $activities = ClassRegistry::init('Activity')->find('all', array(
                                'recursive' => -1,
                                'conditions' => array('Activity.project' => $id),
                                'fields' => array('id', 'project')
                            ));
                            if (!empty($activities)) {
                                foreach ($activities as $act) {
                                    $this->Activity->id = $act['Activity']['id'];
                                    $this->Activity->saveField('project', null);
                                }
                            }
                            $this->Activity->id = $activity['Activity']['id'];
                            $this->Activity->saveField('project', $id);
                        }

                        foreach ($projectTasks as $projectTask) {
                            $project_task_id[] = $projectTask['ProjectTask']['id'];
                        }
                        if (!empty($project_task_id)) {
                            $activityTasks = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('ActivityTask.project_task_id' => $project_task_id)));
                        }
                        foreach ($activityTasks as $activityTask) {
                            $this->ActivityTask->id = $activityTask['ActivityTask']['id'];
                            $this->ActivityTask->delete();
                        }
                        $dataActivityTask = array();
                        foreach ($projectTasks as $projectTask) {
                            $idProjectPhase = $projectTask['ProjectTask']['project_planed_phase_id'];
                            $_name = isset($idProjectPhase) ? $listPlan[$idProjectPhase] : '';
                            $_name = !empty($_name) ? $listProjectPhase[$_name] : '';
                            $nameProjectTask = $projectTask['ProjectTask']['task_title'];
                            $this->ActivityTask->create();
                            $dataActivityTask['ActivityTask']['name'] = $_name . '/ ' . $nameProjectTask;
                            $dataActivityTask['ActivityTask']['activity_id'] = $activityIdTask;
                            $dataActivityTask['ActivityTask']['project_task_id'] = $projectTask['ProjectTask']['id'];
                            $this->ActivityTask->save($dataActivityTask['ActivityTask']);
                        }
                    }
                }
                /**
                 * Save project_employee_manager
                 * Lay danh sach project_employee_manager da tao
                 */
                $listEmployeeRefers = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id
                    ),
                    'fields' => array('id', 'id', 'type'),
                    'group' => array('type', 'id')
                ));

                if (!empty($this->data['project_employee_manager'])) {
                    foreach ($this->data['project_employee_manager'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'PM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['project_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['PM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!$adminSeeAllProjects) {
                    if (!empty($this->data['read_access'])) {
                        foreach ($this->data['read_access'] as $value) {
                            $value = explode('-', $value);
                            $is_profit = 1;
                            if ($value[1] == 0) {
                                $is_profit = 0;
                            }
                            $dataRefers = array(
                                'project_manager_id' => $value[0],
                                // 'is_backup' => 0,
                                'project_id' => $id,
                                'type' => 'RA',
                                'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0,
                                'is_profit_center' => $is_profit
                            );
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['RA'][$lastEmployRefers]);
                            }
                        }
                    }
                    if (!empty($listEmployeeRefers['RA'])) {
                        $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['RA']), false);
                    }
                    unset($listEmployeeRefers['RA']);
                }
                if (!empty($listEmployeeRefers['PM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['PM']), false);
                }
                unset($listEmployeeRefers['PM']);

                if (!empty($this->data['chief_business_list'])) {
                    foreach ($this->data['chief_business_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_chief']) && in_array($value, $this->data['is_backup_chief'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'CB',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['chief_business_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['CB'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['CB'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['CB']), false);
                }
                unset($listEmployeeRefers['CB']);


                if (!empty($this->data['technical_manager_list'])) {
                    foreach ($this->data['technical_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_tech']) && in_array($value, $this->data['is_backup_tech'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'TM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['TM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['TM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['TM']), false);
                }
                unset($listEmployeeRefers['TM']);

                //functional leader & uat manager
                if (!empty($this->data['functional_leader_list'])) {
                    foreach ($this->data['functional_leader_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_lead']) && in_array($value, $this->data['is_backup_lead'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'FL',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['FL'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['FL'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['FL']), false);
                }
                unset($listEmployeeRefers['FL']);
                //uat manager
                if (!empty($this->data['uat_manager_list'])) {
                    foreach ($this->data['uat_manager_list'] as $value) {
                        // $is_backup = 0;
                        // if(!empty($this->data['is_backup_uat']) && in_array($value, $this->data['is_backup_uat'])){
                        //     $is_backup = 1;
                        // }
                        $dataRefers = array(
                            'project_manager_id' => $value,
                            // 'is_backup' => $is_backup,
                            'project_id' => $id,
                            'type' => 'UM',
                            'activity_id' => !empty($this->data['Project']['tmp_activity_id']) ? $this->data['Project']['tmp_activity_id'] : 0
                        );
                        if ($value != $this->data['Project']['technical_manager_id']) {
                            $checkDatas = $this->ProjectEmployeeManager->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->ProjectEmployeeManager->create();
                            if (!empty($checkDatas) && $checkDatas['ProjectEmployeeManager']['id']) {
                                $this->ProjectEmployeeManager->id = $checkDatas['ProjectEmployeeManager']['id'];
                            }
                            if ($this->ProjectEmployeeManager->save($dataRefers)) {
                                $lastEmployRefers = $this->ProjectEmployeeManager->id;
                                unset($listEmployeeRefers['UM'][$lastEmployRefers]);
                            }
                        }
                    }
                }
                if (!empty($listEmployeeRefers['UM'])) {
                    $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.id' => $listEmployeeRefers['UM']), false);
                }
                unset($listEmployeeRefers['UM']);

                $oldPhase = array();
                if (!empty($this->data['project_phase_id'])) {
                    foreach ($this->data['project_phase_id'] as $phaseId) {
                        if (!empty($phaseId)) {
                            if (in_array($phaseId, $phasePlans)) { // ton tai trong phase plan of project
                                $oldPhase[] = $phaseId;
                            } else {
                                $saved = array(
                                    'project_id' => $id,
                                    'project_phase_id' => $phaseId
                                );
                                $this->ProjectPhaseCurrent->create();
                                $this->ProjectPhaseCurrent->save($saved);
                            }
                        }
                    }
                }
                foreach ($phasePlans as $idPlan => $phaseId) {
                    if (in_array($phaseId, $oldPhase)) {
                        //do nothing
                    } else {
                        $this->ProjectPhaseCurrent->delete($idPlan);
                    }
                }
                $this->Session->setFlash(__('Saved', true));
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect("/projects/" . $curentPage . "/" . $project_id);
        }
        // }
        if (empty($this->data)) {
            $this->data = $this->Project->read(null, $id);
        }
        $IdTask = $this->data['Project']['activity_id'];
        $this->set('famAndSubFamOfPrograms', $famAndSubFamOfPrograms);
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id, 'ProjectPhase.activated' => 1), 'order' => 'ProjectPhase.phase_order ASC')));
        $this->set('Complexities', $this->Project->ProjectComplexity->find('list', array('fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 'conditions' => array('ProjectComplexity.company_id ' => $company_id, 'ProjectComplexity.display' => 1), 'order' => 'ProjectComplexity.name ASC')));
        $this->set('Priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id ' => $company_id))));
        $this->set('Statuses', $this->Project->ProjectStatus->find('list', array('order' => array('weight' => 'ASC'),'fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id ' => $company_id))));
        $this->set('ProjectTypes', $this->Project->ProjectType->find('list', array('fields' => array('ProjectType.id', 'ProjectType.project_type'), 'conditions' => array('ProjectType.company_id ' => $company_id, 'ProjectType.display' => 1))));
		$ProjectSubTypes = $this->ProjectSubType->find('list', array('fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 'conditions' => array('ProjectSubType.project_type_id ' => $this->data['Project']['project_type_id'], 'ProjectSubType.display' => 1)));
		$projectSubSubTypes = array();
		if(!empty($this->data['Project']['project_sub_type_id'])){
			$projectSubSubTypes = $this->ProjectSubType->find('list', array(
			'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 
			'conditions' => array(
				'parent_id ' => $this->data['Project']['project_sub_type_id'], 
				'display' => 1,
				)
			));
		}
        $this->set(compact('ProjectSubTypes', 'projectSubSubTypes'));
        $this->set('ProjectArmPrograms', $this->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'), 'conditions' => array('ProjectAmrProgram.company_id ' => $company_id))));
        $this->set('ProjectArmSubPrograms', $this->ProjectAmrSubProgram->find('list', array('fields' => array('ProjectAmrSubProgram.id', 'ProjectAmrSubProgram.amr_sub_program'), 'conditions' => array('ProjectAmrSubProgram.project_amr_program_id ' => $this->data['Project']['project_amr_program_id']))));
        $this->set('ProjectActivities', ClassRegistry::init('Activity')->find('list', array('fields' => array('Activity.id', 'Activity.name'), 'conditions' => array('Activity.company_id ' => $company_id, 'OR' => array('Activity.pms' => 1, 'NOT' => array('Activity.project' => null))))));
        $this->set('listIdActivity', $this->Project->find('list', array('recursive' => -1, 'fields' => array('Project.activity_id'), 'conditions' => array('Project.company_id' => $company_id))));

        // Find Task and Activities in request then block the combo box
        $find_tasks_in_activity = ClassRegistry::init('ActivityTask')->find('all', array('recursive' => -1, 'conditions' => array('activity_id' => $IdTask)));
        $find_tasks_in_activity = Set::combine($find_tasks_in_activity, '{n}.ActivityTask.id', '{n}.ActivityTask');
        $find_tasks_in_activity = array_keys($find_tasks_in_activity);
        // Check if the project is linked to any activity or not
        if (isset($IdTask)) {
            if (!empty($find_tasks_in_activity)) {
                $conditions = array('task_id' => $find_tasks_in_activity);
                $activityRequest = ClassRegistry::init('ActivityRequest')->find(
                        'count', array(
                    'recursive' => -1,
                    'conditions' => array(
                        "OR" => array(
                            'activity_id' => $IdTask,
                            $conditions
                        )
                    )
                        )
                );
            } else {
                $activityRequest = 0;
            }
        } else {
            $activityRequest = 0;
        }
		
        $activityRequest = json_encode($activityRequest);
        // Check activities and tasks, if assigned then lock the combo box
        $this->set('activityRequest', $activityRequest);

        $companyEmployees = $this->Project->Employee->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'role_id'),
            'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
		$curentDate = date('Y-m-d');
		$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
        $projectEmployees = $this->ProjectTask->Employee->find('all', array(
            'order' => 'last_name  ASC',
            'recursive' => -1,
            'conditions' => array(
                'id' => array_keys($companyEmployees),
				'Employee.last_name NOT' => 'NULL'
            ),
            'fields' => array('first_name', 'last_name', 'id', 'avatar_resize','actif', 'available')));
        $_employees = $avatarOfEmploys = array();
        foreach ($projectEmployees as $projectEmployee) {
            $_employees['project'][$projectEmployee['Employee']['id']]['full_name'] = $projectEmployee['Employee']['first_name'] . ' ' . $projectEmployee['Employee']['last_name'];
			$em_actif = intval($projectEmployee['Employee']['actif']) ? intval($projectEmployee['Employee']['available']) : 0;
			$_employees['project'][$projectEmployee['Employee']['id']]['actif'] = $em_actif;
            foreach (array('pm' => array(3, 2), 'tech' => array(3, 5)) as $key => $role) {
                if (in_array($companyEmployees[$projectEmployee['Employee']['id']], $role)) {
                    $_employees[$key][$projectEmployee['Employee']['id']] = $_employees['project'][$projectEmployee['Employee']['id']];
                }
            }
            $avatarOfEmploys[$projectEmployee['Employee']['id']] = $projectEmployee['Employee']['avatar_resize'];
        }
        $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_idd)));

        // edit get start end date from Phase
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('phase_real_start_date' => '0000-00-00', 'phase_real_end_date' => '0000-00-00')
            ),
            'fields' => array(
                'MIN(phase_real_start_date) as MinStartDate',
                'MAX(phase_real_end_date) as MaxEndDate'
            )
        ));
        if (!empty($projectPhasePlans)) {
            $_datas['start_date'] = $projectPhasePlans[0][0]['MinStartDate'];
            $_datas['end_date'] = $projectPhasePlans[0][0]['MaxEndDate'];
            $this->Project->id = $id;
            $this->Project->save($_datas);
        }
        $this->set(compact('projectPhasePlans'));
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $listEmployeeManagers = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'NOT' => array('type' => 'RA')
            ),
            'fields' => array('project_manager_id', 'is_backup', 'type'),
            'group' => array('type', 'project_manager_id')
        ));

        $listReadAccess = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'type' => 'RA'
            ),
            'fields' => array('project_manager_id', 'is_profit_center', 'type'),
            'group' => array('type', 'project_manager_id')
        ));
        $listEmployeeManagers['RA'] = !empty($listReadAccess) && !empty($listReadAccess['RA']) ? $listReadAccess['RA'] : array();

        if (!empty($this->data['Project']['project_manager_id'])) {
            $listEmployeeManagers['PM'][$this->data['Project']['project_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['chief_business_id'])) {
            $listEmployeeManagers['CB'][$this->data['Project']['chief_business_id']] = 0;
        }
        if (!empty($this->data['Project']['technical_manager_id'])) {
            $listEmployeeManagers['TM'][$this->data['Project']['technical_manager_id']] = 0;
        }
        if (!empty($this->data['Project']['functional_leader_id'])) {
            $listEmployeeManagers['FL'][$this->data['Project']['functional_leader_id']] = 0;
        }
        if (!empty($this->data['Project']['uat_manager_id'])) {
            $listEmployeeManagers['UM'][$this->data['Project']['uat_manager_id']] = 0;
        }
        $employBackups = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => array_keys($listEmployeeManagers)),
            'fields' => array('id', 'first_name', 'last_name')
        ));
        /**
         * Lay cac phase co task
         */
        $phaseHaveTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('project_planed_phase_id', 'project_planed_phase_id')
        ));
        /**
         * Viet lai lan 2, get lai phase sau khi thuc hien save
         */
        $phasePlans = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'project_phase_id')
        ));
        /**
         * Change date 07/04/2014.
         * Get all family + sub family of company
         */
        $families = $subFamilies = array();
        $haveActivity = 'false';
        $this->loadModel('Family');
        $_families = $this->Family->find('all', array(
            'order' => array('name ASC'),
            'recursive' => -1,
            'fields' => array('id', 'name', 'parent_id'),
            'conditions' => array('company_id' => $employeeInfo['Company']['id'])));
        if (!empty($_families)) {
            foreach ($_families as $_family) {
                $dx = $_family['Family'];
                if (!empty($dx['parent_id'])) {
                    //sub family
                } else {
                    $families[$dx['id']] = $dx['name'];
                }
            }
            if (!empty($families)) {
                $this->loadModel('ActivityFamily');
                $family_ids = array_keys($families);
                $subFamilies = $this->ActivityFamily->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'parent_id' => array_shift($family_ids)
                    ),
                    'fields' => array('id', 'name'),
                    'order' => array('name')
                ));
            }
        }
        if ($employeeInfo['Role']['name'] === 'admin' || $employeeInfo['Role']['name'] === 'pm') {
            /**
             * Kiem tra xem activity da co timesheet nao chua
             */
            if (!empty($this->data['Project']['activity_id']) || $this->data['Project']['activity_id'] != 0) {
                $this->loadModel('ActivityRequest');
                $this->loadModel('ActivityTask');
                $tasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $this->data['Project']['activity_id']),
                    'fields' => array('id', 'id')
                ));
                if (!empty($tasks)) {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'OR' => array(
                                'activity_id' => $this->data['Project']['activity_id'],
                                'task_id' => $tasks
                            )
                        )
                    ));
                } else {
                    $request = $this->ActivityRequest->find('count', array(
                        'recursive' => -1,
                        'conditions' => array('activity_id' => $this->data['Project']['activity_id'])
                    ));
                }
                if ($request != 0) {
                    $haveActivity = 'true';
                }
            }
        }
        /**
         * Get list budget customer
         */
        $this->loadModel('BudgetCustomer');
        $budgetCustomers = $this->BudgetCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $employeeInfo['Company']['id']),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('phaseHaveTasks', 'phasePlans', 'avatarOfEmploys', 'listEmployeeManagers', 'employBackups', 'employeeInfo', 'families', 'subFamilies', 'haveActivity', 'budgetCustomers'));
        $this->set(compact('project_name', '_employees', 'currency_name', 'name_company', 'changeProjectManager', 'activate_family_linked_program'));
        $this->set('project_id', $id);
        $this->loadModel('Translation');
        $translation_data = $this->Translation->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'page' => $page,
                'TranslationSetting.company_id' => $company_id
            ),
            'fields' => '*',
            'joins' => array(
                array(
                    'table' => 'translation_settings',
                    'alias' => 'TranslationSetting',
                    'conditions' => array(
                        'Translation.id = TranslationSetting.translation_id'
                    ),
                    'type' => 'left'
                )
            ),
            'order' => array(
                'TranslationSetting.setting_order' => 'ASC'
            )
        ));
        /**
         * get dataset
         */
        $this->loadModel('ProjectDataset');
        $rawDatasets = $this->ProjectDataset->find('all', array(
            'conditions' => array('company_id' => $employeeInfo['Company']['id'], 'display' => 1),
            'fields' => array('id', 'name', 'dataset_name'),
            'order' => array(
                'ProjectDataset.name' => 'ASC'
            )
        ));
        $datasets = array(
            'list_1' => array(),
            'list_2' => array(),
            'list_3' => array(),
            'list_4' => array()
        );
        foreach ($rawDatasets as $set) {
            $s = $set['ProjectDataset'];
            $datasets[$s['dataset_name']][$s['id']] = $s['name'];
        }
        // lay milestone for next milestone.
        $nextMilestone = $this->ProjectMilestone->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $id,
                'milestone_date >' => date('Y-m-d', time())
            ),
            'fields' => array('id', 'milestone_date'),
            'order' => array('milestone_date' => 'ASC')
        ));
        $nextMilestoneByDay = $nextMilestoneByWeek = 0;
        if (!empty($nextMilestone)) {
            $nextMilestone = $nextMilestone['ProjectMilestone']['milestone_date'];
            $nextMilestone = strtotime($nextMilestone);
            $now = time();
            if ($now < $nextMilestone) {
                $diff = ($nextMilestone - $now) / (24 * 60 * 60) + 1;
                $nextMilestoneByDay = (int) ($diff);
                $nextMilestoneByWeek = (int) ($diff / 7);
            }
        }
        $profitCenters = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name')
        ));
        $this->set(compact('nextMilestoneByDay', 'nextMilestoneByWeek', 'profitCenters'));
        $this->set('datasets', $datasets);
        $this->set('translation_data', $translation_data);
    }

    // your_form_1
    function your_form_1($id = null) {
		$this->_checkRole(false, $id);
        $page = 'Details_1';
        $curentPage = 'your_form_1';
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_1');
        $this->set('page', $page);
        $this->render('your_form');
    }

    // your_form_2
    function your_form_2($id = null) {
		$this->_checkRole(false, $id);
        $page = 'Details_2';
        $curentPage = 'your_form_2';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $this->_checkWriteProfile('your_form_2');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    // your_form_3
    function your_form_3($id = null) {
		$this->_checkRole(false, $id);
        $page = 'Details_3';
        $curentPage = 'your_form_3';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_3');
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    // your_form_4
    function your_form_4($id = null) {
		$this->_checkRole(false, $id);
        $page = 'Details_4';
        $curentPage = 'your_form_4';
        $this->getDataForYourForm($page, $curentPage, $id);
        $this->_checkWriteProfile('your_form_4');
        $this->set('page', $page);
        $this->loadModel('BudgetSetting');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $this->set('budget_settings', $budget_settings);
        $this->render('your_form');
    }

    public function saveFilterSortVisionTask() {
        $result = false;
        $data = '';
        if (!empty($_POST)) {
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModel('HistoryFilter');
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'path' => 'vision_task_filter_color'
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $data = $this->HistoryFilter->save(array('params' => $_POST['which']));
            } else {
                $this->HistoryFilter->create();
                $data = $this->HistoryFilter->save(array(
                    'params' => $_POST['which'],
                    'path' => 'vision_task_filter_color',
                    'employee_id' => $employee_id
                ));
            }
        }
        if ($data)
            $result = true;
        die(json_encode(array(
            'data' => $data,
            'result' => $result,
        )));
    }
	private function tasks_vision_display_your_form_fields(){
		$your_form_fields = array(
			// select fields
			'list_1' => 'List 1',
			'list_2' => 'List 2',
			'list_3' => 'List 3',
			'list_4' => 'List 4',
			'list_5' => 'List 5',
			'list_6' => 'List 6',
			'list_7' => 'List 7',
			'list_8' => 'List 8',
			'list_9' => 'List 9',
			'list_10' => 'List 10',
			'list_11' => 'List 11',
			'list_12' => 'List 12',
			'list_13' => 'List 13',
			'list_14' => 'List 14',
			'project_type_id' => 'Project type',
			'project_sub_type_id' => 'Sub type',
			'project_sub_sub_type_id' => 'Sub sub type',
			'complexity_id' => 'Implementation Complexity',
			'budget_customer_id' => 'Customer',
			// 'project_priority_id' => 'Priority',
			// 'project_status_id' => 'Status',
			'team' => 'Team',
			// multi-select fields
			'list_muti_1' => 'List(multiselect) 1',
			'list_muti_10' => 'List(multiselect) 10',
			'list_muti_2' => 'List(multiselect) 2',
			'list_muti_3' => 'List(multiselect) 3',
			'list_muti_4' => 'List(multiselect) 4',
			'list_muti_5' => 'List(multiselect) 5',
			'list_muti_6' => 'List(multiselect) 6',
			'list_muti_7' => 'List(multiselect) 7',
			'list_muti_8' => 'List(multiselect) 8',
			'list_muti_9' => 'List(multiselect) 9',
			'project_phase_id' => 'Current Phase',
			// PM fields
			'project_manager_id' => 'Project Manager',
			'read_access' => 'Read Access',
			'technical_manager_id' => 'Technical manager',
			'uat_manager_id' => 'UAT manager',
			'chief_business_id' => 'Chief Business',
			'functional_leader_id' => 'Functional leader',
		);
		return $your_form_fields;
	}
    function tasks_vision_new($style = 1) {
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModel('ProfitCenterManagerBackup');
		$bg_currency = $this->getCurrencyOfBudget();
        $manager = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));
        $manager_backup = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            ),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
        $profit_center_id = $this->employee_info['Employee']['profit_center_id'];
        $profit_center_id = !empty($list_profit_center_managers) ? $list_profit_center_managers : $profit_center_id;
        $list = array();
        $list_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status !=' => 'CL',
                'display' => 1
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'id')
        ));
        switch ($style) {
            case '1':
            case '2':
            case '3':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'assignedResources' => array(
                        '0' => $employee_id
                    ),
                    'nameStatusTask' => $list_status
                );
                break;
            case '4':
            case '5':
            case '6':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'manager' => 1
                );
                break;
            case '7':
            case '8':
            case '9':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'assignedTeam' => $profit_center_id
                );
                break;
        }
        $datas = $this->getDataForVisionTasks($list);
        $curDate = time();
        if (in_array($style, array(2, 5, 8))) {
            foreach ($datas as $key => $data) {
                if (!empty($data['end_date']) && strtotime($data['end_date']) <= $curDate && (in_array($data['status'], $list_status) || empty($data['status']))) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        } else if (in_array($style, array(3, 6, 9))) {
            foreach ($datas as $key => $data) {
                if ($data['overload'] > 0 && in_array($data['status'], $list_status)) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        }
        $langCode = Configure::read('Config.language');
        if ($langCode == 'eng') {
            $fieldName = array('name', 'english');
        } else {
            $fieldName = array('name', 'france');
        }
        $this->loadModel('VisionTaskExport');
        $fieldset = $this->VisionTaskExport->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => $fieldName,
            'order' => array('weight')
        ));
        $clStatus = $this->ProjectStatus->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status' => 'CL'
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id')
        ));

        $this->loadModels('ProjectStatus');
        $task_status = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name', 'status')
        ));
        $list_project_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus.name') : array();
        $list_org_project_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
        $this->set(compact('list_project_status', 'list_org_project_status'));

        $clStatus = !empty($clStatus['ProjectStatus']['id']) ? $clStatus['ProjectStatus']['id'] : 0;
        $this->set(compact('datas', 'fieldset', 'clStatus'));
		
		// Data for kanban_vision
        $datas_value = array_values($datas);
        $projectStatusEX = $this->getStatusEX();
		
        $this->set(compact('datas_value', 'projectStatusEX'));
		
		// Ticket #393. Check enable_newdesign de hien thi button (+) Add new task. Update by QuanNV 06/06/2019.
		$this->loadModel('Menu');
		$on_newdesign_assistant= $this->Menu->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'model' => 'project',
				'controllers' => 'project_tasks'
			),
			'fields' => array('enable_newdesign')
		));
		$on_newdesign_assistant = !empty($on_newdesign_assistant) ? $on_newdesign_assistant['Menu']['enable_newdesign'] : 0;
		
		// Ticket #395. Check user la Profile Project Manager (ppm) va role la Read Access man hinh Task.
		$this->loadModels('Employee','ProfileProjectManagerDetail');
		//Lay gia tri profile_account cua user dang nhap
		$check_ppm = $this->Employee->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'id' => $employee_id
			),
			'field' => array('id','profile_account')
		));
		//Kiem tra user dang nhap co phai ppm.
		if($check_ppm['Employee']['profile_account'] != 0){
			$check_ppm_writeTask = $this->ProfileProjectManagerDetail->find('first',array(
				'recursive' => -1,
				'conditions' => array(
					'model_id' => $check_ppm['Employee']['profile_account'],
					'controllers' => 'project_tasks',
					'read_write' => 1
				)
			));
			$check_ppm_writeTask = !empty($check_ppm_writeTask) ? 1 : 0;
			$this->set(compact('check_ppm_writeTask'));
		}
		/**
		* QuanNV . Update ticket 404.
		*/
		$this->loadModels ('Translation','TranslationSetting');

		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $company_id
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		
		$this->_getListEmployee();
		/*
		* End update ticket 404.
		*/
        // kiem tra role
        $myRole = $this->employee_info['Role']['name'];
        $emp_id_login = $this->employee_info['Employee']['id'];
		// debug($myRole);
		// exit;
		$this->loadModel('HistoryFilter');
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
		$this->set(compact('on_newdesign_assistant','adminTaskSetting', 'bg_currency', 'loadFilter','myRole','emp_id_login'));
		
        $this->render('tasks_vision');
    }

    function kanban_vision($style = 1) {
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModel('ProfitCenterManagerBackup');
        $manager = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'manager_id' => $employee_id
            ),
            'fields' => array('id', 'id')
        ));
        $manager_backup = $this->ProfitCenterManagerBackup->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id
            ),
            'fields' => array('profit_center_id', 'profit_center_id')
        ));
        $list_profit_center_managers = array_unique(array_merge($manager, $manager_backup));
        $profit_center_id = $this->employee_info['Employee']['profit_center_id'];
        $profit_center_id = !empty($list_profit_center_managers) ? $list_profit_center_managers : $profit_center_id;
        $list = array();
        $list_status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'status !=' => 'CL',
                'display' => 1
            ),
            'fields' => array('id', 'id')
        ));
        switch ($style) {
            case '1':
            case '2':
            case '3':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'assignedResources' => array(
                        '0' => $employee_id
                    ),
                    'nameStatusTask' => $list_status
                );
                break;
            case '4':
            case '5':
            case '6':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'manager' => 1
                );
                break;
            case '7':
            case '8':
            case '9':
                $list = array(
                    'nameCategory' => array(
                        '0' => 1
                    ),
                    'nameStatusTask' => $list_status,
                    'assignedTeam' => $profit_center_id
                );
                break;
        }
        $datas = $this->getDataForVisionTasks($list);

        $curDate = time();
        if (in_array($style, array(2, 5, 8))) {
            foreach ($datas as $key => $data) {
                if (!empty($data['end_date']) && strtotime($data['end_date']) <= $curDate && (in_array($data['status'], $list_status) || empty($data['status']))) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        } else if (in_array($style, array(3, 6, 9))) {
            foreach ($datas as $key => $data) {
                if ($data['overload'] > 0 && in_array($data['status'], $list_status)) {
                    // do nothing
                } else {
                    unset($datas[$key]);
                }
            }
        }
    
        $datas_value = array_values($datas);
        $this->loadModels('ProjectStatus');
        $task_status = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name', 'status')
        ));
        $list_org_project_status = !empty($task_status) ? Set::combine($task_status, '{n}.ProjectStatus.id', '{n}.ProjectStatus') : array();
		
        $projectStatusEX = $this->getStatusEX();
        
        $this->set(compact('datas', 'datas_value', 'projectStatusEX', 'list_org_project_status', 'style'));
    }

    private function getStatusEX() {
        $this->loadModel('ProjectStatus');
        $status = array();
        $projectStatus = $this->ProjectStatus->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info["Company"]["id"]
            ),
			'order' => array('weight' => 'ASC'),
            'fields' => array('id', 'name', 'status')
        ));
        foreach ($projectStatus as $key => $value) {
            $status[$key]['text'] = $value['ProjectStatus']['name'];
            $status[$key]['dataField'] = $value['ProjectStatus']['id'];
        }
        return $status;
    }

    protected function _getPath($company_id = null, $project_id = null, $key = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'projects' . DS . $key . DS;
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
        return $path;
    }

    /**
     * Upload
     */
    public function uploads($project_id = null, $key = null) {
        $this->layout = 'ajax';
        $result = array();
		if( !empty($this->data) ){
			if( !empty($this->data['project_id'])) $project_id = $this->data['project_id'];
			if( !empty($this->data['key'])) $key = $this->data['key'];
		}
		if( !$this->_checkRole(false, $project_id) ) {
			die( json_encode( false ));
		}
        $_FILES['FileField'] = array();
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($_FILES['file'])) {
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if (!empty($_FILES)) {
            $path = $this->_getPath($company_id, $project_id, $key);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
            }
            if (!empty($attachment)) {
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->ProjectFile->create();
                if ($this->ProjectFile->save(array(
                            'project_id' => $project_id,
                            'key' => $key,
                            'file_attachment' => $attachment,
                            'size' => $size,
                            'type' => $type))) {
                    $lastId = $this->ProjectFile->id;
                    $result = $this->ProjectFile->find('first', array('recursive' => -1, 'conditions' => array('ProjectFile.id' => $lastId)));
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
                $dataSession = array(
                    'path' => $path,
                    'file' => $attachment
                );
                $_SESSION['file_multiupload'][] = $dataSession;
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

    public function attachment($key = null, $project_id = null, $id = null, $type = null) {
		$this->_checkRole(false, $project_id);
        $company_id = $this->employee_info['Company']['id'];
        $last = $this->ProjectFile->find('first', array(
            'recursive' => -1,
            'fields' => array('project_id', 'file_attachment'),
            'conditions' => array(
                'ProjectFile.id' => $id,
                'ProjectFile.project_id' => $project_id,
                'ProjectFile.key' => $key
            ),
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'Project.id = ProjectFile.project_id',
                        'company_id' => $company_id
                    )
                )
            )
        ));
        $error = true;
        if ($last && $last['ProjectFile']['project_id']) {
            $attachment = $last['ProjectFile']['file_attachment'];
            $path = trim($this->_getPath($company_id, $last['ProjectFile']['project_id'], $key). $last['ProjectFile']['file_attachment']);
            if ($type == 'download') {
                if (file_exists($path) && is_file($path)) {
                    if ($type == 'download') {
                        $info = pathinfo($path);
                        $this->view = 'Media';
                        $params = array(
                            'id' => $info['basename'],
                            'path' => $info['dirname'] . DS,
                            'name' => $info['filename'],
                            'extension' => strtolower($info['extension']),
                            'download' => true
                        );
                        $params['mimeType'][$info['extension']] = 'application/octet-stream';
                        $this->set($params);
                    }
                    $error = false;
                }
            } else {
				if( $this->_checkRole(false, $project_id) ) {
					if (file_exists($path) && is_file($path)) {
						@unlink($path);
					}
					$result = $this->ProjectFile->delete($id);
					if ($this->MultiFileUpload->otherServer == true) {
						$path = trim($this->_getPath($company_id, $last['ProjectFile']['project_id'], $key));
						if (!empty($_SERVER['HTTP_REFERER'])) {
							$redirect = $_SERVER['HTTP_REFERER'];
						} else {
							$redirect = '/projects/your_form/' . $project_id;
						}
						$this->MultiFileUpload->deleteFileToServerOther($path, $attachment, $redirect);
					}
					if( $this->params['isAjax']){
						$this->data['key'] = $key;
						die(json_encode(array(
							'result' => !empty($result) ? 'success' : 'failed',
							'data' => $this->get_upload_document($project_id, true),
							'message' => !empty($result) ? '' : __('File not found.', true)
						)));
					}
					if (!empty($_SERVER['HTTP_REFERER'])) {
						$redirect = $_SERVER['HTTP_REFERER'];
						$this->redirect($redirect);
					} else {
						$this->redirect(array('action' => 'your_form', $project_id));
					}
                }
            }
        }
		if( $this->params['isAjax'] && ($type != 'download')){
			die(json_encode(array(
				'result' => empty($error) ? 'success' : 'failed',
				'data' => '',
				'message' => empty($error) ? '' : __('File not found.', true)
			)));
		}
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
			if (!empty($_SERVER['HTTP_REFERER'])) {
				$redirect = $_SERVER['HTTP_REFERER'];
				$this->redirect($redirect);
			} else {
				$this->redirect(array('action' => 'your_form', $project_id));
			}
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'your_form', $project_id));
        }
    }

    // export Excel Index Screen
    public function export_excel_index() {
        if (!empty($this->data)) {
			
            $this->SlickExporter->init();
            $data = json_decode($this->data['data'], true);
            $this->SlickExporter
                    ->setT('Project Index') //auto translate
                    ->save($data, 'project_index_{date}.xls');
        }
        die;
    }

    public function checkStaffing() {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan', 'ActivityTask', 'ActivityRequest', 'TmpStaffingSystem', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
            $company_id = $this->employee_info['Employee']['company_id'];
            $projects = $this->Project->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Project.id' => $id
                ),
                'fields' => array('id', 'project_name', 'activity_id', 'rebuild_staffing'),
                'order' => array('project_name')
            ));
            $activitiesLinked = Set::combine($projects, '{n}.Project.id', '{n}.Project.linked');
            $projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.project_name');
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 0
                ),
                'fields' => array('id', 'parent_id', 'project_id')
            ));
            $listProjectTaskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');
            $listActivityTaskFollowProject = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id');
            $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
            foreach ($projectTasks as $key => $projectTask) {
                foreach ($parentIds as $parentId) {
                    if ($projectTask['ProjectTask']['id'] == $parentId) {
                        unset($listProjectTaskIds[$key]);
                    }
                }
            }
            $projectTasks = array_values($listProjectTaskIds);
            $tasks = array_values($listActivityTaskFollowProject);
            $activityTasksLinked = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $tasks),
                'fields' => array('project_task_id', 'id')
            ));
            $CONSUMED[$id] = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed'),
                'conditions' => array(
                    'status' => 2,
                    'task_id' => $activityTasksLinked,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0, "task_id" => null)
                ),
            ));
            $CONSUMED[$id] = Set::classicExtract($CONSUMED[$id], '{n}.0.consumed');
            $_previous = array_values($activitiesLinked);
            $previous = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed', 'activity_id'),
                'conditions' => array(
                    'status' => 2,
                    'activity_id' => $_previous,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0),
                ),
                'group' => 'activity_id'
            ));
            $previous = Set::combine($previous, '{n}.ActivityRequest.activity_id', '{n}.0.consumed');
            $results = array();
            $workloadFromTask = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id,
                    'id' => $projectTasks,
                    'special' => 0
                ),
                'fields' => array('SUM(estimated) as workload', 'project_id'),
                'group' => 'project_id'
            ));
            $workloadFromTask = Set::combine($workloadFromTask, '{n}.ProjectTask.project_id', '{n}.0.workload');
            $staffings = $this->TmpStaffingSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $id
                ),
                'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model', 'project_id'),
                'group' => array('model', 'project_id')
            ));
            foreach ($staffings as $index => $data) {
                $_data = array_merge($data[0], $data['TmpStaffingSystem']);
                if ($_data['model'] == 'employee') {
                    $staffingsE[$_data['project_id']] = $_data['workload'];
                } elseif ($_data['model'] == 'profile') {
                    $staffingsP2[$_data['project_id']] = $_data['workload'];
                } else {
                    $staffingsP[$_data['project_id']] = $_data['workload'];
                }
                if ($_data['model'] == 'employee') {
                    $staffingsConsumedE[$_data['project_id']] = $_data['consumed'];
                } else {
                    $staffingsConsumedP[$_data['project_id']] = $_data['consumed'];
                }
            }
            $listIdRebuilds = array();
            $error = 0;
            $_workload = isset($workloadFromTask[$id]) ? $workloadFromTask[$id] : 0.00;
            $_staffingE = isset($staffingsE[$id]) ? $staffingsE[$id] : 0.00;
            $_staffingP = isset($staffingsP[$id]) ? $staffingsP[$id] : 0.00;
            $_staffingP2 = isset($staffingsP2[$id]) ? $staffingsP2[$id] : 0.00;
            if ($_staffingE != $_workload) {
                $error = 1;
            }
            if ($_staffingP != $_workload) {
                $error = 2;
            }
            if ($_staffingP2 != $_workload) {
                $error = 7;
            }
            $_previous = 0;
            if (isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id])) {
                $_previous = $activitiesLinked[$id];
            }
            $_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
            $_consumed = isset($CONSUMED[$id]) ? $_consumed + $CONSUMED[$id][0] : $_consumed;
            $_consumed = number_format($_consumed, 2, '.', '');
            $_staffingConsumedE = isset($staffingsConsumedE[$id]) ? $staffingsConsumedE[$id] : 0.00;
            $_staffingConsumedP = isset($staffingsConsumedP[$id]) ? $staffingsConsumedP[$id] : 0.00;
            if ($_staffingConsumedE != $_consumed) {
                $error = 4;
            }
            if ($_staffingConsumedP != $_consumed) {
                $error = 5;
            }
            if ($error > 0) {
                echo "check staffing";
                $this->ProjectTask->staffingSystem($id);
            }
            echo 'done';
        }
        exit;
    }

    public function checkStaffings() {
        set_time_limit(0);
        ignore_user_abort(true);
        $first = !empty($_POST['first']) ? $_POST['first'] : 1;
        $LIMIT = 70;
        $done = false;
		$timeCurrent = time();
        if ($first == 1) {
            $LIMIT = 20;
            $this->Session->write('ProjectChecked', array());
        }
        $this->loadModels('Project', 'ProjectTask', 'NctWorkload', 'ProfitCenter', 'ProjectPhasePlan', 'ActivityTask', 'ActivityRequest', 'TmpStaffingSystem', 'ProjectTaskEmployeeRefer', 'Employee', 'ProfitCenter');
        $company_id = $this->employee_info['Company']['id'];
		if(!empty($company_id)){
			$check_isset_milestone_staffing = $this->CompanyConfig->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company' => $company_id,
					'cf_name' => 'milestone_check_staffing'
				),
			));
			if(!empty($check_isset_milestone_staffing)){
				$saved = array(
					'id' => $check_isset_milestone_staffing['CompanyConfig']['id'],
					'cf_name' => 'milestone_check_staffing',
					'cf_value' => $timeCurrent,
					'company' => $company_id
				);
			}else{
				$saved = array(
					'cf_name' => 'milestone_check_staffing',
					'cf_value' => $timeCurrent,
					'company' => $company_id
				);
			}
			$this->CompanyConfig->save($saved);
		}
        $projects = $this->Project->find('all', array(
            'limit' => $LIMIT,
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => 1,
                'NOT' => array('Project.id' => $this->Session->read('ProjectChecked'))
            ),
            'fields' => array('id', 'project_name', 'activity_id', 'rebuild_staffing'),
            'order' => array('project_name')
        ));
        $activitiesLinked = Set::combine($projects, '{n}.Project.id', '{n}.Project.linked');
        $projects = Set::combine($projects, '{n}.Project.id', '{n}.Project.project_name');
        $projectListIds = array_keys($projects);
        $projectIdChecked = array_merge($this->Session->read('ProjectChecked'), $projectListIds);
        $this->Session->write('ProjectChecked', $projectIdChecked);
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.project_id' => $projectListIds,
                'ProjectTask.special' => 0,
            ),
            'fields' => array('id', 'parent_id', 'project_id')
        ));
        $listProjectTaskIds = Set::classicExtract($projectTasks, '{n}.ProjectTask.id');
        $listActivityTaskFollowProject = Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id', '{n}.ProjectTask.project_id');
        $parentIds = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
        foreach ($projectTasks as $key => $projectTask) {
            foreach ($parentIds as $parentId) {
                if ($projectTask['ProjectTask']['id'] == $parentId) {
                    unset($listProjectTaskIds[$key]);
                }
            }
        }
        $projectTasks = array_values($listProjectTaskIds);
        //GET CONSUMED
        foreach ($listActivityTaskFollowProject as $projectId => $tasks) {
            $tasks = array_values($tasks);
            $activityTasksLinked = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityTask.project_task_id' => $tasks),
                'fields' => array('project_task_id', 'id')
            ));
            $CONSUMED[$projectId] = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(value) AS consumed'),
                'conditions' => array(
                    'status' => 2,
                    'task_id' => $activityTasksLinked,
                    'company_id' => $company_id,
                    'NOT' => array('value' => 0, "task_id" => null)
                ),
            ));
            $CONSUMED[$projectId] = Set::classicExtract($CONSUMED[$projectId], '{n}.0.consumed');
        }
        $_previous = array_values($activitiesLinked);
        $previous = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array('SUM(value) AS consumed', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $_previous,
                'company_id' => $company_id,
                'NOT' => array('value' => 0),
            ),
            'group' => 'activity_id'
        ));
        $previous = Set::combine($previous, '{n}.ActivityRequest.activity_id', '{n}.0.consumed');
        $results = array();

        $workloadFromTask = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectListIds,
                'id' => $projectTasks,
                'special' => 0
            ),
            'fields' => array('SUM(estimated) as workload', 'project_id'),
            'group' => 'project_id'
        ));
        $workloadFromTask = Set::combine($workloadFromTask, '{n}.ProjectTask.project_id', '{n}.0.workload');
        $staffings = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectListIds
            ),
            'fields' => array('SUM(estimated) as workload, SUM(consumed) as consumed, model', 'project_id'),
            'group' => array('model', 'project_id')
        ));
        foreach ($staffings as $index => $data) {
            $_data = array_merge($data[0], $data['TmpStaffingSystem']);
            //DATA WORKLOAD
            if ($_data['model'] == 'employee') {
                $staffingsE[$_data['project_id']] = $_data['workload'];
            } elseif ($_data['model'] == 'profile') {
                $staffingsP2[$_data['project_id']] = $_data['workload'];
            } else {
                $staffingsP[$_data['project_id']] = $_data['workload'];
            }
            //DATA CONSUMED
            if ($_data['model'] == 'employee') {
                $staffingsConsumedE[$_data['project_id']] = $_data['consumed'];
            } else {
                $staffingsConsumedP[$_data['project_id']] = $_data['consumed'];
            }
        }
        $listIdRebuilds = array();
        foreach ($projects as $id => $name) {
            $error = 0;
            $_workload = isset($workloadFromTask[$id]) ? $workloadFromTask[$id] : 0.00;
            $_staffingE = isset($staffingsE[$id]) ? $staffingsE[$id] : 0.00;
            $_staffingP = isset($staffingsP[$id]) ? $staffingsP[$id] : 0.00;
            $_staffingP2 = isset($staffingsP2[$id]) ? $staffingsP2[$id] : 0.00;
            if ($_staffingE != $_workload) {
                $error = 1;
            }
            if ($_staffingP != $_workload) {
                $error = 2;
            }
            if ($_staffingP2 != $_workload) {
                $error = 7;
            }
            $_previous = 0;
            if (isset($activitiesLinked[$id]) && is_numeric($activitiesLinked[$id])) {
                $_previous = $activitiesLinked[$id];
            }
            $_consumed = isset($previous[$_previous]) ? $previous[$_previous] : 0.00;
            $_consumed = isset($CONSUMED[$id]) ? $_consumed + $CONSUMED[$id][0] : $_consumed;
            $_consumed = number_format($_consumed, 2, '.', '');
            $_staffingConsumedE = isset($staffingsConsumedE[$id]) ? $staffingsConsumedE[$id] : 0.00;
            $_staffingConsumedP = isset($staffingsConsumedP[$id]) ? $staffingsConsumedP[$id] : 0.00;
            if ($_staffingConsumedE != $_consumed) {
                $error = 4;
            }
            if ($_staffingConsumedP != $_consumed) {
                $error = 5;
            }
            if ($error > 0) {
                $listIdRebuilds[] = $id;
            }
        }
        foreach ($listIdRebuilds as $id) {
            if ($id != null)
                $this->ProjectTask->staffingSystem($id);
        }
        $countRecord = $this->Project->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => 1,
            )
        ));
        $progress = round(((count($this->Session->read('ProjectChecked')) / $countRecord) * 100), 2);
        if ($progress >= 100)
            $done = true;
        $result['done'] = $done;
        $result['progress'] = $progress;
        echo json_encode($result);
        exit;
    }

    private function getTaskEuro($projectIds) {
        $result = array();
        $this->loadModels('ProjectTask', 'ActivityTask', 'ActivityRequest');
        $useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
		if(!empty($projectIds)){
			$listTasks = $this->ProjectTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('project_id' => $projectIds),
				'fields' => array('id', 'project_id', 'task_title', 'estimated', 'initial_estimated', 'manual_consumed', 'unit_price', 'special', 'special_consumed', 'parent_id', 'task_completed', 'overload', 'predecessor', 'manual_overload', 'progress_order', 'amount')
			));
			$projectDatas = array();
			if(!empty($listTasks)){
				foreach ($listTasks as $key => $value) {
					$taskItem = $value['ProjectTask'];
					$project_id =  $taskItem['project_id'];
					$task_id = $taskItem['id'];
					$projectDatas[$project_id][$task_id] = $value;
				}
			}
			$parentIds = array_unique(Set::classicExtract($listTasks, '{n}.ProjectTask.parent_id'));
			
            $listPrent = array();
            foreach ($listTasks as $key => $_listTask) {
                foreach ($parentIds as $parentId) {
                    if ($_listTask['ProjectTask']['id'] == $parentId) {
                        $listPrent[$key] = $_listTask['ProjectTask']['id'];
                    }
                }
            }
		
			$listTaskIds = !empty($listTasks) ? Set::classicExtract($listTasks, '{n}.ProjectTask.id') : array();
			$listTaskIdFollowTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $listTaskIds),
                'fields' => array('project_task_id', 'id')
            ));
			$consumed = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array('task_id' => $listTaskIdFollowTasks, 'status' => 2),
                'fields' => array('task_id', 'SUM(value) as consumed'),
                'group' => array('task_id')
            ));
			$inUsedOfActivityTasks = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $listTaskIdFollowTasks,
                ),
                'fields' => array('task_id', 'SUM(IF(`status` != 2, `value`, 0)) as inUsed'),
                'group' => array('task_id')
            ));
            $inUsedOfActivityTasks = !empty($inUsedOfActivityTasks) ? Set::combine($inUsedOfActivityTasks, '{n}.ActivityRequest.task_id', '{n}.0.inUsed') : array();
            $consumed = !empty($consumed) ? Set::combine($consumed, '{n}.ActivityRequest.task_id', '{n}.0.consumed') : array();
			if(!empty($projectDatas)){
				foreach ($projectDatas as $_id => $listTask) {
					
					$workload_euro = $consumed_euro = $remain_euro = $estimated_euro = $_cons = $_workload = $initial_estimated = $unit_price = $_remain = $overload = $manual_overload = $amount = 0;
					$_manual_consumed = $completed = $_inUsed = $_progress_order_amount = 0;
					foreach ($listTask as $key => $value) {
						$dx = $value['ProjectTask'];
						$unit = !empty($dx['unit_price']) ? $dx['unit_price'] : 0;
						$workload = !empty($dx['estimated']) ? $dx['estimated'] : 0;
						$manual_consumed = !empty($dx['manual_consumed']) ? $dx['manual_consumed'] : 0;
						$_consumed_rq = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($consumed[$listTaskIdFollowTasks[$dx['id']]]) ? $consumed[$listTaskIdFollowTasks[$dx['id']]] : 0;
						// special consumed.
						$_specialcs = !empty($dx['special_consumed']) && $dx['special'] == 1 ? $dx['special_consumed'] : 0;
						$_consumed = $_consumed_rq + $_specialcs;
						$_overload = !empty($dx['overload']) ? $dx['overload'] : 0;
						$_remain_euro = ($workload > $_consumed) ? ($workload - $_consumed) * $unit : 0;
						$_cons += $_consumed;
						$consumed_euro += ($_consumed + $manual_consumed) * $unit;
						if ($useManualConsumed) {
							$_consumed += $manual_consumed;
						}

						$_amount = !empty($dx['amount']) ? $dx['amount'] : 0;
						$progress_order_amount = round($dx['progress_order'] * $_amount / 100, 2);
						if (in_array($dx['id'], $listPrent))
							continue;
						$remain = ($workload - $_consumed) > 0 ? $workload - $_consumed : 0;
						$initial_estimated += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] : 0;
						$_progress_order_amount += $progress_order_amount;
						$amount += $_amount;
						$unit_price += $unit;
						$_workload += $workload;
						$_remain += $remain;
						$overload += $_overload;
						$workload_euro += $workload * $unit;
						$remain_euro += $_remain_euro;
						$_manual_consumed += $manual_consumed;
						$manual_overload += !empty($dx['manual_overload']) ? $dx['manual_overload'] : 0;
						$estimated_euro += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] * $unit : 0;
						$inUsed = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]]) ? $inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]] : 0;
						$_inUsed += $inUsed;
					}

					if ($useManualConsumed) {
						$result[$_id]['Overload'] = $manual_overload;

						if ($_manual_consumed != 0) {
							$_completed = ($_workload + $manual_overload) == 0 ? round(($_manual_consumed * 100), 2) : round(($_manual_consumed * 100) / ($_workload + $manual_overload), 2);
						} else {
							$_completed = 0;
						}
					} else {
						$result[$_id]['Overload'] = $overload;
						// if ($_consumed != 0) {
						$_completed = ($_workload + $overload) == 0 ? round(($_cons * 100), 2) : round(($_cons * 100) / ($_workload + $overload), 2);
						// } else {
						//     $_completed = 0;
						// }
					}
					$_progress_order = $amount != 0 ? round($_progress_order_amount / $amount * 100, 2) : 0;
					$_completed = $_completed > 0 ? $_completed : 0;
					$_completed = $_completed < 100 ? $_completed : 100;
					$result[$_id]['Consumed'] = $_cons;
					$result[$_id]['Remain'] = $_remain;
					$result[$_id]['Workload'] = $_workload;
					$result[$_id]['Initialworkload'] = $initial_estimated;
					$result[$_id]['UnitPrice'] = $unit_price;
					$result[$_id]['Workload€'] = $workload_euro;
					$result[$_id]['Consumed€'] = $consumed_euro;
					$result[$_id]['Remain€'] = $remain_euro;
					$result[$_id]['Estimated€'] = $estimated_euro;
					$result[$_id]['ManualOverload'] = $manual_overload;
					$result[$_id]['ManualConsumed'] = $_manual_consumed;
					$result[$_id]['Completed'] = $_completed;
					$result[$_id]['InUsed'] = $_inUsed;
					$result[$_id]['Amount€'] = $amount;
					$result[$_id]['%progressorder'] = $_progress_order;
					$result[$_id]['%progressorder€'] = $_progress_order_amount;
				}
			}
		}
        return $result;
    }
	public function getPCForecast($project_id = array()){
		$a = time();
		$currentYears = date('m-Y', time());
		if(!empty($_POST)){
			$project_id = $_POST['projectIds'];
			$currentYears = str_replace('/','-',$_POST['year']);
			$pc_id = $_POST['pc_id'];
		}
		$this->loadModels('TmpStaffingSystem', 'ProfitCenter', 'Employee', 'AbsenceRequest', 'Holiday');
        $first_date = strtotime('01-' . $currentYears);
		$last_date = strtotime( '+1 year -1 day', $first_date);
		$resources = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'profit_center_id' => $pc_id,
			),
			'fields' => array('id', 'start_date', 'end_date','profit_center_id')
		));
		$list_employees = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee') : array();

		$list_employees_id = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee.id') : array();
		$employee_absence = $this->AbsenceRequest->sumAbsenceByEmployeeAndDate($list_employees_id, $first_date, $last_date,'validated', 'month');
		$getListWorkingDays = $this->Project->getWorkingDays($first_date, $last_date, true);
		$month_working_day = array();
		if(!empty($getListWorkingDays)){
			foreach ($getListWorkingDays as $key => $date) {
				if(empty($month_working_day[date('01_m_Y', $date)])) $month_working_day[date('01_m_Y', $date)] = 0;
				$month_working_day[date('01_m_Y', $date)] += 1;
			}
		}
		$pc_employee_refer = array();
		if(!empty($resources)){
			foreach ($resources as $key => $resource) {
				if(empty($pc_employee_refer[$resource['Employee']['profit_center_id']])) $pc_employee_refer[$resource['Employee']['profit_center_id']] = array();
				$pc_employee_refer[$resource['Employee']['profit_center_id']][] = $resource['Employee']['id'];
			}
		}
		$pc_capacity = array();
		$pc_forecast = array();
		// tinh capacity hien thi o widget man hinh Project.
		foreach ($pc_employee_refer as $pc_id => $employee_id) {
			$pc_capacity[$pc_id] = array();
			foreach ($employee_id as $index => $id) {
				foreach ($month_working_day as $date => $value) {
					$f_date = str_replace('_', '-', $date);
					$f_date = strtotime($f_date);
					$n_month = mktime(0, 0, 0, date("m", $f_date) + 1, date("d", $f_date), date("Y", $f_date));
					if(empty($pc_capacity[$pc_id][$f_date])) $pc_capacity[$pc_id][$f_date] = 0;
					$e_start = (!empty($list_employees[ $id]['start_date']) && ($list_employees[ $id]['start_date'] !='0000-00-00')) ? strtotime($list_employees[ $id]['start_date']) : 0;
					$e_end = (!empty($list_employees[ $id]['end_date']) && ($list_employees[ $id]['end_date'] !='0000-00-00')) ? strtotime($list_employees[ $id]['end_date']) : 0;
					$is_work = 1;
					if((!empty($e_start) && $e_start > $n_month )
						|| (!empty($e_end) && ($e_end < $f_date))){
						// do nothing
					}else{
						$val = $value ? $value : 0;
						$cal_date = !empty($f_date) ? $f_date : 0;
						if(!empty($e_start) && !empty($cal_date)){
							while($cal_date < $n_month){
								if(in_array($cal_date,$getListWorkingDays)){
									if(!empty($e_start) && $cal_date < $e_start){
										$val = ($val - 1);
									}
									if(!empty($e_end) && ($cal_date > $e_end)){
										$val = ($val - 1);
									}
								}
								$cal_date = mktime(0, 0, 0, date("m", $cal_date), date("d", $cal_date) + 1, date("Y", $cal_date));
							}
						}
						$absence_value = !empty($employee_absence) && !empty($employee_absence[$id]) && !empty($employee_absence[$id][$date]) ? $employee_absence[$id][$date] : 0;
						$pc_capacity[$pc_id][$f_date] += ($val - $absence_value);
					}
				}
			}
		}
		$staffingSystems = $this->TmpStaffingSystem->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'model' => 'profit_center',
				'project_id' => $project_id,
				'model_id' => $pc_id,
				'company_id' => $this->employee_info["Company"]["id"],
				'date BETWEEN ? AND ?' => array($first_date, $last_date),
			),
			'fields' => array('model_id', 'project_id', 'SUM(estimated) AS total', 'date'),
			'group' => array('model_id', 'project_id', 'date'),
		));
		$pc_estimated = array();
		if(!empty($staffingSystems)){
			foreach ($staffingSystems as $key => $_staffing) {
				$pc_staffing = $_staffing['TmpStaffingSystem'];
				$project_id = $pc_staffing['project_id'];
				$model_id = $pc_staffing['model_id'];
				$date = $pc_staffing['date'];
				$month_text = date('M-Y',$pc_staffing['date']);
				$value = $_staffing[0]['total'];
				if(empty($pc_estimated[$project_id])) $pc_estimated[$project_id] = array();
				if(empty($pc_estimated[$project_id][$model_id])) $pc_estimated[$project_id][$model_id] = array();
				$pc_estimated[$project_id][$model_id][$date] = array(
					'month_text' => $month_text,
					'value' => $value
				);
			}
		}
		while ($first_date < $last_date) {
			foreach ($pc_estimated as $project_id => $pc_data) {
				$dfFore[$first_date] = array(
					'capacity' => 0,
					'value' => 0,
					'month_text' => date('M-Y',$first_date),
				);
				if(empty($pc_forecast[$project_id])) $pc_forecast[$project_id] = array();
				$pc_forecast[$project_id][$pc_id][$first_date] = $dfFore[$first_date];
				if(!empty($pc_data[$pc_id][$first_date])){
					$pc_forecast[$project_id][$pc_id][$first_date]['value'] = $pc_data[$pc_id][$first_date]['value'];
				}
				if(!empty($pc_capacity[$pc_id][$first_date])){
					$pc_forecast[$project_id][$pc_id][$first_date]['capacity'] = $pc_capacity[$pc_id][$first_date];
				}
			}
			// truong hop pc moi tao chua co trong table TmpStaffingSystem
			if(empty($pc_estimated)){
				$dfFore[$first_date] = array(
					'capacity' => 0,
					'value' => 0,
					'month_text' => date('M-Y',$first_date),
				);
				foreach($project_id as $k => $val){
					$pc_forecast[$val][$pc_id][$first_date] = $dfFore[$first_date];
					if(!empty($pc_capacity[$pc_id][$first_date])){
						$pc_forecast[$val][$pc_id][$first_date]['capacity'] = $pc_capacity[$pc_id][$first_date];
					}
				}
			}
			$first_date = mktime(0, 0, 0, date("m", $first_date) + 1, date("d", $first_date), date("Y", $first_date));
		}
		if( $this->params['isAjax'] ){
			die(json_encode(array(
				'result' =>'success',
				'data' => $pc_forecast,
			)));
		}
		$this->set(compact('pc_forecast', 'pc_estimated', 'pc_capacity'));
	}
	private function _workingDayFollowConfigAdmin($start = null, $end = null, $workdayAdmins = null, $holidays = array()){
		// debug($holidays);
		// exit;
        $results = array();
        if(!empty($start) && !empty($end)){
            while($start <= $end){
                $day = strtolower(date('l', $start));
                if(!empty($workdayAdmins[$day]) && $workdayAdmins[$day] != 0){
                    $results[$start] = $start;
                }
                $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
            }
        }
        return $results;
    }
	// Huynh 
	/* private function getProjectProgress
	 * Ticket #615 «EVOLUTION MUTUALISABLE» «Progress of a project» «EN COURS DE DEV» «Développeur» «z0 Gravity»
	 * Create 5 ways to calculate the progress of a project
	 */
	private function getProjectProgress($projectIds){
		$company_id = $this->employee_info['Company']['id'];
		$result = $this->Project->caculateProgress($projectIds, $this->companyConfigs, $company_id);
		return $result;
	}
	// Huynh 2020-06-09
	function getLikebyProjects($project_ids){
		$this->loadModel('MLike');
		$countLikes =  $this->MLike->get_like_by_project($project_ids);
		$liked = $this->MLike->get_like_by_employee($this->employee_info['Employee']['id'], $project_ids);
		return compact('countLikes', 'liked');
	}
	// Huynh 2020-06-09
	function getFavoritebyEmployee($employee_id, $project_ids=null){
		$this->loadModel('MFavorite');
		$fav =  $this->MFavorite->get_favorite_by_employee($employee_id, $project_ids);
		return $fav;
	}
    // Huynh
    // Do yêu cầu thay đổi không tính overload vào progress nên viết lại hàm này để dùng cho function index_plus
    // _completed có thể lớn hơn 100
    // Email yêu cầu: Zog : IMPORTANT EMAIL : Priority one
    // Date: Jan 14, 2019, 4:12 PM
    // Viet: Fix get tasks special  #493
	private function getProjectProgress_bak($projectIds) {
		$this->loadModels('ActivityRequest', 'ProjectTask', 'ActivityTask');
		$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;
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
        return $results;
    }
    private function getTaskEuro_new($projectIds) {
        $result = array();
        $this->loadModels('ProjectTask', 'ActivityTask', 'ActivityRequest');
        $useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? $this->companyConfigs['manual_consumed'] : 0;

        foreach ($projectIds as $_id) {
            $listTask = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $_id),
                'fields' => array('id', 'task_title', 'estimated', 'initial_estimated', 'manual_consumed', 'unit_price', 'special', 'special_consumed', 'parent_id', 'task_completed', 'overload', 'predecessor', 'manual_overload', 'progress_order', 'amount')
            ));
            $parentIds = array_unique(Set::classicExtract($listTask, '{n}.ProjectTask.parent_id'));
            $listPrent = array();
            foreach ($listTask as $key => $_listTask) {
                foreach ($parentIds as $parentId) {
                    if ($_listTask['ProjectTask']['id'] == $parentId) {
                        $listPrent[$key] = $key;
                    }
                }
            }
            $listTaskIds = !empty($listTask) ? Set::classicExtract($listTask, '{n}.ProjectTask.id') : array();
            $listTaskIdFollowTasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_task_id' => $listTaskIds),
                'fields' => array('project_task_id', 'id')
            ));
            $consumed = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array('task_id' => $listTaskIdFollowTasks, 'status' => 2),
                'fields' => array('task_id', 'SUM(value) as consumed'),
                'group' => array('task_id')
            ));
            $inUsedOfActivityTasks = $this->ActivityRequest->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'task_id' => $listTaskIdFollowTasks,
                ),
                'fields' => array('task_id', 'SUM(IF(`status` != 2, `value`, 0)) as inUsed'),
                'group' => array('task_id')
            ));
            $inUsedOfActivityTasks = !empty($inUsedOfActivityTasks) ? Set::combine($inUsedOfActivityTasks, '{n}.ActivityRequest.task_id', '{n}.0.inUsed') : array();
            $consumed = !empty($consumed) ? Set::combine($consumed, '{n}.ActivityRequest.task_id', '{n}.0.consumed') : array();
            $workload_euro = $consumed_euro = $remain_euro = $estimated_euro = $_cons = $_workload = $initial_estimated = $unit_price = $_remain = $overload = $manual_overload = $amount = 0;
            $_manual_consumed = $completed = $_inUsed = $_progress_order_amount = 0;
            foreach ($listTask as $key => $value) {
                $dx = $value['ProjectTask'];
                $unit = !empty($dx['unit_price']) ? $dx['unit_price'] : 0;
                $workload = !empty($dx['estimated']) ? $dx['estimated'] : 0;
                $manual_consumed = !empty($dx['manual_consumed']) ? $dx['manual_consumed'] : 0;
                $_consumed_rq = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($consumed[$listTaskIdFollowTasks[$dx['id']]]) ? $consumed[$listTaskIdFollowTasks[$dx['id']]] : 0;
                // special consumed.
                $_specialcs = !empty($dx['special_consumed']) && $dx['special'] == 1 ? $dx['special_consumed'] : 0;
                $_consumed = $_consumed_rq + $_specialcs;
                $_overload = !empty($dx['overload']) ? $dx['overload'] : 0;
                $_remain_euro = ($workload > $_consumed) ? ($workload - $_consumed) * $unit : 0;
                $_cons += $_consumed;
                $consumed_euro += ($_consumed + $manual_consumed) * $unit;
                if ($useManualConsumed) {
                    $_consumed += $manual_consumed;
                }

                $_amount = !empty($dx['amount']) ? $dx['amount'] : 0;
                $progress_order_amount = round($dx['progress_order'] * $_amount / 100, 2);
                if (in_array($key, $listPrent))
                    continue;
                $remain = ($workload - $_consumed) > 0 ? $workload - $_consumed : 0;
                $initial_estimated += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] : 0;
                $_progress_order_amount += $progress_order_amount;
                $amount += $_amount;
                $unit_price += $unit;
                $_workload += $workload;
                $_remain += $remain;
                $overload += $_overload;
                $workload_euro += $workload * $unit;
                $remain_euro += $_remain_euro;
                $_manual_consumed += $manual_consumed;
                $manual_overload += !empty($dx['manual_overload']) ? $dx['manual_overload'] : 0;
                $estimated_euro += !empty($dx['initial_estimated']) ? $dx['initial_estimated'] * $unit : 0;
                $inUsed = !empty($listTaskIdFollowTasks[$dx['id']]) && !empty($inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]]) ? $inUsedOfActivityTasks[$listTaskIdFollowTasks[$dx['id']]] : 0;
                $_inUsed += $inUsed;
            }

            if ($useManualConsumed) {
                $result[$_id]['Overload'] = $manual_overload;

                if ($_manual_consumed != 0) {
                    $_completed = ($_workload + $manual_overload) == 0 ? round(($_manual_consumed * 100), 2) : round(($_manual_consumed * 100) / ($_workload + $manual_overload), 2);
                } else {
                    $_completed = 0;
                }
            } else {
                $result[$_id]['Overload'] = $overload;
                // if ($_consumed != 0) {
                $_completed = ($_workload ) == 0 ? round(($_cons * 100), 2) : round(($_cons * 100) / $_workload, 2);
                // } else {
                //     $_completed = 0;
                // }
            }
            $_progress_order = $amount != 0 ? round($_progress_order_amount / $amount * 100, 2) : 0;
            $_completed = $_completed > 0 ? $_completed : 0;
            $result[$_id]['Consumed'] = $_cons;
            $result[$_id]['Remain'] = $_remain;
            $result[$_id]['Workload'] = $_workload;
            $result[$_id]['Initialworkload'] = $initial_estimated;
            $result[$_id]['UnitPrice'] = $unit_price;
            $result[$_id]['Workload€'] = $workload_euro;
            $result[$_id]['Consumed€'] = $consumed_euro;
            $result[$_id]['Remain€'] = $remain_euro;
            $result[$_id]['Estimated€'] = $estimated_euro;
            $result[$_id]['ManualOverload'] = $manual_overload;
            $result[$_id]['ManualConsumed'] = $_manual_consumed;
            $result[$_id]['Completed'] = $_completed;
            $result[$_id]['InUsed'] = $_inUsed;
            $result[$_id]['Amount€'] = $amount;
            $result[$_id]['%progressorder'] = $_progress_order;
            $result[$_id]['%progressorder€'] = $_progress_order_amount;
        }
        return $result;
    }

    function export_pdf() {
        $this->layout = false;
        ini_set('memory_limit', '2048M');
        $employee_name = $this->employee_info['Employee']['fullname'];
        $company_id = $this->employee_info['Company']['id'];
        if (!empty($this->data['Export'])) {
            $project_name = $this->data['Export']['project_name'];
            $project_id = $this->data['Export']['project_id'];
            extract($this->data['Export']);
            $canvas = explode(";", $canvas);
            $type = $canvas[0];
            $canvas = explode(",", $canvas[1]);
            $tmpFile = TMP . 'your_form.png';
            file_put_contents($tmpFile, base64_decode($canvas[1]));
            list($_width, $_height) = getimagesize($tmpFile);
            $height = 230;
            if (!empty($this->data['Export']['export_screen']) && $this->data['Export']['export_screen'] == 'ajax') {
                $height = 50;
            }
            $i = 0;
            $image = imagecreatefrompng($tmpFile);
            $list = array();
            do {
                if ($_height - $height >= 850) {
                    $h = 850;
                } else {
                    $h = $_height - $height;
                }
                if ($h > 100) {
                    $i++;
                    $crop = imagecreatetruecolor($_width, $h);
                    //set white background
                    $white = imagecolorallocate($crop, 255, 255, 255);
                    imagefill($crop, 0, 0, $white);

                    $tmpFile1 = TMP . 'your_form_' . $i . '.png';
                    imagecopy($crop, $image, 0, 0, 0, $height, $_width, $h);
                    imagepng($crop, $tmpFile1);
                    $list[$i] = $tmpFile1;
                    $height_last = $h;
                }
                $height += 850;
            } while ($height < $_height);

            unlink($tmpFile);
            $path = $this->_getPathLivrables($project_id);
            $this->loadModels('ProjectLivrable', 'Menu');
            $your_form_name = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'model' => 'project',
                    'controllers' => 'projects',
                    'functions' => 'your_form_plus',
                    'widget_id' => 'your_form_plus'
                )
            ));
            $now = date('d-m-Y_H-i-s', time());
            if (!empty($your_form_name)) {
                $your_form_name = $your_form_name['Menu']['name_fre'];
            } else {
                $your_form_name = 'Fiche+';
            }
            $file_name = $project_name . '_' . $employee_name . '_' . $now . '.pdf';
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $file_name = str_replace(' ', '-', $file_name);
            $file_name = $str_utility->removeVNUnicode($file_name);
            $saved = array(
                'name' => $your_form_name . ' ' . $now,
                'project_id' => $project_id,
                'livrable_file_attachment' => $file_name,
                'livrable_progression' => 0,
                'upload_date' => time(),
                'format' => 2
            );
            $this->ProjectLivrable->create();
            $this->ProjectLivrable->save($saved);
            $this->set(compact('list', 'height', 'rows', 'i', 'height_last', 'project_name', 'employee_name', 'file_name', 'path'));
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function your_form_plus($id = null) {
        $this->loadModels('BudgetSetting', 'ProjectRisk', 'ProjectRiskOccurrence', 'ProjectRiskSeverity', 'ProjectIssueStatus', 'ProjectIssueSeverity', 'ProjectIssue', 'ProjectLocalView', 'Dependency', 'ProjectDependency', 'ProjectGlobalView', 'ProjectIssueColor');
		$this->_checkRole(false, $id);
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));
		$bg_currency = $this->getCurrencyOfBudget();
        $this->loadModels('Menu', 'YourFormFilter', 'Profile', 'ProjectPhaseStatus', 'LogSystem', 'ProjectAcceptance', 'ProjectAcceptanceType', 'ProjectAmr');
        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project'
            ),
            'fields' => array('controllers', 'functions', 'widget_id', 'display', 'name_eng', 'name_fre')
        ));

        /* Get page menu name in Language */
        $lg_name = array(
            'fr' => 'name_fre',
            'en' => 'name_eng'
        );
        $cur_lang = empty($this->employee_info['Employee']['language']) ? 'en' : $this->employee_info['Employee']['language'];
        $page_title = array();
        foreach ($showMenu as $menu) {
            $page_title[$menu['Menu']['controllers']][$menu['Menu']['functions']] = $menu['Menu'][$lg_name[$cur_lang]];
        }

        $this->set('page_title', $page_title);
        unset($page_title, $cur_lang, $lg_name, $menu);
        /* End Get page menu name in Language */

        $langCode = Configure::read('Config.langCode');
        $_fields = ($langCode == 'fr') ? '{n}.Menu.name_fre' : '{n}.Menu.name_eng';
        $tranMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', $_fields) : array();
        $showMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', '{n}.Menu.display') : array();
        $this->autoInsertFilter();
        $yourFormFilter = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('widget', 'display'),
            'order' => array('weight')
        ));
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $page = 'Details';
        $curentPage = 'your_form_plus';
        $this->getDataForYourForm($page, $curentPage, $id);
        $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
        $projectRisks = $this->ProjectRisk->find("all", array(
            'fields' => array('id', 'project_risk', 'risk_assign_to', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_close_date', 'actions_manage_risk', 'file_attachement', 'format', 'project_issue_status_id', 'weight'),
            'recursive' => -1, "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $riskSeverities = $this->ProjectRiskSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $riskOccurrences = $this->ProjectRiskOccurrence->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_occurrence'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueStatus = $this->ProjectIssueStatus->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_status'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueSeverities = $this->ProjectIssueSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $projectIssues = $this->ProjectIssue->find("all", array(
            'recursive' => -1,
            "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $listProjectIssueId = !empty($projectIssues) ? Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}.ProjectIssue.id') : array();
        // project issue employee reference.
        $this->loadModels('Employee', 'ProfitCenter', 'ProjectIssueEmployeeRefer');
        $listEmployeeAssign = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'company_id IS NULL',
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
        $listProfitAssign = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'pcName')
        ));
        $projectIssueEmployeeRefer = $this->ProjectIssueEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_issue_id' => $listProjectIssueId
            ),
            'fields' => array('id', 'project_issue_id', 'reference_id', 'is_profit_center')
        ));
        $listReference = array();
        foreach ($projectIssueEmployeeRefer as $_projectIssueEmployeeRefer) {
            $dx = $_projectIssueEmployeeRefer['ProjectIssueEmployeeRefer'];
            if ($dx['is_profit_center'] == 0) {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listEmployeeAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listEmployeeAssign[$dx['reference_id']];
                }
            } else {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listProfitAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listProfitAssign[$dx['reference_id']];
                }
            }
        }
        $projectLocalView = $this->ProjectLocalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)
        ));
        if ($projectLocalView) {
            $link = trim($this->_getPathGlobal($id)
                    . $projectLocalView['ProjectLocalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExists = true;
            }
        }
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)
        ));
        //dependency
        $deps = $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name', 'color')
        ));
        $dependencies = $colors = array();
        foreach ($deps as $dep) {
            $dependencies[$dep['Dependency']['id']] = $dep['Dependency']['name'];
            $colors[$dep['Dependency']['id']] = $dep['Dependency']['color'];
        }
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => array(1, 2),
                'id !=' => $id
            ),
            'fields' => array('id', 'project_name')
        ));
        $dataDependency = $this->ProjectDependency->retrieve($id);
        $list = !empty($data) ? array_unique(Set::extract($data, '{n}.ProjectDependency.target_id')) : array();
        $count = $this->ProjectDependency->countChildren($list);
        // history.
        $this->loadModels('HistoryFilter', 'ProjectPhase');
        $employee_info = $this->employee_info;
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'dependency_' . $id,
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if (!empty($history)) {
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $info_image = array();
        // global view
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)));

        if ($projectGlobalView) {
            $link = trim($this->_getPathGlobal($id, 'global')
                    . $projectGlobalView['ProjectGlobalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExistsGlobal = true;
            } else {
                $info_image = getimagesize($link);
            }
        }
        // project budget internal.
        $getDataProjectTasks = $this->Project->dataFromProjectTask($id, $company_id);
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $getDataProjectTasks['workload'] -= $varE;
        $budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $this->loadModels('ProjectBudgetExternal', 'BudgetProvider', 'BudgetType', 'BudgetFunder', 'ProjectFinancePlusDate', 'ProjectPhasePlan', 'ProjectPhaseStatus', 'ProjectPart');
        $externalBudgets = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('SUM(man_day) AS exBudget')
        ));
        $externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
        $engagedErro = 0;
        if (!empty($projectName['Project']['activity_id'])) {
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $emp = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $_id => $val) {
                    $reals = !empty($emp[$_id]['tjm']) ? (float) str_replace(',', '.', $emp[$_id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
        }
        // budget external
        $budgetProviders = $this->BudgetProvider->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $_budgetTypes = $this->BudgetType->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'capex')
        ));
        $budgetTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.name') : array();
        $capexTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.capex') : array();
        $budgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $idOfExternals = !empty($budgetExternals) ? Set::classicExtract($budgetExternals, '{n}.ProjectBudgetExternal.id') : array();
        $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $company_id), null, null, '');
        $funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $taskExternals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1, 'external_id' => $idOfExternals),
            'group' => array('external_id'),
            'fields' => array('SUM(special_consumed) as consumed', 'SUM(estimated) as maday', 'external_id')
        ));
        $taskExternals = !empty($taskExternals) ? Set::combine($taskExternals, '{n}.ProjectTask.external_id', '{n}.0') : array();
        // finance+
        if (!empty($showMenu['finance_plus']) && $yourFormFilter['finance_plus'] == 1) {
            $invStart = $invEnd = $fonStart = $fonEnd = time();
            $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id)
            ));
            if (!empty($getSaveHistory)) {
                $invStart = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_start'] : time();
                $invEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_end'] : time();
                $fonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_start'] : time();
                $fonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_end'] : time();
            }
            /**
             * Lay cac finance+
             */
            $finances = $this->ProjectFinancePlus->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id')
            ));
            /**
             * Finance detail
             */
            $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
            ));
            $dataFinan = $totals = array();
            foreach ($_financeDetails as $key => $value) {
                $dx = $value['ProjectFinancePlusDetail'];
                if ($dx['type'] == 'fon') {
                    $dataFinan['fon'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['fon_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['fon'][$dx['model']])) {
                        $totals['fon'][$dx['model']] = 0;
                    }
                    $totals['fon'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                } else {
                    $dataFinan['inv'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['inv_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['inv'][$dx['model']])) {
                        $totals['inv'][$dx['model']] = 0;
                    }
                    $totals['inv'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                }
            }
        }
        //project phase plan
        $_projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('id', 'project_part_id', 'predecessor', 'planed_duration', 'project_planed_phase_id', 'project_phase_status_id',
                'phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date', 'weight', 'ref1', 'ref2', 'ref3', 'ref4', 'profile_id', 'progress'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id', 'color'
                )),
            'order' => array('weight' => 'asc'),
            'conditions' => array('project_id' => $id)
        ));
        //auto format order
        $count = count($_projectPhasePlans);
        for ($i = 0; $i < $count; $i++) {
            $newWeight = $i + 1;
            if ($_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] != "$newWeight") {
                $_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] = $newWeight;
                $this->ProjectPhasePlan->id = $_projectPhasePlans[$i]['ProjectPhasePlan']['id'];
                $this->ProjectPhasePlan->save(array(
                    'weight' => intval($newWeight)), array('validate' => false, 'callbacks' => false));
            }
        }
        //END
        $_listIDPhasePlans = !empty($_projectPhasePlans) ? Set::classicExtract($_projectPhasePlans, '{n}.ProjectPhasePlan.id') : array();
        $countTaskOfPhases = array();
        if (!empty($_listIDPhasePlans)) {
            $_projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_planed_phase_id' => $_listIDPhasePlans, 'project_id' => $id),
                'fields' => array('id', 'project_planed_phase_id', 'COUNT(project_planed_phase_id) AS Total'),
                'group' => array('project_planed_phase_id')
            ));
            $countTaskOfPhases = !empty($_projectTasks) ? Set::combine($_projectTasks, '{n}.ProjectTask.project_planed_phase_id', '{n}.0.Total') : array();
        }
        $_projectPhases = $this->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
            ),
            'fields' => array('id', 'name', 'profile_id', 'activated'),
            'order' => array('phase_order' => 'asc')
        ));
        $x = $projectPhases1 = array();
        foreach ($_projectPhases as $phase) {
            $p = $phase['ProjectPhase'];
            $projectPhases1[$p['id']] = $p['name'];
            if ($p['activated']) {
                $x[$p['id']] = $p['name'];
            }
        }
        $_projectPhases = $x;
        unset($x);
        $_phaseDefaults = !empty($_projectPhases) ? Set::combine($_projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.profile_id') : array();
        $_projectParts = $this->ProjectPart->find('list', array(
            'conditions' => array(
                'project_id' => $id
        )));
        $_projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'phase_status')
        ));
        $this->loadModels('Profile');
        $_profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $currentPhase = $this->ProjectPhasePlan->Project->find('first', array(
            'fields' => array('Project.id'), 'contain' => array(
                'ProjectPhase' => array('name'),
            ), 'conditions' => array('Project.id' => $id)));
        $this->set('currentPhase', isset($currentPhase['ProjectPhase']['id']) ? (int) $currentPhase['ProjectPhase']['id'] : 0);
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $this->set(compact('_projectPhases', 'projectPhases1', '_projectPhaseStatuses', '_projectPhasePlans', '_projectParts', 'countTaskOfPhases', '_profiles', 'activateProfile', '_phaseDefaults', 'manuallyAchievement'));
        // gantt planning.
        $this->helpers = array_merge($this->helpers, array(
            'GanttVs', 'GanttV2'));
        $mileStone = $this->Project->find("first", array(
            'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC')),
            "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'),
            'conditions' => array('Project.id' => $id)
        ));
        $_view = true;
        $listProjectToActivities[$projectName['Project']['id']] = $projectName['Project']['activity_id'];
        $this->_parseNew($listProjectToActivities, $projectName, $_view);
        // project task
        if (!empty($showMenu['task']) && $showMenu['task'] && $yourFormFilter['project_task'] == 1) {
            $orders = $this->requestAction('/admin_task/getTaskSettings');
            $i18n = $this->requestAction('/translations/getByLang/Project_Task');
            $projectTaskForTasks = $this->requestAction('/project_tasks/getDataTasks/' . $id);
            $settingP = $this->requestAction('/project_settings/get');
            $checkP = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $id),
                'fields' => 'is_freeze'
            ));
        }
        $this->loadModels('ProjectIssueSeverity');
        $listColor = $this->ProjectIssueColor->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'color')
        ));
        $colorDefault = $this->ProjectIssueColor->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'default' => 1
            )
        ));
        $colorDefault = !empty($colorDefault['ProjectIssueColor']['color']) ? $colorDefault['ProjectIssueColor']['color'] : '#004380';
        $colorSeverities = $this->ProjectIssueSeverity->find('list', array(
            'fields' => array('id', 'color'),
            'conditions' => array(
                'company_id' => $company_id
            ),
            'recursive' => -1
        ));
        // end
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id', 'name')
        ));
        $projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'phase_status')
        ));
        $commentKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ProjectAmr',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $todoKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ToDo',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $doneKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'Done',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $acceptancesKpi = $this->ProjectAcceptance->find('all', array(
            'fields' => '*',
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $typeAcceptance = $this->ProjectAcceptanceType->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $budgetKpi = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenusKpi = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        // Internal costs
        $remainExter = $varE - $externalConsumeds;
        $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
        $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $projectName['Project']['id']),
            'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average')
        ));
        $engagedErro = 0;
        $activityId = $projectName['Project']['activity_id'];
        $getDataActivities = $this->_parse($activityId);
        $sumEmployees = $getDataActivities['sumEmployees'];
        $employees = $getDataActivities['employees'];

        if (isset($sumEmployees[$activityId])) {
            foreach ($sumEmployees[$activityId] as $_id => $val) {
                $reals = !empty($employees[$_id]['tjm']) ? (float) str_replace(',', '.', $employees[$_id]['tjm']) : 1;
                $engagedErro += $val * $reals;
            }
        }
        $internals = $internalDetails = array();
        $count = $budgetEuro = $budgetManDay = $average = 0;
        if (!empty($projectBudgetInternals)) {
            foreach ($projectBudgetInternals as $key => $projectBudgetInternal) {
                $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                if (empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average'])) {
                    $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] = 0;
                }
                $average += $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                $internalDetails[$key]['budget_euro'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                $budgetEuro += ($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average']);
                $budgetManDay += !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                $count++;
            }
        }
        $average = ($count == 0) ? 0 : round($average / $count, 2);
        $internals = array(
            'budgetEuro' => $budgetEuro,
            'budgetManDay' => $budgetManDay,
            'forecastedManDay' => $getDataProjectTasks['consumed'] + $getDataProjectTasks['remain'],
            'forecastEuro' => $engagedErro + ($remainInter * $average),
            'varEuro' => ($budgetEuro != 0) ? round(((($engagedErro + ($remainInter * $average)) / $budgetEuro) - 1) * 100, 2) : 0,
            'consumedManday' => $getDataProjectTasks['consumed'],
            'consumedEuro' => $engagedErro,
            'remainManday' => $getDataProjectTasks['remain'],
            'remainEuro' => $remainInter * $average,
        );
        // External costs
        $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $projectName['Project']['id']),
            'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
        ));
        $externals = array();
        if (!empty($projectBudgetExternals)) {
            $exBudgetManDay = $exBudgetEuro = $exRemainEuro = $exForecastEuro = $exOrderEuro = 0;
            foreach ($projectBudgetExternals as $key => $projectBudgetExternal) {
                $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                $exBudgetEuro += !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                $exRemainEuro += !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                $order = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                $exOrderEuro += $order;
                $remain = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                $exForecastEuro += $order + $remain;
                $exBudgetManDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
            }
            $exVarEuro = ($exBudgetEuro == 0 ) ? -100 : round(((($exOrderEuro + $exRemainEuro) / $exBudgetEuro) - 1) * 100, 2);
            $externals['BudgetManDay'] = $exBudgetManDay;
            $externals['BudgetEuro'] = $exBudgetEuro;
            $externals['ForecastEuro'] = $exForecastEuro;
            $externals['VarEuro'] = $exVarEuro;
            $externals['ConsumedEuro'] = $exOrderEuro;
            $externals['RemainEuro'] = $exRemainEuro;
        }
        //********
        $this->getDataOfExternalTask($projectName['Project']['id']);
        $_projectTaskKpi = $this->_getPorjectTask($projectName['Project']['id']);
        $engaged = $_projectTaskKpi['engaged'];
        $validated = $_projectTaskKpi['validated'];
        $progression = $_projectTaskKpi['progression'];
        $this->_dashBoard($projectName['Project']['id']);
        //filter status.
        $_statusFilters = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'widget' => array('issue', 'risk', 'project_task', 'finance_plus', 'global_view', 'finance_two_plus')
            ),
            'fields' => array('widget', 'status')
        ));
        $statusFilters = array();
        foreach ($_statusFilters as $key => $statusFilter) {
            if (!empty($statusFilter)) {
                $statusFilters[$key] = explode(',', $statusFilter);
            }
        }

        if (!isset($showMenu['finance_two_plus'])) {
            $showMenu['finance_two_plus'] = 0;
        }
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
        $projectBackup = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_manager_id')
        ));
        $listCurPhase = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_phase_id')
        ));
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ? $displayParst['Menu']['display'] : 0;
        $breakpage = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'page_break' => 1
            ),
            'fields' => array('id', 'widget')
        ));
        // project budget fiscals.
        /**
         * Start, end date format
         */
        $startCurrent = time();
        $endCurrent = mktime(0, 0, 0, date("m", $startCurrent), date("d", $startCurrent) - 1, date("Y", $startCurrent) + 1);
        /**
         * Start, end date of next one year
         */
        $startNextOneYear = mktime(0, 0, 0, date("m", $endCurrent), date("d", $endCurrent) + 1, date("Y", $endCurrent));
        $endNextOneYear = mktime(0, 0, 0, date("m", $startNextOneYear), date("d", $startNextOneYear) - 1, date("Y", $startNextOneYear) + 1);
        /**
         * Danh sach star date, end date cua year
         */
        $year = date('Y', time());
        $listYears = array(
            $year => array('start' => $startCurrent, 'end' => $endCurrent),
            ($year + 1) => array('start' => $startNextOneYear, 'end' => $endNextOneYear),
        );
        $this->loadModels('ProjectBudgetSale', 'ProjectBudgetInvoice', 'ProjectBudgetPurchase', 'ProjectBudgetPurchaseInvoice');
        $this->ProjectBudgetSale->unbindModelAll();
        $this->ProjectBudgetSale->bindModel(array('hasMany' => array('ProjectBudgetInvoice')));
        $sales = $this->ProjectBudgetSale->find('all', array(
            'conditions' => array(
                'project_id' => $id
            )
        ));
        $purchaseValues = $saleValues = array();
        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $dx = $sale['ProjectBudgetSale'];
                $dy = $sale['ProjectBudgetInvoice'];
                if (!empty($dx['order_date']) && $dx['order_date'] != '0000-00-00') {
                    $_date = strtotime($dx['order_date']);
                    $_year = date('Y', $_date);
                    if (!isset($saleValues['order'][$_year])) {
                        $saleValues['order'][$_year] = 0;
                    }
                    $saleValues['order'][$_year] += $dx['sold'];
                }
                if (!empty($dy)) {
                    foreach ($dy as $val) {
                        if (!empty($val['due_date']) && $val['due_date'] != '0000-00-00') {
                            $_date = strtotime($val['due_date']);
                            $_year = date('Y', $_date);
                            if (!isset($saleValues['toBill'][$_year])) {
                                $saleValues['toBill'][$_year] = 0;
                            }
                            $saleValues['toBill'][$_year] += $val['billed'];
                        }
                        if (!empty($val['effective_date']) && $val['effective_date'] != '0000-00-00') {
                            $_date = strtotime($val['effective_date']);
                            $_year = date('Y', $_date);
                            if (!isset($saleValues['billed'][$_year])) {
                                $saleValues['billed'][$_year] = 0;
                            }
                            $saleValues['billed'][$_year] += $val['billed'];
                        }
                    }
                }
            }
        }
        // tinh purchase
        $this->ProjectBudgetPurchase->unbindModelAll();
        $this->ProjectBudgetPurchase->bindModel(array('hasMany' => array('ProjectBudgetPurchaseInvoice')));
        $purchases = $this->ProjectBudgetPurchase->find('all', array(
            'conditions' => array(
                'project_id' => $id
            )
        ));
        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $dx = $purchase['ProjectBudgetPurchase'];
                $dy = $purchase['ProjectBudgetPurchaseInvoice'];
                if (!empty($dx['order_date']) && $dx['order_date'] != '0000-00-00') {
                    $_date = strtotime($dx['order_date']);
                    $_year = date('Y', $_date);
                    if (!isset($purchaseValues['order'][$_year])) {
                        $purchaseValues['order'][$_year] = 0;
                    }
                    $purchaseValues['order'][$_year] += $dx['sold'];
                }
                if (!empty($dy)) {
                    foreach ($dy as $val) {
                        if (!empty($val['due_date']) && $val['due_date'] != '0000-00-00') {
                            $_date = strtotime($val['due_date']);
                            $_year = date('Y', $_date);
                            if (!isset($purchaseValues['toBill'][$_year])) {
                                $purchaseValues['toBill'][$_year] = 0;
                            }
                            $purchaseValues['toBill'][$_year] += $val['billed'];
                        }
                        if (!empty($val['effective_date']) && $val['effective_date'] != '0000-00-00') {
                            $_date = strtotime($val['effective_date']);
                            $_year = date('Y', $_date);
                            if (!isset($purchaseValues['billed'][$_year])) {
                                $purchaseValues['billed'][$_year] = 0;
                            }
                            $purchaseValues['billed'][$_year] += $val['billed'];
                        }
                    }
                }
            }
        }
        // finance++
        $this->loadModels('ProjectFinanceTwoPlusDate', 'ProjectFinanceTwoPlus', 'ProjectFinanceTwoPlusDetail');
        $getSaveHistory = $this->ProjectFinanceTwoPlusDate->find('first', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $startTwoFinance = !empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['start']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['start'] : time();
        $endTwoFinance = empty($getSaveHistory) && !empty($getSaveHistory['ProjectFinanceTwoPlusDate']['end']) ? $getSaveHistory['ProjectFinanceTwoPlusDate']['end'] : time();
        /**
         * Lay cac finance+
         */
        $twoFinances = $this->ProjectFinanceTwoPlus->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'name'),
        ));
        /**
         * Finance detail
         */
        $_twoFinanceDetails = $this->ProjectFinanceTwoPlusDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
        ));
        $financeTwoYear = $twoFinanceDetails = $totalTwoFinan = array();

        if (!empty($_twoFinanceDetails)) {
            foreach ($_twoFinanceDetails as $twoFinanceDetail) {
                $dx = $twoFinanceDetail['ProjectFinanceTwoPlusDetail'];
                $_dx = $dx;
                unset($_dx['id']);
                unset($_dx['project_id']);
                unset($_dx['project_finance_two_plus_id']);
                unset($_dx['year']);
                unset($_dx['created']);
                unset($_dx['updated']);
                $twoFinanceDetails[$dx['project_finance_two_plus_id']][$dx['year']] = $_dx;
                if (!isset($totalTwoFinan['budget_initial'])) {
                    $totalTwoFinan['budget_initial'] = 0;
                }
                $totalTwoFinan['budget_initial'] += !empty($dx['budget_initial']) ? $dx['budget_initial'] : 0;
                if (!isset($totalTwoFinan['budget_revised'])) {
                    $totalTwoFinan['budget_revised'] = 0;
                }
                $totalTwoFinan['budget_revised'] += !empty($dx['budget_revised']) ? $dx['budget_revised'] : 0;
                if (!isset($totalTwoFinan['last_estimated'])) {
                    $totalTwoFinan['last_estimated'] = 0;
                }
                $totalTwoFinan['last_estimated'] += !empty($dx['last_estimated']) ? $dx['last_estimated'] : 0;
                if (!isset($totalTwoFinan['engaged'])) {
                    $totalTwoFinan['engaged'] = 0;
                }
                $totalTwoFinan['engaged'] += !empty($dx['engaged']) ? $dx['engaged'] : 0;
                if (!isset($totalTwoFinan['bill'])) {
                    $totalTwoFinan['bill'] = 0;
                }
                $totalTwoFinan['bill'] += $dx['bill'];
                if (!isset($totalTwoFinan['disbursed'])) {
                    $totalTwoFinan['disbursed'] = 0;
                }
                $totalTwoFinan['disbursed'] += !empty($dx['disbursed']) ? $dx['disbursed'] : 0;
                // total cho tung nam.
                if (!isset($totalTwoFinan[$dx['year']]['budget_initial'])) {
                    $totalTwoFinan[$dx['year']]['budget_initial'] = 0;
                }
                $totalTwoFinan[$dx['year']]['budget_initial'] += !empty($dx['budget_initial']) ? $dx['budget_initial'] : 0;
                if (!isset($totalTwoFinan[$dx['year']]['budget_revised'])) {
                    $totalTwoFinan[$dx['year']]['budget_revised'] = 0;
                }
                $totalTwoFinan[$dx['year']]['budget_revised'] += !empty($dx['budget_revised']) ? $dx['budget_revised'] : 0;
                if (!isset($totalTwoFinan[$dx['year']]['last_estimated'])) {
                    $totalTwoFinan[$dx['year']]['last_estimated'] = 0;
                }
                $totalTwoFinan[$dx['year']]['last_estimated'] += !empty($dx['last_estimated']) ? $dx['last_estimated'] : 0;
                if (!isset($totalTwoFinan[$dx['year']]['engaged'])) {
                    $totalTwoFinan[$dx['year']]['engaged'] = 0;
                }
                $totalTwoFinan[$dx['year']]['engaged'] += !empty($dx['engaged']) ? $dx['engaged'] : 0;
                if (!isset($totalTwoFinan[$dx['year']]['bill'])) {
                    $totalTwoFinan[$dx['year']]['bill'] = 0;
                }
                $totalTwoFinan[$dx['year']]['bill'] += $dx['bill'];
                if (!isset($totalTwoFinan[$dx['year']]['disbursed'])) {
                    $totalTwoFinan[$dx['year']]['disbursed'] = 0;
                }
                $totalTwoFinan[$dx['year']]['disbursed'] += !empty($dx['disbursed']) ? $dx['disbursed'] : 0;
                // total cho tung id.
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_initial'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_initial'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_initial'] += !empty($dx['budget_initial']) ? $dx['budget_initial'] : 0;
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_revised'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_revised'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['budget_revised'] += !empty($dx['budget_revised']) ? $dx['budget_revised'] : 0;
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['last_estimated'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['last_estimated'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['last_estimated'] += !empty($dx['last_estimated']) ? $dx['last_estimated'] : 0;
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['engaged'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['engaged'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['engaged'] += !empty($dx['engaged']) ? $dx['engaged'] : 0;
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['bill'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['bill'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['bill'] += $dx['bill'];
                if (!isset($totalTwoFinan[$dx['project_finance_two_plus_id']]['disbursed'])) {
                    $totalTwoFinan[$dx['project_finance_two_plus_id']]['disbursed'] = 0;
                }
                $totalTwoFinan[$dx['project_finance_two_plus_id']]['disbursed'] += !empty($dx['disbursed']) ? $dx['disbursed'] : 0;

                $financeTwoYear[$dx['year']] = $dx['year'];
            }
        }
        ksort($financeTwoYear);
        $listProgress = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('id', 'progress', 'phase_real_start_date', 'phase_real_end_date')
        ));
        $listDateCompleted = $listDateP = array();
        foreach ($listProgress as $key => $listProgres) {
            $dx = $listProgres['ProjectPhasePlan'];
            if ($dx['phase_real_start_date'] != '' && $dx['phase_real_end_date'] != '' && $dx['phase_real_start_date'] != '0000-00-00' && $dx['phase_real_end_date'] != '0000-00-00' && strtotime($dx['phase_real_end_date']) > strtotime($dx['phase_real_start_date'])) {
                $s = strtotime($dx['phase_real_start_date']);
                $e = strtotime($dx['phase_real_end_date']);
                $totalP = ($e - $s) / (60 * 60 * 24);
                $dx['progress'] = !empty($dx['progress']) ? $dx['progress'] : 0;
                $per = $totalP * $dx['progress'] / 100;
                $i = 1;
                while ($s <= $e) {
                    if ($i <= $per) {
                        $listDateCompleted[$s] = $s;
                        $i++;
                    }
                    $listDateP[$s] = $s;
                    $s += 60 * 60 * 24;
                }
            }
        }
        if (count($listDateP) > 0) {
            $progresMilestone = (count($listDateCompleted) / count($listDateP)) * 100;
        } else {
            $progresMilestone = 0;
        }
        $progresMilestone = ($progresMilestone > 100) ? 100 : ($progresMilestone < 0 ? 0 : $progresMilestone);
        $this->set(compact('totalTwoFinan', 'twoFinanceDetails', 'twoFinances', 'startTwoFinance', 'endTwoFinance', 'financeTwoYear', 'progresMilestone'));
        $this->set(compact('budgetKpi', 'settingMenusKpi', 'internals', 'externals', 'progression', 'engaged', 'validated', 'projectBackup', 'listCurPhase', 'breakpage', 'saleValues', 'purchaseValues', 'year'));
        $this->set(compact('showMenu', 'tranMenu', 'visionTaskExports', 'yourFormFilter', 'totals', 'manuallyAchievement', 'activateProfile', 'profiles', 'projectPhaseStatuses', 'commentKpi', 'todoKpi', 'doneKpi', 'statusFilters', 'displayParst', 'acceptancesKpi', 'typeAcceptance'));
        $this->set(compact('taskExternals', 'budgetTypes', 'capexTypes', 'budgetProviders', 'budgetExternals', 'dataFinan', 'finances', 'mileStone', 'orders', 'i18n', 'projectTaskForTasks', 'settingP', 'checkP', 'listReference', 'listColor', 'colorDefault', 'colorSeverities'));
        $this->set(compact('dependencies', 'colors', 'listProjects', 'dataDependency', 'project_id', 'list', 'count', 'history', 'projectGlobalView', 'noFileExistsGlobal', 'info_image', 'getDataProjectTasks', 'budgets', 'engagedErro', 'externalBudgets', 'externalConsumeds'));
        $this->set(compact('page', 'budget_settings', 'ProjectArms', 'id', 'projectRisks', 'riskSeverities', 'riskOccurrences', 'issueStatus', 'riskSeverities', 'issueSeverities', 'projectIssues', 'projectLocalView', 'noFileExists', 'projectName', 'bg_currency'));
    }

    public function fixWeatherUpdate() {
        $this->loadModels('Project', 'ProjectAmr');

        $idPA = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'fields' => array('project_id'),
            'conditions' => array('not' => array('project_id' => null)),
        ));

        $listPA = array();
        foreach ($idPA as $key => $value) {
            $listPA[$key] = $value['ProjectAmr']['project_id'];
        }
        // list projects can't update weather
        $projects = $this->Project->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_amr_program_id', 'project_amr_sub_program_id', 'project_manager_id', 'start_date', 'end_date'),
            'conditions' => array('not' => array('id' => $listPA)),
        ));

        // data default
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        if (!empty($projects)) {
            foreach ($projects as $key => $value) {

                $data_arm['ProjectAmr']['project_amr_program_id'] = $value['Project']['project_amr_program_id'];
                $data_arm['ProjectAmr']['project_amr_sub_program_id'] = $value['Project']['project_amr_sub_program_id'];
                $data_arm['ProjectAmr']['project_manager_id'] = $value['Project']['project_manager_id'];
                $data_arm['ProjectAmr']['weather'] = 'sun';
                $data_arm['ProjectAmr']['cost_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['planning_weather'] = 'sun';
                $data_arm['ProjectAmr']['risk_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['organization_weather'] = 'sun';
                $data_arm['ProjectAmr']['perimeter_weather'] = 'sun';
                $data_arm['ProjectAmr']['issue_control_weather'] = 'sun';
                $data_arm['ProjectAmr']['customer_point_of_view'] = 'sun';
                $data_arm['ProjectAmr']['rank'] = 'up';
                $data_arm['ProjectAmr']['created'] = $value['Project']['start_date'];
                $data_arm['ProjectAmr']['updated'] = $value['Project']['start_date'];

                //Begin Create arm project
                $data_arm['ProjectAmr']['project_id'] = $value['Project']['id'];
                $this->ProjectAmr->create();
                $this->ProjectAmr->save($data_arm);
            }
        }
        $this->redirect(array('action' => 'index'));
    }

    protected function _getPathGlobal($project_id, $global_view = false) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'localviews' . DS;
        if ($global_view == 'global')
            $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    public function checkNameAcitivity() {
        if (!empty($_POST)) {
            $name = $_POST['data'];
            $this->loadModel('Activity');
            $check = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'name' => $name
                )
            ));
            if (!empty($check)) {
                echo 1;
            } else {
                echo 0;
            }
        }
        exit;
    }

    private function getNextMilestone($listProjectId) {
        $listColorNextMil = $listNextMilestoneByDay = $listNextMilestoneByWeek = array();
        $this->loadModels('ProjectMilestone');
        if (!empty($listProjectId)) {
            foreach ($listProjectId as $id) {
                $nextMilestone = $this->ProjectMilestone->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_id' => $id,
                        'milestone_date >' => date('Y-m-d', time())
                    ),
                    'fields' => array('id', 'milestone_date'),
                    'order' => array('milestone_date' => 'ASC')
                ));
                $nextMilestoneByDay = $nextMilestoneByWeek = 0;
                $bgc = '#ff290a';
                if (!empty($nextMilestone)) {
                    $nextMilestone = $nextMilestone['ProjectMilestone']['milestone_date'];
                    $nextMilestone = strtotime($nextMilestone);
                    $now = time();
                    if ($now < $nextMilestone) {
                        $diff = ($nextMilestone - $now) / (24 * 60 * 60) + 1;
                        $nextMilestoneByDay = (int) ($diff);
                        $nextMilestoneByWeek = (int) ($diff / 7);
                    }
                    if ($nextMilestoneByWeek > 0 && $nextMilestoneByWeek <= 3) {
                        $bgc = '#F3960B';
                    } else if ($nextMilestoneByWeek > 3) {
                        $bgc = '#5DBF56';
                    }
                }
                $listColorNextMil[$id] = $bgc;
                $listNextMilestoneByDay[$id] = $nextMilestoneByDay;
                $listNextMilestoneByWeek[$id] = $nextMilestoneByWeek;
            }
        }
        return array($listColorNextMil, $listNextMilestoneByDay, $listNextMilestoneByWeek);
    }

    function your_form_filter($project_id) {
        $this->loadModels('YourFormFilter', 'Menu', 'ProjectStatus', 'ProjectIssueStatus', 'ProfileProjectManagerDetail');
        $isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
        $employee_id = $this->employee_info['Employee']['id'];
        $company_id = $this->employee_info['Company']['id'];
        $this->autoInsertFilter();
        $yourFromFilters = $this->YourFormFilter->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
            ),
            'order' => array('weight')
        ));
        $langCode = Configure::read('Config.langCode');
        foreach ($yourFromFilters as $key => $yourFromFilter) {

            $dx = $yourFromFilter['YourFormFilter'];
            $widget = $dx['widget'];

            // thay doi widget_id
            if ($widget == 'buget_internal') {
                $widget = 'internal_cost';
            } else if ($widget == 'budget_externals') {
                $widget = 'external_cost';
            } else if ($widget == 'project_task') {
                $widget = 'task';
            } else if ($widget == 'location') {
                $widget = 'local_view';
            }

            if ($isProfileManager != 0) {
                $check = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model_id' => $isProfileManager,
                        'widget_id' => $widget,
                        'display' => 1
                    )
                ));
                $check = !empty($check['ProfileProjectManagerDetail']) ? $check['ProfileProjectManagerDetail'] : array();
            } else {
                $check = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model' => 'project',
                        'widget_id' => $widget,
                        'display' => 1
                    )
                ));
                $check = !empty($check['Menu']) ? $check['Menu'] : array();
            }


            if (empty($check)) {
                if ($isProfileManager != 0) {
                    $check = $this->ProfileProjectManagerDetail->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model_id' => $isProfileManager,
                            'widget_id' => $widget,
                        )
                    ));
                    $check = !empty($check['ProfileProjectManagerDetail']) ? $check['ProfileProjectManagerDetail'] : array();
                } else {
                    $check = $this->Menu->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model' => 'project',
                            'widget_id' => $widget
                        )
                    ));
                    $check = !empty($check['Menu']) ? $check['Menu'] : array();
                }
            }
            if (!empty($check)) {
                $name = ($langCode == 'fr') ? $check['name_fre'] : $check['name_eng'];
                if (!empty($check) && $check['display'] == 0) {
                    //$yourFromFilters[$key]['display'] = 0;
                    unset($yourFromFilters[$key]);
                } else if ($dx['widget'] == 'kpi') {
                    unset($yourFromFilters[$key]);
                } else if (!empty($check) && ($check['display'] == 1)) {
                    $yourFromFilters[$key]['YourFormFilter']['name'] = $name;
                }
            } else if ($dx['widget'] == 'weather') {
                $yourFromFilters[$key]['YourFormFilter']['name'] = 'Weather';
            } else {
                unset($yourFromFilters[$key]);
            }
        }
        $projectStatus = $this->ProjectStatus->find('list', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'name')
        ));
        $projectIssueStatus = $this->ProjectIssueStatus->find('list', array(
            'recusive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'issue_status')
        ));
        $_statusFilters = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'widget' => array('issue', 'risk', 'project_task', 'finance_plus', 'global_view', 'finance_two_plus')
            ),
            'fields' => array('widget', 'status')
        ));
        $statusFilters = array();
        foreach ($_statusFilters as $key => $statusFilter) {
            $statusFilters[$key] = explode(',', $statusFilter);
        }
        $this->set(compact('yourFromFilters', 'employee_id', 'project_id', 'projectStatus', 'projectIssueStatus', 'statusFilters'));
    }

    public function autoInsertFilter() {
        $employee_id = $this->employee_info['Employee']['id'];
        $this->loadModels('YourFormFilter');
        $default = array(
            0 => array(
                'widget' => 'your_form',
                'employee_id' => $employee_id,
                'name' => 'Your form',
                'display' => 0,
                'weight' => 1
            ),
            1 => array(
                'widget' => 'weather',
                'employee_id' => $employee_id,
                'name' => 'Weather',
                'display' => 1,
                'weight' => 2
            ),
            2 => array(
                'widget' => 'gantt',
                'employee_id' => $employee_id,
                'name' => 'Gantt',
                'display' => 1,
                'weight' => 3
            ),
            3 => array(
                'widget' => 'milestone',
                'employee_id' => $employee_id,
                'name' => 'Gantt detail',
                'display' => 0,
                'weight' => 4
            ),
            4 => array(
                'widget' => 'risk',
                'employee_id' => $employee_id,
                'name' => 'Risk',
                'display' => 0,
                'weight' => 5
            ),
            5 => array(
                'widget' => 'issue',
                'employee_id' => $employee_id,
                'name' => 'Issue',
                'display' => 0,
                'weight' => 6
            ),
            6 => array(
                'widget' => 'location',
                'employee_id' => $employee_id,
                'name' => 'Location',
                'display' => 0,
                'weight' => 7
            ),
            7 => array(
                'widget' => 'dependency',
                'employee_id' => $employee_id,
                'name' => 'Dependency',
                'display' => 0,
                'weight' => 8
            ),
            8 => array(
                'widget' => 'global_view',
                'employee_id' => $employee_id,
                'name' => 'Global views',
                'display' => 0,
                'weight' => 9
            ),
            9 => array(
                'widget' => 'buget_internal',
                'employee_id' => $employee_id,
                'name' => 'Budget internals',
                'display' => 0,
                'weight' => 10
            ),
            10 => array(
                'widget' => 'budget_externals',
                'employee_id' => $employee_id,
                'name' => 'Budget externals',
                'display' => 0,
                'weight' => 11
            ),
            11 => array(
                'widget' => 'project_task',
                'employee_id' => $employee_id,
                'name' => 'Project task',
                'display' => 0,
                'weight' => 12
            ),
            12 => array(
                'widget' => 'phase',
                'employee_id' => $employee_id,
                'name' => 'Phase plan',
                'display' => 0,
                'weight' => 13
            ),
            13 => array(
                'widget' => 'finance_plus',
                'employee_id' => $employee_id,
                'name' => 'Finance+',
                'display' => 0,
                'weight' => 14
            ),
            14 => array(
                'widget' => 'fy_budget',
                'employee_id' => $employee_id,
                'name' => 'FY Budget',
                'display' => 0,
                'weight' => 15
            ),
            15 => array(
                'widget' => 'finance_two_plus',
                'employee_id' => $employee_id,
                'name' => 'Finance++',
                'display' => 0,
                'weight' => 16
            )
        );
        foreach ($default as $key => $value) {
            $check = $this->YourFormFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'widget' => $value['widget'],
                    'employee_id' => $employee_id,
                )
            ));
            if (empty($check)) {
                $this->YourFormFilter->create();
                $this->YourFormFilter->save($value);
            }
        }
    }

    public function update_your_form_filter() {
        $result = false;
        $this->layout = false;
        $this->loadModels('YourFormFilter');
        if (!empty($this->data)) {
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes')
            );
            $this->YourFormFilter->create();
            if (!empty($this->data['id'])) {
                $this->YourFormFilter->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->YourFormFilter->save(array_merge($this->data, $data))) {
                $result = true;
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->YourFormFilter->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    public function order_your_form_filter($employee_id = null) {
        $this->loadModels('YourFormFilter');
        if (!empty($this->data)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight != 0) {
                    $this->YourFormFilter->id = $id;
                    $this->YourFormFilter->saveField('weight', $weight);
                }
            }
        }
        die;
    }

    public function updateSelectStatus() {
        if (!empty($_POST)) {
            $employee_id = $this->employee_info['Employee']['id'];
            $this->loadModels('YourFormFilter');
            $check = $this->YourFormFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'widget' => $_POST['widget'],
                    'employee_id' => $employee_id
                )
            ));
            $status = '';
            foreach ($_POST['data'] as $value) {
                if (empty($status)) {
                    $status .= $value;
                } else {
                    $status .= ',' . $value;
                }
            }
            if (!empty($check)) {
                $id = $check['YourFormFilter']['id'];
                $this->YourFormFilter->id = $id;
                $this->YourFormFilter->save(array('status' => $status));
            }
            echo 'DONE!';
        }
        die;
    }

    public function updateDisplayFilter() {
        if (!empty($this->data)) {
            $id = $this->data['id'];
            $display = $this->data['display'];
            $col = $this->data['col'];
            $this->loadModels('YourFormFilter');
            $this->YourFormFilter->id = $id;
            $this->YourFormFilter->save(array($col => $display));
            echo 'DONE!';
        }
        die;
    }

    public function attachment_static($project_id = null) {
        $this->layout = false;
        $link = '';
        $key = isset($_GET['sid']) ? $_GET['sid'] : '';
        if ($key) {
            $info = $this->ApiKey->retrieve($key);
            if (empty($info)) {
                die('Permission denied');
            }
        } else {
            die('Permission denied');
        }
        $link = trim(FILES . 'staticmap.png');
        if (empty($link)) {
            $link = '';
        } else {
            if (!file_exists($link) || !is_file($link)) {
                $link = '';
            }
            $info = pathinfo($link);
            $this->view = 'Media';
            $params = array(
                'id' => !empty($info['basename']) ? $info['basename'] : '',
                'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                'name' => !empty($info['filename']) ? $info['filename'] : '',
                'mimeType' => array(
                    'bmp' => 'image/bmp',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pps' => 'application/vnd.ms-powerpoint',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                ),
                'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
            );
            if (!empty($this->params['url']['download'])) {
                $params['download'] = true;
            }
            $this->set($params);
        }
    }

    public function getDataOfExternalTask($id) {
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectBudgetExternal');
        $externals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('DISTINCT external_id as external'),
            'conditions' => array('ProjectTask.project_id' => $id,
                'ProjectTask.special' => 1
            ),
            'order' => array('ProjectTask.external_id'),
            'group' => array('ProjectTask.external_id')
        ));
        $dataExternals = array();
        foreach ($externals as $_index => $_external) {
            $external = $_external['ProjectTask']['external'];
            $providerName = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'fields' => array('BP.name'),
                'conditions' => array('ProjectBudgetExternal.id' => $external),
                'joins' => array(
                    array(
                        'table' => 'budget_providers',
                        'alias' => 'BP',
                        'type' => 'LEFT',
                        'foreignKey' => 'budget_provider_id',
                        'conditions' => array(
                            'BP.id = ProjectBudgetExternal.budget_provider_id'
                        )
                    )
                )
            ));
            $setYearExternal = array();
            $dataSetsExternal = array();
            $maxValue = 0;
            $minDate = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('MIN(task_start_date) as date'),
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                )
            ));
            $maxDate = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('MAX(task_end_date) as date'),
                'conditions' => array(
                    'ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                )
            ));
            $minDate = explode('-', $minDate[0][0]['date']);
            $maxDate = explode('-', $maxDate[0][0]['date']);
            $minDate = mktime(0, 0, 0, $minDate[1] - 1, $minDate[2], $minDate[0]);
            $maxDate = mktime(0, 0, 0, $maxDate[1] + 1, $maxDate[2], $maxDate[0]);
            $tasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'fields' => array('SUM(special_consumed) as consumed', 'task_title', 'SUM(estimated) as planed', 'DATE_FORMAT(task_start_date, "%m-%y") as date', 'DATE_FORMAT(task_start_date, "%Y") as year'),
                'conditions' => array('ProjectTask.project_id' => $id,
                    'ProjectTask.special' => 1,
                    'ProjectTask.external_id' => $external
                ),
                'order' => array('ProjectTask.task_start_date'),
                'group' => array('DATE_FORMAT(task_start_date, "%M%Y")')
            ));
            $tasks = Set::combine($tasks, '{n}.0.date', '{n}');
            $sumPlaned = $sumConsumed = $progress = 0;
            while ($minDate <= $maxDate) {
                $key = date("m", $minDate) . '-' . date("y", $minDate);
                if (isset($tasks[$key])) {
                    $_val = $tasks[$key];
                    $setYearExternal[] = $_val[0]['year'];
                    $setNameExternal = $_val['ProjectTask']['task_title'];
                    //progress
                    $sumConsumed += $_val[0]['consumed'];
                    $sumPlaned += $_val[0]['planed'];
                }
                if ($minDate <= time())
                    $dataSetsExternal[] = array('date' => $key, 'planed' => $sumPlaned, 'consumed' => $sumConsumed);
                else
                    $dataSetsExternal[] = array('date' => $key, 'planed' => $sumPlaned);
                $minDate = mktime(0, 0, 0, date("m", $minDate) + 1, date("d", $minDate), date("Y", $minDate));
            }

            if ($sumPlaned == 0 || $sumConsumed == 0)
                $progress = 0;
            else
                $progress = round(($sumConsumed / $sumPlaned), 2) * 100;
            $maxValue = $sumPlaned + round($sumPlaned * 0.1);
            $manDayExternals = $maxValue;
            $setYearExternal = array_unique($setYearExternal);
            $setYearExternal = join("-", $setYearExternal);
            $dataExternals[$external] = array('progressExternal' => $progress, 'dataSetsExternal' => $dataSetsExternal, 'manDayExternals' => $manDayExternals, 'setYearExternal' => $setYearExternal, 'setNameExternal' => $setNameExternal, 'setProviderName' => isset($providerName[0]['BP']['name']) ? $providerName[0]['BP']['name'] : null);
        }
        $this->set(compact('dataExternals'));
    }

    function _parseParams() {
        $year = date('Y');
        if (!empty($this->params['url']['year'])) {
            $year = intval($this->params['url']['year']);
        }
        $_start = strtotime($year . '-1-1');
        $_end = strtotime($year . '-12-31');

        if (empty($_start) || empty($_end)) {
            $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(compact('_start', '_end'));
        return array($_start, $_end);
    }

    private function _dashBoard($project_id) {
        $this->loadModel('Project');
        $filterId = isset($this->params['url']['filter']) ? $this->params['url']['filter'] : 1;
        list($_start, $_end) = $this->_parseParams();
        $staffings = array();
        if ($filterId == 1) {
            $staffings = $this->_staffingEmloyee($project_id);
        }
        $result = $manDays = array();
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $sumConsumed = $sumPlaned = 0;
        if (!empty($staffings)) {
            $datas = $this->_totalStaffing($staffings);
            $_setYear = $years = $estimatedNextMonths = array();
            $consumed = $validated = 0;

            $lastData = count($datas) - 1;
            $i = 0;
            foreach ($datas as $key => $val) {
                if ($i == 0)
                    $startDay = $val['set_date'];
                elseif ($i == $lastData)
                    $endDay = $val['set_date'];
                $i++;
            }
            $minDate = mktime(0, 0, 0, date("m", $startDay) - 1, date("d", $startDay), date("Y", $startDay));
            $maxDate = !empty($endDay) ? mktime(0, 0, 0, date("m", $endDay) + 1, date("d", $endDay), date("Y", $endDay)) : 0;
            while ($minDate < $maxDate) {
                $key = date("m", $minDate) . '/' . date("y", $minDate);
                if (isset($datas[$key])) {
                    $_val = $datas[$key];
                    $setYear[] = date("Y", $minDate);

                    //progress
                    $sumConsumed += $_val['consumed'];
                    $sumPlaned += $_val['validated'];
                }
                if ($minDate <= time()) {
                    $dataSets[] = array('date' => $key, 'validated' => $sumPlaned, 'consumed' => $sumConsumed);
                } else {
                    $dataSets[] = array('date' => $key, 'validated' => $sumPlaned);
                }
                $minDate = mktime(0, 0, 0, date("m", $minDate) + 1, date("d", $minDate), date("Y", $minDate));
            }
        }
        $manDays = $sumPlaned + round($sumPlaned * 0.1, 2);
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('project_name')
        ));
        $_setYear = !empty($_setYear) ? array_unique($_setYear) : array();
        asort($_setYear);
        $setYear = !empty($_setYear) ? implode('-', $_setYear) : '';
        $display = isset($this->params['url']['ControlDisplay']) ? $this->params['url']['ControlDisplay'] : range(1, 3);

        $countLine = 0;
        if (!empty($dataSets))
            $countLine = count($dataSets);
        $this->set(compact('dataSets', 'filterId', 'project_id', 'manDays', 'projectName', 'setYear', 'display', 'countLine'));
    }

    /**
     * Staffing of employee
     *
     *
     * @return array()
     * @access private
     */
    private function _staffingEmloyee($project_id = null) {
        $this->loadModel('TmpStaffingSystem');
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
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * Employee => array(
         *      'time' => array()
         * )
         */
        if (!empty($getDatas)) {
            foreach ($getDatas as $getData) {
                $dx = $getData['TmpStaffingSystem'];
                // date
                $datas[$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if (!isset($datas[$dx['model_id']][$dx['date']]['validated'])) {
                    $datas[$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if (!isset($datas[$dx['model_id']][$dx['date']]['consumed'])) {
                    $datas[$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                $datas[$dx['model_id']][$dx['date']]['remains'] = $datas[$dx['model_id']][$dx['date']]['validated'] - $datas[$dx['model_id']][$dx['date']]['consumed'];
            }
        }
        if (!empty($datas)) {
            $tmpDatas = $datas;
            $datas = $this->_resetRemainSystem($tmpDatas);
        }
        $ids = !empty($datas) ? array_keys($datas) : array();
        $this->loadModel('Employee');
        $employees = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $ids),
            'fields' => array('id', 'fullname'),
            'order' => array('Employee.id')
        ));
        if (!empty($datas['999999999'])) {
            $notAffected = array(
                'Employee' => array(
                    'id' => '999999999',
                    'fullname' => 'Not Affected'
                )
            );
            $employees[] = $notAffected;
        }
        $staffings = array();
        foreach ($employees as $key => $employee) {
            $staffings[$key]['id'] = $employee['Employee']['id'];
            $staffings[$key]['is_check'] = 1;
            $staffings[$key]['name'] = $employee['Employee']['fullname'];
            $staffings[$key]['data'] = isset($datas[$employee['Employee']['id']]) ? $datas[$employee['Employee']['id']] : '';
        }
        return $staffings;
    }

    /**
     *  Tinh toan staffing ho tro cho bo loc va Dashboard generating
     *
     */
    private function _totalStaffing($staffings) {
        $staffings = !empty($staffings) ? Set::combine($staffings, '{n}.id', '{n}.data') : array();
        $datas = array();
        if (!empty($staffings)) {
            foreach ($staffings as $staffing) {
                foreach ($staffing as $time => $values) {
                    $_time = date('m/y', $values['date']);
                    $datas[$time]['date'] = $_time;
                    $datas[$time]['set_date'] = $time;
                    if (!isset($datas[$time]['consumed'])) {
                        $datas[$time]['consumed'] = 0;
                    }
                    $datas[$time]['consumed'] += $values['consumed'];
                    if (!isset($datas[$time]['validated'])) {
                        $datas[$time]['validated'] = 0;
                    }
                    $datas[$time]['validated'] += $values['validated'];
                    if (!isset($datas[$time]['remains'])) {
                        $datas[$time]['remains'] = 0;
                    }
                    $datas[$time]['remains'] += $values['remains'];
                }
            }
        }
        ksort($datas);
        $results = array();
        if (!empty($datas)) {
            foreach ($datas as $key => $data) {
                $_time = date('m/y', $key);
                $results[$_time] = $data;
            }
        }
        return $results;
    }

    /**
     * Ham tinh toan lai remain
     * Neu nhung thang la qua khu thi remain = 0
     * Remain nhung thang qua khu se duoc chia dieu cho thang hien tai va cac thang trong tuong lai
     * Ham cui bap - co thoi gian se viet lai
     */
    private function _resetRemainSystem($datas = array()) {
        $currentDate = strtotime(date('01-m-Y', time()));
        $totalEstimated = $_total = $monthValidates = array();
        foreach ($datas as $id => $data) {
            $works = $cons = $remains = 0;
            foreach ($data as $time => $value) {
                if ($time < $currentDate) {
                    $works += $value['validated'];
                    $cons += $value['consumed'];
                    $remains += $value['remains'];
                    $_total[$id]['validated'] = $works;
                    $_total[$id]['consumed'] = $cons;
                    $_total[$id]['remain'] = $remains;
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    if (!empty($value['validated'])) {
                        $monthValidates[$id][] = $time;
                    }
                }
            }
        }
        if (!empty($monthValidates)) {
            foreach ($monthValidates as $id => $monthValidate) {
                if (in_array($currentDate, $monthValidate)) {
                    //do nothing
                } else {
                    $monthValidates[$id][] = $currentDate;
                }
            }
        }
        foreach ($datas as $id => $data) {
            foreach ($data as $time => $value) {
                if ($time < $currentDate) {
                    $datas[$id][$time]['remains'] = 0;
                } else {
                    $count = !empty($monthValidates[$id]) ? count($monthValidates[$id]) : 1;
                    $remain = !empty($_total[$id]) ? $_total[$id]['remain'] : 0;
                    if ($count == 0) {
                        $remainFirst = 0;
                        $remainSecond = 0;
                    } else {
                        $getRemain = $this->Lib->caculateGlobal($remain, $count, false);
                        $remainFirst = $getRemain['original'];
                        $remainSecond = $getRemain['remainder'];
                    }
                    if (!empty($value['validated']) || $time == $currentDate) {
                        $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst;
                        if (!empty($monthValidates[$id]) && $time == max($monthValidates[$id])) {
                            $datas[$id][$time]['remains'] = $value['remains'] + $remainFirst + $remainSecond;
                        }
                    } else {
                        if (!empty($datas[$id][$currentDate])) {
                            if ($datas[$id][$currentDate]['validated'] && $datas[$id][$currentDate]['validated'] == 0) {
                                $datas[$id][$currentDate]['remains'] = $remainFirst + $remainSecond;
                            }
                        }
                    }
                }
            }
        }
        return $datas;
    }

    protected function _getPathLivrables($project_id) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'livrable' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    function index_plus_bak($cate = 1, $projectL = '') {
        $this->loadModels('Menu', 'ProjectGlobalView', 'ProjectAmr', 'LogSystem', 'CompanyEmployeeReference');
        $company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
        $cate = ($cate != 'undefined') ? $cate : 2;
        $_type = $cate == 5 ? array(1, 2) : ($cate == 6 ? 2 : $cate);
        /**
         * Lay config see all project from admin
         */
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
        if (!$adminSeeAllProjects) {
            $this->loadModels('ProjectEmployeeManager');
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employee_id,
                        'is_profit_center' => 0
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'project_manager_id' => $employee_id
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        // if(!empty($listProjectIds)){
        //     $options['conditions'][] = array('Project.id' => $listProjectIds);
        // }
        // if($viewProjects == false){
        //     $projects = array();
        // } else {
        //     $projects = $this->Project->find('all', $options);
        // }
        $cond = array(
            'company_id' => $company_id,
            'category' => $_type
        );
        $order = '';
        if (!empty($projectL)) {
            $cond['id'] = explode('-', $projectL);
        } else {
            if (!empty($listProjectIds)) {
                $cond['id'] = $listProjectIds;
            }
            // $cond['id'] = !empty($listProjectIds) ? $listProjectIds : array();
        }
        // order theo id o project list index
        if (!empty($cond['id']) && count($cond['id']) > 1) {
            $order = "FIELD(id," . implode(", ", $cond['id']) . ")";
        }
        $_listProjects = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => $cond,
            'order' => $order,
        ));
        $i = 0;
        $listProjects = array();
        $globals = array();
        $listProjectIds = !empty($_listProjects) ? Set::combine($_listProjects, '{n}.Project.id', '{n}.Project.id') : array();
        foreach ($_listProjects as $key => $_listProject) {
            if ($key % 4 == 0 && $key != 0) {
                $i++;
            }
            $listProjects[$i][] = $_listProject;
            // tinh global.
            $project_id = $_listProject['Project']['id'];
            $projectGlobalView = $this->ProjectGlobalView->find("first", array(
                'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
                "conditions" => array('project_id' => $project_id)));

            if ($projectGlobalView) {
                $noFileExists = false;
                $link = trim($this->_getPathGlobal($project_id, 'global')
                        . $projectGlobalView['ProjectGlobalView']['attachment']);
                if (!file_exists($link) || !is_file($link)) {
                    $noFileExists = true;
                }
                $globals[$project_id] = array(
                    'global' => $projectGlobalView,
                    'file' => $noFileExists
                );
            }
        }
        $listWeather = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $listProjectIds
            ),
            'fields' => array('project_id', 'weather', 'rank')
        ));
        $listRank = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.rank') : array();
        $listWeather = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.weather') : array();
        $screenDefaults = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
        ));
        $ACLController = 'projects';
        $ACLAction = 'edit';
        if (!empty($screenDefaults)) {
            $ACLController = $screenDefaults['Menu']['controllers'];
            $ACLAction = $screenDefaults['Menu']['functions'];
        }
        $this->loadModel('HistoryFilter');
        if (!empty($projectL) && in_array($cate, array(1, 2, 3, 4, 5))) {
            $_url = $cate . '/' . $projectL;
            $url_rollback = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url'
                )
            ));
            if (!empty($url_rollback)) {
                $this->HistoryFilter->id = $url_rollback['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $_url));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url',
                    'params' => $_url
                ));
            }
        }
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'path' => 'Project_grid/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        //log system for done & todo
        if (!empty($listProjectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $listProjectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        $this->set(compact('logs', 'listProjects', 'ACLController', 'ACLAction', 'globals', 'listRank', 'listWeather', 'savePosition'));
    }
	
	/* 
	* BEGIN parseInputFilter
	* param $cate, $projectL
	* return array(
			'cate' => 
			'prog_id' => 
			'filter' => 
			
		)
	* Ticket 401
	*/
	private function parseInputFilter($cate){
		$company_id = $this->employee_info['Company']['id'];
        $employee_id = $this->employee_info['Employee']['id'];
		if( empty($cate) ) $cate = '';
		$prog = array();
		$filter = array();
		$save_historyfilter = 1;
		// Get HistoryFilter. Use for get data & save data
		$list_prog_path = 'project_grid_prog_filter';
		$last_prog = $this->Employee->HistoryFilter->find('first', array(
				'recursive' => -1,
				'fields' => array(
					'id', 'params'
				),
				'conditions' => array(
					'path' => $list_prog_path,
					'employee_id' => $employee_id
				)
			));
		// Get data from FORM 
		if( !empty( $this->data['Project']['cate'])){ 
			$cate = $this->data['Project']['cate'];
			$prog = !empty( $this->data['Project']['typeProject']) ? $this->data['Project']['typeProject'] : array();
		}
		// Get data from URL
		elseif( !empty($cate) ){
			// Url with list Prograam ID: p-234-345-346
			if( preg_match('/^p-/', $cate)){
				$prog = explode('-', str_replace('p-', '', $cate));
				$cate = '';
				// Filter from list screen. No save
				$save_historyfilter = 0;
			}
			// Url with list filter: filter-weather_sun-pm_2378-avancement_ADV-pname_PROJECT
			elseif( preg_match('/^filter-/', $cate)){
				$filter = explode('-',str_replace('filter-', '', $cate));
				// Advance Filter from grid screen. No save
				$save_historyfilter = 0;
				
			}else{
				$cate = intval($cate);
			}
		}
		//get From History filter
		elseif (!empty($last_prog)) {
			$history_prog_id = unserialize($last_prog['HistoryFilter']['params']);
			$cate = !empty($history_prog_id['cate']) ? $history_prog_id['cate'] : '1';
			$prog = !empty($history_prog_id['list_prog_id']) ? $history_prog_id['list_prog_id'] : array();
			// nothing changed. No save
			$save_historyfilter = 0;
			
		}
		if(!empty($this->data['Project']['project_program'])){
			$prog = array();
			foreach($this->data['Project']['project_program'] as $key => $value){
				if($value != 0){
					$prog[] = $value;
				}
			}
			$save_historyfilter = 0;
		}
		// END get data 
		
		// Save data 
		if( $save_historyfilter){
			if (empty($last_prog)){
				$this->Employee->HistoryFilter->create();
			}else{
				 $this->Employee->HistoryFilter->id = $last_prog['HistoryFilter']['id'];
			}
			$this->Employee->HistoryFilter->save( 
				array(
					'path' => $list_prog_path,
					'params' => serialize(array(
						'list_prog_id' => array_values($prog),
						'cate' => $cate
						)),
					'employee_id' => $employee_id
				),
				array(
					'validate' => false,
					'callbacks' => false
				)
			);
		}
		// Saved data 
		
		$result = array( $cate, $prog, $filter );
		return $result;
		
	}
	/* END parseInputFilter */
	/* Function listProjectByPM(PM_ID)
	* @param employeeID
	* return array project ID
	*/
	private function listProjectByPMID($e_id, $type='PM'){
		$projects1 = $this->Project->find('list', array(
			'recursive' => -1,
            'conditions' => array('project_manager_id' => $e_id),
            'fields' => array('id')
		));
		$projects2 = $this->Project->find('list', array(
			'recursive' => -1,
            'conditions' => array(				
				'ProjectEmployeeManager.project_manager_id' => $e_id,
				'ProjectEmployeeManager.type' => $type
			),
			'joins' => array( 
				array(
					'table' => 'project_employee_managers',
					'alias' => 'ProjectEmployeeManager',
					'conditions' => array(
						'Project.id = ProjectEmployeeManager.project_id',
					),
				),
			),
            'fields' => array('Project.id')
		));
		$listProjects = array_unique( array_merge( array_values($projects1), array_values($projects2)));

		return $listProjects;
	}
	
    function index_plus($cate = '', $projectL = '') {
		
        $this->loadModels('Menu', 'ProjectGlobalView', 'ProjectAmr', 'LogSystem', 'CompanyEmployeeReference', 'ProjectAmrProgram', 'Employee', 'HistoryFilter', 'ProjectEmployeeManager');
        $company_id = $this->employee_info['Company']['id'];
		if( $company_id) $this->_getListEmployee($company_id);
        $employee_id = $this->employee_info['Employee']['id'];
		$cond = array('Project.company_id' => $company_id);
		$join_cond = $join_avancement = array();
		list($cate, $prog, $filter) = $this->parseInputFilter($cate);
		if (!empty($filter)) {
            foreach ($filter as $value) {
                $value = explode('_', $value);
                if (!empty($value) && $value[0] == 'cate')
					$cate = intval($value[1]);
            }
        }
		$dataFilter = array();
		if(!empty($this->data)){
			$dataFilter = $this->data;
			$dx = $this->data['Project'];			
			if(!empty($dx['project_program'])){
				foreach($dx['project_program'] as $key => $value){
					if($value != 0) $cond['Project.project_amr_program_id'][] = $value;
				}
			}
			if(!empty($dx['weather'])){
				foreach($dx['weather'] as $key => $value){
					if($value != '0'){
						$join_cond['ProjectAmr.weather'][] = $value;
					}
				}
			}
			if(!empty($dx['avancement'])){
				$join_avancement['LogSystem.description LIKE'] = '%' . $dx['avancement'] . '%';
			}
			
			if(!empty($dx['project_manager_id'])){
				$pmList = array();
				foreach($dx['project_manager_id'] as $key => $value){
					if($value != '0'){
						$pmList[] = $value;
					}
				}
				if(!empty($pmList)) $cond['Project.id'] = $this->listProjectByPMID($dx['project_manager_id']);
			}
			if(!empty($dx['project_name'])){
                $cond['Project.project_name LIKE'] = '%' . $dx['project_name'] . '%';
			}
		}
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('loadFilter'));
		
        $_type = $cate == 5 ? array(1, 2) : ($cate == 6 ? 2 : $cate);
		$cond['Project.category'] = $_type;
        /**
         * Lay config see all project from admin
         */
        $appstatus = $cate;
        $_personDefault = $this->Session->read("App.PersonalizedDefault");
        $personDefault = $_personDefault ? true : false;
        $adminSeeAllProjects = isset($this->companyConfigs['see_all_projects']) && !empty($this->companyConfigs['see_all_projects']) ? true : false;
        $role = $this->employee_info['Role']['name'];
        $profitCenterId = $this->employee_info['Employee']['profit_center_id'];
        $getSee = $this->CompanyEmployeeReference->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee_id),
            'fields' => array('see_all_projects')
        ));
        $seeAllOfEmploy = !empty($getSee) && !empty($getSee['CompanyEmployeeReference']['see_all_projects']) ? $getSee['CompanyEmployeeReference']['see_all_projects'] : 0;
        $listProjectIds = array();
        $viewProjects = true;
		$listProjects = array();
        if (!$adminSeeAllProjects) {
			//them dieu kien check de hien thi project doi voi user co quyen Read Access project do. Update by QuanNV 29/06/2019
            if ($role == 'pm' && !$seeAllOfEmploy) {
                $listProjectIdOfEmploys = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $employee_id,
						'or' => array (
							'is_profit_center' => 0,
							'is_profit_center is NULL'
						)
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectOfEmployManager = $this->Project->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'project_manager_id' => $employee_id
                    ),
                    'fields' => array('id', 'id')
                ));
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = array_merge($listProjectIdOfEmploys, $listProjectIdOfPcs, $listProjectOfEmployManager);
                if (!empty($listProjectIds)) {
                    //see one or see multiple
                } else {
                    $viewProjects = false;
                }
            }
            if ($role == 'conslt') {
                $listProjectIdOfPcs = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_manager_id' => $profitCenterId,
                        'is_profit_center' => 1
                    ),
                    'fields' => array('project_id', 'project_id')
                ));
                $listProjectIds = $listProjectIdOfPcs;
            }
        }
        
        if (!empty($prog)) {
            $cond['Project.project_amr_program_id'] = array_values($prog);
        }
        $order = '';
        if (!empty($projectL)) {
            $cond['Project.id'] = explode('-', $projectL);
			if (!empty($listProjectIds)) {
                $cond['Project.id'] = array_intersect($listProjectIds, $cond['Project.id']);
            }
        } else {
            if (!empty($listProjectIds)) {
                $cond['Project.id'] = $listProjectIds;
            }
        }
        // order theo id o project list index
        if (!empty($cond['Project.id']) && count($cond['Project.id']) > 1) {
            $order = "FIELD(id," . implode(", ", $cond['Project.id']) . ")";
        }
        extract(array_merge(array('cate' => $cate), $this->params['url']));

		$options = array(
			'recursive' => -1,
			'conditions' =>  $cond,
			'joins' => array(),
			'fields' => array(
				
			),
		);
		if( !empty($join_cond['ProjectAmr.weather'])){
			$options['joins'][] = array(
				'table' => 'project_amrs',
				'alias' => 'ProjectAmr',
				'conditions' => array(
					'Project.id = ProjectAmr.project_id',
					$join_cond,
				),
			);
		}
		if( !empty($join_avancement)){
			$options['joins'][] = array(
				'table' => 'log_systems',
				'alias' => 'LogSystem',
				'conditions' => array(
					'Project.id = LogSystem.model_id',
					'LogSystem.model = "ProjectAmr"',
					$join_avancement,
				),
				'order' => array('LogSystem.updated' => 'DESC'),
			);
		}

		$_listProjects = $this->Project->find('all', $options);
	
		$listPM = !empty($_listProjects) ? Set::combine($_listProjects, '{n}.Project.id', '{n}.Project.project_manager_id') : array();
        $_listProjects = !empty($_listProjects) ? Set::combine($_listProjects, '{n}.Project.id', '{n}.Project') : array();
        $globals = array();
		
		if(!$adminSeeAllProjects && !$seeAllOfEmploy ){
			if(($role != 'admin') && empty($listProjectIds)){
				$listProjects = $this->Project->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'Project.id' => $listProjectIds
					)
				));
				$listProjects = !empty($listProjects) ? Set::combine($listProjects, '{n}.Project.id', '{n}.Project') : array();
			} else{
				$listProjects = $_listProjects;
			}
		} else {
			$listProjects = $_listProjects;
		}	
		list($listProjects, $userCustomOrders) = $this->customOrderProjects($listProjects);
		$listProjectIds = !empty($listProjects) ? array_keys($listProjects) : array();
        $listProgram = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        'Project.id' => $listProjectIds,
                    )
                )
            ),
            'fields' => array('Project.id', 'ProjectAmrProgram.amr_program')
        ));
		//update ticket 884
		$listProgramColor = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        'Project.id' => $listProjectIds,
                    )
                )
            ),
            'fields' => array('Project.id', 'ProjectAmrProgram.color')
        ));
		//end update ticket 884
        $listProgramFields = $this->ProjectAmrProgram->find('list', array(
            'joins' => array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'conditions' => array(
                        'ProjectAmrProgram.id = Project.project_amr_program_id',
                        // 'Project.id' => $listProjectIds,
                        'Project.company_id' => $company_id,
                    )
                )
            ),
            'fields' => array('Project.project_amr_program_id', 'ProjectAmrProgram.amr_program')
        ));
		if(!empty($listProgramFields)){
			asort($listProgramFields);
		}
		$listPM2 = $this->ProjectEmployeeManager->find('all', array(
			'recursive' => -1,
            'conditions' => array(
				'type' => 'PM',
				'project_id' => array_values($listProjectIds)
			),
			'fields' => array('project_id', 'project_manager_id'),
		));
		
		$listPMFields = array_values($this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'id' => !empty($listProjectIds) ? $listProjectIds : '*',
			),
			'fields' => array('project_manager_id'),
		)));
		$pm = array();
		foreach( $listPM2 as $_pm){
			$_pm = $_pm['ProjectEmployeeManager'];
			$p_id = $_pm['project_id'];
			if( empty($pm[$p_id ])) $pm[$p_id ] = array();
			$pm[$p_id][] =  $_pm['project_manager_id'];
			$listPMFields[] = $_pm['project_manager_id'];
		}
		
		$listProjectManager = array();		
		foreach( $listPM as $p_id => $pm_id){
			if( empty($pm[$p_id])) $pm[$p_id] = array();
			$listProjectManager[$p_id] = array_values( array_filter( array_unique(array_merge( array($pm_id), $pm[$p_id]))) );
		}
		unset( $pm, $listPM, $listPM2);
		$listPMFields =  array_values( array_filter( array_unique( $listPMFields )) ); 
		$i = 0;
        foreach ($listProjects as $key => $_listProject) {
            if ($key % 4 == 0 && $key != 0) {
                $i++;
            }
            // tinh global.
            $project_id = $_listProject['id'];
            $projectGlobalView = $this->ProjectGlobalView->find("first", array(
                'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
                "conditions" => array('project_id' => $project_id)));

            if ($projectGlobalView) {
                $noFileExists = false;
                $link = trim($this->_getPathGlobal($project_id, 'global')
                        . $projectGlobalView['ProjectGlobalView']['attachment']);
                if (!file_exists($link) || !is_file($link)) {
                    $noFileExists = true;
                }
                $globals[$project_id] = array(
                    'global' => $projectGlobalView,
                    'file' => $noFileExists
                );
            }
        }
        $listWeather = $this->ProjectAmr->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $listProjectIds
            ),
            'fields' => array('project_id', 'weather', 'rank')
        ));
        $projectGloView = $this->ProjectGlobalView->find("all", array(
            'recursive' => -1, 'fields' => array('id', 'project_id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $listProjectIds)));
        $projectGloView = !empty($projectGloView) ? Set::combine($projectGloView, '{n}.ProjectGlobalView.project_id', '{n}.ProjectGlobalView') : array();
        $listRank = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.rank') : array();
        $listWeather = !empty($listWeather) ? Set::combine($listWeather, '{n}.ProjectAmr.project_id', '{n}.ProjectAmr.weather') : array();

        $screenDefaults = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array('model' => 'project', 'company_id' => $company_id, 'default_screen' => 1)
        ));
        $screenDashboard = '';
        $screenDashboard = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'project',
                'company_id' => $company_id,
                'functions' => array('indicator', 'your_form_plus'),
                'display' => '1',
            ),
            'fields' => array('name_eng', 'name_fre', 'controllers', 'functions'),
        ));
		
		$screenDashboard = !empty($screenDashboard) ? Set::combine($screenDashboard, '{n}.Menu.functions', '{n}.Menu') : array();
        $ACLController = 'projects';
        $ACLAction = 'edit';
        if (!empty($screenDefaults)) {
            $ACLController = $screenDefaults['Menu']['controllers'];
            $ACLAction = $screenDefaults['Menu']['functions'];
        }
        $this->loadModel('HistoryFilter');
        if (!empty($projectL) && in_array($cate, array(1, 2, 3, 4, 5))) {
            $_url = $cate . '/' . $projectL;
            $url_rollback = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url'
                )
            ));
            if (!empty($url_rollback)) {
                $this->HistoryFilter->id = $url_rollback['HistoryFilter']['id'];
                $this->HistoryFilter->save(array('params' => $_url));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'employee_id' => $employee_id,
                    'path' => 'project_grid_url',
                    'params' => $_url
                ));
            }
        }
        $savePosition = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $employee_id,
                'path' => 'Project_grid/popup_position'
            )
        ));
        $savePosition = !empty($savePosition) ? $savePosition['HistoryFilter']['params'] : 'undefined';
        //log system for done & todo
        if (!empty($listProjectIds)) {
            $logs = $this->LogSystem->query("SELECT * FROM log_systems LogSystem where model_id in (" . implode(',', $listProjectIds) . ") and (model = 'Done' or model = 'ToDo' or model = 'ProjectAmr') order by id asc");
            $logs = Set::combine($logs, '{n}.LogSystem.model', '{n}.LogSystem', '{n}.LogSystem.model_id');
        } else {
            $logs = array();
        }
        $profileName = array();
        $employInfors = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $this->employee_info['Employee']['id']),
            'fields' => array('create_a_project', 'delete_a_project', 'update_your_form', 'profile_account')
        ));
        if (!empty($employInfors['Employee']['profile_account'])) {
			$this->loadModel('ProfileProjectManager', 'ProfileProjectManagerDetail');
            $profileName = $this->ProfileProjectManager->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $employInfors['Employee']['profile_account']
                )
            ));
			$profileCanUpdateYourform = $this->ProfileProjectManagerDetail->find('count', array(
                'recursive' => -1,
				'conditions' => array(
					'widget_id' => 'your_form',
					'model_id' => $employInfors['Employee']['profile_account'],
					'read_write' => 1
				),
            ));
			$this->set('profileCanUpdateYourform', $profileCanUpdateYourform);
        }
        $projectProgress = array();
        if (!empty($listProjectIds)) {
            // $projectProgress = $this->getTaskEuro_new($listProjectIds);
            $projectProgress = $this->getProjectProgress($listProjectIds);
        }
		$project_likes = $this->getLikebyProjects($listProjectIds);
		$favorites = $this->getFavoritebyEmployee($this->employee_info['Employee']['id'], $listProjectIds);
		$listIdModifyByPm = array();
		$roleLogin = $this->employee_info['Role']['name'];
		if ($roleLogin == 'pm') {
			$list_project_my_manager = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
					'project_manager_id' => $employee_id,
				),
				'fields' => array('Project.id', 'Project.id')
			));
			// Ticket #427. Cho phep hien thi project co user la PM, Technical manager, UAT manager, Chief Business, Functional leader
			$list_project_my_manager_bakcup = $this->ProjectEmployeeManager->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectEmployeeManager.project_manager_id' => $employee_id,
					'ProjectEmployeeManager.is_profit_center' => 0,
					'ProjectEmployeeManager.type !=' => 'RA'
				),
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array(
							'Project.id = ProjectEmployeeManager.project_id',
						)
					)
				),
				'fields' => array('ProjectEmployeeManager.project_id', 'ProjectEmployeeManager.project_id')
			));
			$list_project_my_manager = !empty( $list_project_my_manager) ? $list_project_my_manager : array();
			$list_project_my_manager_bakcup = !empty( $list_project_my_manager_bakcup) ? $list_project_my_manager_bakcup : array();
			$list_project_my_manager = array_merge($list_project_my_manager, $list_project_my_manager_bakcup);
			$listIdModifyByPm = array_unique($list_project_my_manager);
		}
	
		$is_task_newdesign = $this->Menu->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'model' => 'project',
				'controllers' => 'project_tasks'
			),
			'fields' => array('enable_newdesign')
		));
		$is_task_newdesign = !empty($is_task_newdesign) ? $is_task_newdesign['Menu']['enable_newdesign'] : 0;
        $this->set(compact('profileName', 'screenDashboard', 'userCustomOrders'));
        $this->set(compact('projectGloView', 'personDefault', 'appstatus', 'cate', 'logs', 'listProjects', 'ACLController', 'ACLAction', 'globals', 'listRank', 'listWeather', 'savePosition', 'listProgram', 'listProgramFields', 'listProgramColor', 'listProjectManager', 'listPMFields', 'prog', 'projectProgress', 'project_likes', 'favorites', 'listIdModifyByPm', 'is_task_newdesign', 'dataFilter'));
		
		/**
		* QuanNV . Update ticket 404.
		*/
		$this->loadModels ('Translation','TranslationSetting');
		$adminTaskSetting = $this->Translation->find('list', array(
			'conditions' => array(
				'page' => 'Project_Task',
				'TranslationSetting.company_id' => $company_id
			),
			'recursive' => -1,
			'fields' => array('original_text', 'TranslationSetting.show'),
			'joins' => array(
				array(
					'table' => 'translation_settings',
					'alias' => 'TranslationSetting',
					'conditions' => array(
						'Translation.id = TranslationSetting.translation_id',
					),
					'type' => 'left'
				)
			),
			'order' => array('TranslationSetting.setting_order' => 'ASC')
		));
		
		/*
		* End update ticket 404.
		*/
        $this->set(compact('adminTaskSetting'));
    }
	private function customOrderProjects($listProjects = array(), $type = null){
		$sorted = $custom_order_projects = array();
		if( empty( $listProjects)) return array(
			$sorted,
			$custom_order_projects
		);
		$this->loadModels('HistoryFilter', 'LogSystem');
		$projectIDs = Set::combine($listProjects, '{n}.id', '{n}.id');
		$listProjects = array_values($listProjects);
		$history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'custom_order_projects',
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if (!empty($history)) {
            $history = json_decode($history['HistoryFilter']['params'], true);
			$history = is_array($history) ? $history : array();
        } else {
            $history = array();
        }
		$custom_order_projects = array_unique( array_merge( $history, $projectIDs));
		if( empty( $type) || $type == 'history'){
			$type = isset($this->filter_render['project_grid_order']) ? $this->filter_render['project_grid_order'] : '';
		}
		switch($type){
			case 'custom_order':
				$listProjects = Set::combine($listProjects, '{n}.id', '{n}');
				foreach( $custom_order_projects as $k => $project_id){
					if( isset( $listProjects[$project_id] ) ){
						$sorted[$project_id] = $listProjects[$project_id];
					}
				}
			break;
			case 'newest_comment':
				$logs = $this->LogSystem->find('all', array(
					'recusive' => -1,
					'conditions' => array(
						'LogSystem.model' => 'ProjectAmr',
						'LogSystem.model_id' => $projectIDs
					),
					'group' => array('LogSystem.model_id'),
					'fields' => array(
						'LogSystem.model_id',
						"MAX(updated) as last_updated_comment",
					)
					
				));
				$logs = !empty($logs) ? Set::combine($logs, '{n}.LogSystem.model_id', '{n}.0.last_updated_comment') : array();
				$last_updated_comment = array();
				foreach( $listProjects as $k => $project){
					$project_id = $project['id'];
					$last_updated_comment[] =  empty($logs[$project_id]) ? 0 :  $logs[$project_id];
					$listProjects[$k]['last_updated_comment'] = $last_updated_comment[$k];
				}
				$sorted = array_multisort($last_updated_comment, SORT_DESC, SORT_NUMERIC, $listProjects);
				$sorted = $listProjects;
			break;
			case 'alphabet_program':
				$this->loadModels('ProjectAmrProgram');
				$company_programs = $this->ProjectAmrProgram->find('list', array(
					'recusive' => -1,
					'conditions' => array(
						'ProjectAmrProgram.company_id' => $this->employee_info['Company']['id'],
					),
					'fields' => array(
						'id',
						'amr_program',
					)
					
				));
				$programs  = array_column($listProjects, 'project_amr_program_id');
				$name = array_column($listProjects, 'project_name');
				foreach( $programs as $k => $program_id ){
					$programs[$k] = isset($company_programs[$program_id]) ? $company_programs[$program_id] : 0;
				}
				$sorted = array_multisort($programs, SORT_ASC, SORT_STRING, $name, SORT_ASC, SORT_STRING, $listProjects);
				$sorted = $listProjects;
			break;
			case 'alphabet_project':
			default:
				$name = array_column($listProjects, 'project_name');
				$sorted = array_multisort($name, SORT_ASC, SORT_STRING, $listProjects);
				$sorted = $listProjects;
				break;
		}
		$sorted = !empty($sorted) ? Set::combine($sorted, '{n}.id', '{n}') : array();
		return array(
			$sorted,
			$custom_order_projects
		);
	}
    function ajax($project_id = null, $view = 'ajax') {
        $this->loadModels('BudgetSetting', 'ProjectRisk', 'ProjectRiskOccurrence', 'ProjectRiskSeverity', 'ProjectIssueStatus', 'ProjectIssueSeverity', 'ProjectIssue', 'ProjectLocalView', 'Dependency', 'ProjectDependency', 'ProjectGlobalView', 'ProjectIssueColor');
        $company_id = $this->employee_info['Company']['id'];
        $budget_settingst = $this->BudgetSetting->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'currency_budget' => 1,
            ),
            'fields' => array('name',)
        ));

        $this->loadModels('Menu', 'YourFormFilter', 'Profile', 'ProjectPhaseStatus', 'LogSystem', 'ProjectAcceptance', 'ProjectAcceptanceType', 'ProjectAmr', 'ProjectEmployeeManager');
        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project'
            ),
            'fields' => array('widget_id', 'display', 'name_eng', 'name_fre')
        ));

        $langCode = Configure::read('Config.langCode');
        $_fields = ($langCode == 'fr') ? '{n}.Menu.name_fre' : '{n}.Menu.name_eng';
        $tranMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', $_fields) : array();
        $showMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', '{n}.Menu.display') : array();
        if (!isset($showMenu['finance_two_plus'])) {
            $showMenu['finance_two_plus'] = 0;
        }
        // filter your form.
        $this->autoInsertFilter();
        $yourFormFilter = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id']
            ),
            'fields' => array('widget', 'display'),
            'order' => array('weight')
        ));
        $id = $project_id;
        $budget_settings = !empty($budget_settingst['BudgetSetting']['name']) ? $budget_settingst['BudgetSetting']['name'] : '&euro;';
        $page = 'Details';
        $curentPage = 'your_form_plus';
        if ($yourFormFilter['your_form'] == 1) {
            $this->getDataForYourForm($page, $curentPage, $id);
        } else {
            $project_name = $project_name = $this->Project->find('first', array('recursive' => -1, 'fields' => array('Project.id', 'Project.project_name', 'Project.updated', 'Project.update_by_employee'), 'conditions' => array('Project.id' => $id)));
            $this->set('project_name', $project_name);
        }
        $ProjectArms = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
        $projectRisks = $this->ProjectRisk->find("all", array(
            'fields' => array('id', 'project_risk', 'risk_assign_to', 'project_risk_severity_id', 'project_risk_occurrence_id', 'risk_close_date', 'actions_manage_risk', 'file_attachement', 'format', 'project_issue_status_id', 'weight'),
            'recursive' => -1, "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $riskSeverities = $this->ProjectRiskSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $riskOccurrences = $this->ProjectRiskOccurrence->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'risk_occurrence'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueStatus = $this->ProjectIssueStatus->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_status'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $issueSeverities = $this->ProjectIssueSeverity->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_severity'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $projectIssues = $this->ProjectIssue->find("all", array(
            'recursive' => -1,
            "conditions" => array('project_id' => $id),
            'order' => array('weight' => 'ASC')
        ));
        $listProjectIssueId = !empty($projectIssues) ? Set::combine($projectIssues, '{n}.ProjectIssue.id', '{n}.ProjectIssue.id') : array();
        // project issue employee reference.
        $this->loadModels('Employee', 'ProfitCenter', 'ProjectIssueEmployeeRefer');
        $listEmployeeAssign = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'company_id IS NULL',
                )
            ),
            'fields' => array('id', 'fullname')
        ));
        $this->ProfitCenter->virtualFields['pcName'] = 'CONCAT("P/C ", `name`)';
        $listProfitAssign = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'pcName')
        ));
        $projectIssueEmployeeRefer = $this->ProjectIssueEmployeeRefer->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_issue_id' => $listProjectIssueId
            ),
            'fields' => array('id', 'project_issue_id', 'reference_id', 'is_profit_center')
        ));
        $listReference = array();
        foreach ($projectIssueEmployeeRefer as $_projectIssueEmployeeRefer) {
            $dx = $_projectIssueEmployeeRefer['ProjectIssueEmployeeRefer'];
            if ($dx['is_profit_center'] == 0) {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listEmployeeAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listEmployeeAssign[$dx['reference_id']];
                }
            } else {
                if (!empty($listReference[$dx['project_issue_id']])) {
                    $listReference[$dx['project_issue_id']] .= ', ' . $listProfitAssign[$dx['reference_id']];
                } else {
                    $listReference[$dx['project_issue_id']] = $listProfitAssign[$dx['reference_id']];
                }
            }
        }
        $projectLocalView = $this->ProjectLocalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)
        ));
        if ($projectLocalView) {
            $link = trim($this->_getPathGlobal($id)
                    . $projectLocalView['ProjectLocalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExists = true;
            }
        }
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)
        ));
        //dependency
        $deps = $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'name', 'color')
        ));
        $dependencies = $colors = array();
        foreach ($deps as $dep) {
            $dependencies[$dep['Dependency']['id']] = $dep['Dependency']['name'];
            $colors[$dep['Dependency']['id']] = $dep['Dependency']['color'];
        }
        $listProjects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'category' => array(1, 2),
                'id !=' => $id
            ),
            'fields' => array('id', 'project_name')
        ));
        $dataDependency = $this->ProjectDependency->retrieve($id);
        $list = !empty($data) ? array_unique(Set::extract($data, '{n}.ProjectDependency.target_id')) : array();
        $count = $this->ProjectDependency->countChildren($list);
        // history.
        $this->loadModels('HistoryFilter', 'ProjectPhase');
        $employee_info = $this->employee_info;
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'dependency_' . $id,
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if (!empty($history)) {
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        // global view
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment', 'is_file', 'is_https'),
            "conditions" => array('project_id' => $id)));

        if ($projectGlobalView) {
            $link = trim($this->_getPathGlobal($id, 'global')
                    . $projectGlobalView['ProjectGlobalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExistsGlobal = true;
            }
        }
        // project budget internal.
        $getDataProjectTasks = $this->Project->dataFromProjectTask($id, $company_id);
        $this->loadModel('ProjectTask');
        $projectTasks = $this->ProjectTask->find('all', array(
            'fields' => array(
                'SUM(ProjectTask.estimated) AS Total',
                'SUM(ProjectTask.special_consumed) AS exConsumed'
            ),
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1)
        ));
        $varE = !empty($projectTasks) && !empty($projectTasks[0][0]['Total']) ? $projectTasks[0][0]['Total'] : 0;
        $externalConsumeds = !empty($projectTasks) && !empty($projectTasks[0][0]['exConsumed']) ? $projectTasks[0][0]['exConsumed'] : 0;
        $getDataProjectTasks['workload'] -= $varE;
        $budgets = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $this->loadModels('ProjectBudgetExternal', 'BudgetProvider', 'BudgetType', 'BudgetFunder', 'ProjectFinancePlusDate', 'ProjectPhasePlan', 'ProjectPhaseStatus', 'ProjectPart');
        $externalBudgets = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id),
            'fields' => array('SUM(man_day) AS exBudget')
        ));
        $externalBudgets = !empty($externalBudgets) && !empty($externalBudgets[0][0]['exBudget']) ? $externalBudgets[0][0]['exBudget'] : 0;
        $engagedErro = 0;
        if (!empty($projectName['Project']['activity_id'])) {
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $emp = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $_id => $val) {
                    $reals = !empty($emp[$_id]['tjm']) ? (float) str_replace(',', '.', $emp[$_id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
        }
        // budget external
        $budgetProviders = $this->BudgetProvider->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $_budgetTypes = $this->BudgetType->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'capex')
        ));
        $budgetTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.name') : array();
        $capexTypes = !empty($_budgetTypes) ? Set::combine($_budgetTypes, '{n}.BudgetType.id', '{n}.BudgetType.capex') : array();
        $budgetExternals = $this->ProjectBudgetExternal->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id)
        ));
        $idOfExternals = !empty($budgetExternals) ? Set::classicExtract($budgetExternals, '{n}.ProjectBudgetExternal.id') : array();
        $profits = $this->ProfitCenter->generateTreeList(array('company_id' => $company_id), null, null, '');
        $funders = $this->BudgetFunder->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $taskExternals = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $id, 'special' => 1, 'external_id' => $idOfExternals),
            'group' => array('external_id'),
            'fields' => array('SUM(special_consumed) as consumed', 'SUM(estimated) as maday', 'external_id')
        ));
        $taskExternals = !empty($taskExternals) ? Set::combine($taskExternals, '{n}.ProjectTask.external_id', '{n}.0') : array();
        // finance+

        if (isset($showMenu['finance_plus']) && $showMenu['finance_plus'] && $yourFormFilter['finance_plus'] == 1) {
            $invStart = $invEnd = $fonStart = $fonEnd = time();
            $getSaveHistory = $this->ProjectFinancePlusDate->find('first', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id)
            ));
            if (!empty($getSaveHistory)) {
                $invStart = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_start']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_start'] : time();
                $invEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['inv_end']) ? $getSaveHistory['ProjectFinancePlusDate']['inv_end'] : time();
                $fonStart = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_start']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_start'] : time();
                $fonEnd = !empty($getSaveHistory['ProjectFinancePlusDate']['fon_end']) ? $getSaveHistory['ProjectFinancePlusDate']['fon_end'] : time();
            }
            /**
             * Lay cac finance+
             */
            $finances = $this->ProjectFinancePlus->find('list', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id')
            ));
            /**
             * Finance detail
             */
            $_financeDetails = $this->ProjectFinancePlusDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $id),
                'fields' => array('project_finance_plus_id', 'model', 'year', 'value', 'type')
            ));
            $dataFinan = $totals = array();
            foreach ($_financeDetails as $key => $value) {
                $dx = $value['ProjectFinancePlusDetail'];
                if ($dx['type'] == 'fon') {
                    $dataFinan['fon'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['fon_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['fon'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['fon'][$dx['model']])) {
                        $totals['fon'][$dx['model']] = 0;
                    }
                    $totals['fon'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                } else {
                    $dataFinan['inv'][$dx['project_finance_plus_id']][$dx['year']][$dx['model']] = !empty($dx['value']) ? $dx['value'] : 0;
                    $dataFinan['inv_year'][$dx['year']] = $dx['year'];
                    if (empty($dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']])) {
                        $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] = 0;
                    }
                    $dataFinan['inv'][$dx['project_finance_plus_id']]['total'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                    if (empty($totals['inv'][$dx['model']])) {
                        $totals['inv'][$dx['model']] = 0;
                    }
                    $totals['inv'][$dx['model']] += !empty($dx['value']) ? $dx['value'] : 0;
                }
            }
        }
        //project phase plan
        $_projectPhasePlans = $this->ProjectPhasePlan->find("all", array(
            'fields' => array('id', 'project_part_id', 'predecessor', 'planed_duration', 'project_planed_phase_id', 'project_phase_status_id',
                'phase_planed_start_date', 'phase_planed_end_date', 'phase_real_start_date', 'phase_real_end_date', 'weight', 'ref1', 'ref2', 'ref3', 'ref4', 'profile_id', 'progress'),
            'contain' => array(
                'ProjectPhase' => array(
                    'id', 'color'
                )),
            'order' => array('weight' => 'asc'),
            'conditions' => array('project_id' => $id)
        ));
        //auto format order
        $count = count($_projectPhasePlans);
        for ($i = 0; $i < $count; $i++) {
            $newWeight = $i + 1;
            if ($_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] != "$newWeight") {
                $_projectPhasePlans[$i]['ProjectPhasePlan']['weight'] = $newWeight;
                $this->ProjectPhasePlan->id = $_projectPhasePlans[$i]['ProjectPhasePlan']['id'];
                $this->ProjectPhasePlan->save(array(
                    'weight' => intval($newWeight)), array('validate' => false, 'callbacks' => false));
            }
        }
        //END
        $_listIDPhasePlans = !empty($_projectPhasePlans) ? Set::classicExtract($_projectPhasePlans, '{n}.ProjectPhasePlan.id') : array();
        $countTaskOfPhases = array();
        if (!empty($_listIDPhasePlans)) {
            $_projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_planed_phase_id' => $_listIDPhasePlans, 'project_id' => $id),
                'fields' => array('id', 'project_planed_phase_id', 'COUNT(project_planed_phase_id) AS Total'),
                'group' => array('project_planed_phase_id')
            ));
            $countTaskOfPhases = !empty($_projectTasks) ? Set::combine($_projectTasks, '{n}.ProjectTask.project_planed_phase_id', '{n}.0.Total') : array();
        }
        $_projectPhases = $this->ProjectPhase->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
            ),
            'fields' => array('id', 'name', 'profile_id', 'activated'),
            'order' => array('phase_order' => 'asc')
        ));
        $x = $projectPhases1 = array();
        foreach ($_projectPhases as $phase) {
            $p = $phase['ProjectPhase'];
            $projectPhases1[$p['id']] = $p['name'];
            if ($p['activated']) {
                $x[$p['id']] = $p['name'];
            }
        }
        $_projectPhases = $x;
        unset($x);
        $_phaseDefaults = !empty($_projectPhases) ? Set::combine($_projectPhases, '{n}.ProjectPhase.id', '{n}.ProjectPhase.profile_id') : array();
        $_projectParts = $this->ProjectPart->find('list', array(
            'conditions' => array(
                'project_id' => $id
        )));
        $_projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'phase_status')
        ));
        $this->loadModels('Profile');
        $_profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        $this->ProjectPhasePlan->Project->Behaviors->attach('Containable');
        $currentPhase = $this->ProjectPhasePlan->Project->find('first', array(
            'fields' => array('Project.id'), 'contain' => array(
                'ProjectPhase' => array('name'),
            ), 'conditions' => array('Project.id' => $id)));
        $this->set('currentPhase', isset($currentPhase['ProjectPhase']['id']) ? (int) $currentPhase['ProjectPhase']['id'] : 0);
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $this->set(compact('_projectPhases', 'projectPhases1', '_projectPhaseStatuses', '_projectPhasePlans', '_projectParts', 'countTaskOfPhases', '_profiles', 'activateProfile', '_phaseDefaults', 'manuallyAchievement'));
        // gantt planning.
        $this->helpers = array_merge($this->helpers, array(
            'GanttVs', 'GanttV2'));
        $mileStone = $this->Project->find("first", array(
            'contain' => array('ProjectMilestone' => array('project_milestone', 'milestone_date', 'validated', 'part_id', 'order' => 'milestone_date ASC')),
            "fields" => array('id', 'project_name', 'start_date', 'end_date', 'planed_end_date'),
            'conditions' => array('Project.id' => $id)
        ));
        $_view = true;
        $listProjectToActivities[$projectName['Project']['id']] = $projectName['Project']['activity_id'];
        $this->_parseNew($listProjectToActivities, $projectName, $_view);
        // project task
        if ($showMenu['task'] && $yourFormFilter['project_task'] == 1) {
            $orders = $this->requestAction('/admin_task/getTaskSettings');
            $i18n = $this->requestAction('/translations/getByLang/Project_Task');
            $projectTaskForTasks = $this->requestAction('/project_tasks/getDataTasks/' . $id);
            $settingP = $this->requestAction('/project_settings/get');
            $checkP = $this->Project->find('first', array(
                'recursive' => -1,
                'conditions' => array('Project.id' => $id),
                'fields' => 'is_freeze'
            ));
        }
        $this->loadModels('ProjectIssueSeverity');
        $listColor = $this->ProjectIssueColor->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 1
            ),
            'fields' => array('id', 'color')
        ));
        $colorDefault = $this->ProjectIssueColor->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'default' => 1
            )
        ));
        $colorDefault = !empty($colorDefault['ProjectIssueColor']['color']) ? $colorDefault['ProjectIssueColor']['color'] : '#004380';
        $colorSeverities = $this->ProjectIssueSeverity->find('list', array(
            'fields' => array('id', 'color'),
            'conditions' => array(
                'company_id' => $company_id
            ),
            'recursive' => -1
        ));
        // end
        $manuallyAchievement = isset($this->companyConfigs['manually_achievement']) && !empty($this->companyConfigs['manually_achievement']) ? true : false;
        $activateProfile = isset($this->companyConfigs['activate_profile']) && !empty($this->companyConfigs['activate_profile']) ? true : false;
        $profiles = $this->Profile->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $projectName['Project']['company_id']),
            'fields' => array('id', 'name')
        ));
        $projectPhaseStatuses = $this->ProjectPhaseStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $projectName['Project']['company_id']
            ),
            'fields' => array('id', 'phase_status')
        ));
        $commentKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ProjectAmr',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $todoKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'ToDo',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $doneKpi = $this->LogSystem->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'Done',
                'model_id' => $projectName['Project']['id'],
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'name', 'id', 'created', 'description'),
            'order' => array('updated' => 'DESC')
        ));
        $acceptancesKpi = $this->ProjectAcceptance->find('all', array(
            'fields' => '*',
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $typeAcceptance = $this->ProjectAcceptanceType->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $budgetKpi = $this->ProjectAmr->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            )
        ));
        $menuBudgets = array('project_budget_internals', 'project_budget_externals', 'project_budget_sales', 'project_budget_synthesis', 'project_budget_provisionals', 'project_budget_fiscals');
        $settingMenusKpi = $this->Menu->find('list', array(
            'recursive' => -1,
            'conditions' => array('controllers' => $menuBudgets, 'company_id' => $company_id, 'model' => 'project'),
            'fields' => array('controllers', 'display')
        ));
        // Internal costs
        $internals = $internalDetails = array();
        if (!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_internals'])) {
            $remainExter = $varE - $externalConsumeds;
            $remainInter = !empty($getDataProjectTasks['remain']) ? $getDataProjectTasks['remain'] - $remainExter : 0;
            $projectBudgetInternals = $this->ProjectBudgetInternalDetail->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectName['Project']['id']),
                'fields' => array('id', 'name', 'validation_date', 'budget_md', 'average')
            ));
            $engagedErro = 0;
            $activityId = $projectName['Project']['activity_id'];
            $getDataActivities = $this->_parse($activityId);
            $sumEmployees = $getDataActivities['sumEmployees'];
            $employees = $getDataActivities['employees'];

            if (isset($sumEmployees[$activityId])) {
                foreach ($sumEmployees[$activityId] as $_id => $val) {
                    $reals = !empty($employees[$_id]['tjm']) ? (float) str_replace(',', '.', $employees[$_id]['tjm']) : 1;
                    $engagedErro += $val * $reals;
                }
            }
            $count = $budgetEuro = $budgetManDay = $average = 0;
            if (!empty($projectBudgetInternals)) {
                foreach ($projectBudgetInternals as $key => $projectBudgetInternal) {
                    $internalDetails[$key]['name'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['name'];
                    $internalDetails[$key]['validation_date'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['validation_date'];
                    if (empty($projectBudgetInternal['ProjectBudgetInternalDetail']['average'])) {
                        $projectBudgetInternal['ProjectBudgetInternalDetail']['average'] = 0;
                    }
                    $average += $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                    $internalDetails[$key]['budget_euro'] = $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average'];
                    $budgetEuro += ($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] * $projectBudgetInternal['ProjectBudgetInternalDetail']['average']);
                    $budgetManDay += !empty($projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md']) ? $projectBudgetInternal['ProjectBudgetInternalDetail']['budget_md'] : 0;
                    $count++;
                }
            }
            $average = ($count == 0) ? 0 : round($average / $count, 2);
            $internals = array(
                'budgetEuro' => $budgetEuro,
                'budgetManDay' => $budgetManDay,
                'forecastedManDay' => $getDataProjectTasks['consumed'] + $getDataProjectTasks['remain'],
                'forecastEuro' => $engagedErro + ($remainInter * $average),
                'varEuro' => ($budgetEuro != 0) ? round(((($engagedErro + ($remainInter * $average)) / $budgetEuro) - 1) * 100, 2) : 0,
                'consumedManday' => $getDataProjectTasks['consumed'],
                'consumedEuro' => $engagedErro,
                'remainManday' => $getDataProjectTasks['remain'],
                'remainEuro' => $remainInter * $average,
            );
        }
        // External costs
        $externals = array();
        if (!empty($settingMenusKpi) && !empty($settingMenusKpi['project_budget_externals'])) {
            $projectBudgetExternals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'conditions' => array('project_id' => $projectName['Project']['id']),
                'fields' => array('id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'progress_erro')
            ));
            if (!empty($projectBudgetExternals)) {
                $exBudgetManDay = $exBudgetEuro = $exRemainEuro = $exForecastEuro = $exOrderEuro = 0;
                foreach ($projectBudgetExternals as $key => $projectBudgetExternal) {
                    $dx = $projectBudgetExternal['ProjectBudgetExternal'];
                    $exBudgetEuro += !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                    $exRemainEuro += !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $order = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                    $exOrderEuro += $order;
                    $remain = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                    $exForecastEuro += $order + $remain;
                    $exBudgetManDay += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                }
                $exVarEuro = ($exBudgetEuro == 0 ) ? -100 : round(((($exOrderEuro + $exRemainEuro) / $exBudgetEuro) - 1) * 100, 2);
                $externals['BudgetManDay'] = $exBudgetManDay;
                $externals['BudgetEuro'] = $exBudgetEuro;
                $externals['ForecastEuro'] = $exForecastEuro;
                $externals['VarEuro'] = $exVarEuro;
                $externals['ConsumedEuro'] = $exOrderEuro;
                $externals['RemainEuro'] = $exRemainEuro;
            }
        }
        $this->getDataOfExternalTask($projectName['Project']['id']);
        $_projectTaskKpi = $this->_getPorjectTask($projectName['Project']['id']);
        $progression = $_projectTaskKpi['progression'];
        $validated = $_projectTaskKpi['validated'];
        $engaged = $_projectTaskKpi['engaged'];
        $this->_dashBoard($projectName['Project']['id']);
        //filter status.
        $_statusFilters = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'widget' => array('issue', 'risk', 'project_task', 'finance_plus', 'global_view', 'finance_two_plus')
            ),
            'fields' => array('widget', 'status')
        ));
        $statusFilters = array();
        foreach ($_statusFilters as $key => $statusFilter) {
            if (!empty($statusFilter)) {
                $statusFilters[$key] = explode(',', $statusFilter);
            }
        }
        $displayParst = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Employee']['company_id'],
                'model' => 'project',
                'controllers' => 'project_parts',
                'functions' => 'index'
            )
        ));
        $projectBackup = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_manager_id')
        ));
        $listCurPhase = $this->ProjectPhaseCurrent->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectName['Project']['id']
            ),
            'fields' => array('id', 'project_phase_id')
        ));
        $breakpage = $this->YourFormFilter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info['Employee']['id'],
                'page_break' => 1
            ),
            'fields' => array('id', 'widget')
        ));
        $displayParst = !empty($displayParst) && !empty($displayParst['Menu']['display']) ? $displayParst['Menu']['display'] : 0;
        $this->set(compact('budgetKpi', 'settingMenusKpi', 'internals', 'externals', 'progression', 'projectBackup', 'listCurPhase', 'breakpage', 'engaged', 'validated'));
        $this->set(compact('showMenu', 'tranMenu', 'visionTaskExports', 'yourFormFilter', 'totals', 'manuallyAchievement', 'activateProfile', 'profiles', 'projectPhaseStatuses', 'commentKpi', 'todoKpi', 'doneKpi', 'statusFilters', 'displayParst', 'acceptancesKpi', 'typeAcceptance'));
        $this->set(compact('taskExternals', 'budgetTypes', 'capexTypes', 'budgetProviders', 'budgetExternals', 'dataFinan', 'finances', 'mileStone', 'orders', 'i18n', 'projectTaskForTasks', 'settingP', 'checkP', 'listReference', 'listColor', 'colorDefault', 'colorSeverities'));
        $this->set(compact('dependencies', 'colors', 'listProjects', 'dataDependency', 'project_id', 'list', 'count', 'history', 'projectGlobalView', 'noFileExistsGlobal', 'getDataProjectTasks', 'budgets', 'engagedErro', 'externalBudgets', 'externalConsumeds'));
        $this->set(compact('page', 'budget_settings', 'ProjectArms', 'id', 'projectRisks', 'riskSeverities', 'riskOccurrences', 'issueStatus', 'riskSeverities', 'issueSeverities', 'projectIssues', 'projectLocalView', 'noFileExists', 'projectName'));
        $this->render('ajax');
    }

    public function savePopupPosition() {
        if (!empty($_POST)) {
            $data = json_encode($_POST);
            $this->loadModel('HistoryFilter');
            $employee_id = $this->employee_info['Employee']['id'];
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'Project_grid/popup_position',
                    'employee_id' => $employee_id
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'path' => 'Project_grid/popup_position',
                    'params' => $data
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'Project_grid/popup_position',
                    'employee_id' => $employee_id,
                    'params' => $data
                ));
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }

    public function savePopupPositions() {
        if (!empty($_POST)) {
            $data = json_encode($_POST);
            $this->loadModel('HistoryFilter');
            $employee_id = $this->employee_info['Employee']['id'];
            $check = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'path' => 'projects/popup_position',
                    'employee_id' => $employee_id
                )
            ));
            if (!empty($check)) {
                $this->HistoryFilter->id = $check['HistoryFilter']['id'];
                $this->HistoryFilter->save(array(
                    'path' => 'projects/popup_position',
                    'params' => $data
                ));
            } else {
                $this->HistoryFilter->create();
                $this->HistoryFilter->save(array(
                    'path' => 'projects/popup_position',
                    'employee_id' => $employee_id,
                    'params' => $data
                ));
            }
            echo 'Done';
            die;
        }
        echo 'Error';
        die;
    }

    public function saveFieldYourForm($project_id) {
		if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		if( empty( $_POST['field'])){
			echo "Error";
			die;
		}
		$update_your_form = ($this->employee_info['Role']['name'] == 'admin');
		if( $_POST['field'] != 'manual_progress'){
			$update_your_form = $can_update = true;
		}elseif( $this->employee_info['Role']['name'] == 'pm'){
			if( !empty( $this->employee_info['Employee']['profile_account']) ){
				$update_your_form = $this->ProfileProjectManagerDetail->find('count', array(
					'recursive' => -1,
					'conditions' => array(
							'widget_id' => 'your_form',
						'model_id' => $this->employee_info['Employee']['profile_account'],
						'read_write' => 1
					),
				));
			}else{
				$update_your_form = $this->employee_info['Employee']['update_your_form'];
			}
		}
        if (!empty($_POST) && $update_your_form) {
            $field = $_POST['field'];
            $value = $_POST['value'];
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            if (strpos($field, 'date_') !== false && (strpos($field, 'date_yy') === false || strpos($field, 'date_mm_yy') === false) ) {
                $value = $str_utility->convertToSQLDate($value);
            }
            if (strpos($field, 'price_') !== false) {
                $value = str_replace(' ', '', $value);
            }
			
			$datas = array(
				$field => $value,
                'last_modified' => time(),
                'update_by_employee' => $this->employee_info['Employee']['fullname']
			);
			/* 
				Viet Nguyen Update: 19/02/2020
				Refresh data khi update:
				 1) project_type -> empty data: project_sub_type, project_sub_sub_type
				 2) project_sub_type -> empty data: project_sub_sub_type
			*/
			$refesh_field = array();
			
			if($field == 'project_type_id'){
				$datas['project_sub_type_id'] = NULL;
				$datas['project_sub_sub_type_id'] = NULL;
			}
			if($field == 'project_sub_type_id'){
				$datas['project_sub_sub_type_id'] = NULL;
			}
			
			if( isset($datas['category']) && $datas['category'] == 3){
				$old_cate = $this->Project->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $project_id
					),
					'fields' => array('id','category')
				));
				$old_cate = $old_cate[$project_id];
				if( $old_cate == 1){
					$datas['updated_ip_arch'] = time();
				}
			}
            $this->Project->id = $project_id;
            $result = $this->Project->save($datas);
			
            $this->loadModels('Activity', 'ProjectEmployeeManager', 'ProjectPhaseCurrent', 'ProjectListMultiple', 'ProjectAmrProgram','ProjectAmrSubProgram');
            $activity_id = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                )
            ));
			//Kiem tra gia tri thay doi co phai la Program.
            if (!empty($activity_id)) {
                if (strpos($field, 'project_amr_program_id') !== false) {
                    $getFamilyActivity = $this->ProjectAmrProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $value,
                            'company_id' => $this->employee_info['Company']['id'],
                        ),
                        'fields' => array('family_id'),
                    ));

                    $family_id = !empty($getFamilyActivity) ? $getFamilyActivity['ProjectAmrProgram']['family_id'] : 0;

                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'family_id' => $family_id,
                    ));
                }
				//Kiem tra field thay doi co phai sub_program.Updated by QuanNV 27/04/2019
				if (strpos($field, 'project_amr_sub_program_id') !== false) {
                    $getSubFamily = $this->ProjectAmrSubProgram->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'id' => $value,
                        ),
                        'fields' => array('sub_family_id'),
                    ));
                    $sub_family_id = !empty($getSubFamily) ? $getSubFamily['ProjectAmrSubProgram']['sub_family_id'] : 0;
					//Thay doi gia tri sub_family_id moi sau khi thay doi sub_program.Updated by QuanNV 27/04/2019
                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'subfamily_id' => $sub_family_id,
                    ));
                }
                if (strpos($field, 'project_name') !== false) {
                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'name' => $value,
                        'long_name' => $value,
                        'short_name' => $value,
                    ));
                }
                if (strpos($field, 'activated') !== false) {
                    $this->Activity->id = $activity_id;
                    $this->Activity->save(array(
                        'activated' => $value,
                    ));
                }
            }
            echo "Done";
            die;
        }
        echo "Error";
        die;
    }
	public function updateProjectTarget(){
		$result = array();
		if (!empty($_POST)) {
			$type = $_POST['type'];
            $value = $_POST['value'];
			if (!empty($type) && isset($value)) {
				$type = 'target_'.$type;
				 $this->loadModel('ProjectTarget');
				 $getProjectTarget = $this->ProjectTarget->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'type' => $type,
						'company_id' => $this->employee_info['Company']['id']
					),
					'fields' => array('id'),
				));
				$saved = array(
					'type' => $type,
					'value' => $value,
					'company_id' => $this->employee_info['Company']['id'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'updated' => time()
				);
				if(!empty($getProjectTarget)){
					 $this->ProjectTarget->id = $getProjectTarget['ProjectTarget']['id'];
					
				}else{
					$this->ProjectTarget->create();
					$saved['created'] = time();
				}
				
				if($this->ProjectTarget->save($saved)){
					$result = $saved;
					$result['employee'] = $this->employee_info['Employee']['fullname'];
					$result['updated'] = date('d/m/Y H:i', $saved['updated']);
					die(json_encode($result));
				}
			}
		}
        die(0);
	}
    public function saveFieldYourFormEditer($project_id) {
        if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		$update_your_form = true;
		if( $this->employee_info['Role']['name'] == 'pm'){
			// Hien tai PM profile khong co "update_your_form" nen k check duoc
			if( !empty( $this->employee_info['Employee']['profile_account'])  || empty($this->employee_info['Employee']['update_your_form'])) $update_your_form = false;
		}
        if (!empty($_POST) && $update_your_form) {
            $field = $_POST['field'];
            $value = $_POST['value'];
            if ($field == 'ProjectEditor1') {
                $field = 'editor_1';
            } else if ($field == 'ProjectEditor2') {
                $field = 'editor_2';
            } else if ($field == 'ProjectEditor3') {
                $field = 'editor_3';
            } else if ($field == 'ProjectEditor4') {
                $field = 'editor_4';
            } else {
                $field = 'editor_5';
            }
            $this->Project->id = $project_id;
            $this->Project->save(array(
                $field => $value,
                'last_modified' => time(),
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            ));
            echo "Done";
            die;
        }
        echo "Error";
        die;
    }
	
    public function saveFieldYourFormPM($project_id) {
		if( !$this->_checkRole(false, $project_id)){
			echo "Error";
			die;
		}
		$update_your_form = true;
		if( $this->employee_info['Role']['name'] == 'pm'){
			// Hien tai PM profile khong co "update_your_form" nen k check duoc
			if( !empty( $this->employee_info['Employee']['profile_account'])  || empty($this->employee_info['Employee']['update_your_form'])) $update_your_form = false;
		}
        if (!empty($_POST) && $update_your_form) {
            $field = $_POST['field'];
            $values = !empty($_POST['value']) ? $_POST['value'] : '';
            $this->loadModels('Activity', 'ProjectEmployeeManager', 'ProjectPhaseCurrent', 'ProjectListMultiple');
            $activity_id = $this->Activity->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'project' => $project_id
                )
            ));
            $activity_id = !empty($activity_id) ? $activity_id['Activity']['id'] : 0;
            if ($field == 'project_employee_manager') {
                $type = 'PM';
                $is_profit_center = 0;
                $project_manager_id = !empty($values) ? $values[0] : 0;
                $this->Project->id = $project_id;
                $this->Project->save(array(
                    'project_manager_id' => $project_manager_id,
                    'last_modified' => time(),
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                ));
            }
            if ($field == 'chief_business_list') {
                $type = 'CB';
                $is_profit_center = 0;
            }
            if ($field == 'technical_manager_list') {
                $type = 'TM';
                $is_profit_center = 0;
				$this->Project->id = $project_id;
                $this->Project->save(array(
                    'technical_manager_id' => '',
                    'last_modified' => time(),
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                ));
            }
            if ($field == 'functional_leader_list') {
                $type = 'FL';
                $is_profit_center = 0;
            }
            if ($field == 'uat_manager_list') {
                $type = 'UM';
                $is_profit_center = 0;
            }
            if ($field == 'read_access') {
                $type = 'RA';
                $is_profit_center = 0;
                $dataRefers = array(
                    'project_id' => $project_id,
                    'type' => $type,
                );
                $checkDatas = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_manager_id', 'id')
                ));
                foreach ($values as $value) {
                    $value = explode('-', $value);
                    $is_profit_center = $value[1];
                    if (in_array($value[0], array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectEmployeeManager->create();
                        $this->ProjectEmployeeManager->save(array(
                            'project_id' => $project_id,
                            'project_manager_id' => $value[0],
                            // 'is_backup' => 0,
                            'activity_id' => $activity_id,
                            'type' => $type,
                            'is_profit_center' => $is_profit_center
                        ));
                    }
                    unset($checkDatas[$value[0]]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectEmployeeManager->id = $va;
                        $this->ProjectEmployeeManager->delete();
                    }
                }
            } else if ($field == 'project_phase_id') {
                $dataRefers = array(
                    'project_id' => $project_id,
                );
                $checkDatas = $this->ProjectPhaseCurrent->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_phase_id', 'id')
                ));
                foreach ($values as $value) {
                    if (in_array($value, array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectPhaseCurrent->create();
                        $this->ProjectPhaseCurrent->save(array(
                            'project_id' => $project_id,
                            'project_phase_id' => $value,
                        ));
                    }
                    unset($checkDatas[$value]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectPhaseCurrent->id = $va;
                        $this->ProjectPhaseCurrent->delete();
                    }
                }
            } else if (strpos($field, 'project_list_multi_') !== false) {
                $dataRefers = array(
                    'project_id' => $project_id,
                    'key' => $field
                );
                $checkDatas = $this->ProjectListMultiple->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_dataset_id', 'id')
                ));
                foreach ($values as $value) {
                    if (in_array($value, array_keys($checkDatas))) {
                        
                    } else {
                        $this->ProjectListMultiple->create();
                        $this->ProjectListMultiple->save(array(
                            'project_id' => $project_id,
                            'project_dataset_id' => $value,
                            'key' => $field
                        ));
                    }
                    unset($checkDatas[$value]);
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectListMultiple->id = $va;
                        $this->ProjectListMultiple->delete();
                    }
                }
            } else {

                //// 
                $dataRefers = array(
                    'project_id' => $project_id,
                    'type' => $type,
                );
                $checkDatas = $this->ProjectEmployeeManager->find('list', array(
                    'recursive' => -1,
                    'conditions' => $dataRefers,
                    'fields' => array('project_manager_id', 'id')
                ));
                if (!empty($values)) {
                    foreach ($values as $value) {
                        if (in_array($value, array_keys($checkDatas))) {
                            
                        } else {
                            $this->ProjectEmployeeManager->create();
                            $this->ProjectEmployeeManager->save(array(
                                'project_id' => $project_id,
                                'project_manager_id' => $value,
                                // 'is_backup' => 0,
                                'activity_id' => $activity_id,
                                'type' => $type,
                                'is_profit_center' => $is_profit_center
                            ));
                        }
                        unset($checkDatas[$value]);
                    }
                }
                if (!empty($checkDatas)) {
                    foreach ($checkDatas as $va) {
                        $this->ProjectEmployeeManager->id = $va;
                        $this->ProjectEmployeeManager->delete();
                    }
                }
            }
            exit;
            echo "Done";
            die;
        }
        echo "Error";
        die;
    }

    public function update() {
        $result = false;
        $this->layout = false;
        $this->loadModel('LogSystem');
        if (!empty($this->data)) {
            $project_id = $this->data['id'];
			$this->_checkRole(false, $project_id);
            $company_id = $this->employee_info['Company']['id'];
            $name = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
            unset($this->data['id']);
            $check = $this->LogSystem->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $project_id
                ),
                'fields' => array('model', 'description'),
                'order' => array('id' => 'ASC')
            ));
            $data = array();
            if (!empty($this->data['ProjectAmr.comment'])) {
                if (!empty($check['ProjectAmr']) && ($check['ProjectAmr'] === $this->data['ProjectAmr.comment'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectAmr',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.comment'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['ProjectAmr.project_amr_risk_information'])) {
                if (!empty($check['ProjectRisk']) && ($check['ProjectRisk'] === $this->data['ProjectAmr.project_amr_risk_information'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectRisk',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.project_amr_risk_information'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['ProjectAmr.project_amr_problem_information'])) {
                if (!empty($check['ProjectIssue']) && ($check['ProjectIssue'] === $this->data['ProjectAmr.project_amr_problem_information'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectIssue',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.project_amr_problem_information'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['ProjectAmr.done'])) {
                if (!empty($check['Done']) && ($check['Done'] === $this->data['ProjectAmr.done'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'Done',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.done'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }

            if (!empty($this->data['ProjectAmr.todo'])) {
                if (!empty($check['ToDo']) && ($check['ToDo'] === $this->data['ProjectAmr.todo'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ToDo',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.todo'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['ProjectAmr.project_amr_solution'])) {
                if (!empty($check['ProjectAmr']) && ($check['ProjectAmr'] === $this->data['ProjectAmr.project_amr_solution'])) {
                    
                } else {
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectAmr',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['ProjectAmr.project_amr_solution'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            $this->LogSystem->create();
            if (!empty($data) && $this->LogSystem->save($data)) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $project_id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    private function _ganttFromPhase($project_id) {
        $this->loadModel('Project');
        $this->Project->recursive = -1;
        $this->Project->Behaviors->attach('Containable');
        $projects = array();
        $projects = $this->Project->find('all', array(
            'conditions' => array('Project.id' => $project_id),
            'contain' => array(
                'ProjectPhasePlan' => array('fields' => array(
                        'id',
                        'phase_planed_start_date',
                        'phase_planed_end_date',
                        'phase_real_start_date',
                        'phase_real_end_date',
                    ),
                    'ProjectPhase' => array('name', 'color')
                )
            ),
            'fields' => array('project_name', 'start_date', 'end_date', 'planed_end_date')
        ));
        //set default real time
        $displayplan = isset($this->params['url']['displayplan']) ? (int) $this->params['url']['displayplan'] : 0;
        $displayreal = isset($this->params['url']['displayreal']) ? (int) $this->params['url']['displayreal'] : 1;
        $this->set(compact('projects', 'display', 'displayreal', 'displayplan'));
    }

    private function _attachReferencedEmployees($projectTasks) {
        $this->loadModel('ProjectTaskEmployeeRefer');
        foreach ($projectTasks as $key => $projectTask) {
            $projectTaskId = $projectTask['ProjectTask']['id'];
            $referencedEmployees = $this->ProjectTaskEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    "project_task_id" => $projectTaskId
                )
            ));
            if (count($referencedEmployees) == 0) {
                
            } else {
                foreach ($referencedEmployees as $key1 => $referencedEmployee) {
                    $projectTasks[$key]['ProjectTask']['ProjectTaskEmployeeRefer'][] = $referencedEmployee['ProjectTaskEmployeeRefer'];
                    $employees = array(
                        'reference_id' => $referencedEmployee['ProjectTaskEmployeeRefer']['reference_id'],
                        'is_profit_center' => $referencedEmployee['ProjectTaskEmployeeRefer']['is_profit_center']
                    );
                    $projectTasks[$key]['ProjectTask']['assigned'][] = $employees;
                }
            }
        }
        return $projectTasks;
    }

    public function deleteActivityProjectNull() {
        $this->loadModel('Activity');
        $activity_id = $this->Activity->find('list', array(
            'conditions' => array(
                'project IS NULL'
            ),
            'fields' => array('id'),
        ));
        if (!empty($activity_id))
            $this->Activity->delete($activity_id);
        $this->redirect(array('action' => 'index'));
    }

    public function getNameEmployee($nameEmployee = null) {
        if (!empty($nameEmployee)) {
            $nameEmployee = trim($nameEmployee);
            $name = explode(" ", $nameEmployee);
            if (!empty($name)) {
                $full_name = trim($name[0]) . ' ' . trim($name[1]);
                $name = substr(trim($name[0]), 0, 1) . '' . substr(trim($name[1]), 0, 1) . '_' . $full_name;
            }
        }
        return $name;
    }

    /* view
     *
     * @return void
     * @access public
     */

    public function upload_global($project_id = null) {
        $this->loadModel('ProjectGlobalView');
		$this->_checkRole(false, $project_id);
        if ($this->data['ProjectGlobalView']['is_file']) {
            if (empty($_FILES)) {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            } else {
				$path = $this->_getPathProjectGlobalView($project_id);
				App::import('Core', 'Folder');
				new Folder($path, true, 0777);

				if (file_exists($path)) {
					$this->MultiFileUpload->encode_filename = false;
					$this->MultiFileUpload->uploadpath = $path;
					$this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
					$this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
					$attachment = $this->MultiFileUpload->upload();
				} else {
					$attachment = "";
					$this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
											, true), $path), 'error');
				}

				if (!empty($attachment)) {
					$attachment = $attachment['attachment']['attachment'];
					$this->ProjectGlobalView->create();
					$last = $this->ProjectGlobalView->find('first', array(
						'recursive' => -1,
						'fields' => array('id', 'attachment'),
						'conditions' => array('project_id' => $project_id)));
					if ($last) {
						$this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
						@unlink($path . $last['ProjectGlobalView']['attachment']);
					}
					if ($this->ProjectGlobalView->save(array(
								'project_id' => $project_id,
								'attachment' => $attachment,
								'is_file' => $this->data['ProjectGlobalView']['is_file']
							))) {
						if ($this->MultiFileUpload->otherServer == true) {
							$this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $project_id);
						}
						$this->Session->setFlash(__('Saved', true), 'success');
					} else {
						@unlink($path . $attachment);
						$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
						if ($this->MultiFileUpload->otherServer == true) {
							$this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $project_id);
						}
					}
				} else {
					$this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
				}
            }
            $this->redirect(array('action' => 'your_form', $project_id));
        } else {
            $last = $this->ProjectGlobalView->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'attachment', 'is_file'),
                'conditions' => array('project_id' => $project_id)));
            if ($last) {
                $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                @unlink($path . $last['ProjectGlobalView']['attachment']);
            }
            $is_https = 0;
            $pos = strpos($this->data['ProjectGlobalView']['attachment'], 'https');
            if ($pos !== false) {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('https://', '', $this->data['ProjectGlobalView']['attachment']);
                $is_https = 1;
            } else {
                $this->data['ProjectGlobalView']['attachment'] = str_replace('http://', '', $this->data['ProjectGlobalView']['attachment']);
                //$is_https = 0;
            }
            if ($this->ProjectGlobalView->save(array(
                        'project_id' => $project_id,
                        'attachment' => $this->data['ProjectGlobalView']['attachment'],
                        'is_file' => $this->data['ProjectGlobalView']['is_file'],
                        'is_https' => $is_https
                    ))) {
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                @unlink($path . $attachment);
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect(array('action' => 'your_form', $project_id));
        }
    }

    protected function _getPathProjectGlobalView($project_id) {
        $this->loadModel('ProjectGlobalView');
        $company = $this->ProjectGlobalView->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectGlobalView->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }

    function save_project_list_filter() {
        if (!empty($_POST)) {
            extract($_POST);
            $path = rtrim($_POST['path'], '/');
            $params = $_POST['params'];
            $employId = $this->Session->read('Auth.Employee.id');
            $employId = isset($employId) ? $employId : null;
            $last = $this->Employee->HistoryFilter->find('first', array(
                'recursive' => -1,
                'fields' => array(
                    'id', 'params'
                ),
                'conditions' => array(
                    'path' => $path,
                    'employee_id' => $this->employee_info['Employee']['id']
            )));

            $this->Employee->HistoryFilter->create();
            if (!empty($last)) {
                $this->Employee->HistoryFilter->id = $last['HistoryFilter']['id'];
                unset($last);
            }
            if (empty($params)) {
                Configure::write('debug', 0);
                echo json_encode($_params);
                exit();
            }
            $this->Employee->HistoryFilter->save(array(
                'path' => $path,
                'params' => $params,
                'employee_id' => $this->employee_info['Employee']['id']), array('validate' => false, 'callbacks' => false)
            );
            echo json_encode(explode('-', $params));
        }
        exit();
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function checkAvatar() {
        $this->loadModel('Employee');
        $checkAvata = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $this->employee_info['Company']['id']),
            'fields' => array('id', 'avatar_resize')
        ));

        $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
        return $checkAvata;
    }

    public function update_text_comment() {
        $result = false;
        $this->layout = false;
        $this->loadModel('LogSystem');
        $model = '';
		$project_id = !empty($this->data['id']) ? $this->data['id'] : '';
        if (!empty($this->data) && $this->_checkRole(false, $project_id)) {

            $company_id = $this->employee_info['Company']['id'];
            $name = $this->employee_info['Employee']['fullname'] . ' ' . date('H:i d/m/Y', time());
            unset($this->data['id']);
            $check = $this->LogSystem->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $project_id
                ),
                'fields' => array('model', 'description'),
                'order' => array('id' => 'ASC')
            ));
            $data = array();
            if (!empty($this->data['field']) && ($this->data['field'] == 'comment' || $this->data['field'] == 'ProjectAmr')) {
                if (!empty($check['ProjectAmr']) && ($check['ProjectAmr'] === $this->data['field'])) {
                    
                } else {
                    $model = 'ProjectAmr';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectAmr',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['field']) && ($this->data['field'] == 'project_amr_risk_information' || $this->data['field'] == 'ProjectRisk')) {
                if (!empty($check['ProjectRisk']) && ($check['ProjectRisk'] === $this->data['field'])) {
                    
                } else {
                    $model = 'ProjectRisk';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectRisk',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['field']) && ($this->data['field'] == 'project_amr_problem_information' || $this->data['field'] == 'ProjectIssue')) {
                if (!empty($check['ProjectIssue']) && ($check['ProjectIssue'] === $this->data['field'])) {
                    
                } else {
                    $model = 'ProjectIssue';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectIssue',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['field']) && ($this->data['field'] == 'done' || $this->data['field'] == 'Done')) {
                if (!empty($check['Done']) && ($check['Done'] === $this->data['field'])) {
                    
                } else {
                    $model = 'Done';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'Done',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }

            if (!empty($this->data['field']) && ($this->data['field'] == 'todo' || $this->data['field'] == 'ToDo')) {
                if (!empty($check['ToDo']) && ($check['ToDo'] === $this->data['field'])) {
                    
                } else {
                    $model = 'ToDo';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ToDo',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['field']) && $this->data['field'] == 'project_amr_solution') {
                if (!empty($check['ProjectAmr']) && ($check['ProjectAmr'] === $this->data['field'])) {
                    
                } else {
                    $model = 'ProjectAmr';
                    $data = array(
                        'company_id' => $company_id,
                        'model' => 'ProjectAmr',
                        'model_id' => $project_id,
                        'name' => $name,
                        'description' => $this->data['text'],
                        'employee_id' => $this->employee_info['Employee']['id'],
                        'update_by_employee' => $this->employee_info['Employee']['fullname']
                    );
                }
            }
            if (!empty($this->data['model']) && ( in_array($this->data['model'], array('Scope', 'Schedule', 'Budget', 'Resources', 'Technical')) )) {
                $model = $this->data['model'];
                $data = array(
                    'company_id' => $company_id,
                    'model' => $model,
                    'model_id' => $project_id,
                    'name' => $name,
                    'description' => $this->data['text'],
                    'employee_id' => $this->employee_info['Employee']['id'],
                    'update_by_employee' => $this->employee_info['Employee']['fullname']
                );
            }
            if (!empty($this->data['logid'])) {
                // edit log comment
                $this->LogSystem->id = $this->data['logid'];
                $save_field = array(
                    'description' => $this->data['text'],
                    'updated' => time(),
                );
                if (!empty($data) && $this->LogSystem->save($save_field)) {
                    $result = true;
                }
            } else {
                $this->LogSystem->create();
                if (!empty($data) && $this->LogSystem->save($data)) {
                    $result = true;
                }
            }
            $this->data['id'] = $project_id;
        }
        if ($result && $model) {
            $this->notifyForComment($project_id, $model, $this->data['text']); // Send notify to users.
            $data = array();
            $this->loadModels('LogSystem', 'Project');
            $logSystems = $this->LogSystem->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'model_id' => $project_id,
                    'model' => $model,
                ),
                //'limit' => 3,
                'fields' => array('id', 'model_id', 'model', 'description', 'updated', 'update_by_employee', 'name', 'employee_id'),
                'order' => array('updated' => 'DESC')
            ));
            $checkAvata = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->employee_info['Company']['id']),
                'fields' => array('id', 'avatar_resize')
            ));
            $checkAvata = Set::combine($checkAvata, '{n}.Employee.id', '{n}.Employee.avatar_resize');
            $data['hasAvatar'] = $checkAvata;
            $listComments = !empty($logSystems) ? Set::combine($logSystems, '{n}.LogSystem.id', '{n}.LogSystem') : array();
            $i = 0;
            foreach ($listComments as $_comment) {
                $data[] = $_comment;
                //$data[$i++]['updated'] = date('d/m/Y', $_comment['updated']);
            }
            $data['current'] = time();
            die(json_encode($data));
        }
        die(0);
    }

    /**
     * Function: Get Employees with in current Project
     * @var     : int $project_id
     * @return  : array of Employees
     * @author : BANGVN
     */
    public function getTeamEmployees($project_id, $getPhase = null, $project_task_id = null) {
        $elm = $this->_getTeamEmployees($project_id, $project_task_id);
		$this->_checkRole(true, $project_id, array(), $project_task_id);
        $success = false;
        if ($elm) {
            $this->layout = 'ajax';
            $success = true;
        }
		$company_id = $this->employee_info['Company']['id'];
		$listPhases = array();
		if( $getPhase ){
			$this->loadModels('ProjectPhasePlan');
			$parts = $this->ProjectPhasePlan->ProjectPart->find('list', array(
				'conditions' => array('project_id' => $project_id),
				'order' => array('weight' => 'ASC')
			));
			$listAllPhases = $this->ProjectPhasePlan->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id,
				),
				'joins' => array(
					array(
						'table' => 'project_phases',
						'alias' => 'ProjectsPhase',
						'conditions' => array(
							'ProjectPhasePlan.project_planed_phase_id = ProjectsPhase.id',
							'ProjectsPhase.company_id = ' . $company_id
						)
					)
				),
				'fields' => array('ProjectPhasePlan.id', 'ProjectPhasePlan.project_planed_phase_id', 'ProjectPhasePlan.project_id', 'ProjectPhasePlan.phase_planed_start_date', 'ProjectPhasePlan.phase_planed_end_date', 'ProjectPhasePlan.phase_real_start_date', 'ProjectPhasePlan.phase_real_end_date', 'ProjectsPhase.name', 'ProjectPhasePlan.project_part_id')
			));
			foreach ($listAllPhases as $Phase) {
				$part_id = $Phase['ProjectPhasePlan']['project_part_id'];
				if( !empty($part_id) && !empty($parts[$part_id ]) ){
					$part_name = $parts[$Phase['ProjectPhasePlan']['project_part_id']];
					$Phase['ProjectsPhase']['name'] = $Phase['ProjectsPhase']['name'] . ' (' . $part_name . ')';
				}
				$listPhases[$Phase['ProjectPhasePlan']['id']] = array_merge($Phase['ProjectPhasePlan'], $Phase['ProjectsPhase']);
			}
		}
        die(json_encode(array(
            'success' => $success,
            'data' => $elm,
            'listPhases' => $listPhases,
            'message' => ''
        )));
    }

    private function _getTeamEmployees($project_id, $project_task_id = null) {
        $this->loadModel('ProjectTeam');
        $this->_checkRole(false, $project_id, array(), $project_task_id);
        $projectName = $this->viewVars['projectName'];
        $this->ProjectTeam->Behaviors->attach('Containable');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('Menu');
        $company_id = $this->employee_info['Company']['id'];
        // lay menu project team. Neu not display thi cho hien thi all employee.
        $checkDisplayProjectTeam = $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'controllers' => 'project_teams',
                'functions' => 'index',
                'widget_id' => 'team',
                'model' => 'project',
            // 'display' => 1
            ),
            'order' => array('id DESC')
        ));
        if (!empty($checkDisplayProjectTeam) && $checkDisplayProjectTeam['Menu']['display'] == 1) {
            // List all employees followed in this project
            $teams = $this->ProjectTeam->find('all', array(
                'fields' => array('id', 'profit_center_id'),
                'contain' => array('ProjectFunctionEmployeeRefer' => array('employee_id', 'profit_center_id')),
                'conditions' => array('project_id' => $project_id)
            ));
            $project = $this->_getProject($project_id);
            $employeeIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.employee_id')) : array();
            if (!empty($employeeIds)) {
                foreach ($employeeIds as $employeeId) {
                    foreach ($employeeId as $v) {
                        $_employeeId[] = $v;
                    }
                }
                $employeeIds = array_unique($_employeeId);
            }
            $employees = $this->ProjectTask->Employee->find('list', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'id' => array_merge($employeeIds, array($project['Project']['project_manager_id'])),
                    'NOT' => array('is_sas' => 1),
                ),
                'fields' => array('id', 'fullname')
            ));
            $rDatas = array();
            if (!empty($employees)) {
                $i = 0;
                foreach ($employees as $id => $name) {
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($name) ? $name : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $i++;
                }
            }
            $getEmploy = !empty($rDatas) ? $rDatas : array();
            $profitCenterIds = !empty($teams) ? array_filter(Set::classicExtract($teams, '{n}.ProjectFunctionEmployeeRefer.{n}.profit_center_id')) : array();
            if (!empty($profitCenterIds)) {
                foreach ($profitCenterIds as $profitCenterId) {
                    foreach ($profitCenterId as $v) {
                        $_profitCenterId[] = $v;
                    }
                }
                $profitCenterIds = array_unique($_profitCenterId);
            }
            if (!empty($profitCenterId)) {
                $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                    'recursive' => -1,
                    'order' => array('ProfitCenter.name' => 'asc'),
                    'conditions' => array(
                        'company_id' => $projectName['Project']['company_id'],
                        'ProfitCenter.id' => $profitCenterIds
                    )
                ));
                $employ = array();
                foreach ($profitCenters as $ks => $profitCenter) {
                    $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                    $employ[$ks]['Employee']['is_profit_center'] = 1;
                    //$employ[$ks]['Employee']['profit_center_id'] = -1;
                    $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
                }
                if (!empty($employ)) {
                    $employees = array_merge($getEmploy, $employ);
                } else {
                    $employees = $getEmploy;
                }
            } else {
                $employees = $getEmploy;
            }
        } else {
			$curentDate = date('Y-m-d');
			$this->ProjectTask->Employee->virtualFields['available'] = 'IF((end_date IS NULL OR end_date = "0000-00-00" OR end_date >= "' .$curentDate . '"), 1, 0)';
            $employees = $this->ProjectTask->Employee->find('all', array(
                'order' => array('Employee.fullname' => 'asc'),
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('is_sas' => 1),
                    'company_id' => $company_id
                ),
                 'fields' => array('id', 'fullname','actif', 'available')
            ));
			
            $rDatas = array();
            if (!empty($employees)) {
                $i = 0;
                foreach($employees as $emp){
					$id = $emp['Employee']['id'];
                    $rDatas[$i]['Employee']['id'] = $id;
                    $rDatas[$i]['Employee']['name'] = isset($emp['Employee']['fullname']) ? $emp['Employee']['fullname'] : '';
                    $rDatas[$i]['Employee']['is_profit_center'] = 0;
                    $rDatas[$i]['Employee']['actif'] = intval($emp['Employee']['actif']) ? intval($emp['Employee']['available']) : 0;
                    $i++;
                }
            }
          
			$employees = $rDatas;
            // lay team
            $profitCenters = $this->ProjectTeam->ProfitCenter->find('all', array(
                'recursive' => -1,
                'order' => array('ProfitCenter.name' => 'asc'),
                'conditions' => array(
                    'company_id' => $projectName['Project']['company_id']
                )
            ));
            $employ = array();
            foreach ($profitCenters as $ks => $profitCenter) {
                $employ[$ks]['Employee']['id'] = $profitCenter['ProfitCenter']['id'];
                $employ[$ks]['Employee']['is_profit_center'] = 1;
                //$employ[$ks]['Employee']['profit_center_id'] = -1;
                $employ[$ks]['Employee']['name'] = 'PC / ' . $profitCenter['ProfitCenter']['name'];
            }
            if (!empty($employ)) {
                $employees = array_merge($employees, $employ);
            }
        }
        return $employees;
    }
	public function getSettings(){
		$this->loadModel('HistoryFilter');
		$settings = '{}';
		$settings = $this->HistoryFilter->find('first', array(
			'conditions' => array(
				'path' => $this->params['url']['url'],
				'employee_id' => $this->employee_info['Employee']['id']
			)
		));
		die($settings);
		exit;
	}
	public function toggleLike($project_id){
		$this->loadModel('MLike');
		$this->_checkRole(false, $project_id);
		$employee_id = $this->employee_info['Employee']['id'];
		$result = $this->MLike->toggle_like($employee_id, $project_id);
		die( json_encode(array(
			'result' => 'success',
			'data' => array(
				'liked' => $result['liked'],
				'countLikes' => $result['countLikes']
			),
			'message' => ''
		)));
	}
	public function toggleFavoriteProject($project_id){
		$this->loadModel('MFavorite');
		$this->_checkRole(false, $project_id);
		$employee_id = $this->employee_info['Employee']['id'];
		$result = $this->MFavorite->toggle_favorite($employee_id, $project_id);
		die( json_encode(array(
			'result' => 'success',
			'favorite' => $result,
			'message' => ''
		)));
	}
	public function updateCustomerLogo($logo_id = null){
		$this->loadModels('CustomerLogo', 'Employee');
		$this->layout = false;
		$result = false;
		$logo_file = $this->CustomerLogo->find('first', array(
			'recursive' => -1,
			'conditions' =>  array(
				'id' => $logo_id,
				'OR' => array(
					'company_id IS NULL',
					'company_id' => $this->employee_info['Company']['id']
				),
			),
			'fields' => array('id', 'logo_name', 'company_id'),
		)); 
		if(!empty($logo_file) || empty($logo_id)){
			$this->Employee->id = $this->employee_info['Employee']['id'];
			if($this->Employee->saveField('logo_id', $logo_id)){
				$result = true;
			}
		}
		die($result);
	}
	private function external_progress_line($project_id, $return = false){
		$this->loadModel('ProjectBudgetExternal');
		$dataset_externals = array();
		$listExternals = $this->ProjectBudgetExternal->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('id', 'project_id', 'name', 'order_date', 'budget_erro', 'ordered_erro', 'remain_erro', 'expected_date', 'due_date')
		));
		$listExternals = !empty($listExternals) ? Set::combine($listExternals, '{n}.ProjectBudgetExternal.id', '{n}.ProjectBudgetExternal', '{n}.ProjectBudgetExternal.project_id') : array();
		// debug($project_id);
		$dataset_externals = array();
		foreach($listExternals as $project_id => $externals){
			$minDateEx = 0;
			$maxDateEx = 0;
			$arrayVals = array();
			$ex_budget =  $ex_ordered =  $ex_forecast = array();
			foreach($externals as $keyEx => &$valEx){
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
			if(empty($dataset_externals[$project_id])) $dataset_externals[$project_id] = array();
			$dataset_externals[$project_id][$dateEx] = $lastValue;
			while($dateEx <= $maxDateEx){
				$lastValue['date'] = $dateEx;
				$lastValue['date_format'] = date('m/y', $dateEx);
				if( !empty($ex_budget[$dateEx])) $lastValue['budget'] += $ex_budget[$dateEx];
				if( !empty($ex_forecast[$dateEx])) $lastValue['forecast'] += $ex_forecast[$dateEx];
				if( !empty($ex_ordered[$dateEx])) $lastValue['ordered'] += $ex_ordered[$dateEx];
				$dataset_externals[$project_id][$dateEx] = $lastValue;
				$dateEx = strtotime('next month', $dateEx);
			}
		}
		$this->set(compact('dataset_externals'));
		if( $return) return $dataset_externals;
	}
	public function get_dash_widget_progress_line(){
		$result = false;
		$message = '';
		$data = array();
		$projectIds = $this->data['projectIds'];
		// debug($this->data);
		// debug($projectIds);
		$projectIds = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array('id' => $projectIds, 'company_id' => $this->employee_info['Company']['id']),
			'fields' => array('id', 'id')
		));
		$projectIds = array_unique(array_values($projectIds));
		$enableWidgets = array();
		$LANG = Configure::read('Config.language');
		$isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
		if($isProfileManager != 0 ){
			$enableWidgets = $this->ProfileProjectManagerDetail->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
				),
				'fields' => array('widget_id',  'name_'.$LANG)
			));
		}else{
			$enableWidgets = $this->Menu->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'display' => 1,
					'model' => 'project'
				),
				'fields' => array('widget_id', 'name_'.$LANG)
			));
		}
		// debug($projectIds); exit;
		$dataset_internals = $dataset_externals = array();
		if( isset($enableWidgets['internal_cost']) || isset($enableWidgets['synthesis'])){ 
			$dataset_internals = $this->internal_progress_line($projectIds, true);
		}
		if( isset($enableWidgets['external_cost']) || isset($enableWidgets['synthesis']) ){ 
			$dataset_externals = $this->external_progress_line($projectIds, true); 
		}
		$syns_progress_keys = array();
		foreach( $projectIds as $id){
			$k_int = !empty( $dataset_internals[$id]) ? array_keys($dataset_internals[$id]) : array();
			$k_ext = !empty( $dataset_externals[$id]) ? array_keys($dataset_externals[$id]) : array();
			$total_key = array_unique(array_merge( $k_int, $k_ext));
			sort( $total_key);
			$syns_progress_keys[$id] = $total_key;
		}
		if( !empty( $dataset_externals) || !empty($dataset_externals) )
			$result = true;
		$data = array(
			'dataset_internals' => $dataset_internals,
			'dataset_externals' => $dataset_externals,
			'syns_progress_keys' => $syns_progress_keys,
		);
		die(json_encode(array(
			'result' => $result ? 'success' : 'failed',
			'message' => $message,
			'data' => $data
		)));
	}
	private function internal_progress_line($project_id, $return = false){
		$dataset_internals = array();
		$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? intval($this->companyConfigs['manual_consumed']) : 0;
		if( $useManualConsumed){
			foreach($project_id as $p_id){
				$project_internal_data = $this->ProjectBudgetInternal->progress_line_manual_consumed($p_id, $this->employee_info['Company']['id']);
				$dataset_internals[$p_id] = $project_internal_data ? $project_internal_data['dataset_internals'] : array();
			}
		}else{
			$this->loadModel('ProjectBudgetInternalDetail', 'ProjectBudgetSyn');

			$staffings = array();
			$staffings = $this->get_data_staffing_projects($project_id);
			
			$dataset_internals = array();
			$dataset_internals = $this->data_cost_staffing_projects($staffings, $project_id);
			
			$valueInternals = $this->ProjectBudgetInternalDetail->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('project_id', 'validation_date', 'budget_md', 'budget_erro')
			));
			$projectBudgetSyn = $this->ProjectBudgetSyn->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('project_id', 'internal_costs_average','internal_costs_forecast')
			));
			$projects_tjm = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn.internal_costs_average') : array();
			$internal_costs_forecast = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn.internal_costs_forecast') : array();
			// Add value budget MD/Euro to data staffing 
			$list_date = array();
			if( !empty( $valueInternals)){
				foreach($valueInternals as $index => $interValue){
					$dx = $interValue['ProjectBudgetInternalDetail'];
					$p_id = $dx['project_id'];
					$date = $dx['validation_date'];
					if( empty( $date) || ($date == '0000-00-00')) continue;
					$date = strtotime( $date );
					$date = strtotime( date( 'Y-m-01', $date));
					$list_date[$date] = $date;
					if( empty( $dataset_internals[$p_id][$date]['budget_md'])) $dataset_internals[$p_id][$date]['budget_md'] = 0;
					$dataset_internals[$p_id][$date]['budget_md'] += (float)$dx['budget_md'];
					if( empty( $dataset_internals[$p_id][$date]['budget_price'])) $dataset_internals[$p_id][$date]['budget_price'] = 0;
					$dataset_internals[$p_id][$date]['budget_price'] += (float)$dx['budget_erro'];
				}
			}
			
			unset($valueInternals);
			
			$max_internal_md = 0;
			$max_internal_euro = 0;
			$countLineIn = 0;
			foreach($project_id as $p_id){
				$list_month = !empty($dataset_internals[$p_id]) ? array_filter(array_keys($dataset_internals[$p_id])) : array();
				$date = !empty($list_month) ? min($list_month) : strtotime(date('Y').'-02-01');
				$maxDate = !empty($list_month) ? max($list_month) : strtotime(date('Y').'-12-01');
				$first_month = strtotime('previous month', $date);
			
				$lastValue = array(
					'date_format' => date( 'm/y' ,$first_month),
					'date' => $first_month,
					'consumed' => 0,
					'consumed_price' => 0,
					'validated' => 0,
					'validated_price' => !empty($internal_costs_forecast[$p_id]) ? (float)$internal_costs_forecast[$p_id] : 0,
					'budget_md' => 0,
					'budget_price' => 0,
				);
				$dataset_internals[$p_id][$first_month] = $lastValue;
				
				while( $date <= $maxDate){
					$data = isset($dataset_internals[$p_id][$date]) ? $dataset_internals[$p_id][$date] : array();
					$lastValue['date_format'] = date('m/y', $date);
					$lastValue['date'] = $date;
					$lastValue['consumed'] += !empty($data['consumed']) ? $data['consumed'] : 0;
					$lastValue['consumed_price'] += !empty($data['consumed_price']) ? $data['consumed_price'] : 0;
					$lastValue['validated'] += !empty($data['validated']) ? $data['validated'] : 0;
					$lastValue['budget_md'] += !empty($data['budget_md']) ? $data['budget_md'] : 0;
					$lastValue['budget_price'] += !empty($data['budget_price']) ? $data['budget_price'] : 0;
					$dataset_internals[$p_id][$date] = $lastValue;
					$date = strtotime('next month', $date);
				}
			}
		}
		ksort($dataset_internals);
		if( $return) return $dataset_internals;
        $this->set(compact('dataset_internals'));
    }
	
	 /**
     * @return array()
     * @access private
     */
    private function get_data_staffing_projects($project_id = null) {
        $this->loadModels('TmpStaffingSystem', 'Profile', 'ProfitCenter', 'Employee');
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
		
        $datas = array();
        /**
         * Chuyen du lieu tu 1 mang phang sang dinh dang kieu
         * Employee => array(
         *      'time' => array()
         * )
         */
		$list_employees = array();
        
		if(!empty($getDatas)){
            foreach($getDatas as $getData){
                $dx = $getData['TmpStaffingSystem'];
				$p_id = $dx['project_id'];
				$list_employees[] = $dx['model_id'];
                // date
                $datas[$p_id][$dx['model_id']][$dx['date']]['date'] = $dx['date'];
                // workload
                if(!isset($datas[$p_id][$dx['model_id']][$dx['date']]['validated'])){
                    $datas[$p_id][$dx['model_id']][$dx['date']]['validated'] = 0;
                }
                $datas[$p_id][$dx['model_id']][$dx['date']]['validated'] += $dx['estimated'];
                //consumed
                if(!isset($datas[$p_id][$dx['model_id']][$dx['date']]['consumed'])){
                    $datas[$p_id][$dx['model_id']][$dx['date']]['consumed'] = 0;
                }
                $datas[$p_id][$dx['model_id']][$dx['date']]['consumed'] += $dx['consumed'];
                
            }
        }
		$list_employees = array_unique($list_employees);
        $employees = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => array('Employee.id' => $list_employees),
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
		$project_tjm = $this->ProjectBudgetSyn->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('project_id', 'internal_costs_average')
		));
		foreach ($employees as &$employee){
			if( empty( $employee['tjm'])){
				if( !empty( $profiles_tjm[$employee['profile_id']])) 
					$employee['tjm'] = $profiles_tjm[$employee['profile_id']];
				elseif( !empty( $team_tjm[$employee['profit_center_id']])) 
					$employee['tjm'] =  $team_tjm[$employee['profit_center_id']];
				// else
					// $employee['tjm'] = $project_tjm;
			}
		}
		$this->employee_tjms = $employees;
		
		/* END Get Employee TJM */
		
		/* Caculate Consumed and workload by Price ( Default Euro)*/
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
		foreach($datas as $project_id => $staffing_data){
			if(!empty($staffing_data['999999999'])){
				$notAffected = array(
					'id' => '999999999',
					'fullname' => 'Not Affected',
					'tjm' => $project_tjm
				);
				$employees['999999999'] = $notAffected;
			}
			// Phan nay se kiem tra $companyConfigs['consumed_used_timesheet'] o function totalStaffing;		
			
			if( !empty( $staffing_data)){
				foreach( $staffing_data as $e_id => $data ){
					foreach($data as $date => $date_data){
						if( !$consumed_used_timesheet){
							$_tjm = (!empty($employees) && !empty($employees[$e_id]) && !empty($employees[$e_id]['tjm'])) ? $employees[$e_id]['tjm'] : (!empty($project_tjm[$project_id]) ? $project_tjm[$project_id] : 1);
							$datas[$project_id][$e_id][$date]['consumed_price'] = (float)$date_data['consumed'] * (float)$_tjm;
						}
					}
				}
			}
		}
		return $datas;
		
    }
	private function data_cost_staffing_projects($staffings, $project_id=null){
        $totalStaffing = array();
		$consumed_used_timesheet = !empty($this->companyConfigs['consumed_used_timesheet']);
		
        $projects_tjm = $this->ProjectBudgetSyn->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id
			),
			'fields' => array('project_id','internal_costs_average')
		));
			
		$consumeds = array();
		if( $consumed_used_timesheet) {
			$this->loadModels('ActivityRequest', 'Project', 'ActivityTask', 'ProjectBudgetSyn');
			
			$activity_id = $this->Project->find('list', array(
				'recursive' => -1,
				'conditions' => array('id' => $project_id),
				'fields' => array('id', 'activity_id')
			));
			$project_by_activity = array_flip($activity_id);
			
			$activityTasks = $this->ActivityTask->find('all', array(
				'recursive' => -1,
				'conditions' => array('activity_id' => $activity_id),
				'fields' => array('id', 'activity_id')
			));
			
			$activty_task_id = array();
			foreach($activityTasks as $index => $activityTask){
				$dx = $activityTask['ActivityTask'];
				$activty_task_id[$dx['id']] = $dx['activity_id'];
			}
			
			$groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
			
			$data_activity = $this->ActivityRequest->find('all', array(
				'recursive' => -1,
				'fields' => array(
					'employee_id', 
					'value',
					'date',
					'cost_price_resource',  
					'cost_price_team', 
					'cost_price_profil',
					'task_id',
					'activity_id'
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
					$actvity_id = !empty($activity['activity_id']) ? $activity['activity_id'] : $activty_task_id[$activity['task_id']];
					$pro_id = $project_by_activity[$actvity_id];
					$e_tjm = !empty( $employees[$e_id]['tjm'] ) ? $employees[$e_id]['tjm'] : $projects_tjm[$pro_id];
					if((float)$activity['cost_price_resource'] > 0) {
						$consumed = $activity['cost_price_resource'];
					}elseif((float)$activity['cost_price_profil'] > 0){
						$consumed = $activity['cost_price_profil'];
					}elseif((float)$activity['cost_price_team'] > 0){
						$consumed = $activity['cost_price_team'];
					}else{
						$consumed = $activity['value']*$e_tjm;
					}
					if( empty( $consumeds[$pro_id][$date])) $consumeds[$pro_id][$date] = array();
					if( empty( $consumeds[$pro_id][$date]['consumed'])) $consumeds[$pro_id][$date]['consumed'] = 0;
					if( !isset( $consumeds[$pro_id][$date]['consumed_price'])) $consumeds[$pro_id][$date]['consumed_price'] = 0;
					$consumeds[$pro_id][$date]['consumed'] += $activity['value'];
					$consumeds[$pro_id][$date]['consumed_price'] += $consumed;
				}
			}
		}
		
		if(!empty($staffings)){
            foreach($staffings as $project_id => $data){
				foreach($data as $employee_id => $staffing){
					foreach($staffing as $time => $values){
						$_time = date('m/y', $values['date']);
						$totalStaffing[$project_id][$time]['date_format'] = $_time;
						$totalStaffing[$project_id][$time]['date'] = $time;
						if( $consumed_used_timesheet) {
							$totalStaffing[$project_id][$time]['consumed'] = 0;
							$totalStaffing[$project_id][$time]['consumed_price'] = 0;
						}else{
							if(!isset($totalStaffing[$time]['consumed'])){
								$totalStaffing[$project_id][$time]['consumed'] = 0;
							}
							$totalStaffing[$project_id][$time]['consumed'] += $values['consumed'];
							if(!isset($totalStaffing[$project_id][$time]['consumed_price'])){
								$totalStaffing[$project_id][$time]['consumed_price'] = 0;
							}
							$totalStaffing[$project_id][$time]['consumed_price'] += $values['consumed_price'];
						}
						if(!isset($totalStaffing[$project_id][$time]['validated'])){
							$totalStaffing[$project_id][$time]['validated'] = 0;
						}
						$totalStaffing[$project_id][$time]['validated'] += $values['validated'];
					}
				}
			}
		}
		if( $consumed_used_timesheet) {
			foreach( $consumeds as $project_id => $value){
				foreach( $value as $date => $consumed){
					if( !isset( $totalStaffing[$project_id][$date])){
						$totalStaffing[$project_id][$date] = array(
							'date_format' => date('m/y',$date),
							'date' => $date,
							'validated' => 0,
							'consumed' => $consumed['consumed'],
							'consumed_price' => $consumed['consumed_price'],
						);
					}else{
						$totalStaffing[$project_id][$date]['consumed'] = $consumed['consumed'];
						$totalStaffing[$project_id][$date]['consumed_price'] = $consumed['consumed_price'];
					}
				}
			}
		}
        ksort($totalStaffing);
		return $totalStaffing;
    
	}
	private function getCountTask($projectIds){
		$company_id = $this->employee_info['Company']['id'];
		$this->loadModels('ProjectTask','ProjectStatus');
		$this->ProjectTask->virtualFields['ed_unix'] = 'UNIX_TIMESTAMP(task_end_date)';
		$parentTasks = $this->ProjectTask->find('list', array(
           'recursive' => -1,
           'conditions' => array(
               'project_id' => $projectIds,
               'parent_id >' => 0,
           ),
           'fields' => array('parent_id', 'parent_id'),
           'group' => array('parent_id')
		));
		$list_project_tasks = $this->ProjectTask->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'NOT' => array(
					'id' => $parentTasks
				)
			),
			'fields' => array('id', 'task_title', 'project_id', 'task_status_id', 'task_end_date', 'ed_unix')
		));
		$project_task_id =  !empty($list_project_tasks) ? Set::classicExtract($list_project_tasks, '{n}.ProjectTask.id') : array();
		$task_refer_project = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask.project_id') : array();
		$project_used = array();
		$typeTaskStatus = $this->ProjectStatus->find('list',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id
			),
			'fields' => array('id', 'status')
		)); 
		$list_project_tasks = !empty($list_project_tasks) ? Set::combine($list_project_tasks, '{n}.ProjectTask.id', '{n}.ProjectTask', '{n}.ProjectTask.project_id') : array();
		$summary_tasks = array();
		$current_date = strtotime( date('Y-m-d', time()));
		foreach($list_project_tasks as $p_id => $list_tasks){
			$summary_tasks[$p_id]['count_task'] = count( $list_tasks );
			$summary_tasks[$p_id]['count_task_intime'] = 0;
			$summary_tasks[$p_id]['count_by_stt'] = array();
			$summary_tasks[$p_id]['count_task_late'] = 0;
			foreach( $list_tasks as $t_id => $task){
				$stt = $task['task_status_id'];
				if( empty( $stt)) continue;
				if( empty( $summary_tasks[$p_id]['count_by_stt'][$stt])) $summary_tasks[$p_id]['count_by_stt'][$stt] = 0;
				$summary_tasks[$p_id]['count_by_stt'][$stt] +=1;
				if( !empty( $typeTaskStatus[$stt]) &&  ($typeTaskStatus[$stt] == 'IP') && $task['ed_unix'] < $current_date){
					$summary_tasks[$p_id]['count_task_late'] += 1;
				} else{
					$summary_tasks[$p_id]['count_task_intime'] += 1;
				}
			}
		}
		return $summary_tasks;
	}
	private function getPhasePlans($projectIds){
		/**
         * Lay ngay ket thuc theo ke hoach cua tat ca project
         */
		 $this->loadModel('ProjectPhasePlan');
        $projectPhasePlans = $this->ProjectPhasePlan->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectIds,
                'NOT' => array(
                    'phase_planed_start_date' => '0000-00-00',
                    'phase_planed_end_date' => '0000-00-00'
                )
            ),
            'fields' => array(
                'project_id',
                'MAX(phase_planed_end_date) AS MaxEndDatePlan',
                'MAX(phase_real_end_date) AS MaxEndDateReal',
                'MIN(phase_planed_start_date) AS MinStartDatePlan',
                'MIN(phase_real_start_date) AS MinStartDateReal'
            ),
            'group' => array('project_id')
        ));
        $projectPhasePlans = !empty($projectPhasePlans) ? Set::combine($projectPhasePlans, '{n}.ProjectPhasePlan.project_id', '{n}.0') : array();
		// debug( $projectPhasePlans);
		foreach( $projectPhasePlans as $p_id=>$phase){
			$maxEndDatePlan = strtotime($phase['MaxEndDatePlan']);
			$maxEndDateReal = strtotime($phase['MaxEndDateReal']);
			$minStartDatePlan = strtotime($phase['MinStartDatePlan']);
			$minStartDateReal = strtotime($phase['MinStartDateReal']);
			$projectPhasePlans[$p_id]['max_end_date_plan'] = !empty( $maxEndDatePlan) ? date('d-m-Y', $maxEndDatePlan) : '';
			$projectPhasePlans[$p_id]['max_end_date_real'] = !empty( $maxEndDateReal) ? date('d-m-Y', $maxEndDateReal) : '';
			$projectPhasePlans[$p_id]['min_start_date_plan'] = !empty( $minStartDatePlan) ? date('d-m-Y', $minStartDatePlan) : '';
			$projectPhasePlans[$p_id]['min_start_date_real'] = !empty( $minStartDateReal) ? date('d-m-Y', $minStartDateReal) : '';
			$projectPhasePlans[$p_id]['diff'] = (!empty( $maxEndDatePlan) && !empty( $maxEndDateReal)) ? floor(($maxEndDateReal - $maxEndDatePlan)/(60*60*24)) : '';
		}
		return $projectPhasePlans;
	}
	public function getFinanceData($projectIds = array(), $year = null){
		if(empty($year)){
			$year = date('Y', time());
		}
		
		if(!empty($_POST['projectIds'])){
			$projectIds = $_POST['projectIds'];
			$year = $_POST['year'];
		}
		
		/**
         * Lay du lieu o table finacement plus
         */
		$finance_progress_column = array();
		$this->loadModel('ProjectFinancePlusDetail');
		
		$financePlusData = $this->ProjectFinancePlusDetail->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'year >=' => $year,
			),
			'fields' => array(
				'project_id', 
				'CONCAT(ProjectFinancePlusDetail.type, "_", ProjectFinancePlusDetail.model) AS keyValue',
				'value',
				'year'
			),
		));
		foreach($financePlusData as $key => $financePlus){
			$dx = $financePlus['ProjectFinancePlusDetail'];
			$dy = $financePlus[0];
			if(empty($finance_progress_column[$dx['project_id']])) $finance_progress_column[$dx['project_id']] = array();
			if(empty($finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']])) $finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']] = 0;
			$finance_progress_column[$dx['project_id']][$dy['keyValue']][$dx['year']] += (!empty($dx['value']) ? $dx['value'] : 0);	
		}
		if(!empty($_POST['projectIds'])){
			die(json_encode(array(
				'result' =>'success',
				'data' => $finance_progress_column,
			)));
		}
		$financeYear = $this->ProjectFinancePlusDetail->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
			),
			'fields' => array(
				'MIN(year) as fin_min_year', 
				'MAX(year) as fin_max_year', 
			),
		));
		$fin_min_year = $fin_max_year = date('Y', time());
		if(!empty($financeYear)){
			$fin_min_year = !empty($financeYear[0][0]['fin_min_year']) ? $financeYear[0][0]['fin_min_year'] : $fin_min_year;
			$fin_max_year = !empty($financeYear[0][0]['fin_max_year']) ? $financeYear[0][0]['fin_max_year'] : $fin_max_year;
		}
		$result = array(
			'fin_min_year' => $fin_min_year,
			'fin_max_year' => $fin_max_year,
			'values' => $finance_progress_column
		);
		return $result;
	}
	private function getMilestoneData($projectIds){
		$milestoneWidgetData = array();
		$this->loadModels('ProjectMilestone');
		$nextMilestone = $lateMilestone = array();
		$futureMilestones = $this->ProjectMilestone->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'milestone_date >' => date('Y-m-d', time()),
			),
			'fields' => array('milestone_date',  'project_milestone', 'project_id'),
			'order' => array('milestone_date' => 'ASC'),
		));
		$futureMilestones = !empty($futureMilestones) ? Set::combine($futureMilestones, '{n}.ProjectMilestone.milestone_date', '{n}.ProjectMilestone', '{n}.ProjectMilestone.project_id') : array();
		foreach( $futureMilestones as $p_id => $milestones){
			if( !empty($milestones)){
				$first = array_shift($milestones);
				if(strtotime($first['milestone_date']) >= time()){
					$first['date'] = date('d-m-Y', strtotime($first['milestone_date']));
					$milestoneWidgetData[$p_id]['next_milestone'] = $first;
				}
			}
		}
		$lateMilestones = $this->ProjectMilestone->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $projectIds,
				'milestone_date <=' => date('Y-m-d', time()),
				'initial_date !=' => 0,
				'initial_date is not NULL',
				'validated !=' => 1
			),
			'fields' => array('milestone_date',  'project_milestone', 'project_id'),
			'order' => array('milestone_date' => 'ASC'),
		));
		$lateMilestones = !empty($lateMilestones) ? Set::combine($lateMilestones, '{n}.ProjectMilestone.milestone_date', '{n}.ProjectMilestone', '{n}.ProjectMilestone.project_id') : array();
		foreach( $lateMilestones as $p_id => $milestones){
			if( !empty($milestones)){
				$first = array_shift($milestones);
				$first['date'] = date('d-m-Y', strtotime($first['milestone_date']));
				$milestoneWidgetData[$p_id]['milestone_late'] = $first;
			}
		}
		return $milestoneWidgetData;
	}
	public function get_data_dashboard(){
		$result = false;
		$message = '';
		$data = array();
		if( !empty( $this->data['fields']) && !empty( $this->data['projectIds'])){
			$fields = $this->data['fields'];
			$projectIds = $this->data['projectIds'];
			$company_id = $this->employee_info["Company"]["id"];
			// debug( $fields);
			// debug( $projectIds);
			/*
			 * Check permission here
			*/
			$joins = array();
			$project_fields = array('Project.id', 'Project.project_name', 'activity_id');
			$maps = array();
			foreach( $fields as $field){
				// echo $field . '<br>';
				switch( $field){
					case 'dash_count_projects':
						$project_fields[] = 'category';
						break;
					case 'dash_list_weather': 
						$project_fields[] =  'ProjectAmr.weather';
						if( empty($joins['ProjectAmr'])){
							$joins['ProjectAmr'] = array(
								'table' => 'project_amrs',
								'alias' => 'ProjectAmr',
								'conditions' => array(
									'ProjectAmr.project_id = Project.id'
								),
								'type' => 'left',
							);
						}
						break;
					case 'dash_progress_chart': 
					case 'dash_sum_progress': 
						if( empty( $data['ProjectProgress'])) $data['ProjectProgress'] = $this->getProjectProgress($projectIds);
						break;
					case 'dash_progress_all_tasks': 
					case 'dash_progress_tasks': 
					case 'dash_count_tasks': 
						if( empty( $data['summary_tasks'])) $data['summary_tasks'] = $this->getCountTask($projectIds);
						if( empty( $data['Company']['ProjectStatus'])){
							$projectStatus = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id),'order' => 'weight'));
							$data['Company']['ProjectStatus'] = $projectStatus;
						}
						break;
					case 'dash_phase':
						if( empty( $data['projectPhasePlans'])) $data['projectPhasePlans'] = $this->getPhasePlans($projectIds);
						if( empty( $data['ProjectProgress'])) $data['ProjectProgress'] = $this->getProjectProgress($projectIds);
						break;
					case 'dash_financeplusinv': 
					case 'dash_financeplusfon': 
					case 'dash_financeplusfinanfon': 
					case 'dash_financeplusfinaninv': 
						if( empty( $data['financeplus'])) $data['financeplus'] = $this->getFinanceData($projectIds);
						break;
					case 'dash_synthesis': 
					case 'dash_externalbudget': 
					case 'dash_internalbudgeteuro': 
					case 'dash_internalbudgetmd':
						if( empty ( $data['ProjectBudgetSyns'])){
							$this->loadModels('ProjectBudgetSyns');
							$allProjectBudgetSyns = $this->ProjectBudgetSyns->find('all', array(
								'recursive' => -1,
								'conditions' => array(
									'project_id' => $projectIds,
								),
							));
							$allProjectBudgetSyns = !empty($allProjectBudgetSyns) ? Set::combine($allProjectBudgetSyns, '{n}.ProjectBudgetSyns.project_id', '{n}.ProjectBudgetSyns') : array();
							$useManualConsumed = isset($this->companyConfigs['manual_consumed']) ? intval($this->companyConfigs['manual_consumed']) : 0;
							$data['useManualConsumed'] = $useManualConsumed;
							if( $useManualConsumed){
								$dataTasks = $this->Project->dataFromProjectTaskManualConsumeds($projectIds);
								foreach(  $allProjectBudgetSyns as $project_id => $budgetSyn){
									$tjm = !empty($budgetSyn['internal_costs_average']) ? $budgetSyn['internal_costs_average'] : 0;
									$forecastEuro = $engagedErro = $remainEuro = 0;
									if( !empty($dataTasks[$project_id])){
										$engagedErro = $dataTasks[$project_id]['consumed'] * $tjm;
										$remainEuro = ($dataTasks[$project_id]['remain'])*$tjm;
										$forecastEuro = $engagedErro + $remainEuro;
									}
									$budgetSyn['internal_costs_forecast'] = $forecastEuro;
									$budgetSyn['internal_costs_engaged'] = $engagedErro;
									$budgetSyn['internal_costs_remain'] = $remainEuro;
									$budgetSyn['internal_costs_remain'] = $remainEuro;
									$allProjectBudgetSyns[$project_id] = $budgetSyn;	
								}
								$data['dataActivityTaskManual'] = $dataTasks;
							}else{
								
							}
							$data['ProjectBudgetSyns'] = $allProjectBudgetSyns;
							
						}
						break;
					case 'dash_late_milestones': 
					case 'dash_next_milestones':  
						if( empty( $data['ProjectMilestones'])) $data['ProjectMilestones'] = $this->getMilestoneData($projectIds);
						break;
					case 'dash_project_managers': 
						$this->loadModel('ProjectEmployeeManager');
						$projectEmployeeManager = $this->ProjectEmployeeManager->find('list', array(
							'recursive' => -1,
							'conditions' => array(
								'ProjectEmployeeManager.is_profit_center' => 0,
								'ProjectEmployeeManager.type' => 'PM',
								'Project.id' => $projectIds,
							),
							'joins' => array(
								array(
									'table' => 'projects',
									'alias' => 'Project',
									'conditions' => array(
										'Project.id = ProjectEmployeeManager.project_id',
									)
								)
							),
							'fields' => array('ProjectEmployeeManager.id', 'ProjectEmployeeManager.project_manager_id', 'ProjectEmployeeManager.project_id'),
						));
						$project_fields[] = 'project_manager_id';
						// $data['ProjectEmployeeManager'] = $projectEmployeeManager;
					case 'dash_project_amr_program_id': 
						$project_fields[] = 'project_amr_program_id';
						$projectAmrProgram = $this->Project->ProjectAmrProgram->find('list', array(
							'recursive' => -1,
							'fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
							'conditions' => array('ProjectAmrProgram.company_id' => $company_id)
						));
						$data['Company']['ProjectAmrProgram'] = $projectAmrProgram;
						$maps[$field] = $projectAmrProgram;
						break;
					case 'dash_project_status_id': 
						$project_fields[] = 'project_status_id';
						if( empty( $data['Company']['ProjectStatus'])){
							$projectStatus = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_id),'order' => 'weight'));
							$data['Company']['ProjectStatus'] = $projectStatus;
						}
						$maps[$field] = $data['Company']['ProjectStatus'];
						break;
					case 'dash_project_priority_id': 
						$project_fields[] = 'project_priority_id';
						$projectPriority = $this->Project->ProjectPriority->find('list', array(
							'fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 
							'conditions' => array('ProjectPriority.company_id' => $company_id),
						));
						$data['Company']['ProjectPriority'] = $projectPriority;
						$maps[$field] = $projectPriority;
						break;
					case 'dash_team':
						$project_fields[] = preg_replace( '/^dash_/', '',  $field);
						$this->loadModel('ProfitCenter');
						$profitCenter = $this->ProfitCenter->generatetreelist(array('company_id' => $company_id), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
						$data['Company']['ProfitCenter'] = $profitCenter;
						$maps[$field] = $profitCenter;
						break;
					case 'dash_list_1': 
					case 'dash_list_2': 
					case 'dash_list_3': 
					case 'dash_list_4': 
					case 'dash_list_5': 
					case 'dash_list_6': 
					case 'dash_list_7': 
					case 'dash_list_8': 
					case 'dash_list_9': 
					case 'dash_list_10': 
					case 'dash_list_11': 
					case 'dash_list_12': 
					case 'dash_list_13': 
					case 'dash_list_14': 
						$key = preg_replace( '/^dash_/', '',  $field);
						$project_fields[] = $key;
						if( empty( $data['ProjectDataset'])){
							$this->loadModel('ProjectDataset');
							$projectDataset = $this->ProjectDataset->find('list', array(
								'conditions' => array(
									'company_id' => $company_id, 
									'display' => 1
								),
								'fields' => array('id', 'name', 'dataset_name')
							));
							$data['Company']['ProjectDataset'] = $projectDataset;
						}
						$maps[$field] = isset($data['Company']['ProjectDataset'][$key]) ? $data['Company']['ProjectDataset'][$key] : array();
						break;
					case 'dash_yn_1': 
					case 'dash_yn_2': 
					case 'dash_yn_3': 
					case 'dash_yn_4': 
					case 'dash_yn_5': 
					case 'dash_yn_6': 
					case 'dash_yn_7': 
					case 'dash_yn_8': 
					case 'dash_yn_9': 
						$key = preg_replace( '/^dash_/', '',  $field);
						$project_fields[] = $key;
						if( !empty( $projectIds)){
							$this->loadModel('Project');
							$projectYesNo = $this->Project->find('list', array(
								'conditions' => array(
									'company_id' => $company_id,
									'id' => $projectIds
								),
								'fields' => array('id', $key)
							));
							$data['Company']['ProjectYesNo'] = $projectYesNo;
						}
						$maps[$field] = isset($data['Company']['ProjectDataset'][$key]) ? $data['Company']['ProjectDataset'][$key] : array();
						break;
					case 'dash_list_muti_1' :
					case 'dash_list_muti_2' :
					case 'dash_list_muti_3' :
					case 'dash_list_muti_4' :
					case 'dash_list_muti_5' :
					case 'dash_list_muti_6' :
					case 'dash_list_muti_7' :
					case 'dash_list_muti_8' :
					case 'dash_list_muti_9' :
					case 'dash_list_muti_10' :
						$key = preg_replace( '/^dash_/', '',  $field);
						if( empty( $data['ProjectDataset'])){
							$this->loadModel('ProjectDataset');
							$projectDataset = $this->ProjectDataset->find('list', array(
								'conditions' => array(
									'company_id' => $company_id, 
									'display' => 1
								),
								'fields' => array('id', 'name', 'dataset_name')
							));
							$data['Company']['ProjectDataset'] = $projectDataset;
						}
						$maps[$field] = isset($data['Company']['ProjectDataset'][$key]) ? $data['Company']['ProjectDataset'][$key] : array();
						$num = explode('_', $key);
						$num = end($num);
						$old_key = $key;
						$key = 'project_list_multi_'.$num;
						if( empty( $dataMulti)){
							$projectListMultiples = $this->Project->ProjectListMultiple->find('all', array(
								'recursive' => -1,
								'conditions' => array('ProjectListMultiple.project_id' => $projectIds),
								'fields' => array('id', 'project_dataset_id', 'project_id', 'key')
							));
							// $projectListMultiples = !empty( $projectListMultiples) ? Set::combine($projectListMultiples, '{n}.ProjectListMultiple.key', '{n}.ProjectListMultiple.project_id', '{n}.ProjectListMultiple.id', '{n}.ProjectListMultiple.project_dataset_id' ) : array();
							$dataMulti = array();
							if( !empty( $projectListMultiples)){
								foreach( $projectListMultiples as $projectListMultiple){
									$projectListMultiple = $projectListMultiple['ProjectListMultiple'];
									$dataMulti[$projectListMultiple['key']][$projectListMultiple['project_id']][] = $projectListMultiple['project_dataset_id'];
								}
							}
						}
						// debug( $dataMulti); exit;
						$data['multi_fields'][$old_key] = !empty( $dataMulti[$key] ) ? $dataMulti[$key]: array();
						break;
					case 'dash_price_1': 
					case 'dash_price_2': 
					case 'dash_price_3': 
					case 'dash_price_4': 
					case 'dash_price_5': 
					case 'dash_price_6': 
					case 'dash_price_7': 
					case 'dash_price_8': 
					case 'dash_price_9': 
					case 'dash_price_10': 
					case 'dash_price_11': 
					case 'dash_price_12': 
					case 'dash_price_13': 
					case 'dash_price_14': 
					case 'dash_price_15': 
					case 'dash_price_16': 
					case 'dash_number_1': 
					case 'dash_number_2': 
					case 'dash_number_3': 
					case 'dash_number_4': 
					case 'dash_number_5': 
					case 'dash_number_6': 
					case 'dash_number_7': 
					case 'dash_number_8': 
					case 'dash_number_9': 
					case 'dash_number_10': 
					case 'dash_number_11': 
					case 'dash_number_12': 
					case 'dash_number_13': 
					case 'dash_number_14': 
					case 'dash_number_15': 
					case 'dash_number_16': 
					case 'dash_number_17': 
					case 'dash_number_18': 
						$project_fields[] = preg_replace( '/^dash_/', '',  $field);
						if( empty( $data['Company']['ProjectTarget'])){
							$this->loadModel('ProjectTarget');
							$project_target = $this->ProjectTarget->find('all', array(
								'recursive' => -1,
								'conditions' => array(
									'company_id' => $company_id
								),
								'fields' => array('type', 'value', 'updated', 'employee_id'),
							));
							$target_employee_id = !empty($project_target) ? Set::combine($project_target, '{n}.ProjectTarget.employee_id', '{n}.ProjectTarget.employee_id') : array();
							$target_employee = array();
							$target_employee = $this->Employee->find('list', array(
								'recursive' => -1,
								'conditions' => array(
									'id' => $target_employee_id,
								),
								'fields' => array('id', 'fullname')
							));
							
							$projectTarget = array();
							if(!empty($project_target) ){
								foreach($project_target as $key => $value){
									$projectTarget[$value['ProjectTarget']['type']] = $value['ProjectTarget'];
									$projectTarget[$value['ProjectTarget']['type']]['employee'] = !empty($target_employee[$value['ProjectTarget']['employee_id']]) ? $target_employee[$value['ProjectTarget']['employee_id']] : '';
									$projectTarget[$value['ProjectTarget']['type']]['updated'] = date('d/m/Y H:i', $value['ProjectTarget']['updated']);
								}
							}
							$data['Company']['ProjectTarget'] = $projectTarget;
						}
						break;
					case 'dash_project_type_id': 
						$project_fields[] = 'project_type_id';
						if( empty( $data['Company']['ProjectType'] )){
							$ProjectType = $this->Project->ProjectType->find('list', array(
								'fields' => array('ProjectType.id', 'ProjectType.project_type'), 
								'conditions' => array('ProjectType.company_id' => $company_id),
							));
							$data['Company']['ProjectType'] = $ProjectType;
						}
						$maps[$field] = $data['Company']['ProjectType'];
						break;
					case 'dash_project_sub_type_id':
						$project_fields[] = 'project_sub_type_id';
						if( empty( $data['Company']['ProjectType'] )){
							$ProjectType = $this->Project->ProjectType->find('list', array(
								'fields' => array('ProjectType.id', 'ProjectType.project_type'), 
								'conditions' => array('ProjectType.company_id' => $company_id),
							));
							$data['Company']['ProjectType'] = $ProjectType;
						}
						if( empty( $data['Company']['ProjectSubType'] )){
							$ProjectSubType = $this->Project->ProjectSubType->find('list', array(
								'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 
								'conditions' => array(
									'ProjectSubType.project_type_id' => array_keys($ProjectType)
								),
							));
							$data['Company']['ProjectSubType'] = $ProjectSubType;
						}
						$maps[$field] = $data['Company']['ProjectSubType'];
						break;
					case 'dash_project_sub_sub_type_id': 
						$project_fields[] = 'project_sub_type_id';
						if( empty( $data['Company']['ProjectType'] )){
							$ProjectType = $this->Project->ProjectType->find('list', array(
								'fields' => array('ProjectType.id', 'ProjectType.project_type'), 
								'conditions' => array('ProjectType.company_id' => $company_id),
							));
							$data['Company']['ProjectType'] = $ProjectType;
						}
						if( empty( $data['Company']['ProjectSubType'] )){
							$ProjectSubType = $this->Project->ProjectSubType->find('list', array(
								'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 
								'conditions' => array(
									'ProjectSubType.project_type_id' => array_keys($ProjectType)
								),
							));
							$data['Company']['ProjectSubType'] = $ProjectSubType;
						}
						$ProjectSubSubType = $this->Project->ProjectSubType->find('list', array(
								'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'), 
								'conditions' => array(
									'ProjectSubType.parent_id' => array_keys($ProjectType)
								),
							));
							$data['Company']['ProjectSubSubType'] = $ProjectSubSubType;
							$maps[$field] = $ProjectSubSubType;
						break;
					case 'dash_complexity_id': 
						$project_fields[] = 'complexity_id';
						if( empty( $data['Company']['ProjectComplexity'] )){
							$projectComplexity = $this->Project->ProjectComplexity->find('list', array(
								'fields' => array('ProjectComplexity.id', 'ProjectComplexity.name'), 
								'conditions' => array(
									'ProjectComplexity.company_id ' => $company_id, 
									'ProjectComplexity.display' => 1),
								'order' => 'ProjectComplexity.name ASC'
							));
							$data['Company']['ProjectComplexity'] = $projectComplexity;
						}
						$maps[$field] = $data['Company']['ProjectComplexity'];
						break;
					case 'dash_budget_customer_id': 
						$project_fields[] = 'budget_customer_id';
						$this->loadModel('BudgetCustomer');
						$budgetCustomers = $this->BudgetCustomer->find('list', array(
							'recursive' => -1,
							'conditions' => array('company_id' => $company_id),
							'fields' => array('id', 'name')
						));
						$data['Company']['BudgetCustomers'] = $budgetCustomers;
						$maps[$field] = $data['Company']['BudgetCustomers'];
						break;
					case 'dash_project_phase_id': 
						// $project_fields[] = 'ProjectPhaseCurrent.project_phase_id';
						$projectPhasesCurrent = $this->Project->ProjectPhaseCurrent->find('list', array(
							'recursive' => -1,
							'conditions' => array('ProjectPhaseCurrent.project_id' => $projectIds),
							'fields' => array('id', 'project_phase_id', 'project_id')
						));
						$data['ProjectPhaseCurrent'] = $projectPhasesCurrent;
						$data['multi_fields']['project_phase_id'] = $projectPhasesCurrent;
						$projectPhases = $this->Project->ProjectPhase->find('list', array(
							'fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 
							'conditions' => array(
								'ProjectPhase.company_id' => $company_id,
								'ProjectPhase.activated' => 1
							),
							'order' => 'ProjectPhase.phase_order ASC'
						));
						$data['Company']['ProjectPhases'] = $projectPhases;
						$maps[$field] = $projectPhases;
						break;
					case 'dash_plan_capacity_1':
					case 'dash_plan_capacity_2':
					case 'dash_plan_capacity_3':
					case 'dash_plan_capacity_4':
					case 'dash_plan_capacity_5':
					case 'dash_plan_capacity_6':
					case 'dash_plan_capacity_7':
					case 'dash_plan_capacity_8':
					case 'dash_plan_capacity_9':
					case 'dash_plan_capacity_10':
						$planPC = $this->_getProfits(false, true);
						$planListPC = array();
						if(!empty($planPC)){
							foreach($planPC as $pc_id => $pc_name){
								$planListPC[] = array(
									'id' => $pc_id,
									'name' => str_replace('--', '&nbsp;&nbsp;&nbsp;|-&nbsp;', $pc_name)
								);
							}
						}						
						$data['Company']['PlanPC'] = $planListPC;
						if( empty( $data['Company']['ProfitCenterManager'])){
							$pc_manager = $this->ProfitCenter->find('list', array(
								'recursive' => -1,
								'conditions' => array(
									'company_id' => $this->employee_info['Company']['id'],
								),
								'fields' => array('id', 'manager_id')
							));
							$data['Company']['ProfitCenterManager'] = $pc_manager;
						}
						break;

				}
			}
			// $this->Project->contain('ProjectListMultiple');
			$projects = $this->Project->find('all', array(
				'recursive' => -1,
				'fields' => $project_fields,
				'conditions' => array(
					'Project.id' => $projectIds,
					'Project.company_id' => $company_id,
				),
				'joins' => array_values($joins),
			));
			// debug( $projects); exit;
			// debug( $projectPhasesCurrent); exit;
			// $a = time();
			$projects = !empty( $projects) ? Set::combine($projects, '{n}.Project.id', '{n}') : array();
			// debug( $projects); exit;
			if( !empty( $projectEmployeeManager)){
				foreach( $projectIds as $p_id){
					$pms = !empty( $projectEmployeeManager[$p_id]) ? $projectEmployeeManager[$p_id] : array();
					$pms = array_values($pms );
					$pm = !empty($projects[$p_id]['Project']['project_manager_id']) ? $projects[$p_id]['Project']['project_manager_id'] : '';
					unset($projects[$p_id]['Project']['project_manager_id']);
					$pms[] = $pm;
					$projectEmployeeManager[$p_id] = array_unique($pms);
				}
				$data['ProjectEmployeeManager'] = $projectEmployeeManager;
			}
			// debug( $a - time()); exit;
			$data['Projects'] = $projects;
			// $data['ProjectAmrs'] = 'working';
			$data['maps'] = $maps;
			
			$result = true;
			
		}
		die(json_encode(array(
			'result' => $result ? 'success' : 'failed',
			'message' => $message,
			'data' => $data
		)));
	}
	function _getProfits($profitId,$getDataByPath = true) {
		if (!($user = $this->_getEmpoyee())) {
			return false;
		}
		$Model = ClassRegistry::init('ProfitCenter');
		//modify by QN, enable control_resource for PM
		$canManageResource = $this->employee_info['CompanyEmployeeReference']['role_id'] == 3 && $this->employee_info['CompanyEmployeeReference']['control_resource'];
		$myPC = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
			'conditions' => array(
				'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id IS NULL'),
				'NOT' => array('ProjectEmployeeProfitFunctionRefer.profit_center_id' => 0),
				'employee_id' => $user['id']
			)
		));
		$conds = array('manager_id' => $user['id']);
		if( $canManageResource )$conds['id'] = $myPC['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
		$profit = $Model->find('list', array(
			'recursive' => -1, 'fields' => array('id'),
			'order' => array('lft' => 'ASC'),
			'conditions' => array(
				'company_id' => $user['company_id'],
				'OR' => $conds
			)
		));
		$this->loadModels('ProfitCenterManagerBackup');
		$backups = $this->ProfitCenterManagerBackup->find('list', array(
			'recursive' => -1,
			'conditions' => array('employee_id' => $user['id']),
			'fields' => array('profit_center_id', 'profit_center_id')
		));
		$profit = array_unique(array_merge($profit, $backups));
		if(empty($profit)){
			$profit = $Model->find('list', array(
				'recursive' => -1, 'fields' => array('id'),
				'order' => array('lft' => 'ASC'),
				'conditions' => array(
					'company_id' => $user['company_id']
				)
			));
		}
		$isAdmin = $this->employee_info['Role']['name'] == 'admin' || $this->employee_info['Role']['name'] == 'hr';
		if ($isAdmin) {
			$paths = array();
			$pc_list = $Model->getTreeList($user['company_id']);
			if(!empty($pc_list)){
				foreach($pc_list as $id => $value){
					if(!empty($value['name'])){
						$name = explode("|",  $value['name']);
						$paths[$id] = $name[0];
					}
				}
			}
		} elseif (!empty($profit)) {
			$paths = array();
			foreach($profit as $_val)
			{
				$paths[] = $_val;
				$pathsTemp = $Model->children($_val);
				$pathsTemp = Set::classicExtract($pathsTemp,'{n}.ProfitCenter.id');
				$paths = array_merge($paths,$pathsTemp);
			}
			$paths = $Model->generatetreelist(array('id' => $paths), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;', -1);
		} else {
			return false;
		}
		
		return $paths;
	}
	/* 
	 * function save_history_sorting
	 * Save custom order by employee
	 * @access: pm, admin
	 * @History: Ticket 3811
	 */
	public function save_history_sorting(){
		$result =  false;
		$data = array();
		$message = '';
		$custom_order_projects = $this->data;
		$path = 'custom_order_projects';
		$employee_id = $this->employee_info['Employee']['id'];
		$this->loadModels('HistoryFilter');
		$last_order = $this->HistoryFilter->find('first', array(
				'recursive' => -1,
				'fields' => array(
					'id', 'params'
				),
				'conditions' => array(
					'path' => $path,
					'employee_id' => $employee_id
				)
			));
			
		if (empty($last_order)){
			$this->HistoryFilter->create();
		}else{
			$this->HistoryFilter->id = $last_order['HistoryFilter']['id'];
			$history_order = json_decode($last_order['HistoryFilter']['params'], true);
			$history_order = is_array($history_order) ? $history_order : array();
			$custom_order_projects = array_unique( array_merge( $custom_order_projects, $history_order));
		}
		
		/* Filter project ID */
		$project = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id']
			),
			'fields' => array('id', 'id')
		));
		$project = !empty( $project) ? array_values($project) : array();
		$custom_order_projects = array_values(array_intersect( $custom_order_projects, $project));
		$result = $this->HistoryFilter->save( 
			array(
				'path' => $path,
				'params' => json_encode($custom_order_projects),
				'employee_id' => $employee_id
			),
			array(
				'validate' => false,
				'callbacks' => false
			)
		);
		die(json_encode(array(
			'result' => $result ? 'success' : 'failed',
			'message' => $message,
			'data' => $custom_order_projects
		)));
	}
}

