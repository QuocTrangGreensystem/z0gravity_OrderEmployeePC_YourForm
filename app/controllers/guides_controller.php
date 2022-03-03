<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class GuidesController extends AppController {
	public $name = 'Guides';
	public $uses = null;
	public $helpers = array('Cache');
	public $cacheAction = array(
	    'index' => '365 days'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('*');
	}

	public function admin(){
		$this->parse('adminguide', 'admin_guides');
	}

	public function index(){
		$this->parse('userguide'.DS.'globalguide', 'guides/globalguide');
	}
	
	public function absence(){
		$this->parse('userguide'.DS.'absence', 'guides/absence');
	}
	public function activity(){
		$this->parse('userguide'.DS.'activity', 'guides/activity');
	}
	public function agenda(){
		$this->parse('userguide'.DS.'agenda', 'guides/agenda');
	}
	public function chat(){
		$this->parse('userguide'.DS.'chat', 'guides/chat');
	}
	public function globalguide(){
		$this->parse('userguide'.DS.'globalguide', 'guides/globalguide');
	}
	public function plandecharge(){
		$this->parse('userguide'.DS.'plandecharge', 'guides/plandecharge');
	}
	public function prise_en_main_z0gravity(){
		$this->parse('userguide'.DS.'prise_en_main_z0gravity', 'guides/prise_en_main_z0gravity');
	}
	public function staffing(){
		$this->parse('userguide'.DS.'staffing', 'guides/staffing');
	}
	public function tache_non_continue(){
		$this->parse('userguide'.DS.'tache_non_continue', 'guides/tache_non_continue');
	}

	private function parse($folder, $base){
		if( @$this->params['url']['url'] == $base )$this->redirect('/' . $base . '/');
		$this->layout = 'ajax';
		$file = $this->params['pass'];
		if( empty($file) )$file = array('index.htm');
		$name = implode(DS, $file);
		$path = USERFILES . $folder . DS . $name;
		if( file_exists($path) ){
			$info = pathinfo($name);
			$content = file_get_contents($path);
			header('Cache-Control: max-age=2592000');
			$url = Router::url('/' . $base . '/', true);
			switch ($info['extension']) {
				case 'htm':
				case 'html':
					header('Content-type: text/html');
					//$content = preg_replace('!href="(.+)"!', 'href="/guides/$1"', $content);
					$content = preg_replace_callback('!(src|href)="([^"]+)"!', function($matches){
						global $url;
						if( Validation::url($matches[2]) || preg_match('!^mailto\:!i', $matches[2]) )return $matches[0];
						return $matches[1] . '="' . $url . $matches[2] . '"';
					}, $content);
					// $content = preg_replace('!src="(.+)"!', 'src="/guides/$1"', $content);
					break;
				case 'gif':
				case 'png':
				case 'bmp':
					header('Content-type: image/'.$info['extension']);
					break;
				case 'jpe':
				case 'jpg':
				case 'jpeg':
					header('Content-type: image/jpeg');
					break;
				case 'js':
					header('Content-type: application/javascript');
					break;
				case 'css':
					header('Content-type: text/css');
					// $content = preg_replace('!href="(.+)"!', 'href="/guides/$1"', $content);
					// $content = preg_replace('!src="(.+)"!', 'src="/guides/$1"', $content);
					break;
				default:
					header('Content-type: text/plain');
					break;
			}
			$this->set('content', $content);
			$this->render('index');
		} else {
			die;
		}
	}
}