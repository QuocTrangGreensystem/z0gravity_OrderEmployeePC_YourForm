<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
 #project_container{
	 float: none;
 }
 .wd-list-project .wd-tab .wd-content label{
	 width: calc(100% - 100px);
	 display: inline-block;
	 float: none;
	 vertical-align: middle;
	 line-height: 22px;
	 padding-left: 10px;
	 height: inherit;
 }
 .wd-list-project .wd-tab .wd-content .wd-bt-switch{
	 display: inline-block;
	 vertical-align: middle;
 }
 .wd-bt-switch .wd-update{
	width: 38px;
    height: 20px;
    border: 1px solid #E1E6E8;
    background: #FFFFFF;
    overflow: visible;
    border-radius: 10px;
    position: relative;
    cursor: pointer;
	box-sizing: border-box;
 }
 .wd-bt-switch .wd-update:before {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 1px solid #E1E6E8;
    background: #FFFFFF;
    left: -1px;
    top: -1px;
    transition: all 0.2s ease;
}
.wd-bt-switch .wd-update.active:before {
    width: 16px;
    height: 16px;
    border: 2px solid #247FC3;
    top: -1px;
    left: calc( 100% - 19px);
}
 .wd-bt-switch .wd-update.loading:before{
	width: 18px;
    height: 18px;
    border: 1px solid #E1E6E8;
    border-top-color: #247FC3;
    animation: wd-rotate 2s infinite;
 }
 .wd-select-options{
	 width: 50%;
	 float: left;
 }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php 
	
	echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3"></h2>
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container">
                                    <div id="wd-select">
                                        <div class="wd-select-options">
											<div class="wd-input-select">
												<div class="wd-bt-switch">
												<?php $ex_value = isset($companyConfigs['average_cost_default']) ? $companyConfigs['average_cost_default'] : 1;
														$ex_class =  $ex_value ? 'active' : ''; ?>
													<a class = "wd-update <?php echo $ex_class; ?>" title = "<?php echo __('Default method with average cost', true);?>">
														  <?php  echo $this->Form->input('average_cost_default', array(
																'div' => false,
																'label' => false,
																'id' => 'average_cost_default',
																'onchange' => "settingBudget('average_cost_default',this.value);",
																"class" => "wd-select-box",
																"value" => $ex_value,
																"rel" => "no-history",
																'type' => 'hidden'
															));
															?>
													</a>
												</div>
												<label><?php echo __('Default method with average cost', true)?></label>
											</div>
											<div class="wd-input-select">
												<div class="wd-bt-switch">
													<?php $ex_value = isset($companyConfigs['budget_euro_fill_manual']) ? $companyConfigs['budget_euro_fill_manual'] : 0;
														$ex_class =  $ex_value ? 'active' : ''; ?>
													<a class = "wd-update <?php echo $ex_class; ?>" title = "<?php echo __('Average price = Total budget '. $currency .' / Total M.D., value used for Forecast '. $currency .' and remain '. $currency .'', true);?>">
														  <?php  echo $this->Form->input('budget_euro_fill_manual', array(
																'div' => false,
																'label' => false,
																'id' => 'budget_euro_fill_manual',
																'onchange' => "settingBudget('budget_euro_fill_manual',this.value);",
																"class" => "wd-select-box",
																"value" => $ex_value,
																"rel" => "no-history",
																'type' => 'hidden'
															));
															?>
													</a>
												</div>
												<label><?php echo __('Average price = Total budget '. $currency .' / Total M.D., value used for Forecast '. $currency .' and remain '. $currency, true)?></label>
											   
											</div>
											<div class="wd-input-select">
												<div class="wd-bt-switch">
													<?php $ex_value = isset($companyConfigs['average_euro_fill_manual']) ? $companyConfigs['average_euro_fill_manual'] : 0;
														$ex_class =  $ex_value ? 'active' : ''; ?>
													<a class = "wd-update <?php echo $ex_class; ?>" title = "<?php echo __('Average price filled by PM. , value used for Forecast '. $currency .' and remain '. $currency , true);?>">
														  <?php  echo $this->Form->input('average_euro_fill_manual', array(
																'div' => false,
																'label' => false,
																'id' => 'average_euro_fill_manual',
																'onchange' => "settingBudget('average_euro_fill_manual',this.value);",
																"class" => "wd-select-box",
																"value" => $ex_value,
																"rel" => "no-history",
																'type' => 'hidden'
															));
															?>
													</a>
												</div>
												<label><?php echo __('Average price filled by PM. , value used for Forecast '. $currency .' and remain '. $currency, true)?></label>
											   
											</div>
										</div>
										<div class="wd-select-options">
											<div class="wd-input-select">
												<div class="wd-bt-switch">
													<?php $ex_value = isset($companyConfigs['consumed_used_timesheet']) ? $companyConfigs['consumed_used_timesheet'] : 0;
														$ex_class =  $ex_value ? 'active' : ''; ?>
													<a class = "wd-update <?php echo $ex_class; ?>" title = "<?php echo __('Consumed: used the freezed values of timesheet', true);?>">
														  <?php  echo $this->Form->input('consumed_used_timesheet', array(
																'div' => false,
																'label' => false,
																'id' => 'consumed_used_timesheet',
																'onchange' => "settingBudget('consumed_used_timesheet',this.value);",
																"class" => "wd-select-box",
																"value" => $ex_value,
																"rel" => "no-history",
																'type' => 'hidden'
															));
															?>
													</a>
												</div>
												<label><?php echo __("Consumed: used the freezed values of timesheet", true)?></label>
											   
											</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function settingBudget(field,value) {
        var data = field+'/'+value;
		$('#'+field).closest('.wd-update').addClass('loading');
        $.ajax({
            url: '/company_configs/settingBudget/',
            data: {
                data : { value : value, field : field }
            },
            type:'POST',
            success:function(data) {
                $('#'+field).closest('.wd-update').removeClass('loading');
				if($('#'+field).val() == 1){
					$('#'+field).closest('.wd-update').addClass('active');
				}else{
					$('#'+field).closest('.wd-update').removeClass('active');
				}
            }
        });
    }
   $('.wd-bt-switch').on('click', function(){
	   var value = $(this).find('input').val();
	   switch_val = (value == 1) ? 0 : 1;
	   $(this).find('input').val(switch_val).trigger('change');
	   $.each( $(this).closest('.wd-input-select').siblings(), function(){
		   if(switch_val == 1){
			   if($(this).find('input').val() != 0){
					$(this).find('input').val(0).trigger('change');
			   }
		   }
	   });
   });
</script>