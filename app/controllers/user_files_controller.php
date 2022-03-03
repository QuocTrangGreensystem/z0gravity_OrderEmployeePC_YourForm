<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UserFilesController extends AppController {

	public $uses = array('Employee', 'Company');
	private $mimeTypes = array(
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'html' => 'text/html',
        'exe' => 'application/octet-stream',
        'zip' => 'application/zip',
        'doc' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpeg' => 'image/jpg',
        'jpg' => 'image/jpg',
        'php' => 'text/plain'
    );
	var $components = array('MultiFileUpload', 'PImage');
	public function beforeFilter(){
		parent::beforeFilter();
	    App::import('Core', 'Folder');
	}
	// small | large
	public function avatar($employee_id, $size = 'small'){
		$avatar = $this->Employee->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $employee_id
			),
			'fields' => array('avatar', 'avatar_resize')
		));
		$file = FILES . 'avatar_employ' . DS . $this->company_dir() . $employee_id . DS;
		if( $size == 'small' ){
			$file .= $avatar['Employee']['avatar_resize'];
		} else {
			$file .= $avatar['Employee']['avatar'];
		}
		if( !file_exists($file) ){

			if( empty($_GET['fallback']) ){
				$file = IMAGES . 'business' . DS . 'avatar.gif';
			} else {
				header('Location: ' . $_GET['fallback']);
				die;
			}
		}
		$this->output($file);
	}
	private function output_bk($file, $download = false){
		ob_clean();
		$filename = explode(DS, $file);
		$filename = $filename[sizeof($filename)-1];
		header("Content-Disposition: inline; filename=\"" . $filename . "\"");
		header('Content-Type: ' . $this->mime($file));
	    readfile($file);
	    die;
	}
	private function output($file, $download = false){
		ob_clean();
		$filename = explode(DS, $file);
		$filename = $filename[sizeof($filename)-1];
		header("Content-Disposition: inline; filename=\"" . $filename . "\"");
		header('Content-Type: ' . $this->mime($file));
	   	readfile($file);
	   die;
	}
	private function mime($file){
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if( !isset($this->mimeTypes[$ext]) ){
			return 'txt';
		}
		return $this->mimeTypes[$ext];
	}

	private function company_dir(){
        $dir = $this->employee_info['Company']['dir'];
        if( empty($dir) )return '';
        else return $dir . DS;
	}

	private function company_id(){
        $dir = $this->employee_info['Company']['id'];
        if( empty($dir) )return '';
        else return $dir . DS;
	}

	public function image(){
		$path = str_replace('/', DS, @$_GET['path']);
		$file = FILES . $path;
		if( file_exists($file) ){
			$this->output($file);
		}
		die;
	}

	public function download($path){

	}

	public function ticket_image_upload($id = ''){
		$dir = $this->company_id() . 'tickets' . DS . $id . DS;

		reset ($_FILES);
		$temp = current($_FILES);

		$i = 0;

		do {
			$name = '[uploaded]' . ($i ? $i . '_' : '') . $temp['name'];
			$destination = FILES . $dir . $name;
			$i++;
		} while ( file_exists($destination) );
		new Folder(dirname($destination), true, 0777);

		move_uploaded_file($temp['tmp_name'], $destination);

		$location = '/user_files/image?path=' . str_replace(DS, '/', $dir . $name);

		die(json_encode(array('location' => $location)));
	}

	public function upload_editor_image(){
		$dir = $this->company_id() . 'editor_images' . DS;

		reset ($_FILES);
		$temp = current($_FILES);

		$i = 0;

		do {
			$name = '[uploaded]_' . ($i ? $i . '_' : '') . $temp['name'];
			$destination = FILES . $dir . $name;
			$i++;
		} while ( file_exists($destination) );
		new Folder(dirname($destination), true, 0777);

		move_uploaded_file($temp['tmp_name'], $destination);

		$location = '/user_files/image?path=' . str_replace(DS, '/', $dir . $name);

		die(json_encode(array('location' => $location)));
	}
}