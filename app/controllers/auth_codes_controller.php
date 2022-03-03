<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AuthCodesController extends AppController {
    var $name = 'AuthCodes';
    var $expires;
	var $accessToken;
	var $uses = array( 'AuthCode', 'AccessToken' );
	var $helpers = array('Validation', 'Html');
	var $limit = 1;
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    function beforeFilter() {
        parent::beforeFilter();
		$this->checkAccessToken();
		$this->set('limit', $this->limit);
    }
    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
		$this->loadModels('Employee');
		$auth_codes = $this->AuthCode->find('all',array(
			'recursive' => -1,
            'conditions' => array(
				'company_id' => $this->employee_info['Company']['id'],
				'user_id' => $this->employee_info['Employee']['id'],
			),
            'fields' => array('*')
		));
		
		//Employee create auth_code
		$listEm = $this->Employee->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $this->employee_info['Company']['id']
            ),
            'fields' => array('id', 'fullname', 'profit_center_id')
        ));
		$listEmIdOfPc = $listEm ? Set::combine($listEm, '{n}.Employee.id', '{n}.Employee.profit_center_id') : array();
		$listIdEm = array_keys($listEmIdOfPc);
		$list_avatar = $this->requestAction('employees/get_list_avatar/', array('pass' => array($listIdEm)));
		$this->set(compact('auth_codes', 'list_avatar'));
    }
	private function checkAccessToken(){
		$accessToken = $this->AccessToken->find('all', array(
			'recursive' => -1,
            'conditions' => array(
				'OR' => array(
                    'expires IS NULL',
                    'expires' => '',
                    'expires >' => date('Y-m-d H:i:s')
                ),
				'OR' => array(
					'company_id' => $this->employee_info['Company']['id'],
					'company_id is null'
				),
			),
            'fields' => array('*')
		));
		if( empty( $accessToken ) ) {
			if( $this->params['isAjax'] ){
				$result = array(
					'result' => '403_forbidden',
					'message' => 'Access Denined. Please contact admin to get new access Token',
				);
				die(json_encode($result));
			}else{
				$this->redirect(array('controller' =>  'administrators', 'action' => 'index'));
				// $this->render('e403');
			}
		}
		foreach( $accessToken as $key => $val){
			if( $val['AccessToken']['expires'] == '' ) $this->accessToken = $val['AccessToken']['token'];
			break;
		}
		$accessTokens = Set::sort($accessToken, '{n}.AccessToken.expires','desc');	
		if( empty( $this->accessToken ) ) {
			$this->accessToken = $accessToken[0]['AccessToken']['token'];
		}
		$this->set('accessToken', $this->accessToken);
		$this->set('accessTokens', $accessTokens);
	}
	private function getAuthCode($auth_code, $strictly = true){
		$cond = array(
			'company_id' => $this->employee_info['Company']['id'],
			'code' => $auth_code,
		);
		if( $strictly){
			$cond['OR'] = array(
				'expires IS NULL',
				'expires' => '',
				'expires >' => date('Y-m-d H:i:s')
			);
		}
		$code = $this->AuthCode->find('first', array(
			'recursive' => -1,
            'conditions' => $cond,
            'fields' => array('*')
		));
		if( !empty($code) ) return $code['AuthCode'];
		return false;
	}
	public function generateAuthCode() {
		$template = array('email','first_name','last_name', 'id');
		$code='';
        foreach ($template as $field) {
            $code .= isset($this->employee_info['Employee'][$field]) ? $this->employee_info['Employee'][$field] : '';
        }
        $code .= microtime();
        return md5($code);
	}
	
	public function deleteAuthCode($auth_code){
		$result = array(
			'result' => false,
			'code' => $auth_code['code'],
		);
		$data = $this->getAuthCode($auth_code, false);
		$this->Session->setFlash(__('Not Saved', true), 'success');
		if( !empty($data)){
			if( $this->employee_info['Employee']['id'] != $data['user_id'] || $this->employee_info['Employee']['is_sas']){
				$this->AuthCode->deleteAll(array('code' => $auth_code), false, false);
				$result = array(
					'result' => 'deleted',
					'code' => $auth_code['code'],
				);
				$this->Session->setFlash(__('Deleted', true), 'success');
			}
		}
		if( $this->params['isAjax']) {	
			die(json_encode($result));
		}else{
			$this->redirect(array('action' => 'index'));
		}
		
	}
	public function updateAuthCode(){
		if (!empty($this->data) ){
			$data = $this->getAuthCode($this->data['code'], false);
			// debug( $data); exit;
			if( empty($data) ){
				$result = array(
					'data' => array(
						'result' => 'false',
						'message' => __('Authentication Code not found', true),
					)
				);
				die(json_encode($result));
			}
			foreach( array('name','expires', 'description', 'ip') as $key){
				$data[$key] = isset( $this->data[$key]) ? $this->data[$key] : null;
			}
			if( $data['expires']) {
				$_expires = new DateTime($data['expires']);
				$data['expires'] = $_expires->format('Y-m-d H:i:s');
			}else{
				$data['expires'] = null;
			}
			$data['update'] = date('Y-m-d H:i:s');
			$data['user_id'] = $this->employee_info['Employee']['id'];
			$this->AuthCode->code = $this->data['code'];
			$result = $this->AuthCode->save($data);
			$result = array(
				'data' => array(
					'result' => 'success',
					'data' => $result,
					'message' => __('Saved', true),
				)
			);
			
			die(json_encode($result));
		}
		else{ // Create New
			$auth_codes = $this->AuthCode->find('all',array(
				'recursive' => -1,
				'conditions' => array(
					'OR' => array(
						'expires IS NULL',
						'expires' => '',
						'expires >' => date('Y-m-d H:i:s')
					),
					'company_id' => $this->employee_info['Company']['id'],
					'user_id' => $this->employee_info['Employee']['id'],
				),
				'fields' => array('*')
			));
			if( count ($auth_codes) >= $this->limit ){
				$this->redirect(array('action' => 'index'));
			}
			$data = array(
				'code' => $this->generateAuthCode(),
				'name' => null,
				'user_id' => $this->employee_info['Employee']['id'],
				'expires' => null,
				'company_id' => $this->employee_info['Company']['id'],
				'created' => date('Y-m-d H:i:s'),
				'update' => date('Y-m-d H:i:s'),
				'description' => __('New Authentication Code', true),
				'ip' => null,
			);
			$this->AuthCode->create();
			$result = $this->AuthCode->save($data);
			if( $result){
				$result = array(
					'result' => 'success',
					'data' => $result,
					'message' => __('Authentication Code has generated', true),
				);
				$this->Session->setFlash(__('Authentication Code has generated', true), 'success');
			}else{
				$result = array(
					'result' => 'failed',
					'data' => $result,
					'message' => __('Not saved', true),
				);
				$this->Session->setFlash(__('Not saved', true), 'error');
			}
			$this->redirect(array('action' => 'index'));
			die(json_encode($result));
		}
	}
	public function cleanAuthCode(){
		$auth_codes = $this->AuthCode->find('all',array(
			'recursive' => -1,
            'conditions' => array(
				'OR' => array(
					'expires' => '0000-00-00 00:00:00'
				),
			),
            'fields' => array('*')
		));
		foreach( $auth_codes as $auth_code){
			$this->AuthCode->deleteAll(array('code' => $auth_code['AuthCode']['code']), false, false);
		}
		$all_employees = $this->Employee->find('list', array(
			'fields' => array('id')
		));
		$auth_codes = $this->AuthCode->find('all',array(
			'recursive' => -1,
            'conditions' => array(
				'NOT'=> array('user_id' => $all_employees)
			),
            'fields' => array('*')
		));
		foreach( $auth_codes as $auth_code){
			$this->AuthCode->deleteAll(array('code' => $auth_code['AuthCode']['code']), false, false);
		}
		$this->Session->setFlash(__('Clean', true), 'success');
		$this->redirect(array('action' => 'index'));
		
	}

}