<?php 
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class LocaleComponent extends Object {
    /**
     * 
     * @var $name
     */
    var $name = 'LocaleComponent';
    var $params = array();
    var $errorCode = null;
    var $errorMessage = null;
    var $overwrite = false;
    var $controller = null;
    /**
     * Contructor function
     * @param Object &$controller pointer to calling controller
     */
    function startup(&$controller) {
    	$this->controller = & $controller;
    	$this->Session = & $controller->Session;
        $this->Cookie = & $controller->Cookie;
    	$this->configs = & $controller->configs;

        //keep tabs on mr. controller's params
        $this->params = & $this->controller->params;
    }
    
    function process($available_langs = array('en', 'fr')) {
    	if (isset($this->params['lang'])) {
			$this->params['named']['lang'] = $this->params['lang'];
		}
		
		// Xử lý phần ngôn ngữ
		$this->controller->languages = Configure::read('Config.languages');
		if (isset($this->params['named']['lang'])) {
		  // delete old cookie language if it is exist
            $langCode = $this->params['named']['lang'];
            //debug($langCode);
		} else if ($this->Session->check('language')) {
			$sess_lang = $this->Session->read('language');
           // debug($sess_lang);
			$langCode = $sess_lang['langCode'];  
            //debug($langCode);          
		}else if($this->Cookie->read('language')) {
		    $cookie_lang = $this->Cookie->read('language');  
			// b.2 Nếu trong session chưa có thông tin về ngôn ngữ thì lấy ngôn ngữ từ cookie
			$langCode = $cookie_lang;
            //debug($langCode);
		}else {
		    // b.3 Nếu trong session chưa có thông tin về ngôn ngữ thì lấy ngôn ngữ mặc định
			$langCode = Configure::read('Config.defaultLanguage');
		}
        //debug($langCode);
		$language = $this->controller->languages[$langCode];
		// Lưu thông tin ngôn ngữ vào Session
		$this->Session->write('language', array(
			'langCode'	=>	$langCode,
			'language'	=>	$language,
		));
		
		// Nạp ngôn ngữ vào config
		Configure::write('Config.language',  $language);
		Configure::write('Config.langCode',  $langCode);
		
		// Xóa ngôn ngữ trong Session & Cookie
    //    $this->Cookie->delete('language');
		$this->Session->delete('Config.language'); 
		
		$this->controller->langCode = $langCode;
		$this->controller->lang = $language;
		$this->controller->default_lang = Configure::read('Config.defaultLanguage');

	    // change languages
		if (isset($this->params['named']['lang']) && in_array($this->params['named']['lang'], $available_langs)) {
			$this->Session->write('Config.language', $this->params['named']['lang']);
            $this->Cookie->delete('language');
            $this->Cookie->write('language', $this->params['named']['lang']);
		}
		
		// Set to Controller
		
    }
}
?>