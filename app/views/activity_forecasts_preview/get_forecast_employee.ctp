<?php
    // Configure::write('debug', 0);
	$svg = array(
		'arrow-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><defs><style>.a{fill:none;}.b{fill:#666;fill-rule:evenodd;}</style></defs><g transform="translate(64) rotate(90)"><rect class="a" width="64" height="64"/><path class="b" d="M0,7.429A.551.551,0,0,1,.217,7L3.492,4,.175.968h0A.544.544,0,0,1,0,.571.6.6,0,0,1,.625,0a.656.656,0,0,1,.434.16h0L4.808,3.588h0A.544.544,0,0,1,5,4H5V4a.547.547,0,0,1-.192.411h0L1.059,7.843l0,0A.652.652,0,0,1,.625,8,.6.6,0,0,1,0,7.429Z" transform="translate(28.093 34.5) rotate(-90)"/></g></svg>',
		'deplier' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"/><path class="b" d="M-3193-1748a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Z" transform="translate(3666 1994)"/></g></svg>', 	
		'deplier-active' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"/><rect class="a" width="32" height="32" transform="translate(464 224)"/><path class="b" d="M-3181.758-1748.343l-4.242-4.243-4.243,4.243a1,1,0,0,1-1.414,0,1,1,0,0,1,0-1.415l4.242-4.242-4.242-4.243a1,1,0,0,1-.293-.707,1,1,0,0,1,.293-.707,1,1,0,0,1,1.414,0l4.243,4.242,4.242-4.242a1,1,0,0,1,1.414,0,1,1,0,0,1,0,1.414l-4.242,4.243,4.243,4.243a1,1,0,0,1,.292.707,1,1,0,0,1-.292.707,1,1,0,0,1-.707.292A1,1,0,0,1-3181.758-1748.343Z" transform="translate(3666 1994)"/></g></svg>', 	
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><defs><style>.a{fill:none;}.b{fill:#666;fill-rule:evenodd;}</style></defs><g transform="translate(-56 -136)"><rect class="a" width="16" height="16" transform="translate(56 136)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1H5V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3A.5.5,0,0,1,5,3V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(56.001 135.999)"/></g></svg>', 
		'submit' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="16" height="16" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 864 1024"><path d="M65 65l735 447L64 959zm0-64Q48 1 34 9 1 28 1 65L0 959q0 37 33 56 7 4 15 6t16 2q9 0 17.5-2.5T97 1013l737-447q30-18 30-54 0-11-3.5-21.5t-10.5-19-16-13.5L98 11q-5-3-10.5-5t-11-3.5T65 1z" fill="#666"/></svg>', 
		'arrow-bottom' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><defs><style>.a{fill:none;}.b{fill:#217fc2;fill-rule:evenodd;}</style></defs><g transform="translate(64) rotate(90)"><rect class="a" width="64" height="64"/><path class="b" d="M0,7.429A.551.551,0,0,1,.217,7L3.492,4,.175.968h0A.544.544,0,0,1,0,.571.6.6,0,0,1,.625,0a.656.656,0,0,1,.434.16h0L4.808,3.588h0A.544.544,0,0,1,5,4H5V4a.547.547,0,0,1-.192.411h0L1.059,7.843l0,0A.652.652,0,0,1,.625,8,.6.6,0,0,1,0,7.429Z" transform="translate(29.593 28)"/></g></svg>', 
		'ascending' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><defs><style>.a{fill:none;}.b{fill:#c6cccf;}</style></defs><g transform="translate(32 23.5) rotate(90)"><rect class="a" width="48" height="48" transform="translate(-23.5 -16)"/><path class="b" d="M9,7.429A.55.55,0,0,1,9.217,7l3.275-3L9.174.968A.545.545,0,0,1,9,.572.6.6,0,0,1,9.625,0a.658.658,0,0,1,.434.16l3.749,3.429A.545.545,0,0,1,14,4V4a.547.547,0,0,1-.192.412L10.059,7.844h0A.656.656,0,0,1,9.625,8,.6.6,0,0,1,9,7.429ZM3.941,7.84h0L.192,4.412h0A.543.543,0,0,1,0,4H0a.547.547,0,0,1,.192-.412L3.941.157h0A.65.65,0,0,1,4.375,0,.6.6,0,0,1,5,.572a.551.551,0,0,1-.217.434L1.508,4,4.825,7.032h0a.546.546,0,0,1,.174.4A.6.6,0,0,1,4.375,8,.658.658,0,0,1,3.941,7.84Z" transform="translate(-6.5 4)"/></g></svg>',
		'icon-move' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a-phase{fill:none;}.b-phase{fill:#d3d3d3;}</style></defs><g transform="translate(4 4)"><rect class="a-phase" width="40" height="40" transform="translate(-4 -4)"/><path class="b-phase" d="M-2915-656v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Zm10-5v-2h2v2Zm-5,0v-2h2v2Zm-5,0v-2h2v2Z" transform="translate(2935 678)"/></g></svg>'
		
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
	$show_priority = !(empty($adminTaskSetting['Priority'])) ? $adminTaskSetting['Priority'] : 0;
	$show_priority_project = !(empty($adminYourformSetting['Priority'])) ? $adminYourformSetting['Priority'] : 0;
	?>
	<?php 
		function array_sort($array, $on, $order=SORT_ASC){
			$new_array = array();
			$sortable_array = array();

			if (count($array) > 0) {
				foreach ($array as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $k2 => $v2) {
							if ($k2 == $on) {
								$sortable_array[$k] = $v2;
							}
						}
					} else {
						$sortable_array[$k] = $v;
					}
				}

				switch ($order) {
					case SORT_ASC:
						asort($sortable_array);
					break;
					case SORT_DESC:
						arsort($sortable_array);
					break;
				}

				foreach ($sortable_array as $k => $v) {
					$new_array[$k] = $array[$k];
				}
			}

			return $new_array;
		}
		
		if( !function_exists('template_task_of_employee')){

			function template_task_of_employee($_this, $data, $class, $listStatus, $svg, $_start, $_end, $countDate, $employees, $profit_id, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived){
				if( empty($data)) return;
				$managerPC = $_this->viewVars['managerPC'];
				$project_manager = $_this->viewVars['project_manager'];
				
				$multilslect = template_multiselect($_this, $data['employee_id']);
				$task_start_date = strtotime($data['task_start_date']);  
				$task_end_date = strtotime($data['task_end_date']);
				$tmp_start = $left = 0;
				if($task_start_date > $_start){
					$left = _workingDayOFTask($_this, $_start, $task_start_date) - 1;
				}
				$tmp_start = ($task_start_date > $_start) ? $task_start_date : $_start;
				$tmp_end   = ($task_end_date > $_end) ? $_end : $task_end_date;
				$text_oppor = __('Opportunity',true);
				$text_inpro = __('In progress',true);
				$text_archi = __('Archived',true);
				$width = _workingDayOFTask($_this, $tmp_start, $tmp_end);
				$drag_item = ( $is_admin || (!empty($managerPC) && !empty($managerPC[$profit_id]) && in_array($employee_logged, $managerPC[$profit_id])) || (!empty($project_manager) && !empty($project_manager[$data['project_id']]) && $project_manager[$data['project_id']] == $employee_logged)) ? 'drag_item' : '';
				$task_html = '';
				$task_html .= '<div data-task-id ="'. $data['p_task_id'] .'" data-width= "'. $width .'" data-left= "'. $left .'" class="task-item '. $class .' '. $drag_item .'"><div class="task-item-inner"><ul>';
				$task_html .= '<li class="icon-move" style="padding-top:5px;height:40px;cursor: move;">'. $svg['icon-move'].'</li>';
				$task_html .= '<li class="task-workload">';
				$task_html .= round($data['sum_workload'], 2);
				$task_html .= '</li>';
				$task_html .= '<li class="task-title">';
				$task_html .= '<div class="project-name block" >';
				$task_html .= '<p class="pro-title">'. $data['project_name'].(($show_priority_project == 1) ? (isset($lists_pro_pri[$data['project_id']]) ? (isset($list_priority[$lists_pro_pri[$data['project_id']]]) ? ' ('.$list_priority[$lists_pro_pri[$data['project_id']]].')' : '') : '') : '').'</p>';
				$_status_class = 'status-project--' . (isset($id_pro_oppor[$data['project_id']]) ? 'oppo' : (isset($id_pro_archived[$data['project_id']]) ? 'archived' : 'inprogress'));
				$_status_name = isset($id_pro_oppor[$data['project_id']]) ? $text_oppor : (isset($id_pro_archived[$data['project_id']]) ? $text_archi : $text_inpro);
				$task_html .= '<p class="pro-status '. $_status_class .'">('.$_status_name.')</p>';
				
				$task_html .= '</div>'; // class="project-name block"
				
				$task_html .= '<p class="phase-color"><span style="background-color: '.$list_phase_color[$data['project_planed_phase_id']].'"></span></p><p class="phase-name">'. $list_phase[$data['project_planed_phase_id']].'</p><p class="task-name">'.$data['nameTask'].(($show_priority == 1) ? (!empty($data['task_priority_id']) ? (isset($list_priority[$data['task_priority_id']]) ? ' ('.$list_priority[$data['task_priority_id']].')' : '') : '') : '') .'</p>';
				
				$task_html .= '</li><li class="task-status">';
				$task_html .= template_task_status($data, $listStatus, $drag_item);
				$task_html .= '</li></ul>';
				if(!empty($drag_item)){
					$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNormalTask.call(this);" ><img src="/img/new-icon/pen.png" /></a>';
					if($data['nct']){
						$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNCTTask.call(this);" ><img src="/img/new-icon/pen-blue.png" /></a>';
					}
					$task_html .= $action_task .'<div class="assign-to"><a href="javascript:void(0);" data-is_nct = "'. $data['nct'] .'" data-pc_id="'. $profit_id .'" data-task_id="'. $data['p_task_id'] .'" onclick="openMultiselect.call(this);" class="open-multilslect">'. $svg['deplier'].'</a>'. $multilslect .'</div>';
				}
				$task_html .= '</div></div>';
				return $task_html;
			}
		}
		
		// Get workday of task in month / week.
		
		function _workingDayOFTask($_this, $start = null, $end = null){
			$countWorkdays = 0;
			$_workdays = $_this->viewVars['_dataFormat'];
			if(!empty($start) && !empty($end)){
				while($start <= $end){
					$day = strtolower(date('l', $start));
					if(!empty($_workdays[$day]) && $_workdays[$day] ==1){
						$countWorkdays++;
					}
					$start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
				}
			}
			return $countWorkdays;
		}

	function template_task_status($task = null, $listStatus = array(), $drag_item){
		if( empty($task)) return;
			ob_start();
			$task_id = $task['p_task_id'];
			$currentStatus = $task['task_status'];
			$enddate = strtotime($task['task_end_date']);
			$today = strtotime(date('d-m-Y'));
			?>
			<div class="item-status">
				<div class="status_texts">
						<?php 
						$index = 0;
						foreach($listStatus as $id => $status){ 
							$color = 'status_blue';
							if( $status['status'] == 'CL') $color = 'status_green';
							elseif( $today > $enddate )  $color = 'status_red';
							?>
					<span class="status_item status_item_<?php echo $id; ?> status_text <?php if( $id == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $id; ?>" data-text="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>"><?php echo $status['name']; ?></span>
						<?php } ?>
				</div>
				<div class="status_dots">
						<?php 
						$index = 0;
						foreach($listStatus as $id => $status){ 
							$color = 'status_blue';
							if( $status['status'] == 'CL') $color = 'status_green';
							elseif( $today > $enddate )  $color = 'status_red';
							?>
					<a href="javascript:void(0);" <?php if(!empty($drag_item)) echo 'onclick="updateTaskStatus.call(this);"'; ?> class="status_item status_item_<?php echo $id; ?> status_dot <?php if( $id == $currentStatus) echo 'active';?> <?php echo $color;?>" data-value="<?php echo $id; ?>" title="<?php echo $status['name']; ?>" data-index="<?php echo $index++;?>" data-taskid="<?php echo $task_id; ?>"></a>
						<?php } ?>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}
	if( !function_exists('template_absence_of_employee')){
		function template_absence_of_employee($_this, $data, $absences, $_start){
			if( empty($data)) return;
			ob_start();
			$left = _workingDayOFTask($_this, $_start, $data['date']) - 1;
			if(!empty($data['am']) && !empty($data['pm']) && $data['am'] == 'rejetion' &&  $data['pm'] == 'rejetion'){
				// do nothing
			}else{
				if(!empty($data['absence_am']) || !empty($data['absence_pm'])){
					?>
					<div data-width= "1" data-left= "<?php echo $left; ?>" class="absence-item">
						<ul>
							<?php if(!empty($data['absence_am']) && !empty($absences[$data['absence_am']])) {  ?>
									<li class="absence absence-am <?php echo $data['am']; ?>"><?php echo $absences[$data['absence_am']]?></li>
							<?php } 
								if(!empty($data['absence_pm']) && !empty($absences[$data['absence_pm']])) {	?>
									<li class="absence absence-pm <?php echo $data['pm']; ?>"><?php echo $absences[$data['absence_pm']]?></li>
							<?php }  ?>
						</ul>
					</div>
					<?php 
				}
				if(!empty($data['holiday'])){
					?>
					<div data-width= "2" data-left= "<?php echo $left; ?>" class="absence-item holiday-item">
						<ul><li class="absence holiday"><?php echo __('Holiday', true); ?></li></ul>
					</div>
					<?php 
				}
			}
			return ob_get_clean();
		}
	}
	if( !function_exists('template_progress')){
		function template_progress($workload, $capacity){
			$progress = 0;
			$color = '#6EAF79';
			if($capacity == 0) {
				$progress = 0;
				if($workload > $capacity){
					$color = '#F59796';
					$progress = 100;
				}
			}else{
				$progress = ($workload / $capacity) * 100;
				if($progress > 100) $progress = 100;
				$progress = round($progress , 1);
				if((int)$workload > (int)$capacity){
					$color = '#F59796';
				} 
			}
			$pg_html = '';
			$pg_html .= '<div class="progress-item">';
			$pg_html .= '<span style="background-color: '. $color .';  width: '. $progress .'%">';
			$pg_html .= '</span>';
			$pg_html .= '</div>';
			return $pg_html;
		}
	}
	function template_multiselect($_this, $data){ 
		if( empty($data)) return;
		ob_start();
		$employees = $_this->viewVars['employOfPC'];
		$pc_id = $_this->viewVars['_pc_id'];
		?>
		<div class="multiselect-filter multiselect-pm">
			<a class="wd-combobox-filter wd-combobox-pm">
				<?php if(!empty($employees)){
					foreach($employees as $id => $pmName) { 
						$exClass = (($id == $data) || ('tNotAffec_'.$id == $data)) ? 'isOwner' : '';
							if($id == $pc_id){
								?>
								<span data-is_pc = "1" data-id="<?php echo $id; ?>" class="circle-name wd-dt-<?php echo $id .' '.$exClass;  ?>" title="<?php echo strip_tags($pmName); ?>"><i class="icon-people" ></i></span>
						<?php }else{
							?>
							<span data-is_pc = "0" data-id="<?php echo $id; ?>" class="circle-name wd-dt-<?php echo $id .' '.$exClass; ?>"><img width="30" height="30" src="<?php echo $_this->UserFile->avatar($id); ?>" alt="avatar" title="<?php echo strip_tags($pmName); ?>"></span>
						<?php }
					}
				}else { ?>
					<p><?php echo __('Project Manager', true); ?></p>
				<?php } ?>
			</a>
			<div class="context-filter context-pm-filter" style="display: none; position: absolute; width: 100%; z-index: 2;"><span><input type="text" rel="no-history" placeholder = "<?php echo  __("Search...", true)?>" ></span></div>
			<div class="wd-datas list-multiselect" style="display: none;">
			<?php foreach($employees as $id => $pmName) { if($id != 'tNotAffec') {?>
				<div class="wd-data-filter wd-group-<?php echo $id;?>">
					<div class="wd-custom-checkbox">
						<label class="wd-option wd-data">
							<?php echo $_this->Form->input('project-pm', array(
								'label' => false,
								'div' => false,
								'type' => 'checkbox',
								'class' => 'checkbox',
								'rel' => 'no-history',
								'name' => 'data[project_manager_id][]',
								'checked' => ($id == $data) ? 'checked' : '',
								'value' => $id));?>
							<span class="wd-checkbox"></span>
							<span class="wd-option-name"><span class="circle-name wd-dt-<?php echo $id;?>" data-id="<?php echo $id; ?>"><img  width="30" height="30" src="<?php echo $_this->UserFile->avatar($id); ?>" alt= "avatar" title="<?php echo strip_tags($pmName); ?>"/></span><span class="employee-name"><?php echo strip_tags($pmName); ?></span></span>
						</label>
					</div>
				</div>
			<?php } } ?>
			</div>
		</div>
		<?php 
		return ob_get_clean();
	}
	function template_pc_forecast($_this, $profit_id, $workloads = array(), $employees = array(), $colspan , $dayMaps = array(), $holidays = array(), $svg = array(), $workdays = array(), $projectStatus = array(), $absences = array(), $_start, $_end, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $time_resource, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived){
		ob_start();
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
		// echo json_encode($dataView);
		if(!empty($workloads)){
			// foreach($workloads as $profit_id => $datas){
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
			// }
		}
		// $absence_all_date = ((count($workdays) + count($holidays)) > $countAbsence) ? false :  true;
		$t_body = '';
		$_rows = '';
		$fixed_workload = $fixed_capacity = 0;
		$absence_exist = array();
		foreach($dataView as $id => $workload){
			$task_list = array();
			$_left = $_right = $_row = '';
			$is_pc = ($id == 'tNotAffec_'.$profit_id) ? 1 : 0;
			$_row ="<tr class='row-employee row-loaded tdLeftRow-".$id."' data-is_pc=".$is_pc." data-employee_id=".$id.">";
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
				// }
				$_right .= '<td class="fixedWidth text-center '. $class_cell .'">';
				$dateWorkload = 0;
				$dateCapacity = 0;
				if(empty($workload_head[$time])){
					$workload_head[$time]['workload'] = 0;
					$workload_head[$time]['capacity'] = 0;
				}
				$workload_head[$time]['is_day_off'] = 0;
				if(!empty($value['holiday'])){
					$dateWorkload = '';
					$holiday = array();
					$holiday['holiday'] = $value['holiday'];
					$holiday['date'] = $time;
					$absence_html .= template_absence_of_employee($_this, $holiday, $absences, $_start);
					$workload_head[$time]['is_day_off'] = 1;
				}else if(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday'){
					$dateWorkload = '';
					$workload_head[$time]['is_day_off'] = 1;
					$_right .= '<div class="cell-style"></div>';
				}else{
					if(!empty($time_resource)){
						$sta = !empty($time_resource['Employee']['start_date']) ? $time_resource['Employee']['start_date'] : 0;
						$end = !empty($time_resource['Employee']['end_date']) ? $time_resource['Employee']['end_date'] : 0;
						if(($sta == 0) && ($end == 0))$employeeCustom[$id]['capacity'] += 1;
						else if(($sta <= $time) && ($end >= $time))$employeeCustom[$id]['capacity'] += 1;
						else if(($sta <= $time) && ($end == 0))$employeeCustom[$id]['capacity'] += 1;
					}
					$workload_head[$time]['capacity'] += 1;
					$dateCapacity = 1;
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
									$dateCapacity -= 0.5;
								}
								if(!empty($val['pm']) && $val['pm'] == 'validated'){
									$employeeCustom[$id]['capacity'] -= 0.5;
									$workload_head[$time]['capacity'] -= 0.5;
									$_is_validated_pm = 0.5;
									$fixed_capacity -= 0.5;
									$dateCapacity -= 0.5;
								}
								
								$absence_html .= template_absence_of_employee($_this, $val, $absences, $_start);
								$has_draw_absence = 1;
							}
							// If absence has validated, do not draw tasks
							// Draw tasks
							//sum workload of tasks in a day
						
							if(!empty($val['p_task_id'])){
								
								$dateWorkload += $val['workload'];
								// $employeeCustom[$id]['capacity'] +=(1/$countRow);
								$employeeCustom[$id]['workload'] += $val['workload'];
								$fixed_workload += $val['workload'];
								// profit data
								if(empty($task_list[$val['p_task_id']])) $task_list[$val['p_task_id']] = $val;
								if(empty($task_list[$val['p_task_id']]['sum_workload'])) $task_list[$val['p_task_id']]['sum_workload'] = 0;
								$task_list[$val['p_task_id']]['sum_workload'] += $val['workload'];
								
							}
						}
						
					}
					
					$cell_color = '';
					$text_color = '';
					$_height_cell = 0;
					if($dateWorkload > $dateCapacity){
						$cell_color .= 'style="height: 100%; background: #FDECEC;"';
						$text_color .= 'style="color: #F05352"';
					}else if($dateWorkload > 0){
						$_height_cell = ($dateWorkload * 100 < 100) ? $dateWorkload * 100 : 100;
						$cell_color .= 'style="height: '. $_height_cell .'%; background: #EFF6F0;"';
						$text_color .= 'style="color: #6EAF79"';
					}
					
					$_right .= '<div class="cell-style" '. $cell_color . '></div><span class="cell-value" '. $text_color .'>'. $dateWorkload . '</span>';
				}
				$_right .= '</td>';
				}
			}
			// percent
			$_left .= "<td width='440' class='tdColEmployee ab-name'><div>";
			$_e_avatar = '';
	
			$employee_name = !empty($employees[$id]) ? strip_tags($employees[$id]) : '';
			if($id != 'tNotAffec_'.$profit_id){
				$avatarEmploy = $_this->UserFile->avatar($id);
				$_e_avatar = '<img style="width: 32px; height: 32px;" src="' . $avatarEmploy . '" alt="'. $employee_name .'">';
				$_left .= '<div class="circle-name inlineblock" title="'. $employee_name .'">'. $_e_avatar .'</div>';
				
			}else{
				$employeeCustom[$id]['capacity'] = 0;
			}
			$_left .= '<span class="wd-employee-name ab-name">'. $employee_name . '</span><span class="tooltiptext">'.__('Assign to', true).' '. $employee_name.'</span>';
			$color_progress = '#6EAF79';
			if((int)$employeeCustom[$id]['workload'] > (int)$employeeCustom[$id]['capacity']) $color_progress = '#F05352';		
			$_left .= '<a class="wd-employee-data">'. $svg['deplier']. $svg['deplier-active']." <span class='wd-loading'></span></a><div class='summary employee-summary summary-ajax' id='totalWorkload-".$id."' class='tdColCapacity' style = 'color:". $color_progress ."'><p class='workload'>".round($employeeCustom[$id]['workload'], 2)."</p>/<p class='capacity1'>". round($employeeCustom[$id]['capacity'], 2)."</p>". template_progress($employeeCustom[$id]['workload'] , $employeeCustom[$id]['capacity'])."</div>";
			$_left .= "</div></td>";
			
			$_row .=  $_left . $_right;
			$_row .= "</tr>";
			$task_html = '';
			if(!empty($task_list)){
				$task_list = array_sort($task_list, 'project_name');
				foreach($task_list as $_id => $task_item){
					$task_html .= template_task_of_employee($_this, $task_item, date('l',$task_item['date']), $projectStatus, $svg, $_start, $_end, $countDate, $employees, $profit_id, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived);
				}
			}
			
				$_row .= "<tr class='row-hidden'>";
					$_row .= '<td colspan = '. $colspan .'>';
						$_row .= "<div class='task-data' style='display: block;'>";
							if(!empty($task_html) || !empty($absence_html)){
							$_row .= "<table><tbody><tr><td>";
								if(!empty($absence_html)){
									$absence_html = '<div class="absence-row">'.$absence_html.'</div>';
								}
								$_row .= $absence_html . $task_html;
							$_row .= "</td></tr></tbody></table>";
							}
						$_row .= "</div>";
					$_row .= "</td>";
				$_row .= "</tr>";
			
			
			$_rows .= $_row;
			
		}
		$t_body .= $_rows;
		echo $_rows;
		
		return ob_get_clean();
	}
	$employee_logged = $employee_info['Employee']['id'];
	$is_admin = $employee_info['Role']['name'] ==  'admin' ? 1 : 0; 
    echo json_encode(template_pc_forecast($this, $_pc_id, $workloads, $_employees, $_colspan + 1 , $_dayMaps, $_holidays, $svg, $_workdays, $projectStatus, $_absences, $_start, $_end, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $time_resource, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived));
?>