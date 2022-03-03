<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectLocalViewsPreviewController extends AppController {

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array('ProjectLocalView');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');

    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm";
    function beforeFilter() {
        parent::beforeFilter();
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
        $this->_checkWriteProfile('local_view');
        $projectLocalView = $this->ProjectLocalView->find("all", array(
            'recursive' => -1, 'fields' => array('id', 'attachment','is_file', 'is_https'),
            "conditions" => array('project_id' => $project_id)));
        // debug($projectLocalView); exit;
        $link = $noFileExists = array();
        if ($projectLocalView) {
            foreach ($projectLocalView as $key => $value) {
               $link[$value['ProjectLocalView']['id']] = trim($this->_getPath($project_id)
                    . $value['ProjectLocalView']['attachment']);
                if (!file_exists($link[$value['ProjectLocalView']['id']]) || !is_file($link[$value['ProjectLocalView']['id']])) {
                    $noFileExists[$value['ProjectLocalView']['id']] = 0;
                }
            }
        }
        $this->set(compact('project_id', 'projectLocalView', 'noFileExists', 'link'));
    }
    function saveAddress($pid){
        $this->_checkRole(false, $pid);
        if( !empty($this->data) ){
            $this->ProjectLocalView->Project->id = $pid;
            $this->ProjectLocalView->Project->save(array(
                'address' => $this->data['address'],
                'latlng' => $this->data['latlng'] ? json_encode($this->data['latlng']) : ''
            ));
        }
        die(1);
    }
	function ajax($project_id = null) {
        $this->_checkRole(false, $project_id);

        $projectLocalView = $this->ProjectLocalView->find("first", array(
            'recursive' => -1, 'fields' => array('attachment','is_file'),
            "conditions" => array('project_id' => $project_id)));

        if ($projectLocalView) {
            $link = trim($this->_getPath($project_id)
                    . $projectLocalView['ProjectLocalView']['attachment']);
            if (!file_exists($link) || !is_file($link)) {
                $noFileExists = true;
            }
        }

        $this->set(compact('projectName', 'project_id', 'projectLocalView', 'noFileExists'));
		$this->render('ajax');
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($project_id = null) {
        if($this->data['ProjectLocalView']['is_file']){
            if (empty($_FILES)) {
                $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
            } else {
                if ($this->_checkRole(true, $project_id)) {
                    $path = $this->_getPath($project_id);

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
                        $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                                , true), $path), 'error');
                    }

                    if (!empty($attachment)) {
                        $attachment = $attachment['attachment']['attachment'];
                        $this->ProjectLocalView->create();
                        // $last = $this->ProjectLocalView->find('first', array(
                        //     'recursive' => -1,
                        //     'fields' => array('id', 'attachment','is_file'),
                        //     'conditions' => array('project_id' => $project_id)));
                        // if ($last) {
                        //     $this->ProjectLocalView->id = $last['ProjectLocalView']['id'];
                        //     @unlink($path . $last['ProjectLocalView']['attachment']);
                        // }
                        if ($this->ProjectLocalView->save(array(
                                    'project_id' => $project_id,
                                    'attachment' => $attachment,
                                    'is_file' => $this->data['ProjectLocalView']['is_file']
                                ))) {
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_local_views/index/' . $project_id);
                            }
                            $this->Session->setFlash(__('Saved', true), 'success');
                        } else {
                            @unlink($path . $attachment);
                            $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                            if($this->MultiFileUpload->otherServer == true){
                                $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_local_views/index/' . $project_id);
                            }
                        }
                    } else {
                        $this->Session->setFlash(__('Please select a file or specify a URL', true), 'error');
                    }
                }
            }
            $this->redirect(array('action' => 'index', $project_id));
        }else{
			/**ticket #982. Updated by QuanNV.
				is_file xac dinh co phai la file hay khong
				is_https xac dinh co phai https:// , is_file = 0 va is_https = 0 thi la http://
			**/
            // $last = $this->ProjectLocalView->find('first', array(
                            // 'recursive' => -1,
                            // 'fields' => array('id', 'attachment','is_file', 'is_https'),
                            // 'conditions' => array('project_id' => $project_id)));
            // if($last){
              // $this->ProjectLocalView->id = $last['ProjectLocalView']['id'];
                            // @unlink($path . $last['ProjectLocalView']['attachment']);
            // }
            $is_https = 0;
            $pos = strpos($this->data['ProjectLocalView']['attachment'],'https:');
            if($pos !== false){
                $this->data['ProjectLocalView']['attachment'] = str_replace('https://','',$this->data['ProjectLocalView']['attachment']);
                $is_https = 1;
            }else{
				$this->data['ProjectLocalView']['attachment'] = str_replace('http://','',$this->data['ProjectLocalView']['attachment']);
			}
			$this->ProjectLocalView->create();
            if($this->ProjectLocalView->save(array(
                                    'project_id' => $project_id,
                                    'attachment' => $this->data['ProjectLocalView']['attachment'],
                                    'is_file' => $this->data['ProjectLocalView']['is_file'],
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

    public function attachment($project_id = null, $file_id = null) {
        $this->layout = false;
        $link = '';
        $projectLocalView = $this->ProjectLocalView->find("first", array(
            'recursive' => -1,
            'fields' => array('id', 'project_id', 'attachment','is_file','is_https'),
            "conditions" => array('id' => $file_id)));
        if ($projectLocalView) {
            $link = trim($this->_getPath($project_id) . $projectLocalView['ProjectLocalView']['attachment']);
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
		/**ticket #982. Updated by QuanNV.
				is_file = 1, open truc tiep
				is_file = 0 open in new tab (neu is_https = 1 thi gan https)
			**/
        // if (!$link && !empty($this->params['url']['download'])) {
            // $this->Session->setFlash(__('File not found.', true), 'error');
            // $this->redirect(array('action' => 'index', $project_id));
        // }
        if($projectLocalView['ProjectLocalView']['is_file']==0){
           $this->set('url',$projectLocalView['ProjectLocalView']['attachment']);
           $this->set('is_https',$projectLocalView['ProjectLocalView']['is_https']);
        }
    }

    protected function _getPath($project_id) {
        $company = $this->ProjectLocalView->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->ProjectLocalView->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'localviews' . DS;
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
            $this->Session->setFlash(__('Invalid id for project local view', true), 'error');
            $this->redirect($this->referer());
        }
        $this->ProjectLocalView->recursive = -1;
        $data = $this->ProjectLocalView->read('project_id, attachment', $id);
        $file = $this->_getPath($data['ProjectLocalView']['project_id']) . $data['ProjectLocalView']['attachment'];
		$project_id = @$data['ProjectLocalView']['project_id'];
		$canModified = !empty($data) ? $this->_checkRole(false, $data['ProjectLocalView']['project_id'])  : false;
		if($canModified){
			if ($this->ProjectLocalView->delete($id)) {
				if( file_exists($file) ){
					unlink($file);
					if( $this->MultiFileUpload->otherServer ){
						$this->MultiFileUpload->deleteFileToServerOther($this->_getPath($data['ProjectLocalView']['project_id']), $data['ProjectLocalView']['attachment']);
					}
				}
			}
			$this->_functionStop(true, $id, __('Deleted', true), false, array('action' => 'index', $project_id));
		}
        $this->_functionStop(false, $id, __('You have not permission to access this function', true), false, array('action' => 'index', $project_id));
    }
    public function getProjectLocalAttachment($project_id){

        $attachments = $this->ProjectLocalView->find('list', array(
            'recursive' => -1,
            'conditions' => array('project_id' => $project_id),
            'fields' => array('id', 'attachment')
        ));
        $data['attachments'] =  $attachments;
        die(json_encode($data));

    }

}
?>
