<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CountriesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Countries';

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
        $employee_info = $this->Session->read("Auth.employee_info");
        if ($this->is_sas == 1)
            $company_id = "";
        else {
            $company_id = $employee_info["Company"]["id"];
        }
        // $this->Country->recursive = 2;
        //Not Admin sas	 
        if ($company_id != "") {
          
			$countries = $this->Country->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				 'fields' => array('id', 'name', 'company_id'),
			));
			
			$companiess = $this->Country->Company->getTreeList($company_id);
			
			$this->set(compact('countries', 'companiess'));
        }

        // Admin sas 	
        else {
            $this->paginate = array(
                // phan trang
                'fields' => array('name', 'company_id'),
                'limit' => 1000
            );
            $this->set('countries', $this->paginate());
            $tree = $this->Country->Company->generateTreeList(null, null, null, '--');
            $this->set('companiess', $this->Country->Company->find('list', array('fields' => array('Company.company_name'))));
            $this->set('tree', $tree);
        }
    }

    /**
     * view
     * @param int $id 
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid country', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('country', $this->Country->read(null, $id));
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
            $this->Session->setFlash(__('Invalid country', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'Country', 'name')){
                if ($this->Country->save($this->data)) {
					$_id = $this->Country->id;
					$this->data = $this->Country->read(null, $id);
					$this->data = $this->data['Country'];
					$result = true;
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true),'error');
                }
            } else {
                $this->Session->setFlash(__('Already exist', true),'error');
            }
           
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
            $this->Session->setFlash(__('Invalid id for country', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'Country'));
        $allowDeleteCountry = $this->_countryIsUsing($id);
        if($check && ($allowDeleteCountry == 'true')){
            if ($this->Country->delete($id)) {
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
     *  Kiem tra country da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _countryIsUsing($id = null){
        $this->loadModel('Employee');
        $checkContractType = $this->Employee->find('count', array(
                'recursive' => -1,
                'conditions' => array('Employee.country_id' => $id)
            ));
        $allowDeleteCountry = 'true';
        if($checkContractType != 0){
            $allowDeleteCountry = 'false';
        }
        
        return $allowDeleteCountry;
    } /**
     * Check duplocate
     *
     * @return void
     * @access public
     */
    function check_duplicate($name = null,$company_id = null) {
        $check = $this->Country->find('count', array('conditions' => array(
                "company_id" => $company_id,
                "name" => strtolower($name)
                )));
        return !$check;
    }

}
?>