<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class SaleLeadsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'SaleLeads';
    //var $layout = 'administrators';

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation');

    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'PImage');

    function beforeFilter() {
        parent::beforeFilter('getPersonalizedViews', 'getPersonalizedViewDeals', 'update_contact', 'update_sale_lead_price', 'update_production', 'update_production_popup', 'update_production_expenses_popup', 'get_data_production_popup', 'update_data_invoice', 'delete_data_invoices', 'delete_data_expenses', 'update_data_expenses', 'get_data_production_expense_popup', 'update_data_log', 'update_avatar', 'get_customer');
        $this->Auth->deny();
        $this->Auth->autoRedirect = false;
    }

    /**
     *  Get Currency Of Business
     */
    private function _getCurrencyBusiness(){
        $saleBusiness = ClassRegistry::init('SaleSetting')->find('first', array(
            'recursive' => -1,
            'conditions' => array('type' => 10, 'company_id' => $this->Session->read('Auth.employee_info.Company.id')),
            'fields' => array('name')
        ));
        return !empty($saleBusiness) && $saleBusiness['SaleSetting']['name'] ? $saleBusiness['SaleSetting']['name'] : '';
    }
    /**
     * List Category of Sales System
     */
    var $categoryOfSales = array('customer_status', 'customer_industry', 'customer_payment', 'customer_country', 'lead_status', 'lead_maturite', 'lead_phase', 'lead_product', 'lead_billing_period', 'lead_type_of_expense', 'currency');

    /**
     *  Lay danh sach cac Personalized Views Cua Deal Status
     */
    public function getPersonalizedViewDeals($dealStatus = 1){
        $this->layout = false;
        $this->loadModel('UserView');
        $userViews = array();
        if(!empty($dealStatus)){
            $conditions = array();
            switch($dealStatus){
                case 2: {
                    $conditions = array(
                        'UserStatusViewSaleDeal.archived' => 1,
                        'UserStatusViewSaleDeal.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 3: {
                    $conditions = array(
                        'UserStatusViewSaleDeal.renewal' => 1,
                        'UserStatusViewSaleDeal.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 4: {
                    $conditions = array(
                        'OR' => array(
                            'UserStatusViewSaleDeal.open' => 1,
                            'UserStatusViewSaleDeal.archived' => 1,
                            'UserStatusViewSaleDeal.renewal' => 1
                        ),
                        'UserStatusViewSaleDeal.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 1:
                default: {
                    $conditions = array(
                        'UserStatusViewSaleDeal.open' => 1,
                        'UserStatusViewSaleDeal.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
            }
            if ($this->is_sas)
                $userViews = $this->UserView->find('list', array(
                    'recursive' => -1,
                    'fields' => array('UserView.id', 'UserView.name'),
					'conditions'=>array('model' => 'deal')));
            else {
                if(!empty($conditions)){
                    $userViews = $this->UserView->find('list', array(
                        'recursive' => 0,
                        'fields' => array('UserView.id', 'UserView.name'),
                        'order' => 'UserView.public ASC',
                        'group' => 'UserView.id',
                        'conditions' => array(
							'UserView.model' => 'deal',
                            'OR' => array(
                                'UserView.employee_id' => $this->employee_info['Employee']['id'],
                                array(
                                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                                    'UserView.public' => true
                                )
                            ),
                            $conditions
                        )
                    ));
                }
            }
        }
        echo json_encode($userViews);
        exit;
    }

    /**
     *  Lay danh sach cac Personalized Views Cua Lead Status
     */
    public function getPersonalizedViews($leadStatus = 1){
        $this->layout = false;
        $this->loadModel('UserView');
        $userViews = array();
        if(!empty($leadStatus)){
            $conditions = array();
            switch($leadStatus){
                case 2: {
                    $conditions = array(
                        'UserStatusViewSale.closed_won' => 1,
                        'UserStatusViewSale.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 3: {
                    $conditions = array(
                        'UserStatusViewSale.closed_lose' => 1,
                        'UserStatusViewSale.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 4: {
                    $conditions = array(
                        'OR' => array(
                            'UserStatusViewSale.open' => 1,
                            'UserStatusViewSale.closed_won' => 1,
                            'UserStatusViewSale.closed_lose' => 1
                        ),
                        'UserStatusViewSale.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
                case 1:
                default: {
                    $conditions = array(
                        'UserStatusViewSale.open' => 1,
                        'UserStatusViewSale.employee_id' => $this->employee_info['Employee']['id']
                    ); break;
                }
            }
            if ($this->is_sas)
                $userViews = $this->UserView->find('list', array(
                    'recursive' => -1,
                    'fields' => array('UserView.id', 'UserView.name'),
					'conditions'=>array('model' => 'business')));
            else {
                if(!empty($conditions)){
                    $userViews = $this->UserView->find('list', array(
                        'recursive' => 0,
                        'fields' => array('UserView.id', 'UserView.name'),
                        'order' => 'UserView.public ASC',
                        'group' => 'UserView.id',
                        'conditions' => array(
							'UserView.model' => 'business',
                            'OR' => array(
                                'UserView.employee_id' => $this->employee_info['Employee']['id'],
                                array(
                                    'UserView.company_id' => $this->employee_info["Company"]["id"],
                                    'UserView.public' => true
                                )
                            ),
                            $conditions
                        )
                    ));
                }
            }
        }
        echo json_encode($userViews);
        exit;
    }

     /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null, $viewId = null) {
        $this->loadModel('Company');
        $this->loadModel('SaleSetting');
        $this->loadModel('UserView');
        $this->loadModel('UserDefaultView');
        $this->loadModel('SaleCustomer');
        $this->loadModel('SaleCustomerContact');
        $this->loadModel('SaleSetting');
        $this->loadModel('SaleLeadEmployeeRefer');
        $this->loadModel('SaleRole');
        $this->loadModel('SaleLeadLog');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $roles = $this->employee_info['Role']['name'];
        $saleCurrency = $this->_getCurrencyBusiness();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
            /**
             * Lay cac salesman of Lead
             */
            $leadEmployRefers = $this->SaleLeadEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => 0
                ),
                'fields' => array('employee_id', 'is_backup', 'sale_lead_id'),
                //'group' => array('sale_lead_id', 'employee_id')
            ));
            /**
             * Lay cac salesman of Lead
             */
            $saleLeadEmployeeRefers = $allSalemans = $salemansOfLeads = array();
            if(!empty($leadEmployRefers)){
                foreach($leadEmployRefers as $leadEmployRefer){
                    $dx = $leadEmployRefer['SaleLeadEmployeeRefer'];
                    $saleLeadEmployeeRefers[$dx['sale_lead_id']][$dx['employee_id']] = $dx['is_backup'];
                    $allSalemans[$dx['employee_id']] = $dx['employee_id'];
                    $salemansOfLeads[$dx['sale_lead_id']][$dx['employee_id']] = $dx['employee_id'];
                }
            }
            if($read == false && !in_array($employeeLoginId, $allSalemans)){
                $fieldset = array(
                    'Sale.sale_customer_id',
                    'Sale.name',
                    'Sale.code',
                    'Sale.status',
                    'Sale.sale_setting_lead_phase',
                    'Sale.sales_price'
                );
                $fieldset = $this->SaleLead->parseViewField($fieldset);
                $this->Session->setFlash(__('You do not have permission in Business Lead.', true), 'error');
            } else {
                $fieldset = '';
                extract(array_merge(array(
                        'lead_status' => null),
                        $this->params['url']));
                if(!empty($lead_status)){
                    $this->Session->write("App.leadStatus", $lead_status);
                }else{
                    $sessionLeadStatus = $this->Session->read("App.leadStatus");
                    if($sessionLeadStatus){
                        $this->Session->write("App.leadStatus", $sessionLeadStatus);
                    }else{
                        $this->Session->write("App.leadStatus", 1);
                    }

                }
                if(empty($viewId)){
                    $viewId = $this->Session->read('App.leadStatusViewId');
                } else {
                    if($viewId == -1 || $viewId == -2){
                        $viewId = null;
                    }
                }
                $getLeadStatus = $this->Session->read("App.leadStatus");
                $status = $getLeadStatus - 1;
                if($status == 3){
                    $status = array(0, 1, 2);
                }
                $checkStatus = 0;
                if ($viewId) {
                    $fieldset = $this->UserView->find('first', array(
                        'fields' => array('UserView.name', 'UserView.content'),
                        'conditions' => array('UserView.id' => $viewId)));
                    if (!empty($fieldset)) {
                        $fieldset = unserialize($fieldset['UserView']['content']);
                    }
                    $this->Session->write('App.leadStatusViewId', $viewId);
                    $checkStatus = $viewId;
                } else {
                    $this->Session->write('App.leadStatusViewId', null);
                    $defaultView = $this->UserDefaultView->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $employeeLoginId,
        					'model'=>'business'
                        ),
                        'fields' => array('user_view_id')
                    ));
                    if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                        $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                        $fieldset = $this->UserView->find('first', array(
                            'fields' => array('UserView.content'),
                            'conditions' => array('UserView.id' => $defaultView)));
                        if (!empty($fieldset)) {
                            $fieldset = unserialize($fieldset['UserView']['content']);
                        }
                        $this->Session->write("App.PersonalizedDefaultSaleStatus", true);
                        $checkStatus = -2;
                    } else {
                        $checkStatus = -1;
                        $fieldset = array(
                            'Sale.sale_customer_id',
                            'Sale.name',
                            'Sale.code',
                            'Sale.status',
                            'Sale.sale_setting_lead_phase',
                            'Sale.sales_price'
                        );
                    }
                }
                $fieldset = $this->SaleLead->parseViewField($fieldset);
                /**
                 * Get Data Of Sale Lead
                 */
                $saleLeads = $this->SaleLead->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id, 'status' => $status)
                ));
                $listIdOfLeads = !empty($saleLeads) ? Set::classicExtract($saleLeads, '{n}.SaleLead.id') : array();
                $saleLeads = !empty($saleLeads) ? Set::combine($saleLeads, '{n}.SaleLead.id', '{n}.SaleLead') : array();
                $companies[$companyName['Company']['id']] = $companyName['Company']['company_name'];
                if($saleRoles == 1 || $saleRoles == 4 || $saleRoles == 5 || $roles == 'admin'){ // doc het
                    //do nothing
                } elseif($saleRoles == 2 || $saleRoles == 3 || in_array($employeeLoginId, $allSalemans)) {
                    foreach($saleLeads as $leadId => $saleLead){
                        if(!empty($salemansOfLeads[$leadId])){
                            if(!in_array($employeeLoginId, $salemansOfLeads[$leadId]) && $roles != 'admin'){
                                unset($saleLeads[$leadId]);
                            }
                        } else {
                            unset($saleLeads[$leadId]);
                        }
                    }
                }
                /**
                 * Lay last log cua cac lead
                 */
                $saleLeadLogs = $this->SaleLeadLog->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('sale_lead_id' => $listIdOfLeads),
                    'fields' => array('sale_lead_id', 'description')
                ));
                /**
                 * Lay Danh sach customer
                 */
                $saleCustomers = $this->SaleCustomer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name'),
                    'order' => array('name')
                ));
                /**
                 * Lay danh sach cac contact cua customer
                 */
                $saleCustomerContacts = $this->SaleCustomerContact->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                    ),
                    'fields' => array('id', 'full_name')
                ));
                /**
                 * Lay danh sach sale settings
                 * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
                 */
                $language = Configure::read('Config.language');
                if(Configure::read('Config.language') === 'eng'){
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name', 'type'),
                        'group' => array('type', 'id'),
                        'order' => array('weight')
                    ));
                    $maturites = $this->SaleSetting->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'type' => 5
                        ),
                        'fields' => array('id', 'name', 'percentage'),
                        'order' => array('weight')
                    ));
                    $maturites = !empty($maturites) ? Set::combine($maturites, '{n}.SaleSetting.id', array('{0} {1}%', '{n}.SaleSetting.name', '{n}.SaleSetting.percentage')) : array();
                } else {
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name_fre', 'type'),
                        'group' => array('type', 'id'),
                        'order' => array('weight')
                    ));
                    $maturites = $this->SaleSetting->find('all', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'type' => 5
                        ),
                        'fields' => array('id', 'name_fre', 'percentage'),
                        'order' => array('weight')
                    ));
                    $maturites = !empty($maturites) ? Set::combine($maturites, '{n}.SaleSetting.id', array('{0} {1}%', '{n}.SaleSetting.name_fre', '{n}.SaleSetting.percentage')) : array();
                }
                /**
                 * Lay cac ong employee co quyen trong sale
                 */
                $saleRoles = $this->SaleRole->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'NOT' => array(
                            'sale_role' => 0
                        )
                    ),
                    'fields' => array('id', 'employee_id')
                ));
                $employees = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'fullname'),
                    'conditions' => array(
                        'Employee.id' => $saleRoles
                    ),
                    'order' => array('fullname')
                ));
             }
        }
        $_personDefault = $this->Session->read("App.PersonalizedDefaultSaleStatus");
        $personDefault = $_personDefault ? true : false;
        $this->set(compact('saleCurrency', 'getLeadStatus', 'personDefault', 'checkStatus', 'read', 'companies', 'company_id', 'companyName', 'fieldset', 'saleLeads', 'saleCustomers', 'saleCustomerContacts', 'maturites', 'saleSettings', 'employees', 'saleLeadEmployeeRefers', 'saleLeadLogs'));
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update($company_id = null, $id = null) {
        $this->Session->write('reCheckActionSaleLead', 'lead');
        $this->loadModel('SaleSetting');
        $this->loadModel('SaleCustomer');
        $this->loadModel('SaleLeadEmployeeRefer');
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleExpense');
        $this->loadModel('SaleLeadFile');
        $this->loadModel('SaleLeadLog');
        $this->loadModel('SaleRole');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $companyName = strtolower($this->employee_info['Company']['company_name']);
        $avatarEmployeeLogin = $this->employee_info['Employee']['avatar_resize'];
        $roles = $this->employee_info['Role']['name'];
        $saleCurrency = $this->_getCurrencyBusiness();
        list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
        if($created == false || $updated == false){
            $this->Session->setFlash(__('You have not permission to Created/Updated Business Lead', true), 'error');
        }
        /**
         * Lay danh sach sale settings
         * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
         */
        $language = Configure::read('Config.language');
        if(Configure::read('Config.language') === 'eng'){
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id'),
                'order' => array('weight')
            ));
            $maturites = $this->SaleSetting->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => 5
                ),
                'fields' => array('id', 'name', 'percentage'),
                'order' => array('weight')
            ));
            $maturites = !empty($maturites) ? Set::combine($maturites, '{n}.SaleSetting.id', array('{0} {1}%', '{n}.SaleSetting.name', '{n}.SaleSetting.percentage')) : array();
        } else {
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name_fre', 'type'),
                'group' => array('type', 'id'),
                'order' => array('weight')
            ));
            $maturites = $this->SaleSetting->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id,
                    'type' => 5
                ),
                'fields' => array('id', 'name_fre', 'percentage'),
                'order' => array('weight')
            ));
            $maturites = !empty($maturites) ? Set::combine($maturites, '{n}.SaleSetting.id', array('{0} {1}%', '{n}.SaleSetting.name_fre', '{n}.SaleSetting.percentage')) : array();
        }
        /**
         * Save Data
         */
        if(!empty($this->data)){
            /**
             * Check Permission
             */
            if($created == false || $updated == false){
                $this->Session->setFlash(__('You have not permission to Created/Updated Business Lead', true), 'error');
                $this->redirect(array('action' => 'update', $company_id, $id));
            }
            if(!empty($this->data['salesman'])){
                $this->data['salesman'] = array_unique($this->data['salesman']);
                if(($key = array_search(0, $this->data['salesman'])) !== false) {
                    unset( $this->data['salesman'][$key]);
                }
            }
            if(!empty($this->data['is_backup'])){
                $this->data['is_backup'] = array_unique($this->data['is_backup']);
                if(($key = array_search(0, $this->data['is_backup'])) !== false) {
                    unset( $this->data['is_backup'][$key]);
                }
            }
            $countEmployee = isset($this->data['salesman']) ? (count($this->data['salesman']) - 1) : 0;
            $countBackup = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            if($countEmployee != $countBackup){
                $this->data = $this->SaleLead->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLead.id' => $id)
                ));
                $this->Session->setFlash(__('Salesman data is not valid. Please input again.', true), 'error');
            } else {
                $this->SaleLead->create();
                $saveLogs = array();
                if (!empty($this->data['SaleLead']['id'])) {
                    $this->SaleLead->id = $this->data['SaleLead']['id'];
                    $oldSaleLeads = $this->SaleLead->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('SaleLead.id' => $this->data['SaleLead']['id']),
                        'fields' => array('status', 'sale_setting_lead_maturite', 'sale_setting_lead_phase')
                    ));
                    if(!empty($this->data['SaleLead']['sale_setting_lead_maturite']) && $this->data['SaleLead']['sale_setting_lead_maturite'] != $oldSaleLeads['SaleLead']['sale_setting_lead_maturite']){
                        $oldMaturity = !empty($maturites[$oldSaleLeads['SaleLead']['sale_setting_lead_maturite']]) ?$maturites[$oldSaleLeads['SaleLead']['sale_setting_lead_maturite']] : '';
                        $newMaturity = !empty($maturites[$this->data['SaleLead']['sale_setting_lead_maturite']]) ?$maturites[$this->data['SaleLead']['sale_setting_lead_maturite']] : '';
                        if(empty($oldMaturity)){
                            $oldMaturity = '--Select--';
                        }
                        $saveLogs[] = array(
                            'company_id' => $company_id,
                            'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                            'description' => 'Change Maturity: from ' .$oldMaturity. ' to ' . $newMaturity,
                            'employee_id' => $employeeLoginId,
                            'update_by_employee' => $employeeLoginName
                        );
                    } elseif(!empty($oldSaleLeads['SaleLead']['sale_setting_lead_maturite']) && $oldSaleLeads['SaleLead']['status'] != 1){
                        if($oldSaleLeads['SaleLead']['sale_setting_lead_maturite'] != $this->data['SaleLead']['sale_setting_lead_maturite']) {
                            $oldMaturity = !empty($maturites[$oldSaleLeads['SaleLead']['sale_setting_lead_maturite']]) ?$maturites[$oldSaleLeads['SaleLead']['sale_setting_lead_maturite']] : '';
                            $saveLogs[] = array(
                                'company_id' => $company_id,
                                'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                                'description' => 'Change Maturity: from ' .$oldMaturity. ' to --Select--',
                                'employee_id' => $employeeLoginId,
                                'update_by_employee' => $employeeLoginName
                            );
                        }
                    }
                    if($this->data['SaleLead']['status'] != $oldSaleLeads['SaleLead']['status']){
                        $leadStatus = array('Open', 'Closed Won', 'Closed lost');
                        $oldStatus = !empty($leadStatus[$oldSaleLeads['SaleLead']['status']]) ?$leadStatus[$oldSaleLeads['SaleLead']['status']] : '';
                        $newStatus = !empty($leadStatus[$this->data['SaleLead']['status']]) ?$leadStatus[$this->data['SaleLead']['status']] : '';
                        $saveLogs[] = array(
                            'company_id' => $company_id,
                            'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                            'description' => 'Change Status: from ' .$oldStatus. ' to ' . $newStatus,
                            'employee_id' => $employeeLoginId,
                            'update_by_employee' => $employeeLoginName
                        );
                    }
                    if(!empty($this->data['SaleLead']['sale_setting_lead_phase']) && $this->data['SaleLead']['sale_setting_lead_phase'] != $oldSaleLeads['SaleLead']['sale_setting_lead_phase']){
                        $phases = !empty($saleSettings[6]) ? $saleSettings[6] : array();
                        $oldPhase = !empty($phases[$oldSaleLeads['SaleLead']['sale_setting_lead_phase']]) ? $phases[$oldSaleLeads['SaleLead']['sale_setting_lead_phase']] : '';
                        $newPhase = !empty($phases[$this->data['SaleLead']['sale_setting_lead_phase']]) ? $phases[$this->data['SaleLead']['sale_setting_lead_phase']] : '';
                        if(empty($oldPhase)){
                            $oldPhase = '--Select--';
                        }
                        $saveLogs[] = array(
                            'company_id' => $company_id,
                            'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                            'description' => 'Change Phase: from ' .$oldPhase. ' to ' . $newPhase,
                            'employee_id' => $employeeLoginId,
                            'update_by_employee' => $employeeLoginName
                        );
                    } elseif(!empty($oldSaleLeads['SaleLead']['sale_setting_lead_phase']) && $oldSaleLeads['SaleLead']['status'] != 1){
                        if($oldSaleLeads['SaleLead']['sale_setting_lead_phase'] != $this->data['SaleLead']['sale_setting_lead_phase']) {
                            $phases = !empty($saleSettings[6]) ? $saleSettings[6] : array();
                            $oldPhase = !empty($phases[$oldSaleLeads['SaleLead']['sale_setting_lead_phase']]) ?$phases[$oldSaleLeads['SaleLead']['sale_setting_lead_phase']] : '';
                            $saveLogs[] = array(
                                'company_id' => $company_id,
                                'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                                'description' => 'Change Phase: from ' .$oldPhase. ' to --Select--',
                                'employee_id' => $employeeLoginId,
                                'update_by_employee' => $employeeLoginName
                            );
                        }
                    }
                } else {
                    $saveLogs[] = array(
                        'company_id' => $company_id,
                        'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                        'description' => 'Add New Lead',
                        'employee_id' => $employeeLoginId,
                        'update_by_employee' => $employeeLoginName
                    );
                }
                unset($this->data['SaleLead']['id']);
                unset($this->data['SaleLead']['code_default']);
                unset($this->data['SaleLead']['order_number_default']);
                /**
                 * Save Audit Mission
                 */
                $this->data['SaleLead']['company_id'] = $company_id;
                $this->data['SaleLead']['deal_start_date'] = !empty($this->data['SaleLead']['deal_start_date']) ? strtotime(str_replace('/', '-', $this->data['SaleLead']['deal_start_date'])) : 0;
                $this->data['SaleLead']['deal_end_date'] = !empty($this->data['SaleLead']['deal_end_date']) ? strtotime(str_replace('/', '-', $this->data['SaleLead']['deal_end_date'])) : 0;
                $this->data['SaleLead']['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                if($this->SaleLead->save($this->data['SaleLead'])){
                    $id = $this->SaleLead->id;
                    /**
                     * Re Update
                     */
                    $code = explode('-', $this->data['SaleLead']['code']);
                    $code[0] = $id;
                    $order_number = explode('-', $this->data['SaleLead']['order_number']);
                    $order_number[0] = $id;
                    $saved = array(
                        'code' => implode('-', $code),
                        'order_number' => implode('-', $order_number)
                    );
                    $this->SaleLead->id = $id;
                    $this->SaleLead->save($saved);
                    /**
                     * Save logs for sales Lead
                     */
                    if(!empty($saveLogs)){
                        foreach($saveLogs as $saveLog){
                            $saveLog['sale_lead_id'] = $id;
                            $this->SaleLeadLog->create();
                            $this->SaleLeadLog->save($saveLog);
                        }
                    }
                    /**
                     * Save Salesman
                     * Lay danh sach Salesman da tao
                     */
                    $listEmployeeRefers = $this->SaleLeadEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'sale_lead_id' => $id,
                            'company_id' => $company_id,
                            'type' => 0
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['salesman'])){
                        foreach($this->data['salesman'] as $value){
                            $is_backup = 0;
                            if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                                $is_backup = 1;
                            }
                            $dataRefers = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => $is_backup,
                                'sale_lead_id' => $id,
                                'type' => 0
                            );
                            $checkDatas = $this->SaleLeadEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->SaleLeadEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['SaleLeadEmployeeRefer']['id']){
                                $this->SaleLeadEmployeeRefer->id = $checkDatas['SaleLeadEmployeeRefer']['id'];
                            }
                            if($this->SaleLeadEmployeeRefer->save($dataRefers)){
                                $lastEmployRefers = $this->SaleLeadEmployeeRefer->id;
                                unset($listEmployeeRefers[$lastEmployRefers]);
                            }
                        }
                    }
                    if(!empty($listEmployeeRefers)){
                        $this->SaleLeadEmployeeRefer->deleteAll(array('SaleLeadEmployeeRefer.id' => $listEmployeeRefers), false);
                    }
                    $this->Session->setFlash('Save.', 'success');
                }  else {
                    $this->Session->setFlash(__('Not Saved.', true), 'error');
                }
                $this->redirect(array('action' => 'update', $company_id, $id));
            }
        } else {
            $this->data = $this->SaleLead->find('first', array(
                'recursive' => -1,
                'conditions' => array('SaleLead.id' => $id, 'company_id' => $this->employee_info['Company']['id'])
            ));
            if(empty($this->data) && !empty($id)){
                $this->redirect(array('action' => 'index'));
            } else {
                if($this->data['SaleLead']['status'] && $this->data['SaleLead']['status'] == 1){
                    $statusClosedWon = 'true';
                } else {
                    $statusClosedWon = 'false';
                }
            }
        }
        /**
         * Lay ID cuoi cung audit recom
         */
        $lastIdOfSaleLead = $this->SaleLead->find('first', array(
            'recursive' => -1,
            'limit' => 1,
            'order' => array('SaleLead.id' => 'DESC'),
            'fields' => array('id')
        ));
        $lastIdOfSaleLead = !empty($lastIdOfSaleLead) && $lastIdOfSaleLead['SaleLead']['id'] ? $lastIdOfSaleLead['SaleLead']['id']+1 : 1;
        /**
         * Lay Danh sach customer
         */
        $saleCustomers = $this->SaleCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name'),
            'order' => array('name')
        ));
        /**
         * Lay cac ong employee co quyen trong sale
         */
        $saleRoles = $this->SaleRole->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'NOT' => array(
                    'sale_role' => 0
                )
            ),
            'fields' => array('id', 'employee_id')
        ));
        $employees = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'fullname'),
            'conditions' => array(
                'Employee.id' => $saleRoles
            ),
            'order' => array('fullname')
        ));
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $salesMans = $this->SaleLeadEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'sale_lead_id' => $id,
                'company_id' => $company_id,
                'type' => 0
            ),
            'fields' => array('employee_id', 'is_backup')
        ));
        /**
         * Lay Cac Product Cua Leads
         */
        $saleLeadProducts = $this->SaleLeadProduct->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'sale_lead_id' => $id
            ),
            'fields' => array(
                'id', 'company_id', 'sale_lead_id', 'sale_setting_lead_product', 'number', 'number_of_year',
                'price', 'total', 'discount_rate', 'amount_due', 'reference'
            )
        ));
        $saleLeadProducts = !empty($saleLeadProducts) ? Set::combine($saleLeadProducts, '{n}.SaleLeadProduct.id', '{n}.SaleLeadProduct') : array();
        /**
         * Lay Cac Expenses Trong Admin
         */
        $saleExpenses = $this->SaleExpense->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'capex_opex', 'unit_us', 'unit_fr')
        ));
        $saleExpenses = !empty($saleExpenses) ? Set::combine($saleExpenses, '{n}.SaleExpense.id', '{n}.SaleExpense') : array();
        /**
         * Lay cac File Upload cua Lead
         */
        $saleLeadFiles = $this->SaleLeadFile->find('all', array(
            'recurisve' => -1,
            'conditions' => array('sale_lead_id' => $id),
            'fields' => array('id', 'term', 'file_attachment', 'size', 'sale_lead_id', 'type')
        ));
        $saleLeadFiles = !empty($saleLeadFiles) ? Set::combine($saleLeadFiles, '{n}.SaleLeadFile.id', '{n}.SaleLeadFile', '{n}.SaleLeadFile.term') : array();
        /**
         * Lay cac Log cua Sale Lead
         */
        $saleLeadLogs = $this->SaleLeadLog->find('all', array(
            'recursive' => -1,
            'conditions' => array('sale_lead_id' => $id, 'company_id' => $company_id),
            'fields' => array('id', 'name', 'description', 'avatar', 'employee_id'),
            'order' => array('updated' => 'DESC')
        ));
        $listEmployeeLogs = !empty($saleLeadLogs) ? array_unique(Set::classicExtract($saleLeadLogs, '{n}.SaleLeadLog.employee_id')) : array();
        $saleLeadLogs = !empty($saleLeadLogs) ? Set::combine($saleLeadLogs, '{n}.SaleLeadLog.id', '{n}.SaleLeadLog') : array();
        /**
         * Danh sach avatar cua employee Logs
         */
        $avatarEmploys = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $listEmployeeLogs),
            'fields' => array('id', 'avatar_resize')
        ));
        /**
         * Kiem tra xem employee dang nhap co quyen gi
         */
        $saleRoles = ClassRegistry::init('SaleRole')->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLoginId),
            'fields' => array('sale_role')
        ));
        $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
        $modifySaleLead = 'false';
        if($roles == 'admin'){
            $modifySaleLead = 'true';
        } elseif(!empty($saleRoles)){
            if($saleRoles == 1){
                $modifySaleLead = 'true';
            }elseif($saleRoles == 2 || $saleRoles == 3){
                if(!empty($id)){
                    if(in_array($employeeLoginId, array_keys($salesMans))){
                        $modifySaleLead = 'true';
                    }
                } else {
                    $modifySaleLead = 'true';
                }
            }elseif($saleRoles == 4){
                $modifySaleLead = 'true';
            }
        }

        $this->set(compact('saleCurrency', 'avatarEmploys', 'employeeLoginId', 'avatarEmployeeLogin', 'roles', 'saleRoles', 'company_id', 'id', 'saleSettings', 'lastIdOfSaleLead', 'saleCustomers', 'employees', 'salesMans', 'saleLeadProducts', 'saleExpenses', 'language', 'saleLeadFiles', 'employeeLoginName', 'saleLeadLogs', 'companyName', 'maturites', 'statusClosedWon', 'modifySaleLead', 'modifySaleLead'));
    }
    /**
     * Get Danh sach customer
     */
    function get_customer($company_id){
        $this->loadModels('SaleCustomer');
        /**
         * Lay Danh sach customer
         */
        $saleCustomers = $this->SaleCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name'),
            'order' => array('name')
        ));
        $this->set(compact('saleCustomers'));
        $this->layout = false;
    }
    /**
     * Update Contact Of Customer
     */
    public function update_contact(){
        $this->loadModel('SaleCustomerContact');
        $this->layout = false;
        $result = '<option value="">--Select--</option>';
        if($_POST){
            $saleCustomerContacts = $this->SaleCustomerContact->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'sale_customer_id' => $_POST['sale_customer_id'],
                    'company_id' => $_POST['company_id'],
                ),
                'fields' => array('id', 'full_name')
            ));
            if(!empty($saleCustomerContacts)){
                foreach($saleCustomerContacts as $id => $name){
                    $result .= '<option value="' . $id . '">' . $name . '</option>';
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Update Contact Of Customer
     */
    public function update_sale_lead_price(){
        $this->layout = false;
        $result = '';
        if($_POST && $_POST['id']){
            $this->SaleLead->id = $_POST['id'];
            unset($_POST['id']);
            if($this->SaleLead->save($_POST)){
                $result = true;
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Update Production
     */
    public function update_production(){
        $this->loadModel('SaleLeadProduct');
        $result = false;
        $this->layout = false;
        if (!empty($this->data) && $this->_getCompany()) {
            $this->SaleLeadProduct->create();
            if (!empty($this->data['id'])) {
                $this->SaleLeadProduct->id = $this->data['id'];
            }
            $data = array(
                'update_by_employee' => $this->employee_info['Employee']['fullname']
            );
            unset($this->data['id']);
            if ($this->SaleLeadProduct->save(array_merge($this->data, $data))) {
                /**
                 * Save Sales Price For Lead
                 */
                $totalAmounts = $this->SaleLeadProduct->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $this->data['company_id'],
                        'sale_lead_id' => $this->data['sale_lead_id']
                    ),
                    'fields' => array('id', 'SUM(amount_due) as TotalAmount')
                ));
                $totalAmounts = !empty($totalAmounts) && $totalAmounts[0][0]['TotalAmount'] ? $totalAmounts[0][0]['TotalAmount'] : 0;
                $this->SaleLead->id = $this->data['sale_lead_id'];
                $this->SaleLead->save(array('sales_price' => $totalAmounts));
                $result = true;
            }
            $this->data['id'] = $this->SaleLeadProduct->id;
        }
        $this->set(compact('result'));
    }

    /**
     * Update Product Popup
     */
    public function update_production_popup(){
        $this->loadModel('SaleLeadProduct');
        $this->layout = false;
        $result = array(
            'SaleLeadProduct' => array()
        );
        if($_POST){
            $this->SaleLeadProduct->id = $_POST['id'];
            unset($_POST['id']);
            $_POST['start_of_billing'] = !empty($_POST['start_of_billing']) ? strtotime(str_replace('/', '-', $_POST['start_of_billing'])) : 0;
            if($this->SaleLeadProduct->save($_POST)){
                /**
                 * Lay Cac Product Cua Leads
                 */
                $saleLeadProducts = $this->SaleLeadProduct->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'SaleLeadProduct.id' => $this->SaleLeadProduct->id
                    ),
                    'fields' => array(
                        'id', 'sale_lead_id', 'sale_setting_lead_product', 'amount_due', 'start_of_billing', 'billing_period', 'number_of_payment', 'amount_due_invoice', 'reference'
                    )
                ));
                if(!empty($saleLeadProducts['SaleLeadProduct'])){
                    $saleLeadProducts['SaleLeadProduct']['start_of_billing'] = !empty($saleLeadProducts['SaleLeadProduct']['start_of_billing']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['start_of_billing']) : '';
                    $result['SaleLeadProduct'] = $saleLeadProducts['SaleLeadProduct'];
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Update Product In Expenses Popup
     */
    public function update_production_expenses_popup(){
        $this->loadModel('SaleLeadProduct');
        $this->layout = false;
        $result = array(
            'SaleLeadProduct' => array()
        );
        if($_POST){
            $this->SaleLeadProduct->id = $_POST['id'];
            unset($_POST['id']);
            $_POST['achievement_start_date'] = !empty($_POST['achievement_start_date']) ? strtotime(str_replace('/', '-', $_POST['achievement_start_date'])) : 0;
            $_POST['achievement_end_date'] = !empty($_POST['achievement_end_date']) ? strtotime(str_replace('/', '-', $_POST['achievement_end_date'])) : 0;
            $_POST['date_go_live'] = !empty($_POST['date_go_live']) ? strtotime(str_replace('/', '-', $_POST['date_go_live'])) : 0;
            if($this->SaleLeadProduct->save($_POST)){
                /**
                 * Lay Cac Product Cua Leads
                 */
                $saleLeadProducts = $this->SaleLeadProduct->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'SaleLeadProduct.id' => $this->SaleLeadProduct->id
                    ),
                    'fields' => array(
                        'id', 'sale_lead_id', 'sale_setting_lead_product', 'reference', 'achievement_start_date', 'achievement_end_date', 'date_go_live', 'number_of_month_of_achievement'
                    )
                ));
                if(!empty($saleLeadProducts['SaleLeadProduct'])){
                    $saleLeadProducts['SaleLeadProduct']['achievement_start_date'] = !empty($saleLeadProducts['SaleLeadProduct']['achievement_start_date']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['achievement_start_date']) : '';
                    $saleLeadProducts['SaleLeadProduct']['achievement_end_date'] = !empty($saleLeadProducts['SaleLeadProduct']['achievement_end_date']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['achievement_end_date']) : '';
                    $saleLeadProducts['SaleLeadProduct']['date_go_live'] = !empty($saleLeadProducts['SaleLeadProduct']['date_go_live']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['date_go_live']) : '';
                    $result['SaleLeadProduct'] = $saleLeadProducts['SaleLeadProduct'];
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Lay du lieu product va invoice cua LEAD
     */
    public function get_data_production_expense_popup(){
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleLeadProductExpense');
        $this->layout = false;
        $result = array(
            'SaleLeadProduct' => array(),
            'Expense' => array()
        );
        if($_POST){
            /**
             * Lay Cac Product Cua Leads
             */
            $saleLeadProducts = $this->SaleLeadProduct->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $_POST['company_id'],
                    'sale_lead_id' => $_POST['sale_lead_id'],
                    'SaleLeadProduct.id' => $_POST['id']
                ),
                'fields' => array(
                    'id', 'sale_setting_lead_product', 'reference', 'achievement_start_date', 'achievement_end_date', 'date_go_live', 'number_of_month_of_achievement'
                )
            ));
            if(!empty($saleLeadProducts['SaleLeadProduct'])){
                $saleLeadProducts['SaleLeadProduct']['achievement_start_date'] = !empty($saleLeadProducts['SaleLeadProduct']['achievement_start_date']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['achievement_start_date']) : '';
                $saleLeadProducts['SaleLeadProduct']['achievement_end_date'] = !empty($saleLeadProducts['SaleLeadProduct']['achievement_end_date']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['achievement_end_date']) : '';
                $saleLeadProducts['SaleLeadProduct']['date_go_live'] = !empty($saleLeadProducts['SaleLeadProduct']['date_go_live']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['date_go_live']) : '';
                $result['SaleLeadProduct'] = $saleLeadProducts['SaleLeadProduct'];
            }
            /**
             * Lay cac Expenses cua Product trong Lead
             */
            $saleLeadExpenses = $this->SaleLeadProductExpense->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $_POST['company_id'],
                    'sale_lead_id' => $_POST['sale_lead_id'],
                    'sale_lead_product_id' => $_POST['sale_lead_product_id']
                ),
                'fields' => array('id', 'sale_lead_id', 'sale_lead_product_id', 'sale_setting_lead_product', 'reference', 'name', 'sale_expense_id', 'capex_opex', 'number', 'unit', 'unit_cost', 'amount_due')
            ));
            $result['Expense'] = !empty($saleLeadExpenses) ? Set::combine($saleLeadExpenses, '{n}.SaleLeadProductExpense.id', '{n}.SaleLeadProductExpense') : array();
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Lay du lieu product va invoice cua LEAD
     */
    public function get_data_production_popup(){
        $this->loadModel('SaleLeadProduct');
        $this->layout = false;
        $result = array(
            'SaleLeadProduct' => array(),
            'Invoice' => array()
        );
        if($_POST){
            /**
             * Lay Cac Product Cua Leads
             */
            $saleLeadProducts = $this->SaleLeadProduct->find('first', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $_POST['company_id'],
                    'sale_lead_id' => $_POST['sale_lead_id'],
                    'SaleLeadProduct.id' => $_POST['id']
                ),
                'fields' => array(
                    'id', 'sale_setting_lead_product', 'amount_due', 'start_of_billing', 'billing_period', 'number_of_payment', 'amount_due_invoice', 'reference'
                )
            ));
            if(!empty($saleLeadProducts['SaleLeadProduct'])){
                $saleLeadProducts['SaleLeadProduct']['start_of_billing'] = !empty($saleLeadProducts['SaleLeadProduct']['start_of_billing']) ? date('d/m/Y', $saleLeadProducts['SaleLeadProduct']['start_of_billing']) : '';
                $result['SaleLeadProduct'] = $saleLeadProducts['SaleLeadProduct'];
            }
            /**
             * Lay cac Invoice in Product Of LEAD.
             */
            $this->loadModel('SaleLeadProductInvoice');
            $getDatas = $this->SaleLeadProductInvoice->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $_POST['company_id'],
                    'sale_lead_id' => $_POST['sale_lead_id'],
                    'sale_lead_product_id' => $_POST['sale_lead_product_id']
                ),
                'fields' => array('id', 'sale_lead_product_id', 'sale_setting_lead_product', 'reference', 'due_date', 'amount_due', 'sale_lead_id')
            ));
            $result['Invoice'] = !empty($getDatas) ? Set::combine($getDatas, '{n}.SaleLeadProductInvoice.id', '{n}.SaleLeadProductInvoice') : array();
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Update Data Invoice Popup In Product of Lead
     */
    public function update_data_invoice($update = 'false'){
        $this->loadModel('SaleLeadProductInvoice');
        $this->layout = false;
        $result = array(
            'Invoice' => array()
        );
        if($update === 'false'){
            if($_POST && $_POST['data']){
                $sale_lead_product_id = $company_id = $sale_lead_id = 0;
                foreach($_POST['data'] as $key => $value){
                    $_POST['data'][$key]['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                    $_POST['data'][$key]['due_date'] = !empty($value['due_date']) ? strtotime(str_replace('/', '-', $value['due_date'])) : 0;
                    $sale_lead_product_id = $value['sale_lead_product_id'];
                    $company_id = $value['company_id'];
                    $sale_lead_id = $value['sale_lead_id'];
                }
                $this->SaleLeadProductInvoice->create();
                if($this->SaleLeadProductInvoice->saveAll($_POST['data'])){
                    $getDatas = $this->SaleLeadProductInvoice->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => $company_id,
                            'sale_lead_id' => $sale_lead_id,
                            'sale_lead_product_id' => $sale_lead_product_id
                        ),
                        'fields' => array('reference', 'id')
                    ));
                    $result['Invoice'] = !empty($getDatas) ? $getDatas : array();
                }
            }
        } else {
            if($_POST && $_POST['id']){
                $this->SaleLeadProductInvoice->id = $_POST['id'];
                unset($_POST['id']);
                $_POST['due_date'] = !empty($_POST['due_date']) ? strtotime(str_replace('/', '-', $_POST['due_date'])) : 0;
                $this->SaleLeadProductInvoice->save($_POST);
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Delete Invoice Of Product In Lead
     */
    public function delete_data_invoices(){
        $this->loadModel('SaleLeadProductInvoice');
        $this->layout = false;
        $result = '';
        if($_POST){
            if($this->SaleLeadProductInvoice->deleteAll(array(
                'SaleLeadProductInvoice.company_id' => $_POST['company_id'],
                'SaleLeadProductInvoice.sale_lead_id' => $_POST['sale_lead_id'],
                'SaleLeadProductInvoice.sale_lead_product_id' => $_POST['sale_lead_product_id']
            ), false)){
                $result = true;
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Delete Expenses Of Product In Lead
     */
    public function delete_data_expenses(){
        $this->loadModel('SaleLeadProductExpense');
        $this->layout = false;
        $result = '';
        if($_POST){
            if($this->SaleLeadProductExpense->delete($_POST['id'])){
                $result = true;
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Save/update Expenses In Product Of Lead
     */
    public function update_data_expenses(){
        $this->loadModel('SaleLeadProductExpense');
        $this->layout = false;
        $result = '';
        if($_POST){
            if($_POST['id'] == -1){
                unset($_POST['id']);
            }
            $this->SaleLeadProductExpense->create();
            if(!empty($_POST['id'])){
                $this->SaleLeadProductExpense->id = $_POST['id'];
            }
            unset($_POST['id']);
            $_POST['update_by_employee'] = $this->employee_info['Employee']['fullname'];
            if($this->SaleLeadProductExpense->save($_POST)){
                $result = $this->SaleLeadProductExpense->id;
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Delete Product Of Lead
     */
    public function delete_production($company_id = null, $sale_lead_id = null, $id = null){
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleLeadProductInvoice');
        $this->loadModel('SaleLeadProductExpense');
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'update', $company_id, $sale_lead_id));
        }
        if ($this->_getCompany($company_id) && $this->SaleLeadProduct->delete($id)) {
            /**
             * Xoa cac Invoice
             */
            $this->SaleLeadProductInvoice->deleteAll(array(
                'SaleLeadProductInvoice.company_id' => $company_id,
                'SaleLeadProductInvoice.sale_lead_id' => $sale_lead_id,
                'SaleLeadProductInvoice.sale_lead_product_id' => $id,

            ), false);
            /**
             * Xoa cac Expenses
             */
            $this->SaleLeadProductExpense->deleteAll(array(
                'SaleLeadProductExpense.company_id' => $company_id,
                'SaleLeadProductExpense.sale_lead_id' => $sale_lead_id,
                'SaleLeadProductExpense.sale_lead_product_id' => $id,

            ), false);
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'update', $company_id, $sale_lead_id));
    }

    /**
     *  Save/edit Log System of Lead
     */
    public function update_data_log(){
        $this->loadModel('SaleLeadLog');
        $this->layout = false;
        $result = '';
        if($_POST){
            if($_POST['id'] == -1){
                unset($_POST['id']);
            }
            $this->SaleLeadLog->create();
            if(!empty($_POST['id'])){
                $this->SaleLeadLog->id = $_POST['id'];
            }
            unset($_POST['id']);
            $_POST['update_by_employee'] = $this->employee_info['Employee']['fullname'];
            if($this->SaleLeadLog->save($_POST)){
                $result = $this->SaleLeadLog->id;
            }
        }
        echo json_encode($result);
        exit;
    }

    /**
     * Upload
     */
    public function upload($company_id = null, $id = null, $term = null) {
        $this->layout = 'ajax';
        $result = array();
        $_FILES['FileField'] = array();
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if(!empty($_FILES)){
            $path = $this->_getPath($company_id, $id, $term);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,eml,msg";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment)) {
                $this->loadModel('SaleLeadFile');
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->SaleLeadFile->create();
                if ($this->SaleLeadFile->save(array(
                    'sale_lead_id' => $id,
                    'term' => $term,
                    'file_attachment' => $attachment,
                    'size' => $size,
                    'type' => $type))) {
                    $lastId = $this->SaleLeadFile->id;
                    $result = $this->SaleLeadFile->find('first', array('recursive' => -1, 'conditions' => array('SaleLeadFile.id' => $lastId)));
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    unlink($path . $attachment);
                    $this->Session->setFlash(__('The attachment could not be uploaded.', true), 'error');
                }
                $dataSession = array(
					'path' => $path,
					'file' => $attachment
				);
				$_SESSION['file_multiupload'][] = $dataSession;
                $reAction = $this->Session->read('reCheckActionSaleLead');
                if(!empty($reAction) && $reAction == 'lead'){
                    $_SESSION['file_multiupload_redirect'] = '/sale_leads/update/' . $company_id . '/' . $id;
                } else {
                    $_SESSION['file_multiupload_redirect'] = '/sale_leads/deal_update/' . $company_id . '/' . $id;
                }
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

    public function attachment($term = null, $company_id = null, $sale_lead_id = null, $id = null, $type = null) {
        $company_id = $this->employee_info['Company']['id'];
        $this->loadModel('SaleLeadFile');
        $checkDeal = !empty($this->params['url']['deal']) ? $this->params['url']['deal'] : '';
        $last = $this->SaleLeadFile->find('first', array(
            'recursive' => -1,
            'fields' => array('sale_lead_id', 'file_attachment'),
            'conditions' => array(
                'SaleLeadFile.id' => $id,
                'SaleLeadFile.sale_lead_id' => $sale_lead_id,
                'SaleLeadFile.term' => $term
            ),
            'joins' => array(
                array(
                    'table' => 'sale_leads',
                    'alias' => 'Sale',
                    'conditions' => array(
                        'Sale.id = SaleLeadFile.sale_lead_id',
                        'company_id' => $company_id
                    )
                )
            )
        ));
        //debug($last);die;
        $error = true;
        if ($last && $last['SaleLeadFile']['sale_lead_id']) {
            $attachment = $last['SaleLeadFile']['file_attachment'];
            if( $type == 'download'){
                $path = trim($this->_getPath($company_id, $last['SaleLeadFile']['sale_lead_id'], $term)
                        . $last['SaleLeadFile']['file_attachment']);
                if (file_exists($path) && is_file($path)) {
                    if ($type == 'download') {
                        $info = pathinfo($path);
                        $this->view = 'Media';
                        $params = array(
                            'id' => $info['basename'],
                            'path' => $info['dirname'] . DS,
                            'name' => $info['filename'],
                            'extension' => strtolower($info['extension']),
                            'download' => true
                        );
                        $params['mimeType'][$info['extension']] = 'application/octet-stream';
                        $this->set($params);
                    }
                    $error = false;
                }
            }
            else {
                @unlink($path);
                $this->SaleLeadFile->delete($id);
                if($this->MultiFileUpload->otherServer == true){
                    $path = trim($this->_getPath($company_id, $last['SaleLeadFile']['sale_lead_id'], $term));
                    $redirect = '/sale_leads/update/' . $company_id . '/' . $sale_lead_id;
                    if(!empty($checkDeal)){
                        $redirect = '/sale_leads/deal_update/' . $company_id . '/' . $sale_lead_id;
                    }
                    $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, $redirect);
                }
                if(!empty($checkDeal)){
                    $this->redirect(array('action' => 'deal_update', $company_id, $sale_lead_id));
                } else {
                    $this->redirect(array('action' => 'update', $company_id, $sale_lead_id));
                }

            }
        }
        if ($type != 'download') {
            $this->Session->delete('Message.flash');
            exit();
        } elseif ($error) {
            $this->Session->setFlash(__('File not found.', true), 'error');
            if(!empty($checkDeal)){
                $this->redirect(array('action' => 'deal_update', $company_id, $sale_lead_id));
            } else {
                $this->redirect(array('action' => 'update', $company_id, $sale_lead_id));
            }
        }
    }

    /**
     * Upload Avatar
     */
    public function update_avatar($company_id = null, $sale_lead_id = null){
        $this->loadModel('SaleLeadLog');
        $this->layout = false;
        $result = '';
        if(!empty($_FILES) && !empty($_POST['data']['Upload']['id'])){
            $path = $this->_getPathAvatar($company_id, $sale_lead_id);
            App::import('Core', 'Folder');
            new Folder($path, true, 0777);
            if (file_exists($path)) {
                $this->MultiFileUpload->encode_filename = false;
                $this->MultiFileUpload->uploadpath = $path;
                $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "jpg,jpeg,bmp,gif,png";
                $this->MultiFileUpload->_properties['MaxSize'] = 10000 * 1024 * 1024;
                $attachment = $this->MultiFileUpload->upload();
            } else {
                $attachment = "";
                $this->Session->setFlash(sprintf(__('File system save failed.', 'Could not create requested directory: %s.'
                                        , true), $path), 'error');
            }
            if (!empty($attachment)) {
                /**
                 * Lay File Image Old And Delete
                 */
                $oldImages = $this->SaleLeadLog->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLeadLog.id' => $_POST['data']['Upload']['id']),
                    'fields' => array('avatar')
                ));
                if(!empty($oldImages) && $oldImages['SaleLeadLog']['avatar']){
                    $imageSaves = $oldImages['SaleLeadLog']['avatar'];
                    unlink($path . $imageSaves);
                    $oldImages = $oldImages['SaleLeadLog']['avatar'];
                    $info = pathinfo($oldImages);
                    $oldImages = explode('_resize_bk_', $oldImages);
                    if(!empty($oldImages)){
                        $oldImages = $oldImages[0] . '.' . $info['extension'];
                        if(!empty($oldImages)){
                            unlink($path . $oldImages);
                        }
                    }
                }
                $attachment = $attachment['attachment']['attachment'];
                $info = pathinfo($attachment);
                $newName = basename($attachment, '.' . $info['extension']) . '_resize_bk_' . time() . '.' . $info['extension'];
                $this->PImage->resizeImage('resizeCrop', $attachment, $path, $newName, 30, 30, 60);
                /**
                 * Save data
                 */
                $this->SaleLeadLog->id = $_POST['data']['Upload']['id'];
                if ($this->SaleLeadLog->save(array(
                    'avatar' => $newName))) {
                    $result = $newName;
                    $this->Session->setFlash(__('Saved', true), 'success');
                } else {
                    unlink($path . $newName);
                    unlink($path . $attachment);
                }
            } else {
                $this->SaleLeadLog->id = $_POST['data']['Upload']['id'];
                $oldImages = $this->SaleLeadLog->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLeadLog.id' => $_POST['data']['Upload']['id']),
                    'fields' => array('avatar')
                ));
                if($this->SaleLeadLog->save(array('avatar' => ''))){
                    if(!empty($oldImages) && $oldImages['SaleLeadLog']['avatar']){
                        $imageSaves = $oldImages['SaleLeadLog']['avatar'];
                        unlink($path . $imageSaves);
                        $oldImages = $oldImages['SaleLeadLog']['avatar'];
                        $info = pathinfo($oldImages);
                        $oldImages = explode('_resize_bk_', $oldImages);
                        if(!empty($oldImages)){
                            $oldImages = $oldImages[0] . '.' . $info['extension'];
                            if(!empty($oldImages)){
                                unlink($path . $oldImages);
                            }
                        }
                        $result = '';
                    }
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    public function delete($company_id = null, $id = null){
        $this->loadModel('SaleLeadEmployeeRefer');
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $companyName = strtolower($this->employee_info['Company']['company_name']);
        $roles = $this->employee_info['Role']['name'];
        /**
         * Check Permission
         */
        list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
        if($delete == false){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $salesMans = $this->SaleLeadEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'sale_lead_id' => $id,
                'company_id' => $company_id,
                'type' => 0
            ),
            'fields' => array('employee_id', 'employee_id')
        ));
        /**
         * Kiem tra xem employee dang nhap co quyen gi
         */
        $saleRoles = ClassRegistry::init('SaleRole')->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLoginId),
            'fields' => array('sale_role')
        ));
        $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
        $allowDelete = 'false';
        if($roles == 'admin'){
            $allowDelete = 'true';
        } elseif(!empty($saleRoles)){
            if($saleRoles = 1){
                $allowDelete = 'true';
            } elseif($saleRoles = 2 || $saleRoles = 3){
                if(in_array($employeeLoginId, $salesMans)){
                    $allowDelete = 'true';
                }
            }
        }
        if($allowDelete === 'false'){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        $this->loadModel('SaleLeadProductInvoice');
        $this->loadModel('SaleLeadProductExpense');
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleLeadLog');
        $this->loadModel('SaleLeadFile');
        $lastLead = $this->SaleLead->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'SaleLead.id' => $id),
            'fields' => array('status')
        ));
        if(!empty($lastLead) && $lastLead['SaleLead']['status'] && $lastLead['SaleLead']['status'] == 1){
            $this->Session->setFlash(__('Not delete Lead have status is Closed Won.', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id) && $this->SaleLead->delete($id)) {
            /**
             * Lay logo/Avatar cua Sale Log And delete Link
             */
            $avatars = $this->SaleLeadLog->find('list', array(
                'recursive' => -1,
                'conditions' => array('SaleLeadLog.sale_lead_id' => $id),
                'fields' => array('id', 'avatar')
            ));
            if(!empty($avatars) && !empty($avatars['SaleLeadLog']['id']) && !empty($avatars['SaleLeadLog']['avatar'])){
                $path = $this->_getPathAvatar($company_id, $id);
                foreach($avatars as $avatar){
                    unlink($path . $avatar);
                    $info = pathinfo($avatar);
                    $avatar = explode('_resize_bk_', $avatar);
                    $avatar = $avatar[0] . '.' . $info['extension'];
                    unlink($path . $avatar);
                }
            }
            /**
             * Xoa Cac Sale Log
             */
            $this->SaleLeadLog->deleteAll(array('SaleLeadLog.sale_lead_id' => $id), false);
            /**
             * Lay cac Sale lead file attach And delete Link
             */
            $saleLeadFiles = $this->SaleLeadFile->find('list', array(
                'recursive' => -1,
                'conditions' => array('SaleLeadFile.sale_lead_id' => $id),
                'fields' => array('file_attachment', 'term')
            ));
            if(!empty($saleLeadFiles)){
                foreach($saleLeadFiles as $file => $term){
                    $path = $this->_getPath($company_id, $id, $term);
                    unlink($path . $file);
                }
            }
            /**
             * Xoa Cac Sale lead file
             */
            $this->SaleLeadFile->deleteAll(array('SaleLeadFile.sale_lead_id' => $id), false);
            /**
             * Xoa SaleLeadEmployeeRefer
             */
            $this->SaleLeadEmployeeRefer->deleteAll(array('SaleLeadEmployeeRefer.sale_lead_id' => $id), false);
            /**
             * Xoa Cac SaleLeadProductInvoice
             */
            $this->SaleLeadProductInvoice->deleteAll(array('SaleLeadProductInvoice.sale_lead_id' => $id), false);
            /**
             * Xoa Cac SaleLeadProductExpense
             */
            $this->SaleLeadProductExpense->deleteAll(array('SaleLeadProductExpense.sale_lead_id' => $id), false);
            /**
             * Xoa Cac SaleLeadProduct
             */
            $this->SaleLeadProduct->deleteAll(array('SaleLeadProduct.sale_lead_id' => $id), false);
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index', $company_id));
    }

    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingSaleLead(){
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : 0;
        $read = $created = $updated = $delete = $saleRoles = false;
        if($role === 'admin'){
            $read = $created = $updated = $delete = true;
        } else {
            $this->loadModel('SaleRole');
            $saleRoles = $this->SaleRole->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLogin),
                'fields' => array('sale_role')
            ));
            $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
            if(!empty($saleRoles)){
                if($saleRoles == 1){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 2){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 3){
                    $read = $created = $updated = $delete = true;
                } elseif($saleRoles == 4){
                    $read = $created = $updated = true;
                } elseif($saleRoles == 5){
                    $read = true;
                }
            }
        }
        return array($read, $created, $updated, $delete, $saleRoles);
    }

    protected function _getPathAvatar($company_id = null, $sale_lead_id = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'sale_leads' . DS . 'logs' . DS;
        $path .= $company['Company']['dir'] . DS . $sale_lead_id . DS;
        return $path;
    }

    protected function _getPath($company_id = null, $sale_lead_id = null, $term = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'sale_leads' . DS . $term . DS;
        $path .= $company['Company']['dir'] . DS . $sale_lead_id . DS;
        return $path;
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$this->is_sas) {
            $company_id = $this->employee_info['Company']['id'];
        } elseif (!$company_id && !empty($this->data['company_id'])) {
            $company_id = $this->data['company_id'];
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function deal($company_id = null, $viewId = null) {
        $this->loadModel('Company');
        $this->loadModel('SaleSetting');
        $this->loadModel('UserView');
        $this->loadModel('UserDefaultView');
        $this->loadModel('SaleCustomer');
        $this->loadModel('SaleCustomerContact');
        $this->loadModel('SaleSetting');
        $this->loadModel('SaleLeadEmployeeRefer');
        $this->loadModel('SaleRole');
        $this->loadModel('SaleLeadLog');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $roles = $this->employee_info['Role']['name'];
        $saleCurrency = $this->_getCurrencyBusiness();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            }
            $company_id = $companyName['Company']['id'];
            list($read, $created, $updated, $delete, $saleRole) = $this->_checkPermissionIsUsingSaleDeal();
            /**
             * Lay danh sach Employee Refer Cua Lead
             */
            $leadEmployRefers = $this->SaleLeadEmployeeRefer->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $company_id
                ),
                'fields' => array('id', 'sale_lead_id', 'type', 'employee_id', 'is_backup')
            ));
            /**
             * Lay cac salesman of Lead
             * Lay cac deal manager of Deal
             */
            $saleLeadEmployeeRefers = $dealManagers = $allDealManagers = $dealManagerOfDeals = array();
            if(!empty($leadEmployRefers)){
                foreach($leadEmployRefers as $leadEmployRefer){
                    $dx = $leadEmployRefer['SaleLeadEmployeeRefer'];
                    if($dx['type'] == 0){
                        $saleLeadEmployeeRefers[$dx['sale_lead_id']][$dx['employee_id']] = $dx['is_backup'];
                    } else {
                        $dealManagers[$dx['sale_lead_id']][$dx['employee_id']] = $dx['is_backup'];
                    }
                    $allDealManagers[$dx['employee_id']] = $dx['employee_id']; // deal manager and salemans
                    $dealManagerOfDeals[$dx['sale_lead_id']][$dx['employee_id']] = $dx['employee_id']; // deal manager and salemans
                }
            }
            if($read == false && !in_array($employeeLoginId, $allDealManagers)){
                $fieldset = array(
                    'Sale.sale_customer_id',
                    'Sale.name',
                    'Sale.code',
                    'Sale.deal_status',
                    'Sale.sales_price'
                );
                $fieldset = $this->SaleLead->parseViewField($fieldset);
                $this->Session->setFlash(__('You do not have permission in Business Lead.', true), 'error');
            } else {
                $fieldset = '';
                extract(array_merge(array(
                        'deal_status' => null),
                        $this->params['url']));
                if(!empty($deal_status)){
                    $this->Session->write("App.dealStatus", $deal_status);
                }else{
                    $sessionDealStatus = $this->Session->read("App.dealStatus");
                    if($sessionDealStatus){
                        $this->Session->write("App.dealStatus", $sessionDealStatus);
                    }else{
                        $this->Session->write("App.dealStatus", 1);
                    }

                }
                if(empty($viewId)){
                    $viewId = $this->Session->read('App.dealStatusViewId');
                } else {
                    if($viewId == -1 || $viewId == -2){
                        $viewId = null;
                    }
                }
                $getDealStatus = $this->Session->read("App.dealStatus");
                $dealStatus = $getDealStatus - 1;
                if($dealStatus == 3){
                    $dealStatus = array(0, 1, 2);
                }
                $checkStatus = 0;
                if ($viewId) {
                    $fieldset = $this->UserView->find('first', array(
                        'fields' => array('UserView.name', 'UserView.content'),
                        'conditions' => array('UserView.id' => $viewId)));
                    if (!empty($fieldset)) {
                        $fieldset = unserialize($fieldset['UserView']['content']);
                    }
                    $this->Session->write('App.dealStatusViewId', $viewId);
                    $checkStatus = $viewId;
                } else {
                    $this->Session->write('App.dealStatusViewId', null);
                    $defaultView = $this->UserDefaultView->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'employee_id' => $employeeLoginId,
        					'model'=> 'deal'
                        ),
                        'fields' => array('user_view_id')
                    ));
                    if ($defaultView && $defaultView['UserDefaultView']['user_view_id'] != 0) {
                        $viewId = $defaultView = $defaultView['UserDefaultView']['user_view_id'];
                        $fieldset = $this->UserView->find('first', array(
                            'fields' => array('UserView.content'),
                            'conditions' => array('UserView.id' => $defaultView)));
                        if (!empty($fieldset)) {
                            $fieldset = unserialize($fieldset['UserView']['content']);
                        }
                        $this->Session->write("App.PersonalizedDefaultSaleDealStatus", true);
                        $checkStatus = -2;
                    } else {
                        $checkStatus = -1;
                        $fieldset = array(
                            'Sale.sale_customer_id',
                            'Sale.name',
                            'Sale.code',
                            'Sale.deal_status',
                            'Sale.sales_price'
                        );
                    }
                }
                $fieldset = $this->SaleLead->parseViewField($fieldset);
                /**
                 * Get Data Of Sale Lead
                 */
                $saleLeads = $this->SaleLead->find('all', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id, 'deal_status' => $dealStatus, 'status' => 1)
                ));
                $listIdOfLeads = !empty($saleLeads) ? Set::classicExtract($saleLeads, '{n}.SaleLead.id') : array();
                $saleLeads = !empty($saleLeads) ? Set::combine($saleLeads, '{n}.SaleLead.id', '{n}.SaleLead') : array();
                $companies[$companyName['Company']['id']] = $companyName['Company']['company_name'];
                if($saleRole == 1 || $saleRole == 4 || $saleRole == 5 || $roles == 'admin'){ // doc het
                    //do nothing
                } elseif($saleRole == 2 || $saleRole == 3 || in_array($employeeLoginId, $allDealManagers)) {
                    foreach($saleLeads as $leadId => $saleLead){
                        if(!empty($dealManagerOfDeals[$leadId])){
                            if(!in_array($employeeLoginId, $dealManagerOfDeals[$leadId]) && $roles != 'admin'){
                                unset($saleLeads[$leadId]);
                            }
                        } else {
                            unset($saleLeads[$leadId]);
                        }
                    }
                }
                /**
                 * Lay last log cua cac deal
                 */
                $saleLeadLogs = $this->SaleLeadLog->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('sale_lead_id' => $listIdOfLeads),
                    'fields' => array('sale_lead_id', 'description')
                ));
                /**
                 * Lay Danh sach customer
                 */
                $saleCustomers = $this->SaleCustomer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name')
                ));
                /**
                 * Lay danh sach cac contact cua customer
                 */
                $saleCustomerContacts = $this->SaleCustomerContact->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                    ),
                    'fields' => array('id', 'full_name')
                ));
                /**
                 * Lay danh sach sale settings
                 * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
                 */
                $language = Configure::read('Config.language');
                if(Configure::read('Config.language') === 'eng'){
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name', 'type'),
                        'group' => array('type', 'id')
                    ));
                } else {
                    $saleSettings = $this->SaleSetting->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'company_id' => array(0, $company_id),
                            'type' => array_keys($this->categoryOfSales)
                        ),
                        'fields' => array('id', 'name_fre', 'type'),
                        'group' => array('type', 'id')
                    ));
               }
                /**
                 * Lay cac ong employee co quyen trong sale
                 */
                $saleRoles = $this->SaleRole->find('list', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id,
                        'NOT' => array(
                            'sale_role' => 0
                        )
                    ),
                    'fields' => array('id', 'employee_id')
                ));
                $employees = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'fullname'),
                    'conditions' => array(
                        'Employee.id' => $saleRoles
                    ),
                    'order' => array('fullname')
                ));
                /**
                 * Get Employee Of Company($company_id)
                 */
                $references = $this->Employee->CompanyEmployeeReference->find('all', array(
                    'group' => 'employee_id',
                    'fields' => array(
                        'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id'
                    ),
                    'recursive' => 1,
                    'conditions' => array('OR' => array('Company.id' => $company_id,
                            'Company.parent_id' => $company_id))));
                $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
                $conditions['Employee.id'] = array_keys($references);
                $employOfCompanies = $this->Employee->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'fullname'),
                    'conditions' => $conditions,
                    'order' => array('fullname')
                ));
             }
        }
        $_personDefault = $this->Session->read("App.PersonalizedDefaultSaleDealStatus");
        $personDefault = $_personDefault ? true : false;
        $this->set(compact('employOfCompanies', 'saleCurrency', 'getDealStatus', 'personDefault', 'checkStatus', 'read', 'company_id', 'companyName', 'fieldset', 'saleLeads', 'companies', 'saleCustomers', 'saleCustomerContacts', 'maturites', 'saleSettings', 'employees', 'saleLeadEmployeeRefers', 'dealManagers', 'saleLeadLogs'));
    }

    /**
     * update
     *
     * @return void
     * @access public
     */
    public function deal_update($company_id = null, $id = null) {
        $this->Session->write('reCheckActionSaleLead', 'deal');
        $this->loadModel('SaleSetting');
        $this->loadModel('SaleCustomer');
        $this->loadModel('SaleLeadEmployeeRefer');
        $this->loadModel('SaleLeadProduct');
        $this->loadModel('SaleExpense');
        $this->loadModel('SaleLeadFile');
        $this->loadModel('SaleLeadLog');
        $this->loadModel('SaleRole');
        if (empty($this->employee_info)) {
            $this->redirect("/login");
        }
        if( !isset($this->employee_info['Company']['id']) ){
            $this->redirect('/');
        }
        $company_id = $this->employee_info['Company']['id'];
        $employeeLoginId = $this->employee_info['Employee']['id'];
        $employeeLoginName = $this->employee_info['Employee']['fullname'];
        $companyName = strtolower($this->employee_info['Company']['company_name']);
        $avatarEmployeeLogin = $this->employee_info['Employee']['avatar_resize'];
        $roles = $this->employee_info['Role']['name'];
        $saleCurrency = $this->_getCurrencyBusiness();
        list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
        if($created == false || $updated == false){
            $this->Session->setFlash(__('You have not permission to Created/Updated Business Lead', true), 'error');
        }
        /**
         * Lay danh sach sale settings
         * Group By follow Type. Key Type xem o $categoryOfSales global de xac dinh the loai
         */
        $language = Configure::read('Config.language');
        if(Configure::read('Config.language') === 'eng'){
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name', 'type'),
                'group' => array('type', 'id')
            ));
        } else {
            $saleSettings = $this->SaleSetting->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => array(0, $company_id),
                    'type' => array_keys($this->categoryOfSales)
                ),
                'fields' => array('id', 'name_fre', 'type'),
                'group' => array('type', 'id')
            ));
        }
        /**
         * Save Data
         */
        if(!empty($this->data)){
            /**
             * Check Permission
             */
            if($created == false || $updated == false){
                $this->Session->setFlash(__('You have not permission to Created/Updated Business Lead', true), 'error');
                $this->redirect(array('action' => 'update', $company_id, $id));
            }
            /**
             * Xu ly salesman
             */
            if(!empty($this->data['salesman'])){
                $this->data['salesman'] = array_unique($this->data['salesman']);
                if(($key = array_search(0, $this->data['salesman'])) !== false) {
                    unset( $this->data['salesman'][$key]);
                }
            }
            if(!empty($this->data['is_backup'])){
                $this->data['is_backup'] = array_unique($this->data['is_backup']);
                if(($key = array_search(0, $this->data['is_backup'])) !== false) {
                    unset( $this->data['is_backup'][$key]);
                }
            }
            $countEmployee = isset($this->data['salesman']) ? (count($this->data['salesman']) - 1) : 0;
            $countBackup = isset($this->data['is_backup']) ? count($this->data['is_backup']) : 0;
            /**
             * Xu ly dealManager
             */
            if(!empty($this->data['manager_deal'])){
                $this->data['manager_deal'] = array_unique($this->data['manager_deal']);
                if(($key = array_search(0, $this->data['manager_deal'])) !== false) {
                    unset( $this->data['manager_deal'][$key]);
                }
            }
            if(!empty($this->data['is_backup_deal_manager'])){
                $this->data['is_backup_deal_manager'] = array_unique($this->data['is_backup_deal_manager']);
                if(($key = array_search(0, $this->data['is_backup_deal_manager'])) !== false) {
                    unset( $this->data['is_backup_deal_manager'][$key]);
                }
            }
            $countDealManager = isset($this->data['manager_deal']) ? (count($this->data['manager_deal']) - 1) : 0;
            $countBackupDealManager = isset($this->data['is_backup_deal_manager']) ? count($this->data['is_backup_deal_manager']) : 0;
            if($countEmployee != $countBackup){
                $this->data = $this->SaleLead->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLead.id' => $id)
                ));
                $this->Session->setFlash(__('Salesman data is not valid. Please input again.', true), 'error');
            } elseif($countDealManager != $countBackupDealManager){
                $this->data = $this->SaleLead->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('SaleLead.id' => $id)
                ));
                $this->Session->setFlash(__('Deal Manager data is not valid. Please input again.', true), 'error');
            } else {
                $this->SaleLead->create();
                $saveLogs = array();
                if (!empty($this->data['SaleLead']['id'])) {
                    $this->SaleLead->id = $this->data['SaleLead']['id'];
                    $oldSaleLeads = $this->SaleLead->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('SaleLead.id' => $this->data['SaleLead']['id']),
                        'fields' => array('deal_status')
                    ));
                    if(!empty($this->data['SaleLead']['deal_status']) && $this->data['SaleLead']['deal_status'] != $oldSaleLeads['SaleLead']['deal_status']){
                        $dealStatus = array('Open', 'Archived', 'Renewal');
                        $oldStatus = !empty($dealStatus[$oldSaleLeads['SaleLead']['deal_status']]) ? $dealStatus[$oldSaleLeads['SaleLead']['deal_status']] : '';
                        $newStatus = !empty($dealStatus[$this->data['SaleLead']['deal_status']]) ? $dealStatus[$this->data['SaleLead']['deal_status']] : '';
                        $saveLogs[] = array(
                            'company_id' => $company_id,
                            'name' => $employeeLoginName . ' ' . date('H:i d/m/Y', time()),
                            'description' => 'Change Deal Status: from ' .$oldStatus. ' to ' . $newStatus,
                            'employee_id' => $employeeLoginId,
                            'update_by_employee' => $employeeLoginName
                        );
                    }
                }
                unset($this->data['SaleLead']['id']);
                unset($this->data['SaleLead']['code_default']);
                unset($this->data['SaleLead']['order_number_default']);
                /**
                 * Save Audit Mission
                 */
                $this->data['SaleLead']['company_id'] = $company_id;
                $this->data['SaleLead']['deal_start_date'] = !empty($this->data['SaleLead']['deal_start_date']) ? strtotime(str_replace('/', '-', $this->data['SaleLead']['deal_start_date'])) : 0;
                $this->data['SaleLead']['deal_end_date'] = !empty($this->data['SaleLead']['deal_end_date']) ? strtotime(str_replace('/', '-', $this->data['SaleLead']['deal_end_date'])) : 0;
                $this->data['SaleLead']['deal_renewal_date'] = !empty($this->data['SaleLead']['deal_renewal_date']) ? strtotime(str_replace('/', '-', $this->data['SaleLead']['deal_renewal_date'])) : 0;
                $this->data['SaleLead']['update_by_employee'] = $this->employee_info['Employee']['fullname'];
                $this->data['SaleLead']['deal_status'] = isset($this->data['SaleLead']['deal_status_tmp']) ? $this->data['SaleLead']['deal_status_tmp'] : 0;
                if($this->SaleLead->save($this->data['SaleLead'])){
                    $id = $this->SaleLead->id;
                    /**
                     * Save logs for sales Lead
                     */
                    if(!empty($saveLogs)){
                        foreach($saveLogs as $saveLog){
                            $saveLog['sale_lead_id'] = $id;
                            $this->SaleLeadLog->create();
                            $this->SaleLeadLog->save($saveLog);
                        }
                    }
                    /**
                     * Save Salesman
                     * Lay danh sach Salesman da tao
                     */
                    $listEmployeeRefers = $this->SaleLeadEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'sale_lead_id' => $id,
                            'company_id' => $company_id,
                            'type' => 0
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['salesman'])){
                        foreach($this->data['salesman'] as $value){
                            $is_backup = 0;
                            if(!empty($this->data['is_backup']) && in_array($value, $this->data['is_backup'])){
                                $is_backup = 1;
                            }
                            $dataRefers = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => $is_backup,
                                'sale_lead_id' => $id,
                                'type' => 0
                            );
                            $checkDatas = $this->SaleLeadEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->SaleLeadEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['SaleLeadEmployeeRefer']['id']){
                                $this->SaleLeadEmployeeRefer->id = $checkDatas['SaleLeadEmployeeRefer']['id'];
                            }
                            if($this->SaleLeadEmployeeRefer->save($dataRefers)){
                                $lastEmployRefers = $this->SaleLeadEmployeeRefer->id;
                                unset($listEmployeeRefers[$lastEmployRefers]);
                            }
                        }
                    }
                    if(!empty($listEmployeeRefers)){
                        $this->SaleLeadEmployeeRefer->deleteAll(array('SaleLeadEmployeeRefer.id' => $listEmployeeRefers), false);
                    }
                    /**
                     * Save Deal Manager
                     * Lay danh sach Deal Manager da tao
                     */
                    unset($listEmployeeRefers);
                    $listEmployeeRefers = $this->SaleLeadEmployeeRefer->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'sale_lead_id' => $id,
                            'company_id' => $company_id,
                            'type' => 1
                        ),
                        'fields' => array('id', 'id')
                    ));
                    if(!empty($this->data['manager_deal'])){
                        foreach($this->data['manager_deal'] as $value){
                            $is_backup = 0;
                            if(!empty($this->data['is_backup_deal_manager']) && in_array($value, $this->data['is_backup_deal_manager'])){
                                $is_backup = 1;
                            }
                            $dataRefers = array(
                                'employee_id' => $value,
                                'company_id' => $company_id,
                                'is_backup' => $is_backup,
                                'sale_lead_id' => $id,
                                'type' => 1
                            );
                            $checkDatas = $this->SaleLeadEmployeeRefer->find('first', array(
                                'recursive' => -1,
                                'conditions' => $dataRefers,
                                'fields' => array('id')
                            ));
                            $this->SaleLeadEmployeeRefer->create();
                            if(!empty($checkDatas) && $checkDatas['SaleLeadEmployeeRefer']['id']){
                                $this->SaleLeadEmployeeRefer->id = $checkDatas['SaleLeadEmployeeRefer']['id'];
                            }
                            if($this->SaleLeadEmployeeRefer->save($dataRefers)){
                                $lastEmployRefers = $this->SaleLeadEmployeeRefer->id;
                                unset($listEmployeeRefers[$lastEmployRefers]);
                            }
                        }
                    }
                    if(!empty($listEmployeeRefers)){
                        $this->SaleLeadEmployeeRefer->deleteAll(array('SaleLeadEmployeeRefer.id' => $listEmployeeRefers), false);
                    }
                    /**
                     * Send Mail Khi renewal date > current date
                     */
                    $lastUpdated = $this->SaleLead->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('company_id' => $company_id, 'SaleLead.id' => $id),
                        'fields' => array('name', 'deal_renewal_date')
                    ));
                    if(!empty($lastUpdated) && $lastUpdated['SaleLead']['deal_renewal_date'] && $lastUpdated['SaleLead']['deal_renewal_date'] > time()){
                        $lastDealManagers = $this->SaleLeadEmployeeRefer->find('list', array(
                            'recursive' => -1,
                            'conditions' => array(
                                'sale_lead_id' => $id,
                                'company_id' => $company_id,
                                'type' => 1
                            ),
                            'fields' => array('employee_id', 'is_backup')
                        ));
                        $emailOfDealManagers = $this->Employee->find('list', array(
                            'recursive' => -1,
                            'conditions' => array('Employee.id' => array_keys($lastDealManagers)),
                            'fields' => array('id', 'email')
                        ));
                        if(!empty($lastDealManagers)){
                            $to = $cc = '';
                            foreach($lastDealManagers as $employ => $isBackup){
                                if($isBackup == 1){
                                    $cc[] = !empty($emailOfDealManagers[$employ]) ? $emailOfDealManagers[$employ] : '';
                                } else {
                                    $to = !empty($emailOfDealManagers[$employ]) ? $emailOfDealManagers[$employ] : '';
                                }
                            }
                            if(!empty($to)){
                                $this->set(compact('lastUpdated', 'company_id', 'id'));
                                $this->_sendEmail($to, __('[Azuree] Change the renewal date of the deal.', true), 'business_deal', false, $cc);
                            }
                        }
                    }
                    $this->Session->setFlash('Save.', 'success');
                }  else {
                    $this->Session->setFlash(__('Not Saved.', true), 'error');
                }
                $this->redirect(array('action' => 'deal_update', $company_id, $id));
            }
        } else {
            $this->data = $this->SaleLead->find('first', array(
                'recursive' => -1,
                'conditions' => array('SaleLead.id' => $id, 'company_id' => $company_id)
            ));
            if(empty($this->data)){
                $this->redirect(array('action' => 'deal', $company_id));
            } else {
                if($this->data['SaleLead']['status'] && $this->data['SaleLead']['status'] == 1){
                    $statusClosedWon = 'true';
                } else {
                    $statusClosedWon = 'false';
                }
            }
        }
        /**
         * Lay ID cuoi cung audit recom
         */
        $lastIdOfSaleLead = $this->SaleLead->find('first', array(
            'recursive' => -1,
            'limit' => 1,
            'order' => array('SaleLead.id' => 'DESC'),
            'fields' => array('id')
        ));
        $lastIdOfSaleLead = !empty($lastIdOfSaleLead) && $lastIdOfSaleLead['SaleLead']['id'] ? $lastIdOfSaleLead['SaleLead']['id']+1 : 1;
        /**
         * Lay Danh sach customer
         */
        $saleCustomers = $this->SaleCustomer->find('list', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name')
        ));
        /**
         * Lay cac ong employee co quyen trong sale
         */
        $saleRoles = $this->SaleRole->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'NOT' => array(
                    'sale_role' => 0
                )
            ),
            'fields' => array('id', 'employee_id')
        ));
        $employees = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'fullname'),
            'conditions' => array(
                'Employee.id' => $saleRoles
            ),
            'order' => array('fullname')
        ));
        /**
         * Get Employee Of Company($company_id)
         */
        $references = $this->Employee->CompanyEmployeeReference->find('all', array(
            'group' => 'employee_id',
            'fields' => array(
                'CompanyEmployeeReference.id', 'role_id', 'company_id', 'employee_id'
            ),
            'recursive' => 1,
            'conditions' => array('OR' => array('Company.id' => $company_id,
                    'Company.parent_id' => $company_id))));
        $references = Set::combine($references, '{n}.CompanyEmployeeReference.employee_id', '{n}.CompanyEmployeeReference');
        $conditions['Employee.id'] = array_keys($references);
        $employOfCompanies = $this->Employee->find('list', array(
            'recursive' => -1,
            'fields' => array('id', 'fullname'),
            'conditions' => $conditions,
            'order' => array('fullname')
        ));
        /**
         * Lay danh sach Employee Salesman refer.
         */
        $saleLeadEmployRefers = $this->SaleLeadEmployeeRefer->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'sale_lead_id' => $id,
                'company_id' => $company_id
            ),
            'fields' => array('employee_id', 'is_backup', 'type'),
            'group' => array('type', 'id')
        ));
        $salesMans = !empty($saleLeadEmployRefers) && !empty($saleLeadEmployRefers[0]) ? $saleLeadEmployRefers[0] : array();
        $dealManagers = !empty($saleLeadEmployRefers) && !empty($saleLeadEmployRefers[1]) ? $saleLeadEmployRefers[1] : array();
        /**
         * Lay Cac Product Cua Leads
         */
        $saleLeadProducts = $this->SaleLeadProduct->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'sale_lead_id' => $id
            ),
            'fields' => array(
                'id', 'company_id', 'sale_lead_id', 'sale_setting_lead_product', 'number', 'number_of_year',
                'price', 'total', 'discount_rate', 'amount_due', 'reference'
            )
        ));
        $saleLeadProducts = !empty($saleLeadProducts) ? Set::combine($saleLeadProducts, '{n}.SaleLeadProduct.id', '{n}.SaleLeadProduct') : array();
        /**
         * Lay Cac Expenses Trong Admin
         */
        $saleExpenses = $this->SaleExpense->find('all', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id),
            'fields' => array('id', 'name', 'capex_opex', 'unit_us', 'unit_fr')
        ));
        $saleExpenses = !empty($saleExpenses) ? Set::combine($saleExpenses, '{n}.SaleExpense.id', '{n}.SaleExpense') : array();
        /**
         * Lay cac File Upload cua Lead
         */
        $saleLeadFiles = $this->SaleLeadFile->find('all', array(
            'recurisve' => -1,
            'conditions' => array('sale_lead_id' => $id),
            'fields' => array('id', 'term', 'file_attachment', 'size', 'sale_lead_id', 'type')
        ));
        $saleLeadFiles = !empty($saleLeadFiles) ? Set::combine($saleLeadFiles, '{n}.SaleLeadFile.id', '{n}.SaleLeadFile', '{n}.SaleLeadFile.term') : array();
        /**
         * Lay cac Log cua Sale Lead
         */
        $saleLeadLogs = $this->SaleLeadLog->find('all', array(
            'recursive' => -1,
            'conditions' => array('sale_lead_id' => $id, 'company_id' => $company_id),
            'fields' => array('id', 'name', 'description', 'avatar', 'employee_id'),
            'order' => array('updated' => 'DESC')
        ));
        $listEmployeeLogs = !empty($saleLeadLogs) ? array_unique(Set::classicExtract($saleLeadLogs, '{n}.SaleLeadLog.employee_id')) : array();
        $saleLeadLogs = !empty($saleLeadLogs) ? Set::combine($saleLeadLogs, '{n}.SaleLeadLog.id', '{n}.SaleLeadLog') : array();
        /**
         * Danh sach avatar cua employee Logs
         */
        $avatarEmploys = $this->Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array('Employee.id' => $listEmployeeLogs),
            'fields' => array('id', 'avatar_resize')
        ));
        /**
         * Kiem tra xem employee dang nhap co quyen gi
         */
        $saleRoles = ClassRegistry::init('SaleRole')->find('first', array(
            'recursive' => -1,
            'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLoginId),
            'fields' => array('sale_role')
        ));
        $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
        $modifySaleDeal = 'false';
        if($roles == 'admin' || in_array($employeeLoginId, array_keys($dealManagers))){
            $modifySaleDeal = 'true';
        }
        $this->set(compact('saleCurrency', 'avatarEmploys', 'employeeLoginId', 'avatarEmployeeLogin', 'roles', 'saleRoles', 'employOfCompanies', 'company_id', 'id', 'saleSettings', 'lastIdOfSaleLead', 'saleCustomers', 'employees', 'salesMans', 'dealManagers', 'saleLeadProducts', 'saleExpenses', 'language', 'saleLeadFiles', 'employeeLoginName', 'saleLeadLogs', 'companyName', 'statusClosedWon', 'modifySaleDeal'));
    }

    /**
     * Phan quyen cho nguoi dung
     */
    private function _checkPermissionIsUsingSaleDeal(){
        $infos = $this->employee_info;
        $company_id = !empty($infos['Company']['id']) ? $infos['Company']['id'] : 0;
        $employeeLogin = !empty($infos['Employee']['id']) ? $infos['Employee']['id'] : 0;
        $role = !empty($infos['Role']['name']) ? $infos['Role']['name'] : 0;
        $read = $created = $updated = $delete = $saleRoles = false;
        if($role === 'admin'){
            $read = $created = $updated = $delete = true;
        } else {
            $this->loadModel('SaleRole');
            $saleRoles = $this->SaleRole->find('first', array(
                'recursive' => -1,
                'conditions' => array('company_id' => $company_id, 'employee_id' => $employeeLogin),
                'fields' => array('sale_role')
            ));
            $saleRoles = !empty($saleRoles) && $saleRoles['SaleRole']['sale_role'] ? $saleRoles['SaleRole']['sale_role'] : '';
            if(!empty($saleRoles)){
                if($saleRoles == 1){
                    $read = true;
                } elseif($saleRoles == 2){
                    $read = true;
                } elseif($saleRoles == 3){
                    $read = true;
                } elseif($saleRoles == 4){
                    $read = true;
                } elseif($saleRoles == 5){
                    $read = true;
                }
            }
        }
        return array($read, $created, $updated, $delete, $saleRoles);
    }
}