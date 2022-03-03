<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDependenciesController extends AppController {

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModels('Project', 'Dependency');
    }

    public function index($project_id = null){
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            if( !((isset($this->params['url']['noredirect']) && $this->params['url']['noredirect']))){
                $_controller = !empty($this->params['controller']) ? $this->params['controller'] : '';
                $_controller_preview =  trim( str_replace('_preview', '', $_controller), ' \t\n\r\0\x0B_').'_preview';
                $_action = !empty($this->params['action']) ? $this->params['action'] : 'index';
                $_url_param = !empty($this->params['url']) ? $this->params['url'] : array();
                $_pass_arr = !empty($this->params['pass']) ? $this->params['pass'] : array();
                $_pass = '';
                foreach ($_pass_arr as $value) {
                    $_pass .= '/'.$value;
                }
                if( isset($_url_param['url'])) unset($_url_param['url']);
                $this->redirect(array(
                    'controller' => $_controller_preview,
                    'action' => $_action,
                    $_pass,
                    '?' => $_url_param,
                ));
            }
        }
        $this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array());
        $this->_checkWriteProfile('dependency');
        $cid = $this->employee_info['Company']['id'];
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $deps = $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $cid
            ),
            'fields' => array('id', 'name', 'color'),
            'order' => array('Dependency.name')
        ));
        $dependencies = $colors = array();
        foreach($deps as $dep){
            $dependencies[(string)$dep['Dependency']['id']] = $dep['Dependency']['name'];
            $colors[$dep['Dependency']['id']] = $dep['Dependency']['color'];
        }
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $cid,
                'category' => array(1, 2),
                'id !=' => $project_id
            ),
            'fields' => array('id', 'project_name'),
            'order' => array('project_name')
        ));
        $data = $this->ProjectDependency->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'project_id' => $project_id
            ),
            'order' => array('target_id' => 'ASC')
        ));
        $this->set(compact('dependencies', 'colors', 'projects', 'data', 'projectName', 'project_id'));
    }
    public function update(){
        $result = false;
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $id = $this->data['id'];
            if( $id ){
                $target = $this->ProjectDependency->currentTarget($id);
                $this->ProjectDependency->remove($this->data['project_id'], $target);
                $id = $this->ProjectDependency->id = null;
            }
            $data['dependency_ids'] = json_encode($this->data['dependency_ids']);
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectDependency->save(array_merge($this->data, $data))) {
                    $this->data['id'] = $this->ProjectDependency->id;
                    //sync
                    $this->ProjectDependency->syncProject($this->data);
                    $result = true;
                    $this->Session->setFlash(__('The dependency has been saved.', true), 'success');
                } else {
                    $this->Session->setFlash(__('The dependency could not be saved. Please, try again.', true), 'error');
                }
            }
        } else {
            $this->Session->setFlash(__('The data submited to server is invaild.', true), 'error');
        }
        $this->set('result', $result);
    }

    function delete($id = null, $project_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project dependency', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
        if ($this->_checkRole(true, $project_id)) {
            $this->ProjectDependency->remove($id);
            $this->Session->setFlash(__('Deleted!', true), 'success');
        }
        $this->redirect(array('action' => 'index', $project_id));
    }

    function view($project_id = null){
        $this->_checkRole(true, $project_id, empty($this->data) ? array('element' => 'warning') : array());
        $cid = $this->employee_info['Company']['id'];
        $projectName = $this->Project->find('first', array(
            'recursive' => -1,
            'conditions' => array('Project.id' => $project_id)
        ));
        $deps = $this->Dependency->find('all', array(
            'conditions' => array(
                'company_id' => $cid
            ),
            'fields' => array('id', 'name', 'color')
        ));
        $dependencies = $colors = array();
        foreach($deps as $dep){
            $dependencies[$dep['Dependency']['id']] = $dep['Dependency']['name'];
            $colors[$dep['Dependency']['id']] = $dep['Dependency']['color'];
        }
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $cid,
                'category' => array(1, 2),
                'id !=' => $project_id
            ),
            'fields' => array('id', 'project_name')
        ));
        $data = $this->ProjectDependency->retrieve($project_id);
        $list = !empty($data) ? array_unique(Set::extract($data, '{n}.ProjectDependency.target_id')) : array();
        $count = $this->ProjectDependency->countChildren($list);
        // history.
        $this->loadModels('HistoryFilter');
        $employee_info = $this->employee_info;
        $history = $this->HistoryFilter->find('first', array(
            'conditions' => array(
                'path' => 'dependency_' . $project_id,
                'employee_id' => $employee_info['Employee']['id']
            ),
            'fields' => array('params')
        ));
        if( !empty($history) ){
            $history = json_decode($history['HistoryFilter']['params'], true);
        } else {
            $history = array();
        }
        $this->set(compact('dependencies', 'colors', 'projects', 'data', 'projectName', 'project_id', 'list', 'count', 'history'));
        $this->render('view3');
    }

    function expand($project_id, $parent = 0){
        $data = $this->ProjectDependency->retrieve($project_id, $parent);
        $list = !empty($data) ? array_unique(Set::extract($data, '{n}.ProjectDependency.target_id')) : array();
        $count = $this->ProjectDependency->countChildren($list);
        die(json_encode(array('data' => $data, 'count' => $count)));
    }
    public function updateImage(){
        if(!empty($_POST)){
            $id = $_POST['id'];
            $value = $_POST['value'];
            $this->ProjectDependency->id = $id;
            $this->ProjectDependency->save(array('value' => $value));
            echo 'Done';
            exit;
        }
        echo 'Error';
        exit;
    }
}
