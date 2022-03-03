<?php
class ProjectLivrablesController extends ApiAppController {
    public $uses = array('ProjectLivrable');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');

    public function beforeFilter(){
        parent::beforeFilter();
    }
    /**
     * Get from Index() in project_livrables_preview_controller.php
     *
     */
    function get_documents() {
        if(isset($this->data['project_id'])){
            $project_id = $this->data['project_id'];

            // redirect ve index neu ko phai la admin hoac pm
            // $this->checkRole(false, $project_id);
            // $this->loadModels('ProjectEmployeeManager','CompanyEmployeeReference');
            // $this->_checkWriteProfile('deliverable');
            // $projectName = $this->viewVars['projectName'];
            // $this->ProjectLivrable->Behaviors->attach('Containable');
            // $this->ProjectLivrable->cacheQueries = true;
            $projectLivrables = $this->ProjectLivrable->find("all", array(
                'recursive' => -1,
                'fields' => array(
                    '*',
                ),
                'order' => array('ProjectLivrable.updated desc'),
                "conditions" => array('project_id' => $project_id)));
            $documents = array();

            foreach( $projectLivrables as $k => $value){
                $value['ProjectLivrable']['url'] = Router::url(array(
                    // 'plugin' => false, 
                    'controller' => $this->params['controller'], 
                    'action' => 'attachment', 
                    $value['ProjectLivrable']['id']
                ), true);
                array_push($documents, $value['ProjectLivrable']);
            }
            $this->ZAuth->respond('success', $documents, 'List Project Livrables', '200');
 
        }
        $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
    }

    public function attachment($id) {
        // $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        // Debug($this->params);
    //    if(isset($this->params['url']['id'])) {

            // $id = $this->params['url']['id'];
            // Debug($id);
            // exit;
            $last = $this->ProjectLivrable->find('first', array(
                'recursive' => -1,
                'fields' => array('livrable_file_attachment', 'project_id', 'format'),
                'conditions' => array('ProjectLivrable.id' => $id)));

            // $error = true;
            // Debug($last);
            if ($last && $last['ProjectLivrable']['project_id']) {
                if($last['ProjectLivrable']['format'] == 1 || $last['ProjectLivrable']['format'] == 3) {
                    $this->ZAuth->respond('fail', null, 'You get a link!', '0');
                    return;
                } 
                $project_id = $last['ProjectLivrable']['project_id'];
                // $this->_checkRole(false, $project_id);
                // if( !$this->UserUtility->user_can('read_project', $project_id)) {
                //     $this->ZAuth->respond('data_incorrect', null, 'data_incorrect', '0');
                //     return;
                // }
                // Debug($this->getPath('livrable' ,$this->employee_info['Company']));
                $path = trim($this->getPath('livrable' ,$this->employee_info['Company']) 
                . $last['ProjectLivrable']['livrable_file_attachment']);
                // Debug($path);
                // exit;
                $attachment = $last['ProjectLivrable']['livrable_file_attachment'];
                // if($type != 'delete'){÷
                    
                    if (file_exists($path) && is_file($path)) {
                        $info = pathinfo($path);
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
                            'download' => true,
                            'extension' => !empty($info['extension']) ? strtolower($info['extension']) : '',
                        );
                        if (!empty($info['extension']) && !in_array($info['extension'], array_keys($params['mimeType']))  ) {
                        // if (!empty($info['extension']) && !in_array( array('bmp','ppt','pps','docx','xlsx' ,'pptx'))  ) {
                        $params['mimeType'][$info['extension']] = 'application/octet-stream';
                        }
                        // debug( $params); exit;
                        $this->set($params);
                        // $error = false;
                        return;
                    }
                // } else {
                //     @unlink($path);
                //     $this->ProjectLivrable->id = $id;
                //     $this->ProjectLivrable->save(array(
                //         'livrable_file_attachment' => '',
                //         'format' => 0,
                //         'upload_date' => time()
                //     ));
                //     if($this->MultiFileUpload->otherServer == true){
                //         $path = trim($this->_getPath($last['ProjectLivrable']['project_id']));
                //         $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_livrables/index/' . $last['ProjectLivrable']['project_id']);
                //     }
                //     $this->loadModels('ProjectLivrableComment');
                //     $this->ProjectLivrableComment->create();
                //     $this->ProjectLivrableComment->save(array(
                //         'project_livrable_id' => $id,
                //         'employee_id' => $this->employee_info['Employee']['id'],
                //         'comment' => 'Document deleted'
                //     ));
                // }
            }
            else {

                $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
            }
        // if ($type != 'download') {
        //     $this->Session->delete('Message.flash');
        //     exit();
        // } elseif ($error) {
        //     $this->Session->setFlash(__('File not found.', true), 'error');
        //     $this->redirect(array('action' => 'index',
        //         $last ? $last['ProjectLivrable']['project_id'] : __('Unknown', true)));
        // }
        // }
        // $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    
    // REF from add_new_document of project livrables preview controller.php

    public function new_document($project_id = null){

        if($this->RequestHandler->isPost()) {
            $data = $_POST;
            // Debug($data);
            // Debug($_FILES);
            // exit;
            if(!isset($data['name']) && !isset($data['project_id']) && (!isset($data['url']) || !empty($_FILES))) {
                $this->ZAuth->respond('fail', null, 'data not empty', '0');
                return;
            }
            //Create new Document
            $new_data = array(
                'name'=> $this->data['name'],
                'project_id' => $data['project_id'],
                'project_livrable_category_id'=> '',
                'project_livrable_status_id' => '',
                'livrable_progression' => 0,

                'livrable_date_delivery' => '',
                'livrable_date_delivery_planed' => '',
                'livrable_responsible' => '',
                'livrable_file_attachment'=> '',
                'upload_date' => time(),
                'created' => time(),
                'updated' => time(),
                'format'=> '',
                'weight'=> 0,
                'employee_id_upload' => $this->employee_info['Employee']['id'],
                'version'=> '',
                );
            /* Upload file */ 
            
            if(!empty($_FILES)) {
                $_FILES['FileField'] = array();
                if(!empty($_FILES['file']['name'])){
                    $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
                    $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
                    $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
                    $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
                    $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
                    $filePath = realpath($_FILES["FileField"]["tmp_name"]['attachment']);
                }
                // Debug($this->employee_info['Company']);
                $path = $this->getPath('livrable', $this->employee_info['Company']);

                // Debug($path);
                if(!empty($_FILES['file']['name'])){
                    unset($_FILES['file']);
                    App::import('Core', 'Folder');
                    new Folder($path, true, 0777);
                    if (file_exists($path)) {
                        $this->MultiFileUpload->encode_filename = false;
                        $this->MultiFileUpload->uploadpath = $path;
                        $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "*";
                        $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                        $attachment = $this->MultiFileUpload->upload();
                        // Debug(1);
                    } else {
                        $attachment = "";
                        $this->ZAuth->respond('fail', null, 'System save fail', '404');
                        // $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                        //                         , true), $path), 'error');
                    }
                    if (!empty($attachment)) {
                        $attachment = $attachment['attachment']['attachment'];
    
                        $new_data['livrable_file_attachment'] = $attachment;
                        $new_data['format'] = 2;
                        // Debug(2);
                        // if($this->MultiFileUpload->otherServer == true){
                        //     $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_livrables/index/' . $project_id);
                        // }
                            
                    } else {
                        // $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
                        $this->ZAuth->respond('fail', null, 'upload file fail', '404');
                    }
                }
            }
           
            // neu co url
            elseif(!empty($data['url'])){
                $pos1 = strpos($this->data['url'], 'ttps://');
                $pos2 = strpos($this->data['url'], 'ttp://');
                if((!empty($pos1) && $pos1 == 1) || (!empty($pos2) && $pos2 == 1)){
                    $format = 1;
                } else {
                    $format = 3;
                }
                $new_data['livrable_file_attachment'] = $data['url'];
                $new_data['format'] = $format;
            } 

            /* Copy from update() function */

            
            $this->ProjectLivrable->create();
            $result = $this->ProjectLivrable->save($new_data);

            $this->ZAuth->respond('success', array(
                'success' => !empty( $result) ? 'success' : 'failed',
                'data' => !empty( $result) ? $result : '',
            ), 'Created a Document', 0);
 
        }     

        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }

    // REF from delete of project livrables preview controller.php
    function delete() {
        if($this->RequestHandler->isPost()) {
            $data = $_POST;

            if(!isset($data['id'])) {
                $this->ZAuth->respond('fail', null, 'data not empty', '0');
                return;
            }
            $id = $data['id'];

            $last = $this->ProjectLivrable->find('first', array(
                'recursive' => -1,
                'fields' => array('project_id','livrable_file_attachment'),
                'conditions' => array('id' => $id)));


            if(!$this->UserUtility->user_can('write_project', $last['ProjectLivrable']['project_id'])){
                $this->ZAuth->respond('fail', null, 'permission deny', '0');
            }
            
            if ($last && $this->ProjectLivrable->delete($id)) {
                @unlink(trim($this->getPath('livrable', $this->employee_info['Company'])
                        . $last['ProjectLivrable']['livrable_file_attachment']));
                        
                $this->ZAuth->respond('success', null, 'Deleted Document', '0');
            }
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');


        
    }
}