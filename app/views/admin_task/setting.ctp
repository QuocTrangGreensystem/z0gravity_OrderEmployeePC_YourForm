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
 .wd-select-box{border:#d8d8d8 solid 1px;padding:6px;}
 .wd-input-select{padding-left:40px;padding-top:10px;font-weight:700;}
 .wd-list-project .wd-tab .wd-content label {
	margin-top: 10px;
 }
 table.table-setting{
	table-layout: fixed;
    display: -webkit-inline-box;
	width: auto;
 }
 table.table-setting td{
	max-width: 600px;
    vertical-align: middle; 
	position: relative;
 }
 table.table-setting .field-label{
	min-width: 370px;
 }
 table.table-setting .field-label >div{
	margin-right: 20px; 
 }
 table.table-setting td input,
 table.table-setting td select{
	margin: 5px 0;
    min-width: 80px;
    width: 100%;
 }
 table.table-setting td select::-ms-expand{
	 display: none;
 }
 table.table-setting td select{
	-webkit-appearance: none;
       -moz-appearance: none;
        -ms-appearance: none;
         -o-appearance: none;
            appearance: none;
	background: url(../../img/new-icon/down.png) no-repeat right 8px center;
	padding-right: 26px;
 }
 .wd-list-project .wd-tab .wd-content table.table-setting label{
	width: 100%;
	display: block;
	height: auto;
 }
  table.table-setting td input.loading,
  table.table-setting td select.loading{
	background: #fff url(/img/loading_check.gif) center right no-repeat;
	background-size: 28px;
  }
  .wd-select-box.saved{
	border-color: green;
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
                                <div class="wd-table-container">
                                    <div id="wd-select">
										
										<?php echo $this->Form->create('Project', array('url' => array('controller' => 'admin_task', 'action' => 'setting'))); ?>
										<div class="setting-table">
											<?php 
											$options = array(__('No', true), __('Yes', true));
											$method_options = array(
												'consumed' => __('Consumed', true),
												'count_close_task' => __('Close Task', true),
												'workload_of_close_task' => __('Close Task with workload', true),
												'manual' => __('Manual', true),
												'no_progress' => __('The progress is not displayed', true)
											);
											$method_default =  array_keys($method_options);
											$method_default =  $method_default[0];
											$method_selected =  isset($companyConfigs['project_progress_method']) ? $companyConfigs['project_progress_method'] : $method_default;
											$fleids = array(
												array(
													'label' => __("Display synthesis in the screen task", true),
													'input' => $this->Form->input('display_synthesis', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('display_synthesis', this.value);",
														'id' => "display_synthesis",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_synthesis']) ? $companyConfigs['display_synthesis'] : 1,
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Display the gap with the linked task', true),
													'input' => $this->Form->input('gap_linked_task', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('gap_linked_task', this.value);",
														'id' => "gap_linked_task",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['gap_linked_task']) ? $companyConfigs['gap_linked_task'] : 0, &$companyConfigs['gap_linked_task'],
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Display vision task', true),
													'input' => $this->Form->input('display_visions', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('display_visions', this.value);",
														'id' => "display_visions",
														"class" => "wd-select-box",
														"default" => &$companyConfigs['display_visions'],
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Create a task without phase', true),
													'input' => $this->Form->input('task_no_phase', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('task_no_phase', this.value);",
														'id' => "task_no_phase",
														"class" => "wd-select-box",
														"default" => &$companyConfigs['task_no_phase'],
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Can\'t create a ntc task', true),
													'input' => $this->Form->input('create_ntc_task', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('create_ntc_task', this.value);",
														'id' => "create_ntc_task",
														"class" => "wd-select-box",
														"default" => &$companyConfigs['create_ntc_task'],
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Show import excel tasks', true),
													'input' => $this->Form->input('import_task_excel', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('import_task_excel', this.value);",
														'id' => "import_task_excel",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['import_task_excel']) ? $companyConfigs['import_task_excel'] : 0,
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Show import csv tasks', true),
													'input' => $this->Form->input('import_task_csv', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('import_task_csv', this.value);",
														'id' => "import_task_csv",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['import_task_csv']) ? $companyConfigs['import_task_csv'] : 1,
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Show import XML MSP tasks', true),
													'input' => $this->Form->input('import_task_xml', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('import_task_xml', this.value);",
														'id' => "import_task_xml",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['import_task_xml']) ? $companyConfigs['import_task_xml'] : 1,
														"options" => $options,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Method for calculating progress', true),
													'input' => $this->Form->input('project_progress_method', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('project_progress_method', this.value);",
														'id' => "project_progress_method",
														"class" => "wd-select-box",
														"options" => $method_options,
														"default" => $method_selected,
														"rel" => "no-history"
													))
												),
												array(
													'label' => __('Display disponibility', true),
													'input' => $this->Form->input('display_disponibility', array(
														'div' => false,
														'label' => false,
														'onchange' => "editMe('display_disponibility', this.value);",
														'id' => "display_disponibility",
														"class" => "wd-select-box",
														"default" => isset($companyConfigs['display_disponibility']) ? $companyConfigs['display_disponibility'] : 0,
														"options" => $options,
														"rel" => "no-history"
													))
												),
												
											);
                                            ?>
											<table class="table-setting"><tbody>
												<?php foreach( $fleids as $fleid ){?>
													<tr>
														<td class="field-label"><div><label><?php echo $fleid['label'];?></label></div></td>
														<td class="field-options"><div><?php echo $fleid['input'];?></div></td>
													</tr>
												<?php } ?> 
											</tbody></table>
                                        </div>
                                        <div class="wd-submit wd-hide" style="margin-top: 10px;">
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
var wdTable = $('.setting-table');
function update_table_height(){
	var heightTable = $(window).height() - wdTable.offset().top - 40;
	wdTable.height(heightTable);
}
$(window).resize(function(){
	update_table_height();
});
update_table_height();
function editMe(field,value) {
	$('#'+field).addClass('loading').prop('disabled', true);
    var data = field+'/'+value;
    $.ajax({
        url: '/company_configs/editMe/',
        data: {
            data : { value : value, field : field }
        },
        type:'POST',
        success:function(data) {
            $('#'+field).removeClass('KO');
			$('#'+field).addClass('saved').removeClass('loading').prop('disabled', false);
			setTimeout(function(){
				$('#'+field).removeClass('saved');
			}, 1500);
        }
    });
}
</script>
