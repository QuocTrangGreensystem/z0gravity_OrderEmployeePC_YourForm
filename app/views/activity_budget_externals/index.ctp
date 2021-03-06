<?php //echo $html->script('jquery.dataTables');                                                                                                                                                                                           ?>
<?php //echo $html->css('jquery.ui.custom');                                                                                                                                                                                           ?>

<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<?php echo $html->script('history_filter'); ?>
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php //echo $html->css('slick_grid/smoothness/jquery-ui-1.8.16.custom'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<?php //echo $html->css('jquery.dataTables'); ?>

<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<!--[if lt IE 8]>
    <style type='text/css'>
        #wd-table{
            height: 300px;
        }
    </style>
<![endif]-->
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .grid-canvas .slick-cell.editable{
        overflow : visible;
    }
    .delete-attachment,.download-attachment{
        background-repeat: no-repeat;
        display: inline-block;
        width: 16px;
        height: 16px;
        vertical-align: middle;
        margin: 0 5px;
        overflow: hidden;
        color: transparent !important;
        text-indent: -999px;
    }
    .delete-attachment{
        background-image: url("<?php echo $this->webroot . 'img/delete.png'; ?>");
    }
    .download-attachment{
        background-image: url("<?php echo $this->webroot . 'img/download.png'; ?>");
    }
    #upload-template,#upload-template form{
        position: relative;
    }
    #upload-template .file{
        background: url("<?php echo $this->webroot . 'img/browse.png'; ?>") no-repeat top right;
        height: 27px;
        width: 99px;
        float: left;
        background-position: 0 0px;
    }
    #upload-template .file-uploading{
        background-position: 0 -54px !important;
        cursor: pointer;
    }
    #upload-template .file:hover{
        background-position: 0 -27px;
    }
    .browse {
        position: absolute;
        top: 0;
        left: 60px;
        width: 20px;
        height: 27px;
        background: url("<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>") no-repeat center center !important;
        display: none;
    }
    .slick-cell .file input{
        position: absolute;
        top : 0;
        left : 0;
        width: 99px;
        height: 27px;
        opacity: 0;
        filter: alpha(opacity = 0);
    }
    .slick-cell .text input{
        position: absolute;
        top: 0;
        left: 99px;
        width: 46px;
        height: 27px;
        opacity: 0;
        filter: alpha(opacity = 0);
    }
    .url-attachment{
        background: url("<?php echo $this->webroot . 'img/link_budget.png'; ?>") no-repeat top right;
        height: 27px;
        width: 46px;
        float: left;
        background-position: 0 0px;
        text-indent: -100px;
    }
    .url-attachment:hover{
        background-position: 0 -27px;
    }
    #action-attach-url{
        background: url("<?php echo $this->webroot . 'img/browse_budget.png'; ?>") no-repeat top right;
        height: 27px;
        width: 140px;
        float: left;
        background-position: 0 0px;
    }
    #action-attach-url:hover{
        background-position: 0 -27px;
    }
    #upload-template .file-uploading{
        background-position: 0 -54px !important;
        cursor: pointer;
    }
    #upload-template .text:hover{
        background-position: 0 -27px;
        cursor: auto;
    }
    #gs-attach{
        background: url("<?php echo $this->webroot . 'img/front/bt-check-box.png'; ?>") no-repeat top right;
        height: 16px;
        width: 16px;
        background-position: -64px 0px;
        position: absolute;
        top: 55px;
        right: 21px;
        cursor: pointer;
    }
    .gs-attach-remove{
        background-position: 0px 0px !important;
    }
    #gs-url{
        background: url("<?php echo $this->webroot . 'img/front/bt-check-box.png'; ?>") no-repeat top right;
        height: 16px;
        width: 16px;
        background-position: 0 0px;
        position: absolute;
        top: 95px;
        right: 21px;
        cursor: pointer;
    }
    .gs-url-add{
        background-position: -64px 0px !important;
    }
    .wd-bt-big a.wd-hover-advance-tooltip{width: 24px !important;}
</style>
<!-- dialog_attachement_or_url -->
<div id="dialog_attachement_or_url" class="buttons" style="display: none;">
    <fieldset>
        <?php 
        echo $this->Form->create('Upload', array(
                'type' => 'file', 'id' => 'form_dialog_attachement_or_url', 
                'url' => array('controller' => 'activity_budget_externals', 'action' => 'upload')
            )); ?>
        <div style="height:auto;" class="wd-scroll-form">
            <div class="wd-input">
                <label for="attachement"><?php __("Attachement") ?></label>
                <p id="gs-attach"></p>
                <?php
                echo $this->Form->hidden('id', array('id' => false, 'rel' => 'no-history', 'value' => ''));
                ?>
                <?php
                echo $this->Form->input('attachment', array('type' => 'file', 'value' => '',
                    'name' => 'FileField[attachment]',
                    'label' => false,
                    'class' => 'update_attach_class',
                    'rel' => 'no-history'));
                ?>   							
            </div>
            <div class="wd-input">
                <label for="url"><?php __("Url") ?></label>
                <p id="gs-url"></p>
                <?php
                echo $this->Form->input('url', array('type' => 'text',
                    'label' => false,
                    'class' => 'update_url',
                    'disabled' => 'disabled',
                    'rel' => 'no-history'));
                ?> 
            </div>
            <p style="color: black;margin-left: 146px; font-size: 12px; font-style: italic;">
                <strong>Ex:</strong> 
                www.example.com
            </p>
        </div>
        <?php
        echo $this->Form->end();
        ?>  
    </fieldset>
    <div style="clear: both;"></div>
    <ul class="type_buttons" style="padding-right: 10px !important">
        <li><a href="javascript:void(0)" class="cancel"><?php __("Cancel") ?></a></li>
        <li><a href="javascript:void(0)" class="new" id="ok_attach"><?php __('OK') ?></a></li>
    </ul>
</div>
<!-- dialog_attachement_or_url.end -->
<!-- export excel  -->
<fieldset style="display: none;">
    <?php
    //echo $this->Form->create('Export', array(
//        'type' => 'POST',
//        'url' => array('controller' => 'project_evolutions', 'action' => 'export', $projectName['Project']['id'])));
//    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
//    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"><div class="wd-panel">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __d(sprintf($_domain, 'External_Cost'), "External Cost", true) . ': ' . $activityName['Activity']['name']; ?></h2>
                    <a href="javascript:void(0);" class="wd-add-project" id="new-external" style="margin-right:5px;" onclick="addExternalCost();"><span><?php __d(sprintf($_domain, 'External_Cost'), 'External Cost') ?></span></a>
                    <a href="javascript:void(0);" class="btn btn-reset-filter" id="clean-filters" style="margin-right:5px;" title="Clean filters"></a>
					
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
                <div id="scrollTopAbsence"><div id="scrollTopAbsenceContent"></div></div>
                <br clear="all"  />
                <div class="wd-table" id="project_container" style="width:100%; height: 430px;">

                </div>
                <div id="pager" style="width:100%;height:36px; overflow: hidden; margin-top: 30px;">

                </div>
            </div>
            <?php echo $this->element('grid_status'); ?>
            </div></div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<?php echo $html->script('slick_grid/lib/jquery-ui-1.8.16.custom.min'); ?>
<?php echo $html->script('slick_grid/lib/jquery.event.drag-2.0.min'); ?>
<?php echo $html->script('slick_grid/slick.core'); ?>
<?php echo $html->script('slick_grid/slick.dataview'); ?>
<?php echo $html->script('slick_grid/controls/slick.pager'); ?>
<?php echo $html->script('slick_grid/slick.formatters'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangedecorator'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellrangeselector'); ?>
<?php echo $html->script('slick_grid/plugins/slick.cellselectionmodel'); ?>
<?php echo $html->script('slick_grid/slick.editors'); ?>
<?php echo $html->script('slick_grid/slick.grid'); ?>
<?php echo $html->script(array('slick_grid_custom')); ?>

<?php

function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Name', true),
        'width' => 160,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBoxCustom'
    ),
    array(
        'id' => 'order_date',
        'field' => 'order_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Order date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker'
    ),
    array(
        'id' => 'budget_provider_id',
        'field' => 'budget_provider_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Provider', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'budget_type_id',
        'field' => 'budget_type_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Type', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'capex_id',
        'field' => 'capex_id',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'CAPEX/OPEX', true),
        'width' => 190,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'budget_erro',
        'field' => 'budget_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Budget ???', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'forecast_erro',
        'field' => 'forecast_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Forecast ???', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'var_erro',
        'field' => 'var_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Var', true),
        'width' => 80,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.percentValues'
        //'editor' => 'Slick.Editors.numericValue'
    ),
    array(
        'id' => 'ordered_erro',
        'field' => 'ordered_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Ordered ???', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.datePicker',
        //'validator' => 'DateValidate.startDate'
    ),
    array(
        'id' => 'remain_erro',
        'field' => 'remain_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Remain ???', true),
        'width' => 130,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudget',
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'man_day',
        'field' => 'man_day',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'M.D', true),
        'width' => 100,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericValueBudgetEdit',
        'formatter' => 'Slick.Formatters.manDayValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'progress_md',
        'field' => 'progress_md',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Progress %', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.numericProgressEdit',
        'formatter' => 'Slick.Formatters.percentValues'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'progress_erro',
        'field' => 'progress_erro',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Progress ???', true),
        'width' => 110,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'opex_calculated',
        'field' => 'opex_calculated',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'OPEX ???', true),
        'width' => 90,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'capex_calculated',
        'field' => 'capex_calculated',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'CAPEX ???', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.erroValue'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'file_attachement',
        'field' => 'file_attachement',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Attachement or URL', true),
        'width' => 150,
        'noFilter' => 1,
        'sortable' => true,
        'resizable' => false,
        'editor' => 'Slick.Editors.Attachement',
        'formatter' => 'Slick.Formatters.Attachement'
    ),
    array(
        'id' => 'reference',
        'field' => 'reference',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
    array(
        'id' => 'reference2',
        'field' => 'reference2',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 2', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
	//CHANGED HERE
	array(
        'id' => 'reference3',
        'field' => 'reference3',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 3', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
	array(
        'id' => 'reference4',
        'field' => 'reference4',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Reference 4', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.textBox'
        //'editor' => 'Slick.Editors.numericValue'
        //'editor' => 'Slick.Editors.mselectBox'
    ),
	array(
        'id' => 'expected_date',
        'field' => 'expected_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Expected Date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker'
    ),
	array(
        'id' => 'due_date',
        'field' => 'due_date',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Due Date', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.datePicker'
    ),
	array(
        'id' => 'profit_center_id',
        'field' => 'profit_center_id',
        'name' => __d(sprintf($_domain, 'Profit_Center'), 'Profit Center', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
		'editor' => 'Slick.Editors.selectBox'
    ),
	 array(
        'id' => 'funder_id',
        'field' => 'funder_id',
        'name' => __d(sprintf($_domain, 'Funder'), 'Funder', true),
        'width' => 150,
        'sortable' => true,
        'resizable' => true,
        'noFilter' => 1,
        'editor' => 'Slick.Editors.selectBox'
    ),
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __d(sprintf($_domain, 'External_Cost'), 'Action', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
    );
$i = 1;
$dataView = array();
$selectMaps = array(
    'budget_provider_id' => $budgetProviders,
    'budget_type_id' => $budgetTypes,
    'capex_id' =>  array('capex' => __('CAPEX', true), 'opex' => __('OPEX', true)),
	'profit_center_id' => $profits,
	'funder_id' => $funders
);
foreach ($budgetExternals as $budgetExternal) {
    $data = array(
        'id' => $budgetExternal['ProjectBudgetExternal']['id'],
        'activity_id' => $budgetExternal['ProjectBudgetExternal']['activity_id'],
        'project_id' => $projectLinked,
        'no.' => $i++
    );
    $data['name'] = (string) $budgetExternal['ProjectBudgetExternal']['name'];
    $data['order_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['order_date']);
    
    $data['budget_provider_id'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_provider_id'];
    $data['budget_type_id'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_type_id'];
    $data['capex_id'] = $capexTypes[$budgetExternal['ProjectBudgetExternal']['budget_type_id']] ? 'capex' : 'opex';
    $data['budget_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['budget_erro'];
    $data['ordered_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['ordered_erro'];
    $data['remain_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['remain_erro'];
    $totalconsumed = !empty($taskExternals[$_id]) && !empty($taskExternals[$_id]['consumed']) ? $taskExternals[$_id]['consumed'] : '';
    $data['special_consumed'] = (string) $totalconsumed;
    $totalManday = !empty($taskExternals[$_id]) && !empty($taskExternals[$_id]['maday']) ? $taskExternals[$_id]['maday'] : '';
    $progress_md = ($totalManday == 0 || $totalManday == '') ? 0 : round(($totalconsumed/$totalManday) * 100, 10);
    if(!empty($totalconsumed) && !empty($budgetExternal['ProjectBudgetExternal']['man_day'])){
        $data['progress_md'] = !empty($progress_md) ? $progress_md : '';
    } else {
        $data['progress_md'] = ($budgetExternal['ProjectBudgetExternal']['progress_md'] != 0) ? $budgetExternal['ProjectBudgetExternal']['progress_md'] : $progress_md;
    }
    $data['progress_erro'] = (string) $budgetExternal['ProjectBudgetExternal']['progress_erro'];
    $data['man_day'] = (string) $budgetExternal['ProjectBudgetExternal']['man_day'];
    $data['file_attachement'] = (string) $budgetExternal['ProjectBudgetExternal']['file_attachement'];
    $data['format'] = (string) $budgetExternal['ProjectBudgetExternal']['format'];
    $data['reference'] = (string) $budgetExternal['ProjectBudgetExternal']['reference'];
    $data['reference2'] = (string) $budgetExternal['ProjectBudgetExternal']['reference2'];
	//CHANGED HERE
	$data['reference3'] = (string) $budgetExternal['ProjectBudgetExternal']['reference3'];
    $data['reference4'] = (string) $budgetExternal['ProjectBudgetExternal']['reference4'];
	$data['expected_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['expected_date']);
	$data['due_date'] = $str_utility->convertToVNDate($budgetExternal['ProjectBudgetExternal']['due_date']);
	
    $data['profit_center_id'] = $budgetExternal['ProjectBudgetExternal']['profit_center_id'];
	$data['funder_id'] = $budgetExternal['ProjectBudgetExternal']['funder_id'];
    //calculated
    $ordered_erro = !empty($budgetExternal['ProjectBudgetExternal']['ordered_erro']) ? $budgetExternal['ProjectBudgetExternal']['ordered_erro'] : 0;
    $remain_erro = !empty($budgetExternal['ProjectBudgetExternal']['remain_erro']) ? $budgetExternal['ProjectBudgetExternal']['remain_erro'] : 0;
    $budget_erro = !empty($budgetExternal['ProjectBudgetExternal']['budget_erro']) ? $budgetExternal['ProjectBudgetExternal']['budget_erro'] : 0;
    $progress_md = $data['progress_md'];
    
    $forecast_erro = $ordered_erro+$remain_erro;
    if($budget_erro == 0){
        $var_erro = (0-1)*100;
    } else {
        $var_erro = (($forecast_erro/$budget_erro)-1)*100;
    }

    $data['forecast_erro'] = $forecast_erro;
    $data['var_erro'] = round($var_erro, 2);
    $data['progress_erro'] = ($ordered_erro*$progress_md)/100;
    if($budgetExternal['ProjectBudgetExternal']['capex_id']){
        $data['opex_calculated'] = 0;
        $data['capex_calculated'] = $ordered_erro+$remain_erro;
    } else {
        $data['opex_calculated'] = $ordered_erro+$remain_erro;
        $data['capex_calculated'] = 0;
    }
    
    
    $data['action.'] = '';

    $dataView[] = $data;
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'Clear' => __('Clear', true),
    'Phase start date must between %1$s and %2$s' => __('Phase start date must between %1$s and %2$s', true),
    'This date value must between %1$s and %2$s' => __('This date value must between %1$s and %2$s', true),
    'This phase of project is exist.' => __('This phase of project is exist.', true)
);
$viewManDay = __('M.D', true);
?>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true), '%3$s')); ?>');" class="wd-hover-advance-tooltip" href="<?php echo $this->Html->url(array('action' => 'delete', '%1$s', '%2$s')); ?>">Delete</a>
        </div>
    </div>
</div>
<div id="attachment-template" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="download-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'download'))); ?>"><?php echo __('Download', true); ?></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%2$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>
<div id="attachment-template-url" style="display: none;">
    <div style="overflow: hidden;" class="img-to-right">
        <a class="url-attachment" target="_blank" href="<?php echo 'http://%2$s'; ?>"></a>
        &nbsp; <a class="delete-attachment" href="<?php echo $this->Html->url(array('action' => 'attachement', '%1$s', '?' => array('type' => 'delete'))); ?>" rel="%3$s"><?php echo __('Delete', true); ?></a>
    </div>
</div>

<script type="text/javascript">
	var budgetCurrency = <?php echo json_encode($bg_currency);?>
    var DateValidate = {},ControlGrid,IuploadComplete = function(json){ 
        var data = ControlGrid.eval('currentEditor');
        data.onComplete(json);
    };
    (function($){
        
        $(function(){
            var $this = SlickGridCustom, gridControl;
            
            
            $('a.delete-attachment').live('click' , function(){
                var row = $(this).attr('rel'),
                data = ControlGrid.getDataItem(row);
                if(data && confirm($this.t('Are you sure you want to delete attachement : %s'
                , data['file_attachement']))){
                    data && $.ajax({
                        cache : false,
                        type : 'GET',
                        url : $(this).attr('href')
                    });
                    data['file_attachement'] = '';
                    ControlGrid.updateRow(row);
                }
                return false;
            });
            
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  <?php echo json_encode(!empty($canModified)); ?>;
            viewManDay = <?php echo json_encode($viewManDay); ?>;
            // For validate date
            var activityName = <?php echo json_encode($activityName['Activity']); ?>;
            var getTime = function(value){
                value = value.split("-");
                return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10), parseInt(value[0], 10))).getTime();
            }
            
            var actionTemplate =  $('#action-template').html(),
            attachmentTemplate =  $('#attachment-template').html(),
            attachmentURLTemplate =  $('#attachment-template-url').html();
            
            $.extend(Slick.Formatters,{
                Attachement : function(row, cell, value, columnDef, dataContext){
                    if(value){
                        if(dataContext.format == 2){
                            value = $this.t(attachmentTemplate,dataContext.id,row);
                        }
                        if(dataContext.format == 1){
                            value = $this.t(attachmentURLTemplate,dataContext.id,dataContext.file_attachement,row);
                        }
                    }
                    return Slick.Formatters.HTMLData(row, cell, value, columnDef, dataContext);
                },
                Action : function(row, cell, value, columnDef, dataContext){
                    return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.id,
                    dataContext.activity_id,dataContext.name), columnDef, dataContext);
                },
                erroValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
 			       return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> '+ budgetCurrency, columnDef, dataContext);
        		},
                percentValues : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
        			return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span>%', columnDef, dataContext);
        		},
                manDayValue : function(row, cell, value, columnDef, dataContext){
                    value = number_format(value, 2, ',', ' ');
        			return Slick.Formatters.HTMLData(row, cell, '<span class="row-number">' + value + '</span> ' + viewManDay, columnDef, dataContext);
        		}
            });
            
            $.extend(Slick.Editors,{
                Attachement : function(args){
                    var self = this;
                    $.extend(this, new BaseSlickEditor(args));
        			this.input = $("<a href='#' id='action-attach-url'></a><div class='browse'></div>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text');
                    $("#ok_attach").click(function(){
                        //self.input[0].remove();
                        $('#action-attach-url').css('display', 'none');
                        $('.browse').css('display', 'block');
                        $("#dialog_attachement_or_url").dialog('close');
                        
                        var form = $("#form_dialog_attachement_or_url");
                        form.find('input[name="data[Upload][id]"]').val(args.item.id);
                        form.submit();
                    });
                    this.focus();
                },
                numericValueBudget : function(args){
        			$.extend(this, new Slick.Editors.textBox(args));
        			this.input.attr('maxlength' , 10).keypress(function(e){
        				var key = e.keyCode ? e.keyCode : e.which;
        				if(!key || key == 8 || key == 13){
        					return;
        				}
        				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                        ///^[\-+]??$/
                        //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
        				if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
        					e.preventDefault();
        					return false;
        				}
        			});
        		},
                numericValueBudgetEdit : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    if(args.item.progress_md>0){
                        this.input.attr('disabled','disabled');
                        this.input.css('background','none');
                        this.input.css('background-color','#F5F5F5');
                    }else{
                        this.input.attr('maxlength' , 10).keypress(function(e){
                            var key = e.keyCode ? e.keyCode : e.which;
                            if(!key || key == 8 || key == 13){
                                return;
                            }
                            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                            ///^[\-+]??$/
                            //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
                            if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                                e.preventDefault();
                                return false;
                            }
                        });
                    }    
                },
                numericProgressEdit : function(args){
                    $.extend(this, new Slick.Editors.textBox(args));
                    if(args.item.man_day!=0){
                        this.input.attr('disabled','disabled');
                        this.input.css('background','none');
                        this.input.css('background-color','#F5F5F5');
                    }else{
                        this.input.attr('maxlength' , 10).keypress(function(e){
                            var key = e.keyCode ? e.keyCode : e.which;
                            if(!key || key == 8 || key == 13){
                                return;
                            }
                            var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
                            ///^[\-+]??$/
                            //&& (!/^[\-+]?([0-9]{1}|[1-9][0-9]{1,2})(\.[0-9]{0,2})?$/.test(val)
                            if(val == '0' || !/^[\-]?([0-9]{0,8})(\.[0-9]{0,2})?$/.test(val)){
                                e.preventDefault();
                                return false;
                            }
                        });
                    }    
                },
                textBoxCustom : function(args){
        			$.extend(this, new BaseSlickEditor(args));
        			this.input = $("<input type='text' placeholder='New External Cost'/>")
        			.appendTo(args.container).attr('rel','no-history').addClass('editor-text placeholder');
        			this.focus();
        		},
            });
        
            var  data = <?php echo json_encode($dataView); ?>;
            var capexTypes = <?php echo json_encode($capexTypes); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            var options = {
                showHeaderRow: false      
            };
            var projectLinked = <?php echo json_encode($projectLinked); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                activity_id : {defaulValue : activityName['id'], allowEmpty : false},
                name : {defaulValue : '' , allowEmpty : false},
                order_date : {defaulValue : ''},
                budget_provider_id : {defaulValue : ''},
                budget_type_id : {defaulValue : ''},
                capex_id : {defaulValue : ''},
                budget_erro : {defaulValue : ''},
                ordered_erro : {defaulValue : ''},
                remain_erro : {defaulValue : ''},
                progress_erro : {defaulValue : ''},
                progress_md : {defaulValue : ''},
                man_day : {defaulValue : ''},
                file_attachement : {defaulValue : ''},
                format : {defaulValue : ''},
                reference : {defaulValue : ''},
                reference2 : {defaulValue : ''},
				//CHANGED HERE
				reference3 : {defaulValue : ''},
                reference4 : {defaulValue : ''},
				expected_date : {defaulValue : ''},
                due_date : {defaulValue : ''},
				profit_center_id : {defaulValue : 0},
				funder_id : {defaulValue : 0},
                project_id : {defaulValue : projectLinked}
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            $this.onBeforeEdit = function(args){
                $('.rowCurrentEdit').find('.slick-cell').removeClass('row-current-edit');
                $('.slick-row').removeClass('rowCurrentEdit');
                $('.active').parent().addClass('rowCurrentEdit');
                $('.rowCurrentEdit').find('.slick-cell').addClass('row-current-edit');
                if(args.column.field == 'file_attachement' && (!args.item || args.item['file_attachement'] || !args.item['id'])){
                    return false;
                }
                return true;
            }
            ControlGrid = $this.init($('#project_container'),data,columns,options);
            
            var _budgetErros = _orderedErros = _remainErros = _forecastErros = _varErros = _progressErros = _opexCalcul = _capexCalcul = _manDay = _progressMd = 0;
            var totalRecord = 0;
            $.each(data, function(ind, vl){
                _budgetErros += vl.budget_erro ? parseFloat(vl.budget_erro) : 0;
                _orderedErros += vl.ordered_erro ? parseFloat(vl.ordered_erro) : 0;
                _remainErros += vl.remain_erro ? parseFloat(vl.remain_erro) : 0;
                _forecastErros += parseFloat(vl.forecast_erro);
                _progressErros += parseFloat(vl.progress_erro);
                _opexCalcul += vl.opex_calculated ? parseFloat(vl.opex_calculated) : 0;
                _capexCalcul += vl.capex_calculated ? parseFloat(vl.capex_calculated) : 0;
                _manDay += vl.man_day ? parseFloat(vl.man_day) : 0;
                _progressMd += vl.progress_md ? parseFloat(vl.progress_md) : 0;
                totalRecord++;
            });
            //_progressMd = (totalRecord == 0) ? '0%' : (_progressMd/totalRecord).toFixed(2) + '%';
            _budgetErros = _budgetErros.toFixed(2);
            _progressErros = _progressErros.toFixed(2);
            _progressMd = (_orderedErros == 0) ? '0%' : ((_progressErros/_orderedErros)*100).toFixed(2) + '%';
            var _calCulVarErro;
            if(_budgetErros == 0){
                _calCulVarErro = (0-1)*100;
            } else {
                _calCulVarErro = ((_forecastErros/_budgetErros)-1)*100;
            }
            _calCulVarErro = _calCulVarErro.toFixed(2);
            _varErros = number_format(_calCulVarErro, 2, ',', ' ') + '%';
            _budgetErros = number_format(_budgetErros, 2, ',', ' ') + budgetCurrency;
            _forecastErros = number_format(_forecastErros, 2, ',', ' ') + budgetCurrency;
            _orderedErros = number_format(_orderedErros, 2, ',', ' ') + budgetCurrency;
            _remainErros = number_format(_remainErros, 2, ',', ' ') + budgetCurrency;
            _progressErros = number_format(_progressErros, 2, ',', ' ') + budgetCurrency;
            _opexCalcul = number_format(_opexCalcul, 2, ',', ' ') + budgetCurrency;
            _capexCalcul = number_format(_capexCalcul, 2, ',', ' ') + budgetCurrency;
            _manDay = number_format(_manDay, 2, ',', ' ') + ' ' + viewManDay;
            ControlGrid.onSort.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
			});
			ControlGrid.onScroll.subscribe(function(args, e, scope){
                $('.row-number').parent().addClass('row-number-custom');
			});
            $this.onCellChange = function(args){
                $('.row-number').parent().addClass('row-number-custom');
                if(args.item){
                    args.item.capex_id = capexTypes[args.item.budget_type_id] ? 'capex' : 'opex';
                    var budget_erro = args.item.budget_erro ? parseFloat(args.item.budget_erro) : 0;
                    var forecast_erro = args.item.forecast_erro ? parseFloat(args.item.forecast_erro) : 0;
                    var ordered_erro = args.item.ordered_erro ? parseFloat(args.item.ordered_erro) : 0;
                    var remain_erro = args.item.remain_erro ? parseFloat(args.item.remain_erro) : 0;
                    var progress_md = args.item.progress_md ? parseFloat(args.item.progress_md) : 0;
                    var _var_erro;
                    
                    args.item.forecast_erro = ordered_erro+remain_erro;
                    if(budget_erro == 0){
                        _var_erro = -100;
                    } else {
                        _var_erro = (((ordered_erro+remain_erro)/budget_erro)-1)*100;
                    }
                    args.item.var_erro = _var_erro.toFixed(2);
                    args.item.progress_erro = ((ordered_erro*progress_md)/100).toFixed(2);
                    
                    if(args.item.capex_id == 'capex'){
                        args.item.opex_calculated = 0;
                        args.item.capex_calculated = ordered_erro+remain_erro;
                    } else {
                        args.item.opex_calculated = ordered_erro+remain_erro;
                        args.item.capex_calculated = 0;
                    }
                    
                    _budgetErros = _orderedErros = _remainErros = _forecastErros = _progressErros = _opexCalcul = _capexCalcul = _manDay = _progressMd = 0;
                    totalRecord = 0;
                    $.each(data, function(ind, vl){
                        _budgetErros += vl.budget_erro ? parseFloat(vl.budget_erro) :0;
                        _orderedErros += vl.ordered_erro ? parseFloat(vl.ordered_erro) : 0;
                        _remainErros += vl.remain_erro ? parseFloat(vl.remain_erro) : 0;
                        _forecastErros += parseFloat(vl.forecast_erro);
                        _progressErros += parseFloat(vl.progress_erro);
                        _opexCalcul += vl.opex_calculated ? parseFloat(vl.opex_calculated) : 0;
                        _capexCalcul += vl.capex_calculated ? parseFloat(vl.capex_calculated) : 0;
                        _manDay += vl.man_day ? parseFloat(vl.man_day) : 0;
                        _progressMd += vl.progress_md ? parseFloat(vl.progress_md) : 0;
                        totalRecord++;
                    });
                    //_progressMd = (totalRecord == 0) ? '0%' : (_progressMd/totalRecord).toFixed(2) + '%';
                    _budgetErros = _budgetErros.toFixed(2);
                    _progressErros = _progressErros.toFixed(2);
                    _progressMd = (_orderedErros == 0) ? '0%' : ((_progressErros/_orderedErros)*100).toFixed(2) + '%';
                    if(_budgetErros == 0){
                        _calCulVarErro = (0-1)*100;
                    } else {
                        _calCulVarErro = ((_forecastErros/_budgetErros)-1)*100;
                    }
                    _calCulVarErro = _calCulVarErro.toFixed(2);
                    if(_calCulVarErro > 0){
                        $('#gs-var-erro').addClass('invai-var');
                    } else {
                        $('#gs-var-erro').removeClass('invai-var');
                    }
                    _varErros = number_format(_calCulVarErro, 2, ',', ' ') + '%';
                    _budgetErros = number_format(_budgetErros, 2, ',', ' ') + budgetCurrency;
                    _forecastErros = number_format(_forecastErros, 2, ',', ' ') + budgetCurrency;
                    _orderedErros = number_format(_orderedErros, 2, ',', ' ') + budgetCurrency;
                    _remainErros = number_format(_remainErros, 2, ',', ' ') + budgetCurrency;
                    _opexCalcul = number_format(_opexCalcul, 2, ',', ' ') + budgetCurrency;
                    _capexCalcul = number_format(_capexCalcul, 2, ',', ' ') + budgetCurrency;
                    _progressErros = number_format(_progressErros, 2, ',', ' ') + budgetCurrency;
                    _manDay = number_format(_manDay, 2, ',', ' ') + ' ' + viewManDay;
                    $('#gs-budget-erro p').html(_budgetErros);
                    $('#gs-ordered-erro p').html(_orderedErros);
                    $('#gs-remain-erro p').html(_remainErros);
                    $('#gs-forecast-erro p').html(_forecastErros);
                    $('#gs-var-erro p').html(_varErros);
                    $('#gs-progress-erro p').html(_progressErros);
                    $('#gs-opex-calcul p').html(_opexCalcul);
                    $('#gs-capex-calcul p').html(_capexCalcul);
                    $('#gs-man-day p').html(_manDay);
                    $('#gs-progress-manDay p').html(_progressMd);
                    
                }
                return true;
            }
            addExternalCost = function(){
                ControlGrid.gotoCell(data.length, 1, true);
            }
            // Chuyen header sang mau xanh la cay
            var headers = $('.slick-header-columns').get(0).children;
            $.each(headers, function(index, val){
                if(index == 11){
                    $('#'+headers[index].id).addClass('gs-custom-cell-md-header');
                }
            });
            var cols = ControlGrid.getColumns();
            var numCols = cols.length;
            var gridW = 0;
            for (var i=0; i<numCols; i++) {
                gridW += cols[i].width;
            }
            ControlGrid.onColumnsResized.subscribe(function (e, args) {			 
				var _cols = ControlGrid.getColumns();
                var _numCols = cols.length;
                var _gridW = 0;
                for (var i=0; i<_numCols; i++) {
                    _gridW += _cols[i].width;
                }
                $('#wd-header-custom').css('width', _gridW);
                $('.row-number').parent().addClass('row-number-custom');
			});
			//CHANGED HERE
            header =
                '<div id="wd-header-custom" class="slick-headerrow-columns" style="width: '+gridW+'px">'
                    + '<div class="ui-state-default slick-headerrow-column l0 r0 wd-row-custom wd-custom-cell"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l1 r1 wd-row-custom wd-custom-cell gs-custom-cell-erro" style="text-align: left;"><p>Total</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l2 r2 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l3 r3 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l4 r4 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l5 r5 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l6 r6 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-budget-erro"><p>' +_budgetErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l7 r7 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-forecast-erro"><p>' +_forecastErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l8 r8 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-var-erro"><p>' +_varErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l9 r9 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-ordered-erro"><p>' +_orderedErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l10 r10 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-remain-erro"><p>' +_remainErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l11 r11 wd-row-custom wd-custom-cell gs-custom-cell-md" id="gs-man-day"><p>' +_manDay+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l12 r12 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-progress-manDay"><p> '+_progressMd+' </p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l13 r13 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-progress-erro"><p>' +_progressErros+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l14 r14 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-opex-calcul"><p>' +_opexCalcul+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l15 r15 wd-row-custom wd-custom-cell gs-custom-cell-erro" id="gs-capex-calcul"><p>' +_capexCalcul+ '</p></div>'
                    + '<div class="ui-state-default slick-headerrow-column l16 r16 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l17 r17 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l18 r18 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l19 r19 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
					+ '<div class="ui-state-default slick-headerrow-column l20 r20 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l21 r21 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
					+ '<div class="ui-state-default slick-headerrow-column l22 r22 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l23 r23 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
					+ '<div class="ui-state-default slick-headerrow-column l24 r24 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
                    + '<div class="ui-state-default slick-headerrow-column l25 r25 wd-row-custom wd-custom-cell gs-custom-cell-erro"></div>'
              + '</div>';
            $('.slick-header-columns').after(header);
            if(_calCulVarErro > 0){
                $('#gs-var-erro').addClass('invai-var');
            } else {
                $('#gs-var-erro').removeClass('invai-var');
            }
            /* table .end */
            var createDialog = function(){
                $('#dialog_attachement_or_url').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog = $.noop;
            }
            
            $("#action-attach-url").live('click',function(){
                createDialog();
                var titlePopup = <?php echo json_encode(__('Attachement or URL', true))?>;
                $("#dialog_attachement_or_url").dialog('option',{title:titlePopup}).dialog('open');
            });
            
            $(".cancel").live('click',function(){
                $("#dialog_attachement_or_url").dialog('close');
            });
            $("#gs-url").click(function(){
                $(this).addClass('gs-url-add');
                $('#gs-attach').addClass('gs-attach-remove');
                $('.update_url').removeAttr('disabled').css('border', '1px solid #3B57EE');
                $('.update_attach_class').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $("#gs-attach").click(function(){
                $(this).removeClass('gs-attach-remove');
                $('#gs-url').removeClass('gs-url-add');
                $('.update_attach_class').removeAttr('disabled').css('border', '1px solid #3B57EE');;
                $('.update_url').attr('disabled', 'disabled').css('border', '1px solid #d4d4d4');
            });
            $('.row-number').parent().addClass('row-number-custom');
            function setupScroll(){
                $("#scrollTopAbsenceContent").width($(".grid-canvas").width()+50);
                $("#scrollTopAbsence").width($(".wd-table").width());
            }
            setTimeout(function(){
                setupScroll();
            }, 2500);
            $("#scrollTopAbsence").scroll(function () {
                $(".wd-table").scrollLeft($("#scrollTopAbsence").scrollLeft());
            });
            $(".slick-viewport").scroll(function () {
                $("#scrollTopAbsence").scrollLeft($(".wd-table").scrollLeft());
            });
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


<style type="text/css">
    #wd-header-custom{
        height: 30px; 
        border: 1px solid #E0E0E0; 
        border-bottom: none;
        border-right: none !important;
    }
    .wd-row-custom{
        margin-right: -7px;
    }
    .wd-row-custom p{
        padding-top: 5px;
        font-weight: bold;
    }
    .wd-custom-cell{
        background: none !important;
        border-right: none !important;
        /*width: 100% !important;*/
    }
    .slick-viewport{
        /*height: 76% !important;*/
    }
    #project_container{
        overflow: visible !important;
    }
    .gs-custom-cell-erro{
        background-color: #95B3D7 !important;
    }
    .invai-var{
        background-color: #FF5959 !important;
    }
    .gs-custom-cell-md-header{
        background: #75923C !important;
    }
    .gs-custom-cell-md{
        background-color: #C2D69A !important;
    }
    .cl-average-daily-rate{
        padding: 7px;
        margin-left: -12px;
        width: 105px;
    }
    .color-rate-loading{
        color: #ccc;
    }
    .color-rate-success{
        color: #3BBD43;
    }
    .color-rate-error{
        color: #F71230;
    }
    .slick-row.odd{
        background: #FFF !important;
    }
    .row-parent-custom{
        background-color: #EAF1FA;
    }
    .row-disabled-custom{
        background-color: #FAFAFA !important;
    }
    .row-number-custom{
        text-align: right;
    }
    .l5, .l7, .l8, .l13, .l14, .l15{
        background: #F5F5F5;
    }
    .row-current-edit{
        border-top: 1px solid #004482 !important;
        border-bottom: 1px solid #004482 !important;
        box-shadow: 0px 0px 5px #004482;
        /*
        zoom: 1;
        filter: progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=0),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=90),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=180),
                progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=2, Direction=270),
                progid:DXImageTransform.Microsoft.Chroma(Color='#ffffff');
        
        filter: 
            progid:DXImageTransform.Microsoft.DropShadow(OffX=0, OffY=0, Color=#00FF3D),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=0),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=90),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=180),
            progid:DXImageTransform.Microsoft.Shadow(Color=#00FF3D, Strength=5, Direction=270);
        
        
        -ms-filter: 
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=0, Color='#00FF3D')",
        "progid:DXImageTransform.Microsoft.Shadow(Strength=15, Direction=180, Color='#00FF3D')"
        ;
        */
    }
</style>