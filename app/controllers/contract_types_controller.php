<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ContractTypesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ContractTypes';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->ContractType->Company->find('list');
            $this->viewPath = 'contract_types' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->ContractType->cacheQueries = true;
            $contractTypes = $this->ContractType->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('contractTypes', 'company_id', 'companyName'));
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $this->ContractType->create();
            if (!empty($this->data['id'])) {
                $this->ContractType->id = $this->data['id'];
            }
            $data = array();
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->ContractType->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
            $this->data['id'] = $this->ContractType->id;
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
            $this->Session->setFlash(__('Invalid id for Contract Type', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ContractType'));
		
        $allowDeleteContractType = $this->_contractTypeIsUsing($id);
         if($check && ($allowDeleteContractType == 'true')){
            if ($this->ContractType->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index', $company_id));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
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
        $companyName = $this->ContractType->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
     /**
     *  Kiem tra contract type da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _contractTypeIsUsing($id = null){
        $this->loadModel('Employee');
        $checkContractType = $this->Employee->find('count', array(
                'recursive' => -1,
                'conditions' => array('Employee.contract_type_id' => $id)
            ));
        $allowDeleteContractType = 'true';
        if($checkContractType != 0){
            $allowDeleteContractType = 'false';
        }
        
        return $allowDeleteContractType;
    }

}
?>