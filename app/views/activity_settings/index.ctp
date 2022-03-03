<?php
echo $html->script('jquery.multiSelect');
echo $html->css('jquery.multiSelect');
echo $html->script('history_filter');
echo $html->css('slick_grid/slick.grid');
echo $html->css('slick_grid/slick.pager');
echo $html->css('slick_grid/slick.common');
echo $html->css('slick_grid/slick.edit');
echo $html->css('preview/tab-admin');
echo $html->css('layout_admin_2019');
?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
.slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}
.wd-input-select label{display:inline-block;min-width:515px;}

.wd-tab .wd-content{
	overflow-y: auto;
}
.wd-table {
	height: inherit;
}
.wd-list-project .wd-tab .wd-content label {
	margin-top: 10px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
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
                                        <?php
                                            $datas = array(
                                                    0 => __("No", true),
                                                    1 => __("Yes", true),
                                                );
                                            echo $this->Form->create('Activity', array('url' => array('controller' => 'activity_settings', 'action' => 'index')));
                                        ?>
										<div class="wd-input-select">
											<label><?php echo __('Can select a project without task in a timesheet', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('select_project_without_task', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'select_project_without_task',
                                                    'onchange' => "editMe('select_project_without_task', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['select_project_without_task'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Only team defined in project/team can consume in project", true)?></label>
                                            <?php
                                                echo $this->Form->input('allow_team_consume', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ActivitySetting']['allow_team_consume'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Only employee defined in project/team can consume in project", true)?></label>
                                            <?php
                                                echo $this->Form->input('allow_employee_in_team_consume', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ActivitySetting']['allow_employee_in_team_consume'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Authorize consume a task even if the Remain = 0", true)?></label>
                                            <?php
                                                echo $this->Form->input('allow_remain_zero_consume', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ActivitySetting']['allow_remain_zero_consume'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Show forecast+ full month from date 1st", true)?></label>
                                            <?php
                                                echo $this->Form->input('show_full_day_in_month', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ActivitySetting']['show_full_day_in_month'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __("Show Activity Review Screen.", true)?></label>
                                            <?php
                                                echo $this->Form->input('show_activity_review', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    "class" => "wd-select-box",
                                                    "default" => $setRemain['ActivitySetting']['show_activity_review'],
                                                    "options" => $datas
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
											<label><?php echo __('Show Activity Forecasts Screen', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_forecast', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_forecast',
                                                    'onchange' => "editMe('show_activity_forecast', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_forecast'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
										<?php
											// If forecast item display on header top, do not display it on the sub header
										if(!(isset($companyConfigs['display_activity_forecast']) && $companyConfigs['display_activity_forecast'] == 1)) { ?>
                                        <div class="wd-input-select">
											<label><?php echo __('Show Activity Forecasts++ Screen', true)?></label>
                                            <?php
                                                $option = array( __('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_forecast_plus', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_forecast_plus',
                                                    'onchange' => "editMe('show_activity_forecast_plus', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_forecast_plus'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
										<?php } ?>
                                        <div class="wd-input-select">
											<label><?php echo __('Show Activity Management Screen', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_index', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_index',
                                                    'onchange' => "editMe('show_activity_index', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_index'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
											<label><?php echo __('Show Activity View Screen', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_view', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_view',
                                                    'onchange' => "editMe('show_activity_view', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_view'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
											<label><?php echo __('Show Activity Export Excel Screen', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_export_excel', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_export_excel',
                                                    'onchange' => "editMe('show_activity_export_excel', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_export_excel'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('A resource sees only tasks assigned to him or to his team', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('a_resource_see_only_task_assigned_to_him', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'a_resource_see_only_task_assigned_to_him',
                                                    'onchange' => "editMe('a_resource_see_only_task_assigned_to_him', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['a_resource_see_only_task_assigned_to_him'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Copy in activity list, the tasks affected to the team', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('copy_in_activity_list', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'copy_in_activity_list',
                                                    'onchange' => "changeActivityList('copy_in_activity_list', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['copy_in_activity_list'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Activate team workload', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('active_team_workload', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'active_team_workload',
                                                    'onchange' => "editMe('active_team_workload', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['active_team_workload'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Activate team workload+', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('active_team_workload_plus', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'active_team_workload_plus',
                                                    'onchange' => "editMe('active_team_workload_plus', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['active_team_workload_plus'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Display FREEZE in Staffing+/team+ screen', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('display_staffing_team_plus', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'display_staffing_team_plus',
                                                    'onchange' => "editMe('display_staffing_team_plus', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['display_staffing_team_plus'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Can fill more than capacity/day', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('fill_more_than_capacity_day', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'fill_more_than_capacity_day',
                                                    'onchange' => "editMe('fill_more_than_capacity_day', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['fill_more_than_capacity_day'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select dependon-value" data-dependon="fill_more_than_capacity_day">
                                            <label><?php echo __('Limited to the capacity of week', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('limited_to_the_capacity_of_week', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'limited_to_the_capacity_of_week',
                                                    'onchange' => "editMe('limited_to_the_capacity_of_week', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['limited_to_the_capacity_of_week'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Show activity forecast comment.', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_activity_forecast_comment', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_activity_forecast_comment',
                                                    'onchange' => "editMe('show_activity_forecast_comment', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_activity_forecast_comment'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Can send a timesheet partially filled', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('send_timesheet_partially_filled', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'send_timesheet_partially_filled',
                                                    'onchange' => "editMe('send_timesheet_partially_filled', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['send_timesheet_partially_filled'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select">
                                            <label><?php echo __('Checking staffing before display staffing', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('run_staffing_before_display_staffing', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'run_staffing_before_display_staffing',
                                                    'onchange' => "editMe('run_staffing_before_display_staffing', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['run_staffing_before_display_staffing'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
										<div class="wd-input-select">
											<label><?php echo __('Show switch of activity staffing', true)?></label>
                                            <?php
                                                $option = array(__('No', true), __('Yes', true));
                                                echo $this->Form->input('show_switch_activity_staffing', array(
                                                    'div' => false,
                                                    'label' => false,
                                                    'id' => 'show_switch_activity_staffing',
                                                    'onchange' => "editMe('show_switch_activity_staffing', this.value);",
                                                    "class" => "wd-select-box",
                                                    "default" => &$companyConfigs['show_switch_activity_staffing'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                ));
                                            ?>
                                        </div>
                                        <div class="wd-submit" style="margin-left: 38px; margin-top: 10px;">
                                            <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                                <span><?php __('Save') ?></span>
                                            </button>
                                        </div>
                                        <?php
                                            echo $this->Form->end();
                                        ?>
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
    var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
    function editMe(field,value) {
        $(loading).insertAfter('#'+field);
        var data = field+'/'+value;
        $.ajax({
            url: '/company_configs/editMe/',
            data: {
                data : { value : value, field : field }
            },
            type:'POST',
            success:function(data) {
                $('#'+field).removeClass('KO');
                $('#loadingElm').remove();
            }
        });
    }
    function changeActivityList(field,value) {
        $(loading).insertAfter('#'+field);
        var data = field+'/'+value;
        $.ajax({
            url: '/company_configs/editMe/',
            data: {
                data : { value : value, field : field }
            },
            type:'POST',
            success:function(data) {
                $('#'+field).removeClass('KO');
                $('#loadingElm').remove();
            }
        });
    }
	$.each( $('.dependon-value'), function(i, _input){
		var _this = $(_input);
		var _dependon_elm = $('#' + _this.data('dependon'));
		if( _dependon_elm.length){
			_this.toggle(_dependon_elm.val() != 0);
			_dependon_elm.on('change', function(){
				_this.toggle($(this).val() != 0);
			});
		}
	})
</script>
