<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class BudgetFundersController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'BudgetFunders';
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
    function beforeFilter()
    {
        parent::beforeFilter();
        if( !$this->employee_info['Employee']['is_sas'] && !$this->employee_info['CompanyEmployeeReference']['role_id'] == 2 && !$this->employee_info['CompanyEmployeeReference']['role_id'] == 3)
        {
            $this->redirect('/');
        }
    }
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
            $this->BudgetFunder->cacheQueries = true;
            $BudgetFunders = $this->BudgetFunder->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('BudgetFunders', 'company_id', 'companyName'));
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
            $this->BudgetFunder->create();
            if (!empty($this->data['id'])) {
                $this->BudgetFunder->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->BudgetFunder->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The Budget Funder could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->BudgetFunder->id;
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
                'conditions' => array('ProjectBudgetExternal.funder_id' => $id)
            ));
			
		$this->loadModel('ProjectBudgetInternalDetail');
        $checkTypes = $this->ProjectBudgetInternalDetail->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectBudgetInternalDetail.funder_id' => $id)
            ));
			
        if($checkTypes != 0){
            $this->Session->setFlash(__('Type already in used. You can not delete.', true), 'error');
            $this->redirect(array('action' => 'index'));
        } else {
			$check = ($this->is_sas || $this->_isBelongToCompany($id, 'BudgetFunder'));
            if ($check && $this->BudgetFunder->delete($id)) {
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