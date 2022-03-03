<div class="group-content">
    <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Budget', true);?></span></h3>
    <div class="wd-input separator">
        <!--label for="cost-control"><?php //__("Cost Control") ?></label-->
        <?php
        $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
        // echo $this->Form->input('project_amr_cost_control_id', array('div' => false, 'label' => false,
        // 	'class' => 'selection-plus',
        // 	'name' => 'data[ProjectAmr][project_amr_cost_control_id]',
        // 	'value' => (!empty($this->data['ProjectAmr']['project_amr_cost_control_id'])) ? $this->data['ProjectAmr']['project_amr_cost_control_id'] : "",
        // 	"empty" => __("-- Select --", true),
        // ));
        ?>
        <div style="float: left; line-height: -40px; width:30%">
            <div class="wd-input wd-weather-list-dd">
                <ul style="float: left; display: inline;">
                    
                    <li><input class="input_weather" checked="true" <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][cost_control_weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][cost_control_weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["cost_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][cost_control_weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                </ul>
                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false));?>
            </div>
        </div>

    </div>
    <div class="wd-input">
        <div id="table-cost">
            <table>
                <?php if(!empty($settingMenus) && (!empty($settingMenus['project_budget_internals']) || !empty($settingMenus['project_budget_externals']))):?>
                <tr>
                    <td style="width: 120px;" rowspan="2"></td>
                    <td colspan="2" class="cost-header"><?php echo __('Budget', true);?></td>
                    <td colspan="2" class="cost-header"><?php echo __('Forecast', true);?></td>
                    <td class="cost-header"><?php echo __('Var', true);?></td>
                    <td colspan="2" class="cost-header"><?php echo __('Consumed', true);?></td>
                    <td colspan="2" class="cost-header"><?php echo __('Remain', true);?></td>
                </tr>
                <tr>
                    <td style="width: 10%;" class="cost-md"><?php echo __($md, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                    <td style="width: 10%;" class="cost-md"><?php echo __($md, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __('%', true);?></td>
                    <td style="width: 10%;" class="cost-md"><?php echo __($md, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                    <td style="width: 10%;" class="cost-md"><?php echo __($md, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                </tr>
                <?php endif;?>
                <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_internals'])):?>
                <tr>
                    <td class="cost-header"><?php echo __('Internal');?></td>
                    <td><?php echo !empty($internals['budgetManDay']) ? number_format($internals['budgetManDay'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($internals['budgetEuro']) ? number_format($internals['budgetEuro'], 2, ',', ' ') : 0?></td>
                    <td>
                        <?php //echo !empty($internals['forecastedManDay']) ? number_format($internals['forecastedManDay'], 2, ',', ' ') : 0
                            echo !empty($workloadInter) ? number_format($workloadInter, 2, ',', ' ') : 0;
                        ?>
                    </td>
                    <td><?php echo !empty($internals['forecastEuro']) ? number_format($internals['forecastEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($internals['varEuro']) ? number_format($internals['varEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($internals['consumedManday']) ? number_format($internals['consumedManday'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($internals['consumedEuro']) ? number_format($internals['consumedEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($remainInter) ? number_format($remainInter, 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($internals['remainEuro']) ? number_format($internals['remainEuro'], 2, ',', ' ') : 0?></td>
                </tr>
                <?php endif;?>
                <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_externals'])):?>
                <tr>
                    <td class="cost-header"><?php echo __('External');?></td>
                    <td><?php echo !empty($externals['BudgetManDay']) ? number_format($externals['BudgetManDay'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externals['BudgetEuro']) ? number_format($externals['BudgetEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($workloadExter) ? number_format($workloadExter, 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externals['ForecastEuro']) ? number_format($externals['ForecastEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externals['VarEuro']) ? number_format($externals['VarEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externalConsumeds) ? number_format($externalConsumeds, 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externals['ConsumedEuro']) ? number_format($externals['ConsumedEuro'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($remainExter) ? number_format($remainExter, 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($externals['RemainEuro']) ? number_format($externals['RemainEuro'], 2, ',', ' ') : 0?></td>
                </tr>
                <?php endif;?>
                <?php if(!empty($settingMenus) && !empty($settingMenus['project_budget_sales'])):?>
                <tr style="height: 25px;">
                </tr>
                <tr>
                    <td style="width: 120px;" rowspan="2"></td>
                    <td colspan="2" class="cost-header"><?php echo __('Sold', true);?></td>
                    <td class="cost-header"><?php echo __('Billed', true);?></td>
                    <td class="cost-header"><?php echo __('Paid', true);?></td>
                </tr>
                <tr>
                    <td style="width: 10%;" class="cost-md"><?php echo __($md, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                    <td style="width: 10%;" class="cost-euro"><?php echo __($budget_settings, true);?></td>
                </tr>
                <?php
                    $print = '';
                    if(!empty($settingMenus) && (empty($settingMenus['project_budget_internals']) && empty($settingMenus['project_budget_externals']))){
                        $print = 'style="width: 10%;"';
                    }
                ?>
                <tr>
                    <td class="cost-header" <?php echo $print;?>><?php echo __('Sale');?></td>
                    <td><?php echo !empty($sales['manDay']) ? number_format($sales['manDay'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($sales['sold']) ? number_format($sales['sold'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($sales['billed']) ? number_format($sales['billed'], 2, ',', ' ') : 0?></td>
                    <td><?php echo !empty($sales['paid']) ? number_format($sales['paid'], 2, ',', ' ') : 0?></td>
                </tr>
                <?php endif;?>
            </table>
        </div>
        <div id="table-sales">

        </div>
    </div>
</div>
