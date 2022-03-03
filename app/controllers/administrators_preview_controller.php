<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AdministratorsPreviewController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'AdministratorsPreview';

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array();

     /**
     * Layout used by the Controller
     *
     * @var array
     * @access public
     */
    var $layout = 'administrators';
 

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    function beforeFilter() {
        //$this->Auth->autoRedirect = false;
        parent::beforeFilter();
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        if ($this->Session->read("Auth.Employee.email") == "vanchuong.nguyen@greensystem.vn") {
            $this->redirect("/employees/my_profile");
        } else {
            $this->Employee->recursive = 0;
            $this->set('employees', $this->paginate());
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
            $this->Session->setFlash(__('Invalid employee', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('employee', $this->Employee->read(null, $id));
    }

    /**
     * login
     *
     * @return void
     * @access public
     */
    function login() {
        
    }

    /**
     * logout
     *
     * @return void
     * @access public
     */
    function logout() {
        $this->Session->destroy();
        $this->Auth->logout();
        $this->redirect($this->Auth->logoutRedirect);
    }

}
?>