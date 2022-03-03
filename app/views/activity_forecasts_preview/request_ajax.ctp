<?php
	$managerHour = false;
	$fillMoreThanCapacity = (!$managerHour && !empty($companyConfigs['fill_more_than_capacity_day'])) ?  1 : 0;
	$role = $employee_info['Role']['name'];
	$avg = intval(($_start + $_end)/2);
	$week = date('W', $avg);
	$year = date('Y', $avg);
	$profit = $employee['profit_center_id'];
	$queryUpdate = '/week?week=' . $week . '&year=' . $year . '&id=' . $idEmployee.'&profit='.$profit;
	$requestStatus = isset( $requestConfirm['ActivityRequestConfirm']['status']) ?  $requestConfirm['ActivityRequestConfirm']['status'] : -1;
	$requestConfirmDate = !empty($requestConfirm['ActivityRequestConfirm']['updated']) ? $requestConfirm['ActivityRequestConfirm']['updated'] : '';
	$requestConfirmName = !empty($requestConfirm['ActivityRequestConfirm']['employee_validate']) ? $requestConfirm['ActivityRequestConfirm']['employee_validate'] : '';
	$dateValidate = !empty($requestConfirmDate) ? date('d/m/Y', $requestConfirmDate) : '';
	$nameValidate = !empty($requestConfirmName) ? $requestConfirmName : '';
	if ($requestStatus == 0){
		$statusAct = 'Sent';
	} elseif ($requestStatus == 1){
		$statusAct = sprintf(__('Rejected (%s)', true), $nameValidate. ', ' .$dateValidate);
	} elseif ($requestStatus == 2){
		$statusAct = sprintf(__('Validated (%s)', true), $nameValidate. ', ' .$dateValidate);
	} else{
		$statusAct = 'In progress';
	}
	$canModified = (($requestStatus == -1 || $requestStatus == 1) && $canWrite);
	$svg_icons = array(
		'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>'
	);
	/* Define Columns */
	$columns = array(
		array(
			'id' => 'favourite',
			'field' => 'favourite',
			'name' => __(' ', true),
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'noFilter' => 1, 
			'cssClass' => 'action-column slick-cell-merged',
			'headerCssClass' => 'slick-cell-merged',
			'formatter' => 'Slick.Formatters.taskFavourite'
		),
		array( 
			'id' => 'task_name',
			'field' => 'task_name',
			'name' => __('Done', true),
			// 'width' => 420,
			'sortable' => true,
			'resizable' => true,
			'editable' => false,
			'headerCssClass' => 'slick-cell-merged',
			'cssClass' => 'slick-cell-merged text-left',
			'formatter' => 'Slick.Formatters.taskName'
		),
		array(
			'id' => 'progress',
			'field' => 'progress',
			'name' => ' ',
			'width' => 90,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'cssClass' => 'slick-cell-merged action-column',
			'headerCssClass' => 'slick-cell-merged',
			'formatter' => 'Slick.Formatters.taskProgress'
		),
		array(
			'id' => 'task_comment',
			'field' => 'task_comment',
			'name' => '<i class = "icon-arrow-left"></i>',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'headerCssClass' => 'prev-week',
			'cssClass' => 'action-column',
			'formatter' => 'Slick.Formatters.taskComment'
		),
		array(
			'id' => 'task_capacity',
			'field' => 'task_capacity',
			'name' => __('Capacity', true),
			'width' => 80,
			'sortable' => false,
			'resizable' => false,
			'cssClass' => 'task-capacity',
			'formatter' => 'Slick.Formatters.taskCapacity'
		)
	);
	foreach( $listWorkingDays as $date => $is_working){
		$columns[] = array(
			'id' => $date,
			'field' => $date,
			'name' => __(substr(date( 'l', $date), 0, 2), true) . " " . date('d', $date) . ($is_working ? (" <a href='javascript:void(0);' class = 'comment-day' data-date=".$date.">" . ($showActivityForecastComment ? $svg_icons['message']:'') . "</a>") : ''),
			'width' => 80,
			'sortable' => false,
			'resizable' => false,
			'calcTotal' => (boolean) $is_working,
			'editable' => (boolean) $is_working,
			'format_date' => date('d-m-Y', $date),
			'cssClass' => $is_working ? 'working-day' : 'day-off',
			'formatter' => 'Slick.Formatters.floatVal',
			'validator' => 'DataValidation.forecastValue',
			'editor' => $is_working ? 'Slick.Editors.forecastValue' : 0,
		);
	}
	$columns[] = array(
		'id' => 'action',
		'field' => 'action',
		'name' => '<i class = "icon-arrow-right"></i>',
		'width' => 40,
		'sortable' => false,
		'resizable' => false,
		'editable' => false,
		'cssClass' => 'action-column',
		'headerCssClass' => 'next-week',
		'formatter' => 'Slick.Formatters.action'
	);
	/* END Define Cloumns */
	$totalCapacity = 0;
	$dataView = array();
	$i = 0;
	/* Build data cho Holiday và Absence */
		/* Holiday */
	$type = 'holiday';
	$data = array(
		'id' => $type,
		'no.' => $i++,
		'type' => $type,
		'MetaData' => array(
			'cssClasses' =>  'not-working '.$type ,
		),
		'task_name' => __('Holiday', true),
		'readonly' => true,
	);
	foreach( $holidays as $date => $holiday){
		$value = 0;
		foreach( $holiday as $half  => $is_repeat){ /* $half : 'am' / 'pm' */
			$value += 0.5*$ratio;
			$data[$date.$half] = 1;
		}
		$data[$date] = $value;
	}
	$dataView[] = $data;
		/* END Holiday */
		/* Absence */
	if( $display_absence){
		$type = 'absence';
		$data = array(
			'id' => $type,
			'no.' => $i++,
			'type' => $type,
			'MetaData' => array(
				'cssClasses' =>  'not-working ' . $type ,
			),
			'task_name' => __('Absence', true),
			'readonly' => true,
		);
		foreach( $absenceRequests as $date => $absenceRequest){
			$value = 0;
			foreach( array('am', 'pm' ) as $half){
				if($absenceRequest['absence_'.$half]){
					$value += 0.5*$ratio;
				}
			}
			$data[$date] = $value;
			$key = $date.'_data';
			$data[$key] = array(
				'absence_pm' => $absenceRequest['absence_pm'],
				'absence_am' => $absenceRequest['absence_am'],
				'response_am' => $absenceRequest['response_am'],
				'response_pm' => $absenceRequest['response_pm']
			);
		}
		$dataView[] = $data;
	}
		/* END Absence */
	/* END Build data cho Holiday và Absence */
	/* Build data cho Task */
	$type = 'ac';
	foreach($activityRequests as $task_id => $task_values){
		$activity_id = $activityTasks[$task_id]['activity_id'];
		$subfamily_id = $activities[$activity_id]['subfamily_id'];
		$family_id = $activities[$activity_id]['family_id'];
		$favourite = !empty( $taskFavourites[$activityTasks[$task_id]['project_task_id'] ]) ? 1 : 0;
		$task_workload = floatVal(!empty($activityTasks[$task_id]['estimated']) ? $activityTasks[$task_id]['estimated'] : 0 );
		$task_consumed = floatVal(!empty($consumed[$task_id]) ? $consumed[$task_id] : 0);
		$data = array(
            'id' => $type . '-0-' . $task_id,
            'no.' => $i++,
            'type' => $type,
            'MetaData' => array(
				'cssClasses' => 'type-ac '. ($type . '-0-' . $task_id) .' '. ($favourite ? 'favourite' : 'non_favourite' ),
			),
            'activity' => 0, // have if select_project_without_task
            'readonly' => false,
			'task_id' => $task_id,
			'task_name' => $activityTasks[$task_id]['name'],
			'part_name' => $activityTasks[$task_id]['part_name'],
			'phase_name' => $activityTasks[$task_id]['phase_name'],
			'activity_name' => $activities[$activity_id]['name'],
			'activity_sname' => $activities[$activity_id]['short_name'],
			'activity_lname' => $activities[$activity_id]['long_name'],
			'subfamily' => !empty($families[$subfamily_id]['name']) ? $families[$subfamily_id]['name'] : '',
			'family' => $families[$family_id]['name'],
			'favourite' => $favourite,
			'workload' => $task_workload,
			'consumed' => $task_consumed,
			'progress' => $task_workload ? intval( ($task_consumed / $task_workload)*100 ) : 100
        );
		$data += $task_values;
		$dataView[] = $data;
	}	
	die (json_encode(array(
		'timesheet' => array(
			'statusAct' => $statusAct,
			'startdate' => $_start,
			'enddate' => $_end,
			'avg_date' => $avg,
			'queryUpdate' => $queryUpdate,
			'canModified' => $canModified,
			'requestStatus' => $requestStatus,
		),
		'capacity' => $capacity,
		'listWorkingDays' => $listWorkingDays,
		'columns' => $columns,
		'dataView' => $dataView,
		'canRequestAbsence' => $canRequestAbsence,
	)));
