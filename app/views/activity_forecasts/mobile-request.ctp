<?php echo $this->Html->script('bootstrap-editable.min') ?>
<?php echo $this->Html->css('mobile/bootstrap-editable') ?>
<?php echo $this->Html->script('jquery.dlmenu') ?>
<?php echo $this->Html->css('mobile/dlmenu') ?>

<style>
	.currentWeek {
		margin-left: 10px;
		margin-right: 10px;
	}
	@media (min-width: 768px) {
		.form-inline .form-group {
			margin-bottom: 5px;
		}
	}
	/*color*/
	<?php foreach($constraint as $class => $con): ?>
	.<?php echo $class == 'holiday' ? 'table-request .holiday' : $class ?> {
		background-color: <?php echo $con['color'] ?>;
	}
	<?php endforeach; ?>

	#copy_forecast {
		background: url(/img/copy.png) center bottom no-repeat;
		display: inline-block;
		width: 32px;
		height: 30px;
		text-indent: -9999px;
		margin-left: 5px;
		line-height: 2;
	}
	#submit-request-all-top {
		background: url(/img/sendMail.png) center bottom no-repeat;
		display: inline-block;
		width: 32px;
		height: 30px;
		text-indent: -9999px;
		margin-left: 5px;
		line-height: 2;
	}
	#refresh_menu {
		background: url(/img/refreshMenu.png) center bottom no-repeat;
		display: inline-block;
		width: 28px;
		height: 30px;
		text-indent: -9999px;
		margin-left: 5px;
		line-height: 2;
	}
	.editable-clear-x {
		display: none !important;
	}
	.editableform .form-control {
		width: 75px;
		padding-right: 8px !important;
	}
	.action {
		position:absolute;
		right:-27px;
	}
	.filter-task {
		margin-right:27px;
	}
	.filter-activity {
		margin-right:27px;
	}
	
	.dl-menu-activity, .dl-menu-activity .dl-submenu{
		background-color: #375f92!important;
	}
	.favourite-list div {
		padding:0px 5px;
	}
	.favourite-list div a {
		color: #000;
	}
	<?php if( $allowRequestRemain['ActivitySetting']['allow_remain_zero_consume'] == 0 ): ?>
	.overload a {
		color: #999;
	}
	<?php endif ?>;
</style>
<?php
$am = __('AM', true);
$pm = __('PM', true);
$typeSelect = 'week';

$capacity = array();
foreach ($listWorkingDays as $day => $time) {
	$capacity[$day] = floatval($workdays[strtolower(date('l', $day))]);
	if (isset($holidays[$time])) {
		foreach ($holidays[$time] as $k => $val) {
			if (!isset($mapDatas['hl']['off'][$day])) {
				$mapDatas['hl']['off'][$day] = 0;
			}
			if($k == 'am' || $k == 'pm'){
				$mapDatas['hl']['off'][$day] += 0.5;
			}
		}
	}
}
$capacity['capacity'] = array_sum($capacity);
$hideOnSent = abs($requestConfirm) != 1  ? ' hide' : '';
$statusClass = '';
switch ($requestConfirm) {
	case '1':
		$statusClass = 'text-danger';
		break;
	case '2':
		$statusClass = 'text-success';
		break;
	case '0':
		$statusClass = 'text-primary';
		break;
	default:
		# code...
		break;
}
?>
<section class="content">
	<div class="row">
		<!-- week naviation -->
		<div class="col-md-12" style="z-index: 2;">
			<div class="box box-primary">
				<div class="box-body">
					<h5 class="pull-left <?php echo $statusClass ?>" id="status">
						<?php 
							$dateValidate = !empty($requestConfirmDate) ? date('d/m/Y', $requestConfirmDate) : '';
							$nameValidate = !empty($requestConfirmName) ? $requestConfirmName : '';
							if ($requestConfirm == 0 && $typeSelect != 'year'){
								//__('Waiting validation');
								__('Sent');
							} elseif ($requestConfirm == 1 && $typeSelect != 'year'){
								__('Rejected'); echo ' ('.$nameValidate. ', ' .$dateValidate . ')';
							} elseif ($requestConfirm == 2 && $typeSelect != 'year'){
								__('Validated'); echo ' (' .$nameValidate. ', ' .$dateValidate . ')';
							} elseif($requestConfirm == -1 && $typeSelect != 'year'){
								__('In progress');
							}
						?>

					</h5>
					<button data-toggle="modal" data-target="#x-menu" class="btn btn-primary pull-right<?php echo $hideOnSent ?>"><i class="glyphicon glyphicon-list"></i></button>
				</div>
			</div>
		</div>
		<!-- /navigation -->
		<!-- diary table -->
		<div class="col-md-12" style="z-index: 1;">
			<div class="box box-primary">
				<div class="box-body no-padding table-responsive">
					<table class="table table-request table-bordered table-diary">
						<thead>
							<tr>
								<th>
									<?php echo $employeeName['first_name'] . ' ' . $employeeName['last_name'] ?>
									<button class="btn btn-request pull-right<?php echo $hideOnSent ?>" id="open-filter-2" style="display:none"><i class="glyphicon glyphicon-filter"></i></button>
								</th>
								<th width="10%" class="text-center"><?php __('Capacity') ?></th>
								<th width="70%" class="demo-1">
									<!-- /dl-menuwrapper -->
									
								</th>
							</tr>
						</thead>
						<tbody>
<?php foreach ($listWorkingDays as $date) { ?>
							<tr class="date-row" data-date="<?php echo $date ?>" id="row-<?php echo $date ?>">
								<th><?php __(date('l', $date)); echo date(' d ', $date); __(date('M', $date)); ?></th>
								<td class="capacity text-center" id="capacity-<?php echo $date ?>">0</td>
								<td class="tasks" id="tasks-<?php echo $date ?>">
								</td>
							</tr>
<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /diary table -->
		<!-- guide lines -->
		<div class="col-md-12">
			<div class="table-diary">
				<div class="tasks">
					<span class="absence validated"><?php __('Absence') ?></span>
					<span class="absence waiting"><?php __('Absence') ?></span>
					<span class="workload"><?php __('Forecast') ?></span>
					<span class="task"><?php __('Task') ?></span>
					<span class="task new-task hint" data-content="<?php __('When reloading the page, those tasks will disappear') ?>"><?php __('Not saved task') ?></span>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
if (empty($isManage) || ($isManage && ($requestConfirm == -1 || $requestConfirm == 1))) :
	if (($requestConfirm == -1 || $requestConfirm == 1) && $typeSelect != 'year'):
?>
<div style="display: none;">
	<?php
	echo $this->Form->create('Request', array(
		'id' => 'form-send-request',
		'type' => 'get',
		'url' => array('controller' => 'activity_forecasts', 'action' => 'confirm_request', $typeSelect, $profit)));
	if ($isManage) {
		echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
		echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit']));
	}
	echo $this->Form->hidden('return', array('value' => $_SERVER["REQUEST_URI"]));
	$valWeek = date('W', $_end);
	$valYear = date('Y', $_end);
	if($typeSelect === 'month'){
		$valWeek = date('W', $_start);
		$valYear = date('Y', $_start);
		echo $this->Form->hidden('month', array('value' => @$_GET['month']));
	}
	echo $this->Form->input('week', array('type' => 'hidden', 'value' => $valWeek));
	echo $this->Form->input('year', array('type' => 'hidden', 'value' => $valYear));
	echo $this->Form->input('rd', array('value' => mt_rand(9999, 99999)));
	echo $this->Form->hidden('get_path', array(
		'name' => 'get_path',
		'value' => $getDataByPath ? 1 : 0
	));
	echo $this->Form->end();
	?>
</div>

<?php
	endif;
else :
	$types = $urls = '';
	$urls = array('profit' => $profit, 'week' => date('W', $_end), 'year' => date('Y', $_end), 'get_path' => $getDataByPath ? 1 : 0);
	if($typeSelect === 'week'){
		$types = 'week';
	} elseif($typeSelect === 'month'){
		$types = 'month';
		$urls = array('profit' => $profit, 'week' => date('W', $_start), 'year' => date('Y', $_start), 'get_path' => $getDataByPath ? 1 : 0);
	}
	$urls['return'] = $_SERVER["REQUEST_URI"];
	echo $this->Form->create('Request', array(
		'escape' => false, 'id' => 'form-response', 'type' => 'post',
		'url' => array('controller' => 'activity_forecasts', 'action' => 'response', $types, '?' => $urls)));
	echo $this->Form->hidden('id.' . $employeeName['id'], array('value' => 1));
	echo $this->Form->hidden('validated', array('name' => 'data[validated]', 'value' => 0, 'id' => 'ac-validated'));
	echo $this->Form->end();
	$employee_info = $this->Session->read('Auth.employee_info');
	$is_sas = $employee_info['Employee']['is_sas'];
	if ($is_sas != 1) {
		$role = $employee_info['Role']['name'];
	}
endif;
?>

<!-- modal -->
<div class="modal fade" id="modal-copy-forecast" tabindex="-1" role="dialog" aria-labelledby="modal-label">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-label"><?php __('Copy forecast') ?></h4>
			</div>
			<div class="modal-body table-responsive">

<?php 
echo $this->Form->create('Copy', array(
		'type' => 'file', 'id' => 'form_dialog_copy_forecasts', 
		'url' => array('controller' => 'activity_forecasts', 'action' => 'copy_forecasts', $typeSelect, $_start, $_end, '?' => array(
			'get_path' => $getDataByPath
		))
	)); ?>
	<table class="table table-bordered table-request">
		<thead>
			<tr>
				<th rowspan="1">&nbsp;</th>
				<th rowspan="2"><?php __('Project/Activity'); ?></th>
				<th rowspan="2"><?php __('Part'); ?></th>
				<th rowspan="2"><?php __('Phase'); ?></th>
				<th rowspan="2"><?php __('Task'); ?></th>
				<?php 
					if(!empty($listWorkingDays)):
						foreach($listWorkingDays as $key => $val):
				?>
				<th colspan="2"><?php echo __(date('l', $val)) . __(date(' d ', $val)) . __(date('M', $val)); ?></th>
				<?php        	   
						endforeach;
					endif;
				?>
			</tr>
		</thead>
		<tbody id="copy-forecast-table">
		<?php
			if(!empty($dataForecasts)):
				echo $this->Form->hidden('get_path', array('value' => isset($getDataByPath) ? $getDataByPath : ''));
				echo $this->Form->hidden('profit', array('value' => !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : $profit));
				echo $this->Form->hidden('id', array('value' => !empty($this->params['url']['id']) ? $this->params['url']['id'] : $employee_info['Employee']['id']));
				echo $this->Form->input('week', array('type' => 'hidden', 'value' => date('W', $_end)));
				echo $this->Form->input('month', array('type' => 'hidden', 'value' => date('m', $_start)));
				echo $this->Form->input('year', array('type' => 'hidden', 'value' => date('Y', $_end)));
				foreach($dataForecasts as $key => $dataForecast):
					if($key == 'project'):
						foreach($dataForecast as $projectId => $values):
							foreach($values as $partId => $value):
								foreach($value as $phaseId => $val):
									foreach($val as $taskId => $vals):
										$exists = false;
										if(in_array($taskId, $lisTaskRequests)){
											$exists = true;
										}
		?>
					<tr class="<?php echo ($exists == true) ? 'tr_copy_exists' : '';?>">
						<?php if($exists == true){?>
						<td class="text-center"><?php echo $this->Form->input('id.' . $taskId, array('type' => 'checkbox', 'label' => false, 'class' => 'copy-checkbox-format', 'div' => false, 'hiddenField' => false, 'disabled' => 'disabled'));?></td>
						<?php } else {?>
						<td class="text-center"><?php echo $this->Form->input('id.' . $taskId, array('type' => 'checkbox', 'label' => false, 'class' => 'copy-checkbox-format', 'div' => false, 'hiddenField' => false));?></td>
						<?php }?>
						<td><?php echo !empty($projectFromTasks[$projectId]) ? $projectFromTasks[$projectId] : '';?></td>
						<td><?php echo !empty($projectPartFromTasks[$partId]) ? $projectPartFromTasks[$partId] : '';;?></td>
						<td><?php echo !empty($projectPhaseFromTasks[$phaseId]) ? $projectPhaseFromTasks[$phaseId] : '' ;?></td>
						<td><?php echo !empty($listProjectTaskFromTasks[$listIdActivityTaskFromTasks[$taskId]]) ? $listProjectTaskFromTasks[$listIdActivityTaskFromTasks[$taskId]] : '';?></td>
						<?php
							foreach($listWorkingDays as $dates){
								$val = !empty($vals[$dates]) ? $vals[$dates] : 0;
							//foreach($vals as $times => $val){
						?>
							<td colspan="2"><?php echo $val;?></td>
						<?php
							}
						?>
					</tr>
		<?php
									endforeach;
								endforeach;
							endforeach;
						endforeach;
					endif;
					if($key == 'activity'):
						foreach($dataForecast as $activityId => $values):
							foreach($values as $taskId => $vals):
								$exists = false;
								if(in_array($taskId, $lisTaskRequests)){
									$exists = true;
								}
		?>
					<tr class="<?php echo ($exists == true) ? 'tr_copy_exists' : '';?>">
						<?php if($exists == true){?>
						<td><?php echo $this->Form->input('id.' . $taskId, array('type' => 'checkbox', 'label' => false, 'div' => false, 'class' => 'copy-checkbox-format', 'hiddenField' => false, 'disabled' => 'disabled'));?></td>
						<?php } else {?>
						<td><?php echo $this->Form->input('id.' . $taskId, array('type' => 'checkbox', 'label' => false, 'div' => false, 'class' => 'copy-checkbox-format', 'hiddenField' => false));?></td>
						<?php }?>
						<td><?php echo !empty($activityFromTasks[$activityId]) ? $activityFromTasks[$activityId] : '';;?></td>
						<td><?php echo '';?></td>
						<td><?php echo '';?></td>
						<td><?php echo !empty($listActivityTaskFromTasks[$taskId]) ? $listActivityTaskFromTasks[$taskId] : '';?></td>
						<?php
							foreach($listWorkingDays as $dates){
								$val = !empty($vals[$dates]) ? $vals[$dates] : 0;
							//foreach($vals as $times => $val){
						?>
							<td colspan="2"><?php echo $val;?></td>
						<?php
							}
						?>
					</tr>
		<?php
							endforeach;
						endforeach;       
					endif;
				endforeach;
			endif;
		?>
		</tbody>
	</table>
<?php
echo $this->Form->hidden('mobile', array('name' => 'data[mobile]', 'value' => 1));
echo $this->Form->end();
?>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('Cancel') ?></button>
				<button type="submit" class="btn btn-primary" form="form_dialog_copy_forecasts" id="btn-copy-forecast"><?php __('Save') ?></button>
			</div>
		</div>
	</div>
</div>

<!-- modal -->
<div class="modal fade" id="x-menu" tabindex="-1" role="dialog" aria-labelledby="modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<!-- <div class="modal-header">
				<h4 class="modal-title" id="modal-label"><?php __('Menu') ?></h4>
			</div> -->
			<div class="modal-body has-scroll">
				<div id="dl-menu" class="dl-menuwrapper">
					<ul id="dl-menu-root" class="dl-menu dl-menuopen"></ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" data-dismiss="modal"><?php __('Close') ?></button>
			</div>
		</div>
	</div>
</div>



<?php
$dataView = array();
$mapDatas = array('hl' => array(), 'ab' => array(), 'ac' => array(), 'chAb' => array());
$absences['off'] = array('print' => __('Holiday', true));
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
				if ($isValidated) {
					$mapDatas['chAb'][$default['absence_' . $type]][] = $requests[$id][$time]['response_' . $type];
					if (!isset($mapDatas['ab'][$default['absence_' . $type]][$day])) {
						$mapDatas['ab'][$default['absence_' . $type]][$day] = 0;
					}
					$mapDatas['ab'][$default['absence_' . $type]][$day] += 0.5;
				}
			}
		}
		$dataView[$id][$day] = $default;
	}
}
$workloadOfEmployees = array();
if(!empty($workloads)){
	foreach($dataView as $id1 => $_dataViews){
		foreach($_dataViews as $time1 => $_dataView){
			foreach($workloads as $id2 => $_workloads){
				foreach($_workloads as $time2 => $_workload){
					if($id1 == $id2 && $time1 == $time2){
						$dataView[$id1][$time1]['workload'] = $_workload;
					}
					$workloadOfEmployees[$time2] = $_workload;
				}
			}    
		}
	}
}
if($typeSelect === 'week'){
	$queryUpdate = '/week?week=' . date('W', $_end) . '&year=' . date('Y', $_end);
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

?>
<script>
	var maps = <?php echo json_encode($mapeds); ?>,
		capacity = <?php echo json_encode($capacity); ?>,
		absences = <?php echo json_encode($absences); ?>,
		workdays = <?php echo json_encode($workdays); ?>,
		listWorkingDays = <?php echo  json_encode($listWorkingDays);?>,
		daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
		activityNotAccessDeletes = <?php echo json_encode($activityNotAccessDeletes);?>,
		taskNotAccessDeletes = <?php echo json_encode($taskNotAccessDeletes);?>,
		checkFullDayActivities = <?php echo json_encode($checkFullDayActivities);?>,
		checkFullDayTasks = <?php echo json_encode($checkFullDayTasks);?>,
		checkFullDays = <?php echo json_encode($checkFullDays);?>,
		activities = maps['activity'], tasks = maps['task'];
	var activityFromTasks = <?php echo json_encode($activityFromTasks); ?>,
		projectFromTasks = <?php echo json_encode($projectFromTasks); ?>,
		consumedForecastForActivityRequests = <?php echo json_encode($consumedForecastForActivityRequests);?>,
		projectLinkedActivity = <?php echo json_encode($projectLinkedActivity);?>,
		familyFromTasks = <?php echo json_encode($familyFromTasks);?>,
		workloads = <?php echo json_encode($workloadOfEmployees);?>,
		activityGroupFromTasks = <?php echo json_encode($activityGroupFromTasks);?>,
		activityRequests = <?php echo json_encode($activityRequests);?>,
		requests = <?php echo json_encode($requests);?>,
		requestConfirm = <?php echo $requestConfirm ?>,
		employeeId = <?php echo $employeeName['id'] ?>,
		holidays = <?php echo json_encode($holidays) ?>,
		typeSelect = <?php echo json_encode($typeSelect) ?>,
	requests = requests[employeeId] ? requests[employeeId] : [];
	//prototype
	var Request = {
		_events: {},
		subscribe: function(name, callback){
			if( !this._events[name] ){
				this._events[name] = [];
				this._events[name].push(callback);
			}
		},
		options: {
			showAssignedTasks: true
		},
		show: true,
		setCapacity: function(totalCapacity, date, value){
			totalCapacity[date] = totalCapacity[date] ? totalCapacity[date] + value : value;
		},
		reset: function(){
			var me = this;
			$('td.tasks').html('');

			var Workload = {};
			$.each(consumedForecastForActivityRequests, function(date, val){
				$.each(val, function(ap, workload){
					var name = '', type = '';
					if( ap.indexOf('pr') != -1 ){
						var id = ap.replace('pr-', '');
						name = projectFromTasks[id];
						//fix lai id
						id = projectLinkedActivity[id];
					} else {
						var id = ap.replace('ac-', '');
						name = activityFromTasks[id];
						type = 'activity';
					}
					$('#tasks-' + date).append('<span class="workload has-popover" id="task-' + ap + '" data-id="' + id + '" data-submit-id="workload-' + ap + '">' + name + ' <i>' + workload + '</i></span>');
				});
			});

			//absence
			$.each(requests, function(date, request){
				var amId = request.absence_am,
					pmId = request.absence_pm,
					responseAM = request.response_am,
					responsePM = request.response_pm
				;
				var html = '';
				if( amId == pmId && responseAM == responsePM ){
					if( absences[amId] ){
						var name = absences[amId].print;
						html = '<span class="absence ' + responseAM + '">' + name + ' <i>1</i></span>';
						Workload[date] = 1;
					}
				} else {
					if( !Workload[date] )
						Workload[date] = 0;
					if( parseInt(amId) ){
						if( absences[amId] ){
							var amName = absences[amId].print;
							html = '<span class="absence ' + responseAM + '">' + amName + ' <i>0.5</i></span>';
							Workload[date] += 0.5;
						}
					}
					if( parseInt(pmId) ){
						if( absences[pmId] ){
							var pmName = absences[pmId].print;
							html += '<span class="absence ' + responsePM + '">' + pmName + ' <i>0.5</i></span>';
							Workload[date] += 0.5;
						}
					}
				}
				$('#tasks-' + date).append(html);
			});

			if( me.options.showAssignedTasks ){
				$('.workload').show();
				me.show = true;
			} else {
				$('.workload').hide();
				me.show = false;
			}

			// var classes = '';
			// switch(requestConfirm){
			// 	case 0:
			// 		classes = 'sent';
			// 		break;
			// 	case 1:
			// 		classes = 'rejected';
			// 		break;
			// 	case 2:
			// 		classes = 'validated';
			// 		break;
			// }

			//tasks
			//separate whole activity with task
			$.each(activityRequests, function(tId, requests){
				var activityId = tId, taskId, type = 'activity';
				if( tId.indexOf('-') != -1 ){
					taskId = tId.split('-')[1];
					type = 'task';
				}
				$.each(requests, function(date, data){
					var name = '';
					if( type == 'task' ){
						activityId = tasks[taskId].activity_id;
						name = tasks[taskId].name;
					} else {
						taskId = activityId;
						name = activities[taskId].short_name;
					}
					$('#tasks-' + date).append('<span class="task has-popover type-' + type + '" id="task-' + taskId + '" data-type="' + type + '" data-date="' + date + '" data-submit-id="' + tId + '" data-id="' + taskId + '" data-activity="' + activityId + '">' + name + ' <i>' + data.value + '</i></span>');
					data.value = parseFloat(data.value);
					Workload[date] = Workload[date] ? Workload[date] + data.value : data.value;
				});
			});

			//holiday
			$.each(holidays, function(date, val){
				$('#tasks-' + date).addClass('holiday').html('<span class="holiday"><?php __('Holiday') ?></span>');
				Workload[date] = 1;
			});

			$.each(listWorkingDays, function(date){
				var wl = Workload[date] ? Workload[date].toFixed(2) : '0.00';
				$('#capacity-' + date).html(wl + '/<b>' + capacity[date].toFixed(2) + '</b>').data({
					capacity: capacity[date].toFixed(2)
				});
			});
		},
		init: function(options){
			var me = this;
			if( options ){
				$.extend(me.options, options, true);
			}
			this.reset();

			//popover
			//x-editable
			me.bindEditable()
		},
		bindEditable: function(){
			var me = this;
			//popover
			$('.has-popover')
			.popover('destroy')
			.popover({
				animation: false,
				html: true,
				container: 'body',
				placement: 'top',
				trigger: 'click',
				title: function(){
					var t = $(this);
					if( t.hasClass('workload') ){
						return activities[t.data('id')].name;
					} else if( t.hasClass('task') ){
						return activities[t.data('activity')].name;
					}
					return '';
				},
				content: function(){
					var t = $(this), html = '';
					if( t.hasClass('workload') ){
						var id = t.data('id'),
							a = activities[id];
					} else if( t.hasClass('task') ){
						var id = t.data('activity'),
							a = activities[id];
					}
					html += '<ul class="list-unstyled">';
					html += '<li>' + a.long_name + '</li>';
					html += '<li>' + a.short_name + '</li>';
					html += '<li>' + (a.family_id ? familyFromTasks[a.family_id] : '') + '</li>';
					html += '<li>' + (a.subfamily_id ? familyFromTasks[a.subfamily_id] : '') + '</li>';
					html += '</ul>';
					//tasks

					if( t.hasClass('task') ){
						html += '<span class="pop-task">' + t.text() + '</span>';
					} else {
						var date = t.closest('tr').data('date'),
							wId = t.prop('id').replace('task-', '');
						var forecastedTasks = workloads[date][wId];
						$.each(forecastedTasks, function(taskId, value){
							var addTask = requestConfirm == -1 || requestConfirm == 1 ? '<button class="add-task-popup btn btn-primary btn-xs" onclick="Request.addTask.call(this)" data-id="' + taskId + '"><i class="glyphicon-plus glyphicon"></i> <?php __('Add') ?></button>' : '';
							html += '<span class="pop-task">' + value + '</span>' + addTask;
						});
					}

					return html;
				}
			}).on('show.bs.popover', function(){
				$('.has-popover').not(this).popover('hide');
			});
			//editable
			if( !'<?php echo $hideOnSent ?>' ){
				$('.task.has-popover > i')
				.editable('destroy')
				.editable({
					value: 0.0,    
					source: [
						{value: 0.00, text: '0.00'},
						{value: 0.10, text: '0.10'},
						{value: 0.20, text: '0.20'},
						{value: 0.25, text: '0.25'},
						{value: 0.30, text: '0.30'},
						{value: 0.40, text: '0.40'},
						{value: 0.50, text: '0.50'},
						{value: 0.60, text: '0.60'},
						{value: 0.70, text: '0.70'},
						{value: 0.75, text: '0.75'},
						{value: 0.80, text: '0.80'},
						{value: 0.90, text: '0.90'},
						{value: 1.00, text: '1.00'}
				    ],
					container: 'body',
					type: 'select',
					title: function(){
						var t = $(this);
						if( t.hasClass('workload') ){
							return activities[t.data('id')].name;
						} else if( t.hasClass('task') ){
							return activities[t.data('activity')].name;
						}
						return '';
					},
					defaultValue: 0,
					placement: 'top',
					validate: function(value){
						var cell = $(this).closest('tr').find('.capacity'),
							current = 0.0,
							capacity = parseFloat(cell.data('capacity'));

						if( !/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(value) ){
							return 'Value must be a number, maximum of 2 decimal points';
						}

						value = parseFloat(value);

						$.each($(this).closest('tr').find('.task > i, .absence > i').not($(this)), function(){
							var v = $(this);
							current += parseFloat(v.text());
						});

						if( value + current > capacity ){
							if( capacity - current == 0 )return 'No more value is allowed';
							return 'Value cannot be greater than ' + (capacity-current).toFixed(2);
						}
					},
					//tao data va submit o day
					url: function(params){
						var t = $(this).closest('span');
						var d = new $.Deferred,
							data = {data:{}};
						if( t.data('type') == 'task' ){
							data.data = {
								id : 'ac-' + t.data('submit-id'),
								last : t.data('submit-id'),
								task_id : t.data('id'),
								activity : 0
							};
						} else {
							data.data = {
								id : 'ac-' + t.data('id'),
								last : t.data('id'),
								task_id : '',
								activity : t.data('id')
							};
						}

						data.data[t.data('date')] = params.value;

						$.ajax({
							url: '<?php echo $this->Html->url('/activity_forecasts/update_request') . $queryUpdate ?>',
							type: 'POST',
							data: data,
							success: function(response){
								d.resolve();
							},
							error: function() {
								d.reject('Problem in server!');
							}
						});
						return d.promise();
					}
				})
				.on('save', function(e, params) {
					//update consume
					me.updateConsume(this, params.newValue);
					//remove new-task markup
					$(this).closest('span').removeClass('new-task')
					alertify.success('<?php __('Saved') ?>');
				})
				.on('shown', function(e){
					$('.task.has-popover > i').not(this).editable('hide');
				})
				.click(function(event){
					event.stopPropagation();
					$('.has-popover').popover('hide');
				});
			}
		},
		updateConsume: function(what, value){
			var tr = $(what).closest('tr'),
				cell = tr.find('.capacity'),
				capacity = parseFloat(cell.data('capacity')),
				current = parseFloat(value);

			$.each(tr.find('.task > i, .absence > i').not(what), function(){
				current += parseFloat($(this).text());
			});

			$('#capacity-' + tr.data('date')).html(current.toFixed(2) + '/<b>' + capacity.toFixed(2) + '</b>');
		}
	};
	var listActi = <?php echo json_encode($lisActivityRequests);?>;
	var listTask = <?php echo json_encode($lisTaskRequests);?>;
	var listHistoryTask = <?php echo '[]';?>;
	var allowRequestRemainZero = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_remain_zero_consume']);?>;
	var onlyEmployeeInProjectHaveRequest = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_employee_in_team_consume']);?>;
	var onlyPorfitInProjectHaveRequest = <?php echo json_encode($allowRequestRemain['ActivitySetting']['allow_team_consume']);?>;

	Request.buildMenu = function(){
		var url = buildMenu_url;
		$.ajax({
			url: url,
			data: {
				listActi: listActi,
				listTask: listTask
			},
			dataType: 'json',
			success: function(data){
				mapeds = data;
				activities = data.activity;
				tasks = data.task;
				var root = $('#dl-menu-root');
				//reset menu
				root.html('');
				//rebuild menu
				//build family + sub family
				$.each(data.family, function(familyId, familyNode){
					root.append('<li id="family-' + familyId + '" class="item-family"><a href="#"><i class="glyphicon glyphicon-user"></i> ' + familyNode.name + '</a></li>');
					if( familyNode.sub ){
						if( familyNode.sub ){
							var child = root.children('#family-' + familyId);
							child.append('<ul class="dl-submenu"></ul>');
							child = child.children('ul');
							$.each(familyNode.sub, function(undefined, subId){
								child.append('<li id="family-' + familyId + '-' + subId + '" class="item-subfamily"><a href="#"><i class="glyphicon glyphicon-user"></i> ' + data.subfamily[subId].name + '</a></li>');
							});
						}
					}
				});
				//build activity
				$.each(data.activity, function(aId, node){
					//i belong to sub-family
					if( node.subfamily_id ){
						var familyNode = root.find('#family-' + node.family_id + '-' + node.subfamily_id);
					} else {
					//i only belong to family
						var familyNode = root.find('#family-' + node.family_id);
					}
					//build ul list
					var ul = familyNode.find('> ul');
					if( !ul.length ){
						familyNode.append('<ul class="dl-submenu"></ul>');
						ul = familyNode.find('> ul');
					}
					//build activity
					ul.append('<li id="activity-' + aId + '" class="item-activity"><a href="#" onclick="Request.addActivity.call(this)" data-id="' + aId + '"><i class="glyphicon glyphicon-calendar"></i> ' + node.short_name + '</a></li>');
				});
				//build tasks
				$.each(data.task, function(tId, node){
					var parent = root.find('#activity-' + node.activity_id);
					var ul = parent.find('> ul');
					//build ul list
					if( !ul.length ){
						parent.append('<ul class="dl-submenu"></ul>');
						ul = parent.find('> ul');
					}
					//build activity
					var consume = node.consumed ? parseFloat(node.consumed) : 0,
						workload = node.estimated ? parseFloat(node.estimated) : 0,
						overload = allowRequestRemainZero == 0 && consume >= workload ? 'overload' : '',
						icon = node.is_parent === 'true' ? 'remove-circle' : 'briefcase';
					overload += node.is_parent === 'true' ? ' parent' : '';
					ul.append('<li id="task-' + tId + '" class="item-task ' + overload + '"><a href="#" onclick="Request.addTask.call(this); return false;" data-id="' + tId + '"><i class="glyphicon glyphicon-' + icon + '"></i> ' + node.name + ' <b>(' + consume + '/' + workload + ')</b></a></li>');
				});

				//set up multi level menu

				$( '#dl-menu' ).dlmenu({
					animationClasses : { classin : 'dl-animate-in-2', classout : 'dl-animate-out-2' }
				});
			}
		});
	}
	Request.doFilter = function(me){
		if( me.data('type') == 'workload' ){
			var e = $('.workload[data-id="' + me.data('id') + '"]');
		} else {
			var e = $('.task[data-id="' + me.data('id') + '"]');
		}
		if( me.prop('checked') ){
			e.show();
		} else {
			e.hide();
		}
	}
	Request.filter = function(f){
		$('.filter-item').each(function(){
			$(this).prop('checked', f);
			Request.doFilter($(this));
		});
	}
	Request.addTask = function(){
		var item = $(this);
		//check if task cannot be selected (overload)
		if( allowRequestRemainZero == 0 && item.closest('li').hasClass('overload') ){
			return false;
		}
		//prevent parent select
		if( item.closest('li').hasClass('parent') ){
			return false;
		}

		var taskId = item.data('id'),
			task = tasks[taskId];
		var success = false;
		$('.date-row').each(function(){
			var date = $(this).data('date'), taskCell = $(this).find('.tasks');
			if( !taskCell.find('.task[data-id="' + taskId + '"]').length && !taskCell.hasClass('holiday') ){
				taskCell.append('<span class="task has-popover new-task type-task" id="task-' + taskId + '" data-id="' + taskId + '" data-date="' + date + '" data-activity="' + task.activity_id + '" data-type="task" data-submit-id="0-' + taskId + '">' + task.name + ' <i>0.00</i></span>');
				success = true;
			}
		});
		if( success ){
			//rebuild editable
			Request.bindEditable();
			// save history task
			$.ajax({
				url: '<?php echo $this->Html->url('/activity_forecasts/save_history_task'); ?>',
				type: 'POST',
				data: {'id':taskId,'type':'task'},
				dataType: 'json',
				success: function(data){
					listHistoryTask = data;
				},
			});
		}
		event.stopPropagation();
		$('#x-menu').modal('hide');
		$('#dl-menu').dlmenu('reset');
	}

	Request.addActivity = function(){
		var item = $(this);
		if( item.closest('li').find('ul').length )return;
		var aId = item.data('id'),
			activity = activities[aId];
		var success = false;
		$('.date-row').each(function(){
			var date = $(this).data('date'), taskCell = $(this).find('.tasks');
			if( !taskCell.find('.task[data-id="' + aId + '"]').length && !taskCell.hasClass('holiday') ){
				taskCell.append('<span class="task has-popover new-task type-activity" id="task-' + aId + '" data-id="' + aId + '" data-date="' + date + '" data-activity="' + aId + '" data-type="activity" data-submit-id="' + aId + '">' + activity.short_name + ' <i>0.00</i></span>');
				success = true;
			}
		});
		if( success ){
			//rebuild editable
			Request.bindEditable();
			// save history task
			$.ajax({
				url: '<?php echo $this->Html->url('/activity_forecasts/save_history_task'); ?>',
				type: 'POST',
				data: {'id':aId,'type':'activity'},
				dataType: 'json',
				success: function(data){
					listHistoryTask = data;
				},
			});
		}
		event.stopPropagation();
		$('#x-menu').modal('hide');
		$('#dl-menu').dlmenu('reset');
	}
	$(document).ready(function(){

		Request.init();
		Request.buildMenu();

		$('#open-filter').popover({
			animation: false,
			html: true,
			container: 'body',
			placement: 'bottom',
			trigger: 'click',
			title: '<?php __('Filter') ?> (<a href="javascript:;" onclick="Request.filter(true)"><?php __('All') ?></a> / <a href="javascript:;" onclick="Request.filter(false)"><?php __('None') ?></a>)',
			content: function(){
				var me = this;
				var data = {};
				$('td .task, td .workload').each(function(){
					var t = $(this);
					if( !data[t.data('submit-id')] ){
						data[t.data('submit-id')] = t;
					}
				});
				var html = '<div class="filter">';
				$.each(data, function(id, e){
					var checked = e.is(':hidden') ? '' : 'checked',
						type = e.hasClass('task') ? e.data('type') : 'workload';
					html += '<div class="checkbox filter-' + type + '"><label><input type="checkbox" class="filter-item checkbox" ' + checked + ' data-id="' + e.data('id') + '" data-type="' + type + '"> ' + e.text().replace(/\s\([0-9\+\-\.]+\)$/, '') + '</label>' + (type != 'workload' && ( requestConfirm == -1 || requestConfirm == 1 ) ? '<button class="btn btn-danger btn-xs action pull-right" data-action="remove-request"><i class="glyphicon glyphicon-remove"></i></button>' : '') + '</div>';
				});
				html += '</div>';
				return html;
			}
		});
		$('#open-favourite').popover({
			animation: false,
			html: true,
			container: 'body',
			placement: 'bottom',
			trigger: 'click',
			title: '<?php __('Favourite list') ?>',
			content: function(){
				var me = this;
				var addTaskString = 'Request.addTask.call(this); return false;';
				var addActivityString = 'Request.addActivity.call(this); return false;';
				if ($('#dl-menu').hasClass('hide')) {
					addTaskString = 'return false;';
					addActivityString = 'return false;';
				}
				var html = '<div class="favourite-list">';
				$.each(listHistoryTask, function(id, e){
					if (e.type=="task") {
						html += '<div><a href="#" onclick="'+addTaskString+'" data-id="'+e.id+'"><i class="glyphicon glyphicon-briefcase"></i> <label>'+tasks[e.id].name+'</label></a></div>';
					} else { // activity
						html += '<div><a href="#" onclick="'+addActivityString+'" data-id="'+e.id+'"><i class="glyphicon glyphicon-calendar"></i> <label>'+activities[e.id].name+'</label></a></div>';
					}
				});
				html += '</div>';
				return html;
			}
		});
		$(document)
		.on('click', '.filter-item', function(e){
			Request.doFilter($(this));
		})
		.on('click', '.action', function(event){
			var item = $(this);
			switch(item.data('action')){
				case 'remove-request':
					var input = item.parent().find('.filter-item');
					window.location.href = '<?php echo $this->Html->url('/activity_forecasts/delete_request/') ?>' + (input.data('type') == 'activity' ? input.data('id') + '/-1' : '0/' + input.data('id')) + '<?php echo $queryUpdate ?>';
				break;
			}
		});

		$('.hint').popover({
			placement: 'bottom'
		});

		$('#submit-request-all-top').click(function(){
			var request = 0, capacity = 0;
			$('.capacity').each(function(){
				var compare = $(this).text().split('/');
				request += parseFloat(compare[0]);
				capacity += parseFloat(compare[1]);
			});
			if( request == capacity ){
				alertify.confirm().set('message', '<?php __('Send request to manager?') ?>').set('onok', function(e){
					//submit
					$('#form-send-request').submit();
				}).show();
			} else {
				alertify.alert(<?php echo json_encode(__('You have to declare %s days before validate your request', true)) ?>.replace('%s', (capacity-request).toFixed(2)));
			}
		});

		//modal
		$('#modal-copy-forecast').modal({
			show: false,
			backdrop: 'static'
		}).on('show.bs.modal hidden.bs.modal', function(){
			$('.copy-checkbox-format').prop('checked', false);
			$('#btn-copy-forecast').prop('disabled', true);
		});
		$('.copy-checkbox-format').on('click', function(){
			if( $('.copy-checkbox-format:checked').length ){
				$('#btn-copy-forecast').prop('disabled', false);
			} else {
				$('#btn-copy-forecast').prop('disabled', true);
			}
		});

		//validate/reject
		$('#submit-request-ok-top').click(function(){
			$(this).prop('disabled', true);
			$('#ac-validated').val(1);
			$('#form-response').submit();
		});
		$('#submit-request-no-top').click(function(){
			$(this).prop('disabled', true);
			$('#ac-validated').val(0);
			$('#form-response').submit();
		});

		var _href = window.location.search;
		if(_href){
			_href = '<?php echo $this->Html->url('/') ?>activity_forecasts/request/'+typeSelect+'/'+_href;
		} else {
			_href = '<?php echo $this->Html->url('/') ?>activity_forecasts/request/'+typeSelect+'/';
		}

		//refresh menu
		$('#refresh_menu').click(function(){
			$.ajax({
				url : refresh_url,
				cache : false,
				success: function(data){
					$('#refresh_menu').hide();
					window.location = _href;
				}
			});
		}); 

		$(this).on('click', function(e){
			var target = $(e.target);
			if( !target.hasClass('has-popover') ){
				$('.has-popover').popover('hide');
			}
		});
		$.ajax({
			url: '<?php echo $this->Html->url('/activity_forecasts/save_history_task'); ?>',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				listHistoryTask = data;
			},
		});

		modalWithScrolling();

		$('#x-menu').modal({
			show: false,
			backdrop: 'static'
		}).on('hidden.bs.modal', function(){
			$('#dl-menu' ).dlmenu('reset');
		});
	});
$(window).resize(modalWithScrolling);
function modalWithScrolling() {
	var altura = $(window).height() - 150; //value corresponding to the modal heading + footer
	$(".has-scroll").css({"max-height":altura,"overflow-y":"auto"});
}
</script>