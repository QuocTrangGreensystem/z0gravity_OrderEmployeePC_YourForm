<style type="text/css">
table#pupop-task{
	font-size: 13px;
}
#pupop-task th{
	background: url(<?php echo $this->Html->url('/img/front/bg-head-table.png');?>) repeat-x #06427A;
	border-right: 1px solid #185790;
	color: #fff;
	height: 28px;
	vertical-align: middle;
}
#pupop-task tr td{
	border: 1px dotted silver;
	height: 28px;
	color: #000;
	vertical-align: middle;
}
.val{text-align: right;}
.input-task{border: none;outline: 0;text-align: right;}
.error-workload{border:1px solid red !important; }
</style>
<table id="pupop-task">
	<thead>
		<tr>
			<th><?php echo __('Phase');?></th>
			<th><?php echo __('Part');?></th>
			<th><?php echo __('Workload M.D');?></th>
			<th><?php echo __('Consumed M.D');?></th>
		</tr>
	</thead>
	<tbody>
		<input type="hidden" name = "data[AutoTask][task_title]" value="<?php echo $manDay['ProjectBudgetExternal']['name'];?>" />
		<input type="hidden" name = "data[AutoTask][task_asign_to]" value="<?php echo $manDay['ProjectBudgetExternal']['budget_provider_id'];?>" />
		<input type="hidden" name = "data[AutoTask][external_id]" value="<?php echo $manDay['ProjectBudgetExternal']['id'];?>" />
		<input type="hidden" name = "data[AutoTask][project_id]" value="<?php echo $project_id;?>" />
		<input type="hidden" name = "data[AutoTask][id]" value="<?php echo $id;?>" />
		<?php $i=1; foreach($phasePs as $phaseP):?>
		<tr> 

			<td><?php echo $phaseP['ProjectPhase']['name'];?></td>
			<td><?php echo $phaseP['ProjectPart']['title'];?></td>
			<!-- phaseHave: mang chua thong tin du lieu workload/consumed. phaseCheck: mang kiem tra phan tu co thuoc trong danh sach khong -->
			<?php if(($phaseCheck==array())&&($i==1)){?>
				<input type="hidden" name = "data[AutoTaskPhase][val-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhase']['name'];?>" />
				<td class="val"><input type="text" name="data[Provider][val-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" class="input-task edit-task-workload sum-workload-<?php echo $phaseP['ProjectPhasePlan']['id'].'-'.$id;?>"  value="<?php echo $this->Number->format($manDay['ProjectBudgetExternal']['man_day'], array('places' => 2,'before' => '','escape' =>false,'decimals' => ',','thousands' => ''));?>" />
				<input type="hidden" name="data[PhaseSE][start-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhasePlan']['phase_real_start_date'];?>" />
				<input type="hidden" name="data[PhaseSE][end-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhasePlan']['phase_real_end_date'];?>" />
				</td>
			<?php }else{ ?>
				<input type="hidden" name = "data[AutoTaskPhase][val-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhase']['name'];?>" />
				<td class="val"><input type="text"  name="data[Provider][val-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" class="input-task edit-task-workload sum-workload-<?php echo $phaseP['ProjectPhasePlan']['id'].'-'.$id;?>"  value="<?php echo in_array($phaseP['ProjectPhasePlan']['id'], $phaseCheck)?$this->Number->format($phaseHave[$phaseP['ProjectPhasePlan']['id']]['ProjectTask']['estimated'], array('places' => 2,'before' => '','escape' =>false,'decimals' => ',','thousands' => '')):'0,00'; ?>" />
				<input type="hidden" name="data[PhaseSE][start-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhasePlan']['phase_real_start_date'];?>" />
				<input type="hidden" name="data[PhaseSE][end-<?php echo $id.'-'.$phaseP['ProjectPhasePlan']['id'];?>]" value="<?php echo $phaseP['ProjectPhasePlan']['phase_real_end_date'];?>" />
				</td>
			<?php } ?>
				<td class="val"><span class="input-task edit-task-consumed sum-consumed-<?php echo $phaseP['ProjectPhasePlan']['id'];?>"><?php echo in_array($phaseP['ProjectPhasePlan']['id'], $phaseCheck)?$this->Number->format($phaseHave[$phaseP['ProjectPhasePlan']['id']]['ProjectTask']['special_consumed'], array('places' => 2,'before' => '','escape' =>false,'decimals' => ',','thousands' => '')):'0,00'; ?> </span></td>
		</tr>
		<?php $i++; endforeach;?>
		<tr>
			<th colspan="3" class="val sum-total-workload"><span class="sum-all"></span>/<span class="sum-total"><?php echo $this->Number->format($manDay['ProjectBudgetExternal']['man_day'], array('places' => 2,'before' => '','escape' =>false,'decimals' => ',','thousands' => ''));?></span></th>
			<th class="val sum-total-consumed">0,00</th>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
var sum_workload_total = 0;
var sum_consumed_total = 0;
<?php foreach($phasePs as $phaseP):?>
    var tmpworkload = $(".sum-workload-<?php echo $phaseP['ProjectPhasePlan']['id'].'-'.$id;?>").val().toString();
    var tmpconsumed = $(".sum-consumed-<?php echo $phaseP['ProjectPhasePlan']['id'];?>").text().toString();
 	sum_workload_total+= parseFloat(tmpworkload.replace(',','.'));
 	sum_consumed_total+= parseFloat(tmpconsumed.replace(',','.'));
<?php endforeach;?>
sum_workload_total = sum_workload_total.toFixed(2).toString();
sum_consumed_total = sum_consumed_total.toFixed(2).toString();
sum_workload_total = sum_workload_total.replace('.',',');
sum_consumed_total = sum_consumed_total.replace('.',',');
$('.sum-total-consumed').text(sum_consumed_total);
$('.sum-total-workload span.sum-all').text(sum_workload_total);
var checkSubmitWL = $(".sum-all").text().toString().replace(',','.');
var checkSubmitWL = parseFloat(checkSubmitWL.replace('',''));
var checkOnSubmitWL = $(".sum-total").text().toString().replace(',','.');
var checkOnSubmitWL = parseFloat(checkOnSubmitWL.replace('',''));
if(checkSubmitWL.toFixed(2)!=checkOnSubmitWL.toFixed(2)){
	$('#ok_attach_auto_task').hide();
}else{
	$('#ok_attach_auto_task').show();
}
//
$('.edit-task-workload').change(function(){
	var sum_workload_total = 0;
	var sum_consumed_total = 0;
	<?php foreach($phasePs as $phaseP):?>
	    var tmpworkload = $(".sum-workload-<?php echo $phaseP['ProjectPhasePlan']['id'].'-'.$id;?>").val();
	    var tmpconsumed = $(".sum-consumed-<?php echo $phaseP['ProjectPhasePlan']['id'];?>").text();
	 	sum_workload_total+= parseFloat(tmpworkload.replace(',','.'));
	 	sum_consumed_total+= parseFloat(tmpconsumed.replace(',','.'));
	<?php endforeach;?>
	classStr = $(this).attr('class');
	lastClass = classStr.substr(classStr.lastIndexOf(' ') + 1);
	var id = lastClass.split("-");
	var checkWL = parseFloat($(".sum-workload-"+id[2]+"-"+<?php echo $manDay['ProjectBudgetExternal']['id'];?>).val().toString().replace(',','.'));
	var checkCS = parseFloat($(".sum-consumed-"+id[2]).text().toString().replace(',','.'));
	if(checkWL<checkCS){
		$(this).focus();
		$(this).parent().addClass('error-workload');
	}else{
		$(this).parent().removeClass('error-workload');
	}
	sum_workload_total = sum_workload_total.toFixed(2).toString();
	sum_consumed_total = sum_consumed_total.toFixed(2).toString();
	sum_workload_total = sum_workload_total.replace('.',',');
	sum_consumed_total = sum_consumed_total.replace('.',',');
	$('.sum-total-consumed').text(sum_consumed_total);
	$('.sum-total-workload span.sum-all').text(sum_workload_total);
	var checkSubmitWL = $(".sum-all").text().toString().replace(',','.');
	var checkSubmitWL = parseFloat(checkSubmitWL.replace('',''));
	var checkOnSubmitWL = $(".sum-total").text().toString().replace(',','.');
	var checkOnSubmitWL = parseFloat(checkOnSubmitWL.replace('',''));
	if((checkSubmitWL.toFixed(2)!=checkOnSubmitWL.toFixed(2))||($('.edit-task-workload').parent().hasClass('error-workload'))){
		$('#ok_attach_auto_task').hide();
	}else{
		$('#ok_attach_auto_task').show();
	}
	$('.edit-task-workload').keyup(function() {
		if($(this).val()==''){
			$(this).val('0,00');
			var sum_workload_total = 0;
			var sum_consumed_total = 0;
			<?php foreach($phasePs as $phaseP):?>
			    var tmpworkload = $(".sum-workload-<?php echo $phaseP['ProjectPhasePlan']['id'].'-'.$id;?>").val();
			    var tmpconsumed = $(".sum-consumed-<?php echo $phaseP['ProjectPhasePlan']['id'];?>").text();
			 	sum_workload_total+= parseFloat(tmpworkload.replace(',','.'));
			 	sum_consumed_total+= parseFloat(tmpconsumed.replace(',','.'));
			<?php endforeach;?>
			classStr = $(this).attr('class');
			lastClass = classStr.substr(classStr.lastIndexOf(' ') + 1);
			var id = lastClass.split("-");
			var checkWL = parseFloat($(".sum-workload-"+id[2]+"-"+<?php echo $manDay['ProjectBudgetExternal']['id'];?>).val().toString().replace(',','.'));
			var checkCS = parseFloat($(".sum-consumed-"+id[2]).text().toString().replace(',','.'));
			if(checkWL<checkCS){
				$(this).focus();
				$(this).parent().addClass('error-workload');
			}else{
				$(this).parent().removeClass('error-workload');
			}
			sum_workload_total = sum_workload_total.toFixed(2).toString();
			sum_consumed_total = sum_consumed_total.toFixed(2).toString();
			sum_workload_total = sum_workload_total.replace('.',',');
			sum_consumed_total = sum_consumed_total.replace('.',',');
			$('.sum-total-consumed').text(sum_consumed_total);
			$('.sum-total-workload span.sum-all').text(sum_workload_total);
			var checkSubmitWL = $(".sum-all").text().toString().replace(',','.');
			var checkSubmitWL = parseFloat(checkSubmitWL.replace('',''));
			var checkOnSubmitWL = $(".sum-total").text().toString().replace(',','.');
			var checkOnSubmitWL = parseFloat(checkOnSubmitWL.replace('',''));
			if((checkSubmitWL.toFixed(2)!=checkOnSubmitWL.toFixed(2))||($('.edit-task-workload').parent().hasClass('error-workload'))){
				$('#ok_attach_auto_task').hide();
			}else{
				$('#ok_attach_auto_task').show();
			}
		}
	});
});
</script>
