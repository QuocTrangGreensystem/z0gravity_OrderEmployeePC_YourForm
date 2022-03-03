<?php 
/*
 * https://developers.onelogin.com/saml/php
 */
class SsoLoginsController extends AppController {
	var $uses = array('SsoLogin', 'Employee', 'CompanyConfig');
	var $name = 'SsoLogins';
	function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('metadata', 'login', 'clear_sso_log', 'trace');
		App::import('Vendor', 'saml2');
	}
	public function clear_sso_log(){
		if( file_exists(SHARED. 'sso.log')) unlink(SHARED. 'sso.log');
		if( file_exists(SHARED. 'debug.log')) unlink(SHARED. 'debug.log');
		if( file_exists(SHARED. 'error.log')) unlink(SHARED. 'error.log');
		echo 'OK';
		exit;
	}
	private function getSsoSetting($company_id = null){
		if( empty( $company_id)){
			$company_id = $this->Session->read('company_id');
			$settingsInfo = $this->Session->read('settingsInfo');
			if( !empty( $settingsInfo )) return $settingsInfo;
		}else{
			$this->Session->write('company_id', $company_id);
		}
		$settingsInfo = $this->SsoLogin->getSsoSetting($company_id);
		$this->Session->write('settingsInfo', $settingsInfo);
		return $settingsInfo;
	}
	private function buildUserSession($auth = null) {
		$company_id = $this->Session->read('company_id');
		$email = $this->Session->read('samlNameId');
		if( empty($company_id) || empty($email )){
			$this->Session->setFlash( __('Empty email or company_id', true), 'default', array(), 'auth');
		}
		$this->loadModels('CompanyEmployeeReference', 'ApiKey', 'HistoryFilter', 'Color', 'ProjectEmployeeProfitFunctionRefer');
		$employee_all_info = $this->CompanyEmployeeReference->find("first", array(
			"conditions" => array(
				"Employee.email" => $email,
				"Employee.company_id" => $company_id
			)	
		));
		
		if( empty( $employee_all_info )){
			$this->Session->setFlash( sprintf( __('Email does not exist', true), $email), 'default', array(), 'auth');
			$this->redirect('/login');
		}
		$employee_all_info["Employee"]["is_sas"] = 0;
		//find pc
		$myPc = $this->ProjectEmployeeProfitFunctionRefer->find('first', array(
			'conditions' => array('employee_id' => $employee_all_info["Employee"]['id'])
		));
		$employee_all_info['Employee']['profit_center_id'] = $myPc['ProjectEmployeeProfitFunctionRefer']['profit_center_id'];
		$myColor = $this->Color->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
			),
		));
		$myColor['Color']['is_new_design'] = 0;
		$employee_all_info['Color'] = $myColor['Color'];
		$this->Session->write('Auth.employee_info', $employee_all_info);
		$this->Session->write('Auth.Employee', $employee_all_info['Employee']);
		// debug( $employee_all_info); exit;
		//set api key to use in global/local view
		$this->ApiKey->generate($this->Session);
		$this->loadModel('HistoryFilter');
		$check = $this->HistoryFilter->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'path' => 'rollback_url_employee_when_login',
				'employee_id' => $employee_all_info['Employee']['id']
			)
		));
		$url = '/';
		if(!empty($check) &&  $check['HistoryFilter']['params'] != '/employees/get_company_role'){
			$url = $check['HistoryFilter']['params'];
		} elseif (!empty($this->params['url']['continue'])) {
			$url = $this->params['url']['continue'];
		}
		$this->writeLog($employee_all_info['Employee'], $employee_all_info, 'Login by SSO');
		$this->redirect($url);
	}
	function index($company_id = null) {
		$this->redirect( array( 'action' => 'sso_information'));
	}
	function logout(){
		$company_id = $this->employee_info['Company']['id'];
		$settingsInfo = $this->getSsoSetting($company_id);
		if( empty($settingsInfo['idp'])){
			$this->Session->setFlash( __('Your company does not support Login by SSO', true), 'default', array(), 'auth');
			$this->redirect('/logout');
		}
		$auth = new OneLogin_Saml2_Auth($settingsInfo);
		$returnTo = Router::url('/', true). 'logout';  
		$parameters = array();
		$nameId = null;
		$sessionIndex = null;
		$nameIdFormat = null;
		$nameIdNameQualifier = null;
		$nameIdSPNameQualifier = null;				   
		if (isset($_SESSION['samlNameId'])) {
			$nameId = $_SESSION['samlNameId'];
		}
		if (isset($_SESSION['samlNameIdFormat'])) {
			$nameIdFormat = $_SESSION['samlNameIdFormat'];
		}
		if (isset($_SESSION['samlNameIdNameQualifier'])) {
			$nameIdNameQualifier = $_SESSION['samlNameIdNameQualifier'];
		}
		if (isset($_SESSION['samlNameIdSPNameQualifier'])) {
			$nameIdSPNameQualifier = $_SESSION['samlNameIdSPNameQualifier'];
		}
		if (isset($_SESSION['samlSessionIndex'])) {
			$sessionIndex = $_SESSION['samlSessionIndex'];
		}
		$auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);
	}
	function login($company_id = null) {
		$settingsInfo = $this->getSsoSetting($company_id); 
		if( empty($settingsInfo['idp'])){
			$this->Session->setFlash( __('Your company does not support Login by SSO', true), 'default', array(), 'auth');
			$this->redirect('/login');
		}
		$auth = new OneLogin_Saml2_Auth($settingsInfo);
		// Login 
		if (isset($_GET['sso'])) { 
			$returnTo = Router::url($this->here, true);
			$auth->login($returnTo);
			
		// Logout
		} else if (isset($_GET['slo'])) { 
			$returnTo = Router::url('/', true). 'logout';  
			$parameters = array();
			$nameId = null;
			$sessionIndex = null;
			$nameIdFormat = null;
			$nameIdNameQualifier = null;
			$nameIdSPNameQualifier = null;				   
			if (isset($_SESSION['samlNameId'])) {
				$nameId = $_SESSION['samlNameId'];
			}
			if (isset($_SESSION['samlNameIdFormat'])) {
				$nameIdFormat = $_SESSION['samlNameIdFormat'];
			}
			if (isset($_SESSION['samlNameIdNameQualifier'])) {
				$nameIdNameQualifier = $_SESSION['samlNameIdNameQualifier'];
			}
			if (isset($_SESSION['samlNameIdSPNameQualifier'])) {
				$nameIdSPNameQualifier = $_SESSION['samlNameIdSPNameQualifier'];
			}
			if (isset($_SESSION['samlSessionIndex'])) {
				$sessionIndex = $_SESSION['samlSessionIndex'];
			}
			$auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);
		} else if (isset($_GET['acs'])) {
			$is_testing = $this->Session->read('Z0gTestXamlConnection');
			// thinh thoang is_testing khong hoat dong			
			if( !empty( $is_testing ) || (!empty( $_POST['RelayState']) && (Router::url('/sso_logins/trace', true) == $_POST['RelayState']))){
				$this->trace_acs();
				$this->action = 'trace_acs';
			}else{
				if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
					$requestID = $_SESSION['AuthNRequestID'];
				} else {
					$requestID = null;
				}
				$auth->processResponse($requestID);
				$errors = $auth->getErrors();
				if (!empty($errors)) {
					echo '<p>',implode(', ', $errors),'</p>';
				}
				if (!$auth->isAuthenticated()) {
					$this->Session->setFlash( __('Not authenticated', true), 'default', array(), 'auth');
					$this->redirect('/login');
				}
				$this->Session->write('samlUserdata',$auth->getAttributes());
				$this->Session->write('samlNameId',$auth->getNameId());
				$this->Session->write('samlNameIdFormat', $auth->getNameIdFormat());
				$this->Session->write('samlNameIdNameQualifier', $auth->getNameIdNameQualifier());
				$this->Session->write('samlNameIdSPNameQualifier', $auth->getNameIdSPNameQualifier());
				$this->Session->write('samlSessionIndex', $auth->getSessionIndex());
				unset($_SESSION['AuthNRequestID']);
				$this->buildUserSession($auth);
				if (isset($_POST['RelayState']) && OneLogin_Saml2_Utils::getSelfURL() != $_POST['RelayState']) {
					$auth->redirectTo($_POST['RelayState']);
				}
			}
		}else if (isset($_GET['sls'])) {
			if (isset($_SESSION) && isset($_SESSION['LogoutRequestID'])) {
				$requestID = $_SESSION['LogoutRequestID'];
			} else {
				$requestID = null;
			}
			$auth->processSLO(false, $requestID);
			$errors = $auth->getErrors();
			if (empty($errors)) {
				if( !empty($this->employee_info)) {
					$this->writeLog($this->employee_info['Employee'], $this->employee_info, 'SSO Logout');
					$this->Session->setFlash( sprintf( __('Sucessfully logged out', true), $email), 'default', array(), 'auth');
				}
				$this->redirect('/login');
			} else {
				$this->Session->setFlash( implode(', ', $errors), $email, 'default', array(), 'auth');
			}
		}
	}
	function sso_config(){
		if( empty($this->companyConfigs['enable_sso'])) $this->cakeError('404');
		$company_id = $this->employee_info['Company']['id'];
		$sso_fields = array('id', 'company_id', 'company_id', 'issuer_url', 'saml_end_point', 'slo_end_point', 'certificate');
		$sso_config = $this->SsoLogin->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
			),
			'fields' => $sso_fields
		));
		$sso_config = !empty($sso_config) ? $sso_config['SsoLogin'] : array();
		if( !empty( $this->data['SsoLogin'])){
			$data = array();
			foreach($this->data['SsoLogin'] as $key => $val){
				$data[$key] = htmlentities($val);
			}
			$data['company_id'] = $company_id;
			$data['created'] = !empty($sso_config['created']) ? $sso_config['created'] : time();
			$data['updated'] = time();
			if( empty($sso_config)){
				$this->SsoLogin->create();
			}else{
				$this->SsoLogin->id = $sso_config['id'];
			}
			$saved = $this->SsoLogin->save($data);
			if( $saved) $this->Session->setFlash(__('Saved', true), 'success');
			else $this->Session->setFlash(__('Not saved', true), 'error');
			$sso_config = $this->SsoLogin->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'company_id' => $company_id,
				),
				'fields' => $sso_fields
			));
			$sso_config = !empty($sso_config) ? $sso_config['SsoLogin'] : array();
		}
		$this->data['SsoLogin'] = $sso_config;
		$settingsInfo = $this->SsoLogin->getSsoSetting($company_id);
		$this->set(compact('settingsInfo'));
	}
	
	function sso_information() {
		$company_id = $this->employee_info['Company']['id'];
		$settingsInfo = $this->SsoLogin->getSsoSetting($company_id);
		$this->data = array(
			'SsoInfo' => $settingsInfo['sp']
		);
	}
	public function metadata($company_id=null){
		$this->autoRender = false;
		$settingsInfo = $this->getSsoSetting($company_id);
		$this->getSsoSetting();
		try {
			#$auth = new OneLogin_Saml2_Auth($settingsInfo);
			#$settings = $auth->getSettings();
			// Now we only validate SP settings
			$settings = new OneLogin_Saml2_Settings($settingsInfo, true);
			$metadata = $settings->getSPMetadata();
			$errors = $settings->validateMetadata($metadata);
			if (empty($errors)) {
				header('Content-Type: text/xml');
				if( isset($_GET['download']) ){
					header('Content-Disposition: attachment; filename="metadata.xml"');
				}
				echo $metadata;
			} else {
				throw new OneLogin_Saml2_Error(
					'Invalid SP metadata: '.implode(', ', $errors),
					OneLogin_Saml2_Error::METADATA_SP_INVALID
				);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		exit;
	}
	
	public function trace() {
		App::import('Vendor', 'saml2_trace');
		$this->layout = 'iframe';
		if( !empty( $this->params['url']['data'])){
			extract($this->params['url']['data']);
		}
		if( !empty( $this->data)){
			extract($this->data);
		}
		$data = array();
		$message = array();
		$result = false;
		$this->Session->write('Z0gTestXamlConnection', 'yes');
		if( !empty($login)){
			$message[] = 'Test connect to SSO';
			if( empty($IssuerUrl) || empty($SamlEndPoint) || empty($SloEndPoint) || empty($Certificate) ){
				$message[] = 'Wrong input</br>';
			}else{
				// $company_id = $this->employee_info['Company']['id'];
				$settingsInfo = $this->getSsoSetting($company_id);
				// $company_id = $this->employee_info['Company']['id'];
				$settingsInfo['idp'] = array(
					'entityId' => $IssuerUrl,
					'singleSignOnService' => array(
						'url' => $SamlEndPoint
					),
					'singleLogoutService'=> array(
						'url' => $SloEndPoint
					),
					'x509cert' => $Certificate
				);
				$auth = new OneLogin_Saml2_TAuth($settingsInfo);
				$returnTo = Router::url($this->here, true);
				$ssoBuiltUrl = $auth->login($returnTo, array('acs' => $this->here), false, false, true);
				if( $ssoBuiltUrl) $result = true;
				$data['ssoBuiltUrl'] = $ssoBuiltUrl;
				$this->set(compact('IssuerUrl', 'SamlEndPoint', 'SloEndPoint', 'Certificate', 'ssoBuiltUrl'));
			}
		}
		$this->set(compact('company_id', 'settingsInfo', 'result'));
	}
	public function trace_acs(){
		App::import('Vendor', 'saml2_trace');
		$this->layout = 'iframe';
		$settingsInfo = $this->getSsoSetting();
		$message = array();
		$attributes  = array();
		/* END Use for trace2 function */
		if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
			$requestID = $_SESSION['AuthNRequestID'];
		} else {
			$requestID = null;
		}
		$auth = new OneLogin_Saml2_TAuth($settingsInfo);
		$auth->processResponse($requestID);
		
		/* Use for trace function */
		$errors = $auth->getErrors();
		$error = array();
		if( !empty($errors)){
			$error[] = array(
				'class' => 'error',
				'msg' => implode( '<br>', $errors)
			);
		}
		$message = array_merge($message, $error, $auth->get_log());
		if (!$auth->isAuthenticated()) {
			$message[] = array(
				'class' => 'error result',
				'msg' => __('Not authenticated', true)
			);
		}else{
			$attributes = $auth->getAttributesWithFriendlyName();
			$samlNameId = $auth->getNameId();
			$message[] = array(
				'class' => 'success result',
				'msg' => sprintf( __('Sucessfully logged in by <i>%s</i>', true), $samlNameId)
			);
			$getNameIdFormat = $auth->getNameIdFormat();
			$message[] = array(
				'class' => 'result',
				'msg' => sprintf( __('Name Id Format: %s', true), $getNameIdFormat)
			);
			if( !empty($attributes)){
				foreach( $attributes as $k => $attribute){
					$va = is_array( $attribute) ? $attribute[0] : $attribute;
					$message[] = array(
						'class' => 'result',
						'msg' => sprintf( __('%s: %s', true), $k, $attribute[0])
					);
				}
			}
		}
		$this->Session->delete('Z0gTestXamlConnection');
		$this->set(compact('message', 'attributes'));
	}
	
	public function upload() {
		// App::import('Vendor', 'xml_to_array');
		$company = $this->employee_info['Company']['id'];
		$result = false;
		$xml = false;
		$this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
		if (!empty($_FILES) || !empty($this->data['Upload']['url'])) {
			try{
				if (!empty($_FILES)) {
					$xml = simplexml_load_file($_FILES['file']['tmp_name']);
				}else{
					$xml = simplexml_load_file($this->data['Upload']['url']);
				}
			}catch( Exception $e){}			
		}
		
		if(!empty( $xml)){
			// $xmltext = $xml->asXML();
			if( $xml->getName() == 'EntitiesDescriptor'){
				//Working
				$result = true;
			}
		}
		$this->set(compact('result'));
	}
	public function test_email($email = '') {
		$this->loadModels('CompanyEmployeeReference');
		$company_id = $this->employee_info['Company']['id'];
		$result = false;
		$message = __('Employee does not exist in this company', true);
		$employee_all_info = $this->CompanyEmployeeReference->find("first", array(
			"conditions" => array(
				"Employee.email" => $email,
				"Employee.company_id" => $company_id
			)	
		));
		if( empty( $employee_all_info )){
			$result = true;
			$message = __('Employees exist in this company', true);
		}
		die( json_encode( array(
			'result' => $result ? 'success' : 'failed',
			'message' => $message,
		)));
	}
}