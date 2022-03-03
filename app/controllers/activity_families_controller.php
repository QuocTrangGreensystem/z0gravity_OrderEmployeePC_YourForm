<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityFamiliesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityFamilies';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->ActivityFamily->Company->find('list');
            $this->viewPath = 'activity_families' . DS . 'list';
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $this->ActivityFamily->cacheQueries = true;
            $activityFamilies =Set::combine($this->ActivityFamily->find("all", array(
                'recursive' => -1,
                "conditions" => array('company_id' => $company_id))) , '{n}.ActivityFamily.id', '{n}.ActivityFamily');
        }
        $this->set(compact('companies', 'activityFamilies', 'company_id', 'companyName'));
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
            $this->ActivityFamily->create();
            if (!empty($this->data['id'])) {
                $this->ActivityFamily->id = $this->data['id'];
            }
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            if ($this->ActivityFamily->save($this->data)) {
                $result = true;
                $this->Session->setFlash(__('The Activity Family has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('The Activity Family could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->ActivityFamily->id;
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
            $this->Session->setFlash(__('Invalid id for Activity Family', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        $allowDeleteActivityFamily = $this->_activityFamilyIsUsing($id);
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ActivityFamily'));
        if ($check && ($allowDeleteActivityFamily == 'true')){
            if ($this->ActivityFamily->delete($id)) {
                $this->Session->setFlash(__('Activity Family has been deleted', true), 'success');
            }
        } else {
            $this->Session->setFlash(__('Activity Family is being in used. You can not delete.', true), 'error');
        }
        $this->redirect($this->referer() );
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
        $companyName = $this->ActivityFamily->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
      /**
     *  Kiem tra activity family da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _activityFamilyIsUsing($id = null){
        $this->loadModel('Activity');
        $checkActivity = $this->Activity->find('count', array(
                'recursive' => -1,
                'conditions' => array('OR'=>array('Activity.family_id' => $id,'Activity.subfamily_id' => $id))
            ));
        $allowDeleteActivityFamily= 'true';
        if($checkActivity != 0){
            $allowDeleteActivityFamily = 'false';
        }
        
        return $allowDeleteActivityFamily;
    }

}
?>