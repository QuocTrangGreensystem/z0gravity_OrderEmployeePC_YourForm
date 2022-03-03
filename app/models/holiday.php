<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class Holiday extends AppModel {

    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'Holiday';

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
        'company_id' => array(
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
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getOptions($start, $end, $company_id, $country_id = false, $cal = false) {
        $conditions = array(
            'company_id' => $company_id,
            'OR' => array(
               '`date` BETWEEN ? AND ?' => array($start, $end),
               'AND' => array(
                   'Holiday.repeat' => 1,
                   'FROM_UNIXTIME(date, "%Y") <=' => date('Y', $start)
               )
            )
        );
        if($country_id){
            $conditions['country_id'] = $country_id;
        } else {
            $conditions['AND'] = array(
                'OR' => array(
                    'Holiday.country_id' => 0,
                    'Holiday.country_id IS NULL'
                )
            );
        }
        $_holidays = $this->find("all", array(
            'recursive' => -1,
            "conditions" => $conditions,
            'fields' => array('date', 'time', 'repeat')
        ));
        $holidays = array();
        if(!empty($_holidays)){
            $dayOffs = array();
            foreach($_holidays as $_holiday){
                $dx = $_holiday['Holiday'];
                $date = date('d-m', $dx['date']);
                $time = !empty($dx['time']) ? 'pm' : 'am';
                if(!isset($dayOffs[$date][$time])){
                    $dayOffs[$date][$time] = 0;
                }
                $dayOffs[$date][$time] = !empty($dx['repeat']) ? $dx['repeat'] : 0;
            }
            if(!empty($start) && !empty($end)){
                while($start <= $end){
                    $sDay = date('d-m', $start);
                    if(in_array($sDay, array_keys($dayOffs))){
                        $holidays[$start] = $dayOffs[$sDay];
                    }
                    $start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
                }
            }
        }
        return $holidays;
    }

    /**
     * Modify function get data holidays
     * @author HuuPC
     */
    public function getOptionHolidays($start, $end, $company_id) {
        return $this->getOptions($start, $end, $company_id);
        $conditions = array('date BETWEEN ? AND ?' => array($start, $end));
        if (date('Y') != date('Y', $end)) {
            $conditions = array_merge($conditions, array(
                array('repeat BETWEEN ? AND ?' => array(strtotime('1980-' . date('m-d', $start)), strtotime('1980-12-31'))),
                array('repeat BETWEEN ? AND ?' => array(strtotime('1980-1-1'), strtotime('1980-' . date('m-d', $end))))));
        } else {
            $conditions[] = array(
                'repeat BETWEEN ? AND ?' => array(strtotime('1980-' . date('m-d', $start)), strtotime('1980-' . date('m-d', $end)))
            );
        }
        $_holidays = $this->find("all", array(
            'recursive' => -1,
            "conditions" => array(
                'or' => $conditions, 'company_id' => $company_id)));
        $setHolidays = array();
        if(!empty($_holidays)){
            foreach($_holidays as $key => $_holiday){
                $dx = $_holiday['Holiday'];
                if(!empty($dx['repeat'])){
                    $_date = date('d-m', $dx['repeat']);
                    $minYear = !empty($start) ? date('Y', $start) : '';
                    $maxYear = !empty($end) ? date('Y', $end) : '';
                    while($minYear <= $maxYear){
                        $tmp = array(
                            'date' => strtotime($_date.'-'.$minYear),
                            'time' => $dx['time']
                        );
                        $setHolidays[] = $tmp;
                        $minYear++;
                    }
                } else {
                    $tmp = array(
                        'date' => $dx['date'],
                        'time' => $dx['time']
                    );
                    $setHolidays[] = $tmp;
                }
            }
        }
        $holidays = array();
        if(!empty($setHolidays)){
            foreach($setHolidays as $setHoliday){
                $date = $setHoliday['date'];
                $time = $setHoliday['time'] ? 'pm' : 'am';
                if (!isset($holidays[$date][$time])) {
                    $holidays[$date][$time] = 0;
                }
                $holidays[$date][$time] += 0.5;
            }
        }
        return $holidays;
    }
    /*FUNCTION GET LIST MONTH AND YEAR BETWEEN TIME*/
    /*CREATED BY VINGUYEN 04/02/2014*/
    public function getArrayMonthsBetweenTime($startDate,$endDate)
    {
        $_start = date('Y-m-d',$startDate);
        $_end = date('Y-m-d',$endDate);
        $start    = new DateTime($_start);
        $start->modify('first day of this month');
        $end      = new DateTime($_end);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $arrayMonths = array();
        $arrayYears = array();
        foreach ($period as $dt) {
            $_year = $dt->format("Y");
            $arrayYears[$_year] = $_year;
            if(!isset($arrayMonths[$_year])) $arrayMonths[$_year] = array();
            $arrayMonths[$_year][] = $dt->format("2012-m");
        }
        return array($arrayYears,$arrayMonths);
    }
    /*FUNCTION GET HOLIDAYS - ONLY ALLOW HOLIDAY FULL DAY (NOT AM-PM)*/
    /*CREATED BY VINGUYEN 04/02/2014*/
    public function getHolidaysBetweenTime($company,$startDate,$endDate)
    {
        list($arrayYears,$arrayMonths) = $this->getArrayMonthsBetweenTime($startDate, $endDate);
        $_holidaysRepeats = array();
        foreach($arrayYears as $val)
        {
            $_holidaysRepeat[$val]=$this->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'repeat <>' => null,
                    'time <>' => 0,
                    'company_id' => $company,
                    "FROM_UNIXTIME(Holiday.date, '%Y-%m')" => $arrayMonths[$val]
                ),
                'fields' => array('DISTINCT FROM_UNIXTIME(date, "%d-%m-'.$val.'") AS date')
            ));
            $_holidaysRepeat[$val] = Set::classicExtract($_holidaysRepeat[$val],'{n}.0.date');
            $_holidaysRepeats = array_merge($_holidaysRepeats,$_holidaysRepeat[$val]);
        }
        $_holidaysNorepeats=$this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'repeat' => null,
                'time <>' => 0,
                'date BETWEEN ? AND ?' => array($startDate, $endDate),
                'company_id' => $company
            ),
            'fields' => array('DISTINCT FROM_UNIXTIME(date, "%d-%m-%Y") AS date')
        ));
        $_holidaysNorepeats = Set::classicExtract($_holidaysNorepeats,'{n}.0.date');
        $totalHoliday = array_merge($_holidaysRepeats,$_holidaysNorepeats);
        $totalHoliday = array_unique($totalHoliday);
        $holidays = array();
        $default = array(
            'am' => 0.5,
            'pm' => 0.5
        );
        $_holidaysRepeats = array_flip($_holidaysRepeats);
        foreach($totalHoliday as $_date)
        {
            $_dateStrToTime = strtotime($_date);
            if( $_dateStrToTime >= $startDate && $_dateStrToTime <= $endDate )
            {
                $holidays[$_dateStrToTime] = $default;
            }
            if(isset($_holidaysRepeats[$_date]))
            {
                $holidays[$_dateStrToTime]['repeat'] = true;
            }
        }
        return $holidays;
    }

    /*FUNCTION GET HOLIDAYS - ONLY ALLOW HOLIDAY FULL DAY (NOT AM-PM)*/
    /*CREATED BY VINGUYEN 06/08/2014*/
    public function getHolidaysInMonth($company,$startDate,$endDate,$returnCount=true)
    {
        /*Ham nay khong select count (*) ma viet theo kieu nay nham muc dich de phong sau nay co yeu cau holiday tinh theo AM-PM*/
		$results = array();
		while($startDate < $endDate){
			$_month=date("m", $startDate);
			$_year=date("Y", $startDate);
			$_holidaysRepeat=$this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'repeat <>' => null,
					'time <>' => 0,
					'company_id' => $company,
					'FROM_UNIXTIME(date, "%m")' => $_month
				),
				'fields' => array('DISTINCT FROM_UNIXTIME(date, "%d-%m") AS date')
			));
			$_holidaysNorepeat=$this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'repeat' => null,
					'time <>' => 0,
					'date BETWEEN ? AND ?' => array($startDate, $endDate),
					'company_id' => $company
				),
				'fields' => array('DISTINCT FROM_UNIXTIME(date, "%d-%m-%Y") AS date')
			));
			if($returnCount)
			{
				$results=count($_holidaysRepeat)+count($_holidaysNorepeat);
			}
			else
			{
				$data = array();
				$_holidaysRepeat = Set::classicExtract($_holidaysRepeat,'{n}.0.date');
				foreach($_holidaysRepeat as $val)
				{
					$_date = $val.'-'.$_year;
					$_dateStrToTime = strtotime($_date);
					if( $_dateStrToTime >= $startDate && $_dateStrToTime <= $endDate )
					$data[]=$_date;
				}
				$_holidaysNorepeat = Set::classicExtract($_holidaysNorepeat,'{n}.0.date');
				$results_next = array_merge($data,$_holidaysNorepeat);
				$results = array_merge($results,$results_next);
				$results = array_unique($results);
			}
			$startDate = mktime(0, 0, 0, date("m", $startDate) + 1, date("d", $startDate), date("Y", $startDate));
		}
        return $results;
    }

    public function getYear($company_id, $year){
        $start = strtotime($year . '-01-01 00:00:00');
        $end = strtotime($year . '-12-31 00:00:00');
        $this->virtualFields['day'] = 'FROM_UNIXTIME(date, "' . $year . '-%m-%d")';
        $raw = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'OR' => array(
                    '`date` BETWEEN ? AND ?' => array($start, $end),
                    'Holiday.repeat IS NOT NULL'
                )
            ),
            'fields' => array('date', 'day', 'time', 'Holiday.repeat'),
            'order' => array('day' => 'ASC')
        ));
        $result = array();
        foreach($raw as $data){
            $date = $data['Holiday']['day'];
            $result[$date]['date'] = $data['Holiday']['date'];
            $result[$date]['repeat'] = $data['Holiday']['repeat'] ? 1 : 0;
            if( $data['Holiday']['time'] == 0 ){
                $result[$date]['am'] = 1;
            } else {
                $result[$date]['pm'] = 1;
            }
        }
        return $result;
    }

    public function get($company_id, $start, $end, $format = ''){
        $this->virtualFields['day'] = 'FROM_UNIXTIME(date, "%m-%d")';
        $raw = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'OR' => array(
                    '`date` BETWEEN ? AND ?' => array($start, $end),
                    'Holiday.repeat' => 1
                )
            ),
            'fields' => array('date', 'day', 'Holiday.repeat'),
            'order' => array('day' => 'ASC')
        ));
        $result = array();
        $startYear = date('Y', $start);
        $endYear = date('Y', $end);
        foreach($raw as $data){
            $md = $data['Holiday']['day'];
            $date = $data['Holiday']['date'];
            if( $data['Holiday']['repeat'] ){
                $range = range($startYear, $endYear);
                foreach($range as $year){
                    $date = strtotime($year . '-' . $md . ' 00:00:00');
                    if( $start <= $date && $date <= $end ){
                        if( $format ){
                            $result[] = date($format, $date);
                        } else {
                            $result[] = $date;
                        }
                    }
                }
            } else {
                if( $format ){
                    $result[] = date($format, $date);
                } else {
                    $result[] = $date;
                }
            }
        }
        return array_unique($result);
    }

    public function get2($company_id, $start, $end, $sum = false){
        $this->virtualFields['day'] = 'FROM_UNIXTIME(date, "%m-%d")';
        $raw = $this->find('all', array(
            'recursive' => -1,
            'conditions' => array(
                'company_id' => $company_id,
                'OR' => array(
                    '`date` BETWEEN ? AND ?' => array($start, $end),
                    'Holiday.repeat' => 1
                )
            ),
            'fields' => array('date', 'day', 'Holiday.repeat', 'time'),
            'order' => array('day' => 'ASC')
        ));
        $result = array();
        $startYear = date('Y', $start);
        $endYear = date('Y', $end);
        foreach($raw as $data){
            $md = $data['Holiday']['day'];
            $date = $data['Holiday']['date'];
            if( $data['Holiday']['repeat'] ){
                $range = range($startYear, $endYear);
                foreach($range as $year){
                    $date = strtotime($year . '-' . $md . ' 00:00:00');
                    if( $start <= $date && $date <= $end ){
                        if( $sum ){
                            if( !isset($result[$date]) ){
                                $result[$date] = 0;
                            }
                            $result[$date] += 0.5;
                        } else {
                            if( $data['Holiday']['time'] == 1 )$result[$date]['pm'] = 1;
                            else $result[$date]['am'] = 1;
                        }
                    }
                }
            } else {
                if( $sum ){
                    if( !isset($result[$date]) ){
                        $result[$date] = 0;
                    }
                    $result[$date] += 0.5;
                } else {
                    if( $data['Holiday']['time'] == 1 )$result[$date]['pm'] = 1;
                    else $result[$date]['am'] = 1;
                }
            }
        }
        return $result;
    }
}
?>
