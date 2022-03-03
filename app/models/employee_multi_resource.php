<?php
/**
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr)
 * and Green System Solutions (http://greensystem.vn)
 */
class EmployeeMultiResource extends AppModel {
    /**
     * Name of the model.
     *
     * @var string
     */
    public $name = 'EmployeeMultiResource';
    public $virtualFields = array(
        'month' => 'FROM_UNIXTIME(`date`, "%Y-%m-01")',
        'sum_value' => 'SUM(`value`)',
        'week' => 'WEEKOFYEAR(FROM_UNIXTIME(`date`))',
        'year' => 'FROM_UNIXTIME(`date`, "%Y")'
    );
    public function getCapacity($ids, $from, $to, $sum = 'month'){
        //holiday settings
        $Holiday = ClassRegistry::init('Holiday');
        $employee_info = CakeSession::read('Auth.employee_info');
        $companyId = $employee_info['Company']['id'];
        $days = $Holiday->get($companyId, $from, $to);
        $arr = array(
            'conditions' => array(
                'employee_id' => $ids,
                'date BETWEEN ? AND ?' => array($from, $to),
                'NOT' => array(
                    'date' => $days
                )
            ),
            'fields' => array('employee_id'),
            'order' => array('date' => 'ASC')
        );
        if( $sum == 'month' ){
            $arr['fields'][] = $sum;
            $arr['fields'][] = 'sum_value';
            $arr['group'] = array('employee_id', $sum);
        }
        else if( $sum == 'week' ){
            $arr['fields'][] = $sum;
            $arr['fields'][] = 'year';
            $arr['fields'][] = 'sum_value';
            $arr['group'] = array('employee_id', 'week', 'year');
        } else {
            $arr['fields'][] = 'value';
            $arr['fields'][] = 'date';
        }
        $data = $this->find('all', $arr);
        $result = array('total' => 0);
        foreach($data as $d){
            $id = $d['EmployeeMultiResource']['employee_id'];
            switch($sum){
                case 'month':
                    $time = strtotime($d['EmployeeMultiResource']['month'] . ' 00:00:00');
                    $val = $d['EmployeeMultiResource']['sum_value'];
                    break;
                case 'week':
                    $dt = new DateTime();
                    $dt->setISODate($d['EmployeeMultiResource']['year'], $d['EmployeeMultiResource']['week']);
                    $dt->setTime(0, 0, 0);
                    $time = $dt->getTimestamp();
                    $val = $d['EmployeeMultiResource']['sum_value'];
                    break;
                default:
                    $time = $d['EmployeeMultiResource']['date'];
                    $val = $d['EmployeeMultiResource']['value'];
                    break;
            }
            if( !isset($result[$id]) ){
                $result[$id] = array();
            }
            $result[$id][$time] = $val;
            $result['total'] += $val;
        }
        return $result;
    }

    public function isMultipleResources($list){
        $Employee = ClassRegistry::init('Employee');
        return $Employee->find('list', array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $list,
                'external' => 2
            ),
            'fields' => array('id', 'id')
        ));
    }

}
 ?>
