<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectSubTypesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectSubTypes';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
        $this->ProjectSubType->recursive = 0;
        $projectSubTypes = array();
        $this->set('projectTypes', $this->ProjectSubType->ProjectType->find("list", array(
                    "fields" => array("ProjectType.id",
                        "ProjectType.project_type"))));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        if ($company_id != "") {
            $companies = $this->ProjectSubType->ProjectType->Company->find('all', array('conditions' => array('OR' => array('Company.id' => $company_id, 'Company.parent_id' => $company_id))));
            foreach ($companies as $company) {
                $projectTypes = $this->ProjectSubType->ProjectType->find('all', array('conditions' => array('OR' => array('ProjectType.company_id' => $company['Company']['id']))));
                foreach ($projectTypes as $projectType) {
                    $projectSubTypes = array_merge($projectSubTypes, $this->ProjectSubType->find('all', array('conditions' => array('ProjectSubType.project_type_id' => $projectType['ProjectType']['id']))));
                }
            }
            $this->set('company_names', $this->ProjectSubType->ProjectType->Company->getTreeList($company_id));
        } else {
            $this->paginate = array(
                'fields' => array('project_sub_type', 'company_id'),
                'limit' => 1005
            );
            $projectSubTypes = $this->ProjectSubType->find('all');
            $this->set('company_names', $this->ProjectSubType->ProjectType->Company->generateTreeList(null, null, null, '--'));
        }
        $this->loadModel('Company');
        $datas = array();
			
        foreach ($projectSubTypes as $key => $projectSubType) {
            $cId = $projectSubType['ProjectType']['company_id'];
            $_companies = $this->Company->find('first', array(
                'recursive' => -1,
                'conditions' => array('Company.id' => $cId)
            ));
            $projectSubType['ProjectType']['Company'] = !empty($_companies) ? $_companies['Company'] : array();
            $datas[$key] = $projectSubType;
        }
        $projectSubTypes = $datas;
        $this->set('projectSubTypes', $projectSubTypes);
    }
	
    function child() {
		$this->loadModels('ProjectType', 'Company');
		$consd = array();
		if ($this->employee_info["Employee"]["is_sas"] != 1){
            $company_id = $this->employee_info["Company"]["id"];
			$consd['ProjectType.company_id'] =  $company_id;
        }else
            $company_id = "";
		
		$projectSubTypes =  $this->ProjectSubType->find('all', array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'project_types',
					'alias' => 'ProjectType',
					'conditions' => array(
						$consd,
						'ProjectSubType.project_type_id = ProjectType.id',
					)
				)
			), 
			'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type', 'ProjectType.company_id'),
		));
		
		$subTypeID = Set::combine($projectSubTypes,'{n}.ProjectSubType.id','{n}.ProjectSubType.id');
		$subTypeName = Set::combine($projectSubTypes,'{n}.ProjectSubType.id','{n}.ProjectSubType.project_sub_type');
		$subTypeCompanyID = Set::combine($projectSubTypes,'{n}.ProjectSubType.id','{n}.ProjectType.company_id');
		
		$subSubTypes = array();
		if(!empty($subTypeID)){
			$subSubTypes = $this->ProjectSubType->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'parent_id' => $subTypeID,
				),
			));
			
			$subSubTypes = Set::combine($subSubTypes,'{n}.ProjectSubType.id','{n}.ProjectSubType');
		}
        $this->set('company_id', $company_id);
        if ($company_id != "") {
            $this->set('company_names', $this->ProjectSubType->ProjectType->Company->getTreeList($company_id));
        } else {
            $this->set('company_names', $this->ProjectSubType->ProjectType->Company->generateTreeList(null, null, null, '--'));
        }
		$this->set(compact('subTypeName', 'subSubTypes', 'subTypeCompanyID'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project sub type', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectSubType', $this->ProjectSubType->read(null, $id));
    }

    /**
     * check_dupplicate
     * Check duplicate subtype
     *
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
		// debug($data);
		// exit;
        if ($data['ProjectSubType']['id'] == "") {
            // add new
			$conds = array();
			if(!empty($data['ProjectSubType']['project_type_id'])){
				$conds['ProjectSubType.project_type_id'] = trim($data['ProjectSubType']['project_type_id']);
			}
            $rs = $this->ProjectSubType->find('count', array('conditions' => array(
                    'ProjectSubType.project_sub_type' => $data['ProjectSubType']['project_sub_type'],
                    $conds,
                 )));
        } else {
            // edit
            $rs = $this->ProjectSubType->find('count', array('conditions' => array(
                    'ProjectSubType.project_sub_type' => $data['ProjectSubType']['project_sub_type'],
                    'ProjectSubType.project_type_id' => $data['ProjectSubType']['project_type_id'],
                    'NOT' => array('ProjectSubType.id' => $data['ProjectSubType']['id'])
                    )));
        }
		
        if ($rs == 0)
            return true; else
            return false;
    }

    /**
     * check_exists_data
     *
     * @return void
     * @access public
     */
    /**
     * check_exists_data
     *
     * @return void
     * @access public
     */
    function check_exists_data($data) {
        $this->loadModels('ProjectAmrProgram');
        $data = !empty($data['ProjectSubType']) ? $data['ProjectSubType'] : array();
        $result = false;
        if(!empty($data['id'])){
            if(!empty($programs)){
                $checkDatas = $this->ProjectSubType->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_type_id' => $data['project_type_id'],
                        'NOT' => array('ProjectSubType.id' => $data['id'])
                    ),
                    'fields' => array('id', 'project_sub_type')
                ));
                if(!empty($checkDatas) && in_array($data['project_sub_type'], $checkDatas)){
                    $result = true;
                }
            }
        } else {
            $checkDatas = $this->ProjectSubType->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_type_id' => $data['project_type_id'],
                    'ProjectSubType.project_sub_type' => $data['project_sub_type']
                )
            ));
            if($checkDatas != 0){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function edit($id = null) {
		
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project sub type', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
			$action = 'index';
			if($this->data['action']){
				$action = $this->data['action'];
				unset($this->data['action']);
			}
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if ($this->ProjectSubType->save($this->data)) {
                        $this->Session->setFlash(__('Saved', true), 'success');
                        $this->redirect(array('action' => $action));
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true));
                    }
                } else {
                    $this->redirect(array('action' => $action));
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
                $this->redirect(array('action' => $action));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectSubType->read(null, $id);
        }
        $projectTypes = $this->ProjectSubType->ProjectType->find('list');	
        $this->set(compact('projectTypes'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $action = null) {
		$actName = 'index';
		if(!empty($action)) {
			$actName = $action;
		}
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project sub type', true));
            $this->redirect(array('action' => $actName));
        }
		$item = $this->ProjectSubType->find('first', array(
			'recursive' => -1,
			'conditions' => array( 
				'ProjectSubType.id' => $id,
			),
			'fields' => array('project_type_id','parent_id')
		));
		if( !empty($item['ProjectSubType']['parent_id'])){
			$item['ProjectSubType']['parent_id'];
			$item = $this->ProjectSubType->find('first', array(
				'recursive' => -1,
				'conditions' => array( 
					'ProjectSubType.id' => $item['ProjectSubType']['parent_id'],
				),
				'fields' => array('project_type_id','parent_id')
			));
		}
		$check = !empty($item ) ? ($this->is_sas || $this->_isBelongToCompany($item['ProjectSubType']['project_type_id'], 'ProjectType')) : false;
        $allowDeleteProjectSubType = $this->_projectSubTypeIsUsing($id);
        if($check && $allowDeleteProjectSubType == 'true'){
            if ($this->ProjectSubType->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => $actName));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => $actName));
        }
        $this->Session->setFlash(__('Not deleted', true));
        $this->redirect(array('action' => $actName));
    }

    /**
     * get_type
     *
     * @return void
     * @access public
     */
    function get_type($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $CountryIds = $this->ProjectSubType->ProjectType->find('all', array('conditions' => array('ProjectType.company_id' => $company_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo "<option value='" . $CountryId['ProjectType']['id'] . "'>" . $CountryId['ProjectType']['project_type'] . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option value="">', '</option>');
            }
        }
    }

    /**
     * get_type
     *
     * @return void
     * @access public
     */
    function get_sub_type($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $projectSubTypes =  $this->ProjectSubType->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'ProjectSubType.parent_id IS NULL'
				),
				'joins' => array(
					array(
						'table' => 'project_types',
						'alias' => 'ProjectType',
						'conditions' => array(
							'ProjectType.company_id' => $company_id,
							'ProjectType.id = ProjectSubType.project_type_id',
						)
					)
				), 
				'fields' => array('ProjectSubType.id', 'ProjectSubType.project_sub_type'),
			));
            if (!empty($projectSubTypes)) {

                foreach ($projectSubTypes as $id => $name) {
                    echo "<option value='" . $id . "'>" . $name . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option value="">', '</option>');
            }
        }
    }

    /**
     * get_2type
     * Get type project
     *
     * @return void
     * @access public
     */
    function get_2type($type_id = null) {
        $this->autoRender = false;
        if ($type_id != "") {
            $CountryIds = $this->ProjectSubType->ProjectType->find('all', array('conditions' => array('ProjectType.id' => $type_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo $CountryId['ProjectType']['company_id'];
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }
        /**
     *  Kiem tra project sub type da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectSubTypeIsUsing($id = null){
        $this->loadModel('Project');
        $checkProjectSubType = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_sub_type_id' => $id)
            ));
        $allowDeleteProjectSubType= 'true';
        if($checkProjectSubType != 0){
            $allowDeleteProjectSubType = 'false';
        }
		$checkProjectSubSubType = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_sub_sub_type_id' => $id)
            ));
		if($checkProjectSubSubType != 0){
            $allowDeleteProjectSubType = 'false';
        }
        return $allowDeleteProjectSubType;
    }

}
?>
