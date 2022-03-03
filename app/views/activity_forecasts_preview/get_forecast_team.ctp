<?php
    // Configure::write('debug', 0);

	$svg = array(
		'arrow-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><defs><style>.a{fill:none;}.b{fill:#666;fill-rule:evenodd;}</style></defs><g transform="translate(64) rotate(90)"><rect class="a" width="64" height="64"/><path class="b" d="M0,7.429A.551.551,0,0,1,.217,7L3.492,4,.175.968h0A.544.544,0,0,1,0,.571.6.6,0,0,1,.625,0a.656.656,0,0,1,.434.16h0L4.808,3.588h0A.544.544,0,0,1,5,4H5V4a.547.547,0,0,1-.192.411h0L1.059,7.843l0,0A.652.652,0,0,1,.625,8,.6.6,0,0,1,0,7.429Z" transform="translate(28.093 34.5) rotate(-90)"/></g></svg>',
		'deplier' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"/><path class="b" d="M-3193-1748a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Z" transform="translate(3666 1994)"/></g></svg>', 	
		'deplier-active' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"/><rect class="a" width="32" height="32" transform="translate(464 224)"/><path class="b" d="M-3181.758-1748.343l-4.242-4.243-4.243,4.243a1,1,0,0,1-1.414,0,1,1,0,0,1,0-1.415l4.242-4.242-4.242-4.243a1,1,0,0,1-.293-.707,1,1,0,0,1,.293-.707,1,1,0,0,1,1.414,0l4.243,4.242,4.242-4.242a1,1,0,0,1,1.414,0,1,1,0,0,1,0,1.414l-4.242,4.243,4.243,4.243a1,1,0,0,1,.292.707,1,1,0,0,1-.292.707,1,1,0,0,1-.707.292A1,1,0,0,1-3181.758-1748.343Z" transform="translate(3666 1994)"/></g></svg>', 	
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><defs><style>.a{fill:none;}.b{fill:#666;fill-rule:evenodd;}</style></defs><g transform="translate(-56 -136)"><rect class="a" width="16" height="16" transform="translate(56 136)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1H5V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3A.5.5,0,0,1,5,3V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(56.001 135.999)"/></g></svg>', 
		'submit' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="16" height="16" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 864 1024"><path d="M65 65l735 447L64 959zm0-64Q48 1 34 9 1 28 1 65L0 959q0 37 33 56 7 4 15 6t16 2q9 0 17.5-2.5T97 1013l737-447q30-18 30-54 0-11-3.5-21.5t-10.5-19-16-13.5L98 11q-5-3-10.5-5t-11-3.5T65 1z" fill="#666"/></svg>', 
		'arrow-bottom' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><defs><style>.a{fill:none;}.b{fill:#217fc2;fill-rule:evenodd;}</style></defs><g transform="translate(64) rotate(90)"><rect class="a" width="64" height="64"/><path class="b" d="M0,7.429A.551.551,0,0,1,.217,7L3.492,4,.175.968h0A.544.544,0,0,1,0,.571.6.6,0,0,1,.625,0a.656.656,0,0,1,.434.16h0L4.808,3.588h0A.544.544,0,0,1,5,4H5V4a.547.547,0,0,1-.192.411h0L1.059,7.843l0,0A.652.652,0,0,1,.625,8,.6.6,0,0,1,0,7.429Z" transform="translate(29.593 28)"/></g></svg>', 
		'ascending' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(32 23.5) rotate(90)"><rect class="a" width="48" height="48" transform="translate(-23.5 -16)"/><path class="b" d="M9,7.429A.55.55,0,0,1,9.217,7l3.275-3L9.174.968A.545.545,0,0,1,9,.572.6.6,0,0,1,9.625,0a.658.658,0,0,1,.434.16l3.749,3.429A.545.545,0,0,1,14,4V4a.547.547,0,0,1-.192.412L10.059,7.844h0A.656.656,0,0,1,9.625,8,.6.6,0,0,1,9,7.429ZM3.941,7.84h0L.192,4.412h0A.543.543,0,0,1,0,4H0a.547.547,0,0,1,.192-.412L3.941.157h0A.65.65,0,0,1,4.375,0,.6.6,0,0,1,5,.572a.551.551,0,0,1-.217.434L1.508,4,4.825,7.032h0a.546.546,0,0,1,.174.4A.6.6,0,0,1,4.375,8,.658.658,0,0,1,3.941,7.84Z" transform="translate(-6.5 4)"/></g></svg>'
		
	);

	$am = __('AM', true);
	$pm = __('PM', true);

	$dayMaps = array(
		'monday' => $_start,
		'tuesday' => $_start + DAY,
		'wednesday' => $_start + (DAY * 2),
		'thursday' => $_start + (DAY * 3),
		'friday' => $_start + (DAY * 4)
	);
	$listDays = array_values($dayMaps);
	
	$rowFixed = $fixed_head = '';
	?>
	<?php 
		if( !function_exists('template_task_of_employee')){

			function template_task_of_employee($_this, $data, $class, $listStatus, $svg, $_start, $_end, $countDate, $employees){
				if( empty($data)) return;
				$multilslect = template_multiselect($_this, $data['employee_id']);
				$task_start_date = strtotime($data['task_start_date']);  
				$task_end_date = strtotime($data['task_end_date']);
				$tmp_start = $left = 0;
				if($task_start_date > $_start){
					$left = _workingDayOFTask($_this, $_start, $task_start_date) - 1;
				}
				$tmp_start = ($task_start_date > $_start) ? $task_start_date : $_start;
				$tmp_end   = ($task_end_date > $_end) ? $_end : $task_end_date;
				
				$width = _workingDayOFTask($_this, $tmp_start, $tmp_end);
				
				$task_html = '';
				$task_html .= '<div data-task-id ="'. $data['p_task_id'] .'" data-width= "'. $width .'" data-left= "'. $left .'" class="task-item '. $class .'"><ul>';
				$task_html .= '<li class="task-workload">';
				$task_html .= $data['sum_workload'];
				$task_html .= '</li>';
				$task_html .= '<li class="task-title">';
				$task_html .= '<p>'. $data['project_name'].'</p><p class="task-name">'.$data['nameTask'] .'</p>';
				$task_html .= '</li><li class="task-status">';
				$task_html .= template_task_status($data, $listStatus);
				$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNormalTask.call(this);" ><img src="../../img/new-icon/pen.png" /></a>';
				if($data['nct']){
					$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNCTTask.call(this);" ><img src="../../img/new-icon/pen-blue.png" /></a>';
				}
				$task_html .= '</li></ul>'. $action_task .'<div class="assign-to"><a href="javascript:void(0);" data-task_id="'. $data['p_task_id'] .'" onclick="openMultiselect.call(this);" class="open-multilslect">'. $svg['deplier'].'</a>'. $multilslect .'</div></div>';
				return $task_html;
			}
		}
		

	if( !function_exists('template_progress')){
		function template_progress($workload, $capacity, $isPC = 0, $pc_id = null ){
			
			$color = '#6EAF79';
			if($capacity == 0 || $workload == 0) {
				$progress = 0;
			}else{
				$progress = ($workload / $capacity) * 100;
				if($progress > 100) $progress = 100;
				$progress = round($progress , 1);
				if((int)$workload > (int)$capacity) $color = '#F59796';
			}
			$pg_html = '';
			if($isPC != 2){
				$ex_class = ($isPC) ? 'profit-summary' : 'employee-summary';
				$ex_actions = ($pc_id) ? 'data-pc_id = '. $pc_id .' href="javascript:void(0);" onclick="getPCWorkload.call(this);" ' : '';
				$pg_html .= '<div '. $ex_actions .'class="summary '. $ex_class .' tdColCapacity" style = "color:'. $color .'">';
	
				$pg_html .= "<p class='workload'>". round($workload, 2) ."</p>/<p class='capacity'>". round($capacity, 2)."</p>";
			}
			$pg_html .= '<div class="progress-item">';
			$pg_html .= '<span style="background-color: '. $color .';  width: '. $progress .'%">';
			$pg_html .= '</span></div>';
			if($isPC) $pg_html .= '<p class="percent" style="color:'. $color .'">'.$progress.'%</p>';
			$pg_html .= '</div>';
			return $pg_html;
		}
	}

function template_pc_forecast($_this, $profit_id, $_pc_name, $workloads = array(), $employees = array(), $colspan = 8, $dayMaps = array(), $holidays = array(), $svg = array(), $workdays = array(), $_start, $_end){
	ob_start(); 
	if(!empty($workloads) && !empty($employees)) : 
	unset($workloads['employees']);
	$dataView = array();
	$isEffected = false;
	$countDate = 0; 
	$countAbsence = 0; 
	foreach ($dayMaps as $day => $time) {
		if(!empty($workdays[$day])){
			if(date('l',$time) != 'Sunday' && date('l',$time) != 'Saturday'){
				$countDate++;
			}
		}
	}
	foreach ($employees as $id => $employee) {
		foreach ($dayMaps as $day => $time) {
			$_holiday=false;
			foreach($holidays as $key=>$value123)
			{
				if($time==$key) $_holiday=true;
			}
			$default = array(
				'holiday'=>$_holiday
			);
			if(!empty($workdays[$day])){
				$dataView[$id][$day] = $default;
			}
		}
	}
	if(!empty($workloads)){
		foreach($dataView as $employee_id => $_dataViews){
			if($employee_id =='tNotAffec_'.$profit_id) 	{
				$isEffected = true;
			}		
			foreach($_dataViews as $time1 => $_dataView){
				foreach($workloads as $e_id => $_workloads){
					foreach($_workloads as $time2 => $_workload){
						if($employee_id == $e_id && $time1 == $time2){
							if(!empty($_workload[0]) && !empty($_workload[0]['am']) && $_workload[0]['am'] == 'validated'){
								$countAbsence += 0.5;
							}
							if(!empty($_workload[0]) && !empty($_workload[0]['pm']) && $_workload[0]['pm'] == 'validated'){
								$countAbsence += 0.5;
							}
							$dataView[$employee_id][$time1]['data'] = $_workload;
						}	
					}															
				}															
			}
		}
	}
	$t_head = $t_body = '';
	$fixed_workload = $fixed_capacity = 0;
	$workload_head = $capacity_head = array();
	// foreach($workloads as $profit_id => $datas){
		$t_head .= "<tr class='trPC trPC-".$profit_id."'>";
		$t_head_right = '';
			$_rows = '';
			$absence_exist = array();
			foreach($dataView as $id => $workload){
				$task_list = array();
				$task_html = '';
				$absence_html = '';
				
				$employeeCustom[$id]['capacity'] = 0;
				$employeeCustom[$id]['workload'] = 0;
				foreach($workload as $time=> $value){
					// days of a week
					// Do not draw sunday and saturday
					
					$class_cell = '';
					if(!(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday')){
						$class_cell = date('l',$time);
					
					$dateWorkload = 0;
					if(empty($workload_head[$time])){
						$workload_head[$time]['workload'] = 0;
						$workload_head[$time]['capacity'] = 0;
					}
					$workload_head[$time]['is_day_off'] = 0;
					if(!empty($value['holiday'])){
						$dateWorkload = '';
						$workload_head[$time]['is_day_off'] = 1;
					}else if(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday'){
						$dateWorkload = '';
						$workload_head[$time]['is_day_off'] = 1;
					
					}else{
						$employeeCustom[$id]['capacity'] += 1;
						$workload_head[$time]['capacity'] += 1;
						$fixed_capacity += 1;
						if($id == 'tNotAffec_'.$profit_id){
							// capacity of PC except capacity of Employe Affected
							$fixed_capacity -= 1;
						}
						// set default
						if(isset($value['data'])){
						
							$dx = $value['data'];
							$countRow = count($dx);
							// sum workload of task
							$_is_validated_am = 0;
							$_is_validated_pm = 0;
							$has_draw_absence = 0;
							foreach($dx as $key=> $val){
								// Draw absence
								if(empty($val['p_task_id']) && empty($has_draw_absence)){
									if(!empty($val['am']) && $val['am'] == 'validated'){
										$employeeCustom[$id]['capacity'] -= 0.5;
										$workload_head[$time]['capacity'] -= 0.5;
										$fixed_capacity -= 0.5;
										$_is_validated_am = 0.5;
									}
									if(!empty($val['pm']) && $val['pm'] == 'validated'){
										$employeeCustom[$id]['capacity'] -= 0.5;
										$workload_head[$time]['capacity'] -= 0.5;
										$_is_validated_pm = 0.5;
										$fixed_capacity -= 0.5;
									}
									$has_draw_absence = 1;
								}
								// If absence has validated, do not draw tasks
								// Draw tasks
								//sum workload of tasks in a day
								if(!empty($val['p_task_id'])){
									$dateWorkload += $val['workload'];
									$employeeCustom[$id]['workload'] += $val['workload'];
									$fixed_workload += $val['workload'];
									// profit data
									$workload_head[$time]['workload'] += $val['workload'];
								}
							}
							
						}
						
						$cell_color = '';
						$text_color = '';
						$_height_cell = 0;
						if($dateWorkload > 1){
							$cell_color .= 'style="height: 100%; background: #FDECEC;"';
							$text_color .= 'style="color: #F05352"';
						}else if($dateWorkload > 0){
							$_height_cell = $dateWorkload * 100;
							$cell_color .= 'style="height: '. $_height_cell .'%; background: #EFF6F0;"';
							$text_color .= 'style="color: #6EAF79"';
						}
					}
					}
				}
				
			}	

		if(!empty($workload_head)){
			foreach($workload_head as $time=> $value){
				$class_cell = '';
				if(!(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday')){
						$class_cell = date('l',$time);
					// }
					$t_head_right .= '<td class="fixedWidth text-center '. $class_cell .'">';
					if($value['is_day_off']){
						// nothing
						if(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday'){
							$t_head_right .= '<div class="cell-style"></div>';
						}
					}else{
						// Sum capacity of resource - capacity of affected 
						// capacity of affected  = 1 
						$capEffect  = $isEffected ? 1 : 0; 
						$t_head_right .= round($value['workload'], 2) .'/'. ($value['capacity'] - $capEffect);
					}
					$t_head_right .= template_progress(round($value['workload'], 2) , round($value['capacity'],2), $isPC = 2);
					$t_head_right .= "</td>";
				}
			}
			
		}
		$t_head .= "<td width='440' class='row-pc'><div><a class='pc-arrow'>". $svg['arrow-right'] . $svg['arrow-bottom'] ."</a><span class='pc-name' title='". str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '', $_pc_name ) ."'>". str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '', $_pc_name ) ."</span>";
		$t_head .= template_progress($fixed_workload , $fixed_capacity, $isPC = 1) ."</div>";
		$t_head .= $svg['ascending'] .'</td>'. $t_head_right . "</tr>";
		echo $t_head;  
	endif;
	return ob_get_clean();
	
	}
	
    echo json_encode(template_pc_forecast($this, $profit_id, $_pc_name, $workloads, $_employees, $_colspan + 1 , $_dayMaps, $_holiday, $svg, $_workdays, $_start, $_end));
?>