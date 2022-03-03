<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class UploadOtherServersController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'UploadOtherServers';
    /**
     * Controller using model
     * @var array
     * @access public
     */
    var $uses = array();
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');
    /**
     * Xoa session file_multiupload and file_multiupload_redirect
     */
   	public function deleteSessionMultiupload(){
		if(isset($_SESSION['file_multiupload'])){
            $_SESSION['file_multiupload'] = array();
		}
        if(isset($_SESSION['file_multiupload_redirect'])){
            $_SESSION['file_multiupload_redirect'] = '';
		}
		exit;
	}
	public function multiuploadToOtherServer(){
		if(isset($_SESSION['file_multiupload']) && !empty($_SESSION['file_multiupload']) && !empty($_SESSION['file_multiupload_redirect'])){
            if($this->MultiFileUpload->otherServer == true){
                $this->MultiFileUpload->uploadMultipleFileToServerOther($_SESSION['file_multiupload'], $_SESSION['file_multiupload_redirect']);
            }
		}
		exit;
	}
}
?>