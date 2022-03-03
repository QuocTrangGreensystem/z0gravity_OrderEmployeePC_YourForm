<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class AbsenceRequest extends AppModel {

    /**
     * Name of the model.
     *
     * @var ResponseConstraint
     */
    protected $_Constraint = null;

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'AbsenceRequest';

    /**
     * List of validation rules.
     *
     * @var array
     */
    public $validate = array(
        'date' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The date is not blank!'
            ),
        ),
        'employee_id' => array(
            'notempty' => array(
                'allowEmpty' => false,
                'rule' => array('notempty'),
                'message' => 'The Employee is not blank!'
            )
        )
    );
    /**
     * I18n validate
     *
     * @param string $field The name of the field to invalidate
     * @param mixed $value Name of validation rule that was not failed, or validation message to
     *    be returned. If no validation key is provided, defaults to true.
     *
     * @return void
     */
    public function invalidate($field, $value = true) {
        $value = __($value, true);
        parent::invalidate($field, $value);
    }

    /**
     *  Detailed list of belongsTo associations.
     *
     * @var array
     */
    public $belongsTo = array(
        'Employee' => array(
            'className' => 'Employee',
            'foreignKey' => 'employee_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'AbsenceAm' => array(
            'className' => 'Absence',
            'foreignKey' => 'absence_am',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'AbsencePm' => array(
            'className' => 'Absence',
            'foreignKey' => 'absence_pm',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    /**
     *  Get ResponseConstraint.
     *
     * @var ResponseConstraint
     */
    public function getConstraint() {
        if (!$this->_Constraint) {
            $this->_Constraint = ClassRegistry::init('ResponseConstraint');
        }
        return $this->_Constraint;
    }

    /**
     *  Get ResponseConstraint.
     *
     * @var ResponseConstraint
     */
    public function getAbsences($company_id, $employee_id = null, $startDate = null) {
        $this->AbsenceAm->EmployeeAbsence->cacheQueries = $this->AbsenceAm->cacheQueries = true;
        $default = Set::combine($this->AbsenceAm->find('all', array(
                            'recursive' => -1, 'conditions' => array('company_id' => $company_id, 'display' => true),
                            'order' => array('weight' => 'ASC')
                            )), '{n}.AbsenceAm.id', '{n}.AbsenceAm');
        if (empty($employee_id)) {
            return $default;
        }
        return $this->getEmployeeAbsences($employee_id, $default, $startDate, false);
    }
	/**
	* Get absence group by employee and date
	* INPUT : start date, end date, type (default = validated)
	* OUTPUT : Array [employee](time=>total absence)
	* @author : ViN
	*/
	public function sumAbsenceByEmployeeAndDate($employeeIds, $startDate, $endDate, $type = 'validated', $dateType = 'month')
	{
		if($dateType == 'day')
		{
			$dateType = "%d_%m_%Y";
			$dateTypeResult = "%d_%m_%Y";
		}
		elseif($dateType == 'week')
		{
			$dateType = "%v_%Y";
			$dateTypeResult = "%v_%Y";
		}
		else
		{
			$dateType = "01_%m_%Y";
			$dateTypeResult = "%m_%Y";
		}
		$datas = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employeeIds,
				'date BETWEEN ? AND ?' 	=> array($startDate, $endDate)
			),
			'fields' => array(
				//'employee_id', 'UNIX_TIMESTAMP(FROM_UNIXTIME(`date`, "%Y%m01")) as date',
				'employee_id', 'FROM_UNIXTIME(`date`, "'.$dateType.'") as date',
				'SUM(CASE WHEN `response_am` = "'.$type.'" AND `response_pm` = "'.$type.'" THEN 1 WHEN `response_am` = "'.$type.'" AND `response_pm` <> "'.$type.'" THEN 0.5 WHEN `response_am` <> "'.$type.'" AND `response_pm` = "'.$type.'" THEN 0.5 ELSE 0 END) AS val',
			),
			'group' => array('employee_id','FROM_UNIXTIME(`date`, "'.$dateTypeResult.'")')
		));
		$results = array();
		foreach( $datas as $key=>$data)
		{
			$results[$data['AbsenceRequest']['employee_id']][$data[0]['date']] = $data[0]['val'];
		}
		return $results;
	}
	/**
	* Get absence group by date for PC
	* INPUT : start date, end date, type (default = validated)
	* OUTPUT : Array(time=>total absence)
	* @author : ViN
	*/
	public function sumAbsenceByDateForPC($employeeIds, $startDate, $endDate, $type = 'validated', $dateType = 'month')
	{
		if($dateType == 'day')
		{
			$dateType = "%d_%m_%Y";
			$dateTypeResult = "%d_%m_%Y";
		}
		elseif($dateType == 'week')
		{
			$dateType = "%W%Y";
			$dateTypeResult = "%W_%Y";
		}
		else
		{
			$dateType = "01_%m_%Y";
			$dateTypeResult = "%m_%Y";
		}
		$datas = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'employee_id' => $employeeIds,
				'date BETWEEN ? AND ?' 	=> array($startDate, $endDate)
			),
			'fields' => array(
				//'employee_id', 'UNIX_TIMESTAMP(FROM_UNIXTIME(`date`, "%Y%m01")) as date',
				'employee_id', 'FROM_UNIXTIME(`date`, "'.$dateType.'") as date',
				'SUM(CASE WHEN `response_am` = "'.$type.'" AND `response_pm` = "'.$type.'" THEN 1 WHEN `response_am` = "'.$type.'" AND `response_pm` <> "'.$type.'" THEN 0.5 WHEN `response_am` <> "'.$type.'" AND `response_pm` = "'.$type.'" THEN 0.5 ELSE 0 END) AS val',
			),
			'group' => array('FROM_UNIXTIME(`date`, "'.$dateTypeResult.'")')
		));
		$results = array();
		foreach( $datas as $key=>$data)
		{
			$results[$data[0]['date']] = $data[0]['val'];
		}
		return $results;
	}
    /**
     * $startDate: array or bien
     */
    function getEmployeeAbsences($employee, $default, $startDate = null, $strict = true) {
        $d = $this->AbsenceAm->EmployeeAbsence->find('all', array(
                            'fields' => array('total', 'begin', 'absence_id', 'year'),
                            'recursive' => -1, 'conditions' => array(
                                'year' => $startDate,
                                'employee_id' => $employee, 'absence_id' => array_keys($default))));

        $absences = Set::combine($d, '{n}.EmployeeAbsence.absence_id', '{n}.EmployeeAbsence');
        if ($strict && empty($absences)) {
            return null;
        }
        foreach ($default as $key => &$absence) {
            $absence['count'] = array();
            if (isset($absences[$key])) {
                unset($absences[$key]['begin']);
                $absence = array_merge($absence, $absences[$key]);
            }
            if ($absence['begin'] && $absence['begin'] != '0000-00-00') {
                $absence['begin'] = substr($absence['begin'], 5);
            } else {
                $absence['begin'] = '';
            }
        }
        return $default;
    }

    public function caculateRequestOfEmployee($employee = null, $dates = null){
        /**
         * Lay company cua employee
         */
        $CompanyEmployeeReference = ClassRegistry::init('CompanyEmployeeReference')->find('first', array(
            'recursive' => -1,
            'conditions' => array('employee_id' => $employee),
            'fields' => array('company_id')
        ));
        $companyId = !empty($CompanyEmployeeReference['CompanyEmployeeReference']['company_id']) ? $CompanyEmployeeReference['CompanyEmployeeReference']['company_id'] : 0;
        /**
         * Lay profit center
         */
        $profitCenters = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0),
                'employee_id' => $employee
            ),
            'fields' => array('employee_id', 'profit_center_id'),
            'order' => array('employee_id')
        ));
        $profitId = !empty($profitCenters['ProjectEmployeeProfitFunctionRefer']['profit_center_id']) ? $profitCenters['ProjectEmployeeProfitFunctionRefer']['profit_center_id'] : 0;
        $listEmployees = array();
        if($profitId != 0){
            $listEmployees = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
                'recursive' => -1,
                'conditions' => array(
                    'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0),
                    'profit_center_id' => $profitId
                ),
                'fields' => array('employee_id', 'profit_center_id'),
                'order' => array('employee_id')
            ));
        }
        $dates = date('m-Y', $dates);
        $startDate = strtotime('01-'.$dates);
        $endDate = strtotime('31-'.$dates);
        $requestQuery = array();
        if(!empty($listEmployees)){
            $_listEmployees = array_keys($listEmployees);
            $requestQuery = ClassRegistry::init('AbsenceRequest')->find(
    			"all",
    			array(
    				'recursive' 	=> -1,
                    'conditions' => array(
                        'date BETWEEN ? AND ?' 	=> array($startDate, $endDate),
                        'employee_id' => $_listEmployees
                    ),
                    'fields' => array('employee_id', 'date', 'absence_am', 'absence_pm', 'response_am', 'response_pm')
    			)
    		);
        }
        $totalAbcenses = array();
        foreach($requestQuery as $request){
            foreach(array('am', 'pm') as $value){
                if($request['AbsenceRequest']['absence_' . $value] && $request['AbsenceRequest']['response_' . $value] == 'validated'){
                    $_dates = date('m-Y', $request['AbsenceRequest']['date']);
                    $dates = strtotime('01-'.$_dates);
                    if(!isset($totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates])){
                        $totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates] = 0;
                    }
                    $totalAbcenses[$request['AbsenceRequest']['employee_id']][$dates] += 0.5;
                }
            }
        }
        $references = ClassRegistry::init('ProjectEmployeeProfitFunctionRefer')->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'NOT' => array('profit_center_id' => null, 'profit_center_id' => 0)
            ),
            'fields' => array('employee_id', 'profit_center_id'),
            'order' => array('employee_id')
        ));
        $datas = $idProfits = array();
        if(!empty($totalAbcenses)){
            foreach($totalAbcenses as $employ => $totalAbcense){
                foreach($totalAbcense as $time => $values){
                    $_datas = array(
                        'profit_center_id' => !empty($references[$employ]) ? $references[$employ] : 0,
                        'employee_id' => $employ,
                        'date' => $time,
                        'total_absence' => $values
                    );
                    $datas[] = $_datas;
                }
            }
        }
        $TmpCaculateAbsence = ClassRegistry::init('TmpCaculateAbsence');
        if(!empty($datas)){
            foreach($datas as $data){
                $tmps = $TmpCaculateAbsence->find('first', array(
                    'recursive' => -1,
                    'conditions' => array(
                        'profit_center_id' => $data['profit_center_id'],
                        'employee_id' => $data['employee_id'],
                        'date' => $data['date'],
                        'company_id' => $companyId
                    ),
                    'fields' => array('id')
                ));
                if(!empty($tmps) && $tmps['TmpCaculateAbsence']['id']){
                    $saved['total_absence'] = $data['total_absence'];
                    $TmpCaculateAbsence->id = $tmps['TmpCaculateAbsence']['id'];
                    $TmpCaculateAbsence->save($saved);
                } else{
                    $saved['profit_center_id'] = $data['profit_center_id'];
                    $saved['employee_id'] = $data['employee_id'];
                    $saved['date'] = $data['date'];
                    $saved['total_absence'] = $data['total_absence'];
                    $saved['company_id'] = $companyId;
                    $TmpCaculateAbsence->create();
                    $TmpCaculateAbsence->save($saved);
                }
            }
        } else {
            $_dates = strtotime('01-'.$dates);
            $deleteDatas = $TmpCaculateAbsence->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'employee_id' => $employee,
                    'date' => $_dates,
                    'company_id' => $companyId
                ),
                'fields' => array('id')
            ));
            if(!empty($deleteDatas)){
                foreach($deleteDatas as $deleteData){
                    $TmpCaculateAbsence->delete($deleteData['TmpCaculateAbsence']['id']);
                }
            }
        }
    }

}
