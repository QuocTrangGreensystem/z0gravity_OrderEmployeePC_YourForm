<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ResponseConstraintsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ResponseConstraints';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->ResponseConstraint->Company->find('list');
            $this->viewPath = 'response_constraints' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->ResponseConstraint->cacheQueries = true;
            $responseConstraints = $this->ResponseConstraint->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
            $responseConstraints = Set::combine($responseConstraints, '{n}.ResponseConstraint.key', '{n}.ResponseConstraint');
			$this->set(compact('responseConstraints', 'company_id', 'companyName'));
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
            $this->ResponseConstraint->create();
            $this->data['key'] = $this->data['id'];
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            $last = $this->ResponseConstraint->find('first', array(
                'recursive' => -1, 'fields' => array('id'),
                'conditions' => array('key' => $this->data['key'], 'company_id' => $this->data['company_id'])));
            if ($last) {
                $this->ResponseConstraint->id = $last['ResponseConstraint']['id'];
            }
            if ($this->ResponseConstraint->save($this->data)) {
                $result = true;
                $this->Session->setFlash(__('The Validation Constraint has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('The Validation Constraint could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->data['key'];
            unset($this->data['key']);
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
            $this->Session->setFlash(__('Invalid id for responseConstraint', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id)) {
            $last = $this->ResponseConstraint->find('first', array(
                'recursive' => -1, 'fields' => array('id'),
                'conditions' => array('key' => $id, 'company_id' => $company_id)));
            if ($last) {
                $this->ResponseConstraint->delete($last['ResponseConstraint']['id']);
            }
            $this->Session->setFlash(__('Validation Constraint has been set on default', true), 'success');
        } else {
            $this->Session->setFlash(__('Validation Constraint could not set on default', true), 'error');
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
        $companyName = $this->ResponseConstraint->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

}