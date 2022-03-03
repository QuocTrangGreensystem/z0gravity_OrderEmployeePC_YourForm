<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UserDefaultViewsController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('UserDefaultView', 'UserView', 'Employee');

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'UserDefaultViews';

    /**
     * update
     *
     * @return void
     * @access public
     */
    function update($employee_id = null, $view_id = null, $is_auto_update = null) {
        $stop = false;
        if (!is_numeric($employee_id) || !is_numeric($view_id)) {
            $stop = true;
        } else {
            $employee = $this->Employee->find('first', array('conditions' => array('Employee.id' => $employee_id)));
            if (!isset($employee["Employee"]["id"]))
                $stop = true;
            else {
                $user_view = $this->UserView->find('first', array('conditions' => array('UserView.id' => $view_id)));
                if (!isset($user_view["UserView"]["id"]))
                    $stop = true;
            }
        }
        if ($stop)
            $this->cakeError('error404', array(array('url' => $employee_id)));
        $this->data["UserDefaultView"]["employee_id"] = $employee_id;
        $this->data["UserDefaultView"]["user_view_id"] = $view_id;
        $default_view = $this->UserDefaultView->find('first', array(
            'conditions' => array(
                "UserDefaultView.employee_id" => $employee_id,
                )));
        $this->data["UserDefaultView"]["id"] = $default_view["UserDefaultView"]["id"];
        if (!empty($this->data)) {
            if ($this->UserDefaultView->save($this->data)) {
                if ($is_auto_update) {
                    if($is_auto_update == 'null'){
                        $this->UserDefaultView->delete($default_view['UserDefaultView']['id']);
                        $this->Session->setFlash(sprintf(__('The %s view has been set as not default', true), '<b>"' . $user_view["UserView"]["name"] . '"</b>'), 'success');
                    }else{
                        $this->Session->setFlash(sprintf(__('The %s view has been saved & set as default', true), '<b>"' . $user_view["UserView"]["name"] . '"</b>'), 'success');
                    }
                    
                }
                else
                    $this->Session->setFlash(sprintf(__('The %s view has been set as default', true), '<b>"' . $user_view["UserView"]["name"] . '"</b>'), 'success');
                $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user default view could not be saved. Please, try again.', true), 'error');
                $this->redirect(array('controller' => 'user_views', 'action' => 'index'));
            }
        }
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        // use when delete an employee
    }

}
?>