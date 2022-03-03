<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ActivityExportsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ActivityExports';
    //var $layout = 'administrators'; 

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');
    
     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null) {
        $this->loadModel('Company');
        $modifyScreen = 'NO';
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            $activityExports = $this->ActivityExport->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'order' => array('weight' => 'ASC')
            ));
            $activityExports = !empty($activityExports) ? Set::combine($activityExports, '{n}.ActivityExport.id', '{n}.ActivityExport') : array();
            $moreField = array(
				0 => array(
					'name' => 'Profit center id',
					'english' => 'ID of the team',
					'france' => 'ID of the team',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 26
				),
				1 => array(
					'name' => 'Project name',
					'english' => 'Project name',
					'france' => 'Project name',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 27
				),
				2 => array(
					'name' => 'Project program',
					'english' => 'Program',
					'france' => 'Program',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 28
				),
				3 => array(
					'name' => 'Phase name',
					'english' => 'Phase name',
					'france' => 'Phase name',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 29
				),
				4 => array(
					'name' => 'Task name',
					'english' => 'Task name',
					'france' => 'Task name',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 30
				),
				5 => array(
					'name' => 'Project code 1',
					'english' => 'Project code',
					'france' => 'Project code',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 31
				),
				6 => array(
					'name' => 'Tjm',
					'english' => 'TJM of resource',
					'france' => 'TJM of resource',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 32
				),
				7 => array(
					'name' => 'Message',
					'english' => 'Text of the day',
					'france' => 'Text of the day',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 33
				),
				8 => array(
					'name' => 'Week Message',
					'english' => 'Text of the timesheet',
					'france' => 'Text of the timesheet',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 34
				),
				9 => array(
					'name' => 'Project ID',
					'english' => 'Project ID',
					'france' => 'Project ID',
					'company_id' => $company_id,
					'display' => 0,
					'weight' => 35
				)
			);
			
			$activityExportDefaults = array(
				0 => array(
					'name' => 'First name',
					'english' => 'First name',
					'france' => 'First name',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 1
				),
				1 => array(
					'name' => 'Last name',
					'english' => 'Last name',
					'france' => 'Last name',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 2
				),
				2 => array(
					'name' => 'Profit Center',
					'english' => 'Profit Center',
					'france' => 'Profit Center',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 3
				),
				3 => array(
					'name' => 'ID1',
					'english' => 'ID1',
					'france' => 'ID1',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 4
				),
				4 => array(
					'name' => 'ID2',
					'english' => 'ID2',
					'france' => 'ID2',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 5
				),
				5 => array(
					'name' => 'ID3',
					'english' => 'ID3',
					'france' => 'ID3',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 6
				),
				6 => array(
					'name' => 'ID4',
					'english' => 'ID4',
					'france' => 'ID4',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 7
				),
				7 => array(
					'name' => 'ID5',
					'english' => 'ID5',
					'france' => 'ID5',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 8
				),
				8 => array(
					'name' => 'ID6',
					'english' => 'ID6',
					'france' => 'ID6',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 9
				),
				9 => array(
					'name' => 'Family',
					'english' => 'Family',
					'france' => 'Family',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 10
				),
				10 => array(
					'name' => 'Sub family',
					'english' => 'Sub family',
					'france' => 'Sub family',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 11
				),
				11 => array(
					'name' => 'Code 1',
					'english' => 'Code 1',
					'france' => 'Code 1',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 12
				),
				12 => array(
					'name' => 'Code 2',
					'english' => 'Code 2',
					'france' => 'Code 2',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 13
				),
				13 => array(
					'name' => 'Code 3',
					'english' => 'Code 3',
					'france' => 'Code 3',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 14
				),
				14 => array(
					'name' => 'Code 4',
					'english' => 'Code 4',
					'france' => 'Code 4',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 15
				),
				15 => array(
					'name' => 'Code 5',
					'english' => 'Code 5',
					'france' => 'Code 5',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 16
				),
				16 => array(
					'name' => 'Code 6',
					'english' => 'Code 6',
					'france' => 'Code 6',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 17
				),
				17 => array(
					'name' => 'REF 1',
					'english' => 'REF 1',
					'france' => 'REF 1',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 18
				),
				18 => array(
					'name' => 'REF 2',
					'english' => 'REF 2',
					'france' => 'REF 2',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 19
				),
				19 => array(
					'name' => 'REF 3',
					'english' => 'REF 3',
					'france' => 'REF 3',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 20
				),
				20 => array(
					'name' => 'REF 4',
					'english' => 'REF 4',
					'france' => 'REF 4',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 21
				),
				21 => array(
					'name' => 'Quantity',
					'english' => 'Quantity',
					'france' => 'Quantity',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 22
				),
				22 => array(
					'name' => 'Date activity/absence',
					'english' => 'Date activity/absence',
					'france' => 'Date activity/absence',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 23
				),
				23 => array(
					'name' => 'Validation date',
					'english' => 'Validation date',
					'france' => 'Validation date',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 24
				),
				24 => array(
					'name' => 'Extraction date',
					'english' => 'Extraction date',
					'france' => 'Extraction date',
					'company_id' => $company_id,
					'display' => 1,
					'weight' => 25
				)
			);
			
			$activityExportDefaults = array_merge($activityExportDefaults, $moreField);
			// ob_clean();debug($activityExportDefaults);exit;
			if(!empty($activityExports) && (sizeof($activityExports) == sizeof($activityExportDefaults))){
                $i = 1;
                foreach($activityExports as $key => $activityExport){
                    $this->ActivityExport->id = $activityExport['id'];
                    $this->ActivityExport->save(array('weight' => $i));
                    $activityExports[$key]['weight'] = $i;
                    $i++;
                }
            } else {
				if(!empty($activityExports)){
					 $this->ActivityExport->deleteAll(array('company_id' => $company_id));
				}
                $this->ActivityExport->create();
                $this->ActivityExport->saveAll($activityExportDefaults);
                $activityExports = $this->ActivityExport->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id
                    ),
                    'order' => array('weight' => 'ASC')
                ));
                $activityExports = !empty($activityExports) ? Set::combine($activityExports, '{n}.ActivityExport.id', '{n}.ActivityExport') : array();
            }
			$this->set(compact('company_id', 'companyName', 'activityExports'));
        }   
    }
    
    /**
     * Order
     *
     * @return void
     * @access public
     */
    public function order($company_id = null) {
        if (!empty($this->data) && $this->_getCompany($id)) {
            foreach ($this->data as $id => $weight) {
                if (!empty($id) && !empty($weight) && $weight!=0) {
                    $this->ActivityExport->id = $id;
                    $this->ActivityExport->saveField('weight', $weight);
                }
            }
        }
        die;
    }
    
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $companyName = $this->_getCompany();
            $company_id = $companyName['Company']['id'];
            $data = array(
                'display' => (isset($this->data['display']) && $this->data['display'] == 'yes')
            );
            $this->ActivityExport->create();
            if (!empty($this->data['id'])) {
                $this->ActivityExport->id = $this->data['id'];
            }
            unset($this->data['id']);
            if ($this->ActivityExport->save(array_merge($this->data, $data))) {
                $result = true;
                $this->Session->setFlash(__('OK.', true), 'success');
            } else {
                $this->Session->setFlash(__('KO.', true), 'error');
            }
            $this->data['id'] = $this->ActivityExport->id;
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
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
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
}
?>