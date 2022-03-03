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

$submitUrl = $this->Html->url(array('controller' => 'activity_forecasts', 'action' => 'response', 'week')) . '?' . http_build_query(array('year' => $year,'profit' => $profit['id'], 'get_path' => $getDataByPath ? 1 : 0));

?>
<style>
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

#absence tbody td span:not(.circle-name) {
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
	border: none
}
</style>
<?php 
$svg_icons = array(
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>');
?>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
			<div class="wd-list-project">
				<?php echo $this->Form->create(false, array('type' => 'get', 'id' => 'menu')) ?>
				<table id="table-control" class="wd-activity-actions wd-title ">
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
							<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&amp;year=<?php echo $year ?>&amp;get_path=1" id="expand-pc-btn" class="btn btn-plus"><?php echo $svg_icons['add'];?></a>
							<a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top" title="<?php __('Validate') ?>"><?php echo $svg_icons['validated'];?></a>
							<a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top" title="<?php __('Reject') ?>"><?php echo $svg_icons['reject'];?></a>
						</td>
					</tr>
				</table>
				<?php echo $this->Form->end() ?>
				<div id="message-place">
					<?php
					echo $this->Session->flash();
					?>
				</div>
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
					</tr>
					<tr>
	<?php
	foreach($workdays as $name => $amount):
		if( floatval($amount) > 0 ):
	?>
						<th width="140" class="head2"><?php __(ucfirst($name)) ?></th>
	<?php
		endif;
	endforeach ?>
					</tr>
					</thead>
					<tbody id="absence-table">
	<?php
	//build list employees
	foreach($list as $id => $value):
		$requests = $value['requests'];
		$absenceRequests = $value['absences'];
        if($managerHour){
            $hour = !empty($hourOfEmployees[$id]) && !empty($hourOfEmployees[$id]['hour']) ? $hourOfEmployees[$id]['hour'] : 0;
            $minutes = !empty($hourOfEmployees[$id]) && !empty($hourOfEmployees[$id]['minutes']) ? $hourOfEmployees[$id]['minutes'] : 0;
            $totalMinutes = $hour * 60 + $minutes;
            $totalMinutes = round($totalMinutes/2, 0);
            $halfMinutes = $totalMinutes%60;
            $halfHour = ($totalMinutes - $halfMinutes)/60;
            $halfMinutes = ($halfMinutes < 10) ? '0' . $halfMinutes : $halfMinutes;
            $halfHour = ($halfHour < 10) ? '0' . $halfHour : $halfHour;
            $hour = ($hour < 10) ? '0' . $hour : $hour;
            $minutes = ($minutes < 10) ? '0' . $minutes : $minutes;
        }
	?>
					<tr id="week-<?php echo $week ?>-<?php echo $id ?>" class="row">
						<td width="32" class="head" align="center"><input type="checkbox" class="checkbox x-check checkbox-<?php echo $week ?>" value="<?php echo $id ?>" data-week="<?php echo $week ?>"></td>
						<td width="20%" class="head">
						<a href="<?php echo $urlEmploy ?>&week=<?php echo $week ?>&id=<?php echo $id ?>">
							<?php
							if( $showAllPicture ){
								$avatarEmploy = $this->UserFile->avatar($id);
								echo '<span class="circle-name inlineblock circle-30" title="'. ( isset($value['name'])? strip_tags($value['name']) : __('Employee') ).'"><img style="width: 30px; height: 30px;" src="' . $avatarEmploy . '" alt="'. ( isset($value['name'])? strip_tags($value['name']) : __('Employee') ).'"></span>';
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
				$date = $dto->setTime(0, 0, 0)->getTimestamp();
		?>
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
								<span class="ab-validated">
									<?php echo $absences[ $absence['absence_am'] ] ?> <?php echo $managerHour ? $hour . ':' . $minutes : '(1.0)' ?>
								</span>
									<?php else: ?>
										<?php if( $absence['absence_am'] > 0 ): ?>
								<span class="ab-validated">
									<?php echo $absences[ $absence['absence_am'] ] ?> <?php echo $managerHour ? $halfHour . ':' . $halfMinutes : '(0.5)' ?>
								</span>
										<?php endif ?>
										<?php if( $absence['absence_pm'] > 0 ): ?>
								<span class="ab-validated">
									<?php echo $absences[ $absence['absence_pm'] ] ?> <?php echo $managerHour ? $halfHour . ':' . $halfMinutes : '(0.5)' ?>
								</span>
										<?php endif ?>
									<?php endif ?>
								</span>
							<?php endif ?>
							<!-- task/activity -->
							<?php if( isset($requests[$date]) ):
								foreach($requests[$date] as $request):
							?>
							<span class="task" data-capacity="<?php echo ($managerHour) ? $request['value_hour'] : floatval($request['value']) ?>"><?php echo $request['activity_id'] ? $activities[ $request['activity_id'] ] : $activitiesByTasks[ $request['task_id'] ] ?> 
                            (<?php echo ($managerHour) ? $request['value_hour'] : floatval($request['value']) ?>)
                            </span>
							<?php
								endforeach;
							endif;
							?>
						</td>
	<?php
			endif;
		endforeach;
	?>
					</tr>
	<?php
	endforeach;
	?>
				</tbody>
				</table>
<?php
endforeach;
?>
			</div></div></div>
		</div>
	</div>
</div>
<!-- dialog_vision_portfolio -->
<div id="confirm" class="buttons" style="display: none;" title="">
	<div class="dialog-request-message">
		<div id="progress"><span class="status">Processing</span><span class="current">50</span></div>
		<div id="message"></div>
	</div>
	<ul class="type_buttons" style="padding-right: 10px !important">
		<li><a href="javascript:void(0)" class="cancel"></a></li>
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
		var total = $('.x-check:checked').length,
			current = 0;
		var t = $('.current').text('0%'), s = $('.status').html(i18n('Processing'));
		$('#progress').show();
		$('.check-week').each(function(i){
			var week = $(this).val(), me = $(this);
			var employee = {};
			var subtotal = 0;
			$('.checkbox-' + week + ':checked').each(function(){
				employee[$(this).val()] = 1;
				subtotal++;
			});
			if( Object.keys(employee).length ){
				//request ajax
				var data = {data:{}};
				data.data.id = employee;
				data.data.selected = me.data('date');
				data.data.validated = status;
				$.ajax({
					url: url + '&no_output=1&week=' + week,
					data: data,
					type: 'POST',
					success: function(){
						//update stt
						current += subtotal;
						if( current >= total ){
							s.html('Done')
							t.html('');
							setTimeout(function(){
								window.location.reload(1);
							}, 500);
						} else {
							s.html(i18n('Processing'));
							t.html(Math.round(current/total * 100) + ' %');
						}
					}
				});
			}
		});
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
		var openDialog = function(title,callback){
			var $dialog = $('#confirm');
			$dialog.find('#message').html(title);
			$dialog.dialog({
				zIndex : 10000,
				modal : true,
				minHeight : 50,
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
		$('#submit-request-ok, #submit-request-ok-top').click(function(){
			var $input = $('.x-check:checked');
			if(!$input.length){
				openDialog(i18n('Please select the employees.'));
			}else{
				openDialog(i18n('Validate?'), function(){
					response(1);
					$('a.ok, a.cancel').hide();
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
