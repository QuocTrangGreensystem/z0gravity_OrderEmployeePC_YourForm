<?php echo $html->css(array(
		'context/jquery.contextmenu', 
		'preview/absence-calendar',
		'add_popup'
	)); ?>
    <?php echo $html->script('context/jquery.contextmenu'); ?>
    <?php echo $html->script('jquery.form'); ?>
    <?php echo $html->script('jquery.ui.touch-punch.min'); ?>
<style type="text/css">
    #absence-fixed thead tr th{height: 43px;text-align: center;vertical-align: middle;}
    #absence-fixed th,#absence-fixed td.st{
        border-right : 1px solid #5fa1c4;
        color: #fff !important;
        text-align: left;
    }
    #absence-fixed .st a{
        color: #fff;
    }
    #absence-fixed .st strong{
        font-size: 0.8em;
        color : #FE4040;
    }
    .wd-title {padding-left: 10px;}
    #absence-scroll {
        overflow-x: scroll;
    }
    .ch-absen-validation{
        background-color: #F0F0F0;
    }
    .monday_am{
        border-left: 2px solid red;
    }
    #absence-table tr td.ch-absen-validation{background-color: #c3dd8c;}
    #absence-wrapper #absence-fixed{ width: 12% !important;}
    .context-menu-item-inner span{color: red;}
    #error{ color:#F00; padding-top:10px; font-size: 12px; }
    .mes_absence_atch{
        color: black;
        font-size: 15px;
        margin: 5px 0;
        font-weight: bold;
    }
    #absence thead tr th{
        min-width: 150px;
        max-width: 300px;
    }

    #sub-nav{
        max-width: 1920px;
        margin: 0 auto
    }
	.wd-tab .wd-panel{
		padding: 0;
		border: none;
	}
	.rp-validated span {
        background-color: unset !important;
    }
    .rp-holiday span {
        background-color: unset !important;
    }
    .absence_selectable,.td.selectable{
        position: relative;
    }
    .absence_selectable:before,.td.selectable:before{
        position: absolute;
        content: '';
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    }
	span#ui-dialog-title-wdConfimDialog {
		color: #666;
		font-weight: 600;
		font-size: 14px;
		line-height: 40px;
		height: 40px;
		text-overflow: ellipsis;
		overflow: hidden;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		display: -webkit-box;
		max-width: 480px;
	}
	p.alert-message.text-center.form-message.request-message {
		padding: 0;
		line-height: 28px;
	}
	span.ui-button-text {
		color: #fff;
	}

</style>
<?php
$svg_icons = array(
	'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
	'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-216 -168)"><rect class="a" width="16" height="16" transform="translate(216 168)"/><path class="b" d="M902-2125h-4v-1h3v-3h1v4Zm-8,0h-4v-4h1v3h3v1Zm8-8h-1v-3h-3v-1h4v4Zm-11,0h-1v-4h4v1h-3v3Z" transform="translate(-672 2307)"/></g></svg>',
	'reload' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16"><g transform="translate(-1323 -240)"><path class="b" d="M199.5,191.5a7.98,7.98,0,0,0-5.44,2.15v-1.51a.64.64,0,0,0-1.28,0v3.2a.622.622,0,0,0,.113.341l.006.009a.609.609,0,0,0,.156.161c.007.005.01.013.017.018s.021.009.031.015a.652.652,0,0,0,.115.055.662.662,0,0,0,.166.034c.012,0,.023.007.036.007h3.2a.64.64,0,1,0,0-1.28h-1.8a6.706,6.706,0,1,1-2.038,4.8.64.64,0,1,0-1.28,0,8,8,0,1,0,8-8Z" transform="translate(1131.5 48.5)"/><rect class="a" width="16" height="16" transform="translate(1323 240)"/></g></svg>',
	'duplicate' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1621.625 -334.663)"><rect class="a" width="16" height="16" transform="translate(1621.625 334.663)"/><g transform="translate(36.824 46.863)"><path class="b" d="M1586.915,301.177a1.116,1.116,0,0,1-1.115-1.115V288.915a1.116,1.116,0,0,1,1.115-1.115h8.525a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Zm0-12.459a.2.2,0,0,0-.2.2v11.147a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V288.915a.2.2,0,0,0-.2-.2Z"/><path class="b" d="M1590.915,305.177a1.116,1.116,0,0,1-1.115-1.115v-.656a.459.459,0,1,1,.918,0v.656a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V292.915a.2.2,0,0,0-.2-.2h-.656a.459.459,0,0,1,0-.918h.656a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Z" transform="translate(-0.754 -1.377)"/></g></g></svg>',
	'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
	'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
	'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
	'validated' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="32" height="32" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
	'reject' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32 viewBox="0 0 40 40"><g transform="translate(-323 -71)"><rect class="a" width="32" height="32" transform="translate(-323 -71)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>',
	'trash' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs></defs><g transform="translate(-1627.041 -485.28)"><rect class="a" width="40" height="40" transform="translate(1627.041 485.28)"/><path class="b" d="M3.6,16a1.483,1.483,0,0,1-1.426-1.532V2.542H.391A.406.406,0,0,1,0,2.122.406.406,0,0,1,.391,1.7H4V.976A.946.946,0,0,1,4.911,0h6.045a.946.946,0,0,1,.909.976V1.7h3.744a.407.407,0,0,1,.391.42.407.407,0,0,1-.391.42H13.693V14.468A1.483,1.483,0,0,1,12.267,16Zm-.644-1.532a.671.671,0,0,0,.644.693h8.667a.671.671,0,0,0,.644-.693V2.542H2.956ZM4.785.976V1.7h6.3V.976A.132.132,0,0,0,10.956.84H4.911A.132.132,0,0,0,4.785.976Zm5.458,11.993V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.69,0V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.71,0V5.261a.4.4,0,1,1,.79,0v7.708a.4.4,0,1,1-.79,0Z" transform="translate(1639.042 497.28)"/></g></svg>'
);
$errorBegin = '<span> - begin period select cell by cell</span>';
$errorExp = '<span>    - Overload         </span>';
/**
 * Returns the calendar's html for the given year and month.
 *
 * @param $year (Integer) The year, e.g. 2015.
 * @param $month (Integer) The month, e.g. 7.
 * @param $events (Array) An array of events where the key is the day's date
 * in the format "Y-m-d", the value is an array with 'text' and 'link'.
 * @return (String) The calendar's html.
 */

function build_html_calendar_week($_this, $year, $month,  $week, $requests = null ,$listWorkingDays, $type) {
	$css_cal_container = 'calendar-container';
	$css_cal = 'calendar';
	$css_cal_row = 'calendar-row';
	$css_cal_day_head = 'calendar-day-head';
	$css_cal_day = 'calendar-day';
	$css_cal_day_number = 'day-number';
	$css_cal_day_blank = 'calendar-day-np';
	$css_cal_day_event = 'calendar-day-absence';
	$css_cal_event = 'calendar-absence';
	$css_cal_weekend = 'calendar-off';
	$css_cal_selectable = 'absence_selectable';
	$css_cal_headding = 'absence-heading';
	$sum_capacity = 0;
	$headings = array(
		'monday' => __('Monday', true), 
		'tuesday'=> __('Tuesday', true), 
		'wednesday'=> __('Wednesday', true), 
		'thursday'=> __('Thursday', true), 
		'friday'=> __('Friday', true), 
		'saturday'=> __('Saturday', true), 
		'sunday'=> __('Sunday', true)
	 );
	 $capacity_text = __('Capacity', true);
	 $text_month = __(date('F', mktime(0, 0, 0, $month, 1, $year)), true);
	 $list_absences = $_this->viewVars['absences'];
	 $workdays = $_this->viewVars['workdays'];
	
	 $calendar_heading = "<table cellpadding='0' cellspacing='0' class='absence-calendar {$css_cal}'><tr class='{$css_cal_row}'>";
	 $calendar_cotent = "<tr class='{$css_cal_row}'>";
	 foreach($listWorkingDays as $key => $date) {
		$calendar_heading .= "<td class='{$css_cal_day_head}'><span class='day-text'>" . $headings[strtolower(date('l', $date))] ."
		</span><div class='block'><span class='am'>AM</span><span class='pm'>PM</span></div></td>";
		$calendar_day = date('d', $date);
		$calendar_cotent .= "<td class='{$css_cal_day}'><div class='{$css_cal_day_number}'>{$calendar_day}</div>";
		if (!empty($requests) && !empty($requests[$date])) {
			if(!empty($requests[$date]['absence_am'])){
				$am_status = $requests[$date]['response_am'];
				$am_id = $requests[$date]['absence_am'];
				$am_text = (!empty($list_absences) && !empty($list_absences[$am_id]['print'])) ?  $list_absences[$am_id]['print'] : '';
				$calendar_cotent .= "<div class='{$css_cal_selectable} select-am {$date}-am {$am_status}' title = '{$am_text}' data-date = '{$date}' data-type = 'am'>0.5</div>";
			}else{
				$sum_capacity += 0.5;
				$calendar_cotent .= "<div class='{$css_cal_selectable} {$date}-am' data-date = '{$date}' data-type = 'am'></div>";
			}
			if(!empty($requests[$date]['absence_pm'])){
				$pm_status = $requests[$date]['response_pm'];
				$pm_id = $requests[$date]['absence_pm'];
				$pm_text = (!empty($list_absences) && !empty($list_absences[$pm_id]['print'])) ?  $list_absences[$pm_id]['print'] : '';
				$calendar_cotent .= "<div class='{$css_cal_selectable} select-pm {$date}-pm {$pm_status}' title = '{$pm_text}'  data-date = '{$date}' data-type = 'pm'>0.5</div>";
			}else{
				$calendar_cotent .= "<div class='{$css_cal_selectable} {$date}-pm'  data-date = '{$date}' data-type = 'pm'></div>";
				$sum_capacity += 0.5;
			}
		}else{
			$calendar_cotent .= "<div class='{$css_cal_selectable} {$date}-am' data-date = '{$date}' data-type = 'am'></div><div class='{$css_cal_selectable} {$date}-pm'  data-date = '{$date}' data-type = 'pm'></div>";
			$sum_capacity += 1;
		}
		$calendar_cotent .= "</td>";
	 }
	 
	 // Draw day off of week
	 foreach ($workdays as $date => $isWorking){
		 if($isWorking == 0){
			 $calendar_heading .= "<td class='{$css_cal_day_head}'><span class='day-text'>" . $headings[$date] ."</span><div class='block'><span class='am'>AM</span><span class='pm'>PM</span></div></td>";
			 $calendar_cotent .= "<td class='{$css_cal_day} {$css_cal_weekend}'></td>";
		 }
	 }
	 
	 $calendar_heading .= '</tr>';
	 $calendar_cotent .= "</tr></table></div>";
	 $calendar_capacity = "<div class='{$css_cal_container} {$type}'><div class= '{$css_cal_headding}'><div class='heading-capacity'><p class='capacity-text'>{$capacity_text}</p><p class='capacity-sum'>{$sum_capacity}</p></div><div class='heading-date'><p class='heading-year'>{$year}</p><p class='heading-month'>{$text_month}</p></div></div>";
	  // All done, return result
	 return $calendar_capacity . $calendar_heading . $calendar_cotent;
}

function build_html_calendar($_this, $year, $month, $events = null, $type) {

	  // CSS classes
	  $css_cal_container = 'calendar-container';
	  $css_cal = 'calendar';
	  $css_cal_row = 'calendar-row';
	  $css_cal_day_head = 'calendar-day-head';
	  $css_cal_day = 'calendar-day';
	  $css_cal_day_number = 'day-number';
	  $css_cal_day_blank = 'calendar-day-np';
	  $css_cal_day_event = 'calendar-day-absence';
	  $css_cal_event = 'calendar-absence';
	  $css_cal_weekend = 'calendar-off';
	  $css_cal_validation = 'ch-absen-validation';
	  $css_cal_selectable = 'absence_selectable';
	  $css_cal_headding = 'absence-heading';
	  $css_cal_holiday = 'rp-holiday';
	  $sum_capacity = 0;
	  // Table headings
	  $workdays = $_this->viewVars['workdays'];
	  $list_absences = $_this->viewVars['absences'];
	  $dayHasValidations = $_this->viewVars['dayHasValidations'];
	  $holidays = $_this->viewVars['holidays'];
	  $comments = $_this->viewVars['comments'];
	  $list_comments = array();
	  if(!empty($comments)){
		  foreach($comments as $id => $values){
			  if(!empty($comments)){
				 $list_comments =  $values;
			  }
		  }
	  }
      // $comments = !empty($comments) ? Set::classicExtract($comments, '{n}') : array();
	  $headings = array(
		'monday' => __('Monday', true), 
		'tuesday'=> __('Tuesday', true), 
		'wednesday'=> __('Wednesday', true), 
		'thursday'=> __('Thursday', true), 
		'friday'=> __('Friday', true), 
		'saturday'=> __('Saturday', true), 
		'sunday'=> __('Sunday', true)
	  );
	  $capacity_text = __('Capacity', true);
	  $holiday_text = __('Holiday', true);
	  // Start: draw table
	  $text_month = __(date('F', mktime(0, 0, 0, $month, 1, $year)), true);
	 
	  $calendar =
		"<table cellpadding='0' cellspacing='0' class='absence-calendar {$css_cal}'>" .
		"<tr class='{$css_cal_row} calendar-header'>" .
		"<td class='{$css_cal_day_head}'><span class='day-text'>" .
		implode("</span><div class='block'><span class='am'>AM</span><span class='pm'>PM</span></div></td><td class='{$css_cal_day_head}'><span class='day-text'>", $headings) .
		"</span><div class='block'><span class='am'>AM</span><span class='pm'>PM</span></div></td>" .
		"</tr>";

	  // Days and weeks
	  $running_day = date('N', mktime(0, 0, 0, $month, 1, $year));
	  $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
	  
	  // Row for week one
	  $calendar .= "<tr class='{$css_cal_row}'>";

	  // Print "blank" days until the first of the current week
	  for ($x = 1; $x < $running_day; $x++) {
		$calendar .= "<td class='{$css_cal_day_blank}'> </td>";
	  }
	  $abences = array();
	  if(!empty($events)){
		  foreach($events as $date => $absence){
			  $abences[date('Y-m-d',$date)] = $absence;
		  }
	  }
	
	  // Keep going with days...
	  for ($day = 1; $day <= $days_in_month; $day++) {
		// Check if there is an event today
		$cur_stamp = mktime(0, 0, 0, $month, $day, $year);
		$cur_date = date('Y-m-d', $cur_stamp);
		$draw_event = false;
		if (isset($events) && isset($events[$cur_date])) {
		  $draw_event = true;
		}
		$css_class = "{$css_cal_day}";
		$css_class .= $draw_event ? " {$css_cal_day_event}" : '';
		
		$weekend = false;
		if ($workdays[strtolower(date('l', mktime(0, 0, 0, $month, $day, $year)))] == 0) {
		  $weekend = true;
		}
		
		$timesheet_validated = false;
		if(in_array($cur_stamp, $dayHasValidations)){
			$timesheet_validated = true;
		}
		
		$css_class .= $weekend ? " {$css_cal_weekend}" : '';
		$css_class .= ($timesheet_validated && !$weekend) ? " {$css_cal_validation}" : '';
		$css_selectable = $timesheet_validated ? '' : $css_cal_selectable;
		// Day cell
		$calendar .= "<td class='{$css_class}'>";

		
		$holiday_am = false;
		$holiday_pm = false;
		if(!empty($holidays[$cur_stamp])){
			if(!empty($holidays[$cur_stamp]['am'])){
				$holiday_am = true;
			}
			if(!empty($holidays[$cur_stamp]['pm'])){
				$holiday_pm = true;
			}
		}
		// Insert an event for this day
		if (!empty($abences) && !empty($abences[$cur_date])) {
			if($holiday_am){
				$calendar .= "<div class='am {$css_cal_holiday} {$cur_stamp}-am ' data-date = '{$cur_stamp}' data-type = 'am' dx = 0 dy = {$cur_stamp}><span>{$holiday_text}</span></div>";
			}else{
				if(isset($abences[$cur_date]['absence_am']) && !empty($abences[$cur_date]['response_am'])){
					$am_status = '';
					$has_comment = '';
					$am_id = $abences[$cur_date]['absence_am'];
					if($am_id == 0) $sum_capacity += 0.5;
					if(!$timesheet_validated){
						$am_status = 'rp-'.$abences[$cur_date]['response_am'];
						$has_comment = 'has-comment';
					}
					$am_text = (!empty($list_absences) && !empty($list_absences[$am_id]['print'])) ?  $list_absences[$am_id]['print'] : '';
					$calendar .= "<div class='am {$has_comment} {$css_selectable} {$am_id} select-am {$cur_stamp}-am {$am_status}' title = '{$am_text}' data-date = '{$cur_stamp}' data-type = 'am' dx = 0 dy = {$cur_stamp}><span>{$am_text}</span></div>";
					
				}else{
					$sum_capacity += 0.5;
					// comment
					$has_comment = '';
					if(!empty($list_comments) && !empty($list_comments[$cur_stamp]) && !empty($list_comments[$cur_stamp]['am'])){
						$has_comment = 'has-comment';
					}
					$calendar .= "<div class='am {$has_comment} {$css_selectable} {$cur_stamp}-am workday' data-date = '{$cur_stamp}' data-type = 'am' dx = 0 dy = {$cur_stamp}><span></span></div>";
				}
			}
			if($holiday_pm){
				$calendar .= "<div class='pm {$css_cal_holiday} {$cur_stamp}-pm ' data-date = '{$cur_stamp}' data-type = 'pm' dx = 0 dy = {$cur_stamp}><span>{$holiday_text}</span></div>";
			}else{
				if(isset($abences[$cur_date]['absence_pm']) && !empty($abences[$cur_date]['response_pm'])){
					$pm_status = '';
					$has_comment = '';
					$pm_id = $abences[$cur_date]['absence_pm'];
					if($pm_id == 0) $sum_capacity += 0.5;
					if(!$timesheet_validated){
						$pm_status = 'rp-'.$abences[$cur_date]['response_pm'];
						$has_comment = 'has-comment';
					}
					$pm_text = (!empty($list_absences) && !empty($list_absences[$pm_id]['print'])) ?  $list_absences[$pm_id]['print'] : '';
					$calendar .= "<div class='pm {$has_comment} {$css_selectable} {$pm_id} select-pm {$cur_stamp}-pm {$pm_status}' title = '{$pm_text}'  data-date = '{$cur_stamp}' data-type = 'pm' dx = 0 dy = {$cur_stamp}><span>{$pm_text}</span></div>";
				}else{
					$has_comment = '';
					if(!empty($list_comments) && !empty($list_comments[$cur_stamp]) && !empty($list_comments[$cur_stamp]['pm'])){
						$has_comment = 'has-comment';
					}
					$calendar .= "<div class='pm {$has_comment} {$css_selectable} {$cur_stamp}-pm workday'  data-date = '{$cur_stamp}' data-type = 'pm' dx = 0 dy = {$cur_stamp}><span></span></div>";
					$sum_capacity += 0.5;
				}
			}
		}else if(!$weekend){
			
			if($holiday_am){
				$calendar .= "<div class='am {$css_cal_holiday} {$cur_stamp}-am ' data-date = '{$cur_stamp}' data-type = 'am' dx = 0 dy = {$cur_stamp}><span>{$holiday_text}</span></div>";
			}else{
				$am_comment = '';
				if(!empty($list_comments) && !empty($list_comments[$cur_stamp]) && !empty($list_comments[$cur_stamp]['am'])){
					$am_comment = 'has-comment';
				}
				$calendar .= "<div class='am {$am_comment} {$css_selectable} {$cur_stamp}-am workday' data-date = '{$cur_stamp}' data-type = 'am' dx = 0 dy = {$cur_stamp}><span></span></div>";
				$sum_capacity += 0.5;
			}
			if($holiday_pm){
				$calendar .= "<div class='pm {$css_cal_holiday} {$cur_stamp}-pm ' data-date = '{$cur_stamp}' data-type = 'pm' dx = 0 dy = {$cur_stamp}><span>{$holiday_text}</span></div>";
			}else{
				$pm_comment = '';
				if(!empty($list_comments) && !empty($list_comments[$cur_stamp]) && !empty($list_comments[$cur_stamp]['pm'])){
					$pm_comment = 'has-comment';
				}
				$calendar .= "<div class='pm {$pm_comment} {$css_selectable} {$cur_stamp}-pm workday'  data-date = '{$cur_stamp}' data-type = 'pm' dx = 0 dy = {$cur_stamp}><span></span></div>";
				$sum_capacity += 0.5;
			}
			
		}
		// Add the day number
		$calendar .= "<div class='{$css_cal_day_number}'>" . $day . "</div>";
		// Close day cell
		$calendar .= "</td>";

		// New row
		if ($running_day == 7) {
		  $calendar .= "</tr>";
		  if (($day + 1) <= $days_in_month) {
			$calendar .= "<tr class='{$css_cal_row}'>";
		  }
		  $running_day = 1;
		}

		// Increment the running day
		else {
		  $running_day++;
		}

	  } // for $day
	  // Finish the rest of the days in the week
	  if ($running_day != 1) {
		for ($x = $running_day; $x <= 7; $x++) {
		  $calendar .= "<td class='{$css_cal_day_blank}'> </td>";
		}
	  }

	  // Final row
	  $calendar .= "</tr>";

	  // End the table
	  $calendar .= '</table></div>';
	  $calendar_heading = "<div class='{$css_cal_container} {$type}'><div class= '{$css_cal_headding}'><div class='heading-capacity'><p class='capacity-text'>{$capacity_text}</p><p class='capacity-sum'>{$sum_capacity}</p></div><div class='heading-date'><p class='heading-year'>{$year}</p><p class='heading-month'>{$text_month}</p></div></div>";
	  // All done, return result
	  return $calendar_heading . $calendar;
}
?>

<div id="wd-container-main" class="wd-project-admin">
        <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
                <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
                    <div class="wd-list-project">
                        
                        <div id="message-place">
                        <?php
						$getDataByPath = $getDataByPath ? 1 : 0;
                        echo $this->Session->flash();
                        $am = __('AM', true);
                        $pm = __('PM', true);
                        $avg = intval(($_start + $_end) / 2);
                        ?>
                        </div>
						
                        <div class="wd-table" id="project_container">
                            <div id="absence-container">
                                <div id="table-control" class="wd-activity-actions" style="clear: both;">
								<div class="wd-avatar">
									<div class="circle-larger-avatar">
										<img src="<?php echo $this->UserFile->avatar($employeeName['id'], 'avatar'); ?>" alt="avatar" title="<?php echo $employeeName['first_name'] . ' '. $employeeName['last_name']; ?>">
									</div>
									 
								</div>
                                <?php
                                echo $this->Form->create('Control', array(
                                    'type' => 'get',
                                    'url' => '/' . Router::normalize($this->here)));
                                ?>
									<h2 class="wd-t1"><i class="icon-user"></i><?php echo sprintf(__("%s", true), $employeeName['first_name'] . ' ' . $employeeName['last_name']); ?></h2>
                                    <fieldset>
                                        <select style="padding:6px;" name="typeRequest" id="typeRequest">
                                            <option value="week" <?php echo $typeSelect=='week'?'selected':'';?>><?php echo __('Week',true);?></option>
                                            <option value="month" <?php echo $typeSelect=='month'?'selected':'';?>><?php echo __('Month',true);?></option>
                                            <option value="year" <?php echo $typeSelect=='year'?'selected':'';?>><?php echo __('Year',true);?></option>
                                        </select>
                                    <?php
                                        echo $this->element('week_activity');
                                        if($isManage){
        								    echo $this->Form->hidden('id', array('value' => $this->params['url']['id']));
                                            echo $this->Form->hidden('profit', array('value' => $this->params['url']['profit']));
        								}
                                    ?>
                                        <span <?php if($typeSelect=='year'){ ?> style="display:none;"<?php }?>>
                                        <?php
										echo $this->Form->hidden('get_path', array('value' => $getDataByPath));
                                        ?>
                                        </span>
                                        <a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn btn-fullscreen"><?php echo $svg_icons['expand'];?></a>
                                    <?php if (empty($requestMessage) && !$isManage) : ?>
                                        <a href="javascript:void(0)" id="submit-request-ok-top" class="btn btn-email" title="<?php __('Send request message')?>"><i class="icon-rocket"></i></a>
                                    <?php endif; ?>
                                        <a href="javascript:;" id="open-menu" class="btn btn-menu">
											<?php echo $svg_icons['add'];?>
										</a>
                                    </fieldset>
                                <?php
                                echo $this->Form->end();
                                ?>
                                </div>
								<?php 
									$month = date('m', $_start);
									$week = date('W', $_start);
									$year = date('Y', $_start);
									
									if($typeSelect == 'year'){ ?>
										<div class="wd-calendar-container">
											<?php for($i = 1; $i <= 12; $i++){
												echo build_html_calendar($this, $year, $i, $requests, $typeSelect);
											}?>
										</div>
									<?php } else if($typeSelect == 'month'){
										echo build_html_calendar($this, $year, $month, $requests, $typeSelect);
									// }else{
										// echo build_html_calendar_week($this, $year, $month, $week, $requests, $listWorkingDays, $typeSelect);
									// }
									// if($typeSelect == 'month' || $typeSelect == 'year'){
										// echo build_html_calendar($this, $year, $month, $requests, $typeSelect);
									}else{
								?>
                                <div id="absence-wrapper">
                                    <table id="absence-fixed">
                                        <thead>
                                            <tr>
                                                <th><?php __('Capacity'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="absence-table-fixed">
                                        </tbody>
                                    </table>
                                    <div id="absence-scroll">
                                        <table id="absence">
                                            <thead>
                                            <?php
                                                $trTop = $trBot = '';
                                                if(!empty($listWorkingDays)){
                                                    foreach($listWorkingDays as $key => $val){
                                                        $_top = __(date('l', $val), true) . __(date(' d ', $val), true) . __(date('M', $val), true);
                                                        $trTop .= '<th colspan="2" style="width: 10%;">' . $_top . '</th>';
                                                        $trBot .= '<th>' . $am . '</th><th>' . $pm . '</th>';
                                                    }
                                                }
                                            ?>
                                                <tr>
                                                <?php echo $trTop;?>
                                                </tr>
                                                <tr>
                                                <?php echo $trBot;?>
                                                </tr>
                                            </thead>
                                            <tbody id="absence-table">
                                                <tr><td colspan="15">&nbsp;</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
									<?php } if (empty($requestMessage) && !$isManage) : ?>
                                <?php
                                echo $this->Form->create('Request', array(
                                    'escape' => false, 'id' => 'request-form', 'type' => 'post',
                                    'url' => array('controller' => 'absence_requests', 'action' => 'index', '?' => array('week' => date('W', $avg), 'year' => date('Y', $avg)))));
                                echo $this->Form->hidden('id.' . $employeeName['id'], array('value' => 1));
                                echo $this->Form->end();
                                ?>
                               
                                <script type="text/javascript">
                                    (function ($) {

                                        $(function () {
                                            var openDialog = function (callback) {
                                                var $dialog = $('#add-comment-dialog2');
                                                $dialog.dialog({
                                                    zIndex: 10000,
                                                    modal: true,
                                                    minHeight: 50,
                                                    close: function () {
                                                        $dialog.dialog('destroy');
                                                    }
                                                });
                                                $dialog.find('a.ok').unbind().click(function () {
                                                    callback.call(this);
                                                });
                                                $dialog.find('a.cancel').unbind().click(function () {
                                                    $dialog.dialog('close');
                                                    return false;
                                                });
                                            };
                                            $('#submit-request-ok, #submit-request-ok-top').click(function () {
                                                if (!$(this).hasClass('cant-submit')) {
                                                    return;
                                                }
                                                openDialog(function () {
                                                    $('#request-form').submit();
                                                });
                                            });
                                        });

                                    })(jQuery);
                                </script>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div></div></div>
        </div>
    </div>
</div>
    <?php
    $dataView = array();
    foreach ($listWorkingDays as $day => $time) {
//          switch (date('l',$time*1000)) {
//                  case 'Sunday':
//                    $day = "sunday";
//                    break;
//                  case 'Monday':
//                    $day = "monday";
//                    break;
//                  case 'Tuesday':
//                    $day = "tuesday";
//                    break;
//                  case 'Wednesday':
//                    $day = "wednesday";
//                    break;
//                  case 'Thursday':
//                    $day = "thursday";
//                    break;
//                  case 'Friday':
//                    $day = "friday";
//                    break;
//                  case 'Saturday':
//                    $day = "saturday";
//                    break;
//            }
        $default = array(
            'date' => $time,
            'absence_am' => 0,
            'absence_pm' => 0,
            'response_am' => 0,
            'response_pm' => 0,
            'employee_id' => $employeeName['id'],
            'day' => date('l',$time)
        );
        if (isset($requests[$time])) {
            unset($requests[$time]['date'], $requests[$time]['employee_id']);
            $default = array_merge($default, array_filter($requests[$time]));
            if (!empty($default['history'])) {
                $default['history'] = unserialize($default['history']);
            }
        }
        $_dataView[$day] = $default;
    }
    $dataView[] = $_dataView;
    $css = '';
    $ctClass = array();
    foreach ($constraint as $key => $data) {
        $ctClass[] = "rp-$key";
        $css .= ".rp-$key span {background-color : {$data['color']};}";
		if($typeSelect == 'month' || $typeSelect == 'year'){
			$css .= ".rp-$key{background-color : {$data['color']};}";
		}
    }
    $ctClass = implode(' ', $ctClass);
    $i18ns = array(
        'Add a comment' => __('Add a comment', true),
        'Remove request' => __('Remove request', true),
        'Holiday' => __('Holiday', true),
        'Date requesting' => __('Date requesting', true),
        'Date validate' => __('Date validate', true),
        'Date reject' => __('Date reject', true),
    );
    echo '<style type="text/css">' . $css . '</style>';
    $queryUpdate = '?week=' . date('W', $_end) . '&year=' . date('Y', $_end);
    if ($isManage) {
        $queryUpdate .= '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
    }
    ?>
<div style="display: none;" id="message-template">
    <div class="message error"><?php echo __('Cannot connect to server ...', true); ?><a href="#" class="close">x</a></div>
</div>

<!-- dialog_vision_portfolio -->
<div id="add-comment-dialog" class="ab-dialog-comment buttons" style="display: none;" title="<?php echo __('Add new comments', true) ?>">
    <fieldset>
        <textarea rel="no-history" name="comment"></textarea>
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons">
        <li><a href="javascript:void(0)" class="cancel btn-form-action btn-cancel"><?php echo __('Cancel', true) ?></a></li>
        <li><a href="javascript:void(0)" class="ok btn-form-action btn-save"><?php echo __('Send', true) ?></a></li>
    </ul>
</div>
<div id="add-comment-dialog2" class="ab-dialog-comment buttons" style="display: none;" title="<?php __('Confirm'); ?>">
    <div class="dialog-request-message">
            <?php __('Your are sure to send you requested? Once sent your could not re sent it'); ?>
    </div>
    <ul class="type_buttons">
       <li><a href="javascript:void(0)" class="cancel btn-form-action btn-cancel"><?php echo __('Cancel', true) ?></a></li>
       <li><a href="javascript:void(0)" class="ok btn-form-action btn-save"><?php echo __('Send', true) ?></a></li>
    </ul>
</div>
<!-- dialog_vision_portfolio.end -->
<!-- document_popup -->
<div id="document_popup" style="display:none;" class="buttons">
        <?php
        echo $this->Form->create('Upload', array('id' => 'uploadForm', 'type' => 'file',
            'url' => array('controller' => 'absence_requests', 'action' => 'update_document')
        ));
        ?>
    <div class="wd-input" style="padding-left: 40px;">
        <p class="mes_absence_atch"><?php echo !empty($absenceAttachments['message']) ? $absenceAttachments['message'] : '';?></p>
            <?php if( isset($absenceAttachments['document_mandatory']) && $absenceAttachments['document_mandatory'] ): ?>
        <p style="color: red; font-size: 12px; margin-bottom: 10px"><?php echo __('Document is required.', true) ?></p>
            <?php else: ?>
        <p style="color: green; font-size: 12px; margin-bottom: 10px"><?php echo __('Document is optional.', true) ?></p>
            <?php endif?>
        <p style="font-size: 12px; color: blue; margin-bottom: 10px"><?php echo sprintf(__('Allowed file types: <span id="file-types">%s</span>', true), $allowedFiles) ?></p>
        <ul id="ch_group_infor_popup_1">
            <li><input type="file" id="textDocument" name="FileField[attachment]" style="margin-left: 0px;font-size: 13px;"/></li>
            <li id="error">&nbsp;</li>
        </ul>
    </div>
    <ul class="type_buttons" style="padding-right: 25px !important;">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Document') ?></a></li>
        <li><a id="document_popup_submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
    </ul>
        <?php echo $this->Form->end(); ?>
</div>
<!-- End document_popup -->

<script type="text/javascript">
	window.onscroll = function() {tableHeaderFixed()};
	function tableHeaderFixed() {
		var cale_header = $('.wd-calendar-container .calendar-container.year:first').find('.absence-heading');
		var header_fixed = $('.wd-calendar-container .calendar-container.year:first').find('.calendar-header');
		if(header_fixed.length > 0){
			  if (document.body.scrollTop >= cale_header.offset().top || document.documentElement.scrollTop >= cale_header.offset().top) {
					offset_top = document.documentElement.scrollTop - cale_header.offset().top;
					header_fixed.css('transform', 'translateY('+ Math.round(offset_top) +'px)');
			  } else {
					header_fixed.css('transform', 'translateY(0)');
			  }
		}
	}
    (function ($) {
        $(function () {
            function checkRequest() {
                var $elTop = $('#submit-request-ok-top');
                if (!$elTop.length) {
                    return;
                }
                if ($('#absence .rp-waiting').length) {
                    $elTop.addClass('cant-submit').css('opacity', 1);
                } else {
                    $elTop.removeClass('cant-submit').css('opacity', 0.5);
                }
            }
            var updateUrl = <?php echo json_encode($this->Html->url(array('action' => 'update')) . $queryUpdate); ?>,
                    updateUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_update'))); ?>,
                    deleteUrl2 = <?php echo json_encode($this->Html->url(array('action' => 'comment_delete'))); ?>,
                    dataSets = <?php echo json_encode($dataView); ?>,
                    comments = <?php echo json_encode(@$comments); ?> || {},
                    holidays = <?php echo json_encode(@$holidays); ?> || {},
                    absences = <?php echo json_encode($absences); ?>,
                    employees = <?php echo json_encode($employees); ?>,
                    workdays = <?php echo json_encode($workdays); ?>,
                    ctClass = <?php echo json_encode($ctClass); ?>,
                    employeeName = <?php echo json_encode($employeeName); ?>,
                    employee_id = <?php echo json_encode($employee_id); ?>,
                    dayHasValidations = <?php echo json_encode($dayHasValidations);?>,
                    typeSelect = <?php echo json_encode($typeSelect);?>,
                    listBeginOfPeriods = <?php echo json_encode($listBeginOfPeriods);?>,
                    absenceAttachments = <?php echo json_encode($absenceAttachments);?>,
                    daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                    $container = (typeSelect == 'month') ? $('.absence-calendar') : ((typeSelect == 'year') ? $('.wd-calendar-container') : $('#absence-table').html('')),
					eleSelectable = (typeSelect == 'month' || typeSelect == 'year') ? '.absence_selectable' : 'td.selectable',
                    $containerFixed = $('#absence-table-fixed').html(''),
                    _currAbsences = {};

            /**
             * Create dialog document for absence request.
             */
            var createDialogTwo = function () {
                $('#document_popup').dialog({
                    position: 'center',
                    autoOpen: false,
                    closeOnEscape: false,
                    autoHeight: true,
                    modal: true,
                    width: 460,
                    height: 220,
                    open: function (e) {
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialogTwo = $.noop;
            }
            createDialogTwo();
            /**
             * Group element when select one absence
             */
            var ABAT_elements = {}, ABAT_datas = {}, ABAT_imenus = {}, ABAT_cmenus = {}, ABAT_e = {};
            var ABAT_dataSends = [];
            var ABAT_absenceId = '';
            /**
             * Handle and checking absence before save request
             */
            var handlerAbsenceRequestValidDocumentAtch = function (_this, data, imenu, cmenu, e) {
                ABAT_elements = _this;
                ABAT_datas = data;
                ABAT_imenus = imenu;
                ABAT_cmenus = cmenu;
                ABAT_e = e;
                $list = $(_this).find('td.ui-selected');
                ABAT_dataSends = [];
                $list.each(function () {
                    var $el = $(this);
                    var ABAT_getTime = $el.attr('dy');
                    var ABAT_getType = $el.attr('class').split(' ')[0];
                    ABAT_dataSends.push(ABAT_getTime + '-' + ABAT_getType);
                });
                ABAT_dataSends = ABAT_dataSends.toString();
                ABAT_absenceId = data.id;
                $("#document_popup").dialog('option', {title: 'Document'}).dialog('open');
            }
            $('#uploadForm').submit(function (e) {
                if (window.FormData !== undefined) {
                    var formData = new FormData($(this)[0]);
                    var formURL = $(this).attr("action");
                    absenceHandler.call(ABAT_elements, ABAT_datas, ABAT_imenus, ABAT_cmenus, ABAT_e);
                    setTimeout(function () {
                        $.ajax({
                            url: formURL + '/' + employeeName.company_id + '/' + employee_id + '/' + ABAT_absenceId + '/' + ABAT_dataSends,
                            type: 'POST',
                            data: formData,
                            mimeType: "multipart/form-data",
                            async: false,
                            cache: false,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function (data) {

                            }
                        });
                    }, 100);
                } else {
                    //upload file qua iframe ko ho tro tren ie8
                    var formObj = $(this);
                    absenceHandler.call(ABAT_elements, ABAT_datas, ABAT_imenus, ABAT_cmenus, ABAT_e);
                    var action = formObj.attr('action');
                    //dung ajax form
                    formObj.ajaxSubmit({
                        url: action + '/' + employeeName.company_id + '/' + employee_id + '/' + ABAT_absenceId + '/' + ABAT_dataSends,
                        type: 'POST',
                        success: function () {
                        }
                    });
                }
                $("#document_popup").dialog("close");
                $('#textDocument').val('');
                $('#error').html('');
                e.preventDefault(); //Prevent Default action.
                return false;
            });
            /**
             * Handle popup when request absence document
             */
            var documentRequired = <?php echo isset($absenceAttachments['document_mandatory']) ? $absenceAttachments['document_mandatory'] : 0 ?>;
            $("#document_popup_submit").click(function () {
                var $file = $('#textDocument').val();
                if (documentRequired) {
                    if ($file == '') {
                        $('#error').html('<?php echo __('Please select attachment!',true);?>');
                        return false;
                    }
                }
                if ($file) {
                    var ext = $file.split('.').pop();
                    var types = $('#file-types').text().split(',');
                    var valid = false;
                    for (var i = 0; i < types.length; i++) {
                        if (types[i] == ext) {
                            valid = true;
                            break;
                        }
                    }
                    if (!valid) {
                        $('#error').html('<?php echo __('This type of file is not allowed!',true);?>');
                        return false;
                    }
                }
                $("#uploadForm").submit(); //Submit the form
            });
            $(".cancel").click(function () {
                // if(absenceAttachments.document_mandatory == 1){
                // 	var $file = $('#textDocument').val();
                // 	if($file == ''){
                // 		$('#error').html('<?php echo __('The document is mandatory!',true);?>');
                // 		return false;
                // 	} else {
                // 		$("#document_popup").dialog("close");
                // 	}
                // 	return false;
                // }
                $('#textDocument').val('');
                $('#error').html('');
                $("#document_popup").dialog("close");
            });
            /**
             * Translate strings to the page language or a given language.
             */
            var i18ns = <?php echo json_encode($i18ns); ?>;
            var format = function (str, args) {
                var regex = /%(\d+\$)?(s)/g,
                        i = 0;
                return str.replace(regex, function (substring, valueIndex, type) {
                    var value = valueIndex ? args[valueIndex.slice(0, -1) - 1] : args[i++];
                    switch (type) {
                        case 's':
                            return String(value);
                        default:
                            return substring;
                    }
                });
            };
            var t = function (str, args) {

                if (i18ns[str]) {
                    str = i18ns[str];
                }
                if (args === undefined) {
                    return str;
                }
                if (!$.isArray(args)) {
                    args = $.makeArray(arguments);
                    args.shift();
                }
                return format(str, args);
            };
            var parseHandler = function (callback, $list, data) {
                $('#message-place').html(data.message);
                setTimeout(function () {
                    $('#message-place .message').fadeOut('slow');
                }, 5000);
                callback($list, data);
            };
            var syncHandler = function (args, dsubmit, callback, check) {
                var submit = {}, $list = $(this).find('.ui-selected');
                $list.each(function () {
                    var $el = $(this);					
					var _ds = dataSets[$el.attr('dx')][$el.attr('dy')];
                    if (!_ds || $el.hasClass('loading') || ($.isFunction(check) && check($el) === false)) {
                        return;
                    }
                    if (!submit[_ds.date]) {
                        submit[_ds.date] = {
                            date: _ds.date,
                            employee_id: _ds.employee_id
                        };
                    }
                    submit[_ds.date][$el.hasClass('am') ? 'am' : 'pm'] = args.value;
                    $el.addClass('loading');
                });
                args.url = args.url + '&get_path=<?php echo $getDataByPath; ?>';
                if (!$.isEmptyObject(submit)) {
                    $.ajax({
                        url: args.url,
                        cache: false,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            data: $.extend(dsubmit, submit)
                        },
                        success: function (data) {
                            parseHandler(callback, $list, data);
							if(data){
								$.each(data, function (ind, val) {
									if(val){
										capacity_request = 0;
										cur_cell = '';
										if(typeof val['absence_am'] != 'undefined'){
											if(val['absence_am'] == 0){
												capacity_request += 0.5;
											}else{
												capacity_request -= 0.5;
											}
											cur_cell = val['date'] + '-am';
										}
										if(typeof val['absence_pm'] != 'undefined'){
											if(val['absence_pm'] == 0){
												capacity_request += 0.5;
											}else{
												capacity_request -= 0.5;
											}
											cur_cell = val['date'] + '-pm';
										}
										if(capacity_request != 0){
											capacity_heading = $('.' + cur_cell).closest('.calendar-container').find('.capacity-sum');
											if(capacity_heading.length > 0){
												capacity_heading.html(parseFloat(capacity_heading.html()) + capacity_request);
											}
										}
									}
									
								});
								
								
							}
							$('.ui-selectee').removeClass('ui-selected');
                        },
                        error: function () {
                            parseHandler(callback, $list, {
                                error: true,
                                message: $('#message-template').html()
                            });
                        }
                    });
                }
            };
            var checkHistory = function (type, dx) {
                return !!(dx && (dx['rq_' + type] || dx['rv_' + type] || dx['rj_' + type]));
            };
            /* --------Custom--------- */
            var absenceHandler = function (save) {
				
                syncHandler.call(this, {value: true, url: updateUrl}, {
                    request: save.id
                }, function ($list, data) {
                    $list.each(function () {
                        var $ct, ab, $el = $(this), _ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                                res = data[_ds.date], type = $el.hasClass('am') ? 'am' : 'pm';
						
                        if (res && res.result) {
                            _ds = $.extend(_ds, data[_ds.date] || {});
                            ab = absences[_ds['absence_' + type]];
                            $el.removeClass(ctClass);
                            $ct = $('#absence-table-fixed').find('.ct span');
                            if (save.id != '0') {
                                switch ($el.find('span').html()) {
                                    case '0.5' :
                                        $ct.html(parseFloat($ct.html()) - 0.5);
                                        break;
                                    default:
                                        if (!ab) {
                                            $ct.html(parseFloat($ct.html()) + 0.5);
                                            $el.addClass('workday').find('span').html('0.5');
                                        }
                                }
                                if (ab) {
                                    $el.find('span').html(ab.print);
                                    if (ab.id == '-1') {
                                        $el.addClass('rp-forecast');
                                    } else {
                                        if (data.auto_validate)
                                            $el.addClass('rp-validated ');
                                        else
                                            $el.addClass('rp-waiting');
                                    }
                                }
                            } else {
                                $ct.html(parseFloat($ct.html()) + 0.5);
                                $el.addClass('workday').find('span').html('0.5');
                            }
                        }
						
                        if (res && checkHistory(type, res.history)) {
                            $el.addClass('has-comment');
                        } else if (save.id == 0) {
                            var $widget = $el.tooltip('widget').find('ul.list-comment');
                            if ($widget.length) {
                                $widget.find('li.info').remove();
                                if ($widget.children().length == 0) {
                                    $el.removeClass('has-comment');
                                    $el.tooltip('close');
                                    $el.tooltip('disable');
                                }
                            }
                        }
                        $el.removeClass('loading');
                    });
                    checkRequest();
                }, function ($el) {
                    var _result = true;
                    if ($el.hasClass('workday')) { // workday and request - waiting
                        if (save.id != '0' || !$.isNumeric($el.find('span').html())) {
                            // true
                        } else {
                            _result = false;
                        }
                    } else {
                        if (save.id == '0') {
                            // true
                        } else {
                            _result = false;
                        }
                    }
                    return _result;
                });
				
            };
            /* --------Check day has activity request--------- */
            var checkHasAcRequest = function (day, listDays) {
                var result = false;
                if (listDays) {
                    $.each(listDays, function (ind, val) {
                        if (parseInt(day) === parseInt(val)) {
                            result = true;
                            return result;
                        }
                    });
                }
                return result;
            };
            /* --------Draw table--------- */
            $.each(dataSets, function (i) {
                var output = '', total = 0;
                $.each(this, function (day, data) {
                    var _day = day * 1000;
//                    _day = new Date(_day);
//                    _day = daysInWeek[_day.getDay()];
                    var _day = get_day(data.day);
//                    console.log(_day, get_day(data.day));
                    var val = parseFloat(workdays[_day]), dt = holidays[data.date] || {},
                            opt = {am: {className: ['am', day], value: '0'}, pm: {className: ['pm', day], value: '0'}};
                    switch (val) {
                        case 1:
                            if (!dt['am']) {
                                if (checkHasAcRequest(day, dayHasValidations)) {
                                    opt['am'].className.push('ch-absen-validation');
                                } else {
                                    opt['am'].className.push('selectable');
                                }
                            } else {
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                            if (!dt['pm']) {
                                if (checkHasAcRequest(day, dayHasValidations)) {
                                    opt['pm'].className.push('ch-absen-validation');
                                } else {
                                    opt['pm'].className.push('selectable');
                                }
                            } else {
                                opt['pm'].className.push('rp-holiday');
                                opt['pm'].value = t('Holiday');
                            }
                            break;
                        case 0.5:
                            if (!dt['am']) {
                                if (checkHasAcRequest(day, dayHasValidations)) {
                                    opt['am'].className.push('ch-absen-validation');
                                } else {
                                    opt['am'].className.push('selectable');
                                }
                            } else {
                                opt['am'].className.push('rp-holiday');
                                opt['am'].value = t('Holiday');
                            }
                    }

                    $.each(['am', 'pm'], function () {
                        try {
                            if (checkHistory(this, data.history) || comments[data.employee_id][data.date][this]) {
                                opt[this].className.push('has-comment');
                            }
                        } catch (ex) {
                        }
                        ;
                        if (data['absence_' + this]) {
                            opt[this].value = (absences[data['absence_' + this]] || {}).print || t('Hidden');
                            opt[this].className.push(data['absence_' + this]);
                            if (data['response_' + this] != 'validated' || !data['response_' + this]) {
                                opt[this].className.push('workday');
                            }
                        } else {
                            val = parseFloat(workdays[_day]);
                            switch (true) {
                                case val == 0.5 && this == 'am' && !dt['am'] :
                                    total += 0.5;
                                    opt['am'].className.push('workday');
                                    opt['am'].value = 0.5;
                                    break;
                                case val == 1 && !dt[this]:
                                    total += 0.5;
                                    opt[this].className.push('workday');
                                    opt[this].value = 0.5;
                                    break;
                            }
                        }
                        if (data['response_' + this]) {
                            opt[this].className.push('rp-' + data['response_' + this]);
                        }
                        opt[this].className.push(_day + '_' + this);
                    });
                    $.each(opt, function () {
                        output += '<td dx="' + i + '" dy="' + day + '" class="' + this.className.join(' ') + '"><span>' + this.value + '</span></td>';
                    });
                });
                $containerFixed.append('<tr><td class="ct"><span>' + total + '</span></td></tr>');
                if(typeSelect == 'week') $container.append('<tr>' + output + '</tr>');
            });
            var contextMenu = {hide: $.noop};
            $container.selectable({
                filter: eleSelectable,
                unselected: function () {
                    contextMenu.hide();
					
                },
				stop: function( event, ui ) {
					var _last = $(this).find('.ui-selectee.ui-selected').last();
					// console.log(_last);
					_last.trigger('contextmenu');
				},
                selected: function (undefined, u) {
                    removeTooltip(u.selected);
                }
            });
            /* Touch support */
            var _touchitem = {};
            var _touchtime = 0;
            $(eleSelectable).on('touchstart', function(e){
                console.log( e);
                _touchitem = $(e.target);
                _touchtime = e.timeStamp;
                if ( !_touchitem.hasClass('ui-selectee')) {
                   _touchitem = {};
                }
            });
            $(eleSelectable).on('touchend', function(e){
                if ( $(e.target).is(_touchitem)){
                    if(e.timeStamp - _touchtime < 300){
                        // select this item
                        _touchitem.toggleClass('ui-selected');
                        //clear touch item
                        _touchitem = {};
                        $container.selectable( "disable" );
                        setTimeout(function(){$container.selectable( "enable" );}, 5);
                        contextMenu.hide();
                    }
                }else{
                    _touchitem.addClass('ui-selected');
                }
            });
            
            /* End Touch support */
            var absenceHistory = function ($el, type, data) {
                var $list = $(this).find('ul.list-comment');
                var $info = $list.find('.info-comment').html('');
                if (!$info.length) {
                    $info = $('<div class="comment info-comment"></div>');
                    var $del = $('<a class="close" title="' + t('Close') + '">x</a>').click(function () {
                        removeTooltip(0);
                        if ($list.children().length == 1) {
                            $.each(data, function (i) {
                                //delete data[i];
                            });
                            //$el.removeClass('has-comment');
                            $el.tooltip('close');
                            //$el.tooltip('disable');
                        }
                        return false;
                    });
                    $list.prepend($(t('<li class="info"><h4 class="title">%s</h4></li>', t('Absence information'))).append($del).append($info));
                }
                if (data['rq_' + type]) {
                    $info.append('<span>' + t('Date requesting') + ': ' + String(data['rq_' + type]) + '</span>');
                }
                if (data['rv_' + type]) {
                    $info.append('<span>' + t('Date validate') + ': ' + String(data['rv_' + type]) + '</span>');
                }
                if (data['rj_' + type]) {
                    $info.append('<span>' + t('Date reject') + ': ' + String(data['rj_' + type]) + '</span>');
                }
            };
            /* --------Comment--------- */
            var removeComment = function ($el, id) {
                $.ajax({
                    url: deleteUrl2,
                    cache: false,
                    type: 'GET',
                    data: {
                        id: id
                    }
                });
                if (this.siblings().length == 0) {
                    $el.removeClass('has-comment');
                    $el.tooltip('close');
                    $el.tooltip('disable');
                }
                this.remove();
            };
            var initComment = function () {
                var $el = $(this), $widget = $el.tooltip('widget');
                if ($widget.is($el)) {
                    $el.tooltip({
                        width: 300,
                        maxHeight: 150,
                        hold: 1000,
                        openEvent: 'mouseenter',
                        closeEvent: 'xmouseleave',
                        content: '<ul class="list-comment" />',
                        open: function () {
                            $el.addClass('comment-open');
                            removeTooltip($el.get(0));
                        }
                    });
                    $widget = $el.tooltip('widget').click(function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                    });
                }
                var ds = dataSets[$el.attr('dx')][$el.attr('dy')], $list = $widget.find('ul');
                var type = $el.hasClass('am') ? 'am' : 'pm';
                try {
                    $.each(comments[ds.employee_id][ds.date][type], function (i, v) {
                        if (v.user_id == employee_id) {
                            var del = $('<a href="javascript:void(0);" class="close delete" title="' + t('Delete this comment, you can\'t undo it.') + '"><i class="icon-trash"></i></a>').click(function () {
                                removeComment.call($(this).parent(), $el, i);
                            });
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span> </h4><div class="comment">%s</div></li>', t('You'), v.created, v.text)).append(del));
                        } else {
                            $list.append($(t('<li><h4 class="title">%s <span class="date">(%s)</span> </h4><div class="comment">%s</div></li>', employees[v.user_id], v.created, v.text)).append(del));
                        }
                        delete comments[ds.employee_id][ds.date][type][i];
                    });
                } catch (ex) {
                }
                ;
                $el.tooltip('enable');
                checkHistory(type, ds.history) && absenceHistory.call($widget, $el, type, ds.history);
                return $widget;
            };
			
            var syncHandler2 = function (args, dsubmit, callback, check) {
                var submit = {}, $list = $(this).find('.ui-selected');
                $list.each(function () {
                    var $el = $(this), _ds = dataSets[$el.attr('dx')][$el.attr('dy')];
                    if (!_ds || $el.hasClass('loading') || ($.isFunction(check) && check($el) === false)) {
                        return;
                    }
                    if (!submit[_ds.employee_id]) {
                        submit[_ds.employee_id] = {};
                    }
                    if (!submit[_ds.employee_id][_ds.date]) {
                        submit[_ds.employee_id][_ds.date] = {
                            date: _ds.date,
                            employee_id: _ds.employee_id
                        };
                    }
                    submit[_ds.employee_id][_ds.date][$el.hasClass('am') ? 'am' : 'pm'] = args.value;
                    $el.addClass('loading');
                });
                if (!$.isEmptyObject(submit)) {
                    $.ajax({
                        url: args.url,
                        cache: false,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            data: $.extend(dsubmit, submit)
                        },
                        success: function (data) {
                            parseHandler(callback, $list, data);
                        },
                        error: function () {
                            parseHandler(callback, $list, {
                                error: true,
                                message: $('#message-template').html()
                            });
                        }
                    });
                }
            };
            var commentHandler = function (data) {
                syncHandler2.call(this, {value: data, url: updateUrl2}, {}, function ($list, data) {
                    $list.each(function () {
                        var $el = $(this), _ds = dataSets[$el.attr('dx')][$el.attr('dy')],
                                type = $el.hasClass('am') ? 'am' : 'pm';
                        if (data[_ds.employee_id]) {
                            var res = data[_ds.employee_id][_ds.date];
                            if (res.result) {
                                if (!comments[_ds.employee_id]) {
                                    comments[_ds.employee_id] = {};
                                }
                                if (!comments[_ds.employee_id][_ds.date]) {
                                    comments[_ds.employee_id][_ds.date] = {};
                                }
                                if (!comments[_ds.employee_id][_ds.date][type]) {
                                    comments[_ds.employee_id][_ds.date][type] = {};
                                }
                                comments[_ds.employee_id][_ds.date][type][res['id_' + type]] = {
                                    text: res[type],
                                    employee_id: _ds.employee_id,
                                    user_id: employee_id,
                                    created: res.created
                                };
                                $el.addClass('has-comment');
                                initComment.call($el.get(0));
                            }
                        }
                        $el.removeClass('loading');
                    });
                });
            };
            var removeTooltip = function (self) {
                $('#absence-table .comment-open').not(self).each(function () {
                    $(this).removeClass('comment-open').tooltip('close');
                });
                $('.absence-calendar .comment-open').not(self).each(function () {
                    $(this).removeClass('comment-open').tooltip('close');
                });
            };

            $(document).on("mouseenter", "#absence-table .has-comment", function (e) {
                var $widget = initComment.call(this);
                if ($widget.is(':hidden')) {
                    $(this).trigger('mouseenter', e);
                }
            });
			
			$(document).on("mouseenter", ".absence-calendar .has-comment", function (e) {
				var $widget = initComment.call(this);
				if ($widget.is(':hidden')) {
					$(this).trigger('mouseenter', e);
				}
			});
		
            $(document).on("mouseleave", "#absence-table .has-comment", function (e) {
                $(this).tooltip('clear');
            });
            $(document).on("mouseleave", ".absence-calendar .has-comment", function (e) {
                $(this).tooltip('clear');
            });
            $(document).click(function (e) {
                removeTooltip($(e.target).closest(eleSelectable).get(0));
            });
            /* -------------------------------------- */
            checkRequest();
            (function () {
                /*--------------------------- HuuPC add new -------------------------*/
                var initMenuFilter = function ($menu) {

                    if ($menu.prev('.context-menu-filter').length || $menu.children('.context-menu-item').length <= 10) {
                        return;
                    }

                    var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');
                    $menu.before($filter);

                    var timeoutID = null, searchHandler = function () {
                        var val = $(this).val();
                        $menu.children('.context-menu-item').each(function () {
                            var $label = $(this);
                            if (!val.length || $label.text().toLowerCase().indexOf(val.toLowerCase()) != -1) {
                                $label.removeClass('notmatch');
                            } else {
                                $label.addClass('notmatch');
                            }
                        });
                    };

                    $filter.find('input').click(function (e) {
                        e.stopImmediatePropagation();
                    }).keyup(function () {
                        var self = this;
                        clearTimeout(timeoutID);
                        timeoutID = setTimeout(function () {
                            searchHandler.call(self);
                        }, 200);
                    });

                };
                /*----------------------------End -----------------------------------*/
                var menu = [{}];
                menu[0][t('Add a comment')] = {
                    onclick: function (imenu, cmenu, e) {
                        var $dialog = $('#add-comment-dialog'), self = this;
                        $dialog.dialog({
                            zIndex: 10000,
							width: 520,
							height: 320,
                            modal: true,
                            close: function () {
                                $dialog.dialog('destroy');
                            }
                        });
                        $dialog.find('textarea').val('');
                        $dialog.find('a.ok').unbind().click(function () {
                            var val = $dialog.find('textarea').val();
							console.log(val);
                            if (val) {
                                commentHandler.call(self, val);
                                $dialog.dialog('close');
                            } else {
                                $dialog.find('textarea').focus();
                            }
                            return false;
                        });
                        $dialog.find('a.cancel').unbind().click(function () {
                            $dialog.dialog('close');
                            return false;
                        });
                    },
                    className: 'add-comment', disabled: false
                };
                var requestConfirm = false;
                //if(!requestConfirm){
                var _absences = Absences();
                _currAbsences = _absences;
                // _absences = $.extend( {0 : {
                //         id : 0,
                //         print : t('Remove request')
                //     }}, _absences);
                _absences.unshift({
                    id: 0,
                    print: t('Remove request')
                });
                $.each(_absences, function (undefined, data) {
                    if (Number(this.id) > 0 && !Number(this.activated)) {
                        return;
                    }
                    var opt = {};
                    var _data = data.print;
                    var _rq = (data.request) ? data.request : 0;
                    var _vl = (data.total) ? data.total : '';
                    var _view;
                    if (!_vl) {
                        if (!_rq) {
                            _view = '';
                        } else {
                            _view = '  (' + _rq + '/' + 'NA' + ')';
                        }
                    } else {
                        if (!_rq) {
                            _view = '  (' + 0 + '/' + _vl + ')';
                        } else {
                            _view = '  (' + _rq + '/' + _vl + ')';
                        }
                    }
                    _data = _data + _view;
                    opt[_data] = {
                        onclick: function (imenu, cmenu, e) {
                            if ($(imenu).attr("id") == 'wd-disabled') {
                                return false;
                            }
                            /**
                             * Check Absence have document or not.
                             */
                            if (data.document == '1') { // absence need document
                                handlerAbsenceRequestValidDocumentAtch(this, data, imenu, cmenu, e);
                            } else {
                                absenceHandler.call(this, data, imenu, cmenu, e);
                            }
                        },
                        disabled: false, title: data.name, className: data.id == 0 ? 'ab-remove' : 'wd-list' + data.id
                    };
                    menu.push(opt);
                });
                // }
                function Absences() {
                    var dataSend = [];
                    $.each($container.find('td.ui-selected'), function (index, value) {
                        var _dx = $(this).attr('dx');
                        var _dy = $(this).attr('dy');
                        var _timeDay = $(this).attr('class').split(' ')[0];
                        var _time = dataSets[_dx][_dy].date ? dataSets[_dx][_dy].date : 0;
                        dataSend[index] = _time;
                    });
                    var href = '';
                    var idEmp = <?php echo isset($_GET['id'])?$_GET['id']:0;?>;
                    var IDurl = (idEmp != 0) ? "&id=" + idEmp : '';
                    if ((window.location.pathname == '/absence_requests/') || (window.location.pathname == '/absence_requests/index/week') || (window.location.pathname == '/absence_requests/index/month') || (window.location.pathname == '/absence_requests/index/year')) {
                        var dateCurrent = new Date();
                        var linkCurrent = $(location).attr('search');
                        if (linkCurrent) { // neu chuyen sang nam khac nam hien tai thi phai tinh lai :)
                            var resYear = <?php echo (isset($_GET['year']))?$_GET['year']:0;?>;
                            if (resYear == 0) {
                                resYear = dateCurrent.getFullYear();
                            }
                            dateCurrent.setFullYear(parseInt(resYear));
                        }
                        var _month = dateCurrent.getMonth() + 1;
                        href = "/absence_requests/requestApi?year=" + dateCurrent.getFullYear() + "&month=" + _month + IDurl + "#";
                    } else {
                        $.urlParam = function (name) {
                            var location = window.location.href;
                            var results = new RegExp('[\\?&amp;]' + name + '=([^&amp;#]*)').exec(location);
                            if (!results)
                                return 0;
                            return results[1] || 0;
                        }
                        _href = window.location.search;
                        _href = _href.substr(1, 4);
                        if (_href == 'year') {
                            href = "/absence_requests/requestApi?year=" + $.urlParam('year') + "&month=" + $.urlParam('month') + IDurl + "#";
                        } else {
                            href = "/absence_requests/requestApi?week=" + $.urlParam('week') + "&year=" + $.urlParam('year') + IDurl + "#";
                        }
                    }
                    var result = "";
                    $.ajax({
                        url: href,
                        async: false,
                        dataType: 'json',
                        data: {
                            dateCurrent: dataSend
                        },
                        success: function (data) {
                            result = data;
                        }
                    });
                    return result;
                }
                //1
                function _draw_menu(_absences) {
                    
                    //count cell request
                    var _count = $container.find('.ui-selected').length;
                    _count = (_count) ? _count * 0.5 : 0;
                    $.each(_absences, function (undefined, data) {
                        var _rq = data.request;
                        var _total = parseFloat(data.total);
                        _rq = (_rq) ? _rq + _count : _count;
                        var _data = data.print;
                        var _rqView = data.request;
                        var _view = '';
                        //if(data.request){
                        if (!_total) {
                            if (!_rqView) {
                                _view = '';
                                _view = _data + _view;
                                $('.wd-list' + data.id).find('.context-menu-item-inner').text(_view);
                            } else {
                                if (_total == 0) {
                                    _view = '  (' + _rqView + '/' + _total + ')';
                                } else {
                                    _view = '  (' + _rqView + '/' + 'NA' + ')';
                                }
                                _view = _data + _view;
                                $('.wd-list' + data.id).find('.context-menu-item-inner').text(_view);
                            }
                        } else {
                            if (!_rqView) {
                                _view = '  (' + 0 + '/' + _total + ')';
                                _view = _data + _view;
                                $('.wd-list' + data.id).find('.context-menu-item-inner').text(_view);
                            } else {
                                _view = '  (' + _rqView + '/' + _total + ')';
                                _view = _data + _view;
                                $('.wd-list' + data.id).find('.context-menu-item-inner').text(_view);
                            }
                        }
                        //}
                        $(contextMenu.menu).find('.wd-list' + data.id).hover(
                            function () {
                                var errorBegin = '<?php echo __($errorBegin,true);?>';
                                var errorExp = '<?php echo __($errorExp,true);?>';
                                var dateSelected = [];
                                $.each($container.find('td.ui-selected'), function (index, value) {
                                    var _dySelected = $(this).attr('dy');
                                    dateSelected[index] = _dySelected;
                                });
                                if (_total || (_total == 0)) {
                                    if (_rq > _total) {
                                        if ($('.wd-list' + data.id).find('.context-menu-item-inner').text().length < 25) {
                                            $('.wd-list' + data.id).find('.context-menu-item-inner').append(errorExp);
                                        }
                                        $(this).attr('id', 'wd-disabled');
                                        $(this).addClass('wd-bt-no');
                                    } else {
                                        $('.wd-list' + data.id).find('.context-menu-item-inner span').remove();
                                        $(this).addClass('wd-bt-yes');
                                        $(this).removeAttr('id');
                                        $(this).removeClass('wd-bt-no');
                                    }
                                } else {
                                    $(this).addClass('wd-bt-yes');
                                    $(this).removeAttr('id');
                                    $(this).removeClass('wd-bt-no');
                                }
                                var dateBeginAbsence = listBeginOfPeriods[data.id] ? listBeginOfPeriods[data.id] : 0;
                                var selectDateStart = dateSelected[0];
                                var selectDateEnd = dateSelected[dateSelected.length - 1];
                                var allowRequest = true;
                                if (dateBeginAbsence == 0) {
                                    //khong co ngay xac dinh -> 0k
                                } else {
                                    if (selectDateStart < dateBeginAbsence) {
                                        if (selectDateEnd < dateBeginAbsence) {
                                            // 2 ngay chon dieu be hon moc xac dinh -> 0k
                                        } else if (selectDateEnd > dateBeginAbsence) {
                                            allowRequest = false;
                                            // start chon be hon va end chon lon hon -> SAI
                                        } else {
                                            allowRequest = false;
                                            // start chon be hon va end chon bang nhau-> SAI
                                        }
                                    } else if (selectDateStart > dateBeginAbsence) {
                                        if (selectDateEnd < dateBeginAbsence) {
                                            // khong co truong hop nay xay ra
                                        } else if (selectDateEnd > dateBeginAbsence) {
                                            // start chon lon hon va end chon lon hon -> OK
                                        } else {
                                            // start chon lon hon va end chon bang nhau-> OKIE
                                        }
                                    } else {
                                        if (selectDateEnd < dateBeginAbsence) {
                                            // khong co truong hop nay xay ra
                                        } else if (selectDateEnd > dateBeginAbsence) {
                                            // start chon bang nhau va end chon lon hon -> OKIE
                                        } else {
                                            // 2 MOC CHON BANG NHAU -> OKIE
                                        }
                                    }
                                }
                                if (allowRequest == false) {
                                    $(this).attr('id', 'wd-disabled');
                                    $(this).addClass('wd-bt-no');
                                    var contentOld = $('.wd-list' + data.id).find('.context-menu-item-inner').text();
                                    $('.wd-list' + data.id).find('.context-menu-item-inner').click(function () {
                                        if ($('.wd-list' + data.id).find('.context-menu-item-inner').text().length < 30) {
                                            var contentOld = $('.wd-list' + data.id).find('.context-menu-item-inner').text();
                                            $('.wd-list' + data.id).find('.context-menu-item-inner').append(errorBegin);
                                            $('.wd-list' + data.id).find('.context-menu-item-inner').attr('id', 'wd-disabled');
                                            $('.wd-list' + data.id).find('.context-menu-item-inner').addClass('wd-bt-no');
                                        }
                                    });
                                }
                            },
                            function () {
                                $(this).removeAttr('id');
                                $(this).removeClass('wd-bt-no');
                                $(this).removeClass('wd-bt-yes');
                            }
                        );
                    });
                }
                $container.contextMenu(menu, {theme: 'vista', beforeShow: function () {
					if (!$container.find('.ui-selected').length){
						wdConfirmIt({
							title: '',
							content: <?php echo json_encode( __('Select your absence before to select +', true));?>,
							width: ( Azuree && Azuree.language && Azuree.language == 'fr') ? 480 : 400,
							buttonModel: 'WD_TWO_BUTTON',
							buttonText: [
								'<?php __('YES');?>',
								'<?php __('NO');?>',
							],
						},function(){
							return false;
						},function(){
							return false;
						});
						return false;
					}
					this.menu.width('200');
                    contextMenu = this;
                    _draw_menu(_currAbsences);
					initMenuFilter($(contextMenu.menu).find('td').addClass('context-container').children('.context-menu').addClass('loading-mark loading'));
                    setTimeout( function(){
                        var _absences = Absences();
                        _currAbsences = _absences;
                        _draw_menu(_currAbsences);                        initMenuFilter($(contextMenu.menu).find('td').addClass('context-container').children('.context-menu').removeClass('loading'));
                    }, 10);
				}});

                $('#open-menu').click(function (e) {
                    var menu = $container.data('cmenu');
                    menu.show($container, e);
                });
            })();
        });
        // tooltip validated
        var temp = setInterval(function () {
            $('.ch-absen-validation').focus(function () {
                $(this).tooltip('option', 'content', '<?php echo __('Timesheet validated',true);?>');
                $(this).tooltip('enable');
            }).mouseup(function () {
                $(this).tooltip('close');
            }).blur(function () {
                $(this).tooltip('option', 'content', '<?php echo __('Timesheet validated',true);?>');
                $(this).tooltip('enable');
            }).tooltip({maxWidth: 1000, maxHeight: 300, content: function (target) {
                    return '<?php echo __('Timesheet validated',true);?>';
                }});
            clearInterval(temp);
        }, 1000);
    })(jQuery);
    <?php
    $month = date('m', $_start);
    $week = date('W', $_start);
    $year = date('Y', $_start);
    $profit = !empty($this->params['url']['profit']) ? $this->params['url']['profit'] : '';
    $idManageValidation = !empty($this->params['url']['id']) ? $this->params['url']['id'] : '';
    ?>
    var $month = <?php echo json_encode($month);?>,
            $week = <?php echo json_encode($week);?>,
            $year = <?php echo json_encode($year);?>,
            $profit = <?php echo json_encode($profit);?>,
            $getDataByPath = <?php echo json_encode($getDataByPath);?>,
            $idManageValidation = <?php echo json_encode($idManageValidation);?>;
	function fnRefreshLink(){
		var linkRequest = '/absence_requests/index/';
		reWeek = $('#ControlWeek').val();
		reMonth = $('#ControlMonth').val();
		reYear = $('#ControlYear').val();
		exPath ='';
        if ($('#typeRequest').val() == 'week') { // change month to week
            linkRequest += 'week';
			exPath +='&week='+reWeek;
        } else if ($('#typeRequest').val() == 'month') { // change week to month
            linkRequest += 'month';
			exPath +='&month='+reMonth;
        } else { // change to year
            exPath +='&month=1';
            linkRequest += 'year';
        }
        var refreshLink = '';
        if ($idManageValidation) {
            refreshLink = linkRequest + '?id=' + $idManageValidation + '&profit=' + $profit + '&year=' + reYear + exPath + '&get_path=' + $getDataByPath;
        } else {
            refreshLink = linkRequest + '?year=' + reYear + exPath + '&profit=' + $profit + '&get_path=' + $getDataByPath;
        }
        window.location.href = refreshLink;
	}
    $('#typeRequest').change(function () {
       fnRefreshLink();
    });
	$('#ControlYear').change(function () {
		fnRefreshLink();
	});
    //EXPAND TREE
    $(document).keyup(function (e) {
        if (window.event)
        {
            var value = window.event.keyCode;
        } else
            var value = e.which;
        if (value == 27) {
            collapseScreen();
        }
    });
    function collapseScreen()
    {
        $('#table-control').show();
        $('.wd-title').show();
        $('#collapse').hide();
        $('#project_container').removeClass('fullScreen');
        $(window).resize();
    }
    function expandScreen()
    {
        $('#table-control').hide();
        $('.wd-title').hide();
        $('#project_container').addClass('fullScreen');
        $('#collapse').show();
        $(window).resize();
    }
    function get_day(day) {
	  switch (day) {
			  case 'Sunday':
				return "sunday";
				break;
			  case 'Monday':
				return "monday";
				break;
			  case 'Tuesday':
				return "tuesday";
				break;
			  case 'Wednesday':
				return "wednesday";
				break;
			  case 'Thursday':
				return "thursday";
				break;
			  case 'Friday':
				return "friday";
				break;
			  case 'Saturday':
				return "saturday";
				break;
		}
	}
	
</script>
<div id="collapse" onclick="collapseScreen();" ><i class="icon-size-actual"></i></div>
