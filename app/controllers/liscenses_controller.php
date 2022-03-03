<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class LiscensesController extends AppController {
    
    public $uses = array();
    
    function index(){
        if(file_exists(KEYS . 'projectkey.php')){
            include KEYS . 'projectkey.php';
            $checkKeyPM = new checkKeyPM();
            $check = $checkKeyPM->validatePM();
            $licensesDate = $checkKeyPM->date;
            $dateRange = $checkKeyPM->dateRange();
            $domain = $checkKeyPM->domain;
        }
        if(!empty($this->data)){
            if(!empty($this->data['Project']['key']['name'])){
                if($this->data['Project']['key']['name'] == 'projectkey.php'){
                    $target_path = KEYS;
                    $target_path .= $this->data['Project']['key']['name'];
                    if(move_uploaded_file($this->data['Project']['key']['tmp_name'], $target_path)){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'liscenses', 'action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash(__('The key does not exist or key not lock project. Please try again.', true), 'error');
                    $this->redirect(array('controller' => 'liscenses', 'action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Your license is not updated. Please try again.', true), 'error');
                $this->redirect(array('controller' => 'liscenses', 'action' => 'index'));
            }
        }
        $this->set(compact('licensesDate', 'dateRange', 'domain'));
    }
    function absence(){
        if(file_exists(KEYS . 'absenceskey.php')){
            include KEYS . 'absenceskey.php';
            $checkKeyAB = new checkKeyAB();
            $check = $checkKeyAB->validateAB();
            $licensesDate = $checkKeyAB->date;
            $dateRange = $checkKeyAB->dateRange();
            $domain = $checkKeyAB->domain;
        }
        if(!empty($this->data)){
            if(!empty($this->data['Absences']['key']['name'])){
                if($this->data['Absences']['key']['name'] == 'absenceskey.php'){
                    $target_path = KEYS;
                    $target_path .= $this->data['Absences']['key']['name'];
                    if(move_uploaded_file($this->data['Absences']['key']['tmp_name'], $target_path)){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'liscenses', 'action' => 'absence'));
                    }
                } else {
                    $this->Session->setFlash(__('The key does not exist or key not lock absences. Please try again.', true), 'error');
                    $this->redirect(array('controller' => 'liscenses', 'action' => 'absence'));
                }
            } else {
                $this->Session->setFlash(__('Your license is not updated. Please try again.', true), 'error');
                $this->redirect(array('controller' => 'liscenses', 'action' => 'absence'));
            }
        }
        $this->set(compact('licensesDate', 'dateRange', 'domain'));
    }
    function activity(){
        if(file_exists(KEYS . 'activitykey.php')){
            include KEYS . 'activitykey.php';
            $checkKeyAC = new checkKeyAC();
            $check = $checkKeyAC->validateAC();
            $licensesDate = $checkKeyAC->date;
            $dateRange = $checkKeyAC->dateRange();
            $domain = $checkKeyAC->domain;
        }
        if(!empty($this->data)){
            if(!empty($this->data['Activity']['key']['name'])){
                if($this->data['Activity']['key']['name'] == 'activitykey.php'){
                    $target_path = KEYS;
                    $target_path .= $this->data['Activity']['key']['name'];
                    if(move_uploaded_file($this->data['Activity']['key']['tmp_name'], $target_path)){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'liscenses', 'action' => 'activity'));
                    }
                } else {
                    $this->Session->setFlash(__('The key does not exist or key not lock activity. Please try again.', true), 'error');
                    $this->redirect(array('controller' => 'liscenses', 'action' => 'activity'));
                }
            } else {
                $this->Session->setFlash(__('Your license is not updated. Please try again.', true), 'error');
                $this->redirect(array('controller' => 'liscenses', 'action' => 'activity'));
            }
        }
        $this->set(compact('licensesDate', 'dateRange', 'domain'));
    }
    function budget(){
        if(file_exists(KEYS . 'budgetkey.php')){
            include KEYS . 'budgetkey.php';
            $checkKeyBG = new checkKeyBG();
            $check = $checkKeyBG->validateBG();
            $licensesDate = $checkKeyBG->date;
            $dateRange = $checkKeyBG->dateRange();
            $domain = $checkKeyBG->domain;
        }
        if(!empty($this->data)){
            if(!empty($this->data['Budget']['key']['name'])){
                if($this->data['Budget']['key']['name'] == 'budgetkey.php'){
                    $target_path = KEYS;
                    $target_path .= $this->data['Budget']['key']['name'];
                    if(move_uploaded_file($this->data['Budget']['key']['tmp_name'], $target_path)){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'liscenses', 'action' => 'budget'));
                    }
                } else {
                    $this->Session->setFlash(__('The key does not exist or key not lock budget. Please try again.', true), 'error');
                    $this->redirect(array('controller' => 'liscenses', 'action' => 'budget'));
                }
            } else {
                $this->Session->setFlash(__('Your license is not updated. Please try again.', true), 'error');
                $this->redirect(array('controller' => 'liscenses', 'action' => 'budget'));
            }
        }
        $this->set(compact('licensesDate', 'dateRange', 'domain'));
    }
    function audit(){
        if(file_exists(KEYS . 'auditkey.php')){
            include KEYS . 'auditkey.php';
            $checkKeyBG = new checkKeyAU();
            $check = $checkKeyBG->validateAU();
            $licensesDate = $checkKeyBG->date;
            $dateRange = $checkKeyBG->dateRange();
            $domain = $checkKeyBG->domain;
        }
        if(!empty($this->data)){
            if(!empty($this->data['Audit']['key']['name'])){
                if($this->data['Audit']['key']['name'] == 'auditkey.php'){
                    $target_path = KEYS;
                    $target_path .= $this->data['Audit']['key']['name'];
                    if(move_uploaded_file($this->data['Audit']['key']['tmp_name'], $target_path)){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'liscenses', 'action' => 'audit'));
                    }
                } else {
                    $this->Session->setFlash(__('The key does not exist or key not lock audit. Please try again.', true), 'error');
                    $this->redirect(array('controller' => 'liscenses', 'action' => 'audit'));
                }
            } else {
                $this->Session->setFlash(__('Your license is not updated. Please try again.', true), 'error');
                $this->redirect(array('controller' => 'liscenses', 'action' => 'audit'));
            }
        }
        $this->set(compact('licensesDate', 'dateRange', 'domain'));
    }
}
?>