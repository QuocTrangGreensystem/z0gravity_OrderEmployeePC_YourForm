<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectPartsController extends AppController {
    var $name = 'ProjectParts';
    function index($project_id = null) {
        $this->_checkRole(false, $project_id);
        $this->_checkWriteProfile('part');
        $projectName = $this->viewVars['projectName'];
        $this->ProjectPart->Behaviors->attach('Containable');
        $this->ProjectPart->cacheQueries = true;

        $projectParts = $this->ProjectPart->find("all", array(
            'fields' => array('id', 'title', 'description'),
            'recursive' => -1,
            "conditions" => array('project_id' => $project_id),
            'order' => array('weight' => 'ASC')));
		$this->loadModels('HistoryFilter');
		$loadFilter = $this->HistoryFilter->loadFilter(rtrim($this->params['url']['url'], '/'));
		$loadFilter = !empty($loadFilter) ? unserialize($loadFilter) : array();
        $this->set(compact('projectName', 'projectParts', 'project_id', 'loadFilter'));
    }
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data)) {
            $this->ProjectPart->create();
            if (!empty($this->data['id'])) {
                $this->ProjectPart->id = $this->data['id'];
            }
            if ($this->_checkRole(true)) {
                unset($this->data['id']);
                if ($this->ProjectPart->save($this->data)) {
                    $result = true;
                } else {
                    $this->Session->setFlash(__('The Part could not be saved. Please, try again.', true), 'error');
                }
                $this->data['id'] = $this->ProjectPart->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    public function export($project_id = null) {
        $this->_checkRole(false, $project_id);
        $conditions = array('project_id' => $project_id);
        if (!empty($this->data['Export']['list'])) {
            $data = array_filter(explode(',', $this->data['Export']['list']));
            if ($data) {
                $conditions['ProjectPart.id'] = $data;
            }
        }
        $this->ProjectPart->Behaviors->attach('Containable');

        $this->ProjectPart->cacheQueries = true;

        $projectParts = $this->ProjectPart->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions));
        $projectParts = Set::combine($projectParts, '{n}.ProjectPart.id', '{n}');
        if (!empty($data)) {
            $data = array_flip($data);
            foreach ($data as $id => $k) {
                if (!isset($projectParts[$id])) {
                    unset($data[$id]);
                    unset($projectParts[$id]);
                    continue;
                }
                $data[$id] = $projectParts[$id];
            }
            $projectParts = $data;
            unset($data);
        }
        $this->set(compact('projectParts'));
        $this->layout = '';
    }

    /**
     * delete
	 * 11/09/2019 Z0g - prod5 - impossible to delete the phase
	 * Check task without phase
     */
    function delete($id = null) {
        $this->loadModel('ProjectPhasePlan');
        $this->loadModel('ProjectTask');
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project part', true), 'error');
            $this->redirect(array('action' => 'index', $project_id));
        }
		$part = $this->ProjectPart->read(null, $id);
		$project_id = @$part['ProjectPart']['project_id'];
        if ($this->_checkRole(false, $project_id)) {
			$n_tasks = 0; // number of task
            $getPhasePlans = $this->ProjectPhasePlan->find('list', array(
                'recursive' => -1,
                'conditions' => array(
					'project_part_id' => $id,
					'project_id' => $project_id
				),
                'fields' => array('id')
            ));
			if( !empty( $getPhasePlans )){
				$n_tasks = $this->ProjectTask->find('all', array(
					'recursive' => -1,
					'conditions' => array('project_id' => $project_id, 'project_planed_phase_id' => array_unique($getPhasePlans))
				));
			}
            if(!empty($n_tasks)){
                $this->Session->setFlash(__('Not Delete.', true), 'error');
            } else {
                if ($this->ProjectPart->delete($id)) {
                    $this->Session->setFlash(__('Project part has been deleted', true), 'success');
                    $this->redirect(array('action' => 'index', $project_id));
                }
                $this->Session->setFlash(__('Project part was not deleted', true), 'error');
            }
        }
        $this->redirect(array('action' => 'index', $project_id));
    }

    function order($id){
        foreach ($this->data as $id => $weight) {
            if (!empty($id) && !empty($weight) && $weight!=0) {
                $this->ProjectPart->id = $id;
                $this->ProjectPart->save(array(
                    'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

}
?>
