<?php
App::import('Vendor', 'ZAuth.Tokenzr', array(
    'file' => 'tokenzr.php'
));

class ZAuthComponent extends Object {

    public $controller;

	public $authenticate = array();
    protected $_authDefaults = array(
        'userModel' => 'Employee',
		'fields' => array('username' => 'email', 'password' => 'password'),
		'authCode' => 'email.first_name.last_name',
        'expires' => '+1 month',
		'login_action' => array(
			'controller' => 'employees',
			'action' => 'login'
		)
    );

    public $accessToken, $User, $authCode;

    protected $_validators = array();

    protected $_user;
    protected $allowedActions = array('login', 'customLogin');

    public function initialize(&$controller) {
        $this->controller = $controller;
        $this->_methods = $controller->methods;
        $this->AccessToken = ClassRegistry::init(array('class' => 'ZAuth.AccessToken', 'alias' => 'AccessToken'));
        $this->AuthCode = ClassRegistry::init(array('class' => 'ZAuth.AuthCode', 'alias' => 'AuthCode'));
		if( ($this->controller == $this->_authDefaults['login_action']['controller']) && ($this->_methods == $this->_authDefaults['login_action']['action']) && ( isset( $this->data['id'])) ){
			$this->authenticate['fields']['id'] =  $this->data['id'];
		}
		ini_set('memory_limit', '512M');
        set_time_limit(0);
        $methods = array_flip(array_map('strtolower', $controller->methods));
        $action = strtolower($controller->params['action']);

        $this->authenticate = array_merge($this->_authDefaults, $this->authenticate);
        $this->User = ClassRegistry::init(array(
            'class' => $this->authenticate['userModel'],
            'alias' => $this->authenticate['userModel']
        ));

        // check access token
        $token = @$controller->params['url']['access_token'];
		if( !empty($_POST['access_token'] )) $token = $_POST['access_token'];

        if( !empty($token) ){
            $this->accessToken = $this->getAccessToken($token);
        }

        if( !$this->accessToken ){
            $this->invalidateAccess();
        }
        $allowedActions = $this->allowedActions;
        $isAllowed = (
            $this->allowedActions == array('*') ||
            in_array($action, array_map('strtolower', $allowedActions))
        );
        if ( !$isAllowed ) {
            $code = @$controller->params['url']['auth_code'];
			if( !empty($_POST['auth_code'] )) $code = $_POST['auth_code'];
            if( !empty($code) ){
                $this->authCode = $this->getAuthCode($code);
            }

            if( !$this->authCode ){
                // die
                $this->invalidateAccess();
            }
			
			/* check IP */
			$code_ip = $this->AuthCode->find('first', array(
                'conditions' => array('AuthCode.code' => $this->authCode),
                'recursive' => -1,
				'fields' => ('ip')
            ));
			$code_ip = $code_ip['AuthCode']['ip']; 
			if( !empty($code_ip)) {
				if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
					if( !in_array( $code_ip, explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ){
						$this->respond('ip_denied', null, 'ip_denied', 0);
					}
				}else if($code_ip != $_SERVER['REMOTE_ADDR'] ){
					$this->respond('ip_denied', null, 'ip_denied', 0);
				}
			}
		}
    }

    public function startup(Controller $controller) {
        // debug($_POST);
        
    }

    public function addValidator($key, $callable){
        $this->_validators[$key][] = $callable;
    }

    protected function executeValidators($key){
        if( !isset($this->_validators[$key]) )return;
        $params = func_get_args();
        array_shift($params);
        foreach ($this->_validators[$key] as $callable) {
            call_user_func_array($callable, $params);
        }
    }

    public function user($field = null){
        $model = $this->authenticate['userModel'];

        if (!$this->_user) {
            $this->AuthCode->bindModel(array(
                'belongsTo' => array(
                    $model => array(
                        'className' => $model,
                        'foreignKey' => 'user_id'
                        )
                    )
                )
            );
            $code = $this->authCode['code'];
            $data = $this->AuthCode->find('first', array(
                'conditions' => array('AuthCode.code' => $code),
                'recursive' => 0
            ));

            if (!$data) {
                return false;
            }
            $this->_user = $data[$model];
        }
        
        if (empty($field)) {
            return $this->_user;
        } elseif (isset($this->_user[$field])) {
            return $this->_user[$field];
        } else if( $field == 'auth_code' ){
            return $code;
        }
        return false;
    }

    public function getAccessToken($token){
        $data = $this->AccessToken->find('first', array(
            'conditions' => array(
                'token' => $token,
                'OR' => array(
                    'expires IS NULL',
                    'expires' => '',
                    'expires >' => date('Y-m-d H:i:s')
                )
            )
        ));
        return !empty($data) ? $data['AccessToken'] : false;
    }

    public function setAccessToken($data){
        $this->AccessToken->create();
        return $this->AccessToken->save($data);
    }

    public function generateAccessToken(){
        return Tokenzr::generate(10);
    }

    public function getAuthCode($code){
        $data = $this->AuthCode->find('first', array(
            'conditions' => array(
                'code' => $code,
                'OR' => array(
                    'expires IS NULL',
                    'expires' => '',
                    'expires >' => date('Y-m-d H:i:s')
                )
            )
        ));
        return !empty($data) ? $data['AuthCode'] : false;
    }

    public function setAuthCode($data){
        $this->AuthCode->create();
        return $this->AuthCode->save($data);
    }

    public function generateAuthCode($user){
        $template = explode('.', $this->authenticate['authCode']);
        $code = $user['id'];
        foreach ($template as $field) {
            $code .= isset($user[$field]) ? $user[$field] : '';
        }
        $code .= microtime();
        return md5($code);
    }

    public function authorize($user){
        $code = $this->generateAuthCode($user);
        $this->authCode = array(
            'code' => $code,
            'company_id' => $user['company_id'],
            'user_id' => $user['id'],
            'expires' => empty($this->authenticate['expires']) ? null : date('Y-m-d H:i:s', strtotime($this->authenticate['expires']))
        );
		// debug( empty($this->authenticate['expires']) ? '' : date('Y-m-d H:i:s', strtotime($this->authenticate['expires']))); exit;
        $this->setAuthCode($this->authCode);
    }

    public function unauthorize(){
        if( !empty($this->authCode) ){
            $this->AuthCode->delete($this->authCode['code']);
        }
    }

    public function respond($response, $data = array(), $message = '', $error_code=''){
        if (ob_get_length()) ob_end_clean();
        header('Content-type: application/json');
		$respon = array(
            'status' => $response,
            'data' => $data,
            'message' => $message
        );
		if( !empty($error_code) ){
			$respon['error_code'] = $error_code;
		}
        // $array= array_map('utf8_encode', $respon);
        // debug(json_encode($array));
        // // debug($respon);
        // exit;

        die(json_encode($respon));
    }

    public function respond_multi($response, $sum = array(), $data = array(), $message = '', $error_code=''){
        ob_clean();
        header('Content-type: application/json');
		$respon = array(
            'status' => $response,
			'sum' => $sum,
            'data' => $data,
            'message' => $message
        );
		if( !empty($error_code) ){
			$respon['error_code'] = $error_code;
		}
        die(json_encode($respon));
    }

    public function invalidateAccess(){
        $this->respond('403_forbidden', null, 'access denied', 0);
    }

    public function allow($action = null) {
        $args = func_get_args();
        if (empty($args) || $action === null) {
            $this->allowedActions = $this->_methods;
        } else {
            if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
            }
            $this->allowedActions = array_merge($this->allowedActions, $args);
        }
    }

    public function deny($action = null) {
        $args = func_get_args();
        if (empty($args) || $action === null) {
            $this->allowedActions = array();
        } else {
            if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
            }
            foreach ($args as $arg) {
                $i = array_search($arg, $this->allowedActions);
                if (is_int($i)) {
                    unset($this->allowedActions[$i]);
                }
            }
            $this->allowedActions = array_values($this->allowedActions);
        }
    }

}
