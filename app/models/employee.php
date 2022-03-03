<?php

/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Employee extends AppModel {

    var $name = 'Employee';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array('Containable');
    public $defAvatarColor = '#6dabd4';
    public $encrypted_fields = array('email', 'first_name', 'last_name', 'work_phone',  'home_phone',  'mobile_phone');
    var $belongsTo = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasOne = array(
        'CompanyEmployeeReference' => array(
            'className' => 'CompanyEmployeeReference',
            'foreignKey' => 'employee_id',
            'dependent' => false
        )
    );
    var $hasMany = array(
        'HistoryFilter' => array(
            'className' => 'HistoryFilter',
            'foreignKey' => 'employee_id',
            'dependent' => true,
        ),
        'CompanyEmployeeReference' => array(
            'className' => 'CompanyEmployeeReference',
            'foreignKey' => 'employee_id',
            'dependent' => true,
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
            'foreignKey' => 'employee_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectLivrable' => array(
            'className' => 'ProjectLivrable',
            'foreignKey' => 'livrable_responsible',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        /*
          'ProjectTeam' => array(
          'className' => 'ProjectTeam',
          'foreignKey' => 'employee_id',
          'dependent' => true,
          'conditions' => '',
          'fields' => '',
          'order' => '',
          'limit' => '',
          'offset' => '',
          'exclusive' => '',
          'finderQuery' => '',
          'counterQuery' => ''
          ),
         */
        'Project' => array(
            'className' => 'Project',
            'foreignKey' => 'project_manager_id',
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
        'ProjectTask' => array(
            'className' => 'ProjectTask',
            'foreignKey' => 'task_assign_to',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectRisk' => array(
            'className' => 'ProjectRisk',
            'foreignKey' => 'risk_assign_to',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectIssue' => array(
            'className' => 'ProjectIssue',
            'foreignKey' => 'issue_assign_to',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectDecision' => array(
            'className' => 'ProjectDecision',
            'foreignKey' => 'project_decision_maker',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ProjectEvolution1' => array(
            'className' => 'ProjectEvolution',
            'foreignKey' => 'evolution_applicant',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'NotifyToken' => array(
            'className' => 'NotifyToken',
            'foreignKey' => 'employee_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function __construct($id = false, $table = null, $ds = null) {
        // parent::__construct();
        $this->validate = array(
            'code_id' => array(
                //'notempty' => array(
//                    'rule' => array('notempty'),
//                    'message' => __('Employee ID is not blank!', true),
//                ),
                'isUnique' => array(
                    'rule' => 'isUnique',
                    'allowEmpty' => true,
                    'message' => __('Employee ID has already been exist.', true),
                    'on' => array('create', 'update')
                )
            ),
            'last_name' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The last name is not blank!', true),
                ),
            ),
            'first_name' => array(
                'notempty' => array(
                    'rule' => array('notempty'),
                    'message' => __('The first name is not blank!', true),
                ),
            ),
            //'email' => array(
//                'notempty' => array(
//                    'rule' => array('email'),
//                    'message' => __('Please provide a valid email address.', true),
//                ),
//                'unique' => array(
//                    'rule' => array('uniqueEmail'),
//                    'message' => __('The email is avaiable, please enter another!', true)
//                ),
//            ),
//            'password' => array(
//                'notempty' => array(
//                    'rule' => array('notempty'),
//                    'message' => __('The password is not blank!', true),
//                    'on' => 'create',
//                ),
//            ),
            'post_code' => array(
                'rule' => array('numeric'),
                'message' => __('The postcode must be number', true),
                'allowEmpty' => true,
            ),
            'work_phone' => array(
                'rule' => array('numeric'),
                'message' => __('The workphone must be number', true),
                'allowEmpty' => true,
            ),
            'home_phone' => array(
                'rule' => array('numeric'),
                'message' => __('The homephone must be number', true),
                'allowEmpty' => true,
            ),
            'mobile_phone' => array(
                'rule' => array('numeric'),
                'message' => __('The mobilephone must be number', true),
                'allowEmpty' => true,
            ),
            'fax' => array(
                'rule' => array('numeric'),
                'message' => __('The fax must be number', true),
                'allowEmpty' => true,
            ),
        );

        parent::__construct($id, $table, $ds);
        $this->virtualFields = array(
            'fullname' => "CONCAT(`{$this->alias}`.`first_name`,' ',`{$this->alias}`.`last_name`)",
            'full_name' => "CONCAT(`{$this->alias}`.`first_name`,' ',`{$this->alias}`.`last_name`)",
            'employee_company' => "CONCAT(`{$this->alias}`.`company_id`,'_',`{$this->alias}`.`id`)"
                //   $this->virtualFields['fullname'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
        );
    }

    /*
      public function __construct($id=false,$table=null,$ds=null){
      parent::__construct($id,$table,$ds);
      $this->virtualFields = array(
      'fullname'=>"CONCAT(`{$this->alias}`.`first_name`,' ',`{$this->alias}`.`last_name`)"
      //   $this->virtualFields['fullname'] = sprintf('CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias);
      );
      } */

    function notEmptyPassword() {
        if ($this->data["{$this->alias}"] ['password'] == md5("")) {
            $this->data["{$this->alias}"] ['password'] = "";
            return false;
        } else {
            return true;
        }
    }

    function uniqueEmail() {
        $company_id = @$this->data["Employee"]["company_id"];
        $role_id = @$this->data["Employee"]["role_id"];
        $is_sas = @$this->data["Employee"]["is_sas"];

        if ($is_sas == 1) {
            return true;
        } else {
            if (!$this->id) {
                if (empty($company_id) || empty($role_id)) {
                    return false;
                }
                $check_email = $this->CompanyEmployeeReference->find("count", array('conditions' => array(
                        "Employee.email" => $this->data["{$this->alias}"]["email"],
                        "CompanyEmployeeReference.company_id" => $company_id
                )));
            } else {
                if (empty($company_id) || empty($role_id)) {
                    $employee = $this->CompanyEmployeeReference->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('employee_id' => $this->id),
                        'fields' => array('company_id', 'role_id')));
                    if (empty($employee)) {
                        return false;
                    }
                    $company_id = $employee['CompanyEmployeeReference']['company_id'];
                    $role_id = $employee['CompanyEmployeeReference']['role_id'];
                }
                $check_email = $this->CompanyEmployeeReference->find("count", array('conditions' => array(
                        "Employee.email" => $this->data["{$this->alias}"]["email"],
                        "CompanyEmployeeReference.company_id" => $company_id,
                        "CompanyEmployeeReference.employee_id <>" => $this->id,)));
            }
            if ($check_email > 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    function hashPasswords($data) {
        if (isset($data['Employee']['password'])) {
            $data['Employee']['password'] = md5($data['Employee']['password']);
            return $data;
        }
        return $data;
    }

	function beforeValidate($options = array()) {
        //check password confirm
        //cake only hashes password when both email & password field present
        //so we need to hash the password manually when email doesn't appear
        //if( !isset($this->data['Employee']['email']) )
        if (isset($this->data['Employee']['password'])) {
            if (!empty($this->data['Employee']['confirm_password']) && $this->data['Employee']['password'] != md5('')) {
                if ($this->data['Employee']['password'] != md5($this->data['Employee']['confirm_password'])) {
                    $this->invalidate('confirm_password', __('Password confirm does not match', true));
                    $this->invalidate('password', __('Password confirm does not match', true));
                    return false;
                }
            }
            if ($this->data['Employee']['password'] == md5('')) {
                unset($this->data['Employee']['password']);
            }
        }
        return true;
    }

    function beforeSave($options = array()) {
        foreach (array('start_date', 'end_date') as $d) {
            if (isset($this->data[$this->alias][$d])) {
                $this->data[$this->alias][$d] = $this->convertTime($this->data[$this->alias][$d]);
            }
        }
        return parent::beforeSave($options);
    }

    public function afterSave($created = false) {
        // #1107 Goi den Login Navigation de crawler employees moi. Phuc vu Mobile App
		$res = parent::afterSave($created);
        App::import('Component','ZNotifyExpoComponent');
        $notify = new ZNotifyExpoComponent();
        $notify->send_notify_login_navigation();

        if (!empty($this->data[$this->alias]['profit_center_id'])) {
			if( !empty($this->data[$this->alias]['company_id'])){
				$companyId = $this->data[$this->alias]['company_id'];
			}else{
				$this->loadModel('ProfitCenter');
				$companyId = $this->ProfitCenter->find('first', array(
					'recursive' => -1,
					'conditions' => array('id' => $this->data[$this->alias]['profit_center_id']),
					'fields' => array('id', 'company_id')
				));
				if( empty( $companyId)) return;
				$companyId = $companyId['ProfitCenter']['company_id'];
			}
				
            $newProfitCenter = $this->data[$this->alias]['profit_center_id'];
            $oldProfitCenter = !empty($this->data[$this->alias]['oldPc']) ? $this->data[$this->alias]['oldPc'] : $newProfitCenter;
            if (!empty($this->data[$this->alias]['id'])) {
                $this->_caculateProfitCenter($companyId, $newProfitCenter, $oldProfitCenter, 0);
            } else {
                $this->_caculateProfitCenter($companyId, $newProfitCenter, $oldProfitCenter, 1);
            }
        }
		return $res;
    }

    public function beforeDelete($cascade = true) {
        $Models = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
        $profit = $Models->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $this->id),
            'fields' => array('profit_center_id')
        ));
        $companyId = ClassRegistry::init('CompanyEmployeeReference')->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $this->id),
            'fields' => array('company_id')
        ));
        $companyId = !empty($companyId) ? $companyId['CompanyEmployeeReference']['company_id'] : 0;
        if (!empty($profit)) {
            $newProfitCenter = $profit['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            $oldProfitCenter = $profit['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
            $this->_caculateProfitCenter($companyId, $newProfitCenter, $oldProfitCenter, -1);
        }
        return true;
    }

    private function _caculateProfitCenter($companyId, $newPc = null, $oldPC = null, $number) {
        $Model = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer');
        $references = $Model->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0),
                'profit_center_id' => array($newPc, $oldPC)
            ),
            'fields' => array('employee_id', 'profit_center_id'),
            'order' => array('employee_id')
        ));
        $employees = array();
        if (!empty($references)) {
            foreach ($references as $employ => $profit) {
                if (!isset($employees[$profit])) {
                    //do nothing
                }
                $employees[$profit][] = $employ;
            }
        }
        $datas = array();
        if (!empty($employees)) {
            foreach ($employees as $profit => $employ) {
                $employees[$profit] = count($employ);
            }
        }
        if ($newPc == $oldPC) {
            $datas[$newPc] = !empty($employees[$newPc]) ? $employees[$newPc] : 1;
        } else {
            $datas[$newPc] = !empty($employees[$newPc]) ? $employees[$newPc] + 1 : 1;
            $datas[$oldPC] = !empty($employees[$oldPC]) ? $employees[$oldPC] - 1 : 0;
        }
        $TmpCaculateProfitCenter = ClassRegistry::init('TmpCaculateProfitCenter');
        if (!empty($datas)) {
            foreach ($datas as $profitId => $data) {
                $tmp = $TmpCaculateProfitCenter->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $profitId, 'company_id' => $companyId),
                    'fields' => array('id')
                ));
                if (!empty($tmp) && $tmp['TmpCaculateProfitCenter']['id']) {
                    $TmpCaculateProfitCenter->id = $tmp['TmpCaculateProfitCenter']['id'];
                    $saved['total_employee'] = $data + $number;
                    $TmpCaculateProfitCenter->save($saved);
                } else {
                    $saved['profit_center_id'] = $profitId;
                    $saved['total_employee'] = $data + $number;
                    $saved['company_id'] = $companyId;
                    $TmpCaculateProfitCenter->create();
                    $TmpCaculateProfitCenter->save($saved);
                }
            }
        }
    }

    function getNameFromString($company = "") {
        $array = explode(",", $company);
        $string = "";
        $names = $this->find("list", array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'companies',
                    'alias' => 'Companies',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.company_id = Companies.id'
                    )
                )
            ),
            'fields' => array('fullname', 'Companies.company_name'),
            'conditions' => array('Employee.id' => $array)));
        foreach ($names as $key => $value) {
            $string .= "$key($value),";
        }
        $string = trim($string, ",");
//        debug($names); exit;
        return $string;
    }

    function getNameFromSqlId($id = "") {

        if ($id == "") {
            return "";
        }
        $string = "";
        $names = $this->find("list", array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'companies',
                    'alias' => 'Companies',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.company_id = Companies.id'
                    )
                ),
                array(
                    'table' => 'sql_manager_employees',
                    'alias' => 'SqlManagerEmployee',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.id = SqlManagerEmployee.employee_id'
                    )
                ),
            ),
            'fields' => array('fullname', 'Companies.company_name'),
            'conditions' => array('SqlManagerEmployee.sql_manager_id' => $id)));
        foreach ($names as $key => $value) {
            $string .= "$key($value),";
        }
        $string = trim($string, ",");
//        debug($names); exit;
        return $string;
    }
	function getEmployeesFromSqlId($id = "") {
        if ($id == "") {
            return "";
        }
        $names = $this->find("all", array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'companies',
                    'alias' => 'Companies',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.company_id = Companies.id'
                    )
                ),
                array(
                    'table' => 'sql_manager_employees',
                    'alias' => 'SqlManagerEmployee',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Employee.id = SqlManagerEmployee.employee_id'
                    )
                ),
            ),
            'fields' => array('id','fullname', 'Companies.company_name'),
            'conditions' => array('SqlManagerEmployee.sql_manager_id' => $id)));
		// debug( $names); exit;
		$result = array();
        foreach ($names as $key => $value) {
            $result[$value['Employee']['id']] = $value['Employee']['fullname'].'('.$value['Companies']['company_name'].')';
        }
        return $result;
    }
	
	private function defaultProfile(){
		$default_val = array(
			'first_name' => true,
			'first_letter_first_name' => false,
			'sperator' => '.',
			'last_name' => true,
			'first_letter_last_name' => false,
			'domain_name' => "@globalsi.fr",
			'password' => '',
			'tjm' => '0',
			'capacity_by_year' => '210',
			'email_receive' => true,
			'activate_copy' => true,
			'activate_copy' => true,
			'is_enable_popup' => true,
			'auto_timesheet' => false,
			'auto_absence' => false,
			'auto_by_himself' => false,
			'control_resource' => false,
			'update_your_form' => true,
			'create_a_project' => true,
			'delete_a_project' => true,
			'change_status_project' => true,
			'avatar_color' => '#6dabd4',
		);
		return $default_val;
	}
    public function getDefaultProfile(){
		return $this->defaultProfile();
	}
    public function default_user_profile($company_id = null){
        $EmployeeDefaultProfile = ClassRegistry::init('EmployeeDefaultProfile');
		$default_val = $this->getDefaultProfile();
		// debug( $company_id); exit;
		$default_user_profile = $EmployeeDefaultProfile->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
			),
			'fields' => array( 'df_value'),
		));
		$default_user_profile = !empty($default_user_profile) ? unserialize($default_user_profile['EmployeeDefaultProfile']['df_value']) : array();		
		$default_user_profile = array_merge( $default_val, $default_user_profile);
		return $default_user_profile;
    }
	public function generateEmployeeAvatar($employee_id, $overwrite = false){
		$employee = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $employee_id
			),
			'fields' => array('id', 'company_id', 'avatar', 'avatar_color', 'first_name', 'last_name')
		));
		if( empty( $employee)) return false;
		$employee = $employee["Employee"];
		$company_id = !empty($employee['company_id']) ? $employee['company_id'] : 0;
		$avatar = !empty($employee['avatar']) ? $employee['avatar'] : '';
		$company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
		$old_path = '';
		if( !empty ($avatar) && !empty($company_id)) $old_path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS . $avatar;
		// $path = IMAGES . DS . $company_id . DS . $employee_id . DS;
		$new_path = IMAGES . 'avatar' . DS ;
		if (!file_exists($new_path )) {
			mkdir($new_path , 0777, true);
		}
		$count = 0;
		if( $old_path && file_exists($old_path)){
			try {
				App::import("vendor", "resize");
				//resize image for thumbnail login image
				$resize = new ResizeImage($old_path);
				$resize->resizeTo(200, 200, 'maxwidth');
				
				if( file_exists($new_path . $employee_id . '_avatar.png') && $overwrite) {
					@unlink($new_path . $employee_id . '_avatar.png');
				}
				if( !file_exists($new_path . $employee_id . '_avatar.png') ) {
					$resize->saveImage($new_path . $employee_id . '_avatar.png');
					$count++;
				}
				$resize->resizeTo(40, 40, 'maxwidth');
				if( file_exists($new_path . $employee_id . '.png') && $overwrite) {
					@unlink($new_path . $employee_id . '.png');
				}
				if( !file_exists($new_path . $employee_id . '.png') ){
					$resize->saveImage($new_path . $employee_id . '.png');
					$count++;
				}
			} catch (Exception $ex) {
				//wrong image, dont save
				@unlink($new_path . $employee_id . '_avatar.png');
				@unlink($new_path . $employee_id . '.png');
				die(json_encode(array(
					'status' => 'error',
					'hint' => __('Error when create avatar image for employee', true). ' ' .$employee_id,
				)));
			}	
		}else{// draw image
			if( !IMG_PNG ) return 0;
			$avatar_color = !empty( $employee['avatar_color']) ? $employee['avatar_color'] : $this->defAvatarColor;
			$first_name = $employee['first_name'];
			$last_name = $employee['last_name'];
			$name_avatar = strtoupper( substr($first_name,0,1).substr($last_name,0,1) );
			if( empty( $name_avatar ) ) $name_avatar = 'AV';
			// header('Content-Type: image/png');
			foreach( array( '_avatar', '') as $type){
				list( $w, $h) = array( 40, 40);
				$font_size = 14;
				if( $type == '_avatar') { list( $w, $h) = array( 200, 200); $font_size = 72; }
				$im = imagecreatetruecolor($w, $h);
				// Create some colors
				list($r, $g, $b) = sscanf($avatar_color, "#%02x%02x%02x");
				$avt_backgr = imagecolorallocate($im, $r, $g, $b);
				imagefilledrectangle($im, 0, 0, $w-1, $h-1, $avt_backgr);
				$white = imagecolorallocate($im, 255, 255, 255);
				$font = APP. 'webroot' . DS . 'fonts'. DS . 'opensans'. DS . 'OpenSans-SemiBold.ttf';
				$bbox = imagettfbbox ($font_size , 0, $font, $name_avatar);
				$t_w = $bbox[2] - $bbox[0];
				$t_h = $bbox[5] - $bbox[3];
				// Add the text
				imagettftext($im, $font_size, 0, ($w/2 - $t_w/2), ($h - $t_h)/2, $white, $font, $name_avatar);
				// Using imagepng() results in clearer text compared with imagejpeg()
				try{
					imagepng($im, $new_path . $employee_id . $type . '.png', 3);
					$count++;
				}catch (Exception $ex){
					
				}
				imagedestroy($im);
			}
		}
		return $count;
	}
	function canAddMoreMax($company_id){
		$this->loadModel('Company');
		$result = true;
		$count_actif = $this->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'actif' => 1,
				'company_id' => $company_id,
			),
		));
		
		$setting_company =  $this->Company->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $company_id,
			),
			'fields' => array('actif_max', 'no_add_more_max'),
		));
		
		if(!empty($setting_company) && !empty($setting_company['Company']['no_add_more_max'])){
			$actif_max = !empty($setting_company['Company']['actif_max']) ? $setting_company['Company']['actif_max'] : 0;
			if($count_actif >= $actif_max) $result = false;
		}
		
		return $result;
	}

}