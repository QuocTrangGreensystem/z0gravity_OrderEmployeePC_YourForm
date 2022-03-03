<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class BudgetTypesController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'BudgetTypes';
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
            $this->BudgetType->cacheQueries = true;
            $budgetTypes = $this->BudgetType->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('budgetTypes', 'company_id', 'companyName'));
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
            $this->BudgetType->create();
            if (!empty($this->data['id'])) {
                $this->BudgetType->id = $this->data['id'];
            }
            $data = array();
            $data = array(
                'capex'       => (isset($this->data['capex']) && $this->data['capex'] == 'capex')
            );
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->BudgetType->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Type could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->BudgetType->id;
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
            $this->Session->setFlash(__('Invalid id for Budget Type', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->loadModel('ProjectBudgetExternal');
        $checkTypes = $this->ProjectBudgetExternal->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetExternal.budget_type_id' => $id)
            ));
        if($checkTypes != 0){
            $this->Session->setFlash(__('Type already in used. You can not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        } else {
            if ($this->_getCompany($company_id) && $this->BudgetType->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
            } else {
                $this->Session->setFlash(__('Type was not deleted', true), 'error');
            }
        }
        $this->redirect(array('action' => 'index'));
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
?>