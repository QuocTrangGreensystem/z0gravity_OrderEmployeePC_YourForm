<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CitiesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Cities';

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
	/* 
	* Huynh" Cho nay chua query data cua child company
	*/
    function index() {
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $this->City->recursive = 0;
        $companies = $this->City->Company->find('list');
        $parent_companies = $this->City->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('id','name','code', 'company_id'),
                'limit' => 1000,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name'),'conditions'=>array('Company.id'=>$company_id)))) ;
            $this->set('company_names', $this->City->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('id','name','code', 'company_id'),
                'limit' => 1000
            );
            //$this->set('company_names',$this->ProjectStatus->Company->find('list',array('fields'=>array('Company.company_name')))) ;
            $this->set('company_names', $this->City->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('cities', $this->paginate());
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid city', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('city', $this->City->read(null, $id));
    }
    
    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid city', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if($this->_checkDuplicate($this->data, 'City', 'name')){
                if ($this->City->save($this->data)) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                } 
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
            $this->redirect(array('action' => 'index'));  
        }
        if (empty($this->data)) {
            $this->data = $this->City->read(null, $id);
        }
    }
    /**
     * update
     *
     * @return void
     * @access public
     */
    function update() {
		$data = array();
		$result = false;
        if(empty($this->data)) {
            $this->Session->setFlash(__('Invalid city', true), 'error');
        }else{
            if( empty( $this->data['id'])){
				unset($this->data['id']);
				$this->City->create();
				$continue = 1;
				$id = $this->City->id;
			}else{
				$id = $this->data['id'];
				$continue = $this->_checkDuplicate($this->data, 'City', 'name');
			}
			if( $continue){
                if ($this->City->save($this->data)) {
                    $this->Session->setFlash(__('Saved', true), 'success');
					$result = true;
					$this->data = $this->City->read(null, $id);
					// debug( $this->data);
					$this->data = $this->data['City'];
                } else {
                    $this->Session->setFlash(__('NOT SAVED', true), 'error');
                } 
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for city', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'City'));
        $allowDeleteCity = $this->_cityIsUsing($id);
        if($check && ($allowDeleteCity == 'true')){
            if ($this->City->delete($id)) {
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
     *  Kiem tra city da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _cityIsUsing($id = null){
        $this->loadModel('Employee');
        $checkCity = $this->Employee->find('count', array(
                'recursive' => -1,
                'conditions' => array('Employee.city_id' => $id)
            ));
        $allowDeleteCity= 'true';
        if($checkCity != 0){
            $allowDeleteCity = 'false';
        }
        
        return $allowDeleteCity;
    }
}
?>