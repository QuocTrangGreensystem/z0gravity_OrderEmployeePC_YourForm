<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityColumnsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityColumns';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
        //$this->ActivityColumn->updateAll(array('weight' => null));
		$originalColumn=array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->ActivityColumn->Company->find('list');
            $this->viewPath = 'activity_columns' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
			$originalColumn=ClassRegistry::init('ActivityColumn')->getOptions();
			$activityColumns=ClassRegistry::init('ActivityColumn')->getOptions($company_id,true);
			$this->set(compact('activityColumns', 'company_id', 'companyName','originalColumn'));
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (isset($this->data['calculate']) && $this->data['calculate'] == 'false') {
            $this->data['calculate'] = '';
        }
        if (!empty($this->data) && $this->_getCompany() && (empty($this->data['calculate'])
                || (preg_match('/^[0-9+\-*\/\\.() ]+$/i', preg_replace('/C\d+/i', '1', $this->data['calculate']))
                && eval('(' . preg_replace('/C\d+/i', '1', $this->data['calculate']) . ');') !== false))) {
            $this->ActivityColumn->create();
            $this->data['key'] = $this->data['id'];
            unset($this->data['id']);
            if (!$this->is_sas) {
                $this->data['company_id'] = $this->employee_info['Company']['id'];
            }
            $last = $this->ActivityColumn->find('first', array(
                'recursive' => -1, 'fields' => array('id'),
                'conditions' => array('key' => $this->data['key'], 'company_id' => $this->data['company_id'])));
            if ($last) {
                $this->ActivityColumn->id = $last['ActivityColumn']['id'];
            }
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes')
            );
            if ($this->ActivityColumn->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('The Activity Column has been saved.', true), 'success');
            } else {
                $this->Session->setFlash(__('The Activity Column could not be saved. Please, try again.', true), 'error');
            }
            $this->data['id'] = $this->data['key'];
            unset($this->data['key'], $this->data['calculate']);
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * Export
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany($company_id)) {
            foreach ($this->data as $id => $weight) {
                $last = $this->ActivityColumn->find('first', array(
                    'recursive' => -1, 'fields' => array('id'),
                    'conditions' => array('key' => $id, 'company_id' => $company_id)));
                if ($last && !empty($weight)) {
                    $this->ActivityColumn->id = $last['ActivityColumn']['id'];
                    $this->ActivityColumn->save(array(
                        'weight' => intval($weight)), array('validate' => false, 'callbacks' => false));
                }
            }
        }
        //$this->showDebug();
        exit(0);
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Activity Column', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
		if((!$this->is_sas) && (!$this->_isBelongToCompany($id, 'ActivityColumn'))){
			$this->_functionStop(false, $id, __('You have not permission to access this function', true), false, array('action' => 'index'));
		}
        if ($this->ActivityColumn->delete($id)) {
            $this->Session->setFlash(__('Activity Column has been deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Activity Column was not deleted', true), 'error');
        }

        $this->redirect(array('action' => 'index', $company_id));
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = $this->ActivityColumn->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
	function add_value_fields() {
		if ($this->is_sas)
		{
			$this->loadModel('Company');
			$listCompany = $this->Company->find('list', array(
				'recursive' => -1,
				'fields' => array('Company.id'),
				'conditions' => array('Company.parent_id' => null)
			));
			foreach($listCompany as $val)
			{
				//get max weight
				$maxWeight = $this->ActivityColumn->find('first', array(
					'recursive' => -1,
					'fields' => array('MAX(weight) as weight'),
					'conditions' => array('ActivityColumn.company_id' => $val)
				));
				$maxWeight=$maxWeight['0']['weight'];
				$maxWeight=$maxWeight!=null?$maxWeight:0;

				$monthArray=array('Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec');
				foreach($monthArray as $value)
				{
					$id=strtolower($value);
					
					//Save fields workload
					$data = array();
					$maxWeight+=1;
					$workloadFields='workload_'.$id;
					$workloadFieldsName='Workload '.$value;
					$count = $this->ActivityColumn->find('count', array('key' => $workloadFields));
					if($count)
					{
						$data = array(
							'value_field' => 1,
							'key' => $workloadFields,
							'name' => $workloadFieldsName,
							'weight' => $maxWeight,
							'company_id' => $val,
							'description' => 'In personalized view',
							'display' => 0
						);
						$this->ActivityColumn->create();
						$this->ActivityColumn->save($data);
					}
					
					//Save fields consumed
					$data = array();
					$maxWeight+=1;
					$consumedFields='consumed_'.$id;
					$consumedFieldsName='Consumed '.$value;
					$count1 = $this->ActivityColumn->find('count', array('key' => $consumedFields));
					if($count1)
					{
						$data = array(
							'value_field' => 1,
							'key' => $consumedFields,
							'name' => $consumedFieldsName,
							'weight' => $maxWeight,
							'company_id' => $val,
							'description' => 'In personalized view',
							'display' => 0
						);
						$this->ActivityColumn->create();
						$this->ActivityColumn->save($data);
					}
				}
				
				$quaterArray=array('First','Second','Third','Fourth');
				foreach($quaterArray as $value)
				{
					$id=strtolower($value);
					
					//Save fields workload
					$data = array();
					$maxWeight+=1;
					$workloadFields='workload_'.$id;
					$workloadFieldsName='Workload '.$value.' Quater';
					$count = $this->ActivityColumn->find('count', array('key' => $workloadFields));
					if($count)
					{
						$data = array(
							'value_field' => 1,
							'key' => $workloadFields,
							'name' => $workloadFieldsName,
							'weight' => $maxWeight,
							'company_id' => $val,
							'description' => 'In personalized view',
							'display' => 0
						);
						$this->ActivityColumn->create();
						$this->ActivityColumn->save($data);
					}
					
					//Save fields consumed
					$data = array();
					$maxWeight+=1;
					$consumedFields='consumed_'.$id;
					$consumedFieldsName='Consumed '.$value.' Quater';
					$count1 = $this->ActivityColumn->find('count', array('key' => $consumedFields));
					if($count1)
					{
						$data = array(
							'value_field' => 1,
							'key' => $consumedFields,
							'name' => $consumedFieldsName,
							'weight' => $maxWeight,
							'company_id' => $val,
							'description' => 'In personalized view',
							'display' => 0
						);
						$this->ActivityColumn->create();
						$this->ActivityColumn->save($data);
					}
				}
				
			}
			exit('Completed!');
		}
		else
		{
			exit('You have not permission to access this function!');
		}
    }
}
?>