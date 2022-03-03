<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class TranslationEntry extends AppModel {
	public $name = 'TranslationEntry';
	public $belongsTo = array(
		'Translation' => array(
			'className' => 'Translation',
			'foreignKey' => 'translation_id'
		)
	);

	public function saveText($translationId, $companyId, $langCode, $langText){
		$data = $this->find('first', array(
			'conditions' => array(
				'translation_id' => $translationId,
				'company_id' => $companyId,
				'code' => $langCode
			),
			'recursive' => -1
		));
		//update
		if( !empty($data) ){
			$this->id = $data['TranslationEntry']['id'];
			$this->save(array(
				'text' => $langText
			));
		} else {
			//insert
			$this->create();
			$this->save(array(
				'translation_id' => $translationId,
				'company_id' => $companyId,
				'code' => $langCode,
				'text' => $langText
			));
		}
		$this->id = null;
	}
}