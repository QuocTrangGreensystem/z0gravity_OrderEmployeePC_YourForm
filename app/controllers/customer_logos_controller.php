<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class CustomerLogosController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'CustomerLogos';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

	var $components = array('MultiFileUpload');
    /**
     * index
     *
     * @return void
     * @access public
     */
	/* 
	* Huynh" Cho nay chua query data cua child company
	*/
    function index() {
        if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
       
        $this->CustomerLogo->recursive = 0;
        $companies = $this->CustomerLogo->Company->find('list');
        $parent_companies = $this->CustomerLogo->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
       
		$this->paginate = array(
			// phan trang
			'fields' => array('id','logo_name', 'company_id'),
			'limit' => 1000
		);
		$this->set('company_names', $this->CustomerLogo->Company->generateTreeList(null, null, null, '--'));
   
        $this->set('customer_logo', $this->paginate());
    }
	function add_logo(){
        if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
        $this->loadModel('CustomerLogo');
        $result = false;
		$company_id = !empty($this->data) ? $this->data['popupUpload']['company_id'] : null;
		$result = false;
		$this->layout = false;
		if(!empty($_FILES)){
		  	/* Upload file */ 
			$_FILES['FileField'] = array();
			if(!empty($_FILES['file']['name'])){
				$_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
				$_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
				$_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
				$_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
				$_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
				$filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);
			}
			$path = $this->_getPath($company_id);

			if(!empty($_FILES['file']['name'])){
				$tmp_name = $_FILES['file']['name'] . time();
				$new_data = array(
					'company_id'=> $company_id,
					'employee_id' => null,
					'created' => time(),
					'updated' => time()
				);
				unset($_FILES['file']);
				App::import('Core', 'Folder');
				new Folder($path, true, 0777);
				if (file_exists($path)) {
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
					$new_data['logo_name'] = $attachment;
					try {
                        App::import("vendor", "resize");
						list($_width, $_height) = getimagesize($path . $attachment);
						if($_height > 65){
							$resize = new ResizeImage($path . $attachment);
							$resize->resizeTo(0, 65, 'maxheight');
							$resize->saveImage($path . $attachment);
						}
						if($this->MultiFileUpload->otherServer == true){
							$this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/customer_logo');
						}
					}catch (Exception $ex) {
						@unlink($path . $attachment);
						if($this->MultiFileUpload->otherServer == true){
							$this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/customer_logo');
						}
					}
					$this->CustomerLogo->create();
					if ($this->CustomerLogo->save($new_data)) {
						$result = true;
						$this->Session->setFlash(__('Saved', true), 'success');
					} else {
						@unlink($path . $attachment);
						$this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
					}
				} else {
					$this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
				}
			}
		} else {
			$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
		}
		
	}
   
    /**
     * update
     *
     * @return void
     * @access public
     */
    function update() {
        if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
		$this->loadModels('Employee', 'CustomerLogo');
		$data = array();
		$result = false;
        if(empty($this->data)) {
            $this->Session->setFlash(__('Invalid customer logo', true), 'error');
        }else{
			$id = $this->data['id'];
			$beforeUpdate = $this->CustomerLogo->find('first', array(
				'recursive' => -1,
				'fields' => array('logo_name', 'company_id'),
				'conditions' => array('id' => $id))
			);
			$beforeCompany = $beforeUpdate['CustomerLogo']['company_id'];
			$logo_name = $beforeUpdate['CustomerLogo']['logo_name'];
			
			$employee_logo = $this->Employee->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'company_id'),
				'conditions' => array('logo_id' => $id))
			);
			if ($this->CustomerLogo->save($this->data)) {
				// Update company_id null thi khong can update lai employee logo
				if(!empty($this->data['company_id'])){
					foreach($employee_logo as $employee_id => $e_company_id){
						// Update company_id trung voi employee company_id thi khong can update lai employee logo
						if($this->data['company_id'] != $e_company_id){
							$this->Employee->id = $employee_id;
							$this->Employee->saveField('logo_id', null);
						}
					}
				}
				if($beforeCompany != $this->data['company_id']){
					$beforePath = $this->_getPath($beforeCompany) . $logo_name;
					$afterPath = $this->_getPath($this->data['company_id']);
					if (file_exists($beforePath)) {
						App::import('Core', 'Folder');
						new Folder($afterPath, true, 0777);
						rename($beforePath, $afterPath . $logo_name);
					}
				}
				$this->Session->setFlash(__('Saved', true), 'success');
				$result = true;
				$this->data = $this->CustomerLogo->read(null, $id);
			} else {
				$this->Session->setFlash(__('NOT SAVED', true), 'error');
			} 
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if ($this->employee_info["Employee"]["is_sas"] != 1) {
			$this->Session->setFlash(__('You have not permission to access this function', true), 'error');
			$this->redirect(array('controller' => 'administrators'));
		}
		$this->loadModels('CustomerLogo', 'Employee');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for customer logo', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$last = $this->CustomerLogo->find('first', array(
			'recursive' => -1,
			'fields' => array('logo_name', 'company_id'),
			'conditions' => array('id' => $id))
		);
		$employee_logo = $this->Employee->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'id'),
			'conditions' => array('logo_id' => $id))
		);
		if (!empty($last) && $this->CustomerLogo->delete($id)) {
			foreach($employee_logo as $employee_id){
				$this->Employee->id = $employee_id;
				$this->Employee->saveField('logo_id', null);
			}
			$company_id = $last['CustomerLogo']['company_id'];
			if ($last['CustomerLogo']['logo_name']) {
				$path = $this->_getPath($company_id);
				unlink($path . $last['CustomerLogo']['logo_name']);
			}
			$this->Session->setFlash(__('Deleted', true), 'success');
			$this->redirect(array('action' => 'index'));
		}
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
	public function attachment($id = null) {
        $this->layout = false;
        $link = '';
		$conditions = array();
		$conditions['id'] = $id;
		if ($this->employee_info["Employee"]["is_sas"] != 1){
			$conditions['OR'][] = 'company_id IS NULL';
			if(!empty($this->employee_info['Company']['id'])) $conditions['OR'][]['company_id'] = $this->employee_info['Company']['id'];
		}
        $logo_file = $this->CustomerLogo->find('first', array(
			'recursive' => -1,
			'conditions' =>  $conditions,
			'fields' => array('id', 'logo_name', 'company_id'),
		)); 
        if ($logo_file) {
			$link = trim($this->_getPath($logo_file['CustomerLogo']['company_id']) . $logo_file['CustomerLogo']['logo_name']);
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
            $this->redirect(array('action' => 'index'));
        }
       
    }
	protected function _getPath($company_id) {
		$this->loadModels('CustomerLogo', 'Company');
        $company = $this->Company->find('first', array(
            'recursive' => -1,
            'fields' => array(
                'parent_id',
                'company_name',
                'dir'
            ), 'conditions' => array('id' => $company_id)));
			
        $pcompany = $this->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES;
		$dir = 'default' . DS . 'logo';
        // if (!empty($company)){
			// $dir = $dir . DS . $company['Company']['dir'];
			// if( !empty($pcompany)) {
				// $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
			// }
		// }
        $path .= $dir . DS;
		// debug($path);
		// exit;
        return $path;
    }
}
?>