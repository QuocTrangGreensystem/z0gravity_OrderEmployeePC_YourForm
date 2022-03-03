<?php echo $this->Html->script('select2.min'); ?>
<?php echo $this->Html->script('jquery.mousewheel.3'); ?>
<?php echo $this->Html->css('mobile/select2'); ?>

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
.table-diary .<?php echo $class ?> {
		background-color: <?php echo $con['color'] ?>;
	}
	<?php endforeach; ?>
</style>
<?php
$am = __('AM', true);
$pm = __('PM', true);
$typeSelect = 'week';
$dayMaps = array(
	'monday' => $_start,
	'tuesday' => $_start + DAY,
	'wednesday' => $_start + (DAY * 2),
	'thursday' => $_start + (DAY * 3),
	'friday' => $_start + (DAY * 4),
	'saturday' => $_start + (DAY * 5),
	'sunday' => $_start + (DAY * 6)
);
asort($employees);
?>
<section class="content">
	<div class="row">
		<!-- week naviation -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header">
					<h5 class="box-title"><?php __('Navigator') ?></h5>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="glyphicon glyphicon-minus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<?php
					echo $this->Form->create('Control', array(
						'type' => 'get',
						'url' => '/' . Router::normalize($this->here),
						'class' => 'form-inline'
					));
					?>
						<div class="form-group">
							<?php
								echo $this->element('week_activity');
							?>
						</div>
						<div class="form-group">
							<?php
							echo $this->Form->year('year', date('Y', $_start) - 5, date('Y', $_start) + 2, date('Y', $_start), array('empty' => false, 'class' => 'form-control'));
							?>
						</div>
						<div class="form-group">
							<?php
							echo $this->Form->month('month', date('m', $_start), array('empty' => false, 'class' => 'form-control'));
							?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'class' => 'form-control')) ?>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-chevron-right"></i></button>
							<?php echo $this->element('mobile_expand_btn') ?>
						</div>
					<?php echo $this->Form->end() ?>
				</div>
			</div>
		</div>
		<!-- /navigation -->
<!--
<?php
$message = trim($this->Session->flash());
if( $message ): ?>
		<div class="col-md-12">
			<?php echo $message ?>
		</div>
<?php endif ?>
-->
		<!-- diary table -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding">
					<table class="table table-request table-bordered table-diary table-responsive">
						<thead>
							<tr>
								<th rowspan="2">
									<!-- employees selector -->
									<select id="current-employee" class="form-control">
<?php
foreach ($employees as $id => $name):
	//format lai name (loai bo html)
	$name = str_replace('<strong> (Manager)</strong>', __(' (Manager)', true), $name);
?><option value="<?php echo $id ?>"><?php echo $name ?></option>
<?php endforeach; ?>
									</select>
									<!-- /employees selector -->
								</th>
								<th width="15%"><a href="#" id="request-link" class="text-white"><?php __('Capacity') ?></a></th>
								<td width="50%" id="capacity"></td>
							</tr>
							<tr>
								<th><?php __('Status') ?></th>
								<td>
									<b id="status" class="pull-left"></b>
									<div class="dropdown hide pull-right" id="action">
										<button id="request-menu" class="btn btn-request text-white" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="glyphicon glyphicon-th-list"></i>
										</button>
										<ul class="dropdown-menu right-position" aria-labelledby="request-menu" id="menu">
											<li><a href="javascript:;" class="action text-primary" data-action="validate"><?php __('Validate') ?></a></li>
											<li><a href="javascript:;" class="action text-danger" data-action="reject"><?php __('Reject') ?></a></li>
										</ul>
									</div>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($listWorkingDays as $date) { ?>
								<tr data-date="<?php echo $date ?>">
									<th><?php echo __(date('l', $date)) . __(date(' d ', $date)) . __(date('M', $date)) ?></th>
									<td colspan="2" class="tasks" id="tasks-<?php echo $date ?>"></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /diary table -->
	</div>
</section>
<?php
$urls = array();
if($typeSelect === 'week'){
	$types = 'week';
	$urls = array('week' => date('W', $_end), 'year' => date('Y', $_end),'profit' => $profit['id']);
} elseif($typeSelect === 'month'){
	$types = 'month';
	$urls = array('month' => date('m', $_start), 'year' => date('Y', $_start),'profit' => $profit['id']);
} else {
	$types = 'year';
	$urls = array('month' => date('m', $_end), 'year' => date('Y', $_end),'profit' => $profit['id']);
}
if($getDataByPath)
	$urls['get_path'] = $getDataByPath;

echo $this->Form->create('Request', array('url' => array('controller' => 'activity_forecasts', 'action' => 'response', $types, '?' => $urls), 'escape' => false, 'id' => 'submit-form', 'class' => 'hide'));
echo $this->Form->hidden('selected', array('id' => 'dataSelected'));

if($typeSelect === 'week'){
	$urlEmploy = $this->Html->url(array('action' => 'request', 'week')) . '?' . http_build_query(array(
		'profit' => $profit['id'], 
		'week' => date('W', $_end), 
		'year' => date('Y', $_end),
		'get_path' => $getDataByPath
	));
} elseif($typeSelect === 'month'){
	$urlEmploy = $this->Html->url(array('action' => 'request', 'month')) . '?' . http_build_query(array(
		'profit' => $profit['id'], 
		'month' => date('m', $_end), 
		'year' => date('Y', $_end),
		'get_path' => $getDataByPath
	));
} else {
	$urlEmploy = $this->Html->url(array('action' => 'request', 'year')) . '?' . http_build_query(array(
		'profit' => $profit['id'], 
		'month' => date('m', $_end), 
		'year' => date('Y', $_end), 'get_path' => $getDataByPath
	));
}

?>
<input type="hidden" name="data[id]" id="dataId">
<input type="hidden" name="data[validated]" id="dataValidated">
<?php echo $this->Form->end() ?>
<script>
	var employees = <?php echo json_encode($employees) ?>,
		requestConfirms = <?php echo json_encode($requestConfirms) ?>,
		holidays = <?php echo json_encode($holidays) ?>,
		activityRequests = <?php echo json_encode($activityRequests) ?>,
		absences = <?php echo json_encode($absences) ?>,
		requests = <?php echo json_encode($requests) ?>
	;
	var Activity = function(){
		return this;
	}
	Activity.prototype.employee = function(){
		return $('#current-employee').val();
	}
	Activity.prototype.refreshView = function(){
		var me = this,
			id = this.employee();
		//update status
		var myStatus = '<?php __('In progress') ?>',
			classes = '';
		if( typeof requestConfirms[id] != 'undefined' && requestConfirms[id] != '-1' ){
			switch(requestConfirms[id]){
				case '0':
					myStatus = '<?php __('Waiting') ?>';
					classes = 'global-waiting';
				break;
				case '1':
					myStatus = '<?php __('Rejected') ?>';
					classes = 'global-rejected';
				break;
				case '2':
					myStatus = '<?php __('Validated') ?>';
					classes = 'global-validated';
				break;
			}
		}
		$('#status').html(myStatus).closest('td').removeClass('holiday global-waiting global-rejected global-validated').addClass(classes);;

		//clean all tasks
		$('.tasks').html('').removeClass('holiday global-waiting global-rejected global-validated').addClass(classes);

		//update absences
		var capacity = 0.00;
		if( requests[id] ){
			$.each(requests[id], function(date, request){
				var amId = request.absence_am,
					pmId = request.absence_pm,
					responseAM = request.response_am,
					responsePM = request.response_pm
				;
				var html = '';
				if( amId == pmId && responseAM == responsePM ){
					//value = 1.0
					if( absences[amId] ){
						var name = absences[amId].print;
						html = '<span class="' + responseAM + '">' + name + ' <i>1</i></span>';
					}
				} else {
					if( parseInt(amId) ){
						if( absences[amId] ){
							var amName = absences[amId].print;
							html = '<span class="' + responseAM + '">' + amName + ' <i>0.5</i></span>';
						}
					}
					if( parseInt(pmId) ){
						if( absences[pmId] ){
							var pmName = absences[pmId].print;
							html += '<span class="' + responsePM + '">' + pmName + ' <i>0.5</i></span>';
						}
					}
				}

				$('#tasks-' + date).html(html);
			});
		}
		//update tasks
		if( activityRequests[id] ){
			$.each(activityRequests[id], function(undefined, value){
				var date = value.ActivityRequest.date;
				var html = '<span class="task">' + value.Activity.name + ' <i>' + value.ActivityRequest.value + '</i></span>';
				$('#tasks-' + date).append(html);
				capacity += parseFloat(value.ActivityRequest.value);
			});
		}
		//update capacity
		$('#capacity').html(requestConfirms[id] == '2' || requestConfirms[id] == '0' ? capacity.toFixed(2) : 0);

		//update holiday (override tasks)
		$.each(holidays, function(date, value){
			var v = value.am + value.pm;
			$('#tasks-' + date).addClass('holiday').html('<span class="holiday"><?php __('Holiday') ?></span>');
		});

		// show the dropdown if status = waiting
		if( requestConfirms[id] == '0' ){
			$('#action').removeClass('hide');
		} else {
			$('#action').addClass('hide');
		}

		//link to request page
		$('#request-link').prop('href', '<?php echo $urlEmploy ?>&id=' + me.employee());
	}
	Activity.prototype.commit = function(status){
		var id = this.employee();
		$('#dataSelected').val(id + '-' + $('tr[data-date]:first').data('date'));
		$('#dataId').prop('name', 'data[id]['+id+']').val(1);
		$('#dataValidated').val(status);
		$('#submit-form').submit();
	}
	Activity.prototype.init = function(){
		var me = this;

		$('#current-employee')
			.select2()
			.change(function(){
				$.cookie('azuree_activity_resource', $(this).val(), {path : '/'});
				me.refreshView();
			});
		;

		var lastEmployee = $.cookie('azuree_activity_resource');
		if( lastEmployee && employees[lastEmployee] ){
			$('#current-employee').val(lastEmployee).trigger('change');
		} else {
			me.refreshView();
		}

		$('.action').click(function(){
			switch($(this).data('action')){
				case 'validate':
					me.commit(1);
				break;
				case 'reject':
					me.commit(0);
				break;
			}
		});
	}

	var request = new Activity();

	$(document).ready(function(){
		request.init();
		$('#absence-prev').addClass('btn btn-primary').html('<i class="glyphicon glyphicon-arrow-left"></i>');
		$('#absence-next').addClass('btn btn-primary').html('<i class="glyphicon glyphicon-arrow-right"></i>');
	});
</script>