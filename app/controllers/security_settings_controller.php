<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SecuritySettingsController extends AppController {
    
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SecuritySettings';
    public function index(){
        $employee = $this->Session->read('Auth.employee_info');
        $companyId = !empty($employee['Company']['id']) ? $employee['Company']['id'] : 0;
        //kiem tra id company da co trong database chua
        $authoSetting = $this->SecuritySetting->find('first',array(
            'conditions' => array(
                'company_id' => $companyId
            )
        ));
        if(empty($authoSetting)){//chua co company thi insert company vao
            $setSecurity['SecuritySetting'] = array(
                'cookie' => 0,
                'multi_click' => 0,
                'company_id' => $companyId
            );
            $this->SecuritySetting->save($setSecurity);
        }else{
            $setSecurity = $authoSetting;
        }
        if(!empty($this->data)){
            $result = false;
            $_SecuritySetting = $this->SecuritySetting->find('first',array(
                'conditions' => array(
                    'company_id' => $companyId
                )
            ));
            $this->SecuritySetting->id = $_SecuritySetting['SecuritySetting']['id'];
            if($this->SecuritySetting->save($this->data['setSecurity'])){
                $result = true;
            }
            if($result == true){
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect('/security_settings');
            } else {
                $this->Session->setFlash(__('The Security Settings could not be saved. Please, try again.', true), 'error');
            }
        }
        $this->set(compact('setSecurity'));
    }
}