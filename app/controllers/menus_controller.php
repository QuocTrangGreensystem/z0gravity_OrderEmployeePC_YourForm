<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class MenusController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Menus';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
	function beforeFilter() {
        parent::beforeFilter();
		$has_new_designs = array(
			'projects' => array(
				'index',
				'your_form',
				'your_form_1',
				'your_form_2',
				'your_form_3',
				'your_form_4',
			),
			'project_dependencies' => array(
				'index',
				'view',
			),
			'project_created_vals' => array(
				'index',
			),
			'project_local_views' => array(
				'index',
			),
			'project_global_views' => array(
				'index',
			),
			'project_images' => array(
				'index',
			),
			'video' => array(
				'index',
			),
			'project_budget_sales' => array(
				'index',
			),
			'project_budget_internals' => array(
				'index',
			),
			'project_budget_externals' => array(
				'index',
			),
			'project_phase_plans' => array(
				'index',
			),
			'project_milestones' => array(
				'index',
			),
			'project_tasks' => array(
				'index',
			),
			'project_finances' => array(
				'index_plus',
			),
			'project_livrables' => array(
				'index',
			),
			'project_issues' => array(
				'index',
			),
			'project_risks' => array(
				'index',
			),
			'project_amrs_preview' => array(
				'indicator',
			),
			'project_staffings' => array(
				'visions',
			),
		);
		$this->set('has_new_designs', $has_new_designs);
	}

     /**
     * Index
     *
     * @return void
     * @access public
     */
    public function getMenu($controller, $function = ''){
        if( !$function )$function = 'index';
        return $this->Menu->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'functions' => $function,
                'controllers' => $controller,
                'company_id' => $this->employee_info['Company']['id']
            ),
            'order' => array('id' => 'DESC')
        ));
    }
    
    function index($model = null, $adminModify = null, $company_id = null) {
        $this->loadModel('Company');
        $modifyScreen = 'NO';
        $SAS = $this->is_sas;
		$companies = array();
		$menus = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            if($adminModify == 'change'){
                $modifyScreen = 'YES';
            }
            $menus = $this->Menu->find('threaded', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'model' => $model
                ),
                'order' => array('weight' => 'ASC')
            ));
            //$menus = !empty($menus) ? Set::combine($menus, '{n}.Menu.id', '{n}.Menu') : array();
            
            if(!empty($menus)){
                $menus = $this->Menu->find('threaded', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model' => $model
                    ),
                    'order' => array('weight' => 'ASC')
                ));
            } else {
                $menuDefaults = array(
                    array(
                        'name_eng' => 'Details',
                        'name_fre' => 'Fiche',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'edit',
                        'display' => 1,
                        'weight' => 1,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form',
                        'name_fre' => 'Votre Fiche',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form',
                        'display' => 1,
                        'weight' => 2,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form 1',
                        'name_fre' => 'Votre Fiche 1',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form_1',
                        'display' => 1,
                        'weight' => 3,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form_2',
                        'name_fre' => 'Votre Fiche_2',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form_2',
                        'display' => 1,
                        'weight' => 4,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form 3',
                        'name_fre' => 'Votre Fiche 3',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form_3',
                        'display' => 1,
                        'weight' => 5,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form 4',
                        'name_fre' => 'Votre Fiche 4',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form_4',
                        'display' => 1,
                        'weight' => 6,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Global View',
                        'name_fre' => 'Vue globale',

                        'company_id' => $company_id,
                        'controllers' => 'project_global_views',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 7,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Localization',
                        'name_fre' => 'Localization',

                        'company_id' => $company_id,
                        'controllers' => 'project_local_views',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 8,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Pictures',
                        'name_fre' => 'Pictures',

                        'company_id' => $company_id,
                        'controllers' => 'project_images',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 9,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Video',
                        'name_fre' => 'Vidéo',

                        'company_id' => $company_id,
                        'controllers' => 'video',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 10,
                        'parent_id' => ''
                    ),
                    // array(
                        // 'name_eng' => 'ZogMsg',
                        // 'name_fre' => 'ZogMsg',
                        // 'model' => $model,
                        // 'company_id' => $company_id,
                        // 'controllers' => 'zog_msgs',
                        // 'functions' => 'detail',
                        // 'display' => 1,
                        // 'weight' => 30,
                        // 'parent_id' => ''
                    // ),
                    array(
                        'name_eng' => 'Created value',
                        'name_fre' => 'Création valeur',

                        'company_id' => $company_id,
                        'controllers' => 'project_created_vals',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 11,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Teams',
                        'name_fre' => 'Equipe',

                        'company_id' => $company_id,
                        'controllers' => 'project_teams',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 12,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Part',
                        'name_fre' => 'Lot',

                        'company_id' => $company_id,
                        'controllers' => 'project_parts',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 13,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Phase',
                        'name_fre' => 'Phase',

                        'company_id' => $company_id,
                        'controllers' => 'project_phase_plans',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 14,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Tasks',
                        'name_fre' => 'Tâches',

                        'company_id' => $company_id,
                        'controllers' => 'project_tasks',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 15,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Milestones',
                        'name_fre' => 'Jalons',

                        'company_id' => $company_id,
                        'controllers' => 'project_milestones',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 16,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Staffing+',
                        'name_fre' => 'Staffing+',

                        'company_id' => $company_id,
                        'controllers' => 'project_staffings',
                        'functions' => 'visions',
                        'display' => 1,
                        'weight' => 17,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Budget',
                        'name_fre' => 'Budget',

                        'company_id' => $company_id,
                        'controllers' => 'project_budget_synthesis',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 18,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Acceptance',
                        'name_fre' => 'Recette',

                        'company_id' => $company_id,
                        'controllers' => 'project_acceptances',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 19,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Finance',
                        'name_fre' => 'Financement',

                        'company_id' => $company_id,
                        'controllers' => 'project_finances',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 20,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Risks',
                        'name_fre' => 'Risques',

                        'company_id' => $company_id,
                        'controllers' => 'project_risks',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 21,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Issues',
                        'name_fre' => 'Problème',

                        'company_id' => $company_id,
                        'controllers' => 'project_issues',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 22,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Decisions',
                        'name_fre' => 'Décisions',

                        'company_id' => $company_id,
                        'controllers' => 'project_decisions',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 23,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Deliverables',
                        'name_fre' => 'Livrables',

                        'company_id' => $company_id,
                        'controllers' => 'project_livrables',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 24,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Evolution',
                        'name_fre' => 'Evolution',

                        'company_id' => $company_id,
                        'controllers' => 'project_evolutions',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 25,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'KPI+',
                        'name_fre' => 'Indicateurs+',

                        'company_id' => $company_id,
                        'controllers' => 'project_amrs',
                        'functions' => 'index_plus',
                        'display' => 1,
                        'weight' => 26,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Dependency',
                        'name_fre' => 'Dépendance',

                        'company_id' => $company_id,
                        'controllers' => 'project_dependencies',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 27,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations',
                        'name_fre' => 'Attendus',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 28,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations 2',
                        'name_fre' => 'Attendus 2',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'view_1',
                        'display' => 1,
                        'weight' => 29,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations 3',
                        'name_fre' => 'Attendus 3',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'view_2',
                        'display' => 1,
                        'weight' => 30,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations 4',
                        'name_fre' => 'Attendus 4',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'view_3',
                        'display' => 1,
                        'weight' => 31,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations 5',
                        'name_fre' => 'Attendus 5',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'view_4',
                        'display' => 1,
                        'weight' => 32,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Expectations 6',
                        'name_fre' => 'Attendus 6',

                        'company_id' => $company_id,
                        'controllers' => 'project_expectations',
                        'functions' => 'view_5',
                        'display' => 1,
                        'weight' => 33,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Your Form+',
                        'name_fre' => 'Votre Fiche+',

                        'company_id' => $company_id,
                        'controllers' => 'projects',
                        'functions' => 'your_form_plus',
                        'display' => 1,
                        'weight' => 34,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Finance++',
                        'name_fre' => 'Finance++',
                        'company_id' => $company_id,
                        'controllers' => 'project_finances',
                        'functions' => 'plus',
                        'display' => 1,
                        'weight' => 35,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'FLASH ++:',
                        'name_fre' => 'FLASH ++',
                        'company_id' => $company_id,
                        'controllers' => 'projects_preview',
                        'functions' => 'flash_info',
                        'display' => 1,
                        'weight' => 36,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Dashboard ND',
                        'name_fre' => 'Dashboard ND',
                        'company_id' => $company_id,
                        'controllers' => 'project_amrs_preview',
                        'functions' => 'indicator',
                        'display' => 1,
                        'weight' => 37,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'PowerBI Dashboard',
                        'name_fre' => 'Tableau de bord PowerBI',
                        'company_id' => $company_id,
                        'controllers' => 'project_powerbi_dashboards',
                        'functions' => 'index',
                        'display' => 1,
                        'weight' => 38,
                        'parent_id' => ''
                    ),
                    array(
                        'name_eng' => 'Communication',
                        'name_fre' => 'Communication',
                        'company_id' => $company_id,
                        'controllers' => 'project_communications',
                        'functions' => 'edit',
                        'display' => 0,
                        'weight' => 39,
                        'parent_id' => ''
                    ),
                );
                $this->Menu->create();
                $this->Menu->saveAll($menuDefaults);
                $idOfBudget = $this->Menu->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id, 'model' => $model, 'name_eng' => 'Budget'),
                    'fields' => array('id')
                ));
                if(!empty($idOfBudget) && $idOfBudget['Menu']['id']){
                    $menuDefaultChild = array(
                        array(
                            'name_eng' => 'Synthesis',
                            'name_fre' => 'Synth',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_synthesis',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 21,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'Sales',
                            'name_fre' => 'Vente',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_sales',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 22,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'Internal Cost',
                            'name_fre' => 'Coût Iernterne',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_internals',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 23,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'External Cost',
                            'name_fre' => 'Coût Externe',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_externals',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 24,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'Provisional',
                            'name_fre' => 'Provisional',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_provisionals',
                            'functions' => 'index',
                            'display' => 0,
                            'weight' => 25,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'FY Budget',
                            'name_fre' => 'FY Budget',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_fiscals',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 26,
                            'parent_id' => $idOfBudget['Menu']['id']
                        ),
                        array(
                            'name_eng' => 'Purchase',
                            'name_fre' => 'Achat',

                            'company_id' => $company_id,
                            'controllers' => 'project_budget_purchases',
                            'functions' => 'index',
                            'display' => 1,
                            'weight' => 27,
                            'parent_id' => $idOfBudget['Menu']['id']
                        )
                    );
                    $this->Menu->create();
                    $this->Menu->saveAll($menuDefaultChild);
                }
                $menus = $this->Menu->find('threaded', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'model' => $model
                    ),
                    'order' => array('weight' => 'ASC')
                ));
                // $menus = !empty($menus) ? Set::combine($menus, '{n}.Menu.id', '{n}.Menu') : array();
            }
			$this->set(compact('company_id', 'companyName', 'modifyScreen'));
        }
		$this->set(compact('menus', 'SAS', 'model'));
        $this->checkMenuBeforeSave();

        // lay menu item trong history
        $this->loadModel('HistoryFilter');
        $saved = array(
            'path' => 'dissable_menu',
            'employee_id' => $this->employee_info['Employee']['id'],
        );
        $history = $this->HistoryFilter->find('first', array(
            'recursive' => -1,
            'conditions' => $saved,
            'fields' => array('params')
        ));
        $history_widget = $history_menu = array();
        if( !empty($history) ){
            $tmp_arr = array();
            $history_menu = json_decode($history['HistoryFilter']['params'], true);

            if(!empty($history_menu) && is_array($history_menu)){
                foreach ($history_menu as $key => $value) {
                  $tmp_arr = $value;
                }
                foreach ($tmp_arr as $key => $value) {
                    $history_widget[$value['widget_id']] =  array(
                        'name_eng' => $value['name_eng'],
                        'name_fre' => $value['name_fre'],
                        'controllers' => $value['controllers'],
                        'functions' => $value['functions'],
                        'enable_newdesign' => isset ($value['enable_newdesign']) ? $value['enable_newdesign'] : '',
						
                    );
                    
                }
            }
        } 
		// debug(  $history_widget);
		// exit;
        if(!empty($history_widget )){
            $this->set('widgets', array_merge($this->widgets($model), $history_widget));
            
        }else{
            $this->set('widgets', $this->widgets($model));
        } 
        
        $this->render('manage');
    }
    // public function history_widget(){

    //     $this->loadModel('HistoryFilter');
    //     $saved = array(
    //         'path' => 'dissable_menu',
    //         'employee_id' => $this->employee_info['Employee']['id'],
    //     );
    //     $checked = $this->HistoryFilter->find('first', array(
    //         'recursive' => -1,
    //         'conditions' => $saved,
    //         'fields' => array('id', 'params')
    //     ));

        
    //     if(!empty($checked)){
    //         $wd_new =  array();
    //         foreach ($checked['HistoryFilter']['params'] as $key => $value) {
    //            return ($key => $value);
    //         }
    //         // $new = array_merge($this->widgets($model), json_decode($checked['HistoryFilter']['params'], true));
    //         // $this->set('widgets', array_merge($this->widgets($model), json_decode($checked['HistoryFilter']['params'], true)));
    //         debug($wd_new); 
    //     }
    // }
    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany($company_id)) {
            foreach ($this->data as $id => $weight) {
                $last = $this->Menu->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('Menu.id' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->Menu->id = $last['Menu']['id'];
                    $this->Menu->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        exit(0);
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($model = null) {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $companyName = $this->_getCompany();
            $company_id = $companyName['Company']['id'];
            $data = array(
                'model' => $model,
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes'),
                'default_screen' => (isset($this->data['default_screen']) && $this->data['default_screen'] == 'yes')
            );
            if($data['display']){
                $menus = array();
                if (!empty($this->data['id'])) {
                    $menus = $this->Menu->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,

                            'NOT' => array('Menu.id' => $this->data['id'])
                        ),
                        'fields' => array('id', 'default_screen')
                    ));
                } else {
                    $menus = $this->Menu->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'model' => $model
                        ),
                        'fields' => array('id', 'default_screen')
                    ));
                }
                if(!empty($menus)){
                    foreach($menus as $id => $menu){
                        $this->Menu->id = $id;
                        $this->Menu->save(array('default_screen' => 0));
                    }
                }
            }
            $this->Menu->create();
            if (!empty($this->data['id'])) {
                $this->Menu->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->Menu->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->Menu->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null, $model = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index', $model));
        }
        if ($this->_getCompany($company_id) && $this->Menu->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index', $model));
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
    public function autoInsert($company_id){
        $model = 'project';
        $menu = array(
            array(
                'name_eng' => 'Details',
                'name_fre' => 'Fiche',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'edit',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form',
                'name_fre' => 'Votre Fiche',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form 1',
                'name_fre' => 'Votre Fiche 1',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form_1',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form 2',
                'name_fre' => 'Votre Fiche 2',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form_2',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form 3',
                'name_fre' => 'Votre Fiche 3',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form_3',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form 4',
                'name_fre' => 'Votre Fiche 4',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form_4',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Global View',
                'name_fre' => 'Vue globale',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_global_views',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Localization',
                'name_fre' => 'Localization',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_local_views',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Pictures',
                'name_fre' => 'Pictures',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_images',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Video',
                'name_fre' => 'Vidéo',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'video',
                'functions' => 'index',
                'display' => 0,
                'parent_id' => ''
            ),
            // array(
                // 'name_eng' => 'ZogMsg',
                // 'name_fre' => 'ZogMsg',
                // 'model' => $model,
                // 'company_id' => $company_id,
                // 'controllers' => 'zog_msgs',
                // 'functions' => 'detail',
                // 'display' => 0,
                // 'parent_id' => ''
            // ),
            array(
                'name_eng' => 'Created value',
                'name_fre' => 'Création valeur',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_created_vals',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Teams',
                'name_fre' => 'Equipe',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_teams',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Part',
                'name_fre' => 'Lot',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_parts',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Phase',
                'name_fre' => 'Phase',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_phase_plans',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Tasks',
                'name_fre' => 'Tâches',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_tasks',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Milestones',
                'name_fre' => 'Jalons',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_milestones',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Staffing+',
                'name_fre' => 'Staffing+',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_staffings',
                'functions' => 'visions',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Budget',
                'name_fre' => 'Budget',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_synthesis',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Acceptance',
                'name_fre' => 'Recette',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_acceptances',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Finance',
                'name_fre' => 'Financement',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_finances',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Risks',
                'name_fre' => 'Risques',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_risks',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Issues',
                'name_fre' => 'Problème',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_issues',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Decisions',
                'name_fre' => 'Décisions',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_decisions',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Deliverables',
                'name_fre' => 'Livrables',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_livrables',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Evolution',
                'name_fre' => 'Evolution',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_evolutions',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'KPI+',
                'name_fre' => 'Indicateurs+',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_amrs',
                'functions' => 'index_plus',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Dependency',
                'name_fre' => 'Dépendance',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_dependencies',
                'functions' => 'index',
                'display' => 0,
                'parent_id' => '',
                'weight' => 'project_amrs::index_plus'
            ),
            array(
                'name_eng' => 'Synthesis',
                'name_fre' => 'Synth',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_synthesis',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'Sales',
                'name_fre' => 'Vente',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_sales',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'Internal Cost',
                'name_fre' => 'Coût Iernterne',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_internals',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'External Cost',
                'name_fre' => 'Coût Externe',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_externals',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'Provisional',
                'name_fre' => 'Provisional',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_provisionals',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'FY Budget',
                'name_fre' => 'FY Budget',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_fiscals',
                'functions' => 'index',
                'display' => 1,
                'weight' => 'project_budget_provisionals::index',
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'Purchase',
                'name_fre' => 'Achat',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_budget_purchases',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => 'project_budget_synthesis::index'
            ),
            array(
                'name_eng' => 'Expectations',
                'name_fre' => 'Attendus',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'index',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Expectations 2',
                'name_fre' => 'Attendus 2',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'view_1',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Expectations 3',
                'name_fre' => 'Attendus 3',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'view_2',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Expectations 4',
                'name_fre' => 'Attendus 4',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'view_3',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Expectations 5',
                'name_fre' => 'Attendus 5',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'view_4',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Expectations 6',
                'name_fre' => 'Attendus 6',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_expectations',
                'functions' => 'view_5',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Your Form+',
                'name_fre' => 'Votre Fiche+',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects',
                'functions' => 'your_form_plus',
                'display' => 1,
                'parent_id' => ''
            ),
             array(
                'name_eng' => 'Flash++',
                'name_fre' => 'Flash++',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'projects_preview',
                'functions' => 'flash_info',
                'display' => 1,
                'parent_id' => ''
            ),
            array(
                'name_eng' => 'Dashboard ND',
                'name_fre' => 'Dashboard ND',
                'model' => $model,
                'company_id' => $company_id,
                'controllers' => 'project_amrs_preview',
                'functions' => 'indicator',
                'display' => 1,
                'parent_id' => ''
            ),
			array(
				'name_eng' => 'PowerBI Dashboard',
				'name_fre' => 'Tableau de bord PowerBI',
				'company_id' => $company_id,
				'controllers' => 'project_powerbi_dashboards',
				'functions' => 'index',
				'display' => 1,
				'weight' => 38,
				'parent_id' => ''
			),
			array(
				'name_eng' => 'Communication',
				'name_fre' => 'Communication',
				'company_id' => $company_id,
				'controllers' => 'project_communications',
				'functions' => 'edit',
				'display' => 0,
				'weight' => 39,
				'parent_id' => ''
			),
        );
        $this->insertMenu($menu, $company_id);
        return;
    }
    public function insertMenu($data = array(), $company_id = null){
        if( empty($data) )return;
        if( !$company_id ){
            return;
            $company_id = !empty($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : '';
        }
        $list = array();
        $i = 0;
        //search if not exists
        foreach($data as $ar){
            $count = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'controllers' => $ar['controllers'],
                    'functions' => $ar['functions'],
                    'company_id' => $company_id
                )
            ));
            if( empty($count) ){
                //check parent_id
                if( $ar['parent_id'] ){
                    if( !isset($list[ $ar['parent_id'] ]) ){
                        $parent = explode('::', $ar['parent_id']);
                        $menu = $this->Menu->find('first', array(
                            'conditions' => array(
                                'controllers' => $parent[0],
                                'functions' => $parent[1],
                                'company_id' => $company_id
                            )
                        ));
                        $menu = $menu['Menu'];
                        $list[ $menu['controllers'] . '::' . $menu['functions'] ] = $menu;
                    }
                    $ar['parent_id'] = $list[ $ar['parent_id'] ]['id'];
                }
                if( isset($ar['weight']) ){
                    if( is_string($ar['weight']) ){
                        if( !isset($list[ $ar['weight'] ]) ){
                            $parent = explode('::', $ar['weight']);
                            $menu = $this->Menu->find('first', array(
                                'conditions' => array(
                                    'controllers' => $parent[0],
                                    'functions' => $parent[1],
                                    'company_id' => $company_id
                                )
                            ));
                            $menu = $menu['Menu'];
                            $list[ $menu['controllers'] . '::' . $menu['functions'] ] = $menu;
                        }
                        $ar['weight'] = intval($list[ $ar['weight'] ]['weight']) + 1;
                    }
                } else {
                    $ar['weight'] = ++$i;
                }
                //insert menu
                $this->Menu->create();
                $this->Menu->save($ar);
            }
        }
        return;
    }
    public function widgets($model = 'project'){
        $widgets = array(
            'project' => array(
                'flash_info' => array(
                    'name_eng' => 'Flash++',
                    'name_fre' => 'Flash++',
                    'controllers' => 'projects_preview',
                    'functions' => 'flash_info'
                ),
                'indicator' => array(
                    'name_eng' => 'Dashboard ND',
                    'name_fre' => 'Dashboard ND',
                    'controllers' => 'project_amrs_preview',
                    'functions' => 'indicator'
                ),
                'details' => array(
                    'name_eng' => 'Details',
                    'name_fre' => 'Fiche',
                    'controllers' => 'projects',
                    'functions' => 'edit'
                ),
                'your_form' => array(
                    'name_eng' => 'Your Form',
                    'name_fre' => 'Votre Fiche',
                    'controllers' => 'projects',
                    'functions' => 'your_form'
                ),
                'your_form_1' => array(
                    'name_eng' => 'Your Form 1',
                    'name_fre' => 'Votre Fiche 1',
                    'controllers' => 'projects',
                    'functions' => 'your_form_1'
                ),
                'your_form_2' => array(
                    'name_eng' => 'Your Form 2',
                    'name_fre' => 'Votre Fiche 2',
                    'controllers' => 'projects',
                    'functions' => 'your_form_2'
                ),
                'your_form_3' => array(
                    'name_eng' => 'Your Form 3',
                    'name_fre' => 'Votre Fiche 3',
                    'controllers' => 'projects',
                    'functions' => 'your_form_3'
                ),
                'your_form_4' => array(
                    'name_eng' => 'Your Form 4',
                    'name_fre' => 'Votre Fiche 4',
                    'controllers' => 'projects',
                    'functions' => 'your_form_4'
                ),
                'global_view' => array(
                    'name_eng' => 'Global View',
                    'name_fre' => 'Vue globale',
                    'controllers' => 'project_global_views',
                    'functions' => 'index'
                ),
                'local_view' => array(
                    'name_eng' => 'Localization',
                    'name_fre' => 'Localization',
                    'controllers' => 'project_local_views',
                    'functions' => 'index'
                ),
                'image' => array(
                    'name_eng' => 'Pictures',
                    'name_fre' => 'Pictures',
                    'controllers' => 'project_images',
                    'functions' => 'index'
                ),
                'video' => array(
                    'name_eng' => 'Video',
                    'name_fre' => 'Vidéo',
                    'controllers' => 'video',
                    'functions' => 'index'
                ),
                'created_value' => array(
                    'name_eng' => 'Created value',
                    'name_fre' => 'Création valeur',
                    'controllers' => 'project_created_vals',
                    'functions' => 'index',
                ),
                'team' => array(
                    'name_eng' => 'Teams',
                    'name_fre' => 'Equipe',
                    'controllers' => 'project_teams',
                    'functions' => 'index'
                ),
                'part' => array(
                    'name_eng' => 'Part',
                    'name_fre' => 'Lot',
                    'controllers' => 'project_parts',
                    'functions' => 'index'
                ),
                'phase' => array(
                    'name_eng' => 'Phase',
                    'name_fre' => 'Phase',
                    'controllers' => 'project_phase_plans',
                    'functions' => 'index'
                ),
                'task' => array(
                    'name_eng' => 'Tasks',
                    'name_fre' => 'Tâches',
                    'controllers' => 'project_tasks',
                    'functions' => 'index'
                ),
                'milestone' => array(
                    'name_eng' => 'Milestones',
                    'name_fre' => 'Jalons',
                    'controllers' => 'project_milestones',
                    'functions' => 'index'
                ),
                'staffing' => array(
                    'name_eng' => 'Staffing+',
                    'name_fre' => 'Staffing+',
                    'controllers' => 'project_staffings',
                    'functions' => 'visions'
                ),
                'acceptance' => array(
                    'name_eng' => 'Acceptance',
                    'name_fre' => 'Recette',
                    'controllers' => 'project_acceptances',
                    'functions' => 'index'
                ),
                'finance' => array(
                    'name_eng' => 'Finance',
                    'name_fre' => 'Financement',
                    'controllers' => 'project_finances',
                    'functions' => 'index'
                ),
                'finance_plus' => array(
                    'name_eng' => 'Finance+',
                    'name_fre' => 'Financement+',
                    'controllers' => 'project_finances',
                    'functions' => 'index_plus'
                ),
                'risk' => array(
                    'name_eng' => 'Risks',
                    'name_fre' => 'Risques',
                    'controllers' => 'project_risks',
                    'functions' => 'index'
                ),
                'issue' => array(
                    'name_eng' => 'Issues',
                    'name_fre' => 'Problème',
                    'controllers' => 'project_issues',
                    'functions' => 'index'
                ),
                'decision' => array(
                    'name_eng' => 'Decisions',
                    'name_fre' => 'Décisions',
                    'controllers' => 'project_decisions',
                    'functions' => 'index'
                ),
                'deliverable' => array(
                    'name_eng' => 'Deliverables',
                    'name_fre' => 'Livrables',
                    'controllers' => 'project_livrables',
                    'functions' => 'index'
                ),
                'evolution' => array(
                    'name_eng' => 'Evolution',
                    'name_fre' => 'Evolution',
                    'controllers' => 'project_evolutions',
                    'functions' => 'index'
                ),
                'kpi' => array(
                    'name_eng' => 'KPI+',
                    'name_fre' => 'Indicateurs+',
                    'controllers' => 'project_amrs',
                    'functions' => 'index_plus'
                ),
                'dependency' => array(
                    'name_eng' => 'Dependency',
                    'name_fre' => 'Dépendance',
                    'controllers' => 'project_dependencies',
                    'functions' => 'index'
                ),
                'synthesis' => array(
                    'name_eng' => 'Synthesis',
                    'name_fre' => 'Synth',
                    'controllers' => 'project_budget_synthesis',
                    'functions' => 'index'
                ),
                'sale' => array(
                    'name_eng' => 'Sales',
                    'name_fre' => 'Vente',
                    'controllers' => 'project_budget_sales',
                    'functions' => 'index'
                ),
                'internal_cost' => array(
                    'name_eng' => 'Internal Cost',
                    'name_fre' => 'Coût Iernterne',
                    'controllers' => 'project_budget_internals',
                    'functions' => 'index',
                ),
                'external_cost' => array(
                    'name_eng' => 'External Cost',
                    'name_fre' => 'Coût Externe',
                    'controllers' => 'project_budget_externals',
                    'functions' => 'index'
                ),
                'provisional' => array(
                    'name_eng' => 'Provisional',
                    'name_fre' => 'Provisional',
                    'controllers' => 'project_budget_provisionals',
                    'functions' => 'index'
                ),
                'fy_budget' => array(
                    'name_eng' => 'FY Budget',
                    'name_fre' => 'FY Budget',
                    'controllers' => 'project_budget_fiscals',
                    'functions' => 'index'
                ),
                'purchase' => array(
                    'name_eng' => 'Purchase',
                    'name_fre' => 'Achat',
                    'controllers' => 'project_budget_purchases',
                    'functions' => 'index'
                ),
                // 'zog_msgs' => array(
                    // 'name_eng' => 'ZogMsg',
                    // 'name_fre' => 'ZogMsg',
                    // 'controllers' => 'zog_msgs',
                    // 'functions' => 'index'
                // ),
                'expectations' => array(
                    'name_eng' => 'Expectations',
                    'name_fre' => 'Attendus',
                    'controllers' => 'project_expectations',
                    'functions' => 'index'
                ),
                'expectations_2' => array(
                    'name_eng' => 'Expectations 2',
                    'name_fre' => 'Attendus 2',
                    'controllers' => 'project_expectations',
                    'functions' => 'view_1'
                ),
                'expectations_3' => array(
                    'name_eng' => 'Expectations 3',
                    'name_fre' => 'Attendus 3',
                    'controllers' => 'project_expectations',
                    'functions' => 'view_2'
                ),
                'expectations_4' => array(
                    'name_eng' => 'Expectations 4',
                    'name_fre' => 'Attendus 4',
                    'controllers' => 'project_expectations',
                    'functions' => 'view_3'
                ),
                'expectations_5' => array(
                    'name_eng' => 'Expectations 5',
                    'name_fre' => 'Attendus 5',
                    'controllers' => 'project_expectations',
                    'functions' => 'view_4'
                ),
                'expectations_6' => array(
                    'name_eng' => 'Expectations 6',
                    'name_fre' => 'Attendus 6',
                    'controllers' => 'project_expectations',
                    'functions' => 'view_5'
                ),
                'your_form_plus' => array(
                    'name_eng' => 'Your Form+',
                    'name_fre' => 'Votre Fiche+',
                    'controllers' => 'projects',
                    'functions' => 'your_form_plus'
                ),
                'finance_two_plus' => array(
                    'name_eng' => 'Finance++',
                    'name_fre' => 'Financement++',
                    'controllers' => 'project_finances',
                    'functions' => 'plus'
                ),
				'communications' => array(
					'name_eng' => 'Communication',
					'name_fre' => 'Communication',
					'controllers' => 'project_communications',
					'functions' => 'edit',
				),
            )
        );
		foreach ( $widgets['project'] as $key => $value){
			$widgets['project'][$key]['enable_newdesign'] = 0;
		}
        return isset($widgets[$model]) ? $widgets[$model] : array();
    }
    public function fixWidgets(){
        $widgets = $this->widgets();
        $db = $this->Menu->getDataSource();
        foreach($widgets as $widget_id => $widget){
            $this->Menu->updateAll(array(
                'widget_id' => $db->value($widget_id, 'string'),
                'display' => $db->value($widget_id, 0 )
            ), array(
                'functions' => $widget['functions'],
                'controllers' => $widget['controllers']
            ));
        }
        $this->redirect('/menus/index/project');
    }

    public function checkMenuBeforeSave($model = 'project'){ 
        $this->loadModels('Menu','Employee');
        $employee = $this->Session->read("Auth.employee_info");
        $company_id = !empty($employee["Company"]["id"]) ? $employee["Company"]["id"] : 0;
        $checks = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model' => 'project',
            ),
            'fields' => array('id','parent_id','weight','widget_id'),
        ));
        $menu_ids = $pa_menu_ids = $old_ids = $widget_ids = $lap_widget_ids = $old_widget_ids = array();
        foreach ($checks as $check) {
            $menu_ids[]    = $check['Menu']['id'];
            if(!empty($check['Menu']['widget_id'])) $widget_ids[]  = $check['Menu']['widget_id'];
            else  $this->Menu->delete($check['Menu']['id']);
            }
        
        foreach ($checks as $check) {
            $pa_menu_ids[] = $check['Menu']['parent_id'];
            if(!empty($check['Menu']['parent_id']) && !in_array($check['Menu']['parent_id'], $menu_ids) ){
                $old_ids[] = $check['Menu']['id'];
                $old_widget_ids[] = $check['Menu']['widget_id'];
            } 
        }
        $widget_laps = array_count_values($widget_ids);
        foreach ($checks as $check) {
            if(!empty($check['Menu']['widget_id']) && $widget_laps[$check['Menu']['widget_id']] > 1 && !in_array($check['Menu']['id'], $old_ids) && !in_array($check['Menu']['widget_id'], $old_widget_ids)){
                $lap_widget_ids[] = $check['Menu']['id'];
            }
        }
        $arr_ids = array_merge($old_ids, $lap_widget_ids);

        if(!empty($arr_ids)){
            foreach($arr_ids as $id){
                $this->Menu->delete($id);
            }
            $this->order($company_id);
        }
    }

    public function save($model = 'project'){
        $map = array();
        if( !empty($this->data)){
			$listID = array_keys($this->data['Menu']);
			$listID = array_filter( $listID, function($v){
				return is_numeric($v);
			});
			$check = $this->Menu->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $listID,
					'company_id' => $this->employee_info['Company']['id'],
				),
			));
			if( $check != count($listID)){
				$this->Session->setFlash(__('Not saved', true), 'error');
				$this->redirect($this->referer());
			}
            $this->saveOldMenu($this->data);
            foreach($this->data['Menu'] as $id => $menu){
                if( strpos($menu['parent_id'], 'new_') !== false ){
                    $menu['parent_id'] = $map[ $menu['parent_id'] ];
                }
                if( is_numeric($id) ){
                    if( $menu['delete'] ){
                        $this->Menu->delete($id);
                    } else {
                        $this->Menu->id = $id;
                        $this->Menu->save($menu);
                        $this->saveHistoryForMenu();
                    }
                } else {
                    //add new
                    $this->Menu->create();
                    $this->Menu->save($menu);
                    $map[$id] = $this->Menu->id;
                    $this->saveHistoryForMenu();
                }
            }

            // remove display child when parent not display 
            
            foreach($this->data['Menu'] as $id => $menu){
                if( is_numeric($id) ){
                    if( $menu['delete'] ){
                       
                    } else {
                       if($menu['display'] == 0){
                            $arr_pa_menu = $this->Menu->find('all', array(
                                'recursive' => -1,
                                'conditions' => array(
                                    'company_id' => $menu['company_id'],
                                    'parent_id' => $id,
                                ),
                                'fields' => array('id'),
                            ));
                            if(!empty($arr_pa_menu)){
                                foreach ($arr_pa_menu as $key => $value) {
                                    $this->Menu->id = $value['Menu']['id'];
                                    $this->Menu->saveField('display', 0);
                                }
                            }
                        }
                    }
                }
            }
			// Change default screen
            // Edit by Huynh
            // Create 26-07-2018 on Prod
			// Modified: 10-10-2018 on Nextversion
            foreach($this->data['Menu'] as $id => $menu){

                // kiem tra dieu kien neu la default screen ma khong duoc hien thi
                if( is_numeric($id) &&  $menu['default_screen'] == 1 && $menu['display'] == 0){
                    // remove deafult
                        $this->Menu->id = $id;
                        $this->Menu->saveField('default_screen', 0);
                    // add default for first display screen
                        foreach($this->data['Menu'] as $id => $menu){
                            if( is_numeric($id) && $menu['display'] == 1){
                                $this->Menu->id = $id;
                                $this->Menu->saveField('default_screen', 1);
                                break;
                            }
                        }
                    // break loop if finish
                    break;
                }
            }						
           
            $this->Session->setFlash(__('Saved', true), 'success');

        }
        $this->redirect(array('action' => 'index', 'project'));
       
    }

    public function saveOldMenu($oldMenu = array()){
        return $oldMenu;
    }
    public function saveHistoryForMenu(){
        $this->loadModel('HistoryFilter');
        if(!empty($this->data)){
            $emp_id = $this->employee_info['Employee']['id'];
            if($this->employee_info['Employee']['company_id'] == null){
                $this->loadModel('Employee');
                $_idEm = $this->Employee->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'fullname' => $this->employee_info['Employee']['fullname'],
                        'company_id' => null
                    ),
                    'fields' => array('id', 'fullname')
                ));
                $emp_id = !empty($_idEm['Employee']['id']) ? $_idEm['Employee']['id'] : 0;
            }
            $path = 'dissable_menu';
            $saved = array(
                'path' => $path,
                'employee_id' => $emp_id
            );
            $checked = $this->HistoryFilter->find('first', array(
                'recursive' => -1,
                'conditions' => $saved,
                'fields' => array('id', 'params')
            ));
            $saved['params'] = json_encode($this->data);
            if(!empty($checked)){
                $this->HistoryFilter->id = $checked['HistoryFilter']['id'];
                $this->HistoryFilter->save($saved);
            }else{
                $this->HistoryFilter->create();
                $this->HistoryFilter->save($saved);
            }
        }
    }

    public function getWidgetDedault( $widget = array() ){
    }
    private function check_widget($widget_id = 'flash_info', $company_id){
       $check_widget = $this->Menu->find('first',  array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'widget_id' => $widget_id,
            )
        )); 
       if(empty($check_widget)) return true;
       return false;
    }
    function add_new_menu(){
        // return 1;
        $company_ids = $this->Menu->find('all',  array(
            'recursive' => -1,
            'fields' => array('DISTINCT company_id')
            )
        );
		
        $company_ids = !empty($company_ids) ? Set::extract($company_ids, '{n}.Menu.company_id') : array();
		$n = 0;
        foreach ($company_ids as $company_id) {
            if($company_id){
                $widgets = $this->new_widgets($company_id);
                foreach ($widgets as $key => $widget) {
                    if($this->check_widget($key, $company_id)){
                        $this->Menu->create();
                        $id = $this->Menu->id;
                        if($this->Menu->save($widget)){
							$n++;
						}
                    }
                }
            }
        }
		if($n > 0) die('Widgets has added');
		else die('All menu already added');
		
    }
    private function new_widgets($company_id){
        $new_widgets = array(
            'flash_info' => array(
                'company_id' => $company_id,
                'name_eng' => 'Flash++',
                'name_fre' => 'Flash++',
                'model' => 'project',
                'controllers' => 'projects_preview',
                'functions' => 'flash_info',
                'display' => '0',
                'weight' => '32',
                'default_screen' =>'0',
                'created' => time(),
                'updated' => time(),
                'widget_id' => 'flash_info'
            ),
            'indicator' => array(
                'company_id' => $company_id,
                'name_eng' => 'Dashboard ND',
                'name_fre' => 'Dashboard ND',
                'model' => 'project',
                'controllers' => 'project_amrs_preview',
                'functions' => 'indicator',
                'display' => '0',
                'weight' => '33',
                'default_screen' =>'0',
                'created' => time(),
                'updated' => time(),
                'widget_id' => 'indicator'
            ),
			'powerbi_dashboard' => array(
				'company_id' => $company_id,
				'name_eng' => 'PowerBI Dashboard',
				'name_fre' => 'Tableau de bord PowerBI',
				'controllers' => 'project_powerbi_dashboards',
				'model' => 'project',
				'functions' => 'index',
				'display' => 0,
				'weight' => 38,
				'created' => time(),
                'updated' => time(),
				'widget_id' => 'powerbi_dashboard'
			),
			'communications' => array(
				'name_eng' => 'Communication',
				'name_fre' => 'Communication',
				'company_id' => $company_id,
				'controllers' => 'project_communications',
				'functions' => 'edit',
				'model' => 'project',
				'display' => 0,
				'weight' => 39,
				'created' => time(),
                'updated' => time(),
				'widget_id' => 'communications'
			),

        );
        return $new_widgets;
    }
}