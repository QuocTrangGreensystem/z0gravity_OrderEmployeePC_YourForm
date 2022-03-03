<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ProjectDetailEmployeeSettingsController extends AppController {

    function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index() {
		$this->redirect('/projects');
		exit;
	}
	function update(){
		$result = false;
		$data = array();
		$message = '';
		if( !empty($this->data['ProjectDetailEmployeeSetting'])){
			$this->loadModels('Translation');
			$data = $this->data['ProjectDetailEmployeeSetting'];
			$return_url = !empty($data['return']) ?  $data['return'] : '/projects';
			if( isset($data['return'])) unset($data['return']);
			$employee_id = $this->employee_info['Employee']['id'];
			$company_id = $this->employee_info['Company']['id'];
			if( !empty($data['translation_setting_id'])){
				$enables = $data['translation_setting_id'];
				unset($data['translation_setting_id']);
				foreach( $enables as $setting_id){
					$data[$setting_id]['field_display'] = 1;
				}
			}
			$setting_ids = Set::classicExtract($data, '{n}.translation_setting_id');
			$check = $this->Translation->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					// 'page' => $page,
					'TranslationSetting.company_id' => $company_id,
					'TranslationSetting.id' => $setting_ids
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
			if( !empty( $check )){
				$check = Set::classicExtract($check, '{n}.TranslationSetting.id');
				$last = $this->ProjectDetailEmployeeSetting->find('list', array(
					'recursive' => -1,
					'conditions' => array(
						'translation_setting_id' => $setting_ids,
						'employee_id' => $employee_id
					),
					'fields' => array('translation_setting_id', 'id')
				));
				foreach( $data as $k => $item){
					if( empty( $item['translation_setting_id']) ) continue;
					$setting_id = $item['translation_setting_id'];
					if( in_array($setting_id, $check)){
						if( !empty($last[$setting_id])){ // update
							$this->ProjectDetailEmployeeSetting->id = $last[$setting_id];
						}else{ // add new
							$this->ProjectDetailEmployeeSetting->create();
						}
						// Save data
						$data =  array(
							'employee_id' => $employee_id,
							'translation_setting_id' => $setting_id,
							'field_display' =>!empty($item['field_display']) ? 1 : 0, 
							// 'block_display' => 1, // default 
							'updated' => time()
						);
						if( isset($item['block_display'])){
							$data['block_display'] = (!empty($item['block_display']) ? 1 : 0);
						}
						$result = $this->ProjectDetailEmployeeSetting->save($data);
					}
				}
			}
		}
		$this->redirect( $return_url );
		exit;
	}
}