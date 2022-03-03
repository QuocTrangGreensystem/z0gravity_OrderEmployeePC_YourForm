<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectAmrSubProgramsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrSubPrograms';

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
        $this->loadModels('ProjectAmrProgram', 'ActivityFamily');
        if ($this->employee_info["Employee"]["is_sas"] != 1){
            $company_id = $this->employee_info["Company"]["id"];
            /**
             * Lay danh sach programs ra
             */
            $programs = $this->ProjectAmrProgram->find('all', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmrProgram.company_id' => $company_id),
                'fields' => array('id', 'amr_program', 'family_id', 'company_id'),
                'order' => array('amr_program')
            ));
            $company_names = $this->ProjectAmrSubProgram->ProjectAmrProgram->Company->getTreeList($company_id);
            /**
             * Danh sach family
             */
            $famLists = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'name'),
                'order' => array('name')
            ));
        } else {
            $company_id = "";
            /**
             * Lay danh sach programs ra
             */
            $programs = $this->ProjectAmrProgram->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'amr_program', 'family_id', 'company_id'),
                'order' => array('amr_program')
            ));
            $company_names = $this->ProjectAmrSubProgram->ProjectAmrProgram->Company->generateTreeList(null, null, null, '--');
            /**
             * Danh sach family
             */
            $famLists = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'order' => array('name')
            ));
        }
        $projectPrograms = !empty($programs) ? Set::combine($programs, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram.amr_program') : array();
        $familyIds = !empty($programs) ? Set::combine($programs, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram.family_id') : array();
        $companyPrograms = !empty($programs) ? Set::combine($programs, '{n}.ProjectAmrProgram.id', '{n}.ProjectAmrProgram.company_id') : array();
        $currentFamId = current($familyIds);
        /**
         * Lay sub family cua then programs hien tai
         */
        $subFamilies = array();
        if(!empty($currentFamId)){
            $subFamilies = $this->ActivityFamily->find('list', array(
                'recursive' => -1,
                'conditions' => array('ActivityFamily.parent_id' => $currentFamId),
                'fields' => array('id', 'name')
            ));
        }
        /**
         * Lay danh sach sub family cua cong ty
         */
        $projectAmrSubPrograms = $this->ProjectAmrSubProgram->find('all', array(
            'recursive' => -1,
            'conditions' => array('project_amr_program_id' => array_keys($projectPrograms)),
            'fields' => array('id', 'amr_sub_program', 'project_amr_program_id', 'sub_family_id'),
            'order' => array('project_amr_program_id', 'amr_sub_program')
        ));
        $this->set(compact('projectAmrSubPrograms', 'subFamilies', 'company_id', 'company_names', 'projectPrograms', 'familyIds', 'companyPrograms', 'famLists'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr sub program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrSubProgram', $this->ProjectAmrSubProgram->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrSubProgram->create();
            if ($this->ProjectAmrSubProgram->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
        }
        $projectAmrPrograms = $this->ProjectAmrSubProgram->ProjectAmrProgram->find('list');
        $this->set(compact('projectAmrPrograms'));
    }

    /**
     * check_dupplicate
     *
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
        if ($data['ProjectAmrSubProgram']['id'] == "") {
            // add new
            $rs = $this->ProjectAmrSubProgram->find('count', array('conditions' => array(
                    'ProjectAmrSubProgram.amr_sub_program' => $data['ProjectAmrSubProgram']['amr_sub_program'],
                    'ProjectAmrSubProgram.project_amr_program_id' => $data['ProjectAmrSubProgram']['project_amr_program_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrSubProgram->find('count', array('conditions' => array(
                    'ProjectAmrSubProgram.amr_sub_program' => $data['ProjectAmrSubProgram']['amr_sub_program'],
                    'ProjectAmrSubProgram.project_amr_program_id' => $data['ProjectAmrSubProgram']['project_amr_program_id'],
                    'NOT' => array('ProjectAmrSubProgram.id' => $data['ProjectAmrSubProgram']['id'])
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
    function check_exists_data($data) {
        $this->loadModels('ProjectAmrProgram');
        $data = !empty($data['ProjectAmrSubProgram']) ? $data['ProjectAmrSubProgram'] : array();
        $result = false;
        if(!empty($data['id'])){
            if(!empty($programs)){
                $checkDatas = $this->ProjectAmrSubProgram->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'project_amr_program_id' => $data['project_amr_program_id'],
                        'NOT' => array('ProjectAmrSubProgram.id' => $data['id'])
                    ),
                    'fields' => array('id', 'amr_sub_program')
                ));
                if(!empty($checkDatas) && in_array($data['amr_sub_program'], $checkDatas)){
                    $result = true;
                }
            }
        } else {
            $checkDatas = $this->ProjectAmrSubProgram->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'project_amr_program_id' => $data['project_amr_program_id'],
                    'ProjectAmrSubProgram.amr_sub_program' => $data['amr_sub_program']
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
		$this->loadModels('ProjectAmrProgram', 'ActivityFamily');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project amr sub program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
					$activate_family_linked_program = isset($this->companyConfigs['activate_family_linked_program']) && !empty($this->companyConfigs['activate_family_linked_program']) ? true : false;
                    if(!isset($this->data['ProjectAmrSubProgram']['sub_family_id'])){
                        $this->data['ProjectAmrSubProgram']['sub_family_id'] = '';
                    }
					$subFamily = $this->ActivityFamily->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'name' => $this->data['ProjectAmrSubProgram']['amr_sub_program'],
						),
						'fields' =>array('id', 'parent_id')
					));
					$linkedFamily = $this->ProjectAmrProgram->find('first', array(
						'recursive' => -1,
						'conditions' => array(
							'id' => $this->data['ProjectAmrSubProgram']['project_amr_program_id'],
						),
						'fields' => array('family_id')
					));
					$linkedFamily = !empty($linkedFamily) ? $linkedFamily['ProjectAmrProgram']['family_id'] : '';
	
					if($activate_family_linked_program && !empty($subFamily)){
						$parent_id = $subFamily['ActivityFamily']['parent_id'];
						if($parent_id != $linkedFamily){
							// Sub Family exists but it's not child of Family linked.
							 $this->Session->setFlash(__('Sub family already exists', true), 'error');
							 $this->redirect(array('action' => 'index'));
						}
					}
                    if ($this->ProjectAmrSubProgram->save($this->data)) {
						$sub_program_id = $this->ProjectAmrSubProgram->id;
						if($activate_family_linked_program && empty($this->data['ProjectAmrSubProgram']['sub_family_id'])){
							// If subFamilies is empty 
							// And setting program linked to a family is YES: auto add new a subfamily
							$sub_family_id = 0;
							if(!empty($subFamily)){
								$sub_family_id = $subFamily['ActivityFamily']['id'];
							}else{
								if(!empty($linkedFamily)){
									$saved = array(
										'name' => $this->data['ProjectAmrSubProgram']['amr_sub_program'],
										'company_id' => $this->employee_info['Company']['id'],
										'parent_id' => $linkedFamily,
									);
									$this->ActivityFamily->create();
									if ($this->ActivityFamily->save($saved)) {
										 $sub_family_id = $this->ActivityFamily->id;
									}
								}
							}
							if($sub_family_id != 0){
								$this->ProjectAmrSubProgram->id = $sub_program_id;
								$this->ProjectAmrSubProgram->saveField('sub_family_id', $sub_family_id);
							}
						}
                        $this->Session->setFlash(__('Saved', true), 'success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                        $this->redirect(array('action' => 'index'));
                    }
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrSubProgram->read(null, $id);
        }
        $projectAmrPrograms = $this->ProjectAmrSubProgram->ProjectAmrProgram->find('list');
        $this->set(compact('projectAmrPrograms'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project amr sub program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
		$last = $this->ProjectAmrSubProgram->find('first', array(
			'recursive' => -1,
			'conditions' => array( 
				'ProjectAmrSubProgram.id' => $id,
			),
			'fields' => array('project_amr_program_id')
		));
		$check = !empty($last ) ? ($this->is_sas || $this->_isBelongToCompany($last['ProjectAmrSubProgram']['project_amr_program_id'], 'ProjectAmrProgram')) : false;
        $check = ($this->is_sas || $check);
        $allowDeleteProjectAmrSubProgram = $this->_projectAmrSubProgramIsUsing($id);
        if($check && ($allowDeleteProjectAmrSubProgram == 'true')){
            if($check && ($this->ProjectAmrSubProgram->delete($id))) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect($this->referer());
            }
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect($this->referer());
    }

    /**
     * get_amr
     *
     * @return void
     * @access public
     */
    function get_amr($company_id = null) {
        $this->autoRender = false;
        if ($company_id != "") {
            $CountryIds = $this->ProjectAmrSubProgram->ProjectAmrProgram->find('all', array('conditions' => array('ProjectAmrProgram.company_id' => $company_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo "<option value='" . $CountryId['ProjectAmrProgram']['id'] . "'>" . $CountryId['ProjectAmrProgram']['amr_program'] . "</option>";
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option value="">', '</option>');
            }
        }
    }

    /**
     * get_2amr
     *
     * @return void
     * @access public
     */
    function get_2amr($amr_id = null) {
        $this->autoRender = false;
        if ($amr_id != "") {
            $CountryIds = $this->ProjectAmrSubProgram->ProjectAmrProgram->find('all', array('conditions' => array('ProjectAmrProgram.id' => $amr_id)));
            if (!empty($CountryIds)) {

                foreach ($CountryIds as $CountryId) {
                    echo $CountryId['ProjectAmrProgram']['company_id'];
                }
            } else {
                echo sprintf(__("%s--Select--%s", true), '<option>', '</option>');
            }
        }
    }
        /**
     *  Kiem tra Project Amr Sub Program da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectAmrSubProgramIsUsing($id = null){
        $this->loadModel('Project');
        $this->loadModel('ProjectAmr');
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array('Project.project_amr_sub_program_id' => $id)
            ));
        $checkProjectAmr = $this->ProjectAmr->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmr.project_amr_sub_program_id' => $id)
            ));
        $allowDeleteProjectAmrSubProgram= 'true';
        if($checkProject != 0 || $checkProjectAmr != 0){
            $allowDeleteProjectAmrSubProgram = 'false';
        }
        
        return $allowDeleteProjectAmrSubProgram;
    }

}
?>