<?php 
 // include js/ css
 // Huynh
 // debug( $employee); exit;
	$managerHour = false;
	$fillMoreThanCapacity = (!$managerHour && !empty($companyConfigs['fill_more_than_capacity_day'])) ?  1 : 0;
	$showActivityForecastComment = (!$managerHour && !empty($companyConfigs['show_activity_forecast_comment'])) ?  1 : 0;
	echo $this->Html->script(array(
		'jquery-ui.multidatespicker',
		'qtip/jquery.qtip',
		'slick_grid/lib/jquery.event.drag-2.0.min',
		'slick_grid/slick.core',
		'slick_grid/slick.dataview',
		'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/slick.editors',
		'slick_grid/slick.grid',
		'slick_grid/slick.grid.activity',
        'slick_grid_custom',
		'context/jquery.contextmenu', // Khong nen su dung thu vien nay nua. thu vien nay ho tro rat kem va khong con document de su dung
	));
	echo $this->Html->css(array(
		'/js/qtip/jquery.qtip',
		'slick_grid/slick.grid',
		'preview/message-popup',
		'slick_grid/slick.common',
		'slick_grid/slick.edit',
		'add_popup',
		'preview/activity_request',
		'context/jquery.contextmenu'
	));
	
	$svg_icons = array(
		'add' => '<svg xmlns="http://www.w3.org/2000/svg" width="16.002" height="16.002" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>',
		'users' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-192 -132)"><rect class="a" width="16" height="16" transform="translate(192 132)"/><g transform="translate(192 134)"><path class="b" d="M205.507,144.938a4.925,4.925,0,0,0-9.707,0h-1.1a6.093,6.093,0,0,1,3.557-4.665l.211-.093-.183-.14a3.941,3.941,0,1,1,4.75,0l-.183.14.21.093a6.1,6.1,0,0,1,3.552,4.664Zm-4.851-10.909a2.864,2.864,0,1,0,2.854,2.864A2.863,2.863,0,0,0,200.657,134.029Z" transform="translate(-194.697 -132.938)"/><path class="b" d="M214.564,143.9a2.876,2.876,0,0,0-2.271-2.665.572.572,0,0,1-.449-.555.623.623,0,0,1,.239-.507,2.869,2.869,0,0,0-1.344-5.114,4.885,4.885,0,0,0-.272-.553,5.52,5.52,0,0,0-.351-.556c.082-.005.164-.008.245-.008a3.946,3.946,0,0,1,3.929,3.955,3.844,3.844,0,0,1-.827,2.406l-.1.13.147.076a3.959,3.959,0,0,1,2.132,3.392Z" transform="translate(-199.639 -133.26)"/></g></g></svg>',
		'reload' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1323 -240)"><path class="b" d="M199.5,191.5a7.98,7.98,0,0,0-5.44,2.15v-1.51a.64.64,0,0,0-1.28,0v3.2a.622.622,0,0,0,.113.341l.006.009a.609.609,0,0,0,.156.161c.007.005.01.013.017.018s.021.009.031.015a.652.652,0,0,0,.115.055.662.662,0,0,0,.166.034c.012,0,.023.007.036.007h3.2a.64.64,0,1,0,0-1.28h-1.8a6.706,6.706,0,1,1-2.038,4.8.64.64,0,1,0-1.28,0,8,8,0,1,0,8-8Z" transform="translate(1131.5 48.5)"/><rect class="a" width="16" height="16" transform="translate(1323 240)"/></g></svg>',
		'star' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-160 -264)"><path class="b star" d="M8,1.032l2.137,4.552L15,6.293,11.5,9.932l.806,5.037L8,12.674l-4.3,2.3.806-5.038-3.5-3.638,4.859-.71L8,1.032M8,0A1.156,1.156,0,0,0,6.958.67L5.149,4.547.985,5.187A1.163,1.163,0,0,0,.333,7.146l3.051,3.145-.707,4.361A1.166,1.166,0,0,0,3.15,15.79a1.152,1.152,0,0,0,1.224.068L8,13.9l3.63,1.953a1.156,1.156,0,0,0,1.7-1.205l-.708-4.361,3.052-3.145a1.163,1.163,0,0,0-.653-1.959l-4.163-.639L9.05.67A1.154,1.154,0,0,0,8,0Z" transform="translate(159.995 263.998)"/></g></svg>',
		'expand' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-216 -168)"><rect class="a" width="16" height="16" transform="translate(216 168)"/><path class="b" d="M902-2125h-4v-1h3v-3h1v4Zm-8,0h-4v-4h1v3h3v1Zm8-8h-1v-3h-3v-1h4v4Zm-11,0h-1v-4h4v1h-3v3Z" transform="translate(-672 2307)"/></g></svg>',
		'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-4 -4)"><rect class="a" width="16" height="16" transform="translate(4 4)"/><path class="b" d="M10.5,8h-5a.5.5,0,0,0,0,1h5a.5.5,0,1,0,0-1ZM8,0C3.581,0,0,3.134,0,7a6.7,6.7,0,0,0,3,5.459V16l4.1-2.048c.3.029.6.047.9.047,4.418,0,8-3.134,8-7S12.417,0,8,0ZM8,13H7L4,14.5V11.891A5.772,5.772,0,0,1,1,7C1,3.686,4.133,1,8,1s7,2.686,7,6S11.865,13,8,13Zm3.5-8h-7a.5.5,0,0,0,0,1h7a.5.5,0,1,0,0-1Z" transform="translate(4.001 4)"/></g></svg>',
		'duplicate' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(-1621.625 -334.663)"><rect class="a" width="16" height="16" transform="translate(1621.625 334.663)"/><g transform="translate(36.824 46.863)"><path class="b" d="M1586.915,301.177a1.116,1.116,0,0,1-1.115-1.115V288.915a1.116,1.116,0,0,1,1.115-1.115h8.525a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Zm0-12.459a.2.2,0,0,0-.2.2v11.147a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V288.915a.2.2,0,0,0-.2-.2Z"/><path class="b" d="M1590.915,305.177a1.116,1.116,0,0,1-1.115-1.115v-.656a.459.459,0,1,1,.918,0v.656a.2.2,0,0,0,.2.2h8.525a.2.2,0,0,0,.2-.2V292.915a.2.2,0,0,0-.2-.2h-.656a.459.459,0,0,1,0-.918h.656a1.116,1.116,0,0,1,1.115,1.115v11.147a1.115,1.115,0,0,1-1.115,1.115Z" transform="translate(-0.754 -1.377)"/></g></g></svg>',
		'agenda' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16" transform="translate(0)"/><path class="b" d="M15,16H1a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h4V.5a.5.5,0,0,1,1,0v1h4V.5a.5.5,0,1,1,1,0v1h4a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16ZM15,2.5H11V3a.5.5,0,1,1-1,0V2.5H6V3a.5.5,0,0,1-1,0V2.5H1V15H15ZM3.5,6h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,3,7.5v-1A.5.5,0,0,1,3.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,3.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,7.5v-1A.5.5,0,0,1,7.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,7.5,10Zm4-4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,6Zm0,4h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,11.5,10Z" transform="translate(0.001 -0.001)"/></g></svg>',
		'delete' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g transform="translate(0)"><rect class="a" width="16" height="16"/><path class="b" d="M.182,9.819a.62.62,0,0,1,0-.876L4.124,5,.182,1.058A.619.619,0,0,1,1.057.182L5,4.124,8.943.182a.619.619,0,1,1,.876.876L5.876,5,9.819,8.943a.62.62,0,1,1-.876.876L5,5.876,1.057,9.819a.619.619,0,0,1-.876,0Z" transform="translate(3 3)"/></g></svg>',
		'ok_no' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="40" height="40" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/><path class="c" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>',
		'ok' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-317 -66)"><rect class="a" width="40" height="40" transform="translate(317 66)"/><path class="b" d="M9.791,1.412h0L4.314,7.757h0a.648.648,0,0,1-1.01,0h0L.209,4.171h0A.9.9,0,0,1,0,3.585a.777.777,0,0,1,.714-.827A.668.668,0,0,1,1.219,3h0l2.59,3L8.781.242h0A.668.668,0,0,1,9.285,0,.778.778,0,0,1,10,.827.9.9,0,0,1,9.791,1.412Z" transform="translate(332 82.001)"/></g></svg>',
		'no' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><g transform="translate(-318 -66)"><rect class="a" width="40" height="40" transform="translate(318 66)"/><path class="b" d="M-287.4-1709.767a.62.62,0,0,1,0-.876l3.942-3.942-3.942-3.943a.619.619,0,0,1,0-.876.619.619,0,0,1,.876,0l3.943,3.943,3.943-3.943a.619.619,0,0,1,.876,0,.619.619,0,0,1,0,.876l-3.942,3.943,3.942,3.942a.62.62,0,0,1,0,.876.62.62,0,0,1-.876,0l-3.943-3.943-3.943,3.943a.618.618,0,0,1-.438.182A.618.618,0,0,1-287.4-1709.767Z" transform="translate(620.586 1800.587)"/></g></svg>',
		'arrow_right' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g transform="translate(-1581 -458)"><rect class="a" width="24" height="24" transform="translate(1581 458)"/><path class="b" d="M0,7.43A.551.551,0,0,1,.216,7L3.491,4,.174.969h0A.544.544,0,0,1,0,.572.6.6,0,0,1,.624,0a.656.656,0,0,1,.434.16h0L4.807,3.589h0A.544.544,0,0,1,5,4H5V4a.547.547,0,0,1-.192.411h0L1.058,7.844l0,0A.652.652,0,0,1,.624,8,.6.6,0,0,1,0,7.43Z" transform="translate(1591.001 465.999)"/></g></svg>',
		'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs></defs><g transform="translate(-272 -128)"><rect class="a" width="24" height="24" transform="translate(272 128)"/><path class="b" d="M.154,15.843a.536.536,0,0,0,.758,0L5.3,11.456A6.5,6.5,0,1,0,4.54,10.7L.154,15.084A.536.536,0,0,0,.154,15.843ZM9.5,1A5.5,5.5,0,1,1,4,6.5,5.5,5.5,0,0,1,9.5,1Z" transform="translate(276.003 132)"/></g></svg>',
		'trash' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><defs><style>.a{fill:none;}.b{fill:#f05352;stroke:rgba(0,0,0,0);}</style></defs><g transform="translate(-1627.041 -485.28)"><rect class="a" width="40" height="40" transform="translate(1627.041 485.28)"/><path class="b" d="M3.6,16a1.483,1.483,0,0,1-1.426-1.532V2.542H.391A.406.406,0,0,1,0,2.122.406.406,0,0,1,.391,1.7H4V.976A.946.946,0,0,1,4.911,0h6.045a.946.946,0,0,1,.909.976V1.7h3.744a.407.407,0,0,1,.391.42.407.407,0,0,1-.391.42H13.693V14.468A1.483,1.483,0,0,1,12.267,16Zm-.644-1.532a.671.671,0,0,0,.644.693h8.667a.671.671,0,0,0,.644-.693V2.542H2.956ZM4.785.976V1.7h6.3V.976A.132.132,0,0,0,10.956.84H4.911A.132.132,0,0,0,4.785.976Zm5.458,11.993V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.69,0V5.261a.4.4,0,1,1,.791,0v7.708a.4.4,0,1,1-.791,0Zm-2.71,0V5.261a.4.4,0,1,1,.79,0v7.708a.4.4,0,1,1-.79,0Z" transform="translate(1639.042 497.28)"/></g></svg>'
	);
	$role = $employee_info['Role']['name'];
	$avg = intval(($_start + $_end)/2);
	$week = date('W', $avg);
	$year = date('Y', $avg);
	$profit = $employee['profit_center_id'];
	$queryUpdate = '/week?week=' . $week . '&year=' . $year . '&id=' . $idEmployee.'&profit='.$profit;
	$requestStatus = isset( $requestConfirm['ActivityRequestConfirm']['status']) ?  $requestConfirm['ActivityRequestConfirm']['status'] : -1;
	$requestConfirmDate = !empty($requestConfirm['ActivityRequestConfirm']['updated']) ? $requestConfirm['ActivityRequestConfirm']['updated'] : '';
	$requestConfirmName = !empty($requestConfirm['ActivityRequestConfirm']['employee_validate']) ? $requestConfirm['ActivityRequestConfirm']['employee_validate'] : '';
	$dateValidate = !empty($requestConfirmDate) ? date('d/m/Y', $requestConfirmDate) : '';
	$nameValidate = !empty($requestConfirmName) ? $requestConfirmName : '';
	if ($requestStatus == 0){
		$statusAct = 'Sent';
	} elseif ($requestStatus == 1){
		$statusAct = sprintf(__('Rejected (%s)', true), $nameValidate. ', ' .$dateValidate);
	} elseif ($requestStatus == 2){
		$statusAct = sprintf(__('Validated (%s)', true), $nameValidate. ', ' .$dateValidate);
	} else{
		$statusAct = 'In progress';
	}
	$canModified = (($requestStatus == -1 || $requestStatus == 1) && $canWrite);
	function jsonParseOptions($options, $safeKeys = array()) {
		$output = array();
		$safeKeys = array_flip($safeKeys);
		foreach ($options as $option) {
			$out = array();
			foreach ($option as $key => $value) {
				if (!is_int($value) && !isset($safeKeys[$key])) {
					$value = json_encode($value);
				}
				$out[] = $key . ':' . $value;
			}
			$output[] = implode(', ', $out);
		}
		return '[{' . implode('},{ ', $output) . '}]';
	}
	/* Define Columns */
	$columns = array(
		'fav' => array(
			'id' => 'favourite',
			'field' => 'favourite',
			'name' => __(' ', true),
			'width' => 40,
			// 'minWidth' => 40,
			// 'maxWidth' => 40,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'noFilter' => 1, 
			'cssClass' => 'action-column slick-cell-merged',
			'headerCssClass' => 'slick-cell-merged',
			'formatter' => 'Slick.Formatters.taskFavourite'
		),
		'name' => array( 
			'id' => 'task_name',
			'field' => 'task_name',
			'name' => __('Done', true),
			// 'width' => 420,
			'sortable' => true,
			'resizable' => true,
			'editable' => false,
			'headerCssClass' => 'slick-cell-merged',
			'cssClass' => 'slick-cell-merged text-left',
			'formatter' => 'Slick.Formatters.taskName'
		),
		'progress' => array(
			'id' => 'progress',
			'field' => 'progress',
			'name' => ' ',
			'width' => 90,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'cssClass' => 'slick-cell-merged action-column',
			'headerCssClass' => 'slick-cell-merged',
			'formatter' => 'Slick.Formatters.taskProgress'
		),
		'task_comment' => array(
			'id' => 'task_comment',
			'field' => 'task_comment',
			'name' => '<i class = "icon-arrow-left"></i>',
			'width' => 40,
			'sortable' => false,
			'resizable' => false,
			'editable' => false,
			'headerCssClass' => 'prev-week',
			'cssClass' => 'action-column',
			'formatter' => 'Slick.Formatters.taskComment'
		),
		'task_capacity' => array(
			'id' => 'task_capacity',
			'field' => 'task_capacity',
			'name' => __('Capacity', true),
			'width' => 80,
			'sortable' => false,
			'resizable' => false,
			'cssClass' => 'task-capacity',
			// 'editable' => false,
			'formatter' => 'Slick.Formatters.taskCapacity'
		)
	);
	
	foreach( $listWorkingDays as $date => $is_working){
		$columns[$date] = array(
			'id' => $date,
			'field' => $date,
			'name' => __(substr(date( 'l', $date), 0, 2), true) . " " . date('d', $date) . ($is_working ? (" <a href='javascript:void(0);' class = 'comment-day' data-date=".$date.">" . ($showActivityForecastComment ? $svg_icons['message']:'') . "</a>") : ''),
			'width' => 80,
			'sortable' => false,
			'resizable' => false,
			'calcTotal' => (boolean) $is_working,
			'editable' => (boolean) $is_working,
			'format_date' => date('d-m-Y', $date),
			'cssClass' => $is_working ? 'working-day' : 'day-off',
			'formatter' => 'Slick.Formatters.floatVal',
			'validator' => 'DataValidation.forecastValue',
			'editor' => $is_working ? 'Slick.Editors.forecastValue' : 0,
		);
	}
	$columns['action'] = array(
		'id' => 'action',
		'field' => 'action',
		'name' => '<i class = "icon-arrow-right"></i>',
		'width' => 40,
		// 'minWidth' => 40,
		// 'maxWidth' => 40,
		'sortable' => false,
		'resizable' => false,
		'editable' => false,
		// 'renderOnResize' => true,
		'cssClass' => 'action-column',
		'headerCssClass' => 'next-week',
		'formatter' => 'Slick.Formatters.action',
		'asyncPostRender' => 'asyncInitSelectable'
	);
	
	// Slick_Grid_custom remove last column
	if( !$canModified ) $columns['nothing'] = array();
	// ob_clean();
	/* END Define Cloumns */
	$totalCapacity = 0;
	/* Build DataView */
	$dataView = array();
	$i = 0;
	/* Build data cho Holiday và Absence */
		/* Holiday */
	$type = 'holiday';
	// ob_clean();
	// debug( $holidays);
	$data = array(
		'id' => $type,
		'no.' => $i++,
		'type' => $type,
		'MetaData' => array(
			'cssClasses' =>  'not-working '.$type ,
		),
		'task_name' => __('Holiday', true),
		'readonly' => true,
	);
	foreach( $holidays as $date => $holiday){
		$value = 0;
		foreach( $holiday as $half  => $is_repeat){ /* $half : 'am' / 'pm' */
			$value += 0.5*$ratio;
			$data[$date.$half] = 1;
		}
		$data[$date] = $value;
	}
	$dataView[] = $data;
		/* END Holiday */
		/* Absence */
	if( $display_absence){
		$type = 'absence';
		// ob_clean();
		// debug( $companyAbsences);
		// debug( $absenceRequests);
		// exit;
		$data = array(
			'id' => $type,
			'no.' => $i++,
			'type' => $type,
			'MetaData' => array(
				'cssClasses' =>  'not-working ' . $type ,
			),
			'task_name' => __('Absence', true),
			'readonly' => true,
		);
		foreach( $absenceRequests as $date => $absenceRequest){
			$value = 0;
			foreach( array('am', 'pm' ) as $half){
				if($absenceRequest['absence_'.$half]){
					$value += 0.5*$ratio;
				}
			}
			$data[$date] = $value;
			$key = $date.'_data';
			$data[$key] = array(
				'absence_pm' => $absenceRequest['absence_pm'],
				'absence_am' => $absenceRequest['absence_am'],
				'response_am' => $absenceRequest['response_am'],
				'response_pm' => $absenceRequest['response_pm']
			);
		}
		$dataView[] = $data;
	}
		/* END Absence */
	/* END Build data cho Holiday và Absence */
	/* Build data cho Task */
	$type = 'ac';
	// debug( $activityRequests); exit;
	foreach($activityRequests as $task_id => $task_values){
		$activity_id = $activityTasks[$task_id]['activity_id'];
		$subfamily_id = $activities[$activity_id]['subfamily_id'];
		$family_id = $activities[$activity_id]['family_id'];
		$favourite = !empty( $taskFavourites[$activityTasks[$task_id]['project_task_id'] ]) ? 1 : 0;
		$task_workload = floatVal(!empty($activityTasks[$task_id]['estimated']) ? $activityTasks[$task_id]['estimated'] : 0 );
		$task_consumed = floatVal(!empty($consumed[$task_id]) ? $consumed[$task_id] : 0);
		$data = array(
            'id' => $type . '-0-' . $task_id,
            'no.' => $i++,
            'type' => $type,
            'MetaData' => array(
				'cssClasses' => 'type-ac '. ($type . '-0-' . $task_id) .' '. ($favourite ? 'favourite' : 'non_favourite' ),
			),
            'activity' => 0, // have if select_project_without_task
            'readonly' => false,
			'task_id' => $task_id,
			'task_name' => $activityTasks[$task_id]['name'],
			'part_name' => $activityTasks[$task_id]['part_name'],
			'phase_name' => $activityTasks[$task_id]['phase_name'],
			'activity_name' => $activities[$activity_id]['name'],
			'activity_sname' => $activities[$activity_id]['short_name'],
			'activity_lname' => $activities[$activity_id]['long_name'],
			'subfamily' => !empty($families[$subfamily_id]['name']) ? $families[$subfamily_id]['name'] : '',
			'family' => $families[$family_id]['name'],
			'favourite' => $favourite,
			'workload' => $task_workload,
			'consumed' => $task_consumed,
			'progress' => $task_workload ? intval( ($task_consumed / $task_workload)*100 ) : 100
        );
		// debug($task_values);
		$data += $task_values;
		$dataView[] = $data;
	}
	// debug($dataView);
	// exit;
	/* END Build DataView */
	
	/* Translate */	
	$i18ns = array(
		'comments' => __('Comment(s)', true),
		'add_comment' => __('Add a comment', true),
		'summary' => __('Summary', true),
		'holiday' => __('Holiday', true),
		'absence' => __('Absence', true),
		'any' => __('-- Any --', true),
		'not_empty' => __('This information is not blank!', true),
		'declare_days' => __('You have to declare %s days before validate your request', true),
		'absence_require' => __('Absence has to be validated before sending the timesheet', true),
		'reject_confirm_desc' => __('Are you sure to reject the activity request?', true),
		'validate_confirm_desc' => __('Are you sure to validate the timesheet? Once validated, you cannot modify it', true),
		'value_between' => __('The value must between %1$s and %2$s.', true),
		'clear' => __('Clear', true),
		'no_name' => __('No name', true),
		'unknown' => __('Unknown', true),
		'no_more' => __('No more value is allowed', true),
		'send' => __('Send timesheet?', true),
		'reject_confirm' => __('Reject Activity request?', true),
		'validate_confirm' => __('Activity validate?', true),
		'error' => __('There is error(s) in your timesheet', true),
		'insert_comment' => __('Please justify to fill more than capacity by day or close the popup to cancel. Justification %s:', true),
		'send_validation' => __('Send Validation', true),
		'picker_text' => __('Week %1$s - %2$s to %3$s', true),
		'Add a comment' => __('Add a comment', true),
        'Remove request' => __('Remove request', true),
        'Holiday' => __('Holiday', true),
        'Date requesting' => __('Date requesting', true),
        'Date validate' => __('Date validate', true),
        'Date reject' => __('Date reject', true),
        'saved' => __('Saved', true),
	);
	
	$trans_month = array();
	$trans_month[date('M', $startdate)] = __(date('M', $startdate), true);
	$trans_month[date('M', $enddate)] = __(date('M', $enddate), true);
	$trans_month['wee'] = __('wee', true);
	
	$status = array(
		-1 => __('In progress', true),
		0 => __('Sent', true),
		1 => __('Rejected',true),
		2 => __('Validated',true),
	);
	
	/* END Translate */
?>
<style>
.wd-tab{
	max-width: none;
}
.wd-heading-action #date-range-picker{
	height: 30px; 
	display: inline;
	vertical-align: top; 
	padding: 0px; 
	width: 0px !important; 
	border: none;
	position: absolute;
}
</style>
<div id="wd-container-main" class="wd-project-admin loading-mark">
    <div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-tab">
				<div class="wd-panel">
					<div class="wd-list-project">
						<?php
                            echo $this->Form->create('Request', array(
                                'type' => 'GET',
								'id' => 'activity_request_form',
                                'url' => '/' . Router::normalize($this->here)));
                         ?>
						<div class="wd-heading-action">
							<!-- actions request -->
							<div class="wd-avatar">
								<div class="circle-larger-avatar">
									<img src="<?php echo $this->UserFile->avatar($data, 'avatar'); ?>" alt="avatar" title="<?php echo $employee['first_name'] . ' '. $employee['last_name']; ?>">
									<?php echo $this->Form->hidden('id', array('value' => $employee['id'], 'rel'=>'no-history')); ?>
								</div>
							</div>
							<div class="wd-title">
								<div class="wd-title-inner">
									<div class="wd-manager-info">
										<p class="manager-name"><?php echo $employee['first_name'] .' '. $employee['last_name']; ?></p>	
										<p class="profit-center"><i><?php echo $svg_icons['users']; ?></i><?php echo $profitCenter['ProfitCenter']['name'] ?></p>
										<?php echo $this->Form->hidden('profit', array('value' => $profitCenter['ProfitCenter']['id'], 'rel'=>'no-history')); ?>
										<p class="manager-status"><?php echo __('Status' , true) .' : '; ?><span class="status-info">
										<?php echo $statusAct;?>
										</span></p>
									</div>
									<div class="wd-actions">
										<a class="btn btn-add-task" <?php if($requestStatus != -1 && $requestStatus != 1){echo 'style="display: none;"';}?>><?php echo $svg_icons['add']; ?></a>
										<a class="btn btn-refresh-menu"><?php echo $svg_icons['reload']; ?></a>
										<?php if($idEmployee == $employee_info['Employee']['id'] && $requestStatus == -1) {
												 $active_class = $auto_fill_favourite == 1 ? 'filled' : '';
										?>
											<a class="btn copy-favourite <?php echo $active_class?>" href="javascript:void(0);" ><?php echo $svg_icons['star']; ?></a>
										<?php } ?> 
										<a href="javascript:;" onclick="expandScreen();" id="expand-btn" class="btn" title="<?php __('')?>"><?php echo $svg_icons['expand']; ?></a>
										<a href="javascript:void(0);" id="btn-sent" class="btn btn-sent" title="<?php __('Send')?>" <?php if($requestStatus != -1 && $requestStatus != 1){echo 'style="display: none;"';}?>><i class="icon-rocket"></i></a>
										<?php if( $canManage){ ?>
											<a href="javascript:void(0);" id="btn-reject" class="btn btn-reject" title="<?php __('Reject Requested')?>" <?php if($requestStatus == -1 || $requestStatus == 1){echo 'style="display: none;"';}?>><i class="icon-close"></i></a>
											<a href="javascript:void(0);" id="btn-validate" class="btn btn-validate" title="<?php __('Validate Requested')?>" <?php if($requestStatus != 0){echo 'style="display: none;"';}?>><i class="icon-check"></i></a>
										<?php } ?> 
										<?php if($showActivityForecastComment){?>
											<a href="javascript:void(0);" id="open-popup-message" class="btn btn-message" title="<?php __('Add a message')?>"><?php echo $svg_icons['message']; ?></a>
										<?php }?>
										<a class="btn copy-forecast" href="javascript:void(0);" title="<?php __('Copy Forecast')?>" <?php if($requestStatus != -1 && $requestStatus != 1){echo 'style="display: none;"';}?>><?php echo $svg_icons['duplicate']; ?></a>
										<a id="btn-collapse" class="btn btn-right" href="javascript:void(0);" onclick="collapseScreen();" style="display:none;"><i class="icon-size-actual"></i></a>
										<a href="javascript:void(0);" class="btn btn-sent-big btn-right-big" id="btn-sent-timesheet" <?php if($requestStatus != -1 && $requestStatus != 1){echo 'style="display: none;"';}?>><?php echo  __("Send Validation", true)?></a>
										<?php if( $canManage){ ?>
											<a href="javascript:void(0);" class="btn btn-reject-big btn-right-big" id="btn-reject-timesheet" <?php if($requestStatus == -1 || $requestStatus == 1){echo 'style="display: none;"';}?>><?php echo  __("Reject Requested", true)?></a>
											<a href="javascript:void(0);" class="btn btn-validate-big btn-right-big" id="btn-validate-timesheet" <?php if($requestStatus != 0){echo 'style="display: none;"';}?>><?php __("Validate Requested")?></a>
										<?php } ?> 
										<input id='date-range-picker' rel="no-history" value="<?php echo date('d-m-Y',$startdate); ?>" readonly="readonly">
										<label id="dateRequest">
											<?php 
											$text_date = '';
											$text_start = date('d ', $startdate) . $trans_month[date('M', $startdate)] . '. ';
											$text_end = date('d ', $enddate) . $trans_month[date('M', $startdate)] . '. ' . date('Y', $enddate);
											$text_date = sprintf(__('Week %1$s - %2$s to %3$s', true), date('W', $startdate),$text_start,$text_end);  
											?>
											<input id='date-range-picker-display' rel="no-history" type="text" value="<?php echo $text_date; ?>" readonly="readonly">											<?php echo $svg_icons['agenda']; 
											echo $this->Form->hidden('week', array('value' => date('W', $startdate), 'rel'=>'no-history'));
											echo $this->Form->hidden('year', array('value' => date('Y', $startdate), 'rel'=>'no-history'));
											?>
										</label>
										<?php if($idEmployee == $employee_info['Employee']['id'] && $requestStatus == -1) { ?>
										<div id = "switch-autofill" class="wd-bt-switch">
											<?php  $active_class = $auto_fill_week == 1 ? 'filled' : ''; ?>
											<a class = "wd-autofill <?php echo $active_class; ?>" title = "<?php echo __('Fill automaticaly', true);?>">
												<input type="hidden" name="autofill" value="<?php echo $auto_fill_week ?>" />
											</a>
											<span><?php echo __('Fill automaticaly', true);?></span>
										</div>
										<?php } ?>
									</div>
								</div>
								<div class="wd-inner-send">
									
								</div>
							</div>
							<div class="add-new-task" id="add_new_task">
								<a href="javascript:void(0);" class="add_new_task_button" <?php if($requestStatus != -1 && $requestStatus != 1){echo 'style="display: none;"';}?>><img title="<?php __('Add new task');?>" src="/img/new-icon/add.png"></a>
							</div>
						</div>
						<?php echo $this->Form->end(); ?>
						<div id="message-place">
							<?php
							echo $this->Session->flash();
							?>
						</div>
						<div class="wd-content">
							<!-- content request -->
							<div class="wd-table" id ="activity-request"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="task-menu-container loading-mark" id="popup-task-menu">
				<div class="task-menu-inner clearfix">
				</div>
			</div>
		</div>
	</div>
	<div id="content_copy_forecast" class="content_copy_forecast" style="display: none;"></div>
</div>
<div id="template_logs" class="template_logs" style="height: 210px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>
<?php if( $display_absence){?>
	<div id="absence_comment_dialog" class="template_logs wd-dialog-comment loading-mark" style="height: 420px; width: 320px;display: none;">
		<div class="add-comment"></div>
	</div>
<?php } ?> 
<div class="wd-full-popup send-timesheet-confirm-popup" id="send-timesheet-confirm-popup" style="display: none;">
	<div class="wd-popup-inner">
		<div class="alert-popup loading-mark wd-popup-container wd-widget">
			<div class="popup-title">
				<h3 class="title"><?php __('Send request for validation'); ?></h3>
				<a href="javascript:void(0);" class="popup-close-btn" onclick="cancel_popup(this);"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
				<?php
				echo $this->Form->create('sendTimesheet', array(
					'type' => 'get',
					'id' => 'sendTimesheetForm',
					'url' => array(
						'controller' => 'activity_forecasts',
						'action' => 'confirm_request',
						'week',
						$employee['profit_center_id']
					),
				));
				echo $this->Form->hidden('id', array('value' => $idEmployee));
				echo $this->Form->hidden('profit', array('value' => $employee['profit_center_id']));
				echo $this->Form->hidden('week', array('value' => $week));
				echo $this->Form->hidden('year', array('value' => $year));
				echo $this->Form->hidden('get_path', array('value' => 1));
				echo $this->Form->hidden('return', array('value' => Router::url(null, true) . $queryUpdate, 'class' => 'sendTimesheetReturn'));
				?>
				<p class="alert-message text-center request-message"><?php __('Send timesheet?');?></p>
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right" id="submitSent">
						<span><?php __('Send');?></span>
					</button>
					<a class="btn-form-action btn-cancel" id="submitCancel" href="javascript:void(0);" onclick="cancel_popup(this);"><?php __('Cancel');?></a>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<div class="wd-full-popup validate-timesheet-confirm-popup" id="validate-timesheet-confirm-popup" style="display: none;">
	<div class="wd-popup-inner">
		<div class="alert-popup loading-mark wd-popup-container wd-widget">
			<div class="popup-title">
				<h3 class="title"><?php __('Reject Activity request?'); ?></h3>
				<a href="javascript:void(0);" class="popup-close-btn" onclick="cancel_popup(this);"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
				<?php
				echo $this->Form->create('validateTimesheet', array(
					'type' => 'post',
					'id' => 'validateTimesheetForm',
					'escape' => false,
					'url' => array(
						'controller' => 'activity_forecasts',
						'action' => 'response',
						'?' => array(
							'profit' => $profit,
							'week' => $week,
							'year' => $year,
						),
					),
				));
				echo $this->Form->hidden('id', array('value' => 1, 'name' => 'data[id]['.$idEmployee.']'));
				echo $this->Form->hidden('validated', array('value' => 0, 'name' => 'data[validated]'));
				echo $this->Form->hidden('return', array('name' => 'data[return]','value' => Router::url(null, true) . $queryUpdate, 'class' => 'sendTimesheetReturn'));
				?>
				<p class="alert-message text-center form-message request-message"><?php __('Reject Activity request?'); ?></p>
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right" id="submitValidated">
						<span><?php __('OK');?></span>
					</button>
					<a class="btn-form-action btn-cancel" id="cancelValidated" href="javascript:void(0);" onclick="cancel_popup(this);"><?php __('Cancel');?></a>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<div class="wd-full-popup autofill-confirm-popup" id="autofill-confirm-popup" style="display: none;">
	<div class="wd-popup-inner">
		<div class="alert-popup loading-mark wd-popup-container wd-widget">
			<div class="popup-title">
				<h3 class="title"><?php __('Fill automaticaly'); ?></h3>
				<a href="javascript:void(0);" class="popup-close-btn" onclick="cancel_popup(this);"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
				<form class="autofill-form">
				<?php echo $this->Form->hidden('fill-week', array('value' => $auto_fill_week, 'name' => 'filled'));?>
				<p class="alert-message text-center form-message request-message"><?php __('The timesheet will be empty, if you automatically fill in the timesheet'); ?></p>
				<div class="wd-submit">
					<a class="btn-form-action btn-ok btn-right" id="confirm-auto-fill-week">
						<span><?php __('OK');?></span>
					</a>
					<a class="btn-form-action btn-cancel" href="javascript:void(0);" onclick="cancel_popup(this);"><?php __('Cancel');?></a>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="wd-full-popup autofill-favourite-confirm-popup" id="autofill-favourite-confirm-popup" style="display: none;">
	<div class="wd-popup-inner">
		<div class="alert-popup loading-mark wd-popup-container wd-widget">
			<div class="popup-title">
				<h3 class="title"><?php __('Automatically fill favourite tasks'); ?></h3>
				<a href="javascript:void(0);" class="popup-close-btn" onclick="cancel_popup(this);"></a>
			</div>
			<div class="template-popup-content wd-popup-content">
				<form class="autofill-form">
				<?php echo $this->Form->hidden('fill-favourite', array('value' => $auto_fill_favourite, 'name' => 'fill_favourite'));?>
				<p class="alert-message text-center form-message request-message"><?php __('The timesheet will be empty, if you automatically fill in the timesheet'); ?></p>
				<div class="wd-submit">
					<a class="btn-form-action btn-ok btn-right" id="confirm-auto-fill-favourite">
						<span><?php __('OK');?></span>
					</a>
					<a class="btn-form-action btn-cancel" href="javascript:void(0);" onclick="cancel_popup(this);"><?php __('Cancel');?></a>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var contextCopyForecast = {};
	$('#date-range-picker-display').on('click', function(){
		$('#date-range-picker').datepicker('show');
	});
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
			var type = 'week';
			var date = $(this).datepicker('getDate');
			$('#date-range-picker').trigger('change');
			var curStart = $('#nct-start-date').datepicker('getDate'),
				curEnd = $('#nct-end-date').datepicker('getDate');
			if( type == 'week' ){
				startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);  //select monday
				endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 5);    //select friday
			} else if(type == 'month'){
				startDate = new Date(date.getFullYear(), date.getMonth(), 1);  //select first day
				endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);  //select last day
			}
			var start = dateString(startDate, 'dd M.');
			var end = dateString(endDate, 'dd M. yy');
			var week = $.datepicker.iso8601Week(startDate);
			var year = getYearFromWeekNumber(startDate, week);
			// selectedDate(start, end);
			setTextDatePicker(start, end, week);
			$('#RequestWeek').val(week).trigger('change');
			$('#RequestYear').val(year).trigger('change');
			loadDataTimeshet(week, year, true);
			
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
	// $("#RequestYear").on('change', function(){   
        // $("#activity_request_form").submit();
    // });
	function selectCurrentRange(){
        $('#date-range-picker').find('.ui-datepicker-current-day').addClass('ui-state-highlight');
    }
	function dateString(date, format){
        if( !format )format = 'yy-mm-dd';
        return $.datepicker.formatDate(format, date);
    }
	$(".copy-forecast").live('click',function(){
		if( !$this.canModified ) return;
		if(!$(this).hasClass('loaded')){
			var _this = $(this);
			_this.addClass('loading');
			$.ajax({
				url: '/activity_forecasts_preview/getDataCopyForecast/',
				type: 'get',
				dataType: 'json',
				data: {
					idEmp: '<?php echo $employee['id'];?>',
					idPc: '<?php echo $profitCenter['ProfitCenter']['id'];?>',
					start: startdate,
					end: enddate,
					
				},
				success: function(response){
					contextCopyForecast = response;
					$('#content_copy_forecast').show();
					render_html = renderTasksCopyForecast(response);
					$('#add_new_task').addClass('active'); 
					$('#content_copy_forecast').append(render_html);
					_height_layout = $('.wd-layout').height();
					$('#content_copy_forecast').find('ul.task-copy').height(_height_layout - 95);
				},
				complete: function(){
					_this.addClass('loaded');
					_this.removeClass('loading');
					
				},
				error: function(){
					
				},
			});
		}else{

			$('#content_copy_forecast').toggle();
			$('#add_new_task').toggleClass('active');
		}
	});
	
	function renderItemTaskCopy(value, projectPhases, projectParts, list_task_added){
		var added = ( $.inArray( parseInt(value.task_id), list_task_added) != -1);
		var canAdd = !added;
		var _class = (value.favourite ? ' favourite' : ' non_favourite') + (canAdd ? ' selectable': ' unselectable' );
		item_task = '';
		if(value){
			item_task +='<li class="task_item '+ _class +'" data-task_id = '+ value['task_id'] +'>';
			item_task +='<a href="javascript:void(0)" onclick=updateTaskFavourite('+ value['task_id'] +',"toggle",true) class="toggle-favourite task-favour"><?php echo $svg_icons['star']; ?></a>';
			item_task +='<div class="task-content"><a href="javascript:void(0)" class="name" onclick="addTaskCopyToTable(this,'+ value['task_id'] +')" title="'+value['task_title'] +'">'+ value['task_title'] +'</a>';
				
			if(value['part_name']) item_task +='<span class="task-part">'+ value['part_name'] +'</span>';
			if(value['phase_name']) item_task +='<span class="task-phase">'+ value['phase_name'] +'</span></div>';
			
			item_task +='<a href="javascript:void(0)"  onclick = getCommentTask("task",'+ value['task_id'] +') id="comment-task" class="no-comment task-comment"><?php echo $svg_icons['message']; ?></a></li>';
		}
		return item_task;
	}
	function renderTasksCopyForecast(datas){
		var _html = '';
		var task_added = SlickGridCustom.getInstance().getData().getItems();
		var list_task_added = [];
		$.each(task_added, function( i, item){
			list_task_added.push( parseInt(item.task_id) );
		});
		if(datas){
			projectPhases = datas['projectPhases'];
			projectParts = datas['projectParts'];
			results = datas['results'];
			pcTask = '';
			empTask = '';
			if(results){
				$.each(results, function( i, item){
					if(item['is_profit_center'] == 1){
						pcTask += renderItemTaskCopy(item, projectPhases, projectParts, list_task_added);
					}else{
						empTask += renderItemTaskCopy(item, projectPhases, projectParts, list_task_added);
					}
				});
			}
		}
		_html += '<div class="wd-copy-forecast">';
		_html += '<div class="menu-col wd-pc-task"><div class="copy-search-task"><span class="btn-search"><?php echo $svg_icons['search']; ?></span>';
		_html += '<input type="text" rel="no-history" onkeyup="searchTask(this);" placeholder="<?php __('Search');?>"></div><div class="title"><a class="filter-favour"><?php echo $svg_icons['star']; ?></a><span><?php echo __('Assigned to my team', true); ?></span></div>';
		_html += '<ul class="task-copy">'+ pcTask +'</ul></div>';
		_html += '<div class="menu-col wd-employee-task">';
		_html += '<div class="copy-search-task"><span class="btn-search"><?php echo $svg_icons['search']; ?></span>';
		_html += '<input type="text" onkeyup="searchTask(this);" rel="no-history" placeholder="<?php __('Search');?>"></div>';
		_html += '<div class="title"><a class="filter-favour"><?php echo $svg_icons['star']; ?></a><span><?php echo __('My tasks', true); ?></span></div>';
		_html += '<ul class="task-copy">'+ empTask +'</ul></div></div>';
		return _html;
	}
	$('body').on('click', function(e){
		var is_content = ($(e.target).hasClass('content_copy_forecast') || $('.content_copy_forecast').find(e.target).length);
		var is_button = ($(e.target).hasClass('copy-forecast') || $('.copy-forecast').find(e.target).length);
		var is_visible = $('#content_copy_forecast:visible').length;
		if((!(is_content || is_button)) && is_visible){
			$('#content_copy_forecast').hide();
			$('#add_new_task').removeClass('active');
		 }
	});
</script>

<script type="text/javascript">

	// Huynh
	$(document).ready(set_slick_table_height);
	function set_slick_table_height(){
		var wdTable = $('.wd-table');
		if( !wdTable.length) return;
		if( wdTable.length){
			var heightTable = $(window).height() - wdTable.offset().top - parseInt($('.wd-tab:first').css('margin-bottom')) - 40; // 40: .gs-custom-total
			wdTable.css({
				height: heightTable,
			});
			if( typeof gridControl != 'undefined') gridControl.resizeCanvas();
			var _table = $('#activity-request');
			var _panel = _table.find('.slick-pane.slick-pane-top.slick-pane-left');
			if( _panel.length){
				var _width = _panel.find('.slick-row:first').width();
				var custom_header_element = _table.find('.gs-custom-total')
				custom_header_element.width(_width);
			}
			wdTable.css({
				height: heightTable + 40, // 40: .gs-custom-total
			});			
		}
	}
	set_slick_table_height();
	var contextMenuData = {};
	var $this = SlickGridCustom,
        managerHour  = <?php echo json_encode($managerHour); ?>,
        capacity = <?php echo json_encode($capacity); ?>,
        holidays = <?php echo json_encode($holidays); ?>,
        canRequestAbsence = <?php echo json_encode($canRequestAbsence); ?>,
        companyAbsences = <?php echo json_encode(!empty($companyAbsences) ? $companyAbsences : array()); ?>,
        display_absence = <?php echo  json_encode($display_absence);?>,
        listWorkingDays = <?php echo  json_encode($listWorkingDays);?>,
        daysInWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
		sendTimesheetPartially = <?php echo json_encode($sendTimesheetPartially);?>,
        fillMoreThanCapacity = <?php echo json_encode($fillMoreThanCapacity);?>,
        showActivityForecastComment = <?php echo json_encode($showActivityForecastComment);?>,
        isMulti = <?php echo json_encode($isMulti);?>,
        ratio = <?php echo $ratio;?>,
		// select_project_without_task = < ?php echo json_encode($select_project_without_task); ?>,
		avg_date = <?php echo json_encode($avg); ?>,
		role = <?php echo json_encode($role); ?>;
		week = <?php echo $week;?>,
		year = <?php echo $year;?>,
		id = <?php echo $idEmployee;?>,
		profit = <?php echo $profit;?>,
		startdate = <?php echo $startdate;?>,
		enddate = <?php echo $enddate;?>,
		has_comment = false,
		auto_fill_week = <?php echo $auto_fill_week;?>;
		
	var loginEmployeeID = <?php echo $loginEmployeeID;?>;
	var canManage = <?php echo json_encode($canManage);?>;
	var old_state = {
		timesheet_time: {
			week: week,
			year: year
		}
	}
	var absenceSelected = {};
	if( 'history' in window ){
		window.history.replaceState(old_state, document.title);
	}
	var action_delete = "<?php echo $this->Html->url(array('action' => 'delete_request', '%1$s')) . $queryUpdate; ?>";
	action_delete = '<div class="wd-actions"><a href="' + action_delete + '" onclick="return confirm(\'<?php __('Delete?');?>\');" class="wd-hover-advance-tooltip"></a></div>';
	var DataValidation = {};
	$.extend(Slick.Formatters,{
		taskFavourite: function(row, cell, value, columnDef, dataContext){
			// console.log( dataContext);
			if( dataContext.type != 'ac' ) return '';
			return '<div class="wd-actions ' + (value ? 'favourite' : ' non_favourite') + '"><a href="javascript:void(0)" class="toggle-favourite"><?php echo $svg_icons['star'];?></a></div>';
		},
		taskName: function(row, cell, value, columnDef, dataContext){
			// if( dataContext.type !='ac') return '';
			var _html = '<div class="request-task-name">';
			_html += '<span class="task-name">' + value + '</span>';
			_html += '<ul class="tag-list">';
			if( dataContext.part_name){
				_html += '<li class="part_name">' + dataContext.part_name + '</li>';
			}
			if( dataContext.phase_name){
				_html += '<li class="phase_name">' + dataContext.phase_name + '</li>';
			}
			_html += '</ul>';
			_html += '</div>';
			return _html;
		},
		taskProgress: function(row, cell, value, columnDef, dataContext){
			if( dataContext.type !='ac') return '';
			var pr = parseInt( value );
			var color = ( pr >= 100 ) ? 'red' : 'green';
			var _html = '<div class="task-progress ' + color + '"><div class="progress-number clearfix">';
			_html += '<span class="pr-left">' + pr + '%</span>';
			_html += '<span class="pr-right">' + parseFloat(dataContext.consumed).toFixed(2) + '/' + parseFloat(dataContext.workload) +'</span>';
			_html += '</div>';
			_html += '<div class="pr-bar" data-value="' + pr + '"><p class="pr-bar-act" style="width: ' + pr + '%;"></p></div>';
			_html += '</div>';
			return _html;
		},
		taskComment: function(row, cell, value, columnDef, dataContext){
			if( dataContext.type !='ac') return '';
			return '<div class="wd-actions"><a href="javascript:void(0)" data-task_id = "'+dataContext.task_id +'" id="comment-task" class="' + (value ? 'has-comment' : 'no-comment') + '"><?php echo $svg_icons['message'];?></a></div>';
		},
		taskCapacity: function(row, cell, value, columnDef, dataContext){
			var task_capacity = input_capacity = 0;
			$.each( listWorkingDays, function(date,is_working){
				if( date in dataContext ){
					input_capacity += parseFloat(dataContext[date] || 0);
				}
			});
			return Number(input_capacity);
			// return input_capacity.toFixed(2);
			
		},
		floatVal: function(row, cell, value, columnDef, dataContext){
			if( dataContext.type =='ac'){
				if(value) return parseFloat(value);
				return '';
			}
			if(dataContext.type =='holiday'){
				var _html = '';
				var field = columnDef.field;
				var class_am = (dataContext[field + 'am'] == 1) ? 'has-holiday' : 'no-holiday';
				var val_am = (dataContext[field + 'am'] == 1) ? 0.5*ratio : '';
				var class_pm = (dataContext[field + 'pm'] == 1) ? 'has-holiday' : 'no-holiday';
				var val_pm = (dataContext[field + 'pm'] == 1) ? 0.5*ratio : '';
				_html += '<div class="holiday-detail" data-value="' + value + '">';
				_html += '<div class="holiday-am ' + class_am + '">' + val_am + '</div>';
				_html += '<div class="holiday-pm ' + class_pm + '">' + val_pm + '</div>';
				_html += '</div>';
				return _html;
			}
			if(dataContext.type =='absence'){
				//companyAbsences
				var _html = '';
				var field = columnDef.field;
				var key = field + '_data';
				var _data = {};
				if( dataContext[key] ) _data = dataContext[key];
				_html += '<div class="holiday-detail" data-value="' + value + '">';
				$.each(['am', 'pm'], function(i, p){
					key = 'absence_' + p;
					var absence = parseInt( (_data && _data[key]) || false );
					// console.log( absence);
					var _class = ['holiday-' + p];
					_class.push( absence ? 'has-absence' : 'no-absence');
					var _title = absence ? companyAbsences[absence]['name'] : '';
					_title = _title ? ('title="' + _title + '"') : '';
					// var _type = absence ? companyAbsences[absence]['type'] : '';
					// _type = _type ? _type : _title;
					var _val = absence ? 0.5*ratio : '';
					var hasHoliday = (holidays[field] && holidays[field][p] && holidays[field][p] == '1');
					var is_working = (columnDef.calcTotal || 0);
					_class.push( hasHoliday ? 'has-holiday' : 'no-holiday');
					_class.push( (hasHoliday || !is_working) ? 'absence_unselectable' : 'absence_selectable');
					_class.push( absence ? _data['response_' + p] : '');
					_html += '<div class="' + _class.join(' ') + '" ' + _title + ' data-date="' + field + '" data-type="' + p + '"><span class="absence-type">' + _val + '</span></div>';
				});
				_html += '</div>';
				return _html;
			}
			return '';
		},
		action: function(row, cell, value, columnDef, dataContext){
			if( $this.canModified && dataContext.type =='ac')
				return Slick.Formatters.HTMLData(row, cell, $this.t(action_delete, dataContext.task_id), columnDef, dataContext);
			return '';
		}
		
	});
	// Editors...
	$.extend(Slick.Editors,{
		forecastValue : function(args){
			// console.log( args);
			if( args.item.readonly == 'true') return false;
			$.extend(this, new Slick.Editors.textBox(args));
			this.input.attr('maxlength' , 7).keypress(function(e){
				var key = e.keyCode ? e.keyCode : e.which;
				if(!key || key == 8 || key == 13 || e.ctrlKey || e.shiftKey){
					return;
				}
				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
				var _val = parseFloat(val, 10);
				if(!(val != '0' && val == '-' || val == '+') && (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val) || !(_val >= -365 && _val <= 365))){
					e.preventDefault();
					return false;
				}
			});
			this.focus();
		}
	});	
	DataValidation.forecastValue = function(value, args){
		/* only edit task value */
		if( args.item.type != 'ac') return false;
		if(managerHour){
			value = value.split(':');
			value = parseInt(value[0]) * 60 + parseInt(value[1]);
			var _value = parseInt(value);
		} else {
			var _value = parseFloat(value);
		}
		var value = 0;
		var _checkAbAndHl = true;
		var nameOffDay = '';
		$.each(args.grid.getData().getItems() , function(){
			if(this[args.column.field] && (!args.item || this.id != args.item.id)){
				if(managerHour){
					var valueHour = this[args.column.field];
					valueHour = valueHour.split(':');
					valueHour = parseInt(valueHour[0]) * 60 + parseInt(valueHour[1]);
					value += parseFloat(valueHour);
				} else {
					value += parseFloat(this[args.column.field]);
				}
			}
			if(managerHour){
				var totalHourOfDay = gHour + ':' + gMinute;
				if(this && (this.type == 'holiday' || this.type == 'absence') && this[args.column.field] === totalHourOfDay){
					nameOffDay = this.type;
					_checkAbAndHl = false;
				}
			} else {
				if(this && (this.type == 'holiday' || this.type == 'absence') && this[args.column.field] == 1*ratio){
					nameOffDay = this.type;
					_checkAbAndHl = false;
				}
			}

		});
		otherVal = value;
		value += _value;
		if(managerHour){
			var totalVal = capacity[args.column.field];
			totalVal = totalVal.split(':');
			totalVal = parseInt(totalVal[0]) * 60 + parseInt(totalVal[1]);
			var _valid = value <= parseInt(totalVal);
			var rangeHour = totalVal - otherVal;
			var rangeMinute = rangeHour%60;
			rangeHour = (rangeHour - rangeMinute)/60;
			rangeHour = (rangeHour < 10) ? '0'+rangeHour : rangeHour;
			rangeMinute = (rangeMinute < 10) ? '0'+rangeMinute : rangeMinute;
			var _msg = parseFloat(totalVal - otherVal) == 0 ? $this.t('No more value is allowed') : $this.t('The value must between %1$s and %2$s.' , '00:00', rangeHour + ':' + rangeMinute);
		} else {
			value = parseFloat(value.toFixed(2));
			var _valid = value <= capacity[args.column.field];
			var _msg = parseFloat((capacity[args.column.field] - otherVal).toFixed(2)) == 0 ? $this.t('No more value is allowed') : $this.t('The value must between %1$s and %2$s.' , 0 , (capacity[args.column.field] - otherVal).toFixed(2));
		}
		if(isMulti){
			_valid = true;
		}
		if(fillMoreThanCapacity){
			if(showActivityForecastComment && (value > 1)){
				date_column = args.column.format_date;
				getCommentRequest('date', args.column.field, date_column, args.item.id, args.column.field);
				if(!has_comment){
					has_comment = false;
					return {
						_valid : false,
						message : '',
					};
				}
			}
			_valid = true;
			_checkAbAndHl = true;
		}
		has_comment = false;
		if(_checkAbAndHl == true){
			return {
				valid : _valid,
				message : _msg
			};
		} else {
			return {
				valid : false,
				message : $this.t(nameOffDay)
			};
		}
	};
	function build_list_fields(){
		$this.fields = {
			id          : {defaulValue : 'ac-0-'},
			task_id     : {defaulValue : '', allowEmpty : false},
			last        : {defaulValue : '0'},
			activity    : {defaulValue : '0'},
		};
		if(listWorkingDays){
			$.each(listWorkingDays, function(ind, val){
				$this.fields[ind] = {defaulValue : 0, required : ['task_id']};
			});
		}
	}
	build_list_fields();
	$this.canModified =  <?php echo json_encode( $canModified); ?>;
	$this.i18n = <?php echo json_encode($i18ns); ?>;
	var dataView = <?php echo json_encode($dataView); ?>;
	var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator', 'sorter', 'asyncPostRender')); ?>;
	$this.url =  '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'update_request')) . $queryUpdate; ?>';
	
	/* Header total */
	function updateTotal(grid, cell){
		var columns = grid.getColumns();
		var column = columns[cell];
		calctotal = (column.calcTotal || 0);
		if( column ){
			var _html = '';
			var total = 0;
			var dataView = grid.getData();
			var i = dataView.getLength();
			while(i){
				i--;
				if( column.field in dataView.getItem(i)) total += (parseFloat(dataView.getItem(i)[column.field]) || 0);
			}
			var _date_capacity = capacity[column.field];
			var  _class = 'red';
			if(_date_capacity == total) _class = 'green';
			_html = '<div class="' + _class + '"> <span class="cur_capacity">' + total.toFixed(2) + '</span>/<span class="tt_capacity">' + _date_capacity.toFixed(2) + '</span>  </div>';
			$('#activity-request').find('.gs-custom-total').find('.gs-custom-cell-' + column.field).html(_html);
		}
	}
	function updateTotals(grid){
		if( !grid) grid = SlickGridCustom.getInstance();
		var _table = $('#activity-request');
		var _panel = _table.find('.slick-pane.slick-pane-top.slick-pane-left');
		var columns = grid.getColumns();
		_table.find('.gs-custom-total').remove();
		var _width = _panel.find('.slick-row:first').width();
		var _header_element = $('<div class="ui-state-default slick-headerrow gs-custom-total"></div>');
		_header_element.width(_width);
		// _header_element.insertAfter(_table.find('.slick-pane.slick-pane-top.slick-pane-left').find('.ui-state-default.slick-headerrow'));
		_header_element.insertAfter(_table.find('.slick-pane.slick-pane-header.slick-pane-left'));
		$.each(columns, function(i, col){
			var _class = 'ui-state-default slick-headerrow-column l' + i + ' r' + i + ' gs-custom-cell gs-custom-cell-' + col.field;
			if( col.headerCssClass) {
				if( $.isArray(col.headerCssClass)){
					_class += ' ' + col.headerCssClass.join(' ');
				}else{
					_class += ' ' + col.headerCssClass
				}
			}
			_header_element.append( $('<div class="' + _class + '"></div>'));
			calctotal = (col.calcTotal || 0);
			if( calctotal) updateTotal(grid, i);
		});
		set_slick_table_height();
	}
	$(window).on('resize', function(e){
		set_slick_table_height();
	});
	/* Header total */ 
	$this.onCellChange = function(args) {
		var cell = args.cell;
		var columns = args.grid.getColumns();
		var col = columns[cell];
		var field = col.field;
		if( field in listWorkingDays){
			if(args.item[field] == ''){
				args.item[field] = managerHour ? '00:00' : 0;
			}
		}
		/* Header total */
		updateTotal(args.grid, cell);
		/* Header total */ 
		return true;
	}
	gridControl = $this.init($('#activity-request'),dataView, columns, {
		forceFitColumns : true,
		showHeaderRow: false,
		headerRowHeight: 40,
		rowHeight: 40,
		enableAddRow: false,
		enableAsyncPostRender: true,
	});
	updateTotals(gridControl);
	$('.add_new_task_button, .btn-add-task').on('click', function(){
		if( !$this.canModified ) return;
		if( $('#add_new_task').hasClass('active') ){
			close_task_menu();
		}else{
			show_task_menu();
		}
	});
	$('.btn-refresh-menu').on('click', function(e){
		$(this).addClass('loading');
		contextMenuData = {};
		$(this).removeClass('loading');
	});
	$('#popup-task-menu').on('click', function(e){
		if( $(e.target).hasClass('task-menu-container')){
			close_task_menu();
		}
	});
	function expandFamilies(elm, f_id){
		var _this = $(elm);
		_this.addClass('active').siblings().removeClass('active');
		var col = _this.closest('.menu-col').next();
		var childs = col.find('.menu-item');
		childs.not('.parent-fam-' + f_id).removeClass('child_show');
		childs.filter('.parent-fam-' + f_id).addClass('child_show');
		col = col.next('.menu-col');
		while(col.length){
			col.find('.menu-item').removeClass('child_show').removeClass('active');
			col = col.next('.menu-col');			
		}
	}
	function expandActivities(elm, a_id){
		var _this = $(elm);
		_this.addClass('active').siblings().removeClass('active');
		var col = _this.closest('.menu-col').next();
		var childs = col.find('.menu-item');
		childs.not('.parent-act-' + a_id).removeClass('child_show');
		childs.filter('.parent-act-' + a_id).addClass('child_show');
		col = col.next('.menu-col');
		while(col.length){
			col.find('.menu-item').removeClass('child_show').removeClass('active');
			col = col.next('.menu-col');			
		}
	}
	function renderFamilies(listItems){
		var _html = '';
		$.each(listItems, function( i, v){
			var _class = 'menu-item families families-' + v.id + (v.parent_id ? ' parent-fam-' + v.parent_id : '');
			_html += '<li class="' + _class + '" onmouseover="expandFamilies(this, '+ v.id + ')"><a href="javascript:void(0)" class="name family-name">' + v.name + '</a><span class="svg_icon expand-icon"><?php echo $svg_icons['arrow_right']; ?></span></li>'; 
		});
		return _html;
	}
	function renderActivities(listItems){
		var _html = '';
		$.each(listItems, function( i, v){
			var _class= 'menu-item activities activities-' + v.id + ' parent-fam-' + v.subfamily_id + ' parent-fam-' + v.family_id ;
			_html += '<li class="' + _class + '" onmouseover="expandActivities(this, '+ v.id + ')"><a href="javascript:void(0)" class="name activity-name">' + v.name + '</a><span class="svg_icon expand-icon"><?php echo $svg_icons['arrow_right']; ?></span></li>';
		});
		return _html;
	}
	function renderTasks(listItems, list_task_added){
		var _html = '';
		$.each(listItems, function( i, v){
			var added = ( $.inArray( parseInt(v.id), list_task_added) != -1);
			var canAdd = !added;
			var _class = 'menu-item task task-' + v.id + (v.favourite ? ' favourite' : ' non_favourite') + (canAdd ? ' selectable': ' unselectable' ) + ' parent-act-' + v.activity_id;
			_html += '<li class="' + _class + '"><span class="svg_icon icon-left"><?php echo $svg_icons['ok_no'];?></span><a href="javascript:void(0)" class="name task-name" onclick="menuAddTaskToTable(this, ' + v.id + ')" title="' + v.name + '">' + v.name + '</a> <a href="javascript:void(0)" onclick="updateTaskFavourite(' + v.id + ', \'toggle\', true)" class="svg_icon icon-favourite loading-mark"><?php echo $svg_icons['star'];?> </a></li>';
		});
		return _html;
	}
	function contextMenuBuild(){
		data = contextMenuData;
		var dataOne = {families: []};
		var dataTwo = {
			subfamilies: [],
			activities: []
		};
		var dataThree = {
			activities: [],
			tasks: []
		};
		var dataFour = {tasks: []};
		$.each(data.families, function(id, family){
			if(family.parent_id){
				dataTwo.subfamilies.push(family);
			}else{
				dataOne.families.push(family);
			}
		});
		$.each(data.activities, function(id, activity){
			if(activity.subfamily_id){
				dataThree.activities.push(activity);
			}else{
				dataTwo.activities.push(activity);
			}
		});
		$.each(data.tasks, function(id, task){
			var activity_id = task.activity_id;
			var hasSub = data.activities[activity_id]['subfamily_id'];
			if(hasSub){
				dataFour.tasks.push(task);
			}else{
				dataThree.tasks.push(task);
			}
		});
		var menuData = data.hasSubFamilies ? {dataOne: dataOne, dataTwo: dataTwo, dataThree: dataThree,  dataFour: dataFour} : {dataOne: dataOne, dataTwo: dataTwo, dataThree: dataThree};
		var menu = [];
		var task_added = SlickGridCustom.getInstance().getData().getItems();
		var list_task_added = [];
		$.each(task_added, function( i, item){
			list_task_added.push( parseInt(item.task_id) );
		});
		
		var _html = '';
		$.each( menuData, function(index, _data){
			_html += '<div class="menu-col menu-col-' + index + '">';
			_html += '<div class="col-search"><input name="col-' + index + '-search" value="" onkeyup="searchTask(this);" placeholder="<?php __('Search');?>"/></div>';
			_html += '<ul>';
			$.each( _data, function( type, listItems){
				if( type == 'families' ) _html += renderFamilies(listItems);
				if( type == 'subfamilies' ) _html += renderFamilies(listItems);
				if( type == 'activities' ) _html += renderActivities(listItems);
				if( type == 'tasks' ) _html += renderTasks(listItems, list_task_added);
			});
			_html += '</ul>';
			_html += '</div>';
		});
		$('#popup-task-menu').find('.task-menu-inner').html(_html);
		
	}
	function showFirstMenuItem(){
		$('#popup-task-menu').find('.menu-col').each(function(){
			$(this).find('.menu-item:visible').first().trigger('mouseover');
		});
	}
	/* Task menu */
	function show_task_menu(){
		$('#popup-task-menu').addClass('loading').show();
		$('#add_new_task').addClass('active');
		setTimeout(function(){
			if( !('tasks' in contextMenuData)){
				$.ajax({
					url : <?php echo json_encode($html->url(array( 
						// 'controller' => 'activity_forecasts',
						'action' => 'contextMenu', 
						
					))); ?>,
					data: {
						week: week,
						year: year,
						id: id
						
					},
					cache : false,
					type : 'GET',
					dataType: 'json',
					async: false,
					success: function(data){
						contextMenuData = data;
					}
				});
			}
			contextMenuBuild();
			showFirstMenuItem();
			$('#popup-task-menu').removeClass('loading');
		}, 10);
	}
	function close_task_menu(){
		$('#add_new_task').removeClass('active');
		$('#popup-task-menu').fadeOut(300, function(){
			$('#popup-task-menu').find('.task-menu-inner').empty();
		});
	}
	function searchTask(elm){
		// console.log( 'search');
		var _this = $(elm);
		var val = _this.val().toLowerCase();
		if(val ==''){
			_this.closest('.menu-col').find('ul li').removeClass('search_notmatch');
			return;
		}
		var text_elm = _this.closest('.menu-col').find('ul li a.name');
		$.each( text_elm, function( i, _el){
			var el = $(_el);
			var text = el.text().toLowerCase();
			var is_show = text.match(val);
			if( is_show) el.closest('li').removeClass('search_notmatch');
			else el.closest('li').addClass('search_notmatch');
		});
	}
	function menuAddTaskToTable(elm, task_id){
		var _this = $(elm);
		if( _this.closest('.menu-item').hasClass('unselectable')) return;
		var task_data = contextMenuData['tasks'][task_id];
		var activity = contextMenuData['activities'][task_data.activity_id];
		var subfamily_id = activity.subfamily_id ? activity.subfamily_id : 0;
		var sub_family = subfamily_id ? contextMenuData.families[subfamily_id]['name'] : '';
		var family_id = activity.family_id ? activity.family_id : 0;
		var family = contextMenuData.families[family_id]['name'];
		var grid = SlickGridCustom.getInstance();
		var no = grid.getDataLength();
		var task_consumed = parseFloat(task_data.consumed);
		var task_estimated = parseFloat(task_data.estimated);
		var task_item = {
			MetaData: {
				cssClasses: task_data.favourite ? "favourite" : "non_favourite",
			},
			id: 'ac-0-' + task_data.id,
			'no.': no,
			type: "ac",
			readonly: false,
			task_id: task_data.id,
			task_name: task_data.name,
			part_name: task_data.part_name,
			phase_name: task_data.phase_name,			
			activity_name: activity.name,
			activity_sname: activity.short_name,
			activity_lname: activity.long_name,
			subfamily: sub_family,
			family: family,
			activity: 0,
			favourite: task_data.favourite,
			workload: task_estimated,
			consumed: task_consumed,
			progress: task_estimated ? parseInt((task_consumed / task_estimated )* 100) : 100
		}
		grid.getData().addItem(task_item);
		_this.closest('.menu-item').removeClass('selectable').addClass('unselectable');
		grid.updateRowCount();
		grid.render();
		updateTotals();
		
	}
	function addTaskCopyToTable(elm, task_id){
		var _this = $(elm);
		if( _this.closest('.task_item').hasClass('unselectable')) return;
		var task_data = contextCopyForecast['results'][task_id];
		var activity = contextCopyForecast['activities'][task_data.activity_id];
		var subfamily_id = activity.subfamily_id ? activity.subfamily_id : 0;
		var sub_family = subfamily_id ? contextCopyForecast.families[subfamily_id]['name'] : '';
		var family_id = activity.family_id ? activity.family_id : 0;
		var family = contextCopyForecast.families[family_id]['name'];
		var grid = SlickGridCustom.getInstance();
		var no = grid.getDataLength();
		var task_consumed = task_data.consumed ? parseFloat(task_data.consumed) : 0;
		var task_estimated = task_data.estimated ? parseFloat(task_data.estimated) : 0;
		var task_item = {
			MetaData: {
				cssClasses: task_data.favourite ? "favourite" : "non_favourite",
			},
			id: 'ac-0-' + task_data.task_id,
			'no.': no,
			type: "ac",
			readonly: false,
			task_id: task_data.task_id,
			task_name: task_data.task_title,
			activity_name: activity.name,
			activity_sname: activity.short_name,
			activity_lname: activity.long_name,
			subfamily: sub_family,
			family: family,
			activity: 0,
			favourite: task_data.favourite,
			workload: task_estimated,
			consumed: task_consumed,
			progress: task_estimated ? parseInt((task_consumed / task_estimated )* 100) : 100
		}
		grid.getData().addItem(task_item);
		_this.closest('.menu-item').removeClass('selectable').addClass('unselectable');
		grid.updateRowCount();
		grid.render();
		_this.closest('.task_item').addClass('unselectable');
		updateTotals();
		
	}
	/* END Task menu */
	// disable the default browser's context menu.
	$('.wd-table').on('contextmenu', function (e) {
		return false;
	});
	/* Send / Reject / Validate Timesheet */
	
	/* function */
	
	/* Not yet Check manager by hour */
	function check_capacity_timeshet(){
		var grid = gridControl;
		var data = grid.getData().getItems();
		var result = true;
		var diff = 0;
		$.each(listWorkingDays, function( date, is_working){
			var ts_cap = {};
			if(is_working){
				if( !ts_cap[date]) ts_cap[date] = 0;
				$.each(data, function( i, item){
					if( item[date] ) ts_cap[date] = (parseFloat(ts_cap[date]) + parseFloat(item[date])).toFixed(2);
				});
				if( ts_cap[date] != capacity[date]){
					result = false;
					diff += (capacity[date] - parseFloat(ts_cap[date]));
				}
			}
		});
		return result ? result : diff;
		
	}
	/* event */
	$('#btn-sent-timesheet,#btn-sent').on('click', function(){
		var dialog_tag = '#send-timesheet-confirm-popup';
		var dialog = $(dialog_tag);
		var valid = true;
		if(!sendTimesheetPartially && !isMulti){
			var check_total_capacity = check_capacity_timeshet();
			// console.log('check capacity: ', check_total_capacity);
			valid = (check_total_capacity === true);			
			/* check absence here
			// END check absence here */
		}
		if(valid){
			dialog.find('.request-message').html($this.i18n.send);
			dialog.find('.wd-submit').removeClass('text-center');
			dialog.find('#submitSent').show();
		}else{
			dialog.find('.request-message').html($this.t('declare_days', (check_total_capacity).toFixed(2)));
			dialog.find('.wd-submit').addClass('text-center');
			dialog.find('#submitSent').hide();
		}
		show_full_popup(dialog_tag, {width: 420});
	});
	$('#btn-reject,#btn-reject-timesheet,#btn-validate,#btn-validate-timesheet').on('click', function(){
		var dialog_tag = '#validate-timesheet-confirm-popup';
		var dialog = $(dialog_tag);
		var reject = $(this).is($('#btn-reject')) || $(this).is($('#btn-reject-timesheet'));
		if(reject){
			$('#validateTimesheetValidated').val(0);
			dialog.find('.title').html($this.i18n.reject_confirm);
			dialog.find('.request-message').html($this.i18n.reject_confirm_desc);
		}else{
			$('#validateTimesheetValidated').val(1);
			dialog.find('.title').html($this.i18n.validate_confirm);
			dialog.find('.request-message').html($this.i18n.validate_confirm_desc);
		}
		show_full_popup(dialog_tag, {width: 420});
	});
	
	/* END Send / Reject / Validate Timesheet */
	function asyncInitSelectable(cellNode, row, dataContext, colDef){
		// console.log( cellNode, row, dataContext, colDef);
		if( dataContext.type == "absence"){
			initSelectable( cellNode.closest('.slick-row'));
		}

	}
	var contextMenu = {hide: $.noop};
	var menu = [{}];
	<?php if( $display_absence){ ?>
	/* Absence Manager */
		function list_menu_option(){
			var opt = {};
			$.each( companyAbsences, function( id, abs){
				// var total = abs.total ? abs.total : 'NA';
				var request = abs.request ? abs.request : 0;
				var key = abs.name
				// if( request) key += ' (' + request + '/' + total + ')';
				opt[key] = {
					title: abs.name,
					className: 'add-absence absence-' + abs.id,
					disabled: true,
					onclick: function (imenu, cmenu, e) {
						updateAbsenceRequest(abs.id);
					},
				};
			});
			opt[$this.t('Add a comment') + <?php echo json_encode($svg_icons['message']);?>] = {
				onclick: function (imenu, cmenu, e) {
					showCommentAbsence();
				},
				title: $this.t('Add a comment'),
				className: 'add-comment', 
				disabled: false,
			};
			opt[$this.t('Remove request') + <?php echo json_encode($svg_icons['trash']);?>] = {
				onclick: function (imenu, cmenu, e) {
					updateAbsenceRequest(0);
				},
				title: $this.t('Remove request'),
				className: 'ab-remove remove', 
				disabled: false,
			}
			return opt;
		}
		menu.push(list_menu_option());
		function selectable_set_selected(element, item){
			var _options = $(element).find('.ui-selectee');
			_options.removeClass('ui-selected');
			if( _options.length){
				$.each(_options, function(i, _tag){
					if($(_tag).is($(item))) $(_tag).addClass('ui-selected');
				});
			}
			absenceSelected = selectable_get_selected(element);
		}
		function selectable_get_selected(element, ui){
			// console.log('selectable_get_selected', element, ui);
			var _seleted = $(element).find('.ui-selectee.ui-selected');
			var data_selected = [];
			if( _seleted.length){
				$.each(_seleted, function(i, _tag){
					data_selected.push( {date: $(_tag).data('date'), type: $(_tag).data('type')});
				});
			}
			// console.log(data_selected);
			return data_selected;
		}
		var absenceCommentDialog = function(){
			$('#absence_comment_dialog').dialog({
				position    :'center',
				autoOpen    : false,
				resizable: false,
				// height      : 460,
				modal       : true,
				width		: 520,
				minHeight   : 50,
				title		: $this.t('Add a comment'),
				// open : function(e){
					// var $dialog = $(e.target);
					// $dialog.dialog({open: $.noop});
				// }
			});
			absenceCommentDialog = $.noop;
		}
		absenceCommentDialog();
		function showCommentAbsence(){
			var popup = $('#absence_comment_dialog');
			popup.find('.add-comment').html('<div class="input-add"><textarea class="text-textarea" id="update-absence-comment" onchange="updateCommentAbsence()" placeholder="' + $this.t('Add a comment') + '" cols="30" rows="10" ></textarea></div>');
			popup.dialog('open');
		}
		function updateCommentAbsence(){
			var _input = $('#update-absence-comment');
			var _loading = _input.closest('.loading-mark');
			var popup = $('#absence_comment_dialog');
			var comment_text = _input.val();
			if( !comment_text){
				return;
			}
			//loginEmployeeID;
			var data = {};
			data[id] = {};
			$.each(absenceSelected, function(i,v){
				if( !data[id][v.date]) data[id][v.date] = {};
				data[id][v.date]['date'] = v.date;
				data[id][v.date]['employee_id'] = id;
				data[id][v.date][v.type] = comment_text;
			});
			// console.log( data);
			$.ajax({
				url: '/absence_requests/comment_update',
				type: 'post',
				dataType: 'json',
				data: {
					data : data
				},
				beforeSend: function(){
					_loading.addClass('loading');
				},
				complete: function(){
					_loading.removeClass('loading');
				},
				success: function(response){
					$('#message-place').html( response.message);
					$(window).trigger('resize');
					popup.dialog('close');
					if( flashtimeout) clearTimeout(flashtimeout);
					flashtimeout = setTimeout(function(){
						$('#flashMessage').slideUp(300, function(){
								$(window).trigger('resize');
						});
					}, 3000);
					
				}
				
			});
		}
		function updateAbsenceRequest(absenceID){
			var data = {};
			var _loading = $('#wd-container-main');
			if( absenceSelected) $.each(absenceSelected, function(i,v){
				if( !data[v.date]) data[v.date] = {};
				data[v.date]['date'] = v.date;
				data[v.date]['employee_id'] = id;
				data[v.date][v.type] = true;
				data.request = absenceID;
			});
			$.ajax({
				url: '/absence_requests/update?week=' + week + '&year=' + year + '&id=' + id +  '&profit=' + profit + '&get_path=0',
				type: 'post',
				dataType: 'json',
				data: {
					data : data
				},
				beforeSend: function(){
					_loading.addClass('loading');
				},
				complete: function(){
					_loading.removeClass('loading');
				},
				success: function(response){
					if( !response.message ) response.message = '<div id="flashMessage" class="message success">' + $this.t('saved') + '<a href="#" class="close">x</a> </div>';
					$('#message-place').html( response.message);
					$(window).trigger('resize');
					var _item = gridControl.getData().getItemById('absence');
					// update data
					$.each( absenceSelected, function( i,v){
						var date = v.date;
						var type = v.type;
						var key = date + '_data';
						if( !_item[key]) _item[key] = {};
						_item[key]['absence_' + type] = response[date]['absence_' + type];
						_item[key]['response_' + type] = response[date]['response_' + type];
					}); 
					//update value
					$.each( absenceSelected, function( i,v){
						var date = v.date;
						var key = date + '_data';
						_item[date] = 0;
						if( _item[key]['absence_am'] && _item[key]['absence_am'] != 0 ) _item[date] += ( 0.5*ratio);
						if( _item[key]['absence_pm'] && _item[key]['absence_pm'] != 0 ) _item[date] += ( 0.5*ratio);
					});
					$this.update_after_edit(_item.id, _item);
					updateTotals();
					if( flashtimeout) clearTimeout(flashtimeout);
					flashtimeout = setTimeout(function(){
						$('#flashMessage').slideUp(300, function(){
								$(window).trigger('resize');
						});
					}, 3000);
					
				}
			});
		}
	/* END Absence Manager */
	<?php } ?> 
	
	function initSelectable(element){
		if( !$this.canModified) return;
		// console.log( 'init selectable');
		var _list = $('body').children('.z0g-menu-table');
		if( _list.length ){ _list.remove();}
		element.selectable({
			filter: '.absence_selectable',
			stop: function( event, ui ) {
				// console.log('stop', event, ui);
				absenceSelected = selectable_get_selected(this, ui);
				// console.log(absenceSelected);
				var _last = $(this).find('.ui-selectee.ui-selected').last();
				_last.trigger('contextmenu');
			},
			unselected: function( event, ui ) {
				absenceSelected = {};
				contextMenu.hide();
			}
		});
		element.find('.absence_selectable').contextMenu( menu, {
			theme: 'z0g',
			showTransition:'slideDown',
			hideTransition:'slideUp',
			showSpeed: 200,
			hideSpeed: 200,
			beforeShow: function () {
				// console.log( this);
				contextMenu = this;
				contextMenu.menu.addClass('z0g-menu-table');
				var _target = $(contextMenu.target);
				var _selectable = _target.closest('.slick-row.absence');
				if( !_target.hasClass('ui-selected')){
					selectable_set_selected( _selectable, _target);
				}
				// console.log( absenceSelected);
				list_date = absenceSelected.map(function(v,i){
					return v.date;
				});
				var weight = list_date.length / 2;
				var items = JSON.parse(JSON.stringify(companyAbsences)); // copy without reference
				$.each( list_date, function( i, date){
					if( canRequestAbsence[date]){
						$.each( items, function( id, abs){
							// loai bo cac item khong request duoc
							if(!( id in canRequestAbsence[date])) items[id] = [];
						});
					}else{
						items = {};
						return false; // break
					}
					
				}); 
				// console.log( items);
				$(contextMenu.menu).find('.add-absence').addClass('context-menu-item-disabled');
				if( items){
					$.each(items,function(id, item){
						$(contextMenu.menu).find('.absence-' + item.id).removeClass('context-menu-item-disabled');
					});
					// console.log( menu);
				}
				this.menu.width('200');
			},
			
			
		});
			
	}

</script>

<script type="text/javascript">
	//by QUANNV
	var default_mess = cell_focus_id = column_field = '';
	var getCommentRequest = function(type, date, date_column, cell_id, column_id) {
		save_capacity = false;
		var _html = '';
		var _comment_default = date_column ? $this.t('insert_comment', date_column) : '';
		default_mess = _comment_default;
		cell_focus_id = cell_id;
		column_field = column_id;       
		var popup = $('#template_logs');
		$('.wd-content').addClass('loading');
		_html += ('<div class="comment"><textarea onfocusout="addCommentRequest(\'' + type + '\')" data-date = '+ date +' cols="30" rows="6" id="update-comment">'+ _comment_default +'</textarea></div>');
		$.ajax({
			url: '/activity_forecasts/getCommentRequest',
			type: 'POST',
			data: {
				data:{
					date: date,
					type: type,
				}
			},
			dataType: 'json',
			success: function(data) {
				_html += '<div class="content-logs">';
				if (data) {
					save_capacity = true;
					$.each(data, function(ind, _data) {
						var idEm =  _data['employee_id'],
						comment = _data['comment'],
						created = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
						var avartarImage = '<span data-id="' + idEm + '" title="' + listEmployeeName[idEm]['fullname'] + '"><img src="' + js_avatar( idEm) + '" alt="avatar"></span>';
						_html += '<div class="content"><div class="avatar">'+ avartarImage +'</div><div class="item-content"><p>'+ created +'</p><div class="comment">'+ comment +'</div></div></div>';						
					});
				} else {
					_html += '';
				}
				_html += '</div>';
				$('#content_comment').html(_html);
				var createDialog2 = function(){
					$('#template_logs').dialog({
						position    :'center',
						autoOpen    : false,
						autoHeight  : true,
						modal       : true,
						width       : 500,
						minHeight   : 50,
						open : function(e){
							var $dialog = $(e.target);
							$dialog.dialog({open: $.noop});
						}
					});
					createDialog2 = $.noop;
				}
				createDialog2();
				$("#template_logs").dialog('option',{title: $this.t('Comment(s)')}).dialog('open');
				$('.wd-content').removeClass('loading');
				
			},
			complete: function(data) {
				$('#update-comment').focus().val("").val(_comment_default);
			}
		});
		
	    return save_capacity;
	};
	var getCommentTask = function(type, task_id) {
		var _html = '';       
		var popup = $('#template_logs');
		$('.wd-content').addClass('loading');
		_html += ('<div class="comment"><textarea onfocusout="addCommentRequest(\'' + type + '\')" data-task_id = '+ task_id +' cols="30" rows="6" id="update-comment"></textarea></div>');
		$.ajax({
			url: '/activity_forecasts/getCommentRequest',
			type: 'POST',
			data: {
				data:{
					task_id: task_id,
					type: type,
				}
			},
			dataType: 'json',
			success: function(data) {
				_html += '<div class="content-logs">';
				if (data) {
					$.each(data, function(ind, _data) {
						var idEm =  _data['employee_id'],
						comment = _data['comment'],
						// created = new Date(_data['created'] * 1e3).toISOString().slice(0, 10);
						created = _data['created'];
						var avartarImage = '<span data-id="' + idEm + '" title="' + listEmployeeName[idEm]['fullname'] + '"><img src="' + js_avatar(idEm) + '" alt="avatar"></span>';
						_html += '<div class="content"><div class="avatar">'+ avartarImage +'</div><div class="item-content"><p>'+ created +'</p><div class="comment">'+ comment +'</div></div></div>';						
					});
				} else {
					_html += '';
				}
				_html += '</div>';
				$('#content_comment').html(_html);
				var createDialog2 = function(){
					$('#template_logs').dialog({
						position    :'center',
						autoOpen    : false,
						autoHeight  : true,
						modal       : true,
						width       : 500,
						minHeight   : 50,
						open : function(e){
							var $dialog = $(e.target);
							$dialog.dialog({open: $.noop});
						}
					});
					createDialog2 = $.noop;
				}
				createDialog2();
				$("#template_logs").dialog('option',{title: $this.t('Comment(s)')}).dialog('open');
				$('.wd-content').removeClass('loading');
			},
			complete: function(data) {
				$('#update-comment').focus().val("");
			}
		});
	};
	var data_view, date_row, data_item;
	var addCommentRequest = function(type, task_id){
      var text = $('#update-comment').val(),
        _date = $('#update-comment').data('date');
		task_id = $('#update-comment').data('task_id');
		var cell_value = $('.editor-text').val();
        if($.trim(text) != default_mess && $.trim(text) != ''){
            var _html = '';
			$('#update-comment').closest('#content_comment').addClass('loading');
            $.ajax({
                url: '/activity_forecasts/addCommentRequest/',
                type: 'POST',
                data: {
                    data:{
                        date: _date,
                        comment: text,
						type: type,
						task_id: task_id,
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data){
                        var idEm =  data['employee_id'],
						comment = data['comment'],
						created = data['created'];
						var avartarImage = '<span data-id="' + idEm + '" title="' + listEmployeeName[idEm]['fullname'] + '"><img src="' + js_avatar(idEm) + '" alt="avatar"></span>';
						_html += '<div class="content"><div class="avatar">'+ avartarImage +'</div><div class="item-content"><p>'+ created +'</p><div class="comment">'+ comment +'</div></div></div>';	
                        $('#content_comment .content-logs').append(_html).scrollTop($('#content_comment .content-logs').height());
                        $('#update-comment').val("");
						has_comment = true;
					}
                },
				complete: function() {
					var dataView = gridControl.getDataView();
					var row = dataView.getRowById(cell_focus_id);
					var this_item = dataView.getItem(row);
					if ( this_item){
						id_att = '#'+this_item.id;
						$('.cell-value[data-field="' + column_field + '"]').click();
						$(id_att).find('.editor-text').val(cell_value);
						$('.slick-cell:first').click();
					}
					$('#update-comment').closest('#content_comment').removeClass('loading');
					has_comment = false;
				}
            });
			
        }
    }
	$('#open-popup-message').click(function(){
		date_column = 0;
		getCommentRequest('week',avg_date, date_column);
	});
	$('#switch-autofill').click(function(){
		employeeLogged = <?php echo json_encode($employee_info['Employee']['id']);?>;
		currentEmployee = <?php echo json_encode($idEmployee); ?>;
		// console.log(employeeLogged, currentEmployee);
		if(employeeLogged != currentEmployee) return;
		var dialog_tag = '#autofill-confirm-popup';
		show_full_popup(dialog_tag, {width: 420});
	});	
	$('.copy-favourite').click(function(){
		employeeLogged = <?php echo json_encode($employee_info['Employee']['id']);?>;
		currentEmployee = <?php echo json_encode($idEmployee); ?>;
		// console.log(employeeLogged, currentEmployee);
		if(employeeLogged != currentEmployee) return;
		var dialog_tag = '#autofill-favourite-confirm-popup';
		show_full_popup(dialog_tag, {width: 420});
	});	
	$('#confirm-auto-fill-week').click(function(){
		employeeLogged = <?php echo json_encode($employee_info['Employee']['id']);?>;
		currentEmployee = <?php echo json_encode($idEmployee); ?>;
		// console.log(week, year, id, loginEmployeeID);
		// $isAutoFillWeek = true;
		fill_week = $('#fill-week').val();
		loadDataTimeshet(week, year, true, fill_week);
		
	});
	$('#confirm-auto-fill-favourite').click(function(){
		employeeLogged = <?php echo json_encode($employee_info['Employee']['id']);?>;
		currentEmployee = <?php echo json_encode($idEmployee); ?>;
		fill_favourite = $('#fill-favourite').val();
		loadDataTimeshet(week, year, true, null, fill_favourite);
		
	});
	gridControl.onClick.subscribe(function(e, args) {
		var _columns = args.grid.getColumns();
		var _field = _columns[args.cell]['field'];
		if(_field == "task_comment"){
			var item = args.grid.getData().getItem(args.row);
			if( item.type=="ac"){
				getCommentTask('task', item.task_id);
			}
		}
		if(_field == "favourite"){
			var item = args.grid.getData().getItem(args.row);
			if( item.type !="ac"){ return;}
			var new_fav = (item.favourite == 0) ? 1 : 0;
			item.favourite = new_fav;
			if(new_fav){
				item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/non_favourite/g, 'favourite');
			}else{
				item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/favourite/g, 'non_favourite');
			}
			$this.update_after_edit(item.id, item);
			updateTaskFavourite(item.task_id, new_fav, false);			
		}
	});
	gridControl.onHeaderClick.subscribe(function(e, args) {
		var grid = args.grid;
		var column = args.column;
		if( $(e.target).hasClass('.comment-day') || $('.wd-table').find('.slick-header-column .comment-day').find($(e.target)).length){
			date = args.column.field;
			date_column = args.column.format_date;
			getCommentRequest('date',date , date_column);
		}
		if( column.field == "task_comment" || column.field == "action"){
			if(column.field == "task_comment"){
				week--;
				if( week == 0){
					week = 52;
					year--;
				}
			}else{
				week++;
				if( week == 53){
					week = 1;
					year++;
				}
			}
			loadDataTimeshet(week, year, true);
		}
	
			
	});
	function setTextDatePicker(startPicker, endPicker, week){
		var text_date = $this.t('picker_text', week, startPicker, endPicker);
		$('#date-range-picker').datepicker('setDate', dateString((new Date(startdate * 1000)), 'dd-mm-yy'));
		$('#date-range-picker-display').val(text_date);
	}
	function loadDataTimeshet(_week, _year, isPushState, fill_week = null, fill_favourite = null){
		if(_week) week = _week;
		if(_year) year = _year;
		_fn_handler = (fill_week == 0 || fill_week == 1) ? 'auto_fill_week' : 'request';
		if(fill_favourite == 0 || fill_favourite == 1){
			_fn_handler = 'auto_fill_favourite'
		}
		var _url = '/activity_forecasts_preview/'+ _fn_handler +'?week=' + week + '&year=' + year + '&id=' + id;
		if(_fn_handler == 'auto_fill_week'){
			_url += '&filled='+fill_week;
		}else if(_fn_handler == 'auto_fill_favourite'){
			_url += '&fill_favourite='+fill_favourite;
		}
		if(!( 'history' in window && typeof history.pushState == 'function')){
			window.location = _url;
			return;
		}
		contextMenuData = {};
		$(".copy-forecast").removeClass('loaded');
		has_comment = false;
		$.ajax({
			url: _url,
			type: 'get',
			dataType : 'json',
			beforeSend: function(){
				$('#wd-container-main').addClass('loading');
			},
			complete: function(){
				$('#wd-container-main').removeClass('loading');
			},
			success: function(data){
				var statusAct = data.timesheet.statusAct;
				var requestStatus = data.timesheet.requestStatus;
				listWorkingDays = data.listWorkingDays;
				canRequestAbsence = data.canRequestAbsence;
				capacity = data.capacity;
				dataView = data.dataView;
				_columns = data.columns;
				startdate = data.timesheet.startdate;
				enddate = data.timesheet.enddate;
				avg_date = data.timesheet.avg_date;
				$this.canModified = data.timesheet.canModified;
				var queryUpdate = data.timesheet.queryUpdate;
				// console.log(startDate);
				
				// set datetime picker
				var startPicker = dateString((new Date(startdate * 1000)), 'dd M.');
				var endPicker = dateString((new Date(enddate * 1000)), 'dd M. yy');
				setTextDatePicker(startPicker, endPicker, week);
				
				$this.url = '<?php echo $html->url(array('controller' => 'activity_forecasts', 'action' => 'update_request')) ?>' +  queryUpdate;
				
				/* Update Form */
				var wdtitle = $('.wd-title');
				wdtitle.find('.manager-status').find('.status-info').html(statusAct);
				switch( requestStatus){ //String not interger
					case '0': //sent
						$('.add_new_task_button, .btn-add-task, .copy-forecast, .copy-favourite, #switch-autofill').hide();
						$('#btn-sent-timesheet,#btn-sent').hide();
						$('#btn-reject-timesheet,#btn-reject').show();
						$('#btn-validate-timesheet,#btn-validate').show();
						break;
					case '2': // Validate
						$('.add_new_task_button, .btn-add-task, .copy-forecast, .copy-favourite, #switch-autofill').hide();
						$('#btn-sent-timesheet,#btn-sent').hide();
						$('#btn-reject-timesheet,#btn-reject').show();
						$('#btn-validate-timesheet,#btn-validate').hide();
						break;
					case '1': // Reject
					case '-1': // In Progress
					default:
						$('.add_new_task_button, .btn-add-task, .copy-forecast, .copy-favourite, #switch-autofill').show();
						$('#btn-sent-timesheet,#btn-sent').show();
						$('#btn-reject-timesheet,#btn-reject').hide();
						$('#btn-validate-timesheet,#btn-validate').hide();
						break;
				}
				$('#sendTimesheetWeek').val(week);
				$('#sendTimesheetYear').val(year);
				$('#sendTimesheetReturn, #validateTimesheetReturn').val('<?php echo Router::url(null, true);?>' + queryUpdate);
				$('.validateTimesheetForm').prop('action','/activity_forecasts/response?profit=' + profit + '&week=' + week + '&year=' + year);
				/* END Update Form */
				
				/* Update State */
				if( isPushState||0){
					window.history.pushState({timesheet_time: {week: week, year: year}}, document.title, '<?php echo $html->url(array('action' => 'request'));?>' + queryUpdate);
				}
				/* END Update State */
				
				/* Update Timesheet */
				var sf_key = ['editor', 'formatter', 'validator', 'sorter'];
				columns = [];
				$.each(_columns, function(ind, _col){
					var col = {};
					$.each(_col, function(key, val){
						if( $.inArray(key,sf_key) != -1 && val){
							val = val.split('.');
							var x = 0;
							$.each(val, function(i,k){
								if( !x) x = window[k];
								else x = x[k];
							});
							col[key] = x;
						}else{
							col[key] = val;
						}
					}); 
					columns[ind] = col;
				});
				gridControl.setColumns(columns);
				gridControl.getData().setItems(dataView);
				gridControl.invalidate();
				gridControl.render();
				updateTotals();
				build_list_fields();
				initSelectable($('.slick-row.absence'));
				/* END Update Timesheet */
				
				if(fill_week == 0 || fill_week == 1){
					_val_fill = (fill_week == 1) ? 0 : 1;
					// console.log(fill_week, _val_fill);
					$('#fill-week').val(_val_fill).trigger('change');
					if(_val_fill){
						$('#fill-favourite').val(0).trigger('change');
						$('.copy-favourite').removeClass('filled');
						$('#switch-autofill').find('.wd-autofill').addClass('filled');
					}else{
						$('#switch-autofill').find('.wd-autofill').removeClass('filled');
						$('#fill-favourite').val(0).trigger('change');
						$('.copy-favourite').removeClass('filled');
					}
				}
				if(fill_favourite == 0 || fill_favourite == 1){
					_val_fill = (fill_favourite == 1) ? 0 : 1;
					$('#fill-favourite').val(_val_fill).trigger('change');
					if(_val_fill){
						$('.copy-favourite').addClass('filled');
						$('#fill-week').val(0).trigger('change');
						$('#switch-autofill').find('.wd-autofill').removeClass('filled');
					}else{
						$('.copy-favourite').removeClass('filled');
						$('#fill-week').val(0).trigger('change');
						$('#switch-autofill').find('.wd-autofill').removeClass('filled');
					}
				}
				cancel_popup('#autofill-confirm-popup');
				cancel_popup('#confirm-auto-fill-favourite');
			}
		});
	}
	$('#message-place').on('click', '.close', function(e){
		e.preventDefault();
		if( flashtimeout) clearTimeout(flashtimeout);
		$('#flashMessage').slideUp(300, function(){
				$(window).trigger('resize');
		});
	});
	$(window).on('popstate', function(e){
		// console.log('popstate', e, e.originalEvent.state);
		e.preventDefault();
		var state = e.originalEvent.state;
		if( !state) state = old_state;
		if( 'timesheet_time' in state){
			loadDataTimeshet(state.timesheet_time.week, state.timesheet_time.year, false);
		}
	});
	//EXPAND TREE
    $(document).keyup(function(e) {
        if (window.event)
        {
            var value = window.event.keyCode;
        }
        else
            var value=e.which;
        if (value == 27) { collapseScreen(); }
    });
    function collapseScreen() {
        $('#expand-btn').show();
        $('#wd-container-main').removeClass('fullScreen');
		$('#btn-collapse').hide();
        $(window).trigger('resize');
    }
    function expandScreen() {
        $('#wd-container-main').addClass('fullScreen');
        $('#expand-btn').hide();
        $('#btn-collapse').show();
        $(window).trigger('resize');
    }
	function updateTaskFavourite(activity_task_id, is_favourite, update_to_grid){
		if( !activity_task_id) return;
		update_to_grid = update_to_grid || false;
		is_favourite = is_favourite || 0;
		$.ajax({
			url: <?php echo json_encode($this->Html->url(array('action' => 'update_task_favourite')));?>,
			type: 'post',
			dataType: 'json',
			data: {
				data: {
					task_id: activity_task_id,
					favourite: is_favourite
				}
			},
			beforeSend: function(){
				$('#popup-task-menu').find('.menu-item.task-' + activity_task_id).find('.icon-favourite').addClass('loading');
			},
			success: function(data){
				if( data.result == "failed"){
					$('#popup-task-menu').find('.menu-item.task-' + activity_task_id).find('.icon-favourite').removeClass('loading').addClass('error');
					window.location.reload();
					return;
				}
				var favourite = parseInt(data.data.ProjectTaskFavourite.favourite);
				var task_id = data.data.activity_task_id;
				if('tasks' in contextMenuData ){
					
					if( task_id in contextMenuData.tasks ){
						
						contextMenuData.tasks[task_id]['favourite'] = favourite;
						$('#popup-task-menu').find('.menu-item.task-' + task_id).removeClass(favourite ? 'non_favourite' : 'favourite').addClass(favourite ? 'favourite' : 'non_favourite');
					}
				}
				if( update_to_grid){
					// Chưa hoàn thành
					var id = 'ac-0-' + task_id;
					item = gridControl.getData().getItemById(id);
					if( item){
						item.favourite = favourite;
						if(favourite){
							item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/non_favourite/g, 'favourite');
						}else{
							item.MetaData.cssClasses = item.MetaData.cssClasses.replace(/favourite/g, 'non_favourite');
						}
						$this.update_after_edit(id, item);
					}
				}
				// update favourite on popup copy
				if(favourite){
					$('#content_copy_forecast').find('.task_item[data-task_id="'+ task_id +'"]').addClass('favourite').removeClass('non_favourite');
				}else{
					$('#content_copy_forecast').find('.task_item[data-task_id="'+ task_id +'"]').addClass('non_favourite').removeClass('favourite');
				}
				
			},
			complete: function(){
				$('#popup-task-menu').find('.menu-item.task-' + activity_task_id).find('.icon-favourite').removeClass('loading');
			},
		});
	}
</script>