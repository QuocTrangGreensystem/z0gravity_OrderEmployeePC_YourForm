<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class WorkdaysController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Workdays';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null, $country_id = null) {
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Workday->Company->find('list');
            $this->viewPath = 'workdays' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->Workday->cacheQueries = true;
            $conditions = array('company_id' => $company_id);
            $this->loadModel('Company');
            $mutil_country = $this->Company->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'multi_country'),
                'conditions' => array('Company.id' => $company_id)
            ));
            $mutil_country = !empty($mutil_country) ? $mutil_country['Company']['multi_country'] : 0;
            if($mutil_country) {
                if(empty($country_id)){
                    $country_id = $this->employee_info['Employee']['country_id'];
                }
                $conditions['country_id'] = $country_id;
            } else {
                $conditions['OR'] = array(
                    array('country_id is NULL'),
                    array('country_id' => 0)
                );
            }
            $workdays = $this->Workday->find("all", array(
                'recursive' => -1,
                "conditions" => $conditions,
                'order' => array('id' => 'ASC'),
                'limit' => 1
            ));
            $this->loadModel('Country');
            $list_country = $this->Country->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array('company_id' => $company_id)
            ));
            $typeSelect = $country_id;

			$this->set(compact('workdays', 'company_id', 'companyName', 'list_country', 'typeSelect', 'mutil_country'));
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
            $theCompanies = $this->_getCompany();
            $theCompanies = $theCompanies['Company']['id'];
            $this->Workday->create();
            $haveWorkdayOfConpanies = false;
            if (!empty($this->data['id'])) {
                $this->Workday->id = $this->data['id'];
            } else {
                if(!empty($this->data['country_id'])){
                    $conditions = array('company_id' => $theCompanies, 'country_id' => $this->data['country_id']);
                } else {
                    $conditions = array('company_id' => $theCompanies);
                }
                $checkWorkdayCompanies = $this->Workday->find('first', array(
                    'recurisve' => -1,
                    'conditions' => $conditions,
                    'fields' => array('id')
                ));
                if(!empty($checkWorkdayCompanies) && $checkWorkdayCompanies['Workday']['id']){
                    $haveWorkdayOfConpanies = true;
                }
            }
            $data = array();
            foreach (array('begin') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->Workday->convertTime($this->data[$key]);
                }
            }
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if($haveWorkdayOfConpanies == true){
                $this->Session->setFlash(__('The Company has been Working day.', true), 'error');
            } else {
                if ($this->Workday->save(array_merge($this->data, $data))) {
                    $result = true;
                    $this->Session->setFlash(__('The Workday has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The Workday could not be saved. Please, try again.', true), 'error');
                }
            }
            $this->data['id'] = $this->Workday->id;
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
            $this->Session->setFlash(__('Invalid id for workday', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id) && $this->Workday->delete($id)) {
            $this->Session->setFlash(__('Workday has been deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Workday was not deleted', true), 'error');
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
        $companyName = $this->Workday->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

}