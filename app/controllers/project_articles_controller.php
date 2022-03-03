<?php
class ProjectArticlesController extends AppController {
	var $publicActions;
	var $components = array('MultiFileUpload', 'PImage');
	var $users = array('Project', 'ProjectArticleUrl');
	public $allowedFiles = "jpg,jpeg,gif,png";
	public function beforeFilter(){
		parent::beforeFilter();
		$this->loadModels('Project', 'ProjectArticleUrl');
		$this->publicActions = array('communication', 'view');
		$this->Auth->allow('communication', 'view', 'attachment');
		$this->Auth->allowedActions = array_merge($this->Auth->allowedActions, $this->publicActions);
		$action = $this->params['action'];
		if( in_array( $action, $this->publicActions)) $this->layout = 'iframe';
	}
	public function index($project_id = null){
		if( empty($project_id)) $this->redirect('/');
		$canModified = $this->canModifiedArticles($project_id);
		if( !$canModified) $this->redirect( array( 'action' => 'communication', $project_id));
		$articles = $this->getArticlesbyProject($project_id);
		$this->set(compact('articles', 'project_id'));
	}
	public function edit($id = null){
		$article = array();
		if( !empty($id) ) {
			$article = $this->get_article($id);
			$canModified = $this->canModifiedArticles($article['ProjectArticle']['project_id']);
			if( !$canModified) $this->redirect( array( 'action' => 'view', $id));
			$this->data = $this->convertVNdate($article);
		}
		if( empty($article)){
			$this->Session->setFlash(__('Article not found', true), 'error');
			$this->redirect('/');
		}
		$project_id = !empty( $article) ? $article['ProjectArticle']['project_id'] : '';
		$this->set(compact('article', 'project_id'));
	}
	
	public function add($project_id = null){
		$canModified = $this->canModifiedArticles($project_id);
		if( !$canModified) $this->redirect( array( 'action' => 'communication', $project_id));
		$this->data = $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('id', 'project_name', 'project_objectives'),
		));
		$this->set(compact('project_id'));
		$this->render('edit');
	}
	
	public function view($id = null){
		$article = $this->get_article($id);
		if( empty($article) ) $this->redirect('/');
		$article = $this->convertVNdate($article);
		$project_id = $article['ProjectArticle']['project_id'];
		$projectName =  $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('id', 'project_name', 'project_objectives'),
		));
		$this->set(compact('article', 'projectName', 'project_id'));
	}
	public function communication($project_id = null){
		if( empty($project_id)) $this->redirect('/');
		$articles = $this->getArticlesbyProject($project_id, true);
		$projectName =  $this->Project->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $project_id,
			),
			'fields' => array('id', 'project_name', 'project_objectives'),
		));
		if( empty($articles) ) $this->Session->setFlash(__('Data empty', true), 'warning');
		$this->set(compact('articles', 'projectName', 'project_id'));
	}
	public function delete($id = null){
		$article = $this->get_article($id);
		$project_id = !empty( $article) ? $article['ProjectArticle']['project_id'] : '';
		$canModified = $this->canModifiedArticles($project_id);
		if( !$canModified) $this->redirect( array( 'action' => 'communication', $project_id));
		if( !empty( $article['ProjectArticle']['image'])){
			$last_image = $this->_getPath($project_id) . $article['ProjectArticle']['image'];
			if( file_exists($last_image)) unlink($last_image);
		} 
		$this->ProjectArticle->id = $id;
		$result = $this->ProjectArticle->delete();
		$this->writeLog( $article['ProjectArticle'], $this->employee_info, 'Delete Project Article');
		if( $this->params['isAjax']){
			die(json_encode(array(
				'result' => $result ? 'success' : 'failed',
				'data' => $id,
				'message' => ''
			)));
		}
		if( $result) $this->Session->setFlash(__('Deleted', true), 'success');
		$this->redirect( array( 'action' => 'index', $project_id));
	}
	public function update(){
		$result = array();
		if( empty( $this->data)) $this->redirect('/');
		if( empty($this->data['ProjectArticle'])){
			$data = array('ProjectArticle' => $this->data);
		}else{
			$data = $this->data;
		}
		if( empty($data['ProjectArticle']['id'])){
			$this->ProjectArticle->create();
		}else{
			$this->ProjectArticle->id = $data['ProjectArticle']['id'];
		}
		$data['ProjectArticle']['employee_updated'] = $this->employee_info['Employee']['id'];
		$data['ProjectArticle']['updated'] = time();
		if( empty( $data['ProjectArticle']['publisher']))  $data['ProjectArticle']['publisher'] = $this->employee_info['Employee']['fullname'];
		$date_columns = array('start_date', 'end_date', 'public_date');
		if( empty( $data['ProjectArticle']['public_date'])) {
			$data['ProjectArticle']['public_date'] = date('d-m-Y', time());
			$this->data['ProjectArticle']['public_date'] = date('d-m-Y', time());
		}
		foreach($date_columns as $date_column ){
			$data['ProjectArticle'][ $date_column ] = $this->ProjectArticle->convertTime(  $data['ProjectArticle'][ $date_column ] );
		}
		$result = $this->ProjectArticle->save($data);
		$project_id = $data['ProjectArticle']['project_id'];
		$this->data['ProjectArticle']['id'] = $this->ProjectArticle->id;
		if( $result){
			$this->Session->setFlash( __('Saved', true), 'success');
		}else{
			$this->Session->setFlash( __('Error', true), 'error');
		}
		if( $result && !empty($data['ProjectArticleUrl'])){
			$this->loadModels('ProjectArticleUrl');
			foreach( $data['ProjectArticleUrl'] as $url){
				if( empty( $url['url'])) continue;
				if( empty( $url['descriptions'])) $url['descriptions'] = $url['url'];
				$this->ProjectArticleUrl->create();
				if( !empty( $url['id'] )) $this->ProjectArticleUrl->id = $url['id'] ;
				$url['article_id'] = $this->data['ProjectArticle']['id'];
				$url['updated'] = time();
				$this->ProjectArticleUrl->save($url);
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
				$last = $this->ProjectArticle->find('first', array(
					'recursive' => -1,
					'conditions' => array('id' => $this->ProjectArticle->id)));
				if ($last && $last['ProjectArticle']['image']) {
					if( file_exists($path . $last['ProjectArticle']['image']) && $last['ProjectArticle']['image'] != $attachment)
						unlink($path . $last['ProjectArticle']['image']);
				}
				$this->ProjectArticle->id = $this->data['ProjectArticle']['id'];
				$upload = $this->ProjectArticle->save(array(
					'image' => $attachment
				));
				if( $upload) {
					if($this->MultiFileUpload->otherServer == true){
						$this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_articles/index/' . $project_id);
					}
					$this->data['ProjectArticle']['image'] = $attachment;
				}else {
					unlink($path . $attachment);
					$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
				}
			}
		}
		if( !$this->params['isAjax']){
			$this->redirect( array( 'action' => 'index', $project_id));
		}
		$result = !empty($result) ? true : false;
		$this->set(compact('project_id', 'result'));
	}
	public function attachment($project_id = null, $id = null){
		$this->layout = false;
		$link = '';
		$image = $this->ProjectArticle->find('first', array(
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
			$link = $this->_getPath($project_id). $image['ProjectArticle']['image'];
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
	public function getArticlesbyProject($project_id = null, $active_only = false){
		$res = array();
		if( !empty($project_id)){
			$cond = array('ProjectArticle.project_id' => $project_id);
			if( $active_only){
				$cond['ProjectArticle.status'] = 1;
			}
			$res = $this->ProjectArticle->find('all', array(
				'conditions' => $cond,
			));
		}
		return $res;
	}
	/* ticket 727 resize to max 1920px */
	private function _compressImage($path = null, $file_name = null){
		$link = $path . $file_name;
		if( !file_exists($link) || !is_file($link)) return null;
		$size = getimagesize($link);
		$id = time();
		if( $size[0] > 1920 || $size[1] > 1080){
			$info = pathinfo($file_name);
			$newName = $info['filename'].$id.'.'.$info['extension'];
			// debug( $newName ); exit;
			$this->PImage->resizeImage('resize', $file_name, $path, $newName, 1920, 1080, 80);
			unlink( $link);
			$res = rename( $path.$newName, $path.$file_name);
			if( !$res) return $newName;
		}
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
		// debug( $company); exit;
        $pcompany = $this->Company->find('first', array(
            'recursive' => -1, 
			'conditions' => array(
			'Company.id' => $company['Company']['parent_id'])
		));
        $path = FILES . 'projects' . DS . 'articles' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS . $project_id . DS;
		App::import('Core', 'Folder');
		new Folder($path, true, 0777);
        return $path;
    }
	private function convertVNdate($article){
		$date_fields = array('start_date', 'end_date', 'public_date');
		foreach($article['ProjectArticle'] as $k => $v){
			if( in_array($k, $date_fields)){
				$article['ProjectArticle'][$k] = date('d-m-Y', strtotime($v));
			}
		}
		return $article;
	}
	private function get_article($id = null){
		$res = array();
		if( !empty($id)){
			$res = $this->ProjectArticle->find('first', array(
				'conditions' => array(
					'ProjectArticle.id' => $id
				),
			));
		}
		return $res;
	}
	private function canModifiedArticles($project_id = null){
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
}