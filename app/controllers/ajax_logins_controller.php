<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AjaxLoginsController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array();

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * ajax_login
     *
     * @return void
     * @access public
     */
    function ajax_login() {
        $this->autoRender = false;
        $this->Employee->recursive = 2;
        $usr = $this->Employee->find('first', array('conditions' => array(
                'Employee.email' => $this->params['form']['email'],
                'Employee.password' => $this->params['form']['password']
                )));
        if (!empty($usr)) {
            foreach ($usr['CompanyEmployeeReference'] as $ref) {
                echo "<option value='" . $ref['id'] . "'>" . $ref['Company']['company_name'] . " - " . $ref['Role']['name'] . "</option>";
            }
        } else {
            return "FAIL";
        }
    }

}
?>