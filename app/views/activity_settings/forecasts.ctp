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
 .slick-cell .multiSelect{width:auto;display:block;overflow:hidden;text-overflow:ellipsis;}.wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}.wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}.wd-input-select label{display:inline-block;min-width:370px;}
    .wd-input-select{
        margin-bottom: 30px;
    }
    .wd-input-select label{
        font-size: 13px;
        font-weight: bold;
        padding-right: 20px;
        margin-top: 12px;
        display: block;
        float: left;
    }
    .wd-input-select select{
        padding: 5px;
        float: left;
        border: 1px solid rgb(179, 179, 179);
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
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
                                    $option = array(__('No', true), __('Yes', true));
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                                    <div id="wd-select" style="width: 550px;">
                                        <div class="wd-input-select">
                                            <label><?php echo __("Manager can modify the task in forecast screen", true)?></label>
                                            <?php
                                                echo $this->Form->input('forecast_modify', array(
                                                    'div' => false, 
                                                    'label' => false,
        											'onchange' => "editMe('forecast_modify', this.value);",
                                                    "class" => "forecast_modify",
                                                    "default" => &$companyConfigs['forecast_modify'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select display-div">
                                            <label><?php echo __("Assigned To", true)?></label>
                                            <?php
                                                echo $this->Form->input('forecast_assigned_to', array(
                                                    'div' => false, 
                                                    'label' => false,
        											'onchange' => "editMe('forecast_assigned_to', this.value);",
                                                    "class" => "forecast_assigned_to",
                                                    "default" => &$companyConfigs['forecast_assigned_to'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select display-div">
                                            <label><?php echo __("Status", true)?></label>
                                            <?php
                                                echo $this->Form->input('forecast_status', array(
                                                    'div' => false, 
                                                    'label' => false,
        											'onchange' => "editMe('forecast_status', this.value);",
                                                    "class" => "forecast_status",
                                                    "default" => &$companyConfigs['forecast_status'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select display-div">
                                            <label><?php echo __("Others fields", true)?></label>
                                            <?php
                                                echo $this->Form->input('forecast_others_fields', array(
                                                    'div' => false, 
                                                    'label' => false,
        											'onchange' => "editMe('forecast_others_fields', this.value);",
                                                    "class" => "forecast_others_fields",
                                                    "default" => &$companyConfigs['forecast_others_fields'],
                                                    "options" => $option,
                                                    "rel" => "no-history"
                                                    ));
                                            ?>
                                        </div>
                                        <div class="wd-input-select display-div">
                                            <label style="color: red;"><?php echo __("Workload, Start date, End date, Duration, Predecessor cannot be modified", true)?></label>
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
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
function editMe(field,value)
{
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
$(document).ready(function(){
    function checkDiv(){
        var forecastModify = $('#forecast_modify').val();
        if(forecastModify == 0 || !forecastModify){
            $('.display-div').hide();
        } else {
            $('.display-div').show();
        }
    }
    checkDiv();
    $('#forecast_modify').change(function(){
        checkDiv();
    });
});
</script>