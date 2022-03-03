<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ColorsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Colors';

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');
    public $allowedFiles = "jpg,jpeg,bmp,gif,png";
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions[] = 'attachment';
        $this->Auth->allowedActions[] = 'logo_client';
        $this->set('allowedFiles', $this->allowedFiles);
    }

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {

        $Color= ClassRegistry::init('Color');
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $company_id = $this->employee_info["Company"]["id"];
            $employee_id = $this->employee_info["Employee"]["id"];
        }else
            $company_id = "";
        $cond = array();
        if(!empty($company_id)){
            $cond['company_id'] = $company_id;
        }else{
			$cond[] = 'company_id is null';
		}
        $colors = $Color->find('first', array(
            'recursive' => -1,
            'conditions' => $cond,
            'fields' => array('*')
        ));
        $this->Session->write('Auth.color_info', $colors);
        $this->set(compact('employee_id', 'company_id', 'colors'));

    }

    function login_setting() {
        $Color= ClassRegistry::init('Color');
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $company_id = $this->employee_info["Company"]["id"];
            $employee_id = $this->employee_info["Employee"]["id"];
			$colors =  $this->Color->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => array('*')
			));
        }else{
            $colors =  $this->Color->find('all', array(
				'recursive' => -1,
				'conditions' => array('company_id is null'),
				'fields' => array('id', 'attachment', 'is_file')
			));
		}
        $this->set(compact('colors'));

    }
    public function about_company(){
		$this->loadModels('Color', 'SasSetting');
	
		$sas_setting = $this->SasSetting->find('all',array(
            'recursive' => -1,
			'conditions' => array(
				'name' => array('first_text', 'last_text', 'logo_client')
			),
            'fields' => array('id', 'name', 'value')
        ));
		
		$logo_client = array();
		$text_about = array();
		if(!empty($sas_setting)){
			foreach($sas_setting as $key => $setting){
				
				if(!empty($setting)){
					$value = $setting['SasSetting'];
					if(!empty($value['name'])){
						if($value['name'] == 'logo_client') {
							$logo_client['logo']['id'] = $value['id'];
							$logo_client['logo']['logo_client'] = $value['value'];
						}
						if($value['name'] == 'first_text') $text_about['first_text'] = $value['value'];
						if($value['name'] == 'last_text') $text_about['last_text'] = $value['value'];
					}
				}
			}
		}
		$this->set(compact('logo_client', 'text_about'));
	}
	public function testimonial(){
		$this->loadModels('SasSetting');
		$testimonials = $this->SasSetting->find('all',array(
            'recursive' => -1,
			'conditions' => array(
				'name' => array('company_testimonial')
			),
            'fields' => array('id', 'value', 'content', 'weight')
        ));
		// debug();
		// exit;
		$this->set(compact('testimonials'));
	}
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $employee_id = $this->employee_info["Employee"]["id"];
        if (!empty($this->data) && $this->_getCompany()) {

            if(!empty($this->data['Color']['color'])) $this->data['Color']['color'] = '#'.$this->data['Color']['color'];
            if(!empty($this->data['Color']['header_color'])) $this->data['Color']['header_color'] = '#'.$this->data['Color']['header_color'];
            if(!empty($this->data['Color']['line_color'])) $this->data['Color']['line_color'] = '#'.$this->data['Color']['line_color'];
            if(!empty($this->data['Color']['table_color'])) $this->data['Color']['table_color'] = '#'.$this->data['Color']['table_color'];
            if(!empty($this->data['Color']['popup_color'])) $this->data['Color']['popup_color'] = '#'.$this->data['Color']['popup_color'];
            if(!empty($this->data['Color']['kpi_color'])) $this->data['Color']['kpi_color'] = '#'.$this->data['Color']['kpi_color'];
            if(!empty($this->data['Color']['tab_selected'])) $this->data['Color']['tab_selected'] = '#'.$this->data['Color']['tab_selected'];
            if(!empty($this->data['Color']['tab_color'])) $this->data['Color']['tab_color'] = '#'.$this->data['Color']['tab_color'];
            if(!empty($this->data['Color']['page_color'])) $this->data['Color']['page_color'] = '#'.$this->data['Color']['page_color'];
            if(!empty($this->data['Color']['button_color'])) $this->data['Color']['button_color'] = '#'.$this->data['Color']['button_color'];
            if(!empty($this->data['Color']['tab_hover'])) $this->data['Color']['tab_hover'] = '#'.$this->data['Color']['tab_hover'];

            $companyName = $this->_getCompany();
            $company_id = $companyName['Company']['id'];
            $data = array(
                'company_id' => $company_id,
            );
            $color = $this->Color->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                ),
                'fields' => array('*')
            ));
            if(!empty($color)){
                foreach($color as $value){
                    $this->Color->id = $value['id'];
                    $this->data['Color']['id'] = $value['id'];
                    $this->Color->save($this->data['Color']);
                }
            }else{
                $this->Color->create();
                if (!empty($this->data['Color']['id'])) {
                    $this->Color->id = $this->data['Color']['id'];
                }
                unset($this->data['Color']['id']);
                if ($this->Color->save(array_merge($this->data['Color'], $data))) {
                    $this->Session->setFlash(__('OK.', true), 'success');
                } else {
                    $this->Session->setFlash(__('KO.', true), 'error');
                }
            }
            // update session color

            $employee_all_info = $this->Session->read("Auth.employee_info");
			$is_new_design = isset( $employee_all_info['Color']['is_new_design'] ) ? $employee_all_info['Color']['is_new_design'] : 0;
            $color = $this->Color->find('first',array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                ),
            ));
            $employee_all_info['Color'] = $color['Color'];
			$employee_all_info['Color']['is_new_design'] = $is_new_design;
            $this->Session->write('Auth.employee_info', $employee_all_info);
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }
    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

     /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($company_id = null) {
        $background_pic = '';
		$result = false;
        if($this->data['Color']['is_file']){
            if (empty($_FILES)) {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            } else {
               $path = $this->_getPath($company_id);
                App::import('Core', 'Folder');
                new Folder($path, true, 0777);
                
                if(!empty($this->data['Color']['attachment_background'])){
                    $_FILES['FileField'] = array();
                    if(!empty($_FILES)){
                        $_FILES['FileField']['name']['attachment_background'] = $_FILES['file']['name'];
                        $_FILES['FileField']['type']['attachment_background'] = $_FILES['file']['type'];
                        $_FILES['FileField']['tmp_name']['attachment_background'] = $_FILES['file']['tmp_name'];
                        $_FILES['FileField']['error']['attachment_background'] = $_FILES['file']['error'];
                        $_FILES['FileField']['size']['attachment_background'] = $_FILES['file']['size'];
                    }
                    unset($_FILES['file']);
                }

                if(!empty($this->data['Color']['attachment'])){
                    $_FILES['FileField'] = array();
                    if(!empty($_FILES)){
                        $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                        $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                        $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                        $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                        $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
                    }
                    unset($_FILES['file']);
                }
               
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
                    $login_pic = !empty($attachment['attachment']['attachment']) ? $attachment['attachment']['attachment'] : '';
                    $background_pic = !empty($attachment['attachment_background']['attachment_background']) ? $attachment['attachment_background']['attachment_background'] : '';
                    if(!empty($background_pic)){
                        $size_background = $attachment['attachment_background']['size'];
                        $attachment_background = $attachment['attachment_background']['attachment_background'];
                        $type = explode('/', $this->get_mime_type($attachment_background));
                        if( $type[0] == 'image' ){
                            try {
                                App::import("vendor", "resize");
                                //resize image for thumbnail slideshow
                                $resize = new ResizeImage($path . $attachment_background);
                                $resize->resizeTo(1910, 372, 'exact');
                                $resize->saveImage($path . $attachment_background);

                                $dataSession = array(
                                    'path' => $path,
                                    'file' => $attachment_background
                                );
                            } catch (Exception $ex) {
                                //wrong image, dont save
                                @unlink($path . $attachment_background);
                                die(json_encode(array(
                                    'status' => 'error',
                                    'hint' => __('Not an image', true)
                                )));
                            }
                        } 
                    }
                    if(!empty($login_pic)){
                        $type = explode('/', $this->get_mime_type($login_pic));
						$login_pic_path = $path.$login_pic;
						$info = pathinfo($login_pic_path);
						$login_pic_thumb = $info['filename'].'_thumbnail.'.$info['extension'];
                        if( $type[0] == 'image' ){
                            try {
                                App::import("vendor", "resize");
                                //resize image for thumbnail login image
                                $resize = new ResizeImage($path . $login_pic);
                                $resize->resizeTo(320, 0, 'maxwidth');
								if( file_exists($path . $login_pic_thumb)) @unlink($path . $login_pic_thumb);
                                $resize->saveImage($path . $login_pic_thumb);
                            } catch (Exception $ex) {
                                //wrong image, dont save
                                @unlink($path . $login_pic_thumb);
                                die(json_encode(array(
                                    'status' => 'error',
                                    'hint' => __('Not an image', true)
                                )));
                            }
                        } 
                    }
                    $last = $this->Color->find('first', array(
                        'recursive' => -1,
                        'fields' => array('id', 'attachment', 'attachment_background'),
                        'conditions' => array('company_id' => $company_id)));
                    if ($last) {
                        $this->Color->id = $last['Color']['id'];
                        if(!empty($login_pic)) $this->Color->create(); //@unlink($path . $last['Color']['attachment']);
                        if(!empty($background_pic)) @unlink($path . $last['Color']['attachment_background']);
                    }else{
                        $this->Color->create();
                    }
                    $saveField = array();
                    if(!empty($background_pic)) $saveField['attachment_background'] = $background_pic;
                    if(!empty($login_pic)) $saveField['attachment'] = $login_pic;
                    $saveField['company_id'] = $company_id;
                    $saveField['is_file'] = $this->data['Color']['is_file'];
					$result = $this->Color->save($saveField);
                    if($result) {
                        if($this->MultiFileUpload->otherServer == true){
                            if(!empty($login_pic)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['Color']['attachment'], $login_pic, '/colors/index/' . $company_id);
                            if(!empty($background_pic)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['Color']['attachment_background'], $background_pic, '/colors/index/' . $company_id);
                        }
                        $this->Session->setFlash(__('Saved', true), 'success');
                    } else {
                        @unlink($path . $attachment);
                        $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                        if($this->MultiFileUpload->otherServer == true){
                            if(!empty($login_pic)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $login_pic, '/colors/index/' . $company_id);
                            if(!empty($background_pic)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $background_pic, '/colors/index/' . $company_id);
                       
                        }
                    }
					
                    // update session color
                    $cond = array();
                    if ($this->employee_info["Employee"]["is_sas"] != 1){
                        $company_id = $this->employee_info["Company"]["id"];
                    }else{
                        $cond = array('company_id is Null');
                    } 
                    if(!empty($company_id)){
                        $cond['company_id'] = $company_id;
                    }else{
						$cond[] = 'company_id is null';
					}
                    $employee_all_info = $this->Session->read("Auth.employee_info");
                    $color = $this->Color->find('first',array(
                        'recursive' => -1,
                        'conditions' => $cond,
                    ));
                    if(!empty($background_pic)) $color['Color']['attachment_background'] = $background_pic;
					$employee_all_info['Color'] = isset( $employee_all_info['Color']) ? $employee_all_info['Color'] : array();
                    $employee_all_info['Color'] = array_merge( $employee_all_info['Color'] , $color['Color']);
                    $this->Session->write('Auth.employee_info', $employee_all_info);

                } else {
                    $this->Session->setFlash(__('Please select a file', true), 'error');
                }
            }
			if (!empty( $login_pic_thumb)) $result['thumbnail'] = $login_pic_thumb;
			$this->set('result', $result);
        }
    }
	public function upload_logo($company_id = null) {
		$this->loadModels('SasSetting');
		$result = false;
       
            if (empty($_FILES)) {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            } else {
                $path = $this->_getPath($company_id, 'logo');
                App::import('Core', 'Folder');
                new Folder($path, true, 0777);
              
				$_FILES['FileField'] = array();
				if(!empty($_FILES)){
					$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
					$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
					$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
					$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
					$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
				}
				unset($_FILES['file']);
               
                if (file_exists($path)) {
                    $this->MultiFileUpload->encode_filename = false;
                    $this->MultiFileUpload->uploadpath = $path;
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = $this->allowedFiles;
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.', true), $path), 'error');
                }
				if (!empty($attachment)) {
					$logo = !empty($attachment['attachment']['attachment']) ? $attachment['attachment']['attachment'] : '';
					if(!empty($logo)){
						$type = explode('/', $this->get_mime_type($logo));
						$logo_path = $path.$logo;
						$info = pathinfo($logo_path);
						$logo_thumb = $info['filename'].'_thumbnail.'.$info['extension'];
						if( $type[0] == 'image' ){
							try {
								App::import("vendor", "resize");
								//resize image for thumbnail login image
								$resize = new ResizeImage($path . $logo);
								$resize->resizeTo(120, 120);
								if( file_exists($path . $logo_thumb)) @unlink($path . $logo_thumb);
								$resize->saveImage($path . $logo_thumb);
							} catch (Exception $ex) {
								//wrong image, dont save
								@unlink($path . $logo_thumb);
								die(json_encode(array(
									'status' => 'error',
									'hint' => __('Not an image', true)
								)));
							}
						} 
					}
					$last = $this->SasSetting->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'name' => array('logo_client')
						),
						'fields' => array('id', 'value')
					));
					if ($last) {
						$this->SasSetting->id = $last['SasSetting']['id'];
					}else{
						$this->SasSetting->create();
					}
					$saveField = array();
					if(!empty($logo)){
						$saveField['value'] = $logo;
						$saveField['name'] = 'logo_client';
					}
					$result = $this->SasSetting->save($saveField);
					if($result) {
						if($this->MultiFileUpload->otherServer == true){
							if(!empty($logo)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $last['SasSetting']['value'], $logo, '/colors/index/' . $company_id);
						}
						$this->Session->setFlash(__('Saved', true), 'success');
					} else {
						@unlink($path . $attachment);
						$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
						if($this->MultiFileUpload->otherServer == true){
							if(!empty($logo)) $this->MultiFileUpload->deleteAndUploadFileToServerOther($path, $logo, '/colors/index/' . $company_id);
					   
						}
					}
				} else {
					$this->Session->setFlash(__('Please select a file', true), 'error');
				}
			}
		
		if (!empty( $logo_thumb)) $result['thumbnail'] = $logo_thumb;
		$this->set('result', $result);
	
    }
    public function attachment($company_id = null, $file_type = 'attachment', $attachment_id = null, $size = 'full') {
        $this->layout = false;
        $link = '';
		$allowSize = array('full', 'thumbnail', 'large');
		$attachment_id = ($attachment_id == 0) ? null : $attachment_id;
        if($company_id == 0) $company_id = null;
		$cond = array(
			'company_id' => $company_id,
			$file_type.' !=' => '', 
		);
		if($attachment_id) {
			$cond['id'] = $attachment_id;
		}
		$color = $this->Color->find('first', array(
			'recursive' => -1,
			'fields' => array('id', $file_type),
			'conditions' => $cond
		));
        if ($color) {
			$attachment = $color['Color'][$file_type];
			$dir = (!empty($file_type) && $file_type == 'logo_client') ? 'logo' : 'color';
            $link = trim($this->_getPath($company_id, $dir) . $attachment);
			if($size != 'full'){
				if( in_array( $size, $allowSize)){
					$info = pathinfo($link);
					$link_size = $info['dirname'] . '/' . $info['filename'].'_'.$size.'.'.$info['extension'];
					if( !file_exists( $link_size )){
						$this->regenerate_thumb($attachment, $company_id, $size);
					} 
				}else{
					$link_size = $link;
				}
				$link = $link_size;
			}
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
    }
    public function logo_client($logo_id = null, $size = 'full') {
		$this->loadModels('SasSetting');
        $this->layout = false;
        $link = '';
		$allowSize = array('full', 'thumbnail', 'large');
        $company_id = null;
		
		if($logo_id) {
			$cond['id'] = $logo_id;
		}
		$logoClient = $this->SasSetting->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'value'),
			'conditions' => $cond
		));
        if ($logoClient) {
			$logo_name = $logoClient[$logo_id];
            $link = trim($this->_getPath($company_id, 'logo') . $logo_name);
			if($size != 'full'){
				if( in_array( $size, $allowSize)){
					$info = pathinfo($link);
					$link_size = $info['dirname'] . '/' . $info['filename'].'_'.$size.'.'.$info['extension'];
					if( !file_exists( $link_size )){
						$this->regenerate_thumb($logo_name, $company_id, $size);
					} 
				}else{
					$link_size = $link;
				}
				$link = $link_size;
			}
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
    }
	function update_testimonial(){
		$this->loadModels('SasSetting');
		$result = array();
		if(!empty($this->data)){
			$text_about =  $this->SasSetting->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'name' => 'company_testimonial',
					'id' => $this->data['id'],
				),
				'fields' => array('id'),
			));
			if (!empty($text_about)) {
				$this->SasSetting->id = $text_about['SasSetting']['id'];
			} else {
				$this->SasSetting->create();
			}
			$saveField = array();
			$saveField['name'] = 'company_testimonial';
			$saveField['value'] = $this->data['value'];
			$saveField['content'] = $this->data['content'];
			$result = $this->SasSetting->save($saveField);
			$result = array(
				'data' => array(
					'result' => 'success',
					'data' => $result,
					'message' => __('Saved', true),
				)
			);
		}else{
			$result = array(
				'data' => array(
					'result' => 'false',
					'message' => __('Data submit is empty', true),
				)
			);
			
		}
		die(json_encode($result));
	}
	function update_text(){
		$result = array();
		if(!empty($this->data)){
			$this->loadModels('Color', 'SasSetting');
			
			$text_about =  $this->SasSetting->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'name' => $this->data['name']
				),
				'fields' => array('id'),
			));
			if (!empty($text_about)) {
				$this->SasSetting->id = $text_about['SasSetting']['id'];
			} else {
				$this->SasSetting->create();
			}
			$saveField = array();
			$saveField['name'] = $this->data['name'];
			$saveField['value'] = $this->data['value'];
			$result = $this->SasSetting->save($saveField);
			$result = array(
				'data' => array(
					'result' => 'success',
					'data' => $result,
					'message' => __('Saved', true),
				)
			);	
		}else{
			$result = array(
				'data' => array(
					'result' => 'false',
					'message' => __('Data submit is empty', true),
				)
			);
			
		}
		die(json_encode($result));
	}
	function delete_testimonial($id = null) {
		if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for testimonial', true), 'error');
            $this->redirect(array('action' => 'testimonial'));
        }
		$this->loadModels('SasSetting');
		$last = $this->SasSetting->find('first', array(
			'recursive' => -1,
			'conditions' => array('SasSetting.id' => $id)));
		if ($last && $this->SasSetting->delete($id)) {
			$this->Session->setFlash(__('Deleted', true), 'success');
			$this->redirect(array('action' => 'testimonial'));
		}
		$this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'testimonial'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
	 * @param id colors id
     */
    function delete($id = null) {
		if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project local view', true), 'error');
            $this->redirect($this->referer());
        }
        $this->Color->recursive = -1;
        $data = $this->Color->read('attachment_background, attachment', $id);
        $com_id = 0;
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $com_id = $this->employee_info["Company"]["id"];
        }
        $file_bg_attachment = $this->_getPath($com_id) . $data['Color']['attachment_background'];
        $file_attachment = $this->_getPath($com_id) . $data['Color']['attachment'];
        $this->Color->id = $id;
        $success = 0;
        if ($this->Color->saveField('attachment_background', null)){
            $success = 1;
        }
		if( !empty($data['Color']['attachment'])){
			$success = intval( $this->Color->delete($id));			
        }
        if ($success){	
            if( !empty($data['Color']['attachment_background']) && file_exists($file_bg_attachment) ){
                unlink($file_bg_attachment);
                if( $this->MultiFileUpload->otherServer ){
                    $this->MultiFileUpload->deleteFileToServerOther($this->_getPath($com_id), $data['Color']['attachment_background']);
                }
            }
            if( !empty($data['Color']['attachment']) && file_exists($file_attachment) ){
                unlink($file_attachment);
				$attachment = $data['Color']['attachment'];
				$path = $this->_getPath($com_id);
				$attachment_path = $path.$attachment;
				$info = pathinfo($attachment_path);
				$attachment_thumb = $info['filename']. '_' . 'thumbnail' . '.' . $info['extension'];
				unlink($path.$attachment_thumb);
				
                if( $this->MultiFileUpload->otherServer ){
                    $this->MultiFileUpload->deleteFileToServerOther($this->_getPath($com_id), $data['Color']['attachment']);
                }
            }
            // update session color'

            $cond = array();
            if ($this->employee_info["Employee"]["is_sas"] != 1){
                $company_id = $this->employee_info["Company"]["id"];
            }else{
                $cond = array('company_id is Null');
            }
                
            if(!empty($company_id)){
                $cond['company_id'] = $company_id;
            }
            $employee_all_info = $this->Session->read("Auth.employee_info");
            $color = $this->Color->find('first',array(
                'recursive' => -1,
                'conditions' => $cond,
            ));
			$employee_all_info['Color'] = isset( $employee_all_info['Color']) ? $employee_all_info['Color'] : array();
			$employee_all_info['Color'] = array_merge( $employee_all_info['Color'] , $color['Color']);
            $this->Session->write('Auth.employee_info', $employee_all_info);

        }
        die(json_encode($success));
    }
    function delete_logo($id = null) {
		if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
		$this->loadModels('SasSetting');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for logo client', true), 'error');
            $this->redirect($this->referer());
        }
        $this->SasSetting->recursive = -1;
       
		$logo_client = $this->SasSetting->find('list',array(
            'recursive' => -1,
			'conditions' => array(
				'id' => $id,
			),
            'fields' => array('id', 'value')
        ));
        $com_id = null;
        $file_attachment = $this->_getPath($com_id, 'logo') . $logo_client[$id];
        $this->SasSetting->id = $id;
        $success = 0;
		if( !empty($logo_client[$id])){
			$success = intval( $this->SasSetting->delete($id));			
        }
        if ($success){	
            if( !empty($logo_client[$id]) && file_exists($file_attachment) ){
                unlink($file_attachment);
				
				$path = $this->_getPath($com_id, 'logo');
				$attachment_path = $path.$file_attachment;
				$info = pathinfo($attachment_path);
				$attachment_thumb = $info['filename']. '_' . 'thumbnail' . '.' . $info['extension'];
				unlink($path.$attachment_thumb);
				
                if( $this->MultiFileUpload->otherServer ){
                    $this->MultiFileUpload->deleteFileToServerOther($this->_getPath($com_id), $logo_client[$id]);
                }
            }
        }
        die(json_encode($success));
    }
    protected function _getPath($company_id = null, $dir = 'color') {
        $folder_name = 'default';
        if(!empty($company_id)) $folder_name = $company_id;
        $path = FILES . $folder_name . DS . $dir . DS;
        return $path;
    }
    public function get_image_upload($company_id = null){
        $last = $this->Color->find('first', array(
        'recursive' => -1,
        'fields' => array('id', 'attachment', 'attachment_background'),
        'conditions' => array('company_id' => $company_id)));
        return $last;
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
	private function regenerate_thumb($attachment = null, $company_id = null, $size){
		$suffixes = $size;
		$sizes = array(
			'thumbnail' => 320,
			'large' => 1920,
		);
		// debug( $size); exit;
		if ( !array_key_exists($size, $sizes) ) return;
		$width = $sizes[$size];
		$height = 0;
		$option = 'maxwidth';
		
		$type = explode('/', $this->get_mime_type($attachment));
		$path = $this->_getPath($company_id);
		$attachment_path = $path.$attachment;
		$info = pathinfo($attachment_path);
		$attachment_thumb = $info['filename']. '_' . $suffixes . '.' . $info['extension'];
		if( $type[0] == 'image' ){
			try {
				App::import("vendor", "resize");
				//resize image for thumbnail login image
				$resize = new ResizeImage($path . $attachment);
				$resize->resizeTo($width, $height, $option);
				if( file_exists($path . $attachment_thumb)) @unlink($path . $attachment_thumb);
				$resize->saveImage($path . $attachment_thumb);
			} catch (Exception $ex) {
				//wrong image, dont save
				@unlink($path . $attachment_thumb);
				die(json_encode(array(
					'status' => 'error',
					'hint' => __('Not an image', true)
				)));
			}
		}
	}
}