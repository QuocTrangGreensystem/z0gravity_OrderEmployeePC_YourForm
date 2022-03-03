<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectImagesController extends AppController {
    public $components = array('MultiFileUpload');

    public $uploadFolder;
    public $uploadUrl = '/files/projects/';
    public $company_id;
    public $allowedExtensions;

    public function beforeFilter(){
        parent::beforeFilter();
        $this->uploadFolder = FILES . 'projects' . DS;
        $this->company_id = $this->employee_info["Company"]["id"];
        $this->allowedExtensions = 'pdf,jpg,jpeg,bmp,gif,png';
        $this->set(array(
            'company_id' => $this->company_id,
            'uploadUrl' => $this->uploadUrl,
            'uploadFolder' => $this->uploadFolder,
            'allowedExtensions' => $this->allowedExtensions,
        ));
    }

    public function index($projectId = null){
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
                $this->redirect(array(
                    'controller' => 'project_images_preview',
                    'action' => 'index/'.$projectId

                ));
        }
        $this->_checkRole(false, $projectId);
        $this->_checkWriteProfile('image');
        //check owner
        $projectName = $this->ProjectImage->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $projectId,
                'company_id' => $this->company_id
            ),
            'fields' => array('id', 'project_name')
        ));
        if( !$projectName ){
            $this->Session->setFlash(__('Project not found', true), 'error');
            $this->redirect('/projects');
        }
        $images = $this->ProjectImage->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $projectId,
                'type' => array('image', 'application')
            )
        ));
        $listDimencision = array();
        foreach ($images as $key => $value) {
            $fileInfo = pathinfo($value['ProjectImage']['file']);
            $p = $this->_path($value['ProjectImage']['company_id'], $value['ProjectImage']['project_id'], $fileInfo['basename']);
            list($width, $height) = getimagesize($p);
            $listDimencision[$value['ProjectImage']['id']]['width'] = $width;
            $listDimencision[$value['ProjectImage']['id']]['height'] = $height;
        }
        if( !empty($this->params['requested']) )return $images;
        $this->set('project_id', $projectId);
        $this->set(compact('images', 'projectName', 'listDimencision'));
    }

    function _size($size) {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

     public function upload($company_id = null, $projectId = null){
        //tao folder companyId/pId/
        ini_set('memory_limit', '512M');
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
            $path = $this->_path($company_id, $projectId);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedExtensions;
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
            }
            if (!empty($attachment)) {
                //save db
                $size = $attachment['attachment']['size'];
                $attachment = $attachment['attachment']['attachment'];
                $type = explode('/', $this->get_mime_type($attachment));
                if( $type[0] == 'image' ){
                    try {
                        App::import("vendor", "resize");
                        //resize image for thumbnail slideshow
                        $resize = new ResizeImage($path . $attachment);
                        $resize->resizeTo(320, 180, 'exact');
                        $resize->saveImage($path . 'r_' . $attachment);

                        $resize = new ResizeImage($path . $attachment);
                        list( $_w, $_h) = getimagesize($path . $attachment);
						if(  $_w > 800 && $_h > 450){
							$resize->resizeTo(800, 450);
						}else{
							$resize->resizeTo($_w, $_h);					
						}
                        $resize->saveImage($path . 'l_' . $attachment);
                        $dataSession = array(
                            'path' => $path,
                            'file' => $attachment
                        );
                        $dataSessionOne = array(
                            'path' => $path,
                            'file' => 'r_' . $attachment
                        );
                        $dataSessionTwo = array(
                            'path' => $path,
                            'file' => 'l_' . $attachment
                        );
                    } catch (Exception $ex) {
                        //wrong image, dont save
                        @unlink($path . $attachment);
                        @unlink($path . 'r_' . $attachment);
                        @unlink($path . 'l_' . $attachment);
                        die(json_encode(array(
                            'status' => 'error',
                            'hint' => __('Not an image', true)
                        )));
                    }
                } else {
                    //$_SESSION['file_multiupload'][] = array(
                    //  'path' => $path,
                    //  'file' => $attachment
                    //);
                }

                $this->ProjectImage->create();
                if( $this->ProjectImage->save(array(
                    'file' => $attachment,
                    'size' => $size,
                    'project_id' => $projectId,
                    'company_id' => $company_id,
                    'type' => $type[0]
                )) ){
                    $result = $this->ProjectImage->read(null);
                    $result['ProjectImage']['size'] = $this->_size($result['ProjectImage']['size']);
                    $result['type'] = 'ok';
                } else {
                    @unlink($path . $attachment);
                    @unlink($path . 'r_' . $attachment);
                    @unlink($path . 'l_' . $attachment);
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

        $file_arr = explode('.',$file);
        $extension = strtolower(end($file_arr));
        return @$mime_types[$extension];
    }

    public function show($pid = null, $id = null, $type = ''){
        $data = $this->ProjectImage->read('company_id, project_id, file', $id);
        if( !empty($data) && $data['ProjectImage']['company_id'] == $this->company_id && $pid == $data['ProjectImage']['project_id'] ){
            $fileInfo = pathinfo($data['ProjectImage']['file']);
            header("Content-type: " . $this->get_mime_type($data['ProjectImage']['file']));
            if( @readfile($this->_path($data['ProjectImage']['company_id'], $data['ProjectImage']['project_id'], substr($type, 0, -5) . $fileInfo['basename']) ) )
                die;
        }
        die;
    }

    public function delete($id = null, $project_id = null){
        if( !$id ){
            $this->Session->setFlash(__('Project not found', true), 'error');
            $this->redirect('/projects');
        }
		$data = $this->ProjectImage->read('company_id, project_id, file, type', $id);
		$project_id = @$data['ProjectImage']['project_id'];
        $this->_checkRole(false, $project_id);
        if( $data ){
            $path = $this->_path($data['ProjectImage']['company_id'], $data['ProjectImage']['project_id']);
            $name = $data['ProjectImage']['file'];
            @unlink($path . 'r_'.$name);
            @unlink($path . 'l_'.$name);
            @unlink($path . $name);

            $this->ProjectImage->delete($id);
            if($this->MultiFileUpload->otherServer == true){
                $datas = array(
                    array(
                        'path' => $path,
                        'file' => $name
                    )
                );
                if( $data['ProjectImage']['type'] == 'image' ){
                    array_push($datas, array(
                            'path' => $path,
                            'file' => 'r_'.$name
                        ),
                        array(
                            'path' => $path,
                            'file' => 'l_'.$name
                        )
                    );
                }
                $this->MultiFileUpload->deleteMultipleFileToServerOther($datas, '/project_images/index/' . $project_id);
            }

        }
        $this->redirect('/project_images/index/' . $project_id);
    }
    public function attachment($project_id = null, $file_id = null, $file_type = '') {
        $this->layout = false;
        $link = '';
        $projectImage = $this->ProjectImage->find("first", array(
            'recursive' => -1,
            'fields' => array('id', 'project_id', 'file','is_file'),
            "conditions" => array('id' => $file_id)));
        if ($projectImage) {
            $link = trim($this->_path($this->employee_info['Company']['id'], $project_id, $file_type.$projectImage['ProjectImage']['file']));
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
       
    }
    protected function _path($c, $i, $f = ''){
        return $this->uploadFolder . $c . DS . $i . DS . $f;
    }
	public function get_thumbnail($project_id = null, $file_id = null, $file_type = '') {
		$this->layout = false;
        $link = '';
        $projectImage = $this->ProjectImage->find("first", array(
            'recursive' => -1,
            'fields' => array('id', 'project_id', 'file','is_file','thumbnail' ),
            "conditions" => array('id' => $file_id)));
        if ($projectImage) {
            $link = trim($this->_path($this->employee_info['Company']['id'], $project_id, $projectImage['ProjectImage']['thumbnail']));
            if (empty($link)) {
                $link = '';
            } else {
                if (!file_exists($link) || !is_file($link)) {
                    $link = '';
                }
                $info = pathinfo($link);
				// debug( $info); exit;
                $this->view = 'Media';
                $params = array(
                    'id' => !empty($info['basename']) ? $info['basename'] : '',
                    'path' => !empty($info['dirname']) ? $info['dirname'] . '/' : '',
                    'name' => !empty($info['filename']) ? $info['filename'] : '',
                    'mimeType' => array(
                        'bmp' => 'image/bmp',
                        'png' => 'image/png',
                        'jpeg' => 'image/jpeg',
                        'jpg' => 'image/jpeg',
                        'bmp' => 'image/bmp',
                    ),
                    'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                );
                if (!empty($this->params['url']['download'])) {
                    $params['download'] = true;
                }
                $this->set($params);
            }
		}
		if (!$link) {
            die('false');
        }
	}

    public function download($id = null){
        $data = $this->ProjectImage->read('company_id, project_id, file', $id);
        if( !empty($data) ){
            $fileInfo = pathinfo($data['ProjectImage']['file']);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: filename=\"".$fileInfo["basename"]."\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            if( @readfile($this->_path($data['ProjectImage']['company_id'], $data['ProjectImage']['project_id'], $fileInfo['basename']) ) )
                die;

        }
        die('File not found!');
    }
}
