<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDependency extends AppModel {
    public $virtualFields = array(
        'grouper' => 'CASE WHEN project_id < target_id THEN CONCAT(project_id, " ", target_id) ELSE CONCAT(target_id, " ", project_id) END'
    );
    public function sync($target, $project, $dependencies){
        if( !is_array($dependencies) ){
            $this->deleteAll(array(
                'target_id' => $project,
                'project_id' => $target
            ));
            return;
        }
        else $dependencies = json_encode($dependencies);
        $f = $this->find('first', array(
            'conditions' => array(
                'target_id' => $project,
                'project_id' => $target
            )
        ));
        $data = array(
            'dependency_ids' => $dependencies,
            'target_id' => $project,
            'project_id' => $target,
            'id' => null
        );
        if( !empty($f) ){
            $data['id'] = $f['ProjectDependency']['id'];
        }
        $this->save($data);
    }
    public function syncProject($data){
        $this->sync($data['target_id'], $data['project_id'], $data['dependency_ids']);
    }
    public function remove($project_id, $target_id = null){
        if( $target_id ){
            $this->deleteAll(array(
                'OR' => array(
                    array(
                        'project_id' => $project_id,
                        'target_id' => $target_id
                    ),
                    array(
                        'project_id' => $target_id,
                        'target_id' => $project_id
                    )
                )
            ));
        } else {
            //delete by id
            $rec = $project_id;
            $target = $this->read('target_id, project_id', $rec);
            $this->remove($target['ProjectDependency']['target_id'], $target['ProjectDependency']['project_id']);
        }
    }
    public function currentTarget($id){
        $data = $this->read('target_id', $id);
        if( $data )return $data['ProjectDependency']['target_id'];
        return;
    }
    public function retrieve($project_id, $exception = 0){
        $data = $this->find('all', array(
            'conditions' => array(
                'project_id' => $project_id,
                'target_id !=' => $exception
            ),
            'fields' => array('id', 'project_id', 'target_id', 'dependency_ids', 'grouper', 'value'),
            'order' => array('target_id' => 'ASC')
        ));
        $targets = !empty($data) ? Set::classicExtract($data, '{n}.ProjectDependency.target_id') : array();
        $data2 = $this->find('all', array(
            'conditions' => array(
                'project_id' => $targets,
                'target_id' => $targets,
                'target_id !=' => $exception,
                'project_id !=' => $exception
            ),
            'fields' => array('id', 'project_id', 'target_id', 'dependency_ids', 'grouper', 'value'),
            'order' => array('target_id' => 'ASC'),
            'group' => array('grouper')
        ));
        return array_merge($data, $data2);
    }
    public function countChildren($projects){
        $data = $this->find('all', array(
            'conditions' => array(
                'project_id' => $projects
            ),
            'fields' => array('project_id', 'dependency_ids')
        ));
        $result = array();
        foreach($data as $dat){
            $id = $dat['ProjectDependency']['project_id'];
            $count = count(json_decode($dat['ProjectDependency']['dependency_ids']));
            if( isset($result[$id]) ){
                $result[$id] += $count;
            } else {
                $result[$id] = $count;
            }
        }
        return $result;
    }
}