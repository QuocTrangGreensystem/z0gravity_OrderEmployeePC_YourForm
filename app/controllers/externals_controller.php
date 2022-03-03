<?php
/** 
 * z0 Gravity�
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ExternalsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Externals';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    function beforeFilter()
    {
        parent::beforeFilter();
    }
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
            $this->External->cacheQueries = true;
            $isAdminSas = ($this->employee_info['Employee']['is_sas'] == '1') || empty($this->employee_info['Employee']['company_id']);

            $externals = $this->External->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id)));
			$this->set(compact('externals', 'company_id', 'companyName', 'isAdminSas'));
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
            
            // debug($this->data);
            $allow_fields = array('id','company_id', 'name', 'description');
            $isAdminSas = ($this->employee_info['Employee']['is_sas'] == '1') || empty($this->employee_info['Employee']['company_id']);
            if ($isAdminSas) {
                $allow_fields = array_merge($allow_fields, array('limit_period', 'limit_support', 'limit_formation', 'limit_coaching'));
            }
            $data = array();
            foreach($allow_fields as $field) {
                if(isset($this->data[$field])) {
                    if($field == 'limit_period') {
                        $data[$field] = $this->External->convertTime($this->data[$field]);
                        continue;
                    }
                    $data[$field] = $this->data[$field];
                }
            }
            if (!isset($data['id'])) {
                $this->External->create();
            }
            // unset($this->data['id']);
            if (!$this->is_sas) {
                $data['company_id'] = $this->employee_info['Company']['id'];
            }
            // debug($data);exit;
            if ($this->External->save($data)) {
                $result = true;
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('The External could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->External->id;
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
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'External'));
        if ($check && $this->External->delete($id)) {
            $this->Session->setFlash(__('Deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('External was not deleted', true), 'error');
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
}
?>