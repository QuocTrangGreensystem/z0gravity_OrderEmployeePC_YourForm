<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class Translation extends AppModel {
	public $name = 'Translation';
	public $hasMany = array(
		'TranslationEntry' => array(
			'className' => 'TranslationEntry',
			'foreignKey' => 'translation_id'
		),
		'TranslationSetting' => array(
			'className' => 'TranslationSetting',
			'foreignKey' => 'translation_id'
		)
	);
	public function has($page, $text){
		$data = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'page' => $page,
				'original_text' => $text
			)
		));
		return empty($data) ? false : $data;
	}
}