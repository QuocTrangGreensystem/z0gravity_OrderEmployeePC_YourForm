<?php
/*
* Run $this->get_employee_info(); in controller beforeFilter
* Run $this->getAllCompanyConfigs(); in controller beforeFilter
*/
class UserUtilityComponent extends Object {

    public $controller;
    protected $_user;
	
	// Chay truoc beforeFilter cua controler
    public function initialize(&$controller) {

        $this->controller = $controller;
        // $this->_methods = $controller->methods;
        // $this->_user = $controller->employee_info;
        // $this->_companyConfigs = $controller->companyConfigs;
    }

    // Chay sau beforeFilter cua controler
	public function startup(Controller $controller) {
		$this->controller = $controller;
        $this->_methods = $controller->methods;
        $this->_user = $controller->employee_info;
		
        $this->_companyConfigs = $controller->companyConfigs;
	}
	
	// Function nay chua day du, can cap nhat theo cac chuc nang se phat trien
	public function user_can($permission = 'read', $model_id = null){
		$permissionArray = array('read_project', 'write_project', 'see_all_project', 'update_your_form', 'change_status_project', 'see_budget', 'create_a_project', 'delete_a_project', 'control_resource', 'can_see_forecast', 'diary_modify', 'diary_status', 'diary_other_fields', 'change_comment_and_file');
		if(!in_array($permission, $permissionArray)){
			return false;
		}
		$result= $this->get_permission($model_id);
		return $result[$permission];
		
		// $result = false;
		// $role_name = @$this->_user['Role']['name'];
		// $employee_id = @$this->_user['Employee']['id'];
		// $sas_only = array(); // dang Cap nhat
		// if( in_array($permission, $sas_only)){
		// 	return false;
		// }
		// switch( $role_name){ 
		// 	case 'admin': 
		// 		switch( $permission){ 
		// 			case 'read_project':
		// 			case 'write_project':
		// 			case 'update_your_form':
		// 			case 'change_status_project':
		// 				return $model_id ? in_array($model_id, $this->list_company_project_ids()) : true;
		// 			default:
		// 				return true;
		// 		}
		// 	case 'conslt': 
		// 		switch($permission) {
		// 			case 'write_task': return true; // Sai, se phai sua lai tham khao lay project cua Conslt:: getProjectsByConslt()
		// 			case 'read_project': return false;
		// 			case 'write_project': return false;
		// 			default: return false;
		// 		}
				
		// 	case 'pm':
		// 	default:
		// 		switch( $permission){ 
		// 			case 'see_all_project':
		// 				return $this->_user['CompanyEmployeeReference']['see_all_projects'] || $this->_companyConfigs['see_all_projects'];
		// 			case 'read_project':
		// 				// $see_all_project = $this->user_can('see_all_project', null);
		// 				return ( $this->user_can('see_all_project', null) || in_array($model_id, $this->project_by_pm('read', $employee_id)) || in_array($model_id, $this->project_by_pm('write')));
		// 			case 'write_project':
		// 				return in_array($model_id, $this->project_by_pm('write'));
		// 			case 'update_your_form':
		// 			case 'change_status_project':
		// 				return $this->_user['Employee'][$permission] && in_array($model_id, $this->project_by_pm('write'));
		// 			case 'see_budget':
		// 				return $this->_user['CompanyEmployeeReference']['see_budget'] && in_array($model_id, $this->project_by_pm('read'));
		// 			case 'update_budget':
		// 				return $this->_user['CompanyEmployeeReference']['see_budget'] && $this->_user['Employee']['update_budget'] && in_array($model_id, $this->project_by_pm('write'));
		// 			case 'create_a_project':
		// 			case 'delete_a_project':
		// 				return $this->_user['Employee'][$permission] && ( $model_id ? in_array($model_id, $this->project_by_pm('write')) : true);
		// 			default:
		// 				return false;
		// 		}
		// }
		// return $result;
	}
	
	public function project_by_pm($permission = 'read', $employee_id = null, $company_id = null){
		$this->controller->loadModel('Project');
		$Project = $this->controller->Project;
		if( empty( $employee_id )) $employee_id = @$this->_user['Employee']['id'];
		if( empty( $company_id )) $company_id = @$this->_user['Company']['id'];
		// return $Project->project_by_pm($permission, $employee_id, $company_id); 
		// trong project.php function project_by_pm($employee_id, $permission = 'read', $company_id = null)
		return $Project->project_by_pm($employee_id, $permission,  $company_id);
		
	}
	public function list_company_project_ids($company_id = null){
		$this->controller->loadModel('Project');
		$Project = $this->controller->Project;
		if( empty( $company_id )) $company_id = @$this->_user['Company']['id'];
		return array_keys($Project->list_company_projects($company_id));
	}
	// Get permissions of user
    public function get_permission($model_id = null) {
		$role_name = @$this->_user['Role']['name'];
		$employee_id = @$this->_user['Employee']['id'];

		$permission['read_project']=false;
		$permission['write_project']=false;
		$permission['see_all_project']=false;
		$permission['update_your_form']=false;
		$permission['change_status_project']=false;
		$permission['see_budget']=false;
		$permission['create_a_project']=false;
		$permission['delete_a_project']=false;
		$permission['control_resource']=false;
		$permission['can_see_forecast']=false;
		//Permission for tasks
		$permission['diary_modify']=false;
		$permission['diary_status']=false;
		$permission['diary_other_fields']=false;
		$permission['change_comment_and_file']=false;

		switch ($role_name) {
			case 'admin':
				$company_project_id = $this->list_company_project_ids();
				$result1= $model_id ? in_array($model_id, $company_project_id) : true;
				$permission['read_project']= $result1;
				$permission['write_project']=$result1;
				$permission['see_all_project']=true;
				$permission['update_your_form']=true;
				$permission['change_status_project']=$result1;
				$permission['see_budget']=$result1;
				$permission['create_a_project']=1;
				$permission['delete_a_project']=$result1;
				$permission['control_resource']=$result1;
				$permission['can_see_forecast']=$result1;
				//Permission for tasks
				$permission['diary_modify']=$result1;
				$permission['diary_status']=$result1;
				$permission['diary_other_fields']=$result1;
				$permission['change_comment_and_file']=$result1;
				return $permission;
			case 'pm':
				$result_pm_see_all=$this->_user['CompanyEmployeeReference']['see_all_projects'] || $this->_companyConfigs['see_all_projects'];
				// Debug($this->controller->getProjectsByPM());
				// exit;

				$projectWrite = $this->project_by_pm('write');
				$pm_write = $projectWrite?in_array($model_id, $projectWrite):false;
				
				$projectRead = $this->project_by_pm('read');
				$pm_read = $projectRead? in_array($model_id, $projectRead):false;
				
				$permission['read_project']=$result_pm_see_all || $pm_write || $pm_read;
				$permission['write_project']=$pm_write;
				$permission['see_all_project']=$result_pm_see_all;
				$permission['update_your_form']=$pm_write && $this->_user['Employee']['update_your_form'];
				$permission['change_status_project']=$pm_write && $this->_user['Employee']['change_status_project'];
				$permission['see_budget']=$permission['read_project'] && $this->_user['CompanyEmployeeReference']['see_budget'];
				$permission['create_a_project']=!!$this->_user['Employee']['create_a_project'];
				$permission['delete_a_project']=$permission['write_project'] && $this->_user['Employee']['delete_a_project'];
				$permission['control_resource']=$permission['write_project'] && $this->_user['CompanyEmployeeReference']['control_resource'];
				$permission['can_see_forecast']=$permission['read_project'] && $this->_user['Employee']['can_see_forecast'];
				//Permission for tasks
				$permission['diary_modify']=$permission['read_project'] && $this->_companyConfigs['diary_modify'];
				$permission['diary_status']=$permission['diary_modify'] && $this->_companyConfigs['diary_status'];
				$permission['diary_other_fields']=$permission['diary_modify'] && $this->_companyConfigs['diary_others_fields'];
				$permission['change_comment_and_file']=$permission['diary_other_fields'];
				return $permission;
			case 'conslt':

				// Debug($this->controller->getProjectsByConslt());
				// exit;
				$conslt_read = in_array($model_id, $this->controller->getProjectsByConslt());
				
				$permission['read_project']=$conslt_read;
				// $permission['write_project']=$pm_write;
				// $permission['see_all_project']=$result_pm_see_all;
				// $permission['update_your_form']=$pm_write && $this->_user['Employee']['update_your_form'];
				// $permission['change_status_project']=$pm_write && $this->_user['Employee']['change_status_project'];
				$permission['see_budget']=$permission['read_project'] && $this->_user['CompanyEmployeeReference']['see_budget'];
				// $permission['create_a_project']=$this->_user['Employee']['create_a_project'];
				// $permission['delete_a_project']=$permission['write_project'] && $this->_user['Employee']['delete_a_project'];
				// $permission['control_resource']=$permission['write_project'] && $this->_user['CompanyEmployeeReference']['control_resource'];
				$permission['can_see_forecast']=$permission['read_project'] && $this->_user['Employee']['can_see_forecast'];
				//Permission for tasks
				$permission['diary_modify']=!!$this->_companyConfigs['diary_modify'];
				$permission['diary_status']=$permission['diary_modify'] && $this->_companyConfigs['diary_status'];
				$permission['diary_other_fields']=$permission['diary_modify'] && $this->_companyConfigs['diary_others_fields'];
				$permission['change_comment_and_file']=$permission['diary_other_fields'];
				return $permission;
			default:
				# code...
				return $permission;
		}
	}

}
