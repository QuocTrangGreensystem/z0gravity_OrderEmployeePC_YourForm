<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAcceptancesController extends AppController {
    var $components = array('MultiFileUpload');

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModels('ProjectAcceptanceType', 'Employee');
    }
    public function index($project_id = null){
        $this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array());
        $this->_checkWriteProfile('acceptance');
        $this->loadModel('Project');
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $company_id = $projectName['Project']['company_id'];
        $acceptances = $this->ProjectAcceptance->find('all', array(
            'fields' => '*',
            'conditions' => array(
                'project_id' => $project_id
            )
        ));
        $employee_ids = Set::classicExtract($acceptances, '{n}.Acceptance.employee_id');
        if( $employee_ids )$employee_ids = array_unique($employee_ids);
        $this->Employee->virtualFields['full_name'] = 'concat(first_name, " ", last_name)';
        $this->set('employees', $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('full_name'),
            'joins' => array(
                array(
                    'table' => 'company_employee_references',
                    'alias' => 'Refer',
                    'type' => 'inner',
                    'conditions' => array(
                        'Employee.id = Refer.employee_id',
                        'Employee.company_id' => $company_id
                    )
                )
            )
        )));
        $this->set('types', $this->ProjectAcceptanceType->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array(
                'company_id' => $company_id
            )
        )));
        $this->set(compact('project_id', 'company_id', 'projectName', 'acceptances'));
    }
    public function upload() {
        $result = false;
        if (empty($this->data['Upload']) && !empty($_FILES)) {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        } else {
            $ProjectAcceptances = $this->ProjectAcceptance->find('first', array(
                'recursive' => -1,
                'fields' => array('*'),
                'conditions' => array(
                    'id' => $this->data['Upload']['id']
                )
            ));
            if ($ProjectAcceptances) {
                $project_id = $ProjectAcceptances['ProjectAcceptance']['project_id'];
            }
            $path = $this->_getPath($project_id);
            // neu co url
            if(!empty($this->data['Upload']['url'])){
                $this->ProjectAcceptance->id = $this->data['Upload']['id'];
                $last = $this->ProjectAcceptance->find('first', array(
                    'recursive' => -1,
                    'fields' => array('file'),
                    'conditions' => array('id' => $this->ProjectAcceptance->id)));
                if ($last && $last['ProjectAcceptance']['file']) {
                    unlink($path . $last['ProjectAcceptance']['file']);
                }
                if ($this->ProjectAcceptance->save(array(
                            'file' => $this->data['Upload']['url'],
                            'file_type' => 0
                        ))) {
                    $this->Session->setFlash(__('Saved', true), 'success');
                    $result = true;
                } else {
                    $this->Session->setFlash(__('The url could not be uploaded.', true), 'error');
                }
            } else {
                App::import('Core', 'Folder');
                new Folder($path, true, 0777);
                if (file_exists($path)) {
                    $this->MultiFileUpload->encode_filename = false;
                    $this->MultiFileUpload->uploadpath = $path;
                    $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,gzip,tgz,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm";
                    $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                    $attachment = $this->MultiFileUpload->upload();
                } else {
                    $attachment = "";
                    $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                            , true), $path), 'error');
                }
                if (!empty($attachment)) {
                    $attachment = $attachment['attachment']['attachment'];
                    $this->ProjectAcceptance->id = $this->data['Upload']['id'];
                    $last = $this->ProjectAcceptance->find('first', array(
                        'recursive' => -1,
                        'fields' => array('file'),
                        'conditions' => array('id' => $this->ProjectAcceptance->id)));
                    if ($last && $last['ProjectAcceptance']['file']) {
                        unlink($path . $last['ProjectAcceptance']['file']);
                    }
                    if ($this->ProjectAcceptance->save(array(
                                'file' => $attachment,
                                'file_type' => 1
                            ))) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        if($this->MultiFileUpload->otherServer == true){
                            $this->MultiFileUpload->uploadFileToServerOther($path, $attachment, '/project_acceptances/index/' . $project_id);
                        }
                        $result = true;
                    } else {
                        unlink($path . $attachment);
                        $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                    }
                } else {
                    $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
                }
            }
        }
        $this->redirect(array('action' => 'index', $project_id));
        //$this->layout = false;
        $this->set(compact('result', 'attachment'));
    }

    function updateWeather(){
        if( !empty($this->data) ){
            $id = $this->data['id'];
            $this->ProjectAcceptance->save(array(
                'id' => $id,
                'weather' => $this->data['weather']
            ));
        }
        die;
    }

    public function update(){
        $result = false;
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $id = $this->data['id'];
            $this->ProjectAcceptance->id = $id;
            $data = array();
            foreach (array('due_date', 'effective_date') as $key) {
                if (!empty($this->data[$key])) {
                    $data[$key] = $this->ProjectAcceptance->convertTime($this->data[$key]);
                }
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectAcceptance->save(array_merge($this->data, $data))) {
                    $result = true;
                    $this->Session->setFlash(__('The acceptance has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The acceptance could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectAcceptance->id;
            }
        } else {
            $this->Session->setFlash(__('The data submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project acceptance', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
		$last = $this->ProjectAcceptance->find('first', array(
                'recursive' => -1,
                'fields' => array('project_id', 'file'),
                'conditions' => array('ProjectAcceptance.id' => $id)));
        $project_id = !empty( $last) ? $last['ProjectAcceptance']['project_id'] : 0;    
        if ($this->_checkRole(false, $project_id)) {
            if ($this->ProjectAcceptance->delete($id)) {
                @unlink(trim($this->_getPath($last['ProjectAcceptance']['project_id'])
                        . $last['ProjectAcceptance']['file']));
                $this->Session->setFlash(__('Deleted.', true), 'success');
                $this->redirect(array('action' => 'index', $project_id));
            }
            $this->Session->setFlash(__('Failed to delete', true), 'error');
        }
        $this->redirect(array('action' => 'index', $project_id));
    }

    public function attachement($id = null) {
        $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
        $last = $this->ProjectAcceptance->find('first', array(
            'recursive' => -1,
            'fields' => array('file', 'project_id'),
            'conditions' => array('ProjectAcceptance.id' => $id)));
        $error = true;
        if ($last && $last['ProjectAcceptance']['project_id']) {
            $path = trim($this->_getPath($last['ProjectAcceptance']['project_id'])
                    . $last['ProjectAcceptance']['file']);
            $attachment = $last['ProjectAcceptance']['file'];
            if( $type == 'download'){
                if (file_exists($path) && is_file($path)) {
                    if ($type == 'download') {
                        $info = pathinfo($path);
                        $this->view = 'Media';
                        $params = array(
                            'id' => $info['basename'],
                            'path' => $info['dirname'] . DS,
                            'name' => $info['filename'],
                            'extension' => strtolower($info['extension']),
                            'download' => true
                        );
                        $params['mimeType'][$info['extension']] = 'application/octet-stream';
                        $this->set($params);
                    }
                    $error = false;
                }
            } else {
                @unlink($path);
                $this->ProjectAcceptance->id = $id;
                $this->ProjectAcceptance->save(array(
                    'file' => '',
                    'format' => 0
                ));
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($last['ProjectAcceptance']['project_id']));
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/project_acceptances/index/' . $last['ProjectAcceptance']['project_id']);
                }
            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            $this->redirect(array('action' => 'index',
                $last ? $last['ProjectAcceptance']['project_id'] : __('Unknown', true)));
        }
    }

    protected function _getPath($project_id) {
        $this->loadModel('Project');
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'acceptances' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
}
