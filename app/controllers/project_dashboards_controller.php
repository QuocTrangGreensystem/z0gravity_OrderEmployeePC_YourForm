<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class ProjectDashboardsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'ProjectDashboards';

	public function beforeFilter(){
		parent::beforeFilter();
		$this->canShare = !empty( $this->companyConfigs['share_a_dashboard']);
		$this->set('canShareDashboard', $this->canShare);
	}
	
    /**
     * index
     * @params $type 
     * @return void
     * @access public
     */
    function index() {
		$this->redirect('/projects');
		exit;
	}
	function get_dashboard_by_id($dashboard_id = null, $return = true) {
		$data = $this->ProjectDashboard->get_dashboard_by_id($dashboard_id);
		if( $return) return $data;
		die(json_encode(array(
			'data' => $data,
		)));
	}
	function update_acti_dashboard($dashboard_id = null) {
		$result = false;
		$data = array();
		$message = '';
		$this->loadModels('ProjectDashboardActive');
		$active = $this->ProjectDashboardActive->find('first', array(
			'recursive' => -1,
			'conditions' => array('employee_id' => $this->employee_info['Employee']['id']),
			'fields' => array('employee_id', 'dashboard_id')
		));
		if( !empty( $active )){
			$result = $this->ProjectDashboardActive->updateAll(
				array('dashboard_id' => $dashboard_id), // set
				array('employee_id' => $this->employee_info['Employee']['id']) // where
			);
		}else{
			$this->ProjectDashboardActive->create();
			$result = $this->ProjectDashboardActive->save(array(
				'employee_id' => $this->employee_info['Employee']['id'],
				'dashboard_id' => $dashboard_id
			));
		}
		die( json_encode( array(
			'result' => $result ? 'success' : 'failed',
			'data' => $result ? $dashboard_id : '',
			'message' => $message,
		)));
	}
	function delete_dashboard($dashboard_id = null) {
		$result = false;
		$data = array();
		$message = '';
		$this->loadModels('ProjectDashboardShare');
		$result = $this->ProjectDashboard->deleteAll(array(
			'ProjectDashboard.id' => $dashboard_id,
			'ProjectDashboard.employee_id' => $this->employee_info['Employee']['id']
		), true);
		if( $result){
			$this->ProjectDashboardShare->deleteAll(array(
				'ProjectDashboardShare.dashboard_id' => $dashboard_id,
			), false);
		}
		die( json_encode( array(
			'result' => $result ? 'success' : 'failed',
			'data' => $data,
			'message' => $message,
		)));
	}
	function update_dashboard() {
		$result = false;
		$data = array();
		$message = '';
		$this->loadModels('ProjectDashboardShare');
		if( !empty($this->data['ProjectDashboard'])){
			$this->data = array_merge( $this->data, $this->data['ProjectDashboard']);
			unset( $this->data['ProjectDashboard']);
		}
		if( !empty($this->data['share_resource'])){
			$this->data['share_resource'] = array_unique(array_filter($this->data['share_resource']));
		}
		if( !empty( $this->data)){
			if( isset($this->data['dashboard_data']) ) $this->data['dashboard_data'] = serialize($this->data['dashboard_data']);
			$this->data['employee_id'] = $this->employee_info['Employee']['id'];
			$this->data['company_id'] = $this->employee_info['Company']['id'];
			if( !empty($this->data['id'])){
				// check employee
				// $this->ProjectDashboard->recursive = -1;
				$this->ProjectDashboard->id = $this->data['id'];
				$item = $this->ProjectDashboard->read();
				if($item['ProjectDashboard']['employee_id'] == 
				$this->employee_info['Employee']['id']){
					$result = $this->ProjectDashboard->save($this->data);
				}
			}else{
				$this->ProjectDashboard->create();
				$result = $this->ProjectDashboard->save($this->data);
			}
			if( $result){
				$dashboard_id = $this->ProjectDashboard->id;
				if( ('resource' == @$this->data['share_type']) && isset($this->data['share_resource'])){
					$old_shared = $this->ProjectDashboardShare->find('list', array(
						'recursive' => -1,
						'conditions' => array('dashboard_id' => $dashboard_id),
						'fields' => array('employee_id', 'id')
					));
					if( is_array($this->data['share_resource'])){
						foreach($this->data['share_resource'] as $e_id){
							if( !empty($old_shared[$e_id])){
								unset($old_shared[$e_id]);
							}else{
								$this->ProjectDashboardShare->create();
								$item = $this->ProjectDashboardShare->save(array(
									'dashboard_id' => $dashboard_id,
									'employee_id' => $e_id,
									'updated' => time()
								));
							}
						}
					}
					if( !empty( $old_shared)){
						$this->ProjectDashboardShare->deleteAll(array(
							'ProjectDashboardShare.id' => array_values($old_shared),
						), false);
					}
				}
				$data = $this->get_dashboard_by_id($dashboard_id, true);
				if( !empty($data['ProjectDashboardShare'])){
					$data['ProjectDashboard']['share_resource'] = array();
					foreach( $data['ProjectDashboardShare'] as $v){
						$data['ProjectDashboard']['share_resource'][] = $v['employee_id'];
					}
					
				}
			}
		}
		die( json_encode( array(
			'result' => $result ? 'success' : 'failed',
			'data' => !empty($data['ProjectDashboard']) ? $data['ProjectDashboard'] : array(),
			'message' => $message,
		)));
	}
}