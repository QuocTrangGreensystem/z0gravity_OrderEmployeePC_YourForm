<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectCreatedValsController extends AppController {

    var $uses = array('ProjectCreatedValue', 'ProjectCreatedVal', 'Project');

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectCreatedVals';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Gantt');

    /**
     * index
     * @params $type
     * @return void
     * @access public
     */
    function index($project_id = null) {
        if(isset($this->employee_info['Color']['is_new_design']) && $this->employee_info['Color']['is_new_design']){
            $this->redirect(array(
                'controller' => 'project_created_vals_preview',
                'action' => 'index/'.$project_id

            ));
        }
        if (!$this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
        }
        $this->_checkWriteProfile('created_value');
        $project_name = $this->Project->find('first', array(
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('Project.project_name'),
            'recursive' => -1));
        $this->set('project_name', $project_name['Project']['project_name']);
        $projectCreatedVals = $this->ProjectCreatedVal->find('all', array('conditions' => array('ProjectCreatedVal.project_id' => $project_id)));
        $projectC = array();
        $id = '';
        if (!empty($projectCreatedVals)) {
            foreach ($projectCreatedVals as $projectCreatedVal) {
                if ($projectCreatedVal['ProjectCreatedVal']['description'] != '0')
                    $projectC['ProjectCreatedVal'] = unserialize($projectCreatedVal['ProjectCreatedVal']['description']);
                else
                    $projectC['ProjectCreatedVal'] = "";
            }
            $id = $projectCreatedVals[0]['ProjectCreatedVal']['id'];
        }
        $this->set('id', $id);
        $this->set('projectC', $projectC);
        //$langCode = Configure::read('Config.langCode');
        $this->set(compact('project_id'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'value_order'),
				'order' => array('value_order'), 
			));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        } else {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'fields' => array('id', 'description', 'value', 'type_value', 'language')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        }
        $this->set(compact('created_values','totalValue'));

    }
	function ajax($project_id = null) {
        if (!$this->_checkRole(true, $project_id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
        }

        $project_name = $this->Project->find('first', array(
            'conditions' => array('Project.id' => $project_id),
            'fields' => array('Project.project_name'),
            'recursive' => -1));
        $this->set('project_name', $project_name['Project']['project_name']);
        $projectCreatedVals = $this->ProjectCreatedVal->find('all', array('conditions' => array('ProjectCreatedVal.project_id' => $project_id)));
        $projectC = array();
        $id = '';
        if (!empty($projectCreatedVals)) {
            foreach ($projectCreatedVals as $projectCreatedVal) {
                if ($projectCreatedVal['ProjectCreatedVal']['description'] != '0')
                    $projectC['ProjectCreatedVal'] = unserialize($projectCreatedVal['ProjectCreatedVal']['description']);
                else
                    $projectC['ProjectCreatedVal'] = "";
            }
            $id = $projectCreatedVals[0]['ProjectCreatedVal']['id'];
        }
        $this->set('id', $id);
        $this->set('projectC', $projectC);
        //$langCode = Configure::read('Config.langCode');
        $this->set(compact('project_id'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
         $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        } else {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'fields' => array('id', 'description', 'value', 'type_value', 'language')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        }
        $this->set(compact('created_values','totalValue'));
		$this->render('ajax');
    }

    /**
     * saveCreated
     * @params $type
     *
     * @return void
     * @access public
     */
    function saveCreated() {
        if (!$this->_checkRole(true, $this->data['project_id'])) {
            exit();
        }
        $this->autoRender = false;
        $this->data['ProjectCreatedVal']['project_id'] = $this->data['project_id'];
        if (!empty($this->data['created_value'])) {
            $this->data['ProjectCreatedVal']['description'] = serialize($this->data['created_value']);
        }
        else
            $this->data['ProjectCreatedVal']['description'] = 0;
        $procrevalue = $this->ProjectCreatedVal->findByProjectId($this->data['project_id']);

        if (!empty($procrevalue)) {
            $this->ProjectCreatedVal->id = $procrevalue['ProjectCreatedVal']['id'];
            $this->ProjectCreatedVal->save($this->data['ProjectCreatedVal']);
            $this->Project->id = $this->data['project_id'];
            $this->Project->saveField('created_value', $this->data['value']);
        } else {
            if ($this->ProjectCreatedVal->save($this->data['ProjectCreatedVal'])) {
                $this->Project->id = $this->data['project_id'];
				$this->Project->saveField('created_value', $this->data['value']);
            }
        }
    }

}
?>
