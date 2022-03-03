<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProfitCenter extends AppModel {

    var $name = 'ProfitCenter';
    var $displayField = 'name';
    var $tree = array();
    var $treePro = array();
    var $actsAs = array('Tree');
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    var $hasMany = array(
        'ProjectTeam' => array(
            'className' => 'ProjectTeam',
            'foreignKey' => 'project_function_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectEmployeeProfitFunctionRefer' => array(
            'className' => 'ProjectEmployeeProfitFunctionRefer',
            'foreignKey' => 'profit_center_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
		),
        'ProfitCenterManagerBackup' => array(
            'className' => 'ProfitCenterManagerBackup',
            'foreignKey' => 'profit_center_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
		),
    );
    var $belongsTo = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id'),
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'manager_id'),
        'Profile' => array(
            'className' => 'Profile',
            'foreignKey' => 'profile_id'),
    );

    function __construct() {
        parent::__construct();
        $this->validate = array(
            'name' => array(
                'Length of the name' => array(
                    'rule' => array('between', 1, 255),
                    'message' => __('Cannot be empty', true),
                )
            ),
            'company_id' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('Cannot be empty', true),
                )
            ),
            'analytical' => array(
                'maxLength' => array(
                    'rule' => array('maxLength', 20),
                    'message' => 'Analytical must be no larger than 20 characters long.'
                )
            ),
        );
    }

    public function reBuildProfitCenter($company_id = null, $listPC = array(), $endDate = null){
        /**
         * Get All PC of company
         */
        $profitCenters = $this->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'ProfitCenter.id' => $listPC),
            'fields' => array('id', 'manager_id')
        ));
        /**
         * Danh sach pc cua employee
         */
        $tmp_end_date = date('Y-m-d', $endDate);
        $Employee = ClassRegistry::init('Employee');
        $Employee->virtualFields['tmp_end_date'] = 'if(Employee.end_date = "0000-00-00" OR Employee.end_date IS NULL, "' . $tmp_end_date . '", Employee.end_date)';
        $employeeRequests = $Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id
            ),
            'fields' => array('id', 'start_date', 'tmp_end_date', 'capacity_by_year', 'profit_center_id')
        ));
        $pcOfEms = !empty($employeeRequests) ? array_unique(Set::classicExtract($employeeRequests, '{n}.Employee.profit_center_id')) : array();
        $listChilds = $listAlls = $pcNotEmployees = array();
        if(!empty($profitCenters)){
            foreach($profitCenters as $id => $managerId){
                $childs = $this->children($id);
                $childs = !empty($childs) ? Set::classicExtract($childs, '{n}.ProfitCenter.id') : array();
                $childs[] = $id;
                if(!empty($childs)){
                    foreach($childs as $key){
                        if(!in_array($key, $listAlls)){
                            $listAlls[$key] = $key;
                        }
                        if(!in_array($key, $pcOfEms) && empty($managerId)){
                            $pcNotEmployees[$key] = $key;
                        }
                    }
                }
                $listChilds[$id] = $childs;
            }
        }
        return array($listChilds, $listAlls, $pcNotEmployees, $employeeRequests);
    }

    public function getParentManager($profit_id){
        $parents = $this->getPath($profit_id, array(
            'id',
            'IF(manager_id IS NOT NULL OR manager_id != "", manager_id, IF(manager_backup_id IS NOT NULL OR manager_backup_id != "", manager_backup_id, "")) as manager'), -1);
        $len = count($parents);
        $manager = '';
        if( $len > 1 ){
            for($i = $len-2; $i >=0; $i--){
                $node = $parents[$i][0];
                if( !empty($node['manager']) ){
                    $id = $node['manager'];
                    $manager = ClassRegistry::init('Employee')->find('first',array(
                        'recursive' => -1,
                        'conditions' => array('Employee.id' => $id),
                        'fields' => array('fullname')
                    ));
                    $manager = $manager['Employee']['fullname'];
                    break;
                }
            }
        }
        // if( !$manager ){
        //     $employee = ClassRegistry::init('Employee')->find('first', array(
        //         'recursive' => -1,
        //         'conditions' =>
        //     ));
        // }
        return $manager;
    }

    public function checkAutoValidOfProfitCenter($profit_id = null){
        $Employee = ClassRegistry::init('Employee');
        /**
         * Tim xem manager cua profit center nay la ai
         */
        $managerOfPc = $this->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'ProfitCenter.id' => $profit_id
            ),
            'fields' => array('manager_id')
        ));
        /**
         * Kiem tra cac manager/backup manager co chon auto validate timesheet/absence khong?
         */
        $result = array(
            'timesheet' => false,
            'absence' => false,
            'manager' => '',
            'email_receive' => ''
        );
        if(!empty($managerOfPc) && !empty($managerOfPc['ProfitCenter']['manager_id'])){
            $employees = $Employee->find('first', array(
                'recursive' => -1,
                'conditions' => array('Employee.id' => $managerOfPc['ProfitCenter']['manager_id']),
                'fields' => array('id', 'auto_timesheet', 'auto_absence', 'fullname', 'email_receive')
            ));
            $result['timesheet'] = !empty($employees) && !empty($employees['Employee']['auto_timesheet']) ? true : false;
            $result['absence'] = !empty($employees) && !empty($employees['Employee']['auto_absence']) ? true : false;
            if( !empty($employees) ){
                $result['manager'] = $employees['Employee']['fullname'];
                $result['email_receive'] = $employees['Employee']['email_receive'];
            }
        }
        //for consultant auto-validate
        // if( empty($result['manager']) ){
        //     $result['manager'] = $this->getParentManager($profit_id);
        // }
        return $result;
    }

    function getTree($array1, $pre) {
        foreach ($array1 as $array) {
            foreach ($array as $item) {
                if (!empty($item['ProfitCenter']['parent_id']))
                    $parent = $item['ProfitCenter']['parent_id'] . "-"; else
                    $parent = "";
                $this->tree[$item['ProfitCenter']['id']]['name'] = $pre . $item['ProfitCenter']['name'] . "|" . $item['ProfitCenter']['id'] . "|" . $parent . $item['ProfitCenter']['id'];
                $this->tree[$item['ProfitCenter']['id']]['company'] = $item['Company']['company_name'] . "|" . $item['Company']['id'];
                $this->tree[$item['ProfitCenter']['id']]['manager'] = $item['Employee']['first_name'] . " " . $item['Employee']['last_name'] . "|" . $item['Employee']['id'];
                $this->tree[$item['ProfitCenter']['id']]['manager_backup'] = $item['ProfitCenter']['manager_bk_name_firstname'] . " " . $item['ProfitCenter']['manager_bk_name_lastname'] . "|" . $item['ProfitCenter']['manager_backup_id'];
                $this->tree[$item['ProfitCenter']['id']]['analytical'] = $item['ProfitCenter']['analytical'];
                $this->tree[$item['ProfitCenter']['id']]['tjm'] = $item['ProfitCenter']['tjm'];
                $this->tree[$item['ProfitCenter']['id']]['profile_id'] = $item['ProfitCenter']['profile_id'];

                if (!empty($item['children']))
                    $this->getTree1($item['children'], $pre . "--", $item['ProfitCenter']['name']);
            }
        }
    }

    function getTree1($array, $pre, $parent_name) {
		
        foreach ($array as $item) {
            $this->tree[$item['ProfitCenter']['id']]['name'] = $pre . $item['ProfitCenter']['name'] . "|" . $item['ProfitCenter']['id'] . "|" . $item['ProfitCenter']['parent_id'] . "-" . $item['ProfitCenter']['id'];
            $this->tree[$item['ProfitCenter']['id']]['company'] = $item['Company']['company_name'] . "|" . $item['Company']['id'];
            $this->tree[$item['ProfitCenter']['id']]['manager'] = $item['Employee']['first_name'] . " " . $item['Employee']['last_name'] . "|" . $item['Employee']['id'];
            $this->tree[$item['ProfitCenter']['id']]['analytical'] = $item['ProfitCenter']['analytical'];
            $this->tree[$item['ProfitCenter']['id']]['tjm'] = $item['ProfitCenter']['tjm'];
            $this->tree[$item['ProfitCenter']['id']]['profile_id'] = $item['ProfitCenter']['profile_id'];
            $this->tree[$item['ProfitCenter']['id']]['manager_backup'] = $item['ProfitCenter']['manager_bk_name_firstname'] . " " . $item['ProfitCenter']['manager_bk_name_lastname'] . "|" . $item['ProfitCenter']['manager_backup_id'];
            $this->tree[$item['ProfitCenter']['id']]['parent'] = $parent_name;
            if (!empty($item['children']))
                $this->getTree1($item['children'], $pre . "--", $item['ProfitCenter']['name']);
        }
    }

    function getTreeList($companyid = null) {
        if (!empty($companyid)) {
            $companyRoot = $this->find('all', array('order' => array('ProfitCenter.name' => 'ASC'),
                'conditions' => array('AND' => array('ProfitCenter.company_id' => $companyid, 'ProfitCenter.parent_id' => NULL)))); // not the root
        } else {
            $companyRoot = $this->find('all', array('order' => array('ProfitCenter.name' => 'ASC'),
                'conditions' => array('ProfitCenter.parent_id' => NULL))); // not the root
        }
        $companyTree = $companyac = array();
        foreach ($companyRoot as $value) {
            $companyTree = $this->find('threaded', array(
                'conditions' => array(
                    'ProfitCenter.lft >=' => $value['ProfitCenter']['lft'],
                    'ProfitCenter.rght <=' => $value['ProfitCenter']['rght']
                ),
				'order' => array('ProfitCenter.name' => 'ASC'),
                    ));
            $companyac[] = $companyTree;
        }
        $this->getTree($companyac, '');
        return $this->tree;
    }

    function getTreePro($array1, $pre, $include_external) {
        foreach ($array1 as $array) {
            foreach ($array as $item) {
                $name1 = '';
                if (!empty($item['ProjectEmployeeProfitFunctionRefer'])) {
                    $name = array();
                    $_date = time();
                    foreach ($item['ProjectEmployeeProfitFunctionRefer'] as $val) {
                        $check = false;
                        if($include_external){
                            // hien thi het
                            $check = true;
                        } else {
                            if($val['external'] == 0 || $val['external'] == ''){
                                $check = true;
                            }
                        }
                        //if( !$include_external && $val['external'] == 1)continue;
                        if($val['actif'] == 1 && $check == true && (strtotime($val['end_date']) >= $_date || $val['end_date'] == '0000-00-00' || $val['end_date'] == '')){
                            if (!empty($item['ProfitCenter']['manager_id'])) {
                                if ($val['employee_id'] != $item['ProfitCenter']['manager_id']) {
                                    $name[] .= $val['employee_id'] . "->" . $val['firstname'] . " " . $val['lastname'] . "=><br/>";
                                }
                            } else {
                                $name[] .=  $val['employee_id'] . "->" . $val['firstname'] . " " . $val['lastname'] . "=><br/>";
                            }
                        }
                    }
                    $name1 = implode('', array_unique($name));
                }
                $this->treePro[$item['ProfitCenter']['id']] = $pre . $item['ProfitCenter']['name'] . "|" . $item['ProfitCenter']['manager_id'] . "->" . $item['ProfitCenter']['manager_name_firstname'] . " " . $item['ProfitCenter']['manager_name_lastname'] . "|" . $name1;
                if (!empty($item['children']))
                    $this->getTreePro1($item['children'], $pre . "--", $include_external);
            }
        }
    }

    function getTreePro1($array, $pre, $include_external) {
        foreach ($array as $item) {
            $name1 = '';
            if (!empty($item['ProjectEmployeeProfitFunctionRefer'])) {
                $ids = array();
                foreach ($item['ProjectEmployeeProfitFunctionRefer'] as $val) {
                    $_date = time();
                    $check = false;
                    if($include_external){
                        // hien thi het
                        $check = true;
                    } else {
                        if($val['external'] == 0 || $val['external'] == ''){
                            $check = true;
                        }
                    }
                    //if( !$include_external && $val['external'] == 1)continue;
                    if($val['actif'] == 1 && $check == true && (strtotime($val['end_date']) >= $_date || $val['end_date'] == '0000-00-00' || $val['end_date'] == '')){
                        //if( !$include_external && $val['external'] == 1 )continue;
                        if (isset($ids[$val['employee_id']])) {
                            continue;
                        }
                        $ids[$val['employee_id']] = '';
                        if ($val['employee_id'] != $item['ProfitCenter']['manager_id']) {
                            $name1 .= $val['employee_id'] . "->" . $val['firstname'] . " " . $val['lastname'] . "=><br/>";
                        }
                    }
                }
            }
            $this->treePro[$item['ProfitCenter']['id']] = $pre . $item['ProfitCenter']['name'] . "|" . $item['ProfitCenter']['manager_id'] . "->" . $item['ProfitCenter']['manager_name_firstname'] . " " . $item['ProfitCenter']['manager_name_lastname'] . "|" . $name1;
            if (!empty($item['children']))
                $this->getTreePro1($item['children'], $pre . "--", $include_external);
        }
    }
    public $tree2;
    function buildTree($tree, $prefix = ''){
        foreach($tree as $branch){
            $resources = array();
            $pc = $branch['ProfitCenter'];
            if( !empty($branch['Employee']) ){
                foreach($branch['Employee'] as $resource){
                    if( $resource['end_date'] == '0000-00-00' || strtotime($resource['end_date']) >= time() || !$resource['end_date'] ){
                        if( $resource['id'] == $pc['manager_id'] ){
                            continue;
                        } else {
                            $resources[] = $resource['fullname'] . ( $resource['id'] == $pc['manager_backup_id'] ? ' (B)' : '') . '=><br/>';
                        }
                    }
                }
            }
            $this->tree2[$pc['id']] = $prefix . $pc['name'] . '|' . $pc['manager_name'] . '|' . implode('', $resources);
            if( !empty($branch['children']) ){
                $this->buildTree($branch['children'], $prefix . '--');
            }
        }
    }

    function treeWithResource($id, $include_external){
        $companyRoot = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProfitCenter.company_id' => $id,
                'ProfitCenter.parent_id' => NULL
            )
        ));
        $this->unbindModelAll();
        $this->bindModel(array(
            'hasMany' => array(
                'Employee' => array(
                    'className' => 'Employee'
                )
            )
        ));
        $contain = array(
            'Employee.id',
            'Employee.fullname',
            'Employee.actif = 1',
            'Employee.end_date'
        );
        if( !$include_external ){
            $contain[] = 'Employee.external = 0';
        }
        $this->Behaviors->attach('Containable');
        $this->tree2 = array();
        $company = array();
        foreach ($companyRoot as $value) {
            $companyTree = $this->find('threaded', array(
                'conditions' => array(
                    'ProfitCenter.lft >=' => $value['ProfitCenter']['lft'],
                    'ProfitCenter.rght <=' => $value['ProfitCenter']['rght']
                ),
                'contain' => $contain,
            ));
            $company[] = $companyTree;
            $this->buildTree($companyTree);
        }
        return $this->tree2;
    }

    function getTreeListPro($id = null, $include_external = 1) {
        //return $this->treeWithResource($id, $include_external);
        if (!empty($id)) {
            $companyRoot = $this->find('all', array(
                'recursive' => -1,
                'conditions' => array('AND' => array('ProfitCenter.company_id' => $id, 'ProfitCenter.parent_id' => NULL)))); // not the root
        } else {
            $companyRoot = $this->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProfitCenter.parent_id' => NULL))); // not the root
        }
        $companyTree = $companyac = array();
        $this->Behaviors->attach('Containable');
        $contain = array(
            'ProjectEmployeeProfitFunctionRefer.employee_id',
            'ProjectEmployeeProfitFunctionRefer.lastname',
            'ProjectEmployeeProfitFunctionRefer.firstname',
            'ProjectEmployeeProfitFunctionRefer.actif',
            'ProjectEmployeeProfitFunctionRefer.end_date',
            'ProjectEmployeeProfitFunctionRefer.external'
        );
        foreach ($companyRoot as $value) {
            $companyTree = $this->find('threaded', array(
                //'recursive' => -1,
                'conditions' => array(
                    'ProfitCenter.lft >=' => $value['ProfitCenter']['lft'],
                    'ProfitCenter.rght <=' => $value['ProfitCenter']['rght']
                ),
                'contain' => $contain,
            ));
            $companyac[] = $companyTree;
        }
        $this->getTreePro($companyac, '', $include_external);
        return $this->treePro;
    }

    var $virtualFields = array(
        'manager_name_lastname' => 'SELECT last_name FROM employees as ProfitCenter WHERE ProfitCenter.manager_id = id',
        'manager_name_firstname' => 'SELECT first_name FROM employees as ProfitCenter WHERE ProfitCenter.manager_id = id',
        'manager_bk_name_lastname' => 'SELECT last_name FROM employees as ProfitCenter WHERE ProfitCenter.manager_backup_id = id',
        'manager_bk_name_firstname' => 'SELECT first_name FROM employees as ProfitCenter WHERE ProfitCenter.manager_backup_id = id',
        'manager_name' => 'SELECT CONCAT(first_name, " ", last_name) FROM employees as ProfitCenter WHERE ProfitCenter.manager_id = id'
    );
	

}
?>
