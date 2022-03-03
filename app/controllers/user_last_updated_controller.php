<?php
/**
 * z0 Gravity™
 * Copyright 2018 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class UserLastUpdatedController extends AppController {
	public function beforeFilter(){
		parent::beforeFilter();
		if ($this->RequestHandler->requestedWith('json')) {
            if (function_exists('json_decode')) {
                $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);
            }

            if (!is_null($jsonData) and $jsonData != false) {
                $this->data = $jsonData;
            }
        }
	}
	public function getSettings(){
		$settings = '{}';
		if( !empty($this->data) ){
			$settings = $this->UserLastUpdated->find('first', array(
				'conditions' => array(
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'company_id' => $this->employee_info['Company']['id'],
				)
			));
			if( empty($settings) ){
				$settings = $this->UserLastUpdated->save(array(
					'id' => null,
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'company_id' => $this->employee_info['Company']['id'],
					'action' => '{}'
				));
				$settings = '{}';
			}
			else {
				$settings = $settings['UserLastUpdated']['action'];
				if( $settings == '[]' )$settings = '{}';
			}
		}
		die($settings);
	}
	public function saveSettings(){
		if( !empty($this->data) ){
			$data = $this->UserLastUpdated->getDatasource()->value(json_encode($this->data['store'], JSON_FORCE_OBJECT), 'string');
			$settings = $this->UserLastUpdated->find('first', array(
				'conditions' => array(
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'company_id' => $this->employee_info['Company']['id'],
				)
			));
			if( empty($settings) ){
				$settings = $this->UserLastUpdated->save(array(
					'id' => null,
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'company_id' => $this->employee_info['Company']['id'],
					'action' => '{}'
				));
			}
			$this->UserLastUpdated->updateAll(array(
				'action' => $data
			), array(
				'path' => $this->data['path'],
				'employee_id' => $this->employee_info['Employee']['id'],
				'company_id' => $this->employee_info['Company']['id'],
			));
		}
		$this->getSettings();
	}
}
