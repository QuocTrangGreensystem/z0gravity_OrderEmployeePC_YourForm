<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class KpiSettingsController extends AppController {
    public $uses = array();
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModels('CompanyConfig');
        $this->set('company', $this->employee_info['Company']['id']);

    }
    public function index($view = ''){
        $this->set('data', $this->get());
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModels('Menu');
        $showMenu = $this->Menu->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'display' => 0,
                'model' => 'project', 
            ),
            'fields' => array('widget_id', 'display', 'company_id')
        ));
        $this->set(compact('view','showMenu'));
    }


    public function save(){
        if( !empty($this->data) ){
            $this->store($this->data);
        }
        die(1);
    }
    /*

    */
    public function get(){
        $default = array(
            'customer_point_of_view|1',
            'comment|1',
            'done|1',
            'to_do|1',
            'planning|1',
            'progress|1',
            'budget|0',
            'staffing|0',
            'acceptance|0',
            'risk|0',
            'issue|0',
            'log_comment|1'
        );
        if( isset($this->companyConfigs['kpi_settings']) ){
            $raw = json_decode($this->companyConfigs['kpi_settings']);
            $new = array_merge($this->arrList($default) , $this->arrList($raw));
            return $this->arrArray($new);
        } else {
            //insert
            $this->CompanyConfig->save(array(
                'cf_name' => 'kpi_settings',
                'company' => $this->employee_info['Company']['id'],
                'cf_value' => json_encode($default)
            ));
        }
        return $default;
    }
    public function store($data){
        $this->companyConfigs['kpi_settings'] = json_encode($data);
        //save
        $str = $this->CompanyConfig->getDatasource()->value(json_encode($data), 'string');
        $this->CompanyConfig->updateAll(array(
            'cf_value' => $str
        ), array(
            'cf_name' => 'kpi_settings',
            'company' => $this->employee_info['Company']['id']
        ));
    }
    // convert array to array list
    public function arrList( $arrs = null){
        $_arr = array();
        if(!empty($arrs)){
            foreach ($arrs as $key => $value) {
                $name = explode('|', $value);
                $_arr[$name[0]] = $name[1];
            }
        }
        return $_arr;
    }
    // convert array list to array
    public function arrArray( $arrs = null){
        $_arr = array();
        if(!empty($arrs)){
            foreach ($arrs as $key => $value) {
                $_arr[] = $key . '|' . $value;
            }
        }
        return $_arr;
    }
}
