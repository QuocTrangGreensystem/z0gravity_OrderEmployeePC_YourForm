<?php 
	echo $this->Html->css(array(
		'preview/datepicker-new',
	));


if($typeSelect != 'week'):?>
<style>
    #absence-scroll {overflow-x:scroll;}#absence{width: auto !important;}
</style>
<?php endif; ?>
<style>
	#table-control fieldset #dateRequest{
		height: 32px;
		line-height: 32px;
		text-align: center;
		border: 1px solid #E8E8E8;
		border-radius: 3px;
		background-color: #fff;
		display: inline-block;
		cursor: pointer;
		float: none;
		width: 282px !important;
		padding: 0;
		text-align: left;
		vertical-align: top;
		box-sizing: border-box;
	}
	#table-control fieldset #dateRequest input{
		border: none;
		padding-left: 7px;
		width: 254px;
		line-height: 30px;
		vertical-align: top;
		box-sizing: border-box;
		height: 30px;
		display: inline-block;
		background: transparent;
	}
	#table-control fieldset #dateRequest svg{
		position: relative;
		top: 1px;
	}
	#table-control{
		margin-bottom: 0;
		margin-top: 20px;
	}
	#absence-container .wd-activity-actions select::-ms-expand {
		display: none;
	}
	#ui-datepicker-div select::-ms-expand {
		display: none;
	}
	
	#table-control #date-range-picker{
		height: 30px; padding: 0px; width: 0px !important; border: none;
		display: inline;
		vertical-align: top;
		position: absolute;
	}
</style>
<?php 
$svg_icons = array(
	'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
);
$text_format = 'next friday';
$workdays = ClassRegistry::init('Workday')->getOptions($employee_info['Company']['id']);
if(!empty($workdays)){
	foreach($workdays as $key => $isWorking){
		if($isWorking == 1) $text_format = 'next '.$key;
	}
}
$_end = strtotime($text_format, $_start);

$trans_month = array();
$trans_month[date('M', $_start)] = __(date('M', $_start), true);
$trans_month[date('M', $_end)] = __(date('M', $_end), true);
$trans_month['wee'] = __('wee', true);
$date_format = '';
?>
<input id='date-range-picker' rel="no-history" value="<?php echo date('d-m-Y',$_start); ?>" readonly="readonly">
<label id="dateRequest">
	<?php 
	$text_date = '';
	$text_start = date('d ', $_start) . $trans_month[date('M', $_start)] . '. ';
	$text_end = date('d ', $_end) . $trans_month[date('M', $_end)] . '. ' . date('Y', $_end);
	if($typeSelect == 'week'){
		$text_date = sprintf(__('Week %1$s - %2$s to %3$s', true), date('W', $_start),$text_start,$text_end); 
		$date_format = __("'Week' d - d M.  'to' d M. yy'", true);
	}else if($typeSelect == 'month'){
		// $text_start = '01 ' . $trans_month[date('M', $_start)] . '. ';
		// $text_end = date('t ', $_end) . $trans_month[date('M', $_start)] . '. ' . date('Y', $_end);
		$text_date = sprintf(__('Month %1$s - %2$s to %3$s', true), date('m', $_start),$text_start,$text_end); 
		$date_format = __("'Month' m - d M.  'to' d M. yy'", true);
	}else{
		$text_start = date('d ', $_start) . $trans_month[date('M', $_start)] . '. ' . date('Y', $_start);
		$text_date = sprintf(__('%2$s to %3$s', true), '' ,$text_start,$text_end); 
		$date_format = __("d M. yy 'to' d M. yy", true);
	}
	?>
	
	<input id='date-range-picker-display' rel="no-history" type="text" value="<?php echo $text_date; ?>" readonly="readonly">
	<?php echo $svg_icons['agenda']; 
	echo $this->Form->hidden('week', array('value' => date('W', $_start), 'rel'=>'no-history'));
	echo $this->Form->hidden('month', array('value' => date('m', $_start), 'rel'=>'no-history'));
	echo $this->Form->hidden('year', array('value' => date('Y', $_start), 'rel'=>'no-history'));
	?>
</label>
<?php 
$i18ns = array(
	'text_week' => __('Week %1$s - %2$s to %3$s', true),
	'text_month' => __('Month %1$s - %2$s to %3$s', true),
	'text_year' => __('%2$s to %3$s', true),
	'Month' => __('Month', true),
	'to' => __('au', true),
);
?>
<script type="text/javascript">
	i18n = <?php echo json_encode($i18ns); ?>;
	var i18ns = <?php echo json_encode($i18ns); ?>;
	var date_format = <?php echo json_encode($date_format); ?>;
	var format = function(str,args) {
		var regex = /%(\d+\$)?(s)/g,
		i = 0;
		return str.replace(regex, function (substring, valueIndex, type) {
			var value = valueIndex ? args[valueIndex.slice(0, -1)-1] : args[i++];
			switch (type) {
				case 's':
					return String(value);
				default:
					return substring;
			}
		});
	};
	var t = function (str,args) {

		if (i18ns[str]) {
			str = i18ns[str];
		}
		if(args === undefined){
			return str;
		}
		if (!$.isArray(args)) {
			args = $.makeArray(arguments);
			args.shift();
		}
		return format(str, args);
	};
	function selectedDate(start, end){
		if(start && end) {
			_startDate = dateString(start);
			_endDate = dateString(end);
			_day_start = _startDate.split("-");
			_day_end = _endDate.split("-");
			_class = '.date-'+_day_start[0]+'-'+_day_start[1]+'-';
			for(i = _day_start[2]; i<= _day_end[2]; i++){
				$('#date-range-picker').find(( _class.toString() + i)).addClass('ui-state-highlight');
			}
		}
	}
	$('#date-range-picker-display').on('click', function(){
		// $('#date-range-picker').trigger('click');
		$('#date-range-picker').datepicker('show');
	});
	$('#date-range-picker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		// dateFormat: "'day' d 'of' MM 'in the year' yy",
		// dateFormat: date_format,
		dateFormat: 'dd-mm-yy',
		showAnim: 'slideDown',
		changeMonth: true,
		changeYear: true,
		onSelect: function(dateText, inst) {
			var typeSelect = $(this).closest('fieldset').find('#typeRequest');
			var type = (typeSelect.length > 0) ? typeSelect.val() : 'week';
			var date = $(this).datepicker('getDate');
			// consqole.log(date);
			var input_date = dateString(date, 'dd-mm-yy');
			// $('#date-range-picker').val(input_date);
			$('#date-range-picker').trigger('change');
			var curStart = $('#nct-start-date').datepicker('getDate'),
				curEnd = $('#nct-end-date').datepicker('getDate');
			if( type == 'week' ){
				startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
				endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
			} else if(type == 'month' ){
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
			} else{
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear() + 1, date.getMonth(), 0);  //select last day
			}
			
			var start = dateString(startDate, 'dd M.');
			if(type == 'year')  start = dateString(startDate, 'dd M. yy');
			var end = dateString(endDate, 'dd M. yy');
			var week = $.datepicker.iso8601Week(startDate);
			var year = getYearFromWeekNumber(startDate, week);
			var month = date.getMonth();
			var typeVal = (type == 'week') ? week : ((type == 'month') ? (date.getMonth() + 1) : date.getFullYear());
			selectedDate(startDate, endDate);
			$('#ControlWeek').val(week).trigger('change');
			$('#ControlMonth').val(dateString(endDate, 'm')).trigger('change');
			$('#ControlYear').val(year).trigger('change');
			setTextDatePicker(start, end, typeVal, type);
			
		},
		onChangeMonthYear: function(year, month, inst) {
			selectCurrentRange();
		}
	});
	function getYearFromWeekNumber(date, week) {
        var year = date.getFullYear();
        if (week == 1 && date.getMonth() == 11) {
			year++;
        }
        return year;
    }
	function setTextDatePicker(startPicker, endPicker, typeVal, type){
		type_text = (type == 'week') ? 'text_week' : ((type == 'month') ? 'text_month' : 'text_year');
		var text_date = t(type_text, typeVal, startPicker, endPicker);
		// $('#date-range-picker').datepicker('setDate', dateString((new Date(startdate * 1000)), 'dd-mm-yy'));
		$('#date-range-picker-display').val(text_date);
	}
	function selectCurrentRange(){
        $('#date-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
	function dateString(date, format){
        if( !format )format = 'yy-mm-dd';
        return $.datepicker.formatDate(format, date);
    }
	
</script>