<?php
class ActionLogsController extends AppController {

	public function index(){
		$conditions = array();
		list($start, $end) = $this->parseParams();
		$conditions['ActionLog.created >='] = $start;
		$conditions['ActionLog.created <='] = $end;
		if( isset($this->employee_info['Company']['id']) )$conditions['ActionLog.company_id'] = $this->employee_info['Company']['id'];
		//
		$settingLogin = isset($this->companyConfigs['action_dont_store_login']) ? $this->companyConfigs['action_dont_store_login'] : 0;
		if($settingLogin){
			$conditions['NOT']['ActionLog.url'] = array('login', 'logout', 'employees/login', 'employees/logout', 'employees/get_company_role');
		}
		$logs = $this->ActionLog->find('all', array(
			'recursive' => -1,
			'fields' => array('ActionLog.*, Company.company_name, Employee.first_name, Employee.last_name, Employee.email'),
			'conditions' => $conditions,
			'order' => array('created' => 'DESC'),
			'joins' => array(
				array(
					'table' => 'companies',
					'alias' => 'Company',
					'type' => 'left',
					'conditions' => array(
						'Company.id = ActionLog.company_id'
					)
				),
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'left',
					'conditions' => array(
						'Employee.id = ActionLog.employee_id'
					)
				)
			)
		));
		
		
		$this->set('logs', $logs);
	}
	
	function archiveLogs(){
		set_time_limit(0);
		ignore_user_abort(true);
		if($this->is_sas || (isset($this->employee_info['Company']['id'])  && $this->employee_info['Role']['name'] == 'admin' )){
			$company_id = isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : '';	
			$conditions1 = array();
			$conditions2 = array();
			if(!empty($company_id)){
				$conditions1['id'] = $company_id;
				$conditions2['company_id'] = $company_id;
			}
			$this->loadModels('Company');
			$company_dir = $this->Company->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'dir'),
				'conditions' => $conditions1,
				
			));			
			$logs = $this->ActionLog->find('all', array(
				'recursive' => -1,
				'fields' => array('*'),
				'conditions' => $conditions2,
			));
			// debug($logs);
			$taskArchived = array();
			if(!empty($logs)){
				foreach($logs as $key => $value){
					$dLog = $value['ActionLog'];
					if(!empty($dLog)){
						$log_company_dir = (!empty($company_dir) && !empty($company_dir[$dLog['company_id']])) ? $company_dir[$dLog['company_id']] : 'noname';
						$log_created = $dLog['created'];
						// Delete / Write logs
						// Keep save data logs in current month
						if(date('Y_m', strtotime($log_created)) != date('Y_m', strtotime("now"))){
							$path = $this->_getPath($log_company_dir);
							new Folder($path, true, 0777);
							$file_log = $path . DS . 'action_log_'.date('Y_m', strtotime($log_created)).'.log';
							if (file_exists($file_log)) {
							  $fh = fopen($file_log, 'a');
							  fwrite($fh, json_encode($value)."\n");
							} else {
							  $fh = fopen($file_log, 'w');
							  fwrite($fh, json_encode($value)."\n");
							}
							fclose($fh);
							$taskArchived[] = $dLog['id'];
						}
					}
				}
			}
			if(!empty($taskArchived)){
				$this->ActionLog->deleteAll(array('id' => $taskArchived));
			}
		}
		die(1);
	}
	function popupActionLogs(){
		set_time_limit(0);
		ignore_user_abort(true);
		$data = array();
		if($this->is_sas || (isset($this->employee_info['Company']['id'])  && $this->employee_info['Role']['name'] == 'admin' )){
			$company_id = isset($this->employee_info['Company']['id']) ? $this->employee_info['Company']['id'] : '';	
			$conditions = array();
			$dir = FILES . 'logs';
			$rootPath = realpath($dir);
			if(!empty($company_id)){
				$company_dir = $this->Company->find('first', array(
					'recursive' => -1,
					'fields' => array('dir'),
					'conditions' => array(
						'id' => $company_id,
					)
				));	
		
				$rootPath = realpath($dir . DS . $company_dir['Company']['dir']);
			}
			if (file_exists($rootPath)){
				// Create recursive directory iterator
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($rootPath),
					RecursiveIteratorIterator::LEAVES_ONLY
				);			
				foreach ($files as $name => $file){
					// Skip directories (they would be added automatically)
					if (!$file->isDir()){
						// Get real and relative path for current file
						$path = $file->getRealPath();
						if($this->is_sas){
							$data[] = substr($path, strlen($rootPath) + 1);
						}else{
							$data[] = substr($path, strlen(realpath($dir)) + 1);
						}

					}
				}
			}else{
				die(0);
			}
		}else{
			$this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index'));
		}	
		die(json_encode($data));
	}
	function attachment($company_name, $log_name) {
		if($this->is_sas || (isset($this->employee_info['Company']['id'])  && $this->employee_info['Role']['name'] == 'admin' )){
			$this->layout = false;
			$link = '';
			$key = isset($_GET['sid']) ? $_GET['sid'] : '';
			if( $key ){
				if(!$this->is_sas){
					$info = $this->ApiKey->retrieve($key);
					if( empty($info) ){
						die('Permission denied');
					}
					$company = $this->Company->find('count', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $info['ApiKey']['company_id'],
							'dir' => $company_name
						)
					));
				
					if( !$company ){
						$this->Session->setFlash(__('Permission denied.', true), 'error');
						$this->redirect(array('action' => 'index'));
					}
				}
			} else {
					$this->Session->setFlash(__('Permission denied.', true), 'error');
					$this->redirect(array('action' => 'index'));
			}
			
			$logFile = FILES . 'logs'. DS .$company_name. DS . $log_name; 
			
			if (empty($logFile)) {
				$logFile = '';
			} else {
				if (!file_exists($logFile) || !is_file($logFile)) {
					$logFile = '';
				}
				$info = pathinfo($logFile);
				$this->view = 'Media';
				$params = array(
					'id' => !empty($info['basename']) ? $info['basename'] : '',
					'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
					'name' => !empty($info['filename']) ? $info['filename'] : '',
					'mimeType' => array(
						'log' => 'text/plain',
					),
					'download' => true,
					'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
				);
				
				$this->set($params);
			}
			if (!$logFile && !empty($this->params['url']['download'])) {
				$this->Session->setFlash(__('File not found.', true), 'error');
				$this->redirect(array('action' => 'index'));
			}
		}else{
			$this->Session->setFlash(__('File not found.', true), 'error');
			$this->redirect(array('action' => 'index'));
			
		}
	}
	
	function _getPath($log_company_dir){
		App::import('Core', 'Folder');
		$path = FILES . 'logs' . DS . $log_company_dir;
		return $path;
	}
	function getStartAndEndDate($week, $year) {
		$dto = new DateTime();
		$dto->setISODate($year, $week);
		$dto->modify('last sunday');
		$ret[0] = $dto->format('Y-m-d');
		//$dto->modify('next sunday');
		$dto->modify('next saturday');
		$ret[1] = $dto->format('Y-m-d');
		return $ret;
	}
	private function parseParams(){
		$start = DateTime::createFromFormat('d-m-Y', trim(@$this->params['url']['start']));
		if( $start ){
			$sql_start = $start->format('Y-m-d 00:00:00');
			$startD = $start->format('d-m-Y');
		}
		else {
			$sql_start = date('Y-m-d 00:00:00');
			$startD = date('d-m-Y');
		}
		$end = DateTime::createFromFormat('d-m-Y', trim(@$this->params['url']['end']));
		if( $end ){
			$sql_end = $end->format('Y-m-d 23:59:59');
			$endD = $end->format('d-m-Y');
		}
		else {
			$sql_end = date('Y-m-d 23:59:59');
			$endD = date('d-m-Y');
		}
		// if( $start->diff($end, true)->format('%a') > 31 ){
		// 	return array();
		// }
		$this->set(array('start' => $startD, 'end' => $endD));
		return array($sql_start, $sql_end);
	}
	// public function clear(){
	// 	$conditions = array();
	// 	if( isset($this->employee_info['Company']['id']) ){
	// 		$this->ActionLog->deleteAll(array(
	// 			'ActionLog.company_id' => $this->employee_info['Company']['id']
	// 		));
	// 		$this->writeLog('company ID=' . $this->employee_info['Company']['id'], $this->employee_info, 'Clear logs for ' . $this->employee_info['Company']['company_name']);
	// 	//hard reset for sas without company
	// 	} else {
	// 		$this->ActionLog->query('truncate table action_logs');
	// 		$this->writeLog('', $this->employee_info, 'SAS cleared all logs');
	// 	}
	// 	$this->redirect('/action_logs');
	// }
	public function export(){
		//$this->index();
		set_time_limit(0);
		$this->layout = 'ajax';
		$list = explode(',', $this->data['list']);
		$conditions = array();
		// list($start, $end) = $this->parseParams();
		// $conditions['ActionLog.created >='] = $start;
		// $conditions['ActionLog.created <='] = $end;
		if( !empty($list) )$conditions['ActionLog.id'] = $list;
		if( isset($this->employee_info['Company']['id']) )$conditions['ActionLog.company_id'] = $this->employee_info['Company']['id'];
		$logs = $this->ActionLog->find('all', array(
			'fields' => array('ActionLog.*, Company.company_name, Employee.first_name, Employee.last_name, Employee.email'),
			'conditions' => $conditions,
			'order' => array('created' => 'DESC'),
			'joins' => array(
				array(
					'table' => 'companies',
					'alias' => 'Company',
					'type' => 'left',
					'conditions' => array(
						'Company.id = ActionLog.company_id'
					)
				),
				array(
					'table' => 'employees',
					'alias' => 'Employee',
					'type' => 'left',
					'conditions' => array(
						'Employee.id = ActionLog.employee_id'
					)
				)
			)
		));
		$this->set('logs', $logs);
	}
	public function saveSetting(){
		$data = $this->data;
		$this->requestAction('/company_configs/saveAll', array('pass' => array($data)));
		die;
	}
    
    public function read_config(){
        $dbconfig =  get_class_vars('DATABASE_CONFIG'); 
        include CONFIGS . 'mail_setting.php';
        $mails = new MailSetting();
        $mailconfig = $mails->mailConfig;
        $arrCoreConfig= array('debug','log','App.encoding','Cache.disable','Session.save','Session.cookie','Session.timeout','Session.start','Session.checkAgent','Security.level',
                        'Security.salt','Security.cipherSeed','Acl.classname','Acl.database','Config.defaultLanguage','Config.language','Config.languages',
                        'Install.secured','Install.installed','Route.default');
        $allConfig = array();
//        debug($mails->mailConfig);exit;
        foreach ($arrCoreConfig as $key){
            $value = Configure::read($key); 
            $allConfig[$key]= "<pre>". print_r($value, true). "</pre>";
        }
        
        foreach ($dbconfig as $key => $value){

            $valueconfig = "<pre>".print_r($value, true) . "</pre>";
            $allConfig['DATABASE_CONFIG : '.$key] = $valueconfig;
        }
        
        $valuemail = "<pre>". print_r($mailconfig, true). "</pre>";
        $allConfig['MailSetting : mailConfig'] = $valuemail;

        $this->set('allConfig', $allConfig);
    }
   public function listDirectory($dir)
      {
        $result = array();
        $root = scandir($dir);
        foreach($root as $value) {
          if($value === '.' || $value === '..') {
            continue;
          }
          if(is_file("$dir$value")) {
            $result[] = "$dir$value";
            continue;
          }
          if(is_dir("$dir$value")) {
            $result[] = "$dir$value/";
          }
          foreach(self::listDirectory("$dir$value/") as $value)
          {
            $result[] = $value;
          }
        }
        return $result;
    }

    public function download_log_confirm(){
        // Get real path for our folder
        $source_dir =  FILES . 'logs'.DS. 'thecamp';
        $zip_file = FILES . 'logs' .DS."logs.zip"; 
        $file_list = $this->listDirectory($source_dir.DIRECTORY_SEPARATOR);
		// debug($file_list);
		// exit;
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
          foreach ($file_list as $file) {
            if ($file !== $zip_file) {
              $zip->addFile($file, substr($file, strlen($source_dir)));
            }
          }
          $zip->close();
        }
        
        $file_name = basename($zip_file);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . $file_name);
        header("Content-Length: " . filesize($zip_file));
        readfile($zip_file);
        // unlink ($zip_file);
        exit;
        
    }
    public function server_file(){
   
        $source_dir =  FILES;
        list($start, $end) = $this->parseParams();
        $file_list = $this->getDirContents($source_dir);
        $files = array();
        $i=1;
        foreach ($file_list as $value) {
            
            $path = dirname($value);
            $file_name=  basename($value);
            $date =   filectime($value);
            $image = str_replace(FILES,"/user_files/image/?path=",$value);
            
            if($date>=strtotime($start) && $date <=strtotime($end)){
                $files[] = array(
                            "id" => $i++,
                            "path" => $path,
                            "filename"=>$file_name,
                            "date" => $date,
                            "image" => $image

                );
            
            }
        }

        $this->set(compact('files'));
    }
    public function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
            }
        }

        return $results;
    }

}