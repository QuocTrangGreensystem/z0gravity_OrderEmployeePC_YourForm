<?php

App::import('Core', 'Validation');
App::import('Model', 'Model');

//        App::import('Component', 'PhasePlan');
//        $PhasePlan = new PhasePlanComponent();
//        $PhasePlan->createUser();
//        $this->showDebug();

class PhasePlanComponent extends Object {

    protected $_logs = array();

    /**
     * Parse duration
     */
    public function parseWeight($Model) {
        $Model->Project->Behaviors->attach('Containable');
        $Model->Project->bindModel(array('hasMany' => array($Model->alias)));
        $projects = $Model->Project->find('all', array('contain' => array(
                $Model->alias => array('id', 'weight')
                )));

        foreach ($projects as $project) {
            $i = 1;
            foreach ($project[$Model->alias] as $phasePlan) {
                $Model->id = $phasePlan['id'];
                if (!$Model->save(array('weight' => $i++), array('validate' => false, 'callbacks' => false))) {
                    $this->_logs[] = sprintf('Error : Cannot save phase', $Model->id);
                }
            }
        }
        pr($this->_logs);
    }

    /**
     * Parse duration
     */
    public function parseDuration($Model, $phasePlans, $config = array()) {
        $config = array_merge(array(
            'week' => array(
                0 => true, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => true
                )), $config);
        foreach ($phasePlans as $phasePlan) {
            $phasePlan = array_shift($phasePlan);
            $start = $this->toTime($phasePlan['phase_planed_start_date']);
            $end = $this->toTime($phasePlan['phase_planed_end_date']);

            if (empty($start) && empty($end)) {
                continue;
                $this->_logs[] = sprintf('Error : Start date and End date of phase %s is invaild', $phasePlan['id']);
            } elseif (empty($start)) {
                $this->_logs[] = sprintf('Warning : Start date of phase %s is invaild', $phasePlan['id']);
                $start = $end;
            } elseif (empty($end)) {
                $this->_logs[] = sprintf('Warning : End date of phase %s is invaild', $phasePlan['id']);
                $end = $start;
            }

            if ($start > $end) {
                $this->_logs[] = sprintf('Warning : End date less than Start date, Phase %s', $phasePlan['id']);
                $end = $start;
            }

            $phasePlan['phase_planed_start_date'] = date('Y-m-d', $start);
            $phasePlan['phase_planed_end_date'] = date('Y-m-d', $end);
            $phasePlan['planed_duration'] = $this->getDuration($start, $end, $config);

            $phasePlan = $Model->parseData(array($Model->alias => $phasePlan));

            $Model->id = $phasePlan[$Model->alias]['id'];
            unset($phasePlan[$Model->alias]['id']);
            if (!$Model->save($phasePlan, array('validate' => false, 'callbacks' => false))) {
                $this->_logs[] = sprintf('Error : Cannot save phase', $Model->id);
            }
        }
        pr($this->_logs);
    }

    /**
     * Remove duplication employee
     */
    public function duplicateEmployee($Model) {
        $employees = array_values($Model->CompanyEmployeeReference->find('list', array('recursive' => -1, 'fields' => array('id', 'employee_id'))));
        $Model->unbindModel(array(
            'belongsTo' => array('City', 'Country')
        ));
        $Model->deleteAll(array('not' => array('id' => $employees)));
    }

    /**
     * Parse duration
     */
    public function getDuration($start, $end, $config) {
        $duration = 0;
        $max = round(($end - $start) / 86400);
        list($y, $m, $d) = explode('-', date('Y-m-d', $start));

        while ($max >= 0) {
            if (empty($config['week'][$this->getDay($d, $m, $y)]) &&
                    !(!empty($config['months'][$m]) && in_array($d, $config['months'][$m]))) {
                $duration++;
            }
            $dx = $this->dayOfMonth($m, $y);
            $d++;
            if ($d > $dx) {
                $m++;
                $d = 1;
            }
            if ($m > 12) {
                $m = 1;
                $y++;
            }
            $max--;
        }
        return $duration;
    }

    /**
     * Get days of month
     *
     * @param integer $_m, the number of month
     * @param integer $_y, the number of year
     * 
     * @return integer
     */
    public function dayOfMonth($m, $y) {
        $timestamp = mktime(0, 0, 0, $m, 1, $y);
        return date("t", $timestamp);
    }

    /**
     * Get days of month
     *
     * @param integer $_m, the number of month
     * @param integer $_y, the number of year
     * 
     * @return integer
     */
    public function getDay($d, $m, $y) {
        $timestamp = mktime(0, 0, 0, $m, $d, $y);
        return date("w", $timestamp);
    }

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     * 
     * @return integer, a Unix timestamp value
     */
    public function toTime($date) {
        if (empty($date) || !Validation::date($date, 'ymd')) {
            return 0;
        }
        return intval(strtotime($date));
    }

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     * 
     * @return integer, a Unix timestamp value
     */
    public function createUser() {
        $Model = new Model(array('table' => 'employees', 'ds' => 'default'));
        $Role = new Model(array('table' => 'company_employee_references', 'ds' => 'default'));
        $Profit = new Model(array('table' => 'project_employee_profit_function_refers', 'ds' => 'default'));
        $Center = new Model(array('table' => 'profit_centers', 'ds' => 'default'));
        $list = array_values($Center->find('list', array('conditions' => array(
                        'company_id' => 57,
                        'name <>' => 'DEFAULT',
                    ), 'fields' => array('id'))));
        $max = ceil(450 / count($list));
        $i = $j = 0;
        foreach (range(1, 450) as $c) {
            $data = array(
                'code_id' => NULL,
                'first_name' => 'Test',
                'last_name' => 'Perform ' . $c,
                'email' => "test.perform$c@greensystem.vn",
                'password' => 'e10adc3949ba59abbe56e057f20f883e',
                'address' => '',
                'post_code' => '',
                'work_phone' => '',
                'home_phone' => '',
                'mobile_phone' => '000000000000',
                'fax' => '',
                'city_id' => '66',
                'country_id' => '51',
                'tjm' => NULL,
                'actif' => NULL,
                'external' => NULL,
                'start_date' => NULL,
                'end_date' => NULL,
                'identifiant' => NULL,
                'is_sas' => 0,
                'email_receive' => '1'
            );
            $Model->create($data);
            $Model->save();
            $Role->create(array(
                'company_id' => 57,
                'role_id' => 3,
                'employee_id' => $Model->getLastInsertID()
            ));
            $Role->save();
            $Profit->create(array(
                'profit_center_id' => isset($list[$i]) ? $list[$i] : $list[0],
                'employee_id' => $Model->getLastInsertID()
            ));
            $Profit->save();
            $j++;
            if ($j >= $max) {
                $j = 0;
                $i++;
            }
        }
    }

}

?>