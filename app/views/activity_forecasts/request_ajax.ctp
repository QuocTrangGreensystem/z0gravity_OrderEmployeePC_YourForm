<?php 
$avg = intval(($_start + $_end)/2);
$month = date('m', $avg);
$week = date('W', $avg);
$year = date('Y', $avg);
function formatForecast($n) {
    return number_format($n, 2, '.', '');
}
/**
 * Xay dung du lieu cho phan forecast va phan timesheet
 */
$mapDatas = array('hl' => array(), 'ab' => array(), 'ac' => array(), 'chAb' => array());
$can_send = true;
foreach ($employees as $id => $employee) {
    foreach ($listWorkingDays as $day => $time) {
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'activity_am' => 0,
            'activity_pm' => 0,
            'employee_id' => $id
        );
        foreach (array('am', 'pm') as $type) {
            if (!empty($holidays[$time][$type])) {
                continue;
            }
            $isValidated = isset($requests[$id][$time]['absence_' . $type]) && ($requests[$id][$time]['response_' . $type] == 'validated' || $requests[$id][$time]['response_' . $type] == 'waiting');
            if (!empty($forecasts[$id][$time]['activity_' . $type]) && $forecasts[$id][$time][$type . '_model']) {
                $default['activity_' . $type] = $forecasts[$id][$time]['activity_' . $type];
                $default['model_' . $type] = strtolower($forecasts[$id][$time][$type . '_model']);
                if ($isValidated) {
                    unset($activityRequests[$default['activity_' . $type]]);
                }
            }
            if (!empty($requests[$id][$time]['absence_' . $type])
                    && ($requests[$id][$time]['response_' . $type] === 'validated'
                    || $requests[$id][$time]['response_' . $type] === 'waiting'
                    || empty($forecasts[$id][$time]['activity_' . $type]))) {
                $default['absence_' . $type] = $requests[$id][$time]['absence_' . $type];
                $default['response_' . $type] = $requests[$id][$time]['response_' . $type];
				if($requests[$id][$time]['response_' . $type] === 'waiting'){
					$can_send = false;
				}
                if ($isValidated) {
                    $mapDatas['chAb'][$default['absence_' . $type]][] = $requests[$id][$time]['response_' . $type];
                    if (!isset($mapDatas['ab'][$default['absence_' . $type]][$day])) {
                        $mapDatas['ab'][$default['absence_' . $type]][$day] = 0;
                    }
                    $mapDatas['ab'][$default['absence_' . $type]][$day] += 0.5;
                }
            }
        }
        $dataSets[$id][$day] = $default;
    }
}
$header_text = array();
$_col = 3;
$columns = array(
    array(
	'id' => 'no.',
	'field' => 'no.',
	'name' => '#',
	'sortable' => false,
	'resizable' => false,
	'noFilter' => 1,
    ),
    array(
    'rerenderOnResize' => true,
	'id' => 'activity',
	'field' => 'activity',
	'name' => __('Activity', true),
	'width' => 130,
	'sortable' => true,
	'resizable' => true,
	'noFilter' => 1,
	'formatter' => 'Slick.Formatters.Activity',
	'editor' => 'Slick.Editors.activityLabel',
	'sorter' => 'activityNameSorter'
    ),
    array(
    'rerenderOnResize' => true,
	'id' => 'capacity',
	'field' => 'capacity',
	'name' => __('Capacity', true),
	'width' => 130,
	'sortable' => false,
	'resizable' => false,
	'noFilter' => 1,
	'formatter' => 'Slick.Formatters.Capacity'
    )
);
if(!empty($listWorkingDays)){
    foreach($listWorkingDays as $key => $val){
        $columns[] = array(
            'rerenderOnResize' => true,
                'id' => $val,
                'field' => $val,
                'name' => __(date('l', $val), true) . ' / ' . date('d M', $val),
                'width' => 130,
                'sortable' => false,
                'resizable' => false,
				'format_date' => date('d-m-Y', $val),
                'noFilter' => 1,
                'fillable' => true,
                'editor' => 'Slick.Editors.forecastValue',
                'validator' => 'DataValidation.forecastValue',
                'formatter' => 'Slick.Formatters.forecastValue'
        );
		$header_text['col-'.$_col++] = __(date('l', $val), true) . __(date(' d ', $val), true) . __(date('M', $val), true);
    }
}
$columns[] = array(
    'rerenderOnResize' => true,
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Action', true),
        'width' => 50,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
);
$i = 1;
$gridView = array();
$capacity = array();
$totalCapacity = 0;
$gHour = !empty($getHour) && !empty($getHour['Employee']['hour']) ? $getHour['Employee']['hour'] : 0;
$gMinute = !empty($getHour) && !empty($getHour['Employee']['minutes']) ? $getHour['Employee']['minutes'] : 0;
foreach ($listWorkingDays as $day => $time) {
    if( $isMulti ){
        $capacity[$day] = floatval(isset($mCapacity[$day]) ? $mCapacity[$day] : 0);
    } else {
        //2016-11-04 ratio
        $capacity[$day] = floatval($workdays[strtolower(date('l', $day))]*$ratio);
    }
    if($managerHour){
        $capacity[$day] = $gHour . ':' . $gMinute;
        $totalCapacity += $gHour*60+$gMinute;
    }
    if (isset($holidays[$time])) {
        if( $isMulti ){
            $capacity[$day] = 0;
            $mapDatas['hl']['off'][$day] = 0;
        } else {
            foreach ($holidays[$time] as $k => $val) {
                if (!isset($mapDatas['hl']['off'][$day])) {
                    $mapDatas['hl']['off'][$day] = 0;
                }
                if($k == 'am' || $k == 'pm'){
                    $mapDatas['hl']['off'][$day] += 0.5*$ratio;
                }
            }
        }
    }
}
if($managerHour){
    $minutes = $totalCapacity%60;
    $hour = ($totalCapacity - ($totalCapacity%60))/60;
    $capacity['capacity'] = $hour . ':' . $minutes;
} else {
    $capacity['capacity'] = array_sum($capacity);
}
$_mapDatas = !empty($mapDatas['chAb']) ? $mapDatas['chAb'] : array();
unset($mapDatas['chAb']);

foreach ($mapDatas as $type => $mapData) {
    foreach ($mapData as $activity => $dx) {
        $data = array(
            'id' => $type . '-' . $activity,
            'no.' => $i++,
            'type' => $type,
            'MetaData' => array(),
            'readonly' => true
        );
        if ($type == 'ab') {
            $data['MetaData']['cssClasses'] = 'disabled ab-validated';
            if(!empty($_mapDatas[$activity]) && in_array('waiting', $_mapDatas[$activity])){
                $data['MetaData']['cssClasses'] = 'disabled ab-validated ab-waiting';
            } else {
                $data['MetaData']['cssClasses'] = 'disabled ab-validated ab-validation';
            }
        } elseif ($type == 'hl') {
            $data['MetaData']['cssClasses'] = 'disabled ab-holiday';
        }
        $data['activity'] = $activity;
        foreach ($dx as $day => $val) {
            if($managerHour){
                if($val == 1){
                    $data[$day] = $gHour . ':' . $gMinute;
                } else {
                    $tHour = $gHour * 60 + $gMinute;
                    $tHour = round($tHour/2, 0);
                    $mHour = $tHour%60;
                    $hHour = ($tHour- $mHour)/60;
                    $hHour = ($hHour < 10) ? '0' . $hHour : $hHour;
                    $mHour = ($mHour < 10) ? '0' . $mHour : $mHour;
                    $data[$day] = $hHour . ':' . $mHour;
                }
            } else {
                $data[$day] = formatForecast($val);
                //2016-11-04 ratio
                if($type == 'ab'){
                    $data[$day] *= $ratio;
                }
            }

        }
        if ($type == 'ac') {
            foreach ($dayMaps as $day => $time) {
                if (isset($activityRequests[$activity][$time])) {
                    if($managerHour){
                        $data[$day] = $activityRequests[$activity][$time]['value_hour'];
                    } else {
                        $data[$day] = formatForecast($activityRequests[$activity][$time]['value']);
                    }
                }
            }
            unset($activityRequests[$activity]);
        }
        $data['action.'] = '';
        $gridView[] = $data;
    }
}

foreach ($activityRequests as $activity => $data) {
    $data = array(
        'id'          => 'ac-' . $activity,
        'no.'         => $i++,
        'type'        => 'ac',
        'MetaData'    => array(),
        'last'        => $activity
    );
	
    $new   = $activity;
    $task  = '';
    if(strpos($activity, '-') !== false){
        list($new , $task) = explode('-', $activity , 2);
    }

    $data['activity'] = $new;
    $data['task_id']  = $task;
    foreach ($listWorkingDays as $day => $time) {
        if (isset($activityRequests[$activity][$time])) {
            if($managerHour){
                $data[$day] = $activityRequests[$activity][$time]['value_hour'];
            } else {
                $data[$day] = formatForecast($activityRequests[$activity][$time]['value']);
            }

        }
    }
	
    $data['action.'] = '';
    $gridView[] = $data;
}
if($typeSelect === 'week'){
    $queryUpdate = '/week?week=' . date('W', $avg) . '&year=' . date('Y', $avg);
}else{
    $queryUpdate = '/month?month=' . date('m', $_start) . '&year=' . date('Y', $_start);
}
if ($isManage) {
    $queryUpdate .= '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
}
if ($is_sas != 1) {
    $role = $employee_info['Role']['name'];
}
$queryUpdate = $getDataByPath ? $queryUpdate.'&get_path='.$getDataByPath : $queryUpdate;
$dateValidate = !empty($requestConfirmInfo['updated']) ? date('d/m/Y', $requestConfirmInfo['updated']) : '';
$nameValidate =  !empty($requestConfirmInfo['employee_validate']) ? $requestConfirmInfo['employee_validate'] : '';
$requestConfirmText = '';
switch($requestConfirm){
	case 0: 
		$requestConfirmText = __('Sent', true);
		break;
	case 1: 
		$requestConfirmText = sprintf(__('Rejected (%s)', true), $nameValidate. ', ' .$dateValidate);
		break;
	case 2: 
		$requestConfirmText = sprintf(__('Validated (%s)', true), $nameValidate. ', ' .$dateValidate);
		break;
	case -1: 
		$requestConfirmText = __('In progress', true);
		break;
	default: 
		$requestConfirmText = __('In progress', true);
		break;
}
$copyURL = $this->Html->url( array(
	'controller' => 'activity_forecasts',
	'action' => 'copy_forecasts',
	$typeSelect, 
	$_start,
	$_end, 
	'?' => array(
		'get_path' => $getDataByPath
	)
));
die(json_encode(array(
	'listWorkingDays' => $listWorkingDays, 
	'dataSets' => $dataSets, 
	'holidays' => $holidays, 
	'absences' => $absences, 
	'capacity' => $capacity, 
	'gridView' => $gridView,
	'columns' => $columns,
	'header_text' => $header_text,
	'lisTaskRequests' => $lisTaskRequests,
	'lisActivityRequests' => $lisActivityRequests,
	'listTaskDisplay' => $listTaskDisplay,
	'listActivityDisplay' => $listActivityDisplay,
	'activityNotAccessDeletes' => $activityNotAccessDeletes,
	'taskNotAccessDeletes' => $taskNotAccessDeletes,
	'checkFullDayActivities' => $checkFullDayActivities,
	'checkFullDayTasks' => $checkFullDayTasks,
	'checkFullDays' => $checkFullDays,
	'canSend' => $can_send,
	'requestConfirm' => $requestConfirm,
	'requestConfirmText' => $requestConfirmText,
	'start' => $_start,
	'end' => $_end,
	'avg_date' => $avg,
	'queryUpdate' => $queryUpdate,
	'copyURL' => $copyURL,
	'mapeds' => $mapeds,
	'timesheet_info' => array(
		'week' => $week,
		'month' => $month,
		'year' => $year,
		'selectday' => $this->element('week_activity'),
	),
)));


/*
total capacity
update comment action
re draw table
change URL
*/