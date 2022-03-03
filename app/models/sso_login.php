<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class SsoLogin extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
//    public $useTable = true;
    var $name = 'SsoLogin';

    /**
     * List of validation rules.
     *
     * @var array
     */
    var $belongTo  = array(
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'dependent' => true
        )
    );
	public function getSsoSetting($company_id = null){
		$spBaseUrl =  Router::url('/', true);
		$idpSettting = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array('company_id' => $company_id),
			'fields' => array('id', 'company_id', 'company_id', 'issuer_url', 'saml_end_point', 'slo_end_point', 'certificate')
		));
		$idp = array();
		if( !empty( $idpSettting)) {
			$idpSettting = $idpSettting['SsoLogin'];
			$idp = array(
				'entityId' => $idpSettting['issuer_url'],
				'singleSignOnService' => array (
					'url' => $idpSettting['saml_end_point'],
				),
				'singleLogoutService' => array (
					'url' => $idpSettting['slo_end_point'],
				),
				'x509cert' => $idpSettting['certificate']
			);
		}
		$settingsInfo = array (
			'sp' => array (
				// Neu edit loginURL, can edit button SSO o man hinh login
				'loginURL' => $spBaseUrl.'sso_logins/login/' . $company_id . '?sso',
				'logoutURL' => $spBaseUrl.'sso_logins/logout',
				'entityId' => $spBaseUrl.'sso_logins/metadata/' . $company_id,
				'assertionConsumerService' => array (
					'url' => $spBaseUrl.'sso_logins/login/' . $company_id . '?acs',
				),
				'singleLogoutService' => array (
					'url' => $spBaseUrl.'sso_logins/login/' . $company_id . '?sls',
				),
				'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
				'asc_url_validator' => '.*',
				'RelayState' => '',
				'RelayState' => Router::url('/', true),
			),
			'idp' => $idp,
		);
		return $settingsInfo;
	}
}