<?php
$arg = $this->passedArgs;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'month';

$rows = 0;
$start = $end = 0;
$data = $projectId = $conditions = array();

foreach ($activities as $activitie) {
	$_data = array(
		'name' => $activitie['Activity']['name'],
		'phase' => array(
			'name' => $activitie['Activity']['name'],
			'start' => $activitie['Activity']['start_date'],
			'end' => $activitie['Activity']['end_date'],
			'rstart' => $activitie['Activity']['start_date'],
			'rend' => $activitie['Activity']['end_date'],
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

$showType = isset($this->params['url']['type']) ? (int) $this->params['url']['type'] : 0;
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
	$this->GanttVs->create($type, $start, $end, array(), false);
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
	if(($showType == 1 && $isCheck == false) || ($showType == 0 && $isCheck == 1))
	{

		$staffings['companyConfigs']=$companyConfigs;
	}
	$staffings['data']=$staffingsTmp;
	$staffings['dateType']=$dateType;
	echo $this->GanttVs->drawStaffing($staffings, $md, $summary, $showType, $isCheck, $newDataStaffings, $activityType, true ,$_classParent,$_classPParent);
}
if (!empty($start) && !empty($end) && empty($staffings)) {
	//echo $this->Html->tag('h1', __('No data exist to create staffing', true), array('style' => 'color:red'));
}
