<?php
class ProjectCommunicationsController extends AppController {
	var $publicActions;
	var $components = array('MultiFileUpload', 'PImage');
	var $uses = array('ProjectCommunication', 'Project', 'ProjectCommunicationUrl');
	public $allowedFiles = "jpg,jpeg,gif,png";
	public $image_size = array('w' => 1144, 'h' => 858);
	public $image_thumb = array('w' => 320, 'h' => 240);
	public $companyConfigs = array();
	public $public_config = array(
		'communication_title',
		'white_list_httpreferer',
		'communication_iframe_only'
	);
	public function beforeFilter(){
		parent::beforeFilter();
		// $this->loadModels('Project', 'ProjectCommunicationUrl');
		$this->publicActions = array('index', 'view', 'attachment');
		$this->Auth->allow('index', 'view', 'attachment');
		// $this->Auth->allowedActions = array_merge($this->Auth->allowedActions, $this->publicActions);
		$action = $this->params['action'];
		if( in_array( $action, $this->publicActions)) $this->layout = 'iframe';
		$image_size = $this->image_size;
		$image_thumb = $this->image_thumb;
		$this->set(compact('image_size', 'image_thumb'));
	}
	public function index($company_public_key = null){		
		if( !empty($this->z_debug)){
			Configure::write('debug', 2);
			debug( $_SERVER);
		}
		$companyConfigs = $this->companyConfigs = $this->getCompanyConfigsbyKey($company_public_key);
		$company_id = $companyConfigs['company_id'];
		$isAdmin =  !empty($this->employee_info) && ($this->employee_info['Role']['name'] == 'admin') && ($this->employee_info['Company']['id'] == $company_id);
		$canCommunication =  isset($this->employee_info['Employee']['can_communication']) && ($this->employee_info['Employee']['can_communication'] == '1') && ($this->employee_info['Company']['id'] == $company_id);
		if( !($isAdmin || $canCommunication) ){
			$this->checkRefrenceSite($company_id);
		}
		$articles = $this->getPostsbyCompany($company_id, true);
		$articles = $this->checkUpdatePublicKey($articles); // Do thay doi ticket 727 nen phai update old data
		if( empty($articles)){
			// $this->cakeError('error404');
			// exit;
			$this->Session->setFlash(__('No posts displayed', true), 'error');
			
		}
		$this->set(compact('articles', 'company_id', 'companyConfigs'));
	}
	public function edit($project_id = null){
		$canModified = $this->canCommunication($project_id);
		if( !$canModified) $this->redirect( array( 'action' => 'index', $this->employee_info['Company']['id']));
		$projectName =  $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('id', 'project_name', 'project_objectives'),
		));
		$article = $this->getPostbyProject($project_id);
		if( empty($article)){
			$this->ProjectCommunication->create();
			$this->ProjectCommunication->save(array(
				'project_id' => $project_id,
				'public_key' => $this->_generatePublicKey($project_id)
			));
			$article = $this->getPostbyProject($project_id);			
		}
		$this->data = $this->convertVNdate($article);
		$company_public_key = $this->getCompanyPublicKey();
		$this->set(compact('article', 'projectName', 'project_id', 'company_public_key'));
	}
	public function view($public_key = null){
		$article = $this->getPostbyKey($public_key);
		$project_id = $article['ProjectCommunication']['project_id'];
		if( empty($article) ) $this->cakeError('error404');
		$article = $this->convertVNdate($article);
		$projectName =  $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('id', 'project_name', 'project_objectives', 'company_id'),
		));
		$company_id = $projectName['Project']['company_id'];
		$isAdmin =  !empty($this->employee_info) && $this->employee_info['Role']['name'] == 'admin' && $this->employee_info['Company']['id'] == $company_id;
		$canCommunication =  isset($this->employee_info['Employee']['can_communication']) && ($this->employee_info['Employee']['can_communication'] == '1') && ($this->employee_info['Company']['id'] == $company_id);
		if( !($isAdmin || $canCommunication) ){
			// $this->checkRefrenceSite($company_id);
			$this->checkRefrenceSite($projectName['Project']['company_id']);
		}
		$this->set(compact('article', 'projectName', 'project_id'));
	}
	public function delete($project_id = null){
		$article = $this->getPostbyProject($project_id);
		$result = false;
		$message = '';
		if( !empty( $article)){
			$canModified = $this->canCommunication($project_id);
			if( !$canModified) $this->redirect( array( 'action' => 'view', $project_id));
			if( !empty( $article['ProjectCommunication']['image'])){
				$last_image = $this->_getPath($project_id) . $article['ProjectCommunication']['image'];
				if( file_exists($last_image)) unlink($last_image);
			} 
			
			$this->ProjectCommunication->id = $id;
			$result = $this->ProjectCommunication->delete();
			$this->writeLog( $article['ProjectCommunication'], $this->employee_info, 'Delete Project Communication');
		}else{
			$message = __('Data empty', true);
			if( $result) $this->Session->setFlash(__('Data empty', true), 'error');
		}
		if( $this->params['isAjax']){
			die(json_encode(array(
				'result' => $result ? 'success' : 'failed',
				'data' => $id,
				'message' => ''
			)));
		}
		if( $result) $this->Session->setFlash(__('Deleted', true), 'success');
		$this->redirect( array( 'action' => 'edit', $project_id));
	}
	public function update($project_id){
		$canModified = $this->canCommunication($project_id);
		if( !$canModified) $this->redirect( array( 'action' => 'view', $project_id));
		$result = array();
		if( empty( $this->data)) $this->redirect( array( 'action' => 'edit', $project_id));
		if( empty($this->data['ProjectCommunication'])){
			$data = array('ProjectCommunication' => $this->data);
		}else{
			$data = $this->data;
		}
		unset($data['ProjectCommunication']['id']);
		$last = $this->getPostbyProject($project_id);
		if( empty( $last)){
			$this->ProjectCommunication->create();
			$data['ProjectCommunication']['project_id'] = $project_id;
			$data['ProjectCommunication']['public_key'] = $this->_generatePublicKey($project_id);
		}else{
			$this->ProjectCommunication->id = $last['ProjectCommunication']['id'];
			unset($data['ProjectCommunication']['public_key']);
		}
		$data['ProjectCommunication']['employee_updated'] = $this->employee_info['Employee']['id'];
		$data['ProjectCommunication']['updated'] = time();
		if( empty( $data['ProjectCommunication']['publisher']))  $data['ProjectCommunication']['publisher'] = $this->employee_info['Employee']['fullname'];
		$date_columns = array('start_date', 'end_date', 'public_date');
		if( empty( $data['ProjectCommunication']['public_date'])) {
			$data['ProjectCommunication']['public_date'] = date('d-m-Y', time());
			$this->data['ProjectCommunication']['public_date'] = date('d-m-Y', time());
		}
		foreach($date_columns as $date_column ){
			$data['ProjectCommunication'][ $date_column ] = $this->ProjectCommunication->convertTime(  $data['ProjectCommunication'][ $date_column ] );
		}
		$result = $this->ProjectCommunication->save($data['ProjectCommunication']);
		$this->data['ProjectCommunication']['id'] = $this->ProjectCommunication->id;
		if( isset( $data['ProjectCommunication']['public_key'])) $this->data['ProjectCommunication']['public_key'] = $data['ProjectCommunication']['public_key'];
		if( $result){
			$this->Session->setFlash( __('Saved', true), 'success');
		}else{
			$this->Session->setFlash( __('Error', true), 'error');
		}
		if( isset($data['CompanyConfig']['communication_title']) ){
			$this->loadModel('CompanyConfig');
			$company_id = $this->employee_info['Company']['id'];
			$field = 'communication_title';
			$value = $data['CompanyConfig']['communication_title'];
			$check = $this->CompanyConfig->find('first',array(
				'recursive' => -1,
				'conditions' => array(
					'company' => $company_id,
					'cf_name' => $field
				)
			));
			if($check){
				$this->CompanyConfig->id = $check['CompanyConfig']['id'];
			}else{
				$this->CompanyConfig->create();
			}
			$cf_data = array(
				'company'=>$company_id,
				'cf_name'=> $field,
				'cf_value'=> $value
			);
			$success = $this->CompanyConfig->save($cf_data);
		}
		foreach( $this->public_config as $key){
			if( isset($data['CompanyConfig'][$key]) ){
				$this->loadModel('CompanyConfig');
				$company_id = $this->employee_info['Company']['id'];
				$value = $data['CompanyConfig'][$key];
				$value = strip_tags($value);
				$check = $this->CompanyConfig->find('first',array(
					'recursive' => -1,
					'conditions' => array(
						'company' => $company_id,
						'cf_name' => $key
					)
				));
				if($check){
					$this->CompanyConfig->id = $check['CompanyConfig']['id'];
				}else{
					$this->CompanyConfig->create();
				}
				$cf_data = array(
					'company'=>$company_id,
					'cf_name'=> $key,
					'cf_value'=> $value
				);
				$success = $this->CompanyConfig->save($cf_data);
			}
		}
		if( $result && !empty($data['ProjectCommunicationUrl'])){
			$this->loadModels('ProjectCommunicationUrl');
			foreach( $data['ProjectCommunicationUrl'] as $url){
				if( empty( $url['url'])) {
					if( !empty( $url['id']) && $this->checkUrlID( $url['id'], $this->ProjectCommunication->id)){
						$this->ProjectCommunicationUrl->id = $url['id'] ;
						$this->ProjectCommunicationUrl->delete();
					}
					continue;
				}
				if( empty( $url['descriptions'])) $url['descriptions'] = $url['url'];
				$this->ProjectCommunicationUrl->create();
				if( !empty( $url['id'] )){
					//security
					if( !$this->checkUrlID( $url['id'], $this->ProjectCommunication->id)) continue;
					$this->ProjectCommunicationUrl->id = $url['id'] ;
				}
				$url['communication_id'] = $this->data['ProjectCommunication']['id'];
				$url['updated'] = time();
				$this->ProjectCommunicationUrl->save($url);
			}
		}
		if( $result && !empty($_FILES)){
			$_FILES['FileField'] = array();
            if(!empty($_FILES)){
                $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            }
            unset($_FILES['file']);

            $filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);
            $path = $this->_getPath($project_id);
			if (file_exists($path) && is_writable($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
            }
			if (!empty($attachment)) {
				$attachment = $attachment['attachment']['attachment'];
				$attachment = $this->_compressImage( $path, $attachment);
				$last = $this->ProjectCommunication->find('first', array(
					'recursive' => -1,
					'conditions' => array('id' => $this->ProjectCommunication->id)));
				if ($last && $last['ProjectCommunication']['image']) {
					if( file_exists($path . $last['ProjectCommunication']['image']) && $last['ProjectCommunication']['image'] != $attachment)
						unlink($path . $last['ProjectCommunication']['image']);
				}
				$this->ProjectCommunication->id = $this->data['ProjectCommunication']['id'];
				$upload = $this->ProjectCommunication->save(array(
					'image' => $attachment
				));
				if( $upload) {
					if($this->MultiFileUpload->otherServer == true){
						$this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_articles/index/' . $project_id);
					}
					$this->data['ProjectCommunication']['image'] = $attachment;
				}else {
					unlink($path . $attachment);
					$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
				}
			}
		}
		if( !$this->params['isAjax']){
			$this->redirect( array( 'action' => 'edit', $project_id));
		}
		$result = !empty($result) ? true : false;
		$this->set(compact('project_id', 'result'));
	}
	public function attachment($project_id = null, $id = null, $type='full'){
		$this->layout = false;
		$link = '';
		$image = $this->ProjectCommunication->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'project_id' => $project_id,
				'id' => $id,
				'image IS NOT NULL',
			),
			'fields' => array('id', 'project_id', 'image')
		));
		$this->view = 'Media';
		if( $image){
			$link = $this->_getPath($project_id). $image['ProjectCommunication']['image'];
			if( $type=='thumb' && file_exists($link)){
				$info = pathinfo($link);
				// debug( $info); exit;
				$new_link = $info['dirname'].DS.$info['filename'].'_thumb.'. $info['extension'];
				if (!file_exists($new_link) || !is_file($new_link)) {
					$newName = $info['filename'].'_thumb.'. $info['extension'];
					$this->PImage->resizeImage('resize', $info['basename'], $info['dirname'].DS, $newName, $this->image_thumb['w'], $this->image_thumb['h'], 80);
				}
				$link = $new_link;
			}
		}
		if (!file_exists($link) || !is_file($link)) {
			// Default: Global View 
			$this->loadModel('ProjectGlobalView');
			$projectGlobalView = $this->ProjectGlobalView->find("first", array(
				'fields' => array('id', 'project_id', 'attachment','is_file','is_https'),
				"conditions" => array('project_id' => $project_id)));
			if ($projectGlobalView) {
				$link = trim($this->_getGlobalViewPath($project_id) . $projectGlobalView['ProjectGlobalView']['attachment']);
				$is_image = getimagesize($link);
				if( empty($is_image )){ $link = '';}
			}
		}
		if (!file_exists($link) || !is_file($link)) {
			// Default image in webroot
			$link = WWW_ROOT . 'img' . DS . 'project_preview_default2x.png';
		}
		if (file_exists($link) && is_file($link)) {
			$info = pathinfo($link);
			$params = array(
				'id' => !empty($info['basename']) ? $info['basename'] : '',
				'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
				'name' => !empty($info['filename']) ? $info['filename'] : '',
				'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
				'mimeType' => array(),
			);
			$this->set($params);
		}else{
			
		}
	}
	private function checkUrlID($id, $communication_id){
		return $this->ProjectCommunicationUrl->find('count', array(
			'conditions' => array('id' => $id,'communication_id' => $communication_id)
		));
	}
	private function _generatePublicKey($project_id){
        $code = '';
        $code .= microtime();
        $code .= $project_id;
        $code .= $this->employee_info['Employee']['id'];
        $code .= $this->employee_info['Company']['id'];
        return md5($code);
	}
	private function _compressImage($path = null, $file_name = null){
		$link = $path . $file_name;
		if( !file_exists($link) || !is_file($link)) return null;
		$size = getimagesize($link);
		$id = time();
		$res = true;
		$info = pathinfo($link);
		$newName = $info['filename'].$id.'.'.$info['extension'];
		$thumb = $info['filename'].'_thumb.'.$info['extension'];
		if( $size[0] > $this->image_size['w'] || $size[1] > $this->image_size['h']){
			$this->PImage->resizeImage('resize', $file_name, $path, $newName, $this->image_size['w'], $this->image_size['h'], 80);
			unlink( $link);
			$res = rename( $path.$newName, $path.$file_name);
		}
		$this->PImage->resizeImage('resize', $file_name, $path, $thumb, $this->image_thumb['w'], $this->image_thumb['h'], 80);
		if( !$res) return $newName;
		return $file_name;
	}
	private function _getGlobalViewPath($project_id){
		$company = $this->ProjectGlobalView->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectGlobalView->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
		App::import('Core', 'Folder');
		new Folder($path, true, 0777);
        return $path;
	}
	private function _getPath($project_id){
		$this->loadModels('Project','Company');
		$company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 
			'conditions' => array('Project.id' => $project_id)
		));
        $pcompany = $this->Company->find('first', array(
            'recursive' => -1, 
			'conditions' => array(
			'Company.id' => $company['Company']['parent_id'])
		));
        $path = FILES . 'projects' . DS . 'communication' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
		App::import('Core', 'Folder');
		new Folder($path, true, 0777);
        return $path;
    }
	private function convertVNdate($article){
		if( empty($article)) return array();
		$date_fields = array('start_date', 'end_date', 'public_date');
		foreach($article['ProjectCommunication'] as $k => $v){
			if( in_array($k, $date_fields) && isset($v)){
				$article['ProjectCommunication'][$k] = date('d-m-Y', strtotime($v));
			}
		}
		return $article;
	}
	private function canCommunication($project_id = null){
		$company = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'id' => $project_id
			),
			'fields' => array('id', 'company_id')
		));
		if( empty($company)) return false;
		$isAdmin = !empty($this->employee_info['Role']['name']) && $this->employee_info['Role']['name'] == 'admin';
		$canCommunication = $this->employee_info['Employee']['can_communication'];
		$canCommunication = ($this->_checkRole( true, $project_id) && !empty($canCommunication));
		return $isAdmin || $canCommunication;
	}
	private function _denided(){
		$this->cakeError('error404');
		exit;
	}
	// Neu canCommunication thi full quyen
	// iframe_only == 0 thì cho view binh thuong, public (1)
	// Neu iframe_only thi chi cho embed, khong cho truy cap truc tiep, khong bublic voi mang xa hoi, search (2)
	// white_list_httpreferer empty thi cho tat ca site embed (3)
	// Neu co white_list_httpreferer thi chi cho nhung site trong white_list_httpreferer embed (4)
	private function checkRefrenceSite($company_id){
		if( empty($company_id)) $this->_denided();
		if( empty( $this->companyConfigs)){
			$this->loadModel('CompanyConfig');
			$companyConfigs = $this->CompanyConfig->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'cf_name' => array('white_list_httpreferer', 'communication_iframe_only'),
					'company' => $company_id
				),
				'fields' => array('cf_name', 'cf_value')
			));
		}else{
			$companyConfigs = $this->companyConfigs;
		}
		$iframe_only = isset( $companyConfigs['communication_iframe_only'] ) ? $companyConfigs['communication_iframe_only']==1 : '';
		if( !$iframe_only) return true; // (1)
		if( $iframe_only && (!empty($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] != 'iframe' )) $this->_denided(); //(2)
		$white_list = !empty( $companyConfigs['white_list_httpreferer']) ?$companyConfigs['white_list_httpreferer'] : '';
		if( empty($_SERVER['HTTP_REFERER'])) $this->_denided(); // (2)
		$white_list = str_replace(PHP_EOL, ' ', $white_list);
		if( empty($white_list)) return true; // (3)
		$refrence = $_SERVER['HTTP_REFERER'];
		$refrence =  parse_url($refrence); 
		$refrence = !empty( $refrence['host'] ) ? $refrence['host'] : '';
		$host = preg_replace ( '/^www./', '', $refrence);
		if( $host ==  $_SERVER['HTTP_HOST']) return true; // (4)
		$white_list = explode(' ', $white_list);
		$result = false;
		foreach( $white_list as $allow_ref){
			$allow_ref =  parse_url($allow_ref);
			$allow_ref = !empty( $allow_ref['host'] ) ? $allow_ref['host'] : (!empty( $allow_ref['path'] ) ? $allow_ref['path'] : '');
			$allow_ref = preg_replace ( '/^www./', '', $allow_ref); 
			if( $allow_ref == $host) $result = true; // (4)
		}
		if( !$result) $this->_denided();
		return $result;
	}
	private function checkUpdatePublicKey($articles){
		if( !is_array( $articles)) return;
		foreach( $articles as $k => $article){
			if( empty($article['ProjectCommunication']['public_key'])){
				$public_key = $this->_generatePublicKey($article['ProjectCommunication']['id']);
				$this->ProjectCommunication->id = $article['ProjectCommunication']['id'];
				$this->ProjectCommunication->save(array('public_key' => $public_key)); 
				$articles[$k]['ProjectCommunication']['public_key'] = $public_key;
			}
		}
		return $articles;
	}
	private function getCompanyPublicKey(){
		if( !empty($this->companyConfigs['company_public_key'])) return $this->companyConfigs['company_public_key'];
		$company_id = $this->employee_info['Company']['id'];
		$this->loadModel('CompanyConfig');
		$this->CompanyConfig->create();
		$res = $this->CompanyConfig->save(array(
			'cf_name' => 'company_public_key',
			'cf_value' => $this->_generatePublicKey($company_id),
			'company' => $company_id,				
		));
		return $res['CompanyConfig']['cf_value'];
		
	}
	private function getCompanyConfigsbyKey($company_public_key = null){
		$this->loadModel('CompanyConfig');
		$company_id = '';
		$company = $this->CompanyConfig->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				'cf_name' => 'company_public_key',
				'cf_value' => $company_public_key
			),
			'fields' => array('company')
		));
		$companyConfigs = array();
		$public_config = $this->public_config;
		if( !empty($company)){
			$company_id = array_shift($company);
			$companyConfigs = $this->CompanyConfig->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company' => $company_id,
					'cf_name' => $public_config,
				),
				'fields' => array('cf_name', 'cf_value')
			));
		}
		$companyConfigs['company_id'] = $company_id;
		return $companyConfigs;
	}
	private function getPostbyKey($public_key = null){
		$res = array();
		if( !empty($public_key)){
			$res = $this->ProjectCommunication->find('first', array(
				'conditions' => array(
					'ProjectCommunication.public_key' => $public_key
				),
			));
		}
		return $res;
	}
	private function getPostByProject($project_id = null){
		$res = array();
		if( !empty($project_id)){
			$res = $this->ProjectCommunication->find('first', array(
				'conditions' => array(
					'ProjectCommunication.project_id' => $project_id
				),
			));
		}
		// debug( $res); exit;
		return $res;
	}
	private function getPostsbyCompany($company_id = null, $active_only = false){
		$res = array();
		if( empty( $company_id) && !empty($this->employee_info) ) $company_id = $this->employee_info['Company']['id'];
		if( !empty($company_id)){
			$cond = array('Project.company_id' => $company_id);
			if( $active_only){
				$cond['ProjectCommunication.published'] = 1;
			}
			$res = $this->ProjectCommunication->find('all', array(
				'conditions' => $cond,
			));
		}
		return $res;
	}
}