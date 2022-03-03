<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class RecycleBinsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'RecycleBins';
	public $components = array('MultiFileUpload');

    /**
     * Controller using model
     * @var array
     * @access public
     */
    var $uses = array();
    public function beforeFilter(){
      parent::beforeFilter();
      App::import('Vendor', 'str_utility');
	  $this->Auth->allow('encryptData', 'cleanup');
    }
	public function run_after_update(){
		$functions = array(
			// 'syncEngagedInternalToBudgetSync',
			
			'home' => array(
				'name' => 'GO HOME',
				'url' => '/'
			),
			'admin' => array(
				'name' => 'GO Administration',
				'url' => '/administrators'
			),
			'hr',
			'updateProjectBudgetSyn',
			'updateProjectBudgetSyn',
			'removeZogMSGFromMenu',
			'generateAvatar',
			'delete_old_city',
			'update_data_activity_id',
			'recoveryProjectStatus',
			'add_new_menu' => array(
				'name' => 'Add new menu',
				'url' => '/menus/add_new_menu'
			),
			'updateOrderSetting' => array(
				'name' => 'Update Order Translation',
				'url' => '/translations/updateOrderSetting'
			),
			'reWriteTranslate',
			'reInitIndicatorSetting',
			'refreshEnddateOfTimesheetConfirm',
			'deleteRequestConfirmDuplicate',
			'updateActivityID',
			'setDefaultPCForEmployee',
			'update_company_id',
			'update_company_id_for_view',
			'deleteViewNotExist',
			'update_dashboard_setting',
			'remove_duplicate_view',
			'update_company_default_view' => array(
				'name' => '#1167 - 1. Detect company without default view',
				'url' => '/recycle_bins/update_company_default_view'
			),
			'update_employee_default_view' => array(
				'name' => '#1167 - 2. Update employee default view',
				'url' => '/recycle_bins/update_employee_default_view'
			),
			'deleteDuplicatePM',
			'deleteDuplicatePublicKey',
			'hr',
			'encryptdata' => array(
				'name' => 'Database encryption',
				'url' => '/recycle_bins/encryptData'
			),
		);
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if ($is_sas) {
			$functions[] = 'delete_files';
		}
		foreach( $functions as $key => $function){
			if( $function == 'hr') { echo '<hr>'; continue;}
			if( is_string( $function)){
				echo "<a href='/recycle_bins/{$function}' target='_blank'>{$function}</a><br>";
			}elseif(is_array( $function) ){
				echo "<a href='" . $function['url'] ."' target='_blank' class='$key'>" . $function['name'] . "</a><br>";
			}
		}
		echo '<div class="manual-actions">';
		echo "<p> Comment line <strong>ini_set('session.referer_check', \$this->host); </strong> in file <strong>/cake/libs/cake_session.php </strong></p>";
		echo '</div>';
		exit;
	}
	public function dev_tools(){
		$functions = array(			
			'home' => array(
				'name' => 'GO HOME',
				'url' => '/'
			),
			'admin' => array(
				'name' => 'GO Administration',
				'url' => '/administrators'
			),
			'hr',
			'cleanup',
			'php_info',
			'avatar_permission' => array(
				'name' => 'Check permission "Avatar" folder',
				'url' => '/recycle_bins/get_file_info'
			),
			'exec_sql',
			'reset_sasp',
			'update_config_2fa',
			'setdebug',
			'unsetdebug',
		);
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if ($is_sas) {
			$functions[] = 'delete_files';
		}
		foreach( $functions as $key => $function){
			if( $function == 'hr') { echo '<hr>'; continue;}
			if( is_string( $function)){
				echo "<a href='/recycle_bins/{$function}' target='_blank'>{$function}</a><br>";
			}elseif(is_array( $function) ){
				echo "<a href='" . $function['url'] ."' target='_blank' class='$key'>" . $function['name'] . "</a><br>";
			}
		}
		echo '<div class="manual-actions">';
		echo "<p> Comment line <strong>ini_set('session.referer_check', \$this->host); </strong> in file <strong>/cake/libs/cake_session.php </strong></p>";
		echo '</div>';
		exit;
	}
	
	public function setdebug($val=null){
		if( !in_array($val, array(0,1,2)) )$val = 0;
		$this->Session->write('z_debug', $val);
		die( $val);
	}
	public function unsetdebug(){
		$this->Session->delete('z_debug');
		die('OK');
	}
	public function remove_duplicate_view(){
		$this->loadModels('UserView', 'UserDefaultView');
		$views = $this->UserView->find('all', array(
			'recursive' => -1,
			'fields' => array('UserView.id', 'UserView.model', 'UserView.name', 'UserView.employee_id', 'count(id) as num'),
			'group' => array('employee_id', 'name', 'model')
		));
		if( empty( $views)) die('Data is empty');
		// debug( $views);;
		$views = array_values(array_filter( $views, function($item){
			return $item[0]['num'] > 1;
		}));
		// debug( $views);
		$defaultViews = $this->UserDefaultView->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'user_view_id')
		));
		$count = 0;
		foreach($views as $view){
			$count += $view[0]['num'];
			$view = $view['UserView'];
			$this->UserView->deleteAll(array(
				array('UserView.id !=' => $view['id']),
				array('UserView.id NOT' => array_values($defaultViews)),
				'UserView.name' => $view['name'],
				'UserView.employee_id' => $view['employee_id'],
				'UserView.model' => $view['model'],
			), false);
		}
		echo 'Done';
		echo '<p style="display:none;">'. $count . ' item(s) was deleted </p>';
		$views = $this->UserView->find('list', array(
			'recursive' => -1,
			'fields' => array('UserView.id', 'UserView.id'),
		));
		// debug( $views);
		if( !empty( $views)) {
			$views = array_values($views);
			$this->loadModels('UserStatusView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale', 'UserDefaultView', 'CompanyViewDefaults');
			foreach(array( 'UserStatusView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale') as $model){
				$this->$model->deleteAll(array(
					$model.'.user_view_id NOT' => $views,
				), false);
			}
		}
		exit;
		
	}
	public function update_company_id_for_view(){
		$models = array('UserView');
		$this->loadModels('Employee');
		$count = 0;
		foreach( $models as $model){
			$this->loadModel($model);
			$employees = $this->$model->find('list', array(
				'recursive' => -1,
				'fields' => array( $model.'.employee_id', 'Employee.company_id'),
				'conditions' => array(
					'Employee.id is not NULL'
				),
				'joins' => array(
					array(
						'table' => 'employees',
						'alias' => 'Employee',
						'conditions' => array('Employee.id = '.$model.'.employee_id'),
						'type' => 'inner'
					)
				),
			));
			// debug( $employees); exit;
			foreach( $employees as $employee_id => $company_id){
				$this->$model->updateAll(
				array($model.'.company_id' => $company_id),	// fields
				array($model.'.employee_id' => $employee_id )	// conditions
			);
			}
			echo 'Update done for model: ' . $model . '</br>';
			
		}
		die( 'Done');
	}
	public function update_company_id(){
		$this->loadModels('CompanyEmployeeReference', 'Employee');
		$count = 0;
		$employees = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Employee.company_id is null',
				'CompanyEmployeeReference.company_id is not null'
				
			),
			'joins' => array(
				array(
					'table' => 'company_employee_references',
					'alias' => 'CompanyEmployeeReference',
					'conditions' => array('Employee.id = CompanyEmployeeReference.employee_id'),
					'type' => 'inner'
				)
			),
			'fields' => array('Employee.id', 'CompanyEmployeeReference.company_id')
		));
		if( !empty($employees)){
			foreach( $employees as $e_id => $e_com){
				$this->Employee->id = $e_id;
				$result = $this->Employee->saveField('company_id', $e_com);
				// debug($result);
				$count++;
			}
		}
		die($count . ' items is updated.');
	}
	public function update_dashboard_setting(){
		if( !$this->isAdminSAS) die('No permission!');
		$this->loadModels('ProjectDashboard', 'ProjectDashboardActive', 'ProjectDashboardShare', 'HistoryFilter');
		$employee_dashboards = $this->HistoryFilter->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'dashboard_history'
			)
		));
		$count = 0;
		$employees = !empty($employee_dashboards) ? Set::classicExtract($employee_dashboards, '{n}.HistoryFilter.employee_id') : array();
		$companies = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $employees,
				'company_id is not null'
			),
			'fields' => array('id', 'company_id')
		));
		if( !empty( $employee_dashboards ) ){
			foreach( $employee_dashboards as $dashboards){
				$dashboards = $dashboards['HistoryFilter'];
				$params = unserialize($dashboards['params']);
				$active = !empty($params['acti']) ? $params['acti'] : 0;
				$params = !empty($params['history']) ? $params['history'] : array();
				$active = !empty($params['acti']) ? $params['acti'] : 0;
				if( isset( $params['acti'])) unset($params['acti']);
				foreach($params as $k => $v){
					$save = array(
						'employee_id' => $dashboards['employee_id'],
						'company_id' => $companies[$dashboards['employee_id']],
						'dashboard_data'  => serialize($v),
						'share_type' => 'nobody',
						'created' => $dashboards['created'],
						'updated' => $dashboards['updated'],
					);
					$this->ProjectDashboard->create();
					$result = $this->ProjectDashboard->save($save);
					if( $result) $count++;
					if( $k == $active){
						$id =  $this->ProjectDashboard->id;
						$this->ProjectDashboardActive->create();
						$this->ProjectDashboardActive->save(array(
							'employee_id' => $dashboards['employee_id'],
							'dashboard_id' => $id
						));
					}
				}
			}
		}
		die( 'Done. ' . $count . ' records is updated');
	}
	function update_config_2fa(){
		if( !$this->isAdminSAS) die('No permission!');
		$this->loadModels('CompanyConfig');
		$result = $this->CompanyConfig->updateAll(
			array('CompanyConfig.cf_value' => "'2fa_app'"),	// fields
			array(
				'CompanyConfig.cf_name' => 'company_two_factor_auth',
				'CompanyConfig.cf_value' => array('microsoft_auth', 'google_auth')
			)	// conditions
		);
		die( $result ? 'OK' : 'KO');
	}
	function revert_project_name(){
		$this->loadModels( 'Project', 'Activity');
		$projects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array('project_name is NULL'),
			'fields' => array('id', 'activity_id')
		));
		// debug( $projects);
		$activities = $this->Activity->find('list', array(
			'recursive' => -1,
			'conditions' => array('id' => array_values($projects)),
			'fields' => array('project', 'name')
		));
		// debug( $activities);
		foreach( $activities as $p_id => $activity_name){
			$this->Project->id =  $p_id;
			$this->Project->save(array('project_name' => $activity_name));
		}
		$new_projects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array('id' => array_keys($projects)),
			'fields' => array('id', 'project_name')
		));
		echo '<pre>';
		print_r( $new_projects);
		echo '</pre>';
		echo '<br> Done!';
		$this->cleanup(false);
		exit;
	}
	function cleanup($echo = true) {
        foreach (array(
			CACHE . 'views' . DS,
			CACHE . 'models' . DS,
			CACHE . 'persistent' . DS,
			TMP . 'uploads' . DS,
			TMP . 'uploads' . DS . 'Employee' . DS) as $path) {

            $normalFiles = glob($path . '*');
            $hiddenFiles = glob($path . '\.?*');

            $normalFiles = $normalFiles ? $normalFiles : array();
            $hiddenFiles = $hiddenFiles ? $hiddenFiles : array();

            $files = array_merge($normalFiles, $hiddenFiles);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (preg_match('/(\.|\.\.)$/', $file)) {
                        continue;
                    }
                    if (is_file($file) === true) {
                        @unlink($file);
                    }
                }
            }
        }
		if( $echo ) echo "Cleanup! <br>";
        return true;
    }
	function encryptData(){
		if( !defined('Z0G_KEY_STRING')) die( "Z0G_KEY_STRING is not defined");
		set_time_limit(0); 
		ignore_user_abort(true);
		$db = ConnectionManager::getDataSource('default');
		$sql = 'Create table if not exists `model_encrypteds` (`id`  int(10) NOT NULL AUTO_INCREMENT,`table_name`  varchar(255) NOT NULL ,`column_name`  varchar(255) NOT NULL ,`updated`  int(10) NULL ,PRIMARY KEY (`id`));';
        $db->query($sql);
		$this->cleanup(false);
		$tables = array('employees', 'companies', 'projects');
		$models = array_map(function($table) {
            return Inflector::camelize(Inflector::singularize($table)) ;
        }, $tables);
		$models = array_combine($tables, $models);
		$this->loadModels( 'ModelEncrypted');
		$encrypteds = $this->ModelEncrypted->find('list', array(
			'fields' => array('column_name', 'id', 'table_name'),
		));
		foreach( $models as $table => $model){
			$this->loadModels( $model);
			// debug( $this->$model->encrypted_fields);
			$fields = $this->$model->encrypted_fields;
			foreach( $this->$model->encrypted_fields as $field){
				if( isset( $encrypteds[$table][$field])) continue;
				try{
					$sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}_encrypted`  blob NULL DEFAULT NULL after `{$field}`;";
					$res = $db->query($sql);
					if( $res) {
						$sql = "update `{$table}` set `{$field}_encrypted` = AES_ENCRYPT(`{$field}`, '". Z0G_KEY_STRING ."');";
						$res = $db->query($sql);
					}
					if( $res) {
						$sql = "ALTER TABLE `{$table}` DROP COLUMN `{$field}`;";
						$res = $db->query($sql);
					}
					if( $res) {
						$sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$field}_encrypted`  `{$field}` blob";
						$res = $db->query($sql);
					}
					if( $res) {
						$saved = array(
							'table_name' => $table,
							'column_name' => $field,
							'updated' => time()
						);
						$this->ModelEncrypted->create();
						$this->ModelEncrypted->save($saved);
						echo "Encrypted data for table {$table}, column {$field}</br>";
					}
					if( !$res ){
						echo "Failed trying to encode table {$table}, field {$field} </br>";
					}
					
				}catch (Exception $e) {
					echo "Failed trying to encode table {$table}, field {$field} </br>";
					echo '  Caught exception: ',  $e->getMessage(), "\n </br>";
				}
			}
		}
		
		echo 'Done <br>';
		$this->cleanup( false);
		exit;
	}
	function updateActivityID(){
		$sql = 'UPDATE `projects` SET `projects`.`activity_id` = NULL  WHERE `projects`.`activity_id` = 0';
		$db = ConnectionManager::getDataSource('default');
        $db->query($sql);
		echo 'Finish';
		exit;
	}
	function deleteHistoryFilter(){
		if(!empty($_GET['path'])){
			$path = $_GET['path'];
			if(!empty($path)){
				$this->loadModel('HistoryFilter');
				if($this->HistoryFilter->deleteAll(array('HistoryFilter.path' => $path), false)){
					echo 'Deleted';
				}else echo "Can't delete";
				
			}else echo "Path is empty";
		}else echo "Path is empty";
		exit;
	}
	function exec_sql($x=null){
		// debug($this->isAdminSAS); exit;
		if( !$this->isAdminSAS) die(403);
		set_time_limit(0); 
		ignore_user_abort(true);
		Configure::write('debug', 2);
		$sql = @$_POST['scr'];
		$result = '';
		if( $sql && !$x && strpos($sql, 'SELECT') !== 0){ 
			echo ("SELECT only");
		}elseif( $sql){
			$db = ConnectionManager::getDataSource('default');
			$result = $db->query($sql);
			if( !$result){
				die('Error');
			}
		}
		echo '<form method="POST">';
		echo "<input type=\"text\" name=\"scr\" value=\"{$sql}\">";
		echo "<button type='submit'>Submit</button>";
		echo "<button type='reset'>Reset</button>";
		echo '</form>';
		echo '<pre>';
		debug( $result);
		echo '</pre>';
		die('Finish');
	}
	private function _widget_name(){
        $list_wid = array(
			'project_synthesis' => 'Synthesis',
			'project_gantt' => 'Gantt++',
			'project_milestones' => 'Milestones',
            'project_pictures' => 'Vision',
            'project_location' => 'Localisation',
            'indicator_assign_object' => 'Participants & Objectifs',
            'project_budget' => 'Budget',
			'project_progress_line' => 'Progression',
            'project_task' => 'Tasks',
            'project_messages' => 'Messages',
            'project_risk' => 'Risques',
            'project_created_value' => __('Created Value', true),
            'project_status' => 'Status',
            'project_synthesis_budget' => __('Synthesis Budget',true),
        );
        return  $list_wid;
    }
	public function reInitIndicatorSetting(){
		$this->loadModels('ProjectIndicatorSetting', 'CompanyDefaultSetting');
		
		$popup_setting = $this->ProjectIndicatorSetting->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'widget_setting') 
		));
		
		if(!empty($popup_setting)){
			foreach($popup_setting as $key => $value){
				$id = $value['ProjectIndicatorSetting']['id'];
				$widget_setting = $value['ProjectIndicatorSetting']['widget_setting'];
				$setting = $this->_format_indicator_setting($widget_setting);
				if(!empty($setting)){
					$this->ProjectIndicatorSetting->id = $id;
					$this->ProjectIndicatorSetting->saveField('widget_setting', serialize($setting));
				}
			}
		}
		$defaultSetting = $this->CompanyDefaultSetting->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'df_value')
		));
		
		if(!empty($defaultSetting)){
			foreach($defaultSetting as $key => $defSetting){
				$df_id = $defSetting['CompanyDefaultSetting']['id'];
				$df_setting = $defSetting['CompanyDefaultSetting']['df_value'];
				$df_value = $this->_format_indicator_setting($df_setting);
				if(!empty($df_value)){
					$this->CompanyDefaultSetting->id = $df_id;
					$this->CompanyDefaultSetting->saveField('df_value', serialize($df_value));
				}
			}
		}
		die('OK');
	}
	private function _format_indicator_setting($indicatorSetting){
		$grid_setting = $order_setting = array();
		$widget_name = $this->_widget_name();
		if(!empty($indicatorSetting)){
            $indicatorSetting = json_decode($indicatorSetting);
            $i = $row = 0;
			if(!empty($indicatorSetting)){
				foreach ($indicatorSetting as $value) {
					$value = explode( '|',$value);
					if(!empty($widget_name[$value[0]])){
						$grid_setting = explode('-',$value[1]);
						$order_setting[$value[0]]['display'] =  $value[2];
						$order_setting[$value[0]]['widget'] =  $value[0];
						$order_setting[$value[0]]['name'] = $widget_name[$value[0]];
						foreach ($grid_setting as $set) {
							$set = explode( '_', $set);
							$order_setting[$value[0]][$set[0]] = $set[1];
							if($set[0] = 'row'){
								$row = max($set[1], $row);
							}
						}
						if($value[0] == 'project_task' && !empty($value[3])){
							$task_status = explode( '-',$value[3]);
							$status =array();
							foreach ($task_status as $status) {
								if($status){
									$status = explode( '_', $status);
									$order_setting[$value[0]]['task_status'][$status[0]] = $status[1];
									$order_setting[$value[0]]['task_default'][$status[0]] = !empty($status[2]) ? $status[2] : '';
								}
							}
						}elseif(!empty($value[3])){
							$models = explode( '-',$value[3]);
							foreach ($models as $model) {
								if($model){
									$model = explode( '_', $model);
									$order_setting[$value[0]]['model'][$model[0]] = $model[1];
								}
							}
						}
						
					}
					$i++;   
				}
            }
        }
		$list_widgets = !empty($order_setting) ? $order_setting : array();
		
		$re_data = array();
		if(!empty($list_widgets)){
			foreach($list_widgets as $key => $val){
				$re_data[$key] = $val;
				if($key == 'project_task'){
					if(!empty($val['task_status'])){
						$re_data[$key]['task_status'] = array();
						foreach($val['task_status'] as $status_id => $is_display){
							$re_data[$key]['task_status'][] = array(
								'status_id' => $status_id,
								'status_display' => !empty($is_display) ? $is_display : 1,
							);
						}
						
					}
					if(!empty($val['task_default'])){
						$re_data[$key]['task_default'] = array();
						foreach($val['task_default'] as $status_id => $is_display){
							$re_data[$key]['task_default'][] = array(
								'status_id' => $status_id,
								'status_display' => !empty($is_display) ? $is_display : 1,
							);
						}
						
					}
				}
				if($key == 'project_synthesis' || $key == 'project_synthesis_budget' || $key == 'project_budget' || $key == 'project_status'){
					if(!empty($val['model'])){
						$re_data[$key]['options'] = array();
						foreach($val['model'] as $model_id => $model_display){
							$re_data[$key]['options'][] = array(
								'model' => $model_id,
								'model_display' => !empty($model_display) ? $model_display : 1,
							);
						}
						unset($re_data[$key]['model']);
					}
				}
				
			}
		}
		return $re_data;
	}
	public function recoveryProjectStatus(){
		$this->loadModels('Company', 'ProjectStatus', 'Project', 'ProjectTask');
		$company = $this->Company->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'company_name')
        ));
		
		$company_id = array_keys($company);

		$projectStatus = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
			'conditions' => array('ProjectStatus.company_id' => $company_id),
            'fields' => array('id')
        ));
		
		$project_status_id = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('Project.project_status_id NOT' => $projectStatus),
			'fields' => array('project_status_id', 'company_id')
        ));
       
		$task_status_id = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('ProjectTask.task_status_id NOT' => $projectStatus),
			'joins' => array(
				array(
					'table' => 'projects',
					'alias' => 'Project',
					'conditions' => array('Project.id = ProjectTask.project_id')
				)
			),
			'fields' => array('ProjectTask.task_status_id', 'Project.company_id')
        ));
		
		$list_status_recover = array_unique($project_status_id + $task_status_id);
		$i = 1;
		if(!empty($list_status_recover)){
			foreach($list_status_recover as $status_id => $company_id){
				if(!empty($status_id) && !empty($company_id)){
					$statu_recover =  array(
						'id' => $status_id,
						'name' => 'Status '. $i,
						'status' => 'IP',
						'company_id' => $company_id,
						'display' => 1
					);
					$this->ProjectStatus->create();
					$saved = $this->ProjectStatus->save($statu_recover);
					if($saved){
						echo '<br /> Status '. $i++ .' has recovered on the '. $company[$company_id];
					}
				}
			}
		}
		
		exit;
	}
	public function reWriteTranslate(){
		$this->loadModels('Translation', 'TranslationEntry');
		$a = time();
		$data = $this->Translation->find('all', array(
			'fields' => 'Translation.original_text, TranslationEntry.text, TranslationEntry.company_id, TranslationEntry.code, Translation.page',
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'translation_entries',
					'alias' => 'TranslationEntry',
					'conditions' => array(
						'TranslationEntry.translation_id = Translation.id'
					)
				)
			)
		));
		
		$translate = array();
		if(!empty($data)){
			foreach($data as $value){
				$dx = $value['Translation'];
				$dy = $value['TranslationEntry'];
				if(!isset($translate[$dy['company_id']])) $translate[$dy['company_id']] = array();
				if(!isset($translate[$dy['company_id']][$dx['page']])) $translate[$dy['company_id']][$dx['page']] = array();
				$translate[$dy['company_id']][$dx['page']][] = array(
					'code' => $dy['code'],
					'original_text' => $dx['original_text'],
					'text' => $dy['text'],
					
				);
			}
		}
		$sync_en = array();
		$sync_fr = array();
		foreach($translate as $company_id => $value){
			foreach($value as $page_name => $vals){
				echo 'Write text translate to file: ' . $page_name . '_' . $company_id . '.po <br>';
				$file_en = new File(APP . 'locale/eng/LC_MESSAGES/' . $page_name . '_' . $company_id . '.po', true);
				$file_fr = new File(APP . 'locale/fre/LC_MESSAGES/' . $page_name . '_' . $company_id . '.po', true);
				$output_en = '';
				$output_fr = '';
				foreach($vals as $index => $trans){
					if($trans['code'] == 'eng'){
						$output_en .= 'msgid "'.$trans['original_text'].'"' . "\n";
						$output_en .= 'msgstr "'.$trans['text'].'"' . "\n\n";
					}else if($trans['code'] == 'fre'){
						$output_fr .= 'msgid "'.$trans['original_text'].'"' . "\n";
						$output_fr .= 'msgstr "'.$trans['text'].'"' . "\n\n";
					}
				}
				$file_en->write($output_en);
				$file_fr->write($output_fr);
                // $sync_en[] = array(
                    // 'path' => $file_en->Folder->pwd() . DS,
                    // 'file' => $file_en->name
                // );
                // $sync_fr[] = array(
                    // 'path' => $file_fr->Folder->pwd() . DS,
                    // 'file' => $file_fr->name
                // );
                // $file_en->close();
                // $file_fr->close();
			}
		}
		
		// if( $this->MultiFileUpload->otherServer ){
			// $this->MultiFileUpload->uploadTranslate($sync_en, null);
			// $this->MultiFileUpload->uploadTranslate($file_fr, null);
		// }
		// debug(time() - $a);
		// debug($translate);
		echo 'Finished write .PO in: ' . (time() - $a) . 's <br>';
		exit;
	}
	public function delete_old_city(){
		$db = ConnectionManager::getDataSource('default');
        $sql = "Delete FROM cities where company_id not in(Select id from companies)";
        $datas = $db->query($sql);
        if($datas) echo 'Success';
        else echo 'Failed';
		exit;
	}
	public function removeZogMSGFromMenu(){
		$this->loadModels('Menu', 'ProfileProjectManagerDetail');
		$this->ProfileProjectManagerDetail->deleteAll(array( 'controllers' => 'zog_msgs' )); 
		if( $this->Menu->deleteAll(array( 'controllers' => 'zog_msgs' )) ) {
			die('success');
		}else{
			die('failure');
		}
		
	}
	public function update_data_activity_id(){
		$this->loadModels('Project', 'Activity');
		$listProjects = $this->Project->find('list', array(
			'recursive' => -1,
            'conditions' => array(
				'Project.company_id is not NULL',
			),
			'joins' => array(
				array(
                    'table' => 'activities',
                    'alias' => 'Activity',
                    'conditions' => array('Activity.project = Project.id'),
					'type' => 'left'
                )
			),
            'fields' => array('Project.id', 'Activity.id')
		));
		// debug( $allProjects); exit;
		$cond = array();
		foreach( $listProjects as $p_id => $a_id){
			$cond['OR'][] = array(
				'project_id' => $p_id,
				'activity_id !=' => !empty($a_id) ? $a_id : 0
			);
		}
		// debug( $cond );
		$this->loadModels('ProjectFinancePlus', 'ProjectEmployeeManager','ProjectFinancePlusDetail');
		$deleteFinances = $this->ProjectFinancePlus->deleteAll($cond,true);
		$deleteFinances = $this->ProjectFinancePlusDetail->deleteAll($cond,false);

		foreach( $listProjects as $p_id => $a_id){
			$this->ProjectEmployeeManager->updateAll(
				array('ProjectEmployeeManager.activity_id' => $a_id),	// fields
				array('ProjectEmployeeManager.project_id' => $p_id)		// conditions
			);
		}
		$deletePM = $this->deleteDuplicatePM(true);
		if( $deleteFinances) echo 'OK';
		else echo 'Failed to delete wrong FinancePlus data';
		exit;
	}
    public function updateBackupManagerToNewTableProfitCenter(){
        $this->loadModels('ProfitCenter', 'ProfitCenterManagerBackup');
        $profits = $this->ProfitCenter->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'NOT' => array('manager_backup_id' => 0),
                    'manager_backup_id IS NOT NULL'
                )
            ),
            'fields' => array('id', 'company_id', 'manager_backup_id')
        ));
        $check = $this->ProfitCenterManagerBackup->find('count', array('recursive' => -1));
        if(!empty($profits) && $check == 0){
            foreach($profits as $profit){
                $dx = $profit['ProfitCenter'];
                $saved = array(
                    'profit_center_id' => $dx['id'],
                    'company_id' => $dx['company_id'],
                    'employee_id' => $dx['manager_backup_id']
                );
                $this->ProfitCenterManagerBackup->create();
                $this->ProfitCenterManagerBackup->save($saved);
            }
        }
        echo 'OKIE!';
        exit;
    }
	public function reset_sasp(){
        if($this->isAdminSAS){
			$this->loadModels('Employee');
			$last = $this->Employee->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'email' => 'support@azuree-app.com'
				),
				'fields' => array('*')
			));
			$last['Employee']['password'] = '26eaa9e48953aac800c2c60e41f219cd'; // htx@.....
			if( $this->Employee->save( $last)) die('OK');
			die( 'Error');
		}
		die('Not permission');
	}
	public function php_info(){
		phpinfo();
		exit;
	}
    public function updateTypeAndCompanyToFinancePlus(){
        $this->loadModels('ProjectFinancePlusDetail', 'ProjectFinancePlus', 'Project');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'company_id')
        ));
        $fins = $this->ProjectFinancePlus->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'project_id', 'type')
        ));
        if(!empty($fins)){
            foreach($fins as $fin){
                $dx = $fin['ProjectFinancePlus'];
                $company_id = !empty($projects[$dx['project_id']]) ? $projects[$dx['project_id']] : 0;
                $this->ProjectFinancePlus->id = $dx['id'];
                $this->ProjectFinancePlus->save(array('company_id' => $company_id));
                $type = '"' . $dx['type'] . '"';
                $this->ProjectFinancePlusDetail->updateAll(
                    array(
                        'ProjectFinancePlusDetail.type' => $type,
                        'ProjectFinancePlusDetail.company_id' => $company_id
                    ),
                    array(
                        'ProjectFinancePlusDetail.project_finance_plus_id' => $dx['id']
                    )
                );
            }
        }
        echo 'OKIE!';
        exit;
    }
    function copyCurrentPhaseFromProjectToNewTable(){
        $this->loadModels('ProjectPhaseCurrent', 'Project');
        $phases = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('NOT' => array('project_phase_id is Null')),
            'fields' => array('id', 'project_phase_id')
        ));
        if(!empty($phases)){
            $saved = array();
            foreach($phases as $project_id => $project_phase_id){
                $saved[] = array(
                    'project_id' => $project_id,
                    'project_phase_id' => $project_phase_id
                );
            }
            $checkDatas = $this->ProjectPhaseCurrent->find('count', array(
                'recursive' => -1
            ));
            if($checkDatas == 0){
                $this->ProjectPhaseCurrent->saveAll($saved);
            }
        }
        echo 'OKIE!';
        exit;
    }
    function formatDataErrorProvisional(){
        $this->loadModels('ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectBudgetProvisional');
        $internals = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        $visionInternals = $this->ProjectBudgetProvisional->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'Internal',
                'NOT' => array('model_id' => $internals)
            ),
            'fields' => array('id', 'id')
        ));
        $externals = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        $visionExternals = $this->ProjectBudgetProvisional->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'model' => 'External',
                'NOT' => array('model_id' => $externals)
            ),
            'fields' => array('id', 'id')
        ));
        $deleted = array_merge($visionInternals, $visionExternals);
        if(!empty($deleted)){
            $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.id' => $deleted), false);
        }
        echo 'OKIE!';
        exit;
    }
    function updateTextWrongInSale(){
        $this->loadModels('SaleLead');
        $saleLeads = $this->SaleLead->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'code', 'order_number')
        ));
        if(!empty($saleLeads)){
            foreach($saleLeads as $saleLead){
                $dx = $saleLead['SaleLead'];
                $code = explode('-', $dx['code']);
                $order_number = explode('-', $dx['order_number']);
                $saved = array(
                    'code' => $dx['id'] . '-' . (!empty($code[1]) ? $code[1] : ''),
                    'order_number' => $dx['id'] . '-' . (!empty($order_number[1]) ? $order_number[1] : '')
                );
                $this->SaleLead->id = $dx['id'];
                $this->SaleLead->save($saved);
            }
        }
        echo 'OKIE!';
        exit;
    }
    public function updateActivityIdLinkedProjectForAllModel(){
        $this->loadModels('Project', 'Activity', 'ProjectBudgetSyn', 'ProjectBudgetProvisional');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('activity_id IS NOT NULL'),
            'fields' => array('id', 'activity_id')
        ));
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('project IS NOT NULL'),
            'fields' => array('id', 'project')
        ));
        if(count($projects) > count($activities)){
            foreach($projects as $id => $activity_id){
                $this->Activity->id = $activity_id;
                $saved = array(
                    'project' => $id
                );
                $this->Activity->save($saved);
                $this->ProjectBudgetSyn->updateAll(array('ProjectBudgetSyn.project_id' => $id), array('ProjectBudgetSyn.activity_id' => $activity_id));
                $this->ProjectBudgetProvisional->updateAll(array('ProjectBudgetProvisional.project_id' => $id), array('ProjectBudgetProvisional.activity_id' => $activity_id));
            }
        } else {
            foreach($activities as $id => $project_id){
                $this->Project->id = $project_id;
                $saved = array(
                    'activity_id' => $id
                );
                $this->Project->save($saved);
                $this->ProjectBudgetSyn->updateAll(array('ProjectBudgetSyn.activity_id' => $id), array('ProjectBudgetSyn.project_id' => $project_id));
                $this->ProjectBudgetProvisional->updateAll(array('ProjectBudgetProvisional.activity_id' => $id), array('ProjectBudgetProvisional.project_id' => $project_id));
            }
        }
        echo 'OKIE!';
        exit;
    }
    function formatProvisionals(){
        $this->loadModels('ProjectBudgetProvisional', 'Project');
        $pros = $this->ProjectBudgetProvisional->find('all', array(
            'recursive' => -1
        ));
        $groupAcFollowPrs = array();
        if(!empty($pros)){
            foreach($pros as $pro){
                $dx = $pro['ProjectBudgetProvisional'];
                $groupAcFollowPrs[$dx['project_id']][] = $dx['activity_id'];
            }
            if(!empty($groupAcFollowPrs)){
                foreach($groupAcFollowPrs as $project => $groupAcFollowPr){
                    $groupAcFollowPr = array_unique($groupAcFollowPr);
                    if(count($groupAcFollowPr) > 1){
                        $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.project_id' => $project, 'ProjectBudgetProvisional.activity_id' => 0), false);
                        /**
                         * Lay Activity ID linked voi project
                         */
                        $activity_id = $this->Project->find('first', array(
                            'recursive' => -1,
                            'conditions' => array('Project.id' => $project),
                            'fields' => array('activity_id')
                        ));
                        $activity_id = !empty($activity_id) && !empty($activity_id['Project']['activity_id']) ? $activity_id['Project']['activity_id'] : '';
                        if(!empty($activity_id)){
                            foreach($groupAcFollowPr as $acti){
                                if($acti == $activity_id){
                                    // do nothing
                                } else {
                                    $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.project_id' => $project, 'ProjectBudgetProvisional.activity_id' => $acti), false);
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 'OKIE';
        exit;
    }
    function formatHolidays(){
        $this->loadModels('Holiday');
        $holidays = $this->Holiday->find('list', array(
            'recursive' => -1,
            'conditions' => array('Holiday.repeat IS NOT NULL'),
            'fields' => array('id', 'id')
        ));
        if(!empty($holidays)){
            $this->Holiday->updateAll(array('Holiday.repeat' => 1), array('Holiday.id' => $holidays));
        }
        echo 'OKIE!';
        exit;
    }
    public function removeProvision(){
        $this->loadModels('ProjectBudgetInternalDetail', 'ProjectBudgetProvisional', 'ProjectBudgetExternal');
        /**
         * Lay danh sach cua internal
         */
        $internals = $this->ProjectBudgetInternalDetail->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        /**
         * Lay danh sach cua External
         */
        $externals = $this->ProjectBudgetExternal->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        /**
         * Danh sach cuar Provisional
         */
        $provisionals = $this->ProjectBudgetProvisional->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'model_id', 'model'),
            'group' => array('model', 'id')
        ));
        if(!empty($provisionals)){
            foreach($provisionals as $key => $provisional){
                if($key == 'Internal'){
                    $ins = array_unique($provisional);
                    foreach($ins as $id){
                        if(in_array($id, $internals)){
                            //do nothing
                        } else {
                            $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'Internal', 'ProjectBudgetProvisional.model_id' => $id), false);
                        }
                    }
                }
                if($key == 'External'){
                    $exs = array_unique($provisional);
                    foreach($exs as $id){
                        if(in_array($id, $externals)){
                            //do nothing
                        } else {
                            $this->ProjectBudgetProvisional->deleteAll(array('ProjectBudgetProvisional.model' => 'External', 'ProjectBudgetProvisional.model_id' => $id), false);
                        }
                    }
                }
            }
        }
        echo 'OKIE';
        exit;
    }
    public function checkProjectName(){
        $this->loadModels('Project');
        $ps = $this->Project->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'CHAR_LENGTH(project_name) >' => 124
            ),
            'fields' => array('Project.id', 'C.company_name', 'project_name', 'CHAR_LENGTH(project_name) as len'),
            'joins' => array(
                array(
                    'table' => 'companies',
                    'alias' => 'C',
                    'conditions' => array('company_id = C.id')
                )
            )
        ));
        echo '<pre>';
        foreach($ps as $p){
            $d = $p['Project'];
            $d['len'] = $p[0]['len'];
            $d['company_name'] = $p['C']['company_name'];
            echo str_pad($d['id'], 10, ' ') . str_pad($d['company_name'], 40, ' ') . str_pad($d['project_name'], 160, ' ') . '<b>' . $d['len'] . "</b>\n";
        }
        die('</pre>');
    }
    /**
     * updateActivityOfBNPP
     */
    public function updateActivityOfBNPP(){
        $db = ConnectionManager::getDataSource('default');
        $sql = "UPDATE `activities` SET project = NULL WHERE id = 3181 AND company_id = 23";
        $db->query($sql);
        echo 'OKIE';
        exit;
    }
    /**
     * test using sql
     */
    public function usingSQL(){
        $db = ConnectionManager::getDataSource('default');
        debug($db);
        exit;
        $sql = "SELECT * FROM absence_attachments";
        $datas = $db->query($t);
        debug($datas);
        exit;
    }
    /**
     * Update module company
     */
    public function updateModuleCompany(){
        $this->loadModels('Company');
        $company = $this->Company->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'module', 'module_pms', 'module_rms', 'module_audit', 'module_report', 'module_busines')
        ));
        $company = Set::combine($company, '{n}.Company.id', '{n}.Company');
        foreach ($company as $key => $value) {
            if($value['module'] == 0){
                $value['module_pms'] = 1;
                $value['module_rms'] = 0;
                $value['module_audit'] = 0;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }elseif($value['module'] == 1){
                $value['module_pms'] = 0;
                $value['module_rms'] = 1;
                $value['module_audit'] = 0;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }elseif($value['module'] == 2){
                $value['module_pms'] = 1;
                $value['module_rms'] = 0;
                $value['module_audit'] = 0;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }elseif($value['module'] == 3){
                $value['module_pms'] = 1;
                $value['module_rms'] = 1;
                $value['module_audit'] = 0;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }elseif($value['module'] == 4){
                $value['module_pms'] = 1;
                $value['module_rms'] = 0;
                $value['module_audit'] = 1;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }elseif($value['module'] == 5){
                $value['module_pms'] = 0;
                $value['module_rms'] = 1;
                $value['module_audit'] = 1;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }else{
                $value['module_pms'] = 1;
                $value['module_rms'] = 1;
                $value['module_audit'] = 1;
                $value['module_report'] = 0;
                $value['module_busines'] = 0;
            }
            $saved = array(
                'module_pms' => $value['module_pms'],
                'module_rms' => $value['module_rms'],
                'module_audit' => $value['module_audit'],
                'module_report' => $value['module_report'],
                'module_busines' => $value['module_busines']
            );
            $this->Company->id = $key;
            $this->Company->save($saved);
        }
        echo 'OKIE';
        exit;
    }
    /**
     * Update activated projects
     */
    public function updateActivatedProjects(){
        set_time_limit(0);
        /**
         * Lay tat ca activity linked voi projects
         */
        $this->loadModels('Activity', 'Project');
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('project is null')
            ),
            'fields' => array('project', 'activated')
        ));
        if(!empty($activities)){
            foreach($activities as $id => $activated){
                $this->Project->id = $id;
                $this->Project->save(array('activated' => $activated));
            }
        }
        echo 'OKIE!';
        exit;
    }
    /**
     * Rename Employee
     */
    public function renameEmployeeProjectAndActivity($companyName = null){
        set_time_limit(0);
        $this->loadModels('Employee', 'Company', 'Project', 'Activity');
        $company_id = $this->Company->find('first', array(
            'recursive' => -1,
            'conditions' => array('Company.company_name' => trim(strtolower($companyName))),
            'fields' => array('id')
        ));
        $company_id = !empty($company_id) && !empty($company_id['Company']['id']) ? $company_id['Company']['id'] : '';
        /**
         * Rename Employee
         */
        if(!empty($company_id)){
            $employees = $this->Employee->find('all', array(
                'recursive' => -1,
                'conditions' => array('Employee.company_id' => $company_id, 'is_sas' => 0),
                'fields' => array('id', 'first_name', 'last_name', 'email')
            ));
            if(!empty($employees)){
                $count = 1;
                foreach($employees as $employee){
                    $dx = $employee['Employee'];
                    $email = explode('@', $dx['email']);
                    $save['last_name'] = $count;
                    if(!empty($email[0])){
                        if(strpos($email[0], '.') !== false){
                           $save['email'] = preg_replace('/\.(.*)/', ".$count", $email[0]) . '@' . trim(strtolower($companyName)) . '.com';
                        } else {
                            $save['email'] = $email[0] . ".$count" . '@' . trim(strtolower($companyName)) . '.com';
                        }
                    }
                    $this->Employee->id = $dx['id'];
                    $this->Employee->save($save);
                    $count++;
                }
            }
        }
        /**
         * Rename Project linked Activity
         */
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'activity_id IS NOT NULL'),
            'fields' => array('id','activity_id'),
            'order' => array('id')
        ));
        $runs = 1;
        if(!empty($projects)){
            foreach($projects as $id => $activityId){
                $saveLinkProject = array(
                    'project_name' => 'Project name P' . $runs,
                    'long_project_name' => 'Project long name P' . $runs
                );
                /**
                 * Save Project
                 */
                $this->Project->id = $id;
                $this->Project->save($saveLinkProject);
                $saveLinkActivity = array(
                    'name' => 'Activity name A' . $runs,
                    'long_name' => 'Activity long name A' . $runs,
                    'short_name' => 'Activity short name A' . $runs
                );
                /**
                 * Save Activity
                 */
                $this->Activity->id = $activityId;
                $this->Activity->save($saveLinkActivity);
                $runs++;
            }
        }
        /**
         * Rename Project not Activity
         */
        $tmp_runPs = $tmp_runAs = $runs;
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'activity_id IS NULL'),
            'fields' => array('id', 'id'),
            'order' => array('id')
        ));
        if(!empty($projects)){
            foreach($projects as $id){
                $save = array(
                    'project_name' => 'Project name P' . $tmp_runPs,
                    'long_project_name' => 'Project long name P' . $tmp_runPs
                );
                $this->Project->id = $id;
                $this->Project->save($save);
                $tmp_runPs++;
            }
        }
        /**
         * Rename Activity
         */
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'project IS NULL'),
            'fields' => array('id', 'id'),
            'order' => array('id')
        ));
        if(!empty($activities)){
            foreach($activities as $id){
                $save = array(
                    'name' => 'Activity name A' . $tmp_runAs,
                    'long_name' => 'Activity long name A' . $tmp_runAs,
                    'short_name' => 'Activity short name A' . $tmp_runAs
                );
                $this->Activity->id = $id;
                $this->Activity->save($save);
                $tmp_runAs++;
            }
        }
        echo 'OK';
        exit;
    }
    /**
     * Add company ID vao employee table
     */
    public function addCompanyIdAndProfitCenterIdToEmployee(){
        set_time_limit(0);
        $this->loadModels('CompanyEmployeeReference', 'Employee', 'ProjectEmployeeProfitFunctionRefer');
        $listEmploys = $this->CompanyEmployeeReference->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'company_id')
        ));
        $listPCOfEmployees = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
            'recursive' => -1,
            'fields' => array('employee_id', 'profit_center_id')
        ));
        $total = 0;
        if(!empty($listPCOfEmployees)){
            foreach($listPCOfEmployees as $emID => $pcID){
                $total++;
                $companyIdOfEm = !empty($listEmploys[$emID]) ? $listEmploys[$emID] : '';
                $this->Employee->id = $emID;
                $this->Employee->save(array('profit_center_id' => $pcID, 'company_id' => $companyIdOfEm));
            }
        }
        echo 'OKIE!';
        exit;
    }
    public function addProjectManagerSeeAllProjectsOfAllCompany(){
        $this->loadModels('Company', 'CompanyConfig');
        $companies = $this->Company->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        if(!empty($companies)){
            foreach($companies as $company_id){
                $last = $this->CompanyConfig->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company' => $company_id, 'cf_name' => 'see_all_projects'),
                    'fields' => array('id')
                ));
                if(!empty($last) && !empty($last['CompanyConfig']['id'])){
                    $this->CompanyConfig->id = $last['CompanyConfig']['id'];
                    $this->CompanyConfig->save(array('cf_value' => 1));
                } else {
                    $saved = array('company' => $company_id, 'cf_name' => 'see_all_projects', 'cf_value' => 1);
                    $this->CompanyConfig->create();
                    $this->CompanyConfig->save($saved);
                }
            }
        }
        echo 'OKIE!';
        exit;
    }
    public function updateStartEndDateActivity(){
        set_time_limit(0);
        $this->loadModels('Activity', 'ActivityTask', 'ProjectTask');
        $infors = $this->Session->read('Auth.employee_info');
        $company_id = $infors['Company']['id'];
        $projectTasks = $activityTasks = array();
        $activities = $this->Activity->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'start_date', 'end_date', 'project', 'created', 'updated')
        ));
        $activityIds = !empty($activities) ? Set::classicExtract($activities, '{n}.Activity.id') : array();
        $projectIds = !empty($activities) ? array_unique(Set::classicExtract($activities, '{n}.Activity.project')) : array();
        if(!empty($activityIds)){
            $activityTasks = $this->ActivityTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'activity_id' => $activityIds,
                    'NOT' => array('task_start_date' => null, 'task_end_date' => null)
                ),
                'fields' => array(
                    'activity_id',
                    'MIN(task_start_date) AS startDate',
                    'MAX(task_end_date) AS endDate'
                ),
                'group' => array('activity_id')
            ));
            $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.activity_id', '{n}.0') : array();
        }
        if(!empty($projectIds)){
            $projectTasks = $this->ProjectTask->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $projectIds,
                    'NOT' => array('task_start_date' => '0000-00-00', 'task_end_date' => '0000-00-00')
                ),
                'fields' => array(
                    'project_id',
                    'MIN(task_start_date) AS startDate',
                    'MAX(task_end_date) AS endDate'
                ),
                'group' => array('project_id')
            ));
            $projectTasks = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.project_id', '{n}.0') : array();
        }
        $count = 0;
        $total = 0;
        if(!empty($activities)){
            $total = count($activities);
            foreach($activities as $activity){
                $dx = $activity['Activity'];
                $dx['start_date'] = empty($dx['start_date']) ? $dx['created'] : $dx['start_date'];
                $dx['end_date'] = empty($dx['end_date']) ? $dx['updated'] : $dx['end_date'];
                $saved = array();
                if(!empty($dx['project'])){
                    $pTaskOfActivity = !empty($projectTasks[$dx['project']]) ? $projectTasks[$dx['project']] : array();
                    if(!empty($pTaskOfActivity)){
                        $start = strtotime($pTaskOfActivity['startDate']);
                        $end = strtotime($pTaskOfActivity['startDate']);
                        $saved['start_date'] = (!empty($dx['start_date']) && $dx['start_date'] <= $start) ? $dx['start_date'] : $start;
                        $saved['end_date'] = ($dx['end_date'] >= $end) ? $dx['end_date'] : $end;
                    } else {
                        $saved['start_date'] = $dx['start_date'];
                        $saved['end_date'] = $dx['end_date'];
                    }
                } else {
                    $aTaskOfActivity = !empty($activityTasks[$dx['id']]) ? $activityTasks[$dx['id']] : array();
                    if(!empty($aTaskOfActivity)){
                        $saved['start_date'] = (!empty($dx['start_date']) && $dx['start_date'] <= $aTaskOfActivity['startDate']) ? $dx['start_date'] : $aTaskOfActivity['startDate'];
                        $saved['end_date'] = ($dx['end_date'] >= $aTaskOfActivity['endDate']) ? $dx['end_date'] : $aTaskOfActivity['endDate'];
                    } else {
                        $saved['start_date'] = $dx['start_date'];
                        $saved['end_date'] = $dx['end_date'];
                    }
                }
                $this->Activity->id = $dx['id'];
                if($this->Activity->save($saved)){
                    $count++;
                }
            }
        }
        echo 'Saved: ' . $count . ' / ' . $total;
        exit;
    }
    /**
     * checkTaskHaveRequestButNotExistsSystem
     */
    public function checkTaskHaveRequestButNotExistsSystem(){
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        /**
         * Lay tat ca cac task cua he thong
         */
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        $activityTasks[0] = 0;
        /**
         * Lay tat cac cac task duoc request
         */
        $activityRequests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'task_id'),
            'conditions' => array(
                'NOT' => array(
                    'OR' => array(
                        'task_id IS NULL',
                        'task_id' => $activityTasks
                    )
                )
            )
        ));
        $activityRequests = !empty($activityRequests) ? array_unique($activityRequests) : array();
        debug($activityRequests); exit;
    }
    /**
     * Modify year = 1980 of repeat of holiday
     */
    public function modifyYearInRepeatOfHolidays(){
        $this->loadModel('Holiday');
        $holidays = $this->Holiday->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('repeat' => 'IS NULL')
            ),
            'fields' => array('id', 'repeat')
        ));
        if(!empty($holidays)){
            foreach($holidays as $id => $date){
                $date = strtotime(date('d-m', $date) . '-1980');
                $this->Holiday->id = $id;
                $this->Holiday->save(array('repeat' => $date));
            }
        }
        echo 'Finish!';
        exit;
    }
    /**
     * Delete database company
     */
    public function deleteAllDatabaseOfCompany($company = null){
        if(!empty($company)){
            /**
             * Xoa du lieu trong admin
             */
            $this->loadModel('ProjectSetting');
            $this->loadModel('AbsenceAttachment');
            $this->loadModel('AbsenceHistory');
            $this->loadModel('AbsenceRequestConfirm');
            $this->loadModel('Absence');
            $this->loadModel('ActionLog');
            $this->loadModel('ActivityColumn');
            $this->loadModel('ActivityExport');
            $this->loadModel('ActivityFamily');
            $this->loadModel('ActivityRequestConfirmMonth');
            $this->loadModel('ActivityRequestConfirm');
            $this->loadModel('ActivityRequestCopy');
            $this->loadModel('ActivityRequest');
            $this->loadModel('AuditAdmin');
            $this->loadModel('AuditMissionEmployeeRefer');
            $this->loadModel('AuditMission');
            $this->loadModel('AuditMissionFile');
            $this->loadModel('AuditRecomEmployeeRefer');
            $this->loadModel('AuditRecom');
            $this->loadModel('AuditRecomFile');
            $this->loadModel('AuditSetting');
            $this->loadModel('BudgetCustomer');
            $this->loadModel('BudgetProvider');
            $this->loadModel('BudgetSetting');
            $this->loadModel('BudgetType');
            $this->loadModel('ProjectCreatedValue');
            $this->loadModel('City');
            $this->loadModel('Company');
            $this->loadModel('CompanyEmployeeReference');
            $this->loadModel('Employee');
            $this->loadModel('ContractType');
            $this->loadModel('Country');
            $this->loadModel('Currency');
            $this->loadModel('Holiday');
            $this->loadModel('LogSystem');
            $this->loadModel('Menu');
            $this->loadModel('ProfitCenter');
            $this->loadModel('ProjectAmrCategory');
            $this->loadModel('ProjectAmrSubCategory');
            $this->loadModel('ProjectAmrCostControl');
            $this->loadModel('ProjectAmrOrganization');
            $this->loadModel('ProjectAmrPerimeter');
            $this->loadModel('ProjectAmrPlan');
            $this->loadModel('ProjectAmrProblemControl');
            $this->loadModel('ProjectAmrProgram');
            $this->loadModel('ProjectAmrSubProgram');
            $this->loadModel('ProjectAmrRiskControl');
            $this->loadModel('ProjectAmrStatuse');
            $this->loadModel('ProjectComplexity');
            $this->loadModel('ProjectEvolutionImpact');
            $this->loadModel('ProjectEvolutionType');
            $this->loadModel('ProjectFunction');
            $this->loadModel('ProjectIssueSeverity');
            $this->loadModel('ProjectIssueStatuse');
            $this->loadModel('ProjectLivrableCategory');
            $this->loadModel('ProjectPhaseStatuse');
            $this->loadModel('ProjectPriority');
            $this->loadModel('ProjectPhase');
            $this->loadModel('ProjectRiskOccurrence');
            $this->loadModel('ProjectRiskSeverity');
            $this->loadModel('ProjectStatuse');
            $this->loadModel('ProjectType');
            $this->loadModel('ProjectSubType');
            $this->loadModel('ResponseConstraint');
            $this->loadModel('SaleCustomerContact');
            $this->loadModel('SaleCustomerIban');
            $this->loadModel('SaleCustomer');
            $this->loadModel('SaleExpense');
            $this->loadModel('SaleLeadEmployeeRefer');
            $this->loadModel('SaleLead');
            $this->loadModel('SaleLeadFile');
            $this->loadModel('SaleLeadLog');
            $this->loadModel('SaleLeadProductExpense');
            $this->loadModel('SaleLeadProductInvoice');
            $this->loadModel('SaleLeadProduct');
            $this->loadModel('SaleRole');
            $this->loadModel('SaleSetting');
            $this->loadModel('SecuritySetting');
            $this->loadModel('TmpCaculateAbsence');
            $this->loadModel('TmpCaculateProfitCenter');
            $this->loadModel('TmpModuleActivityExport');
            $this->loadModel('TmpStaffingSystem');
            $this->loadModel('TranslationEntry');
            $this->loadModel('TranslationSetting');
            $this->loadModel('UserView');
            $this->loadModel('Workday');
            $this->loadModel('AbsenceComment');
            $this->loadModel('AbsenceRequest');
            $this->loadModel('AbsenceRequestFile');
            $this->loadModel('ActivityComment');
            $this->loadModel('ActivityForecast');
            $this->loadModel('EmployeeAbsence');
            $this->loadModel('HistoryFilter');
            $this->loadModel('ProjectEmployeeProfitFunctionRefer');
            $this->loadModel('UserDefaultView');
            $this->loadModel('UserStatusViewActivity');
            $this->loadModel('UserStatusViewSaleDeal');
            $this->loadModel('UserStatusViewSale');
            $this->loadModel('UserStatusView');
            $this->loadModel('Profile');
            $this->loadModel('ProfileValue');
            $this->loadModel('ApiKey');
            $this->loadModel('CompanyConfig');
            $this->loadModel('BudgetFunder');
            $this->loadModel('ProjectAcceptanceType');
            $company = strtolower(trim($company));
            $companies = $this->Company->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_name' => $company),
                'fields' => array('id')
            ));
            if(!empty($companies) && !empty($companies['Company']['id'])){
                $company_id = $companies['Company']['id'];
                /**
                 * CompanyConfig
                 */
                $this->CompanyConfig->deleteAll(array('CompanyConfig.company' => $company_id), false);
                /**
                 * ApiKey
                 */
                $this->ApiKey->deleteAll(array('ApiKey.company_id' => $company_id), false);
                /**
                 * ProjectSetting
                 */
                $this->ProjectSetting->deleteAll(array('ProjectSetting.company_id' => $company_id), false);
                /**
                 * AbsenceAttachment
                 */
                $this->AbsenceAttachment->deleteAll(array('AbsenceAttachment.company_id' => $company_id), false);
                /**
                 * AbsenceHistory
                 */
                $this->AbsenceHistory->deleteAll(array('AbsenceHistory.company_id' => $company_id), false);
                /**
                 * AbsenceRequestConfirm
                 */
                $this->AbsenceRequestConfirm->deleteAll(array('AbsenceRequestConfirm.company_id' => $company_id), false);
                /**
                 * Absence
                 */
                $this->Absence->deleteAll(array('Absence.company_id' => $company_id), false);
                /**
                 * ActionLog
                 */
                $this->ActionLog->deleteAll(array('ActionLog.company_id' => $company_id), false);
                /**
                 * ActivityColumn
                 */
                $this->ActivityColumn->deleteAll(array('ActivityColumn.company_id' => $company_id), false);
                /**
                 * ActivityExport
                 */
                $this->ActivityExport->deleteAll(array('ActivityExport.company_id' => $company_id), false);
                /**
                 * ActivityFamily
                 */
                $this->ActivityFamily->deleteAll(array('ActivityFamily.company_id' => $company_id), false);
                /**
                 * ActivityRequestConfirmMonth
                 */
                $this->ActivityRequestConfirmMonth->deleteAll(array('ActivityRequestConfirmMonth.company_id' => $company_id), false);
                /**
                 * ActivityRequestConfirm
                 */
                $this->ActivityRequestConfirm->deleteAll(array('ActivityRequestConfirm.company_id' => $company_id), false);
                /**
                 * ActivityRequestCopy
                 */
                $this->ActivityRequestCopy->deleteAll(array('ActivityRequestCopy.company_id' => $company_id), false);
                /**
                 * ActivityRequest
                 */
                $this->ActivityRequest->deleteAll(array('ActivityRequest.company_id' => $company_id), false);
                /**
                 * AuditAdmin
                 */
                $this->AuditAdmin->deleteAll(array('AuditAdmin.company_id' => $company_id), false);
                /**
                 * AuditMissionEmployeeRefer
                 */
                $this->AuditMissionEmployeeRefer->deleteAll(array('AuditMissionEmployeeRefer.company_id' => $company_id), false);
                /**
                 * AuditMission
                 */
                $auditMissions = $this->AuditMission->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('AuditMission.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->AuditMissionFile->deleteAll(array('AuditMissionFile.audit_mission_id' => $auditMissions), false);
                $this->AuditMission->deleteAll(array('AuditMission.company_id' => $company_id), false);
                /**
                 * AuditRecomEmployeeRefer
                 */
                $this->AuditRecomEmployeeRefer->deleteAll(array('AuditRecomEmployeeRefer.company_id' => $company_id), false);
                /**
                 * AuditRecom
                 */
                $auditRecoms = $this->AuditRecom->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('AuditRecom.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->AuditRecomFile->deleteAll(array('AuditRecomFile.audit_recom_id' => $company_id), false);
                $this->AuditRecom->deleteAll(array('AuditRecom.company_id' => $company_id), false);
                /**
                 * AuditSetting
                 */
                $this->AuditSetting->deleteAll(array('AuditSetting.company_id' => $company_id), false);
                /**
                 * BudgetCustomer
                 */
                $this->BudgetCustomer->deleteAll(array('BudgetCustomer.company_id' => $company_id), false);
                /**
                 * BudgetProvider
                 */
                $this->BudgetProvider->deleteAll(array('BudgetProvider.company_id' => $company_id), false);
                /**
                 * BudgetSetting
                 */
                $this->BudgetSetting->deleteAll(array('BudgetSetting.company_id' => $company_id), false);
                /**
                 * BudgetType
                 */
                $this->BudgetType->deleteAll(array('BudgetType.company_id' => $company_id), false);
                /**
                 * BudgetFunder
                 */
                $this->BudgetFunder->deleteAll(array('BudgetFunder.company_id' => $company_id), false);
                /**
                 * City
                 */
                $this->City->deleteAll(array('City.company_id' => $company_id), false);
                /**
                 * CompanyEmployeeReference
                 */
                $CompanyEmployeeReferences = $this->CompanyEmployeeReference->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('CompanyEmployeeReference.company_id' => $company_id),
                    'fields' => array('employee_id', 'employee_id')
                ));
                $this->CompanyEmployeeReference->deleteAll(array('CompanyEmployeeReference.company_id' => $company_id), false);
                $this->Employee->deleteAll(array('Employee.id' => $CompanyEmployeeReferences), false);
                /**
                 * ContractType
                 */
                $this->ContractType->deleteAll(array('ContractType.company_id' => $company_id), false);
                /**
                 * Country
                 */
                $this->Country->deleteAll(array('Country.company_id' => $company_id), false);
                /**
                 * Currency
                 */
                $this->Currency->deleteAll(array('Currency.company_id' => $company_id), false);
                /**
                 * ProjectCreatedValue
                 */
                $this->ProjectCreatedValue->deleteAll(array('ProjectCreatedValue.company_id' => $company_id), false);
                /**
                 * Holiday
                 */
                $this->Holiday->deleteAll(array('Holiday.company_id' => $company_id), false);
                /**
                 * LogSystem
                 */
                $this->LogSystem->deleteAll(array('LogSystem.company_id' => $company_id), false);
                /**
                 * Menu
                 */
                $this->Menu->deleteAll(array('Menu.company_id' => $company_id), false);
                /**
                 * ProfitCenter
                 */
                $this->ProfitCenter->deleteAll(array('ProfitCenter.company_id' => $company_id), false);
                /**
                 * ProjectAmrCategory
                 */
                $ProjectAmrCategory = $this->ProjectAmrCategory->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectAmrCategory.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectAmrSubCategory->deleteAll(array('ProjectAmrSubCategory.project_amr_category_id' => $ProjectAmrCategory), false);
                $this->ProjectAmrCategory->deleteAll(array('ProjectAmrCategory.company_id' => $company_id), false);
                /**
                 * ProjectAmrCostControl
                 */
                $this->ProjectAmrCostControl->deleteAll(array('ProjectAmrCostControl.company_id' => $company_id), false);
                /**
                 * ProjectAmrOrganization
                 */
                $this->ProjectAmrOrganization->deleteAll(array('ProjectAmrOrganization.company_id' => $company_id), false);
                /**
                 * ProjectAmrPerimeter
                 */
                $this->ProjectAmrPerimeter->deleteAll(array('ProjectAmrPerimeter.company_id' => $company_id), false);
                /**
                 * ProjectAmrPlan
                 */
                $this->ProjectAmrPlan->deleteAll(array('ProjectAmrPlan.company_id' => $company_id), false);
                /**
                 * ProjectAmrProblemControl
                 */
                $this->ProjectAmrProblemControl->deleteAll(array('ProjectAmrProblemControl.company_id' => $company_id), false);
                /**
                 * ProjectAcceptanceType
                 */
                $this->ProjectAcceptanceType->deleteAll(array('ProjectAcceptanceType.company_id' => $company_id), false);
                /**
                 * ProjectAmrProgram
                 */
                $ProjectAmrProgram = $this->ProjectAmrProgram->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectAmrProgram.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectAmrSubProgram->deleteAll(array('ProjectAmrSubProgram.project_amr_program_id' => $ProjectAmrProgram), false);
                $this->ProjectAmrProgram->deleteAll(array('ProjectAmrProgram.company_id' => $company_id), false);
                /**
                 * ProjectAmrRiskControl
                 */
                $this->ProjectAmrRiskControl->deleteAll(array('ProjectAmrRiskControl.company_id' => $company_id), false);
                /**
                 * ProjectAmrStatuse
                 */
                $this->ProjectAmrStatuse->deleteAll(array('ProjectAmrStatuse.company_id' => $company_id), false);
                /**
                 * ProjectComplexity
                 */
                $this->ProjectComplexity->deleteAll(array('ProjectComplexity.company_id' => $company_id), false);
                /**
                 * ProjectEvolutionImpact
                 */
                $this->ProjectEvolutionImpact->deleteAll(array('ProjectEvolutionImpact.company_id' => $company_id), false);
                /**
                 * ProjectEvolutionType
                 */
                $this->ProjectEvolutionType->deleteAll(array('ProjectEvolutionType.company_id' => $company_id), false);
                /**
                 * ProjectFunction
                 */
                $this->ProjectFunction->deleteAll(array('ProjectFunction.company_id' => $company_id), false);
                /**
                 * ProjectIssueSeverity
                 */
                $this->ProjectIssueSeverity->deleteAll(array('ProjectIssueSeverity.company_id' => $company_id), false);
                /**
                 * ProjectIssueStatuse
                 */
                $this->ProjectIssueStatuse->deleteAll(array('ProjectIssueStatuse.company_id' => $company_id), false);
                /**
                 * ProjectLivrableCategory
                 */
                $this->ProjectLivrableCategory->deleteAll(array('ProjectLivrableCategory.company_id' => $company_id), false);
                /**
                 * ProjectPhaseStatuse
                 */
                $this->ProjectPhaseStatuse->deleteAll(array('ProjectPhaseStatuse.company_id' => $company_id), false);
                /**
                 * ProjectPriority
                 */
                $this->ProjectPriority->deleteAll(array('ProjectPriority.company_id' => $company_id), false);
                /**
                 * ProjectPhase
                 */
                $this->ProjectPhase->deleteAll(array('ProjectPhase.company_id' => $company_id), false);
                /**
                 * ProjectRiskOccurrence
                 */
                $this->ProjectRiskOccurrence->deleteAll(array('ProjectRiskOccurrence.company_id' => $company_id), false);
                /**
                 * ProjectRiskSeverity
                 */
                $this->ProjectRiskSeverity->deleteAll(array('ProjectRiskSeverity.company_id' => $company_id), false);
                /**
                 * ProjectStatuse
                 */
                $this->ProjectStatuse->deleteAll(array('ProjectStatuse.company_id' => $company_id), false);
                /**
                 * ProjectType
                 */
                $ProjectType = $this->ProjectType->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectType.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectSubType->deleteAll(array('ProjectSubType.project_type_id' => $ProjectType), false);
                $this->ProjectType->deleteAll(array('ProjectType.company_id' => $company_id), false);
                /**
                 * ResponseConstraint
                 */
                $this->ResponseConstraint->deleteAll(array('ResponseConstraint.company_id' => $company_id), false);
                /**
                 * SaleCustomerContact
                 */
                $this->SaleCustomerContact->deleteAll(array('SaleCustomerContact.company_id' => $company_id), false);
                /**
                 * SaleCustomerIban
                 */
                $this->SaleCustomerIban->deleteAll(array('SaleCustomerIban.company_id' => $company_id), false);
                /**
                 * SaleCustomer
                 */
                $this->SaleCustomer->deleteAll(array('SaleCustomer.company_id' => $company_id), false);
                /**
                 * SaleExpense
                 */
                $this->SaleExpense->deleteAll(array('SaleExpense.company_id' => $company_id), false);
                /**
                 * SaleLeadEmployeeRefer
                 */
                $this->SaleLeadEmployeeRefer->deleteAll(array('SaleLeadEmployeeRefer.company_id' => $company_id), false);
                /**
                 * SaleLead
                 */
                $SaleLead = $this->SaleLead->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLead.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->SaleLeadFile->deleteAll(array('SaleLeadFile.sale_lead_id' => $SaleLead), false);
                $this->SaleLead->deleteAll(array('SaleLead.company_id' => $company_id), false);
                /**
                 * SaleLeadLog
                 */
                $this->SaleLeadLog->deleteAll(array('SaleLeadLog.company_id' => $company_id), false);
                /**
                 * SaleLeadProductExpense
                 */
                $this->SaleLeadProductExpense->deleteAll(array('SaleLeadProductExpense.company_id' => $company_id), false);
                /**
                 * SaleLeadProductInvoice
                 */
                $this->SaleLeadProductInvoice->deleteAll(array('SaleLeadProductInvoice.company_id' => $company_id), false);
                /**
                 * SaleLeadProduct
                 */
                $this->SaleLeadProduct->deleteAll(array('SaleLeadProduct.company_id' => $company_id), false);
                /**
                 * SaleRole
                 */
                $this->SaleRole->deleteAll(array('SaleRole.company_id' => $company_id), false);
                /**
                 * SaleSetting
                 */
                $this->SaleSetting->deleteAll(array('SaleSetting.company_id' => $company_id), false);
                /**
                 * SecuritySetting
                 */
                $this->SecuritySetting->deleteAll(array('SecuritySetting.company_id' => $company_id), false);
                /**
                 * TmpCaculateAbsence
                 */
                $this->TmpCaculateAbsence->deleteAll(array('TmpCaculateAbsence.company_id' => $company_id), false);
                /**
                 * TmpCaculateProfitCenter
                 */
                $this->TmpCaculateProfitCenter->deleteAll(array('TmpCaculateProfitCenter.company_id' => $company_id), false);
                /**
                 * TmpModuleActivityExport
                 */
                $this->TmpModuleActivityExport->deleteAll(array('TmpModuleActivityExport.company_id' => $company_id), false);
                /**
                 * TmpStaffingSystem
                 */
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.company_id' => $company_id), false);
                /**
                 * TranslationEntry
                 */
                $this->TranslationEntry->deleteAll(array('TranslationEntry.company_id' => $company_id), false);
                /**
                 * TranslationSetting
                 */
                $this->TranslationSetting->deleteAll(array('TranslationSetting.company_id' => $company_id), false);
                /**
                 * UserView
                 */
                $this->UserView->deleteAll(array('UserView.company_id' => $company_id), false);
                /**
                 * Workday
                 */
                $this->Workday->deleteAll(array('Workday.company_id' => $company_id), false);
                /**
                 * Profile
                 */
                $Profile = $this->Profile->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('Profile.company_id' => $company_id),
                    'fields' => array('id', 'id')
                ));
                $this->ProfileValue->deleteAll(array('ProfileValue.profile_id' => $Profile), false);
                $this->Profile->deleteAll(array('Profile.company_id' => $company_id), false);
                /**
                 * AbsenceComment
                 */
                $this->AbsenceComment->deleteAll(array('AbsenceComment.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * AbsenceRequest
                 */
                $this->AbsenceRequest->deleteAll(array('AbsenceRequest.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * AbsenceRequestFile
                 */
                $this->AbsenceRequestFile->deleteAll(array('AbsenceRequestFile.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * ActivityComment
                 */
                $this->ActivityComment->deleteAll(array('ActivityComment.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * ActivityForecast
                 */
                $this->ActivityForecast->deleteAll(array('ActivityForecast.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * EmployeeAbsence
                 */
                $this->EmployeeAbsence->deleteAll(array('EmployeeAbsence.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * HistoryFilter
                 */
                $this->HistoryFilter->deleteAll(array('HistoryFilter.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * ProjectEmployeeProfitFunctionRefer
                 */
                $this->ProjectEmployeeProfitFunctionRefer->deleteAll(array('ProjectEmployeeProfitFunctionRefer.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * UserDefaultView
                 */
                $this->UserDefaultView->deleteAll(array('UserDefaultView.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * UserStatusViewActivity
                 */
                $this->UserStatusViewActivity->deleteAll(array('UserStatusViewActivity.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * UserStatusViewSaleDeal
                 */
                $this->UserStatusViewSaleDeal->deleteAll(array('UserStatusViewSaleDeal.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * UserStatusViewSale
                 */
                $this->UserStatusViewSale->deleteAll(array('UserStatusViewSale.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * UserStatusView
                 */
                $this->UserStatusView->deleteAll(array('UserStatusView.employee_id' => $CompanyEmployeeReferences), false);
                /**
                 * Delete company
                 */
                $this->Company->delete($company_id);
                echo 'OKIE!';
            } else {
                echo 'Company not found!';
            }
        } else {
            echo 'Company not found!';
        }
        exit;
    }
    /**
     * Delete project and activity of company
     */
    public function deleteProjectAndActivityOfCompany($company = null){
        if(!empty($company)){
            $this->loadModel('Company');
            $this->loadModel('Project');
            $this->loadModel('Activity');
            $this->loadModel('ProjectTask');
            $this->loadModel('ProjectTaskEmployeeRefer');
            $this->loadModel('ActivityTask');
            $this->loadModel('ActivityTaskEmployeeRefer');
            $this->loadModel('ProjectGlobalView');
            $this->loadModel('ProjectLocalView');
            $this->loadModel('ProjectImage');
            $this->loadModel('ProjectCreatedVal');
            $this->loadModel('ProjectTeam');
            $this->loadModel('ProjectFunctionEmployeeRefer');
            $this->loadModel('ProjectPart');
            $this->loadModel('ProjectPhasePlan');
            $this->loadModel('ProjectMilestone');
            $this->loadModel('ProjectBudgetSyn');
            $this->loadModel('ProjectFinance');
            $this->loadModel('ProjectFinancePartner');
            $this->loadModel('ProjectRisk');
            $this->loadModel('ProjectIssue');
            $this->loadModel('ProjectDecision');
            $this->loadModel('ProjectLivrable');
            $this->loadModel('ProjectLivrableActor');
            $this->loadModel('ProjectEvolution');
            $this->loadModel('ProjectEvolutionImpactRefer');
            $this->loadModel('ProjectAmr');
            $this->loadModel('ProjectBudgetSale');
            $this->loadModel('ProjectBudgetInternal');
            $this->loadModel('ProjectBudgetInternalDetail');
            $this->loadModel('ProjectBudgetExternal');
            $this->loadModel('ProjectEmployeeManager');
            $this->loadModel('ActivityProfitRefer');
            $this->loadModel('ActivityRequest');
            $this->loadModel('NctWorkload');
            $this->loadModel('ProjectAcceptance');
            $this->loadModel('ProjectBudgetInvoice');
            $this->loadModel('ProjectBudgetProvisional');
            $company = strtolower(trim($company));
            $companies = $this->Company->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_name' => $company),
                'fields' => array('id')
            ));
            if(!empty($companies) && !empty($companies['Company']['id'])){
                $company_id = $companies['Company']['id'];
                /**
                 * Get all projects of company
                 */
                $projects = $this->Project->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'activity_id')
                ));
                $activityOfProjects = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.activity_id') : array();
                $projectIdLists = !empty($projects) ? Set::classicExtract($projects, '{n}.Project.id') : array();
                /**
                 * Delete projects
                 */
                $this->Project->deleteAll(array('Project.id' => $projectIdLists), false);
                /**
                 * Get All project task of projects
                 */
                $projectTasks = $this->ProjectTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('project_id' => $projectIdLists),
                    'fields' => array('id', 'id')
                ));
                /**
                 * Delete project tasks
                 */
                $this->ProjectTask->deleteAll(array('ProjectTask.id' => $projectTasks), false);
                /**
                 * Delete project task assign to
                 */
                $this->ProjectTaskEmployeeRefer->deleteAll(array('ProjectTaskEmployeeRefer.project_task_id' => $projectTasks), false);
                /**
                 * Delete ProjectGlobalView
                 */
                $this->ProjectGlobalView->deleteAll(array('ProjectGlobalView.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectAcceptance
                 */
                $this->ProjectAcceptance->deleteAll(array('ProjectAcceptance.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectGlobalView
                 */
                $this->ProjectGlobalView->deleteAll(array('ProjectGlobalView.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectLocalView
                 */
                $this->ProjectLocalView->deleteAll(array('ProjectLocalView.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectImage
                 */
                $this->ProjectImage->deleteAll(array('ProjectImage.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectCreatedVal
                 */
                $this->ProjectCreatedVal->deleteAll(array('ProjectCreatedVal.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectPart
                 */
                $this->ProjectPart->deleteAll(array('ProjectPart.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectPhasePlan
                 */
                $this->ProjectPhasePlan->deleteAll(array('ProjectPhasePlan.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectMilestone
                 */
                $this->ProjectMilestone->deleteAll(array('ProjectMilestone.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectBudgetSyn
                 */
                $this->ProjectBudgetSyn->deleteAll(array('ProjectBudgetSyn.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectFinance
                 */
                $projectFinances = $this->ProjectFinance->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectFinance.project_id' => $projectIdLists),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectFinance->deleteAll(array('ProjectFinance.project_id' => $projectIdLists), false);
                $this->ProjectFinancePartner->deleteAll(array('ProjectFinancePartner.finance_id' => $projectFinances), false);
                /**
                 * Delete ProjectRisk
                 */
                $this->ProjectRisk->deleteAll(array('ProjectRisk.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectIssue
                 */
                $this->ProjectIssue->deleteAll(array('ProjectIssue.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectDecision
                 */
                $this->ProjectDecision->deleteAll(array('ProjectDecision.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectLivrable
                 */
                $projectLivrables = $this->ProjectLivrable->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectLivrable.project_id' => $projectIdLists),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectLivrableActor->deleteAll(array('ProjectLivrableActor.project_livrable_id' => $projectLivrables), false);
                $this->ProjectLivrable->deleteAll(array('ProjectLivrable.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectEvolution
                 */
                $projectEvolutions = $this->ProjectEvolution->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectEvolution.project_id' => $projectIdLists),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectEvolutionImpactRefer->deleteAll(array('ProjectEvolutionImpactRefer.project_evolution_id' => $projectEvolutions), false);
                $this->ProjectEvolution->deleteAll(array('ProjectEvolution.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectAmr
                 */
                $this->ProjectAmr->deleteAll(array('ProjectAmr.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectTeam
                 */
                $projectTeams = $this->ProjectTeam->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('ProjectTeam.project_id' => $projectIdLists),
                    'fields' => array('id', 'id')
                ));
                $this->ProjectFunctionEmployeeRefer->deleteAll(array('ProjectFunctionEmployeeRefer.project_team_id' => $projectTeams), false);
                $this->ProjectTeam->deleteAll(array('ProjectTeam.project_id' => $projectIdLists), false);
                /**
                 * Delete ProjectEmployeeManager
                 */
                $this->ProjectEmployeeManager->deleteAll(array('ProjectEmployeeManager.project_id' => $projectIdLists), false);
                /**
                 * Get all activity of company
                 */
                $activities = $this->Activity->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'OR' => array(
                            'company_id' => $company_id,
                            'Activity.id' => $activityOfProjects
                        )
                    ),
                    'fields' => array('id', 'id')
                ));
                /**
                 * Delete ActivityProfitRefer
                 */
                $this->ActivityProfitRefer->deleteAll(array('ActivityProfitRefer.activity_id' => $activities), false);
                /**
                 * Delete ProjectBudgetSale
                 */
                $this->ProjectBudgetSale->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetSale.project_id' => $projectIdLists,
                        'ProjectBudgetSale.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete ProjectBudgetInvoice
                 */
                $this->ProjectBudgetInvoice->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetInvoice.project_id' => $projectIdLists,
                        'ProjectBudgetInvoice.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete ProjectBudgetProvisional
                 */
                $this->ProjectBudgetProvisional->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetProvisional.project_id' => $projectIdLists,
                        'ProjectBudgetProvisional.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete ProjectBudgetInternal
                 */
                $this->ProjectBudgetInternal->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetInternal.project_id' => $projectIdLists,
                        'ProjectBudgetInternal.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete ProjectBudgetInternalDetail
                 */
                $this->ProjectBudgetInternalDetail->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetInternalDetail.project_id' => $projectIdLists,
                        'ProjectBudgetInternalDetail.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete ProjectBudgetExternal
                 */
                $this->ProjectBudgetExternal->deleteAll(array(
                    'OR' => array(
                        'ProjectBudgetExternal.project_id' => $projectIdLists,
                        'ProjectBudgetExternal.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete activities
                 */
                $this->Activity->deleteAll(array('Activity.id' => $activities), false);
                /**
                 * Get all activity tasks
                 */
                $activityTasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('activity_id' => $activities),
                    'fields' => array('id', 'id')
                ));
                /**
                 * Delete activity task
                 */
                $this->ActivityTask->deleteAll(array('ActivityTask.id' => $activityTasks), false);
                /**
                 * Delete activity task assign to
                 */
                $this->ActivityTaskEmployeeRefer->deleteAll(array('ActivityTaskEmployeeRefer.activity_task_id' => $activityTasks), false);
                /**
                 * Delete ActivityRequest
                 */
                $this->ActivityRequest->deleteAll(array(
                    'OR' => array(
                        'ActivityRequest.task_id' => $activityTasks,
                        'ActivityRequest.activity_id' => $activities
                    )
                ), false);
                /**
                 * Delete NctWorkload
                 */
                $this->NctWorkload->deleteAll(array(
                    'OR' => array(
                        'NctWorkload.project_task_id' => $projectTasks,
                        'NctWorkload.activity_task_id' => $activityTasks
                    )
                ), false);
                echo 'OKIE!';
            } else {
                echo 'Company not found!';
            }
        } else {
            echo 'Company not found!';
        }
        exit;
    }
    /**
     * Ham dung de kiem tra mot so van de cua he thong.
     * Em co the edit va save tuy y
     */
    public function checkBugSystem(){
        $ActivityRequest = ClassRegistry::init('ActivityRequest');
    }
    /**
     * Add activity id vao table activity request.
     * Chua xong
     */
    public function addActivityIdToActivityRequest(){
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('ActivityRequest');
        $activityRequests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'NOT' => array('task_id' => '')
            ),
            'fields' => array('')
        ));
    }
    /**
     * Xoa menu cua project va he thong
     */
    public function deleteMenuOfCompanyLogin($model = null){
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('Menu');
        $this->Menu->deleteAll(array('company_id' => $company_id, 'model' => $model), false);
        echo 'OK!';
        exit();
    }
    /**
     * Xoa activity export module fields
     */
    public function deleteActivityExportOfCompanyLogin(){
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('ActivityExport');
        $this->ActivityExport->deleteAll(array('company_id' => $company_id), false);
        echo 'OK!';
        exit();
    }
    /**
     * Ham dung de kiem tra cac task co consumed, nhung lai ton tai sub-task.
     * Chuyen cac consumed cua task cho 1 sub-task thuoc task do.
     *
     */
    public function changeConsumedAllTask(){
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $company_id = $this->employee_info['Company']['id'];
        /**
         * Kiem tra cac project co linked voi activity
         */
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array(
                    'activity_id' => null
                )
            ),
            'fields' => array('id', 'id')
        ));
        /**
         * Lay tat cac cac task co project linked voi activity
         */
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $projects),
            'fields' => array('id', 'parent_id'),
            'order' => array('id' => 'DESC')
        ));
        /**
         * Lay nhung task parent, nhung id task cos sub-task
         */
        $parentIds = !empty($projectTasks) ? array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id')) : array();
        /**
         * Lay cac activity task linked voi project Task
         */
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $parentIds),
            'fields' => array('id', 'project_task_id')
        ));
        /**
         * Kiem tra xem nhung task parent nao co consumed
         */
        $activityRequests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array('task_id' => array_keys($activityTasks)),
            'fields' => array('id', 'task_id')
        ));
        $projectTaskParentHaveConsumeds = array();
        foreach($activityRequests as $activityTaskId){
            $taskId = !empty($activityTasks[$activityTaskId]) ? $activityTasks[$activityTaskId] : 0;
            $projectTaskParentHaveConsumeds[$activityTaskId] = $taskId;
        }
        $Childs = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.id') : array();
        $pTaskChilds = array();
        foreach($projectTaskParentHaveConsumeds as $aTask => $parent){
            $pTaskChilds[$aTask] = !empty($Childs[$parent]) ? $Childs[$parent] : 0;
        }
        /**
         * Lay nhung child activity task linked voi project Task.
         */
        $childTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => $pTaskChilds),
            'fields' => array('project_task_id', 'id')
        ));
        /**
         * Gan task cha cho task con
         */
        $changeTasks = array();
        foreach($pTaskChilds as $parent => $id){
            $changeTasks[$parent] = !empty($childTasks[$id]) ? $childTasks[$id] : 0;
        }
        /**
         * Chuyen consumed o project task: chuyen thang cha cho thang con gan nhat.
         */
        foreach($activityRequests as $id => $parent){
            $this->ActivityRequest->id = $id;
            $_saved['task_id'] = !empty($changeTasks[$parent]) ? $changeTasks[$parent] : 0;
            $_saved['company_id'] = $company_id;
            $this->ActivityRequest->save($_saved);
        }
        /**
         * Lay tat ca cac task có activity ko linked voi project
         */
        $aTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_task_id' => null
            ),
            'fields' => array('id', 'parent_id'),
            'order' => array('id' => 'DESC')
        ));
         /**
         * Lay nhung task parent, nhung id task cos sub-task
         */
        $aParents = !empty($aTasks) ? array_unique(Set::classicExtract($aTasks, '{n}.ActivityTask.parent_id')) : array();
        /**
         * Kiem tra xem nhung task parent nao co consumed
         */
        $requests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array('task_id' => $aParents),
            'fields' => array('id', 'task_id')
        ));
        $Achilds = !empty($aTasks) ? Set::combine($aTasks, '{n}.ActivityTask.parent_id', '{n}.ActivityTask.id') : array();
        foreach($requests as $id => $parent){
            $this->ActivityRequest->id = $id;
            $_saved['task_id'] = !empty($Achilds[$parent]) ? $Achilds[$parent] : 0;
            $_saved['company_id'] = $company_id;
            $this->ActivityRequest->save($_saved);
        }
        echo 'finish!';
        exit;
    }
    /**
    * kiem tra tat ca cac Project Phase khong co real start date
    * thi real start date = plan tart date
    */
    public function auto_set_real_time(){
        $this->loadModel('ProjectPhasePlan');
        $ProjectPhasePlans = $this->ProjectPhasePlan->find('all',array(
            'recursive' => -1,
            'conditions' => array(
                'OR' => array(
                    'phase_real_start_date IS null',
                    'phase_real_start_date' => '0000-00-00',
                )
            )
        ));
        foreach($ProjectPhasePlans as $key => $ProjectPhasePlan){
            $ProjectPhasePlan['ProjectPhasePlan']['phase_real_start_date'] = $ProjectPhasePlan['ProjectPhasePlan']['phase_planed_start_date'];
            $this->ProjectPhasePlan->id = $ProjectPhasePlan['ProjectPhasePlan']['id'];
            $this->ProjectPhasePlan->save($ProjectPhasePlan['ProjectPhasePlan']);
        }
        echo "Finish";
        exit;
    }
    /**
     * Update project_budget_extertals
     * Cap nhat id = 1 co attach la 1 file. Doi format thanh 2.
     */
    public function updateFormatFileExternal(){
        $this->loadModel('ProjectBudgetExternal');
        $datas = $this->ProjectBudgetExternal->find('first', array(
            'recursive' => -1,
            'conditions' => array('ProjectBudgetExternal.id' => 1)
        ));
        unset($datas['ProjectBudgetExternal']['id']);
        $datas = $datas['ProjectBudgetExternal'];
        $datas['format'] = 2;
        $this->ProjectBudgetExternal->id = 1;
        $this->ProjectBudgetExternal->save($datas);
        echo 'Finish!';
        exit;
    }
    /**
     * Kiem tra du lieu o table project_livrables.
     * Neu co ton tai livrable_file_attachment thi update cot format gia tri thanh 2.
     */
    public function updateFormatFileLivrables(){
        $this->loadModel('ProjectLivrable');
        $datas = $this->ProjectLivrable->find('list', array(
            'recursive' => -1,
            'conditions' => array('NOT' => array('livrable_file_attachment' => '')),
            'fields' => array('id', 'livrable_file_attachment')
        ));
        $totalDatas = count($datas);
        $check = 0;
        foreach($datas as $id => $data){
            $this->ProjectLivrable->id = $id;
            $saved = array('format' => 2);
            if($this->ProjectLivrable->save($saved)){
                $check++;
            }
        }
        echo 'Saved: ' . $check . '/' . $totalDatas;
        echo '<br />Finish!';
        exit;
    }
    function loadt(){
         $this->loadModel('Employee');
         for($i = 1; $i <= 450; $i++){
            $this->data["Employee"]['email'] = 'test.perform' .$i. '@greensystem.vn';
            $this->data["Employee"]['password'] = md5('123456');
            $this->data["Employee"]['is_sas'] = 1;
            $this->data["Employee"]['actif'] = 1;
            $this->data["Employee"]['start_date'] = '0000-00-00';
            $this->data["Employee"]['end_date'] = '0000-00-00';
            $this->Employee->create();
            $this->Employee->save($this->data["Employee"]);
         }
         exit;
    }
    function changeEmailAndPassEmployee(){
        $this->loadModel('Employee');
        $employees = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('is_sas' => 1),
                'NOT' => array('Employee.id' => 117)
            ),
            'fields' => array('id', 'id')
        ));
        $count = 0;
        $i = 1;
        foreach($employees as $id){
            $this->Employee->id = $id;
            $saved = array(
                'email' => 'test.perform' .$i. '@greensystem.vn',
                'password' => md5('123456'),
                'actif' => 1,
                'start_date' => '0000-00-00',
                'end_date' => '0000-00-00'
            );
            if($this->Employee->save($saved)){
                $count++;
                $i++;
            }
        }
        echo 'Save: ' . $count . ' / ' . count($employees);
        exit;
    }
    public function set_status_alancer_for_tasks(){
        $this->loadModel('ProjectTask');
        $taskNoStatus = $this->ProjectTask->find('list',array(
            'recursive' => -1,
            'conditions' => array(
                'task_status_id is null'
            ),
            'fields' => array('id','id')
        ));
        if(!empty($taskNoStatus)){
            $this->ProjectTask->updateAll(
                array('ProjectTask.task_status_id' => 71),
                array('ProjectTask.id ' => $taskNoStatus)
            );
        }
        echo 'finish';
        exit;
    }
    /**
     * Xoa cac model thuoc phan staffing khong can thiet, khong dung nua
     *
     */
    public function deleteFileModelNotUsed(){
        $path = APP . 'models' . DS;
        $fileDeletes = array(
            'tmp_staffing_activity', 'tmp_staffing_activity_profit_center', 'tmp_staffing_function',
            'tmp_staffing_profit_center', 'tmp_staffing_project', 'project_staffing_employee',
            'project_staffing_demo', 'project_staffing', 'activity_staffing', 'tmp_staffing_employee'
        );
        foreach($fileDeletes as $fileDelete){
            if(file_exists($path . $fileDelete.'.php')){
                $files = $path . $fileDelete.'.php';
                 unlink($files);
            }
        }
        echo 'Finish!';
        exit;
    }
    /**
     * Xoa cac request khong co y nghia.
     * Detail: Co mot so employee vao request activity or task nhung khi luu du lieu thi value = 0, activity_id = 0, task_id = 0.
     * Cac du lieu nay se khong co y nghia trong qua trinh su dung. Nen xoa di.
     */
     public function deleteRequestNotUsing(){
        $this->loadModel('ActivityRequest');
        $requests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => 0,
                'task_id' => 0
            ),
            'fields' => array('id', 'activity_id', 'task_id', 'value')
        ));
        if(!empty($requests)){
            foreach($requests as $request){
                $dx = $request['ActivityRequest'];
                echo $dx['activity_id'] . ' - ' . $dx['task_id'] . ' - ' . $dx['value'] . '<br />';
                $this->ActivityRequest->delete($dx['id']);
            }
        }
        echo 'Finish!';
        exit;
     }
     /**
      * Xoa cac du lieu khong can thiet o table tmp_staffing_systems
      */
     public function allProjectLinked(){
        $this->loadModel('Project');
        $lists = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        debug($lists);
        echo 'Finish!';
        exit;
     }
     /**
      * Xoa absence request confirm bi du thua
      */
     public function deleteAbsenceNotUsing(){
        $this->loadModel('AbsenceRequestConfirm');
        $this->AbsenceRequestConfirm->delete(4877);
        echo 'Finish!';
        exit;
     }
     /**
      * Doi table tu MyISAM sang InnoDB
      */
     public function changeMyisamIntoInnoDB(){
        $db = ConnectionManager::getDataSource('default');
        $sql = "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine <> 'InnoDB'";
        $datas = $db->query($sql);
        if(!empty($datas)){
            $datas = Set::combine($datas, '{n}.TABLES.table_name', '{n}.TABLES.table_name');
            foreach($datas as $table){
                $_check = 'SHOW TABLES LIKE "' .$table. '"';
                $runs = $db->query($_check);
                if(!empty($runs)) {
                    $_SQL = 'ALTER TABLE ' . $table . ' ENGINE=INNODB';
                    $db->query($_SQL);
                } else {
                    //do nothing
                }
            }
        }
        echo 'Finish!';
        exit;
     }
     /**
      * Tim tat ca cac project task parent va activity task parent co consumed va tien hanh remove
      */
     public function removeTaskParentHaveConsumed(){
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $projectTasks = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('parent_id' => 0)
            ),
            'fields' => array('id',' parent_id')
        ));
        if(!empty($projectTasks)){
            $listParents = array_unique(Set::classicExtract($projectTasks, '{n}.ProjectTask.parent_id'));
            if(!empty($listParents)){
                $listPTasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('project_task_id' => $listParents),
                    'fields' => array('project_task_id', 'id')
                ));
                $listATasks = $this->ActivityTask->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_task_id' => null,
                        'NOT' => array('parent_id' => 0)
                    ),
                    'fields' => array('id', 'parent_id')
                ));
                $listATasks = !empty($listATasks) ? array_unique($listATasks) : array();
                $activityTasks = array_unique(array_merge($listPTasks, $listATasks));
                if(!empty($activityTasks)){
                    $requests = $this->ActivityRequest->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('task_id' => $activityTasks),
                        'fields' => array('id')
                    ));
                    if(!empty($requests)){
                        foreach($requests as $id){
                            $this->ActivityRequest->delete($id);
                        }
                    }
                }
            }
        }
        echo 'Finish!';
        exit;
     }
     /**
      * Cap nhat lai gia tr? average cho internal.
      * luc truoc xay dung 1 average dung chung duoc luu o table: project_budget_internal.
      * Sau nay yeu cau thay doi: cu moi record trong internal dieu co 1 gia tr? average khac nhau.
      * function nay giup lay gia tr? average cu update cho tat ca cac record moi.
      */
     public function updateAverageInternalDetail(){
        $this->loadModel('ProjectBudgetInternal');
        $this->loadModel('ProjectBudgetInternalDetail');
        $internals = $this->ProjectBudgetInternal->find('list', array(
            'recursive' => -1,
            'fields' => array('project_id', 'average_daily_rate')
        ));
        if(!empty($internals)){
            foreach($internals as $project_id => $average){
                $this->ProjectBudgetInternalDetail->updateAll(
                    array('ProjectBudgetInternalDetail.average' => $average),
                    array('ProjectBudgetInternalDetail.project_id' => $project_id)
                );
            }
        }
        echo 'OK';
        exit;
     }
     /**
      * Cap nhat lai cac task bi mat consumed trong luc linkd va unlink
      */
     public function updateTaskLoseConsumed(){
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_task_id' => array(3474, 3475, 3476, 3514, 3583)),
            'fields' => array('project_task_id', 'id')
        ));
        $linkTaskOlds = array(
            3474 => 4612,
            3475 => 4929,
            3476 => 4610,
            3514 => 4613,
            3583 => 4930
        );
        $linkTaskNews = array();
        if(!empty($activityTasks)){
            foreach($activityTasks as $idPr => $newId){
                $oldId = !empty($linkTaskOlds[$idPr]) ? $linkTaskOlds[$idPr] : 0;
                $linkTaskNews[$oldId] = $newId;
            }
        }
        $requests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array('task_id' => $linkTaskOlds),
            'fields' => array('id', 'task_id')
        ));
        if(!empty($requests)){
            foreach($requests as $id => $val){
                $addId = !empty($linkTaskNews[$val]) ? $linkTaskNews[$val] : 0;
                $saved = array('task_id' => $addId);
                $this->ActivityRequest->id = $id;
                $this->ActivityRequest->save($saved);
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Cap nhat lai cac project linked voi activity.
      * Do trong qua trinh hoat dong. Mot so project da mat lien ket voi activity.
      * Nen ham nay dung de kiem tra cac project nao mat lien ket voi activity thi tien hanh linked lai
      */
     public function updateLinkedProject(){
        $this->loadModel('Activity');
        $this->loadModel('Project');
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('project' => null)
            ),
            'fields' => array('id', 'project')
        ));
        if(!empty($activities)){
            foreach($activities as $activityId => $projectId){
                $this->Project->id = $projectId;
                $saved = array(
                    'activity_id' => $activityId
                );
                $this->Project->save($saved);
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Lay tat cac cac task va update trang thai theo nhu mo ta sau:
      * Project status = archived: All Task status = CLOS
      * Project status = opportunity: All Tasks status = a lancer
      * Project status = in progress: Task without status = a lancer
      */
     public function updateTaskStatusFollowStatusProject(){
        $this->loadModel('ProjectStatus');
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $infors = $this->Session->read('Auth.employee_info');
        $status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                //'name' => array('Clos', 'closed ', 'A lancer', 'A launch'),
                'company_id' => $infors['Company']['id']
            )
        ));
        $close = $aLancer = '';
        if(!empty($status)){
            foreach($status as $id => $val){
                $val = trim(strtolower($val));
                if($val == 'clos' || $val == 'closed'){
                    $close = $id;
                } elseif($val == 'a lancer' || $val == 'a launch'){
                    $aLancer = $id;
                }
            }
        }
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'category'),
            'order' => array('id')
        ));
        if(!empty($projects)){
            foreach($projects as $id => $cate){
                if($cate == 3){ // archived
                    $this->ProjectTask->updateAll(
                        array('ProjectTask.task_status_id' => $close),
                        array('ProjectTask.project_id' => $id)
                    );
                } else if($cate == 1){ //in progress
                    $tmps = $this->ProjectTask->find('list', array(
                        'recursive' => -1,
                        'conditions' => array('project_id' => $id),
                        'fields' => array('id', 'task_status_id')
                    ));
                    if(!empty($tmps)){
                        foreach($tmps as $idTask => $tmp){
                            if(!empty($tmp)){
                                // have status
                            } else {
                                $this->ProjectTask->id = $idTask;
                                $this->ProjectTask->save(array('task_status_id' => $aLancer));
                            }
                        }
                    }
                } else if($cate == 2){ //opportunity
                    $this->ProjectTask->updateAll(
                        array('ProjectTask.task_status_id' => $aLancer),
                        array('ProjectTask.project_id' => $id)
                    );
                }
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Lay tat ca cac activities khong linked voi project va co status la: not activated.
      * Kiem tra cac task cua activities tren. Neu task nao khong co status thi update status cho task do = CLOS
      */
     public function updateTaskStatusFollowActivityNotActivated(){
        $this->loadModel('ProjectStatus');
        $this->loadModel('Activity');
        $this->loadModel('ActivityTask');
        $infors = $this->Session->read('Auth.employee_info');
        $status = $this->ProjectStatus->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                //'name' => array('Clos', 'closed ', 'A lancer', 'A launch'),
                'company_id' => $infors['Company']['id']
            )
        ));
        $close = $aLancer = '';
        if(!empty($status)){
            foreach($status as $id => $val){
                $val = trim(strtolower($val));
                if($val == 'clos' || $val == 'closed'){
                    $close = $id;
                } elseif($val == 'a lancer' || $val == 'a launch'){
                    $aLancer = $id;
                }
            }
        }
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project' => null,
                'activated' => 0
            ),
            'fields' => array('id', 'id')
        ));
        if(!empty($activities)){
            $tasks = $this->ActivityTask->find('list', array(
                'recursive' => -1,
                'conditions' => array('activity_id' => $activities),
                'fields' => array('id', 'task_status_id')
            ));
            if(!empty($tasks)){
                foreach($tasks as $id => $status){
                    if(!empty($status)){
                        //do nothing
                    } else {
                        $this->ActivityTask->id = $id;
                        $this->ActivityTask->save(array('task_status_id' => $close));
                    }
                }
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Xoa het tat ca history cua he thong.
      */
     public function deleteHistoryOfSystem(){
        $this->loadModel('Employee');
        $histories = $this->Employee->HistoryFilter->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id'),
            'order' => array('id' => 'ASC')
        ));
        if(!empty($histories)){
            foreach($histories as $id){
                $this->Employee->HistoryFilter->delete($id);
            }
        }
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'ALTER TABLE history_filters AUTO_INCREMENT = 1';
        $db->query($setAuto);
        echo 'OK!';
        exit;
     }
      /**
      * Update project manager backup cho cac activity co linked voi project
      */
     public function updateProjectManagerActivityBackup(){
        $this->loadModel('Project');
        $this->loadModel('ProjectEmployeeManager');
        $infors = $this->Session->read('Auth.employee_info');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $infors['Company']['id'],
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('id', 'activity_id')
        ));
        if(!empty($projects)){
            foreach($projects as $projectId => $activityId){
                $this->ProjectEmployeeManager->updateAll(
                    array('ProjectEmployeeManager.activity_id' => $activityId),
                    array('ProjectEmployeeManager.project_id' => $projectId)
                );
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Update project manager primary cho activity co linked voi project
      */
     public function updateProjectManagerActivityPrimary(){
        $this->loadModel('Project');
        $this->loadModel('Activity');
        $infors = $this->Session->read('Auth.employee_info');
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $infors['Company']['id'],
                'NOT' => array('activity_id' => null)
            ),
            'fields' => array('activity_id', 'project_manager_id')
        ));
        if(!empty($projects)){
            foreach($projects as $activityId => $projectManager){
                $this->Activity->id = $activityId;
                $this->Activity->save(array('project_manager_id' => $projectManager));
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Update manager of activity to ActivityEmployeeManager From ProjectEmployeeManager
      */
     public function updateMangerActivityFromProjectEmployeeManager(){
        $this->loadModel('Project');
        $this->loadModel('ActivityEmployeeManager');
        $this->loadModel('ProjectEmployeeManager');
        $activityManagers = $this->ActivityEmployeeManager->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'activity_id', 'project_manager_id')
        ));
        $activityId = !empty($activityManagers) ? array_unique(Set::classicExtract($activityManagers, '{n}.ActivityEmployeeManager.activity_id')) : array();
        $projectLinkeds = $this->Project->find('list', array(
            'recurisve' => -1,
            'conditions' => array('activity_id' => $activityId),
            'fields' => array('activity_id', 'id')
        ));
        if(!empty($activityManagers)){
            foreach($activityManagers as $activityManager){
                $dx = $activityManager['ActivityEmployeeManager'];
                $tmp = $this->ProjectEmployeeManager->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'activity_id' => $dx['activity_id'],
                        'project_manager_id' => $dx['project_manager_id']
                    )
                ));
                if(!empty($tmp)){
                    //do nothing
                } else {
                    $saved = array(
                        'activity_id' => $dx['activity_id'],
                        'project_manager_id' => $dx['project_manager_id'],
                        'is_backup' => 1,
                        'project_id' => !empty($projectLinkeds[$dx['activity_id']]) ? $projectLinkeds[$dx['activity_id']] : 0
                    );
                    $this->ProjectEmployeeManager->create();
                    $this->ProjectEmployeeManager->save($saved);
                }
            }
        }
        echo 'OK!';
        exit;
     }
     /**
      * Xoa cac tmp TmpProfitCenterOfActivity khong luu activity id. Khong can thiet va khong dung den
      */
     public function deleteTmpProfitCenterOfActivityNotUsed(){
        $this->loadModel('TmpProfitCenterOfActivity');
        $this->TmpProfitCenterOfActivity->deleteAll(array('TmpProfitCenterOfActivity.activity_id' => null), false);
        echo 'OK!';
        exit;
     }
     /**
      * Update company for tmp
      */
     public function updateCompanyForTmp(){
        $this->loadModel('TmpCaculateAbsence');
        $this->loadModel('TmpCaculateProfitCenter');
        $this->loadModel('TmpProfitCenterOfActivity');
        $this->loadModel('TmpStaffingSystem');
        $infors = $this->Session->read('Auth.employee_info');
        $this->TmpCaculateAbsence->updateAll(
            array('TmpCaculateAbsence.company_id' => $infors['Company']['id']),
            array('TmpCaculateAbsence.id >=' => 0)
        );
        $this->TmpCaculateProfitCenter->updateAll(
            array('TmpCaculateProfitCenter.company_id' => $infors['Company']['id']),
            array('TmpCaculateProfitCenter.id >=' => 0)
        );
        $this->TmpProfitCenterOfActivity->updateAll(
            array('TmpProfitCenterOfActivity.company_id' => $infors['Company']['id']),
            array('TmpProfitCenterOfActivity.id >=' => 0)
        );
        $this->TmpStaffingSystem->updateAll(
            array('TmpStaffingSystem.company_id' => $infors['Company']['id']),
            array('TmpStaffingSystem.id >=' => 0)
        );
        echo 'OK!';
        exit;
     }
     /**
      * Delete table budget synthesis
      */
     public function deleteProjectBudgetSyn(){
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'TRUNCATE TABLE `project_budget_syns`';
        $db->query($setAuto);
        echo 'Completed!';
        exit;
        $this->loadModel('ProjectBudgetSyn');
        $this->ProjectBudgetSyn->deleteAll(array('ProjectBudgetSyn.id >=' => 0), false);
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'ALTER TABLE project_budget_syns AUTO_INCREMENT = 1';
        $db->query($setAuto);
        echo 'OK.';
        exit;
     }
	 
	 /**
		Created by Viet Nguyen
		Ticket #526
		Change logic caculate data project budget internal
	    Data project budget internal sync on DB table project_budget_sync.
		Do db cu khong thuc hien sync nen bay gio phai viet function sync data cu 
		Function nay chi sync data cua project budget internal vao bang project_budget_sync
	 */
	 public function dataFromProjectTask($listProject){
		$this->loadModels('ActivityRequest', 'ActivityTask', 'ProjectTask', 'ProjectTaskEmployeeRefer', 'Project');
        $projectTasks = $this->ProjectTask->find( "all", array(
			'fields' => array(
				'id',
				'project_id',
				'estimated',
				'parent_id',
				'special',
				'special_consumed',
				'manual_consumed',
				),
			'recursive' => -1,
			"conditions" => array('project_id' => $listProject))
        );
	
		$_projectTaskId = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.id', '{n}.ProjectTask.id') : array();
        $_parentIds = !empty($projectTasks) ? Set::combine($projectTasks, '{n}.ProjectTask.parent_id', '{n}.ProjectTask.parent_id') : array();
		
		$_projectIds = array();
		$_projectParentIds = array();
        foreach($projectTasks as $key => $projectTask){
			$dx = $projectTask['ProjectTask'];
            if(in_array($dx['id'], $_parentIds)){
				$_projectParentIds[$dx['project_id']][] =  $dx['id'];
                unset($projectTasks[$key]);
            }else{
				$_projectIds[$dx['project_id']][] = $dx['id'];
			}
        }
		
        foreach($_parentIds as $k => $value){
            if($value == 0){
                unset($_parentIds[$k]);
            }
        }
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'project_task_id'),
            'conditions' => array(
                'project_task_id' => $_projectTaskId,
                'NOT' => array("project_task_id" => null))
        ));
        $_activityTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $activityTasks = !empty($activityTasks) ? Set::combine($activityTasks, '{n}.ActivityTask.project_task_id', '{n}.ActivityTask') : array();
        $activityRequests = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'fields' => array( 'id', 'employee_id', 'task_id', 'SUM(value) as value'),
            'group' => array('task_id'),
            'conditions' => array(
                'status' => 2,
                'task_id' => $_activityTaskId,
                'NOT' => array('value' => 0, "task_id" => null),
            )
        ));
        $newActivityRequests = array();
        foreach ($activityRequests as $key => $activityRequest) {
            $newActivityRequests[$activityRequest['ActivityRequest']['task_id']] = $activityRequest[0]['value'];
        }
        $activityRequests = $newActivityRequests;
        $total = array();
        $sumEstimated = array();
		$sumRemain = array();
		$sumRemainConsumed = array();
		$sumRemainNotConsumed = array();
		$t = 0;
		// debug($projectTasks);
		// exit;
        foreach ($projectTasks as $key => $projectTask) {
			$dx = $projectTask['ProjectTask'];
            $projectTaskId = $dx['id'];
			if(!isset($sumRemainNotConsumed[$dx['project_id']])) $sumRemainNotConsumed[$dx['project_id']] = 0;
			if(!isset($total[$dx['project_id']])) $total[$dx['project_id']] = 0;
            if($dx['parent_id'] == 0){
				if(!isset($sumEstimated[$dx['project_id']])) $sumEstimated[$dx['project_id']] = 0;
                $sumEstimated[$dx['project_id']] += $dx['estimated'];
            }
            $estimated = isset($dx['estimated']) ? $dx['estimated'] : 0;
            // Check if Activity Task Existed
            if (isset($activityTasks[$projectTaskId])) {
                $activityTaskId = $activityTasks[$projectTaskId]['id'];
                // Check if Request Existed
                if (isset($activityRequests[$activityTaskId]) && empty($dx['special'])) {
                    $consumed = $activityRequests[$activityTaskId];
                    $overload = 0;
                    if($consumed >= $estimated){
                        $overload = $consumed - $estimated;
                    }
                    $completed = ($estimated + $overload) - $consumed;
                    if($completed < 0){
                        $completed = 0;
                    }
					if(!isset($sumRemainConsumed[$dx['project_id']])) $sumRemainConsumed[$dx['project_id']] = 0;
                    $sumRemainConsumed[$dx['project_id']] += $completed;
                    $total[$dx['project_id']] += $consumed;
                } else {
                    if(in_array($dx['id'], $_parentIds, true)){
                        //unset($projectTask);
                    } else {
                        if(!empty($dx['special']) && $dx['special'] == 1){
                            $specialCS = !empty($dx['special_consumed']) ? $dx['special_consumed'] : 0;
                            $sumRemainNotConsumed[$dx['project_id']] += ($estimated - $specialCS);
                        } else {
                            $sumRemainNotConsumed[$dx['project_id']] += $estimated;
                        }
                    }
                    $total[$dx['project_id']] += 0;
                }
            } else {
                // Error Handle
                $sumRemainNotConsumed[$dx['project_id']] += $estimated;
                $total[$dx['project_id']] += 0;
            }
        }
		
        if(!empty($_projectParentIds)){
            foreach($_projectParentIds as $_parentId => $_listParentTasks){
				if(!isset($total[$_parentId])) $total[$_parentId] = 0;
				foreach($_listParentTasks as $task_id){
					$_activityTaskId = !empty($activityTasks[$task_id]['id']) ? $activityTasks[$task_id]['id'] : '';
					$_consumed = !empty($activityRequests[$_activityTaskId]) ? $activityRequests[$_activityTaskId] : 0;
					$total[$_parentId] += $_consumed;
				}
            }
        }
        $activity_ids = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array('Project.id' => $listProject),
			'fields' => array('id', 'activity_id')
		));
		// Consumed of activity
        $consumedOfActivity = $this->ActivityRequest->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'activity_id' => $activity_ids,
                'status' => 2,
                'NOT' => array('value' => 0)
            ),
			'fields' => array('activity_id','SUM(value) as consumed'),
			'group' => array('activity_id')
        ));
		if(!empty($consumedOfActivity)){
			$projectLinked = array_flip(array_filter($activity_ids));
			foreach($consumedOfActivity as $values){
				$dx = $values['ActivityRequest'];
				$dy = $values[0];
				if(isset($projectLinked[$dx['activity_id']])){
					if(!isset($total[$projectLinked[$dx['activity_id']]])) $total[$projectLinked[$dx['activity_id']]] = 0;
					$total[$projectLinked[$dx['activity_id']]] += $dy['consumed'];
				}
				
			}
		}
		$_projectTask = array();
		foreach($listProject as $project_id){
			//consumed
			$consumed = !empty($total[$project_id]) ? $total[$project_id] : 0;
			$_projectTask[$project_id]['consumed'] = $consumed;
			
			// sumRemain
			$remainConsumed = !empty($sumRemainConsumed[$project_id]) ? $sumRemainConsumed[$project_id] : 0;
			$remainNotConsumed = !empty($sumRemainNotConsumed[$project_id]) ? $sumRemainNotConsumed[$project_id] : 0;
			$_projectTask[$project_id]['remain'] = $remainConsumed + $remainNotConsumed;
			
			// workload
			$sumRemain = $remainConsumed + $remainNotConsumed;
			$_projectTask[$project_id]['workload'] = $sumRemain + $consumed;
			
		}
        return $_projectTask;
    }
	function updateProjectBudgetSyn($type='list', $listProjectID = null){
		set_time_limit(0); 
		ignore_user_abort(true);
		$is_sas = $this->employee_info['Employee']['is_sas'] || ( $this->employee_info['Employee']['company_id'] == '');
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		if( $type == 'list'){
			$this->loadModel('Project');
			$listProject = $this->Project->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'project_name'),
				'conditions' => array(
					'company_id is not null',
					'project_name is not null',
					'category' => array( 1,2) // Inprogress, Opportunity
				)
			));
			$this->set(compact('listProject'));
			// debug( count( $listProject));
			// debug( $listProject);
			$this->layout='recycle_bin';
			$this->render();
		}
		$this->loadModel('ProjectBudgetSyn');
		// debug( $_POST); exit;
		$listProjectID = $this->data;
		if( !empty($listProjectID)){ // run
			$result = array();
			foreach( $listProjectID as $project_id){
				$result[$project_id] = $this->ProjectBudgetSyn->updateBudgetSyn($project_id);
			}
			die(json_encode(array(
				'result' => !empty($result) ? 'success' : 'failed',
				'data' => $result,
				'message' => ''
			)));
		}
	}
	function refreshProjectStaffing($type='list', $listProjectID = null){
		set_time_limit(0); 
		ignore_user_abort(true);
		$is_sas = $this->employee_info['Employee']['is_sas'] || ( $this->employee_info['Employee']['company_id'] == '');
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		if( $type == 'list'){
			$this->loadModel('Project');
			$listProject = $this->Project->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'project_name'),
				'conditions' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'project_name is not null',
					'category' => array( 1,2) // Inprogress, Opportunity
				)
			));
			$this->set(compact('listProject'));
			$this->layout='recycle_bin';
			$this->render();
		}
		$this->loadModel('ProjectTask');
		$listProjectID = $this->data;
		if( !empty($listProjectID)){ // run
			$result = array();
			foreach( $listProjectID as $project_id){
				$stf = $this->ProjectTask->staffingSystem($project_id, true);
				$result[$project_id] = $stf;
			
			}
			die(json_encode(array(
				'result' => !empty($result) ? 'success' : 'failed',
				'data' => $result,
				'message' => '',
			)));
		}
	}
	function syncBudgetInternalToBudgetSync(){
		echo 'Function nay khong dung nua. chi giu lai de tham khao <br>';
		echo 'Ticket 526 2020-04-09  <br>';
		echo ' Email: Ticket #526 Apply new design for Synthesis screen. <br>';
		echo '4. do not take into account overload <br>';
		echo 'Thay doi cach tinh inforecastMD<br>';
		exit;
		$this->loadModel('ProjectBudgetSyn');
		$this->loadModel('ProjectBudgetInternalDetail');
		
		$internals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'fields' => array('budget_md', 'average', 'project_id', 'activity_id')
        ));
		$projects = array();
		$listProject = array();
        $budgetInternals = array();
        if(!empty($internals)){
            foreach($internals as $internal){
				// internal_costs_budget
                $dx = $internal['ProjectBudgetInternalDetail'];
                if(empty($dx['budget_md'])){
                    $dx['budget_md'] = 0;
                }
                if(empty($dx['average'])){
                    $dx['average'] = 0;
                }
                if(!isset($budgetInternals[$dx['project_id']]['budgetEuro'])){
                    $budgetInternals[$dx['project_id']]['budgetEuro'] = 0;
                }
                $budgetInternals[$dx['project_id']]['budgetEuro'] += $dx['budget_md']*$dx['average'];
                if(!isset($budgetInternals[$dx['project_id']]['budgetManday'])){
                    $budgetInternals[$dx['project_id']]['budgetManday'] = 0;
                }
                $budgetInternals[$dx['project_id']]['budgetManday'] += $dx['budget_md'];
                if(!isset($budgetInternals[$dx['project_id']]['average'])){
                    $budgetInternals[$dx['project_id']]['average'] = 0;
                }
                $budgetInternals[$dx['project_id']]['average'] += $dx['average'];
                if(!isset($budgetInternals[$dx['project_id']]['countRecord'])){
                    $budgetInternals[$dx['project_id']]['countRecord'] = 0;
                }
                $budgetInternals[$dx['project_id']]['countRecord'] += 1;
                $projects[$dx['project_id']] = $dx['activity_id'];
				$listProject[] = $dx['project_id'];
            }
        }
		$getDataProject = $this->dataFromProjectTask($listProject);
		// Save Project budget Sync Of Project
        if(!empty($projects)){
            $saved = array();
            foreach($projects as $projectId => $activityId){
                if($projectId != 0){
                    $inAverage = !empty($budgetInternals[$projectId]['average']) ? $budgetInternals[$projectId]['average'] : 0;
                    $inBudgetEuro = !empty($budgetInternals[$projectId]['budgetEuro']) ? $budgetInternals[$projectId]['budgetEuro'] : 0;
                    $inBudgetManDay = !empty($budgetInternals[$projectId]['budgetManday']) ? $budgetInternals[$projectId]['budgetManday'] : 0;
                    $inBudgetCountRecord = !empty($budgetInternals[$projectId]['countRecord']) ? $budgetInternals[$projectId]['countRecord'] : 0;
                    $inAverage = ($inBudgetCountRecord == 0) ? 0 : round($inAverage/$inBudgetCountRecord, 2);
                    $inRemainMD = !empty($getDataProject[$projectId]) ? $getDataProject[$projectId]['remain'] : 0;
					$inRemainEuro = $inRemainMD * $inAverage;
                    $inConsumedMD = !empty($getDataProject[$projectId]) ? $getDataProject[$projectId]['consumed'] : 0;
                    $inforecastMD = $inRemainMD + $inConsumedMD;
                    $saved[] = array(
                        'project_id' => $projectId,
                        // 'activity_id' => $activityId,
                        'internal_costs_budget' => $inBudgetEuro,
                        'internal_costs_budget_man_day' => $inBudgetManDay,
                        // 'internal_costs_forecast' => $,
                        // 'internal_costs_engaged' => $,
                        'internal_costs_remain' => $inRemainEuro,
                        // 'internal_costs_engaged_md' => $inConsumedMD,
                        'internal_costs_forecasted_man_day' => $inforecastMD,
                        'internal_costs_average' => $inAverage,
                    );
                }
            }
            if(!empty($saved)){
                // $this->ProjectBudgetSyn->saveAll($saved);
				foreach($saved as $data){
					$this->ProjectBudgetSyn->updateAll($data,
						array(
							'ProjectBudgetSyn.project_id' => $data['project_id']
						)
					);
				}
            }
        }
		echo 'OK.';
		exit;
	}
	 
	/* Huynh 2020-04-09 Edit cua Viet
	* Update data budget sync 
	+ Forecast 
	Theo yêu cầu 
	Ticket #526 Apply new design for Synthesis screen.
	4. do not take into account overload
	*/
	function syncEngagedInternalToBudgetSync(){
		$this->loadModels('ProjectBudgetSyn', 'Profile', 'ProfitCenter', 'ProjectTask');
		 
		$projectBudgetSyn = $this->ProjectBudgetSyn->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'project_id', 'activity_id', 'internal_costs_remain', 'internal_costs_average', 'internal_costs_budget'), 
		));
		$listProjects = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.activity_id', '{n}.ProjectBudgetSyn.project_id') : array(); 
		// debug( $projectBudgetSyn );
		// debug( $listProjects );
		$listActivities = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.project_id', '{n}.ProjectBudgetSyn.activity_id') : array(); 
		$tjm_projects = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.activity_id', '{n}.ProjectBudgetSyn.internal_costs_average') : array(); 
		$projectBudgetSyn = !empty($projectBudgetSyn) ? Set::combine($projectBudgetSyn, '{n}.ProjectBudgetSyn.id', '{n}.ProjectBudgetSyn') : array(); 
		$getDataActivities = $this->_parse($listActivities);
		
		$sumEmployees = $getDataActivities['sumEmployees'];
		// debug( $getDataActivities ); exit;
		$resources = $getDataActivities['employees'];
		$employees = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee') : array();
		
		// TJM of profil
		$profiles = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee.profile_id') : array();
		$profiles_tjm = array();
		if(!empty($profiles)){
			$profiles_tjm =  $this->Profile->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => $profiles),
					'fields' => array('id', 'tjm'), 
				)
			);
			
			$profiles_tjm = !empty($profiles_tjm) ? Set::combine($profiles_tjm, '{n}.Profile.id', '{n}.Profile.tjm') : array();
		}
		
		// TJM of TEAM
		$profit_centers = !empty($resources) ? Set::combine($resources, '{n}.Employee.id', '{n}.Employee.profit_center_id') : array();
		$team_tjm = array();
		if(!empty($profit_centers)){
			$team_tjm =  $this->ProfitCenter->find(
				'all', array(
					'recursive' => -1,
					'conditions' => array('id' => $profit_centers),
					'fields' => array('id', 'tjm'), 
				)
			);
			$team_tjm = !empty($team_tjm) ? Set::combine($team_tjm, '{n}.ProfitCenter.id', '{n}.ProfitCenter.tjm') : array();
		}

		$engagedErro = array();
		foreach ($sumEmployees as $act_id => $actRequests) {
			if(!isset($engagedErro[$listProjects[$act_id]])) $engagedErro[$listProjects[$act_id]] = 0;
			foreach ($actRequests as $emp_id => $val){
				$reals = 1;
				if(!empty($employees[$emp_id]['tjm'])){
					$reals = $employees[$emp_id]['tjm'];
				}else if(!empty($employees[$emp_id]) && !empty($profiles_tjm[$employees[$emp_id]['profile_id']])){
					$reals = $profiles_tjm[$employees[$emp_id]['profile_id']];
					
				}else if(!empty($employees[$emp_id]) && !empty($team_tjm[$employees[$emp_id]['profit_center_id']])){
					$reals = $team_tjm[$employees[$emp_id]['profit_center_id']];
				}else{
					$reals = $tjm_projects[$act_id];
				}
				$engagedErro[$listProjects[$act_id]] += $val * $reals;
			}
		}
		$save = array();
		$totalWorkloadByProject = $this->ProjectTask->find('all', array(
			'fields' => array(
				'project_id',
                'SUM(ProjectTask.estimated) AS Total',
            ),
            'recursive' => -1,
            'conditions' => array(
				'project_id' => array_values($listProjects),
				'special'=> 0
			),
			'group' => array('project_id')
		));
		$totalWorkloadByProject = !empty($totalWorkloadByProject) ? Set::combine($totalWorkloadByProject, '{n}.ProjectTask.project_id', '{n}.0.Total') : array();
		// debug( $totalWorkloadByProject);
		// exit;
		foreach($projectBudgetSyn as $id => $budgetSyn){
			$project_id = $budgetSyn['project_id'];
			$act_id = $budgetSyn['activity_id'];
			$inEngagedErro = !empty($engagedErro[$project_id]) ? $engagedErro[$project_id] : 0;
			$inCostsRemain = !empty($budgetSyn['internal_costs_remain']) ? $budgetSyn['internal_costs_remain'] : 0;
			$inCostsBudget = !empty($budgetSyn['internal_costs_budget']) ? $budgetSyn['internal_costs_budget'] : 0;
			$inCostsForecast = $inEngagedErro + $inCostsRemain;
			$tjm = $tjm_projects[$act_id];
			$inCostsVar = ($inCostsBudget != 0) ? ($inCostsForecast / $inCostsBudget - 1) * 100 : 0;
			
			$this->ProjectBudgetSyn->updateAll(
				array(
					'internal_costs_forecasted_man_day' => !empty($totalWorkloadByProject[$project_id]) ? $totalWorkloadByProject[$project_id] : 0,
					'internal_costs_forecast' => $inCostsForecast ,
					'internal_costs_engaged' => $inEngagedErro,
					'internal_costs_var' => round($inCostsVar, 2),
				),
				array(
					'ProjectBudgetSyn.id' => $id,
				)
			);
		}
		echo 'OK';
		exit;
	}
	 /**
     * Index
     *
     * @return void
     * @access public
     */
     protected function _parse($activity_id) {
        
        $this->loadModel('ActivityRequest');
        $employees = $sumEmployees = $sumActivities = array();
        $datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'activity_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'activity_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => $activity_id,
                // 'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $this->loadModel('ActivityTask');
        $activityTasks = $this->ActivityTask->find('all', array(
            'recursive' => -1,
            'conditions' => array('activity_id' => $activity_id),
            'fields' => array('id', 'activity_id')
        ));
        $groupTaskId = !empty($activityTasks) ? Set::classicExtract($activityTasks, '{n}.ActivityTask.id') : array();
        $_datas = $this->ActivityRequest->find(
            'all',
            array(
                'recursive' => -1,
                'fields' => array(
                    'id',
                    'employee_id',
                    'task_id',
                    'SUM(value) as value'
                ),
            'group' => array('employee_id', 'task_id'),
            'conditions' => array(
                'status' => 2,
                'activity_id' => 0,
                'task_id' => $groupTaskId,
                // 'company_id' => $employeeName['company_id'],
                'NOT' => array('value' => 0))
            )
        );
        $_sumActivitys = $_sumEmployees = array();
        foreach($_datas as $_data){
            foreach($activityTasks as $activityTask){
                if($_data['ActivityRequest']['task_id'] == $activityTask['ActivityTask']['id']){
                    $_sumActivitys[$activityTask['ActivityTask']['activity_id']][] = $_data[0]['value'];
                }
            }
            if (!isset($_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']])) {
                $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] = 0;
            }
            $_sumEmployees[$_data['ActivityRequest']['task_id']][$_data['ActivityRequest']['employee_id']] += $_data[0]['value'];
        }
        $dataFromEmployees = array();
        foreach($activityTasks as $activityTask){
			$task_id = $activityTask['ActivityTask']['id'];
			$taskConsumed = !empty($_sumEmployees[$task_id]) ? $_sumEmployees[$task_id] : array();
			if(!empty($taskConsumed)) $dataFromEmployees[$activityTask['ActivityTask']['activity_id']][] = $taskConsumed;
        }
		
        $rDatas = array();
        if(!empty($dataFromEmployees)){
            foreach($dataFromEmployees as $id => $dataFromEmployee){
                foreach($dataFromEmployee as $values){
                    foreach($values as $employ => $value){
                        if(!isset($rDatas[$id][$employ])){
                            $rDatas[$id][$employ] = 0;
                        }
                        $rDatas[$id][$employ] += $value;
                    }
                }
            }
        }
        foreach($_sumActivitys as $k => $_sumActivity){
            $_sumActivitys[$k] = array_sum($_sumActivitys[$k]);
        }
        foreach ($datas as $data) {
            $dx = $data['ActivityRequest'];
            $data = $data['0']['value'];
            if (!isset($sumActivities[$dx['activity_id']])) {
                $sumActivities[$dx['activity_id']] = 0;
            }
            $sumActivities[$dx['activity_id']] += $data;
            if (!isset($sumEmployees[$dx['activity_id']][$dx['employee_id']])) {
                $sumEmployees[$dx['activity_id']][$dx['employee_id']] = 0;
            }
            $sumEmployees[$dx['activity_id']][$dx['employee_id']] += $data;
            $employees[$dx['employee_id']] = $dx['employee_id'];
        }
        $_dataFromEmployees = array();
        if(!empty($rDatas)){
            foreach($rDatas as $id => $rData){
                if(in_array($id, array_keys($sumEmployees))){

                } else {
                    $sumEmployees[$id] = $rData;
                    unset($rDatas[$id]);
                }
            }
        }
        $sumEmployGroups = array();
        if(!empty($sumEmployees)){
            unset($sumEmployees[0]);
            $sumEmployGroups[0] = $sumEmployees;
        }
        if(!empty($rDatas)){
            $sumEmployGroups[1] = $rDatas;
        }
        $sumEmployees = array();
		$listEmployee = array();
        if(!empty($sumEmployGroups)){
            foreach($sumEmployGroups as $key => $sumEmployGroup){
                foreach($sumEmployGroup as $acId => $values){
                    foreach($values as $employs => $value){
                        if(!isset($sumEmployees[$acId][$employs])){
                            $sumEmployees[$acId][$employs] = 0;
							$listEmployee[] =  $employs;
                        }
                        $sumEmployees[$acId][$employs] += $value;
                    }
                }
            }
        }
		$employees = $this->ActivityRequest->Employee->find(
			'all', array(
				'recursive' => -1,
				'conditions' => array('id' => $listEmployee),
				'fields' => array('id', 'tjm', 'profile_id', 'profit_center_id'), 
			)
		);
		
        $setDatas = array();
        $setDatas['sumEmployees'] = !empty($sumEmployees) ? $sumEmployees : array();
        $setDatas['employees'] = !empty($employees) ? $employees : array();

        return $setDatas;
    }
     /**
      * Update table budget synthesis
      */
	  /*
     public function updateProjectBudgetSyn(){
        $this->loadModel('ProjectBudgetSyn');
        $this->loadModel('ProjectBudgetInternalDetail');
        $this->loadModel('ProjectBudgetExternal');
        $this->loadModel('ProjectBudgetSale');
        $this->loadModel('ProjectBudgetInvoice');
        $this->ProjectBudgetSyn->deleteAll(array('ProjectBudgetSyn.id >=' => 0), false);
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'ALTER TABLE project_budget_syns AUTO_INCREMENT = 1';
        $db->query($setAuto);
        // Internal costs
        $internals = $this->ProjectBudgetInternalDetail->find('all', array(
            'recursive' => -1,
            'fields' => array('budget_md', 'average', 'project_id', 'activity_id')
        ));
        $projects = array();
        $budgetInternals = array();
        if(!empty($internals)){
            foreach($internals as $internal){
				// internal_costs_budget
                $dx = $internal['ProjectBudgetInternalDetail'];
                if(empty($dx['budget_md'])){
                    $dx['budget_md'] = 0;
                }
                if(empty($dx['average'])){
                    $dx['average'] = 0;
                }
                if(!isset($budgetInternals[$dx['project_id']]['budgetEuro'])){
                    $budgetInternals[$dx['project_id']]['budgetEuro'] = 0;
                }
                $budgetInternals[$dx['project_id']]['budgetEuro'] += $dx['budget_md']*$dx['average'];
                if(!isset($budgetInternals[$dx['project_id']]['budgetManday'])){
                    $budgetInternals[$dx['project_id']]['budgetManday'] = 0;
                }
                $budgetInternals[$dx['project_id']]['budgetManday'] += $dx['budget_md'];
                if(!isset($budgetInternals[$dx['project_id']]['average'])){
                    $budgetInternals[$dx['project_id']]['average'] = 0;
                }
                $budgetInternals[$dx['project_id']]['average'] += $dx['average'];
                if(!isset($budgetInternals[$dx['project_id']]['countRecord'])){
                    $budgetInternals[$dx['project_id']]['countRecord'] = 0;
                }
                $budgetInternals[$dx['project_id']]['countRecord'] += 1;
                $projects[$dx['project_id']] = $dx['activity_id'];
            }
        }
		// debug($budgetInternals);
		// exit;
        // External Cost
        $externals = $this->ProjectBudgetExternal->find('all', array(
                'recursive' => -1,
                'fields' => array('budget_erro', 'ordered_erro', 'remain_erro', 'man_day', 'progress_md', 'project_id', 'activity_id')
            ));
        $budgetExternals = array();
        if(!empty($externals)){
            foreach($externals as $external){
                $dx = $external['ProjectBudgetExternal'];
                $dx['budget_erro'] = !empty($dx['budget_erro']) ? $dx['budget_erro'] : 0;
                $dx['ordered_erro'] = !empty($dx['ordered_erro']) ? $dx['ordered_erro'] : 0;
                $dx['remain_erro'] = !empty($dx['remain_erro']) ? $dx['remain_erro'] : 0;
                $dx['man_day'] = !empty($dx['man_day']) ? $dx['man_day'] : 0;
                $dx['progress_md'] = !empty($dx['progress_md']) ? $dx['progress_md'] : 0;
                if(!isset($budgetExternals[$dx['project_id']]['budgetEuro'])){
                    $budgetExternals[$dx['project_id']]['budgetEuro'] = 0;
                }
                $budgetExternals[$dx['project_id']]['budgetEuro'] += $dx['budget_erro'];
                if(!isset($budgetExternals[$dx['project_id']]['forecastEuro'])){
                    $budgetExternals[$dx['project_id']]['forecastEuro'] = 0;
                }
                $budgetExternals[$dx['project_id']]['forecastEuro'] += $dx['ordered_erro'] + $dx['remain_erro'];
                if(!isset($budgetExternals[$dx['project_id']]['orderedEuro'])){
                    $budgetExternals[$dx['project_id']]['orderedEuro'] = 0;
                }
                $budgetExternals[$dx['project_id']]['orderedEuro'] += $dx['ordered_erro'];
                if(!isset($budgetExternals[$dx['project_id']]['remainEuro'])){
                    $budgetExternals[$dx['project_id']]['remainEuro'] = 0;
                }
                $budgetExternals[$dx['project_id']]['remainEuro'] += $dx['remain_erro'];
                if(!isset($budgetExternals[$dx['project_id']]['manDay'])){
                    $budgetExternals[$dx['project_id']]['manDay'] = 0;
                }
                $budgetExternals[$dx['project_id']]['manDay'] += $dx['man_day'];
                if(!isset($budgetExternals[$dx['project_id']]['progressEuro'])){
                    $budgetExternals[$dx['project_id']]['progressEuro'] = 0;
                }
                $budgetExternals[$dx['project_id']]['progressEuro'] += round(($dx['ordered_erro']*$dx['progress_md'])/100, 2);
                $projects[$dx['project_id']] = $dx['activity_id'];
            }
        }
        // Sales
        $sales = $this->ProjectBudgetSale->find('all', array(
                'recursive' => -1,
                'fields' => array('sold', 'man_day', 'project_id', 'activity_id')
            ));
        $budgetSales = array();
        if(!empty($sales)){
            foreach($sales as $sale){
                $dx = $sale['ProjectBudgetSale'];
                if(!isset($budgetSales[$dx['project_id']]['soldEuro'])){
                    $budgetSales[$dx['project_id']]['soldEuro'] = 0;
                }
                $budgetSales[$dx['project_id']]['soldEuro'] += !empty($dx['sold']) ? $dx['sold'] : 0;
                if(!isset($budgetSales[$dx['project_id']]['manDay'])){
                    $budgetSales[$dx['project_id']]['manDay'] = 0;
                }
                $budgetSales[$dx['project_id']]['manDay'] += !empty($dx['man_day']) ? $dx['man_day'] : 0;
                $projects[$dx['project_id']] = $dx['activity_id'];
            }
        }
        // Invoice
        $invoices = $this->ProjectBudgetInvoice->find('all', array(
                'recursive' => -1,
                'fields' => array('billed', 'paid', 'effective_date', 'project_id', 'activity_id')
            ));
        if(!empty($invoices)){
            foreach($invoices as $invoice){
                $dx = $invoice['ProjectBudgetInvoice'];
                $dx['billed'] = !empty($dx['billed']) ? $dx['billed'] : 0;
                $dx['paid'] = !empty($dx['paid']) ? $dx['paid'] : 0;
                if(!isset($budgetSales[$dx['project_id']]['toBillEuro'])){
                    $budgetSales[$dx['project_id']]['toBillEuro'] = 0;
                }
                $budgetSales[$dx['project_id']]['toBillEuro'] += $dx['billed'];
                if(!isset($budgetSales[$dx['project_id']]['paidEuro'])){
                    $budgetSales[$dx['project_id']]['paidEuro'] = 0;
                }
                $budgetSales[$dx['project_id']]['paidEuro'] += $dx['paid'];
                if(!empty($dx['effective_date']) && $dx['effective_date'] != '0000-00-00'){
                    if(!isset($budgetSales[$dx['project_id']]['billedEuro'])){
                        $budgetSales[$dx['project_id']]['billedEuro'] = 0;
                    }
                    $budgetSales[$dx['project_id']]['billedEuro'] += $dx['billed'];
                }
                $projects[$dx['project_id']] = $dx['activity_id'];
            }
        }
        // Save Project budget Sync Of Project
        if(!empty($projects)){
            $saved = array();
            foreach($projects as $projectId => $activityId){
                if($projectId != 0){
                    $inAverage = !empty($budgetInternals[$projectId]['average']) ? $budgetInternals[$projectId]['average'] : 0;
                    $inBudgetEuro = !empty($budgetInternals[$projectId]['budgetEuro']) ? $budgetInternals[$projectId]['budgetEuro'] : 0;
                    $inBudgetManDay = !empty($budgetInternals[$projectId]['budgetManday']) ? $budgetInternals[$projectId]['budgetManday'] : 0;
                    $inBudgetCountRecord = !empty($budgetInternals[$projectId]['countRecord']) ? $budgetInternals[$projectId]['countRecord'] : 0;
                    $inAverage = ($inBudgetCountRecord == 0) ? 0 : round($inAverage/$inBudgetCountRecord, 2);
                    $exBudgetEuro = !empty($budgetExternals[$projectId]['budgetEuro']) ? $budgetExternals[$projectId]['budgetEuro'] : 0;
                    $exForecastEuro = !empty($budgetExternals[$projectId]['forecastEuro']) ? $budgetExternals[$projectId]['forecastEuro'] : 0;
                    $exOrderEuro = !empty($budgetExternals[$projectId]['orderedEuro']) ? $budgetExternals[$projectId]['orderedEuro'] : 0;
                    $exRemainEuro = !empty($budgetExternals[$projectId]['remainEuro']) ? $budgetExternals[$projectId]['remainEuro'] : 0;
                    $exVarEuro = ($exBudgetEuro == 0) ? -100 : round(((($exOrderEuro + $exRemainEuro)/$exBudgetEuro)-1)*100, 2);
                    $exManDay = !empty($budgetExternals[$projectId]['manDay']) ? $budgetExternals[$projectId]['manDay'] : 0;
                    $exProgressEuro = !empty($budgetExternals[$projectId]['progressEuro']) ? $budgetExternals[$projectId]['progressEuro'] : 0;
                    $exProgressManDay = ($exOrderEuro == 0) ? 0 : round(($exProgressEuro/$exOrderEuro)*100, 2);
                    $saleSoldEuro = !empty($budgetSales[$projectId]['soldEuro']) ? $budgetSales[$projectId]['soldEuro'] : 0;
                    $saleManDay = !empty($budgetSales[$projectId]['manDay']) ? $budgetSales[$projectId]['manDay'] : 0;
                    $saleToBillEuro = !empty($budgetSales[$projectId]['toBillEuro']) ? $budgetSales[$projectId]['toBillEuro'] : 0;
                    $saleBilledEuro = !empty($budgetSales[$projectId]['billedEuro']) ? $budgetSales[$projectId]['billedEuro'] : 0;
                    $salePaidEuro = !empty($budgetSales[$projectId]['paidEuro']) ? $budgetSales[$projectId]['paidEuro'] : 0;
                    $saved[] = array(
                        'project_id' => $projectId,
                        'activity_id' => $activityId,
                        'internal_costs_average' => $inAverage,
                        'internal_costs_budget' => $inBudgetEuro,
                        'internal_costs_budget_man_day' => $inBudgetManDay,
                        'external_costs_budget' => $exBudgetEuro,
                        'external_costs_forecast' => $exForecastEuro,
                        'external_costs_var' => $exVarEuro,
                        'external_costs_ordered' => $exOrderEuro,
                        'external_costs_remain' => $exRemainEuro,
                        'external_costs_man_day' => $exManDay,
                        'external_costs_progress' => $exProgressManDay,
                        'external_costs_progress_euro' => $exProgressEuro,
                        'sales_sold' => $saleSoldEuro,
                        'sales_man_day' => $saleManDay,
                        'sales_to_bill' => $saleToBillEuro,
                        'sales_billed' => $saleBilledEuro,
                        'sales_paid' => $salePaidEuro
                    );
                }
            }
            if(!empty($saved)){
                $this->ProjectBudgetSyn->saveAll($saved);
            }
        }
        echo 'OK.';
        exit;
     }
	 
	 */
     public function deleteRecordsNotAssignAndNotConsumedInTmpStaffingSystem(){
        $this->loadModel('TmpStaffingSystem');
        $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.estimated'=>0,'TmpStaffingSystem.consumed'=>0), false);
        echo 'Completed';
        exit;
    }
     /**
      * Xoa cac du lieu cua project va activity trong tmp staffing system khi project/activity do khong ton tai trong he thong
      */
     public function deleteAllProjectAndActivityNotUsingInTmpStaffingSystem(){
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $this->loadModel('Activity');
        $this->loadModel('TmpStaffingSystem');
        $infors = $this->Session->read('Auth.employee_info');
        /**
         * Lay cac project cua cong ty dang login
         */
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $infors['Company']['id'])
        ));
        /**
         * Lay cac project cua cac cong ty khac
         */
        $projectNotOfCompanies = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array(
                    'company_id' => $infors['Company']['id']
                )
            )
        ));
        $projects = !empty($projectNotOfCompanies) ? array_merge($projects, $projectNotOfCompanies) : $projects;
        /**
         * Lay cac task thuoc project da bi xoa cua cong ty dang login va khong thuoc lay cac task thuoc cong ty khac
         */
        $projectTasks = $this->ProjectTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('project_id' => $projects)
            ),
            'fields' => array('id', 'id')
        ));
        /**
         * Xoa cac assign task cua project da bi xoa cua cong ty dang login
         */
        if(!empty($projectTasks)){
            $this->loadModel('ProjectTaskEmployeeRefer');
            $this->ProjectTaskEmployeeRefer->deleteAll(array('ProjectTaskEmployeeRefer.project_task_id' => $projectTasks), false);
            $this->ActivityTask->deleteAll(array('ActivityTask.project_task_id' => $projectTasks), false);
        }
        /**
         * Xoa cac task va tmp cua project da bi xoa cua cong ty dang login
         */
        $this->ProjectTask->deleteAll(array('NOT' => array('ProjectTask.project_id' => $projects)), false);
        $projects[0] = 0;
        $this->TmpStaffingSystem->deleteAll(array('NOT' => array('TmpStaffingSystem.project_id' => $projects)), false);
        /**
         * Lay cac activity cua cong ty dang login
         */
        $activities = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $infors['Company']['id']),
            'fields' => array('id', 'id')
        ));
        /**
         * Lay cac activity cua cac cong ty khac
         */
        $activityNotOfCompanies = $this->Activity->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('company_id' => $infors['Company']['id'])
            ),
            'fields' => array('id', 'id')
        ));
        $activities = !empty($activityNotOfCompanies) ? array_merge($activities, $activityNotOfCompanies) : $activities;
        /**
         * Lay cac task thuoc activity da bi xoa cua cong ty dang login va khong thuoc lay cac task thuoc cong ty khac
         */
        $activityTasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('activity_id' => $activities)
            ),
            'fields' => array('id', 'id')
        ));
        /**
         * Xoa cac assign task cua project da bi xoa cua cong ty dang login
         */
        if(!empty($activityTasks)){
            $this->loadModel('ActivityTaskEmployeeRefer');
            $this->ActivityTaskEmployeeRefer->deleteAll(array('ActivityTaskEmployeeRefer.activity_task_id' => $activityTasks), false);
        }
        /**
         * Xoa cac task va tmp cua project da bi xoa cua cong ty dang login
         */
        $this->ActivityTask->deleteAll(array('NOT' => array('ActivityTask.activity_id' => $activities)), false);
        $activities[0] = 0;
        $this->TmpStaffingSystem->deleteAll(array('NOT' => array('TmpStaffingSystem.activity_id' => $activities)), false);
        echo 'OK';
        exit;
     }
     /**
      * Kiem tra cac task da validate trong activity request,
      * Nhung khong ton tai ben activity task
      * Bug Nguy hiem
      */
     public function checkTaskUsingInActivityRequestAndNotExistActivityTask(){
        $this->loadModel('ActivityTask');
        $this->loadModel('ActivityRequest');
        $tasks = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'id')
        ));
        $requests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'status' => 2, 'activity_id' => 0,
                'NOT' => array('task_id' => $tasks)
            ),
            'fields' => array('task_id', 'task_id')
        ));
        if(!empty($requests)){
            foreach($requests as $taskId){
                echo 'Activity Task Id: ' . $taskId . '<br />';
            }
            echo 'TotalL : ' . count($requests);
        }
        exit;
     }
     /**
      * Doi status = null thanh status = -1 trong table activity request
      */
     public function changeStatusInActivityRequest(){
        $this->loadModel('ActivityRequest');
        $requests = $this->ActivityRequest->find('list', array(
            'recursive' => -1,
            'conditions' => array('status' => null),
            'fields' => array('id', 'id')
        ));
        if(!empty($requests)){
            $this->ActivityRequest->updateAll(array('status' => -1), array(
                'ActivityRequest.id' => $requests
            ));
        }
        echo 'Finish!';
        exit;
     }
     /**
      * Tong so ngay xin nghi absence cua 1 employee trong 1 thang
      * Luu vao table TmpCaculateAbsence
      */
     public function caculateRequestOfEmployeeControler(){
        $this->loadModel('AbsenceRequest');
        $this->loadModel('TmpCaculateAbsence');
        $checkDatas = $this->TmpCaculateAbsence->find('count');
        if($checkDatas == 0){
            $infors = $this->Session->read('Auth.employee_info');
            $requestQuery = $this->AbsenceRequest->find(
                "all",
                array(
                    'recursive' 	=> -1,
                    'fields' => array('employee_id', 'date', 'absence_am', 'absence_pm', 'response_am', 'response_pm')
                )
            );
            $totalAbcenses = array();
            foreach($requestQuery as $request){
                foreach(array('am', 'pm') as $value){
                    if($request['AbsenceRequest']['absence_' . $value] && $request['AbsenceRequest']['response_' . $value] == 'validated'){
                        $_dates = date('m-Y', $request['AbsenceRequest']['date']);
                        $dates = strtotime('01-'.$_dates);
                        if(!isset($totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates])){
                            $totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates] = 0;
                        }
                        $totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates] += 0.5;
                    }
                }
            }
            $references = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id')
            ));
            $datas = $idProfits = array();
            if(!empty($totalAbcenses)){
                foreach($totalAbcenses as $employ => $totalAbcense){
                    foreach($totalAbcense as $time => $values){
                        $_datas = array(
                            'profit_center_id' => !empty($references[$employ]) ? $references[$employ] : 0,
                            'employee_id' => $employ,
                            'date' => $time,
                            'total_absence' => $values,
                            'company_id' => $infors['Company']['id']
                        );
                        $datas[] = $_datas;
                    }
                }
            }
            if(!empty($datas)){
                $this->TmpCaculateAbsence->saveAll($datas);
            }
            echo 'Okie';
        } else {
            echo 'Already Have The Data!';
        }
        exit;
    }
    /**
     * Xoa cache trong table TmpCaculateAbsence
     */
    public function clearCaheTmpCaculateAbsence(){
        $this->loadModel('TmpCaculateAbsence');
        $this->TmpCaculateAbsence->deleteAll(array('TmpCaculateAbsence.id >' => 0), false);
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'ALTER TABLE tmp_caculate_absences AUTO_INCREMENT = 1';
        $db->query($setAuto);
        echo 'Okie';
        exit;
    }
    /**
     * Check activity/project co workload be hon 0?
     */
    public function checkWorkloadNegativeNumberInTmpStaffing(){
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTask');
        $staffings = $this->TmpStaffingSystem->find('all', array(
            'recursive' => -1,
            'conditions' => array('estimated <' => 0),
            'fields' => array('project_id', 'activity_id')
        ));
        $activities = $projects = array();
        if(!empty($staffings)){
            foreach($staffings as $staffing){
                $dx = $staffing['TmpStaffingSystem'];
                if($dx['project_id'] == 0){
                    $activities[$dx['activity_id']] = $dx['activity_id'];
                } else {
                    $projects[$dx['project_id']] = $dx['activity_id'];
                }
            }
        }
        if(!empty($activities)){
            echo 'Activity: <br />';
            foreach($activities as $id){
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.activity_id' => $id), false);
                $this->ActivityTask->staffingSystem($id);
                echo '------------ ' . $id . '<br />';
            }
        }
        if(!empty($projects)){
            echo 'Project: <br />';
            foreach($projects as $id => $val){
                $this->TmpStaffingSystem->deleteAll(array('TmpStaffingSystem.project_id' => $id), false);
                $this->ProjectTask->staffingSystem($id);
                echo '------------ ' . $id . '<br />';
            }
        }
        if(empty($activities) && empty($projects)) {
            echo 'Okie';
        }
        exit;
    }
    /**
     * xoa cache table TmpCaculateProfitCenter
     */
    public function clearCaheTmpCaculateProfitCenter(){
        $this->loadModel('TmpCaculateProfitCenter');
        $this->TmpCaculateProfitCenter->deleteAll(array('TmpCaculateProfitCenter.id >' => 0), false);
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'ALTER TABLE tmp_caculate_profit_centers AUTO_INCREMENT = 1';
        $db->query($setAuto);
        echo 'Okie';
        exit;
    }
    /**
     * ham dung de tinh toan tong so employee cua 1 profit center
     */
    public function caculateProfitCenter($profitCenters = null){
        $references = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id')
            ));
        $employees = array();
        if(!empty($references)){
            foreach($references as $employ => $profit){
                if(!isset($employees[$profit])){
                    //do nothing
                }
                $employees[$profit][] = $employ;
            }
        }
        $TmpCaculateProfitCenter = ClassRegistry::init('TmpCaculateProfitCenter');
        $company = $this->employee_info['Company']['id'];
        if(!empty($employees)){
            foreach($employees as $profitId => $employee){
                $tmp = $TmpCaculateProfitCenter->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('profit_center_id' => $profitId),
                    'fields' => array('id')
                ));
                if(!empty($tmp) && $tmp['TmpCaculateProfitCenter']['id']){
                    $TmpCaculateProfitCenter->id = $tmp['TmpCaculateProfitCenter']['id'];
                    $saved['total_employee'] = count($employee);
                    $TmpCaculateProfitCenter->save($saved);
                } else {
                    $saved['profit_center_id'] = $profitId;
                    $saved['total_employee'] = count($employee);
                    $saved['company_id'] = $company;
                    $TmpCaculateProfitCenter->create();
                    $TmpCaculateProfitCenter->save($saved);
                }
            }
        }
        echo 'Okie';
        exit;
    }
    /*lam moi table tmp_staffing_systems*/
    public function truncateTableTmpStaffingSystem(){
        echo 'Completed!';
        exit;
        $db = ConnectionManager::getDataSource('default');
        $setAuto = 'TRUNCATE TABLE `tmp_staffing_systems`';
        $db->query($setAuto);
        echo 'Completed!';
        exit;
    }
    public function checkTaskCreateAutomatic(){
        $db = ConnectionManager::getDataSource('default');
        $setForProject = 'ALTER TABLE `project_tasks` ADD `special` INT( 1 ) NOT NULL DEFAULT 0 AFTER `weight`';
        $setForAtivity = 'ALTER TABLE `activity_tasks` ADD `special` INT( 1 ) NOT NULL DEFAULT 0 AFTER `overload`';
        $db->query($setForProject);
        $db->query($setForAtivity);
        echo 'Completed!';
        exit;
    }
    public function syncEmployeeCompanyProfitCenter(){
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('CompanyEmployeeReference');
        $this->loadModel('Company');
        $listCompany=$this->Company->find('list',array(
            'recurisve' => -1,
            'fields' => array('id')
        ));
        foreach($listCompany as $val)
        {
            $checkDefaultPC=$this->ProfitCenter->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProfitCenter.company_id'=>$val,'ProfitCenter.name'=>'DEFAULT'
                )
            ));
            if(!$checkDefaultPC)
            {
                $defaultPC = array(
                    'ProfitCenter' => array(
                                'name' => 'DEFAULT',
                                'company_id' => $val
                        )
                );
                $this->ProfitCenter->create();
                $this->ProfitCenter->save($defaultPC);
            }
            $PCDefaultOfCompany[$val]=$this->ProfitCenter->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ProfitCenter.company_id'=>$val,'ProfitCenter.name'=>'DEFAULT'
                ),
                'fields'=>array('company_id','id')
            ));
        }
        //debug($PCDefaultOfCompany); exit;
        $PCDefaultOfCompany=Set::combine($PCDefaultOfCompany,'{n}.ProfitCenter.company_id','{n}.ProfitCenter.id');
        //debug($PCDefaultOfCompany); exit;
        $_fields=array(
            'Employee.id',
            'Employee.is_sas',
            'ERP.profit_center_id',
            'ERP.id',
            'PC.company_id',
            'CRE.company_id',
            'CRE.role_id',
        );
        $_joins = array(
            array(
                'table' => 'project_employee_profit_function_refers',
                'alias' => 'ERP',
                'type' => 'LEFT',
                'foreignKey' => 'employee_id',
                'conditions'=> array(
                    'Employee.id = ERP.employee_id'
                )
            ),
            array(
                'table' => 'profit_centers',
                'alias' => 'PC',
                'type' => 'LEFT',
                'foreignKey' => 'profit_center_id',
                'conditions'=> array(
                    'PC.id = ERP.profit_center_id'
                )
            ),
            array(
                'table' => 'company_employee_references',
                'alias' => 'CRE',
                'type' => 'LEFT',
                'foreignKey' => 'employee_id',
                'conditions'=> array(
                    'CRE.employee_id = Employee.id'
                )
            )
        );
        $listEmployee=$this->Employee->find('all', array(
                'recursive' => -1,
                'joins'=>$_joins,
                'fields'=>$_fields,
                'conditions'=> array(
                    'Employee.is_sas <>' => 1
                )
            ));
        foreach($listEmployee as $_index=>$_data)
        {
            if($_data['PC']['company_id']!=$_data['CRE']['company_id'])
            {
                if($_data['ERP']['profit_center_id']!='')
                {
                    $dataEdit = array(
                        'ProjectEmployeeProfitFunctionRefer' => array(
                                'profit_center_id' => $PCDefaultOfCompany[$_data['CRE']['company_id']]
                            )
                    );
                    $this->ProjectEmployeeProfitFunctionRefer->id=$_data['ERP']['id'];
                    $this->ProjectEmployeeProfitFunctionRefer->save($dataEdit);
                }
                else
                {
                    $dataEdit = array(
                        'ProjectEmployeeProfitFunctionRefer' => array(
                                'employee_id'=>$_data['Employee']['id'],
                                'profit_center_id' => $PCDefaultOfCompany[$_data['CRE']['company_id']]
                            )
                    );
                    $this->ProjectEmployeeProfitFunctionRefer->create();
                    $this->ProjectEmployeeProfitFunctionRefer->save($dataEdit);
                }
            }
        }
        echo 'Finish!!!';
        exit;
    }
    function syncEmployeeActivityProfitCenterRefers()
    {
        $this->loadModel('ActivityProfitRefer');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $datas = $this->ActivityProfitRefer->find('all',array(
            'order'=>array('activity_id')
        ));
        $pcForActivity=array();
        foreach($datas as $data)
        {
            $key=$data['ActivityProfitRefer']['activity_id'];
            $PC=$data['ActivityProfitRefer']['profit_center_id'];
            if(!isset($pcForActivity[$key]))
            {
                $pcForActivity[$key]=array();
            }
            $pcForActivity[$key][$PC]=$PC;
        }
        //debug($pcForActivity);
        $_fields=array(
            'Activity.activity_id',
            'ERP.profit_center_id'
        );
        $_joins = array(
            array(
                'table' => 'activity_tasks',
                'alias' => 'Activity',
                'type' => 'LEFT',
                'foreignKey' => 'id',
                'conditions'=> array(
                    'ActivityTaskEmployeeRefer.activity_task_id = Activity.id',
                )
            ),
            array(
                'table' => 'project_employee_profit_function_refers',
                'alias' => 'ERP',
                'type' => 'RIGHT',
                'foreignKey' => 'employee_id',
                'conditions'=> array(
                    'ActivityTaskEmployeeRefer.reference_id = ERP.employee_id'
                )
            ),
        );
        $datas1 = $this->ActivityTaskEmployeeRefer->find('all',array(
            'conditions'=>array(
                'ActivityTaskEmployeeRefer.is_profit_center'=>0
            ),
            'fields' => $_fields,
            'joins' => $_joins,
            'order' => 'Activity.activity_id'
        ));
        $pcForActivity1=array();
        foreach($datas1 as $val)
        {
            $key=$val['Activity']['activity_id'];
            $PC=$val['ERP']['profit_center_id'];
            if(!isset($pcForActivity1[$key]))
            {
                $pcForActivity1[$key]=array();
            }
            $pcForActivity1[$key][$PC]=$PC;
        }
        $save=array();
        foreach($pcForActivity1 as $_key=>$val)
        {
            if($_key!='')
            {
                foreach($val as $_val)
                {
                    if(!isset($pcForActivity[$_key][$_val]))
                    {
                        //echo $_key; echo '-'; echo $_val; echo "<br />";
                        //$this->ActivityProfitRefer->create();
                        $save[] = array('activity_id'=>$_key,'profit_center_id'=>$_val,'type'=>0);
                        //$this->ActivityProfitRefer->save($save);
                    }
                }
            }
        }
        $this->ActivityProfitRefer->saveAll($save);
        echo 'Completed!';
        exit;
    }
    function deleteTasksInvalid()
    {
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        //DELETE ACTIVITY TASK
        $this->ActivityTask->deleteAll(
            array('ActivityTask.name is null'), false
        );
        //DELETE PROJECT TASK
        $this->ProjectTask->deleteAll(
            array('ProjectTask.task_title is null'), false
        );
        //DELETE ASSIGN TASK
        $this->ActivityTaskEmployeeRefer->deleteAll(
            array('ActivityTaskEmployeeRefer.activity_task_id is null'), false
        );
        $this->ProjectTaskEmployeeRefer->deleteAll(
            array('ProjectTaskEmployeeRefer.project_task_id is null'), false
        );
        echo 'Completed!';
        exit;
    }
    function resetTaskAssigned(){
        //Ham nay dung de format du lieu khac phuc bug : PC da duoc assign task nhung bi xoa ma khong validate.
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProfitCenter');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityTask');
        $this->loadModel('ProjectTask');
        $company = $this->employee_info['Company']['id'];
        //GET ALL PC
        $listPc = $this->ProfitCenter->find('list',array(
            'recursive' => -1,
            'conditions' => array('company_id'=>$company),
            'fields' => array('id')
        ));
        //CONDISTIONS GENERAL
        $joins = array(
            array(
                'table' => 'project_employee_profit_function_refers',
                'alias' => 'ERP',
                'type' => 'LEFT',
                'foreignKey' => 'employee_id',
                'conditions'=> array(
                    'ActivityRequest.employee_id = ERP.employee_id'
                )
            )
        );
        $fields=array(
            'ActivityRequest.task_id',
            'ERP.profit_center_id',
        );
        //GET PC ASSIGNED TO ACTIVITY TASK
        $listPcAssign = $this->ActivityTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 1),
            'fields' => array('DISTINCT reference_id')
        ));
        $listPcAssign = Set::classicExtract($listPcAssign,'{n}.ActivityTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO ACTIVITY TASK, BUT DELETED THIS
        $listPcDeleted = array_diff($listPcAssign,$listPc);
        //GET ALL ACTIVITY TASK REFER PC DELETED
        $_joins = array(
            array(
                'table' => 'profit_centers',
                'alias' => 'Activity',
                'type' => 'LEFT',
                'foreignKey' => 'id',
                'conditions'=> array(
                    'ActivityTaskEmployeeRefer.activity_task_id = Activity.id',
                ),
            )
        );
        $_fields=array(
            'ActivityTaskEmployeeRefer.activity_task_id',
            'ActivityTaskEmployeeRefer.reference_id'
        );
        $activityTaskRefers = $this->ActivityTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('ActivityTaskEmployeeRefer.is_profit_center' => 1, 'ActivityTaskEmployeeRefer.reference_id' => $listPcDeleted),
            'joins' => $_joins,
            'fields' => $_fields
        ));
        $activityTaskIDs = Set::combine($activityTaskRefers,'{n}.ActivityTaskEmployeeRefer.activity_task_id','{n}.ActivityTaskEmployeeRefer.reference_id');
        foreach($activityTaskIDs as $val => $key )
        {
            /*$activityTasks = $this->ActivityTask->find('all',array(
                //'recursive' => -1,
                'conditions' => array('ActivityTask.id' => $val),
                //'fields' => array('DISTINCT reference_id')
            ));*/
            $pcNeedReAssign = $this->ActivityRequest->find('all',array(
                //'recursive' => -1,
                'conditions' => array('ActivityRequest.task_id' => $val, 'ActivityRequest.status' => 2),
                'joins' => $joins,
                'fields' => $fields,
                'order' => array('ActivityRequest.task_id'),
                'limit' => 1
            ));
            if(empty($pcNeedReAssignProject))
            {
                $this->ActivityTaskEmployeeRefer->deleteAll(
                    array('ActivityTaskEmployeeRefer.reference_id' => $key, 'ActivityTaskEmployeeRefer.activity_task_id' => $val),false
                );
            }
            else
            {
                $this->ActivityTaskEmployeeRefer->updateAll(
                    array('ActivityTaskEmployeeRefer.reference_id' => $pcNeedReAssign[0]['ERP']['profit_center_id']),
                    array('ActivityTaskEmployeeRefer.reference_id' => $key, 'ActivityTaskEmployeeRefer.activity_task_id' => $val)
                );
            }
        }
        /*-----------------PROJECT-----------------*/
        //GET PC ASSIGNED TO PROJECT TASK
        $listPcAssignProject = $this->ProjectTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 1),
            'fields' => array('DISTINCT reference_id')
        ));
        $listPcAssignProject = Set::classicExtract($listPcAssignProject,'{n}.ProjectTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO TASK, BUT DELETED THIS
        $listPcDeletedProject = array_diff($listPcAssignProject,$listPc);
        //GET ALL ACTIVITY TASK REFER PC DELETED
        $_joins = array(
            array(
                'table' => 'profit_centers',
                'alias' => 'Activity',
                'type' => 'LEFT',
                'foreignKey' => 'id',
                'conditions'=> array(
                    'ProjectTaskEmployeeRefer.project_task_id = Activity.id',
                ),
            )
        );
        $_fields=array(
            'ProjectTaskEmployeeRefer.project_task_id',
            'ProjectTaskEmployeeRefer.reference_id'
        );
        $projectTasks = $this->ProjectTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('ProjectTaskEmployeeRefer.is_profit_center' => 1, 'ProjectTaskEmployeeRefer.reference_id' => $listPcDeletedProject),
            'joins' => $_joins,
            'fields' => $_fields
        ));
        $projectTaskIDs = Set::combine($projectTasks,'{n}.ProjectTaskEmployeeRefer.project_task_id','{n}.ProjectTaskEmployeeRefer.reference_id');
        foreach($projectTaskIDs as $val => $key )
        {
            /*$projectTaskLists = $this->ProjectTask->find('all',array(
                //'recursive' => -1,
                'conditions' => array('ProjectTask.id' => $val),
                //'fields' => array('DISTINCT reference_id')
            ));*/
            $pcNeedReAssignProject = $this->ActivityRequest->find('all',array(
                'recursive' => -1,
                'conditions' => array('ActivityRequest.task_id' => $val, 'ActivityRequest.status' => 2),
                'joins' => $joins,
                'fields' => $fields,
                'order' => array('ActivityRequest.task_id'),
                'limit' => 1
            ));
            //debug($pcNeedReAssignProject);
            if(empty($pcNeedReAssignProject))
            {
                $this->ProjectTaskEmployeeRefer->deleteAll(
                    array('ProjectTaskEmployeeRefer.reference_id' => $key, 'ProjectTaskEmployeeRefer.project_task_id' => $val),false
                );
            }
            else
            {
                $this->ProjectTaskEmployeeRefer->updateAll(
                    array('ProjectTaskEmployeeRefer.reference_id' => $pcNeedReAssignProject[0]['ERP']['profit_center_id']),
                    array('ProjectTaskEmployeeRefer.reference_id' => $key, 'ProjectTaskEmployeeRefer.project_task_id' => $val)
                );
            }
        }
        echo "Finish !!!";
        exit;
    }
    function transferEmployeeVsProfitInProjectAndActivityRefer(){
        //Ham nay dung de format du lieu khac phuc bug : PC da duoc assign task nhung bi xoa ma khong validate.
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProfitCenter');
        $this->loadModel('Employee');
        $this->loadModel('ActivityRequest');
        $company = $this->employee_info['Company']['id'];
        //GET ALL PC
        $listPc = $this->ProfitCenter->find('list',array(
            'recursive' => -1,
            'conditions' => array('company_id'=>$company),
            'fields' => array('id')
        ));
        //GET ALL Employee
        $this->loadModel('CompanyEmployeeReference');
        $listEmp = $this->CompanyEmployeeReference->find('list',array(
            'recursive' => -1,
            'conditions' => array('company_id'=>$company),
            'fields' => array('employee_id')
        ));
        //GET EMPLOYEE ASSIGNED TO ACTIVITY TASK
        $listEmpAssign = $this->ActivityTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 0),
            'fields' => array('DISTINCT reference_id')
        ));
        $listEmpAssign = Set::classicExtract($listEmpAssign,'{n}.ActivityTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO ACTIVITY TASK, BUT DELETED THIS
        $listEmpDeleted = array_diff($listEmpAssign,$listEmp);
        foreach($listEmpDeleted as $val)
        {
            if(in_array($val, $listPc))
            {
                $this->ActivityTaskEmployeeRefer->updateAll(
                    array('ActivityTaskEmployeeRefer.is_profit_center' => 1),
                    array('ActivityTaskEmployeeRefer.reference_id' => $val, 'ActivityTaskEmployeeRefer.is_profit_center' => 0)
                );
            }
        }
        //GET PC ASSIGNED TO ACTIVITY TASK
        $listPcAssign = $this->ActivityTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 1),
            'fields' => array('DISTINCT reference_id')
        ));
        $listPcAssign = Set::classicExtract($listPcAssign,'{n}.ActivityTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO ACTIVITY TASK, BUT DELETED THIS
        $listPcDeleted = array_diff($listPcAssign,$listPc);
        foreach($listPcDeleted as $val)
        {
            if(in_array($val, $listEmp))
            {
                $this->ActivityTaskEmployeeRefer->updateAll(
                    array('ActivityTaskEmployeeRefer.is_profit_center' => 0),
                    array('ActivityTaskEmployeeRefer.reference_id' => $val, 'ActivityTaskEmployeeRefer.is_profit_center' => 1)
                );
            }
        }
        /*-----------------PROJECT-----------------*/
        //GET EMPLOYEE ASSIGNED TO ACTIVITY TASK
        $listEmpAssignProject = $this->ProjectTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 0),
            'fields' => array('DISTINCT reference_id')
        ));
        $listEmpAssignProject = Set::classicExtract($listEmpAssignProject,'{n}.ProjectTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO ACTIVITY TASK, BUT DELETED THIS
        $listEmpDeletedProject = array_diff($listEmpAssignProject,$listEmp);
        foreach($listEmpDeletedProject as $val)
        {
            if(in_array($val, $listPc))
            {
                $this->ProjectTaskEmployeeRefer->updateAll(
                    array('ProjectTaskEmployeeRefer.is_profit_center' => 1),
                    array('ProjectTaskEmployeeRefer.reference_id' => $val, 'ProjectTaskEmployeeRefer.is_profit_center' => 0)
                );
            }
        }
        //GET PC ASSIGNED TO PROJECT TASK
        $listPcAssignProject = $this->ProjectTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            'conditions' => array('is_profit_center' => 0),
            'fields' => array('DISTINCT reference_id')
        ));
        $listPcAssignProject = Set::classicExtract($listPcAssignProject,'{n}.ProjectTaskEmployeeRefer.reference_id');
        //GET ALL PC ASSIGNED TO TASK, BUT DELETED THIS
        $listPcDeletedProject = array_diff($listPcAssignProject,$listPc);
        foreach($listPcDeletedProject as $val)
        {
            if(in_array($val, $listEmp))
            {
                $this->ProjectTaskEmployeeRefer->updateAll(
                    array('ProjectTaskEmployeeRefer.is_profit_center' => 0),
                    array('ProjectTaskEmployeeRefer.reference_id' => $val, 'ProjectTaskEmployeeRefer.is_profit_center' => 1)
                );
            }
        }
        echo "Finish !!!";
        exit;
    }
    /*Delete Project Teams not exits function refer and insert new Project Teams + add function refer for this.*/
    function formatProjectTeam()
    {
        $company = $this->employee_info['Company']['id'];
        $this->loadModel('ProjectTeam');
        $this->loadModel('Project');
        $this->loadModel('ProjectTask');
        $this->loadModel('ProjectFunctionEmployeeRefer');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $this->loadModel('ProjectTaskEmployeeRefer');
        //GET ALL PROJECT TEAMS
        $projectTeams = $this->ProjectTeam->find('list',array(
            'recursive' => -1,
            //'conditions' => array('company_id'=>$company),
            'fields' => array('id')
        ));
        //GET PROJECT TEAMS EXISTS FUNCTION REFER
        $projectTeamRefers = $this->ProjectFunctionEmployeeRefer->find('all',array(
            'recursive' => -1,
            //'conditions' => array('company_id'=>$company),
            'fields' => array('DISTINCT project_team_id')
        ));
        $projectTeamRefers = Set::classicExtract ($projectTeamRefers, '{n}.ProjectFunctionEmployeeRefer.project_team_id');
        //GET TEAMS NOT REFER EMPLOYEE, PC, SKILL
        $projectTeamsEmpty = array_diff($projectTeams,$projectTeamRefers);
        //DELETED TEAMS NOT REFER EMPLOYEE, PC, SKILL
        $this->ProjectTeam->deleteAll(
            array('ProjectTeam.id' => $projectTeamsEmpty), false
        );
        //GET PROJECT EXISTS PROJECT TEAMS
        $projectForTeams = $this->ProjectTeam->find('all',array(
            'recursive' => -1,
            //'conditions' => array('id'=>$projectTeamsEmpty),
            'fields' => array('DISTINCT project_id')
        ));
        $projectForTeams = Set::classicExtract($projectForTeams,'{n}.ProjectTeam.project_id');
        //GET ALL PROJECT
        $projects = $this->Project->find('list',array(
            'recursive' => -1,
            //'conditions' => array('id'=>$projectTeamsEmpty),
            'fields' => array('id')
        ));
        //GET PROJECT IS NOT EXISTS PROJECT TEAM
        $projectNotExistsTeam = array_diff($projects,$projectForTeams);
        //GET ALL PROJECT EXISTS ASSIGNED
        $_joins = array(
            array(
                'table' => 'project_tasks',
                'alias' => 'ProjectTask',
                'type' => 'LEFT',
                'foreignKey' => 'id',
                'conditions'=> array(
                    'ProjectTaskEmployeeRefer.project_task_id = ProjectTask.id',
                ),
            )
        );
        $_fields=array(
            'DISTINCT ProjectTask.project_id'
        );
        $projectRefers = $this->ProjectTaskEmployeeRefer->find('all',array(
            'recursive' => -1,
            //'conditions' => array('ProjectTaskEmployeeRefer.is_profit_center' => 1, 'ProjectTaskEmployeeRefer.reference_id' => $listPcDeletedProject),
            'joins' => $_joins,
            'fields' => $_fields
        ));
        $projectRefers = Set::classicExtract($projectRefers,'{n}.ProjectTask.project_id');
        //debug($projectNotExistsTeam);
        //GET LIST PROJECT NEED ADD THE TEAM
        //$projectNeedAddTeams = array_intersect ($projectRefers,$projectNotExistsTeam);
        $projectNeedAddTeams = $projectRefers ;
        //GET DATA EMPLOYEE REFER PROFIT CENTER
        $employeeReferPc = $this->ProjectEmployeeProfitFunctionRefer->find('list',array(
            'recursive' => -1,
            //'conditions' => array('ProjectEmployeeProfitFunctionRefer.is_profit_center' => 1, 'ProjectTaskEmployeeRefer.reference_id' => $listPcDeletedProject),
            'fields' => array('employee_id','profit_center_id')
        ));
        //MAIN
        $dataSaves = array();
        foreach($projectNeedAddTeams as $id)
        {
            echo 'Project : '.$id.'<br />';
            $projectLists = $this->ProjectTask->find('list',array(
                'recursive' => -1,
                'conditions' => array('ProjectTask.project_id' => $id),
                'fields' => 'id'
            ));
            $listEmployeePCReFers = $this->ProjectTaskEmployeeRefer->find('all',array(
                'recursive' => -1,
                'conditions' => array('ProjectTaskEmployeeRefer.project_task_id' => $projectLists),
                'fields' => array('ProjectTaskEmployeeRefer.reference_id, ProjectTaskEmployeeRefer.is_profit_center')
            ));
            $dataPcRefer = array();
            $dataPc = array();
            //ADD THE TEAM
            $this->ProjectTeam->create();
            $this->ProjectTeam->save(
                array('project_id' => $id, 'price_by_date' => 0)
            );
            $teamId = $this->ProjectTeam->getLastInsertID();
            foreach($listEmployeePCReFers as $val)
            {
                $refer = $val['ProjectTaskEmployeeRefer']['reference_id'];
                if($val['ProjectTaskEmployeeRefer']['is_profit_center']==1)
                {
                    $dataPc[] = $refer;
                }
                else
                {
                    if(isset($employeeReferPc[$refer]))
                    {
                        $dataPcRefer[] = $employeeReferPc[$refer];
                        $dataSaves[] = array('employee_id' => $refer, 'profit_center_id' => $employeeReferPc[$refer], 'project_team_id' => $teamId, 'backup' => null);
                    }
                }
            }
            $dataPc = array_diff($dataPc,$dataPcRefer);
            $dataPc = array_unique($dataPc);
            foreach($dataPc as $pc)
            {
                $dataSaves[] = array('employee_id' => null, 'profit_center_id' => $pc, 'project_team_id' => $teamId, 'backup' => 0);
            }
        }
        $this->ProjectFunctionEmployeeRefer->create();
        $this->ProjectFunctionEmployeeRefer->saveAll($dataSaves);
        //END
        exit('Finish!!!');
    }
    function checkConsumedInvalid()
    {
        $this->loadModel('ActivityRequest');
        $this->loadModel('Holiday');
        $this->loadModel('Employee');
        $this->loadModel('ProfitCenter');
        $this->loadModel('ProjectEmployeeProfitFunctionRefer');
        $activityRequests = $this->ActivityRequest->find('list',array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'status' => 2,
                'value <>' => 0,
                "FROM_UNIXTIME(date, '%d-%m-%Y')" => '25-12-2014'
            ),
            'fields' => 'employee_id'
        ));
        $activityRequests = array_values($activityRequests);
        $employeeReferPc = $this->ProjectEmployeeProfitFunctionRefer->find('list',array(
            'recursive' => -1,
            'conditions' => array(
                'employee_id' => $activityRequests
            ),
            'fields' => array('employee_id','profit_center_id')
        ));
        $employees = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $activityRequests
            )
        ));
        $employees = Set::combine($employees,'{n}.Employee.id','{n}.Employee');
        $datasPC = array();
        foreach($employeeReferPc as $emp=>$pc)
        {
            $emp = $employees[$emp]['first_name'].' '.$employees[$emp]['last_name'];
            $datasPC[$pc] = isset($datasPC[$pc]) ? $datasPC[$pc] : array() ;
            $datasPC[$pc][] = $emp;
        }
        $idPc = array_keys($datasPC);
        $profitCenters = $this->ProfitCenter->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id'],
                'id' => $idPc
            ),
            'fields' => array('name'),
            'order' => 'lft'
        ));
        foreach($profitCenters as $id=>$name)
        {
            echo "<b>$name</b>"; echo '<br />';
            foreach($datasPC[$id] as $val)
            {
                echo "<i>$val</i>";echo '<br />';
            }
            echo '-------';
            echo '<br />';
        }
        exit;
    }
    function syncWorkloadFormTaskVsWorkloadFromRefers()
    {
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->loadModel('ProjectTask');
        $this->loadModel('ActivityTaskEmployeeRefer');
        $this->loadModel('ActivityTask');
    }
    /*
    * Loai bo staffing cua nhung project/activity ko ton tai
    */
    function trimStaffing(){
        exit('Completed!');
        $projects = ClassRegistry::init('Project')->find('list', array(
            'recursive' => -1,
            'fields' => array('id')
        ));
        $activities = ClassRegistry::init('Activity')->find('list', array(
            'recursive' => -1,
            'fields' => array('id')
        ));
        //tien hanh xoa
        $staffing = ClassRegistry::init('TmpStaffingSystem');
        $staffing->query('delete from tmp_staffing_systems where project_id != 0 and NOT ( project_id IN ('. implode(',', $projects) .') )');
        $staffing->query('delete from tmp_staffing_systems where activity_id != 0 and NOT ( activity_id IN ('. implode(',', $activities) .') )');
        exit('done');
    }
    function deleteTaskReferenceInvalid()
    {
        $this->loadModel('ProjectTaskEmployeeRefer');
        $this->ProjectTaskEmployeeRefer->deleteAll(
            array('ProjectTaskEmployeeRefer.reference_id' => 0), false
        );
        exit('Completed!!!');
    }
    function addAcceptance(){
        $this->loadModels('Company', 'Menu');
        $cids = $this->Company->find('list', array('fields' => 'id'));
        foreach($cids as $c){
            //tim xem company da co acceptance chua
            $menu = $this->Menu->find('first', array(
                'conditions' => array(
                    'company_id' => $c,
                    'controllers' => 'project_acceptances',
                    'functions' => 'index'
                )
            ));
            if( $menu )continue;
            $this->Menu->create();
            $this->Menu->save(array(
                'company_id' => $c,
                'controllers' => 'project_acceptances',
                'model' => 'project',
                'name_eng' => 'Acceptance',
                'name_fre' => 'Recette',
                'functions' => 'index',
                'display' => 1,
                'weight' => 99
            ));
        }
        die('ok');
    }
    function removeTranslation(){
        $this->loadModels('Translation');
        $this->Translation->deleteAll(array(
            'Translation.original_text' => array('Var %', 'Action', 'Invoice', 'Total')
        ));
        die('ok');
    }
    function updateMenu(){
        $this->loadModel('Company');
        $list = $this->Company->find('list', array('fields' => array('id')));
        foreach($list as $id){
            $this->requestAction('/menus/autoInsert', array('pass' => array($id)));
        }
        die('OK');
    }
    function addManual(){
        $this->loadModels('Company', 'ActivityColumn');
        $list = $this->Company->find('list', array('fields' => array('id')));
        foreach ($list as $id) {
            $e = $this->ActivityColumn->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'ActivityColumn.key' => 'manual_consumed',
                    'company_id' => $id
                )
            ));
            if( !empty($e) )continue;
            $this->ActivityColumn->create();
            $this->ActivityColumn->save(array(
                'name' => 'Manual Consumed',
                'key' => 'manual_consumed',
                'description' => '',
                'display' => 0,
                'weight' => 999,
                'company_id' => $id
            ));
        }
        die('ok');
    }
    function fixDir(){
        $list = $this->Company->find('list', array('fields' => array('id', 'company_name')));
        foreach($list as $id => $name){
            $dir = strtolower(Inflector::slug($name));
            $this->Company->id = $id;
            $this->Company->saveField('dir', $dir);
        }
        die('OK');
    }
    function updateActivityTaskId(){
        $this->loadModels('NctWorkload', 'ActivityTask');
        $refers = $this->ActivityTask->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_task_id IS NOT NULL',
                'is_nct' => 1
            ),
            'fields' => array('project_task_id', 'id')
        ));
        foreach($refers as $pid => $aid){
            $this->NctWorkload->updateAll(array(
                'NctWorkload.activity_task_id' => $aid
            ), array(
                'NctWorkload.project_task_id' => $pid
            ));
        }
        die('ok');
    }
    function showMe(){
        print_r($this->Session->read('Auth.employee_info.Employee'));
        die;
    }
    function cleanQuanManure(){
        $this->loadModels('Employee', 'CompanyEmployeeReference');
        $id = $this->Employee->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'email' => 'support@azuree-app.com'
            )
        ));
        $id = $id['Employee']['id'];
        $this->CompanyEmployeeReference->deleteAll(array(
            'CompanyEmployeeReference.employee_id' => $id
        ));
        die('done');
    }
    function formatNCT(){
        $this->loadModels('NctWorkload');
        $cid = $this->employee_info['Company']['id'];
        //remove all invalid rows
        $this->NctWorkload->deleteAll(array(
            'activity_task_id' => 0,
            'project_task_id' => 0
        ));
        $this->NctWorkload->virtualFields['the_day'] = 'IF(group_date IS NULL OR group_date = "", task_date, group_date)';
        $raw = $this->NctWorkload->find('all', array(
            'fields' => array(
                'the_day', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date',
                'SUM(estimated) as total'
            ),
            'group' => array(
                'the_day', 'reference_id', 'is_profit_center', 'project_task_id', 'activity_task_id', 'group_date'
            )
        ));
        $data = array();
        foreach($raw as $d){
            $da = $d['NctWorkload'];
            $dd = $da['the_day'];
            if( strlen($dd) > 10 ){
                list($type, $date, $ed) = explode('_', $dd);
                $date = str_utility::convertToSQLDate($date);
                $ed = str_utility::convertToSQLDate($ed);
            } else {
                $date = $ed = $dd;
            }
            $data[] = array(
                'reference_id' => $da['reference_id'],
                'is_profit_center' => $da['is_profit_center'],
                'project_task_id' => $da['project_task_id'],
                'activity_task_id' => $da['activity_task_id'],
                'group_date' => $da['group_date'],
                'estimated' => $d[0]['total'],
                'task_date' => $date,
                'end_date' => $ed
            );
        }
        pr($data);
        $this->NctWorkload->query('TRUNCATE TABLE nct_workloads');
        $this->NctWorkload->saveAll($data);
        die('done');
    }
    function diffNctWorkload(){
        $this->loadModels('ProjectTask', 'ActivityTask');
        $data = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('ProjectTask.id', 'SUM(W.estimated) as workload', 'ProjectTask.estimated', 'ProjectTask.project_id', 'W.activity_task_id'),
            'conditions' => array(
                'is_nct' => 1
            ),
            'joins' => array(
                array(
                    'table' => 'nct_workloads',
                    'type' => 'inner',
                    'alias' => 'W',
                    'conditions' => array('ProjectTask.id = W.project_task_id')
                )
            ),
            'group' => array('ProjectTask.id', 'project_id HAVING workload != ProjectTask.estimated')
        ));
        $data2 = Set::combine($data, '{n}.ProjectTask.id', '{n}', '{n}.ProjectTask.project_id');
        echo '<pre>';
        foreach($data2 as $pid => $tasks){
            echo "Project: $pid\n";
            foreach($tasks as $d){
                $workload = $d[0]['workload'];
                $taskId = $d['ProjectTask']['id'];
                $aTask = $d['W']['activity_task_id'];
                echo "\tTask: <a href='/project_tasks/index/$pid/?id=$taskId' target='_blank'>$taskId</a> [current workload: {$d['ProjectTask']['estimated']}]\t->\tcorrect workload: <b>$workload</b>\n";
                //process
                $this->ProjectTask->id = $taskId;
                $this->ProjectTask->saveField('estimated', $workload);
                if( $aTask ){
                    $this->ActivityTask->id = $aTask;
                    $this->ActivityTask->saveField('estimated', $workload);
                }
            }
            echo "-----------------------------\n";
            //rebuild staffing
        }
        echo '</pre>';
        die;
    }
    //update timesheet filling activity for project in progess
    function updateTimesheetActivity(){
        $db = ConnectionManager::getDataSource('default');
        if(!empty($this->employee_info['Company']['id'])){
            $company_id = $this->employee_info['Company']['id'];
            $sql = "UPDATE `projects` SET activated = '1' WHERE category = 1 AND company_id = $company_id";
            $db->query($sql);
            echo 'OKIE';
            exit;
        }
        echo 'Error';
        exit;
    }
    //lai lay du lieu text project task qua project task txt.
    function getTextOfProjectTask(){
        $this->loadModels('ProjectTask', 'ProjectTaskTxt', 'Employee');
        $listEmployee = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('fullname', 'id')
        ));
        $listTexts = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'text_1', 'text_updater', 'text_time'),
            'conditions' => array(
                'text_1 !=' => null,
                'text_updater !=' => null
            )
        ));
        foreach ($listTexts as $listText) {
            $dx = $listText['ProjectTask'];
            if( !empty($dx['id']) && !empty($dx['text_1']) && !empty($dx['text_updater']) && !empty($dx['text_time']) ){
                $saved = array(
                    'project_task_id' => $dx['id'],
                    'employee_id' => $listEmployee[$dx['text_updater']],
                    'comment' => $dx['text_1'],
                    'created' => $dx['text_time']
                );
                $this->ProjectTaskTxt->create();
                $this->ProjectTaskTxt->save($saved);
            }
        }
        echo "Done!!!";
        die;
    }
    // lay du lieu tu project issue assign to qua multi project issue employee refer
    function getAssignToProjectIssue(){
        $this->loadModels('ProjectIssue', 'ProjectIssueEmployeeRefer');
        $listAssign = $this->ProjectIssue->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'issue_assign_to')
        ));
        foreach ($listAssign as $id => $assign) {
            if(!empty($assign)){
                $saved = array(
                    'project_issue_id' => $id,
                    'reference_id' => $assign,
                    'is_profit_center' => 0
                );
                $check = $this->ProjectIssueEmployeeRefer->find('first', array(
                    'recursive' => -1,
                    'conditions' =>$saved
                ));
                if(empty($check)){
                    $this->ProjectIssueEmployeeRefer->create();
                    $this->ProjectIssueEmployeeRefer->save($saved);
                }
            }
        }
        echo "Done!!!";
        die;
    }
    // ham nay rat nguy hiem. viet xong nho xoa.
    function deleteAllCompanyData($company_id){
        set_time_limit(0);
        $db = ConnectionManager::getDataSource('default');
        $this->loadModels('Company', 'Employee', 'Project', 'HistoryFilter', 'UserView', 'TranslationEntry', 'Translation', 'Activity','NctWorkload','ActivityTask');
        $is_sas = $this->employee_info['Employee']['is_sas'];
        if($is_sas == 1){
            // danh sach id nhung company can delete.
            $list_company_id_delete = $this->Company->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'id !=' => $company_id
                ),
                'fields' => array('id', 'id')
            ));
			$this->deletelistCompanyDatabase($list_company_id_delete);            
            echo  "Done!!!";
            die(1);
        }
        echo "Not permissions";
        die;
    }
	function deletelistCompanyData(){
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$list_company_id_delete = array(
			'1' => 'DEMONSTRATEUR',
			'2' => 'HAZURE Co',
			'6' => 'AZUREE',
			'7' => 'AZUREE',
			'8' => 'GLOBALSI',
			'21' => 'MAIRIE',
			'27' => 'z0 Gravity',
			'28' => 'CMACGM',
			'32' => 'Collectivité',
			'36' => 'GROUPE CASINO',
			'37' => 'ASANTE',
			'38' => 'SPIREPAYMENTS',
			'41' => 'ONET DEMONSTRATEUR',
			'43' => 'thecamp',
			'44' => 'INNOVATION',
			'45' => 'VINACAPITAL',
			'46' => 'CA PROVENCE COTE D AZUR',
			'47' => 'ALTEN GROUPE',
			'54' => 'AMP',
			'54' => 'INNOVATION2',
		);
		$list_company_id_delete = array_keys($list_company_id_delete);
		$this->deleteCompanyData($list_company_id_delete);
		exit;
	}
	function deleteCompanyData($company_id=null){
		set_time_limit(0);        
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		if( !$company_id ){
			$this->cakeError('error404');
			exit;
		}
		$is_ajax = $this->params['isAjax'];
		$list_company_id_delete = array();
		
		$db = ConnectionManager::getDataSource('default');
        $this->loadModels('Company');
		if( $company_id ) {
			$list_company_id_delete = $this->Company->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $company_id
				),
				'fields' => array('id', 'id')
			));	
		}
		$this->deletelistCompanyFolder($list_company_id_delete);
		if( !$is_ajax) echo '<p>'. __('Deleted file', true).'</p>';
		
		$this->deletelistCompanyDatabase($list_company_id_delete);
		if( !$is_ajax ) echo '<p>'. __('Deleted database', true).'</p>';
		
		if( $is_ajax  ) die( 1 );
		exit;
		
	}
	private function deletelistCompanyDatabase($list_company_id_delete){
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$db = ConnectionManager::getDataSource('default');
        $this->loadModels('Company', 'Employee', 'Project', 'HistoryFilter', 'UserView', 'TranslationEntry', 'Translation', 'ActivityTask');
		
		$list_project_delete = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $list_company_id_delete
			),
			'fields' => array('id', 'id')
		));
		// debug( $list_company_id_delete);
		
		
		
		/* Delete employee data */
		// Update 02/12/2019. DEV update thi cap nhat lai ngay update.
		$employee_table_model = array(
			'history_filters',
			'activity_comments',
			'activity_forecasts',
			'employee_absences',
			'employee_multi_resources',
			'project_employee_profit_function_refers',
			'project_function_employee_refers',
			'project_texts',
			'user_default_views',
			'user_status_view_activities',
			'user_status_view_sale_deals',
			'user_status_view_sales',
			'user_status_views',
			'your_form_filters',
			'employee_default_profiles',
			'zog_msg_likes',
			'activity_forecast_comments',
			'project_task_attachment_views',
			'project_task_txt_refers',
			'zog_msgs',
			'zog_msg_refers',
			'project_powerbi_dashboards',
			'project_finance_plus_txts',
			'project_finance_plus_txt_views',
			'project_finance_plus_attachments',
			'project_finance_plus_attachment_views',
			'project_task_favourites',
			'project_detail_employee_settings',
			'employee_last_logins',
			'project_dashboard_shares',
			'project_dashboard_actives',
		);
		
		foreach ($employee_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE etb FROM $table etb INNER JOIN employees e ON e.id = etb.employee_id WHERE e.company_id=$_id";
					$db->query($query);
				}
			}
		}
		
		/* END Delete employee data */
		
		/* Delete nct_workloads data */
		$list_activitytask_delete = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'A.company_id' => $list_company_id_delete
			),
			'fields' => array('ActivityTask.id', 'ActivityTask.id'),
			'joins' => array(
				array(
					'table' => 'activities',
					'alias' => 'A',
					'conditions' => array('ActivityTask.activity_id = A.id')
				)
			)
		));
		//debug( $list_nct_workloads_delete); exit;
		$activity_table_model = array(
			'nct_workloads',
		);
		foreach ($activity_table_model as $table) {
			foreach ($list_activitytask_delete as $id) {
				if(!empty($id)){
					$query = "DELETE FROM $table WHERE `id` = $id";
					$db->query($query);
				}
			}
		}
		/* End Delete nct_workloads data */
		
		/* Delete profiles data */
		$profile_table_model = array(
			'profile_values',
		);
		foreach ($profile_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pvtb FROM $table pvtb INNER JOIN profiles prf ON prf.id = pvtb.profile_id WHERE prf.company_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete employee data */
		
		/* Delete project_amr_sub_programs data */
		$project_amr_sub_programs_table_model = array(
			'project_amr_sub_programs',
		);
		foreach ($project_amr_sub_programs_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pa_sp FROM $table pa_sp INNER JOIN project_amr_programs pap ON pap.id = pa_sp.project_amr_program_id WHERE pap.company_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_amr_sub_programs data */
		
		/* Delete project_amr_sub_categories data */
		$project_amr_sub_categories_table_model = array(
			'project_amr_sub_categories',
		);
		foreach ($project_amr_sub_categories_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pa_sc FROM $table pa_sc INNER JOIN project_amr_categories pac ON pac.id = pa_sc.project_amr_category_id WHERE pac.company_id=$_id";
					$db->query($query);
				}
			}
		}
		/* END Delete project_amr_sub_categories data */
		
		/* Delete family data */
		$family_table_model = array(
			'activity_profit_refers',
			'activity_tasks',
		);
		
		foreach ($family_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE ftb FROM $table ftb INNER JOIN activities acf ON acf.id = ftb.activity_id WHERE acf.company_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete family data */
		
		/* Delete project_expectation_employee_refers data */
		$employee_refers_table_model = array(
			'project_expectation_employee_refers',
		);
		
		foreach ($employee_refers_table_model as $table) {
			foreach ($list_project_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE peef FROM $table peef INNER JOIN project_expectations pe ON pe.id = peef.project_expectation_id WHERE pe.project_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_expectation_employee_refers data */
		
		/* Delete project_issue_employee_refers data */
		$issue_employee_refers_table_model = array(
			'project_issue_employee_refers',
		);
		
		foreach ($issue_employee_refers_table_model as $table) {
			foreach ($list_project_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pier FROM $table pier INNER JOIN project_issues pi ON pi.id = pier.project_issue_id WHERE pi.project_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_issue_employee_refers data */
		
		/* Delete project_livrable_comments data */
		$issue_employee_refers_table_model = array(
			'project_livrable_comments',
		);
		
		foreach ($issue_employee_refers_table_model as $table) {
			foreach ($list_project_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE plc FROM $table plc INNER JOIN project_livrables pl ON pl.id = plc.project_livrable_id WHERE pl.project_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_livrable_comments data */
		
		/* Delete project_task_attachments data */
		$issue_employee_refers_table_model = array(
			'project_task_attachments',
			'project_task_favourites',
		);
		
		foreach ($issue_employee_refers_table_model as $table) {
			foreach ($list_project_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pta FROM $table pta INNER JOIN project_tasks pt ON pt.id = pta.task_id WHERE pt.project_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_task_attachments data */
		
		/* Delete sale lead data */
		$sale_lead_table_model = array(
			'sale_lead_employee_refers',
			'sale_lead_files',
			'sale_lead_logs',
			'sale_lead_product_expenses',
			'sale_lead_product_invoices',
			'sale_lead_products',
		);
		foreach ($sale_lead_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE slt FROM $table slt INNER JOIN sale_leads sl ON sl.id = slt.sale_lead_id WHERE sl.company_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete sale lead data */
		
		/* Delete ticket data */
		// Update 18/11/2020. DEV update thi cap nhat lai ngay update.
		$ticket_profile_status_table_model = array(
			'ticket_profile_status_references',
		);
		foreach ($ticket_profile_status_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE tpsr FROM $table tpsr INNER JOIN ticket_statuses ts ON ts.id = tpsr.ticket_status_id WHERE ts.company_id=$_id";
					$db->query($query);
				}
			}
		}
		
		$ticket_table_model = array(
			'ticket_attachments',
			'ticket_comments',
			'ticket_notes',
			'ticket_subscriptions',
		);
		foreach ($ticket_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE tkt FROM $table tkt INNER JOIN tickets tk ON tk.id = tkt.ticket_id WHERE tk.company_id=$_id and tk.company_model = 'Company'";
					$db->query($query);
				}
			}
		}			
		
		$ticket_table_model = array(
			'ticket_metas',
			'ticket_profiles',
			'ticket_statuses',
		);
		foreach ($ticket_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE FROM $table WHERE `company_id` = $_id";
					$db->query($query);
				}
			}
		}
		
		$ticket_table_model = array(
			'tickets'
		);
		foreach ($ticket_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE tb FROM $table tb INNER JOIN externals ext ON ext.id = tb.company_id WHERE ext.company_id = $_id  and tb.company_model = 'External'";
					$db->query($query);
				}
			}
		}
		foreach ($ticket_table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE FROM $table WHERE `company_id` = $_id and `company_model` = 'Company'";
					$db->query($query);
				}
			}
		}
		/* END Delete ticket data */
		
		/* Delete data 14/08/2021*/
		// Update 14/08/2021. DEV update thi cap nhat lai ngay update.
		$table_project_m = array(
			'm_favorites',
			'm_likes'
		);
		foreach ($table_project_m as $table) {
			foreach ($list_project_delete as $id) {
				if(!empty($id)){
					$query = "DELETE FROM $table WHERE `modelId` = $id";
					$db->query($query);
				}
			}
		}
		/* Delete project_task_attachments data */
		$refers_2nd_to_projects_tables = array(
			'project_communication_urls' => array(
				'table' => 'project_communications',
				'ref_key' => 'communication_id'
			),
		);
		
		foreach ($refers_2nd_to_projects_tables as $table => $refer_table) {
			foreach ($list_project_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE pta FROM $table pta INNER JOIN ". $refer_table['table'] ." refer_tbl ON refer_tbl.id = pta.". $refer_table['ref_key']." WHERE refer_tbl.project_id=$_id";
					$db->query($query);
				}
			}
		}			
		/* END Delete project_task_attachments data */
		$table_project_p = array(
			'project_communications',
		);
		foreach ($table_project_p as $table) {
			foreach ($list_project_delete as $id) {
				if(!empty($id)){
					$query = "DELETE FROM $table WHERE `project_id` = $id";
					$db->query($query);
				}
			}
		}
		$table_company = array(
			'project_targets',
			'company_employee_references',
		);
		foreach ($table_company as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE FROM $table WHERE `company_id` = $_id";
					$db->query($query);
				}
			}
		}
		/*END Delete data 14/08/2021*/
		
		//delete all project.
		$project_table_model = array(
			'project_global_views',
			'project_images',
			'project_finances',
			'project_files',
			'project_amrs',
			'project_budget_externals',
			'project_budget_internal_details',
			'project_budget_internals',
			'project_budget_invoices',
			'project_budget_provisionals',
			'project_budget_purchase_invoices',
			'project_budget_purchases',
			'project_budget_sales',
			'project_budget_syns',
			'project_created_vals',
			// 'tmp_staffing_systems',
			'project_acceptances',
			'project_decisions',
			'project_dependencies',
			'project_employee_managers',
			'project_expectations',
			'project_issues',
			'project_list_multiples',
			'project_livrable_actors',
			'project_livrables',
			'project_local_views',
			'project_milestones',
			'project_parts',
			'project_phase_currents',
			'project_phase_plans',
			'project_risks',
			'project_tasks',
			'project_teams',
			'subscribes',
			'project_evolution_impact_refers',
		);
		foreach ($project_table_model as $table) {
			foreach ($list_project_delete as $id) {
				if(!empty($id)){
					$query = "DELETE FROM $table WHERE `project_id` = $id";
					$db->query($query);
					// $query = "DELETE FROM `projects` WHERE `id` = $id";
					// $db->query($query);
				}
			}
		}
		
		// END delete all project.
		
		//delete data lien quan toi company.
		// Update 14/08/2021. DEV update thi cap nhat lai ngay update.
		$table_model = array(
			'absences',
			'action_logs',
			'employees',
			'translation_settings',
			'translation_entries',
			'activity_budgets',
			'activity_columns',
			'activity_exports',
			'activity_families',
			'activity_request_confirms',
			'activity_request_confirm_months',
			'activity_request_copies',
			'activity_requests',
			'activities',
			'api_keys',
			'audit_admins',
			'audit_recom_employee_refers',
			'audit_recoms',
			'audit_settings',
			'budget_customers',
			'budget_funders',
			'budget_providers',
			'budget_settings',
			'budget_types',
			'categories',
			'cities',
			'colors',
			'contract_types',
			'countries',
			'currencies',
			'dependencies',
			'easyraps',
			'expectation_colors',
			'expectation_datasets',
			'expectation_translations',
			'expectations',
			'favory_absences',
			'holidays',
			'log_systems',
			'mail_not_send_yets',
			'menus',
			'my_assistants',
			'periods',
			'profile_project_manager_details',
			'profile_project_managers',
			'profiles',
			'profit_center_manager_backups',
			'profit_centers',
			'project_acceptance_types',
			'project_alerts',
			'project_amr_categories',
			'project_amr_cost_controls',
			'project_amr_organizations',
			'project_amr_perimeters',
			'project_amr_plans',
			'project_amr_problem_controls',
			'project_amr_programs',
			'project_amr_risk_controls',
			'project_amr_statuses',
			'project_complexities',
			'project_created_vals_comments',
			'project_created_values',
			'project_datasets',
			'project_evolution_impacts',
			'project_evolution_types',
			'project_finance_plus_dates',
			'project_finance_plus_details',
			'project_finance_pluses',
			'project_functions',
			'project_indicator_settings',
			'project_issue_colors',
			'project_issue_severities',
			'project_issue_statuses',
			'project_livrable_categories',
			'project_phase_statuses',
			'project_phases',
			'project_priorities',
			'project_risk_occurrences',
			'project_risk_severities',
			'project_types',
			'reports',
			'response_constraints',
			'sale_customer_contacts',
			'sale_customer_ibans',
			'sale_customers',
			'sale_expenses',
			'sale_leads',
			'sale_roles',
			'sale_settings',
			'security_settings',
			'externals',
			'tmp_caculate_absences',
			'tmp_caculate_profit_centers',
			'tmp_module_activity_exports',
			'tmp_profit_center_of_activities',
			'tmp_staffing_systems',
			'user_last_updateds',
			'user_views',
			'vision_task_exports',
			'vm_activity_requests',
			'vm_companies',
			'vm_tasks',
			'workdays',
			'projects',
			'project_settings',
			'project_statuses',
			'company_view_defaults',
			'company_column_defaults',
			'company_default_settings',
			'project_task_favourites',
			'project_targets',
			'sso_logins',
			'customer_logos',
			'project_manager_update_fields',
			'two_factor_authens',
			'project_dashboards',
			// 'ticket_phone_numbers', // Chua update prod
		);
		
		foreach ($table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE FROM $table WHERE `company_id` = $_id";
					// if( $table == 'tmp_staffing_systems') debug( $query);
					$db->query($query);
				}
			}
		}
		$table_model = array(
			'company_configs',
			'sql_managers',
		);
		foreach ($table_model as $table) {
			foreach ($list_company_id_delete as $_id) {
				if(!empty($_id)){
					$query = "DELETE FROM $table WHERE `company` = $_id";
					$db->query($query);
				}
			}
		}
		
		foreach ($list_company_id_delete as $_id) {
			if(!empty($_id)){
				$query = "DELETE FROM `companies` WHERE `id` = $_id";
				$db->query($query);
			}
		}
		die(json_encode(array(
			'company_id' => $list_company_id_delete,
		)));
	}
	 function get_all_directory_and_files($dir){
		 $dh = new DirectoryIterator($dir);   
		 // Directory object 
		 $list_dir = array();
		 foreach ($dh as $item) {
			 if (!$item->isDot()) {
				if ($item->isDir()) {
					$list_dir[] = $dir . $item;
					$child = $this->get_all_directory_and_files("$dir$item".DS);
					$list_dir = array_merge($list_dir, $child );
				} else {
					$list_dir[] = $dir . $item;
				}
			 }
		}
		return $list_dir;
   }
	public function unsetPath(){
		$success = false;
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		if(!empty($_POST)){
			$_path = !empty($_POST['path']) ? $_POST['path'] : '';
			if($this->rrmdir($_path)){
				$success = true;
			}
		}
		die($success);
	}
	public function unsetAllPath(){
		$success = false;
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$success = false;
		if(!empty($_POST)){
			$_path = !empty($_POST['path']) ? $_POST['path'] : '';
			foreach($_path as $key => $value){
				$this->rrmdir($value['name']);
			}
			$success = true;
		}
		die($success);
	}
	public function delete_files(){
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$this->loadModels('Company');
		$list_company = $this->Company->find('all', array(
			'recursive' => -1,
			'fields' => array( 'id', 'dir', 'company_name')
		));
		$list_company_id = !empty($list_company) ? array_unique(Set::classicExtract($list_company, '{n}.Company.id')) : array();
		$list_company_dir = !empty($list_company) ? Set::combine($list_company,'{n}.Company.id','{n}.Company.dir') : array();
		$companies = !empty($list_company) ? Set::combine($list_company,'{n}.Company.id','{n}.Company.company_name') : array();
		if(!empty($_POST)){
			$select_company = !empty($_POST['company_id']) ? $_POST['company_id'] : array();
			$list_dir = array();
			$dir  = FILES;
			$listCompanyFolder = array();
			$listCompanyFolder[] = $dir . 'default';
			$listCompanyFolder[] = $dir . 'easyrap';
			if(!empty($select_company)){
				foreach($select_company as $index => $comp_id){
					$comp_dir = $list_company_dir[$comp_id];
					$listCompanyFolder[] = $dir . $comp_id;
					$listCompanyFolder[] = $dir . 'projects' . DS. $comp_id;
					$listCompanyFolder[] = $dir . 'absence_request_files' . DS. $comp_dir;
					$listCompanyFolder[] = $dir . 'absence_attachment' . DS. $comp_dir;
					$listCompanyFolder[] = $dir . 'logs' . DS. $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'globalviews' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'project_tasks' . DS . $comp_id;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'issue' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'risk' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'decision' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'acceptances' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'expectaions' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'communication' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'financeplus' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'projects' . DS . 'livrable' . DS . $comp_dir;
					$listCompanyFolder[] = $dir . 'avatar_employ' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'sales' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'externals' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'evolution' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'activity_sales' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'activity_tasks' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'project_budgets' . DS . 'activity_externals' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'projects' . DS . 'localviews' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'audit' . DS . 'mission' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'audit' . DS . 'recoms' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'budgets' . DS . 'customer' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'budgets' . DS . 'providers' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'sale_leads' . DS . 'estimate' . DS . $comp_id;
					$listCompanyFolder[] = $dir. 'business' . DS . 'customers' . DS . $comp_dir;
					$listCompanyFolder[] = $dir. 'sale_leads' . DS . 'logs' . DS . $comp_dir;
					for($i=1; $i<= 5; $i++){
						$listCompanyFolder[] = $dir. 'projects' . DS . 'upload_documents_' . $i . DS . $comp_dir;
					}
				}
			}
			$list = array();
			if(!empty($listCompanyFolder)){
				$list = $this->get_all_directory_and_files($dir);
				$new_list = array();
				if(!empty($list)){
					 foreach ($list as $index => $path) {
						 if(!empty($list[$index + 1])){
							 $pos = strpos($list[$index + 1], $path);
							 if ($pos !== false){
								 unset($list[$index]);
							 }else{
								 foreach ($listCompanyFolder as $key => $hard_path) {
									$pos = strpos($path, $hard_path);
									if ($pos !== false) {
										unset($list[$index]);
									}
								 }
							 }
						 }else{
							  foreach ($listCompanyFolder as $key => $hard_path) {
								$pos = strpos($path, $hard_path);
								if ($pos !== false) {
									unset($list[$index]);
								}
							 }
						 }
					 }
				}
			}
			if( $this->params['isAjax']) die(json_encode( $list));
		}
		$this->set(compact('companies'));
	}
	private function deletelistCompanyFolder($list_company_id_delete){
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$this->loadModels('Company');
		$list_company_delete = $this->Company->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $list_company_id_delete
			),
			'fields' => array( 'id', 'dir', 'company_name')
		));
		$fileFolder = FILES;
		foreach($list_company_delete as $company){
			// debug($company); exit;
			$com_dir = $company['Company']['dir'];
			$listCompanyFolder = array(
				'companyProjectFolder' => $fileFolder . 'projects' . DS. $company['Company']['id'],
				'comGlobalView' => $fileFolder . 'projects' . DS . 'globalviews' . DS. $com_dir,
				'comLivrable' => $fileFolder . 'projects' . DS . 'livrable' . DS. $com_dir,
				'comEmployee' => $fileFolder . 'avatar_employ' . DS . $com_dir,
				'budgetSale' => $fileFolder. 'project_budgets' . DS . 'sales' . DS . $com_dir,
				'budgetExternals' => $fileFolder. 'project_budgets' . DS . 'externals' . DS . $com_dir,
				'budgetActivitySales' => $fileFolder. 'project_budgets' . DS . 'activity_sales' . DS . $com_dir,
				'budgetActivityExternals' => $fileFolder. 'project_budgets' . DS . 'activity_externals' . DS . $com_dir,
				// 'comticket' => $fileFolder . $company['Company']['id'],
			);
			foreach( $listCompanyFolder as $_dir) { $this->rrmdir( $_dir);}
			
		} 
		
	}
	private function rrmdir($src) {
		$is_ajax = $this->params['isAjax'];
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		if( is_file($src)){
			unlink($src);
			if( !$is_ajax) echo '<p>' . __('Removed file: ', true) . $src.'</p>';
		}
		if( is_dir($src) ){
			$dir = opendir($src);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					$full = $src . DS . $file;
					$this->rrmdir($full);
				}
			}
			closedir($dir);
			rmdir($src);
			if( !$is_ajax)  echo '<p>' . __('Removed directory: ', true) . $src.'</p>';
		}
				
	}
	function updateDataFieldActivityIDBudget(){
		$this->loadModels('Project', 'Activity');
        $project_activity = $this->Project->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'activity_id')
        ));
		foreach($project_activity as $project_id => $activity_id){
			$activity_id = !empty($activity_id) ? $activity_id : 0;
			$this->updateProjectBudget($project_id, $activity_id);
			
		}
		echo 'OK';
		exit;
		
	}
	// Create by VN
	// Update actvity_id khi chuyen trang thai
	// OP -> IN -> activity_id = activity_id_linked
	// IP -> OP ->activity_id = 0
	private function updateProjectBudget($project_id, $activity_id){
		$this->loadModels('ProjectBudgetSyn', 'ProjectBudgetInternalDetail', 'ProjectBudgetExternal', 'ProjectBudgetSale');
			// Update activity_id linked in project budget internal detail
			$acId_internal = $this->ProjectBudgetInternalDetail->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                ),
                'fields' => array('id', 'id')
            ));
			if(!empty($acId_internal)){
				foreach($acId_internal as $acId_inter){
					$this->ProjectBudgetInternalDetail->id = $acId_inter;
					$this->ProjectBudgetInternalDetail->saveField('activity_id', $activity_id);
				}
			}
			
			// Update activity_id linked in project budget external
			$acId_external = $this->ProjectBudgetExternal->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                ),
                'fields' => array('id', 'id')
            ));
			if(!empty($acId_external)){
				foreach($acId_external as $acId_exter){
					$this->ProjectBudgetExternal->id = $acId_exter;
					$this->ProjectBudgetExternal->saveField('activity_id', $activity_id);
				}
			}
			
			// Update activity_id linked in project budget sales
			$acId_sale = $this->ProjectBudgetSale->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_id' => $project_id,
                ),
                'fields' => array('id', 'id')
            ));
			if(!empty($acId_sale)){
				foreach($acId_sale as $acId_sl){
					$this->ProjectBudgetSale->id = $acId_sl;
					$this->ProjectBudgetSale->saveField('activity_id', $activity_id);
				}
			}
			// Update activity_id linked in ProjectBudgetSyn table
			$project_budget_sysn = $this->ProjectBudgetSyn->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'project_id' => $project_id
				),
				'fields' => array('id')
			));
			$project_budget_sysn_id = !empty($project_budget_sysn) ? $project_budget_sysn['ProjectBudgetSyn']['id'] : '';
			if(!empty($project_budget_sysn_id)){
				$this->ProjectBudgetSyn->id = $project_budget_sysn_id;
				$this->ProjectBudgetSyn->saveField('activity_id', $activity_id);
			}
		
	}
	
	/* Function emptyActivityExport
	*  truncate tmp_module_activity_exports
	*/ 
	public function emptyActivityExport(){
		$employee = $this->employee_info;
		if( !$employee['Employee']['is_sas']) die('Access Denied!');
		$db = ConnectionManager::getDataSource('default');
		$sql = 'truncate table `tmp_module_activity_exports`';
		$result = $db->query($sql);
		if($result) echo "OK</br>";
		else echo "Failed</br>";
		echo $result;
		exit;
	}
	
	
	
	/* Function update_login_history */ 
	/* 
	* Old value: https://prod1.my-azuree.com/project_tasks/index/2117?hl=fr
	* New value:                            /project_tasks/index/2117?hl=fr
	* Chay duoc cho tat ca cac site
	*/ 
	
	public function update_login_history(){
		// check permission
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		
		// get all value
		$this->loadModel('HistoryFilter');
		$all_data = $this->HistoryFilter->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'rollback_url_employee_when_login'
			),
			'fields' => array('id', 'params'),
		));
		$result = array();
		$count = 0;	
		$new_data = array();
		
		// update
		if( !empty( $all_data)){
			foreach ( $all_data as $id => $data){
				$_url = parse_url($data);
				$_host = !empty( $_url['host']) ? $_url['host'] : '';
				$_port = !empty($_url['port']) ? ( ':' . $_url['port'] ) : '' ;
				$_host = $_host.$_port;
				$data = (!empty($_host) &&  (strpos($_host, $data) != -1) ) ?  explode( $_host, $data ) : array();
				if( !empty( $data[1]) ){
					$data = $data[1];
					$this->HistoryFilter->id = $id;
					$result[] = $this->HistoryFilter->saveField('params', $data);
					$count++;
				}
			}
		}
		
		// show result 
		die( json_encode( array(
			'result' => !empty( $result) ? 'sucsess' : 'failed', 
			'count' => $count,
			'data' => $result,
		)));
	}
    public function deleteDuplicatePM($return = false){
		// Remove this function after run 
		// exit;
		// Checkrole 
		$employee_info = $this->employee_info;
		// if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModel('ProjectEmployeeManager');
		$list_pm_ids = $this->ProjectEmployeeManager->find('list', array(
			'fields' => array('ProjectEmployeeManager.id', 'max(ProjectEmployeeManager.id)'),
			'group' => array(
				'project_manager_id', 'activity_id', 'project_id', 'type', 'is_profit_center'
			),
			'order' => array('id'),
		));
		
		
		$list_pm_ids = array_keys($list_pm_ids);
		if( !empty($list_pm_ids)){
			$this->ProjectEmployeeManager->deleteAll(array(
				'ProjectEmployeeManager.id not' => $list_pm_ids,
			), false);
		}
		if( $return)  return count($list_pm_ids);
		die('OK! '. count($list_pm_ids) . ' record saved!');
		exit;
	}
    public function deleteDuplicatePublicKey($return = false){
		// Remove this function after run 
		// exit;
		// Checkrole 
		$employee_info = $this->employee_info;
		if( !$this->isAdminSAS) die('No permission!');
		$this->loadModel('CompanyConfig');
		$listPublicKeys = $this->CompanyConfig->find('list', array(
			'fields' => array('CompanyConfig.cf_value', 'CompanyConfig.id'),
			'conditions' => array(
				'cf_name' => 'company_public_key',
			),
			'order' => array('id'=>'desc'),
		));
		$listPublicKeys = array_values($listPublicKeys);
		if( !empty($listPublicKeys)){
			$this->CompanyConfig->deleteAll(array(
				'cf_name' => 'company_public_key',
				'NOT' => array('CompanyConfig.id' => $listPublicKeys),
			), false);
		}
		if( $return)  return count($listPublicKeys);
		die('OK! '. count($listPublicKeys) . ' record saved!');
		exit;
	}
	/*
	public function renderAvatar($company_id = null, $overwrite = true){
		// debug( IMAGETYPE_PNG); exit;
		// $employee_info = $this->employee_info;
		// if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		// $this->loadModel('Employee');
		$cond = array();
		set_time_limit(300);
		ignore_user_abort(true);
		if( $company_id) $cond['company_id'] = $company_id ;
		$list_employees = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => $cond,
			'fields' => array('id', 'company_id', 'first_name', 'last_name', 'avatar_color', 'avatar'),
		));
		$count = 0;
		// debug( $list_employees); exit;
		if( !empty( $list_employees )){
			foreach( $list_employees as $employee){
				$count += $this->renderEmployeeAvatar($employee, $overwrite);
			}
		}
		die($count . ' avatar was created');
	}
	private function renderEmployeeAvatar($employee, $overwrite = false){
		if( empty( $employee['Employee']['company_id']) ) return 100;
		$company_id = $employee['Employee']['company_id'];
		$employee_id = $employee['Employee']['id'];
		$avatar = $employee['Employee']['avatar'];
		$company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
		$av_path = '';
		if( !empty ($avatar)) $av_path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS . $avatar;
		if( !file_exists($av_path)) {
			$av_path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS . $employee_id . '_avatar.png';
			$path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS;
		}
		$count = 0;
		if( !file_exists($av_path) || ($overwrite && empty($avatar)) ){ // draw image
			if( !IMG_PNG ) return 0;
			$avatar_color = !empty( $employee['Employee']['avatar_color']) ? $employee['Employee']['avatar_color'] : '#6dabd4';
			$first_name = $employee['Employee']['first_name'];
			$last_name = $employee['Employee']['last_name'];
			$name_avatar = strtoupper( substr($first_name,0,1).substr($last_name,0,1) );
			if( empty( $name_avatar ) ) $name_avatar = 'AV';
			// header('Content-Type: image/png');
			foreach( array( 'avatar', 'avatar_resize') as $type){
				$result = '';
				list( $w, $h) = array( 40, 40);
				$font_size = 14;
				if( $type == 'avatar') {
					list( $w, $h) = array( 200, 200); 
					$font_size = 72; 
				}
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
					if (!file_exists($path)) {
						mkdir($path , 0777, true);
					}
					$result = imagepng($im, $path . $employee_id . '_' . $type . '.png', 3);
					$count++;
				}catch (Exception $ex){
					
				}
				imagedestroy($im);
			}
		}
		return $count;
	}
	*/
	public function change_chmode($file=null, $mode=null){
		$employee = $this->employee_info;
		if( !$this->isAdminSAS) die('No permission!');
		if( !empty($_GET['file'])){
			$file = $_GET['file'];
		}
		if( !empty($_GET['mode'])){
			$mode = $_GET['mode'];
		}
		$mode = octdec($mode);
		echo $file . '  ' . $mode;
		echo '<br>';
		if( empty($_GET['file'])){
			return chmod($file, $mode);
		}
		if( chmod($file, $mode)){
			echo 'OK';
		}else{
			echo 'Failed';
		}
		echo '<br>';
		$this->get_file_info($file);
	}
	public function get_file_info($file=null){
		// debug( 1); exit;
		$employee = $this->employee_info;
		if( !$this->isAdminSAS) die('No permission!');
		echo 'Current user is: "' . get_current_user() . '" <br>';
		if( empty($file) )$file = IMAGES . 'avatar' . DS;
		if( !empty($_GET['file'])){
			$file = $_GET['file'];
		}
		// debug( $file);
		$owner = fileowner($file);
		echo 'fileowner ID is: ' . $owner .'<br>';
		if( function_exists('posix_getpwuid')) print_r(posix_getpwuid(fileowner($file)));
		echo '<br>';
		$perms = fileperms($file);
		echo "Info for {$file}</br>";
		echo "Permission: " .decoct($perms & 0777).'<br>'; // return "755" for example
		switch ($perms & 0xF000) {
			case 0xC000: // socket
				$info = 'socket: s';
				break;
			case 0xA000: // symbolic link
				$info = 'symbolic link: l';
				break;
			case 0x8000: // regular
				$info = 'regular: r';
				break;
			case 0x6000: // block special
				$info = 'block special: b';
				break;
			case 0x4000: // directory
				$info = 'directory: d';
				break;
			case 0x2000: // character special
				$info = 'character special: c';
				break;
			case 0x1000: // FIFO pipe
				$info = 'FIFO pipe: p';
				break;
			default: // unknown
				$info = 'unknown: u';
		}
		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));

		echo $info;
		echo "<br>Last modified: " . date ("Y-m-d H:i:s.", filemtime($file));
		if( !empty($_GET['print']) && is_file($file)){
			$content = htmlspecialchars(file_get_contents($file, false));
			echo '<div style="background: #eee; color:#111; overflow:auto; height: calc( 100vh - 150px);">';
			echo '<pre>';
			echo $content;
			echo '</pre>';
			echo '</div>';
		}
		die;
		
	}
	public function generateAvatar($company_id = null, $overwrite = true){
		// debug( IMAGETYPE_PNG); exit;
		// $employee_info = $this->employee_info;
		// if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		// $this->loadModel('Employee');
		$cond = array();
		set_time_limit(300);
		ignore_user_abort(true);
		if( $company_id) $cond['company_id'] = $company_id ;
		$list_employees = $this->Employee->find('all', array(
			'recursive' => -1,
			'conditions' => $cond,
			'fields' => array('id', 'company_id', 'first_name', 'last_name', 'avatar_color', 'avatar'),
		));
		$count = 0;
		// debug( $list_employees); exit;
		if( !empty( $list_employees )){
			foreach( $list_employees as $employee){
				
				$count += $this->generateEmployeeAvatar($employee, $overwrite);
			}
		}
		die($count . ' avatar was created');
	}
	private function generateEmployeeAvatar($employee, $overwrite = false){
		$company_id = $employee['Employee']['company_id'];
		$employee_id = $employee['Employee']['id'];
		$avatar = $employee['Employee']['avatar'];
		$company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
		$old_path = '';
		if( !empty ($avatar) && !empty($company_id)) $old_path = FILES . 'avatar_employ' . DS . $company['Company']['dir'] . DS . $employee_id . DS . $avatar;
		// $path = IMAGES . DS . $company_id . DS . $employee_id . DS;
		$new_path = IMAGES . 'avatar' . DS ;
		if (!file_exists($new_path )) {
			mkdir($new_path , 0777, true);
		} 
		if( !is_writable($new_path) ){
			die( $new_path . ' is not writable');
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
			$avatar_color = !empty( $employee['Employee']['avatar_color']) ? $employee['Employee']['avatar_color'] : '#6dabd4';
			$first_name = $employee['Employee']['first_name'];
			$last_name = $employee['Employee']['last_name'];
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
					$result = imagepng($im, $new_path . $employee_id . $type . '.png', 3);
					if( $result) $count++;
					else echo 'Error when save image to folder "'.$new_path .'" employee_id : ' . $employee_id . PHP_EOL;
				}catch (Exception $ex){
					
				}
				imagedestroy($im);
			}
		}
		return $count;
	}
	
	public function deleteAttachmentFileOfTaskNotExists(){
		$this->loadModels('ProjectTaskAttachment', 'ProjectTask', 'ProjectTaskTxt', 'ProjectTaskTxtRefer',  'ProjectTaskAttachmentView');
		// Get all tasks
		$projectTasks = $this->ProjectTask->find('list', array(
            'fields' => array('id')
        ));
		
		// Get all comment of task not exists.
		echo('Task comment of task not exists: deleting....');
		$taskComment = $this->ProjectTaskTxt->find('list', array(
			'conditions' => array(
				'project_task_id NOT' => $projectTasks,
			),
            'fields' => array('id')
        ));
		$this->ProjectTaskTxt->deleteAll(array('ProjectTaskTxt.id' => $taskComment), false);
		echo('Task comment of task not exists: Deleted');
		
		// Get all status of task comment of task not exists.
		echo('Status of task comment of task not exists: deleting....');
		$statusTaskComment = $this->ProjectTaskTxtRefer->find('list', array(
			'conditions' => array(
				'task_id NOT' => $projectTasks,
			),
            'fields' => array('id')
        ));
		$this->ProjectTaskTxtRefer->deleteAll(array('ProjectTaskTxtRefer.id' => $statusTaskComment), false);
		echo('Status of task comment of task not exists: Deleted');
		
		// Get all task attachment and status attachment of task not exists.
		echo('Task attachment and status of task attachment of task not exists: deleting....');
		
		// Get file of task not etxists
		$fileAttachmentOfTaskNotExists = $this->ProjectTaskAttachment->find('list', array(
			'conditions' => array(
				'task_id NOT' => $projectTasks,
			),
            'fields' => array('task_id')
        ));
		foreach($fileAttachmentOfTaskNotExists as $key => $task_id){
			$this->requestAction('project_tasks/delete_attachment/', array('pass' => array($task_id, false)));
		}
		echo('Task attachment and status of task attachment of task not exists: Deleted');
		
		exit;
	}
	
	public function getAttachmentFileOfTaskNotExists(){
		// $url=Router::url();
// $url_arr=explode("/",$url);
		// debug($url);
		// exit;
		$this->loadModels('ProjectTaskAttachment', 'ProjectTask');
		// Get all tasks
		$projectTasks = $this->ProjectTask->find('list', array(
            'fields' => array('id')
        ));
		// Get file of task not etxists
		$fileAttachmentOfTaskNotExists = $this->ProjectTaskAttachment->find('all', array(
			'conditions' => array(
				'task_id NOT' => $projectTasks,
			),
            'fields' => array('id','project_id','task_id','attachment', 'is_file', 'is_https')
        ));
		$api_key = $this->employee_info['Employee']['api_key'];
		// Get link attachment file
		foreach($fileAttachmentOfTaskNotExists as $key => $taskAttachments){
			$attachment = $taskAttachments['ProjectTaskAttachment'];
			if($attachment['is_file'] == 1){
				$link = '/kanban/attachment/' . $attachment['id'] . '/?sid='. $api_key;
				$project_id = $attachment['project_id'];
				$task_id = $attachment['task_id'];
				$attachment_name =  $attachment['attachment'];
				echo nl2br ("\n Project ID: ". $project_id . ",Task ID: ". $task_id . ", Task name: ".$attachment_name);
				echo nl2br ("\n Attachment file: ". $link);
				echo nl2br ("\n -------------");
			}
		}
		exit;
	}
	
	private function _getPath($task_id) {
        $company = $this->employee_info['Company']['id'];
        $path = FILES . 'projects' . DS . 'project_tasks' . DS . $company . DS;
        return $path;
    }
	
	public function checkNull(){
		$this->loadModels('Project','Activity','ProjectTask');
		$countProject = $this->Project->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'project_name is NULL'
			)
		));
		$countTask = $this->ProjectTask->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'task_title is NULL'
			)
		));
		$countActivity = $this->Activity->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'name' => '',
				'long_name is NULL',
				'short_name is NULL'
			)
		));
		echo 'Have '.$countProject.' null projects';
		echo '<br>Have '.$countTask.' null tasks';
		echo '<br>Have '.$countActivity.' null activities';
		exit;
	}
	
	public function deleteValueNullAllCompany(){
		$this->loadModels('Project','Activity','ProjectTask');
		$countProject = $this->Project->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'project_name is NULL'
			)
		));
		$countTask = $this->ProjectTask->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'task_title is NULL'
			)
		));
		$countActivity = $this->Activity->find('count', array(
			'recursive' => -1,
			'field' => array('id'),
			'conditions' => array(
				'name' => '',
				'long_name is NULL',
				'short_name is NULL'
			)
		));
		$resProject = $this->Project->deleteAll('Project.project_name is null');
		$resTask = $this->ProjectTask->deleteAll('ProjectTask.task_title is NULL');
		$resActivity = $this->Activity->deleteAll(array('Activity.name' => '', 'Activity.long_name is NULL', 'Activity.short_name is NULL'));
	
		echo 'Deleted '.$countProject.' null projects';
		echo '<br>Deleted '.$countTask.' null tasks';
		echo '<br>Deleted '.$countActivity.' null activities';
		echo '<br>Finish!';
        exit;
	}
	function deleteTimesheetValidatedZeroValue(){
		set_time_limit(0);
		ignore_user_abort(true);
		$this->loadModels('ActivityRequest');
		$activityRequest = $this->ActivityRequest->find('list', array(
			'conditions' => array(
				'status' => 2,
				'value' => 0,
			),
			'fields' => array('id'),
		));
		$count = $total = 0;
		if(!empty($activityRequest)){
			$total = count($activityRequest);
			$activityRequest = array_values($activityRequest);
			$activityRequest = array_chunk($activityRequest, 5000);
			foreach( $activityRequest as $records){
				$res = $this->ActivityRequest->deleteAll(array('ActivityRequest.id' => $records), false);
				if( $res) $count += count($records);
			}
		}
		echo $count . '/' . $total . ' record(s) was deleted';
		die();
	}
	
	/* Ticket 550 Delete old unused tasks */
	function deleteOldTask($isCount = 1, $company_id = null, $year = 2018){		
		set_time_limit(0);
		ignore_user_abort(true);
		$start_time = time();
		$employee_info = $this->employee_info;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$oldDate = intval( empty($year)?2018:$year ).'-12-31';
		$this->loadModels('ProjectTask', 'ActivityTask', 'ActivityRequest');
		if( empty( $company_id)){
			$listOldTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'or' => array(
						'estimated' => 0,
						'estimated is null',
					),
					'task_end_date <= ' => $oldDate
				),
				'fields' => array('id', 'is_nct')
			));
		}else{
			$listOldTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array('Project.id = ProjectTask.project_id')
					),
				),
				'conditions' => array(
					'or' => array(
						'ProjectTask.estimated' => 0,
						'ProjectTask.estimated is null',
					),
					'ProjectTask.task_end_date <= ' => $oldDate,
					'Project.company_id' => $company_id
				),
				'fields' => array('ProjectTask.id', 'ProjectTask.is_nct')
			));
		}
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $listOldTask
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $task_id){
				unset( $listOldTask[$task_id]);
			}
		}
		$listNCTtask = array_filter($listOldTask);
		// debug( $listOldTask);
		// debug( $listNCTtask); exit;
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($listOldTask)
			),
			'fields' => array('project_task_id', 'id')
		)); //'project_task_id' => 'activity_task_id'
		
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id'),
		));
		$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		// debug( $listActivityTasksLinked );
		// foreach( $listActivityTasksLinked as $t=> $at){
			// echo $at.',';
		// }
		$listOldTask = array_keys( $listActivityTasksLinked);
		$count = 0;
		if( !empty($listActivityTasksLinked)){
			$this->loadModels('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer', 'ProjectTaskEmployeeRefer', 'ProjectTaskTxt', 'ActivityRequest', 'ActivityTaskEmployeeRefer', 'ActivityTask', 'NctWorkload');
			/* Delete table linked to project task */
			$projectTaskModels = array('ProjectTaskEmployeeRefer', 'ProjectTaskTxt');
			foreach( $projectTaskModels as $Model){
				if( $isCount) $count += $this->$Model->find('count', array(
					'conditions' => array(
						'project_task_id' => array_keys( $listActivityTasksLinked)
					)
				));
				$this->$Model->deleteAll(array(
					'project_task_id' => array_keys( $listActivityTasksLinked)
				), false);
			}
			$projectTaskModels = array('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer');
			foreach( $projectTaskModels as $Model){
				if( $isCount) $count += $this->$Model->find('count', array(
					'conditions' => array(
						'task_id' => array_keys( $listActivityTasksLinked)
					)
				));
				$this->$Model->deleteAll(array(
					'task_id' => array_keys( $listActivityTasksLinked)
				), false);
			}
			if( $isCount) $count += count($listActivityTasksLinked);
			$this->ProjectTask->deleteAll(array(
				'ProjectTask.id' => array_keys( $listActivityTasksLinked)
			), false);
			if( $isCount) $count += $this->NctWorkload->find('count', array(
				'conditions' => array(
					'NctWorkload.project_task_id' => array_keys( $listNCTtask)
				)
			));
			$this->NctWorkload->deleteAll(array(
				'NctWorkload.project_task_id' => array_keys( $listNCTtask)
			), false);
			
			if( $isCount) $count += $this->NctWorkload->find('count', array(
				'conditions' => array(
					'NctWorkload.estimated' => 0
				)
			));
			$this->NctWorkload->deleteAll(array(
				'NctWorkload.estimated' => 0
			), false);
			
			/* END Delete table linked to project task */
			
			/* Delete table linked to Activity task */
			if( $isCount) $count += $this->ActivityRequest->find('count', array(
				'conditions' => array(
					'task_id' => array_values( $listActivityTasksLinked)
				)
			));
			$this->ActivityRequest->deleteAll(array(
				'task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			if( $isCount) $count += $this->ActivityTaskEmployeeRefer->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'activity_task_id' => array_values( $listActivityTasksLinked)
				)
			));
			$this->ActivityTaskEmployeeRefer->deleteAll(array(
				'activity_task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			if( $isCount) $count += count($listActivityTasksLinked);
			$this->ActivityTask->deleteAll(array(
				'ActivityTask.id' => array_values( $listActivityTasksLinked)
			), false);
			/* Delete table linked to Activity task */
		}
		if( $isCount) echo $count.' record(s) was deleted. ';
		else echo 'Complete. ';
		echo 'Load time: ' . number_format( (time() - $start_time), 2) . 's'; 
		exit;
	}
	/* Ticket END 550 Delete old unused tasks */
	function updateProjectFunction($part = 0){
		set_time_limit(0);
		ignore_user_abort(true);
		$START = time();
		$this->loadModels('Company', 'Project', 'ProjectTask');
		$projects = $this->Project->find('list', array(
			'conditions' => array('project_name is not null'),
			'fields' => array('id', 'project_name'),
		));
		/* get list assigned by project_id
		*/
		$project_part = array();
		$i = 0;
		if( !empty($part) && count($projects) >= 100*($part-1)){
			foreach( $projects as $pid => $pname){
				if( (($part-1)*100 <= $i) &&  ($i < ($part * 100)) ){
					$project_part[$pid] = $pname;
				}
				$i++;
			}
			$projects = $project_part;
		}	
		// debug( count($projects)); exit;		
		$listed = array();
		$listAllAssiged = $this->ProjectTask->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'ProjectTask.project_id' => array_keys($projects)
            ),
            'joins' => array(
                array(
                    'table' => 'project_task_employee_refers',
                    'alias' => 'ProjectTaskEmployeeRefer',
                    'type' => 'left',
                    'conditions' => array('ProjectTaskEmployeeRefer.project_task_id = ProjectTask.id')
                )
            ),
            'fields' => array('ProjectTask.project_id', 'ProjectTaskEmployeeRefer.reference_id', 'ProjectTaskEmployeeRefer.is_profit_center')
        ));
		foreach( $listAllAssiged as $assigned){
			$pid = $assigned['ProjectTask']['project_id'];
			if( empty($employeeByProject[$pid])) $employeeByProject[$pid] = array();
			if( empty($listed[$pid])) $listed[$pid] = array();
			if( empty($assigned['ProjectTaskEmployeeRefer']['reference_id'])) continue;
			if( $assigned['ProjectTaskEmployeeRefer']['is_profit_center'] == '' ) continue;
			$emp = $assigned['ProjectTaskEmployeeRefer']['reference_id'].'-'.$assigned['ProjectTaskEmployeeRefer']['is_profit_center'];
			if( !in_array($emp, $listed[$pid])){
				$employeeByProject[$pid][] = $assigned['ProjectTaskEmployeeRefer'];
				$listed[$pid][] = $emp;
			}
		}
		
		/* END get list assigned by project_id
		*/
		$count = 0;
		foreach($employeeByProject as $pid => $list_empl_assign){
			if( $this->updateEmployeeAssignedToTeam($pid, $list_empl_assign))
				$count++;
			
		}
		echo PHP_EOL;
		echo $count . ' project(s) updated within: ' . (time() - $START + 1) . 's';
		exit;
	}
	/*Ticket 489. Get data loi khi xoa employee la PM nhung khong xoa cac record trong bang nay.*/
	function getDataErrorInProjectEmployeeManager(){
		set_time_limit(0);
		ignore_user_abort(true);
		$this->loadModels('ProjectEmployeeManager', 'Employee', 'ActionLog');
		$listEmployeeIds = $this->Employee->find('list', array(
			'recursive' => -1,
			'fields' => 'id'
		));
		$listRecordIssues = $this->ProjectEmployeeManager->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectEmployeeManager.project_manager_id NOT' => $listEmployeeIds
			),
			'fields' => array('project_id','project_manager_id','type'),
		));
		echo 'Have '.count($listRecordIssues).' record error.'.'<br>';
		echo 'List employee id not empty in table Employee<br>';
		foreach($listRecordIssues as $key => $listRecordIssue){
			echo $listRecordIssue['ProjectEmployeeManager']['project_manager_id'].'<br>';
		}
		echo '<br>Finish!';
        exit;
	}
	function deleteDataErrorInProjectEmployeeManager(){
		set_time_limit(0);
		ignore_user_abort(true);
		$this->loadModels('ProjectEmployeeManager', 'Employee', 'ActionLog');
		$listEmployeeIds = $this->Employee->find('list', array(
			'recursive' => -1,
			'fields' => 'id'
		));
		$listRecordIssues = $this->ProjectEmployeeManager->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectEmployeeManager.project_manager_id NOT' => $listEmployeeIds
			),
			'fields' => array('project_manager_id'),
		));
		$count = $total = 0;
		if(!empty($listRecordIssues)){
			$total = count($listRecordIssues);
			foreach($listRecordIssues as $key => $listRecordIssue){
				$res = $this->ProjectEmployeeManager->deleteAll(array(
					'ProjectEmployeeManager.project_manager_id' => $listRecordIssue
				),false);
				$count++;
			}
		}
		echo $count . '/' . $total . ' record(s) was deleted';
		echo '<br>Finish!';
        exit;
	}
	//delete data error bang thuoc User View
	function getDataErrorInUserView(){
		set_time_limit(0);
		ignore_user_abort(true);
		$this->loadModels('UserDefaultView', 'UserLastUpdated', 'UserStatusView', 'UserView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale', 'ProjectTaskTxtRefer', 'ProjectTaskAttachmentView', 'ProjectFinancePlusAttachmentView', 'ActivityRequestCopy', 'Employee');
		$listEmployeeIds = $this->Employee->find('list', array(
			'recursive' => -1,
			'fields' => 'id'
		));
		$list1 = $this->UserDefaultView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserDefaultView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		// debug($list1);exit;
		$list2 = $this->UserLastUpdated->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserLastUpdated.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list3 = $this->UserStatusView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list4 = $this->UserView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list5 = $this->UserStatusViewActivity->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewActivity.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list6 = $this->UserStatusViewSaleDeal->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewSaleDeal.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list7 = $this->UserStatusViewSale->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewSale.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list8 = $this->ProjectTaskTxtRefer->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTaskTxtRefer.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list9 = $this->ProjectTaskAttachmentView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTaskAttachmentView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list10 = $this->ProjectFinancePlusAttachmentView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectFinancePlusAttachmentView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list11 = $this->ActivityRequestCopy->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ActivityRequestCopy.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list1 = array_unique($list1);
		$list2 = array_unique($list2);
		$list3 = array_unique($list3);
		$list4 = array_unique($list4);
		$list5 = array_unique($list5);
		$list6 = array_unique($list6);
		$list7 = array_unique($list7);
		$list8 = array_unique($list8);
		$list9 = array_unique($list9);
		$list10 = array_unique($list10);
		$list11 = array_unique($list11);
		echo 'Have '.count($list1).' record error in table UserDefaultView.'.'<br>';
		echo 'Have '.count($list2).' record error in table UserLastUpdated.'.'<br>';
		echo 'Have '.count($list3).' record error in table UserStatusView.'.'<br>';
		echo 'Have '.count($list4).' record error in table UserView.'.'<br>';
		echo 'Have '.count($list5).' record error in table UserStatusViewActivity.'.'<br>';
		echo 'Have '.count($list6).' record error in table UserStatusViewSaleDeal.'.'<br>';
		echo 'Have '.count($list7).' record error in table UserStatusViewSale.'.'<br>';
		echo 'Have '.count($list8).' record error in table ProjectTaskTxtRefer.'.'<br>';
		echo 'Have '.count($list9).' record error in table ProjectTaskAttachmentView.'.'<br>';
		echo 'Have '.count($list10).' record error in table ProjectFinancePlusAttachmentView.'.'<br>';
		echo 'Have '.count($list11).' record error in table ActivityRequestCopy.'.'<br>';
		echo '<br>Finish!';
        exit;
	}
	function deleteDataErrorInUserView(){
		set_time_limit(0);
		ignore_user_abort(true);
		$this->loadModels('UserDefaultView', 'UserLastUpdated', 'UserStatusView', 'UserView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale', 'ProjectTaskTxtRefer', 'ProjectTaskAttachmentView', 'ProjectFinancePlusAttachmentView', 'ActivityRequestCopy', 'Employee');
		$listEmployeeIds = $this->Employee->find('list', array(
			'recursive' => -1,
			'fields' => 'id'
		));
		$list1 = $this->UserDefaultView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserDefaultView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		// debug($list1);exit;
		$list2 = $this->UserLastUpdated->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserLastUpdated.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list3 = $this->UserStatusView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list4 = $this->UserView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list5 = $this->UserStatusViewActivity->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewActivity.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list6 = $this->UserStatusViewSaleDeal->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewSaleDeal.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list7 = $this->UserStatusViewSale->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'UserStatusViewSale.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list8 = $this->ProjectTaskTxtRefer->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTaskTxtRefer.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list9 = $this->ProjectTaskAttachmentView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTaskAttachmentView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list10 = $this->ProjectFinancePlusAttachmentView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectFinancePlusAttachmentView.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list11 = $this->ActivityRequestCopy->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ActivityRequestCopy.employee_id NOT' => $listEmployeeIds
			),
			'fields' => array('employee_id'),
		));
		$list1 = array_unique($list1);
		$list2 = array_unique($list2);
		$list3 = array_unique($list3);
		$list4 = array_unique($list4);
		$list5 = array_unique($list5);
		$list6 = array_unique($list6);
		$list7 = array_unique($list7);
		$list8 = array_unique($list8);
		$list9 = array_unique($list9);
		$list10 = array_unique($list10);
		$list11 = array_unique($list11);
		$count1 = $count2 = $count3 = $count4 = $count5 = $count6 = $count7 = $count8 = $count9 = $count10 = $count11 = 0;
		$total1 = $total2 = $total3 = $total4 = $total5 = $total6 = $total7 = $total8 = $total9 = $total10 = $total11 = 0;
		if(!empty($list1)){
			$total1 = count($list1);
			foreach($list1 as $key => $list){
				$this->UserDefaultView->deleteAll(array('UserDefaultView.employee_id' => $list),false);
				$count1++;
			}
			echo $count1 . '/' . $total1 . ' record(s) was deleted in table UserDefaultView'.'<br>';
		}
		if(!empty($list2)){
			$total2 = count($list2);
			foreach($list2 as $key => $list){
				$this->UserLastUpdated->deleteAll(array('UserLastUpdated.employee_id' => $list),false);
				$count2++;
			}
			echo $count2 . '/' . $total2 . ' record(s) was deleted in table UserLastUpdated'.'<br>';
		}
		if(!empty($list3)){
			$total3 = count($list3);
			foreach($list3 as $key => $list){
				$this->UserStatusView->deleteAll(array('UserStatusView.employee_id' => $list),false);
				$count3++;
			}
			echo $count3 . '/' . $total3 . ' record(s) was deleted in table UserStatusView'.'<br>';
		}
		if(!empty($list4)){
			$total4 = count($list4);
			foreach($list4 as $key => $list){
				$this->UserView->deleteAll(array('UserView.employee_id' => $list),false);
				$count4++;
			}
			echo $count4 . '/' . $total4 . ' record(s) was deleted in table UserView'.'<br>';
		}
		if(!empty($list5)){
			$total5 = count($list5);
			foreach($list5 as $key => $list){
				$this->UserStatusViewActivity->deleteAll(array('UserStatusViewActivity.employee_id' => $list),false);
				$count5++;
			}
			echo $count5 . '/' . $total5 . ' record(s) was deleted in table UserStatusViewActivity'.'<br>';
		}
		if(!empty($list6)){
			$total6 = count($list6);
			foreach($list6 as $key => $list){
				$this->UserStatusViewSaleDeal->deleteAll(array('UserStatusViewSaleDeal.employee_id' => $list),false);
				$count6++;
			}
			echo $count6 . '/' . $total6 . ' record(s) was deleted in table UserStatusViewSaleDeal'.'<br>';
		}
		if(!empty($list7)){
			$total7 = count($list7);
			foreach($list7 as $key => $list){
				$this->UserStatusViewSale->deleteAll(array('UserStatusViewSale.employee_id' => $list),false);
				$count7++;
			}
			echo $count7 . '/' . $total7 . ' record(s) was deleted in table UserStatusViewSale'.'<br>';
		}
		if(!empty($list8)){
			$total8 = count($list8);
			foreach($list8 as $key => $list){
				$this->ProjectTaskTxtRefer->deleteAll(array('ProjectTaskTxtRefer.employee_id' => $list),false);
				$count8++;
			}
			echo $count8 . '/' . $total8 . ' record(s) was deleted in table ProjectTaskTxtRefer'.'<br>';
		}
		if(!empty($list9)){
			$total9 = count($list9);
			foreach($list9 as $key => $list){
				$this->ProjectTaskAttachmentView->deleteAll(array('ProjectTaskAttachmentView.employee_id' => $list),false);
				$count9++;
			}
			echo $count9 . '/' . $total9 . ' record(s) was deleted in table ProjectTaskAttachmentView'.'<br>';
		}
		if(!empty($list10)){
			$total10 = count($list10);
			foreach($list10 as $key => $list){
				$this->ProjectFinancePlusAttachmentView->deleteAll(array('ProjectFinancePlusAttachmentView.employee_id' => $list),false);
				$count10++;
			}
			echo $count10 . '/' . $total10 . ' record(s) was deleted in table ProjectFinancePlusAttachmentView'.'<br>';
		}
		if(!empty($list11)){
			$total11 = count($list11);
			foreach($list11 as $key => $list){
				$this->ActivityRequestCopy->deleteAll(array('ActivityRequestCopy.employee_id' => $list),false);
				$count11++;
			}
			echo $count11 . '/' . $total11 . ' record(s) was deleted in table ActivityRequestCopy'.'<br>';
		}
		echo 'Finish!';
        exit;
	}
	function viewOldNormalTask(){
		set_time_limit(0);
		ignore_user_abort(true);
		$start_time = time();
		$employee_info = $this->employee_info;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest', 'Company');
		$listIdProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3
			),
			'fields' => array('id')
		));
		$listOldNormalTasks = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'is_nct' => 0,
				'project_id' => $listIdProjects,
				'or' => array(
					'estimated' => 0,
					'estimated is null',
				),
			),
			'fields' => array('id')
		));
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $listOldNormalTasks
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $task_id){
				unset( $listOldNormalTasks[$task_id]);
			}
		}
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($listOldNormalTasks)
			),
			'fields' => array('project_task_id', 'id')
		));
		
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id')
		));
		
		$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		
		// lay name project
		$listIdProjectsSorted = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTask.id' => array_keys( $listActivityTasksLinked)
			),
			'fields' => array('project_id')
		));
		$listIdProjectsSorted = array_unique($listIdProjectsSorted);
		
		$listNameProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.id' => $listIdProjectsSorted
			),
			'fields' => array('id', 'project_name')
		));
		
		$listNameCompanys = $this->Project->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.id' => $listIdProjectsSorted
			),
			'fields' => array('Project.id', 'Company.company_name', 'Project.project_name'),
			'joins' => array(
				array(
					'table' => 'companies',
					'alias' => 'Company',
					'conditions' => array('Company.id = Project.company_id')
				)
			)
		));
		
		$count = !empty($listActivityTasksLinked) ? count($listActivityTasksLinked) : 0;
		$countP = !empty($listNameProjects) ? count($listNameProjects) : 0;
		
		echo 'Have '. $count. ' normal task in all project Archived with Workload = 0, Consumed = 0.'.'<br>';
		echo 'In '. $countP. ' project.'.'<br>';
		if(!empty($listNameCompanys)){
			foreach($listNameCompanys as $id => $name){
				echo 'Company: '.$name['Company']['company_name'].'. Project_id: '.$name['Project']['id'].'. Project_name: '.$name['Project']['project_name'].'<br>';
			}
		}
		echo 'Load time: ' . number_format( (time() - $start_time), 2) . 's'; 
		exit;
	}
	function deleteOldNormalTask(){
		set_time_limit(0);
		ignore_user_abort(true);
		$employee_info = $this->employee_info;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest');
		$listIdProjects = $_POST;
		$listOldNormalTasks = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'is_nct' => 0,
				'project_id' => array_keys($listIdProjects),
				'or' => array(
					'estimated' => 0,
					'estimated is null',
				),
			),
			'fields' => array('id')
		));
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $listOldNormalTasks
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $task_id){
				unset( $listOldNormalTasks[$task_id]);
			}
		}
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($listOldNormalTasks)
			),
			'fields' => array('project_task_id', 'id')
		));
		
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id')
		));
		
		// $listTaskNotLinkActivity = array_diff($listOldNormalTasks, array_keys($listActivityTasksLinked));
		
		$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		
		if( !empty($listActivityTasksLinked)){
			$this->loadModels('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer', 'ProjectTaskEmployeeRefer', 'ProjectTaskTxt', 'ActivityTaskEmployeeRefer', 'NctWorkload');
			
			/* Delete ProjectTaskAttachment */
			$deleteTasks = array_keys( $listActivityTasksLinked);
			$task_companies = $this->ProjectTask->find('list', array(
				'recurisve' => -1,
				'conditions' => array('ProjectTask.id' => $deleteTasks),
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array('Project.id = ProjectTask.project_id')
					)
				),
				'fields' => array('ProjectTask.id', 'ProjectTask.id', 'Project.company_id')
			));
			$attachment = $this->ProjectTaskAttachment->find('list', array(
				'recurisve' => -1,
				'conditions' => array('task_id' => $deleteTasks),
				'fields' => array('id', 'attachment', 'task_id' )
			));
			foreach($task_companies as $comp_id => $tasks){
				$com_path = FILES . 'projects' . DS . 'project_tasks' . DS . $comp_id . DS;
				foreach( $tasks as $task_id){
					if( !empty($attachment[$task_id])){
						foreach ( $attachment[$task_id] as $id => $attt){
							$path = $com_path . $attt;
							if( file_exists( $path )) unlink( $path);
						}
					}
				}
			}
			/* END Delete ProjectTaskAttachment */
			
			/* Delete table linked to project task */
			
			$this->ProjectTaskEmployeeRefer->deleteAll(array(
				'project_task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTaskTxt->deleteAll(array(
				'project_task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTaskAttachmentView->deleteAll(array(
				'task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTaskAttachment->deleteAll(array(
				'task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTaskFavourite->deleteAll(array(
				'task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTaskTxtRefer->deleteAll(array(
				'task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTask->deleteAll(array(
				'ProjectTask.id' => array_keys( $listActivityTasksLinked)
			), false);
			
			/* END Delete table linked to project task */
			
			/* Delete table linked to Activity task */
			
			$this->ActivityRequest->deleteAll(array(
				'task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			$this->ActivityTaskEmployeeRefer->deleteAll(array(
				'activity_task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			$this->ActivityTask->deleteAll(array(
				'ActivityTask.id' => array_values( $listActivityTasksLinked)
			), false);
			/* Delete table linked to Activity task */
		}
		if(!empty($listIdProjects)) die ('true');
		exit;
	}
	function viewOldNctTask(){
		set_time_limit(0);
		ignore_user_abort(true);
		$start_time = time();
		$employee_info = $this->employee_info;
		$isCount = 1;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest');
		$listIdProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3
			),
			'fields' => array('id')
		));
		$listOldNctTask = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'is_nct' => 1,
				'project_id' => $listIdProjects,
				'or' => array(
					'estimated' => 0,
					'estimated is null',
				),
			),
			'fields' => array('id')
		));
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $listOldNctTask
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $task_id){
				unset( $listOldNctTask[$task_id]);
			}
		}
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($listOldNctTask)
			),
			'fields' => array('project_task_id', 'id')
		));
		
		$listTaskNotLinkActivity = array_diff($listOldNctTask, array_keys($listActivityTasksLinked));
		
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id'),
		));
		$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		$listIdProjectsSorted = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'ProjectTask.id' => array_keys( $listActivityTasksLinked)
			),
			'fields' => array('project_id')
		));
		$listIdProjectsSorted = array_unique($listIdProjectsSorted);
		
		$listNameProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.id' => $listIdProjectsSorted
			),
			'fields' => array('id', 'project_name')
		));
		
		$listNameCompanys = $this->Project->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Project.id' => $listIdProjectsSorted
			),
			'fields' => array('Project.id', 'Company.company_name', 'Project.project_name'),
			'joins' => array(
				array(
					'table' => 'companies',
					'alias' => 'Company',
					'conditions' => array('Company.id = Project.company_id')
				)
			)
		));
		
		$count = !empty($listActivityTasksLinked) ? count($listActivityTasksLinked) : 0;
		$countT = !empty($listTaskNotLinkActivity) ? count($listTaskNotLinkActivity) : 0;
		$countP = !empty($listNameProjects) ? count($listNameProjects) : 0;
		$count = $count + $countT;
		echo 'Have '. $count. ' NCT task in all project Archived with Workload = 0, Consumed = 0.'.'<br>';
		echo 'In '. $countP. ' project.'.'<br>';
		if(!empty($listNameCompanys)){
			foreach($listNameCompanys as $id => $name){
				echo 'Company: '.$name['Company']['company_name'].'. Project_id: '.$name['Project']['id'].'. Project_name: '.$name['Project']['project_name'].'<br>';
			}
		}
		echo 'Load time: ' . number_format( (time() - $start_time), 2) . 's'; 
		exit;
	}
	function deleteOldNctTask(){
		set_time_limit(0);
		ignore_user_abort(true);
		$employee_info = $this->employee_info;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest');
		$listIdProjects = $_POST;
		$listOldNctTask = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'is_nct' => 1,
				'project_id' => array_keys($listIdProjects),
				'or' => array(
					'estimated' => 0,
					'estimated is null',
				),
			),
			'fields' => array('id')
		));
		$tasksHasChild = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'parent_id' => $listOldNctTask
			),
			'fields' => array('parent_id', 'parent_id'),
			'group' => array('parent_id')
		));
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $task_id){
				unset( $listOldNctTask[$task_id]);
			}
		}
		
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => array_keys($listOldNctTask)
			),
			'fields' => array('project_task_id', 'id')
		));
		
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id'),
		));
		$listTaskNotLinkActivity = array_diff($listOldNctTask, array_keys($listActivityTasksLinked));
		$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		if( !empty($listActivityTasksLinked) || !empty($listTaskNotLinkActivity)){
			$this->loadModels('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer', 'ProjectTaskEmployeeRefer', 'ProjectTaskTxt', 'ActivityRequest', 'ActivityTaskEmployeeRefer', 'ActivityTask', 'NctWorkload');
			/* Delete table linked to project task */
			$projectTaskModels = array('ProjectTaskEmployeeRefer', 'ProjectTaskTxt');
			foreach( $projectTaskModels as $Model){
				$this->$Model->deleteAll(array(
					'project_task_id' => array_keys( $listActivityTasksLinked)
				), false);
			}
			$projectTaskModels = array('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer');
			foreach( $projectTaskModels as $Model){
				$this->$Model->deleteAll(array(
					'task_id' => array_keys( $listActivityTasksLinked)
				), false);
			}
			
			/* END Delete table linked to project task */
			
			/* Delete ProjectTaskAttachment */
			$deleteTasks = array_unique( array_merge(array_keys( $listActivityTasksLinked), array_keys( $listTaskNotLinkActivity)));
			$task_companies = $this->ProjectTask->find('list', array(
				'recurisve' => -1,
				'conditions' => array('ProjectTask.id' => $deleteTasks),
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array('Project.id = ProjectTask.project_id')
					)
				),
				'fields' => array('ProjectTask.id', 'ProjectTask.id', 'Project.company_id')
			));
			$attachment = $this->ProjectTaskAttachment->find('list', array(
				'recurisve' => -1,
				'conditions' => array('task_id' => $deleteTasks),
				'fields' => array('id', 'attachment', 'task_id' )
			));
			foreach($task_companies as $comp_id => $tasks){
				$com_path = FILES . 'projects' . DS . 'project_tasks' . DS . $comp_id . DS;
				foreach( $tasks as $task_id){
					if( !empty($attachment[$task_id])){
						foreach ( $attachment[$task_id] as $id => $attt){
							$path = $com_path . $attt;
							if( file_exists( $path )) unlink( $path);
						}
					}
				}
			}
			/* END Delete ProjectTaskAttachment */
			
			/* Delete table linked to Activity task */
			
			$this->ActivityTaskEmployeeRefer->deleteAll(array(
				'activity_task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			$this->ActivityTask->deleteAll(array(
				'ActivityTask.id' => array_values( $listActivityTasksLinked)
			), false);
			
			/* Delete table linked to Activity task */
			
			$this->ActivityRequest->deleteAll(array(
				'task_id' => array_values( $listActivityTasksLinked)
			), false);
			
			$this->NctWorkload->deleteAll(array(
				'NctWorkload.project_task_id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->NctWorkload->deleteAll(array(
				'NctWorkload.project_task_id' => array_keys( $listTaskNotLinkActivity)
			), false);
			
			$this->ProjectTask->deleteAll(array(
				'ProjectTask.id' => array_keys( $listActivityTasksLinked)
			), false);
			
			$this->ProjectTask->deleteAll(array(
				'ProjectTask.id' => array_keys( $listTaskNotLinkActivity)
			), false);
		}
		if(!empty($listIdProjects)) die ('true');
		exit;
	}
	function delete_task_ntc_noworkload_noassigned(){
		$a = time();
		$result = false;
		$message = '';
		$list_task = $this->data['task_ids'];
		// debug( count( $list_task));
		if( !is_array($list_task) || (!$this->employee_info['Employee']['is_sas'])){
			$message = 'Wrong input.';
			die(json_encode(array(
				'result' => $result ? 'success' : 'failed',
				'data' => $list_task,
				'message' => $message
			)));
		}
		$this->loadModels('Project', 'ProjectTask', 'ProjectTaskEmployeeRefer', 'ActivityTask', 'ActivityRequest', 'Company', 'ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer', 'ProjectTaskTxt', 'ActivityTaskEmployeeRefer', 'NctWorkload');
		
/* Check Input */
		$listProjects = $listOldNctTask = $task_nct_has_assigned = array();
		$listProjects = $this->Project->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'category' => 3
			),
			'fields' => array('id', 'project_name')
		));
		if( !empty($listProjects)){
			$listOldNctTask = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'is_nct' => 1,
					'project_id' => array_keys($listProjects),
					'id' => $list_task,
					'or' => array(
						'estimated' => 0,
						'estimated is null',
					),
				),
				'fields' => array('id', 'task_title')
			));
		}
		if( !empty($listOldNctTask)){
			$task_nct_has_assigned = $this->ProjectTaskEmployeeRefer->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'estimated !=' => 0,
					'estimated is not null',
					'project_task_id' => $listOldNctTask
				),
				'fields' => array('project_task_id', 'project_task_id')
			));
			$NCT_tasks_zero = array_diff( $listOldNctTask, $task_nct_has_assigned);
		}
		if( !empty($NCT_tasks_zero)){
			$tasksHasChild = $this->ProjectTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => array_keys($NCT_tasks_zero)
				),
				'fields' => array('parent_id', 'parent_id'),
				'group' => array('parent_id')
			));
		}
		if( !empty($tasksHasChild)){
			foreach( $tasksHasChild as $parent_id){
				unset( $NCT_tasks_zero[$parent_id]);
			}
		}
		$listActivityTasksLinked = $listTaskHasConsumed = $listTaskNotLinkActivity = array();
		if( !empty($NCT_tasks_zero)){
			$listActivityTasksLinked = $this->ActivityTask->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'project_task_id' => $NCT_tasks_zero
				),
				'fields' => array('project_task_id', 'id')
			));
			// debug( count($listActivityTasksLinked));
			$listTaskNotLinkActivity = array_diff_key($NCT_tasks_zero, $listActivityTasksLinked);
		}
		if( !empty($listActivityTasksLinked)){
			$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'status' => 2,
					'value !=' => 0,
					'task_id' => array_values($listActivityTasksLinked)
				),
				'fields' => array('task_id', 'task_id'),
			));
			// Loai bo cac task co consumed
			$listActivityTasksLinked = array_diff( $listActivityTasksLinked, $listTaskHasConsumed);
		}
		$deleteTasks = array_unique( array_merge(array_keys( $listActivityTasksLinked), array_keys( $listTaskNotLinkActivity)));
		$deleteActivityTasks = array_values( $listActivityTasksLinked);
/* END Check Input */
/* Delete NCT task No workload - No Assigned - No Consumed */
		if( !empty($deleteTasks)){ // project_task_id => activity_task_id
			/* Delete table linked to Activity task */
			if( !empty( $deleteActivityTasks)){
				$this->ActivityRequest->deleteAll(array(
					'task_id' => $deleteActivityTasks
				), false);
				$this->ActivityTaskEmployeeRefer->deleteAll(array(
					'activity_task_id' => $deleteActivityTasks
				), false);
				$this->ActivityTask->deleteAll(array(
					'ActivityTask.id' => $deleteActivityTasks
				), false);
			}
			/* END Delete table linked to Activity task */
			/* Delete ProjectTaskAttachment */
			$task_companies = $this->ProjectTask->find('list', array(
				'recurisve' => -1,
				'conditions' => array('ProjectTask.id' => $deleteTasks),
				'joins' => array(
					array(
						'table' => 'projects',
						'alias' => 'Project',
						'conditions' => array('Project.id = ProjectTask.project_id')
					)
				),
				'fields' => array('ProjectTask.id', 'ProjectTask.id', 'Project.company_id')
			));
			$attachment = $this->ProjectTaskAttachment->find('list', array(
				'recurisve' => -1,
				'conditions' => array('task_id' => $deleteTasks),
				'fields' => array('id', 'attachment', 'task_id' )
			));
			foreach($task_companies as $comp_id => $tasks){
				$com_path = FILES . 'projects' . DS . 'project_tasks' . DS . $comp_id . DS;
				foreach( $tasks as $task_id){
					if( !empty($attachment[$task_id])){
						foreach ( $attachment[$task_id] as $id => $attt){
							$path = $com_path . $attt;
							if( file_exists( $path )) unlink( $path);
						}
					}
				}
			}
			/* END Delete ProjectTaskAttachment */			
			
			/* Delete table linked to project task */
			$projectTaskModels = array('ProjectTaskEmployeeRefer', 'ProjectTaskTxt', 'NctWorkload');
			foreach( $projectTaskModels as $Model){
				$this->$Model->deleteAll(array(
					'project_task_id' => $deleteTasks
				), false);
			}
			$projectTaskModels = array('ProjectTaskAttachmentView', 'ProjectTaskAttachment', 'ProjectTaskFavourite', 'ProjectTaskTxtRefer');
			foreach( $projectTaskModels as $Model){
				$this->$Model->deleteAll(array(
					'task_id' => $deleteTasks
				), false);
			}
			$result = $this->ProjectTask->deleteAll(array(
				'ProjectTask.id' => $deleteTasks
			), false);
			/* END Delete table linked to project task */			
		}

/* END Delete NCT task No workload - No Assigned - No Consumed */		
		die(json_encode(array(
			'result' => $result ? 'success' : 'failed',
			'data' => $deleteTasks,
			'message' => $message
		)));
		
	}
	function deleteSubSubNctTask(){
		set_time_limit(0);
		ignore_user_abort(true);
		$employee_info = $this->employee_info;
		if( !$employee_info['Employee']['is_sas']) die('Not permission!');
		$this->loadModels('Project', 'ProjectTask', 'ActivityTask', 'ActivityRequest', 'NctWorkload');
		$listIdProjects = $_POST;
		$listNctTask = $this->ProjectTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'is_nct' => 1,
				'project_id' => array_keys($listIdProjects)
			),
			'fields' => array('id')
		));
		$listActivityTasksLinked = $this->ActivityTask->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'project_task_id' => $listNctTask
			),
			'fields' => array('project_task_id', 'id')
		));
		$listNctTaskNotLinked = array_diff($listNctTask,array_keys($listActivityTasksLinked));
		$listTaskHasConsumed = $this->ActivityRequest->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'status' => 2,
				'value !=' => 0,
				'task_id' => array_values($listActivityTasksLinked)
			),
			'fields' => array('task_id'),
		));
		$listActivityTaskLinkedNotConsumed = array_diff($listActivityTasksLinked,array_values($listTaskHasConsumed));
		$listActivityTaskLinkedHaveConsumed = array_diff($listActivityTasksLinked,$listActivityTaskLinkedNotConsumed);
		$count = 0;
		$count = $this->NctWorkload->find('count', array(
					'conditions' => array(
					'NctWorkload.project_task_id' => array_keys( $listActivityTaskLinkedNotConsumed),
					'or' => array(
						'NctWorkload.estimated' => 0,
						'NctWorkload.estimated is null',
					)
					)
				));
		$this->NctWorkload->deleteAll(array(
			'NctWorkload.project_task_id' => array_keys( $listActivityTaskLinkedNotConsumed),
			'or' => array(
				'NctWorkload.estimated' => 0,
				'NctWorkload.estimated is null',
			)
		), false);
		
		if(!empty($listNctTaskNotLinked)){
			$this->NctWorkload->deleteAll(array(
				'NctWorkload.project_task_id' => $listNctTaskNotLinked,
				'or' => array(
					'NctWorkload.estimated' => 0,
					'NctWorkload.estimated is null',
				),
			), false);
		}
		if(!empty($listIdProjects)) die ('true');
		exit;
	}
	function swapStartEnddateNctTasks(){
		$this->loadModel('NctWorkload');
		$nct_workloads = $this->NctWorkload->find('all', array(
			'recurisve' => -1,
			'conditions' => array(
				'task_date > end_date',
			)
		));
		$nct_workloads = !empty($nct_workloads) ? Set::combine($nct_workloads, '{n}.NctWorkload.id', '{n}.NctWorkload') : array();
		if(!empty($nct_workloads)){
			echo "task_id ----- start_date' ------------------- gropup_date ----------------------- end_date";
			echo ('</br>');
			echo ('</br>');
			foreach($nct_workloads as $id => $value){
				
				$tmp_start_date = (string) $value['end_date'];
				$tmp_end_date = (string) $value['task_date'];
				$group_date = explode('_', $value['group_date']);
				$tmp_group_date = (string)$group_date[0] .'_'. $group_date[2] .'_'.$group_date[1];
				
				$this->NctWorkload->updateAll(
					array(
						'NctWorkload.task_date' => "'".$tmp_start_date."'",
						'NctWorkload.group_date' => "'".$tmp_group_date."'",
						'NctWorkload.end_date' => "'".$tmp_end_date."'",
					),
					array('NctWorkload.id' => $id)
				);
				
				echo "{$value['project_task_id']}------ {$value['task_date']} --------- {$value['group_date']} -------------{$value['end_date']}";
				echo ('</br>');
				
				echo "{$value['project_task_id']}------ {$tmp_start_date} --------- {$tmp_group_date} -------------{$tmp_end_date}";
				echo ('</br>');
				echo ('</br>');
			}
		}else{
			echo "Empty!";
		}
		exit;
	}
	function deleteItemReportNotExist(){
		$this->loadModels('SqlManager', 'SqlManagerEmployee');
		$sqlManager = $this->SqlManager->find("list", array(
			'recursive' => -1,
			'fields' => array('id'),
		));
		if(!empty($sqlManager)){
			$sqlManagerEmployeeNotExist = $this->SqlManagerEmployee->find("list", array(
				'recursive' => -1,
				'conditions' => array(
					 array('NOT' => array('sql_manager_id' => $sqlManager)),
				),
				'fields' => array('id', 'id')
			));
			if(!empty($sqlManagerEmployeeNotExist)){
				if($this->SqlManagerEmployee->deleteAll(array('SqlManagerEmployee.id' => $sqlManagerEmployeeNotExist), false, false)){
					echo "OK!";
				}else{
					echo "Error!";
				}
			}else{
				echo "Empty!";
			}
		}else{
			echo "Empty!";
		}
		exit;
	}
	function deleteProgramNotCompany(){
		$this->loadModels('ProjectAmrProgram', 'Company');
		$listCompanyId = $this->Company->find('list', array(
			'recursive' => -1,
			'fields' => array('id')
		));
		if($this->ProjectAmrProgram->deleteAll(array('ProjectAmrProgram.company_id not' => $listCompanyId
		), false, false)){
			echo "OK!";
		}else{
			echo "Empty!";
		}
		exit;
	}
	function deleteEmployeeEmpty(){
		$this->loadModels('Employee', 'CompanyEmployeeReference');
		$listEmpEmpty = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'or'=>array(
					'email is NULL',
					'email' => ''
				)
			),
			'fields' => array('id')
		));
		$this->Employee->deleteAll(array('Employee.id' => $listEmpEmpty), false);
		$this->CompanyEmployeeReference->deleteAll(array('CompanyEmployeeReference.employee_id' => $listEmpEmpty), false);
		if(!empty($listEmpEmpty)){
			echo "OK!";
		}else{
			echo "Empty!";
		}
		exit;
	}
	function deleteRecordIssueFinancementPlus(){
		$this->loadModel('Translation');
		$page = array('Finance_Operation', 'Finance_Investment', 'Budget_Operation', 'Budget_Investment');
		$original_text = array('Investment Percent (Y)', 'Investment Avancement (Y)', 'Investment Budget (Y)');
		$this->Translation->deleteAll(array(
			'Translation.page' => $page,
			'Translation.original_text' => $original_text
		), false);
		if(!empty($page)){
			echo "OK!";
		}
		exit;
	}
	function deleteRecordErrorVersion(){
		$this->loadModel('Version');
		$this->Version->deleteAll(array(
			'Version.name' => 'V9.0'
		), false);
		echo "OK!";
		exit;
	}
	function deleteRecordErrorTranslation(){
		$this->loadModel('Translation');
		$this->Translation->deleteAll(array(
			'Translation.page' => 'Details',
			'Translation.id >' => 1000
		), false);
		echo "OK!";
		exit;
	}
	//Khi delete company nhung khong xoa data table: company_employee_references
	function deleteDataErrorWhenDeteleCompany(){
		$is_sas = $this->employee_info['Employee']['is_sas'];
		if (!$is_sas) {
			$this->cakeError('error404');
			exit;
		}
		$this->loadModels('Employee');
		$db = ConnectionManager::getDataSource('default');
		$table_company = array(
			'company_employee_references',
		);
		foreach ($table_company as $table) {
			$query = "DELETE FROM $table WHERE `employee_id` not in (SELECT `id` FROM employees )";
			$db->query($query);
		}
		echo "OK!";
		exit;
	}
	function refreshEnddateOfTimesheetConfirm(){
		$this->loadModel('ActivityRequestConfirm');
		$requestConfirm = $this->ActivityRequestConfirm->find('all', array(
			'recursive' => -1,
			'fields' => array('id', 'end', 'employee_id', 'company_id'),
		));
		$requestConfirm = !empty($requestConfirm) ? Set::combine($requestConfirm, '{n}.ActivityRequestConfirm.id', '{n}.ActivityRequestConfirm') : array();
		if(!empty($requestConfirm)){
			
			foreach($requestConfirm as $id => $confirm){
				$end = $confirm['end'];
				if(!empty($end)){
					$end = $confirm['end'];
					$employee_id = $confirm['employee_id'];
					$company_id = $confirm['company_id'];
					if(date('w', $end) == 5){
						$new_end = strtotime('next sunday', $end);
						if(date('w', $new_end) == 0){
							$this->ActivityRequestConfirm->id = $id;
							if($this->ActivityRequestConfirm->saveField('end', $new_end)){
								$text_end = date('w d-m-Y', $end);
								$text_new_end = date('w d-m-Y', $new_end);
								echo "{$employee_id}------ {$company_id} --------- {$text_end} ---- to -----{$text_new_end }";
								echo ('</br>');
								echo ('</br>');
							}
						}
					}
				}
			}
		}
		exit;
	}
	function deleteRequestConfirmDuplicate(){
		// run refreshEnddateOfTimesheetConfirm() before run this action
		$this->loadModel('ActivityRequestConfirm');
		$requestConfirm = $this->ActivityRequestConfirm->find('all', array(
            'recursive' => -1,
			'conditions' => array(
				'end <>' => 0,
			),
			'fields' => array('id', 'start', 'end', 'employee_id','status', 'company_id'),
        ));
		$requestConfirm = !empty($requestConfirm) ? Set::combine($requestConfirm, '{n}.ActivityRequestConfirm.id', '{n}.ActivityRequestConfirm') : array();
		$all_item = array();
		$list_delete = array();
		if(!empty($requestConfirm)){
			foreach($requestConfirm as $id => $confirm){
				$match_key = $confirm['employee_id'] . '_' . $confirm['start'] . '_' . $confirm['end'] . '_' . $confirm['company_id'];
				if(!empty($all_item) && in_array($match_key, $all_item)){
					$list_delete[$id] = $match_key;
				}
				$all_item[$id] = $match_key;
			}
		}
		if(!empty($list_delete)){
			foreach($list_delete as $id => $match_key){
				if($this->ActivityRequestConfirm->delete($id)){
					echo "{$id}------ {$match_key}";
					echo ('</br>');
					echo ('</br>');
				}
			}
		}else{
			echo ('No data duplicate');
		}
		exit;
	}
	function setDefaultPCForEmployee(){
		$this->loadModels('ProjectTeam', 'ProfitCenter', 'Employee', 'ProjectEmployeeProfitFunctionRefer');
		// Sử dụng cùng cách lấy data profit center như màn hình add để lấy được item đầu tiên làm profit default
		$company_id = $this->employee_info['Company']['id'];
		$profitCenters = $this->ProjectTeam->ProfitCenter->generateTreeList(array('company_id' => $company_id), null, null, '--');
		if(!empty($profitCenters)){
			$pc_list = array_keys($profitCenters);
			if(!empty($pc_list)){
				$default_pc_id = $pc_list[0];
				$employee_no_pc = $this->Employee->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'company_id' => $company_id,
						'OR'=> array(
							'profit_center_id IS NULL',
							'profit_center_id NOT' => $pc_list,
						),
					),
					'fields' => array('id', 'id'),
				));
				if(!empty($employee_no_pc)){
					foreach($employee_no_pc as $employee_id){
						$this->Employee->id = $employee_id;
						if($this->Employee->saveField('profit_center_id', $default_pc_id)){
							echo "Employee ID: {$employee_id}------ Profit center ID:  {$default_pc_id}";
							echo ('</br>');
							echo ('</br>');
							$profitFunctionRefer = $this->ProjectEmployeeProfitFunctionRefer->find('list', array(
								'recursive' => -1,
								'conditions' => array(
									'employee_id' => $employee_id,
								),
								'fields' => array('id', 'id'),
							));
							$this->Employee->ProjectEmployeeProfitFunctionRefer->editFunctionEmployee($default_pc_id, $employee_id, $profitFunctionRefer);
						}
					}
				}else{
					echo "No Employee is empty PC";
				}
			
			}
		
		}
		exit;
	}
	//#1167
	public function update_employee_default_view(){
		if( !$this->isAdminSAS) die('No permission!');
		$this->deleteViewNotExist(true);
		$this->loadModels('Employee', 'UserDefaultView', 'UserView', 'CompanyViewDefaults');
		$companies_default_view = $this->CompanyViewDefaults->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'default_view' => 1
			),
			'fields' => array('company_id', 'user_view_id'),
		));
		$employees_has_default = $this->UserDefaultView->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'model' => 'project',
			),
			'fields' => array('employee_id', 'user_view_id'),
		));
		$employees_no_default = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id is not null',
				"NOT" => array('id' => array_keys($employees_has_default))
			),
			'fields' => array('id', 'company_id'),
		));
		if( !empty($employees_no_default)){
			$count = 0;
			foreach( $employees_no_default as $employee_id => $company_id){
				$this->UserDefaultView->create();
				$res = $this->UserDefaultView->save(array(
					'model' => 'project',
					'employee_id' => $employee_id,
					'user_view_id' => $companies_default_view[$company_id],
					'created' => time(),
					'updated' => time(),
				));
				if( $res) $count++;
			}
			die(sprintf('Done. Updated for %s employee(s)', $count));
		}else{
			die('All employee has default view'); 
		}
		
	}
	//#1167
	public function update_company_default_view(){
		if( !$this->isAdminSAS) die('No permission!');
		$this->loadModels('Company', 'UserStatusView', 'UserView', 'CompanyViewDefaults');
		echo "<b>Clean junk data</b><br>";
		$this->deleteViewNotExist(true);
		$has_default_view = $this->CompanyViewDefaults->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'default_view' => 1
			),
			'fields' => array('company_id', 'user_view_id'),
		));
		$companies_no_default_view = $this->Company->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'company_name'),
			'conditions' => array(
				"NOT" => array('id' => array_keys($has_default_view)),
			)
		));
		$default_pilotage = array(
			'Project.project_name',
			'Project.project_amr_program_id',
			'Project.project_manager_id',
			'ProjectAmr.weather',
			'ProjectAmr.comment',
		);
		$default_pilotage = serialize($default_pilotage);
		if( empty($companies_no_default_view)){
			die( 'All company has default view.' );
		}else{
			echo "<b>List company without default view </b><br>";
			echo '<ul style="list-style: none;">';
			foreach ($companies_no_default_view as $company_id => $company_name){
				echo '<li>' . $company_id . ' - ' . $company_name .'</li>';
			}
			echo '</ul>';
			
			/* 
				Check public view with name "Pilotage"
                If Yes: Set this view as the default view
                If No: Create a new "Pilotage" view with list columns (program, project name , project manager, weather, advancement), then set as the default 
			*/			
			foreach ($companies_no_default_view as $company_id => $company_name){
				echo '<p><b>' . $company_id . ' - ' . $company_name .'</b></p>';
				echo '<pre>';
				$has_pilotage = $this->UserView->find('first', array(
					'recursive' => -1,
					'fields' => array('UserView.id'),
					'conditions' => array(
						'UserView.name' => 'Pilotage',
						'UserView.model' => 'project',
						'UserView.company_id' => $company_id,
						'UserView.public' => 1,
					)
				));
				if( !empty($has_pilotage)){
					echo '<p> Has views with name "Pilotage"</p>';
					$this->CompanyViewDefaults->create();
					$res = $this->CompanyViewDefaults->save(array(
						'user_view_id' => $has_pilotage['UserView']['id'],
						'company_id' => $company_id,
						'progress_view' => 1,
						'oppor_view' => 1,
						'default_view' => 1,
					));
					$res = 1;
					if( !empty($res)){
						echo '<p> Set as default view</p>';
					}else{
						echo '<p> Default setting failed</p>';
					}
					
				}else{
					$this->loadModels('Employee');
					echo '<p> Create new view with name "Pilotage"</p>';
					$admin_of_company = $this->Employee->CompanyEmployeeReference->find("first", array(
						"recursive" => -1,
						"conditions" => array(
							"Employee.company_id" => $company_id,
							"CompanyEmployeeReference.company_id" => $company_id,
							"CompanyEmployeeReference.role_id" => 2
						),
						'joins' => array(
							array(
								'table' => 'employees',
								'alias' => 'Employee',
								'conditions' => array(
									'Employee.id = CompanyEmployeeReference.employee_id'
								)
							)
						),
						'fields' => array('CompanyEmployeeReference.employee_id', 'Employee.first_name', 'Employee.last_name')
					));
					// debug( $admin_of_company ); 
					if( !empty( $admin_of_company )){
						$this->UserView->create();
						$res = $this->UserView->save(array(
							'model' => 'project',
							'name' => 'Pilotage',
							'description' => '',
							'content' => $default_pilotage,
							'company_id' => $company_id,
							'public' => 1,
							'gantt_view' => 0,
							'type' => 0,
							'from' => 0,
							'to' => 0,
							'stones' => 0,
							'display_all_name_of_milestones' => 0,
							'created_date' => date('Y-m-d'),
							'employee_id' => $admin_of_company['CompanyEmployeeReference']['employee_id'],
							'created' => time(),
							'updated' => time(),
							'mobile' => 0,
						));
						if( !empty($res)){
							echo '<p> Pilotage created</p>';
							$this->CompanyViewDefaults->create();
							$res = $this->CompanyViewDefaults->save(array(
								'user_view_id' => $this->UserView->id,
								'company_id' => $company_id,
								'progress_view' => 1,
								'oppor_view' => 1,
								'default_view' => 1,
							));
							if( !empty($res)){
								echo '<p> Set as default view</p>';
							}else{
								echo '<p> Default setting failed</p>';
							}
						}else{
							echo '<p>Failed when create "Pilotage" view</p>';
						}
					}else{
						echo "No admin found for company: <b>" . $company_name . ". Please try co create an admin employee then try again";
					}
				}
				echo '</pre>';
			}
		}
		
		die('Done');
		
	}
	
	function deleteViewNotExist($return = false){
		$this->loadModels('UserView');
		/* delete from user_views where employee_id not in (select id from employees where company_id is not null) */
		$employees = $this->Employee->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id is not null',
			),
			'fields' => array('id', 'id'),
		));
		$clear_view_without_employee = $this->UserView->deleteAll(array(
			'UserView.employee_id not' => array_values($employees )
		), false);
		
		
		$views = $this->UserView->find('list', array(
			'recursive' => -1,
			'fields' => array('UserView.id', 'UserView.id'),
		));
		if( !empty( $views)) {
			$views = array_values($views);
			$this->loadModels('UserStatusView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale', 'UserDefaultView', 'CompanyViewDefaults');
			foreach(array( 'UserStatusView', 'UserStatusViewActivity', 'UserStatusViewSaleDeal', 'UserStatusViewSale', 'UserDefaultView', 'CompanyViewDefaults') as $model){
				$this->$model->deleteAll(array(
					$model.'.user_view_id NOT' => $views,
				), false);
			}
		}
		if( $return) return true;
		die('Done');
		exit;
	}
	function convertDataRisk(){
		$this->loadModels('ProjectRisk', 'ProjectRiskOccurrence', 'ProjectRiskSeverity');
		$listSeverityFaible = $this->ProjectRiskSeverity->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_severity' => 'FAIBLE',
				'value_risk_severitie !=' => 0
			),
			'fields' => array('id', 'value_risk_severitie')
		));
		if(!empty($listSeverityFaible)){
			foreach($listSeverityFaible as $k => $v){
				$saved['value_risk_severitie'] = 0;
				$this->ProjectRiskSeverity->id = $k;
				$this->ProjectRiskSeverity->save($saved);
			}
		}
		$listSeverityMoyenne = $this->ProjectRiskSeverity->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_severity' => 'MOYENNE',
				'value_risk_severitie !=' => 1
			),
			'fields' => array('id', 'value_risk_severitie')
		));
		if(!empty($listSeverityMoyenne)){
			foreach($listSeverityMoyenne as $k => $v){
				$saved['value_risk_severitie'] = 1;
				$this->ProjectRiskSeverity->id = $k;
				$this->ProjectRiskSeverity->save($saved);
			}
		}
		$listSeverityForte = $this->ProjectRiskSeverity->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_severity' => 'FORTE',
				'value_risk_severitie !=' => 2
			),
			'fields' => array('id', 'value_risk_severitie')
		));
		if(!empty($listSeverityForte)){
			foreach($listSeverityForte as $k => $v){
				$saved['value_risk_severitie'] = 2;
				$this->ProjectRiskSeverity->id = $k;
				$this->ProjectRiskSeverity->save($saved);
			}
		}
		//Risk Occu
		$listOccurrenceFaible = $this->ProjectRiskOccurrence->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_occurrence' => 'FAIBLE',
				'value_risk_occurrence !=' => 0
			),
			'fields' => array('id', 'value_risk_occurrence')
		));
		if(!empty($listOccurrenceFaible)){
			foreach($listOccurrenceFaible as $k => $v){
				$saved['value_risk_occurrence'] = 0;
				$this->ProjectRiskOccurrence->id = $k;
				$this->ProjectRiskOccurrence->save($saved);
			}
		}
		$listOccurrenceMoyenne = $this->ProjectRiskOccurrence->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_occurrence' => 'MOYENNE',
				'value_risk_occurrence !=' => 1
			),
			'fields' => array('id', 'value_risk_occurrence')
		));
		if(!empty($listOccurrenceMoyenne)){
			foreach($listOccurrenceMoyenne as $k => $v){
				$saved['value_risk_occurrence'] = 1;
				$this->ProjectRiskOccurrence->id = $k;
				$this->ProjectRiskOccurrence->save($saved);
			}
		}
		$listOccurrenceForte = $this->ProjectRiskOccurrence->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'risk_occurrence' => 'FORTE',
				'value_risk_occurrence !=' => 2
			),
			'fields' => array('id', 'value_risk_occurrence')
		));
		if(!empty($listOccurrenceForte)){
			foreach($listOccurrenceForte as $k => $v){
				$saved['value_risk_occurrence'] = 2;
				$this->ProjectRiskOccurrence->id = $k;
				$this->ProjectRiskOccurrence->save($saved);
			}
		}
		echo "OK!";
		exit;
	}
}
?>