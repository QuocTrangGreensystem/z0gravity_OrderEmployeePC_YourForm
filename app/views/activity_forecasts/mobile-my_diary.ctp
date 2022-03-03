<style>

	.currentWeek {
		margin-left: 10px;
		margin-right: 10px;
	}
	@media (min-width: 768px) {
		.form-inline .form-group {
			margin-bottom: 5px;
		}
	}
	/*color*/
	<?php foreach($constraint as $class => $con): ?>
.table-diary .<?php echo $class ?> {
		background-color: <?php echo $con['color'] ?>;
	}
	<?php endforeach; ?>
</style>
<?php
$am = __('AM', true);
$pm = __('PM', true);
$typeSelect = 'week';
$dayMaps = array(
	'monday' => $_start,
	'tuesday' => $_start + DAY,
	'wednesday' => $_start + (DAY * 2),
	'thursday' => $_start + (DAY * 3),
	'friday' => $_start + (DAY * 4),
	'saturday' => $_start + (DAY * 5),
	'sunday' => $_start + (DAY * 6)
);

?>
<section class="content">
	<div class="row">
		<!-- week naviation -->
		<!-- /navigation -->
		<!-- diary table -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body no-padding">
					<table class="table table-request table-bordered table-diary table-responsive">
						<thead>
							<tr>
								<th><?php echo $employeeName['first_name'] . ' ' . $employeeName['last_name'] ?></th>
								<th width="70%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
<?php
$totalWorkload = 0;
$totalCapacity = 0;

$workloadData = isset($workloads[$employeeName['id']]) ? $workloads[$employeeName['id']] : array();

if($typeSelect == 'week'){
	$countWorkdays = 0;
	if(!empty($workdays)):
		$workdays = array_combine(array_values($dayMaps),array_values($workdays));
		foreach($workdays as $date => $val):
			if(!empty($val) && $val != 0):
				$countWorkdays++;
				$dayMaps[$date] = $date;
				$class = '';
				$text = array();
				$absence = '';
				$capacity = 1;
				$workload = 0;
				if( isset($holidays[$date]) ){
					$class = 'holiday';
					$absence = '<span class="holiday">' . __('Holiday', true) . '</span>';
					$capacity = 0;
				} else {
					//check absence using vacation
					if( isset($workloadData[$date]) ){
						$flag=0;
						foreach ($workloadData[$date] as $data) {
							$vacation = $data['vacation'];
							if($vacation==1)
							{
								$absence = "<span class='validated'>CP (1)</span>";
								$capacity = 0;
							}
							else if($vacation==3)
							{
								$absence = "<span class='validated'>CP (0.5 AM)</span>";
								$capacity = 0.5;
							}
							else if($vacation==5||$vacation==13)
							{
								$absence = "<span class='validated'>CP (0.5 AM)</span>";
								$absence .= "<span class='waiting'>CP (0.5 PM)</span>";
								$capacity = 0.5;
							}
							else if($vacation==7)
							{
								$absence = "<span class='validated'>CP (0.5 PM)</span>";
								$capacity = 0.5;
							}
							else if($vacation==9||$vacation==11)
							{
								$absence = "<span class='validated'>CP (0.5 PM)</span>";
								$absence .= "<span class='waiting'>CP (0.5 AM)</span>";
								$capacity = 0.5;
							}
							else if($vacation==2)
							{
								$absence = "<span class='waiting'>CP (1)</span>";
							}
							else if($vacation==4)
							{
								$absence = "<span class='waiting'>CP (0.5 AM)</span>";
							}
							else if($vacation==6)
							{
								$absence = "<span class='waiting'>CP (0.5 PM)</span>";
							}
							if( $vacation != 1 ){
								if( isset($data['idPr']) )
								{
									$text[] = '<span class="task">' . $data['namePr'] .' <i>(' . $data['workload'] . ')</i></span>';
									$workload += $data['workload'];
								}
								if( isset($data['idAc']) ){
									$text[] = '<span class="task">' . $data['nameAc'] .' <i>(' . $data['workload'] . ')</i></span>';
									$workload += $data['workload'];
									
								}
							}
						}
					}
				}

?>
							<tr data-date="<?php echo $date ?>">
								<th class="date-name"><?php __(date('l', $date)); __(date(' d ', $date)); __(date('M', $date)); ?></th>
								<td class="tasks <?php echo $class ?>"><?php echo $absence ?><?php echo implode('', $text) ?></td>
							</tr>
<?php
				$totalWorkload += $workload;
				$totalCapacity += $capacity;
			endif;
		endforeach;
	endif;
}

$countEmployees = count($employees);
?> 
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /diary table -->
	</div>
</section>
<script>
	
	$('#total-workload').html('<?php echo $totalWorkload ?>');
	$('#total-capacity').html('<?php echo $totalCapacity ?>');
	$(document).ready(function(){
		$('#absence-prev').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-left"></i>');
		$('#absence-next').addClass('btn btn-menu btn-sm').html('<i class="glyphicon glyphicon-arrow-right"></i>');
		$('.currentWeek').html(" ");
		$('.currentWeek').css("margin","0px");
		
	});
</script>