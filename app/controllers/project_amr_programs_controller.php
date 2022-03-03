<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectAmrProgramsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectAmrPrograms';

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
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->set('company_id', $company_id);
        $this->ProjectAmrProgram->recursive = 0;
        $companies = $this->ProjectAmrProgram->Company->find('list');
        $parent_companies = $this->ProjectAmrProgram->Company->find('list', array('fields' => array('Company.id', 'Company.parent_id')));
        $this->set(compact('companies', 'parent_companies'));
        $families = $subFamilies = array();
        if ($company_id != "") {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_program', 'company_id', 'family_id', 'sub_family_id', 'color'),
                'limit' => 1005,
                'conditions' => array('OR' => array('company_id' => $company_id, 'parent_id' => $company_id))
            );
            $this->set('company_names', $this->ProjectAmrProgram->Company->getTreeList($company_id));
            $this->loadModel('ActivityFamily');
            $AllFamilies = $this->ActivityFamily->find('all', array(
                'order' => array('name ASC'),
                'recursive' => -1,
                'fields'    => array('id', 'name', 'parent_id'),
                'conditions' => array(
                    'company_id' => $company_id,
                    //'parent_id IS NULL'
                )
            ));
            if(!empty($AllFamilies)){
                foreach($AllFamilies as $AllFamily){
                    $dx = $AllFamily['ActivityFamily'];
                    if(!empty($dx['parent_id'])){
                        $subFamilies[$dx['id']] = $dx['name'];
                    } else {
                        $families[$dx['id']] = $dx['name'];
                    }
                }
            }
            $AllFamilies = !empty($AllFamilies) ? Set::combine($AllFamilies, '{n}.ActivityFamily.id', '{n}.ActivityFamily.name') : array();
        } else {
            $this->paginate = array(
                // phan trang
                'fields' => array('amr_program', 'company_id'),
                'limit' => 1005
            );
            $this->set('company_names', $this->ProjectAmrProgram->Company->generateTreeList(null, null, null, '--'));
        }
        $this->set('projectAmrPrograms', $this->paginate());
        $this->set(compact('families', 'subFamilies', 'AllFamilies'));
    }

    /**
     * view
     * @param int $id
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project amr program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectAmrProgram', $this->ProjectAmrProgram->read(null, $id));
    }

    /**
     * check_dupplicate
     * @param array $data
     * @return void
     * @access public
     */
    function check_dupplicate($data) {
        if ($data['id'] == "") {
            // add new
            $rs = $this->ProjectAmrProgram->find('count', array('conditions' => array(
                    'ProjectAmrProgram.amr_program' => $data['amr_program'],
                    'ProjectAmrProgram.company_id' => $data['company_id']
                    )));
        } else {
            // edit
            $rs = $this->ProjectAmrProgram->find('count', array('conditions' => array(
                    'ProjectAmrProgram.amr_program' => $data['amr_program'],
                    'ProjectAmrProgram.company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrProgram.id' => $data['id'])
                    )));
        }
        if ($rs == 0)
            return true;
        else
            return false;
    }

    /**
     * check_exists_data
     *
     * @return void
     * @access public
     */
    function check_exists_data($data) {
        $data = !empty($data) ? $data : array();
        $result = false;
        if(!empty($data['id'])){
            $checkDatas = $this->ProjectAmrProgram->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $data['company_id'],
                    'NOT' => array('ProjectAmrProgram.id' => $data['id'])
                ),
                'fields' => array('id', 'amr_program')
            ));
            if(!empty($checkDatas) && in_array($data['amr_program'], $checkDatas)){
                $result = true;
            }
        } else {
            $checkDatas = $this->ProjectAmrProgram->find('count', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $data['company_id'],
                    'ProjectAmrProgram.amr_program' => $data['amr_program']
                )
            ));
            if($checkDatas != 0){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectAmrProgram->create();
            if ($this->ProjectAmrProgram->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
        }
    }

    private function _createNewFamily($company_id = null, $programName = null){
        /**
         * Check xem family nay co chua
         */
        $this->loadModels('ActivityFamily');
        $results = 0;
        $fams = $this->ActivityFamily->find('first', array(
            'recursive' => - 1,
            'conditions' => array('company_id' => $company_id, 'name' => $programName, 'parent_id IS NULL'),
            'fields' => array('id')
        ));
        if(!empty($fams) && !empty($fams['ActivityFamily']['id'])){
            $results = $fams['ActivityFamily']['id'];
        } else {
            $saved = array(
                'name' => $programName,
                'company_id' => $company_id
            );
            $this->ActivityFamily->create();
            if($this->ActivityFamily->save($saved)){
                $results = $this->ActivityFamily->id;
            }
        }
        return $results;
    }

    /**
     * update
     * @param int $id
     * @return void
     * @access public
     */
    function update($id = null) {
		$result = false;
		$this->layout = false;
        $this->loadModels('ActivityFamily');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project amr program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->check_dupplicate($this->data)) {
                if ($this->check_exists_data($this->data) == false) {
                    if(isset($this->data['family_id']) && empty($this->data['family_id'])){
                        $this->data['sub_family_id'] = '';
                        $this->data['family_id'] = $this->_createNewFamily($this->data['company_id'], $this->data['amr_program']);
                    } else { // rename of famil
                        $this->ActivityFamily->id = $this->data['family_id'];
                        $this->ActivityFamily->save(array('name' => $this->data['amr_program']));
                    }
                    if ($this->ProjectAmrProgram->save($this->data)) {
						$this->data = $this->ProjectAmrProgram->read(null, $id);
						$this->data = $this->data['ProjectAmrProgram'];
						$result = true;
                        $this->Session->setFlash(__('Saved', true), 'success');
                    } else {
                        $this->Session->setFlash(__('NOT SAVED', true), 'error');
                    }
                }
            } else {
                $this->Session->setFlash(__('Already exist', true), 'error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ProjectAmrProgram->read(null, $id);
        }
		$this->set(compact('result'));
    }

    /**
     * delete
     * @param int $id
     * @return void
     * @access public
     */
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project amr program', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectAmrProgram'));
		$allowDeleteProjectAmrProgram = $this->_projectAmrProgramIsUsing($id);
        if($check && ($allowDeleteProjectAmrProgram == 'true')){
            if ($this->ProjectAmrProgram->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }
    /**
     *  Kiem tra Project Amr Program da co su dung
     *  @return boolean
     *  @access private
     */

    private function _projectAmrProgramIsUsing($id = null){
        $this->loadModel('Project');
        $this->loadModel('ProjectAmr');
        $this->loadModel('ProjectAmrSubProgram');
        $checkProject = $this->Project->find('count', array(
                'recursive' => -1,
                'conditions' => array(
					'Project.project_amr_program_id' => $id,
					'Project.company_id' => $this->employee_info['Company']['id'],
				)
            ));
        // $checkProjectAmr = $this->ProjectAmr->find('count', array(
                // 'recursive' => -1,
                // 'conditions' => array('ProjectAmr.project_amr_program_id' => $id)
            // ));
        $checkProjectAmrSubProgram = $this->ProjectAmrSubProgram->find('count', array(
                'recursive' => -1,
                'conditions' => array('ProjectAmrSubProgram.project_amr_program_id' => $id)
            ));
        $allowDeleteProjectAmrProgram= 'true';
        if($checkProject != 0 || $checkProjectAmrSubProgram != 0){
            $allowDeleteProjectAmrProgram = 'false';
        }
        return $allowDeleteProjectAmrProgram;
    }

}
?>
