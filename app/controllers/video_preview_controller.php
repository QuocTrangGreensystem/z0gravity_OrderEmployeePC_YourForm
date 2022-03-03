<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class VideoPreviewController extends AppController {
    public $uses = 'ProjectImage';

    public $components = array('MultiFileUpload');

    public $uploadFolder;
    public $uploadUrl = '/files/projects/';
    public $company_id;
    public $allowedExtensions;
    public $allowedThumbExtensions ="jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";

    public function beforeFilter(){
        parent::beforeFilter();
        App::import('vendor', 'MimeList', array('file' => 'MimeList.php'));
        App::import('vendor', 'VideoStream');
        App::import('Core', 'Folder');
        $this->loadModels('Project', 'Company');

        $this->uploadFolder = FILES . 'projects' . DS;
        $this->company_id = $this->employee_info["Company"]["id"];
        $this->allowedExtensions = 'mp4,m4v,flv,ogg,ogv,webm';
        $this->allowedThumbExtensions = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";

        $this->mimeTypes = MimeList::get($this->allowedExtensions);

        $this->set(array(
            'company_id' => $this->company_id,
            'uploadUrl' => $this->uploadUrl,
            'uploadFolder' => $this->uploadFolder,
            'allowedExtensions' => $this->allowedExtensions,
            'allowedThumbExtensions' => $this->allowedThumbExtensions,
        ));
    }

    public function index($project_id = null){
		// debug(  0); exit;
        $this->loadModels('Project', 'ProjectEmployeeManager');
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('video');
        //check owner
        $projectName = $this->ProjectImage->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $this->company_id
            ),
            'fields' => array('id', 'project_name')
        ));
        if( !$projectName ){
            $this->Session->setFlash(__('Project not found', true), 'error');
            $this->redirect('/projects');
        }
        $videos = $this->ProjectImage->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id,
                'type' => 'video'
            ),
            'order' => array('is_file' => 'DESC')
        ));
         // kiem tra PM co the change project manager select field
        $pmCanChange = $this->ProjectEmployeeManager->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'project_id' => $project_id
            )
        )); 
        // kiem tra PM o bang project
        $pmOfProject = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'project_manager_id' => $this->employee_info['Employee']['id'],
                'id' => $project_id
            )
        ));

        $pmCanChange = (!empty($pmCanChange) || !empty($pmOfProject) || $this->employee_info['CompanyEmployeeReference']['role_id'] == 2) ? true : false;
        if( !empty($this->params['requested']) )return $videos;
        $this->set(compact('videos', 'project_id', 'projectName', 'pmCanChange'));
    }

    public function upload($project_id = null){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $result = array();
        $_FILES['FileField'] = array();
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if(!empty($_FILES)){
            $path = $this->_path($project_id);
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedExtensions;
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('Could not create directory: %s.', true), $path), 'error');
            }
            //on upload complete
            if (!empty($attachment)) {
                //save db
                $size = $attachment['attachment']['size'];
                $attachment = $attachment['attachment']['attachment'];
                $this->ProjectImage->create();
                if( $this->ProjectImage->save(array(
                    'file' => $attachment,
                    'size' => $size,
                    'project_id' => $project_id,
                    'company_id' => $this->company_id,
                    'type' => 'video'
                )) ){
                    $result['ProjectImage']['size'] = $size;
                    $result['type'] = 'ok';
                } else {
                    @unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
            } else {
                die(json_encode(array(
                    'status' => 'error',
                    'hint' => __('Not allowed', true)
                )));
            }
        }
        die(json_encode($result));
    }

    public function stream($project_id, $video_id){
        $data = $this->ProjectImage->read('company_id, project_id, file', $video_id);
        if( !empty($data) && $data['ProjectImage']['company_id'] == $this->company_id && $project_id == $data['ProjectImage']['project_id'] ){
            $sfile = $data['ProjectImage']['file'];
            $file = $this->_path($project_id, $sfile);
            $mime = MimeList::get($sfile);
            $stream = new VideoStream($file, $mime);
            $stream->start();
        }
        die;
    }

    public function saveUrl($project_id){
		if ( !$this->_checkRole(false, $project_id) ) {
			exit;
		}
        if( !empty($this->data['url']) ){
            $this->ProjectImage->create();
            $this->ProjectImage->save(array(
                'file' => $this->data['url'],
                'is_file' => 0,
                'type' => 'video',
                'company_id' => $this->company_id,
                'project_id' => $project_id
            ));
            $this->Session->setFlash(__('Saved', true), 'success');
        }
        $this->redirect(array( 
			'controller' => $this->params['controller'], 
			'action' => 'index',
			$project_id
			)
		);
    }

    public function delete($project_id, $video_id){
        if ( !$this->_checkRole(false, $project_id) ) {
			$this->redirect( array('action' => 'index', $project_id));
		}
        $count = $this->ProjectImage->Project->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $this->company_id
            )
        ));
        if( !$count ){
            $this->Session->setFlash(__('Project not found', true), 'error');
            $this->redirect('/projects');
        }
        $data = $this->ProjectImage->read('file, is_file, thumbnail', $video_id);
        if( !empty($data) ){
            $this->ProjectImage->delete($video_id);
            if( $data['ProjectImage']['is_file'] ){
                @unlink($this->_path($project_id, $data['ProjectImage']['file']));
            }
            if(!empty($data['ProjectImage']['thumbnail'] )){
                @unlink($this->_path($project_id, $data['ProjectImage']['thumbnail']));
            }
            $this->Session->setFlash(__('Deleted', true), 'success');
        }
        $this->redirect(array('action' => 'index', $project_id));
    }

    protected function _path($pid, $file = ''){
        return $this->uploadFolder . $this->company_id . DS . $pid . DS . $file;
    }

    public function download($project_id, $video_id = null){
		if ( !$this->_checkRole(false, $project_id) ) {
			exit;
		}
        $count = $this->ProjectImage->Project->find('count', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $project_id,
                'company_id' => $this->company_id
            )
        ));
        if( !$count ){
            $this->Session->setFlash(__('Project not found', true), 'error');
            $this->redirect('/projects');
        }
        $data = $this->ProjectImage->read('company_id, project_id, type, file, is_file', $video_id);
        if( !empty($data) && $data['ProjectImage']['is_file'] && $data['ProjectImage']['type'] == 'video' ){
            $fileInfo = pathinfo($data['ProjectImage']['file']);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: filename=\"{$fileInfo["basename"]}\"");
            header('Cache-Control: cached');
            header('Pragma: public');
            if( @readfile($this->_path($project_id, $fileInfo['basename']) ) )
                die;

        }
        die;
    }
	function add_new_video($project_id = null){
		if ( !$this->_checkRole(false, $project_id) ) {
			exit;
		}
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $result = array();
		$is_upload_thumb = isset( $this->data['popupUpload']['isThumb'] ) ? $this->data['popupUpload']['isThumb']: '0';
		$video_id = isset( $this->data['popupUpload']['videoId'] ) ? $this->data['popupUpload']['videoId']: '';
		$thumbExt = isset( $this->data['popupUpload']['thumbExt'] ) ? $this->data['popupUpload']['thumbExt']: '';
		$thumb_name = '';
		if( $is_upload_thumb && empty($video_id) ){
			$this->Session->setFlash(__('Not allowed.', true), 'error');
			$result = array(
				'status' => 'error',
				'hint' => __('Not allowed to upload here', true)
			);
			die(json_encode($result));
		}
		if($is_upload_thumb){
			$thumb_name = isset( $this->data['popupUpload']['videoName'] ) ? $this->data['popupUpload']['videoName']: '';
			$thumb_name = explode('.',$thumb_name);
			if( count($thumb_name) >1 ) {
				unset($thumb_name[count($thumb_name)-1]);
			}
			$thumb_name = implode('', $thumb_name).'_thumb'.($thumbExt ? '.'.$thumbExt : '');
		}
        $_FILES['FileField'] = array();
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
			if( $is_upload_thumb ) $_FILES['FileField']['name']['attachment'] = $thumb_name;
        }
        if(!empty($_FILES)){
            $path = $this->_path($project_id);
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedExtensions;
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
				if( $is_upload_thumb ){
					$this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedThumbExtensions;
				}
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('Could not create directory: %s.', true), $path), 'error');
            }
            //on upload complete
            if (!empty($attachment)) {
                //save db
                $size = $attachment['attachment']['size'];
                $attachment = $attachment['attachment']['attachment'];
				if( $is_upload_thumb){
					$this->ProjectImage->create();
					$this->ProjectImage->id = $video_id;
					$upload_result = $this->ProjectImage->saveField('thumbnail', $attachment);
				}else{
					$this->ProjectImage->create();					
					$upload_result = $this->ProjectImage->save(array(
						'id' => $this->ProjectImage->id,
						'file' => $attachment,
						'size' => $size,
						'project_id' => $project_id,
						'company_id' => $this->company_id,
						'type' => 'video'
					));
				}
                if( !empty($upload_result)){
					$upload_result['ProjectImage']['id'] = $this->ProjectImage->id;
                    $result = $upload_result;
                    $result['type'] = 'ok';
                    $result['is_upload_file'] = empty( $is_upload_thumb ) ? !$is_upload_thumb : '0';
					
                } else {
                    @unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
            } else {
                $result = array(
                    'status' => 'error',
                    'hint' => __('Not allowed', true)
                );
				$this->Session->setFlash(__('Not allowed.', true), 'error');
            }
        }
        die(json_encode($result));
	}
}
