<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class EasyrapsController extends AppController {
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Easyraps';/**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    var $uses = array(
        'Easyrap',
        'Category',
        'SaleCustomer'
    );
    var $_developersMode = false;
    /**
     * Components used by the Controller
     *
     * @var array
     * @access public
     */
    var $components = array('MultiFileUpload', 'SlickExporter', 'LogSystem');
    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    var $helpers = array('Validation', 'Excel', 'Xml', 'Gantt', 'GanttSt','Number');
    function beforeFilter() {
        parent::beforeFilter();
        $this->fileTypes = 'jpg,jpeg,bmp,gif,png,txt,zip,gzip,tgz,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,xlsm,csv';
        $this->set('fileTypes', $this->fileTypes);
    }
    /**
    * Index
    *
    * @return void
    * @access public
    */
    function index($company_id = null) {
        $this->loadModel('Company');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            list($read, $created, $updated, $deleteEasyrap, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
            if($read == false){
                $this->Session->setFlash(__('You do not have permission in Easyraps.', true), 'error');
            } else {
                $this->Easyrap->cacheQueries = true;
                $easyraps = $this->Easyrap->find("all", array(
                    'recursive' => -1,
                    "conditions" => array('company_id' => $company_id)
                ));
                $category = $this->Category->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'category')
                ));
                $sales = $this->SaleCustomer->find('all', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'company_id' => $company_id
                    )
                ));
                $saleNames = !empty($sales) ? Set::combine($sales, '{n}.SaleCustomer.id', '{n}.SaleCustomer.name') : array();
                $saleTypes = !empty($sales) ? Set::combine($sales, '{n}.SaleCustomer.id', '{n}.SaleCustomer.type') : array();
				$this->set(compact('easyraps', 'company_id', 'companyName', 'category', 'sales', 'saleTypes', 'saleNames', 'deleteEasyrap'));
            }
        }
    }
    /**
     * update
     *
     * @return void
     * @access public
     */
    public function update() {
        $result = false;
        $this->layout = false;
		App::import("vendor", "str_utility");
        $str = new str_utility();
        if (!empty($this->data) && $this->_getCompany()) {
            list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
            if($created == false || $updated == false){
                $this->Session->setFlash(__('You have not permission to Created/Updated Easyraps', true), 'error');
            } else {
                if (!empty($this->data['id'])) {
                    $this->Easyrap->id = $this->data['id'];
                } else {
                     $this->Easyrap->create();
                }
    			foreach (array('date_value', 'date_operation') as $key) {
    				if (!empty($this->data[$key])) {
    					$this->data[$key] = $this->Easyrap->convertTime($this->data[$key]) == false ? $this->data[$key] : $this->Easyrap->convertTime($this->data[$key]);
    				}
    			}
                unset($this->data['id']);
                if ($this->Easyrap->save($this->data)) {
                    $result = true;
                    $this->Session->setFlash(__('OK.', true), 'success');
                } else {
                    $this->Session->setFlash(__('KO.', true), 'error');
                }
                $this->data['id'] = $this->Easyrap->id;
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }
    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null, $company_id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid ID', true), 'error');
            $this->redirect(array('action' => 'index'));
        }
        /**
         * Check Permission
         */
        list($read, $created, $updated, $delete, $saleRoles) = $this->_checkPermissionIsUsingSaleLead();
        if($delete == false){
            $this->Session->setFlash(__('You have not permission to access this function', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id) && $this->Easyrap->delete($id)) {
            $this->Session->setFlash(__('OK.', true), 'success');
        } else {
            $this->Session->setFlash(__('KO.', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
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
    public function updateCustomer(){
        if(!empty($_POST) && !empty($_POST['id'])){
            $this->Easyrap->id = $_POST['id'];
            if($this->Easyrap->save(array('customer_id' => $_POST['customer_id']))){
                echo 'Done';
                exit;
            }
        }
        echo 'ERROR';
        exit;
    }
    // export Excel
    public function export(){
    	if( !empty($this->data) ){
    		$this->SlickExporter->init();
    		$data = json_decode($this->data['data'], true);
            if(!empty($data['body'])){
                $company_id = $this->employee_info['Company']['id'];
                $sales = $this->SaleCustomer->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id', 'name')
                ));
                foreach ($data['body'] as $key => $value) {
                    $check = $this->Easyrap->find('first', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Easyrap.id' => $value[0]
                        ),
                        'fields' => array('id', 'customer_id')
                    ));
                    $data['body'][$key][6] = (!empty($check['Easyrap']['customer_id']) && !empty($sales[ $check['Easyrap']['customer_id'] ])) ? $sales[ $check['Easyrap']['customer_id'] ] : '';
                }
            }
    		$this->SlickExporter
    			->setT('Easyraps')	//auto translate
    			->save($data, 'easyrap_{date}.xls');
    	}
    	die;
    }
    protected function _getPath($company_id = null, $key = null) {
        $company = ClassRegistry::init('Company')->find('first', array(
            'recursive' => -1, 'conditions' => array('Company.id' => $company_id)));
        $path = FILES . 'easyrap' . DS . $key . DS;
        $path .= $company['Company']['dir'] . DS;
        return $path;
    }
    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update_document($id) {
        $this->layout = 'ajax';
        $result = array();
        $_FILES['FileField'] = array();
        $company_id = $this->employee_info['Company']['id'];
        if(!empty($_FILES['file'])){
            $_FILES['FileField']['name']['attachment'] = $_FILES['file']['name'];
            $_FILES['FileField']['type']['attachment'] = $_FILES['file']['type'];
            $_FILES['FileField']['tmp_name']['attachment'] = $_FILES['file']['tmp_name'];
            $_FILES['FileField']['error']['attachment'] = $_FILES['file']['error'];
            $_FILES['FileField']['size']['attachment'] = $_FILES['file']['size'];
            unset($_FILES['file']);
        }
        if(!empty($_FILES)){
            $path = $this->_getPath($company_id);
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
                $size = $attachment['attachment']['size'];
                $type = $_FILES['FileField']['type']['attachment'];
                $attachment = $attachment['attachment']['attachment'];
                $this->Easyrap->id = $id;
                if ($this->Easyrap->save(array(
                    'purchase_journal' => $attachment))) {
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
            } else {
                $this->Session->setFlash(implode(', ', array_values($this->MultiFileUpload->errors)), 'error');
            }
        }
        echo json_encode($result);
        exit;
    }

        public function attachement($id = null) {
            $type = isset($this->params['url']['type']) ? $this->params['url']['type'] : 'download';
            $company_id = $this->employee_info['Company']['id'];
            $last = $this->Easyrap->find('first', array(
                'recursive' => -1,
                'fields' => array('purchase_journal', 'id'),
                'conditions' => array('Easyrap.id' => $id)));
            $error = true;
            if ($last && $last['Easyrap']['id']) {
                $path = trim($this->_getPath($company_id)
                        . $last['Easyrap']['purchase_journal']);
                $attachment = $last['Easyrap']['purchase_journal'];
                if($type == 'download'){
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
                } else {
                    @unlink($path);
                    $this->Easyrap->id = $id;
                    $this->Easyrap->save(array(
                        'purchase_journal' => '',
                        'format' => 0,
                    ));
                    if($this->MultiFileUpload->otherServer == true){
                        $path = trim($this->_getPath($company_id));
                        $this->MultiFileUpload->deleteFileToServerOther($path, $attachment, '/easyraps/');
                    }
                }
            }
            if ($type != 'download') {
                $this->Session->delete('Message.flash');
                exit();
            } elseif ($error) {
                $this->Session->setFlash(__('File not found.', true), 'error');
                $this->redirect(array('action' => 'index',
                    $last ? $last['Easyrap']['id'] : __('Unknown', true)));
            }
        }

    /**
     * If case on exists then notify.
     *
     * @param
     * @return void
     * @access public
     */
    function import_csv() {
        $company_id = $this->employee_info['Company']['id'];
        App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
        App::import('Core', 'Validation', false);
        $csv = new parseCSV();
        if (!empty($_FILES['FileField']['name']['csv_file_attachment'])) {
            $this->MultiFileUpload->encode_filename = false;
            $this->MultiFileUpload->uploadpath = TMP . 'uploads' . DS . 'Easyraps' . DS;
            $this->MultiFileUpload->_properties['AttachTypeAllowed'] = "csv";
            $this->MultiFileUpload->_properties['MaxSize'] = 25 * 1024 * 1000;
            $reVal = $this->MultiFileUpload->upload();
            if (!empty($reVal)) {
                $filename = TMP . 'uploads' . DS . 'Easyraps' . DS . $reVal['csv_file_attachment']['csv_file_attachment'];
                $csv->auto($filename);
                if (!empty($csv->data)) {
                    $records = array(
                        'Create' => array(),
                        'Error' => array()
                    );
                    $columnMandatory = array(
                        'Date Operation',
                        'Date Value',
                        'Amount',
                        'Label',
                        'Balances',
                        'Customer',
                        'Category'
                    );
                    $default = array();
                    if(!empty($csv->titles)){
                        foreach($csv->titles as $headName){
                            if(in_array($headName, $columnMandatory)){
                                $default[$headName] = '';
                            }
                        }
                    }
                    $defaultKeys = array_keys($default);
                    $count = count($default);
                    $validate = array('Date Operation', 'Date Value', 'Label');
                    // tach cac tham so can thiet
                    foreach($csv->data as $row){
                        if(isset($row['#']) || isset($row['No.'])){
                            unset($row['#']);
                            unset($row['No.']);
                        }
                        foreach(array_keys($row) as $name){
                            if(!in_array($name, $columnMandatory)){
                                unset($row[$name]);
                            }
                        }
                        $error = false;
                        $row = array_merge(array_combine($defaultKeys, array_slice(array_map('trim', array_values($row))
                                                + array_fill(0, $count, ''), 0, $count)), array(
                            'data' => array(),
                            'error' => array()));
                        $row['data']['label'] = !empty($row['Label']) ? $row['Label'] : '';
                        foreach ($validate as $key => $value) {
                            $row[$value] = trim($row[$value]);
                            if (empty($row[$value])) {
                                $row['columnHighLight'][$value] = '';
                                $row['error'][] = sprintf(__('The %s is not blank', true), $value);
                            }
                        }
                        if(empty($row['error'])) {
                            if(!empty($row['Customer'])){
                                $check = $this->SaleCustomer->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'name' => $row['Customer'],
                                        'company_id' => $company_id
                                    ),
                                    'fields' => array('id', 'name')
                                ));
                                if(!empty($check)){
                                    $row['data']['customer_id'] = $check['SaleCustomer']['id'];
                                } else {
                                    $row['columnHighLight']['Customer'] = '';
                                    $row['error'][] = __('The customer name not found in company', true);
                                }
                            }
                            if(!empty($row['Category'])){
                                $check = $this->Category->find('first', array(
                                    'recursive' => -1,
                                    'conditions' => array(
                                        'category' => $row['Category'],
                                        'company_id' => $company_id
                                    ),
                                    'fields' => array('id', 'category')
                                ));
                                if(!empty($check)){
                                    $row['data']['category_id'] = $check['Category']['id'];
                                } else {
                                    $row['columnHighLight']['Category'] = '';
                                    $row['error'][] = __('The category name not found in company', true);
                                }
                            }
                            if(!empty($row['Amount'])){
                                if(is_numeric($row['Amount'])){
                                    $row['data']['amount'] = $row['Amount'];
                                } else {
                                    $row['columnHighLight']['Amount'] = '';
                                    $row['error'][] = __('The amonut is not number', true);
                                }
                            }
                            if(!empty($row['Balances'])){
                                if(is_numeric($row['Balances'])){
                                    $row['data']['balances'] = $row['Balances'];
                                } else {
                                    $row['columnHighLight']['Balances'] = '';
                                    $row['error'][] = __('The balances is not number', true);
                                }
                            }
                            if(!empty($row['Date Operation']) || !empty($row['Date Value'])){
                                $_start = $_end = 0;
                                if(!empty($row['Date Operation'])){
                                    $_start = $this->_formatDateCustom($row['Date Operation']);
                                    if($_start != 0){
                                        $row['Date Operation'] = $_start;
                                        $row['data']['date_operation'] = $row['Date Operation'];
                                    } else {
                                        $row['columnHighLight']['Date Operation'] = 0;
                                        $row['error'][] = __('The date not right', true);
                                    }
                                }
                                if(!empty($row['Date Value'])){
                                    $_end = $this->_formatDateCustom($row['Date Value']);
                                    if($_end != 0){
                                        $row['Date Value'] = $_end;
                                        $row['data']['date_value'] = $row['Date Value'];
                                    } else {
                                        $row['columnHighLight']['Date Value'] = 0;
                                        $row['error'][] = __('The date not right', true);
                                    }
                                }

                            }
                        }
                        if (!empty($row['error'])) {
                            unset($row['data']);
                            $records['Error'][] = $row;
                        } else {
                            $records['Create'][] = $row;
                        }
                    }
                }
            }
            $this->set('records', $records);
            $this->set('default', $default);
        } else {
            $this->redirect(array('controller' => 'easyraps', 'action' => 'index'));
        }
    }
    /**
     * Save import of easyrap
     */
    function save_file_import() {
        set_time_limit(0);
        if (!empty($this->data)) {
            extract($this->data['Import']);
            if ($task === 'do') {//export
                $import = array();
                foreach (explode(',', $type) as $type) {
                    if (empty($this->data[$type][$task])) {
                        continue;
                    }
                    $import = array_merge($import, $this->data[$type][$task]);
                }
                if (empty($import)) {
                    $this->Session->setFlash(__('The data to export was not found. Please try again.', true));
                    $this->redirect(array('action' => 'index', $project_id));
                }
                $company_id = $this->employee_info['Company']['id'];
                $complete = 0;
                $totalRecordImport = count($import);
                foreach($import as $key => $data){
                    $this->Easyrap->create();
                    $this->Easyrap->save(array(
                        'company_id' => $company_id,
                        'date_operation' => !empty($data['date_operation']) ? $data['date_operation'] : '',
                        'date_value' => !empty($data['date_value']) ? $data['date_value'] : '',
                        'amount' => !empty($data['amount']) ? $data['amount'] : 0,
                        'label' => !empty($data['label']) ? $data['label'] : '',
                        'balances' => !empty($data['balances']) ? $data['balances'] : 0,
                        'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : '',
                        'category_id' => !empty($data['category_id']) ? $data['category_id'] : ''
                    ));
                }
                $this->Session->setFlash(__('Esyraps imported complete', true));
                $this->redirect(array('action' => 'index'));
            } else { // export csv
                App::import('Vendor', 'ParsecsvLib', array('file' => 'parsecsv.lib.php'));
                $csv = new parseCSV();
                header("Content-Type: text/html; charset=ISO-8859");
                // export
                $header = array();
                $type = '';
                if ($this->data['Import']['type'] == 'Error' && !empty($this->data['Error']['export']))
                    $type = 'Error';
                if ($this->data['Import']['type'] == 'Create' && !empty($this->data['Create']['export']))
                    $type = 'Create';
                if ($this->data['Import']['type'] == 'Update' && !empty($this->data['Update']['export']))
                    $type = 'Update';
                if (!empty($type)){
                    $_listEmployee = array();
                    foreach ($this->data[$type]['export'][1] as $key => $value) {
                        $header[] = __($key , true);
                    }
                    foreach($this->data[$type]['export'] as $key => $value){
                        $_listEmployee[$key] = $this->_utf8_encode_mix($value);
                    }
                    $csv->output($type . ".csv",  $_listEmployee ,$this->_mix_coloumn($header), ",");
                } else {
                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
        exit;
    }
    private function _mix_coloumn($input){
        $result = array();
        foreach($input as $value){
            $result[] = mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    private function _utf8_encode_mix($input){
        $result = array();
        foreach($input as $key => $value){
            $result[mb_convert_encoding($key,'UTF-16LE', 'UTF-8')] =  mb_convert_encoding($value,'UTF-16LE', 'UTF-8');
        }
        return $result;
    }
    /**
     * Ham dung de nhan dang va dinh dang lai date time
     */
    private function _formatDateCustom($date = null){
        $date = str_replace('/', '-', $date);
        $date = preg_replace('/[^\d-]/i', '', $date);
        $date = explode('-', $date);
        $currentDate = date('Y', time());
        $century = substr($currentDate, 0, 2);
        $day = $month = $year = 0;
        $day = !empty($date[0]) ? preg_replace('/\D/i', '', $date[0]) : '00';
        $month = !empty($date[1]) ? preg_replace('/\D/i', '', $date[1]) : '00';
        $year = !empty($date[2]) ? preg_replace('/\D/i', '', $date[2]) : '0000';
        $result = $year . '-' . $month . '-' . $day;
        return $result;
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
                } elseif($saleRoles == 6){
                    $read = $created = $updated = true;
                }
            }
        }
        return array($read, $created, $updated, $delete, $saleRoles);
    }
}
