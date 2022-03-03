<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class ReportsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Reports';
	var $helpers = array('Tableau');
    /**
     * Controller using model
     * @var array
     * @access public
     */
    //var $uses = array();
    /**
     * Controller index
     * @var array
     * @access public
     */
    public function index($company_id =null, $type = null) {
        $this->loadModel('Company');
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Company->find('list');
            $this->viewPath = 'reports' . DS . 'list';
            $this->set(compact('companies'));
        } else {
            $type = !empty($type) ? $type : 'absence';
            // $companyName = $this->_getCompany();
            $isAdmin = (!empty($this->employee_info) && !empty($this->employee_info['Role']) && ($this->employee_info['Role']['name'] == 'admin') || $this->is_sas) ? true : false;
            $reports = array();
            if (empty($company_id)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
            } else {
                $companyName = $this->Company->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('id' => $company_id),
                    'fields' => array('id', 'company_name')
                ));
                $companyName = $companyName['Company']['company_name'];
                $reports = $this->Report->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id, 'type' => $type),
                    'fields' => array('id', 'data')
                ));
    			//'guilhem.davion@in-genia.fr'
            }
            $this->set(compact('email','reports', 'companyName', 'company_id', 'type', 'isAdmin'));
        }
    }

    /**
     * Controller update
     * @var array
     * @access public
     */
    public function update($type = null, $company_id = null, $id = null) {
        if (!empty($this->data) && $this->_getCompany($company_id)) {
            $this->Report->create();
            if (!empty($id)) {
                $this->Report->id = $id;
            }
            $this->data['Report']['company_id'] = $company_id;
            $this->data['Report']['type'] = $type;
            if ($this->Report->save($this->data['Report'])) {
                $this->Session->setFlash(__('Saved', true), 'success');
            } else {
                $this->Session->setFlash(__('Not saved', true), 'error');
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->redirect(array('action' => 'index', $company_id, $type));
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($company_id = null, $id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Employee', true), 'error');
            $this->redirect(array('action' => 'index', $company_id));
        }
        if ($this->_getCompany($company_id) && $this->Report->delete($id)) {
            $this->Session->setFlash(__('Deleted', true), 'success');
        } else {
            $this->Session->setFlash(__('Not deleted', true), 'error');
        }
        $this->redirect(array('action' => 'index', $company_id));
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _getCompany($company_id = null) {
        if (!$company_id){
            if (!$this->is_sas) {
                $company_id = $this->employee_info['Company']['id'];
            } elseif (!$company_id && !empty($this->data['company_id'])) {
                $company_id = $this->data['company_id'];
            }
        }
        $companyName = ClassRegistry::init('Company')->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }
    public function sql_report() {
        $this->loadModels('SqlManager','Company');
        $user = $this->employee_info;
        $is_sas = !empty($user['Employee']['is_sas']);
		$is_admin = !empty($user['Role']['name']) && $user['Role']['name'] == 'admin';
        if ($is_sas) {
            //sas can create and edit all
            $data = $this->SqlManager->find("all", array(
                'fields' => array('*'),
                'recursive' => -1,
            ));
        } else{
            //admin company only access sql have right
            $companyId = $user['Company']['id'];
			$employee_id = $user['Employee']['id'];
            $data = $this->SqlManager->find("all", array(
                'joins' => array(
                    array(
                        'table' => 'sql_manager_employees',
                        'alias' => 'SqlManagerEmployee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'SqlManager.id = SqlManagerEmployee.sql_manager_id'
                        )
                    )

                ),
                'fields' => array('*'),
                'conditions' => array(
					"FIND_IN_SET('$companyId',SqlManager.company)",
					'SqlManagerEmployee.employee_id' => $employee_id, 
				),
                'group' => array('SqlManager.id'),
                'recursive' => -1,
            ));
			if( count($data) == 1){
				$this->redirect(array('action' => 'viewReport', $data['0']['SqlManager']['id']));
			}
        }
		$dataView = array();
		foreach( $data as $key => $val){
			$val =  $val['SqlManager'];
			$val['company'] = $this->Company->getListFromString($val['company']);
			$val['resource'] = $this->Employee->getEmployeesFromSqlId($val['id']);			
			$dataView[] = $val;
		}
        $this->set(compact('dataView'));
    }
    public function excutesql() {
        App::import("Controller", "SqlManager");
        $SqlControler = new SqlManagerController();
		$isAjax = $this->params['isAjax'];
		if( $isAjax  ) $this->layout = 'ajax';
		$this->set('isAjax', $isAjax );
        $this->loadModel('SqlManager');
		if( isset($_POST['viewIframeText']) || isset($_POST['viewIframe'])){
			if (isset($_POST['viewIframeText'])) {
				$iframeText = $_POST['viewIframeText'];
			}
			if (isset($_POST['viewIframe'])) {
				$id = $_POST['viewIframe'];
				$sql = $this->SqlManager->read(null, $id);
				$iframeText = $sql['SqlManager']['request_sql'];
			}
			$show_iframe = 1;
			$this->set(compact('show_iframe','iframeText'));
			
		}else{
			if (isset($_POST['requireSql'])) {
				$requireSql = $_POST['requireSql'];
			}
			if (isset($_POST['requireID'])) {
				$id = $_POST['requireID'];
				$sql = $this->SqlManager->read(null, $id);
				$requireSql = $sql['SqlManager']['request_sql'];
			}
			$result = $SqlControler->makeDataResult($requireSql);
			$columns = $result['columns'];
			$datas = $result['datas'];
			$status = $result['status'];
			
			$this->set(compact('datas','columns', 'status', 'requireSql'));
		}
	}
    public function viewReport($id) {
        App::import("Controller", "SqlManager");
		$isAjax = $this->params['isAjax'];
		$this->layout = 'recycle_bin';
		$this->action = 'excutesql';
		if( $isAjax  ) $this->layout = 'ajax';
		$this->set('isAjax', $isAjax );
        $this->loadModel('SqlManager');
		if( $this->employee_info['Employee']['is_sas']){
			$sql = $this->SqlManager->find('first', array(
				'recursive' => -1,
				'conditions' => array( 'id' => $id),
				'fields' => array('id', 'request_name', 'request_sql', 'type' )
			));
		}else{			
			$sql = $this->SqlManager->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'SqlManager.id' => $id,
					'SqlManagerEmployee.employee_id' => $this->employee_info['Employee']['id']
				),
				'joins' => array(
					array(
                        'table' => 'sql_manager_employees',
                        'alias' => 'SqlManagerEmployee',
                        'type' => 'INNER',
                        'conditions' => array(
                            'SqlManager.id = SqlManagerEmployee.sql_manager_id'
                        )
					),
				),
				'fields' => array('SqlManager.id', 'SqlManager.request_name', 'SqlManager.request_sql', 'SqlManager.type' )
			));
		}
		if( empty($sql)){
			$status = __('Data empty', true);
			$this->set(compact('status'));
		}elseif( $sql['SqlManager']['type'] == 'link'){
			$link= $sql['SqlManager']['request_sql'];
			$this->redirect($link);
		}elseif( $sql['SqlManager']['type'] == 'iframe'){
			$iframeText = $sql['SqlManager']['request_sql'];
			$show_iframe = 1;
			$this->set(compact('show_iframe','iframeText'));
		}elseif( $sql['SqlManager']['type'] == 'sql'){
			$SqlControler = new SqlManagerController();
			$requireSql = $sql['SqlManager']['request_sql'];
			$result = $SqlControler->makeDataResult($requireSql);
			$columns = $result['columns'];
			$datas = $result['datas'];
			$status = $result['status'];
			$this->set(compact('datas','columns', 'status', 'requireSql'));
		}
	}
}
?>
