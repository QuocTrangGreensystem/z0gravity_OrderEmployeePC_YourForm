<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class UserViewsPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('UserView', 'SaleLead', 'Project', 'ProjectPhase', 'Company', 'ProjectFunction', 'ProjectAmr', 'UserDefaultView', 'ProjectAmrProgram', 'ProjectAmrSubProgram', 'UserStatusView','UserStatusViewActivity','ActivityColumn', 'UserStatusViewSale', 'UserStatusViewSaleDeal', 'Ticket');

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'UserViewsPreview';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml');


    /**
     * index
     *
     * @return void
     * @access public
     */
    public function index() {
        $model=(isset($this->params['url']['model'])&&!empty($this->params['url']['model']))?$this->params['url']['model']:'project';
        if ($this->is_sas) {
            $userViews = $this->UserView->find('all', array(
                'recursive' => 0,
                'fields' => array('UserView.id', 'UserView.name', 'UserView.mobile', 'UserView.description', 'UserView.created_date', 'UserView.public', 'UserView.employee_id', 'Employee.first_name', 'Employee.last_name', 'Employee.avatar'),
                'conditions' => array('UserView.model'=>$model)));
        } else {
            $userViews = $this->UserView->find('all', array(
                'recursive' => 0,
                'fields' => array('UserView.id', 'UserView.name', 'UserView.mobile', 'UserView.description', 'UserView.created_date', 'UserView.public', 'UserView.employee_id', 'Employee.first_name', 'Employee.last_name', 'Employee.avatar'),
                'order' => 'UserView.public ASC',
                'group' => 'UserView.id',
                'conditions' => array(
                    'UserView.model'=>$model,
					'UserView.company_id' => $this->employee_info["Company"]["id"],
					'Employee.company_id' => $this->employee_info["Company"]["id"],
                    'OR' => array(
                        'UserView.employee_id' => $this->employee_info['Employee']['id'],
                        array(
                            'UserView.company_id' => $this->employee_info["Company"]["id"],
                            'UserView.public' => true
                    )))));
        }
		
        $defaultView = $this->UserDefaultView->find('first', array(
            'recursive' => -1,
            'conditions' => array('model'=>$model,'employee_id' => $this->employee_info["Employee"]["id"]),
            'fields' => array('user_view_id')));
        if($model=='project') {
            $statusView = $this->UserStatusView->find('all', array(
                'recursive' => -1,
                'conditions' => array('employee_id' => $this->employee_info["Employee"]["id"]),
                'fields' => array('user_view_id', 'progress_view', 'oppor_view', 'archived_view', 'model_view', 'mobile')));

            $statusView = !empty($statusView) ? Set::combine($statusView, '{n}.UserStatusView.user_view_id', '{n}.UserStatusView') : array();
			
			$this->loadModel('CompanyViewDefault');
			$conpanyDefaultView = $this->CompanyViewDefault->find('all', array(
				'recursive' => -1,
				'conditions' => array('company_id' => $this->employee_info["Company"]["id"]),
				'fields' => array('*')));
			$conpanyDefaultView = !empty($conpanyDefaultView) ? Set::combine($conpanyDefaultView, '{n}.CompanyViewDefault.user_view_id', '{n}.CompanyViewDefault') : array();
			
        } else if($model=='business') {
            $statusView = $this->UserStatusViewSale->find('all', array(
                'recursive' => -1,
                'conditions' => array('employee_id' => $this->employee_info["Employee"]["id"]),
                'fields' => array('user_view_id','open', 'closed_won', 'closed_lose')));
            $statusView = !empty($statusView) ? Set::combine($statusView, '{n}.UserStatusViewSale.user_view_id', '{n}.UserStatusViewSale') : array();
        } else if($model=='deal') {
            $statusView = $this->UserStatusViewSaleDeal->find('all', array(
                'recursive' => -1,
                'conditions' => array('employee_id' => $this->employee_info["Employee"]["id"]),
                'fields' => array('user_view_id','open', 'archived', 'renewal')));
            $statusView = !empty($statusView) ? Set::combine($statusView, '{n}.UserStatusViewSaleDeal.user_view_id', '{n}.UserStatusViewSaleDeal') : array();
        } else {
            $statusView = $this->UserStatusViewActivity->find('all', array(
                'recursive' => -1,
                'conditions' => array('employee_id' => $this->employee_info["Employee"]["id"]),
                'fields' => array('user_view_id','activated', 'not_activated', 'activated_and_not_activated')));
            $statusView = !empty($statusView) ? Set::combine($statusView, '{n}.UserStatusViewActivity.user_view_id', '{n}.UserStatusViewActivity') : array();
        }
        $isAdmin = (isset($this->employee_info['Role']['name']) && $this->employee_info['Role']['name'] === 'admin');
        $company_id = $this->employee_info['Company']['id'];
        $this->set(compact('defaultView', 'userViews', 'isAdmin', 'statusView','model', 'company_id', 'conpanyDefaultView'));
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function toggle($model='project',$viewId = null, $switch = null) {
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['OR'] = array(
                'UserView.employee_id' => $this->employee_info["Employee"]["id"],
                array(
                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                    'UserView.public' => true
                )
            );
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'fields' => array('name'), 'conditions' => $conditions));
        if (!$userView) {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
        }
		else{
			$defaultView = $this->UserDefaultView->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'model'=>$model,
					'employee_id' => $this->employee_info["Employee"]["id"]
				),
				//'fields' => array('id', 'user_view_id')
			));
			
				// debug($defaultView); exit;
			$defaultView = $defaultView['UserDefaultView'];
			$defaultView['employee_id'] = isset( $defaultView['employee_id'] ) ? $defaultView['employee_id'] : $this->employee_info["Employee"]["id"]; 
			$defaultView['model'] = isset( $defaultView['model'] ) ? $defaultView['model'] : $model; 
			
			$defaultView['user_view_id'] = ( isset ($defaultView['user_view_id']) && $defaultView['user_view_id'] == $viewId ) ? 0 : $viewId;
			$defaultView['updated'] = time();
			$this->UserDefaultView->create();	
			if ( isset ($defaultView['id'] ) ) {
                $this->UserDefaultView->id = $defaultView['id'];
            }
			$toggle_data = $this->UserDefaultView->save($defaultView);
			if ($toggle_data ) {
				$this->Session->setFlash(__('Saved', true), 'success');
				$result = true;
			}
			else{
				$this->Session->setFlash(__('The user default view could not set as default. Please, try again.', true), 'error');
			}
				
		}
        if( $is_ajax ){
			$fields = 'default_view';
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}
        $this->redirect(array('action' => 'index?model='.$model));
    }

    public function toggle_mobile($id = null, $value = null){
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
		
        $model = 'project';
        $eid = $this->employee_info["Employee"]["id"];
        $userView = $this->UserView->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $id
            )
        ));
        if( !$userView ){
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $id . '"</b>'), 'error');
        }else{
			
			//set default view
			$statusView = array();
			$statusView = $this->UserStatusView->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'UserStatusView.user_view_id' => $id,
					'UserStatusView.employee_id' => $eid,
				)
			));
			$statusView = $statusView['UserStatusView'];
				// reset another value
				$this->UserStatusView->updateAll(array(
					'UserStatusView.mobile' => 0
				), array(
					'UserStatusView.employee_id' => $eid,
					'UserView.model' => $model
				));
				
				// set new value
				$statusView['employee_id'] = isset( $statusView['employee_id'] ) ? $statusView['employee_id'] : $this->employee_info["Employee"]["id"]; 
				$statusView['user_view_id'] = isset( $statusView['user_view_id'] ) ? $statusView['user_view_id'] : $id; 
				
				
				$this->UserDefaultView->create();
				if( isset ($statusView['id']) ){
					$this->UserDefaultView->id = $statusView['id'];
				}
				$new_value = !empty($statusView['mobile']) ? 0 : 1;
				$new_value = ($value !== null) ? $value : $new_value;
				$statusView['mobile'] = $new_value;
				$toggle_data = $this->UserStatusView->save($statusView);
				if ($toggle_data ) {
					$this->Session->setFlash(__('Saved', true), 'success');
					$result = true;
				}else{
					$this->Session->setFlash(__('The user default view could not set as default. Please, try again.', true), 'error');
				}
				
				
			$this->Session->setFlash(__('Saved', true));
			if( $is_ajax ){
				$fields = 'default_mobile_view';
				$this->set(compact('result','fields', 'toggle_data'));
				$this->render('index_ajax');
				die;
			}
			$this->redirect(array('action' => 'index?model=' . $model));
		}
    }

    /**
     * index
     *
     * @return void
     * @access public
		// This field is not display, so I do not update yet
     */
    public function toggle_public($model='project',$viewId = null, $switch = null) {
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
		
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['UserView.employee_id'] = $this->employee_info["Employee"]["id"];
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'conditions' => $conditions));
        if (!$userView || $this->employee_info["Role"]["name"] !== 'admin') {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist or you can\'t edit this item', true), '<b>"' . $viewId . '"</b>'), 'error');
			$data['result'] = $result;
			if( $is_ajax ) die(json_encode($data) );
        }else{
			$this->UserView->id = $viewId;
			$public = $userView['UserView']['public'] ? 0 : 1;
			$public = $switch !== null ? $switch : $public;
			$toggle_data = $this->UserView->saveField('public', $public);
			if (!empty($toggle_data)) {
				if( !empty($public)){
					$this->Session->setFlash(__('Saved', true));
					$result = true;
				} else {
					$this->UserDefaultView->deleteAll(array(
						'UserDefaultView.user_view_id' => $viewId,
						'NOT' => array(
							'UserDefaultView.employee_id' => $this->employee_info["Employee"]["id"]
						),
					));
					$this->Session->setFlash(__('Saved', true));
					$result = true;
				}
			} else {
				$this->Session->setFlash(__('The user default view could not set as public view. Please, try again.', true), 'error');
			}
		}
		if( $is_ajax ){
			$fields = 'public';
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}
        $this->redirect(array('action' => 'index?model='.$model));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('You are not the owner of this view', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('userView', $this->UserView->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    public function add() {
        $model=(isset($this->params['url']['model'])&&!empty($this->params['url']['model']))?$this->params['url']['model']:'project';
		$role = $this->employee_info['Role']['name'];
		$resetRole = !empty($this->employee_info['CompanyEmployeeReference']['role_id']) ? $this->employee_info['CompanyEmployeeReference']['role_id'] : 4;
        $seeBudgetPM = !empty($this->employee_info['CompanyEmployeeReference']['see_budget']) ? $this->employee_info['CompanyEmployeeReference']['see_budget'] : 0;
		
		$EPM_see_the_budget = isset($this->companyConfigs['EPM_see_the_budget']) || !empty($this->companyConfigs['EPM_see_the_budget']) ?  true : false;
		$canSeeBudget = ($resetRole == 3 && ((!$EPM_see_the_budget || !$seeBudgetPM))) ? false : true;
        $this->loadModels('ProjectBudgetSyn', 'Translation', 'CompanyConfig', 'Menu', 'ProjectFinanceTwoPlus', 'ProfileProjectManagerDetail');
        $financeFields = $financeFieldPlus = $financeFieldTwoPlus = array();
		$tranBud = $enableKPI = false;
        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model' => 'project'
            ),
            'fields' => array('widget_id', 'display', 'name_eng', 'name_fre')
        ));
        $showMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', '{n}.Menu.display') : array();
		$othersField = array();
		$LANG = Configure::read('Config.language');
        if($model=='project') {
            $this->loadModel('ProjectFinance');
            $budgetFields = $this->ProjectBudgetSyn->get();
            $budgetFields['others']['ProjectAmr.manual_consumed'] = 'Manual Consumed';
            $valueFields = array();
            $isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
            // set lai
            if($isProfileManager != 0 ){
                $enableFinance = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index',
                        'widget_id' => 'finance'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinance) && $enableFinance['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinancePlus = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index_plus',
                        'widget_id' => 'finance_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinancePlus) && $enableFinancePlus['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFieldPlus = $this->ProjectFinance->defaultFieldPlus($this->employee_info["Company"]["id"]);
                }
                $enableFinanceTwoPlus = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'plus',
                        'widget_id' => 'finance_two_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinanceTwoPlus) && $enableFinanceTwoPlus['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFieldTwoPlus = $this->ProjectFinanceTwoPlus->defaultField($this->employee_info["Company"]["id"]);
                }
				$enableWidgets = $this->ProfileProjectManagerDetail->find('list', array(
					'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
						'display' => 1,
                    ),
                    'fields' => array('widget_id',  'name_'.$LANG)
                ));
            } else {
                $enableFinance = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index',
                        'widget_id' => 'finance'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinance) && $enableFinance['Menu']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinancePlus = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index_plus',
                        'widget_id' => 'finance_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinancePlus) && $enableFinancePlus['Menu']['display'] == 1){
                    $financeFieldPlus = $this->ProjectFinance->defaultFieldPlus($this->employee_info["Company"]["id"]);
                }
                if(!empty($enableFinance) && $enableFinance['Menu']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinanceTwoPlus = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'plus',
                        'widget_id' => 'finance_two_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinanceTwoPlus) && $enableFinanceTwoPlus['Menu']['display'] == 1){
                    $financeFieldTwoPlus = $this->ProjectFinanceTwoPlus->defaultField($this->employee_info["Company"]["id"]);
                }
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
			if( isset($enableWidgets['phase']) ){
				$othersField['ProjectWidget.Phase'] = $enableWidgets['phase'];
			}
			if( isset($enableWidgets['milestone']) ){
				$othersField['ProjectWidget.Milestone'] = $enableWidgets['milestone'];
			}
			if( isset($enableWidgets['finance_plus']) && $canSeeBudget){
				$othersField['ProjectWidget.FinancePlus'] = $enableWidgets['finance_plus'];
			}
			if( isset($enableWidgets['synthesis']) && $canSeeBudget){
				$othersField['ProjectWidget.Synthesis'] = $enableWidgets['synthesis'];
			}
			if( isset($enableWidgets['internal_cost']) && $canSeeBudget){
				$othersField['ProjectWidget.InternalBudget'] = $enableWidgets['internal_cost'];
			}
			if( isset($enableWidgets['external_cost']) && $canSeeBudget){
				$othersField['ProjectWidget.ExternalBudget'] = $enableWidgets['external_cost'];
			}
            //remove nhung truong da not show trong Translation cua project detail va KPI.

			$enableKPI = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $this->employee_info['Company']['id'],
                    'model' => 'project',
                    'widget_id' => array('kpi', 'indicator'),
                    'display' => 1,
                ),
                'order' => array('id' => 'DESC')
            )); 
            //if(!empty($enableKPI) && $enableKPI['Menu']['display'] == 1){
            $datas = $this->Translation->find('all', array(
                'conditions' => array(
                    'page' => array('Details'),
					'TranslationSetting.company_id' => $this->employee_info['Company']['id']
                ),
                'fields' => array('*', 'CASE
                        WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
                        WHEN TranslationSetting.setting_order IS NULL THEN 999
                    END as custom_order'
                ),
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
                'order' => array(
                    'custom_order' => 'ASC'
                )
            ));
			
            if(!empty($datas)){
                foreach ($datas as $data) {
                    if(!empty($data['Translation']['field'])){
                        $key = 'Project.' . $data['Translation']['field'];
                        // remove pictures
                        if($key == 'Project.pictures') continue;
                        if($data['TranslationSetting']['show'] == 1) $projectFields[$key] = $data['Translation']['original_text'];
                    }
                }
            }
			$progress_method = isset($this->companyConfigs['project_progress_method'] ) ? $this->companyConfigs['project_progress_method'] : '';
			if( $progress_method != 'no_progress'){
				$projectFields['ProjectWidget.Progress'] = __('% Progress',true);
			}
			$projectFields['Project.created'] = __('Project creation date',true);
			$projectFields['Project.category'] = __('Project Status',true);
			$projectFields['Project.updated_opp_ip'] = __('Date Opportunity to In progress',true);
			$projectFields['Project.updated_ip_arch'] = __('Date In progres to Archived',true);
            // KPI
            $datas = $this->Translation->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'page' => array('KPI')
                ),
                'fields' => array('*')
            ));

            $amrFields = array(
                'ProjectAmr.id' => 'Id',
                'ProjectAmr.project_id' => 'Project',
            );
            if(!empty($datas)){
                foreach ($datas as $data) {
                    if(!empty($data['Translation']['field'])){
                        $key = 'ProjectAmr.' . $data['Translation']['field'];
                        $amrFields[$key] = $data['Translation']['original_text'];
                    }
                }
            }
             $default = array(
                'customer_point_of_view|0',
                'comment|0',
                'done|0',
                'to_do|0',
                'planning|0',
                'progress|0',
                'budget|0',
                'staffing|0',
                'acceptance|0',
                'risk|0',
                'issue|0',
                'log_comment|0'
            );
            $kpi = json_decode($this->companyConfigs['kpi_settings']);
            $new = array();
            if( isset($this->companyConfigs['kpi_settings']) ){
                $raw = json_decode($this->companyConfigs['kpi_settings']);
                $new = array_merge($this->arrList($default) , $this->arrList($raw));
            }
            foreach ($new as $kpi => $value) {
                $key = 'ProjectAmr.' . $kpi;
                // doan nay doi key
                switch ($kpi) {
                    case 'to_do':
                        $key = 'ProjectAmr.todo';
                        break;
                    case 'comment':
                        $key = 'ProjectAmr.project_amr_solution';
                        break;
                    case 'risk':
                        if($value == 0){
                            unset($amrFields['ProjectAmr.project_amr_risk_information']);
                            unset($amrFields['ProjectAmr.risk_control_weather']);
                        }
                        break;
                    case 'planning':
                        $key = 'ProjectAmr.planning_weather';
                        break;
                    case 'progress':
                        $key = 'ProjectAmr.project_amr_progression';
                        break;
                    case 'staffing':
                        $key = 'ProjectAmr.organization_weather';
                        break;
                    case 'issue':
                        if($value == 0){
                            unset($amrFields['ProjectAmr.project_amr_problem_information']);
                            unset($amrFields['ProjectAmr.issue_control_weather']);
                        }
                        break;
                }
                if($value == 0 && isset($kpi[$key])) unset($amrFields[$key]);
            }
            
            $_list = array('ProjectAmr.organization_weather','ProjectAmr.delay','ProjectAmr.id', 'ProjectAmr.project_id', 'ProjectAmr.cost_control_weather', 'ProjectAmr.project_amr_solution_description', 'ProjectAmr.updated', 'ProjectAmr.created'); 
            foreach ($_list as $value) {
                unset($amrFields[$value]);
            }

            if(isset($amrFields['ProjectAmr.md_validated'])){
                $budgetFields['others']['ProjectAmr.md_validated'] = $amrFields['ProjectAmr.md_validated'];
                unset($amrFields['ProjectAmr.md_validated']);
            }
            // remove budgetFields
            if($isProfileManager != 0 ){
                $idOfBudget = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'model_id' => $isProfileManager, 'name_eng' => 'Budget'),
                    'fields' => array('id'),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($idOfBudget) && $idOfBudget['ProfileProjectManagerDetail']['id']){
                    $listBudget = $this->ProfileProjectManagerDetail->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id'],
                            'model_id' => $isProfileManager,
                            'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                            'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                            'functions' => 'index',
                            'widget_id NOT' => null
                        ),
                        'fields' => array('controllers', 'display'),
                        'order' => array('id' => 'DESC')
                    ));
                    if(!empty($listBudget)){
                        foreach ($listBudget as $key => $value) {
                            switch ($key) {
                                case 'project_budget_sales':
                                    if($value == 0) unset($budgetFields['sales']);
                                    break;
                                case 'project_budget_internals':
                                    if($value == 0) unset($budgetFields['internal']);
                                    break;
                                case 'project_budget_externals':
                                    if($value == 0) unset($budgetFields['external']);
                                    break;
                                case 'project_budget_provisionals':
                                    if($value == 0) unset($budgetFields['provisional']);
                                    break;
                                case 'project_budget_purchases':
                                    if($value == 0) unset($budgetFields['purchases']);
                                    break;
                            }
                        }
                    }
                    $menuLists = array('sales','internal','external','provisional','purchases');
                    foreach ($menuLists as $menuList) {
                       if(!empty($showMenu[$menuList])){
                            unset($budgetFields[$menuList]);
                       }
                    }
                    $othersField += $budgetFields['others'];
                    unset($budgetFields['others']);
                }
                $langCode = Configure::read('Config.langCode');
                $fields = ($langCode == 'fr') ? array('controllers', 'name_fre') : array('controllers', 'name_eng');
                $tranBud = $this->ProfileProjectManagerDetail->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'model_id' => $isProfileManager,
                        'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                        'functions' => 'index',
                        'widget_id NOT' => null
                    ),
                    'fields' => $fields,
                    'order' => array('id' => 'DESC')
                ));
            } else {
                $idOfBudget = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'model' => 'project', 'name_eng' => 'Budget'),
                    'fields' => array('id'),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($idOfBudget) && $idOfBudget['Menu']['id']){
                    $listBudget = $this->Menu->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id'],
                            'model' => 'project',
                            'parent_id' => $idOfBudget['Menu']['id'],
                            'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                            'functions' => 'index',
                            'widget_id NOT' => null
                        ),
                        'fields' => array('controllers', 'display'),
                        'order' => array('id' => 'DESC')
                    ));
                    if(!empty($listBudget)){
                        foreach ($listBudget as $key => $value) {
                            switch ($key) {
                                case 'project_budget_sales':
                                    if($value == 0) unset($budgetFields['sales']);
                                    break;
                                case 'project_budget_internals':
                                    if($value == 0) unset($budgetFields['internal']);
                                    break;
                                case 'project_budget_externals':
                                    if($value == 0) unset($budgetFields['external']);
                                    break;
                                case 'project_budget_provisionals':
                                    if($value == 0) unset($budgetFields['provisional']);
                                    break;
                                case 'project_budget_purchases':
                                    if($value == 0) unset($budgetFields['purchases']);
                                    break;
                            }
                        }
                    }

                    $menuLists = array('sales','internal','external','provisional','purchases');
                    foreach ($menuLists as $menuList) {
                       if(!empty($showMenu[$menuList]) && $showMenu[$menuList]){
                            unset($budgetFields[$menuList]);
                       }
                    }
                    $othersField += $budgetFields['others'];
                    unset($budgetFields['others']);
                }
                $langCode = Configure::read('Config.langCode');
                $fields = ($langCode == 'fr') ? array('controllers', 'name_fre') : array('controllers', 'name_eng');
                $tranBud = $this->Menu->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'model' => 'project',
                        'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                        'functions' => 'index',
                        'widget_id NOT' => null
                    ),
                    'fields' => $fields,
                    'order' => array('id' => 'DESC')
                ));
            }
        } elseif($model=='business') {
            $projectFields = $this->SaleLead->getViewFieldNames();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } elseif($model=='deal') {
            $projectFields = $this->SaleLead->getViewFieldNameForDeals();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } elseif($model=='ticket') {
            $projectFields = $this->Ticket->getViewFieldNames();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } else {
            $projectFields = $this->ActivityColumn->getViewFieldNames($this->employee_info["Company"]["id"]);
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        }
        $isAdmin = (!empty($this->employee_info['Role']) && $this->employee_info['Role']['name'] === 'admin');
        $enableProjectDetails =  $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model' => 'project',
                'widget_id' => 'details',
                'display' => 1,
            ),
            'fields' => 'display',
            'order' => array('id' => 'DESC')
        ));  

        $menuLists = array('sale','internal_cost','external_cost','provisional','purchase', 'task');
        foreach ($menuLists as $menuList) {
            switch ($menuList) {
                case 'sale':
                    if(isset($showMenu['sale']) && $showMenu['sale'] == 0) unset($budgetFields['sales']);
                    break;
                case 'internal_cost':
                    if(isset($showMenu['internal_cost']) && $showMenu['internal_cost'] == 0) unset($budgetFields['internal']);
                    break;
                case 'external_cost':
                    if(isset($showMenu['external_cost']) && $showMenu['external_cost'] == 0) unset($budgetFields['external']);
                    break;
                case 'provisional':
                    if(isset($showMenu['provisional']) && $showMenu['provisional'] == 0) unset($budgetFields['provisional']);
                    break;
                case 'purchase':
                    if(isset($showMenu['purchase']) && $showMenu['purchase'] == 0) unset($budgetFields['purchases']);
                    break;
                case 'task':
                    if(isset($showMenu['task']) && $showMenu['task'] == 0) unset($budgetFields['Task']);
                    break;
            }
        }
        $ex_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'External_Cost',
                array('external_cost')
            )
        ));
        $in_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'Internal_Cost',
                array('internal_cost')
            )
        ));
        $sale_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'Sales',
                array('sales')
            )
        ));
        foreach ($budgetFields as $key => $value) {
            switch ($key) {
                case 'sales':
                    if(!isset($showMenu['sale']) || !$showMenu['sale']){
                        unset($budgetFields['sales']);
                    }else{
                        if(!isset($sale_settings['sold'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_sold']);
                        if(!isset($sale_settings['billed'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_to_bill']);
                        if(!isset($sale_settings['billed_check'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_billed']);
                        if(!isset($sale_settings['paid'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_paid']);
                        if(!isset($sale_settings['man_day'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_man_day']);
                    }
                    break;
                case 'purchases':
                    if(!isset($showMenu['purchase']) || !$showMenu['purchase']) unset($budgetFields['purchases']); 
                    break;
                case 'Task':
                    if(!isset($showMenu['task']) || !$showMenu['task']) unset($budgetFields['Task']); 
                    unset($budgetFields['others']['ProjectBudgetSyn.workload']);
                    break;
                case 'internal':
                    if(!isset($showMenu['internal_cost']) || !$showMenu['internal_cost']){
                        unset($budgetFields['internal']);
                    }else{
                        if(!isset($in_settings['budget_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_budget']);
                        if(!isset($in_settings['budget_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_budget_man_day']);
                        if(!isset($in_settings['forecast_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_forecast']);
                        if(!isset($in_settings['var_percent'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_var']);
                        if(!isset($in_settings['engaged_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_engaged']);
                        if(!isset($in_settings['engaged_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_engaged_md']);
                        if(!isset($in_settings['forecast_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_forecasted_man_day']);
                        if(!isset($in_settings['remain_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_remain']);
                        if(!isset($in_settings['average'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_average']);
                    } 
                    break;
                case 'external':
                    if(!isset($showMenu['external_cost']) || !$showMenu['external_cost']) {
                        unset($budgetFields['external']);
                    }else{
                        if(!isset($ex_settings['budget_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_budget']);
                        if(!isset($ex_settings['forecast_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_forecast']);
                        if(!isset($ex_settings['var_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_var']);
                        if(!isset($ex_settings['ordered_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_ordered']);
                        if(!isset($ex_settings['remain_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_remain']);
                        if(!isset($ex_settings['man_day'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_man_day']);
                        if(!isset($ex_settings['progress_md'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_progress']);
                        if(!isset($ex_settings['progress_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_progress_euro']);
                    }
                    break;
                case 'provisional':
                    if(!isset($showMenu['provisional']) || !$showMenu['provisional']) unset($budgetFields['provisional']); 
                    break;
                case 'others':
                    if(isset($showMenu['internal_cost']) && isset($showMenu['external_cost']) && $showMenu['internal_cost'] == 0 && $showMenu['external_cost'] == 0){
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_budget']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_forecast']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_var']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_engaged']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_remain']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_man_day']);
                         unset($budgetFields['others']['ProjectBudgetSyn.roi']);
                    }
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Workload') == 0)){
                        unset($budgetFields['others']['ProjectBudgetSyn.workload']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_thr_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_thr_y']);
                    } 
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Consumed') == 0)){
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_thr_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_thr_y']);
                        
                    }
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Manual Consumed') == 0)){
                        unset($budgetFields['others']['ProjectAmr.manual_consumed']);
                    } 
                    if(!isset($showMenu['task']) || !$showMenu['task']){
                        unset($budgetFields['others']['ProjectBudgetSyn.assign_to_profit_center']);
                        unset($budgetFields['others']['ProjectBudgetSyn.assign_to_employee']);
                        unset($budgetFields['others']['ProjectBudgetSyn.overload']);
                    }
                    break;
           }
        }
        $this->set(compact('projectFields', 'amrFields', 'budgetFields', 'othersField', 'isAdmin','valueFields', 'financeFields', 'financeFieldTwoPlus', 'financeFieldPlus', 'canSeeBudget', 'tranBud', 'enableKPI', 'enableProjectDetails' ));
        if (!empty($this->data)) {
            $this->data['UserView']['content'] = Set::sort($this->data['UserView']['content'], '{n}.weight', 'asc');
            $this->data['UserView']['content'] = serialize(Set::classicExtract($this->data['UserView']['content'], '{n}.value'));
            $this->data['UserView']['created_date'] = date("Y-m-d");
            $this->data['UserView']['employee_id'] = $this->employee_info['Employee']['id'];
            $this->data['UserView']['company_id'] = $this->employee_info["Company"]["id"];
            $this->data['UserView']['gantt_view'] = 0;
            if(!empty($this->data['UserView']['initial']) || !empty($this->data['UserView']['real']) || !empty($this->data['UserView']['stones'])){
                $this->data['UserView']['gantt_view'] = 1;
            }
            if ($this->UserView->save($this->data)) {
                if($model=='project') {
                    if(!empty($this->data['UserStatusView'])){
                        $this->UserStatusView->create();
                        $saved = array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'user_view_id' => $this->UserView->getInsertID(),
                            'progress_view' => $this->data['UserStatusView']['progress_view'],
                            'oppor_view' => $this->data['UserStatusView']['oppor_view'],
                            'archived_view' => $this->data['UserStatusView']['archived_view'],
                            'model_view' => $this->data['UserStatusView']['model_view']
                        );
                        $this->UserStatusView->save($saved);
                    }
                } elseif($model=='business') {
                    if(!empty($this->data['UserStatusViewSale'])){
                        $this->UserStatusViewSale->create();
                        $open = $closed_won = $closed_lose = 0;
                        switch($this->data['UserStatusViewSale']['lead_status']){
                            case 1: {
                                $closed_won = 1;
                                break;
                            }
                            case 2: {
                                $closed_lose = 1;
                                break;
                            }
                            case 3: {
                                $open = $closed_won = $closed_lose = 1;
                                break;
                            }
                            case 0:
                            default: {
                                $open = 1;
                                break;
                            }
                        }
                        $saved = array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'user_view_id' => $this->UserView->getInsertID(),
                            'open' => $open,
                            'closed_won' => $closed_won,
                            'closed_lose' => $closed_lose
                        );
                        $this->UserStatusViewSale->save($saved);
                    }
                } elseif($model=='deal') {
                    if(!empty($this->data['UserStatusViewSaleDeal'])){
                        $this->UserStatusViewSaleDeal->create();
                        $open = $archived = $renewal = 0;
                        switch($this->data['UserStatusViewSaleDeal']['deal_status']){
                            case 1: {
                                $archived = 1;
                                break;
                            }
                            case 2: {
                                $renewal = 1;
                                break;
                            }
                            case 3: {
                                $open = $archived = $renewal = 1;
                                break;
                            }
                            case 0:
                            default: {
                                $open = 1;
                                break;
                            }
                        }
                        $saved = array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'user_view_id' => $this->UserView->getInsertID(),
                            'open' => $open,
                            'archived' => $archived,
                            'renewal' => $renewal
                        );
                        $this->UserStatusViewSaleDeal->save($saved);
                    }
                } elseif($model=='ticket') {
                    //do nothing
                } else {
                    if(!empty($this->data['UserStatusViewActivity'])){
                        $this->UserStatusViewActivity->create();
                        $saved = array(
                            'employee_id' => $this->employee_info['Employee']['id'],
                            'user_view_id' => $this->UserView->getInsertID(),
                            'activated' => $this->data['UserStatusViewActivity']['activated'],
                            'not_activated' => $this->data['UserStatusViewActivity']['not_activated'],
                            'activated_and_not_activated' => $this->data['UserStatusViewActivity']['activated_and_not_activated']
                        );
                        $this->UserStatusViewActivity->save($saved);
                    }
                }
                if (!empty($this->data['UserView']['default'])) {
                    $this->redirect(array('controller' => 'user_views', 'action' => 'toggle', $model, $this->UserView->getInsertID(), true));
                }
                $this->Session->setFlash(__('Saved', true));
                //$this->Session->setFlash(sprintf(__('The %s view has been saved', true), '<b>"' . $this->data["UserView"]["name"] . '"</b>'), 'success');
                $this->redirect(array('action' => 'index?model='.$model));
            } else {
                $this->Session->setFlash(__('Not saved', true));
            }
        }
        $employees = $this->UserView->Employee->find('list');
        $this->set(compact('employees','model', 'LANG', 'showMenu'));
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    public function edit($id = null) {
        //$model=(isset($this->params['url']['model'])&&!empty($this->params['url']['model']))?$this->params['url']['model']:'project';
        $this->loadModels('ProjectBudgetSyn', 'Translation', 'CompanyConfig', 'Menu', 'ProjectFinanceTwoPlus', 'ProfileProjectManagerDetail');
        $role = $this->employee_info['Role']['name'];
        $employee_id = $this->employee_info['Employee']['id'];
		$resetRole = !empty($this->employee_info['CompanyEmployeeReference']['role_id']) ? $this->employee_info['CompanyEmployeeReference']['role_id'] : 4;
        $seeBudgetPM = !empty($this->employee_info['CompanyEmployeeReference']['see_budget']) ? $this->employee_info['CompanyEmployeeReference']['see_budget'] : 0;
		$EPM_see_the_budget = isset($this->companyConfigs['EPM_see_the_budget']) || !empty($this->companyConfigs['EPM_see_the_budget']) ?  true : false;
		$canSeeBudget = ($resetRole == 3 && ((!$EPM_see_the_budget || !$seeBudgetPM))) ? false : true;
		$userViewDetail=$this->UserView->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('id' => $id)
                ));


        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'model' => 'project'
            ),
            'fields' => array('widget_id', 'display', 'name_eng', 'name_fre')
        ));

        
        $showMenu = !empty($showMenu) ? Set::combine($showMenu, '{n}.Menu.widget_id', '{n}.Menu.display') : array();
        
        if ( !$id || empty($userViewDetail) || ($role != 'admin' && $employee_id != $userViewDetail['UserView']['employee_id'])) {

            $this->Session->setFlash(__('You have not permission to access this function', true));
            $this->redirect(array('action' => 'index'));
        }
        $model=$userViewDetail['UserView']['model'];
        $isAdmin = (!empty($this->employee_info['Role']) && $this->employee_info['Role']['name'] === 'admin');
        $financeFields = $financeFieldPlus = $financeFieldTwoPlus = array();
		$othersField = array();
		$LANG = Configure::read('Config.language');
        if($model=='project') {
            $this->loadModel('ProjectFinance');
            $budgetFields = $this->ProjectBudgetSyn->get();
            $budgetFields['others']['ProjectAmr.manual_consumed'] = 'Manual Consumed';
            $valueFields = array();
            $isProfileManager = !empty($this->employee_info['Employee']['profile_account']) ? $this->employee_info['Employee']['profile_account'] : 0;
            // set lai
            if($isProfileManager != 0 ){
                $enableFinance = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index',
                        'widget_id' => 'finance'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinance) && $enableFinance['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinancePlus = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index_plus',
                        'widget_id' => 'finance_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinancePlus) && $enableFinancePlus['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFieldPlus = $this->ProjectFinance->defaultFieldPlus($this->employee_info["Company"]["id"]);
                }
                if(!empty($enableFinance) && $enableFinance['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinanceTwoPlus = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'plus',
                        'widget_id' => 'finance_two_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinanceTwoPlus) && $enableFinanceTwoPlus['ProfileProjectManagerDetail']['display'] == 1){
                    $financeFieldTwoPlus = $this->ProjectFinanceTwoPlus->defaultField($this->employee_info["Company"]["id"]);
                }
				$enableWidgets = $this->ProfileProjectManagerDetail->find('list', array(
					'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
						'display' => 1,
                    ),
                    'fields' => array('widget_id',  'name_'.$LANG)
                ));
            } else {
                $enableFinance = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index',
                        'widget_id' => 'finance'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinance) && $enableFinance['Menu']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinancePlus = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'index_plus',
                        'widget_id' => 'finance_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinancePlus) && $enableFinancePlus['Menu']['display'] == 1){
                    $financeFieldPlus = $this->ProjectFinance->defaultFieldPlus($this->employee_info["Company"]["id"]);
                }
                if(!empty($enableFinance) && $enableFinance['Menu']['display'] == 1){
                    $financeFields = $this->ProjectFinance->defaultFields($this->employee_info["Company"]["id"]);
                }
                $enableFinanceTwoPlus = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'controllers' => 'project_finances',
                        'functions' => 'plus',
                        'widget_id' => 'finance_two_plus'
                    ),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($enableFinanceTwoPlus) && $enableFinanceTwoPlus['Menu']['display'] == 1){
                    $financeFieldTwoPlus = $this->ProjectFinanceTwoPlus->defaultField($this->employee_info["Company"]["id"]);
                }
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
			if( isset($enableWidgets['phase']) ){
				$othersField['ProjectWidget.Phase'] = $enableWidgets['phase'];
			}
			if( isset($enableWidgets['milestone']) ){
				$othersField['ProjectWidget.Milestone'] = $enableWidgets['milestone'];
			}
			if( isset($enableWidgets['finance_plus']) && $canSeeBudget){
				$othersField['ProjectWidget.FinancePlus'] = $enableWidgets['finance_plus'];
			}
			if( isset($enableWidgets['synthesis']) && $canSeeBudget){
				$othersField['ProjectWidget.Synthesis'] = $enableWidgets['synthesis'];
			}
			if( isset($enableWidgets['internal_cost']) && $canSeeBudget){
				$othersField['ProjectWidget.InternalBudget'] = $enableWidgets['internal_cost'];
			}
			if( isset($enableWidgets['external_cost']) && $canSeeBudget ){
				$othersField['ProjectWidget.ExternalBudget'] = $enableWidgets['external_cost'];
			}
            //remove nhung truong da not show trong Translation cua project detail va KPI.
            $datas = $this->Translation->find('all', array(
                'conditions' => array(
                    'page' => array('Details'),
					'TranslationSetting.company_id' => $this->employee_info['Company']['id']
                ),
                'fields' => array('*', 'CASE
                        WHEN TranslationSetting.setting_order IS NOT NULL THEN TranslationSetting.setting_order
                        WHEN TranslationSetting.setting_order IS NULL THEN 999
                    END as custom_order'
                ),
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
                'order' => array(
                    'custom_order' => 'ASC'
                )
            ));

            if(!empty($datas)){
                foreach ($datas as $data) {
                    if(!empty($data['Translation']['field'])){
                        $key = 'Project.' . $data['Translation']['field'];
                        // remove pictures
                        if($key == 'Project.pictures') continue;
                        if($data['TranslationSetting']['show'] == 1) $projectFields[$key] = $data['Translation']['original_text'];
                    }
                }
            }
			$progress_method = isset($this->companyConfigs['project_progress_method'] ) ? $this->companyConfigs['project_progress_method'] : '';
			if( $progress_method != 'no_progress'){
				$projectFields['ProjectWidget.Progress'] = __('% Progress',true);
			}
			$projectFields['Project.created'] = __('Project creation date',true);
			$projectFields['Project.category'] = __('Project Status',true);
			$projectFields['Project.updated_opp_ip'] = __('Date Opportunity to In progress',true);
			$projectFields['Project.updated_ip_arch'] = __('Date In progres to Archived',true);
            // KPI
            $datas = $this->Translation->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'page' => array('KPI')
                ),
                'fields' => array('*')
            ));

            $amrFields = array(
                'ProjectAmr.id' => 'Id',
                'ProjectAmr.project_id' => 'Project',
            );
            if(!empty($datas)){
                foreach ($datas as $data) {
                    if(!empty($data['Translation']['field'])){
                        $key = 'ProjectAmr.' . $data['Translation']['field'];
                        $amrFields[$key] = $data['Translation']['original_text'];
                    }
                }
            }
             $default = array(
                'customer_point_of_view|0',
                'comment|0',
                'done|0',
                'to_do|0',
                'planning|0',
                'progress|0',
                'budget|0',
                'staffing|0',
                'acceptance|0',
                'risk|0',
                'issue|0',
                'log_comment|0'
            );
            $kpi = json_decode($this->companyConfigs['kpi_settings']);
            $new = array();
            if( isset($this->companyConfigs['kpi_settings']) ){
                $raw = json_decode($this->companyConfigs['kpi_settings']);
                $new = array_merge($this->arrList($default) , $this->arrList($raw));
            }
            foreach ($new as $kpi => $value) {
                $key = 'ProjectAmr.' . $kpi;
                // doan nay doi key
                switch ($kpi) {
                    case 'to_do':
                        $key = 'ProjectAmr.todo';
                        break;
                    case 'comment':
                        $key = 'ProjectAmr.project_amr_solution';
                        break;
                    case 'risk':
                        if($value == 0){
                            unset($amrFields['ProjectAmr.project_amr_risk_information']);
                            unset($amrFields['ProjectAmr.risk_control_weather']);
                        }
                        break;
                    case 'planning':
                        $key = 'ProjectAmr.planning_weather';
                        break;
                    case 'progress':
                        $key = 'ProjectAmr.project_amr_progression';
                        break;
                    case 'staffing':
                        $key = 'ProjectAmr.organization_weather';
                        break;
                    case 'issue':
                        if($value == 0){
                            unset($amrFields['ProjectAmr.project_amr_problem_information']);
                            unset($amrFields['ProjectAmr.issue_control_weather']);
                        }
                        break;
                }
                if($value == 0 && isset($kpi[$key])) unset($amrFields[$key]);
            }
            
            $_list = array('ProjectAmr.organization_weather','ProjectAmr.delay','ProjectAmr.id', 'ProjectAmr.project_id', 'ProjectAmr.cost_control_weather', 'ProjectAmr.project_amr_solution_description', 'ProjectAmr.updated', 'ProjectAmr.created'); 
            foreach ($_list as $value) {
                unset($amrFields[$value]);
            }

            if(isset($amrFields['ProjectAmr.md_validated'])){
                $budgetFields['others']['ProjectAmr.md_validated'] = $amrFields['ProjectAmr.md_validated'];
                unset($amrFields['ProjectAmr.md_validated']);
            }
            // remove budgetFields
            if($isProfileManager != 0 ){
                $idOfBudget = $this->ProfileProjectManagerDetail->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'model_id' => $isProfileManager, 'name_eng' => 'Budget'),
                    'fields' => array('id'),
                    'order' => array('id' => 'DESC')
                ));
                if(!empty($idOfBudget) && $idOfBudget['ProfileProjectManagerDetail']['id']){
                    $listBudget = $this->ProfileProjectManagerDetail->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id'],
                            'model_id' => $isProfileManager,
                            'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                            'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                            'functions' => 'index',
                            'widget_id NOT' => null
                        ),
                        'fields' => array('controllers', 'display'),
                        'order' => array('id' => 'DESC')
                    ));
                    if(!empty($listBudget)){
                        foreach ($listBudget as $key => $value) {
                            switch ($key) {
                                case 'project_budget_sales':
                                    if($value == 0) unset($budgetFields['sales']);
                                    break;
                                case 'project_budget_internals':
                                    if($value == 0) unset($budgetFields['internal']);
                                    break;
                                case 'project_budget_externals':
                                    if($value == 0) unset($budgetFields['external']);
                                    break;
                                case 'project_budget_provisionals':
                                    if($value == 0) unset($budgetFields['provisional']);
                                    break;
                                case 'project_budget_purchases':
                                    if($value == 0) unset($budgetFields['purchases']);
                                    break;
                            }
                        }
                    }
                    $othersField += $budgetFields['others'];
                    unset($budgetFields['others']);
                }
                $langCode = Configure::read('Config.langCode');
                $fields = ($langCode == 'fr') ? array('controllers', 'name_fre') : array('controllers', 'name_eng');
                $tranBud = $this->ProfileProjectManagerDetail->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'model_id' => $isProfileManager,
                        'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                        'functions' => 'index',
                        'widget_id NOT' => null
                    ),
                    'fields' => $fields,
                    'order' => array('id' => 'DESC')
                ));
            } else {
                $idOfBudget = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $this->employee_info['Company']['id'], 'model' => 'project', 'name_eng' => 'Budget'),
                    'fields' => array('id'),
                    'order' => array('id' => 'DESC')
                ));

                if(!empty($idOfBudget) && $idOfBudget['Menu']['id']){
                    $listBudget = $this->Menu->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $this->employee_info['Company']['id'],
                            'model' => 'project',
                            'parent_id' => $idOfBudget['Menu']['id'],
                            'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                            'functions' => 'index',
                            'widget_id NOT' => null
                        ),
                        'fields' => array('controllers', 'display'),
                        'order' => array('id' => 'DESC')
                    ));
                    if(!empty($listBudget)){
                        foreach ($listBudget as $key => $value) {
                            switch ($key) {
                                case 'project_budget_sales':
                                    if($value == 0) unset($budgetFields['sales']);
                                    break;
                                case 'project_budget_internals':
                                    if($value == 0) unset($budgetFields['internal']);
                                    break;
                                case 'project_budget_externals':
                                    if($value == 0) unset($budgetFields['external']);
                                    break;
                                case 'project_budget_provisionals':
                                    if($value == 0) unset($budgetFields['provisional']);
                                    break;
                                case 'project_budget_purchases':
                                    if($value == 0) unset($budgetFields['purchases']);
                                    break;
                            }
                        }
                    }
                    $othersField += $budgetFields['others'];
                    unset($budgetFields['others']);
                }
                $langCode = Configure::read('Config.langCode');
                $fields = ($langCode == 'fr') ? array('controllers', 'name_fre') : array('controllers', 'name_eng');
                $tranBud = $this->Menu->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->employee_info['Company']['id'],
                        'model' => 'project',
                        'controllers' => array('project_budget_sales', 'project_budget_internals', 'project_budget_externals', 'project_budget_provisionals', 'project_budget_purchases'),
                        'functions' => 'index',
                        'widget_id NOT' => null
                    ),
                    'fields' => $fields,
                    'order' => array('id' => 'DESC')
                ));
            }
        } elseif($model=='business') {
            $projectFields = $this->SaleLead->getViewFieldNames();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } elseif($model=='deal') {
            $projectFields = $this->SaleLead->getViewFieldNameForDeals();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } elseif($model=='ticket') {
            $projectFields = $this->Ticket->getViewFieldNames();
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        } else {
            $projectFields = $this->ActivityColumn->getViewFieldNames($this->employee_info["Company"]["id"]);
            $amrFields = array();
            $budgetFields = array();
            $valueFields = array();
        }
        $defaultView = $this->UserDefaultView->find('first', array(
            'fields' => array('id'),
            'conditions' => array(
                "UserDefaultView.user_view_id" => $id,
                "UserDefaultView.employee_id" => $this->employee_info["Employee"]["id"])));
        $ex_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'External_Cost',
                array('external_cost')
            )
        ));
        $in_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'Internal_Cost',
                array('internal_cost')
            )
        ));
         $sale_settings = $this->requestAction('/translations/getSettings',
            array('pass' => array(
                'Sales',
                array('sales')
            )
        ));
        foreach ($budgetFields as $key => $value) {
            switch ($key) {
                case 'sales':
                    if(!isset($showMenu['sale']) || !$showMenu['sale']){
                        unset($budgetFields['sales']);
                    }else{
                        if(!isset($sale_settings['sold'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_sold']);
                        if(!isset($sale_settings['billed'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_to_bill']);
                        if(!isset($sale_settings['billed_check'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_billed']);
                        if(!isset($sale_settings['paid'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_paid']);
                        if(!isset($sale_settings['man_day'])) unset($budgetFields['sales']['ProjectBudgetSyn.sales_man_day']);
                    }
                    break;
                case 'purchases':
                    if(!isset($showMenu['purchase']) || !$showMenu['purchase']) unset($budgetFields['purchases']); 
                    break;
                case 'Task':
                    if(!isset($showMenu['task']) || !$showMenu['task']) unset($budgetFields['Task']); 
                    unset($budgetFields['others']['ProjectBudgetSyn.workload']);
                    break;
                case 'internal':
                    if(!isset($showMenu['internal_cost']) || !$showMenu['internal_cost']){
                        unset($budgetFields['internal']);
                    }else{
                        if(!isset($in_settings['budget_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_budget']);
                        if(!isset($in_settings['budget_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_budget_man_day']);
                        if(!isset($in_settings['forecast_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_forecast']);
                        if(!isset($in_settings['var_percent'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_var']);
                        if(!isset($in_settings['engaged_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_engaged']);
                        if(!isset($in_settings['engaged_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_engaged_md']);
                        if(!isset($in_settings['forecast_md'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_forecasted_man_day']);
                        if(!isset($in_settings['remain_erro'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_remain']);
                        if(!isset($in_settings['average'])) unset($budgetFields['internal']['ProjectBudgetSyn.internal_costs_average']);
                    } 
                    break;
                case 'external':
                    if(!isset($showMenu['external_cost']) || !$showMenu['external_cost']) {
                        unset($budgetFields['external']);
                    }else{
                        if(!isset($ex_settings['budget_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_budget']);
                        if(!isset($ex_settings['forecast_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_forecast']);
                        if(!isset($ex_settings['var_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_var']);
                        if(!isset($ex_settings['ordered_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_ordered']);
                        if(!isset($ex_settings['remain_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_remain']);
                        if(!isset($ex_settings['man_day'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_man_day']);
                        if(!isset($ex_settings['progress_md'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_progress']);
                        if(!isset($ex_settings['progress_erro'])) unset($budgetFields['external']['ProjectBudgetSyn.external_costs_progress_euro']);
                    }
                    break;
                case 'provisional':
                    if(!isset($showMenu['provisional']) || !$showMenu['provisional']) unset($budgetFields['provisional']); 
                    break;
                case 'others':
                    if(isset($showMenu['internal_cost']) && isset($showMenu['external_cost']) && $showMenu['internal_cost'] == 0 && $showMenu['external_cost'] == 0){
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_budget']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_forecast']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_var']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_engaged']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_remain']);
                         unset($budgetFields['others']['ProjectBudgetSyn.total_costs_man_day']);
                         unset($budgetFields['others']['ProjectBudgetSyn.roi']);
                    }
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Workload') == 0)){
                        unset($budgetFields['others']['ProjectBudgetSyn.workload']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_last_thr_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.workload_next_thr_y']);
                    } 
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Consumed') == 0)){
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_last_thr_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_one_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_two_y']);
                        unset($budgetFields['others']['ProjectBudgetSyn.consumed_next_thr_y']);
                        
                    }
                    if(!isset($showMenu['task']) || !$showMenu['task'] || ( $showMenu['task'] && $this->getShowFieldTask('Manual Consumed') == 0)){
                        unset($budgetFields['others']['ProjectAmr.manual_consumed']);
                    } 
                    if(!isset($showMenu['task']) || !$showMenu['task']){
                        unset($budgetFields['others']['ProjectBudgetSyn.assign_to_profit_center']);
                        unset($budgetFields['others']['ProjectBudgetSyn.assign_to_employee']);
                        unset($budgetFields['others']['ProjectBudgetSyn.overload']);
                    }
                    break;
           }
        }
        $this->set(compact('projectFields', 'amrFields', 'defaultView', 'othersField', 'id', 'budgetFields', 'isAdmin','model','valueFields', 'financeFields', 'financeFieldPlus', 'financeFieldTwoPlus', 'LANG', 'canSeeBudget', 'tranBud', 'showMenu'));
        if (!empty($this->data)) {
            $this->data['UserView']['content'] = Set::sort($this->data['UserView']['content'], '{n}.weight', 'asc');
            $this->data['UserView']['content'] = serialize(Set::classicExtract($this->data['UserView']['content'], '{n}.value'));
            $this->data['UserView']['company_id'] = $this->employee_info["Company"]["id"];
            $this->data['UserView']['gantt_view'] = 0;
            if(!empty($this->data['UserView']['initial']) || !empty($this->data['UserView']['real']) || !empty($this->data['UserView']['stones'])){
                $this->data['UserView']['gantt_view'] = 1;
            }
            $this->UserView->id = $id;
            if ($this->UserView->save($this->data)) {
                if($model=='project') {
                    if(!empty($this->data['UserStatusView'])){
                        $tmp = $this->UserStatusView->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'user_view_id' => $id
                            ),
                            'fields' => array('id')
                        ));
                        if($tmp && !empty($tmp['UserStatusView']['id'])){
                            $this->UserStatusView->id = $tmp['UserStatusView']['id'];
                            $saved = array(
                                'progress_view' => $this->data['UserStatusView']['progress_view'],
                                'oppor_view' => $this->data['UserStatusView']['oppor_view'],
                                'archived_view' => $this->data['UserStatusView']['archived_view'],
                                'model_view' => $this->data['UserStatusView']['model_view']
                            );
                            $this->UserStatusView->save($saved);
                        }
                    }
                } elseif($model=='business') {
                    if(!empty($this->data['UserStatusViewSale'])){
                        $tmp = $this->UserStatusViewSale->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'user_view_id' => $id
                            ),
                            'fields' => array('id')
                        ));
                        if($tmp && !empty($tmp['UserStatusViewSale']['id'])){
                            $this->UserStatusViewSale->id = $tmp['UserStatusViewSale']['id'];
                            $open = $closed_won = $closed_lose = 0;
                            switch($this->data['UserStatusViewSale']['lead_status']){
                                case 1: {
                                    $closed_won = 1;
                                    break;
                                }
                                case 2: {
                                    $closed_lose = 1;
                                    break;
                                }
                                case 3: {
                                    $open = $closed_won = $closed_lose = 1;
                                    break;
                                }
                                case 0:
                                default: {
                                    $open = 1;
                                    break;
                                }
                            }
                            $saved = array(
                                'open' => $open,
                                'closed_won' => $closed_won,
                                'closed_lose' => $closed_lose
                            );
                            $this->UserStatusViewSale->save($saved);
                        }
                    }
                } elseif($model=='deal') {
                    if(!empty($this->data['UserStatusViewSaleDeal'])){
                        $tmp = $this->UserStatusViewSaleDeal->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'user_view_id' => $id
                            ),
                            'fields' => array('id')
                        ));
                        if($tmp && !empty($tmp['UserStatusViewSaleDeal']['id'])){
                            $this->UserStatusViewSaleDeal->id = $tmp['UserStatusViewSaleDeal']['id'];
                            $open = $archived = $renewal = 0;
                            switch($this->data['UserStatusViewSaleDeal']['deal_status']){
                                case 1: {
                                    $archived = 1;
                                    break;
                                }
                                case 2: {
                                    $renewal = 1;
                                    break;
                                }
                                case 3: {
                                    $open = $archived = $renewal = 1;
                                    break;
                                }
                                case 0:
                                default: {
                                    $open = 1;
                                    break;
                                }
                            }
                            $saved = array(
                                'open' => $open,
                                'archived' => $archived,
                                'renewal' => $renewal
                            );
                            $this->UserStatusViewSaleDeal->save($saved);
                        }
                    }
                } else {
                    if(!empty($this->data['UserStatusViewActivity'])){
                        $tmp = $this->UserStatusViewActivity->find('first', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'employee_id' => $this->employee_info['Employee']['id'],
                                'user_view_id' => $id
                            ),
                            'fields' => array('id')
                        ));
                        if($tmp && !empty($tmp['UserStatusViewActivity']['id'])){
                            $this->UserStatusViewActivity->id = $tmp['UserStatusViewActivity']['id'];
                            $saved = array(
                                'activated' => $this->data['UserStatusViewActivity']['activated'],
                                'not_activated' => $this->data['UserStatusViewActivity']['not_activated'],
                                'activated_and_not_activated' => $this->data['UserStatusViewActivity']['activated_and_not_activated']
                            );
                            $this->UserStatusViewActivity->save($saved);
                        }
                    }
                }
                if (empty($defaultView) && !empty($this->data['UserView']['default'])) {
                    $this->redirect(array('controller' => 'user_views', 'action' => 'toggle',$model, $id, true));
                } elseif (!empty($defaultView) && empty($this->data['UserView']['default'])) {
                    $this->redirect(array('controller' => 'user_views', 'action' => 'toggle',$model, $id));
                }
                $this->Session->setFlash(__('Saved', true));
                //$this->Session->setFlash(sprintf(__('The %s view has been saved', true), '<b>"' . $this->data["UserView"]["name"] . '"</b>'), 'success');
                $this->redirect(array('action' => 'index?model='.$model));
            } else {
                $this->Session->setFlash(__('Not saved', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->UserView->read(null, $id);
            $this->data['UserView']['default']=!empty($this->data['UserDefaultView']['user_view_id'])?1:0;
        }
    }

    // get show value of Task fields

    public function getShowFieldTask( $field_name = 'Consumed') {

            $this->loadModels('TranslationSetting');
            $checkFieldTask = $this->TranslationSetting->find('first', array(
                'fields' => array('show'),
                'joins' => array(
                    array(
                        'table' => 'translations',
                        'alias' => 'Translation',
                        'conditions' => array(
                            'Translation.id = TranslationSetting.translation_id',
                            'TranslationSetting.company_id' => $this->employee_info['Company']['id'],
                            'Translation.original_text' => $field_name
                        )
                    )
                ),
            ));
            return $checkFieldTask['TranslationSetting']['show'];
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    public function delete($model='project',$id = null) {
        $role = $this->employee_info['Role']['name'];
        if (!$id || !$this->UserView->find('count', array(
                    'recursive' => -1,
                    'conditions' => array('id' => $id)
                ))) {
            $this->Session->setFlash(__('Invalid id for user view', true), 'error');
            $this->redirect(array('action' => 'index?model='.$model));
        }
        $is_default_view = $this->UserDefaultView->find('first', array(
            'conditions' => array(
                "UserDefaultView.user_view_id" => $id,
                "UserDefaultView.employee_id" => $this->employee_info["Employee"]["id"],
                )));
        if (isset($is_default_view["UserDefaultView"])) {
            $this->Session->setFlash(__('This view is the current default view. You can not delete it.', true), 'warning');
            $this->redirect(array('action' => 'index?model='.$model));
        }
        if ($this->UserView->delete($id)) {
            $this->UserDefaultView->deleteAll(array(
                'UserDefaultView.user_view_id' => $id
            ));
			$this->UserStatusView->deleteAll(array(
                'UserStatusView.user_view_id' => $id
            ));
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index?model='.$model));
        }
        $this->Session->setFlash(__('Not deleted', true), 'success');
        $this->redirect(array('action' => 'index?model='.$model));
    }

    /**
     * project_view
     *
     * @return void
     * @access public
     */
    function project_view($view_id = null) {
        if (!$view_id) {
            $this->Session->setFlash(__('Invalid id for user view', true));
            $this->redirect(array('action' => 'index'));
        }

        $this->UserView->recursive = -1;
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));

        if ($view_content == "") {
            $this->Session->setFlash(__('Invalid id for user view', true));
            $this->redirect(array('action' => 'index'));
        }
        $view_fields = $view_content["UserView"]["content"];
        $this->set('view_fields', $view_fields);
        if ($this->is_sas) {
            $projects = $this->Project->find('all');
        } else {
            $projects = array();
            $sub_companies = $this->Project->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
            foreach ($sub_companies as $sub_company) {
                $projects = array_merge($projects, $this->Project->find('all', array('conditions' => array('Project.company_id' => $sub_company['Company']['id']))));
            }
        }
        $this->set('projects', $projects);
        $this->set('email', $this->Session->read("Auth.Employee.email"));
        $search_manager = array();
        $search_phase = array();
        $search_priority = array();
        $search_status = array();
        $search_company = array();

        foreach ($projects as $p) {
            if (!in_array($p['Employee'], $search_manager)) {
                $search_manager[] = $p['Employee'];
            }
            if (isset($p['ProjectPriority'])) {
                if (!in_array($p['ProjectPriority'], $search_priority)) {
                    $search_priority[] = $p['ProjectPriority'];
                }
            }
            if (isset($p['ProjectPhase'])) {
                if (!in_array($p['ProjectPhase'], $search_phase)) {
                    $search_phase[] = $p['ProjectPhase'];
                }
            }
            if (isset($p['ProjectStatus'])) {
                if (!in_array($p['ProjectStatus'], $search_status)) {
                    $search_status[] = $p['ProjectStatus'];
                }
            }
            if (isset($p['Company'])) {
                if (!in_array($p['Company'], $search_company)) {
                    $search_company[] = $p['Company'];
                }
            }
        }

        $tree = $this->Company->generateTreeList(null, null, null, '->');
        $amrs = $this->ProjectAmr->find("all");
        $this->Employee->recursive = -1;
        $employees = $this->Employee->find('all');
        $this->ProjectAmrProgram->recursive = -1;
        if ($this->is_sas) {
            $amr_programs = $this->ProjectAmr->ProjectAmrProgram->find("all");
        } else {
            $amr_programs = $this->ProjectAmr->ProjectAmrProgram->find("all", array(
                'conditions' => array('company_id' => $this->employee_info['Company']['id'])));
        }
        $this->ProjectAmrSubProgram->Behaviors->attach('Containable');
        $amr_sub_progs = $this->ProjectAmr->ProjectAmrSubProgram->find("all", array('contain' => array('ProjectAmrProgram')));
        $amr_sub_programs = array();
        foreach ($amr_programs as $amr_prog) {
            foreach ($amr_sub_progs as $amr_sub_prog) {
                if ($amr_prog['ProjectAmrProgram']['id'] == $amr_sub_prog['ProjectAmrProgram']['id']) {
                    $amr_sub_programs[] .= $amr_sub_prog['ProjectAmrSubProgram']['amr_sub_program'];
                }
            }
        }
        $this->set(compact('search_manager', 'search_phase', 'search_company', 'search_priority', 'search_status', 'amrs', 'tree', 'view_id', 'amr_programs', 'amr_sub_programs', 'employees'));
    }

    function project_view2($view_id = null) {
        if (!$view_id) {
            $this->Session->setFlash(__('Invalid id for user view', true));
            $this->redirect(array('action' => 'index'));
        }

        $this->UserView->recursive = -1;
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));

        if ($view_content == "") {
            $this->Session->setFlash(__('Invalid id for user view', true));
            $this->redirect(array('action' => 'index'));
        }
        $view_fields = $view_content["UserView"]["content"];
        App::import("vendor", "str_utility");
        $str_utility = new str_utility();
        $view_fields = $str_utility->xmlstr_to_array($view_fields);
        $arr_fieldnames = array();

        $this->Project->Behaviors->attach('Containable');
        $arr_contain = array();
        $i = 0;
        $arr_foreign_keys = $this->Project->belongsTo;
        $arr_models = array();
        foreach ($arr_foreign_keys as $arr_foreign_key) {
            $arr_models[$arr_foreign_key["className"]] = $arr_foreign_key["foreignKey"];
        };
        foreach ($view_fields as $view_field) {
            foreach ($arr_models as $model_name => $foreign_key) {
                $i = 0;
                foreach ($view_field[$i]['@attributes'] as $field_name => $field_name_alias) {
                    if ($field_name == $foreign_key) {
                        $arr_contain[] = $model_name;
                        break;
                    }
                    $i++;
                }
            }
        }
        $this->Project->contain = array("ProjectAMR", "ProjectPhase");

        if ($this->is_sas) {
            $projects = $this->Project->find('all');
        } else {
            $projects = array();
            $sub_companies = $this->Project->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
            foreach ($sub_companies as $sub_company) {
                $projects = array_merge($projects, $this->Project->find('all', array('conditions' => array('Project.company_id' => $sub_company['Company']['id']))));
            }
        }
        $this->set('projects', $projects);
        $this->set('email', $this->Session->read("Auth.Employee.email"));
        $search_manager = array();
        $search_phase = array();
        $search_priority = array();
        $search_status = array();
        $search_company = array();

        foreach ($projects as $p) {
            if (!in_array($p['Employee'], $search_manager)) {
                $search_manager[] = $p['Employee'];
            }
            if (isset($p['ProjectPriority'])) {
                if (!in_array($p['ProjectPriority'], $search_priority)) {
                    $search_priority[] = $p['ProjectPriority'];
                }
            }
            if (isset($p['ProjectPhase'])) {
                if (!in_array($p['ProjectPhase'], $search_phase)) {
                    $search_phase[] = $p['ProjectPhase'];
                }
            }
            if (isset($p['ProjectStatus'])) {
                if (!in_array($p['ProjectStatus'], $search_status)) {
                    $search_status[] = $p['ProjectStatus'];
                }
            }
            if (isset($p['Company'])) {
                if (!in_array($p['Company'], $search_company)) {
                    $search_company[] = $p['Company'];
                }
            }
        }

        $tree = $this->Company->generateTreeList(null, null, null, '->');
        $amrs = $this->ProjectAmr->find("all");
        $this->Employee->recursive = -1;
        $employees = $this->Employee->find('all');
        $this->ProjectAmrProgram->recursive = -1;
        if ($this->is_sas) {
            $amr_programs = $this->ProjectAmr->ProjectAmrProgram->find("all");
        } else {
            $amr_programs = $this->ProjectAmr->ProjectAmrProgram->find("all", array(
                'conditions' => array('company_id' => $this->employee_info['Company']['id'])));
        }
        $this->ProjectAmrSubProgram->Behaviors->attach('Containable');
        $amr_sub_progs = $this->ProjectAmr->ProjectAmrSubProgram->find("all", array('contain' => array('ProjectAmrProgram')));
        $amr_sub_programs = array();
        foreach ($amr_programs as $amr_prog) {
            foreach ($amr_sub_progs as $amr_sub_prog) {
                if ($amr_prog['ProjectAmrProgram']['id'] == $amr_sub_prog['ProjectAmrProgram']['id']) {
                    $amr_sub_programs[] .= $amr_sub_prog['ProjectAmrSubProgram']['amr_sub_program'];
                }
            }
        }
        $this->set(compact('search_manager', 'search_phase', 'search_company', 'search_priority', 'search_status', 'amrs', 'tree', 'view_id', 'amr_programs', 'amr_sub_programs', 'employees'));
    }

    /**
     * exportProjectViewToExcel
     *
     * @return void
     * @access public
     */
    function exportProjectViewToExcel($view_id = null) {
        $this->set('columns', $this->name_columna);
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));

        $view_content = $view_content["UserView"]["content"];
        if ($view_content == "") {
            $this->Session->setFlash(__('Invalid id for user view', true));
            $this->redirect(array('action' => 'index'));
        }

        $this->set('view_content', $view_content);
        //$this->layout = 'excel';
        if ($this->is_sas) {
            $projects = $this->Project->find('all');
        } else {
            $projects = array();
            $sub_companies = $this->Project->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $this->employee_info['Company']['id'], 'Company.parent_id' => $this->employee_info['Company']['id']))));
            foreach ($sub_companies as $sub_company) {
                $projects = array_merge($projects, $this->Project->find('all', array('conditions' => array('Project.company_id' => $sub_company['Company']['id']))));
            }
        }
        $amrs = $this->ProjectAmr->find("all");
        $this->Employee->recursive = -1;
        $employees = $this->Employee->find('all');
        $this->set('amrs', $amrs);
        $this->set('projects', $projects);
        $this->set('employees', $employees);
    }

    /**
     * project_detail_view
     *
     * @return void
     * @access public
     */
    function project_detail_view($view_id = null, $id = null) {
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));
        $view_content = $view_content["UserView"]["content"];
        $this->set('view_content', $view_content);
        if ($this->Session->check("Auth.Employee")) {
            $fullname = $this->Session->read("Auth.Employee.first_name") . " " . $this->Session->read("Auth.Employee.last_name");
            $this->set("fullname", $fullname);
        } else {
            $this->redirect("/login");
        }
        $company_id_of_project = $this->Project->find("first", array("fields" => array("Project.company_id"), 'conditions' => array('Project.id' => $id)));
        $company_keke = $company_id_of_project['Project']['company_id'];

        if (empty($this->data)) {
            $this->data = $this->Project->read(null, $id);
        }
        $projectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_keke)));
        $projectPriorities = $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id' => $company_keke)));
        $projectStatuses = $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id' => $company_keke)));
        $currencies = $this->Project->Currency->find('list');
        $amrCurrencies = $this->Project->Currency->find('list');
        $projectManagers = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_keke)));
        //$employeeIds = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields'=>array('CompanyEmployeeReference.employee_id'),'conditions'=>array('CompanyEmployeeReference.company_id'=>$company_id)));
        $projectFunctions = $this->ProjectFunction->find('list');
        $this->set(compact('projectPhases', 'projectPriorities', 'projectStatuses', 'currencies', 'projectManagers', 'projectFunctions'));
        $this->set('project_id', $id);
        $this->set('view_id', $view_id);
        //tree company
        $tree = $this->Project->Company->generateTreeList(null, null, null, '->');
        $company_ids = $this->Project->find('list', array('fields' => array('company_id'), 'conditions' => array("Project.id" => $id)));
        $company_name = $this->Project->Company->find('list', array('fields' => 'Company_name'));
        $tree = array();
        foreach ($company_ids as $key => $value) {
            foreach ($company_name as $key2 => $value2) {
                if ($value == $key2) {
                    $tree[$key2] = $value2;
                    $name_company = $value2;
                    $company_id = $key2;
                    break;
                }
            }
        }

        $this->set('name_company', $name_company);
        $this->set('company_id', $company_id);
        $company_id = $this->Project->find("first", array("fields" => array("Project.company_id"),
            'conditions' => array('Project.id' => $id)));
        $company_id = $company_id['Project']['company_id'];
        $this->set('ProjectPhases', $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id))));
        $this->set('Priorities', $this->Project->ProjectPriority->find('list', array('fields' => array('ProjectPriority.id', 'ProjectPriority.priority'), 'conditions' => array('ProjectPriority.company_id ' => $company_id))));
        $this->set('Statuses', $this->Project->ProjectStatus->find('list', array('fields' => array('ProjectStatus.id', 'ProjectStatus.name'), 'conditions' => array('ProjectStatus.company_id ' => $company_id))));

        $allEmployees = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $employeeIds = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array();
        foreach ($employeeIds as $key => $value) {
            foreach ($allEmployees as $key2 => $value2) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $value2;
                    break;
                }
            }
        }
        $this->set('projectManagers', $projectManagers);
        $this->set('view_id', $view_id);
        $this->set('project_id', $id);
        $project = $this->Project->find('first', array('fields' => array('Project.id', 'Project.project_name'), 'conditions' => array('Project.id' => $id)));
        $this->set('project_name', $project);
    }

    /**
     * project_amr_view
     * Export to Excel for project amr view
     *
     * @return void
     * @access public
     */
    function project_amr_view($view_id = null, $id = null) {
        $view_content = $this->UserView->find("first", array("conditions" => array("UserView.id" => $view_id)));
        $view_content = $view_content["UserView"]["content"];
        $this->set('view_content', $view_content);
        $this->set('view_id', $view_id);
        $this->set('project_id', $id);
        if (empty($this->data)) {
            $this->data = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $id)));
            $this->set(compact('data'));
        } else {
            App::import("vendor", "str_utility");
            $str_utility = new str_utility();
            $this->data["ProjectAmr"]["project_amr_mep_date"] = $str_utility->convertToSQLDate($this->data["ProjectAmr"]["project_amr_mep_date"]);

            $this->data["ProjectAmr"]["weather"] = $this->data["ProjectAmr"]["weather"][0];
            if ($this->Project->ProjectAmr->save($this->data["ProjectAmr"])) {

                if (empty($this->data['ProjectAmr']['id'])) {
                    $project_amr_id = $this->Project->ProjectAmr->getLastInsertID();
                } else {
                    $project_amr_id = $this->data['ProjectAmr']['id'];
                }
                $this->Session->setFlash(__('The project AMR has been saved', true), 'success');
            }else {
                $this->Session->setFlash(__('The project AMR could not be saved. Please try again enter informations for (*) fields match.', true), 'error');
            }
            $this->redirect($this->referer());
        }

        $projectStatuses = $this->Project->ProjectStatus->find('list');
        $amrCurrencies = $this->Project->Currency->find('list');
        $project_detail = $this->Project->find("first", array('conditions' => array('Project.id' => $id)));
        $project_manager = $this->Project->Employee->find('first', array('fields' => array('Employee.id', 'Employee.fullname'),
            'conditions' => array('Employee.id' => $project_detail['Project']['project_manager_id'])));
        $company_id = $project_detail['Project']['company_id'];
        $projectAmrManagers = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $projectAmrPrograms = $this->Project->ProjectAmr->ProjectAmrProgram->find('list', array('fields' => array('ProjectAmrProgram.id', 'ProjectAmrProgram.amr_program'),
            'conditions' => array('ProjectAmrProgram.company_id' => $company_id)));

        $projectAmrCategories = $this->Project->ProjectAmr->ProjectAmrCategory->find('list', array('fields' => array('ProjectAmrCategory.id', 'ProjectAmrCategory.amr_category'),
            'conditions' => array('ProjectAmrCategory.company_id' => $company_id)));
        $projectAmrSubCategories = $this->Project->ProjectAmr->ProjectAmrSubCategory->find('list', array('fields' => array('ProjectAmrSubCategory.id', 'ProjectAmrSubCategory.amr_sub_category')));
        $projectAmrStatuses = $this->Project->ProjectAmr->ProjectAmrStatus->find('list', array('fields' => array('ProjectAmrStatus.id', 'ProjectAmrStatus.amr_status'),
            'conditions' => array('ProjectAmrStatus.company_id' => $company_id)));
        $projectAmrCostControls = $this->Project->ProjectAmr->ProjectAmrCostControl->find('list', array('fields' => array('ProjectAmrCostControl.id', 'ProjectAmrCostControl.amr_cost_control'),
            'conditions' => array('ProjectAmrCostControl.company_id' => $company_id)));
        $projectAmrOrganizations = $this->Project->ProjectAmr->ProjectAmrOrganization->find('list', array('fields' => array('ProjectAmrOrganization.id', 'ProjectAmrOrganization.amr_organization'),
            'conditions' => array('ProjectAmrOrganization.company_id' => $company_id)));
        $projectAmrPlans = $this->Project->ProjectAmr->ProjectAmrPlan->find('list', array('fields' => array('ProjectAmrPlan.id', 'ProjectAmrPlan.amr_plan'),
            'conditions' => array('ProjectAmrPlan.company_id' => $company_id)));
        $projectAmrPerimeters = $this->Project->ProjectAmr->ProjectAmrPerimeter->find('list', array('fields' => array('ProjectAmrPerimeter.id', 'ProjectAmrPerimeter.amr_perimeter'),
            'conditions' => array('ProjectAmrPerimeter.company_id' => $company_id)));
        $projectAmrRiskControls = $this->Project->ProjectAmr->ProjectAmrRiskControl->find('list', array('fields' => array('ProjectAmrRiskControl.id', 'ProjectAmrRiskControl.amr_risk_control'),
            'conditions' => array('ProjectAmrRiskControl.company_id' => $company_id)));
        $projectAmrProblemControls = $this->Project->ProjectAmr->ProjectAmrProblemControl->find('list', array('fields' => array('ProjectAmrProblemControl.id', 'ProjectAmrProblemControl.amr_problem_control'),
            'conditions' => array('ProjectAmrProblemControl.company_id' => $company_id)));
        $projectAmrPhases = $this->Project->ProjectPhase->find('list');
        $allEmployees = $this->Project->Employee->find('list', array('fields' => array('Employee.id', 'Employee.fullname')));
        $employeeIds = $this->Project->Employee->CompanyEmployeeReference->find('list', array('fields' => array('CompanyEmployeeReference.employee_id'), 'conditions' => array('CompanyEmployeeReference.company_id' => $company_id)));
        $projectManagers = array();
        foreach ($employeeIds as $key => $value) {
            foreach ($allEmployees as $key2 => $value2) {
                if ($value == $key2) {
                    $projectManagers[$key2] = $value2;
                    break;
                }
            }
        }
        $ProjectPhases = $this->Project->ProjectPhase->find('list', array('fields' => array('ProjectPhase.id', 'ProjectPhase.name'), 'conditions' => array('ProjectPhase.company_id' => $company_id)));
        $projectName = $this->Project->find("first", array("fields" => array("Project.project_name"),
            'conditions' => array('Project.id' => $id)));
        $currency_name = $this->Project->Currency->find('list', array('fields' => array('Currency.sign_currency'), 'conditions' => array('Currency.company_id' => $company_id)));
        $start_date = $project_detail['Project']['start_date'];
        $end_date = $project_detail['Project']['end_date'];
        $end_plan_date = $project_detail['Project']['planed_end_date'];
        $this->set(compact('projectStatuses', 'projectManagers', 'project_manager', 'start_date', 'end_date', 'end_plan_date', 'view_content', 'projectAmrManagers', 'projectAmrPrograms', 'projectAmrSubPrograms', 'projectAmrCategories', 'projectAmrSubCategories', 'ProjectPhases', 'projectAmrStatuses', 'projectAmrCostControls', 'projectAmrOrganizations', 'projectAmrPlans', 'projectAmrPerimeters', 'projectAmrRiskControls', 'projectAmrProblemControls', 'projectAmrPhases', 'amrCurrencies', 'currency_name', 'projectName'));
    }

    /**
     * export_project_amr
     * Export to Excel for project amr
     *
     * @return void
     * @access public
     */
    function export_project_amr($view_id = null, $project_id = null) {
        $this->layout = 'excel';
        $this->ProjectAmr->recursive = 0;
        $view_content = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
        $view_content = $view_content['UserView']['content'];
        $amr_content = $this->ProjectAmr->find('first', array('conditions' => array('ProjectAmr.project_id' => $project_id)));
        if ($amr_content == "") {
            $this->Session->setFlash(__('Save data before export', true), 'error');
            $this->redirect("/user_views/project_amr_view/" . $view_id . "/" . $project_id);
        } else {
            $project_name = $amr_content['Project']['project_name'];
            $weather = $amr_content['ProjectAmr']['weather'];
            $amr_program = $amr_content['ProjectAmrProgram']['amr_program'];
            $amr_sub_program = $amr_content['ProjectAmrSubProgram']['amr_sub_program'];
            $amr_category = $amr_content['ProjectAmrCategory']['amr_category'];
            $amr_sub_category = $amr_content['ProjectAmrSubCategory']['amr_sub_category'];
            $project_manager = $amr_content['Employee']['fullname'];
            $budget = $amr_content['Project']['budget'];
            $amr_status = $amr_content['ProjectAmrStatus']['amr_status'];
            $amr_phase = $amr_content['ProjectPhases']['name'];
            $amr_cost_control = $amr_content['ProjectAmrCostControl']['amr_cost_control'];
            $amr_organization = $amr_content['ProjectAmrOrganization']['amr_organization'];
            $amr_plan = $amr_content['ProjectAmrPlan']['amr_plan'];
            $amr_perimeter = $amr_content['ProjectAmrPerimeter']['amr_perimeter'];
            $amr_risk_control = $amr_content['ProjectAmrRiskControl']['amr_risk_control'];
            $amr_problem_control = $amr_content['ProjectAmrProblemControl']['amr_problem_control'];
            $amr_risk_info = $amr_content['ProjectAmr']['project_amr_risk_information'];
            $amr_problem_info = $amr_content['ProjectAmr']['project_amr_problem_information'];
            $amr_solution = $amr_content['ProjectAmr']['project_amr_solution'];
            $amr_solution_decs = $amr_content['ProjectAmr']['project_amr_solution_description'];
            $mepdate = $amr_content['ProjectAmr']['project_amr_mep_date'];
            $progress = $amr_content['ProjectAmr']['project_amr_progression'];
            $currency = $this->Project->Currency->find('first', array('fields' => array('Currency.sign_currency'),
                'conditions' => array('Currency.id' => $amr_content['ProjectAmr']['currency_id'])));
            $currency = $currency['Currency']['sign_currency'];
            $this->set(compact('view_content', 'weather', 'amr_program', 'amr_sub_program', 'amr_category', 'amr_sub_category', 'project_manager', 'budget', 'amr_status', 'amr_status', 'amr_phase', 'amr_cost_control', 'amr_organization', 'amr_plan', 'amr_perimeter', 'amr_risk_control', 'amr_problem_control', 'amr_risk_info', 'amr_problem_info', 'amr_solution', 'amr_solution_decs', 'mepdate', 'currency', 'progress', 'project_name'
                    ));
        }
    }

    /**
     * exportExcelDetail
     * Export to Excel
     *
     * @return void
     * @access public
     */
    function exportExcelDetail($view_id = null, $project_id = null) {
        $view_content = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
        $view_content = $view_content['UserView']['content'];
        if ($view_content == "") { //default view
            $view_content = '<user_view>
                                    <project_detail project_name = "Project Name" />
                                    <project_detail company_id = "Company" />
                                    <project_detail project_manager_id = "Project Manager" />
                                    <project_detail project_priority_id = "Priority" />
                                    <project_detail project_status_id = "Status" />
                                    <project_detail start_date = "Start Date" />
                                    <project_detail issues = "Issues" />
                                    <project_detail constraint = "Constraint" />
                                    <project_detail remark = "Remark" />
                                    <project_detail project_objectives = "Project Objectives" />
                                    <project_amr weather = "Weather" />
                                    <project_amr project_amr_program_id = "Program" />
                                    <project_amr project_amr_sub_program_id = "Sub Program" />
                                    <project_amr project_amr_category_id = "Category" />
                                    <project_amr project_amr_sub_category_id = "Sub Category" />
                                    <project_amr project_manager_id = "Project Manager" />
                                    <project_amr budget = "Budget" />
                                    <project_amr project_amr_status_id = "Status" />
                                    <project_amr project_amr_mep_date = "MEP Date" />
                                    <project_amr project_amr_progression = "Progression" />
                                    <project_amr project_phases_id = "Phase" />
                                    <project_amr project_amr_cost_control_id = "Cost Control" />
                                    <project_amr project_amr_organization_id = "Organization" />
                                    <project_amr project_amr_plan_id = "Planning" />
                                    <project_amr project_amr_perimeter_id = "Perimeter" />
                                    <project_amr project_amr_risk_control_id = "Risk Control" />
                                    <project_amr project_amr_problem_control_id = "Problem Control" />
                                    <project_amr project_amr_risk_information = "Risk Information" />
                                    <project_amr project_amr_problem_information = "Problem Information" />
                                    <project_amr project_amr_solution = "Solution" />
                                    <project_amr project_amr_solution_description = "Solution Description" />
                            </user_view>';
        }
        $this->set('view_content', $view_content);
        $projects = $this->Project->find('all', array('conditions' => array('Project.id' => $project_id)));
        $project = $this->Project->find('first', array('fields' => array('Project.id', 'Project.project_name'), 'conditions' => array('Project.id' => $project_id)));
        $this->set('project_name', $project);
        $this->set('projects', $projects);
        $this->layout = 'excel';
    }
    // convert array to array list
    public function arrList( $arrs = null){
        $_arr = array();
        if(!empty($arrs)){
            foreach ($arrs as $key => $value) {
                $name = explode('|', $value);
                $_arr[$name[0]] = $name[1];
            }
        }
        return $_arr;
    }
    // convert array list to array
    public function arrArray( $arrs = null){
        $_arr = array();
        if(!empty($arrs)){
            foreach ($arrs as $key => $value) {
                $_arr[] = $key . '|' . $value;
            }
        }
        return $_arr;
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    public function toggle_status_views($fields = null, $viewId = null, $switch = null) {
		// debug( $this->employee_info["Employee"]["id"] ); exit;
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['OR'] = array(
                'UserView.employee_id' => $this->employee_info["Employee"]["id"],
                array(
                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                    'UserView.public' => true
                )
            );
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'fields' => array('name'), 'conditions' => $conditions));
        if (!$userView) {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $statusView = $this->UserStatusView->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info["Employee"]["id"],
                'user_view_id' => $viewId
				),
			));
		$statusView = $statusView['UserStatusView'];
		//debug( $statusView );
        $status_name = '';
        if($fields === 'progress_view'){
            $status_name = 'In progress view';
        } elseif ($fields === 'oppor_view'){
            $status_name = 'Opportunity view';
        } elseif ($fields === 'archived_view'){
            $status_name = 'Archived view';
        } elseif ($fields === 'model_view'){
            $status_name = 'Model view';
        }
		$statusView['employee_id'] = isset( $statusView['employee_id'] ) ? $statusView['employee_id'] : $this->employee_info["Employee"]["id"]; 
		$statusView['user_view_id'] = isset( $statusView['user_view_id'] ) ? $statusView['user_view_id'] : $viewId; 
		$statusView[$fields] = !empty( $statusView[$fields] ) ? '0' : '1';
		$statusView[$fields] = isset(  $switch ) ? $switch : $statusView[$fields];
		$this->UserStatusView->create();
		if( isset ($statusView['id'] )) $this->UserStatusView->id = $statusView['id'];
		$toggle_data = $this->UserStatusView->save($statusView);
		if ($toggle_data ) {
			$this->Session->setFlash(__('Saved', true), 'success');
			$result = true;
		} else {
			$this->Session->setFlash(__('Error. Please try again.', true), 'error');
		}
		
		if( $is_ajax ){
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}
        $this->redirect(array('action' => 'index'));
    }
    public function toggle_status_views_activity($fields = null, $viewId = null, $switch = null) {
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['OR'] = array(
                'UserView.employee_id' => $this->employee_info["Employee"]["id"],
                array(
                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                    'UserView.public' => true
                )
            );
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'fields' => array('name'), 'conditions' => $conditions));
        if (!$userView) {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
        }else{
			$statusView = $this->UserStatusViewActivity->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'employee_id' => $this->employee_info["Employee"]["id"],
					'user_view_id' => $viewId
				),
				//'fields' => array('id')
			));
			$statusView = $statusView['UserStatusViewActivity'];
			$status_name = '';
			if($fields === 'activated'){
				$status_name = 'Activated';
			} elseif ($fields === 'not_activated'){
				$status_name = 'Not Activated';
			} elseif ($fields === 'activated_and_not_activated'){
				$status_name = 'Activated and not activated';
			}
			$statusView = array_merge(
				array(
					'employee_id' => $this->employee_info["Employee"]["id"],
					'user_view_id' => $viewId,
					$fields => ''
				),
				empty($statusView) ? array() : $statusView
			);
				$statusView[$fields] = $statusView[$fields] ? '0' : '1';
				$statusView[$fields] = $switch ? $switch : $statusView[$fields];
				$this->UserStatusViewActivity->create();
				if( isset($statusView['id'])) $this->UserStatusViewActivity->id = $statusView['id'];
				$toggle_data = $this->UserStatusViewActivity->save($statusView);
				if ($toggle_data ) {
					$this->Session->setFlash(__('Saved', true), 'success');
					$result = true;
				} else {
					$this->Session->setFlash(__('Error. Please try again.', true), 'error');
				}
				
				
		}
		if( $is_ajax ){
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}
        $this->redirect(array('action' => 'index?model=activity'));
    }

    public function toggle_status_views_sales($fields = null, $viewId = null, $switch = null) {
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
		
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['OR'] = array(
                'UserView.employee_id' => $this->employee_info["Employee"]["id"],
                array(
                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                    'UserView.public' => true
                )
            );
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'conditions' => $conditions));
        if (!$userView) {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
			$data['result'] = $result;
			if( $is_ajax ) die(json_encode($data) );
        }
        $statusView = $this->UserStatusViewSale->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info["Employee"]["id"],
                'user_view_id' => $viewId
            ),
		));
		$statusView = $statusView['UserStatusViewSale'];
        $status_name = '';
        if($fields === 'open'){
            $status_name = 'Open';
        } elseif ($fields === 'closed_won'){
            $status_name = 'Closed Won';
        } elseif ($fields === 'closed_lose'){
            $status_name = 'Closed Lose';
        }
		
			$statusView = array_merge(
				array(
					'employee_id' => $this->employee_info["Employee"]["id"],
					'user_view_id' => $viewId,
					$fields => ''
				),
				empty($statusView) ? array() : $statusView
			);
			$statusView[$fields] = $statusView[$fields] ? 0 : 1;
			$statusView[$fields] = $switch !== null ? $switch : $statusView[$fields];
			
			$this->UserStatusViewSale->create();
			if( isset($statusView['id'])) $this->UserStatusViewActivity->id = $statusView['id'];
			$toggle_data = $this->UserStatusViewSale->save($statusView);
			if ($toggle_data ) {
				$this->Session->setFlash(__('Saved', true), 'success');
				$result = true;
			}else{
				$this->Session->setFlash(__('The user default view could not set as default. Please, try again.', true), 'error');
				$data['result'] = $result;
				if( $is_ajax ) die(json_encode($data) );
			}

		if( $is_ajax ){
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}        
        $this->redirect(array('action' => 'index?model=business'));
    }

    public function toggle_status_views_sale_deals($fields = null, $viewId = null, $switch = null) {
		$is_ajax = $this->params['isAjax'];
		$result = false;
		$toggle_data = array();
		
        $conditions = array(
            'UserView.id' => $viewId,
        );
        if (!$this->is_sas) {
            $conditions['OR'] = array(
                'UserView.employee_id' => $this->employee_info["Employee"]["id"],
                array(
                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                    'UserView.public' => true
                )
            );
        }
        $userView = $this->UserView->find('first', array('recursive' => -1, 'conditions' => $conditions));
        if (!$userView) {
            $this->Session->setFlash(sprintf(__('The #%s view does not exist!', true), '<b>"' . $viewId . '"</b>'), 'error');
			$data['result'] = $result;
			if( $is_ajax ) die(json_encode($data) );
            $this->redirect(array('action' => 'index?model=deal'));
        }
        $statusView = $this->UserStatusViewSaleDeal->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $this->employee_info["Employee"]["id"],
                'user_view_id' => $viewId
            ),
        ));
		$statusView = $statusView['UserStatusViewSaleDeal'];
        $status_name = '';
        if($fields === 'open'){
            $status_name = 'Open';
        } elseif ($fields === 'archived'){
            $status_name = 'Archived';
        } elseif ($fields === 'renewal'){
            $status_name = 'Renewal';
        }
		$statusView = array_merge(
			array(
				'employee_id' => $this->employee_info["Employee"]["id"],
				'user_view_id' => $viewId,
				$fields => ''
			),
			empty($statusView) ? array() : $statusView
		);
	
		$this->UserStatusViewSaleDeal->create();
			if( isset($statusView['id'])) $this->UserStatusViewSaleDeal->id = $statusView['id'];
		$statusView[$fields] = $statusView[$fields] ? 0 : 1;
		$statusView[$fields] = $switch !== null ? $switch : $statusView[$fields];
		$toggle_data = $this->UserStatusViewSaleDeal->save($statusView);
		if ($toggle_data ) {
			$this->Session->setFlash(__('Saved', true), 'success');
			$result = true;
		}else{
			$this->Session->setFlash(__('The user default view could not set as default. Please, try again.', true), 'error');
			$data['result'] = $result;
			if( $is_ajax ) die(json_encode($data) );
		}
		if( $is_ajax ){
			$this->set(compact('result','fields', 'toggle_data'));
			$this->render('index_ajax');
			die;
		}
        $this->redirect(array('action' => 'index?model=deal'));
    }
}