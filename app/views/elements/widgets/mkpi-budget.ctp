<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><?php echo __d(sprintf($_domain, 'KPI'), 'Budget', true);?></h3>
				</div>
				<div class="box-body">
					<!--label for="cost-control"><?php //__("Cost Control") ?></label-->
					<?php
					// echo $this->Form->input('project_amr_cost_control_id', array('div' => false, 'label' => false,
					// 	'class' => 'selection-plus',
					// 	'name' => 'data[ProjectAmr][project_amr_cost_control_id]',
					// 	'value' => (!empty($this->data['ProjectAmr']['project_amr_cost_control_id'])) ? $this->data['ProjectAmr']['project_amr_cost_control_id'] : "",
					// 	"empty" => __("-- Select --", true),
					// ));
					?>

							<ul class="list-inline">
								<li><input checked="true" style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][cost_control_weather]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
								<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][cost_control_weather]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
								<li><input type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][cost_control_weather]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
							</ul>
							<?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false));?>
					<style>
					.align-right{
						text-align:right !important;
						padding-right:20px;
					}
					.sale th{
						text-align:center;
					}
					.cost-header.value{
						vertical-align: bottom !important;
					}
					</style>
					<div id="table-cost">
						<table class="table table-request">
						<tr>
						<th width="40%" colspan="2"></th>
						<th class="align-right"><?php echo __('Internal');?></th>
						<th class="align-right"><?php echo __('External');?></th>
						</tr>
						<!--Budget-->
						<tr>
							<th width="20%" rowspan="2"><?php echo __('Budget', true);?></th>
							<td width="20%" class="cost-md"><?php echo __('M.D', true);?></td>
							<td class="align-right"><?php echo !empty($internals['budgetManDay']) ? number_format($internals['budgetManDay'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externals['BudgetManDay']) ? number_format($externals['BudgetManDay'], 2, ',', ' ') : 0?></td>

						</tr>
						<tr>
							<td class="cost-euro"><?php echo __('&euro;', true);?></td>
							<td class="align-right"><?php echo !empty($internals['budgetEuro']) ? number_format($internals['budgetEuro'], 2, ',', ' ') : 0?></td>
							 <td class="align-right"><?php echo !empty($externals['BudgetEuro']) ? number_format($externals['BudgetEuro'], 2, ',', ' ') : 0?></td>
						</tr>
						<!--End budget-->

						<!--Forecast-->
						<tr>
							<th rowspan="2"><?php echo __('Forecast', true);?></th>
							<td class="cost-md"><?php echo __('M.D', true);?></td>
							<td class="align-right"><?php echo !empty($workloadInter) ? number_format($workloadInter, 2, ',', ' ') : 0;?></td>
							 <td class="align-right"><?php echo !empty($workloadExter) ? number_format($workloadExter, 2, ',', ' ') : 0?></td>

						</tr>
						<tr>
							<td class="cost-euro"><?php echo __('&euro;', true);?></td>
							<td class="align-right"><?php echo !empty($internals['forecastEuro']) ? number_format($internals['forecastEuro'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externals['ForecastEuro']) ? number_format($externals['ForecastEuro'], 2, ',', ' ') : 0?></td>
						</tr>
						<!--End Forecast-->


						<!--Var-->
						<tr>
							<th ><?php echo __('Var', true);?></th>
							<td class="cost-md"><?php echo __('%', true);?></td>
							<td class="align-right"><?php echo !empty($internals['varEuro']) ? number_format($internals['varEuro'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externals['VarEuro']) ? number_format($externals['VarEuro'], 2, ',', ' ') : 0?></td>

						</tr>
						<!--End Var-->

						<!--Consumed-->
						<tr>
							<th rowspan="2"><?php echo __('Consumed', true);?></th>
							<td class="cost-md"><?php echo __('M.D', true);?></td>
							<td class="align-right"><?php echo !empty($internals['consumedManday']) ? number_format($internals['consumedManday'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externalConsumeds) ? number_format($externalConsumeds, 2, ',', ' ') : 0?></td>

						</tr>
						<tr>
							<td class="cost-euro"><?php echo __('&euro;', true);?></td>
							<td class="align-right"><?php echo !empty($internals['consumedEuro']) ? number_format($internals['consumedEuro'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externals['ConsumedEuro']) ? number_format($externals['ConsumedEuro'], 2, ',', ' ') : 0?></td>
						</tr>
						<!--End Consumed-->

						<!--Remain-->
						<tr>
							<th rowspan="2"><?php echo __('Remain', true);?></th>
							<td class="cost-md"><?php echo __('M.D', true);?></td>
							<td class="align-right"><?php echo !empty($remainInter) ? number_format($remainInter, 2, ',', ' ') : 0?></td>
							 <td class="align-right"><?php echo !empty($remainExter) ? number_format($remainExter, 2, ',', ' ') : 0?></td>


						</tr>
						<tr>
							<td class="cost-euro"><?php echo __('&euro;', true);?></td>
							<td class="align-right"><?php echo !empty($internals['remainEuro']) ? number_format($internals['remainEuro'], 2, ',', ' ') : 0?></td>
							<td class="align-right"><?php echo !empty($externals['RemainEuro']) ? number_format($externals['RemainEuro'], 2, ',', ' ') : 0?></td>
						</tr>
						<!--End Remain-->

						</table>
						<table class="table table-request sale">

							<tr>
								<th width="20%" rowspan="3" class="cost-header value"><?php echo __('Sale');?></th>
								<th width="40%" colspan="2" class="cost-header"><?php echo __('Sold', true);?></th>
								<th width="20%" class="cost-header"><?php echo __('Billed', true);?></th>
								<th width="20%" class="cost-header"><?php echo __('Paid', true);?></th>
							</tr>
							<tr>
								<td width="20%" class="cost-md"><?php echo __('M.D', true);?></td>
								<td width="20%" class="cost-euro"><?php echo __('&euro;', true);?></td>
								<td  class="cost-euro"><?php echo __('&euro;', true);?></td>
								<td  class="cost-euro"><?php echo __('&euro;', true);?></td>
							</tr>
							<tr>
								<td><?php echo !empty($sales['manDay']) ? number_format($sales['manDay'], 2, ',', ' ') : 0?></td>
								<td><?php echo !empty($sales['sold']) ? number_format($sales['sold'], 2, ',', ' ') : 0?></td>
								<td><?php echo !empty($sales['billed']) ? number_format($sales['billed'], 2, ',', ' ') : 0?></td>
								<td><?php echo !empty($sales['paid']) ? number_format($sales['paid'], 2, ',', ' ') : 0?></td>
							</tr>
						</table>
					</div>
					<div id="table-sales"></div>
				</div>
			</div>
