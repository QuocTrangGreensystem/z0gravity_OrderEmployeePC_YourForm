<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CurrenciesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Currencies';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    //var $layout = 'administrators';

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {

        $companies1 = $this->Currency->Company->find('list');
        $parent_companies = $this->Currency->Company->find('list', array('fields' => array('id', 'parent_id')));
        $this->set(compact('companies1', 'parent_companies'));

        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $companyId = $this->employee_info["Company"]["id"];
        else
            $companyId = "";
        $this->set('company_id', $companyId);

        $this->Currency->recursive = 0;
        if ($companyId != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('sign_currency', 'description', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $companyId, 'parent_id' => $companyId))
            );
            $this->set('companies', $this->Currency->Company->getTreeList($companyId));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('sign_currency', 'description', 'company_id'),
                'limit' => 1000,
            );
            $this->set('companies', $this->Currency->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('currencies', $this->paginate());
    }

    /**
     * view
     * @param int $id 
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid currency', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('currency', $this->Currency->read(null, $id));
    }
    
    /**
     * edit
     * @param int $id 
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid currency', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'Currency', 'sign_currency')){
                if ($this->Currency->save($this->data)) {
					$this->data = $this->Currency->read(null, $id);
					$this->data = $this->data['Currency'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
            
            // $this->redirect(array('action' => 'index'));
        }
        if (empty($this->data)) {
            $this->data = $this->Currency->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     * @param int $id 
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for currency', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'Currency'));
         $allowDeleteCurrency = $this->_currencyIsUsing($id);
        if($check && ($allowDeleteCurrency == 'true')){
            if ($this->Currency->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
      /**
     *  Kiem tra currency da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _currencyIsUsing($id = null){
        $this->loadModel('Project');
        $this->loadModel('ProjectAmr');
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.currency_id' => $id)
            ));
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('OR'=>array('ProjectAmr.currency_id' => $id,'ProjectAmr.validated_currency_id' => $id,
                    'ProjectAmr.engaged_currency_id' => $id,'ProjectAmr.forecasted_currency_id' => $id,'ProjectAmr.variance_currency_id' => $id))
            ));
        $allowDeleteCurrency= 'true';
        if($checkProject != 0 || $checkProjectAmr != 0 ){
            $allowDeleteCurrency = 'false';
        }
        
        return $allowDeleteCurrency;
    }
     /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->Currency->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "sign_currency" => strtolower($name)
                )));
        return !$check;
    }

}
?>