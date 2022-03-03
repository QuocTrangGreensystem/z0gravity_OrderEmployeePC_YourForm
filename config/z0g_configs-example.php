<?php
    class GMapAPISetting {
        /**
         * Config for Google MAP API
         * 
         */
		private static $gapis = array(
			'AIzaSyA0KkqSSeKk3wOgkDZQcf9Vy_SiNOqFluc', // API 1
			'AIzaSyD5IpwnYBiBrmtKgPFHmc0CIwGqRybHp-E'  // API 2
				// API ...
		);
		
		/*
		* get Google map API for web
		* @params $index
		* call GMapAPISetting::getGAPI()
		*/
		public static function getGAPI($index = 'rand'){
			$gapis = self::$gapis;
			if( !empty( $gapis[$index])) return $gapis[$index];
			return $gapis[rand(0, count($gapis)-1)];
		}
    }
	
	class MAppNotifyExpo {
		 /**
         * Config for Google MAP API
         * 
         */
		private static $enable_notify_module = true;//false / true;
		private static $enable_crawl = true;//false / true;

        private static $service_name = '';
        private static $login_navigation_url = 'https://preprod39.z0gravity.com/login_navigation/services/auto_crawl';
        private static $login_navigation_token = 'crawler-token';
		
		// get getServiceName
		public static function getServiceName(){
			if(empty( self::$service_name )){
				$service_name = ucfirst(array_shift((explode('.', $_SERVER['HTTP_HOST']))));  
				$service_name = !empty($service_name) ? $service_name : 'No_Name';
				return $service_name;
			}
			return self::$service_name;
		}
		
		// check is enable
		public static function isEnableCrawl(){
			return !empty(self::$enable_crawl);
		}
		// check is enable module?
		public static function isEnableNotifyModule(){
			return !empty(self::$enable_notify_module);
		}

		public static function getLoginNavigationUrl(){
			return self::$login_navigation_url;
		}
		public static function getLoginNavigationToken(){
			return self::$login_navigation_token;
		}
	}
	
	// toDEV: Do not echo php close tag 