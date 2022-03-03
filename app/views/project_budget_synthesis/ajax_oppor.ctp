            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo sprintf(__("Synthesis: %s", true), $projectName['Project']['project_name']); ?></h2>						
                    <?php /*
                    <a href="<?php echo $html->url("/project_phase_plans/phase_vision/" . $projectName['Project']['id']) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Gantt+') ?></span></a>
                    <a href="javascript:void(0);" class="wd-add-project" id="export-submit" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                    */ ?>
                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-table" id="project_container" style="width:100%; height: auto;">
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_sales'])):?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="8"><?php echo __d(sprintf($_domain, 'Sales'), 'Sales', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Order Date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Sales'), 'Sold €', true)?></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%" class="man-day-second"><?php echo __d(sprintf($_domain, 'Sales'), 'M.D', true)?></td>
                            <td width="10%" class="none-using"></td>
                            <td width="10%" class="none-using"></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td id="sales-sold" class="td-numberic"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="man-day-total td-numberic" id="sales-man-day"></td>
                        </tr>
                        <?php 
                            if(!empty($sales)):
                                foreach($sales as $sale):
                        ?>
                        <tr>
                            <td><?php echo $sale['name'];?></td>
                            <td><?php echo ($sale['order_date'] != '0000-00-00') ? date('d/m/Y', strtotime($sale['order_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo number_format($sale['sold'], 2, ',', ' ') . ' '.$budget_settings;?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="td-numberic"><?php echo number_format($sale['man_day'], 2, ',', ' ') . ' '.__d(sprintf($_domain, 'Sales'), 'M.D', true);?></td>
                        </tr>
                        <?php 
                                endforeach;
                            endif;
                        ?>
                    </table>
                    <?php endif;?>
                    <div id="total-cost" style="width:100%;">
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_internals']) && !empty($settingMenus['project_budget_externals'])):?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="8"><?php echo __('Total Costs', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"><?php echo __('Budget €', true)?></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%" class="man-day-second"><?php echo __('M.D', true)?></td>
                            <td width="10%" class="none-using"></td>
                            <td width="10%" class="none-using"></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td></td>
                            <td></td>
                            <td id="total-budget" class="td-numberic"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="man-day-total td-numberic" id="total-man-day"></td>
                        </tr>
                        <tr><th colspan="8"></th></tr>
                    </table>
                    <?php endif;?>
                     <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_internals'])):?>
                    <!-- Internal Cost -->
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="8"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Internal costs', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Validation date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'Internal_Cost'), 'Budget €', true)?></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%" class="man-day-second"></td>
                            <td width="10%" class="none-using"></td>
                            <td width="10%" class="none-using"></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td class="td-numberic"><?php echo number_format($internals['budgetEuro'], 2, ',', ' ') . ' '.$budget_settings;?></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="man-day-total td-numberic"></td>
                        </tr>
                        <?php
                            if(!empty($internalDetails)):
                                foreach($internalDetails as $internalDetail):
                        ?>
                        <tr>
                            <td><?php echo $internalDetail['name'];?></td>
                            <td><?php echo ($internalDetail['validation_date'] != '0000-00-00') ? date('d/m/Y', strtotime($internalDetail['validation_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo number_format($internalDetail['budget_euro'], 2, ',', ' ') . ' '.$budget_settings;?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                                endforeach;
                            endif;
                        ?>
                        <tr><th colspan="8"></th></tr>
                    </table>
                    <?php endif;?>
                    <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_externals'])):?>
                    <table cellspacing="0" cellpadding="0" id="project-budget-table">
                        <tr class="budget-title">
                            <th colspan="10"><?php echo __d(sprintf($_domain, 'External_Cost'), 'External Costs', true)?></th>
                        </tr>
                        <tr class="budget-title-second">
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Name', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Order date', true)?></td>
                            <td width="10%"><?php echo __d(sprintf($_domain, 'External_Cost'), 'Budget €', true)?></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td width="10%" class="man-day-second"><?php echo __d(sprintf($_domain, 'External_Cost'), 'M.D', true)?></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                        </tr>
                        <tr class="budget-title-total">
                            <td><?php echo __('Total', true)?></td>
                            <td></td>
                            <td id="external-budget-euro" class="td-numberic"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="man-day-total td-numberic" id="external-man-day"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                            if(!empty($externals)):
                                foreach($externals as $external):
                        ?>
                        <tr>
                            <td><?php echo $external['name'];?></td>
                            <td><?php echo ($external['order_date'] != '0000-00-00') ? date('d/m/Y', strtotime($external['order_date'])) : '';?></td>
                            <td class="td-numberic"><?php echo number_format($external['budget_erro'], 2, ',', ' ') . ' '.$budget_settings;?></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"><?php echo number_format($external['man_day'], 2, ',', ' ') . ' '.__d(sprintf($_domain, 'External_Cost'), 'M.D', true);?></td>
                            <td class="td-numberic"></td>
                            <td class="td-numberic"></td>
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
            var budgetCurrency = <?php echo json_encode(isset($budget_settings) ? $budget_settings : '&euro;') ?>;
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
            $totalSold = number_format($totalSold, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalBilled = number_format($totalBilled, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalBilledCheck = number_format($totalBilledCheck, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalPaid = number_format($totalPaid, 2, ',', ' ') + ' '+ budgetCurrency;
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
            $totalBudgetEuros = $totalForecastEuros = $totalOrderEuros = $totalRemainEuros = $totalManDayExternal = $totalProgressEuro = $totalProgressManDay = 0;
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
                    count++;
                });
            }
            $totalCost_BudgetEuro = $totalCost_ForecastEuro = $totalCost_EngagedEuro = $totalCost_RemainEuro = $totalCost_ManDay = $totalCost_VarEuro = 0;
            
            $totalCost_BudgetEuro = $totalBudgetEuros + internals.budgetEuro;
            $totalCost_ForecastEuro = $totalForecastEuros + 0;
            $totalCost_EngagedEuro = $totalOrderEuros + 0;
            $totalCost_RemainEuro = $totalRemainEuros + 0;
            $totalCost_ManDay = $totalManDayExternal + 0;
            $totalCost_VarEuro = (((($totalForecastEuros + 0)/($totalBudgetEuros + internals.budgetEuro))-1)*100).toFixed(2);
            
            $totalCost_BudgetEuro = number_format($totalCost_BudgetEuro, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalCost_ForecastEuro = number_format($totalCost_ForecastEuro, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalCost_VarEuro = number_format($totalCost_VarEuro, 2, ',', ' ') + ' %';
            $totalCost_EngagedEuro = number_format($totalCost_EngagedEuro, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalCost_RemainEuro = number_format($totalCost_RemainEuro, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalCost_ManDay = number_format($totalCost_ManDay, 2, ',', ' ') + ' ' + viewManDay;
            
            $('#total-budget').html($totalCost_BudgetEuro);
            $('#total-forecast').html($totalCost_ForecastEuro);
            $('#total-var').html($totalCost_VarEuro);
            $('#total-engaged').html($totalCost_EngagedEuro);
            $('#total-remain').html($totalCost_RemainEuro);
            $('#total-man-day').html($totalCost_ManDay);
            
            $totalProgressManDay = ($totalOrderEuros == 0) ? 0 : ($totalProgressEuro/$totalOrderEuros)*100;
            $totalVarEuros = ((($totalForecastEuros/$totalBudgetEuros)-1)*100).toFixed(2);
            $totalVarEuros = number_format($totalVarEuros, 2, ',', ' ') + ' %';
            $totalBudgetEuros = number_format($totalBudgetEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalForecastEuros = number_format($totalForecastEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalOrderEuros = number_format($totalOrderEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalRemainEuros = number_format($totalRemainEuros, 2, ',', ' ') + ' '+ budgetCurrency;
            $totalManDayExternal = number_format($totalManDayExternal, 2, ',', ' ') + ' ' + viewManDay;
            $totalProgressEuro = number_format($totalProgressEuro, 2, ',', ' ') + ' '+ budgetCurrency;
            //$totalProgressManDay = ($totalProgressManDay/count).toFixed(2);
            $totalProgressManDay = number_format($totalProgressManDay, 2, ',', ' ') + ' %';
            
            $('#external-budget-euro').html($totalBudgetEuros);
            $('#external-forecast-euro').html($totalForecastEuros);
            $('#external-order-euro').html($totalOrderEuros);
            $('#external-remain-euro').html($totalRemainEuros);
            $('#external-man-day').html($totalManDayExternal);
            $('#external-progress-euro').html($totalProgressEuro);
            $('#external-var-euro').html($totalVarEuros);
            $('#external-progress').html($totalProgressManDay);
        });
    })(jQuery);
</script>
<script>
    //format float number
    function number_format(number, decimals, dec_point, thousands_sep) {
      // http://kevin.vanzonneveld.net
      // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +     bugfix by: Michael White (http://getsprink.com)
      // +     bugfix by: Benjamin Lupton
      // +     bugfix by: Allan Jensen (http://www.winternet.no)
      // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
      // +     bugfix by: Howard Yeend
      // +    revised by: Luke Smith (http://lucassmith.name)
      // +     bugfix by: Diogo Resende
      // +     bugfix by: Rival
      // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
      // +   improved by: davook
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Jay Klehr
      // +   improved by: Brett Zamir (http://brett-zamir.me)
      // +      input by: Amir Habibi (http://www.residence-mixte.com/)
      // +     bugfix by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Theriault
      // +      input by: Amirouche
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // *     example 1: number_format(1234.56);
      // *     returns 1: '1,235'
      // *     example 2: number_format(1234.56, 2, ',', ' ');
      // *     returns 2: '1 234,56'
      // *     example 3: number_format(1234.5678, 2, '.', '');
      // *     returns 3: '1234.57'
      // *     example 4: number_format(67, 2, ',', '.');
      // *     returns 4: '67,00'
      // *     example 5: number_format(1000);
      // *     returns 5: '1,000'
      // *     example 6: number_format(67.311, 2);
      // *     returns 6: '67.31'
      // *     example 7: number_format(1000.55, 1);
      // *     returns 7: '1,000.6'
      // *     example 8: number_format(67000, 5, ',', '.');
      // *     returns 8: '67.000,00000'
      // *     example 9: number_format(0.9, 0);
      // *     returns 9: '1'
      // *    example 10: number_format('1.20', 2);
      // *    returns 10: '1.20'
      // *    example 11: number_format('1.20', 4);
      // *    returns 11: '1.2000'
      // *    example 12: number_format('1.2000', 3);
      // *    returns 12: '1.200'
      // *    example 13: number_format('1 000,50', 2, '.', ' ');
      // *    returns 13: '100 050.00'
      // Strip all characters but numerical ones.
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
</script>
