<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 *
 * ZNotifyExpo Component
 * PHP versions 7
 *
 * Custom email component by Z0g
 * edited by Trung Vo 19-08-2021
 */
App::import('Core', 'CakeString');
class ZNotifyExpoComponent extends Object{
	public function send_notify_login_navigation(){
		// $enable = true;//<------ Edit here....
		$enable = MAppNotifyExpo::isEnableCrawl();
		if(!$enable) return "No sending to login navigation config";

		// Huynh - Next enhancement: Services name in config file
		// $service_name = ucfirst(array_shift((explode('.', $_SERVER['HTTP_HOST']))));
		// $service_name = !empty($service_name) ? $service_name : 'Preprod1';
		$service_name = MAppNotifyExpo::getServiceName();
		$LN_url = MAppNotifyExpo::getLoginNavigationUrl();
		$LN_token = MAppNotifyExpo::getLoginNavigationToken();
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => $LN_url, //<-- Edit here...
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('access_token' => $LN_token,'service_name' => $service_name),//<-- Edit here...
		CURLOPT_HTTPHEADER => array(
			'Cookie: PHPSESSID=094577bf6ec04746a1b6ba224b46509b'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		CakeLog::write('activity', $service_name.' Call to:'.$LN_url.' Response:'.$response );
		return;
		
	}
	public function send_notify_expo($expoPushToken, $title, $body, $arr_data){
		if(empty($expoPushToken)) return "No Token to send";
		if(!MAppNotifyExpo::isEnableNotifyModule()) return 'Notify module is disable';

		$url = 'https://exp.host/--/api/v2/push/send';
		
		$postdata = array(
			'to' => array_merge(array_unique($expoPushToken)),
			'sound'=> 'default',
			'title'=> $title,
			'body'=> $body,
			'data' => $arr_data
		);
		
		// Debug($postdata);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/json",
			"Accept-Encoding: gzip, deflate",
			"Content-Type: application/json",
			"cache-control: no-cache",
			"host: exp.host",
		));
		$response = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);

		// debug( $response); 
		// debug( $err); 
		// exit;

		if($err) {
			return json_decode($err);
		} else {
			return json_decode($response);
		}
		
		
	}
	
}
