<?php echo $this->Html->script('jquery.form'); ?>
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
.table-request td.<?php echo $class ?> {
		color: #00F;
		background-color: <?php echo $con['color'] ?>;
	}
	<?php endforeach; ?>
</style>
<?php
$am = __('AM', true);
$pm = __('PM', true);
$curDate = intval( ($_start + $_end) / 2);
foreach ($requests as &$req) {
	foreach($req as &$value)
		if( isset($value['history']) )$value['history'] = unserialize($value['history']);
}
//sort employees
asort($employees);
$urlRequests = array('controller' => 'absence_requests', 'action' => 'index', '?' => array(
	'profit' => $profit['id'], 'week' => date('W', $curDate), 'year' => date('Y', $curDate), 'get_path' => $getDataByPath));
if($typeSelect === 'month'){
	$urlRequests = array('controller' => 'absence_requests', 'action' => 'index','month', '?' => array('month' => date('m', $curDate), 'year' => date('Y', $curDate),
		'profit' => $profit['id'], 'get_path' => $getDataByPath));
} elseif($typeSelect === 'year') {
	$urlRequests = array('controller' => 'absence_requests', 'action' => 'index','year', '?' => array('month' => 1, 'year' => date('Y', $curDate),
		'profit' => $profit['id'], 'get_path' => $getDataByPath));
}

?>
<section class="content">
	<!-- week naviation -->
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<!-- <div class="box-header">
					<h5 class="box-title"><?php __('Navigator') ?></h5>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="glyphicon glyphicon-plus"></i></button>
					</div>
				</div> -->
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
								echo $this->element('week_absence');
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
							echo $this->Form->hidden('get_path', array('value' => $getDataByPath));
							?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'escape' => false, 'class' => 'form-control')) ?>
						</div>
						<div class="form-group">
							<?php
							$params = array_combine(array_keys($constraint), Set::extract('{s}.name', $constraint));
							unset($params['forecast'], $params['holiday']);
							echo $this->Form->select('st', $params, $status, array(
								'empty' => __('--- Status ---', true), 'escape' => false, 'class' => 'form-control'));
							?>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-chevron-right"></i></button>
							<?php echo $this->element('mobile_expand_btn') ?>
						</div>
					<?php
					echo $this->Form->end();
				?>
				</div>
			</div>
		</div>
	<!-- /navigation -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding table-responsive">
					<table class="table table-bordered table-request">
						<thead>
						<tr>
							<th>
								<!-- employees selector -->
								<select id="current-employee" class="form-control">
<?php
foreach ($employees as $id => $name):
	//format lai name (loai bo html)
	$name = str_replace('<strong> (Manager)</strong>', __('(Manager)', true), $name);
?><option value="<?php echo $id ?>"><?php echo $name ?></option>
<?php endforeach; ?>
								</select>
								<!-- /employees selector -->
							</th>
							<th width="15%" class="text-center"><button type="button" id="visit-profile" class="btn btn-request text-white"><?php __('Capacity') ?></button></th>
							<th width="40%" id="remains">5</th>
							<th width="5%">
								<!-- <a href="javascript:;" id="request-menu"><i class="glyphicon glyphicon-th-list"></i></a> -->
								<div class="dropdown">
								<button id="request-menu" class="btn btn-request text-white" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="glyphicon glyphicon-th-list"></i>
								</button>
								<ul class="dropdown-menu right-position" aria-labelledby="request-menu" id="menu">
									<li><a href="javascript:;" class="action text-primary" data-action="add-comment"><?php __('Add a comment') ?></a></li>
									<li role="separator" class="divider"></li>
<?php
foreach($constraint as $key => $con):
	if( $key == 'holiday' || $key == 'forecast' )continue;
?>
									<li><a href="javascript:;" class="action" data-action="response" data-response="<?php echo $key ?>"><?php echo $con['name'] ?></a></li>
<?php
endforeach;
?>
								</ul>
								</div>
							</th>
						</tr>
						</thead>
						<tbody>
					<?php
						foreach($listWorkingDays as $val):
							$rowName = __(date('l', $val), true) . __(date(' d ', $val), true) . __(date('M', $val), true);
							$amId = '';//isset($requests[$val]['absence_am']) ? $requests[$val]['absence_am'] : 0;
							$pmId = '';//isset($requests[$val]['absence_pm']) ? $requests[$val]['absence_pm'] : 0;
							$amName = '';//isset($absences[$amId]['print']) ? $absences[$amId]['print'] : '0.5';
							$pmName = '';//isset($absences[$pmId]['print']) ? $absences[$pmId]['print'] : '0.5';

							$amClass = '';//isset($requests[$val]['response_am']) && $requests[$val]['response_am'] ? 'has-request ' . $requests[$val]['response_am'] : '';
							$pmClass = '';//isset($requests[$val]['response_pm']) && $requests[$val]['response_pm'] ? 'has-request ' . $requests[$val]['response_pm'] : '';

							// if( isset($holidays[$val]['am']) && $holidays[$val]['am'] == 0.5 ){
							// 	$amName = __('Holiday', true);
							// 	$amClass = 'holiday';
							// }
							// if( isset($holidays[$val]['pm']) && $holidays[$val]['pm'] == 0.5 ){
							// 	$pmName = __('Holiday', true);
							// 	$pmClass = 'holiday';
							// }
					?>
						<tr data-date="<?php echo $val ?>" data-type="am">
							<td rowspan="2"><?php echo $rowName ?></td>
							<td class="head2"><?php echo $am ?></td>
							<td id="val-am-<?php echo $val ?>" class="absence-name <?php echo $amClass ?>"><?php echo $amName ?></td>
							<td><input class="x-checkbox" type="checkbox" id="date-am-<?php echo $val ?>" value="<?php echo $val ?>"></td>
						</tr>
						<tr data-date="<?php echo $val ?>" data-type="pm">
							<td class="head2"><?php echo $pm ?></td>
							<td id="val-pm-<?php echo $val ?>" class="absence-name <?php echo $pmClass ?>"><?php echo $pmName ?></td>
							<td><input class="x-checkbox" type="checkbox" id="date-pm-<?php echo $val ?>" value="<?php echo $val ?>"></td>
						</tr>
					<?php endforeach ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modal-comment" tabindex="-1" role="dialog" aria-labelledby="modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
				<h4 class="modal-title" id="modal-label"><?php __('Add a comment') ?></h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="comment-text" class="control-label"><?php __('Content:') ?></label>
						<textarea class="form-control" id="comment-text" rows="5"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('Cancel') ?></button>
				<button type="button" class="btn btn-primary" id="save-comment"><?php __('Save') ?></button>
			</div>
		</div>
	</div>
</div>

<script>
	var allRequests = <?php echo json_encode($requests) ?>,
		employees = <?php echo json_encode($employees) ?>,
		absences = <?php echo json_encode($absences); ?>,
		comments = <?php echo json_encode($comments) ?> || {},
		holidays = <?php echo json_encode(@$holidays); ?> || {},
		myId = <?php echo $employee_info['Employee']['id'] ?>,
		profit = <?php echo json_encode(!empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '');?>,
		start = <?php echo $_start ?>,
		end = <?php echo $_end ?>,
		typeSelect = <?php echo json_encode($typeSelect) ?>,
		timesheets = <?php echo json_encode($dayHasValidations); ?>
		;
	//alternative for single employee
	function employee(){
		return $('#current-employee').val();
	}
	function myName(){
		return employees[employee()].split('<strong>')[0];
	}
	function removeComment(id, date, type){
		var employeeId = employee();
		$.ajax({
			url: '<?php echo $this->Html->url('/') ?>absence_requests/comment_delete?id=' + id,
			type: 'GET',
			success: function(){
				delete comments[date][type][id];
				if( !Object.keys(comments[date][type]).length ){
					var t = $('#val-' + type + '-' + date)
					t.removeClass('has-comment');
					//reset popover
					t.popover('hide');
					t.popover('show');
					//if( !t.hasClass('has-request') )t.popover('destroy');
				}
				$('.comment-' + id).remove();
				alertify.success('<?php __('Saved') ?>');
			}
		});
	}
	//AbsenceRow object
	var AbsenceRow = function(tr){
		this.tr = tr;
		this.date = tr.data('date'),
		this.type = tr.data('type');
		return this;
	}
	AbsenceRow.prototype.setHasTimesheet = function(){
		var date = this.date,
			type = this.type;
		var list = timesheets[employee()];
		if( typeof holidays[date] != 'undefined' && holidays[date][type] && holidays[date][type] == 0.5 ){
			$('#val-' + type + '-' + date).text('<?php __('Holiday') ?>');
			$('#val-' + type + '-' + date).prop('class', 'holiday');
			this.tr.find('.x-checkbox').hide();
		} else if( typeof list != 'undefined' && typeof list[date] != 'undefined' ){
			this.tr.find('.x-checkbox').hide();
		}
		else {
			this.tr.find('.x-checkbox').show();
		}
		return this;
	}
	AbsenceRow.prototype.setHasComment = function(){
		var date = this.date,
			type = this.type
			id = employee();
		if( comments[id] && comments[id][date] && comments[id][date][type] ){
			this.tr.find('.absence-name').addClass('has-comment');
		}
		return this;
	}
	AbsenceRow.prototype.setAbsence = function(){
		var date = this.date,
			type = this.type;
		var td = this.tr.find('.absence-name');
		td.removeClass('has-comment waiting validated rejetion temporarily forecast has-request');
		var eId = employee();
		requests = allRequests[eId] || {};
		if( requests[date] && requests[date]['absence_' + type] && requests[date]['absence_' + type] != '0' ){
			var id = requests[date]['absence_' + type],
				name = absences[id].print,
				classes = requests[date]['response_' + type] ? 'has-request ' + requests[date]['response_' + type] : '';
			td.html(name).addClass(classes);
			var amount = 0;
			if( requests[date]['absence_' + type] != -1 ){
				td.removeClass('forecast');
			}
		} else if( requests[date] && requests[date]['response_' + type] && requests[date]['response_' + type] == 'rejetion' ){
			var name = '0.5',
				classes = requests[date]['response_' + type] ? 'has-comment ' + requests[date]['response_' + type] : '';
			td.html(name).addClass(classes);
			var amount = 0.5;
			if( requests[date]['absence_' + type] != -1 ){
				td.removeClass('forecast');
			}
		} else {
			td.html('0.5');//.removeClass('has-request waiting forecast');
			var amount = 0.5;
		}
		return amount;
	}

	var Absence = function(){
		return this;
	}

	Absence.prototype.checkMenu = function(){
		//neu ko co checkbox nao dang check thi disable menu
		if( $('.x-checkbox:checked').length ){
			$('#request-menu').prop('disabled', false);
		} else {
			$('#request-menu').prop('disabled', true);
		}
	}


	Absence.prototype.refreshAbsence = function(){
		var amount = 0,
			hasRequest = false;
		$('.table-request > tbody > tr').each(function(){
			var row = new AbsenceRow($(this));
			//kiem tra holiday
			//check comment
			amount += row.setAbsence();
			if( row.tr.find('.absence-name').hasClass('has-request') )hasRequest = true;
			row
				.setHasComment()
				.setHasTimesheet()
			;
		});
		$('#remains').text(amount.toFixed(1));
		if( !hasRequest )$('#send-email').prop('disabled', true);
		else $('#send-email').prop('disabled', false);
	}

	Absence.prototype.response = function(responseType, item){
		var data = {data : {}},
			me = this,
			employeeId = employee();
		data.data[employeeId] = {};
		var x = data.data[employeeId];
		//build data
		$('.x-checkbox:checked').each(function(){
			var tr = $(this).closest('tr');
			var absenceCell = tr.find('.absence-name');
			//if( absenceCell.hasClass('validated') || absenceCell.hasClass('holiday') )return;
			var date = tr.data('date'),
				type = tr.data('type');
			if( !x[date] ){
				x[date] = {};
			}
			x[date].date = date;
			x[date].employee_id = employeeId;
			x[date][type] = true;
		});
		if( !Object.keys(x).length )return;
		data.data.response = responseType;
		//ajax request here
		$.ajax({
			url: '<?php echo $this->Html->url('/absence_requests/manage_update/') ?>' + profit + '/' + start + '/' + end + '/' + typeSelect + '?get_path=' + <?php echo $getDataByPath ? 1 : 0 ?>,
			data: data,
			dataType: 'json',
			type: 'POST',
			success: function(response){
				if( !allRequests[employeeId] ){
					allRequests[employeeId] = {};
				}
				var requests = allRequests[employeeId];
				$.each(response[employeeId], function(date, value){
					if( !requests[date] ){
						requests[date] = value;
					} else {
						if( value.absence_am )requests[date].absence_am = value.absence_am;
						if( value.absence_pm )requests[date].absence_pm = value.absence_pm;

						if( value.response_am )requests[date].response_am = value.response_am;
						if( value.response_pm )requests[date].response_pm = value.response_pm;

						requests[date].history = value.history;
					}
				});
				me.refreshAbsence();

				alertify.success('<?php __('Saved') ?>');
			}
		});
	}

	Absence.prototype.init = function(){
		var me = this;


		//change employee
		$('#current-employee')
			.select2()
			.change(function(){
				$('.x-checkbox').prop('checked', false);
				$('.absence-name').removeClass('selected');
				$('.absence-name').popover('hide');
				me.refreshAbsence();
				$.cookie('azuree_absence_resource', $(this).val(), {path : '/'});
			})
			.on('select2:open', function(){
				$('.absence-name').popover('hide');
			});
		;
		var lastEmployee = $.cookie('azuree_absence_resource');
		if( lastEmployee && employees[lastEmployee] ){
			$('#current-employee').val(lastEmployee).trigger('change');
		} else {
			me.refreshAbsence();
		}

		//checkbox behavior
		$('.x-checkbox').click(function(){
			if( $(this).prop('checked') ){
				$(this).closest('tr').find('.absence-name').addClass('selected');
			} else {
				$(this).closest('tr').find('.absence-name').removeClass('selected');
			}
			me.checkMenu();
			$('.absence-name').popover('hide');
		});
		me.checkMenu();

		//row name popover
		$('.absence-name').popover({
			animation: false,
			html: true,
			container: 'body',
			placement: 'top',
			trigger: 'click',
			title: function(){
				if( $(this).hasClass('has-comment') || $(this).hasClass('has-request') )
					return '<?php __('Absence information') ?>';
				return '';
			},
			content: function(){
				var tr = $(this).closest('tr');
				var date = tr.data('date'),
					type = tr.data('type'),
					employeeId = employee(),
					requests = allRequests[employeeId];
				if( $(this).hasClass('has-comment') || $(this).hasClass('has-request') ){
					var html = '<div class="comments">';
					if( requests && requests[date] && requests[date]['history'] ){
						if( requests[date]['history']['rq_' + type] ){
							html += '<div class="request-time"><?php __('Date request:') ?>' + requests[date]['history']['rq_' + type] + '</div>';
						}
						if( requests[date]['history']['rj_' + type] ){
							html += '<div class="request-time"><?php __('Date reject:') ?>' + requests[date]['history']['rj_' + type] + '</div>';
						}
						if( requests[date]['history']['rv_' + type] ){
							html += '<div class="request-time"><?php __('Date validate:') ?>' + requests[date]['history']['rv_' + type] + '</div>';
						}
					}
					if( comments[employeeId] && comments[employeeId][date] && comments[employeeId][date][type] ){
						$.each(comments[employeeId][date][type], function(index, val){
							var author = val.user_id == myId ? 'You' : employees[val.user_id];
							html += '<div class="comment comment-' + index + '">'
							html += '<h5 class="comment-author">' + author + '<span class="text-muted">(' + val.created + ')</span><i class="glyphicon glyphicon-remove pull-right" onclick="removeComment(' + index + ', ' + date + ', \'' + type + '\')"></i></h5>';
							html += '<div class="comment-body">' + val.text + '</div>';
							html += '</div>';
						});
					}
					html += '</div>';
					return html;
				}
				return '';
			}
		}).on('show.bs.popover', function(){
			$('.absence-name').not(this).popover('hide');
		});

		//All modal
		me.modal = $('#modal-comment');
		me.modal.modal({
			show: false,
			backdrop: 'static'
		}).on('hidden.bs.modal', function(){
			$('#comment-text').val('');
			$(this).prop('disabled', false);
		});
		//user_id = author (who wrote comments)
		//employee_id = commented user (who received comments from others)
		$('#save-comment').click(function(){
			var text = $('#comment-text').val();
			if( !text ){
				return false;
			}
			var x = {data: {}};
			var data = x.data;
			var employeeId = employee();
			data[myId] = {};
			$('.x-checkbox:checked').each(function(){
				var date = $(this).closest('tr').data('date'),
					type = $(this).closest('tr').data('type');
				if( !data[myId][date] ){
					data[myId][date] = {};
				}
				data[myId][date].date = date;
				data[myId][date].employee_id = employeeId;
				data[myId][date][type] = text;
			});
			//ajax save here
			$(this).prop('disabled', true);
			$.ajax({
				url: '<?php echo $this->Html->url('/') ?>absence_requests/comment_update',
				data: x,
				dataType: 'json',
				type: 'POST',
				success: function(response){
					//delete response.message;
					//add responses to comments array
					$.each(response[myId], function(date, comment){
						if( !comments[employeeId] ){
							comments[employeeId] = {};
						}
						if( !comments[employeeId][date] ){
							comments[employeeId][date] = {};
						}
						if( comment.id_am ){
							if( !comments[employeeId][date].am ){
								comments[employeeId][date].am = {};
							}
							comments[employeeId][date].am[ comment.id_am ] = {
								created : comment.created,
								text : comment.am,
								user_id : comment.employee_id
							};
							//update ui
							new AbsenceRow($('#val-am-' + date).closest('tr')).setHasComment();
						}
						if( comment.id_pm ){
							if( !comments[employeeId][date].pm ){
								comments[employeeId][date].pm = {};
							}
							comments[employeeId][date].pm[ comment.id_pm ] = {
								created : comment.created,
								text : comment.pm,
								user_id : comment.employee_id
							};
							//update ui
							new AbsenceRow($('#val-pm-' + date).closest('tr')).setHasComment();
						}
					});
					alertify.success('<?php __('Saved') ?>');
				},
				complete: function(){
					me.modal.modal('hide');
				}
			});
		});

		//do action for menu
		$(document).on('click', '.action', function(event){
			var menu = $(this);
			if( menu.hasClass('disabled-link') || menu.hasClass('exceeded') )return false;

			switch(menu.data('action')){
				case 'add-comment':
					me.modal.modal('show');
				break;
				case 'response':
					var what = menu.data('response');
					me.response(what, menu);
				break;
			}
		});
		$('#visit-profile').click(function(){
			window.location.href = '<?php echo $this->Html->url($urlRequests) ?>&id=' + employee();
		});
	}

	//instantiation
	var absenceObject = new Absence();

	$(document).ready(function(){

		absenceObject.init();

		$('#absence-prev').addClass('btn btn-primary').html('<i class="glyphicon glyphicon-arrow-left"></i>');
		$('#absence-next').addClass('btn btn-primary').html('<i class="glyphicon glyphicon-arrow-right"></i>');
		$(this).on('click', function(e){
			var target = $(e.target);
			if( !target.hasClass('absence-name') ){
				$('.absence-name').popover('hide');
			}
		});
	});
</script>
