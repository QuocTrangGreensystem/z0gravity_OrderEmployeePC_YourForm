<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->css('projects'); ?>
<?php echo $html->css('preview/project_synthesis'); ?>
<?php echo $html->css('preview/layout'); ?>
<?php
	echo $this->Html->script(array(
	'jquery.multiSelect',
	// 'draw-progress',
	'progresspie/jquery-progresspiesvg-min.js',
	'dashboard/jqx-all',
	'dashboard/jqxchart_preview',
	'dashboard/jqxcore',
    'dashboard/jqxdata',
));
?>
<style type="text/css">
body{
	font-family: "Open Sans";
}
.percen-cost-sold{
	float: left;
	padding-top: 8px;
}
.wd-t1{
	width: 100%;
}
#wd-container-footer{
    display: none;
}
.wd-table .budget-title{
	text-align: left !important;
}
body .wd-table .budget-title-total{
	background-color: #E8F0FA;
	color: inherit;
	font-weight: 600;
}
.budget-progress-circle{
	width: 210px;
    float: left;
	display: inline-block;
}
.progress-circle{
	box-shadow: none;
    width: 100%;
    float: none;
    margin-bottom: 7px;
	position: relative;
	background-color: #fff;
}
.progress-circle .progress-circle-inner {
    padding-bottom: 0;
    padding: 20px;
    text-align: center;
}
.progress-values{
	display: inline-block;
	width: calc(100% - 215px);
    float: left;
}
.progress-values .wd-t1 {
    max-width: 200px;
    color: #424242;
    font-size: 24px;
    font-weight: 700;
}
.progress-value.progress-engaged, .progress-value:last-child {
    border-top: none;
}
.progress-value p {
    display: inline-block;
    font-weight: 600;
}
.progress-value span {
    display: inline-block;
    float: right;
	color: black;
}
#project-budget-table tr {
    height: 40px;
    vertical-align: middle;
	font-size: 14px;
}
.wd-table .budget-title th {
	text-align: left;
    vertical-align: middle;
}
#project-budget-table tr td, #project-budget-table tr th {
    border: 1px solid #E9E9E9;
    padding-right: 5px;
}
.chard-content-progress.clearfix{
	max-width: 1700px;
	margin: auto;
}
.bg-var-green{
	background-color: #75AF7E !important;
}
.bg-var-red{
	background-color: #DB414F !important;
}
.var-percent{
	position: absolute;
    height: 50%;
	bottom: 0;
    left: 0;
    z-index: 1;
	display: block;
}
.var-number {
    position: relative;
}
.number-percent {
	z-index: 2;
    position: relative;
}
#project-budget-table tr {
    line-height: 40px;
}
.chart-content-graph{
	max-width: 1700px;
	height: 160px;
	margin: auto;
	width: 1400px;
}
.graph-internal.wd-budget-chart.wd-col.wd-col-md-4{
	padding: 0;
}
body .jqx-rc-all.jqx-button {
    z-index: 101;
}
.wd-budget-chart #chartContainerSyn{
	margin-bottom: 0 !important;
	max-width: 400px;
	top: 35px;
}
.wd-budget-chart #chartContainerIn{
	margin-bottom: 0 !important;
	max-width: 400px;
	top: 35px;
}
.wd-budget-chart #chartContainerEx{
	margin-bottom: 0 !important;
	max-width: 400px;
	top: 35px;
}
.progress-line .progress-line-inner {
    overflow: hidden;
}
.wd-layout > .wd-main-content > .wd-tab{
	margin: 0;
}
</style>
		<div class="wd-list-project">
				<?php
					$menuBudgets = array(
						'project_budget_internals' => !empty($settingMenus['project_budget_internals']),
						'project_budget_externals' => !empty($settingMenus['project_budget_externals']),
						'project_budget_synthesis' => !empty($settingMenus['project_budget_synthesis'])
					);
					$class_row = 'wd-col wd-col-lg-' . 12/count(array_filter($menuBudgets));
					$type = 'monthyear';
					$options = array(
						'type' => $type,
						'chartHeight' => '200'
					);
					$listBudgetSyns = !empty($listBudgetSyns) ? $listBudgetSyns : array();
					$interBudgetProgress = (float)$internals['budgetEuro'];
					$interEngagedProgress = (float)$internals['engagedEuro'];
					$interForecastProgress = (float)$internals['forecastEuro'];
					$interVarProgress = !empty($interBudgetProgress) ? round(($interForecastProgress / $interBudgetProgress)*100,2) : 0;
					$_interVarProgress = $interVarProgress - 100;
					
					$exterBudgetProgress = !empty($listBudgetSyns['external_costs_budget']) 	? (float)$listBudgetSyns['external_costs_budget'] 	: 0;
					$exterOrderedProgress = !empty($listBudgetSyns['external_costs_ordered']) 	? (float)$listBudgetSyns['external_costs_ordered'] 	: 0;
					$exterForecastProgress = !empty($listBudgetSyns['external_costs_forecast'])	? (float)$listBudgetSyns['external_costs_forecast'] : 0;
					$exterVarProgress = !empty($exterBudgetProgress) ? round(($exterForecastProgress / $exterBudgetProgress)*100,2) : 0;
					
					$totalVarProgress = $totalBudgetProgress = $totalEngagedProgress = $totalForecastProgress = 0;
					
					$totalBudgetProgress = $interBudgetProgress + $exterBudgetProgress;
					$totalEngagedProgress = $interEngagedProgress + $exterOrderedProgress;
					$totalForecastProgress = $interForecastProgress + $exterForecastProgress;
					$totalVarProgress = !empty($totalBudgetProgress) ? round(($totalForecastProgress / $totalBudgetProgress)*100,2) : 0;
					$_totalVarProgress = $totalVarProgress - 100;
					$totalVarProgress = $totalVarProgress > 0 ? $totalVarProgress : 0;
					
				
				?> 
				<div id="widget-synthesis-budget">
					<div class="wd-row">
					<?php if( $menuBudgets['project_budget_synthesis']){?>
						<div class="<?php echo $class_row. ' BudgetSynthesis';?>">
							<div class="column-chart chard-total">
								<!-- PIE -->
								<div class="chard-total wd-budget-chart clear-fix">
									<div class="budget-progress-circle">
										<div id="progress-circle-total" class="wd-progress-pie" data-val="<?php echo $totalVarProgress;?>"></div>
									</div>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Total Costs', true)?></h3>
										<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true)?></p><span><?php echo number_format($totalBudgetProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true)?></p><span><?php echo number_format($totalForecastProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Engaged €', true)?></p><span><?php echo number_format($totalEngagedProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
									</div>
								</div>
								
								<!-- Line -- >
								<div class="wd-budget-graph graph-total">	
									< ?php 
										$file = 'widgets'.DS.'synthesis_progress_line';
										echo $this->element($file, $options);
									?>
								</div>-->
							</div>
						</div>
					<?php } ?>
					<?php if( $menuBudgets['project_budget_internals']){?>
						<div class="<?php echo $class_row. ' BudgetInternal';?>">
							<div class="column-chart chard-internal">
								<!-- PIE -->
								<div class="chard-internal wd-budget-chart clear-fix">
									<div class="budget-progress-circle">
										<div id="progress-circle-internal" class="wd-progress-pie" data-val="<?php echo $interVarProgress;?>"></div>
									</div>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost', true)?></h3>
										<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true)?></p><span><?php echo number_format($interBudgetProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true)?></p><span><?php echo number_format($interForecastProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true)?></p><span><?php echo number_format($interEngagedProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
									</div>
								</div>
								
								<!-- Line -- >
								<div class="wd-budget-graph graph-internal">	
									< ?php 
										$file = 'widgets'.DS.'internal_progress_line';
										echo $this->element($file, $options);
									?>
								</div> -->
							</div>
						</div>
					<?php } ?> 
					<?php if( $menuBudgets['project_budget_externals']){?>
						<div class="<?php echo $class_row. ' BudgetExternal';?>">
							<div class="column-chart chard-external">
								<!-- PIE -->
								<div class="chard-external wd-budget-chart clear-fix">
									<div class="budget-progress-circle">
										<div id="progress-circle-external" class="wd-progress-pie" data-val="<?php echo $exterVarProgress;?>"></div>
									</div>
									<div class="progress-values">
										<h3 class="wd-t1"><?php echo __d(sprintf($_domain, 'External_Cost'), 'External Cost', true)?></h3>
										<div class ="progress-value progress-budget"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true)?></p><span><?php echo number_format($exterBudgetProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-forecast"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true)?></p><span><?php echo number_format($exterForecastProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
										<div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true)?></p><span><?php echo number_format($exterOrderedProgress, 2, ',', ' ') . ' ' .$budget_settings;?></span></div>
									</div>
								</div>
								
								<!-- Line -- >
								<div class="wd-budget-graph graph-external">	
									< ?php 
										$file = 'widgets'.DS.'external_progress_line';
										echo $this->element($file, $options);
									?>
								</div> -->
							</div>
						</div>
					<?php } ?> 
					</div>
				</div> 
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%; height: auto; max-width:1700px; margin: auto;">
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_sales'])):?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="7"><?php echo __d(sprintf($_domain, 'Sales'), 'Sales', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Order date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Sold €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'To Bill €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Billed €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Paid €', true)?></td>
                            <td width="10%"></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td id="sales-sold" class="td-numberic"></td>
                            <td id="sales-billed" class="td-numberic"></td>
                            <td id="sales-billed-check" class="td-numberic"></td>
                            <td id="sales-paid" class="td-numberic"></td>
                            <td></td>
                        </tr>
                        <?php
                            if(!empty($sales)):
                                foreach($sales as $sale):
                        ?>
                        <tr>
                            <td><?php echo $sale['name'];?></td>
                            <td><?php echo ($sale['order_date'] != '0000-00-00') ? date('d/m/Y', strtotime($sale['order_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo number_format($sale['sold'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td class="td-numberic"><?php echo number_format($sale['billed'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td class="td-numberic"><?php echo number_format($sale['billed_check'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td class="td-numberic"><?php echo number_format($sale['paid'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td></td>
                        </tr>
                        <?php
                                endforeach;
                            endif;
                        ?>
                    </table>
                    <?php endif;?>
                    <div id="total-cost" style="width:100%;">
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_internals']) && !empty($settingMenus['project_budget_externals'])):?>
					<?php 
					$total_cost_setting = $this->requestAction('/translations/getSettings',
						array('pass' => array(
							'Total_Cost',
							array('total_cost')
						)
					));
					$total_cost_setting_content = array(
						'tt_cost_budget' => array(
							'title' => __d(sprintf($_domain, 'Total_Cost'), 'Budget €', true),
							'id' => 'total-budget',
							'class' => 'td-numberic'
						),
						'tt_cost_forecast' => array(
							'title' => __d(sprintf($_domain, 'Total_Cost'), 'Forecast €', true),
							'id' => 'total-forecast',
							'class' => 'td-numberic'
						),
						'tt_cost_var' => array(
							'title' => __d(sprintf($_domain, 'Total_Cost'), 'Var %', true),
							'id' => 'total-var',
							'class' => 'td-numberic'
						),
						'tt_cost_engaged' => array(
							'title' => __d(sprintf($_domain, 'Total_Cost'), 'Engaged €', true),
							'id' => 'total-engaged',
							'class' => 'td-numberic'
						),
						'tt_cost_remain' => array(
							'title' => __d(sprintf($_domain, 'Total_Cost'), 'Remain €', true),
							'id' => 'total-remain',
							'class' => 'td-numberic'
						),
					);
					?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="<?php echo count($total_cost_setting) ? (count($total_cost_setting) + 2) : 3; ?>"><?php echo __d(sprintf($_domain, 'Total_Cost'), 'Total Costs', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"></td>
                            <td width="10%"></td>
							<?php 
							$total_index = 2;
							foreach($total_cost_setting as $k => $v){
								echo '<td width="10%">' . $total_cost_setting_content[$k]['title'] . '</td>';
								$total_index++;
							}?>
							<?php 
							$total_index++;
							while( $total_index < 7){
								echo '<td width="10%" class="none-using"></td>';
								$total_index++;
							} ?>
							
                        </tr>
                        <tr class="budget-title-total">
                            <td></td>
                            <td></td>
                            <?php $total_index = 2;
							
								$valueTotalVar = isset($totalVarProgress) ? round($totalVarProgress,0) : 0;
								$bgColorTotalVar = '#75AF7E !important';
								if($valueTotalVar > 100){
									$valueTotalVar = 100;
									$bgColorTotalVar = '#DB414F !important';
								}elseif( $valueTotalVar < 0){
									$valueTotalVar = 0;
								}
							
							foreach($total_cost_setting as $k => $v){
								if($k != 'tt_cost_var'){
									echo '<td id="' . $total_cost_setting_content[$k]['id'] . '" class="' . $total_cost_setting_content[$k]['class'] . '"></td>';
									$total_index++;
								}else{
									echo '<td id="total-var" class="td-numberic"><div class="var-number"><p class="number-percent">'. number_format($_totalVarProgress, 2, ',', ' ') . ' % </p>';
								?>
									<p class="var-percent" style="width:<?php echo $valueTotalVar . '%';?>; background-color:<?php echo $bgColorTotalVar; ?>;" ></p></div></td>
							<?php
								$total_index++;
								}
							}?>
							<?php 
							$total_index++;
							while( $total_index < 7){
								echo '<td width="10%" class="none-using"></td>';
								$total_index++;
							} ?>
                        </tr>
                        <tr><th colspan="<?php echo count($total_cost_setting) ? (count($total_cost_setting) + 2) : 3; ?>"></th></tr>
                    </table>
                    <?php endif;?>
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_internals'])):?>
                    <!-- Internal Cost -->
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="7"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Internal Cost', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Validation date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Forecast €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Var %', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Engaged €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Remain €', true)?></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td id="internal-budget-euro" class="td-numberic"><?php echo number_format($internals['budgetEuro'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td id="internal-forecast-euro" class="td-numberic"><?php echo number_format($internals['forecastEuro'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <?php
								$valueInterVarEuro = isset($interVarProgress) ? round($interVarProgress,0) : 0;
								$bgColorInterVar = '#75AF7E !important';
								if($valueInterVarEuro > 100){
									$valueInterVarEuro = 100;
									$bgColorInterVar = '#DB414F !important';
								}
							?>
							<td id="internal-var-euro" class="td-numberic"><div class="var-number"><p class="number-percent"><?php echo number_format($_interVarProgress, 2, ',', ' ') . ' %';?></p>
								<p class="var-percent" style="width:<?php echo $valueInterVarEuro . '%';?>; background-color:<?php echo $bgColorInterVar; ?>;" ></p></div>
							</td>
                            <td id="internal-engaged-euro" class="td-numberic"><?php echo number_format($internals['engagedEuro'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                            <td id="internal-remain-euro" class="td-numberic"><?php echo number_format($internals['remainEuro'], 2, ',', ' ') . ' ' .$budget_settings;?></td>
                        </tr>
                        <?php
                            if(!empty($internalDetails)):
                                foreach($internalDetails as $internalDetail):
                        ?>
                        <tr>
                            <td><?php echo $internalDetail['name'];?></td>
                            <td><?php echo ($internalDetail['validation_date'] != '0000-00-00') ? date('d/m/Y', strtotime($internalDetail['validation_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo number_format($internalDetail['budget_euro'], 2, ',', ' '). ' ' .$budget_settings;?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                                endforeach;
                            endif;
                        ?>
                        <tr><th colspan="7"></th></tr>
                    </table>
                    <?php endif;?>
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_externals'])):?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="7"><?php echo __d(sprintf($_domain, 'External_Cost'), 'External Cost', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Order date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Forecast €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Var', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Ordered €', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Remain €', true)?></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td id="external-budget-euro" class="td-numberic"></td>
                            <td id="external-forecast-euro" class="td-numberic"></td>
							<?php
								$_valueExterVar = $valueExterVar = isset($exterVarProgress) ? round($exterVarProgress,0) : 0;
								$bgColorExterVar = '#75AF7E !important';
								if($valueExterVar > 100){
									$valueExterVar = 100;
									$_valueExterVar = $_valueExterVar - 100;
									$bgColorExterVar = '#DB414F !important';
								}elseif( $valueExterVar < 0){
									$valueExterVar = 0;
								}
							?>
                            <td id="external-var-euro" class="td-numberic"><div class="var-number"><p class="number-percent"><?php echo number_format($_valueExterVar, 2, ',', ' ') . ' %';?></p>
								<p class="var-percent" style="width:<?php echo $_valueExterVar . '%';?>; background-color:<?php echo $bgColorExterVar; ?>;" ></p></div>
							</td>
                            <td id="external-order-euro" class="td-numberic"></td>
                            <td id="external-remain-euro" class="td-numberic"></td>
                        </tr>
                        <?php
                            if(!empty($externals)):
                                foreach($externals as $external):
                        ?>
                        <tr>
                            <td><?php echo $external['name'];?></td>
                            <td><?php echo ($external['order_date'] != '0000-00-00') ? date('d/m/Y', strtotime($external['order_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo !empty($external['budget_erro']) ? number_format($external['budget_erro'], 2, ',', ' ') . ' ' .$budget_settings : '';?></td>
                            <td class="td-numberic"><?php echo !empty($external['forecast_erro']) ? number_format($external['forecast_erro'], 2, ',', ' ') . ' ' .$budget_settings : '';?></td>
                            <td class="td-numberic"><?php echo !empty($external['var_erro']) ? number_format($external['var_erro'], 2, ',', ' ') . ' %' : '';?></td>
                            <td class="td-numberic"><?php echo !empty($external['ordered_erro']) ? number_format($external['ordered_erro'], 2, ',', ' ') . ' ' .$budget_settings : '';?></td>
                            <td class="td-numberic"><?php echo !empty($external['remain_erro']) ? number_format($external['remain_erro'], 2, ',', ' ') . ' ' .$budget_settings : '';?></td>
                        </tr>
                        <?php
                                endforeach;
                            endif;
                        ?>
                    </table>
                    <?php endif;?>
                    </div>
                </div>
            </div>
<?php
    $viewManDay = __('M.D', true);
?>
<script type="text/javascript">
    (function($){
        $(function(){
            // sales
            var sales = <?php echo json_encode($sales); ?>;
            var budget_settings = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
            var interVarProgress = <?php echo json_encode($interVarProgress); ?>;
            var $totalS = $totalB = 0;
            var viewManDay = <?php echo json_encode($viewManDay); ?>;
            $totalSold = $totalBilled = $totalPaid = $totalManDay = $totalBilledCheck = 0;
            if(sales){
                $.each(sales, function(index, vl){
                    $totalSold += vl.sold ? parseFloat(vl.sold) : 0;
                    $totalBilled += vl.billed ? parseFloat(vl.billed) : 0;
                    $totalBilledCheck += vl.billed_check ? parseFloat(vl.billed_check) : 0;
                    $totalPaid += vl.paid ? parseFloat(vl.paid) : 0;
                    $totalManDay += vl.man_day ? parseFloat(vl.man_day) : 0;
                });
            }
            $totalS  = $totalSold;
            $totalSold = number_format($totalSold, 2, ',', ' ') + ' ' + budget_settings;
            $totalBilled = number_format($totalBilled, 2, ',', ' ')  + ' ' + budget_settings;
            $totalBilledCheck = number_format($totalBilledCheck, 2, ',', ' ')  + ' ' + budget_settings;
            $totalPaid = number_format($totalPaid, 2, ',', ' ')  + ' ' + budget_settings;
            $totalManDay = number_format($totalManDay, 2, ',', ' ') + ' ' + viewManDay;
            $('#sales-sold').html($totalSold);
            $('#sales-billed').html($totalBilled);
            $('#sales-billed-check').html($totalBilledCheck);
            $('#sales-paid').html($totalPaid);
            $('#sales-man-day').html($totalManDay);
            // internal costs
            var internals = <?php echo json_encode($internals); ?>;
            // external costs
            var externals = <?php echo json_encode($externals); ?>;
            $totalBudgetEuros = $totalForecastEuros = $totalOrderEuros = $totalRemainEuros = $totalManDayExternal = $totalProgressEuro = $totalProgressManDay = $totalConsumedMD = 0;
            if(externals){
                var count = 0;
                $.each(externals, function(ind, val){
                    $totalBudgetEuros += val.budget_erro ? parseFloat(val.budget_erro) : 0;
                    $totalForecastEuros += val.forecast_erro ? parseFloat(val.forecast_erro) : 0;
                    $totalOrderEuros += val.ordered_erro ? parseFloat(val.ordered_erro) : 0;
                    $totalRemainEuros += val.remain_erro ? parseFloat(val.remain_erro) : 0;
                    $totalManDayExternal += val.man_day ? parseFloat(val.man_day) : 0;
                    $totalProgressManDay += val.progress_md ? parseFloat(val.progress_md) : 0;
                    $totalProgressEuro += val.progress_erro ? parseFloat(val.progress_erro) : 0;
                    $totalConsumedMD += val.consumed_md ? parseFloat(val.consumed_md) : 0;
                    count++;
                });
            }
            $totalCost_BudgetEuro = $totalCost_ForecastEuro = $totalCost_EngagedEuro = $totalCost_RemainEuro = $totalCost_ManDay = $totalCost_VarEuro = 0;
			
            $totalCost_BudgetEuro = $totalBudgetEuros + parseFloat(internals.budgetEuro);
            $totalCost_ForecastEuro = $totalForecastEuros + parseFloat(internals.forecastEuro);
            $totalCost_EngagedEuro = $totalOrderEuros + parseFloat(internals.engagedEuro);
            $totalCost_RemainEuro = $totalRemainEuros + parseFloat(internals.remainEuro);
            $totalCost_ManDay = $totalManDayExternal + parseFloat(internals.forecastedManDay);
            $totalCost_VarEuro = (((($totalForecastEuros + parseFloat(internals.forecastEuro))/($totalBudgetEuros + parseFloat(internals.budgetEuro)))-1)*100).toFixed(2);
			
			var _totalCost_VarEuro = $totalCost_ForecastEuro / $totalCost_BudgetEuro;
			
            $totalB = $totalCost_BudgetEuro;
            $totalCost_BudgetEuro = number_format($totalCost_BudgetEuro, 2, ',', ' ') + ' ' + budget_settings;
            $totalCost_ForecastEuro = number_format($totalCost_ForecastEuro, 2, ',', ' ') + ' ' + budget_settings;
            $totalCost_VarEuro = number_format($totalCost_VarEuro, 2, ',', ' ') + ' %';
            $totalCost_EngagedEuro = number_format($totalCost_EngagedEuro, 2, ',', ' ') + ' ' + budget_settings;
            $totalCost_RemainEuro = number_format($totalCost_RemainEuro, 2, ',', ' ')  + ' ' + budget_settings;
            $totalCost_ManDay = number_format($totalCost_ManDay, 2, ',', ' ') + ' ' + viewManDay;

            $('#total-budget').html($totalCost_BudgetEuro);
            $('#total-forecast').html($totalCost_ForecastEuro);
            $('#total-engaged').html($totalCost_EngagedEuro);
            $('#total-remain').html($totalCost_RemainEuro);
            $('#total-man-day').html($totalCost_ManDay);
            //tinh %
            var $roisold = number_format($totalS, 2, '.', '');
            var $roibudget = number_format($totalB, 2, '.', '');
            if($roibudget!='0.00'){
                //ROI = ( Doanh thu - chi phi ) / chi phi * 100
                var $roi = ( ($roisold - $roibudget) /$roibudget)*100;
            }else{
                var $roi = 0;
            }
            $('.roi-percent').html($roi.toFixed(2));
            $totalProgressManDay = ($totalOrderEuros == 0) ? 0 : ($totalProgressEuro/$totalOrderEuros)*100;
            $totalVarEuros = ((($totalForecastEuros/$totalBudgetEuros)-1)*100).toFixed(2);
			
			var _totalVarEuros = $totalForecastEuros/$totalBudgetEuros;
			
            $totalVarEuros = number_format($totalVarEuros, 2, ',', ' ') + ' %';
            $totalBudgetEuros = number_format($totalBudgetEuros, 2, ',', ' ')  + ' ' + budget_settings;
            $totalForecastEuros = number_format($totalForecastEuros, 2, ',', ' ') + ' ' + budget_settings;
            $totalOrderEuros = number_format($totalOrderEuros, 2, ',', ' ')  + ' ' + budget_settings;
            $totalRemainEuros = number_format($totalRemainEuros, 2, ',', ' ')  + ' ' + budget_settings;
            $totalManDayExternal = number_format($totalManDayExternal, 2, ',', ' ') + ' ' + viewManDay;
            $totalProgressEuro = number_format($totalProgressEuro, 2, ',', ' ') + ' ' + budget_settings;
            $totalProgressManDay = number_format($totalProgressManDay, 2, ',', ' ') + ' %';

            $('#external-budget-euro').html($totalBudgetEuros);
            $('#external-forecast-euro').html($totalForecastEuros);
            $('#external-order-euro').html($totalOrderEuros);
            $('#external-remain-euro').html($totalRemainEuros);
            $('#external-man-day').html($totalManDayExternal);
            $('#external-progress-euro').html($totalProgressEuro);
            $('#external-progress').html($totalProgressManDay);
        });
        $('.percen-cost-sold').focus(function(){
            $(this).tooltip('option' , 'content' , '<?php echo __('ROI=(( profit – cost )/cost*100)',true);?>');
            $(this).tooltip('enable');
        }).mouseup(function(){
            $(this).tooltip('close');
        }).blur(function(){
            $(this).tooltip('option' , 'content' , '<?php echo __('ROI=(( profit – cost )/cost*100)',true);?>');
            $(this).tooltip('enable');
        }).tooltip({maxWidth : 1000, maxHeight : 200,content: function(target){
                return '<?php echo __('ROI = (total sales - total cost) / total cost*100',true);?>';
            }});
		function updateChart(percent){
			// percent = Math.min(100, Math.max(0, percent));
			pie_progress.data('val', percent).progressPie();
			pie_progress_inter.data('val', percent).progressPie();
			pie_progress_exter.data('val', percent).progressPie();
			return;
		}
    })(jQuery);
	
	var pie_progress = $('#progress-circle-total');
	var progress_width = pie_progress.width();
	
	var pie_progress_inter = $('#progress-circle-internal');
	var progress_width_inter = pie_progress_inter.width();
	
	var pie_progress_exter = $('#progress-circle-external');
	var progress_width_exter = pie_progress_exter.width();
	
	pie_progress.addClass('wd-progress-pie').setupProgressPie({
		size: progress_width ? progress_width : 140,
		strokeWidth: 8,
		ringWidth: 8,
		ringEndsRounded: true,
		strokeColor: "#f3f3f3",
		color: function(value){
			var red = 'rgba(233, 71, 84, 1)';
			var green = 'rgba(110, 175, 121, 1)';
			return pie_progress.data('val') > 100 ? red : green;
		},
		valueData: "val",
		contentPlugin: "progressDisplay",
		contentPluginOptions: {
			fontFamily: 'Open Sans',
			multiline: [
				/*{
					cssClass: "progresspie-progressText",
					fontSize: 11,
					textContent: ('<?php __('Progress');?>').toUpperCase(),
					color: '#ddd',
				},*/
				{
					cssClass: "progresspie-progressValue",
					fontSize: 28,
					textContent: '%s%' ,
					color: function(value){
						var red = 'rgba(233, 71, 84, 1)';
						var green = 'rgba(110, 175, 121, 1)';
						return pie_progress.data('val') > 100 ? red : green;
					}
				},
			],
			fontSize: 28
		},
		animate: {
			dur: "1.5s"
		}
	}).progressPie();
	pie_progress_inter.addClass('wd-progress-pie').setupProgressPie({
		size: progress_width_inter ? progress_width_inter : 140,
		strokeWidth: 8,
		ringWidth: 8,
		ringEndsRounded: true,
		strokeColor: "#f3f3f3",
		color: function(value){
			var red = 'rgba(233, 71, 84, 1)';
			var green = 'rgba(110, 175, 121, 1)';
			return pie_progress_inter.data('val') > 100 ? red : green;
		},
		valueData: "val",
		contentPlugin: "progressDisplay",
		contentPluginOptions: {
			fontFamily: 'Open Sans',
			multiline: [
				/*{
					cssClass: "progresspie-progressText",
					fontSize: 11,
					textContent: ('<?php __('Progress');?>').toUpperCase(),
					color: '#ddd',
				}, */
				{
					cssClass: "progresspie-progressValue",
					fontSize: 28,
					textContent: '%s%' ,
					color: function(value){
						var red = 'rgba(233, 71, 84, 1)';
						var green = 'rgba(110, 175, 121, 1)';
						return pie_progress_inter.data('val') > 100 ? red : green;
					}
				},
			],
			fontSize: 28
		},
		animate: {
			dur: "1.5s"
		}
	}).progressPie();
	pie_progress_exter.addClass('wd-progress-pie').setupProgressPie({
		size: progress_width_exter ? progress_width_exter : 140,
		strokeWidth: 8,
		ringWidth: 8,
		ringEndsRounded: true,
		strokeColor: "#f3f3f3",
		color: function(value){
			var red = 'rgba(233, 71, 84, 1)';
			var green = 'rgba(110, 175, 121, 1)';
			return pie_progress_exter.data('val') > 100 ? red : green;
		},
		valueData: "val",
		contentPlugin: "progressDisplay",
		contentPluginOptions: {
			fontFamily: 'Open Sans',
			multiline: [
				/*{
					cssClass: "progresspie-progressText",
					fontSize: 11,
					textContent: ('<?php __('Progress');?>').toUpperCase(),
					color: '#ddd',
				},*/
				{
					cssClass: "progresspie-progressValue",
					fontSize: 28,
					textContent: '%s%' ,
					color: function(value){
						var red = 'rgba(233, 71, 84, 1)';
						var green = 'rgba(110, 175, 121, 1)';
						return pie_progress_exter.data('val') > 100 ? red : green;
					}
				},
			],
			fontSize: 28
		},
		animate: {
			dur: "1.5s"
		}
	}).progressPie();
</script>
