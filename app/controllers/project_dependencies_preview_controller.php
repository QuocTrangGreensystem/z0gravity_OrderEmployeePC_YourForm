<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDependenciesPreviewController extends AppController {

    var $name='ProjectDependenciesPreview';
    var $uses = array('ProjectDependency');
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModels('Project', 'Dependency');
    }


    public function index($project_id = null){
        // debug(0); exit;
        $this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array());
		$this->loadModels('HistoryFilter');
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
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
		// debug($loadFilter); exit;
        $this->set(compact('dependencies', 'colors', 'projects', 'data', 'projectName', 'project_id', 'loadFilter'));

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
        $data = $this->ProjectDependency->retrieve($project_id);
        $list_deps_prjs = !empty( $data) ? Set::extract($data, '{n}.ProjectDependency.target_id') : array();
        $list_deps_prjs = array_unique( array_merge( array($project_id), $list_deps_prjs ));
        $projects = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $cid,
                'category' => array(1, 2),
            ),
            'fields' => array('id', 'project_name'),
            'order' => array('project_name')
        ));
        $globalViews = $this->getImageGlobalView(array_keys($projects));
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
        $this->set(compact('dependencies', 'colors', 'projects', 'data', 'projectName', 'project_id', 'list', 'count', 'history', 'globalViews'));
    }
	private function getImageGlobalView($project_ids = array() ){
		$this->loadModels('ProjectGlobalView');
		$globalViews = array();
        $projectGlobalView = $this->ProjectGlobalView->find("all", array(
            'recursive' => -1, 
            'fields' => array('id','project_id', 'attachment','is_file', 'is_https'),
            "conditions" => array('project_id' => $project_ids)
            )
        );
        $projectGlobalView = !empty( $projectGlobalView) ? Set::combine( $projectGlobalView, '{n}.ProjectGlobalView.project_id', '{n}.ProjectGlobalView') : array();
		foreach ($project_ids as $key => $p_id) {
			if( !empty( $projectGlobalView[$p_id])){
				$fileExist = $isImageFile = false;
				$link = trim($this->_getPathGlobal($p_id, 'global')
							. $projectGlobalView[$p_id]['attachment']);
				if (file_exists($link) && is_file($link)) {
					$fileExist = true;
					$isImageFile = preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $projectGlobalView[$p_id]['attachment']);
				}
				$globalViews[$p_id] = array(
					'project_id' => $p_id,
					'attachment' => $projectGlobalView[$p_id]['attachment'],
					'file' => $fileExist,
					'isImage' => $isImageFile
				);
			}else{
				$globalViews[$p_id] = array(
					'project_id' => $p_id,
					'attachment' => '',
					'file' => false,
					'isImage' => false
				);
			}
		}
		return $globalViews;
	}
	protected function _getPathGlobal($project_id, $global_view = false) {
        $company = $this->Project->find('first', array(
            'recursive' => 0,
            'fields' => array(
                'Company.parent_id',
                'Company.company_name',
                'Company.dir'
            ), 'conditions' => array('Project.id' => $project_id)));
        $pcompany = $this->Project->Company->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company['Company']['parent_id'])));
        $path = FILES . 'projects' . DS . 'localviews' . DS;
        if ($global_view == 'global')
            $path = FILES . 'projects' . DS . 'globalviews' . DS;
        if ($pcompany) {
            $path .= strtolower(Inflector::slug(' ', '_', $pcompany['Company']['dir'])) . DS;
        }
        $path .= $company['Company']['dir'] . DS;
        return $path;
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
    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $cid = $this->employee_info['Company']['id'];
        $conditions = array('project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectDependency.id'] = $data;
            }
        }
        $this->ProjectDependency->Behaviors->attach('Containable');

        $this->ProjectDependency->cacheQueries = true;

        $projectDependenciesPreviews = $this->ProjectDependency->find("all", array(
            'fields' => array('id', 'target_id', 'dependency_ids', 'value'),
            'recursive' => -1,
            "conditions" => $conditions
            )
        );
        $projectDependenciesPreviews = Set::combine($projectDependenciesPreviews, '{n}.ProjectDependency.id', '{n}.ProjectDependency');
        $list_deps_prjs = !empty( $projectDependenciesPreviews) ? Set::extract($projectDependenciesPreviews, '{n}.target_id') : array();
        $list_project = $this->Project->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $cid,
                'category' => array(1, 2),
                'id' => $list_deps_prjs
            ),
            'fields' => array('id', 'project_name'),
            'order' => array('project_name')
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
        if( !empty($projectDependenciesPreviews)){
            $i=1;
            foreach ($projectDependenciesPreviews as $key => $item) {
                $projectDependenciesPreviews[$key]['index'] = $i++;
                $projectDependenciesPreviews[$key]['target_name'] = $list_project[$projectDependenciesPreviews[$key]['target_id']];
            }
        }
        $this->set(compact('projectDependenciesPreviews', 'dependencies', 'colors'));
    }
}
