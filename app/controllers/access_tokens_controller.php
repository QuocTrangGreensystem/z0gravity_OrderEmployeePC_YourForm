<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 * path: app/controllers/access_tokens_controller.php
 */
class AccessTokensController extends AppController {
    var $name = 'AccessTokens';
    var $defaultExpires = '';
	// var $uses = array( 'AuthCode', 'AccessToken' );
	// var $helpers = array('Validation', 'Html');
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    function beforeFilter() {
        parent::beforeFilter();
		// $this->checkAccessToken();
		// $this->set('limit', $this->limit);
    }
    /**
     * index
     *
     * @return void
     * @access public
     */
    function index() {
		if( !$this->isAdminSAS) $this->redirect(array('controller' =>  'security_settings', 'action' => 'index'));
		$this->loadModels('Company');
		$company_id = $this->employee_info["Company"]["id"];
		$tokens = $this->AccessToken->find('all',array(
			'recursive' => -1,
			'conditions' => array(
				'OR' => array(
					'company_id' => $company_id,
					'company_id is NULL'	
				)
			),
            'fields' => array('*')
		));
		$listCompanies = $this->Company->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_name is not NULL',
				'id' => $company_id
            ),
            'fields' => array('id', 'company_name'),
        ));
		$this->set(compact('tokens', 'listCompanies'));
    }
	public function update(){
		$this->layout = 'ajax';
		$result = false;
		$data = array();
		if (!empty($this->data) && $this->isAdminSAS){
			$listCompanies = $this->Company->find('list', array(
				'recursive' => -1,
				'conditions' => array(
					'company_name is not NULL',
				),
				'fields' => array('id', 'company_name'),
			));
			if( !empty( $this->data['token'])){ // update
				$data = $this->AccessToken->find('first', array(
					'recursive' => -1,
					'conditions' => array( 'token' => $this->data['token']),
					'fields' => array('*')
				));
				if( empty( $data )) {
					$this->Session->setFlash( sprintf( __('%s could not be saved. Please, try again.', true), 'Token'), 'error');
					die(json_encode( array(
						'result' => false,
						'data' => array(),
						'message' => sprintf( __('%s could not be saved. Please, try again.', true), 'Token')
					)));
				}
				$data = !empty( $data) ? $data['AccessToken'] : array();
			}else{ //create
				$this->AccessToken->create();
				$data = array(
					'created' => date('Y-m-d H:i:s'),
					'expires' => null,
					'token' => $this->generateToken()
				);
			}
			if( isset($this->data['expires'])) {
				$_expires = DateTime::createFromFormat('d-m-Y', $this->data['expires']);
				$data['expires'] = $_expires ? $_expires->format('Y-m-d H:i:s') : null;
			}
			$data['update'] = date('Y-m-d H:i:s');
			// check company is exists
			$data['company_id'] = null;
			if( !empty( $this->data['company_id'])){
				if( !empty( $listCompanies[$this->data['company_id']])) $data['company_id'] = $this->data['company_id'];
			}
			$this->AccessToken->token = $data['token'];
			$saved = $this->AccessToken->save($data);
			if( $saved ){
				// debug( $saved); exit;
				$data = $this->AccessToken->find('first', array(
					'recursive' => -1,
					'conditions' => array( 'token' => $saved['AccessToken']['token']),
					'fields' => array('*')
				));
				if(!empty( $data)){
					$data = $data['AccessToken'];
					$dateFields = array('created', 'updated', 'expires');
					$date = '';
					foreach( $dateFields as $fields){
						if( !empty($data[$fields]) && ($data[$fields] != '0000-00-00 00:00:00')){
							$date = new DateTime($data[$fields]);
							$data[$fields] = $date->format('d-m-Y');
						}else{
							$data[$fields] = null;
						}
							
					}
				}
				$result = true;
				$this->Session->setFlash(__('Saved', true), 'success');
			}else{
				$this->Session->setFlash( sprintf( __('%s could not be saved. Please, try again.', true), 'Token'), 'error');
			}
		} else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
		$this->set(compact('result', 'data'));
		if( !$this->params['isAjax']){
			$this->redirect(array('action' => 'index'));
		}
	}
	public function createNewToken(){
		$this->layout = 'ajax';
		$result = false;
		$data = array();
		$this->Session->setFlash(__('Permission denied', true), 'error');
		if( $this->isAdminSAS ){
			$this->AccessToken->create();
			$saved = array(
				'created' => date('Y-m-d H:i:s'),
				'updated' => date('Y-m-d H:i:s'),
				'company_id' => null,
				'expires' => null,
				'token' => $this->generateToken()
			);
			$saved = $this->AccessToken->save($saved);
			$this->Session->setFlash(__('Not saved', true), 'error');
			if( !empty($saved)){
				$result = true;
				$data = $saved['AccessToken'];
				$dateFields = array('created', 'updated', 'expires');
				$date = '';
				foreach( $dateFields as $fields){
					if( !empty($data[$fields]) && ($data[$fields] != '0000-00-00 00:00:00')){
						$date = new DateTime($data[$fields]);
						$data[$fields] = $date->format('d-m-Y');
					}else{
						$data[$fields] = null;
					}
						
				}
				$this->Session->setFlash(__('Saved', true), 'success');
			}
		}
		$this->set(compact('result', 'data'));
		if( !$this->params['isAjax']){
			$this->redirect(array('action' => 'index'));
		}
	}
	public function deleteToken($token){
		$this->layout = 'ajax';
		$result = false;
		$data = array();
		$this->Session->setFlash(sprintf(__('The %s has not deleted', true), 'Token'), 'error');
		if( !empty($token) && $this->isAdminSAS ){
			$data = $token;
			$this->AccessToken->token = $token;
			$result = $this->AccessToken->delete();
			if($result){
				$this->Session->setFlash(sprintf(__('The %s has been deleted', true), 'Token'), 'success');
			}
		}
		$this->set(compact('result', 'data'));
		if( !$this->params['isAjax']){
			$this->redirect(array('action' => 'index'));
		}
	}
	public function generateToken() {
		$template = array('email','first_name','last_name', 'id');
		$code='';
        foreach ($template as $field) {
            $code .= isset($this->employee_info['Employee'][$field]) ? $this->employee_info['Employee'][$field] : '';
        }
        $code .= microtime();
        return md5($code);
	}
}