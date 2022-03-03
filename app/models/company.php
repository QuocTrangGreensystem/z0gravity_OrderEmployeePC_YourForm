<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Company extends AppModel {

    var $name = 'Company';
    var $displayField = 'company_name';
    var $tree = array();
    var $actsAs = array('Tree');
    var $validate = array(
        'company_name' => array(
            'The company must be between 1 and 255 characters.' => array(
                'rule' => array('between', 1, 255),
                'message' => 'Cannot be empty'
            ),
        ),
        'company_id' => array(
            'Notempty' => array(
                'rule' => 'notEmpty',
                'message' => 'Cannot be empty'
            )
        ),
		'day_established' => array(
            'Notempty' => array(
                'rule' => 'notEmpty',
                'message' => 'Cannot be empty'
            )
        )
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $hasMany = array(
        'CompanyEmployeeReference' => array(
            'className' => 'CompanyEmployeeReference',
            'foreignKey' => 'company_id',
            'dependent' => true
        ),
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'company_id',
            'dependent' => true
        ),
    );
    var $belongsTo = array(
        'ParentCompany' => array(
            'className' => 'Company',
            'foreignKey' => 'parent_id'
        )
    );
    
    function getTree($array, $pre) {
        foreach ($array as $item) {
            $this->tree[$item['Company']['id']] = $pre . $item['Company']['company_name'];
            if (count($item['children']) > 0)
                $this->getTree($item['children'], $pre . "--");
        }
    }

    function getTreeList($id) {
        $companyRoot = $this->find('first', array('conditions' => array('Company.id' => $id))); // not the root
        $companyTree = $this->find('threaded', array(
            'conditions' => array(
                'Company.lft >=' => $companyRoot['Company']['lft'],
                'Company.rght <=' => $companyRoot['Company']['rght']
            )
                ));
        $this->getTree($companyTree, '');
        return $this->tree;
    }

    function getName($id = null) {
        $name = $this->find("first", array(
            'recursive' => -1,
            'fields' => 'company_name',
            'conditions' => array('id' => $id)));
        return $name;
    }
	function getDayEstablished($id = null) {
        $company = $this->find("first", array(
            'recursive' => -1,
            'fields' => 'day_established',
            'conditions' => array('id' => $id)));
        return $company['Company']['day_established'];
    }
    function getNameFromString($company = "") {
        $array = explode(",", $company);
        $string="";
        $names = $this->find("list", array(
            'recursive' => -1,
            'fields' => 'company_name',
            'conditions' => array('id' => $array)));
        foreach ($names as $key => $value){
            $string .= $value.",";  
        }
        $string = trim($string, ",");
        return $string;
    }
    function getListFromString($company = "") {
        $array = explode(",", $company);
        $names = $this->find("list", array(
            'recursive' => -1,
            'fields' => 'company_name',
            'conditions' => array('id' => $array)));
        return $names;
    }

}
?>