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

#table-control {
	padding-top: 5px;
	margin-bottom: 20px !important;
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

#absence tbody td span {
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
	padding: 10px 50px !important;
	background: #f0f0f0;
	box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
	margin: 0 !important;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div id="table-control" class="wd-title">
					<?php echo $this->Form->create(false, array('type' => 'get')) ?>
					<fieldset>
					<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&year=<?php echo $year-1 ?>&get_path=<?php echo $getDataByPath ? 1 : 0 ?>" id="absence-prev">Prev</a>
					<span class="currentWeek"><?php echo $year ?></span>
					<?php if( $year < date('Y') ): ?><a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&year=<?php echo $year+1 ?>&get_path=<?php echo $getDataByPath ? 1 : 0 ?>" id="absence-next">Next</a><?php endif ?>
					<div class="input"><?php echo $this->Form->select('profit', $paths, $profit['id'], array('empty' => false, 'name' => 'profit', 'escape' => false)); ?></div>
					<div class="button">
						<input type="hidden" name="get_path" value="<?php echo $getDataByPath ? 1 : 0 ?>">
						<input type="hidden" name="year" value="<?php echo $year ?>">
						<input type="submit" value="OK">
					</div>
					<a href="javascript:void(0)" id="submit-request-ok-top" class="validate-for-validate validate-for-validate-top" title="Validate"><span>Validate</span></a>
					<a href="javascript:void(0)" id="submit-request-no-top" class="validate-for-reject validate-for-reject-top" title="Reject"><span>Reject</span></a>
					<a href="<?php echo $this->here ?>?profit=<?php echo $profit['id'] ?>&year=<?php echo $year ?>&get_path=1" id="expand-pc-btn" style="float:left; margin-top:-4px; background:none;" class="validate-for-validate validation-for-validate-top"><img src="/img/btn-expand.png" alt=""></a>
					<div class="input"><input type="checkbox" id="check-all" style="width: auto; margin-top: 5px; margin-left: 10px"><label for="check-all" style="float: none; width: auto; line-height: 1"><?php __('Select all') ?></label></div>
					</fieldset>
				</div>
				<div id="message-place">
					<?php
					echo $this->Session->flash();
					?>
				</div>
				
			</div>
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