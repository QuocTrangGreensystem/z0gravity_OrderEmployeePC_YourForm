<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class HistoryFiltersController extends AppController {
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
			$settings = $this->HistoryFilter->find('first', array(
				'conditions' => array(
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id']
				)
			));
			if( empty($settings) ){
				$settings = $this->HistoryFilter->save(array(
					'id' => null,
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'params' => '{}'
				));
				$settings = '{}';
			}
			else {
				$settings = $settings['HistoryFilter']['params'];
				if( $settings == '[]' )$settings = '{}';
			}
		}
		die($settings);
	}
	public function saveSettings(){
		if( !empty($this->data) ){
			$data = $this->HistoryFilter->getDatasource()->value(json_encode($this->data['store'], JSON_FORCE_OBJECT), 'string');
			$settings = $this->HistoryFilter->find('first', array(
				'conditions' => array(
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id']
				)
			));
			if( empty($settings) ){
				$settings = $this->HistoryFilter->save(array(
					'id' => null,
					'path' => $this->data['path'],
					'employee_id' => $this->employee_info['Employee']['id'],
					'params' => '{}'
				));
			}
			$this->HistoryFilter->updateAll(array(
				'params' => $data
			), array(
				'path' => $this->data['path'],
				'employee_id' => $this->employee_info['Employee']['id']
			));
		}
		$this->getSettings();
	}
}
