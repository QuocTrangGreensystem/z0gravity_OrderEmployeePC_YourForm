<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class HolidaysController extends AppController {

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    var $name = 'Holidays';

    /**
     * Index
     *
     * @return void
     * @access public
     */
    function index($company_id = null, $country_id = null) {
		$companies = array();
        if ($this->is_sas && empty($company_id)) {
            $companies = $this->Holiday->Company->find('list');
            $this->viewPath = 'holidays' . DS . 'list';
			$this->set(compact('companies'));
        } else {
            $companyName = $this->_getCompany($company_id);
            if (empty($companyName)) {
                $this->Session->setFlash(sprintf(__('The company "#%s" was not found, please try again', true), $company_id), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $company_id = $companyName['Company']['id'];
            $conditions = array('company_id' => $company_id);
            $this->loadModel('Company');
            $mutil_country = $this->Company->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'multi_country'),
                'conditions' => array('Company.id' => $company_id)
            ));

            $mutil_country = !empty($mutil_country) ? $mutil_country['Company']['multi_country'] : 0;
            list($_start, $_end) = $this->_parseParams();
            $company_id = $companyName['Company']['id'];
            $typeSelect = $country_id;
            if(empty($country_id)){
                $this->loadModel('Country');
                $country_id = $this->Country->find('first', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('id')
                ));
                $country_id = !empty($country_id) ? $country_id['Country']['id'] : 0;
            }
            if($mutil_country){
                $holidays = $this->Holiday->getOptions($_start, $_end, $company_id, $country_id);
                $workdays = ClassRegistry::init('Workday')->getOptions($company_id, $country_id);
            }else{
                $holidays = $this->Holiday->getOptions($_start, $_end, $company_id);
                $workdays = ClassRegistry::init('Workday')->getOptions($company_id);
            }
            $constraint = ClassRegistry::init('ResponseConstraint')->getOptions($company_id);

            $this->loadModel('Country');
            $list_country = $this->Country->find('list', array(
                'recursive' => -1,
                'fields' => array('id', 'name'),
                'conditions' => array('company_id' => $company_id)
            ));

			$this->set(compact('holidays', 'company_id', 'companyName', 'workdays', 'constraint', 'mutil_country', 'list_country', 'typeSelect', 'country_id'));
        }
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    public function update($company_id) {
        $success = 0;
        $warning = array();
        $this->layout = false;
        $this->Holiday->cacheQueries = false;
        $this->loadModel('TmpStaffingSystem');
        $this->loadModel('ActivityRequest');
        $this->loadModel('ActivityRequestConfirm');
        $this->loadModel('AbsenceRequest');
        $this->loadModel('CompanyEmployeeReference');
        $this->loadModel('Company');
        $mutil_country = $this->Company->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'multi_country'),
            'conditions' => array('Company.id' => $company_id)
        ));
		
		$max_date_request = $this->ActivityRequest->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'company_id' => $company_id,
				'status' => 2,
			),
			'fields' => array('FROM_UNIXTIME(MAX(`date`), "%Y") as max_date')
		));
		
		$max_date_request = !empty($max_date_request) ? $max_date_request[0]['max_date'] : date('Y', time());
		
        $mutil_country = !empty($mutil_country) ? $mutil_country['Company']['multi_country'] : 0;
        if ( !empty($this->data) && isset($this->data['request']) && $this->_getCompany($company_id) ) {
            if (!$this->is_sas) {
                $company_id = $this->employee_info['Company']['id'];
            }
            $request = $this->data['request'];
            unset($this->data['request']);
            if ($request == 0) {
                foreach ($this->data as &$data) {
                    if (!empty($data['date'])) {
						$country_id = $mutil_country ? $data['country_id'] : 0;
                        $conditions = array(
                            'repeat' => 1,
                            'company_id' => $company_id,
                            'FROM_UNIXTIME(date, "%d-%m")' => date('d-m', $data['date'])
                        );
                        $_conditions = array(
                            'Holiday.repeat IS NULL',
                            'company_id' => $company_id
                        );
						if(!(!empty($data['am']) && !empty($data['pm']))){
							if(!empty($data['am'])){
								 $conditions['time'] = 0;
							}
							
							if(!empty($data['pm'])){
								 $conditions['time'] = 1;
							}
						}
                        if($country_id == 0){
                            $conditions['OR'] = array(
                                array('country_id is NULL'),
                                array('country_id' => 0)
                            );
                            $_conditions['OR'] = array(
                                array('country_id is NULL'),
                                array('country_id' => 0)
                            );
                        } else {
                            $conditions['country_id'] = $country_id;
                            $_conditions['country_id'] = $country_id;
                        }
                        /**
                         * kiem tra xem ngay nay trong nam co repeat khong?
                         */
                        $checkRepeats = $this->Holiday->find('list', array(
                            'recursive' => -1,
                            'fields' => array('id','date'),
                            'conditions' => $conditions
                        ));
                        if(!empty($checkRepeats)){
                            $start = min($checkRepeats);
							if(!$this->is_sas){
								$list_future_date = array();
								$list_future_date[] = $data['date'];
								if(date('Y',$data['date']) < $max_date_request){
									$s_date = date('Y',$data['date']);
									while ($s_date <= $max_date_request) {
										$list_future_date[] = strtotime(date('d-m-',$data['date']) . $s_date);
										$s_date = $s_date + 1;
									}
								}
								$checkRequests = $this->ActivityRequest->find('count',array(
									'recursive' => -1,
									'conditions' => array(
										'company_id' => $company_id,
										'status' => 2,
										'date' => $list_future_date
									)
								));
								if($checkRequests != 0){
									$warning[] = 'A timesheet has been already validated';
									break;
								}
							}
                            while ($start < $data['date']) {
                                foreach (array('am', 'pm') as $type) {
                                    if(!empty($data[$type])){
                                        $_conditions['date'] = $start;
                                        $_conditions['time'] = ($type == 'pm') ? '1' : '0';
                                        $checkDate = $this->Holiday->find('first', array(
                                            'recursive' => -1,
                                            'fields' => array('id'),
                                            'conditions' => $_conditions
                                        ));
                                        if(!empty($checkDate)){
                                            //do nothing
                                        } else {
                                            $save = array(
                                                'date' => $start,
                                                'company_id' => $company_id,
                                                'time' => ($type == 'pm') ? '1' : '0',
                                                'repeat' => '',
                                                'country_id' => $country_id
                                            );
                                            $this->Holiday->create();
                                            $this->Holiday->save($save);
                                        }
                                    }
                                }
                                $start = mktime(0, 0, 0, date('m', $start), date('d', $start), date('Y', $start)+1);
                            }
                            $data['result'] = (bool) $this->Holiday->deleteAll(array('Holiday.id' => array_keys($checkRepeats)), false);
                            if(!empty($data['result'])){
                                $success++;
                            }
                        } else{
							if(!$this->is_sas){
								$checkRequests = $requestConfirm = 0;
								$checkRequests = $this->ActivityRequest->find('count',array(
									'recursive' => -1,
									'conditions' => array(
										'company_id' => $company_id,
										'status' => 2,
										'date' => $data['date']
									)
								));
								
								$_start = strtotime('last monday', $data['date']);
								$_end = strtotime('next sunday', $data['date']);
								$requestConfirm = $this->ActivityRequestConfirm->find('count', array(
									'recursive' => -1,
									'conditions' => array(
										'company_id' => $company_id,
										'start' => $_start,
										'end' => $_end,
										'status' => 2
									)
								));
								if($checkRequests != 0 || $requestConfirm != 0){
									$warning[] = 'A timesheet has been already validated for this day';
									break;
								}
							}
                            $success = $this->_remove($company_id);
                        }
                    }
                }	
            } else {
                /**
                 * Lay employee cua cong ty
                 */
                $employOfCompanies = $this->CompanyEmployeeReference->find('list', array(
                    'recursive' => -1,
                    'conditions' => array('company_id' => $company_id),
                    'fields' => array('employee_id', 'employee_id')
                ));
                foreach ($this->data as &$data) {
                    // kiem tra country_id
                    $country_id = $mutil_country ? $data['country_id'] : 0;
                    $conditions = array(
                        'Holiday.repeat' => 1,
                        'company_id' => $company_id,
                        'FROM_UNIXTIME(date, "%d-%m")' => date('d-m', $data['date'])
                    );
					if(!(!empty($data['am']) && !empty($data['pm']))){
						if(!empty($data['am'])){
							 $conditions['time'] = 0;
						}
						
						if(!empty($data['pm'])){
							 $conditions['time'] = 1;
						}
					}
                    if($country_id == 0){
                        $conditions['OR'] = array(
                            array('country_id is NULL'),
                            array('country_id' => 0)
                        );
                    } else {
                        $conditions['country_id'] = $country_id;
                    }
                    if (!empty($data['date'])) {
                        $checkRepeats = $this->Holiday->find('all', array(
                            'recursive' => -1,
                            'fields' => array('id','date', 'time'),
                            'conditions' => $conditions
                        ));
						$change_repeat_holiday = false;
                        if(!empty($checkRepeats) && $request == 2){
							$change_repeat_holiday = true;
                        }
						
						$checkRequests = $requestConfirm = 0;
						$checkRequests = $this->ActivityRequest->find('count',array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'status' => 2,
								'date' => $data['date']
							)
						));
						/**
						 * Check ActivityRequestConfirm
						 */
						$_start = strtotime('last monday', $data['date']);
						$_end = strtotime('next sunday', $data['date']);
						$requestConfirm = $this->ActivityRequestConfirm->find('count', array(
							'recursive' => -1,
							'conditions' => array(
								'company_id' => $company_id,
								'start' => $_start,
								'end' => $_end,
								'status' => 2
							)
						));
						if(!$this->is_sas){		
							if($checkRequests != 0 || $requestConfirm != 0){
								$warning[] = 'A timesheet has been already validated for this day';
								break;
							}
						}
                        //END
                        //CHECK STAFFING
                        $this->TmpStaffingSystem->checkStaffingByDate($data['date'],$company_id,true);
                        //END
                        /**
                         * Kiem tra neu tuan/thang da validation timesheet thi khong cho set holiday
                         * Neu chua validation timesheet thi reject ngay set holidays
                         */
                        if($request == 1){
                            if(!empty($checkRepeats)){
                                if($country_id != 0){
                                    $_conditions = array(
                                        'Holiday.repeat IS NULL',
                                        'company_id' => $company_id,
                                        'country_id' => $country_id
                                    );
                                } else {
                                    $_conditions = array(
                                        'Holiday.repeat IS NULL',
                                        'company_id' => $company_id,
                                        'OR' => array(
                                            array('country_id is NULL'),
                                            array('country_id' => 0)
                                        )
                                    );
                                }
                                $start = min($checkRepeats);
								if(!$this->is_sas){
									$list_future_date = array();
									$list_future_date[] = $data['date'];
									if(date('Y',$data['date']) < $max_date_request){
										$s_date = date('Y',$data['date']);
										while ($s_date <= $max_date_request) {
											$list_future_date[] = strtotime(date('d-m-',$data['date']) . $s_date);
											$s_date = $s_date + 1;
										}
									}
									$checkRequests = $this->ActivityRequest->find('count',array(
										'recursive' => -1,
										'conditions' => array(
											'company_id' => $company_id,
											'status' => 2,
											'date' => $list_future_date
										)
									));
									if($checkRequests != 0){
										$warning[] = 'A timesheet has been already validated';
										break;
									}
								}
                                while ($start <= $data['date']) {
                                    foreach (array('am', 'pm') as $type) {
                                        $_conditions['date'] = $start;
                                        $_conditions['time'] = ($type == 'pm') ? '1' : '0';
                                        if(!empty($data[$type])){
                                            $checkDate = $this->Holiday->find('first', array(
                                                'recursive' => -1,
                                                'fields' => array('id'),
                                                'conditions' => $_conditions
                                            ));
                                            if(!empty($checkDate)){
                                                //do nothing
                                            } else {
                                                $save = array(
                                                    'date' => $start,
                                                    'company_id' => $company_id,
                                                    'time' => ($type == 'pm') ? '1' : '0',
                                                    'repeat' => '',
                                                    'country_id' => $country_id
                                                );
                                                $this->Holiday->create();
                                                $this->Holiday->save($save);
                                            }
                                        }
                                    }
                                    $start = mktime(0, 0, 0, date('m', $start), date('d', $start), date('Y', $start)+1);
                                }
                                $data['result'] = (bool) $this->Holiday->deleteAll(array('Holiday.id' => array_keys($checkRepeats)), false);
                                if(!empty($data['result'])){
                                    $success++;
                                }
                            } else{
                                foreach (array('am', 'pm') as $type) {
                                    $save = array(
                                        'date' => $data['date'],
                                        'company_id' => $company_id,
                                        'time' => ($type == 'pm') ? '1' : '0',
                                        'country_id' => $country_id
                                    );
                                    if (!empty($data[$type])) {
                                        $last = $this->Holiday->find('first', array('recursive' => -1, 'fields' => array('id'),
                                            'conditions' => $save));
                                        if ($last) {
                                            $this->Holiday->id = $last['Holiday']['id'];
                                        } else {
                                            $this->Holiday->create();
                                        }
                                        $save['repeat'] = '';
                                        if (!($data['result'] = (bool) $this->Holiday->save($save))) {
                                            break;
                                        } else {
											if($checkRequests == 0 || $requestConfirm == 0){
												/**
												 * Reject tat ca cac absence
												 */
												$this->AbsenceRequest->deleteAll(array('AbsenceRequest.date' => $data['date'], 'AbsenceRequest.employee_id' => $employOfCompanies), false);
												/**
												 * Reject tat ca cac timesheet
												 */
												$this->ActivityRequest->deleteAll(array('ActivityRequest.date' => $data['date'], 'ActivityRequest.employee_id' => $employOfCompanies, 'ActivityRequest.company_id' => $company_id), false);
											}
                                        }
                                    }
                                }
                                if (!empty($data['result'])) {
                                    $success++;
                                }
                            }
                        } else {
							if(!$this->is_sas){
								$list_future_date = array();
								$list_future_date[] = $data['date'];
								if(date('Y',$data['date']) < $max_date_request){
									$s_date = date('Y',$data['date']);
									while ($s_date <= $max_date_request) {
										$list_future_date[] = strtotime(date('d-m-',$data['date']) . $s_date);
										$s_date = $s_date + 1;
									}
								}
								$checkRequests = $this->ActivityRequest->find('count',array(
									'recursive' => -1,
									'conditions' => array(
										'company_id' => $company_id,
										'status' => 2,
										'date' => $list_future_date
									)
								));
								if($checkRequests != 0){
									$warning[] = 'A timesheet has been already validated';
									break;
								}
							}
							$checkRepeats = !empty($checkRepeats) ? Set::combine($checkRepeats, '{n}.Holiday.id', '{n}.Holiday') : array();
                            foreach (array('am', 'pm') as $type) {
                                $save = array(
                                    'date' => $data['date'],
                                    'company_id' => $company_id,
                                    'time' => ($type == 'pm') ? '1' : '0',
                                    'country_id' => $country_id
                                );
								if(!empty($checkRepeats) && $request == 2){
									foreach($checkRepeats as $id => $repeatValue){
										if($repeatValue['time'] == $save['time'] && date('d-m', $save['date']) == date('d-m', $repeatValue['date'])){
											if( $save['date'] < $repeatValue['date'] ){
												// delete the future day is repeat holiday.
												// and add new repeat holiday to a day submited in bellow
												$this->Holiday->delete($id);
												
											}else{
												$warning[] = 'The holiday repeat can not be changed.';
												break;
											}
										}
									}
								}
								
								if (!empty($data[$type])) {
									$last = $this->Holiday->find('first', array('recursive' => -1, 'fields' => array('id'),
										'conditions' => $save));
									if ($last) {
										$this->Holiday->id = $last['Holiday']['id'];
									} else {
										$this->Holiday->create();
									}
									if ($request == 2) {
										$save['repeat'] = 1;
									} else {
										$save['repeat'] = '';
									}
									if (!($data['result'] = (bool) $this->Holiday->save($save))) {
										break;
									} else {
										if($checkRequests == 0 || $requestConfirm == 0){
											/**
											 * Reject tat ca cac absence
											 */
											$this->AbsenceRequest->deleteAll(array('AbsenceRequest.date' => $data['date'], 'AbsenceRequest.employee_id' => $employOfCompanies), false);
											/**
											 * Reject tat ca cac timesheet
											 */
											$this->ActivityRequest->deleteAll(array('ActivityRequest.date' => $data['date'], 'ActivityRequest.employee_id' => $employOfCompanies, 'ActivityRequest.company_id' => $company_id), false);
										}
									}
								}
										
                            }
                            if (!empty($data['result'])) {
                                $success++;
                            }
                        }
                    }
                }
            }
            if ($success == count($this->data)) {
                $this->Session->setFlash(__('The data has been saved.', true), 'success');
            } else {
                if(!empty($warning))
                {
                    $this->Session->setFlash(__($warning[0], true), 'warning');
                }
                else
                {
                    $this->Session->setFlash(__('Some data could not be saved because invalidate.', true), 'warning');
                }
            }
        } else {
            $this->Session->setFlash(__('The data has been submited to server is invaild.', true), 'error');
        }
        $this->set(compact('result'));
    }

    /**
     * view
     *
     * @return void
     * @access public
     */
    protected function _remove($company_id) {
        $success = 0;
        $this->loadModel('TmpStaffingSystem');
        foreach ($this->data as &$data) {
            if (!empty($data['date'])) {
                //CHECK STAFFING
                $this->TmpStaffingSystem->checkStaffingByDate($data['date'],$company_id,true);
                //END
                foreach (array('am', 'pm') as $type) {
                    $save = array(
                        'date' => $data['date'],
                        'company_id' => $company_id,
                        'time' => ($type == 'pm') ? '1' : '0'
                    );
                    if (!empty($data[$type])) {
                        $last = $this->Holiday->find('first', array('recursive' => -1, 'fields' => array('id'),
                            'conditions' => $save));
                        if ($last) {
                            if (!($data['result'] = (bool) $this->Holiday->delete($last['Holiday']['id']))) {
                                break;
                            }
                        }
                    }
                }
                if (!empty($data['result'])) {
                    $success++;
                }
            }
        }
        return $success;
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
        $companyName = $this->Holiday->Company->find('first', array('recursive' => -1
            , 'conditions' => array('id' => $company_id)));
        if (empty($companyName)) {
            return false;
        }
        return $companyName;
    }

    /**
     * Get Company By ID
     *
     * @return void
     * @access protected
     */
    function _parseParams() {
        $params = array('year' => 0, 'month' => 0, 'week' => 0);
        foreach (array_keys($params) as $type) {
            if (!empty($this->params['url'][$type])) {
                $params[$type] = intval($this->params['url'][$type]);
            }
        }
        if ($params['week'] && $params['year']) {
            $week = intval(date('W', mktime(0, 0, 0, 12, 31, $params['year'])));
            if (($week == 1 && $params['week'] <= 53) || ($week != 1 && $params['week'] <= $week)) {
                $date = new DateTime();
                $date->setISODate($params['year'], $params['week']);
                $date = strtotime($date->format('Y-m-d'));
            }
        } elseif ($params['month'] && $params['year']) {
            $date = mktime(0, 0, 0, $params['month'], 1, $params['year']);
        }
        if (empty($date)) {
            if (!empty($this->params['url']['month']) || !empty($this->params['url']['week']) || !empty($this->params['url']['year'])) {
                $this->Session->setFlash(__('The data was not found, please try again', true), 'error');
                $this->redirect(array('action' => 'index'));
            }
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));
        }
        $_start = (date('w', $date) == 1) ? $date : strtotime('next monday', $date);
        $_end = strtotime('next sunday', $_start);

        $this->set(compact('_start', '_end'));

        return array($_start, $_end);
    }


    function manage($year = null){
        if( !$year )$year = date('Y');
        $company_id = $this->employee_info['Company']['id'];
        $holidays = $this->Holiday->getYear($company_id, $year);
        $workdays = ClassRegistry::init('Workday')->getOptions($company_id);
        $constraint = ClassRegistry::init('ResponseConstraint')->getOptions($company_id);
        $this->set(compact('year', 'company_id', 'holidays', 'constraint', 'workdays'));
    }

    function get($start, $end){
        $holidays = $this->Holiday->get($this->employee_info['Company']['id'], strtotime($start), strtotime($end), 'd-m-Y');
        die(json_encode($holidays));
    }

    function getYear($year){
        $start = strtotime($year . '-01-01 00:00:00');
        $end = strtotime($year . '-12-31 00:00:00');
        $holidays = $this->Holiday->get($this->employee_info['Company']['id'], $start, $end, 'd-m-Y');
        die(json_encode(array_values($holidays)));
    }
}