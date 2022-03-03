<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class CategoriesController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Categories';
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
           $this->Category->cacheQueries = true;
           $categories = $this->Category->find("all", array(
               'recursive' => -1,
               "conditions" => array('company_id' => $company_id)));
		$this->set(compact('categories', 'company_id', 'companyName'));
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
           if (!empty($this->data['id'])) {
               $this->Category->id = $this->data['id'];
           } else {
                $this->Category->create();
           }
           unset($this->data['id']);
           if ($this->Category->save($this->data)) {
               $result = true;
               $this->Session->setFlash(__('OK.', true), 'success');
           } else {
               $this->Session->setFlash(__('KO.', true), 'error');
           }
           $this->data['id'] = $this->Category->id;
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
		   $this->Session->setFlash(__('Invalid ID', true), 'error');
		   $this->redirect(array('action' => 'index'));
	   }
	   $check = ($this->is_sas || $this->_isBelongToCompany($id, 'Category'));
	   if ($check && $this->Category->delete($id)) {
		   $this->Session->setFlash(__('OK.', true), 'success');
	   } else {
		   $this->Session->setFlash(__('KO.', true), 'error');
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
