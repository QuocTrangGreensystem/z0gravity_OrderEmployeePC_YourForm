<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php

function getStartAndEndDate($week, $year, $endOfWeek = 'saturday') {
	$dto = new DateTime();
	$dto->setISODate($year, $week);
	//week start on monday
	$dto->modify('monday this week')->setTime(0, 0, 0);
	$ret[0] = $dto->getTimestamp();
	if( $endOfWeek == 'sunday' )
		$dto->modify('sunday next week');
	else $dto->modify($endOfWeek . ' this week');
	$ret[1] = $dto->getTimestamp();
	return $ret;
}

$urlEmploy = $this->Html->url(array('action' => 'request', 'week')) . '?' . http_build_query(array(
	'profit' => $profit['id'],
	'year' => $year,
	'get_path' => $getDataByPath
));

$submitUrl = $this->Html->url(array('controller' => 'activity_forecasts', 'action' => 'sendMailToEmployee', 'week')) . '?' . http_build_query(array('year' => $year,'profit' => $profit['id'], 'get_path' => $getDataByPath ? 1 : 0));

if($order_time_sheet == 1) $data = array_reverse($data,true);
$svg_icons = array(
	'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>');
?>
<style>
#form-not-send h3
{
	font-size: 16px;
}
#form-not-send input
{
	font-size: 13px;
	padding: 5px;
	border: 1px solid #e0e0e0;
}
#form-not-send textarea
{
	font-size: 13px;
	border: 1px solid #e0e0e0;
}
#absence {
	float: none !important;
	margin-bottom: 20px;
}
<?php foreach($constraint as $class => $con): ?>
.ab-<?php echo $class ?> {
	background-color: <?php echo $con['color'] ?>;
}
<?php endforeach ?>
#absence-table .head {
	vertical-align: middle;
	font-weight: bold;
}
.head.selected {
	background: #7CB5D2;
}
.task {
	background-color: #f6d5b9;
}

/*#absence-next, #absence-prev, .currentWeek {
	float: none !important;
	display: inline-block !important;
	margin-top: 0;
}*/
.dialog-request-message {
	padding: 0 10px;
	color: red;
	font-size: 12px;
}
#progress {
	text-align: center;
	display: none;
}
.status {
	color: #000;
	margin-right: 15px;
}
.current {
	color: green;
}

#absence tbody td span{
	padding: 5px !important;
	margin-bottom: 3px;
}
#absence tbody td span:last-child {
	margin-bottom: 0;
}
.fixed {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 9999;
	background: #f0f0f0;
	box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
	margin: 0 !important;
}
#menu {
	margin-bottom: 20px !important;
}
#table-control td {
	padding-top: 5px;
	vertical-align: middle;
}
#auto-cell {
	padding: 2px;
	text-align: center;
}
.wd-tab {
	max-width: 1920px;
}
.wd-tab .wd-panel{
	border: none;
	padding-top: 0;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
			<div class="wd-list-project">
				<?php echo $this->Form->create(false, array('type' => 'get', 'id' => 'menu')) ?>
				<table id="table-control" class="wd-activity-actions wd-title">
					<tr>
						<td id="auto-cell"><input type="checkbox" id="check-all"></td>
						<td>
							<fieldset style="float: left; overflow: auto; margin-right: 5px">
								<?php echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'name' => 'profit', 'escape' => false, 'style' => 'padding: 6px')); ?>
								<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&amp;year=<?php echo $year-1 ?>&amp;get_path=<?php echo $getDataByPath ? 1 : 0 ?>" id="absence-prev">Prev</a>
								<span class="currentWeek"><?php echo $year ?></span>
								<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&amp;year=<?php echo $year+1 ?>&amp;get_path=<?php echo $getDataByPath ? 1 : 0 ?>" id="absence-next">Next</a>
								<input type="hidden" name="get_path" value="<?php echo $getDataByPath ? 1 : 0 ?>">
								<input type="hidden" name="year" value="<?php echo $year ?>">
							</fieldset>
							<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&amp;year=<?php echo $year ?>&amp;get_path=1" id="expand-pc-btn" class="btn btn-plus"><?php echo $svg_icons['add']; ?></a>
							<a href="javascript:void(0)" id="submit-request-all-top" class="send-for-validate send-for-validate-top" title="<?php __('Send')?>"><i class="icon-rocket"></i></a>
							<?php 
								$langCode = Configure::read('Config.langCode');
								$w_order = $langCode == 'fr' ? 110 : 150;
								$opts_order = array(__('The most overdue', true), __('The less overdue', true));
								echo $this->Form->select('order-timesheet', $opts_order, $order_time_sheet , array('empty' => false, 'name' => 'ts-order', 'escape' => false, 'style' => 'padding: 6px; width: '. $w_order .'px;')); ?>
						</td>
					</tr>
				</table>
				<?php echo $this->Form->end() ?>
				<div id="message-place">
					<?php
					echo $this->Session->flash();
					?>
				</div>
				<div id="list-timesheet">
				<?php 
				foreach($data as $week => $list):
					list($weekStart, $weekEnd) = getStartAndEndDate($week, $year, $endOfWeek);
				?>
				<table id="absence">
					<thead>
						<tr>
							<th rowspan="2"><input type="checkbox" class="checkbox check-week" value="<?php echo $week ?>" data-date="<?php echo $weekStart ?>"></th>
							<th rowspan="2"><?php __('Employee') ?></th>
							<th rowspan="2"><?php __('Capacity') ?></th>
							<th colspan="<?php echo $totalWorkDaysInWeek ?>"><?php printf(__('From %s to %s', true), date('d-m-Y', $weekStart), date('d-m-Y', $weekEnd)) ?></th>
							<th rowspan="2"><?php __('Status') ?></th>
						</tr>
						<tr>
						<?php foreach($workdays as $name => $amount):
							if( floatval($amount) > 0 ):?>
							<th width="140" class="head2"><?php __(ucfirst($name)) ?></th>
						<?php endif;
						endforeach ?>
						</tr>
					</thead>
					<tbody id="absence-table">
					<?php
					//build list employees
					foreach($list as $id => $value):
						$requests = $value['requests'];
						$absenceRequests = null;
						if(isset($value['absences'])){
							$absenceRequests = $value['absences'];
						}
						if($managerHour){
							$hour = !empty($hourDayOfEmployees[$id]) && !empty($hourDayOfEmployees[$id]['hour']) ? $hourDayOfEmployees[$id]['hour'] : 0;
							$minutes = !empty($hourDayOfEmployees[$id]) && !empty($hourDayOfEmployees[$id]['minutes']) ? $hourDayOfEmployees[$id]['minutes'] : 0;
							$totalMinutes = $hour * 60 + $minutes;
							$totalMinutes = round($totalMinutes/2, 0);
							$halfMinutes = $totalMinutes%60;
							$halfHour = ($totalMinutes - $halfMinutes)/60;
							$halfMinutes = ($halfMinutes < 10) ? '0' . $halfMinutes : $halfMinutes;
							$halfHour = ($halfHour < 10) ? '0' . $halfHour : $halfHour;
							$hour = ($hour < 10) ? '0' . $hour : $hour;
							$minutes = ($minutes < 10) ? '0' . $minutes : $minutes;
						} ?>
						<tr id="week-<?php echo $week ?>-<?php echo $id ?>" class="row">
							<td width="32" class="head" align="center"><input type="checkbox" class="checkbox x-check checkbox-<?php echo $week ?>" value="<?php echo $id ?>" data-week="<?php echo $week ?>"></td>
							<td width="20%" class="head">
							<a href="<?php echo $urlEmploy ?>&week=<?php echo $week ?>&id=<?php echo $id ?>">
								<?php
								if( $showAllPicture ){
									$avatarEmploy = $this->UserFile->avatar($id);
									echo '<div class="circle-name inlineblock circle-30" title="'. ( isset($value['name'])? strip_tags( $value['name']) : __('Employee') ).'"><img style="width: 30px; height: 30px;" src="' . $avatarEmploy . '" alt="'. ( isset($value['name'])? strip_tags($value['name']) : __('Employee') ).'"></div>';
								}
								if(isset($value['name'])){ echo $value['name']; }?>
							</a>
							</td>
							<td width="5%" class="head capacity" align="center">0</td>
							<?php
							foreach($workdays as $name => $amount):
								if( floatval($amount) > 0 ):
									$dto = new DateTime();
									$dto->setTimestamp($weekStart);
									if( $name == 'sunday' ){
										$dto->modify('sunday next week');	//because week starts on sunday
									} else {
										$dto->modify($name . ' this week');
									}
									$date = $dto->setTime(0, 0, 0)->getTimestamp();?>
								<td>
									<?php if( isset($holidays[$date]) ): ?><span class="ab-holiday"><?php __('Holiday') ?> (<?php 
										$hlAm = isset($holidays[$date]['am']) ? 0.5 : 0;
										$hlPm = isset($holidays[$date]['pm']) ? 0.5 : 0;
										$totalHolidays = $hlAm + $hlPm;
										if($managerHour){
											echo $hour . ':' . $minutes; 
										} else {
											echo $totalHolidays; 
										}
									?>)</span><?php endif ?>
									<?php if( isset($absenceRequests[$date]) ): $absence = $absenceRequests[$date]; ?>
										<?php if( $absence['absence_am'] == $absence['absence_pm'] && $absence['response_pm'] == $absence['response_am'] ): ?>
										<span class="ab-<?php echo $absence['response_am'] ?>">
											<?php echo @$absences[ $absence['absence_am'] ] ?> (1.0)
										</span>
										<?php else: ?>
											<?php if( $absence['absence_am'] ): ?>
										<span class="ab-<?php echo $absence['response_am'] ?>">
											<?php echo @$absences[ $absence['absence_am'] ] ?> (0.5)
										</span>
											<?php endif ?>
											<?php if( $absence['absence_pm'] ): ?>
										<span class="ab-<?php echo $absence['response_pm'] ?>">
											<?php echo @$absences[ $absence['absence_pm'] ] ?> (0.5)
										</span>
											<?php endif ?>
										<?php endif ?>
										</span>
									<?php endif ?>
									<!-- task/activity -->
									<?php if( isset($requests[$date]) ):
										foreach($requests[$date] as $request):
											$type = $request['status'];
									?>
									<span class="task" data-capacity="<?php echo ($managerHour) ? $request['value_hour'] : floatval($request['value']) ?>">
										<?php echo ( $request['activity_id'] && !empty($activities[ $request['activity_id'] ]) ) ? $activities[ $request['activity_id'] ] : ( !empty($activitiesByTasks[ $request['task_id'] ]) ? $activitiesByTasks[ $request['task_id'] ] : 0) ?>
										(<?php echo ($managerHour) ? $request['value_hour'] : floatval($request['value']) ?>)</span>
									<?php
										endforeach;
									endif;
									?>
								</td>
						<?php endif;
						endforeach; ?>
						<td width="32" class="head" align="center"><?php if($value['status_confirm'] == 1){ echo '<span style="color:red">';__("Rejected");echo' </span>'; }else{ echo '<span style="color:blue">';__("In progress");echo' </span>';}?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endforeach; ?>
			</div> <!-- End list timesheet -->
			</div></div></div>
		</div>
	</div>
</div>
<!-- dialog_vision_portfolio -->
<div id="confirm" class="buttons" style="display: none; " title="Send Timesheet">
	<div class="dialog-request-message">
		<div id="progress"><span class="status">Processing</span><span class="current">50</span></div>
		<div id="message"></div>
	</div>
	<div style="padding-left: 10px " id="form-not-send">
		<form >
			<!-- <input type="text" placeholder="To" style="width:96%" id="to-mail-not-send"><br><br> -->
			<?php
				if(!empty($info_mail) && !empty($info_mail['MailNotSendYet']))
				{ ?>
				<h3 style="color:black"><?php echo __('Subject', true);?></h3>
				<input type="text" id="subject-not-sent" placeholder="Subject" style=" width:97.4%" value="<?php echo $info_mail['MailNotSendYet']['subject'];?>"><br><br>
				<h3 style="color:black"><?php echo __('Content', true);?></h3>
				<textarea id="content-not-sent" style=" width:98%" rows="15" placeholder="Content"><?php echo $info_mail['MailNotSendYet']['content'];?></textarea>
				<h4 style="color:black; font-size: 12px;"><?php echo sprintf(__('Modify by %s at %s', true), $employee_name_mail, $info_mail['MailNotSendYet']['updated']);?>  </h4>
			<?php	}else{ ?>
				<h3 style="color:black"><?php echo __('Subject', true);?></h3>
				<input type="text" id="subject-not-sent" placeholder="Subject" style=" width:97.4%"><br><br>
				<h3 style="color:black"><?php echo __('Content', true);?></h3>
				<textarea id="content-not-sent" style=" width:98%" rows="15" placeholder="Content"></textarea>
			<?php }
			?>
		</form>
	</div>
	<ul class="type_buttons" style="padding-right: 10px !important">
		<li><a href="javascript:void(0)" class="cancel">Save</a></li>
		<li><a href="javascript:void(0)" class="ok"></a></li>
	</ul>
</div>
<script>
	var locale = <?php echo json_encode(array(
        'Please select the employees.' => __('Please select the employees.', true),
        'Reject?' => __('Reject?', true),
        'Validate?' => __('Validate?', true),
        'Processing' => __('Processing', true)
    )) ?>;
    function i18n(text){
        if( typeof locale[text] != 'undefined' )return locale[text];
        return text;
    }
	var year = <?php echo $year ?>,
		url = <?php echo json_encode($submitUrl) ?>,
        managerHour = <?php echo json_encode($managerHour);?>;
	function response(status){
		if($("#subject-not-sent" && "#content-not-sent").val() == '')
		{
			alert('Subject and Content of email not null!');return false;
		}
		if($("#subject-not-sent").val() == '')
		{
			alert('Subject of email not null!');return false;
		}
		if($("#content-not-sent").val() == '')
		{
			alert('Content of email not null!');return false;
		}
		var total = $('.x-check:checked').length,
			current = 0;
		var t = $('.current').text('0%'), s = $('.status').html(i18n('Processing'));
		$('#progress').show();
        var employee = {};
		$('.check-week').each(function(i){
			//lay tat ca check week
			var week = $(this).val(),
			me = $(this);
            var _dates = me.data('date');
            var listEmploy = '';
			$('.checkbox-' + week + ':checked').each(function(){
                listEmploy += $(this).val() + '-';
			});
            listEmploy = listEmploy.substr(0, listEmploy.length-1);
            if(listEmploy){
                employee[_dates] = listEmploy;
            }

        });
        employee['subject'] = $("#subject-not-sent").val();
        employee['content'] = $("#content-not-sent").val();
		if( Object.keys(employee).length ){
			$.ajax({
				url: url,
				data: {
					employee: employee
				},
				dataType: 'json',
				type: 'POST',
				success: function(){
					$("#form-not-send").hide();
					$('a.ok, a.cancel').hide();
					s.html(i18n('Done!'));
					t.html('');
				    setTimeout(function(){
						window.location.reload(1);
					}, 1000);
				}
			});
		}

	}

	$(window).bind('scroll', function () {
		var menu = $('#menu');
		var table = $('#absence')
		if ($(window).scrollTop() > 150) {
			menu.addClass('fixed');
			if( table.length )menu.css('padding-left', table.offset().left + 'px');
		} else {
			menu.removeClass('fixed');
			menu.css('padding-left', 0);
		}
	}).bind('resize', function(){
		var t = $('.row td:first');
		if( t.length ){
			$('#auto-cell').width(t.width());
		}
	});
	$(document).ready(function(){
		$('.check-week').each(function(i){
					var week = $(this).val();
					$('.checkbox-' + week + ':checked').each(function(){
						alert(123);
					});
		        });
		var openDialog = function(title,callback){
			var $dialog = $('#confirm');
			$dialog.find('#message').html(title);
			var lWidth = $(window).width();
			var DialogFull = Math.round((50*lWidth)/100);
			$dialog.dialog({
				zIndex : 10000,
				modal : true,
				minHeight : 50,
				width: DialogFull,
				close : function(){
					$dialog.dialog('destroy');
				}
			});
			$dialog.find('a.ok').unbind().click(function(){
				if(!$.isFunction(callback)){
					$dialog.dialog('close');
				}else{
					callback.call(this);
				}
				return false;
			});
			$dialog.find('a.cancel').unbind().click(function(){
				$dialog.dialog('close');
				return false;
			}).toggle($.isFunction(callback));
		};
		$('#submit-request-no, #submit-request-no-top').click(function(){
			var $input = $('.x-check:checked');
			if(!$input.length){
				openDialog(i18n('Please select the employees.'));
			}else{
				openDialog(i18n('Reject?'),function(){
					response(0);
					$('a.ok, a.cancel').hide();
				});
			}
		});
		$('#submit-request-ok, #submit-request-all-top').click(function(){
			var $input = $('.x-check:checked');
			var select_a_resource = <?php echo json_encode(__('Select a resource', true)) ?>;
			var send_timesheet = <?php echo json_encode(__('Send timesheet', true)) ?>;
			if(!$input.length){
				$("#form-not-send").hide();
				$("#confirm").prop('title', '');
				openDialog(i18n(select_a_resource));
			}else{
				$("#form-not-send").show();
				$("#confirm").prop('title', send_timesheet);
				openDialog(i18n(''), function(){
					response(1);
					//$('a.ok, a.cancel').hide();
				});
			}
		});
		$('.row').each(function(){
			var capacity = 0;
            if(managerHour){
                $(this).find('.task').each(function(){
                    var _hour = $(this).data('capacity');
                    _hour = _hour.split(':');
                    _hour = parseInt(_hour[0]) * 60 + parseInt(_hour[1]);
    				capacity += parseInt(_hour);
    			});
                var m = capacity%60;
                var h = (capacity - m)/60;
                m = (m < 10) ? '0'+m : m;
                h = (h < 10) ? '0'+h : h;
    			$(this).find('.capacity').html(h + ':' + m);
            } else {
                $(this).find('.task').each(function(){
    				capacity += parseFloat($(this).data('capacity'));
    			});
    			$(this).find('.capacity').html(capacity.toFixed(2));
            }
		});

		$('.check-week').click(function(event) {
			$('.checkbox-' + $(this).val()).prop('checked', $(this).prop('checked'));
		});
		$('#check-all').click(function(){
			$('.checkbox').prop('checked', $(this).prop('checked'));
		});

		$(window).trigger('resize');
		$( "#profit" ).change(function() {
			var currentUrl = updateQueryStringParameter(location.href,'profit',$("#profit").val());
			location.href = currentUrl;
		});
		$( "#order-timesheet" ).change(function() {
			var list = $("#list-timesheet");
			var item = list.find("table");
			var _content_item = [];
			var n = 0;
			for (i = item.length; i >= 0; i--) {
				_content_item[n++] = $(item[i]).html();
			}
			if(_content_item.length > 0){
				list.empty();
				$.each(_content_item, function(i, content){
					if(content) list.append('<table id="absence">' + content + '</table>');
				});
				_val_order = $(this).val();
				$.ajax({
					type: 'POST',
					url: '/activity_forecasts/save_history_order_timesheet',
					data: {
						path: 'timesheet_not_sent_yet_order',
						params: _val_order,
					},
					cache: false,
				});
			}
		});
		function updateQueryStringParameter(uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				return uri + separator + key + "=" + value;
			}
		}
	});
</script>
