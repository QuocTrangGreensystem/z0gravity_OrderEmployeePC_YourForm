<?php

class StrHelper extends AppHelper {

	private $str;

	public function __construct() {
        parent::__construct();
        App::import('vendor', 'str_utility');
        $this->str = new str_utility();
    }

    public function __call($name, $arguments){
    	return call_user_method_array($name, $this->str, $arguments);
    }

}