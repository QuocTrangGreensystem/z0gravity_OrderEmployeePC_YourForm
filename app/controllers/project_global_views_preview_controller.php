<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectGlobalViewsPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectGlobalView');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');
    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions[] = 'attachment';
        $this->Auth->allowedActions[] = 'attachment_index';
        $this->set('allowedFiles', $this->allowedFiles);
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('global_view');
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('id', 'attachment','is_file', 'is_https'),
            "conditions" => array('project_id' => $project_id)));
		$noFileExists = false;
        if ($projectGlobalView) {
            $link = trim($this->_getPath($project_id)
                    . $projectGlobalView['ProjectGlobalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExists = true;
            }
        }

        $this->set(compact('project_id', 'projectGlobalView', 'noFileExists'));
    }
    function ajax($project_id = null, $view = 'ajax') {
        $this->_checkRole(false, $project_id);
        $type = '';
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1, 'fields' => array('attachment','is_file'),
            "conditions" => array('project_id' => $project_id)));

        if ($projectGlobalView) {
            $type = $projectGlobalView['ProjectGlobalView']['is_file'] == 1 ? 'file' : 'url';
            $link = trim($this->_getPath($project_id)
                    . $projectGlobalView['ProjectGlobalView']['attachment']);
            if ($type == 'file' && !file_exists($link)) {
                die('<p class="error">' . __('File not found', true) . '</p>');
            }
        }
        $this->set(compact('project_id', 'projectGlobalView', 'type', 'view'));
        $this->render('ajax');
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($project_id = null, $is_file = 0) {
		if($is_file == 1) $this->data['ProjectGlobalView']['is_file'] = $is_file;
		if(!isset($this->data['ProjectGlobalView']['is_file'])) $this->data['ProjectGlobalView']['is_file'] = 1;
        if($this->data['ProjectGlobalView']['is_file']){
            if($this->data['ProjectGlobalView']['is_file'] == 2){
                if(empty($this->data['ProjectGlobalView']['attachment'])){
                    $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
                }else{
                    $this->ProjectGlobalView->create();
                    
                    $this->ProjectGlobalView->save(array(
                        'project_id' => $project_id,
                        'attachment' => $this->data['ProjectGlobalView']['attachment'],
                        'is_file' => 0,
                        'is_https' => 0,
                    ));

                }
            }else{
                if (empty($_FILES)) {
                    $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
                } else {
                    $_FILES['FileField'] = array();
                    if(!empty($_FILES)){
                        $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                        $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                        $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                        $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                        $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
                    }
                    unset($_FILES['file']);
                    if ($this->_checkRole(true, $project_id)) {
                        $path = $this->_getPath($project_id);

                        App::import('Core', 'Folder');
                        new Folder($path, true, 0777);

                        if (file_exists($path)) {
                            $this->MultiFileUpload->encode_filename = false;
                            $this->MultiFileUpload->uploadpath = $path;
                            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
                            $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                            $attachment = $this->MultiFileUpload->upload();
                        } else {
                            $attachment = "";
                            $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                                    , true), $path), 'error');
                        }

                        if (!empty($attachment)) {
                            $attachment = $attachment['attachment']['attachment'];
                            $this->ProjectGlobalView->create();
                            $last = $this->ProjectGlobalView->find('first', array(
                                'recursive' => -1,
                                'fields' => array('id', 'attachment'),
                                'conditions' => array('project_id' => $project_id)));
                            if ($last) {
                                $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                                @unlink($path . $last['ProjectGlobalView']['attachment']);
                            }
                            if ($this->ProjectGlobalView->save(array(
                                        'project_id' => $project_id,
                                        'attachment' => $attachment,
                                        'is_file' => $this->data['ProjectGlobalView']['is_file']
                                    ))) {
                                if($this->MultiFileUpload->otherServer == true){
                                    $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['ProjectGlobalView']['attachment'], $attachment, '/project_global_views/index/' . $project_id);
                                }
                                $this->Session->setFlash(__('Saved', true), 'success');
                            } else {
                                @unlink($path . $attachment);
                                $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                                if($this->MultiFileUpload->otherServer == true){
                                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_global_views/index/' . $project_id);
                                }
                            }
                        } else {
                            $this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
                        }
                    }
                }
            }
            $this->redirect(array('action' => 'index', $project_id));
        }else{
            $last = $this->ProjectGlobalView->find('first', array(
                            'recursive' => -1,
                            'fields' => array('id', 'attachment','is_file'),
                            'conditions' => array('project_id' => $project_id)));
            if($last){
              $this->ProjectGlobalView->id = $last['ProjectGlobalView']['id'];
                            @unlink($path . $last['ProjectGlobalView']['attachment']);
            }
            $is_https = 0;
            $pos = strpos($this->data['ProjectGlobalView']['attachment'],'https');
            if($pos !== false){
                $this->data['ProjectGlobalView']['attachment'] = str_replace('https://','',$this->data['ProjectGlobalView']['attachment']);
                $is_https = 1;
            }
            else{
                $this->data['ProjectGlobalView']['attachment'] = str_replace('http://','',$this->data['ProjectGlobalView']['attachment']);
                //$is_https = 0;
            }
            if($this->ProjectGlobalView->save(array(
                                    'project_id' => $project_id,
                                    'attachment' => $this->data['ProjectGlobalView']['attachment'],
                                    'is_file' => $this->data['ProjectGlobalView']['is_file'],
                                    'is_https' => $is_https
                                ))) {
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                @unlink($path . $attachment);
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
            $this->redirect(array('action' => 'index', $project_id));
         }
    }
    // public function uploadAttachment($project_id = null){
    //     return 1;
    // }
    public function attachment($project_id = null) {
        $this->layout = false;
        $link = '';
        $key = isset($_GET['sid']) ? $_GET['sid'] : '';
        $auth_code = isset($_GET['auth_code']) ? $_GET['auth_code'] : '';
        if( $key ){
            $info = $this->ApiKey->retrieve($key);
            if( empty($info) ){
                die('Permission denied');
            }
            $project = $this->ProjectGlobalView->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $info['ApiKey']['company_id'],
                    'id' => $project_id
                )
            ));
            if( !$project ){
                die('Permission denied.');
            }
        } elseif($auth_code) {
			$check = $this->ProjectGlobalView->Project->find('count', array(
				'recursive' => -1,
				'conditions' => array(
                    'Project.id' => $project_id,
					'AuthCode.code' => $auth_code,
					'OR' => array(
						'AuthCode.expires >= "' . date('Y-m-d H:i:s') . '"',
						'AuthCode.expires is NULL',
						'AuthCode.expires' => '0000-00-00 00:00:00',
					),
                ),
				'joins' => array(
					array(
						'table' => 'auth_codes',
						'alias' => 'AuthCode',
						'conditions' => array('Project.company_id = AuthCode.company_id'),
						'type' => 'inner',
					),
				),
			));
            if( !$check ){
                die('Permission denied');
            }
        } else {
            die('Permission denied');
        }
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'fields' => array('id', 'project_id', 'attachment','is_file','is_https'),
            "conditions" => array('project_id' => $project_id)));

        if ($projectGlobalView) {
            $link = trim($this->_getPath($project_id) . $projectGlobalView['ProjectGlobalView']['attachment']);
            if (empty($link)) {
                $link = '';
            } else {
                if (!file_exists($link) || !is_file($link)) {
                    $link = '';
                }
                $info = pathinfo($link);
                $this->view = 'Media';
                $params = array(
                    'id' => !empty($info['basename']) ? $info['basename'] : '',
                    'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                    'name' => !empty($info['filename']) ? $info['filename'] : '',
                    'mimeType' => array(
                        'bmp' => 'image/bmp',
                        'ppt' => 'application/vnd.ms-powerpoint',
                        'pps' => 'application/vnd.ms-powerpoint',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                    ),
                    'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                );
                if (!empty($this->params['url']['download'])) {
                    $params['download'] = true;
                }
                $this->set($params);
            }
        }
        if (!$link && !empty($this->params['url']['download'])) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if($projectGlobalView['ProjectGlobalView']['is_file']==0){
           $this->set('url',$projectGlobalView['ProjectGlobalView']['attachment']);
           $this->set('is_https',$projectGlobalView['ProjectGlobalView']['is_https']);
        }
    }
	
	
	public function get_mime_type($file) {
        // our list of mime types
        $mime_types = array(
            "pdf"=>"application/pdf"
            ,"exe"=>"application/octet-stream"
            ,"zip"=>"application/zip"
            ,"docx"=>"application/msword"
            ,"doc"=>"application/msword"
            ,"xls"=>"application/vnd.ms-excel"
            ,"ppt"=>"application/vnd.ms-powerpoint"
            ,"gif"=>"image/gif"
            ,"png"=>"image/png"
            ,"jpeg"=>"image/jpg"
            ,"jpg"=>"image/jpg"
            ,"mp3"=>"audio/mpeg"
            ,"wav"=>"audio/x-wav"
            ,"mpeg"=>"video/mpeg"
            ,"mpg"=>"video/mpeg"
            ,"mpe"=>"video/mpeg"
            ,"mov"=>"video/quicktime"
            ,"avi"=>"video/x-msvideo"
            ,"3gp"=>"video/3gpp"
            ,"css"=>"text/css"
            ,"jsc"=>"application/javascript"
            ,"js"=>"application/javascript"
            ,"php"=>"text/html"
            ,"htm"=>"text/html"
            ,"html"=>"text/html",
            'mp4' => 'video/mp4'
        );

        $extension = strtolower(end(explode('.',$file)));

        return @$mime_types[$extension];
    }
	/* Get attachment for grid index*/
    public function attachment_index($project_id = null, $size=320) {
        $this->layout = false;
        $link = '';
		$link_index = '';
        $key = isset($_GET['sid']) ? $_GET['sid'] : '';
        if( $key ){
            $info = $this->ApiKey->retrieve($key);
            if( empty($info) ){
                die('Permission denied');
            }
            $project = $this->ProjectGlobalView->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $info['ApiKey']['company_id'],
                    'id' => $project_id
                )
            ));
            if( !$project ){
                die('Permission denied');
            }
        } else {
            die('Permission denied');
        }
        $projectGlobalView = $this->ProjectGlobalView->find("first", array(
            'fields' => array('id', 'project_id', 'attachment','is_file','is_https'),
            "conditions" => array('project_id' => $project_id)));

        if ($projectGlobalView) {
            $link = trim($this->_getPath($project_id) . $projectGlobalView['ProjectGlobalView']['attachment']);
			// debug( $link); exit;
            if (empty($link)) {
                $link = '';
            } else {
                if (!file_exists($link) || !is_file($link)) {
                    $link = '';
                }
				$info = pathinfo($link);
				$link_index = $info['dirname'] . '/' . $info['filename'].'_'.$size.'.'.$info['extension'];
				if (!file_exists($link_index) || !is_file($link_index)) {
                    // create file
					$type = explode('/', $this->get_mime_type($link));
					if($type[0] == 'image'){
						try {
							App::import("vendor", "resize");
							//resize image for thumbnail slideshow
							$resize = new ResizeImage($link);
							$resize->resizeTo(320, 0, 'maxwidth');
							$resize->saveImage($link_index);
						} catch (Exception $ex) {
							//wrong image, dont save
							@unlink($link_index);
							$link_index = '';
						}
					}else {
						$link_index = '';
					}
                }
				$info = pathinfo($link_index);
				
				// debug( $link_index); exit;
                $this->view = 'Media';
                $params = array(
                    'id' => !empty($info['basename']) ? $info['basename'] : '',
                    'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                    'name' => !empty($info['filename']) ? $info['filename'] : '',
                    'mimeType' => array(
                        'bmp' => 'image/bmp',
                        'ppt' => 'application/vnd.ms-powerpoint',
                        'pps' => 'application/vnd.ms-powerpoint',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                    ),
                    'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                );
                if (!empty($this->params['url']['download'])) {
                    $params['download'] = true;
                }
                $this->set($params);
            }
        }
        if (!$link && !empty($this->params['url']['download'])) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if($projectGlobalView['ProjectGlobalView']['is_file']==0){
           $this->set('url',$projectGlobalView['ProjectGlobalView']['attachment']);
           $this->set('is_https',$projectGlobalView['ProjectGlobalView']['is_https']);
        }
		$this->render('attachment');
    }
	
    protected function _getPath($project_id) {
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
        return $path;
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project delivrable', true), 'error');
            //$this->redirect($this->referer());
        }
		$ProjectGlobalView  = $this->ProjectGlobalView->find("first", array(
            'recursive' => -1,
			'fields' => array('project_id', 'id'),
            "conditions" => array('id' => $id))
			);
		$project_id = isset( $ProjectGlobalView['ProjectGlobalView']['project_id']) ?  $ProjectGlobalView['ProjectGlobalView']['project_id'] : '';
		if( !$this->_checkRole(true, $project_id)){
			$this->Session->setFlash(__('Permission denied', true), 'error');
			die;
		}
        $this->ProjectGlobalView->recursive = -1;
        $data = $this->ProjectGlobalView->read('project_id, attachment', $id);
        $file = $this->_getPath($data['ProjectGlobalView']['project_id']) . $data['ProjectGlobalView']['attachment'];
        if ($this->ProjectGlobalView->delete($id)) {
            if( file_exists($file) ){
                $info = pathinfo($file);
				$link_index = $info['dirname'] . '/' . $info['filename'] . '_320.' . $info['extension'];
                unlink($file);
                if( $this->MultiFileUpload->otherServer ){
                    $this->MultiFileUpload->deleteFileToServerOther($this->_getPath($data['ProjectGlobalView']['project_id']), $data['ProjectGlobalView']['attachment']);
                }
				try {
					@unlink($link_index);
				} catch (Exception $ex) {
					//
				}
            }
        }
        //$this->redirect($this->referer());
        die;
    }

}