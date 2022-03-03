<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class BudgetSettingsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'BudgetSettings';
    //var $layout = 'administrators'; 

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
    function index($company_id = null) {
        $this->loadModel('Company');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
            $this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->BudgetSetting->cacheQueries = true;
            $budgetSettings = $this->BudgetSetting->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('budgetSettings', 'company_id', 'companyName'));
        }
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
            $this->BudgetSetting->create();
            if (!empty($this->data['id'])) {
                $this->BudgetSetting->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->BudgetSetting->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Setting could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->BudgetSetting->id;
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
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'BudgetSetting'));
        if ($check  && $this->BudgetSetting->delete($id)) {
            $this->Session->setFlash(__('Deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Budget Setting was not deleted', true), 'error');
        }

        $this->redirect(array('action' => 'index', $company_id));
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
     * Fiscal
     *
     * @return void
     * @access public
     */
    function fiscal($company_id = null){
        $this->loadModels('Company', 'Menu');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
            $this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $menuFiscals = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'controllers' => 'project_budget_fiscals', 'functions' => 'index'),
                'fields' => array('display')
            ));
            $display = !empty($menuFiscals) && !empty($menuFiscals['Menu']['display']) ? true: false;
			$this->set(compact('company_id', 'companyName', 'display'));
        }
    }
    
    public function editDisplayMenuOfProject(){	
        if(!empty($this->data)){
            $this->loadModels('Menu');
            $menuFiscals = $this->Menu->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $this->data['company_id'], 'controllers' => 'project_budget_fiscals', 'functions' => 'index'),
                'fields' => array('id')
            ));
            if(!empty($menuFiscals) && !empty($menuFiscals['Menu']['id'])){
                $this->Menu->id = $menuFiscals['Menu']['id'];
                $value = $this->data['value'];
                $this->Menu->save(array('display' => $value));
            }
        }
		echo 1;
		exit;
	}
    public function update_default($id){
       $company_id= $this->employee_info['Company']['id'];
       $currency_default=$this->BudgetSetting->find('first',array(
            'recursive' => -1,
            'conditions' => array(
                'currency_budget' =>1,
                'company_id' => $company_id,
                ),
            'fields' => array('id'),
        ));
        $this->BudgetSetting->id=$id;
        $this->BudgetSetting->save(array(
            'currency_budget' =>1,
            ));
        if(!empty($currency_default)){
            $this->BudgetSetting->id=$currency_default['BudgetSetting']['id'];
            $this->BudgetSetting->save(array(
              'currency_budget' =>null,
               ));
        }
        $this->redirect('index');
    }
}