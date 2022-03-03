<?php
/*
if(isset($project_id))
{
	$keyword = 'Project';
	$ID = $project_id;
}
else
{
	$keyword = 'Activity';
	$ID = $activity_id;
}
if(isset($rebuildStaffing) && $rebuildStaffing == 1)
{ ?>
	<div id="flagRebuildStaffing">
	<img style="margin-right:5px;" src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt="building"  />
	<strong style="color:#013d74">Staffing building</strong>
	</div>
	<script type="text/javascript">
	var countRequest = 0;
	var timeInterval = setInterval(function(){
		if(countRequest > 5)
		{
			clearInterval(timeInterval);
		}
		else
		{
			$.ajax({
				data : {data : {projectId : <?php echo $ID; ?>}},
				type : 'POST',
				url : '/project_staffings/checkRebuildStaffing/<?php echo $keyword;?>/<?php echo $ID; ?>',
				success: function(data){
					if(data == 0)
					{
						clearInterval(timeInterval);
						$('#flagRebuildStaffing').remove();
						location.reload(true);
					}
				}
			});
		}
		countRequest++;
	},1500);
	</script>
<?php
}
?>
*/
