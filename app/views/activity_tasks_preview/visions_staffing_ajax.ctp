<?php
$arg = $this->passedArgs;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';

$rows = 0;
$start = $end = 0;
$data = $projectId = $conditions = array();
$showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
$activities = $showType == 2 ? $projects : $activities;
foreach ($activities as $activitie) {
	$dx = array();
	if( $showType == 2){
		$dx = $activitie['Project'];
		$dx['name'] = $activitie['Project']['project_name'];
	}else{
		$dx = $activitie['Activity'];
	}
	$_data = array(
		'name' => $dx['name'],
		'phase' => array(
			'name' => $dx['name'],
			'start' => $dx['start_date'],
			'end' => $dx['end_date'],
			'rstart' => $dx['start_date'],
			'rend' => $dx['end_date'],
			'color' => '#004380'
		)
	);
	if ($_data['phase']['rstart'] > 0) {
		$_start = min($_data['phase']['start'], $_data['phase']['rstart']);
	} else {
		$_start = $_data['phase']['start'];
	}
	if (!$start || ($_start > 0 && $_start < $start)) {
		$start = $_start;
	}
	$_end = max($_data['phase']['end'], $_data['phase']['rend']);
	if (!$end || $_end > $end) {
		$end = $_end;
	}
	$data[] = $_data;
}
$summary = isset($this->params['url']['summary']) ? $this->params['url']['summary'] : false;


$start = !empty($startDateFilter) ? $startDateFilter : $start;
$end = !empty($endDateFilter) ? $endDateFilter : $end;
if (empty($start) || empty($end)) {
	//echo $this->Html->tag('h1', __('No data exist to create Gantt chart', true), array('style' => 'color:red'));
} else {
	$listDays = $dateType == 'week' ? $listWeeks : $listDays;
	$type = array(
		'type' => $dateType, // truyen kieu hien thi date, week, month, year
		'dateTypes' => $listDays // danh sach ngay lam viec tu start date -> end date, lay tu libs
	);
	$this->GanttVsPreview->create($type, $start, $end, array(), false);
	$isCheck=isset($isCheck)?$isCheck:false;
	$newDataStaffings=isset($newDataStaffings)?$newDataStaffings:array();
	$staffings=isset($staffings)?$staffings:false;
	$summary=isset($summary)?$summary:array();
	$showType=isset($showType)?$showType:false;


	if(isset($arrGetUrl['classParent']))
	{
		$_classParent=$arrGetUrl['classParent'];
		$_classParent=str_replace('onload acti','',$_classParent);
		$_classParent=str_replace('trPC','',$_classParent);
		$_classParent=str_replace('gantt-staff','',$_classParent);
		$_classParent.=' tr-'.$arrGetUrl['ItMe'].' p-tr-'.$arrGetUrl['ItMe'];
		$_classPParent=$_classParent.' pp-tr-'.$arrGetUrl['ItMe'];

	}
	else
	{
		$_classParent='';
		$_classPParent='';
	}
	$staffingsTmp = $staffings;
	$staffings = array();
if(($showType == 1 && $isCheck == false) || (($showType == 0 || $showType == 2)&& $isCheck == 1))
	{

		$staffings['companyConfigs']=$companyConfigs;
	}
	$staffings['data']=$staffingsTmp;
	$staffings['dateType']=$dateType;
	$isMultiYear = $arrGetUrl['aEndYear'] - $arrGetUrl['aStartYear'];                  
	$staffings['isMultiYear']=$isMultiYear;   
	// debug($staffings); exit;
	$showType = $showType == 2 ? 0 : $showType;
	echo $this->GanttVsPreview->drawStaffing($staffings, $md, $summary, $showType, $isCheck, $newDataStaffings, $activityType, true ,$_classParent,$_classPParent);
}
if (!empty($start) && !empty($end) && empty($staffings)) {
	//echo $this->Html->tag('h1', __('No data exist to create staffing', true), array('style' => 'color:red'));
}
