<?php echo $html->script('jquery.form'); ?>
<style>
	.currentWeek {
		padding-left: 10px;
		padding-right: 10px;
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
foreach($requests as &$request){
	$request['history'] = unserialize($request['history']);
}
$curDate = intval( ($_start + $_end) / 2);
?>
<section class="content">
	<div class="row">
		<!-- week naviation -->
		<!-- /week naviation -->
		<!-- main table -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding">
					<table class="table table-bordered table-request">
						<thead>
						<tr>
							<th><?php echo $employeeName['first_name'] . ' ' . $employeeName['last_name'] ?></th>
							<th width="15%" class="text-center"><?php __('Capacity') ?></th>
							<th width="40%" id="remains">5</th>
							<th width="5%">
								<!-- <a href="javascript:;" id="request-menu"><i class="glyphicon glyphicon-th-list"></i></a> -->
								<input id="checkAll" type="checkbox">
							</th>
						</tr>
						</thead>
						<tbody>
					<?php
						$employeeId = $employeeName['id'];
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
		<!-- /main table -->
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

<div class="modal fade" id="modal-send-email" tabindex="-1" role="dialog" aria-labelledby="modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<?php echo $this->Form->create('Request', array('escape' => false, 'url' => '/absence_requests/index/week?week=' . date('W', $curDate) . '&year=' . date('Y', $curDate))); ?>
			<div class="modal-header">
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
				<h4 class="modal-title" id="modal-label"><?php __('Confirm') ?></h4>
			</div>
			<div class="modal-body">
				<?php __('Send an email to manager?') ?>
				<?php echo $this->Form->hidden('id.' . $employeeName['id'], array('value' => 1)); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('Cancel') ?></button>
				<button type="submit" class="btn btn-primary" id="confirm-send-email"><?php __('OK') ?></button>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-document" tabindex="-1" role="dialog" aria-labelledby="modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<?php echo $this->Form->create(false, array('url' => '/absence_requests/update_document', 'id' => 'upload-document-form', 'type' => 'file')); ?>
			<div class="modal-header">
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
				<h4 class="modal-title" id="modal-label"><?php echo !empty($absenceAttachments['message']) ? $absenceAttachments['message'] : '';?></h4>
			</div>
			<div class="modal-body">
					<div class="form-group">
					<?php if( isset($absenceAttachments['document_mandatory']) && $absenceAttachments['document_mandatory'] ): ?>
						<label for="document-field" class="text-danger"><?php echo __('Document is required.', true) ?></label>
					<?php else: ?>
						<label for="document-field" class="text-success"><?php echo __('Document is optional.', true) ?></label>
					<?php endif?>
						<input type="file" name="FileField[attachment]" id="document-field">
						<label id="error" class="text-danger"></label>
						<p class="help-block"><?php echo sprintf(__('Allowed file types: <span id="file-types" class="text-info">%s</span>', true), $allowedFiles) ?></p>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('Cancel') ?></button>
				<button type="button" class="btn btn-primary action" data-action="upload-document"><?php __('Save') ?></button>
			</div>
			<?php echo $this->Form->end() ?>
		</div>
	</div>
</div>
<script>
	//build comments and popup
	var employeeId = <?php echo $employeeName['id'] ?>,
		employeeName = <?php echo json_encode($employeeName) ?>,
		myName = '<?php echo $employeeName['first_name'] . ' ' . $employeeName['last_name'] ?>',
		requests = <?php echo json_encode($requests); ?>,
		absences = <?php echo json_encode($absences); ?>,
		allComments = <?php echo json_encode(!empty($comments) ? $comments[$employeeName['id']] : array()) ?>,
		employees = <?php echo json_encode($employees); ?>,
		timesheets = <?php echo json_encode($dayHasValidations); ?>,
		holidays = <?php echo json_encode($holidays); ?> || {};
	var documentRequired = <?php echo isset($absenceAttachments['document_mandatory']) ? $absenceAttachments['document_mandatory'] : 0 ?>;
	var Absence = function(){
		return this;
	}
	var AbsenceRow = function(tr){
		this.tr = tr;
		this.date = tr.data('date'),
		this.type = tr.data('type');
		return this;
	}
	AbsenceRow.prototype.setHasTimesheet = function(){
		var date = this.date,
			type = this.type;
		if( typeof holidays[date] != 'undefined' && holidays[date][type] && holidays[date][type] == 0.5 ){
			$('#val-' + type + '-' + date).text('<?php __('Holiday') ?>');
			$('#val-' + type + '-' + date).prop('class', 'holiday');
			this.tr.find('.x-checkbox').hide();
		} else if( typeof timesheets[date] != 'undefined'){
			this.tr.find('.x-checkbox').hide();
			$('#checkAll').hide();
		} else {
			this.tr.find('.x-checkbox').show();
			$('#checkAll').show();
		}
		return this;
	}
	AbsenceRow.prototype.setHasComment = function(){
		var date = this.date,
			type = this.type;
		if( allComments[date] && allComments[date][type] ){
			this.tr.find('.absence-name').addClass('has-comment');
		}
		return this;
	}
	AbsenceRow.prototype.setAbsence = function(){
		var date = this.date,
			type = this.type;
		var td = this.tr.find('.absence-name');

		td.removeClass('has-comment waiting validated rejetion forecast temporarily has-request');

		if( requests[date] && requests[date]['absence_' + type] && requests[date]['absence_' + type] != '0'  ){
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
		}
		else {
			var name = '0.5',
				classes = '';
			td.html(name).removeClass('has-request waiting forecast');
			var amount = 0.5;
		}
		return amount;
	}
	Absence.prototype.updateAbsenceNumber = function(){
		var dateList = '', ab = this;
		$('.x-checkbox:checked').each(function(){
			dateList += '&dateCurrent[]=' + $(this).val();
		});
		if( !dateList.length )return;
		$('.action').addClass('disabled-link');
		$.ajax({
			url: '<?php echo $this->Html->url('/absence_requests/requestApi?year=' . date('Y', $curDate) . '&month=' . date('m', $curDate)) ?>' + dateList,
			type: 'GET',
			dataType: 'json',
			success: function(data){
				var currentSelected = $('.x-checkbox:checked').length * 0.5;
				$.each(data, function(index, val){
					var id = val.id,
						me = $('[data-absence-id="'+id+'"]'),
						total = val.total ? val.total : 'NA',
						current = val.request,
						d = val.document,
						xTotal = parseFloat(total);
					if( current || !isNaN(xTotal) ){
						current = parseFloat(current);
						if( isNaN(current) )current = 0;
						me.html(val.print + ' <span>(' + current + '/' + total + ')</span>');
						if( !isNaN(xTotal) && current + currentSelected > xTotal)me.addClass('exceeded');
						else me.removeClass('exceeded');
					}
					else me.html(val.print).removeClass('exceeded');;
					me.data('document', d);
				});
				$('.action').removeClass('disabled-link');
			}
		});
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
			if( row.tr.find('.absence-name').hasClass('waiting') )hasRequest = true;
			row
				//.setHoliday()
				.setHasComment()
				.setHasTimesheet()
			;
		});
		$('#remains').text(amount.toFixed(1));
		if( !hasRequest )$('#send-email').prop('disabled', true);
		else $('#send-email').prop('disabled', false);
	}

	Absence.prototype.makeRequest = function(absenceId, item){
		var data = {data : {}},
			x = data.data,
			me = this;
		//build data
		$('.x-checkbox:checked').each(function(){
			var tr = $(this).closest('tr');
			var absenceCell = tr.find('.absence-name');
			if( absenceCell.hasClass('validated') || absenceCell.hasClass('holiday') )return;
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
		x.request = absenceId ? absenceId : 0;
		//ajax request here
		$.ajax({
			url: '<?php echo $this->Html->url('/absence_requests/update?week=' . date('W', $_end) . '&year=' . date('Y', $_end)) ?>',
			data: data,
			dataType: 'json',
			type: 'POST',
			success: function(response){
				delete response.message;
				//auto validate
				var auto = response.auto_validate;
				delete response.auto_validate;
				$.each(response, function(date, value){
					if( !requests[date] ){
						requests[date] = value;
					} else {
						if( value.absence_am )requests[date].absence_am = value.absence_am;
						if( value.absence_pm )requests[date].absence_pm = value.absence_pm;

						if( value.response_am )requests[date].response_am = value.response_am;
						if( value.response_pm )requests[date].response_pm = value.response_pm;

						requests[date].history = value.history;
					}
					if( auto ){
						if( requests[date].response_am )requests[date].response_am = 'validated';
						if( requests[date].response_pm )requests[date].response_pm = 'validated';
					}
				});
				me.refreshAbsence();

				alertify.success('<?php __('Saved') ?>');
			}
		});
	}

	Absence.prototype.init = function(){
		var me = this;

		me.refreshAbsence();

		//checkbox behavior
		$('.x-checkbox').click(function(){
			if( $(this).prop('checked') ){
				$(this).closest('tr').find('.absence-name').addClass('selected');
			} else {
				$(this).closest('tr').find('.absence-name').removeClass('selected');
			}
			me.checkMenu();
		});

		me.checkMenu();

		//row name behavior
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
					type = tr.data('type');
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
					if( allComments[date] && allComments[date][type] ){
						$.each(allComments[date][type], function(index, val){
							var author = val.user_id == employeeId ? 'You' : employees[val.user_id];
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

		//ajax update absence number
		$('#request-menu').closest('.dropdown').on('show.bs.dropdown', function(){
			me.updateAbsenceNumber();
		});

		//All modals
		me.modal = $('#modal-comment');
		me.modal.modal({
			show: false,
			backdrop: 'static'
		}).on('hidden.bs.modal', function(){
			$('#comment-text').val('');
			$(this).prop('disabled', false);
		});
		$('#modal-send-email').modal({
			show: false,
			backdrop: 'static'
		});
		$('#modal-document').modal({
			show: false,
			backdrop: 'static'
		}).on('hidden.bs.modal', function(){
			$('#document-field').val('');
			$('#error').html('');
		});

		$('#send-email').click(function(event) {
			$('#modal-send-email').modal('show');
		});

		//save comment
		$('#save-comment').click(function(){
			var text = $('#comment-text').val();
			if( !text ){
				return false;
			}
			var x = {data: {}};
			var data = x.data;
			data[employeeId] = {};
			$('.x-checkbox:checked').each(function(){
				var date = $(this).closest('tr').data('date'),
					type = $(this).closest('tr').data('type');
				if( !data[employeeId][date] ){
					data[employeeId][date] = {};
				}
				data[employeeId][date].date = date;
				data[employeeId][date].employee_id = employeeId;
				data[employeeId][date][type] = text;
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
					$.each(response[employeeId], function(date, comment){
						if( !allComments[date] ){
							allComments[date] = {};
						}
						if( comment.id_am ){
							if( !allComments[date].am ){
								allComments[date].am = {};
							}
							allComments[date].am[ comment.id_am ] = {
								created : comment.created,
								text : comment.am,
								user_id : comment.employee_id
							};
							//update ui
							new AbsenceRow($('#val-am-' + date).closest('tr')).setHasComment();
						}
						if( comment.id_pm ){
							if( !allComments[date].pm ){
								allComments[date].pm = {};
							}
							allComments[date].pm[ comment.id_pm ] = {
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

		//upload document
		$('#upload-document-form').submit(function(){
			var formObj = $(this);
			//make requests
			me.makeRequest(formObj.data('id'), formObj.data('menu'));
			var action = formObj.attr('action');
			var dateList = [];
			$('.x-checkbox:checked').each(function(){
				var tr = $(this).closest('tr');
				dateList.push(tr.data('date') + '-' + tr.data('type'));
			});
			//ajax form
			formObj.ajaxSubmit({
				url: action  + '/' + employeeName.company_id + '/' + employeeId + '/' + formObj.data('id') + '/' + dateList.join(','),
				type: 'POST',
				success: function(){
				}
			});
			$('#modal-document').modal('hide');
			$('#document-field').val('');
			$('#error').html('');
			event.preventDefault(); //Prevent Default action.
			return false;
		});

		//do action for menu
		$(document).on('click', '.action', function(event){
			var menu = $(this);
			if( menu.hasClass('disabled-link') || menu.hasClass('exceeded') )return false;

			switch(menu.data('action')){
				case 'add-comment':
					me.modal.modal('show');
				break;
				case 'remove-request':
				case 'add-request':
					var absenceId = menu.data('absence-id'),
						dr = menu.data('document');
					if( !dr ){
						me.makeRequest(absenceId, menu);
					} else {
						$('#upload-document-form').data('id', absenceId).data('menu', menu);
						$('#modal-document').modal('show');
					}
				break;
				case 'upload-document':
					//check if document is required
					var $file = $('#document-field').val();
					if( documentRequired ){
						if($file == ''){
							$('#error').html('<?php __('Please select attachment!') ?>');
							return false;
						}
					}
					if( $file ) {
						var ext = $file.split('.').pop().toLowerCase();
						var types = $('#file-types').text().split(',');
						var valid = false;
						for(var i=0; i<types.length; i++){
							if( types[i].toLowerCase() == ext ){
								valid = true;
								break;
							}
						}
						if( !valid ){
							$('#error').html('<?php __('This type of file is not allowed!') ?>');
							return false;
						}
					}
					menu.closest('form').submit();
				break;
			}
		});
	}	//end init

	function removeComment(id, date, type){
		$.ajax({
			url: '<?php echo $this->Html->url('/') ?>absence_requests/comment_delete?id=' + id,
			type: 'GET',
			success: function(){
				delete allComments[date][type][id];
				if( !Object.keys(allComments[date][type]).length ){
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
	var obj = new Absence();
	var flash = <?php echo json_encode($this->Session->flash()) ?>;
	$(document).ready(function(){
		obj.init();
		// $('#send-email').click(function(){

		// });
		$('#absence-prev').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-left"></i>').css('margin-right', '5px');
		$('#absence-next').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-right"></i>');
		//hide by request from customer (PMS - Responsive issue on IPAD/SMARTPHONE)
		$('.currentWeek').hide();
		if( flash )alertify.notify(flash, 'success');
		$(this).on('click', function(e){
			var target = $(e.target);
			if( !target.hasClass('absence-name') ){
				$('.absence-name').popover('hide');
			}
		});
		$("#checkAll").change(function () {
			$('.x-checkbox').prop('checked', $(this).prop('checked'));
			$('.x-checkbox:checked').closest('tr').find('.absence-name').addClass('selected');
			$('.x-checkbox:not(:checked)').closest('tr').find('.absence-name').removeClass('selected');
			$('.x-checkbox').each(function(){
				var absence = $(this).closest('tr').find('.absence-name');
				if (absence.hasClass('validated')||absence.hasClass('waiting')) {
					absence.removeClass('selected');
					$(this).prop('checked', false);
				}
			});
			obj.checkMenu();
		});

	});
</script>
