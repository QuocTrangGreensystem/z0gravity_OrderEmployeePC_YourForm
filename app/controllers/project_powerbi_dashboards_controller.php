<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectPowerbiDashboardsController extends AppController {
	
	/**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $name = 'ProjectPowerbiDashboards';
	
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload');
    public $allowedFiles = "jpg,jpeg,bmp,gif,png,txt,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm,msg";
    function beforeFilter() {
        parent::beforeFilter();
    }
	
	/**
     * Index
     *
     * @return void
     * @access public
     */
    function index($project_id = null) {
        $this->_checkRole(false, $project_id);
        $powerbiDashboards = $this->ProjectPowerbiDashboard->find("first", array(
            'recursive' => -1,
            "conditions" => array('project_id' => $project_id),
			'fields' => array('id', 'project_id', 'iframe'),
		));	
		
        $this->set(compact('powerbiDashboards', 'project_id'));
	}
	
	 /**
     * view
     *
     * @return void
     * @access public
     */
    public function upload($project_id = null) {
		$this->_checkRole(false, $project_id);
		if(empty($this->data['ProjectPowerbiDashboard']['iframe'])){
			$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
		}else{
			$powerbiDashboards = $this->ProjectPowerbiDashboard->find("first", array(
				'recursive' => -1,
				"conditions" => array('project_id' => $project_id),
				'fields' => array('id'),
			));	
			$data = array();
			if(!empty($powerbiDashboards)){
				$this->ProjectPowerbiDashboard->id = $powerbiDashboards['ProjectPowerbiDashboard']['id'];
				$data = array(
					'iframe' => $this->data['ProjectPowerbiDashboard']['iframe'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'updated' => time(),
				);
			}else{
				$this->ProjectPowerbiDashboard->create();
				$data = array(
					'project_id' => $project_id,
					'iframe' => $this->data['ProjectPowerbiDashboard']['iframe'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'created' => time(),
					'updated' => time(),
				);
			}
			$this->ProjectPowerbiDashboard->save($data);

		}
		$this->redirect(array('action' => 'index', $project_id));
	}
	
	 /**
     * view
     *
     * @return void
     * @access public
     */
    public function delete($id = null) {
		$success = 0;
		$this->ProjectPowerbiDashboard->id = $id;
		$last = $this->ProjectPowerbiDashboard->read();
		$project_id = @$last['ProjectPowerbiDashboard']['project_id'];
		$check = $this->_isBelongToCompany($project_id, 'Project');
		if ($this->_checkRole(false, $project_id) && $check && $this->ProjectPowerbiDashboard->delete($id)) {
			$success = 1;
		}
		die(json_encode($success));
	}
}