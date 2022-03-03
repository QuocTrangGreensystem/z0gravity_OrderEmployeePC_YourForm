<?php foreach($results as $index=>$data)
{
	?>
	<tr>
		<td><?php echo $index;?></td>
		<td><?php echo $data['name'];?></td>
		<td class="valWorkload"><?php echo $data['workload'];?></td>
		<td class="valWorkload <?php echo $data['clsE'];?>"><?php echo $data['staffingE'];?></td>
		<td class="valWorkload <?php echo $data['clsP'];?>"><?php echo $data['staffingP'];?></td>
		<td class="valWorkload <?php echo $data['clsS'];?>"><?php echo $data['staffingS'];?></td>
		<td></td>
		<td></td>
	</tr>
	<?php
} ?>