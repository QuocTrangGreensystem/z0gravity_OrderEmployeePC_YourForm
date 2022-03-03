<?php

/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class SqlManagerController extends AppController {

    var $name = 'SqlManager';

    /**
     * Controller using model
     * @var array
     * @access public
     */
	/* 
	* Modified by Huynh 20-08-2019
	* Z0G 15/8/2019 POWERBI
	* Yêu cầu ở màn hình SQL hiện tại thêm chức năng xem iframe
	
	*/
	 
	
    var $uses = array();
    public function index() {
        $this->loadModels('SqlManager','Company');
        $user = $this->employee_info;
		$is_sas = !empty($user['Employee']['is_sas']);
		$is_admin = !empty($user['Role']['name']) && $user['Role']['name'] == 'admin';
        if ($is_sas) {
            //sas can create and edit all
            $datas = $this->SqlManager->find("all", array(
                'fields' => array('*'),
                'recursive' => -1,
            ));
        } else{
            //admin company only access sql have right
            $companyId = $user['Company']['id'];
			$employee_id = $user['Employee']['id'];
            $datas = $this->SqlManager->find("all", array(
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
        }
		$dataView = array();
		foreach( $datas as $key => $val){
			$data =  $val['SqlManager'];
			$data['company'] = $this->Company->getListFromString($data['company']);
			$data['resource'] = $this->Employee->getEmployeesFromSqlId($data['id']);$dataView[] = $data;
		}
        $this->set(compact('dataView'));
		$type_column = $this->SqlManager->getColumnType('type') ;
		preg_match("/^enum\(\'(.*)\'\)$/", $type_column, $matches);
		$typelist = array();
		if( !empty($matches[1]) ){
			$typelist = explode("','", $matches[1]);
			$typelist = array_combine ($typelist, $typelist);
		}
		$companylist = array();
		if( $is_sas){
			$companylist = $this->Company->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'company_name')
			));
		}
        $this->set(compact('dataView','companylist','typelist','is_sas','is_admin'));
    }

    public function add() {
		$is_sas = !empty( $this->employee_info['Employee']['is_sas'] );
		if (!$is_sas) return;
        $this->layout = false;
        $this->loadModels('SqlManager', 'SqlManagerEmployee');
        if (!empty($this->data)) {
            $this->data['SqlManager']['company'] = implode(',', $this->data['SqlManager']['company']);
            if($this->employee_info['Employee']['is_sas'] == 1){
                // set is template if create by sas
                $this->data['SqlManager']['is_template'] =1;
            }
            if ($this->SqlManager->save($this->data['SqlManager'])) {
                $idSql = $this->SqlManager->id;
                $SqlManagerEmployee = $this->data['SqlManagerEmployee']['resource'];
                foreach ($SqlManagerEmployee as $key => $value) {
                    $temp = explode('_', $value);
                    $employees[] = array('company_id' => $temp[0], 'employee_id' => $temp[1], 'sql_manager_id' => $idSql);
                }
                $this->data['SqlManagerEmployee'] = $employees;
                $datas = $this->SqlManagerEmployee->saveAll($this->data['SqlManagerEmployee']);
            }
        }
        if ($datas) {
            $this->Session->setFlash(__('Saved', true), 'success');
        } else {
            $this->Session->setFlash(__('Error', true), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }

    function edit($id = null) {
		$is_sas = !empty( $this->employee_info['Employee']['is_sas'] );
		if (!$is_sas) return;
        $this->layout = false;
        $this->layout = false;
        $this->loadModels('SqlManager', 'SqlManagerEmployee');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid require', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $this->data['SqlManager']['company'] = implode(',', $this->data['SqlManager']['company']);
            if ($this->SqlManager->save($this->data['SqlManager'])) {
                $this->SqlManagerEmployee->deleteAll(array('SqlManagerEmployee.sql_manager_id' => $id));
                $idSql = $id;
                $SqlManagerEmployee = $this->data['SqlManagerEmployee']['resource'];
                foreach ($SqlManagerEmployee as $key => $value) {
//                    debug($SqlManagerEmployee);exit;
                    $temp = explode('_', $value);
                    $employees[] = array('company_id' => $temp[0], 'employee_id' => $temp[1], 'sql_manager_id' => $idSql);
                }
                $this->data['SqlManagerEmployee'] = $employees;
                $datas = $this->SqlManagerEmployee->saveAll($this->data['SqlManagerEmployee']);
                $this->Session->setFlash(__('The request has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The require could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->SqlManager->read(null, $id);
            $companySelected = $resourceSelected = array();
            if (isset($this->data['SqlManagerEmployee'])) {
                foreach ($this->data['SqlManagerEmployee'] as $employee) {
                    $resourceSelected[] = $employee['company_id'] . "_" . $employee['employee_id'];
                }
            }
            $companySelected = $this->data['SqlManager']['company'] = explode(',', $this->data['SqlManager']['company']);
            $user = $this->employee_info;
            if ($user['Employee']['is_sas'] == 1) {

            $companylist = $this->Company->find('list', array(
                    'recursive' => -1,
                    'fields' => array('id', 'company_name')
                ));
            } elseif ($user['Role']['name'] == 'admin') {

                $companylist  = $this->Company->find('list', array(
                        'recursive' => -1,
                        'conditions' => array(
                            'Company.id' => $user['Company']['id']
                        ),
                        'fields' => array('id', 'company_name')
                    ));
            }
			$type_column = $this->SqlManager->getColumnType('type') ;
			preg_match("/^enum\(\'(.*)\'\)$/", $type_column, $matches);
            $typelist = array();
            if( !empty($matches[1]) ){
                $typelist = explode("','", $matches[1]);
                $typelist = array_combine ($typelist, $typelist);
            }
//            $resourceSelected = $this->data['SqlManager']['resource'] = explode(',', $this->data['SqlManager']['resource']);
//            debug($resourceSelected);exit;
            $this->set(compact('companySelected', 'resourceSelected','companylist', 'typelist'));
        }
    }

    /**
     * delete
     *
     * @return void
     * @access public
     */
    function delete($id = null) {

        $this->layout = false;
        $this->loadModels('SqlManager', 'SqlManagerEmployee');

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for require', true), 'error');
            $this->redirect(array('action' => 'index'));
        }

        if ($this->SqlManager->delete($id, true)) {
			$this->SqlManagerEmployee->deleteAll(array('SqlManagerEmployee.sql_manager_id' => $id));
            $this->Session->setFlash(__('Deleted', true), 'success');
            $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('Not deleted', true), 'error');
        $this->redirect(array('action' => 'index'));
    }

    public function getresource() {
        //render resource from company
        $this->loadModel('Employee');
        if (!empty($this->params['url'])) {
            $companySelect = $this->params['url']['companySelect'];
            $priorities = $this->Employee->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'company_id' => $companySelect,
                    'not' => array('Employee.company_id' => null)
                ),
                'fields' => array('employee_company', 'fullname')
            ));
            $option = "";
            foreach ($priorities as $id => $value) {
                $option.= "<option value=\"$id\">$value </option>";
            }
            echo $option;
            exit;
        } else {
            echo json_encode('fail getresource');
            exit;
        }
    }

    public static function handleError($code, $description, $file = null, $line = null, $context = null) {
        //handle sql error to get message
        if (error_reporting() == 0 || $code === 2048 || $code === 8192) {
            return;
        }

        throw new Exception(strip_tags($description));
    }

    public function excutesql() {
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
			$result = $this->makeDataResult($requireSql);
			$datas = $result['datas'];
			$status = $result['status'];
			$columns = $result['columns'];
			$this->set(compact('datas','columns' , 'status', 'requireSql'));
		}
    }

    public function export_excel() {
        $this->layout = false;
        if (isset($_POST['requireSql'])) {
            $requireSql = $_POST['requireSql'];
        }
        ini_set('max_execution_time', 1000);
        ini_set('memory_limit', '-1');
        $result = $this->makeDataResult($requireSql);
        $datas = $result['datas'];
        $status = $result['status'];
        $columns = $result['columns'];

        $this->set(compact('datas','columns' ,'status', 'requireSql'));
    }

    function makeDataResult($requireSql) {
        // return data if success , status error
        ini_set('memory_limit', '-1');
        $this->loadModel('SqlManager');
        set_error_handler('SqlManagerController::handleError');
        if( empty($this->employee_info['Employee']['is_sas'])) $this->SqlManager->setDataSource('sqlExecute');
        $datas = array();
        $status = '';
        try {
            $results = $this->SqlManager->query($requireSql);
            if(!empty($results)){
                foreach ($results as $result) {
                    $i = 1;
                    foreach ($result as $modelname => $row) {
                        $count = count($result);
                        foreach ($row as $key => $value) {
                            if (isset($dataT)) {
                                if (array_key_exists($key, $dataT)) {
                                    $dataT[$modelname . "." . $key] = $value;
                                } else {
                                    $dataT[$key] = $value;
                                }
                            } else {
                                $dataT[$key] = $value;
                            }
                        }
                        if ($i == $count) {
                            $datas[] = $dataT;

                            $dataT = array();
                        }
                        $i = $i + 1;
                    }
                }
            }else{
				$status = __('Data empty', true);
			}
        } catch (Exception $ex) {
            $datas = array();
            $status = $ex->getMessage();
        }
        $columns = !empty($datas) ? array_keys(current($datas)) : array();
        $datas = $this->lowarray($datas,CASE_LOWER);
        
        
        return array('datas' => $datas,
                    'columns' =>  $columns,  
                    'status' => $status
        );
    }
    function lowArray($input, $case = CASE_LOWER)
    {
        $input = array_change_key_case($input, $case);

        foreach($input as $key => $array)
            if(is_array($array))
                $input[$key] = $this->lowArray($array, $case);

        return $input;
    }
}

?>
