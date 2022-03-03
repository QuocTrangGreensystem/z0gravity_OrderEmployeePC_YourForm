<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CompanyEmployeeReferencesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'CompanyEmployeeReferences';

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        $this->CompanyEmployeeReference->recursive = 0;
        $this->set('companyEmployeeReferences', $this->paginate());
    }

    /**
     * view
     * @param int $id 
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid company employee reference', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('companyEmployeeReference', $this->CompanyEmployeeReference->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->CompanyEmployeeReference->create();
            if ($this->CompanyEmployeeReference->save($this->data)) {
                $this->Session->setFlash(__('The company employee reference has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The company employee reference could not be saved. Please, try again.', true));
            }
        }
        $companies = $this->CompanyEmployeeReference->Company->find('list');
        $employees = $this->CompanyEmployeeReference->Employee->find('list');
        $roles = $this->CompanyEmployeeReference->Role->find('list');
        $this->set(compact('companies', 'employees', 'roles'));
    }

    /**
     * edit
     * @param int $id 
     * @return void
     * @access public
     */
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid company employee reference', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->CompanyEmployeeReference->save($this->data)) {
                $this->Session->setFlash(__('The company employee reference has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The company employee reference could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->CompanyEmployeeReference->read(null, $id);
        }
        $companies = $this->CompanyEmployeeReference->Company->find('list');
        $employees = $this->CompanyEmployeeReference->Employee->find('list');
        $roles = $this->CompanyEmployeeReference->Role->find('list');
        $this->set(compact('companies', 'employees', 'roles'));
    }

    /**
     * delete
     * @param int $id 
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for company employee reference', true));
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }

        // security
        if (!is_numeric($id))
            $this->cakeError('error404', array(array('url' => $id)));
        $company_id_of_employee = $this->CompanyEmployeeReference->find("first", array('conditions' => array('CompanyEmployeeReference.id' => $id)));
        if ($company_id_of_employee['CompanyEmployeeReference']['company_id'] == "" || $company_id_of_employee['Employee']['is_sas'] == 1) {
            $this->Session->setFlash(__('Invalid id for employee', true));
            $this->redirect(array('controller' => 'employees', 'action' => 'index'));
        }
        if ($this->is_sas != 1) {
            if ($company_id_of_employee['Role']['name'] == "admin") {
                $this->Session->setFlash(__('You are not allowed deleting this employee', true));
                $this->redirect(array('controller' => 'employees', 'action' => 'index'));
            }
            $company_id_of_employee = $company_id_of_employee['CompanyEmployeeReference']['company_id'];
            $parent_id_of_company_id_of_employee = $this->CompanyEmployeeReference->Company->find("first", array("fields" => array("Company.parent_id"), 'conditions' => array('Company.id' => $company_id_of_employee)));
            if ($parent_id_of_company_id_of_employee['Company']['parent_id'] != "") {
                $parent_id_of_company_id_of_employee = $parent_id_of_company_id_of_employee['Company']['parent_id'];
            }
            else
                $parent_id_of_company_id_of_employee = "";
            $company_id_of_admin = $this->employee_info["Company"]["id"];
            if ($company_id_of_admin == $company_id_of_employee)
                $isThisCompany = true;
            else
                $isThisCompany = false;
            if (!$isThisCompany) {
                if ($parent_id_of_company_id_of_employee == "" || $company_id_of_admin != $parent_id_of_company_id_of_employee) {
                    $this->Session->setFlash(__('Invalid id for employee', true));
                    $this->redirect(array('controller' => 'employees', 'action' => 'index'));
                }
            }
        }
        // security   

        $chuanbixoa = $this->CompanyEmployeeReference->find('first', array('conditions' => array('CompanyEmployeeReference.id' => $id)));
        $employ_id_chuanbixoa = $chuanbixoa['Employee']['id'];
        $project_list = $this->CompanyEmployeeReference->Employee->Project->find('list', array('fields' => array("Project.id", "Project.project_name"), 'conditions' => array('Project.project_manager_id' => $employ_id_chuanbixoa)));
        $name_chuanbixoa = $chuanbixoa['Employee']['fullname'];
        if (empty($project_list)) {
            if ($this->CompanyEmployeeReference->delete($id)) {
                $this->Session->setFlash(__('The employee was deleted', true));
                $this->redirect(array('controller' => 'employees', 'action' => 'delete', $employ_id_chuanbixoa));
                $this->redirect(array('controller' => 'employees', 'action' => 'index'));
            }
            $this->Session->setFlash(__('Company employee reference was not deleted', true));
        } else {
            $pl = "<ul style='padding-left:10px; margin-left:10px;'>";
            foreach ($project_list as $k => $v) {
                $pl .= "<li style='list-style:square;'>" . $v . "</li>";
            }
            $pl .= "</ul>";
            $this->Session->setFlash(sprintf(__('Cannot delete %s because this user is now a project manager of: %s', true), '<b>' . $name_chuanbixoa . '</b>', '<br/>' . $pl));
        }
        $this->redirect(array('controller' => 'employees', 'action' => 'index'));
    }

}
?>