<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class MultiFileUploadComponent extends Object {
    /**
     * Settings Upload And Download len 1 server khac
     */
	var $controller;
    var $otherServer = false;
    var $IP = '87.106.28.63';
    var $account = 'myazuree';
    var $password = 'gs#azuree#2015';


    var $content_types = array(
    	'jpg'	=>	'image/jpeg',
    	'png'	=>	'image/png',
    	'gif'	=>	'image/gif',
    );
    /* component configuration */
    var $name = 'MultiFileUploadComponent';
    var $params = array();
    var $errorCode = null;
    var $errorMessage = null;

    // file and path configuration
    var $uploadpath;
    var $overwrite = false;
    var $encode_filename = true;
    var $filename;
    var $postName = 'Filedata';
	var $errors = array();

    var $_properties = array(
		"AttachTypeAllowed"		=>	"jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,pdf,xls,docx,flv,mpeg,avi",
		"ImgTypes"			     =>	"jpg,jpeg,bmp,gif,png",
		"MaxSize"				=>	1024000000, // 9MB
		"MaxWidth"				=>	10000,
		"MaxHeight"				=>	10000,
	);

    /**
     * Connect To Server
     */
    private function _connectServer(){
        $ftpConnect = ftp_connect($this->IP);
        $ftpLogin = ftp_login($ftpConnect, $this->account, $this->password);
        $result = true;
        if(!$ftpConnect || !$ftpLogin){
            $result = false;
        }
        return array($result, $ftpConnect);
    }

    /**
     * Upload file to server other.
     */
    public function uploadFileToServerOther($path = null, $file = null, $redirect = null){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($path)){
            $pathLocal = $path . $file;
            $path = explode('webroot', $path);
            $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
            $splitPaths = explode('/', trim($path, '/'));
            $default = '';
            foreach($splitPaths as $splitPath){
                $default .= '/' . $splitPath;
                @ftp_mkdir($ftpConnect, $default);
                @ftp_chmod($ftpConnect, 0777, $default);
            }
            ftp_put($ftpConnect, $path . $file, $pathLocal, FTP_BINARY);
        }
        ftp_close($ftpConnect);
    }

    /**
     * Upload Multiple file to server other.
     */
    public function uploadMultipleFileToServerOther($datas = array(), $redirect = null, $oldDatas = array()){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($datas)){
            foreach($datas as $data){
                $pathLocal = $data['path'] . $data['file'];
                $path = explode('webroot', $data['path']);
                $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
                $splitPaths = explode('/', trim($path, '/'));
                $default = '';
                foreach($splitPaths as $splitPath){
                    $default .= '/' . $splitPath;
                    @ftp_mkdir($ftpConnect, $default);
                    @ftp_chmod($ftpConnect, 0777, $default);
                }
                ftp_put($ftpConnect, $path . $data['file'], $pathLocal, FTP_BINARY);
            }
        }
        if($conn == true && !empty($oldDatas)){
            foreach($oldDatas as $oldData){
                $pathLocal = $oldData['path'] . $oldData['file'];
                $path = explode('webroot', $oldData['path']);
                $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
                ftp_delete($ftpConnect, $path . $oldData['file']);
            }
        }
        ftp_close($ftpConnect);
    }

    /**
     * Upload Multiple file to server other.
     * /uploads
     */

    public function uploadTranslate($datas = array(), $redirect = null){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        $rootPath = '/uploads/locale';
        if($conn == true && !empty($datas)){
            foreach($datas as $data){
                $pathLocal = $data['path'] . $data['file'];
                $path = explode('locale', $data['path']);
                $path = $rootPath . (!empty($path[1]) ? str_replace('\\', '/', $path[1]) : '');
                $splitPaths = explode('/', trim($path, '/'));
                $default = '';
                foreach($splitPaths as $splitPath){
                    $default .= '/' . $splitPath;
                    @ftp_mkdir($ftpConnect, $default);
                    @ftp_chmod($ftpConnect, 0777, $default);
                }
                ftp_put($ftpConnect, $path . $data['file'], $pathLocal, FTP_BINARY);
            }
        }
        ftp_close($ftpConnect);
    }

    /**
     * Download file to server other
     */
    public function downloadFileToServerOther($path = null, $file = null){
        set_time_limit(0);
        $path = explode('webroot', $path);
        $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
        header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=\"".$file."\"");
		header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
        echo file_get_contents('ftp://' . $this->account . ':' . $this->password. '@' . $this->IP . $path . $file);
        die;
    }

    /**
     * Doc link tu server khac
     */
    public function readFileToServerOther($path = null, $file = null){
        set_time_limit(0);
        $path = explode('webroot', $path);
        $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
        list($conn, $ftpConnect) = $this->_connectServer();
        $res = ftp_size($ftpConnect, $path . $file);
        if ($res != -1) {
            return $info = pathinfo('ftp://' . $this->account . ':' . $this->password. '@' . $this->IP . $path . $file);
        } else {
            return '';
        }
        ftp_close($ftpConnect);
    }

    /**
     * Down file tu server khac
     */
    public function dowloadFileToServerOtherUsingFtp($path = array(), $file = null){
        set_time_limit(0);
        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($path) && !empty($file)){
            $pathLocal = $path;
            $path = explode('webroot', $path);
            $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
            ftp_get($ftpConnect, $pathLocal . $file, $path . $file, FTP_BINARY);
            ftp_get($ftpConnect, $pathLocal . 'r_' . $file, $path . 'r_' . $file, FTP_BINARY);
            ftp_get($ftpConnect, $pathLocal . 'l_' . $file, $path . 'l_' . $file, FTP_BINARY);
        }
        ftp_close($ftpConnect);
    }

    /**
     * Xoa File tren mot server khac.
     */
    public function deleteFileToServerOther($path = null, $file = null, $redirect = null){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($path)){
            $path = explode('webroot', $path);
            $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
            ftp_delete($ftpConnect, $path . $file);
        }
        ftp_close($ftpConnect);
    }

    /**
     * Xoa File va ket hop Upload File tren mot server khac.
     */
    public function deleteAndUploadFileToServerOther($path = null, $fileOld = null, $fileNew = null, $redirect = null){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($path)){
            $pathLocal = $path . $fileNew;
            $path = explode('webroot', $path);
            $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
            ftp_delete($ftpConnect, $path . $fileOld);
            $splitPaths = explode('/', trim($path, '/'));
            $default = '';
            foreach($splitPaths as $splitPath){
                $default .= '/' . $splitPath;
                @ftp_mkdir($ftpConnect, $default);
            }
            ftp_put($ftpConnect, $path . $fileNew, $pathLocal, FTP_BINARY);
        }
        ftp_close($ftpConnect);
    }

    /**
     * Delete Multiple file to server other.
     */
    public function deleteMultipleFileToServerOther($datas = array(), $redirect = null){
        set_time_limit(0); // ko gioi han thoi gian su dung
        header("Content-type: text / plain");
        header('Connection: close'); // ngat ket noi
        ob_start();  // khoi tao 1 bo dem
        header('Content-Length: '.ob_get_length()); // do dai cua 1 noi dung
        session_write_close(); // optional, this will close the session.
        header('Location: '. $redirect); // nhay den 1 trang nao do. redirect page
        ob_end_flush(); // dong bo dem
        ob_flush();
        flush(); // xoa bo dem
        ignore_user_abort(true); // chong ngat ket noi giua chung. Khi user tat trinh duyet
        if (function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        list($conn, $ftpConnect) = $this->_connectServer();
        if($conn == true && !empty($datas)){
            foreach($datas as $data){
                $pathLocal = $data['path'] . $data['file'];
                $path = explode('webroot', $data['path']);
                $path = !empty($path[1]) ? str_replace('\\', '/', $path[1]) : '';
                ftp_delete($ftpConnect, $path . $data['file']);
            }
        }
        ftp_close($ftpConnect);
    }

    /**
     * Contructor function
     * @param Object &$controller pointer to calling controller
     */
    function startup(&$controller) {
        // initialize members
        $this->uploadpath = UPLOADS;

        //keep tabs on mr. controller's params
        $this->params = $controller->params;
		$this->IP = $controller->getConfig('upload_multiple_server_ip');
        if( $_SERVER['SERVER_ADDR'] == $this->IP )
            $this->IP = $controller->getConfig('upload_multiple_server_ip_1');
		$this->otherServer = $controller->getConfig('upload_multiple_server') == '1';
		$this->account = $controller->getConfig('upload_multiple_server_user');
		$this->password = $controller->getConfig('upload_multiple_server_pass');
    }

    /**
     * Uploads a file to location
     * @return boolean true if upload was successful, false otherwise.
     */
    function upload() {
        $attachment = "";
		$attachment_name = "";
		$attachment_size = "";
		$extension = "";
		$check = true;

		$returnArr = array();
		if (is_array($_FILES)) {
			foreach ($_FILES['FileField']['name'] as $key => $value) {
				if ($_FILES['FileField']["size"][$key] > 0) {
					if ($this->check_validate($_FILES,$key)) {
						continue;
					} else {
						$this->setError(1000, 'File system save failed.');
						$check = false;
						break;
					}
				}
			}

			if ($check) {
				$returnArr = array();
				foreach ($_FILES['FileField']['name'] as $key => $value) {
					if ($_FILES['FileField']['size'][$key] > 0)
						$returnArr[$key] = $this->write($_FILES,$key);
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		return $returnArr;
    }

    /**
     * finds a unique name for the file for the current directory
     * @param array an array of filenames which exist in the desired upload directory
     * @return string a unique filename for the file
     */
    function findUniqueFilename($existing_files = null) {
        // append a digit to the end of the name
        $filenumber = 0;
        $filesuffix = '';
        $fileparts = explode('.', $this->filename);
        $fileext = '.' . array_pop($fileparts);
        $filebase = implode('.', $fileparts);

        if (is_array($existing_files)) {
            do {
                $newfile = $filebase . $filesuffix . $fileext;
                $filenumber++;
                $filesuffix = '(' . $filenumber . ')';
            } while (in_array($newfile, $existing_files));
        }

        return $newfile;
    }

    /**
     * moves the file to the desired location from the temp directory
     * @return boolean true if the file was successfully moved from the temporary directory to the desired destination on the filesystem
     */
    function write($postFile,$index=-1) {
        // Include libraries
        if (!class_exists('Folder')) {
            uses ('folder');
        }

        $moved = false;
        $folder = new Folder($this->uploadpath, true, 0755);
        //$folder = true;
        if (!$folder) {
            $this->setError(1500, 'File system save failed.', 'Could not create requested directory: ' . $this->uploadpath);
        } else {
        	$attachment = $postFile['FileField']["tmp_name"][$index];
			App::import("vendor","str_utility");
            $str_utility = new str_utility();
            $attachment_name = str_replace(" ", "_",strtolower($str_utility->removeVNUnicode($postFile['FileField']['name'][$index])));
	    	$attachment_size = $postFile['FileField']['size'][$index];
	    	$extension = substr(strrchr($attachment_name,"."),1);


        	if ($this->encode_filename) {
				$newpath = "";
				$unique = substr(md5(microtime()),0,32);
				$this->filename = $unique.".".$extension;

        	} else if (!$this->overwrite) {
                $contents = $folder->read();  //get directory contents
                $this->filename = $attachment_name;
                $this->filename = $this->findUniqueFilename($contents[1]);  //pass the file list as an array
            }

            //$str_utility->removeVNUnicode(
	        $newpath = $this->uploadpath.$this->filename;

	        if (is_uploaded_file($attachment)) {
	        	$moved = move_uploaded_file($attachment, $newpath);
				$filesize=filesize($newpath);
				if ($filesize!=$attachment_size || strstr($newpath,"..")!="") {
					@unlink($newpath);
					$this->setError(1000, 'File system save failed.');
				}
			}
        }
        if ($moved) {
        	if ($this->encode_filename) {
        		return array(
        			"index"			=>	$index,
        			"encrypt_name"	=>	$this->filename,
        			"original_name"	=>	$attachment_name,
        			"size"			=>	$attachment_size,
        			"extension"		=>	$extension
        		);
        	}
        	else
        		return array(
        			$index=>$this->filename,
        			"size"			=>	$attachment_size,
        			"extension"		=>	$extension
        		);
        }
        else
        	return false;
    }

    /**
     * parses file upload error code into human-readable phrase.
     * @param int $err PHP file upload error constant.
     * @return string human-readable phrase to explain issue.
     */
    function getUploadErrorMessage($err) {
        $msg = null;
        switch ($err) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
                $msg = ('The uploaded file exceeds the upload_max_filesize directive ('.ini_get('upload_max_filesize').') in php.ini.');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $msg = ('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = ('The uploaded file was only partially uploaded.');
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = ('No file was uploaded.');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $msg = ('The remote server has no temporary folder for file uploads.');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $msg = ('Failed to write file to disk.');
                break;
            default:
                $msg = ('Unknown File Error. Check php.ini settings.');
        }

        return $msg;
    }

    /**
     * sets an error code which can be referenced if failure is detected by controller.
     * note: the amount of info stored in message depends on debug level.
     * @param int $code a unique error number for the message and debug info
     * @param string $message a simple message that you would show the user
     * @param string $debug any specific debug info to include when debug mode > 1
     * @return bool true unless an error occurs
     */

    function setError($code = 1, $message = 'An unknown error occured.', $debug = '') {
        if (!isset($this->errors[$code])) {
        	$this->errors[$code] = $message;
        } else {
        	if (is_array($this->errors[$code])) {
        		$this->errors[$code][] = $message;
        	} else {
        		$this->errors[$code] = array(
        			$this->errors[$code],
        			$message
        		);
        	}
        }
    }

    private function check_validate($postFile,$index=-1)
	{
		$attachment = $postFile['FileField']["tmp_name"][$index];
	   	$attachment_name = strtolower($postFile['FileField']["name"][$index]);
	   	$attachment_size = $postFile['FileField']["size"][$index];
	   	$extension = substr(strrchr($attachment_name,"."),1);


	   	if ($attachment_size == 0) {
	   		$this->setError(2000, __('No file was uploaded.', true));
	   		return false;
	   	}

	   	//debug($this->_properties['AttachTypeAllowed']);exit;
	    if (!in_array($extension,explode(',', $this->_properties['AttachTypeAllowed'])) && $this->_properties['AttachTypeAllowed'] != "*") {
			$this->setError(2000, __("The type of uploaded file ({$attachment_name}) is invalid.", true));
	   		return false;
		}

		if ($attachment_size > $this->_properties['MaxSize']) {
			$this->setError(2000, __("The uploaded file ({$attachment_name}) exceeds the limited size.", true));
	   		return false;
		}

		if ($this->isImage($attachment_name)) {
			$sizeinfo = getimagesize($attachment);
			if ($sizeinfo[0] > $this->_properties['MaxWidth'] || $sizeinfo[1] > $this->_properties['MaxHeight']) {
				$this->setError(2000, __("The dimension of uploaded file ({$attachment_name}) is invalid.", true));
	   			return false;
			}
		}
		return true;
	}

	function getContentType($extension) {
		return $this->content_types[$extension];
	}

	function isImage($filename) {
		$file_info = $this->getFileExtension($filename);
		return in_array($file_info['extension'], array(".jpg", ".jpeg", ".png", ".gif"));
	}

	function getFileExtension($filename) {
		// Get File Extension & Generate Thumbnail Name
		$ext = strrchr($filename, ".");
		$pos = strrpos($filename, ".");
		$main_part = substr($filename,0,$pos);
		return array(
			'filename'	=>	$main_part,
			'extension'	=>	strtolower($ext)
		);
	}
}
?>
