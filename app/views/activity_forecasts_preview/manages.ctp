<?php echo $html->css('preview/activity_forcast'); ?>
<?php echo $html->css('add_popup'); ?>
<?php echo $html->script('jquery-ui.multidatespicker');
	  echo $html->script('jquery-ui.min');
 ?>
<script type="text/javascript">
	$.datepicker.regional.en = {
		closeText: <?php echo json_encode(__('OK', true)) ?>,
		prevText: <?php echo json_encode(__('Previous', true)) ?>,
		nextText: <?php echo json_encode(__('Next', true)) ?>,
		currentText: <?php echo json_encode(__('Today', true)) ?>,
		dayNames: <?php echo json_encode(array(__('Sunday', true), __('Monday', true), __('Tuesday', true), __('Wednesday', true), __('Thursday', true), __('Friday', true), __('Saturday', true))) ?>,
		dayNamesShort: <?php echo json_encode(array(__('Sun', true), __('Mon', true), __('Tues', true), __('Wed', true), __('Thu', true), __('Fri', true), __('Sat', true))) ?>,
		dayNamesMin: <?php echo json_encode(array(__('Su', true), __('Mo', true), __('Tu', true), __('We', true), __('Th', true), __('Fr', true), __('Sa', true))) ?>,
		monthNames: <?php echo json_encode(array(__('January', true), __('February', true), __('March', true), __('April', true), __('May', true), __('June', true), __('July', true),  __('August', true),  __('September', true),  __('October', true),  __('November', true),  __('December', true))) ?>,
		monthNamesShort: <?php echo json_encode(array(__('Jan', true), __('Feb', true), __('Mar', true), __('Apr', true), __('May', true), __('Jun', true), __('Jul', true),  __('Aug', true),  __('Sep', true),  __('Oct', true),  __('Nov', true),  __('Dec', true))) ?>,
		//dateFormat: "dd/mm/yyyy",
		firstDay: 1
	};
	$.datepicker.setDefaults( $.datepicker.regional.en );
</script>
<?php 
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
	if(!empty($_start)){
		$dayMaps = array(
			'monday' => $_start,
			'tuesday' => $_start + DAY,
			'wednesday' => $_start + (DAY * 2),
			'thursday' => $_start + (DAY * 3),
			'friday' => $_start + (DAY * 4)
		);
		$listDays = array_values($dayMaps);
	}
	$keyProfitCenter = !empty($profit['id']) ? $profit['id'] : 0; // khong dc xoa
	function caculateTwoNumber($x,$y)
	{
		$x=number_format($x,2, '.', '');
		$y=number_format($y,2, '.', '');
		return $x-$y;
	}
	$rowFixed = $fixed_head = '';
	$project_id = 0;
	$show_workload = !(empty($adminTaskSetting['Workload'])) ? $adminTaskSetting['Workload'] : 0;
	$show_priority = !(empty($adminTaskSetting['Priority'])) ? $adminTaskSetting['Priority'] : 0;
	$show_priority_project = !(empty($adminYourformSetting['Priority'])) ? $adminYourformSetting['Priority'] : 0;
	
	 $theme_colors = array(
		'oppo' => '#666',
		'archived' => '#6EAF79',
		'inprogress' => '#217FC2',
	 );
?>
<style>
	#wd-container-main {
		max-width: 1920px;
	}
	.task-item ul li.task-title{
		width: calc(100% - 265px);
		min-width: 270px;
		margin-right: 3px;
		margin-top: 4px;
	}
	.task-item ul li.task-title p.task-name, .task-item ul li.task-title .project-title{
		text-overflow: ellipsis;
		overflow: hidden;
	}
	.forcasts-actions {
		display: inline-block;
	}
	div#display-oppor-switch {
		display: inline-block;
	}
	.wd-input.wd-checkbox-switch label .wd-btn-switch:before {
		top: -2px;
	}
	.wd-input.wd-checkbox-switch input:checked ~ label .wd-btn-switch:before {
		top: -2px;
	}
	.task-item ul li.task-title p.task-name {
		display: inline-block;
		max-width: 150px;
		vertical-align: middle;
		margin-top: unset;
	}
	.task-item ul li.task-title span {
		line-height: 25px;
		background-color: #217FC2;
		display: inline-block;
		width: 3px;
		height: 20px;
		vertical-align: top;
		margin-right: 5px;
	}
	p.phase-name {
		font-size: 10px !important;
		display: inline-block;
		padding-right: 4px;
		vertical-align: middle;
	}
	p.phase-color {
		display: inline-block;
	}
	.task-title .pro-title{
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.task-title .pro-status{
		flex-grow: 1;
		padding-left: 6px;
	}
	.task-title .project-name{
		display: flex;
		justify-content: flex-start;
		margin-bottom: 4px;
	}
	.block{
		display: block;
	}
	<?php foreach( $theme_colors as $status_name => $hex_color){?>
		.task-title .pro-status.status-project--<?php echo $status_name;?> {
			color: <?php echo $hex_color;?>;
		}
	<?php } ?>
</style>
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
			$employOfPC = $_this->viewVars['employOfPC'][$profit_id];
			$managerPC = $_this->viewVars['managerPC'];
			$project_manager = $_this->viewVars['project_manager'];
			$pc_list = $_this->viewVars['pc_list'];
			$employOfPC[$profit_id] = $pc_list[$profit_id];
			// $resources = $_this->viewVars['resources'];
			$multilslect = template_multiselect($_this, $employOfPC, $data['employee_id']);
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
			$drag_item = ($is_admin || (!empty($managerPC) && !empty($managerPC[$profit_id]) && in_array($employee_logged, $managerPC[$profit_id])) || (!empty($project_manager) && !empty($project_manager[$data['project_id']]) && $project_manager[$data['project_id']] == $employee_logged)) ? 'drag_item' : '';
			$task_html = '';
			$task_html .= '<div data-task-id ="'. $data['p_task_id'] .'" data-width= "'. $width .'" data-left= "'. $left .'" class="task-item '. $class .' '. $drag_item .'"><div class="task-item-inner"><ul>';
			$task_html .= '<li class="icon-move" style="padding-top:5px;height:40px;cursor: move;">'. $svg['icon-move'].'</li>';
			$task_html .= '<li class="task-workload">';
			$task_html .= $data['sum_workload'];
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
				$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNormalTask.call(this);" ><img src="../../img/new-icon/pen.png" /></a>';
				if($data['nct']){
					$action_task = '<a class="wd-edit-task" href="javascript:void(0);" data-project_id = "'. $data['project_id'] .'" data-task_id="'. $data['p_task_id'] .'" onclick="editNCTTask.call(this);" ><img src="../../img/new-icon/pen-blue.png" /></a>';
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
		$workdays = $_this->viewVars['workdays'];
		if(!empty($start) && !empty($end)){
			while($start <= $end){
				$day = strtolower(date('l', $start));
				if(!empty($workdays[$day]) && $workdays[$day] ==1){
					$countWorkdays++;
				}
				$start = mktime(0, 0, 0, date("m", $start), date("d", $start)+1, date("Y", $start));
			}
		}
		return $countWorkdays;
	}
?>
<?php 
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
?>
<?php 
	if( !function_exists('template_progress')){
		function template_progress($workload, $capacity, $isPC = 0, $pc_id = null, $date = null ){
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
				$progress = round($progress , 2);
				if($workload > $capacity) $color = '#F59796';
			}
			$pg_html = '';
			if($isPC != 2){
				$ex_class = ($isPC) ? 'profit-summary' : 'employee-summary';
				$ex_class .= ($date) ? ' profit-month' : '';
				$ex_actions = ($pc_id) ? 'data-pc_id = '. $pc_id .' href="javascript:void(0);" onclick="getPCWorkload.call(this);" ' : '';
				if($date) $ex_actions = 'target="_blank" href="/activity_forecasts_preview/manages/month/'. $pc_id .'/'. $date . '" ';
				$pg_html .= '<a '. $ex_actions .'class="summary '. $ex_class .' tdColCapacity" style = "color:'. $color .'">';
				$pg_html .= "<p class='workload'>". round($workload, 2) ."</p>/<p class='capacity'>". round($capacity, 2)."</p>";
			}
			$pg_html .= '<div class="progress-item">';
			$pg_html .= '<span style="background-color: '. $color .';  width: '. $progress .'%">';
			$pg_html .= '</span></div>';
			if($isPC) $pg_html .= '<p class="percent" style="color:'. $color .'">'.$progress.'%</p>';
			$pg_html .= '</a>';
			return $pg_html;
		}
	}
?>
<?php 

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
function template_multiselect($_this, $employees, $data){ 
	if( empty($employees)) return;
	$pc_list = $_this->viewVars['pc_list'];
	ob_start();
	?>
	<div class="multiselect-filter multiselect-pm">
		<p class="field-title"><?php echo __('Project Manager', true); ?></p>
		<a class="wd-combobox-filter wd-combobox-pm">
			<?php if(!empty($employees)){
				foreach($employees as $id => $empName){
					$exClass = (($id == $data) || ('tNotAffec_'.$id == $data)) ? 'isOwner' : '';
					if(!empty($pc_list[$id])){
						?>
						<span data-is_pc = "1" data-id="<?php echo $id; ?>" class="circle-name wd-dt-<?php echo $id .' '.$exClass;  ?>" title="<?php echo strip_tags($empName); ?>"><i class="icon-people" ></i></span>
				<?php
					}else{
					?>
					<span data-is_pc = "0" data-id="<?php echo $id; ?>" class="circle-name wd-dt-<?php echo $id .' '.$exClass; ?>"><img width="30" height="30" src="<?php echo $_this->UserFile->avatar($id); ?>" alt="avatar" title="<?php echo strip_tags($empName); ?>"></span>
				<?php } 
				}			
			}?>
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
function template_pc_forecast($_this, $profit_id, $workloads = array(), $employees = array(), $colspan = 8, $dayMaps = array(), $holidays = array(), $svg = array(), $pc_list = array(), $workdays = array(), $projectStatus = array(), $absences = array(), $_start, $_end, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived){
	ob_start(); 
	if(!empty($workloads) && !empty($employees)) : 
	unset($workloads['employees']);
	?>
	<tr class="elmTemp">
		<td class="elmTemp" colspan = "<?php echo $colspan;?>">
			<div class="tbl-tbody" >
				<table>
					<?php
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
								$_left = $_right = $_row = '';
								$is_pc = ($id == 'tNotAffec_'.$profit_id) ? 1 : 0;
								$_row ="<tr class='row-employee row-loaded tdLeftRow-".$id."' data-is_pc = '".$is_pc."'  data-employee_id = '".$id."'>";
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
										$employeeCustom[$id]['capacity'] += 1;
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
													if($val['am'] == 'validated'){
														$employeeCustom[$id]['capacity'] -= 0.5;
														$workload_head[$time]['capacity'] -= 0.5;
														$fixed_capacity -= 0.5;
														$_is_validated_am = 0.5;
														$dateCapacity -= 0.5;
													}
													if($val['pm'] == 'validated'){
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
													$workload_head[$time]['workload'] += $val['workload'];
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
								// percent
								$_left .= '<span class="wd-employee-name">'. $employee_name . '</span><span class="tooltiptext">'.__('Assigner to', true).' '. $employee_name.'</span>';
								
								$_left .= '<a class="wd-employee-data">'. $svg['deplier']. $svg['deplier-active'].'<span class="wd-loading"></span></a>'. template_progress($employeeCustom[$id]['workload'] , $employeeCustom[$id]['capacity'], $isPC = 0);
								$_left .= "</div></td>";
								
								$_row .=  $_left . $_right;
								$_row .= "</tr>";
								$task_html = '';
								$task_list = array_sort($task_list, 'project_name');
								if(!empty($task_list)){
									foreach($task_list as $_id => $task_item){
										$task_html .= template_task_of_employee($_this, $task_item, date('l',$task_item['date']), $projectStatus, $svg, $_start, $_end, $countDate, $employees, $profit_id, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived);
									}
								}
								$_row .= "<tr class='row-hidden'>";
									$_row .= '<td colspan = '. $colspan .'>';
										$_row .= "<div class='task-data'>";
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
						// exit;
						if(!empty($workload_head)){
							foreach($workload_head as $time=> $value){
								$class_cell = '';
								if(!(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday')){
										$class_cell = date('l',$time);
									// }
									$t_head_right .= '<td class="fixedWidth text-center '. $class_cell .'">';
									if($value['is_day_off']){
										// nothing
										// $t_head_right .= '<div> </div>';
										if(date('l',$time) == 'Sunday' || date('l',$time) == 'Saturday'){
											$t_head_right .= '<div class="cell-style"></div>';
										}
									}else{
										// Sum capacity of resource - capacity of affected 
										// capacity of affected  = 1 
										$capEffect  = $isEffected ? 1 : 0; 
										$t_head_right .= round($value['workload'], 2) .'/'. ($value['capacity'] - $capEffect);
									}
									$t_head_right .= template_progress($value['workload'] , $value['capacity'], $isPC = 2);
									$t_head_right .= "</td>";
								}
							}
							
						}
						
						$t_head .= "<td width='440' class='row-pc'><div><a class='pc-arrow'>". $svg['arrow-right'] . $svg['arrow-bottom'] ."</a><span class='pc-name' title='". str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$profit_id])."'>". str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$profit_id])."</span>";
						$t_head .= template_progress($fixed_workload , $fixed_capacity, $isPC = 1) ."</div>";
						$t_head .= $svg['ascending'] .'</td>'. $t_head_right . "</tr>";
					// }
					?>
			
					<tbody id="absence-table">	
						<?php echo $t_head; ?>
						<tr class="trEmployee trEmployee-<?php echo $profit_id; ?>"><td colspan="<?php echo $colspan;?>">
							<div class="employee-data">
								<table>
									<tbody>
										<?php echo $t_body; ?>
									</tbody>
								</table>
							</div>
						</td></tr>
					</tbody>
				</table>
			</div>
		</td>
	</tr>
	<?php 
	endif;
	return ob_get_clean();
	
	}
?>
<?php 
	if( !function_exists('template_forecast_summary')){
		function template_forecast_summary($_this, $workloads, $resources, $pc_list, $employees, $colspan, $svg = array()){
			ob_start(); 
			
			foreach($resources as $pc_id => $resource){ ?>
				<tr class="elmTemp">
					<td class="elmTemp" colspan = "<?php echo $colspan;?>">
						<div class="tbl-tbody">
						<table>
							<tbody id="absence-table">	
								<tr class="trPC trPC-<?php echo $pc_id; ?>">
									<td width='440' class='row-pc'>
										<div>
											<a class='pc-arrow'><?php echo $svg['arrow-right']; echo $svg['arrow-bottom']; ?></a>
											<span class="pc-name" title="<?php echo str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$pc_id])?>"><?php echo str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$pc_id])?></span>
											<?php 
												$pc_workload = !empty($workloads[$pc_id]['workload']) ? round($workloads[$pc_id]['workload'], 2) : 0;
												$pc_capacity = !empty($workloads[$pc_id]['capacity']) ? round($workloads[$pc_id]['capacity'], 2) : 0;
												echo template_progress($pc_workload, $pc_capacity, $isPC = 1, $pc_id);
												?>
										</div>
										<?php echo $svg['ascending']; ?>
									</td>
									<td colspan = "<?php echo $colspan;?>"></td>
								</tr>
								<?php if(!empty($resource)){
								
								?>
									<tr class="trEmployee trEmployee-<?php echo $pc_id; ?>"><td colspan="2">
										<div class="employee-data">
											<table>
												<tbody>
													<?php foreach($resource as $key => $employe_id){
														$is_pc = ($employe_id == 'tNotAffec_'.$pc_id) ? 1 : 0;
													?>
													<tr class="row-employee tdLeftRow-<?php echo $employe_id; ?>" data-is_pc = "<?php echo $is_pc ?>" data-employee_id = "<?php echo $employe_id; ?>">
														<td width="440" class="tdColEmployee ab-name">
															<div>
																<?php if($employe_id != 'tNotAffec_'.$pc_id){?>
																	<div class="circle-name inlineblock">
																	<img style="width: 32px; height: 32px;" src="<?php echo $_this->UserFile->avatar($employe_id)?>" alt="<?php echo strip_tags($employees[$employe_id]);?>"></div>
																<?php } ?>
																<span class="wd-employee-name"><?php echo $employees[$employe_id]?></span><span class="tooltiptext"><?php echo __('Assign to', true) .' '. strip_tags($employees[$employe_id]); ?></span>
																<a href="javascript:void(0);" data-pc-id="<?php echo $pc_id; ?>" data-employee-id = "<?php echo $employe_id; ?>" class="wd-employee-data">
																	<?php echo $svg['deplier']; echo $svg['deplier-active']; ?>
																	<span class="wd-loading"></span>
																</a>
																<?php
																	$e_workload = !empty($workloads[$employe_id]['workload']) ? round($workloads[$employe_id]['workload'], 2) : 0;
																	$e_capacity = !empty($workloads[$employe_id]['capacity']) ? round($workloads[$employe_id]['capacity'], 2) : 0;
																	echo template_progress($e_workload, $e_capacity, $isPC = 0); ?>
															</div>
														</td>
														<td colspan = "<?php echo $colspan;?>"></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</td></tr>
								<?php } ?>
							</tbody>
						</table>	 
						</div>
					</td>
				</tr>
			<?php }
			return ob_get_clean();
		}
	}

?>
<?php 
	if( !function_exists('template_forecast_year')){
		function template_forecast_year($_this, $workloads, $pc_list, $svg = array(), $_start, $_end){
			ob_start(); 
			$pc_workload = 0;
			$pc_capacity = 0;
			
			foreach($workloads as $pc_id => $values){
				$pc_workload = !empty($values['workload']) ? array_sum($values['workload']) : 0;
				$pc_capacity = !empty($values['capacity']) ? array_sum($values['capacity']) : 0; ?>
				<tr class="elm-Temp">
					<td class="elmTemp" colspan = "13">
						<div class="tbl-tbody">
						<table>
							<tbody id="absence-table">	
								<tr class="trPC trPC-<?php echo $pc_id; ?>">
									<td width='440' class='row-pc'>
										<div>
											<span class="pc-name" title="<?php echo str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$pc_id])?>"><?php echo str_replace('&nbsp;&nbsp;&nbsp;|-&nbsp;', '',$pc_list[$pc_id])?></span>
											<?php echo template_progress($pc_workload, $pc_capacity, $isPC = 1, $pc_id); 	?>
										</div>
									</td>
									<?php 
										$tmp_start = $_start;
										while ($tmp_start < $_end) {
											$m_pc_workload = !empty($values['workload'][$tmp_start]) ? $values['workload'][$tmp_start] : 0;
											$m_pc_capacity = !empty($values['capacity'][$tmp_start]) ? $values['capacity'][$tmp_start] : 0;
											echo '<td class="fixedWidth text-center">'. template_progress($m_pc_workload, $m_pc_capacity, $isPC = 1, $pc_id, $tmp_start) .'</td>';
											$tmp_start = mktime(0, 0, 0, date("m", $tmp_start) + 1, date("d", $tmp_start), date("Y", $tmp_start));
										}
									?>
								</tr>
							</tbody>
						</table>	 
						</div>
					</td>
				</tr>
			<?php }
			return ob_get_clean();
		}
	} 
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content <?php echo $typeSelect;?>">
            <div class="wd-list-project">
				<div class="forcasts-actions">
					<?php echo $this->Form->create('Control', array(
                                'type' => 'POST',
                                'url' =>$this->Html->url(),
							));
                            ?>
							<ul id="typeRequest">
								<li>
									<label><input rel="no-history" type="radio"  <?php if($typeSelect == 'week') echo 'checked'; ?> value="week" name="data[date_type]" /><span><?php echo __('Week',true);?></span></label>
								</li>
								<li>
									<label><input rel="no-history"  type="radio" <?php if($typeSelect == 'month') echo 'checked'; ?> value="month" name="data[date_type]" /><span><?php echo __('Month',true);?></span></label>
								</li>
								<li>
									<label><input rel="no-history"  type="radio" <?php if($typeSelect == 'year') echo 'checked'; ?> value="year" name="data[date_type]" /><span><?php echo __('Year',true);?></span></label>
								</li>
							</ul>
							
							<label id="dateRequest">
								
								<input id='date-range-picker' rel="no-history" class="wd-" type="text" name="data[date]" value="<?php echo $date_selected; ?>">
								<?php echo $svg['agenda']; ?>
							</label>
							<?php if(empty($isDiary)):?>
							<div class="wd-listPC">
							<ul id = "listPC">
								<li class="listpc-selected">
									<?php if(!empty($pc_list)) {
									$item_replace =  array("&nbsp;", "|-");
									foreach($pc_list as $id => $name){ 
										if(!empty($profiltId) && in_array($id, $profiltId)) { ?>
											<span data-id="<?php echo $id; ?>"><?php echo str_replace($item_replace, '', $name) .', ';?></span>
										<?php } 
									} ?>
									<input type="hidden" name="-1" value="-1">
									<?php } ?>
								</li>
								<ul>
									<?php if(!empty($pc_list)) {
									foreach($pc_list as $id => $name){
										$check = '';
										if(!empty($profiltId) && in_array($id, $profiltId)) $check = 'checked';
									?>
										<li>
											<label> 
												<input <?php echo $check; ?> type="checkbox" class="checkbox" id="pc-<?php echo $id;?>" name="data[pcList][]" value="<?php echo $id;?>"/>
												<span><?php echo $name;?></span>
											</label>
										</li>
									<?php } 
									} ?>
								</ul>
							</ul>
							<?php $show_alert = !empty($profiltId) && count($profiltId) > 10 ? '' : 'display: none';?>
							<div class="wd-listPC-alert" style="<?php echo $show_alert; ?>">
								<i class="icon-info"></i>
								<span class="listPC-alert-content">
									<?php echo __("For a better experience and reactivity, it's better to select less than 10 teams"); ?>
								</span>
							</div>
							</div>
							<button type="submit" id="submit" title="<?php __('OK'); ?>"><?php echo $svg['submit']; ?></button>
							<?php endif; ?>
					<?php echo $this->Form->end(); ?>
					<div class="wd-action-switch">
						<div id="wd-switch-wlzero">
							<?php 
							echo $this->Form->input('Project.displayWlzero', array(
								'type' => 'checkbox',
								'label' => false,
								'rel' => 'no-history',
								'checked'=> !empty($displayWlzero) ? true : false,
								'id' => 'displayWlzero',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch',
									'title' => (!empty($displayWlzero) && $displayWlzero == 1) ? __('Tasks with workload = 0 displayed, unselect to hidde the tasks = 0',true) : __('The tasks with workload = 0 are not displayed, select to display',true),
									),
								'type' => 'checkbox', 
							));
							?>
						</div>
						<div id="display-oppor-switch">
							<?php 
							echo $this->Form->input('Project.displayProOppor', array(
								'type' => 'checkbox',
								'label' => false,
								'rel' => 'no-history',
								'checked'=> !empty($displayProOppor) ? true : false,
								'id' => 'displayProOppor',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch',
									'title' => (!empty($displayProOppor) && $displayProOppor == 1) ? __('Project in "Opportnity" are displayed, deselect to see only the "In progress  projects"',true) : __('Project in "Opportnity" are not displayed, select to see the "In progress  projects" and "Opportunity"',true),
									),
								'type' => 'checkbox', 
							));
							?>
						</div>
						<div id="display-archived-switch">
							<?php 
							echo $this->Form->input('Project.displayProArchived', array(
								'type' => 'checkbox',
								'label' => false,
								'rel' => 'no-history',
								'checked'=> !empty($displayProArchived) ? true : false,
								'id' => 'displayProArchived',
								'div' => array(
									'class' => 'wd-input wd-checkbox-switch',
									'title' => (!empty($displayProArchived) && $displayProArchived == 1) ? __('Project in "Archived" are displayed',true) : __('Project in Archived are not displayed, select to see them in forecast',true),
									),
								'type' => 'checkbox', 
							));
							?>
						</div>
					</div>
				</div>
				
				<div id="message-place">
					<?php
					App::import("vendor", "str_utility");
					$str_utility = new str_utility();
					echo $this->Session->flash();
					?>
				</div>
				<?php if(!empty($pc_list)) { ?>
				<div id="absence-wrapper">
					<div id="absence-scroll" >
						<table id="absence" class="<?php echo (!empty($isDiary) ? 'isDiary' : '');?>">
							<tbody>
							<tr class="week-heading">
								<th width="440" rowspan="2"><div style="width: 440px;"></div></th>
								<?php 
								if($typeSelect=='week'){
									if(!empty($workdays)):
										$format_workday = array_combine(array_values($dayMaps),array_values($workdays));
										$i = 0;
										$n = count($format_workday);
										foreach($format_workday as $key => $val): 
											if(!(date('N', $key) == 6 || date('N', $key) == 7)){
												if($i == 0 || date('N', $key) == 1){
													$colSpan = 6 - date('N', $key);
													echo '<th colspan="'.$colSpan.'">';
													echo '<div class="week-heading clearfix"><p class="date-of-week">'. date('d', $key) . ' - ';
												}
												if($i == $n || date('N', $key) == 5){
													echo date('d', $key). ' ' . __(date('F', $key), true) .' '. date('Y', $key) .'</p>';
													echo '<p class="week-number">'. sprintf(__('Week', true)) .'<span>'. date('W', $key) .'</span></p></div>';
													echo '</th>';
												}								
												$i++;
											}
										endforeach;
									endif;
								}else if($typeSelect=='month'){ 
									if(!empty($dayWorks)):
									
										$i=0;
										$n = count($dayWorks);
										foreach($dayWorks as $key => $val):
										
											if(!(date('N', $val[2]) == 6 || date('N', $val[2]) == 7)){
												
												if($i == 0 || date('N', $val[2]) == 1){
													$colSpan = 6 - date('N', $val[2]);
													$ex_class = '';
													if($n - $i < 5){
														$colSpan = $n - $i;
													}
													$ex_class = ($colSpan == 1) ? 'one-day' : '';
													echo '<th colspan="'.$colSpan.'">';
													echo '<div class="week-heading '. $ex_class .'"><p class="date-of-week">'. date('d', $val[2]) . ' - ';
												}
												if($i == $n - 1 || date('N', $val[2]) == 5){
													$date_F = date('F', $val[2]);
													echo date('d', $val[2]). ' ' . __( $date_F, true) .' '. date('Y', $val[2]) .'</p>';
													echo '<p class="week-number">'. sprintf(__('Week', true)) .'<span>'. date('W', $val[2]) .'</span></p></div>';
													echo '</th>';
												}								
												$i++;
											}
										endforeach;
									endif;
								} else {
									if(date('Y', $_start) == date('Y', $_end)){
										echo '<th colspan="12"><div class="week-heading"><p>'. date('Y', $_start) .'</p></div></th>';
									}else {
										$start_colspan = 13 - date('m', $_start);
										$end_colspan = 12 - $start_colspan;
										echo '<th colspan="'. $start_colspan .'"><div class="week-heading"><p>'. date('Y', $_start) .'</p></div></th>';
										echo '<th colspan="' . $end_colspan . '"><div class="week-heading"><p>'. date('Y', $_end) .'</p></div></th>';
									}
									
								} ?>
							</tr>
							<tr class="elm-Temp">
								<?php
									$dataFormat = $workdays;
									if($typeSelect=='week'){
										$countWorkdays = 0;
										if(!empty($workdays)):
											$workdays = array_combine(array_values($dayMaps),array_values($workdays));
											foreach($workdays as $key => $val):
												$countWorkdays++;
												$dayMaps[$key] = $key; 
												$subDay = substr(date('l', $key), 0 , 2);
												$class_cell = '';
												if(!(date('l',$key) == 'Sunday' || date('l',$key) == 'Saturday')){
													$class_cell = date('l',$key);
													?>
													<th class="fixedWidth <?php echo $class_cell; ?>" id="<?php echo 'fore'.$countWorkdays;?>"><span><?php echo __($subDay, true); ?><?php __(date(' d ', $key)); ?><span></th>
													<?php
												}
											endforeach;
										endif;
									
									?>
								<?php }else if($typeSelect=='month') { ?>
								<?php
									$workdaysTmp = array();
									
									if(!empty($dayWorks)):
										$dayMaps = array();
										$i=0;
										foreach($dayWorks as $key => $val):
											$keyTmp = $val[1];
											if(!in_array($val[1], $workdays)){
												$keyTmp = $val[2];
											}
											$workdaysTmp[$keyTmp] = 1;
											$dayMaps[$keyTmp] = $val[2];
											$subDay = substr(date('l', $val[2]), 0 , 2);
											$class_cell = '';
											if(!(date('l',$val[2]) == 'Sunday' || date('l',$val[2]) == 'Saturday')){
												$class_cell = date('l',$val[2]);
											// }
											?>
											<th class="fixedWidth <?php echo $class_cell; ?>" id="<?php echo 'fore'.ucfirst($key);?>">
												<span><?php echo __($subDay, true); ?><?php __(date(' d ', $val[2])); ?></span>
											</th>
											<?php										
											$i++; 
											}
										endforeach;
									endif;?>
								<?php $workdays = $workdaysTmp; $countWorkdays = count($workdays); 
								}else {
									$tmp_start = $_start;
									while ($tmp_start < $_end) {
										echo '<th class="fixedWidth" ><div class="week-heading"><span>'. __(date('F', $tmp_start), true) .'</span></div></th>';
										$tmp_start = mktime(0, 0, 0, date("m", $tmp_start) + 1, date("d", $tmp_start), date("Y", $tmp_start));
										$countWorkdays = 12;
									}
								}
								$colspan = $countWorkdays + 1;
								
								?>
							</tr>
							<?php
								$employee_logged = $employee_info['Employee']['id'];
								$is_admin = $employee_info['Role']['name'] ==  'admin' ? 1 : 0; 
								if($typeSelect =='week' || (!empty($isDiary) && $isDiary)){
									if(!empty($profiltId)){
										foreach($profiltId as $key => $pc_id){
											if(!empty($workloads[$pc_id])){
												$employees = $workloads[$pc_id]['employees'];
												echo sprintf('%s', template_pc_forecast($this, $pc_id, $workloads[$pc_id], $workloads[$pc_id]['employees'], $colspan, $dayMaps, $holidays, $svg, $pc_list, $workdays, $projectStatus, $absences, $_start, $_end, $employee_logged, $is_admin, $id_pro_oppor, $list_phase, $list_phase_color, $lists_pro_pri, $list_priority, $show_priority, $show_priority_project, $id_pro_archived));
											}
										}
									}
								}else if($typeSelect =='month'){
									echo sprintf('%s', template_forecast_summary($this, $workloads, $resources, $pc_list, $employees, $colspan, $svg));
								}else{
									echo sprintf('%s', template_forecast_year($this, $workloads, $pc_list, $svg,$_start, $_end));
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div id="addProjectTemplate" class="loading-mark"></div>
<?php
	$isDiary = !empty($isDiary) ? $isDiary : 0;
 ?>
<script type="text/javascript">
	var wdTable = $('#absence-wrapper');
	var isDiary = <?php echo json_encode($isDiary); ?>;
	var _start = <?php echo json_encode($_start); ?>;
	var _end = <?php echo json_encode($_end); ?>;
	var _absences = <?php echo json_encode($absences); ?>;
	var _workdays = <?php echo json_encode($workdays); ?>;
	var _dataFormat = <?php echo json_encode($dataFormat); ?>;
	var _holidays = <?php echo json_encode($holidays); ?>;
	var _colspan = <?php echo json_encode($colspan); ?>;
	var _dayMaps = <?php echo json_encode($dayMaps); ?>;
	var _resources = <?php echo json_encode($resources); ?>;
	var _pc_list = <?php echo json_encode($pc_list); ?>;
	var typeSelect = <?php echo json_encode($typeSelect); ?>;
	var show_workload = <?php echo json_encode($show_workload); ?>; 
	
	$(window).resize(set_table_height);
	
	function set_table_height(){
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - 20;
			wdTable.css({
				height: heightTable,
			});
		}
	}
	$('#listPC').on('click', function(e){
		if(!($('#listPC ul').find(e.target).length )){
			$('#listPC').find('ul').toggle();
		}
	});
	$('body').on('click', function(e){
		if(!($(e.target).parents().andSelf().is('#listPC'))){
			$('#listPC ul').hide();
		}
		
	});
	function toggleTask(elm){
		var _this = $(elm);
		_row_employee =_this.closest('.row-employee');
		if( _row_employee.hasClass('active')){
			$('.task-data').slideUp();
			$('tr.row-hidden').removeClass('active');
			$('tr.row-employee').removeClass('active');
			
		}else{
			_row_employee.siblings('.row-employee').each(function( i, _tr){
				var _row = $(_tr);
				if( _row.hasClass('active')){
					_row.next('.row-hidden').find('.task-data').slideUp();
					_row.removeClass('active');
					_row.next('.row-hidden').removeClass('active')
					
				}
			});
			_row_employee.addClass('active');
			_row_employee.next('.row-hidden').addClass('active').find('.task-data').slideDown();
		}
		
		
	}
	
	$('#absence').on('click', '.wd-employee-data', function(e){
		e.preventDefault();
		var _employee_id = $(this).data('employee-id');
		var _pc_id = $(this).data('pc-id');
		var _this = $(this);
		getEmployeeWorkload(_this, _employee_id, _pc_id, 0);
	});
	function getEmployeeWorkload(_this, _employee_id, _pc_id, re_assign){
		var _row_class = '';
		if(_pc_id){
			_row_class = '.row-employee.tdLeftRow-'+_employee_id;
		}else{
			_row_class = '.' + ($.trim( _this.closest('.row-employee').attr('class').replace('active', '')).split(' ').join('.'));
		}
		if(_employee_id){
			_this.find('.wd-loading').show();
			_this.find('svg').hide();
			$.ajax({
				url: '/activity_forecasts_preview/get_forecast_employee/',
				cache : true,
				type: 'post',
				dataType: 'json',
				// async: false,
				data: {
					pc_id : _pc_id,
					employe_id: _employee_id,
					start: _start,
					end: _end,
					absences: _absences,
					workdays: _workdays,
					dataFormat: _dataFormat,
					holidays: _holidays,
					colspan: _colspan,
					dayMaps: _dayMaps,
				},
				success: function(data){
					if(data){	
						if(re_assign){
							$('.row-employee.tdLeftRow-'+_employee_id).next('.row-hidden').remove();
						}else{
							data = $(data);
							data.find('.task-data').hide();
						}
						$('.row-employee.tdLeftRow-'+_employee_id).after(data).remove();
						initWidthTask('.absence-item');
						initWidthTask('.task-item');
						multiSelectFilter();
						_this.find('.wd-loading').hide();
						_this.find('svg').show();
						// re_assign = 2 refresh ajax when re-assign-table
						// re_assign = 0 toggle tasks when open data tasks of employee.
						if(re_assign == 2 || re_assign == 0){
							toggleTask($(_row_class));
						}
						$('.active.status_red').closest('.task-item').addClass('task-red');
						$('.active.status_green').closest('.task-item').addClass('task-green');
						dragAndDrop();
						reSizeTask();
					}
				},
				complete: function(){
					
				},
			});
		}else{
			toggleTask($(_row_class));
		}
	}
	function getPCWorkload(){
		if(typeSelect == 'year') return;
		var _this = $(this);
		var pc_id = _this.data('pc_id');
		data = {
			pc_id: pc_id,
			start: _start,
			end: _end,
			resources: _resources[pc_id],
			absences: _absences,
			workdays: _workdays,	
			dataFormat: _dataFormat,
			holidays: _holidays,
			colspan: _colspan,
			dayMaps: _dayMaps,
			pc_name: _pc_list[pc_id],
		};
		_this.addClass('loading');
		$.ajax({
			url: 'getForecastTeam/',
			cache : true,
			type: 'post',
			dataType: 'json',
			data: data,
			success: function(data){
				_this.closest('.trPC').after(data).remove();
				$('.trEmployee-' + pc_id + ' > td').attr('colspan', _colspan);
			},
			complete: function(){
				_this.removeClass('loading');
			},
		});
		
	}
	
	$('.active.status_red').closest('.task-item').addClass('task-red');
	$('.active.status_green').closest('.task-item').addClass('task-green');
	$('.trEmployee').hide();
	
	$('#dateRequest').on('click', function(e){
		$('#date-range-picker').show();
	});
	// function toggleEmployes(elm){
		// var _this = $(elm);
		$('#absence').on('click', '.row-pc .pc-arrow, .row-pc > svg', function(e){
			_trPC = $(this).closest('.trPC');
			if(!(_trPC.hasClass('active'))){
				$('.trEmployee').hide();
				$('.trEmployee').removeClass('active');
				$('.trPC').removeClass('active');
				_trPC.addClass('active');
				_trPC.next('tr.trEmployee').addClass('active');
				_trPC.next('tr.trEmployee').show();
			}else{
				$('.trEmployee').hide();
				$('.trEmployee').removeClass('active');
				$('.trPC').removeClass('active');
			}
		});
	// }	
	
	
	function selectedDate(start, end){
		if(start && end) {
			_day_start = start.split("-");
			_day_end = end.split("-");
			_class = '.date-'+_day_start[0]+'-'+_day_start[1]+'-';
			for(i = _day_start[2]; i<= _day_end[2]; i++){
				$('#date-range-picker').find(( _class.toString() + i)).addClass('ui-state-highlight');
			}
		}
	}
	$('#date-range-picker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: 'dd-mm-yy',
		onSelect: function(dateText, inst) {
			var type = $("input[name='data[date_type]']:checked").val();
			var date = $(this).datepicker('getDate');
			var input_date = dateString(date, 'dd-mm-yy');
			$('#date-range-picker').val(input_date);
			$('#date-range-picker').trigger('change');
			var curStart = $('#nct-start-date').datepicker('getDate'),
				curEnd = $('#nct-end-date').datepicker('getDate');
			if( type == 'week' ){
				startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
				endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
			} else if(type == 'month'){
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
			}else {
				startDate = curStart;
				endDate = curEnd ;
			}
			var start = dateString(startDate);
			var end = dateString(endDate);
			selectedDate(start, end);
		},
		onChangeMonthYear: function(year, month, inst) {
			selectCurrentRange();
		}
	});
	function selectCurrentRange(){
        $('#date-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
	function dateString(date, format){
        if( !format )format = 'yy-mm-dd';
        return $.datepicker.formatDate(format, date);
    }
	function selected_pc_options(){
		var listPC = $('#listPC');
		if(listPC.length > 0){
			
		}
	}
	function initWidthTask(el){
		$.each($(el), function(){
			value_width = $(this).data('width');
			value_left = $(this).data('left');
			_width_cell = $('#fore1').outerWidth();
			_width_task  = _width_cell * value_width;
			_left_task = _width_cell * value_left + 440;
			$(this).css({"width": _width_task , "left": _left_task});
		});
	}
	
	initWidthTask('.absence-item');
	initWidthTask('.task-item');
	
	$('#absence').on('click', '.wd-combobox-filter span', function(e){ 
		_this = $(this);
		reAssignTask(_this);
	});
	function reAssignTask(_this){
		if(_this.hasClass('assigned')) return;
		is_nct = _this.closest('.assign-to').find('.open-multilslect').data('is_nct');
		is_pc = _this.data('is_pc');
		task_id = _this.closest('.assign-to').find('.open-multilslect').data('task_id');
		pc_id = _this.closest('.assign-to').find('.open-multilslect').data('pc_id');
		project_id = _this.closest('.task-item').find('.wd-edit-task').data('project_id');
		workload = _this.closest('.task-item').find('.task-workload').text();
		employee_id = _this.data('id');
		assigned = _this.siblings('span.isOwner').data('id');
		assigned_is_pc = _this.siblings('span.isOwner').data('is_pc');

		if(assigned){
			_this.addClass('loading');
			$.ajax({
				url: 'reAssignTask/',
				cache : true,
				type: 'post',
				dataType: 'json',
				data: {
					employee_id: employee_id,
					assigned: assigned, 
					is_nct: is_nct, 
					task_id: task_id, 
					is_pc: is_pc,
				},
				success: function(data){
					if(data == true){
						$.ajax({
							url: 'refreshStaffing/'+ project_id,
							type: 'POST',
						});
						// re_assign = 1, 2: load ajax refresh data workload
						// re_assign = 2: toggle tasks
						if(assigned){
						    assigned = (assigned_is_pc == 1) ? 'tNotAffec_'+assigned : assigned
							wd_employee_data = $('.row-employee.tdLeftRow-'+assigned).find('.wd-employee-data');
							getEmployeeWorkload(wd_employee_data, assigned, pc_id, re_assign = 2);
						}
						if(employee_id && ($('.row-employee.tdLeftRow-'+employee_id).hasClass('row-loaded') || workload != '0')){
							// IF has loaded tasks, refresh data loaded
							employee_id = (is_pc == 1) ? 'tNotAffec_'+employee_id : employee_id
							wd_employee_data = $('.row-employee.tdLeftRow-'+employee_id).find('.wd-employee-data');
							getEmployeeWorkload(wd_employee_data, employee_id, pc_id, re_assign = 1);
						}
					}
				},
				complete: function(){
					// _this.removeClass('loading');
				},
			});
		}
	};
	$('#listPC').on('click', '.checkbox', function(e){ 
		_this = $(this);
		_select_pc = _this.closest('#listPC');
		if ( _this.is( ":checked" ) ){			
			_this.prop('checked', true);
			var _selected_text = $('<span>'+ _this.siblings().text() + '</span>').html().replace(/&nbsp;/gi,"");
			_select_pc.find('.listpc-selected').append('<span data-id="'+ _this.val()+'">'+ _selected_text.replaceAll('|-', '') +', </span>');
		}else{
			_listpc_selected = _select_pc.find('.listpc-selected');
			item = _listpc_selected.find('span[data-id="'+ _this.val() +'"]');
			if(item.length > 0){
				item.remove();
			}
		}
		is_checked = _select_pc.find('input.checkbox:checked');
		if(is_checked.length > 10){
			_select_pc.siblings('.wd-listPC-alert').show();
		}else{
			_select_pc.siblings('.wd-listPC-alert').hide();
		}
	});
	
	function updateTaskStatus(){
		var _this = $(this);
		task_id = _this.data('taskid');
		status_id = _this.data('value');
		if( _this.hasClass('active') || _this.hasClass('loading') || _this.closest('.status_dots').hasClass('loading') ) return;
		_this.addClass('loading');
		$.ajax({
			url: '/kanban/update_task_status',
			cache : true,
			type: 'post',
			dataType: 'json',
			data: {
				id: task_id,
				status: status_id, 
			},
			success: function(data){
				if(data){
					if(data.result){
						// success
						var _parent = _this.closest('.item-status');
						_parent.find('.status_item').removeClass('active');
						_parent.find('.status_item_'+status_id).addClass('active');
						// var _status_text = '.status_item_'+status_id;						
					}else{
						$('#message-place').html(data.message);
						setTimeout(function(){
							$('#message-place .message').fadeOut('slow');
						} , 5000);
					}
				}
			},
			complete: function(){
				_this.removeClass('loading');
			},
		});
		
	}
	//data 
	var multiSelectFilter = function(){
			return;
			var $idProSelected = [];
			// fieldFilter
			var fieldFilter = $('.multiselect-pm');
			 fieldFilter.find(':checkbox').on('change', function(){
				 var _this = $(this);
				 var _parent = _this.closest('.multiselect-pm');
				 var data = _this.closest('.wd-data');
				 var e_id = _this.val();
				 if( _this.is(":checked")){
					var icon = data.find('.wd-dt-'+e_id);
					// $idProSelected.push(_datas);
					_op_text = data.find('.circle-name').html();
					$(_parent).find('.wd-combobox-filter').append($(icon[0].outerHTML));
					$(_parent).find('.wd-combobox-filter').find('p').hide();
				} else {
					var icon = _parent.find('.wd-combobox-filter .wd-dt-'+e_id);;
					icon.remove();
					if( !_parent.find('.wd-combobox-filter .circle-name').length){
						$(_parent).find('.wd-combobox-filter').find('p').show();
					}
				}
			 });
			 fieldFilter.on('click', '.wd-combobox-filter .circle-name', function(){
				 var _this = $(this);
				 var _id = _this.data('id');
				 var _checkbox = _this.closest('.multiselect-pm').find(':checkbox[value="' + _id + '"]');
				 _checkbox.prop('checked', false).trigger('change');
				 
			 });
			 
		};
		// multiSelectFilter();
		$('body').on('click', function(e){
			return;
			if(!( $(e.target).hasClass('multiselect-filter') || $('.multiselect-filter').find(e.target).length || $(e.target).hasClass('assign-to') || $('.assign-to').find(e.target).length || $('.multiselect-filter').find(e.target).context == 'img')){
				$('.multiselect-filter').removeClass('active');
				$('.multiselect-filter').hide();
			}
		});
		function openMultiselect(){
			var _this = $(this);
			var task_id = _this.data('task_id');
			var pc_id = _this.data('pc_id');
			if(!(_this.next('.multiselect-filter').hasClass('active'))){
				_this.addClass('loading');
				$.ajax({
					url: 'getEmployeeAssignToTasks/',
					cache : true,
					type: 'post',
					dataType: 'json',
					data: {
						task_id: task_id,
						pc_id: pc_id,
						employees: _resources[pc_id],
					},
					success: function(data){
						if(data){
							$.each(data, function(id, isPC){
								_this.next('.multiselect-filter').find('.wd-dt-'+id).addClass('assigned');
							});
						}
						if(!(_this.next('.multiselect-filter').hasClass('active'))){
							$('.multiselect-filter').removeClass('active');
							$('.multiselect-filter').hide();
							_this.next('.multiselect-filter').addClass('active');
							_this.next('.multiselect-filter').show();
							// set position show popup.
							tbHeight = $('#absence').height();
							yTable = $('#absence').offset().top;
							yMe = _this.offset().top;
							if((( tbHeight + yTable ) - yMe) < 90){
								_this.next('.multiselect-filter').css('bottom', '100%');
							}
						}else{
							_this.next('.multiselect-filter').removeClass('active');
							_this.next('.multiselect-filter').hide();
						}
					},
					complete: function(){
						_this.removeClass('loading');
					},
				});
			}else{
				_this.next('.multiselect-filter').removeClass('active');
				_this.next('.multiselect-filter').hide();
			}
			
		}		

		function editNCTTask() {
			elm = $(this);
			var id = $(this).data('task_id');
			project_id = $(this).data('project_id');
			$('#popupnct-id').val(id);
			var popup = '#template_add_nct_task';
			$(popup).find('.loading-mark:first').addClass('loading');
			show_full_popup(popup, {width: 'inherit'});
			// cho nay can get list Phase do vao bien listPhases neu la normal task,  popup_listPhases neu la NCT task-data
			$.ajax({
				url: '/projects/getTeamEmployees/'+ project_id + '/true/'+ id,
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if( data.success == true){
						var _cont = $('#multiselect-popupnct-pm .option-content');
						var _html = '';
						$.each(data.data, function(ind, emp){
							emp = emp.Employee;
							_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + '">';
							_html += '<p class="projectManager wd-data">';
							_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
							_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
							_html += '</p> </div>';
						});
						_cont.html(_html);
						init_multiselect('#multiselect-popupnct-pm');
						if( data.listPhases){
							popup_listPhases = data.listPhases;
							var _html = '';
							$.each( data.listPhases, function( id, _phase){
								_html += '<option value="' + id + '">' + _phase.name + '</option>';
							});
							$('#popupnct-phase').html(_html);
						}
						$.ajax({
							url : "/project_tasks/getNcTask/",
							type: "POST",
							cache: false,
							data: {data: {id: id}},
							dataType: 'json',
							success: function (data) {
								global_task_data = data;
								var type = 1;
								if( 'data' in global_task_data){
									$.each(global_task_data.data, function (key, val){
										if( typeof val[0]['type'] !== 'undefined') type = val[0]['type'];
										return false;
									});
								}
								$('#popupnct-range-type').val(type);					
								$('.popup-back').empty().html('<?php __('Edit NCT task');?>');
								$('#popupnct-project-id').val(project_id).trigger('change');
								$('#popupnct-name').val(data['task']['task_title']).trigger('change');
								$('#popupnct-phase').val(data['task']['project_planed_phase_id']).trigger('change');
								$('#popupnct-start-date').val(data['task']['task_start_date']).trigger('change');
								$('#popupnct-end-date').val(data['task']['task_end_date']).trigger('change');
								var assigns = [];
								if(data['columns']){
									$.each(data['columns'], function(key, column){
										id = (column.id).split('-');
										assigns.push({reference_id: id[0], is_profit_center: id[1]});
									});
								}
								render_list_date(data);
								set_assigned($('#template_add_nct_task').find('.popupnct_nct_list_assigned'), assigns);
								resetOptionAssigned(data.employees_actif, '#multiselect-popupnct-pm');
								$('#popupnct-status').val(data['task']['task_status_id']).trigger('change');
								$('#popupnct-profile').val(data['task']['profile_id']).trigger('change');
								$('#popupnct-priority').val(data['task']['task_priority_id']).trigger('change');
								$('#popupnct_per-workload').val(data['task']['estimated']).trigger('change');
								$('#template_add_nct_task #btnNCTSave').empty().html('<?php __('Save');?>');
								// set_width_popupnct();
							},
							complete: function(){
								$(popup).find('.loading-mark:first').removeClass('loading');
							}
						});
					}
				}			
			});
		}
		function render_list_date(data){
			var request = data.request;
			var data = data.data;
			var consumed = 0, in_used = 0;
			$.each(data, function(row, val){
				var date = row.substr(2);
				var date_name = toRowName(row);
				var html = '<tr><td id="date-' + date + '" class="popupnct-date" style="text-align: left">' + toRowName(row) + '<span class="cancel" onclick="removeRow(this)" href="javascript:;"></span></td>';
				
				var c = parseFloat(request[row][0]), iu = parseFloat(request[row][1]);
				if( isNaN(c) )c = 0;
				if( isNaN(iu) )iu = 0;
				//last col
				html += '<td style="background: #f0f0f0" class="ciu-cell">' + c.toFixed(2) + ' (' + iu.toFixed(2) + ')</td>';
				html += '<td class="row-action">';
				if(!( c > 0 || iu > 0 )){
					html += '<a class="cancel" onclick="popupnct_removeRow(this)" href="javascript:;"></a>';
				}
				html += '</td></tr>';
				$('#popupnct-assign-table tbody').append(html);
				consumed += c;
				in_used += iu;
			});
			$('#popupnct_total-consumed').empty().html(consumed.toFixed(2) +' ('+ in_used.toFixed(2) +')');
		}
		function set_assigned($elm, datas) {
			$elm.find('.wd-combobox >.circle-name').remove();
			$elm.find('.wd-combobox >p').show();
			$elm.find('.wd-data input[type="checkbox"]').prop('checked', false).trigger('change');
			$elm.find('.wd-input-search').val('').trigger('change');
			$.each(datas, function (ind, data) {
				if (data.is_profit_center == 1) {
					$elm.find('input[value="' + data.reference_id + '-1"]').closest('.wd-data').click();
				} else {
					$elm.find('input[value="' + data.reference_id + '-0"]').closest('.wd-data').click();
					$elm.find('input[value="' + data.reference_id + '"]').closest('.wd-data').click();
				}
			});
		}
		function editNormalTask() {
			var _this = $(this);
			var taskid = _this.data('task_id');
			project_id = $(this).data('project_id');
			var popup = $('#template_add_task');
			popup.find('.loading-mark:first').addClass('loading');
			var popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task', {width: popup_width});
			$.ajax({
				url: '/projects/getTeamEmployees/'+ project_id + '/true/'+taskid,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if( data.success == true){
						var _cont = $('#popupnct_nct_list_assigned .option-content');
						var _html = '';
						$.each(data.data, function(ind, emp){
							emp = emp.Employee;
							_html += '<div class="projectManager wd-data-manager wd-group-' + emp.id + '">';
							_html += '<p class="projectManager wd-data">';
							_html += '<input type="checkbox" name="data[task_assign_to_id][]" value="' + emp.id + '-' + emp.is_profit_center + '" id="newtaskTaskAssignToId-'+ emp.id +'">';
							_html += '<span class="option-name" style="padding-left: 5px;">' + emp.name + '</span>';
							_html += '</p> </div>';
						});
						_cont.html(_html);
						init_multiselect('#popupnct_nct_list_assigned');
						if( data.listPhases){
							popup_listPhases = data.listPhases;
							var _html = '';
							$.each( data.listPhases, function( id, _phase){
								_html += '<option value="' + id + '">' + _phase.name + '</option>';
							});
							$('#toPhase').html(_html);
						}
						$.ajax({
							url: '/project_tasks/get_task_info/',
							type: 'POST',
							data: {
								data: {
									id: taskid,
								}
							},
							dataType: 'json',
							success: function (data) {
								if (data) {
									if (data.result == 'success') {
										$('#ProjectTaskProjectId').val(project_id).trigger('change');
										$('#ProjectTaskId').val(taskid).trigger('change');
										show_full_popup('#template_add_task', {width: 'inherit'});
										c_task_data = data.data;
										$('#editTaskID').val(data.data.id).trigger('change');
										$('#newTaskName').val(data.data.task_title).trigger('change');
										$('#toPhase').val(data.data.project_planed_phase_id).trigger('change');
										$('#newTaskStatus').val(data.data.task_status_id).trigger('change');
										var start_date = data.data.task_start_date,
												end_date = data.data.task_end_date;
										var st_date = start_date.split('-');
										var en_date = end_date.split('-');
										st_date = st_date[2] + '-' + st_date[1] + '-' + st_date[0];
										en_date = en_date[2] + '-' + en_date[1] + '-' + en_date[0];
										$('#newTaskStartDay').val(st_date).trigger('change');
										$('#newTaskEndDay').val(en_date).trigger('change');
										set_assigned(popup.find('.popupnct_nct_list_assigned'), data.data.assigned);
										resetOptionAssigned(data.employees_actif, '#popupnct_nct_list_assigned');
										$('.popupnct_nct_list_assigned').removeClass('loading');
									} else {
										show_form_alert('#ProjectTask', "data.message");
									}
								} else {
									c_task_data = {};
									show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
								}
								popup.find('.loading-mark:first').removeClass('loading');
							},
							error: function () {
								c_task_data = {};
								show_form_alert('#ProjectTask', "<?php __('Get task failed');?>");
								popup.find('.loading-mark:first').removeClass('loading');
							}
						});
					}
				}
			});

		}
		function resetOptionAssigned(employees_actif, id_element){
			if(employees_actif.length > 0){
				$.each(employees_actif, function(i, employee){
					if(employee['Employee']['actif'] == 0){
						itemEle = '.wd-group-'+employee['Employee']['id'];
						$(id_element).find(itemEle).addClass('wd-actif-0');
					}
				});
			}
		}
	$(document).ready(function(){
		set_table_height();
		multiSelectFilter();
		if(isDiary){
			$('.trEmployee').show();
			$('tr.row-employee').removeClass('active');
			_row_employee = $('.row-employee');
			_row_employee.addClass('active');
			_row_employee.next('tr.row-hidden').addClass('active');
			_row_employee.next('tr.row-hidden').find('.task-data').show();
			
			// Submit form
			_input_change = $('.forcasts-actions input');
			_input_change.on('change', function(){
				 $('.forcasts-actions form').submit();
			});
		} 
		
		$('#displayProArchived').on('change', function(){
			var _checked = $(this).is(':checked');
			$(this).addClass('loading');
			switchDisplay('display_project_archived', _checked);
		});
		$('#displayProOppor').on('change', function(){
			var _checked = $(this).is(':checked');
			$(this).addClass('loading');
			switchDisplay('display_project_opportunity', _checked);
		});
		$('#displayWlzero').on('change', function(){
			var _checked = $(this).is(':checked');
			$(this).addClass('loading');
			switchDisplay('display_task_wlzero', _checked);
		});
		function switchDisplay(_path, _value){
			$.ajax({
				url: '<?php echo $this->Html->url('/activity_forecasts_preview/switchDisplayTask') ?>',
				type: 'POST',
				data: { data: {
					value: _value,
					key: _path,
				}},
				success: function (res) {
					location.reload();
				}
			});
		}
		
		
		setTimeout(function(){
			// if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
				// $('#add_nct_task-popup').addClass('loading');
				$.ajax({
					url : "/project_tasks_preview/add_task_popup/",
					type: "GET",
					cache: false,
					success: function (html) {
						$('#addProjectTemplate').empty().append($(html));
						$('.tabPopup .liPopup a.active').empty().html('<?php __('Edit task');?>');
						$('#template_add_task .btn-ok span').empty().html('<?php __('Save');?>');
						$('.right-link').hide();
						$('#add-form').trigger('reset');
						$(window).trigger('resize');
						$('#addProjectTemplate').addClass('loaded');
						$('#addProjectTemplate').removeClass('loading');
						$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
						if( $('#addProjectTemplate').hasClass('open') ){
							var popup_width = show_workload ? 1080 : 580;
							show_full_popup( '#template_add_task', {width: popup_width});
							$('#template_add_task').find('input, select').trigger('change');
						}
					},
					complete: function(){
						// $('#addProjectTemplate').removeClass('loading');
					},
					error: function(){
						
					}
				});
			// }
		}, 2000);
		dragAndDrop();
	});
	
	$(window).resize(function(){
		initWidthTask('.absence-item');
		initWidthTask('.task-item');
	});
	var task_dragging;
	function reSizeTask(){
		var old_width = 500;
		var old_left = 500;
		$('body').find('.task-item').hover(
		   function () {
			  if(!$(this).hasClass('absence-item') && $(this).outerWidth() < 500){
					old_width = $(this).outerWidth();
					old_left = $(this).position().left;
					_width_container = $(this).closest('.task-data').width();
					$(this).css({"width": "fit-content","min-width": "635px"});
					console.log(old_left, old_left + $(this).outerWidth(), _width_container);
					if((old_left + $(this).outerWidth()) > _width_container){
						new_left = _width_container - $(this).outerWidth();
						$(this).css({"left": new_left});
					}
					$(this).addClass('hover');
					
				}
		   }, 
		   function () {
				if(!$(this).hasClass('absence-item') && old_width < 500 && $(this).hasClass('hover')){
					$(this).css({"width": old_width, "left": old_left,"min-width": "auto"});
					$(this).removeClass('hover');
				}
		   }
		);
	}
	reSizeTask();
	function dragAndDrop(){
		var left_item_dragging = 440;
		var task_id_dragging = 0;
		var employee_id_dragging = 0;
		var taskworkload = 0;
		$( "#absence").find(".drag_item").draggable({
			  scope: 'demoBox',
			  revertDuration: 100,
			  start: function( event, ui ) {
				 left_item_dragging = $(this).css('left');
				 taskworkload = parseFloat($(this).find('.task-workload').text());
				 task_id_dragging = $(this).data('task-id');
				 employee_id_dragging = $(this).closest('.row-hidden').prev('.row-employee').data('employee_id');
				 $( ".drag_item" ).draggable( "option", "revert", true );
				 task_dragging = $(this);
			  },
			  
		});

		$( "#absence").find(".row-employee" ).droppable({
			 scope: 'demoBox',
			  over: function( event, ui ) {
				 $(this).find( ".tooltiptext" ).addClass("active");
				 e_workload = $(this).find('.workload').text();
				 e_capacity = $(this).find('.capacity').text();
				 if((taskworkload + parseFloat(e_workload)) > parseFloat(e_capacity)){
					 $(this).find( ".tooltiptext" ).css('border-color', 'red');
				 }
			  },
			  out: function( event, ui ) {
				  $(this).find( ".tooltiptext" ).removeClass("active");
			  },
			  drop: function( event, ui ) {
				  $(ui.draggable).detach().remove();
				  var employee_id = $(this).data('employee_id');
				  var isPC = $(this).data('is_pc');
				  if(isPC == 1){
					 pc_id = employee_id.split('_');
					 employee_id = pc_id[1];
				  }
				  var _this = task_dragging.find('.wd-dt-'+employee_id);
				  reAssignTask(_this);
			  }
		});
	
	}
	

</script>