
<?php if(isset($profit['id'])):?>
	<?php $query = isset($profit) ? '&profit=' . $profit['id'] : ''; ?>
<?php else: ?>
	<?php $query = ""?>
<?php endif; ?>
<?php 

if (!empty($isManage)) {
    $query = '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
}
if($this->params['controller'] == 'absence_requests' && isset($this->params['url']['st'])){
    $query .= '&st=' . $this->params['url']['st'];
}
//debug(date('d-m-Y', $_end));
//$week = intval(date('W', $_end + DAY + DAY));
//$year = intval(date('Y', $_end + DAY + DAY));


$week = intval(date('W', $_start - (7 * DAY)));
$year = intval(date('Y', $_start - (7 * DAY)));
if ($year < date('Y', $_start) && $week == 1) {
    $year++;
}
?>
<a id="absence-prev" href="<?php echo $this->Html->here . '?week=' . $week . '&year=' . $year . $query; ?>">
    <span><?php __('Prev') ?></span>
</a>
<?php if(($this->params['controller']=='holidays')||($this->params['controller']=='activity_forecasts' && ($this->params['action']=='my_diary' || $this->params['action']=='request' || $this->params['action']=='response' || $this->params['action']=='manages'))):?>
	<span class="currentWeek"><?php echo __(date('d ', $_start)) . __(date('M', $_start)) . __(' to ') . __(date('d ', $_end)) . __(date('M', $_end)) ;?></span>
<?php 
    endif;
$week = intval(date('W', $_end + (7 * DAY)));
$year = intval(date('Y', $_end + (7 * DAY)));
if( $week >= 50 && $week <= 53 ){
	$year = date('Y', $_start);
}
else if ($year < date('Y', $_end) && $week == 1) {
	$year++;
}
?>
<a id="absence-next" href="<?php echo $this->Html->here . '?week=' . $week . '&year=' . $year . $query; ?>">
    <span><?php __('Next') ?></span>
</a>

