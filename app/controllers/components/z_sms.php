<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 *
 * ZSms Component
 * PHP versions 4 and 5
 *
 * Custom email component by Z0g
 * edited by Huynh Le 03-08-2021
 */
App::import('Core', 'String');
class ZSmsComponent extends Object{
	var $service = 'default';
	var $current_data = 'default';
	var $configs = array();
	function initialize(&$controller, $settings = array()) {
		// $this->Controller =& $controller;
		if( !empty( $settings)) $this->updateConfig($settings);
		// debug($this->configs);
	}
	function startup(&$controller) {}
	
	function reset() {
		$this->service = 'default';
		$this->current_data = $this->default;
	}
	public function updateConfig( $settings = array()){
		if( !empty( $settings)){
			// debug($settings);
			// $settings = Set::sort($settings, '{n}.priority', 'asc');
			$this->configs = $settings;
			// debug($settings);
			// debug($this->configs);
			return true;
		}
		return false;
	}
	protected function check_rule($number = '', $rule = array()){
		if( is_string( $rule)){
			if(  ($rule == '*') || (preg_match($rule, $number, $matches )) ) {
				return true;
			}
			return false;
		}elseif( is_array($rule)){
			foreach($rule as $r){
				if( $this->check_rule( $number, $r )){
					return true;
				}
			}
		}
		return false;
	}
	public function choose_service( $number = ''){
		$settings = $this->configs;
		foreach( $this->configs as $name => $config){
			$rule = isset( $config['rule'] ) ? $config['rule'] : '*';
			if( $this->check_rule($number, $rule)){
				$this->service = $name;
				$this->current_data =  $config;
				return true;
			}
		}
		$this->current_data = $this->configs['default'];
		return true;
	}
	public function set_data( $arr = array()){
		// debug( $arr);
		$data = $this->current_data;
		if( empty( $data)) return false;
		foreach( $arr as $k => $v){
			if( isset( $data['params'][$k]) ){
				$data['data'][ $data['params'][$k]] = $v;
			}
		}
		$this->current_data = $data;
		return $data['data'];
	}
	public function send(){
		$data = $this->current_data;
		if( empty( $data)) return false;
		$str = '';
		foreach($data['data'] as $k => $v){
			if( empty( $str)) $str = '?'; else $str .= '&';
			$str .= $k . '=' . urlencode($v);
		}
		$uri = $data['base_url'] . $str;
		$curl = curl_init($uri); 
		foreach( $data['options'] as $k => $v){
			curl_setopt($curl, $k, $v); 
		}
		curl_setopt($curl, CURLOPT_HTTPGET , true); 
		$result = curl_exec($curl); 
		$obj = json_decode($result,true);
		curl_close($curl);
		return $obj;
	}
	public function multiSend($phones){
		// review htt ps://ww w.php .net/manual/en/function.curl-multi-init.php
	}
	public function send_esms(){
		$APIKey="DCBF39C6472623AD8A578DB2C059D9";
		$SecretKey="251CAACE80ACBE42F9E96832F76A25";
		$YourPhone="0707746612";
		$Content='2356 la ma xac minh dang ky Baotrixemay cua ban';
		$SendContent=urlencode($Content);
		$data = $this->current_data;
		$content = $data['data']['text'];
		debug( $content); exit;
		$url="https://api.smsfactor.com/send?to=%2B84979045612&text=conga&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIyMTEyNCIsImlhdCI6MTU1NjAxMDEyNX0.mvbtwke3ji2UZ_npySJ-LTepr5NEud9BIdtBT68RgXQ";
		//De dang ky brandname rieng vui long lien he hotline 0901.888.484 hoac nhan vien kinh Doanh cua ban
		$curl = curl_init($url); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
		$result = curl_exec($curl); 
			
		$obj = json_decode($result,true);
		debug($obj); exit;
	}
	public function send_sms_factor_post(){
		$data = $this->current_data['data'];
		$url = 'https://api.smsfactor.com/send';
		$token = $data['token']; // https://secure.smsfactor.com/token.html;
		$content = "Test API";
		$numbers = array('84979045612');
		$sender = "Z0GAPP";
		foreach ($numbers as $n) {
		  $recipients[] = array('value' => $n);
		}


		$postdata = array(
		  'sms' => array(
		   'message' => array(
			'text' => $content,
			'sender' => $sender
		   ),
		   'recipients' => array('gsm' => $recipients)
		  )
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.smsfactor.com/send");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $token));
		$response = curl_exec($ch);
		debug( $response); exit;
		
	}
	
}
