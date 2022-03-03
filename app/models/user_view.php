<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UserView extends AppModel {

    var $name = 'UserView';
    var $displayField = 'name';
//The Associations below have been created with all possible keys, those that are not needed can be removed

    var $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasOne = array(
        'UserDefaultView' => array(
            'className' => 'UserDefaultView',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserStatusView' => array(
            'className' => 'UserStatusView',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
		'UserStatusViewActivity' => array(
            'className' => 'UserStatusViewActivity',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserStatusViewSale' => array(
            'className' => 'UserStatusViewSale',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UserStatusViewSaleDeal' => array(
            'className' => 'UserStatusViewSaleDeal',
            'foreignKey' => 'user_view_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mandatory field',
            ),
        )
    );

    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }

    function saveUserView($data) {
//$this->deleteAll(array("CompanyEmployeeReference.id"=>$id));
        $this->data["CompanyEmployeeReference"]["id"] = $id;
        $this->data["CompanyEmployeeReference"]["company_id"] = $company_id;
        $this->data["CompanyEmployeeReference"]["employee_id"] = $employee_id;
        $this->data["CompanyEmployeeReference"]["role_id"] = $role_id;
        $this->save($this->data["CompanyEmployeeReference"]);
    }

    function addUserView($data) {
//debug($data);
        $this->data["UserView"]["name"] = $data['UserView']['name'];
        $this->data["UserView"]["description"] = $data['UserView']['description'];
        $this->data["UserView"]["content"] = $data['UserView']['xml'];
        $this->data["UserView"]["created_date"] = $data['UserView']['created_date'];
        $this->data["UserView"]["employee_id"] = $data['UserView']['employee_id'];
        return $this->save($this->data["UserView"]);
    }

    // var $virtualFields = array(
    //     'is_default' => 'SELECT COUNT(id) FROM user_default_views as UserDefaultView WHERE id = UserDefaultView.user_view_id',
    // );
}
?>