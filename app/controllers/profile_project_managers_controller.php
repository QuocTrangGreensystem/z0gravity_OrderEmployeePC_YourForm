<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProfileProjectManagersController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProfileProjectManagers';

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
        $listProfiles = $this->ProfileProjectManager->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            )
        ));
        $this->set(compact('listProfiles', 'company_id'));
	}

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $this->ProfileProjectManager->create();
            if (!empty($this->data['id'])) {
                $this->ProfileProjectManager->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->ProfileProjectManager->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Profile could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->ProfileProjectManager->id;
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
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Budget Setting', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProfileProjectManager'));
        $isUsig = $this->isUsing($id);
        if($check && !$isUsig){
			if ($this->ProfileProjectManager->delete($id)) {
				$this->Session->setFlash(__('Deleted', true), 'success');
				$this->redirect(array('action' => 'index'));
			}
        }
		$this->Session->setFlash(__('Profile was not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
	private function isUsing($id){
        $this->loadModels('Employee', 'ProfileProjectManager');
        $count = $this->Employee->find('count', array(
            'recursive' => -1,
            'conditions' => array('Employee.profile_account' => $id)
        ));
		return $count;
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
    /**
     * edit profile
     *
     * @return void
     * @access public
     */
    function edit($model_id = null) {
        $company_id = $this->employee_info['Company']['id'];
        $modifyScreen = 'YES';
        $this->loadModels('ProfileProjectManagerDetail');
        $menus = $this->ProfileProjectManagerDetail->find('threaded', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'model_id' => $model_id
            ),
            'order' => array('weight' => 'ASC')
        ));
        if(!empty($menus)){
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
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'details'
                ),
                array(
                    'name_eng' => 'Your Form',
                    'name_fre' => 'Votre Fiche',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form',
                    'display' => 0,
                    'weight' => 2,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form'
                ),
                array(
                    'name_eng' => 'Your Form 1',
                    'name_fre' => 'Votre Fiche 1',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form_1',
                    'display' => 0,
                    'weight' => 3,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form_1'
                ),
                array(
                    'name_eng' => 'Your Form_2',
                    'name_fre' => 'Votre Fiche_2',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form_2',
                    'display' => 0,
                    'weight' => 4,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form_2'
                ),
                array(
                    'name_eng' => 'Your Form 3',
                    'name_fre' => 'Votre Fiche 3',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form_3',
                    'display' => 0,
                    'weight' => 5,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form_3'
                ),
                array(
                    'name_eng' => 'Your Form 4',
                    'name_fre' => 'Votre Fiche 4',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form_4',
                    'display' => 0,
                    'weight' => 6,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form_4'
                ),
                array(
                    'name_eng' => 'Global View',
                    'name_fre' => 'Vue globale',
                    'company_id' => $company_id,
                    'controllers' => 'project_global_views',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 7,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'global_view'
                ),
                array(
                    'name_eng' => 'Localization',
                    'name_fre' => 'Localization',
                    'company_id' => $company_id,
                    'controllers' => 'project_local_views',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 8,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'local_view'
                ),
                array(
                    'name_eng' => 'Pictures',
                    'name_fre' => 'Pictures',
                    'company_id' => $company_id,
                    'controllers' => 'project_images',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 9,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'image'
                ),
                array(
                    'name_eng' => 'Video',
                    'name_fre' => 'Vidéo',
                    'company_id' => $company_id,
                    'controllers' => 'video',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 10,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'video'
                ),
                // array(
                    // 'name_eng' => 'ZogMsg',
                    // 'name_fre' => 'ZogMsg',
                    // 'model' => $model_id,
                    // 'company_id' => $company_id,
                    // 'controllers' => 'zog_msgs',
                    // 'functions' => 'detail',
                    // 'display' => 0,
                    // 'weight' => 30,
                    // 'parent_id' => '',
                    // 'model_id' => $model_id,
                    // 'widget_id' => 'zog_msgs'
                // ),
                array(
                    'name_eng' => 'Created value',
                    'name_fre' => 'Création valeur',
                    'company_id' => $company_id,
                    'controllers' => 'project_created_vals',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 11,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'created_value'
                ),
                array(
                    'name_eng' => 'Teams',
                    'name_fre' => 'Equipe',
                    'company_id' => $company_id,
                    'controllers' => 'project_teams',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 12,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'team'
                ),
                array(
                    'name_eng' => 'Part',
                    'name_fre' => 'Lot',
                    'company_id' => $company_id,
                    'controllers' => 'project_parts',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 13,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'part'
                ),
                array(
                    'name_eng' => 'Phase',
                    'name_fre' => 'Phase',
                    'company_id' => $company_id,
                    'controllers' => 'project_phase_plans',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 14,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'phase'
                ),
                array(
                    'name_eng' => 'Tasks',
                    'name_fre' => 'Tâches',
                    'company_id' => $company_id,
                    'controllers' => 'project_tasks',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 15,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'task'
                ),
                array(
                    'name_eng' => 'Milestones',
                    'name_fre' => 'Jalons',
                    'company_id' => $company_id,
                    'controllers' => 'project_milestones',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 16,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'milestone'
                ),
                array(
                    'name_eng' => 'Staffing+',
                    'name_fre' => 'Staffing+',
                    'company_id' => $company_id,
                    'controllers' => 'project_staffings',
                    'functions' => 'visions',
                    'display' => 1,
                    'weight' => 17,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'staffing'
                ),
                array(
                    'name_eng' => 'Budget',
                    'name_fre' => 'Budget',
                    'company_id' => $company_id,
                    'controllers' => 'project_budget_synthesis',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 18,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'synthesis'
                ),
                array(
                    'name_eng' => 'Acceptance',
                    'name_fre' => 'Recette',
                    'company_id' => $company_id,
                    'controllers' => 'project_acceptances',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 19,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'acceptance'
                ),
                array(
                    'name_eng' => 'Finance',
                    'name_fre' => 'Financement',
                    'company_id' => $company_id,
                    'controllers' => 'project_finances',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 20,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'finance'
                ),
                array(
                    'name_eng' => 'Risks',
                    'name_fre' => 'Risques',
                    'company_id' => $company_id,
                    'controllers' => 'project_risks',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 21,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'risk'
                ),
                array(
                    'name_eng' => 'Issues',
                    'name_fre' => 'Problème',
                    'company_id' => $company_id,
                    'controllers' => 'project_issues',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 22,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'issue'
                ),
                array(
                    'name_eng' => 'Decisions',
                    'name_fre' => 'Décisions',
                    'company_id' => $company_id,
                    'controllers' => 'project_decisions',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 23,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'decision'
                ),
                array(
                    'name_eng' => 'Deliverables',
                    'name_fre' => 'Livrables',
                    'company_id' => $company_id,
                    'controllers' => 'project_livrables',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 24,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'deliverable'
                ),
                array(
                    'name_eng' => 'Evolution',
                    'name_fre' => 'Evolution',
                    'company_id' => $company_id,
                    'controllers' => 'project_evolutions',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 25,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'evolution'
                ),
                array(
                    'name_eng' => 'KPI+',
                    'name_fre' => 'Indicateurs+',
                    'company_id' => $company_id,
                    'controllers' => 'project_amrs',
                    'functions' => 'index_plus',
                    'display' => 1,
                    'weight' => 26,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'kpi'
                ),
                array(
                    'name_eng' => 'Dependency',
                    'name_fre' => 'Dépendance',
                    'company_id' => $company_id,
                    'controllers' => 'project_dependencies',
                    'functions' => 'index',
                    'display' => 1,
                    'weight' => 27,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'dependency'
                ),
                array(
                    'name_eng' => 'Expectations',
                    'name_fre' => 'Attendus',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'index',
                    'display' => 0,
                    'weight' => 28,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations'
                ),
                array(
                    'name_eng' => 'Expectations 2',
                    'name_fre' => 'Attendus 2',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'view_1',
                    'display' => 0,
                    'weight' => 29,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations_2'
                ),
                array(
                    'name_eng' => 'Expectations 3',
                    'name_fre' => 'Attendus 3',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'view_2',
                    'display' => 0,
                    'weight' => 30,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations_3'
                ),
                array(
                    'name_eng' => 'Expectations 4',
                    'name_fre' => 'Attendus 4',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'view_3',
                    'display' => 0,
                    'weight' => 31,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations_4'
                ),
                array(
                    'name_eng' => 'Expectations 5',
                    'name_fre' => 'Attendus 5',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'view_4',
                    'display' => 0,
                    'weight' => 32,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations_5'
                ),
                array(
                    'name_eng' => 'Expectations 6',
                    'name_fre' => 'Attendus 6',
                    'company_id' => $company_id,
                    'controllers' => 'project_expectations',
                    'functions' => 'view_5',
                    'display' => 0,
                    'weight' => 33,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'expectations_6'
                ),
                array(
                    'name_eng' => 'Your Form+',
                    'name_fre' => 'Votre Fiche+',
                    'company_id' => $company_id,
                    'controllers' => 'projects',
                    'functions' => 'your_form_plus',
                    'display' => 0,
                    'weight' => 34,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'your_form_plus'
                ),
                array(
                    'name_eng' => 'Finance++',
                    'name_fre' => 'Finance++',
                    'company_id' => $company_id,
                    'controllers' => 'project_finances',
                    'functions' => 'plus',
                    'display' => 1,
                    'weight' => 35,
                    'parent_id' => '',
                    'model_id' => $model_id,
                    'widget_id' => 'finance_two_plus'
                ),
				array(
					'name_eng' => 'Tableau de bord',
					'name_fre' => 'Tableau de bord',
					'company_id' => $company_id,
					'controllers' => 'project_amrs_preview',
					'functions' => 'indicator',
					'display' => 1,
					'weight' => 36,
					'parent_id' => '',
					'model_id' => $model_id,
					'widget_id' => 'indicator'
				),
            );
            $this->ProfileProjectManagerDetail->create();
            $this->ProfileProjectManagerDetail->saveAll($menuDefaults);
            $idOfBudget = $this->ProfileProjectManagerDetail->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'model_id' => $model_id, 'name_eng' => 'Budget'),
                'fields' => array('id')
            ));
            if(!empty($idOfBudget) && $idOfBudget['ProfileProjectManagerDetail']['id']){
                $menuDefaultChild = array(
                    array(
                        'name_eng' => 'Synthesis',
                        'name_fre' => 'Synth',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_synthesis',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 21,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'synthesis'
                    ),
                    array(
                        'name_eng' => 'Sales',
                        'name_fre' => 'Vente',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_sales',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 22,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'sale'
                    ),
                    array(
                        'name_eng' => 'Internal Cost',
                        'name_fre' => 'Coût Iernterne',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_internals',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 23,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'internal_cost'
                    ),
                    array(
                        'name_eng' => 'External Cost',
                        'name_fre' => 'Coût Externe',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_externals',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 24,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'external_cost'
                    ),
                    array(
                        'name_eng' => 'Provisional',
                        'name_fre' => 'Provisional',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_provisionals',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 25,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'provisional'
                    ),
                    array(
                        'name_eng' => 'FY Budget',
                        'name_fre' => 'FY Budget',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_fiscals',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 26,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'fy_budget'
                    ),
                    array(
                        'name_eng' => 'Purchase',
                        'name_fre' => 'Achat',
                        'company_id' => $company_id,
                        'controllers' => 'project_budget_purchases',
                        'functions' => 'index',
                        'display' => 0,
                        'weight' => 27,
                        'parent_id' => $idOfBudget['ProfileProjectManagerDetail']['id'],
                        'model_id' => $model_id,
                        'widget_id' => 'purchase'
                    )
                );
                $this->ProfileProjectManagerDetail->create();
                $this->ProfileProjectManagerDetail->saveAll($menuDefaultChild);
            }
            $menus = $this->ProfileProjectManagerDetail->find('threaded', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'model_id' => $model_id
                ),
                'order' => array('weight' => 'ASC')
            ));
        }
        $profile = $this->ProfileProjectManager->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $model_id,
                'company_id' => $this->employee_info['Company']['id']
            )
        ));
        $profileName = !empty($profile) ? $profile['ProfileProjectManager']['profile_name'] : '';
        $this->set(compact('company_id', 'menus', 'modifyScreen', 'model_id', 'profileName', 'profile'));
        $this->set('widgets', $this->widgets());
    }
    private function widgets(){
        $widgets = array(
            'project' => array(
                'details' => array(
                    'name_eng' => 'Details',
                    'name_fre' => 'Fiche',
                    'controllers' => 'projects',
                    'functions' => 'edit'
                ),
                'indicator' => array(
                    'name_eng' => 'Tableau de bord',
                    'name_fre' => 'Tableau de bord',
                    'controllers' => 'project_amrs_preview',
                    'functions' => 'indicator'
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
            )
        );
        return $widgets['project'];
    }
    public function saveMenu($model_id){
        $map = array();
		//check model_id
		$check = $this->ProfileProjectManager->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $model_id,
				'company_id' => $this->employee_info['Company']['id'],
			),
		));
		if( !$check) $this->_functionStop(false, $this->data, __('You have not permission to access this function', true), false, array('action' => 'index'));
        if( !empty($this->data)){
            $this->loadModel('ProfileProjectManagerDetail');
			// check list ID
			$listID = array_keys($this->data['ProfileProjectManagerDetail']);
			$listID = array_filter( $listID, function($v){
				return is_numeric($v);
			});
			
			$check = $this->ProfileProjectManagerDetail->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'model_id' => $model_id,
					'id' => $listID,
					'company_id' => $this->employee_info['Company']['id'],
				),
			));
			if( $check != count( $listID) ) $this->_functionStop(false, $this->data, __('You have not permission to access this function', true), false, array('action' => 'index'));			
            foreach($this->data['ProfileProjectManagerDetail'] as $id => $menu){
                if( strpos($menu['parent_id'], 'new_') !== false ){
                    $menu['parent_id'] = $map[ $menu['parent_id'] ];
                }
                if( is_numeric($id) ){
                    if( $menu['delete'] ){
                        $this->ProfileProjectManagerDetail->delete($id);
                    } else {
                        $this->ProfileProjectManagerDetail->id = $id;
                        $this->ProfileProjectManagerDetail->save($menu);
                    }
                } else {
                    $this->ProfileProjectManagerDetail->create();
                    $this->ProfileProjectManagerDetail->save($menu);
                    $map[$id] = $this->ProfileProjectManagerDetail->id;
                }
            }
            $this->Session->setFlash(__('Saved', true), 'success');
        }
        $this->redirect(array('action' => 'edit', $model_id));
    }
    public function savePermision($model_id){
        if(!empty($model_id) && !empty($_POST)){
            $column = $_POST['column'];
            $checked = $_POST['checked'];
            $this->ProfileProjectManager->id = $model_id;
            $this->ProfileProjectManager->save(array($column => $checked));
        }
        die(1);
    }
}
