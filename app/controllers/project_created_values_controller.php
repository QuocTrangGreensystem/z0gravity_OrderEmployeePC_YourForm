<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectCreatedValuesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectCreatedValues';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * index
     * @params $type 
     * @return void
     * @access public
     */
    function index($type = 'financial') {
        if (!empty($type)) {
            $this->set('type', $type);
            $this->ProjectCreatedValue->recursive = 0;
            $companies1 = $this->ProjectCreatedValue->Company->find('list');
            $parent_companies = $this->ProjectCreatedValue->Company->find('list', array('fields' => array('id', 'parent_id')));
            $this->set(compact('companies1', 'parent_companies'));

            if ($this->employee_info["Employee"]["is_sas"] != 1)
                $companyId = $this->employee_info["Company"]["id"];
            else
                $companyId = "";
            $this->set('company_id', $companyId);

            if ($companyId != "") {
                $this->paginate = array(
                    //paginate
                    'conditions' => array('ProjectCreatedValue.type_value' => $type, 'OR' => array(
                            'ProjectCreatedValue.company_id' => $companyId, 'parent_id' => $companyId
                    )),
                    'fields' => array('id', 'description', 'type_value', 'value', 'company_id', 'value_order', 'block_name', 'next_block'),
                    'order' => array('company_id ASC', 'value_order IS NULL ASC', 'value_order ASC'),
                    'limit' => 1000,
                );
                $this->set('companies', $this->ProjectCreatedValue->Company->getTreeList($companyId));
            } else {
                $this->paginate = array(
                    //paginate
                    'conditions' => array('ProjectCreatedValue.type_value' => $type),
                    'fields' => array('id', 'description', 'type_value', 'value', 'company_id', 'value_order','block_name', 'next_block'),
                    'order' => array('company_id ASC', 'value_order IS NULL ASC', 'value_order ASC'),
                    'limit' => 1000,
                );

                $this->set('companies', $this->ProjectCreatedValue->Company->generateTreeList(null, null, null, '--'));
            }
            // edit label name and question

            $this->loadModel('Translation');
            $translation_data = $this->Translation->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'page' => 'Created_Value',
                    'TranslationSetting.company_id' => $companyId
                ),
                'fields' => '*',
                'joins' => array(
                    array(
                        'table' => 'translation_settings',
                        'alias' => 'TranslationSetting',
                        'conditions' => array(
                            'Translation.id = TranslationSetting.translation_id'
                        ),
                        'type' => 'left'
                    )
                ),
                'order' => array(
                    'TranslationSetting.setting_order' => 'ASC'
                )
            ));
            // debug($translation_data); exit;
            $this->set('translation_data', $translation_data);
            $this->set('createdValues', $this->paginate());
        }
    }

    /**
     * get_order_up
     * Get order up
     * 
     * @return void
     * @access public
     */
    function get_order_up() {
        $this->layout = 'ajax';
        $this->autoRender = false;

		foreach ($this->data as $id => $value_order) {
            if (!empty($id) && !empty($value_order) && $value_order!=0) {
                $this->ProjectCreatedValue->id = $id;
                $this->ProjectCreatedValue->save(array(
                    'value_order' => intval($value_order)), array('validate' => false, 'callbacks' => false));
            }
        }
        die;
    }

    /**
     * get_order_down
     * Get order down
     *
     * @return void
     * @access public
     */
    function get_order_down() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $id_up = $_POST['created_id_up'];
        $comapny_id_up = $_POST['company_id_up'];
        $value_order_up = $_POST['value_order_up'];

        $id_down = $_POST['created_id_down'];
        $comapny_id_down = $_POST['company_id_down'];
        $value_order_down = $_POST['value_order_down'];

        if ($value_order_down == null || $value_order_down == '')
            $value_order_down = 2;
        if ($value_order_up == null || $value_order_up == '')
            $value_order_up = 1;

        if ($value_order_down == $value_order_up) {
            $value_order_down++;
            $value_order_up--;
        }

        $this->ProjectCreatedValue->id = $id_up;
        $this->ProjectCreatedValue->saveField('value_order', $value_order_down);
        echo $value_order_down . "|";

        $this->ProjectCreatedValue->id = $id_down;
        $this->ProjectCreatedValue->saveField('value_order', $value_order_up);
        echo $value_order_up;
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid project phase', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('projectPhase', $this->ProjectPhase->read(null, $id));
    }

    /**
     * add
     *
     * @return void
     * @access public
     */
    function add() {
        if (!empty($this->data)) {
            $this->ProjectPhase->create();
            if ($this->ProjectPhase->save($this->data)) {
                $this->Session->setFlash(__('Saved', true), 'success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
            }
        }
    }

    /**
     * edit
     *
     * @return void
     * @access public
     */
    function update($id = null) {
		$success = false;
		$this->layout = false;
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid project created value', true), 'error');
            $this->redirect(array('action' => 'index', $this->data['type_value']));
        }
        if (!empty($this->data)) {
            if (empty($this->data["id"])) {
                $this->ProjectCreatedValue->recursive = -1;
                $last_order = $this->ProjectCreatedValue->find("first", array(
                    "fields" => array("(Max(ProjectCreatedValue.value_order)+1) value_last_order"),
                    "conditions" => array("ProjectCreatedValue.company_id" => $this->data["company_id"])));
                $this->data["value_order"] = $last_order[0]["value_last_order"];
            }
			if (!empty($this->data['id'])) {
                $this->ProjectCreatedValue->id = $this->data['id'];
            }else{
				$this->ProjectCreatedValue->create();
			}
			$result = $this->ProjectCreatedValue->save($this->data);
            if ($result) {
				$id = $this->ProjectCreatedValue->id;
				$this->data = $this->ProjectCreatedValue->read(null, $id);
				$this->data = $this->data['ProjectCreatedValue'];
                $this->Session->setFlash(__('Saved', true), 'success');
				$success = true;
            } else {
                $this->Session->setFlash(__('NOT SAVED', true), 'error');
                $this->redirect(array('action' => 'index', $this->data['type_value']));
            }
        }
        $this->set(compact('success'));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $type = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for project created value', true), 'error');
            $this->redirect(array('action' => 'index', $type));
        }
		$check = ($this->is_sas || $this->_isBelongToCompany($id, 'ProjectCreatedValue'));
        $allowDeleteProjectCreatedValue = $this->_projectCreatedValueIsUsing($id);
		if($check && ($allowDeleteProjectCreatedValue == 'true')){
            if ($this->ProjectCreatedValue->delete($id)) {
                $this->Session->setFlash(__('Deleted', true), 'success');
                $this->redirect(array('action' => 'index', $type));
            }
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
            $this->redirect(array('action' => 'index', $type));
        }
        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index', $type));
    }
        /**
     *  Kiem tra project created value da co su dung
     *  @return boolean
     *  @access private
     */
    
    private function _projectCreatedValueIsUsing($id = null){
        $this->loadModel('ProjectCreatedVal');
        $id = '"'.$id.'"';
        $checkProjectCreatedVal = $this->ProjectCreatedVal->find('first', array(
                'recursive' => -1,
                'conditions' => array('ProjectCreatedVal.description LIKE' => "%$id%"),
                'fields'=>array('id')
            ));
        $allowDeleteProjectCreatedValue= 'true';
        if($checkProjectCreatedVal != 0){
            $allowDeleteProjectCreatedValue = 'false';
        }
        
        return $allowDeleteProjectCreatedValue;
    }
    public function saveSettingNextBlock(){
         if( !empty($this->data) ){
            $id = $this->data['id'];
            $setting = $this->data['next_block'];
            $data = array(
                'next_block' => $setting ? $setting : 0,
                'company_id' => $this->employee_info['Company']['id'],
            );
            if( !$id ){
                $this->ProjectCreatedValue->create();
            } else {
                $this->ProjectCreatedValue->id = $id;
            }
            $this->ProjectCreatedValue->save($data);
            die('ok');
        }
        die('not allowed!');
    }

}
?>