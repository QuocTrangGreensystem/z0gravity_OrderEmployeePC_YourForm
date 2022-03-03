<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectCreatedValsPreviewController extends AppController {

    var $uses = array('ProjectCreatedValue', 'ProjectCreatedVal', 'Project');

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectCreatedValsPreview';

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
		
        if (!$this->_checkRole(false, $project_id, empty($this->data) ? array('element' => 'warning') : array())) {
            if (empty($this->viewVars['projectName'])) {
                return $this->redirect(array(
                            'controller' => 'projects', 'action' => 'index'
                        ));
            }
        }
		$employee_info = $this->employee_info;
        $this->_checkWriteProfile('created_value');
		$sum_of_type = $this->updateCreatedValue($project_id);
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
        /** Created by Dai Huynh 29/05/2018 
        * table project_created_values
        * input: $projectC['ProjectCreatedVal'] : id type_value
        * output: type_value / sum('value')
        */
		
        $sumSelectedOfTypeVals = array();

		if(!empty($projectC['ProjectCreatedVal'])){
			$sumSelectedOfTypeVals = $this->ProjectCreatedValue->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $projectC['ProjectCreatedVal'],
				),
				'fields' => array(
					'type_value',
					'SUM(`value`) as `value`',
				),
				'group' => array('type_value')
			));
			$sumSelectedOfTypeVals = !empty($sumSelectedOfTypeVals) ? Set::combine($sumSelectedOfTypeVals, '{n}.ProjectCreatedValue.type_value', '{n}.0.value') : array();
		}
	
        $this->set('id', $id);
        $this->set('projectC', $projectC);
        $this->set(compact('project_id'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        $sumOfTypeVal = array();
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'value_order', 'block_name', 'next_block'),
				'order' => array('value_order'), 
			));
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] = 0;
            }
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] += $value['ProjectCreatedValue']['value'];
            }
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        } else {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'block_name', 'next_block')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        }
		// totalValue
		if(!empty($sum_of_type)){
			$sumOfTypeVal = $sum_of_type;
			$totalValue['ProjectCreatedValue']['total'] =  array_sum($sum_of_type);
		}
        $dataProjectCreatedValsComment = $this->getDataProjectCreatedValsComment($project_id);

        $employees_commentID = array();
        $employees_commentID = !empty($dataProjectCreatedValsComment) ? Set::extract($dataProjectCreatedValsComment, '{n}.ProjectCreatedValsComment.employee_id') : array();
        $employees_commentID = array_unique($employees_commentID);
        $employees_comment_info = array();
        $employees_comment_info = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('id' => $employees_commentID),
            'fields' => array('id','first_name', 'last_name','avatar_resize')
        ));
        $employees_comment_info = !empty($employees_comment_info) ? Set::combine($employees_comment_info, '{n}.Employee.id', '{n}.Employee') : '';
		$canComment = $this->_canComment($project_id);
		
        $this->set(compact('created_values','totalValue', 'sumOfTypeVal','sumSelectedOfTypeVals','dataProjectCreatedValsComment','employees_comment_info','employee_info', 'canComment'));

    }
	function updateCreatedValue($project_id){
		// Update data created value when user changed setting in admin project created
		// Updated: in a block: authorize seleted only 1 item.
		$this->loadModels('ProjectCreatedVal', 'Project');
		$projectCreatedVals = $this->ProjectCreatedVal->find('all', array('conditions' => array('ProjectCreatedVal.project_id' => $project_id)));
        $projectC = array();
		$sumOfTypeVal = array();
        $projectCnew = array();
        $projectCname = array();
        if (!empty($projectCreatedVals)) {
            foreach ($projectCreatedVals as $projectCreatedVal) {
                if ($projectCreatedVal['ProjectCreatedVal']['description'] != '0')
                    $projectC['ProjectCreatedVal'] = unserialize($projectCreatedVal['ProjectCreatedVal']['description']);
                else
                    $projectC['ProjectCreatedVal'] = "";
            }
            $id = $projectCreatedVals[0]['ProjectCreatedVal']['id'];
        }
		if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'value_order', 'block_name', 'next_block'),
				'order' => array('value_order'), 
			));
			$total_sum = 0;
			$old_val = array();
            foreach ($created_values as $key => $value) {
				foreach( array('financial','customer','business','learning') as $key){
					if($key == $value['ProjectCreatedValue']['type_value']){
					if( empty($sumOfTypeVal[$key])) $sumOfTypeVal[$key] = 0;
						$a = $key;
						$ax = $key.'x';
						if( empty ($$a)) $$a = 0;
						if( empty ($$ax)) $$ax = 0;
						
						if($value['ProjectCreatedValue']['next_block'] == 1){
							$$a = 1;
							$$ax = 0;
							$old_val[$key] = 0;
						}
						$id = $value['ProjectCreatedValue']['id'];
						$val = $value['ProjectCreatedValue']['value'];
						if( !empty($projectC['ProjectCreatedVal']) && in_array( $id, $projectC['ProjectCreatedVal']) && (!$$a || !$$ax ) ) {
							$projectCnew[] = $value['ProjectCreatedValue']['id'];
							$projectCname[$id] = $value['ProjectCreatedValue']['description'];
							$$ax = 1;
							$total_sum += $value['ProjectCreatedValue']['value'];
						}
						if( $$a == 0){
							$sumOfTypeVal[$key] +=$val;
						}
						else{ // next block 
							if($val > $old_val[$key]){
								$sumOfTypeVal[$key] += ( $val - $old_val[$key] );
								$old_val[$key] = $val;
							}
						}
					}
				}
            }
        }
		if(!empty($projectCnew)){
			$datas = array();
			$datas['description'] =  serialize($projectCnew);
			$datas['updated'] = time();
			$procrevalue = $this->ProjectCreatedVal->findByProjectId($project_id);
			if (!empty($procrevalue)) {
				$this->ProjectCreatedVal->id = $procrevalue['ProjectCreatedVal']['id'];
				$this->ProjectCreatedVal->saveField('description', serialize($projectCnew));
				// created in project.
				$this->Project->id = $project_id;
				$this->Project->saveField('created_value', $total_sum);
			}
		}
		return $sumOfTypeVal;
	}

	function ajax($project_id = null) {
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
		
		$sum_of_type = $this->updateCreatedValue($project_id);
		
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
        /** Created by Dai Huynh 29/05/2018 
        * table project_created_values
        * input: $projectC['ProjectCreatedVal'] : id type_value
        * output: type_value / sum('value')
        */

        $sumSelectedOfTypeVals = array();
        if(!empty($projectC['ProjectCreatedVal'])){
            $sumSelectedOfTypeVals = $this->ProjectCreatedValue->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $projectC['ProjectCreatedVal'],
                ),
                'fields' => array(
                    'type_value',
                    'SUM(`value`) as `value`',
                ),
                'group' => array('type_value')
            ));
            $sumSelectedOfTypeVals = !empty($sumSelectedOfTypeVals) ? Set::combine($sumSelectedOfTypeVals, '{n}.ProjectCreatedValue.type_value', '{n}.0.value') : array();
        }
        $this->set('id', $id);
        $this->set('projectC', $projectC);
        $this->set(compact('project_id'));
        if ($this->employee_info["Employee"]["is_sas"] != 1)
            $company_id = $this->employee_info["Company"]["id"];
        else
            $company_id = "";
        $this->ProjectCreatedValue->virtualFields = array('total' => 'SUM(ProjectCreatedValue.value)');
        $sumOfTypeVal = array();
        if ($company_id != "") {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'block_name', 'next_block'),
				'order' => array('value_order')));
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] = 0;
            }
            foreach ($created_values as $key => $value) {
                $sumOfTypeVal[$value['ProjectCreatedValue']['type_value']] += $value['ProjectCreatedValue']['value'];
            }
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        } else {
            $created_values = $this->ProjectCreatedValue->find("all", array(
                'fields' => array('id', 'description', 'value', 'type_value', 'language', 'block_name', 'next_block'),
				'order' => array('value_order')));
            $totalValue = $this->ProjectCreatedValue->find("first", array(
                'conditions' => array('company_id' => $company_id),
                'fields' => array('total')));
        }
		// totalValue
		if(!empty($sum_of_type)){
			$sumOfTypeVal = $sum_of_type;
			$totalValue['ProjectCreatedValue']['total'] =  array_sum($sum_of_type);
		}
        $dataProjectCreatedValsComment = $this->getDataProjectCreatedValsComment($project_id);

        $employees_commentID = array();
        $employees_commentID = !empty($dataProjectCreatedValsComment) ? Set::extract($dataProjectCreatedValsComment, '{n}.ProjectCreatedValsComment.employee_id') : array();
        $employees_commentID = array_unique($employees_commentID);
        $employees_comment_info = array();
        $employees_comment_info = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array('id' => $employees_commentID),
            'fields' => array('id','first_name', 'last_name','avatar_resize')
        ));
        $employees_comment_info = !empty($employees_comment_info) ? Set::combine($employees_comment_info, '{n}.Employee.id', '{n}.Employee') : '';
		$canComment = $this->_canComment($project_id);
        $this->set(compact('created_values','totalValue', 'sumOfTypeVal','sumSelectedOfTypeVals','dataProjectCreatedValsComment','employees_comment_info','employee_info', 'canComment'));

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
    function saveProjectCreatedValsComment(){
		$project_id = isset ( $_POST['project_id'] ) ? $_POST['project_id'] : '';
		$canComment = $this->_canComment($project_id);
		if( !$canComment ) die(0);
        $this->loadModel('ProjectCreatedValsComment');
        if(!empty($_POST)){
            $time = time();
            $data = array(
                'company_id'=> $this->employee_info["Company"]["id"],
                'employee_id' => $this->employee_info["Employee"]["id"],
                'project_id' => $_POST['project_id'],
                'type_value' => $_POST['type_value'],
                'comment' =>  $_POST['comment'],
                'created' => $time
            );
            $this->ProjectCreatedValsComment->create();
            if($this->ProjectCreatedValsComment->save($data)){
				 $dataProjectCreatedValsComment = $this->ProjectCreatedValsComment->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'project_id' => $_POST['project_id'],
						'type_value' => $_POST['type_value'],
					),
					'fields' => array(
						'employee_id', 
						'comment', 
						'created', 
						'type_value', 
					),
					'order' => array('created' => 'DESC'),
				));
               die(json_encode($dataProjectCreatedValsComment)); 
            }
        }
        die(0);
    }

    /* Function getDataProjectCreatedValsComment
    * Created by Dai Huynh 29/5/2018
    * @params $project_id
    * Return array of comment for ProjectCreatedVals
    */
    function getDataProjectCreatedValsComment($project_id = null, $is_return = 1){
        $this->loadModel('ProjectCreatedValsComment');
         if(empty($project_id)) return false;
		 $conditions = array();
		 $conditions['project_id'] = $project_id;
		  if(!empty($_POST)){
			  $conditions['type_value'] = $_POST['type'];
		  }
        $dataProjectCreatedValsComment = $this->ProjectCreatedValsComment->find('all', array(
            'recursive' => -1,
            'conditions' => $conditions,
            'fields' => array(
                'employee_id', 
                'comment', 
                'created', 
                'type_value', 
            ),
			'order' => array('created' => 'DESC'),
        ));
		
		if($is_return == 1){
			return $dataProjectCreatedValsComment;
		}else die(json_encode($dataProjectCreatedValsComment)); 
    }

}
?>
