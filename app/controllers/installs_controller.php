<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
App::import('Core', 'File');
App::import('Model', 'ConnectionManager', false);

    class InstallsController extends Controller {

        public $uses = array();

        public $defaultConfig = array(
            'driver' => 'mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'pms_system',
            'schema' => null,
            'prefix' => null,
            'encoding' => 'UTF8',
            'port' => null,
        );

        /**
         * beforeFilter
         *
         * @return void
         * @access public
        */
        public function beforeFilter() {

               parent::beforeFilter();

            $this->layout = 'install';
            //$this->_generateAssets();
        }

        /**
         * If settings.json exists, app is already installed
         *
         * @return void
         */
        protected function _check() {
            if (Configure::read('Install.installed') && Configure::read('Install.secured')) {
                $this->redirect('/login');
            }
        }


        public function index(){
            $this->_check();
            $this->set('title_for_layout', __('Installation: Welcome', true));
        }

        public function language(){
            $this->_check();
            $this->set('title_for_layout', __('Step 1: Choose Language', true));
            if(!empty($this->data)){
                if($this->data['language']['display'] == 0){
                    Configure::write('Config.language', 'eng');
                } else {
                    Configure::write('Config.language', 'fre');
                }
                $this->redirect(array('action' => 'database'));
            }
        }

        public function database(){
            $this->set('title_for_layout', __('Step 2: Database', true));
            $this->_check();

            if (!empty($this->data)) {
                $result = $this->createDatabaseFile(array(
                    'Install' => $this->data,
                ));
                if ($result !== true) {
                    $this->Session->setFlash(__('Could not write database.php file or database name does not exist', true), 'error');
                } else {
                     $db = ConnectionManager::getDataSource('default');
                     if(!$db->isConnected()) {
                         $this->Session->setFlash(__('Could not connect to database.', true), 'error');
                    } else {
                        $this->redirect(array('action' => 'data'));
                    }
                }
            }
        }

        public function data($alias = null){
            $this->set('title_for_layout', __('Step 3: Build database', true));
            $this->_check();
            $db = ConnectionManager::getDataSource('default');

            if(!$db->isConnected()) {
                $this->Session->setFlash(__('Could not connect to database.', true), 'error');
            }
            if(!empty($alias)){
                $sources = $db->listSources();
                if (!empty($sources)) {
                    $this->Session->setFlash(__('Warning: Database is not empty. Please, delete data before building new data', true), 'error');
                    $this->redirect(array('action' => 'data'));
                } else {
                    $this->__executeSQLScript($db, CONFIGS . 'database_install.sql');
                    $this->createCoreFile();
                    $this->redirect(array('action' => 'adminuser'));
                }
            }
        }

        public function adminuser(){
            $this->set('title_for_layout', __('Step 4: Create Admin User', true));
            $this->loadModel('Employee');
            $this->_check();
            if (!empty($this->data)) {
                $this->Employee->create();

                if ($this->data["Employee"]['password'] == md5("")) {
                    $this->data["Employee"]['password'] = "";
                } else {$this->data["Employee"]['password'] = md5($this->data["Employee"]['password']);}

                if ($this->data["Employee"]['confirm_password'] == md5("")) {
                    $this->data["Employee"]['confirm_password'] = "";
                } else {$this->data["Employee"]['confirm_password'] = $this->data["Employee"]['confirm_password'];}

                $this->data["Employee"]['first_name'] = 'Admin';
                $this->data["Employee"]['last_name'] = 'SAS';
                $this->data["Employee"]['is_sas'] = 1;
                $this->data["Employee"]['end_date'] = '0000-00-00';
                //debug($this->data["Employee"]); exit;
                if($this->Employee->save($this->data["Employee"])){
                    $this->redirect(array('action' => 'lisence'));
                }
            }
        }

        public function lisence($alias = null){
            $this->set('title_for_layout', __('Step 5: License', true));
            $this->_check();
            if(!empty($this->data)){
                if($this->data['Project']['display'] == 1){
                    $check = false;
                    $path = WWW_ROOT."/key/";
                    if(!empty($this->data['Project']['pm']['name'])){
                        if($this->data['Project']['pm']['name'] == 'projectkey.php'){
                            $target_path = $path . $this->data['Project']['pm']['name'];
                            move_uploaded_file($this->data['Project']['pm']['tmp_name'], $target_path);
                            $check = true;
                        } else {$check = false;}
                    }
                    if(!empty($this->data['Project']['am']['name'])){
                        if($this->data['Project']['am']['name'] == 'activitykey.php'){
                            $target_path = $path . $this->data['Project']['am']['name'];
                            move_uploaded_file($this->data['Project']['am']['tmp_name'], $target_path);
                            $check = true;
                        } else {$check = false;}
                    }
                    if(!empty($this->data['Project']['bm']['name'])){
                        if($this->data['Project']['bm']['name'] == 'absenceskey.php'){
                            $target_path = $path . $this->data['Project']['bm']['name'];
                            move_uploaded_file($this->data['Project']['bm']['tmp_name'], $target_path);
                            $check = true;
                        } else {$check = false;}
                    }
                    if(!empty($this->data['Project']['aum']['name'])){
                        if($this->data['Project']['aum']['name'] == 'auditkey.php'){
                            $target_path = $path . $this->data['Project']['aum']['name'];
                            move_uploaded_file($this->data['Project']['aum']['tmp_name'], $target_path);
                            $check = true;
                        } else {$check = false;}
                    }
                    if(!empty($this->data['Project']['bdm']['name'])){
                        if($this->data['Project']['bdm']['name'] == 'budgetkey.php'){
                            $target_path = $path . $this->data['Project']['bdm']['name'];
                            move_uploaded_file($this->data['Project']['bdm']['tmp_name'], $target_path);
                            $check = true;
                        } else {$check = false;}
                    }
                    if($check == true){
                        $this->Session->setFlash(__('The new license has been successfully updated', true), 'success');
                        $this->redirect(array('controller' => 'installs', 'action' => 'lisence', 'completed'));
                    } else {
                        $this->Session->setFlash(__('The key does not exist or key not lock activity. Please try again.', true), 'error');
                        $this->redirect(array('controller' => 'installs', 'action' => 'lisence'));
                    }
                }
            }
            $this->set(compact('alias'));
        }

        public function mail(){
            $this->set('title_for_layout', __('Step 6: Mail Settings', true));
            if(!empty($this->data)){
                copy(CONFIGS . 'mail_setting_install.php', CONFIGS . 'mail_setting.php');
                $file = new File(CONFIGS . 'mail_setting.php', true);
                $content = $file->read();
                foreach ($this->data['Mail'] as $configKey => $configValue) {
                    if($configKey == 'port'){
                        $content = str_replace('\'{default_' . $configKey . '}\'', $configValue, $content);
                    } else {
                        $content = str_replace('{default_' . $configKey . '}', $configValue, $content);
                    }
                }
                if (!$file->write($content)) {
                    $this->Seesion->setFlash(__('Could not write mail_setting.php file', true), 'error');
                } else {
                    $this->redirect(array('action' => 'finish'));
                }
            }
        }

        public function finish(){
            $this->set('title_for_layout', __('Installation successful', true));
            $this->_check();
               $result = $this->createSettingsFile();
            if ($result) {
                $this->installCompleted();
            }
        }




        // phan khu tai nguyen
        public function createDatabaseFile($data) {
            $config = $this->defaultConfig;

            foreach ($data['Install']['Database'] as $key => $value) {
                if (isset($data['Install']['Database'][$key])) {
                    $config[$key] = $value;
                }
            }
            copy(CONFIGS . 'database.php.install', CONFIGS . 'database.php');
            $file = new File(CONFIGS . 'database.php', true);
            $content = $file->read();

            foreach ($config as $configKey => $configValue) {
                $content = str_replace('{default_' . $configKey . '}', $configValue, $content);
            }

            if (!$file->write($content)) {
                $this->Seesion->setFlash(__('Could not write database.php file or database name does not exist', true), 'error');
            }

            try {
              ConnectionManager::create('default', $config);
              $db = ConnectionManager::getDataSource('default');
            }
            catch (MissingConnectionException $e) {
                $this->Session->setFlash(__('Could not connect to database. Please try again.', true), 'error');
            }

            if (!$db->isConnected()) {
                  $this->Session->setFlash(__('Could not connect to database. Please try again.', true), 'error');
            }
            return true;
        }

        public function createCoreFile() {
            $coreConfigFile = CONFIGS . 'core.php';
            copy($coreConfigFile . '.install', $coreConfigFile);
            $File = new File($coreConfigFile);
            $salt = Security::generateAuthKey();
            $seed = mt_rand() . mt_rand();
            $contents = $File->read();
            $contents = preg_replace('/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents);
            $contents = preg_replace('/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents);
            if (!$File->write($contents)) {
                $this->log('Unable to write your Config' . DS . 'croogo.php file. Please check the permissions.');
                return false;
            }
            Configure::write('Security.salt', $salt);
            Configure::write('Security.cipherSeed', $seed);

            return true;
        }

        /**
         * Create settings.json from default file
         *
         * @return bool true when successful
         */
        public function createSettingsFile() {
            return copy(CONFIGS . 'database_install.sql', CONFIGS . 'database.sql');
        }

        /**
         * Mark installation as complete
         *
         * @return bool true when successful
         */
        public function installCompleted() {
            return Configure::write('pms.installed', 1);
        }

        function __executeSQLScript($db, $fileName) {
            $statements = file_get_contents($fileName);
            $statements = explode(';', $statements);

            foreach ($statements as $statement) {
                if (trim($statement) != '') {
                    $db->query($statement);
                }
            }
        }
        public function syncDatabase($sqlScript=null){
            $this->set('title_for_layout', __('Synch database', true));
            //$this->_check();
            $db = ConnectionManager::getDataSource('default');

            if(!$db->isConnected()) {
                $this->Session->setFlash(__('Could not connect to database.', true), 'error');
            }
            $sqlScript=APP . 'webroot' . DS . $sqlScript.'.sql';
            if(file_exists($sqlScript))
            {
                $this->__executeSQLScript($db, $sqlScript);
                unlink($sqlScript);
                $this->remove_file_dir(APP . '/tmp/cache/models');
                $this->redirect(array('controller'=>'pages','action' => 'index'));
            }
            else
            {
                $this->remove_file_dir(APP . '/tmp/cache/models');
                //$this->Session->setFlash(__('SQL script file find not found.', true), 'error');
                $this->redirect(array('controller'=>'pages','action' => 'index'));
            }
        }
        function remove_file_dir($dir) {
            if ($handle = opendir("$dir")) {
                while (false !== ($item = readdir($handle))) {
                    if ($item != "." && $item != "..") {
                        if (is_dir("$dir/$item")) {
                            remove_file_dir("$dir/$item");
                        } else {
                            chmod("$dir/$item", 0777);
                            unlink("$dir/$item");
                        }
                    }
                }
                closedir($handle);
            }
        }
        public function cloneDataForNewCompany($company = null, $companyNew = null){
            $db = ConnectionManager::getDataSource('default');
            $error = array();
            if(!empty($error))
            {
                echo $error[0];
                exit;
            }
            //ARR TABLE ADMIN
            $tables = array(
                'absences',
                'absence_attachments',
                'absence_histories',
                'absence_request_confirms',
                'activity_columns',
                'activity_exports',
                'activity_families',
                'budget_settings',
                'budget_funders',
                'budget_customers',
                'budget_providers',
                'budget_types',
                'company_configs',
                'cities',
                'contract_types',
                'countries',
                'currencies',
                'dependencies',
                'holidays',
                'user_views',
                'menus',
                'project_acceptance_types',
                'project_settings',
                'project_phases',
                'project_statuses',
                'project_priorities',
                // 'project_types',
                'project_complexities',
                // 'project_datasets',
                'project_created_values',
                'project_functions',
                'project_phase_statuses',
                'project_risk_severities',
                'project_risk_occurrences',
                'project_issue_statuses',
                'project_issue_severities',
                'project_livrable_categories',
                'project_evolution_types',
                'project_evolution_impacts',
                'project_amr_programs',
                'project_amr_categories',
                'project_amr_statuses',
                'project_amr_cost_controls',
                'project_amr_organizations',
                'project_amr_plans',
                'project_amr_perimeters',
                'project_amr_risk_controls',
                'project_amr_problem_controls',
                'security_settings',
                'reports',
                'response_constraints',
                'workdays',
                'translation_entries',
                'translation_settings',
                'sale_settings',
				'vision_task_exports'
            );
            foreach($tables as $table)
            {
                if( $table == 'company_configs' ){
                    $count = $db->query("SELECT count(id) count FROM $table WHERE `company` = $companyNew");
                } else {
                    $count = $db->query("SELECT count(id) count FROM $table WHERE `company_id` = $companyNew");
                }
                $count = Set::ClassicExtract($count,'{n}.0.count');
                $count = $count[0];
                if($count != 0){
                    if( $table == 'company_configs' ){
                        $query = "DELETE FROM $table WHERE `company` = $companyNew" ;
                    } else {
                        $query = "DELETE FROM $table WHERE `company_id` = $companyNew" ;
                    }
                    $db->query($query);
                }
                $columns = $db->query("SHOW COLUMNS FROM $table");
                $columns = Set::ClassicExtract($columns,'{n}.COLUMNS.Field');
                $columns = join('`,`',$columns);
                $columns = substr($columns,4);
                $columns.='`';
                $columns = str_replace(',`company_id`','',$columns);
                $columns = str_replace('`company_id`,','',$columns);
                if( $table == 'company_configs' ){
                    $columns = str_replace(',`company`','',$columns);
                    $columnsAdd = $columns.",`company`";
                } else {
                    $columnsAdd = $columns.",`company_id`";
                }
                if( $table == 'company_configs' ){
                    $query = "INSERT INTO $table ($columnsAdd) SELECT $columns,$companyNew FROM $table WHERE `company` = $company" ;
                } else {
                    $query = "INSERT INTO $table ($columnsAdd) SELECT $columns,$companyNew FROM $table WHERE `company_id` = $company" ;
                }
				// if( $table == 'user_views'){ debug( $query); exit;}
                $db->query($query);
            }
			// #963 Remove company_public_key
			$query = "delete from  `company_configs` where `company`=$companyNew and `cf_name` = 'company_public_key'";
			$db->query($query);
            // exit;
			//Doi voi cong ty da clone truoc, co data trong table project_datasets+project_types, khi clone lai thi delete data table project_datasets
			$tableDatasets = array(
				'project_datasets',
				'project_types'
			);
			foreach($tableDatasets as $tableDataset)
            {
				$query = "DELETE FROM $tableDataset WHERE `company_id` = $companyNew" ;
				$db->query($query);
			}
			//End
			$exportPO = $this->requestAction('/translations/exportPO/' . $companyNew);
			if( $exportPO){
				echo '<div>' . __('Export language file successfully') . '</div>';
			}else{
				echo '<div>' . __('Fail to export language file') . '</div>';
			}
            echo "Clone data complete!";
            exit;
        }
    }
?>
